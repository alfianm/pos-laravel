<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TenantDomain extends Model
{
    /** @use HasFactory */
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'id',
        'tenant_id',
        'domain',
        'is_primary',
        'is_verified',
        'is_active',
        'ssl_status',
        'ssl_certificate_info',
        'last_checked_at',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
        'ssl_certificate_info' => 'array',
        'last_checked_at' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }
}
