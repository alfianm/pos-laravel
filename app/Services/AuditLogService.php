<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogService
{
    public static function log(
        string $event,
        ?Model $auditable = null,
        ?array $oldValues = [],
        ?array $newValues = [],
        array|string $tags = []
    ): AuditLog {
        $user = Auth::user();

        return AuditLog::create([
            'tenant_id' => $user?->tenant_id,
            'branch_id' => $user?->active_branch_id,
            'user_id' => $user?->id,
            'event' => $event,
            'auditable_type' => $auditable ? get_class($auditable) : null,
            'auditable_id' => $auditable?->id,
            'old_values' => $oldValues ?: null,
            'new_values' => $newValues ?: null,
            'url' => Request::fullUrl(),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'tags' => is_array($tags) ? implode(',', $tags) : $tags,
        ]);
    }

    public static function created(Model $model, array $tags = []): AuditLog
    {
        return static::log(
            event: 'created',
            auditable: $model,
            newValues: $model->toArray(),
            tags: $tags
        );
    }

    public static function updated(Model $model, array $tags = []): AuditLog
    {
        return static::log(
            event: 'updated',
            auditable: $model,
            oldValues: $model->getOriginal(),
            newValues: $model->getChanges(),
            tags: $tags
        );
    }

    public static function deleted(Model $model, array $tags = []): AuditLog
    {
        return static::log(
            event: 'deleted',
            auditable: $model,
            oldValues: $model->toArray(),
            tags: $tags
        );
    }

    public static function login($user = null): AuditLog
    {
        return static::log(
            event: 'login',
            auditable: $user ?? Auth::user(),
            tags: ['auth', 'login']
        );
    }

    public static function logout($user = null): AuditLog
    {
        return static::log(
            event: 'logout',
            auditable: $user ?? Auth::user(),
            tags: ['auth', 'logout']
        );
    }

    public static function loginFailed(string $email, ?string $reason = null): AuditLog
    {
        return static::log(
            event: 'login_failed',
            newValues: array_filter([
                'email' => $email,
                'reason' => $reason,
            ]),
            tags: ['auth', 'login_failed']
        );
    }
}
