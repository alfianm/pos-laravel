<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantAware;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountCategory extends Model
{
    use Auditable, HasFactory, HasUuids, SoftDeletes, TenantAware;

    protected $table = 'account_categories';

    protected $fillable = [
        'tenant_id',
        'code',
        'name',
        'type',
        'normal_balance',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'normal_balance' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function chartOfAccounts()
    {
        return $this->hasMany(ChartOfAccount::class, 'category_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('code');
    }

    public function isDebitNormal(): bool
    {
        return $this->normal_balance === 1;
    }

    public function isCreditNormal(): bool
    {
        return $this->normal_balance === -1;
    }
}
