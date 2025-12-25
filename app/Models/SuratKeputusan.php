<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SuratKeputusan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'surat_keputusan';

    protected $fillable = [
        'NOMOR_SK',
        'TANGGAL',
        'PERIHAL',
        'PENANDATANGAN',
        'UNIT_KERJA',
        'NAMA',
        'USER',
        'pdf_path',
        'approval_status',
        'approved_by',
        'approved_at',
        'rejection_reason',
    ];

    protected $casts = [
        'TANGGAL' => 'date',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'USER', 'BADGE');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by', 'BADGE');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('approval_status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('approval_status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('approval_status', 'rejected');
    }

    public function scopeByUser($query, $badge)
    {
        return $query->where('USER', $badge);
    }

    // Helper methods
    public function isPending(): bool
    {
        return $this->approval_status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->approval_status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->approval_status === 'rejected';
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->approval_status) {
            'pending' => 'bg-warning',
            'approved' => 'bg-success',
            'rejected' => 'bg-danger',
            default => 'bg-secondary'
        };
    }
}
