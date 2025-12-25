<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SuratKeputusan;
use App\Models\SuratPerjanjian;
use App\Models\SuratAddendum;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->middleware('auth');
        $this->middleware('role:user');
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        $user = Auth::user();
        $badge = $user->BADGE;

        // Statistik dokumen
        $skCount = SuratKeputusan::byUser($badge)->count();
        $spCount = SuratPerjanjian::byUser($badge)->count();
        $addendumCount = SuratAddendum::byUser($badge)->count();

        // Status approval
        $pendingCount = SuratKeputusan::byUser($badge)->pending()->count() +
                       SuratPerjanjian::byUser($badge)->pending()->count() +
                       SuratAddendum::byUser($badge)->pending()->count();

        $approvedCount = SuratKeputusan::byUser($badge)->approved()->count() +
                        SuratPerjanjian::byUser($badge)->approved()->count() +
                        SuratAddendum::byUser($badge)->approved()->count();

        $rejectedCount = SuratKeputusan::byUser($badge)->rejected()->count() +
                        SuratPerjanjian::byUser($badge)->rejected()->count() +
                        SuratAddendum::byUser($badge)->rejected()->count();

        // Dokumen terbaru
        $recentDocs = collect();
        
        $skRecent = SuratKeputusan::byUser($badge)
            ->orderBy('TANGGAL', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($doc) {
                return [
                    'jenis' => 'sk',
                    'jenis_text' => 'Surat Keputusan',
                    'nomor' => $doc->NOMOR_SK,
                    'tanggal' => $doc->TANGGAL,
                    'perihal' => $doc->PERIHAL,
                    'approval_status' => $doc->approval_status,
                    'status_class' => $doc->getStatusBadgeClass(),
                ];
            });

        $spRecent = SuratPerjanjian::byUser($badge)
            ->orderBy('TANGGAL', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($doc) {
                return [
                    'jenis' => 'sp',
                    'jenis_text' => 'Surat Perjanjian',
                    'nomor' => $doc->NO,
                    'tanggal' => $doc->TANGGAL,
                    'perihal' => $doc->PERIHAL,
                    'approval_status' => $doc->approval_status,
                    'status_class' => $doc->getStatusBadgeClass(),
                ];
            });

        $addendumRecent = SuratAddendum::byUser($badge)
            ->orderBy('TANGGAL', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($doc) {
                return [
                    'jenis' => 'addendum',
                    'jenis_text' => 'Addendum',
                    'nomor' => $doc->NO,
                    'tanggal' => $doc->TANGGAL,
                    'perihal' => $doc->PERIHAL,
                    'approval_status' => $doc->approval_status,
                    'status_class' => $doc->getStatusBadgeClass(),
                ];
            });

        $recentDocs = $recentDocs->concat($skRecent)
                                 ->concat($spRecent)
                                 ->concat($addendumRecent)
                                 ->sortByDesc('tanggal')
                                 ->take(5);

        // Notifikasi
        $recentNotifications = $this->notificationService->getNotifications($badge, 5);
        $unreadCount = $this->notificationService->getUnreadCount($badge);

        // Info batas maksimal input

        $maxPending = config('surat.max_user_pending_documents', 10);
        $pendingSK = SuratKeputusan::byUser($badge)->where('approval_status', 'pending')->count();
        $pendingSP = SuratPerjanjian::byUser($badge)->where('approval_status', 'pending')->count();
        $pendingAdd = SuratAddendum::byUser($badge)->where('approval_status', 'pending')->count();
        $pendingLimit = $maxPending;
        $pendingUsed = $pendingSK + $pendingSP + $pendingAdd;
        $pendingSisa = max(0, $pendingLimit - $pendingUsed);

        $approvedSK = SuratKeputusan::byUser($badge)->where('approval_status', 'approved')->count();
        $approvedSP = SuratPerjanjian::byUser($badge)->where('approval_status', 'approved')->count();
        $approvedAdd = SuratAddendum::byUser($badge)->where('approval_status', 'approved')->count();

        $rejectedSK = SuratKeputusan::byUser($badge)->where('approval_status', 'rejected')->count();
        $rejectedSP = SuratPerjanjian::byUser($badge)->where('approval_status', 'rejected')->count();
        $rejectedAdd = SuratAddendum::byUser($badge)->where('approval_status', 'rejected')->count();

        return view('user.dashboard', compact(
            'user',
            'skCount',
            'spCount',
            'addendumCount',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'recentDocs',
            'recentNotifications',
            'unreadCount',
            'pendingLimit',
            'pendingUsed',
            'pendingSisa',
            'pendingSK', 'pendingSP', 'pendingAdd',
            'approvedSK', 'approvedSP', 'approvedAdd',
            'rejectedSK', 'rejectedSP', 'rejectedAdd'
        ));
    }
}
