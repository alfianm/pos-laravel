<?php

namespace App\Models;

use App\Traits\TenantAware;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    use HasFactory, HasUuids, TenantAware;

    protected $guarded = [];

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}
