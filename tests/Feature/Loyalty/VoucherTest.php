<?php

use App\Livewire\POS\Index as POSIndex;
use App\Models\Branch;
use App\Models\CashRegisterSession;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Voucher;

beforeEach(function () {
    $this->tenant = Tenant::factory()->create();
    $this->branch = Branch::factory()->create(['tenant_id' => $this->tenant->id]);
    $this->user = User::factory()->create([
        'tenant_id' => $this->tenant->id,
        'active_branch_id' => $this->branch->id,
    ]);

    CashRegisterSession::create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'user_id' => $this->user->id,
        'opened_at' => now(),
        'opening_balance' => 100000,
        'status' => 'open',
    ]);

    $this->actingAs($this->user);
});

describe('Voucher - Fixed Discount', function () {
    it('applies fixed voucher correctly', function () {
        Voucher::create([
            'tenant_id' => $this->tenant->id,
            'code' => 'FIXED50K',
            'type' => 'fixed',
            'value' => 50000,
            'min_order_amount' => 100000,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDays(30),
        ]);

        Livewire::test(POSIndex::class)
            ->set('voucher_code', 'FIXED50K')
            ->call('applyVoucher')
            ->assertSessionHas('success')
            ->assertSet('applied_voucher.code', 'FIXED50K')
            ->assertSet('discount', 50000);
    });

    it('rejects voucher with insufficient order amount', function () {
        Voucher::create([
            'tenant_id' => $this->tenant->id,
            'code' => 'MIN200K',
            'type' => 'fixed',
            'value' => 50000,
            'min_order_amount' => 200000,
            'starts_at' => now()->subDay(),
        ]);

        Livewire::test(POSIndex::class)
            ->set('voucher_code', 'MIN200K')
            ->set('subtotal', 100000)
            ->call('applyVoucher')
            ->assertSet('voucher_error', 'Minimal order Rp200.000 tidak tercapai.')
            ->assertSet('applied_voucher', null);
    });
});

describe('Voucher - Percentage Discount', function () {
    it('applies percentage voucher correctly', function () {
        Voucher::create([
            'tenant_id' => $this->tenant->id,
            'code' => 'DISKON20',
            'type' => 'percentage',
            'value' => 20,
            'min_order_amount' => 0,
            'starts_at' => now()->subDay(),
        ]);

        Livewire::test(POSIndex::class)
            ->set('voucher_code', 'DISKON20')
            ->set('subtotal', 100000)
            ->call('applyVoucher')
            ->assertSessionHas('success')
            ->assertSet('applied_voucher.code', 'DISKON20')
            ->assertSet('discount', 20000);
    });

    it('calculates percentage discount based on subtotal', function () {
        Voucher::create([
            'tenant_id' => $this->tenant->id,
            'code' => 'DISKON10',
            'type' => 'percentage',
            'value' => 10,
            'min_order_amount' => 0,
            'starts_at' => now()->subDay(),
        ]);

        $pos = Livewire::test(POSIndex::class)
            ->set('voucher_code', 'DISKON10')
            ->set('subtotal', 250000)
            ->call('applyVoucher');

        expect($pos->get('discount'))->toBe(25000.0);
    });
});

describe('Voucher - Validity', function () {
    it('rejects voucher that is not yet valid', function () {
        Voucher::create([
            'tenant_id' => $this->tenant->id,
            'code' => 'FUTURE',
            'type' => 'fixed',
            'value' => 10000,
            'starts_at' => now()->addDays(7),
        ]);

        Livewire::test(POSIndex::class)
            ->set('voucher_code', 'FUTURE')
            ->call('applyVoucher')
            ->assertSet('voucher_error', 'Voucher belum berlaku.');
    });

    it('rejects expired voucher', function () {
        Voucher::create([
            'tenant_id' => $this->tenant->id,
            'code' => 'EXPIRED',
            'type' => 'fixed',
            'value' => 10000,
            'starts_at' => now()->subDays(30),
            'ends_at' => now()->subDay(),
        ]);

        Livewire::test(POSIndex::class)
            ->set('voucher_code', 'EXPIRED')
            ->call('applyVoucher')
            ->assertSet('voucher_error', 'Voucher sudah kadaluwarsa.');
    });

    it('rejects voucher that reached usage limit', function () {
        Voucher::create([
            'tenant_id' => $this->tenant->id,
            'code' => 'LIMITREACHED',
            'type' => 'fixed',
            'value' => 10000,
            'usage_limit' => 10,
            'used_count' => 10,
            'starts_at' => now()->subDay(),
        ]);

        Livewire::test(POSIndex::class)
            ->set('voucher_code', 'LIMITREACHED')
            ->call('applyVoucher')
            ->assertSet('voucher_error', 'Voucher sudah mencapai batas penggunaan.');
    });

    it('rejects non-existent voucher', function () {
        Livewire::test(POSIndex::class)
            ->set('voucher_code', 'NOTFOUND')
            ->call('applyVoucher')
            ->assertSet('voucher_error', 'Voucher tidak ditemukan.')
            ->assertSet('applied_voucher', null);
    });
});

describe('Voucher - Case Insensitivity', function () {
    it('accepts voucher code regardless of case', function () {
        Voucher::create([
            'tenant_id' => $this->tenant->id,
            'code' => 'PROMO2024',
            'type' => 'fixed',
            'value' => 25000,
            'starts_at' => now()->subDay(),
        ]);

        Livewire::test(POSIndex::class)
            ->set('voucher_code', 'promo2024')
            ->call('applyVoucher')
            ->assertSessionHas('success')
            ->assertSet('applied_voucher.code', 'PROMO2024');
    });
});

describe('Voucher - Removal', function () {
    it('can remove applied voucher and reset discount', function () {
        Voucher::create([
            'tenant_id' => $this->tenant->id,
            'code' => 'REMOVEABLE',
            'type' => 'fixed',
            'value' => 15000,
            'starts_at' => now()->subDay(),
        ]);

        Livewire::test(POSIndex::class)
            ->set('voucher_code', 'REMOVEABLE')
            ->call('applyVoucher')
            ->assertSet('discount', 15000)
            ->call('$set', 'applied_voucher', null)
            ->call('$set', 'discount', 0)
            ->assertSet('discount', 0)
            ->assertSet('applied_voucher', null);
    });
});

describe('Voucher - Tenant Isolation', function () {
    it('only applies vouchers from same tenant', function () {
        $otherTenant = Tenant::factory()->create();

        Voucher::create([
            'tenant_id' => $otherTenant->id,
            'code' => 'OTHERTENANT',
            'type' => 'fixed',
            'value' => 50000,
            'starts_at' => now()->subDay(),
        ]);

        Livewire::test(POSIndex::class)
            ->set('voucher_code', 'OTHERTENANT')
            ->call('applyVoucher')
            ->assertSet('voucher_error', 'Voucher tidak ditemukan.')
            ->assertSet('applied_voucher', null);
    });
});
