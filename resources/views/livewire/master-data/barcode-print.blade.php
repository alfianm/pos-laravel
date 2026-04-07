<div class="min-h-screen bg-gray-50 dark:bg-gray-900" x-data="{ showPreview: @entangle('showPreview') }">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Print Label Barcode
                    </h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Pilih produk dan generate label barcode untuk print
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-sm text-gray-600 dark:text-gray-400">
                        <span class="font-semibold text-indigo-600 dark:text-indigo-400" x-text="$wire.selectedProducts.length"></span> produk dipilih
                    </span>
                    <button
                        wire:click="openPreview"
                        wire:loading.attr="disabled"
                        :disabled="$wire.selectedProducts.length === 0"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Preview & Print
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Settings -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Cari Produk</label>
                    <div class="relative">
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Nama, SKU, atau barcode..."
                            class="w-full pl-10 pr-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                        >
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Category Filter -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Kategori</label>
                    <select
                        wire:model.live="categoryFilter"
                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                    >
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Template -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Template Label</label>
                    <select
                        wire:model="selectedTemplateId"
                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                    >
                        <option value="">-- Pilih Template --</option>
                        @foreach($labelTemplates as $template)
                            <option value="{{ $template->id }}">
                                {{ $template->name }}
                                @if($template->is_default) (Default) @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Quantity -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Jumlah per Produk</label>
                    <input
                        type="number"
                        wire:model="quantityPerProduct"
                        min="1"
                        max="100"
                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                    >
                </div>
            </div>
        </div>

        <!-- Product Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <!-- Table Header -->
            <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <input
                        type="checkbox"
                        wire:model.live="selectAll"
                        class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                    >
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Pilih Semua di Halaman Ini
                    </span>
                    @if(count($selectedProducts) > 0)
                        <button
                            wire:click="clearSelection"
                            class="text-xs text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 underline"
                        >
                            Batal Pilih ({{ count($selectedProducts) }})
                        </button>
                    @endif
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $products->total() }} produk ditemukan
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="w-12 px-4 py-3"></th>
                            <th class="px-4 py-3 text-left font-medium text-gray-700 dark:text-gray-300">
                                <button wire:click="sortByColumn('name')" class="flex items-center gap-1 hover:text-indigo-600">
                                    Produk
                                    @if($sortBy === 'name')
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            @if($sortDirection === 'asc')
                                                <path d="M5 10l5-5 5 5H5z"/>
                                            @else
                                                <path d="M15 10l-5 5-5-5h10z"/>
                                            @endif
                                        </svg>
                                    @endif
                                </button>
                            </th>
                            <th class="px-4 py-3 text-left font-medium text-gray-700 dark:text-gray-300">SKU / Barcode</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-700 dark:text-gray-300">Kategori</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-700 dark:text-gray-300">Harga</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-700 dark:text-gray-300">Stok</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($products as $product)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 @if(in_array($product->id, $selectedProducts)) bg-indigo-50 dark:bg-indigo-900/20 @endif">
                                <td class="px-4 py-3">
                                    <input
                                        type="checkbox"
                                        value="{{ $product->id }}"
                                        @checked(in_array($product->id, $selectedProducts))
                                        wire:click="toggleProductSelection('{{ $product->id }}')"
                                        class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                    >
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        @if($product->hasMedia('images'))
                                            <img src="{{ $product->getFirstMediaUrl('images', 'thumb') }}" alt="" class="w-10 h-10 rounded-lg object-cover">
                                        @else
                                            <div class="w-10 h-10 rounded-lg bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-medium text-gray-900 dark:text-white">{{ $product->name }}</p>
                                            @if($product->description)
                                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-xs">{{ Str::limit($product->description, 50) }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-gray-900 dark:text-white font-mono text-xs">{{ $product->sku ?? '-' }}</span>
                                    @if($product->barcode)
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $product->barcode }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-1 text-xs rounded-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                        {{ $product->category?->name ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <span class="font-medium text-gray-900 dark:text-white">
                                        Rp {{ number_format($product->default_price, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @php
                                        $stock = $product->branches->sum('pivot.current_stock');
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-1 text-xs rounded-full
                                        @if($stock <= 0) bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300
                                        @elseif($stock <= $product->min_stock) bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300
                                        @else bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300
                                        @endif">
                                        {{ number_format($stock) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center text-gray-500 dark:text-gray-400">
                                    <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                    </svg>
                                    <p>Tidak ada produk ditemukan</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $products->links() }}
            </div>
        </div>
    </div>

    <!-- Preview Modal -->
    @if($showPreview)
        <div
            class="fixed inset-0 z-50 overflow-y-auto"
            x-show="showPreview"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm" x-on:click="$wire.closePreview()"></div>

            <div class="relative min-h-screen flex items-center justify-center p-4">
                <div
                    class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden"
                    x-show="showPreview"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    @keydown.escape.window="$wire.closePreview()"
                >
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Preview Label</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                Total {{ count($selectedProducts) * $quantityPerProduct }} label akan di-print
                            </p>
                        </div>
                        <button wire:click="closePreview" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Preview Content -->
                    <div class="p-6 overflow-y-auto max-h-[60vh]">
                        @php
                            $printData = $this->generatePrintData();
                        @endphp

                        @if(!empty($printData['labels']))
                            <div class="print-preview-container bg-gray-100 dark:bg-gray-900 p-4 rounded-lg">
                                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                    @foreach(array_slice($printData['labels'], 0, 8) as $label)
                                        <div class="bg-white dark:bg-gray-800 p-3 rounded shadow-sm">
                                            {!! $label['html'] !!}
                                        </div>
                                    @endforeach
                                </div>
                                @if(count($printData['labels']) > 8)
                                    <div class="text-center py-4 text-gray-500 dark:text-gray-400">
                                        ... dan {{ count($printData['labels']) - 8 }} label lainnya
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                Tidak ada label untuk ditampilkan
                            </div>
                        @endif
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex items-center justify-between px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            <span class="font-medium">Template:</span>
                            {{ $printData['template']->name ?? 'Default' }}
                            ({{ $printData['template']->width_mm ?? 50 }}x{{ $printData['template']->height_mm ?? 30 }}mm)
                        </div>
                        <div class="flex items-center gap-3">
                            <button
                                wire:click="closePreview"
                                class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                            >
                                Tutup
                            </button>
                            <button
                                wire:click="printLabels"
                                class="px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                </svg>
                                Print Label
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Print Template (Hidden) -->
        <div id="print-area" class="hidden">
            @php
                $printData = $this->generatePrintData();
            @endphp

            @if(!empty($printData['labels']))
                <style>
                    @page {
                        size: auto;
                        margin: 5mm;
                    }
                    body {
                        margin: 0;
                        padding: 0;
                    }
                    .label-sheet {
                        display: flex;
                        flex-wrap: wrap;
                        gap: 2mm;
                    }
                    .label-item {
                        page-break-inside: avoid;
                    }
                    @media print {
                        .no-print {
                            display: none !important;
                        }
                    }
                </style>
                <div class="label-sheet">
                    @foreach($printData['labels'] as $label)
                        <div class="label-item">
                            {!! $label['html'] !!}
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    <!-- Print Script -->
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('trigger-print', () => {
                const printArea = document.getElementById('print-area');
                if (printArea) {
                    const printWindow = window.open('', '_blank');
                    printWindow.document.write('<html><head><title>Print Label Barcode</title>');
                    printWindow.document.write('</head><body>');
                    printWindow.document.write(printArea.innerHTML);
                    printWindow.document.write('</body></html>');
                    printWindow.document.close();

                    setTimeout(() => {
                        printWindow.focus();
                        printWindow.print();
                        printWindow.close();
                    }, 250);
                }
            });
        });
    </script>
</div>
