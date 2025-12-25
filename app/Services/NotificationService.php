<?php

namespace App\Services;

use App\Models\UserNotification;
use App\Models\UserLog;

class NotificationService
{
    /**
     * Buat notifikasi baru
     */
    public function create(string $userBadge, string $title, string $message, string $type = 'info'): UserNotification
    {
        return UserNotification::create([
            'user_badge' => $userBadge,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'is_read' => false,
        ]);
    }

    /**
     * Notifikasi untuk approval
     */
    public function notifyApproval(string $userBadge, string $documentType, string $documentNo): void
    {
        $this->create(
            $userBadge,
            'Dokumen Disetujui',
            "Dokumen {$documentType} nomor {$documentNo} telah disetujui oleh admin.",
            'success'
        );
    }

    /**
     * Notifikasi untuk rejection
     */
    public function notifyRejection(string $userBadge, string $documentType, string $documentNo, string $reason): void
    {
        $this->create(
            $userBadge,
            'Dokumen Ditolak',
            "Dokumen {$documentType} nomor {$documentNo} ditolak. Alasan: {$reason}",
            'danger'
        );
    }

    /**
     * Notifikasi untuk pending approval (ke admin)
     */
    public function notifyPendingApproval(string $adminBadge, string $documentType, string $documentNo, string $userBadge): void
    {
        $this->create(
            $adminBadge,
            'Dokumen Menunggu Approval',
            "Dokumen {$documentType} nomor {$documentNo} dari user {$userBadge} menunggu persetujuan.",
            'warning'
        );
    }

    /**
     * Ambil notifikasi untuk user
     */
    public function getNotifications(string $userBadge, int $limit = null)
    {
        $query = UserNotification::forUser($userBadge)
            ->orderBy('created_at', 'desc');
        
        if ($limit) {
            $query->limit($limit);
        }
        
        return $query->get();
    }

    /**
     * Hitung notifikasi yang belum dibaca
     */
    public function getUnreadCount(string $userBadge): int
    {
        return UserNotification::forUser($userBadge)
            ->unread()
            ->count();
    }

    /**
     * Tandai notifikasi sebagai dibaca
     */
    public function markAsRead(int $notificationId): bool
    {
        $notification = UserNotification::find($notificationId);
        if ($notification) {
            $notification->markAsRead();
            return true;
        }
        return false;
    }

    /**
     * Tandai semua notifikasi user sebagai dibaca
     */
    public function markAllAsRead(string $userBadge): void
    {
        UserNotification::forUser($userBadge)
            ->unread()
            ->update(['is_read' => true]);
    }
}
