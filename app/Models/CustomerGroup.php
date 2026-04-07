<?php

namespace App\Models;

use App\Traits\TenantAware;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerGroup extends Model
{
    use HasFactory, HasUuids, SoftDeletes, TenantAware;

    protected $fillable = [
        'tenant_id',
        'name',
        'description',
        'discount_percentage',
    ];

    protected $casts = [
        'discount_percentage' => 'decimal:2',
    ];

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
}
