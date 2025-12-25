<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'badge',
        'activity',
        'details',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'badge', 'badge');
    }

    // Static helper methods
    public static function log(string $badge, string $activity, ?string $details = null): void
    {
        self::create([
            'badge' => $badge,
            'activity' => $activity,
            'details' => $details,
        ]);
    }

    public static function logLogin(string $badge, string $nama): void
    {
        self::log($badge, 'LOGIN', "User {$nama} berhasil login (badge: {$badge})");
    }

    public static function logLogout(string $badge, string $nama): void
    {
        self::log($badge, 'LOGOUT', "User {$nama} berhasil logout");
    }

    public static function logCreate(string $badge, string $documentType, string $documentNo): void
    {
        self::log($badge, 'CREATE', "Membuat dokumen {$documentType}: {$documentNo}");
    }

    public static function logUpdate(string $badge, string $documentType, string $documentNo): void
    {
        self::log($badge, 'UPDATE', "Mengubah dokumen {$documentType}: {$documentNo}");
    }

    public static function logDelete(string $badge, string $documentType, string $documentNo): void
    {
        self::log($badge, 'DELETE', "Menghapus dokumen {$documentType}: {$documentNo}");
    }

    public static function logApprove(string $badge, string $documentType, string $documentNo): void
    {
        self::log($badge, 'APPROVE', "Menyetujui dokumen {$documentType}: {$documentNo}");
    }

    public static function logReject(string $badge, string $documentType, string $documentNo, string $reason): void
    {
        self::log($badge, 'REJECT', "Menolak dokumen {$documentType}: {$documentNo}. Alasan: {$reason}");
    }
}
