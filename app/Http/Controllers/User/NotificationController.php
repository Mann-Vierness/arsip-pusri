<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
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
        $notifications = $this->notificationService->getNotifications($user->badge);
        $unreadCount = $this->notificationService->getUnreadCount($user->badge);

        return view('user.notifications', compact('notifications', 'unreadCount'));
    }

    public function markAsRead($id)
    {
        $this->notificationService->markAsRead($id);

        return redirect()->back()->with('success', 'Notifikasi telah ditandai sebagai dibaca');
    }

    public function markAllAsRead()
    {
        $user = Auth::user();
        $this->notificationService->markAllAsRead($user->badge);

        return redirect()->back()->with('success', 'Semua notifikasi telah ditandai sebagai dibaca');
    }
}
