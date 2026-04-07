<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantAware;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImportBatch extends Model
{
    use Auditable, HasFactory, HasUuids, SoftDeletes, TenantAware;

    protected $guarded = [];

    protected $casts = [
        'errors_log' => 'json',
        'mapping_config' => 'json',
        'meta' => 'json',
        'total_rows' => 'integer',
        'processed_rows' => 'integer',
        'success_count' => 'integer',
        'error_count' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function incrementProcessed()
    {
        $this->increment('processed_rows');
    }

    public function incrementSuccess()
    {
        $this->increment('success_count');
    }

    public function incrementError()
    {
        $this->increment('error_count');
    }

    public function addError(int $row, string $error)
    {
        $errors = $this->errors_log ?? [];
        $errors[] = ['row' => $row, 'error' => $error];
        $this->update(['errors_log' => $errors]);
    }
}
