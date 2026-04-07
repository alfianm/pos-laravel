<div class="p-6 space-y-6">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Bulk Import System</h1>
            <p class="text-slate-500">Impor data produk, pelangggan, atau pemasok secara massal melalui file CSV.</p>
        </div>
        <div class="flex space-x-2">
            <a href="#" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 bg-indigo-50 px-3 py-2 rounded-lg transition border border-indigo-100 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Unduh Template Produk
            </a>
            <a href="#" class="text-xs font-bold text-slate-600 hover:text-slate-700 bg-slate-50 px-3 py-2 rounded-lg transition border border-slate-100 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Unduh Template Pelanggan
            </a>
        </div>
    </div>

    @if(session()->has('message'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-600 rounded-xl flex items-center shadow-sm animate-in fade-in slide-in-from-top-4 duration-500">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Import Form -->
        <div class="lg:col-span-1">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 sticky top-6">
                <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center uppercase tracking-wider">
                    <span class="w-2 h-6 bg-indigo-500 rounded-full mr-2"></span>
                    Pengaturan Impor
                </h3>

                <form wire:submit.prevent="startImport" class="space-y-6">
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Jenis Data</label>
                        <select wire:model="importType" class="block w-full px-4 py-3 text-sm text-slate-700 border border-slate-200 rounded-xl bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-150 ease-in-out">
                            <option value="products">Produk & Inventory</option>
                            <option value="customers">Daftar Pelanggan / Member</option>
                            <option value="suppliers">Daftar Pemasok</option>
                        </select>
                        @error('importType') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Pilih File CSV</label>
                        <div class="relative">
                            <div class="flex items-center justify-center w-full">
                                <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-40 border-2 border-slate-300 border-dashed rounded-xl cursor-pointer bg-slate-50 hover:bg-slate-100 transition duration-300">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                        <p class="mb-2 text-sm text-slate-700 font-semibold italic text-center px-4">
                                            {{ $file ? $file->getClientOriginalName() : 'Klik untuk unggah atau seret file' }}
                                        </p>
                                        <p class="text-xs text-slate-500">CSV Only (Max 10MB)</p>
                                    </div>
                                    <input id="dropzone-file" wire:model="file" type="file" class="hidden" />
                                </label>
                            </div>
                        </div>
                        @error('file') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        <div wire:loading wire:target="file" class="text-xs text-indigo-500 font-bold mt-1">Mengunggah file...</div>
                    </div>

                    <button type="submit" wire:loading.attr="disabled"
                            class="w-full py-4 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 active:scale-[0.98] transition-all shadow-lg shadow-indigo-100 flex items-center justify-center group uppercase tracking-widest text-sm">
                        <span wire:loading.remove>
                            Proses Impor Massive
                        </span>
                        <span wire:loading class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Memulai Antrian...
                        </span>
                    </button>
                    
                    <div class="p-3 bg-amber-50 rounded-lg text-[10px] text-amber-700 font-medium italic border border-amber-100">
                        * Mohon pastikan urutan kolom sesuai dengan template standar. Sistem akan mengupdate data yang sudah ada berdasarkan SKU/No_HP.
                    </div>
                </form>
            </div>
        </div>

        <!-- History & Status -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden min-h-[400px]">
                <div class="p-6 border-b border-slate-50 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-800 uppercase tracking-wider">
                        <span class="w-2 h-6 bg-amber-400 rounded-full mr-2"></span>
                        Riwayat Impor & Status Antrian
                    </h3>
                    <button wire:click="loadBatches" class="text-xs font-bold text-indigo-600 hover:underline flex items-center transition active:rotate-180 duration-500">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        Refresh List
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-left">
                        <thead>
                            <tr class="bg-slate-50/50">
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">File / Deskripsi</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Tipe Tipe</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Status / Progress</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-right">Hasil Hasil</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($batches as $batch)
                                <tr class="hover:bg-slate-50/50 transition duration-150 items-center">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-slate-800">{{ $batch->original_filename }}</div>
                                        <div class="text-[10px] text-slate-400 font-medium">{{ $batch->created_at->format('d M Y, H:i') }} • Oleh: {{ $batch->user->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-[10px] font-bold rounded-md bg-slate-100 text-slate-600 uppercase tracking-tighter">
                                            {{ $batch->import_type }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col items-center">
                                            @php
                                                $statusColor = match($batch->status) {
                                                    'completed' => 'text-emerald-500',
                                                    'processing' => 'text-indigo-500 animate-pulse font-black',
                                                    'pending' => 'text-amber-500',
                                                    'failed' => 'text-rose-500',
                                                    default => 'text-slate-400'
                                                };
                                                $progress = $batch->total_rows > 0 ? round(($batch->processed_rows / $batch->total_rows) * 100) : 0;
                                            @endphp
                                            <span class="text-[10px] font-bold uppercase {{ $statusColor }} mb-1">{{ $batch->status }}</span>
                                            
                                            <div class="w-full bg-slate-100 rounded-full h-1.5 max-w-[100px] overflow-hidden">
                                                <div class="{{ $statusColor === 'text-rose-500' ? 'bg-rose-500' : 'bg-indigo-500' }} h-1.5 rounded-full transition-all duration-500" style="width: {{ $progress }}%"></div>
                                            </div>
                                            <span class="text-[9px] text-slate-400 mt-1 font-bold">{{ $batch->processed_rows }}/{{ $batch->total_rows }} Baris</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex flex-col items-end space-y-1">
                                            <span class="text-[10px] font-bold text-emerald-600">✅ {{ $batch->success_count }} Success</span>
                                            <span class="text-[10px] font-bold text-rose-500">❌ {{ $batch->error_count }} Failed</span>
                                            @if($batch->error_count > 0)
                                                <button class="text-[9px] text-slate-400 underline hover:text-indigo-600 font-bold italic">Lihat Detail Error</button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-20 text-center text-slate-300">
                                        <div class="flex flex-col items-center justify-center opacity-40">
                                            <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                            <p class="text-sm italic font-bold">Belum ada riwayat impor.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
