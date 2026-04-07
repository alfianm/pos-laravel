<?php

namespace App\Traits;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait TenantAware
{
    public static function bootTenantAware()
    {
        static::creating(function ($model) {
            if (empty($model->tenant_id)) {
                $model->tenant_id = Auth::user()?->tenant_id;
            }
        });

        static::addGlobalScope('tenant', function (Builder $builder) {
            if (Auth::check()) {
                $user = Auth::user();
                
                // Allow super_admin to see everything, OR restrict them to their tenant too
                // For now, if super_admin is NOT assigned to a tenant, they see everything.
                // But usually, in multi-tenant, even super admins choose a tenant context.
                if (!$user->hasRole('super_admin') && $user->tenant_id) {
                    $builder->where($builder->getQuery()->from . '.tenant_id', $user->tenant_id);
                }
            }
        });
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
