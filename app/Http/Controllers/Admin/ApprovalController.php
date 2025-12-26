<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SuratKeputusan;
use App\Models\SuratPerjanjian;
use App\Models\SuratAddendum;
use App\Models\UserLog;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ApprovalController extends Controller
{
    public function viewPdf($type, $id)
    {
        $model = $this->getModel($type);
        $document = $model::findOrFail($id);

        if (!$document->pdf_path || !\Storage::disk('minio')->exists($document->pdf_path)) {
            abort(404, 'File PDF tidak ditemukan');
        }

        // Mendapatkan URL file PDF dari MinIO
        $pdfUrl = \Storage::disk('minio')->url($document->pdf_path);

        // Kirim ke view khusus preview PDF
        return view('admin.approval.pdf_preview', [
            'pdfUrl' => $pdfUrl,
            'type' => $type,
            'document' => $document,
        ]);
    }
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');
        $type = $request->get('type', 'all');

        $documents = collect();

        if ($type === 'all' || $type === 'sk') {
            $sk = SuratKeputusan::with('user')
                ->where('approval_status', $status)
                ->get()
                ->map(function ($doc) {
                    return [
                        'id' => $doc->id,
                        'type' => 'sk',
                        'type_text' => 'Surat Keputusan',
                        'nomor' => $doc->NOMOR_SK,
                        'tanggal' => $doc->TANGGAL,
                        'perihal' => $doc->PERIHAL,
                        'user' => $doc->user->Nama,
                        'user_badge' => $doc->USER,
                        'status' => $doc->approval_status,
                        'created_at' => $doc->created_at,
                        'approved_at' => $doc->approved_at,
                        'approved_by' => $doc->approved_by,
                        'rejection_reason' => $doc->rejection_reason,
                    ];
                });
            $documents = $documents->concat($sk);
        }

        if ($type === 'all' || $type === 'sp') {
            $sp = SuratPerjanjian::with('user')
                ->where('approval_status', $status)
                ->get()
                ->map(function ($doc) {
                    return [
                        'id' => $doc->id,
                        'type' => 'sp',
                        'type_text' => 'Surat Perjanjian',
                        'nomor' => $doc->NO,
                        'tanggal' => $doc->TANGGAL,
                        'perihal' => $doc->PERIHAL,
                        'user' => $doc->user->Nama,
                        'user_badge' => $doc->USER,
                        'status' => $doc->approval_status,
                        'created_at' => $doc->created_at,
                        'approved_at' => $doc->approved_at,
                        'approved_by' => $doc->approved_by,
                        'rejection_reason' => $doc->rejection_reason,
                    ];
                });
            $documents = $documents->concat($sp);
        }

        if ($type === 'all' || $type === 'addendum') {
            $addendum = SuratAddendum::with('user')
                ->where('approval_status', $status)
                ->get()
                ->map(function ($doc) {
                    return [
                        'id' => $doc->id,
                        'type' => 'addendum',
                        'type_text' => 'Addendum',
                        'nomor' => $doc->NO,
                        'tanggal' => $doc->TANGGAL,
                        'perihal' => $doc->PERIHAL,
                        'user' => $doc->user->Nama,
                        'user_badge' => $doc->USER,
                        'status' => $doc->approval_status,
                        'created_at' => $doc->created_at,
                        'approved_at' => $doc->approved_at,
                        'approved_by' => $doc->approved_by,
                        'rejection_reason' => $doc->rejection_reason,
                    ];
                });
            $documents = $documents->concat($addendum);
        }

        $documents = $documents->sortByDesc('created_at');

        return view('admin.approval.index', compact('documents', 'status', 'type'));
    }

    public function show($type, $id)
    {
        $document = $this->getDocument($type, $id);

        if (!$document) {
            abort(404, 'Dokumen tidak ditemukan');
        }

        return view('admin.approval.show', compact('document', 'type'));
    }

    public function approve($type, $id)
    {
        $model = $this->getModel($type);
        $document = $model::findOrFail($id);

        if (!$document->isPending()) {
            return redirect()->back()->with('error', 'Dokumen sudah di-approve/reject sebelumnya');
        }

        DB::beginTransaction();
        try {
            $document->update([
                'approval_status' => 'approved',
                'approved_by' => Auth::user()->BADGE,
                'approved_at' => now(),
                'rejection_reason' => null,
            ]);

            UserLog::logApprove(
                Auth::user()->BADGE,
                $this->getDocumentTypeName($type),
                $this->getDocumentNumber($document, $type)
            );

            $this->notificationService->notifyApproval(
                $document->USER,
                $this->getDocumentTypeName($type),
                $this->getDocumentNumber($document, $type)
            );

            DB::commit();

            return redirect()->route('admin.approval.index')
                ->with('success', 'Dokumen berhasil disetujui');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal approve dokumen: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, $type, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:10|max:500',
        ], [
            'rejection_reason.required' => 'Alasan penolakan wajib diisi',
            'rejection_reason.min' => 'Alasan penolakan minimal 10 karakter',
        ]);

        $model = $this->getModel($type);
        $document = $model::findOrFail($id);

        if (!$document->isPending()) {
            return redirect()->back()->with('error', 'Dokumen sudah di-approve/reject sebelumnya');
        }

        DB::beginTransaction();
        try {
            $document->update([
                'approval_status' => 'rejected',
                'approved_by' => Auth::user()->BADGE,
                'approved_at' => now(),
                'rejection_reason' => $request->rejection_reason,
            ]);

            UserLog::logReject(
                Auth::user()->BADGE,
                $this->getDocumentTypeName($type),
                $this->getDocumentNumber($document, $type),
                $request->rejection_reason
            );

            $this->notificationService->notifyRejection(
                $document->USER,
                $this->getDocumentTypeName($type),
                $this->getDocumentNumber($document, $type),
                $request->rejection_reason
            );

            DB::commit();

            return redirect()->route('admin.approval.index')
                ->with('success', 'Dokumen berhasil ditolak');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal reject dokumen: ' . $e->getMessage());
        }
    }

    public function downloadPdf($type, $id)
    {
        $model = $this->getModel($type);
        $document = $model::findOrFail($id);

        if (!$document->pdf_path || !Storage::disk('minio')->exists($document->pdf_path)) {
            return redirect()->back()->with('error', 'File PDF tidak ditemukan di server atau storage.');
        }

        // Ambil metadata file PDF via MinioHelper
        $metadata = \App\Helpers\MinioHelper::getFileMetadata($document->pdf_path);
        if (!$metadata || !$metadata['size']) {
            return redirect()->back()->with('error', 'Gagal mengambil metadata file dari MinIO. File mungkin tidak ada, atau akses MinIO/policy bermasalah.');
        }

        $nomor = $this->getDocumentNumber($document, $type);
        $fileName = strtoupper($type) . '_' . str_replace('/', '_', $nomor) . '.pdf';

        // Download file seperti biasa
        return Storage::disk('minio')->download($document->pdf_path, $fileName);
    }

    // Helper methods
    private function getModel($type)
    {
        return match($type) {
            'sk' => SuratKeputusan::class,
            'sp' => SuratPerjanjian::class,
            'addendum' => SuratAddendum::class,
            default => abort(404, 'Invalid document type')
        };
    }

    private function getDocument($type, $id)
    {
        $model = $this->getModel($type);
        return $model::with('user')->find($id);
    }

    private function getDocumentTypeName($type)
    {
        return match($type) {
            'sk' => 'Surat Keputusan',
            'sp' => 'Surat Perjanjian',
            'addendum' => 'Surat Addendum',
            default => 'Dokumen'
        };
    }

    private function getDocumentNumber($document, $type)
    {
        return match($type) {
            'sk' => $document->NOMOR_SK,
            'sp' => $document->NO,
            'addendum' => $document->NO,
            default => ''
        };
    }
}
