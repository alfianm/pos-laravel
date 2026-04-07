<?php

namespace Tests\Feature\Livewire\MasterData;

use App\Livewire\MasterData\ProductDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class ProductDetailTest extends TestCase
{
    public function test_renders_successfully()
    {
        Livewire::test(ProductDetail::class)
            ->assertStatus(200);
    }
}
