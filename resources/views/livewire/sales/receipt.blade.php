<div class="min-h-screen bg-white text-slate-900 font-mono text-[12px] p-4 max-w-[300px] mx-auto overflow-hidden">
    {{-- Receipt Header --}}
    <div class="text-center space-y-2 mb-6">
        <h1 class="text-lg font-black uppercase tracking-tighter">{{ $sale->branch->name }}</h1>
        <p class="text-[10px] leading-tight">{{ $sale->branch->address }}</p>
        <p class="text-[10px]">{{ $sale->branch->phone }}</p>
    </div>

    <div class="border-t border-dashed border-slate-300 py-3 space-y-1">
        <div class="flex justify-between">
            <span>Tgl:</span>
            <span>{{ $sale->sale_date->format('d/m/y H:i') }}</span>
        </div>
        <div class="flex justify-between">
            <span>No:</span>
            <span>{{ $sale->sale_no }}</span>
        </div>
        <div class="flex justify-between">
            <span>Ksr:</span>
            <span>{{ $sale->creator->name }}</span>
        </div>
        <div class="flex justify-between">
            <span>Plg:</span>
            <span class="truncate ml-2">{{ $sale->customer->name ?? 'Walk-in' }}</span>
        </div>
    </div>

    {{-- Items --}}
    <div class="border-t border-dashed border-slate-300 py-3 space-y-2">
        @foreach($sale->items as $item)
            <div>
                <div class="flex justify-between font-bold">
                    <span class="truncate pr-2">{{ $item->product->name }}</span>
                </div>
                <div class="flex justify-between text-[11px]">
                    <span>{{ $item->qty }} x {{ number_format($item->unit_price, 0, ',', '.') }}</span>
                    <span>{{ number_format($item->line_total, 0, ',', '.') }}</span>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Totals --}}
    <div class="border-t border-dashed border-slate-300 py-3 space-y-1">
        <div class="flex justify-between">
            <span>Subtotal:</span>
            <span>{{ number_format($sale->subtotal, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between">
            <span>Pajak:</span>
            <span>{{ number_format($sale->tax_amount, 0, ',', '.') }}</span>
        </div>
        @if($sale->discount_amount > 0)
            <div class="flex justify-between">
                <span>Diskon:</span>
                <span>-{{ number_format($sale->discount_amount, 0, ',', '.') }}</span>
            </div>
        @endif
        <div class="flex justify-between font-black text-[14px] pt-2">
            <span>TOTAL:</span>
            <span>Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- Payments --}}
    <div class="border-t border-dashed border-slate-300 py-3 space-y-1">
        @foreach($sale->payments as $payment)
            <div class="flex justify-between text-[11px]">
                <span class="uppercase">{{ $payment->payment_method }}:</span>
                <span>{{ number_format($payment->amount, 0, ',', '.') }}</span>
            </div>
        @endforeach
    </div>

    <div class="text-center mt-8 space-y-4">
        <p class="text-[10px] uppercase font-bold tracking-widest">Terima Kasih</p>
        <p class="text-[8px] text-slate-400">Powered by ChainPOS</p>
        
        {{-- Auto Print Script --}}
        <div class="no-print pt-4">
            <button onclick="window.print()" class="px-4 py-2 bg-slate-900 text-white rounded-lg text-[10px] font-bold">CETAK</button>
        </div>
    </div>

    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white; margin: 0; padding: 0; }
            @page { margin: 0; }
        }
    </style>
</div>
