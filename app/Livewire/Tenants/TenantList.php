<?php

namespace App\Livewire\Tenants;

use App\Models\Tenant;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class TenantList extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.tenants.tenant-list', [
            'tenants' => Tenant::latest()->paginate(10),
        ]);
    }
}
