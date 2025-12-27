<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SuratKeputusan;
use App\Models\User;
use App\Models\UserLog;
use App\Services\DocumentNumberService;
use App\Services\NotificationService;
use App\Services\CsvExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SuratKeputusanController extends Controller
{
    protected $documentNumberService;
    protected $notificationService;
    protected $csvExportService;

    public function __construct(
        DocumentNumberService $documentNumberService, 
        NotificationService $notificationService,
        CsvExportService $csvExportService
    )
    {
        $this->middleware('auth');
        $this->middleware('role:user');
        $this->documentNumberService = $documentNumberService;
        $this->notificationService = $notificationService;
        $this->csvExportService = $csvExportService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $query = SuratKeputusan::byUser($user->BADGE);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('NOMOR_SK', 'like', "%{$search}%")
                  ->orWhere('PERIHAL', 'like', "%{$search}%");
            });
        }

        // Sort functionality
        $sort = $request->get('sort', 'desc');
        $query->orderBy('NOMOR_SK', strtoupper($sort))
              ->orderBy('TANGGAL', strtoupper($sort));

        $documents = $query->paginate(20);

        return view('user.sk.index', compact('documents'));
    }

    public function create()
    {
        return view('user.sk.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'TANGGAL' => 'required|date|date_equals:today',
            'PERIHAL' => 'required|string|max:500',
            'PENANDATANGAN' => 'required|string|max:100',
            'UNIT_KERJA' => 'required|string|max:100',
            'NAMA' => 'required|string|max:100',
            'pdf_file' => 'required|file|mimes:pdf|max:20480', // Max 20MB
        ], [
            'TANGGAL.required' => 'Tanggal wajib diisi',
            'TANGGAL.date' => 'Format tanggal tidak valid',
            'PERIHAL.required' => 'Perihal wajib diisi',
            'PENANDATANGAN.required' => 'Penandatangan wajib diisi',
            'UNIT_KERJA.required' => 'Unit kerja wajib diisi',
            'NAMA.required' => 'Nama wajib diisi',
            'pdf_file.required' => 'File PDF wajib diupload',
            'pdf_file.mimes' => 'File harus berformat PDF',
            'pdf_file.max' => 'Ukuran file maksimal 10MB',
        ]);

        try {
            // Generate nomor SK
            $nomorSK = $this->documentNumberService->generateSKNumber($request->TANGGAL);

            // Upload PDF ke MinIO
            $pdfPath = null;
            if ($request->hasFile('pdf_file')) {
                $file = $request->file('pdf_file');
                $fileName = '' . str_replace(['/', ' '], '_', $nomorSK) . '_' . time() . '.pdf';
                $pdfPath = Storage::disk('minio')->putFileAs('surat-keputusan', $file, $fileName);
            }

            // Simpan data
            $document = SuratKeputusan::create([
                'NOMOR_SK' => $nomorSK,
                'TANGGAL' => $request->TANGGAL,
                'PERIHAL' => $request->PERIHAL,
                'PENANDATANGAN' => $request->PENANDATANGAN,
                'UNIT_KERJA' => $request->UNIT_KERJA,
                'NAMA' => $request->NAMA,
                'USER' => Auth::user()->BADGE,
                'pdf_path' => $pdfPath,
                'approval_status' => 'pending',
            ]);

            // Log aktivitas
            UserLog::logCreate(Auth::user()->BADGE, 'Surat Keputusan', $nomorSK);

            // Notifikasi ke admin
            $admins = User::where('ROLE', 'admin')->get();
            foreach ($admins as $admin) {
                $this->notificationService->notifyPendingApproval(
                    $admin->BADGE,
                    'Surat Keputusan',
                    $nomorSK,
                    Auth::user()->BADGE
                );
            }

            return redirect()->route('user.sk.index')
                ->with('success', 'Surat Keputusan berhasil dibuat dengan nomor: ' . $nomorSK);

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal membuat Surat Keputusan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $document = SuratKeputusan::findOrFail($id);
        
        // Pastikan user hanya bisa melihat dokumennya sendiri
        if ($document->USER !== Auth::user()->BADGE) {
            abort(403, 'Unauthorized action.');
        }

        return view('user.sk.show', compact('document'));
    }

    public function edit($id)
    {
        $document = SuratKeputusan::findOrFail($id);
        
        if ($document->USER !== Auth::user()->BADGE) {
            abort(403, 'Unauthorized action.');
        }

        // Tidak bisa edit jika sudah disetujui
        if ($document->isApproved()) {
            return redirect()->route('user.sk.index')
                ->with('error', 'Dokumen yang sudah disetujui tidak dapat diubah');
        }
        // Boleh edit jika status rejected

        return view('user.sk.edit', compact('document'));
    }

    public function update(Request $request, $id)
{
    $document = SuratKeputusan::findOrFail($id);
    
    if ($document->USER !== Auth::user()->BADGE) {
        abort(403, 'Unauthorized action.');
    }

    if ($document->isApproved()) {
        return redirect()->route('user.sk.index')
            ->with('error', 'Dokumen yang sudah disetujui tidak dapat diubah');
    }

    $request->validate([
        'PERIHAL' => 'required|string|max:500',
        'PENANDATANGAN' => 'required|string|max:100',
        'UNIT_KERJA' => 'required|string|max:100',
        'NAMA' => 'required|string|max:100',
        'pdf_file' => 'nullable|file|mimes:pdf|max:20480',
    ]);

    try {
        $data = [
            'PERIHAL' => $request->PERIHAL,
            'PENANDATANGAN' => $request->PENANDATANGAN,
            'UNIT_KERJA' => $request->UNIT_KERJA,
            'NAMA' => $request->NAMA,
            // RESET STATUS KE PENDING
            'approval_status' => 'pending',
            'approved_by' => null,
            'approved_at' => null,
            'rejection_reason' => null,
        ];

        // Upload PDF baru jika ada
        if ($request->hasFile('pdf_file')) {
            // Hapus file lama
            if ($document->pdf_path) {
                Storage::disk('minio')->delete($document->pdf_path);
            }

            $file = $request->file('pdf_file');
            $fileName = str_replace(['/', ' '], ['_', '_'], $document->NOMOR_SK) . '_' . time() . '.pdf';
            $data['pdf_path'] = Storage::disk('minio')->putFileAs('surat-keputusan', $file, $fileName);
        }

        $document->update($data);

        UserLog::logUpdate(Auth::user()->BADGE, 'Surat Keputusan', $document->NOMOR_SK);

        // Notifikasi ke admin bahwa ada dokumen yang disubmit ulang
        $admins = User::where('ROLE', 'admin')->get();
        foreach ($admins as $admin) {
            $this->notificationService->notifyPendingApproval(
                $admin->BADGE,
                'Surat Keputusan',
                $document->NOMOR_SK,
                Auth::user()->BADGE
            );
        }

        return redirect()->route('user.sk.index')
            ->with('success', 'Surat Keputusan berhasil diupdate dan dikirim untuk review ulang');

    } catch (\Exception $e) {
        return redirect()->back()
            ->withInput()
            ->with('error', 'Gagal mengupdate Surat Keputusan: ' . $e->getMessage());
    }
}

    public function destroy($id)
    {
        $document = SuratKeputusan::findOrFail($id);
        
        if ($document->USER !== Auth::user()->BADGE) {
            abort(403, 'Unauthorized action.');
        }

        if ($document->isApproved()) {
            return redirect()->route('user.sk.index')
                ->with('error', 'Dokumen yang sudah disetujui tidak dapat dihapus');
        }

        try {
            // Soft delete
            $document->delete();

            UserLog::logDelete(Auth::user()->BADGE, 'Surat Keputusan', $document->NOMOR_SK);

            return redirect()->route('user.sk.index')
                ->with('success', 'Surat Keputusan berhasil dihapus');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus Surat Keputusan: ' . $e->getMessage());
        }
    }

    public function downloadPdf($id)
    {
        $document = SuratKeputusan::findOrFail($id);
        
        if ($document->USER !== Auth::user()->BADGE) {
            abort(403, 'Unauthorized action.');
        }

        if (!$document->pdf_path || !Storage::disk('minio')->exists($document->pdf_path)) {
            abort(404, 'File PDF tidak ditemukan');
        }

        return Storage::disk('minio')->download($document->pdf_path, 
            'SK_' . str_replace('/', '_', $document->NOMOR_SK) . '.pdf');
    }

    public function exportCsv()
    {
        $user = Auth::user();
        $documents = SuratKeputusan::byUser($user->BADGE)
            ->orderBy('TANGGAL', 'desc')
            ->orderBy('NOMOR_SK', 'desc')
            ->get()
            ->map(function ($item, $index) {
                $item->row_index = $index + 1;
                return $item;
            });

        return $this->csvExportService->exportDocuments($documents, 'sk');
    }
}
