<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SuratKeputusan;
use App\Models\SuratPerjanjian;
use App\Models\SuratAddendum;
use App\Models\User;
use App\Models\UserLog;
use App\Services\CsvExportService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    /**
     * Download SP PDF for admin panel
     */
    public function downloadSP($id)
    {
        $sp = SuratPerjanjian::findOrFail($id);
        if (!$sp->pdf_path) {
            return redirect()->back()->with('error', 'File PDF tidak ditemukan di database.');
        }
        if (!\Storage::disk('minio')->exists($sp->pdf_path)) {
            return redirect()->back()->with('error', 'File PDF tidak ditemukan di storage MinIO.');
        }
        $metadata = \App\Helpers\MinioHelper::getFileMetadata($sp->pdf_path);
        if (!$metadata || !$metadata['size']) {
            return redirect()->back()->with('error', 'Gagal mengambil metadata file dari MinIO. File mungkin tidak ada, atau akses MinIO/policy bermasalah.');
        }
        $fileName = 'SP_' . str_replace('/', '_', $sp->NO) . '.pdf';
        return \Storage::disk('minio')->download($sp->pdf_path, $fileName);
    }
    
    /**
     * Show detail Surat Perjanjian for admin
     */
    public function showSP($id)
    {
        $sp = SuratPerjanjian::findOrFail($id);
        return view('admin.documents.sp-detail', compact('sp'));
    }
    
    /**
     * Download SK PDF for admin panel
     */
    public function downloadSK($id)
    {
        $sk = SuratKeputusan::findOrFail($id);
        if (!$sk->pdf_path) {
            return redirect()->back()->with('error', 'File PDF tidak ditemukan di database.');
        }
        if (!\Storage::disk('minio')->exists($sk->pdf_path)) {
            return redirect()->back()->with('error', 'File PDF tidak ditemukan di storage MinIO.');
        }
        $metadata = \App\Helpers\MinioHelper::getFileMetadata($sk->pdf_path);
        if (!$metadata || !$metadata['size']) {
            return redirect()->back()->with('error', 'Gagal mengambil metadata file dari MinIO. File mungkin tidak ada, atau akses MinIO/policy bermasalah.');
        }
        $fileName = 'SK_' . str_replace('/', '_', $sk->NOMOR_SK) . '.pdf';
        return \Storage::disk('minio')->download($sk->pdf_path, $fileName);
    }
    
    /**
     * Show detail Surat Keputusan for admin
     */
    public function showSK($id)
    {
        $sk = SuratKeputusan::findOrFail($id);
        return view('admin.documents.sk-detail', compact('sk'));
    }
    
    /**
     * Show detail Addendum for admin
     */
    public function showAddendum($id)
    {
        $addendum = SuratAddendum::findOrFail($id);
        return view('admin.documents.addendum-detail', compact('addendum'));
    }
    
    protected $csvExportService;

    public function __construct(CsvExportService $csvExportService)
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
        $this->csvExportService = $csvExportService;
    }

    public function index()
    {
        $admin = Auth::user();

        // Total statistik
        $totalUsers = User::where('role', 'user')->count();
        $totalSK = SuratKeputusan::count();
        $totalSP = SuratPerjanjian::count();
        $totalAddendum = SuratAddendum::count();
        $totalDocuments = $totalSK + $totalSP + $totalAddendum;

        // Pending approval
        $pendingSK = SuratKeputusan::pending()->count();
        $pendingSP = SuratPerjanjian::pending()->count();
        $pendingAddendum = SuratAddendum::pending()->count();
        $totalPending = $pendingSK + $pendingSP + $pendingAddendum;

        // Approved & Rejected
        $approvedCount = SuratKeputusan::approved()->count() +
                        SuratPerjanjian::approved()->count() +
                        SuratAddendum::approved()->count();

        $rejectedCount = SuratKeputusan::rejected()->count() +
                        SuratPerjanjian::rejected()->count() +
                        SuratAddendum::rejected()->count();

        // Recent pending documents
        $pendingDocuments = collect();
        
        $skPending = SuratKeputusan::pending()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
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
                    'created_at' => $doc->created_at,
                ];
            });

        $spPending = SuratPerjanjian::pending()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
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
                    'created_at' => $doc->created_at,
                ];
            });

        $addendumPending = SuratAddendum::pending()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
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
                    'created_at' => $doc->created_at,
                ];
            });

        $pendingDocuments = $pendingDocuments->concat($skPending)
                                             ->concat($spPending)
                                             ->concat($addendumPending)
                                             ->sortByDesc('created_at')
                                             ->take(10);

        // Recent activity logs
        $recentLogs = UserLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Document statistics by month (last 6 months)
        $monthlyStats = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->format('Y-m');
            $monthName = $date->format('M Y');
            
            $skCount = SuratKeputusan::whereYear('TANGGAL', $date->year)
                                    ->whereMonth('TANGGAL', $date->month)
                                    ->count();
            
            $spCount = SuratPerjanjian::whereYear('TANGGAL', $date->year)
                                     ->whereMonth('TANGGAL', $date->month)
                                     ->count();
            
            $addCount = SuratAddendum::whereYear('TANGGAL', $date->year)
                                    ->whereMonth('TANGGAL', $date->month)
                                    ->count();
            
            $monthlyStats[] = [
                'month' => $monthName,
                'sk' => $skCount,
                'sp' => $spCount,
                'addendum' => $addCount,
                'total' => $skCount + $spCount + $addCount,
            ];
        }

        // Top users by document count
        $topUsers = User::where('role', 'user')
            ->withCount([
                'suratKeputusan',
                'suratPerjanjian',
                'suratAddendum'
            ])
            ->get()
            ->map(function ($user) {
                return [
                    'badge' => $user->badge,
                    'nama' => $user->Nama,
                    'departemen' => $user->departemen,
                    'total' => $user->surat_keputusan_count + 
                              $user->surat_perjanjian_count + 
                              $user->surat_addendum_count,
                ];
            })
            ->sortByDesc('total')
            ->take(5);

        return view('admin.dashboard', compact(
            'admin',
            'totalUsers',
            'totalSK',
            'totalSP',
            'totalAddendum',
            'totalDocuments',
            'pendingSK',
            'pendingSP',
            'pendingAddendum',
            'totalPending',
            'approvedCount',
            'rejectedCount',
            'pendingDocuments',
            'recentLogs',
            'monthlyStats',
            'topUsers'
        ));
    }

    public function allSK(Request $request)
    {
        $query = SuratKeputusan::with('user');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('NOMOR_SK', 'like', "%{$search}%")
                  ->orWhere('PERIHAL', 'like', "%{$search}%")
                  ->orWhere('NAMA', 'like', "%{$search}%")
                  ->orWhere('USER', 'like', "%{$search}%");
            });
        }

        $sort = $request->get('sort', 'desc');
        $documents = $query->orderBy('NOMOR_SK', strtoupper($sort))
            ->orderBy('TANGGAL', strtoupper($sort))
            ->paginate(20);

        return view('admin.documents.sk', compact('documents'));
    }

    public function allSP(Request $request)
    {
        $query = SuratPerjanjian::with('user');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('NO', 'like', "%{$search}%")
                  ->orWhere('PERIHAL', 'like', "%{$search}%")
                  ->orWhere('NAMA', 'like', "%{$search}%")
                  ->orWhere('USER', 'like', "%{$search}%");
            });
        }

        $sort = $request->get('sort', 'desc');
        $documents = $query->orderBy('NO', strtoupper($sort))
            ->orderBy('TANGGAL', strtoupper($sort))
            ->paginate(20);

        return view('admin.documents.sp', compact('documents'));
    }

    public function allAddendum(Request $request)
    {
        $query = SuratAddendum::with('user');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('NO', 'like', "%{$search}%")
                  ->orWhere('PERIHAL', 'like', "%{$search}%")
                  ->orWhere('NAMA', 'like', "%{$search}%")
                  ->orWhere('USER', 'like', "%{$search}%");
            });
        }

        $sort = $request->get('sort', 'desc');
        $documents = $query->orderBy('NO', strtoupper($sort))
            ->orderBy('TANGGAL', strtoupper($sort))
            ->paginate(20);

        return view('admin.documents.addendum', compact('documents'));
    }

    public function exportCsvSK(Request $request)
    {
        $query = SuratKeputusan::query();

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('NOMOR_SK', 'like', "%{$search}%")
                  ->orWhere('PERIHAL', 'like', "%{$search}%")
                  ->orWhere('NAMA', 'like', "%{$search}%")
                  ->orWhere('USER', 'like', "%{$search}%");
            });
        }

        $sort = $request->get('sort', 'desc');
        $documents = $query->orderBy('NOMOR_SK', strtoupper($sort))
            ->orderBy('TANGGAL', strtoupper($sort))
            ->get()
            ->map(function ($item, $index) {
                $item->row_index = $index + 1;
                return $item;
            });

        return $this->csvExportService->exportDocuments($documents, 'sk');
    }

    public function exportCsvSP(Request $request)
    {
        $query = SuratPerjanjian::query();

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('NO', 'like', "%{$search}%")
                  ->orWhere('PERIHAL', 'like', "%{$search}%")
                  ->orWhere('NAMA', 'like', "%{$search}%")
                  ->orWhere('USER', 'like', "%{$search}%");
            });
        }

        $sort = $request->get('sort', 'desc');
        $documents = $query->orderBy('NO', strtoupper($sort))
            ->orderBy('TANGGAL', strtoupper($sort))
            ->get()
            ->map(function ($item, $index) {
                $item->row_index = $index + 1;
                return $item;
            });

        return $this->csvExportService->exportDocuments($documents, 'sp');
    }

    public function exportCsvAddendum(Request $request)
    {
        $query = SuratAddendum::query();

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('NO', 'like', "%{$search}%")
                  ->orWhere('PERIHAL', 'like', "%{$search}%")
                  ->orWhere('NAMA', 'like', "%{$search}%")
                  ->orWhere('USER', 'like', "%{$search}%");
            });
        }

        $sort = $request->get('sort', 'desc');
        $documents = $query->orderBy('NO', strtoupper($sort))
            ->orderBy('TANGGAL', strtoupper($sort))
            ->get()
            ->map(function ($item, $index) {
                $item->row_index = $index + 1;
                return $item;
            });

        return $this->csvExportService->exportDocuments($documents, 'addendum');
    }
}