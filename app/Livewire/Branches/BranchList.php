<?php

namespace App\Livewire\Branches;

use App\Models\Branch;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class BranchList extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.branches.branch-list', [
            'branches' => Branch::with('tenant')->latest()->paginate(10),
        ]);
    }
}
