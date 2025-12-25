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
    public function createSK()
    {
        return view('admin.documents.create-sk');
    }

    public function storeSK(Request $request)
    {
        $request->validate([
            'TANGGAL' => 'required|date',
            'PENANDATANGAN' => 'required|string|max:100',
            'PERIHAL' => 'required|string|max:500',
            'UNIT_KERJA' => 'required|string|max:100',
            'NAMA' => 'required|string|max:100',
            'pdf_file' => 'required|file|mimes:pdf|max:20480',
        ]);

        DB::beginTransaction();
        try {
            $nomorSK = $this->documentNumberService->generateSKNumber($request->TANGGAL);

            $pdfFile = $request->file('pdf_file');
            $pdfFileName = 'SK_' . str_replace(['/', ' '], ['_', '_'], $nomorSK) . '.pdf';
            $pdfPath = $pdfFile->storeAs('surat-keputusan', $pdfFileName, 'minio');

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

            UserLog::logCreate(Auth::user()->BADGE, 'Surat Keputusan', $nomorSK);
            DB::commit();

            return redirect()->route('admin.documents.sk')
                ->with('success', 'SK berhasil dibuat dan langsung approved!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    // ==================== SURAT PERJANJIAN ====================
    public function createSP()
    {
        return view('admin.documents.create-sp');
    }

    public function storeSP(Request $request)
    {
        $request->validate([
            'TANGGAL' => 'required|date',
            'DIR' => 'nullable|string|max:50',
            'PIHAK_PERTAMA' => 'required|string|max:200',
            'PIHAK_LAIN' => 'required|string|max:200',
            'PERIHAL' => 'required|string|max:500',
            'PENANDATANGAN' => 'required|string|max:100',
            'UNIT_KERJA' => 'required|string|max:100',
            'NAMA' => 'required|string|max:100',
            'pdf_file' => 'required|file|mimes:pdf|max:20480',
        ]);

        DB::beginTransaction();
        try {
            $nomorSP = $this->documentNumberService->generateSPNumber($request->TANGGAL, $request->DIR);

            $pdfFile = $request->file('pdf_file');
            $pdfFileName = 'SP_' . str_replace(['/', ' '], ['_', '_'], $nomorSP) . '.pdf';
            $pdfPath = $pdfFile->storeAs('surat-perjanjian', $pdfFileName, 'minio');

            SuratPerjanjian::create([
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
                'approval_status' => 'approved',
                'approved_by' => Auth::user()->BADGE,
                'approved_at' => now(),
            ]);

            UserLog::logCreate(Auth::user()->BADGE, 'Surat Perjanjian', $nomorSP);
            DB::commit();

            return redirect()->route('admin.documents.sp')
                ->with('success', 'SP berhasil dibuat dan langsung approved!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    // ==================== SURAT ADDENDUM ====================
    public function createAddendum()
    {
        return view('admin.documents.create-addendum');
    }

    public function storeAddendum(Request $request)
    {
        $request->validate([
            'TANGGAL' => 'required|date',
            'DIR' => 'nullable|string|max:50',
            'NOMOR_PERJANJIAN_ASAL' => 'required|string|max:100',
            'PIHAK_PERTAMA' => 'required|string|max:200',
            'PIHAK_LAIN' => 'required|string|max:200',
            'PERIHAL' => 'required|string|max:500',
            'PERUBAHAN' => 'required|string',
            'PENANDATANGAN' => 'required|string|max:100',
            'UNIT_KERJA' => 'required|string|max:100',
            'NAMA' => 'required|string|max:100',
            'pdf_file' => 'required|file|mimes:pdf|max:20480',
        ]);

        DB::beginTransaction();
        try {
            $nomorAddendum = $this->documentNumberService->generateAddendumNumber($request->TANGGAL, $request->DIR);

            $pdfFile = $request->file('pdf_file');
            $pdfFileName = 'ADD_' . str_replace(['/', ' '], ['_', '_'], $nomorAddendum) . '.pdf';
            $pdfPath = $pdfFile->storeAs('surat-addendum', $pdfFileName, 'minio');

            SuratAddendum::create([
                'NO' => $nomorAddendum,
                'TANGGAL' => $request->TANGGAL,
                'NOMOR_PERJANJIAN_ASAL' => $request->NOMOR_PERJANJIAN_ASAL,
                'PIHAK_PERTAMA' => $request->PIHAK_PERTAMA,
                'PIHAK_LAIN' => $request->PIHAK_LAIN,
                'PERIHAL' => $request->PERIHAL,
                'PERUBAHAN' => $request->PERUBAHAN,
                'PENANDATANGAN' => $request->PENANDATANGAN,
                'UNIT_KERJA' => $request->UNIT_KERJA,
                'NAMA' => $request->NAMA,
                'USER' => Auth::user()->BADGE,
                'pdf_path' => $pdfPath,
                'approval_status' => 'approved',
                'approved_by' => Auth::user()->BADGE,
                'approved_at' => now(),
            ]);

            UserLog::logCreate(Auth::user()->BADGE, 'Surat Addendum', $nomorAddendum);
            DB::commit();

            return redirect()->route('admin.documents.addendum')
                ->with('success', 'Addendum berhasil dibuat dan langsung approved!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal: ' . $e->getMessage());
        }
    }
}
