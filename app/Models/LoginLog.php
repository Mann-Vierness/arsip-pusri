<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{
    protected $fillable = [
        'user_badge',
        'user_name',
        'role',
        'ip_address',
        'user_agent',
        'login_at',
        'logout_at',
    ];

    protected $casts = [
        'login_at' => 'datetime',
        'logout_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_badge', 'BADGE');
    }

    /**
     * Log user login
     */
    public static function logLogin($user, $request)
    {
        return self::create([
            'user_badge' => $user->BADGE,
            'user_name' => $user->Nama,
            'role' => $user->ROLE,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'login_at' => now(),
        ]);
    }

    /**
     * Log user logout
     */
    public static function logLogout($badge)
    {
        $log = self::where('user_badge', $badge)
            ->whereNull('logout_at')
            ->latest('login_at')
            ->first();

        if ($log) {
            $log->update(['logout_at' => now()]);
        }
    }

    /**
     * Get session duration in human readable format
     */
    public function getDurationAttribute()
    {
        if (!$this->logout_at) {
            return 'Online';
        }

        $diff = $this->login_at->diff($this->logout_at);
        return sprintf('%02d:%02d:%02d', $diff->h, $diff->i, $diff->s);
    }
}
