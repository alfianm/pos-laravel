<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentGatewayConfig extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'provider',
        'name',
        'config',
        'is_active',
        'is_test_mode',
    ];

    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean',
        'is_test_mode' => 'boolean',
    ];
}
