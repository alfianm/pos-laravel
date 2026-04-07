<?php

namespace App\Models;

use Database\Factories\TenantFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Tenant extends Model
{
    /** @use HasFactory<TenantFactory> */
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'id',
        'name',
        'code',
        'slug',
        'email',
        'phone',
        'logo_url',
        'currency',
        'timezone',
        'tax_number',
        'address',
        'city',
        'province',
        'postal_code',
        'status',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tenant) {
            if (! $tenant->slug) {
                $tenant->slug = Str::slug($tenant->name);
            }
        });
    }

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(TenantSubscription::class);
    }

    public function quotas()
    {
        return $this->hasMany(TenantQuota::class);
    }

    public function domains()
    {
        return $this->hasMany(TenantDomain::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(TenantSubscription::class)->whereIn('status', ['active', 'trial']);
    }
}
