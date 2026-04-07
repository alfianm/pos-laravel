<?php

namespace App\Models;

use App\Traits\TenantAware;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadSource extends Model
{
    use HasFactory, HasUuids, TenantAware;

    protected $guarded = [];
}

// In same file for brevity if system allows or separate later. 
// For now I will focus on proper separation.
