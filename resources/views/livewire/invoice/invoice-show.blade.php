<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('invoices.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Invoice {{ $invoice->invoice_number }}</h2>
                <p class="text-sm text-gray-500">{{ $invoice->invoice_date->format('d F Y') }}</p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <span
                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $invoice->status->badgeClass() }}">
                {{ $invoice->status->label() }}
            </span>
            @if($invoice->status !== App\Constants\InvoiceStatus::PAID && $invoice->status !== App\Constants\InvoiceStatus::CANCELLED)
                <button wire:click="openPaymentModal"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm font-medium">
                    Catat Pembayaran
                </button>
                <button wire:click="openCancelModal"
                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm font-medium">
                    Batalkan
                </button>
            @endif
        </div>
    </div>

    {{-- Invoice Info --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4">Informasi Customer</h3>
            <div class="space-y-2">
                <p class="text-lg font-medium text-gray-900">{{ $invoice->customer?->name ?? '-' }}</p>
                <p class="text-sm text-gray-600">{{ $invoice->customer?->phone ?? '-' }}</p>
                <p class="text-sm text-gray-600">{{ $invoice->customer?->email ?? '-' }}</p>
                <p class="text-sm text-gray-600">{{ $invoice->customer?->address ?? '-' }}</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4">Ringkasan Pembayaran</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Total Invoice</span>
                    <span class="text-sm font-medium text-gray-900">Rp
                        {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Sudah Dibayar</span>
                    <span class="text-sm font-medium text-green-600">Rp
                        {{ number_format($invoice->paid_amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between border-t pt-3">
                    <span class="text-sm font-medium text-gray-900">Sisa Pembayaran</span>
                    <span
                        class="text-lg font-bold {{ $invoice->remaining_amount > 0 ? 'text-red-600' : 'text-green-600' }}">
                        Rp {{ number_format($invoice->remaining_amount, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Items --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Item Invoice</h3>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Harga
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($invoice->items as $item)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $item->product?->name ?? 'Produk #' . $item->product_id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">
                            {{ number_format($item->quantity, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">
                            Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-gray-900">
                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-50">
                <tr>
                    <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-900">Total</td>
                    <td class="px-6 py-4 text-right text-sm font-bold text-gray-900">
                        Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Payments --}}
    @if($invoice->payments->count() > 0)
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Riwayat Pembayaran</h3>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Metode
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Referensi
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($invoice->payments as $payment)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $payment->payment_date->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $payment->method->label() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $payment->reference_number ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-gray-900">
                                Rp {{ number_format($payment->amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $payment->status->badgeClass() }}">
                                    {{ $payment->status->label() }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Notes --}}
    @if($invoice->notes)
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Catatan</h3>
            <p class="text-sm text-gray-700">{{ $invoice->notes }}</p>
        </div>
    @endif

    {{-- Payment Modal --}}
    @if($showPaymentModal)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Catat Pembayaran</h3>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Pembayaran</label>
                        <input type="number" step="0.01" wire:model="paymentData.amount"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        @error('paymentData.amount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran</label>
                        <select wire:model="paymentData.method"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">Pilih Metode</option>
                            @foreach($paymentMethods as $method)
                                <option value="{{ $method->value }}">{{ $method->label() }}</option>
                            @endforeach
                        </select>
                        @error('paymentData.method') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Referensi</label>
                        <input type="text" wire:model="paymentData.reference_number"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pembayaran</label>
                        <input type="date" wire:model="paymentData.payment_date"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        @error('paymentData.payment_date') <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                        <textarea wire:model="paymentData.notes" rows="2"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"></textarea>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                    <button wire:click="closePaymentModal"
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 text-sm">
                        Batal
                    </button>
                    <button wire:click="recordPayment"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm font-medium">
                        Simpan Pembayaran
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Cancel Modal --}}
    @if($showCancelModal)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Batalkan Invoice</h3>
                </div>
                <div class="px-6 py-4">
                    <p class="text-sm text-gray-600 mb-4">
                        Apakah Anda yakin ingin membatalkan invoice ini? Tindakan ini tidak dapat dibatalkan.
                    </p>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alasan Pembatalan (Opsional)</label>
                        <textarea wire:model="cancelReason" rows="3"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"></textarea>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                    <button wire:click="closeCancelModal"
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 text-sm">
                        Batal
                    </button>
                    <button wire:click="cancelInvoice"
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm font-medium">
                        Ya, Batalkan Invoice
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>