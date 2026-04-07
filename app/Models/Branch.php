<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantAware;
use Database\Factories\BranchFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    /** @use HasFactory<BranchFactory> */
    use Auditable, HasFactory, HasUuids, SoftDeletes, TenantAware;

    protected $fillable = [
        'tenant_id',
        'code',
        'name',
        'type',
        'email',
        'phone',
        'address',
        'city',
        'province',
        'postal_code',
        'is_main',
        'status',
        'settings',
    ];

    protected $casts = [
        'is_main' => 'boolean',
        'settings' => 'array',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'branch_user')
            ->withTimestamps();
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }
}
