<?php

namespace Tests\Feature\Livewire\MasterData;

use App\Livewire\MasterData\CustomerGroupList;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class CustomerGroupListTest extends TestCase
{
    public function test_renders_successfully()
    {
        Livewire::test(CustomerGroupList::class)
            ->assertStatus(200);
    }
}
