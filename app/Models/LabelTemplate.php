<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantAware;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabelTemplate extends Model
{
    use Auditable, HasFactory, HasUuids, SoftDeletes, TenantAware;

    protected $table = 'label_templates';

    protected $fillable = [
        'tenant_id',
        'name',
        'type',
        'content',
        'size',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'content' => 'json',
        'size' => 'json',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeForType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function setAsDefault()
    {
        // Remove default from other templates of same type
        self::where('tenant_id', $this->tenant_id)
            ->where('type', $this->type)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        $this->update(['is_default' => true]);
    }
}
