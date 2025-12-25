<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SuratPerjanjian;
use App\Models\User;
use App\Models\UserLog;
use App\Services\DocumentNumberService;
use App\Services\NotificationService;
use App\Services\CsvExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SuratPerjanjianController extends Controller
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
        $query = SuratPerjanjian::byUser($user->BADGE);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('NO', 'like', "%{$search}%")
                  ->orWhere('PERIHAL', 'like', "%{$search}%");
            });
        }

        // Sort functionality
        $sort = $request->get('sort', 'desc');
        $query->orderBy('NO', strtoupper($sort))
              ->orderBy('TANGGAL', strtoupper($sort));

        $documents = $query->paginate(20);

        return view('user.sp.index', compact('documents'));
    }

    public function create()
    {
        return view('user.sp.create');
    }

    public function store(Request $request)
    {
        // Batasi input jika pending sudah maksimal
        $maxPending = config('surat.max_user_pending_documents', 10);
        $pendingCount = SuratPerjanjian::where('USER', Auth::user()->BADGE)
            ->where('approval_status', 'pending')->count();
        if ($pendingCount >= $maxPending) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Maksimal input Surat Perjanjian (pending) sudah tercapai. Silakan tunggu persetujuan admin atau hubungi admin untuk menambah batas.');
        }
        $request->validate([
            'TANGGAL' => ($request->DIR === 'NON DIR') ? 'required|date|before_or_equal:today' : 'required|date',
            'DIR' => 'required|string|in:DIR,NON DIR',
            'PIHAK_PERTAMA' => 'required|string|max:200',
            'PIHAK_LAIN' => 'required|string|max:200',
            'PERIHAL' => 'required|string|max:500',
            'PENANDATANGAN' => 'required|string|max:100',
            'UNIT_KERJA' => 'required|string|max:100',
            'NAMA' => 'required|string|max:100',
            'pdf_file' => 'required|file|mimes:pdf|max:20480',
        ]);

        try {
            $nomorSP = $this->documentNumberService->generateSPNumber($request->TANGGAL, $request->DIR);

            $pdfPath = null;
            if ($request->hasFile('pdf_file')) {
                $file = $request->file('pdf_file');
                $fileName = 'SP_' . str_replace(['/', ' '], '_', $nomorSP) . '_' . time() . '.pdf';
                $pdfPath = Storage::disk('minio')->putFileAs('surat-perjanjian', $file, $fileName);
            }

            $document = SuratPerjanjian::create([
                'NO' => $nomorSP,
                'TANGGAL' => $request->TANGGAL,
                'PIHAK_PERTAMA' => $request->PIHAK_PERTAMA,
                'PIHAK_LAIN' => $request->PIHAK_LAIN,
                'PERIHAL' => $request->PERIHAL,
                'PENANDATANGAN' => $request->PENANDATANGAN,
                'UNIT_KERJA' => $request->UNIT_KERJA,
                'NAMA' => $request->NAMA,
                'USER' => Auth::user()->BADGE,
                'pdf_path' => $pdfPath,
                'approval_status' => 'pending',
            ]);

            UserLog::logCreate(Auth::user()->BADGE, 'Surat Perjanjian', $nomorSP);

            $admins = User::where('ROLE', 'admin')->get();
            foreach ($admins as $admin) {
                $this->notificationService->notifyPendingApproval(
                    $admin->BADGE,
                    'Surat Perjanjian',
                    $nomorSP,
                    Auth::user()->BADGE
                );
            }

            return redirect()->route('user.sp.index')
                ->with('success', 'Surat Perjanjian berhasil dibuat dengan nomor: ' . $nomorSP);

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal membuat Surat Perjanjian: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $document = SuratPerjanjian::findOrFail($id);
        
        if ($document->USER !== Auth::user()->BADGE) {
            abort(403, 'Unauthorized action.');
        }

        return view('user.sp.show', compact('document'));
    }

    public function edit($id)
    {
        $document = SuratPerjanjian::findOrFail($id);
        
        if ($document->USER !== Auth::user()->BADGE) {
            abort(403, 'Unauthorized action.');
        }

        if ($document->isApproved()) {
            return redirect()->route('user.sp.index')
                ->with('error', 'Dokumen yang sudah disetujui tidak dapat diubah');
        }
        // Boleh edit jika status rejected

        return view('user.sp.edit', compact('document'));
    }

    public function update(Request $request, $id)
    {
        $document = SuratPerjanjian::findOrFail($id);
        
        if ($document->USER !== Auth::user()->BADGE) {
            abort(403, 'Unauthorized action.');
        }

        if ($document->isApproved()) {
            return redirect()->route('user.sp.index')
                ->with('error', 'Dokumen yang sudah disetujui tidak dapat diubah');
        }

        $request->validate([
            'PIHAK_PERTAMA' => 'required|string|max:200',
            'PIHAK_LAIN' => 'required|string|max:200',
            'PERIHAL' => 'required|string|max:500',
            'PENANDATANGAN' => 'required|string|max:100',
            'UNIT_KERJA' => 'required|string|max:100',
            'NAMA' => 'required|string|max:100',
            'pdf_file' => 'nullable|file|mimes:pdf|max:20480',
        ]);

        try {
            $data = [
                'PIHAK_PERTAMA' => $request->PIHAK_PERTAMA,
                'PIHAK_LAIN' => $request->PIHAK_LAIN,
                'PERIHAL' => $request->PERIHAL,
                'PENANDATANGAN' => $request->PENANDATANGAN,
                'UNIT_KERJA' => $request->UNIT_KERJA,
                'NAMA' => $request->NAMA,
            ];

            if ($request->hasFile('pdf_file')) {
                if ($document->pdf_path) {
                    Storage::disk('minio')->delete($document->pdf_path);
                }

                $file = $request->file('pdf_file');
                $fileName = 'SP_' . str_replace(['/', ' '], '_', $document->NO) . '_' . time() . '.pdf';
                $data['pdf_path'] = Storage::disk('minio')->putFileAs('surat-perjanjian', $file, $fileName);
            }

            $document->update($data);

            UserLog::logUpdate(Auth::user()->BADGE, 'Surat Perjanjian', $document->NO);

            return redirect()->route('user.sp.index')
                ->with('success', 'Surat Perjanjian berhasil diupdate');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate Surat Perjanjian: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $document = SuratPerjanjian::findOrFail($id);
        
        if ($document->USER !== Auth::user()->BADGE) {
            abort(403, 'Unauthorized action.');
        }

        if ($document->isApproved()) {
            return redirect()->route('user.sp.index')
                ->with('error', 'Dokumen yang sudah disetujui tidak dapat dihapus');
        }

        try {
            $document->delete();

            UserLog::logDelete(Auth::user()->BADGE, 'Surat Perjanjian', $document->NO);

            return redirect()->route('user.sp.index')
                ->with('success', 'Surat Perjanjian berhasil dihapus');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus Surat Perjanjian: ' . $e->getMessage());
        }
    }

    public function downloadPdf($id)
    {
        $document = SuratPerjanjian::findOrFail($id);
        
        if ($document->USER !== Auth::user()->BADGE) {
            abort(403, 'Unauthorized action.');
        }

        if (!$document->pdf_path || !Storage::disk('minio')->exists($document->pdf_path)) {
            abort(404, 'File PDF tidak ditemukan');
        }

        return Storage::disk('minio')->download($document->pdf_path, 
            'SP_' . str_replace('/', '_', $document->NO) . '.pdf');
    }

    public function exportCsv()
    {
        $user = Auth::user();
        $documents = SuratPerjanjian::byUser($user->BADGE)
            ->orderBy('TANGGAL', 'desc')
            ->orderBy('NOMOR_SP', 'desc')
            ->get()
            ->map(function ($item, $index) {
                $item->row_index = $index + 1;
                return $item;
            });

        return $this->csvExportService->exportDocuments($documents, 'sp');
    }
}
