<?php

namespace App\Livewire\CustomerPortal;

use App\Models\Sale;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.customer')]
class Dashboard extends Component
{
    public function render()
    {
        $customer = Auth::guard('customer')->user();
        
        $stats = [
            'total_orders' => Sale::where('customer_id', $customer->id)->count(),
            'total_spent' => Sale::where('customer_id', $customer->id)->sum('grand_total'),
            'last_purchase' => Sale::where('customer_id', $customer->id)->latest()->first(),
        ];

        $recentOrders = Sale::where('customer_id', $customer->id)
            ->latest()
            ->take(5)
            ->get();

        return view('livewire.customer-portal.dashboard', [
            'customer' => $customer,
            'stats' => $stats,
            'recentOrders' => $recentOrders,
        ]);
    }
}
