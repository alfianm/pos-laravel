<?php

namespace Tests\Feature\Livewire\MasterData;

use App\Livewire\MasterData\SupplierDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class SupplierDetailTest extends TestCase
{
    public function test_renders_successfully()
    {
        Livewire::test(SupplierDetail::class)
            ->assertStatus(200);
    }
}
