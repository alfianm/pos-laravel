<?php

namespace App\Models;

use App\Traits\TenantAware;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class WebhookInboundLog extends Model
{
    use HasUuids, TenantAware;

    protected $fillable = [
        'tenant_id',
        'source',
        'event_type',
        'payload',
        'response_payload',
        'status_code',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'payload' => 'array',
        'response_payload' => 'array',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
