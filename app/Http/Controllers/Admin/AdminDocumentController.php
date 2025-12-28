<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SuratKeputusan;
use App\Models\SuratPerjanjian;
use App\Models\SuratAddendum;
use App\Models\UserLog;
use App\Services\DocumentNumberService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminDocumentController extends Controller
{
    protected $documentNumberService;

    public function __construct(DocumentNumberService $documentNumberService)
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
        $this->documentNumberService = $documentNumberService;
    }

    // ==================== SURAT KEPUTUSAN ====================
    
    /**
     * Show form untuk create SK
     */
    public function createSK()
    {
        return view('admin.documents.create-sk');
    }

    /**
     * Store SK baru (auto-approved)
     */
    public function storeSK(Request $request)
    {
        $request->validate([
            'TANGGAL' => 'required|date',
            'PENANDATANGAN' => 'required|string|max:100',
            'PERIHAL' => 'required|string|max:500',
            'UNIT_KERJA' => 'required|string|max:100',
            'NAMA' => 'required|string|max:100',
            'pdf_file' => 'required|file|mimes:pdf|max:20480',
        ], [
            'TANGGAL.required' => 'Tanggal wajib diisi',
            'TANGGAL.date' => 'Format tanggal tidak valid',
            'PENANDATANGAN.required' => 'Penandatangan wajib diisi',
            'PERIHAL.required' => 'Perihal wajib diisi',
            'UNIT_KERJA.required' => 'Unit kerja wajib diisi',
            'NAMA.required' => 'Nama wajib diisi',
            'pdf_file.required' => 'File PDF wajib diupload',
            'pdf_file.mimes' => 'File harus berformat PDF',
            'pdf_file.max' => 'Ukuran file maksimal 20MB',
        ]);

        DB::beginTransaction();
        try {
            // Generate nomor SK
            $nomorSK = $this->documentNumberService->generateSKNumber($request->TANGGAL);

            // Upload PDF ke MinIO
            $pdfFile = $request->file('pdf_file');
            $pdfFileName = str_replace(['/', ' '], ['_', '_'], $nomorSK) . '.pdf';
            $pdfPath = $pdfFile->storeAs('surat-keputusan', $pdfFileName, 'minio');

            // Simpan SK dengan status approved
            SuratKeputusan::create([
                'NOMOR_SK' => $nomorSK,
                'TANGGAL' => $request->TANGGAL,
                'PERIHAL' => $request->PERIHAL,
                'PENANDATANGAN' => $request->PENANDATANGAN,
                'UNIT_KERJA' => $request->UNIT_KERJA,
                'NAMA' => $request->NAMA,
                'USER' => Auth::user()->BADGE,
                'pdf_path' => $pdfPath,
                'approval_status' => 'approved',
                'approved_by' => Auth::user()->BADGE,
                'approved_at' => now(),
            ]);

            // Log aktivitas
            UserLog::logCreate(Auth::user()->BADGE, 'Surat Keputusan', $nomorSK);
            
            DB::commit();

            return redirect()->route('admin.documents.sk')
                ->with('success', 'SK berhasil dibuat dan langsung approved dengan nomor: ' . $nomorSK);
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal membuat SK: ' . $e->getMessage());
        }
    }

    // ==================== SURAT PERJANJIAN ====================
    
    /**
     * Show form untuk create SP
     */
    public function createSP()
    {
        return view('admin.documents.create-sp');
    }

    /**
     * Store SP baru (auto-approved)
     */
   public function storeSP(Request $request)
{
    // Validasi dinamis berdasarkan DIR
    $rules = [
        'DIR' => 'required|string|in:DIR,NON DIR',
        'PIHAK_PERTAMA' => 'required|string|max:200',
        'PIHAK_LAIN' => 'required|string|max:200',
        'PERIHAL' => 'required|string|max:500',
        'PENANDATANGAN' => 'required|string|max:100',
        'UNIT_KERJA' => 'required|string|max:100',
        'NAMA' => 'required|string|max:100',
        'pdf_file' => 'required|file|mimes:pdf|max:20480',
    ];

    // Tambahkan validasi tanggal khusus untuk NON DIR
    if ($request->DIR === 'NON DIR') {
        $rules['TANGGAL'] = 'required|date|before_or_equal:today';
    } else {
        $rules['TANGGAL'] = 'required|date';
    }

    $request->validate($rules, [
        'TANGGAL.required' => 'Tanggal wajib diisi',
        'TANGGAL.before_or_equal' => 'Tanggal NON DIR maksimal hari ini',
        'DIR.required' => 'Jenis SP wajib dipilih',
        'DIR.in' => 'Jenis SP harus DIR atau NON DIR',
        'PIHAK_PERTAMA.required' => 'Pihak pertama wajib diisi',
        'PIHAK_LAIN.required' => 'Pihak lain wajib diisi',
        'PERIHAL.required' => 'Perihal wajib diisi',
        'PENANDATANGAN.required' => 'Penandatangan wajib diisi',
        'UNIT_KERJA.required' => 'Unit kerja wajib diisi',
        'NAMA.required' => 'Nama wajib diisi',
        'pdf_file.required' => 'File PDF wajib diupload',
        'pdf_file.mimes' => 'File harus berformat PDF',
        'pdf_file.max' => 'Ukuran file maksimal 20MB',
    ]);

    DB::beginTransaction();
    try {
        // Generate nomor SP
        $nomorSP = $this->documentNumberService->generateSPNumber($request->TANGGAL, $request->DIR);

        // Upload PDF ke MinIO
        $pdfFile = $request->file('pdf_file');
        $pdfFileName = str_replace(['/', ' '], ['_', '_'], $nomorSP) . '.pdf';
        $pdfPath = $pdfFile->storeAs('surat-perjanjian', $pdfFileName, 'minio');

        // Simpan SP dengan status approved
        SuratPerjanjian::create([
            'NO' => $nomorSP,
            'TANGGAL' => $request->TANGGAL,
            'DIR' => $request->DIR,
            'PIHAK_PERTAMA' => $request->PIHAK_PERTAMA,
            'PIHAK_LAIN' => $request->PIHAK_LAIN,
            'PERIHAL' => $request->PERIHAL,
            'PENANDATANGAN' => $request->PENANDATANGAN,
            'UNIT_KERJA' => $request->UNIT_KERJA,
            'NAMA' => $request->NAMA,
            'USER' => Auth::user()->BADGE,
            'pdf_path' => $pdfPath,
            'approval_status' => 'approved',
            'approved_by' => Auth::user()->BADGE,
            'approved_at' => now(),
        ]);

        // Log aktivitas
        UserLog::logCreate(Auth::user()->BADGE, 'Surat Perjanjian', $nomorSP);
        
        DB::commit();

        return redirect()->route('admin.documents.sp')
            ->with('success', 'SP berhasil dibuat dan langsung approved dengan nomor: ' . $nomorSP);
            
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->withInput()
            ->with('error', 'Gagal membuat SP: ' . $e->getMessage());
    }
}

    // ==================== SURAT ADDENDUM ====================
    
    /**
     * Show form untuk create Addendum
     */
    public function createAddendum()
    {
        return view('admin.documents.create-addendum');
    }

    /**
     * Store Addendum baru (auto-approved)
     */
    public function storeAddendum(Request $request)
    {
        $request->validate([
            'TANGGAL' => 'required|date',
            'DIR' => 'required|string|in:DIR,NON DIR',
            'PIHAK_PERTAMA' => 'required|string|max:200',
            'PIHAK_LAIN' => 'required|string|max:200',
            'PERIHAL' => 'required|string|max:500',
            'PENANDATANGAN' => 'required|string|max:100',
            'UNIT_KERJA' => 'required|string|max:100',
            'NAMA' => 'required|string|max:100',
            'pdf_file' => 'required|file|mimes:pdf|max:20480',
        ], [
            'TANGGAL.required' => 'Tanggal wajib diisi',
            'DIR.required' => 'Jenis DIR wajib dipilih',
            'DIR.in' => 'Jenis DIR harus DIR atau NON DIR',
            'PIHAK_PERTAMA.required' => 'Pihak pertama wajib diisi',
            'PIHAK_LAIN.required' => 'Pihak lain wajib diisi',
            'PERIHAL.required' => 'Perihal wajib diisi',
            'PENANDATANGAN.required' => 'Penandatangan wajib diisi',
            'UNIT_KERJA.required' => 'Unit kerja wajib diisi',
            'NAMA.required' => 'Nama wajib diisi',
            'pdf_file.required' => 'File PDF wajib diupload',
            'pdf_file.mimes' => 'File harus berformat PDF',
            'pdf_file.max' => 'Ukuran file maksimal 20MB',
        ]);

        DB::beginTransaction();
        try {
            // Generate nomor Addendum
            $nomorAddendum = $this->documentNumberService->generateAddendumNumber($request->TANGGAL, $request->DIR);

            // Upload PDF ke MinIO
            $pdfFile = $request->file('pdf_file');
            $pdfFileName = str_replace(['/', ' '], ['_', '_'], $nomorAddendum) . '.pdf';
            $pdfPath = $pdfFile->storeAs('surat-addendum', $pdfFileName, 'minio');

            // Simpan Addendum dengan status approved
            SuratAddendum::create([
                'NO' => $nomorAddendum,
                'TANGGAL' => $request->TANGGAL,
                'PIHAK_PERTAMA' => $request->PIHAK_PERTAMA,
                'PIHAK_LAIN' => $request->PIHAK_LAIN,
                'PERIHAL' => $request->PERIHAL,
                'PENANDATANGAN' => $request->PENANDATANGAN,
                'UNIT_KERJA' => $request->UNIT_KERJA,
                'NAMA' => $request->NAMA,
                'USER' => Auth::user()->BADGE,
                'pdf_path' => $pdfPath,
                'approval_status' => 'approved',
                'approved_by' => Auth::user()->BADGE,
                'approved_at' => now(),
            ]);

            // Log aktivitas
            UserLog::logCreate(Auth::user()->BADGE, 'Surat Addendum', $nomorAddendum);
            
            DB::commit();

            return redirect()->route('admin.documents.addendum')
                ->with('success', 'Addendum berhasil dibuat dan langsung approved dengan nomor: ' . $nomorAddendum);
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal membuat Addendum: ' . $e->getMessage());
        }
    }
}