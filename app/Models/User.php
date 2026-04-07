<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasUuids, HasRoles, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'active_branch_id',
        'name',
        'email',
        'phone',
        'avatar_url',
        'email_verified_at',
        'password',
        'is_super_admin',
        'status',
        'last_login_at',
        'last_login_ip',
        'preferences',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
            'is_super_admin' => 'boolean',
            'preferences' => 'array',
        ];
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function activeBranch()
    {
        return $this->belongsTo(Branch::class, 'active_branch_id');
    }

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'branch_user')
            ->withTimestamps();
    }
}
