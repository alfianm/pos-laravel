<?php

namespace App\Traits;

use App\Services\AuditLogService;

trait Auditable
{
    protected bool $auditEnabled = true;

    protected array $auditTags = [];

    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            if ($model->isAuditEnabled()) {
                AuditLogService::created($model, $model->getAuditTags('created'));
            }
        });

        static::updated(function ($model) {
            if ($model->isAuditEnabled()) {
                AuditLogService::updated($model, $model->getAuditTags('updated'));
            }
        });

        static::deleted(function ($model) {
            if ($model->isAuditEnabled()) {
                AuditLogService::deleted($model, $model->getAuditTags('deleted'));
            }
        });
    }

    public function isAuditEnabled(): bool
    {
        return $this->auditEnabled;
    }

    public function disableAudit(): static
    {
        $this->auditEnabled = false;

        return $this;
    }

    public function enableAudit(): static
    {
        $this->auditEnabled = true;

        return $this;
    }

    public function setAuditTags(array $tags): static
    {
        $this->auditTags = $tags;

        return $this;
    }

    protected function getAuditTags(string $event): array
    {
        return array_unique(array_merge(
            [$event, strtolower(class_basename($this))],
            $this->auditTags
        ));
    }

    public function getExcludedAuditAttributes(): array
    {
        return property_exists($this, 'auditExcluded') ? $this->auditExcluded : [];
    }
}
