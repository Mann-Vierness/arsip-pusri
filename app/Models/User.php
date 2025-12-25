<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'user';
    protected $primaryKey = 'BADGE';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'BADGE',
        'Nama',
        'Password',
        'ROLE',
        'Departemen',
    ];

    protected $hidden = [
        'Password',
        'remember_token',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Override auth methods to use Password column
    public function getAuthPassword()
    {
        return $this->Password;
    }

    public function getAuthIdentifierName()
    {
        return 'BADGE';
    }

    public function getAuthIdentifier()
    {
        return $this->BADGE;
    }

    // Relationships
    public function suratKeputusan()
    {
        return $this->hasMany(SuratKeputusan::class, 'USER', 'BADGE');
    }

    public function suratPerjanjian()
    {
        return $this->hasMany(SuratPerjanjian::class, 'USER', 'BADGE');
    }

    public function suratAddendum()
    {
        return $this->hasMany(SuratAddendum::class, 'USER', 'BADGE');
    }

    public function notifications()
    {
        return $this->hasMany(UserNotification::class, 'user_badge', 'BADGE');
    }

    // Helper methods
    public function isAdmin(): bool
    {
        return $this->ROLE === 'admin';
    }

    public function isUser(): bool
    {
        return $this->ROLE === 'user';
    }
}
