<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantAware;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use Auditable, HasFactory, HasUuids, SoftDeletes, TenantAware;

    protected $guarded = [];

    protected $casts = [
        'converted_at' => 'datetime',
    ];

    public function source()
    {
        return $this->belongsTo(LeadSource::class, 'lead_source_id');
    }

    public function stage()
    {
        return $this->belongsTo(LeadStage::class, 'lead_stage_id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function followUps()
    {
        return $this->hasMany(FollowUp::class);
    }

    public function timelines()
    {
        return $this->hasMany(CustomerTimeline::class);
    }

    public function convertedCustomer()
    {
        return $this->belongsTo(Customer::class, 'converted_customer_id');
    }
}
