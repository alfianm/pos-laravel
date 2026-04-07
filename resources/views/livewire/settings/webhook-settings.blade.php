<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 px-4 sm:px-0">
            <div>
                <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">External Webhooks</h2>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Hubungkan RasaNusa dengan aplikasi pihak ketiga Anda (misal: CRM eksternal, Slack, atau sistem gudang sendiri).</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('settings.webhooks.logs') }}" wire:navigate class="inline-flex items-center px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-xl text-sm font-bold text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    Lihat Log
                </a>
                <button wire:click="create" class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-2xl font-bold text-white hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-500/20 transition-all shadow-lg shadow-indigo-500/30 active:scale-95">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Tambah Webhook
                </button>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800 rounded-2xl flex items-center gap-3 text-emerald-700 dark:text-emerald-400 font-bold">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>{{ session('message') }}</span>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-2xl sm:rounded-[2rem] border border-gray-100 dark:border-gray-700">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-separate border-spacing-0">
                    <thead>
                        <tr class="bg-gray-50/50 dark:bg-gray-900/40">
                            <th class="px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700">Nama & URL</th>
                            <th class="px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700 text-center">Status</th>
                            <th class="px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700 text-center">Events</th>
                            <th class="px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                        @forelse($webhooks as $webhook)
                        <tr class="group hover:bg-gray-50/50 dark:hover:bg-gray-900/50 transition-all">
                            <td class="px-8 py-6">
                                <div class="text-base font-bold text-gray-900 dark:text-white">{{ $webhook->name }}</div>
                                <div class="text-xs text-gray-400 mt-1 font-mono leading-tight max-w-sm truncate">{{ $webhook->url }}</div>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <button wire:click="toggleActive('{{ $webhook->id }}')" class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none ring-offset-2 focus:ring-2 focus:ring-indigo-600 {{ $webhook->is_active ? 'bg-indigo-600' : 'bg-gray-200 dark:bg-gray-700' }}">
                                    <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $webhook->is_active ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                </button>
                            </td>
                            <td class="px-8 py-6 text-center">
                                @if(empty($webhook->monitored_events))
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400 uppercase tracking-tighter">ALL</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 uppercase tracking-tighter">{{ count($webhook->monitored_events) }} Events</span>
                                @endif
                            </td>
                            <td class="px-8 py-6 text-right space-x-2">
                                <button wire:click="edit('{{ $webhook->id }}')" class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-xl transition-all">
                                    <svg class="w-5 h-5 font-bold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4l-4 4m0 0l-4-4m4 4V3"></path></svg>
                                </button>
                                <button onclick="confirm('Hapus webhook ini?') || event.stopImmediatePropagation()" wire:click="delete('{{ $webhook->id }}')" class="p-2 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-xl transition-all">
                                    <svg class="w-5 h-5 font-bold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-8 py-12 text-center text-gray-400 italic font-medium">Belum ada webhook yang terdaftar.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Add/Edit Modal --}}
        @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="$set('showModal', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-100 dark:border-gray-700">
                    <div class="p-10">
                        <div class="flex justify-between items-center mb-8">
                            <h3 class="text-2xl font-black text-gray-900 dark:text-white">{{ $webhookId ? 'Edit' : 'Tambah' }} Webhook</h3>
                            <button wire:click="$set('showModal', false)" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <form wire:submit.prevent="save" class="space-y-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Nama Webhook</label>
                                <input type="text" wire:model="name" placeholder="misal: Zapier Integration" class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-4 focus:ring-indigo-500/20 transition-all dark:text-white">
                                @error('name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Endpoint URL (HTTPS)</label>
                                <input type="text" wire:model="url" placeholder="https://your-api.com/webhooks" class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-4 focus:ring-indigo-500/20 transition-all dark:text-white">
                                @error('url') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">X-RasaNusa-Signature Secret</label>
                                <div class="flex gap-2">
                                    <input type="text" wire:model="secret" class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-4 focus:ring-indigo-500/20 transition-all dark:text-white font-mono">
                                    <button type="button" wire:click="$set('secret', '{{ Str::random(32) }}')" class="px-4 py-3 bg-gray-100 dark:bg-gray-700 rounded-xl text-xs font-bold hover:bg-gray-200">GEN</button>
                                </div>
                                <p class="mt-2 text-[10px] text-gray-400">Gunakan secret ini untuk memvalidasi request berasal dari RasaNusa.</p>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">Monitor Events</label>
                                <div class="grid grid-cols-2 gap-3">
                                    @foreach($availableEvents as $event)
                                    <label class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-900/50 rounded-xl cursor-pointer hover:bg-indigo-50/50 transition-all group">
                                        <input type="checkbox" wire:model="selectedEvents" value="{{ $event }}" class="w-5 h-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <span class="text-xs font-bold text-gray-600 dark:text-gray-400 group-hover:text-indigo-600">{{ $event }}</span>
                                    </label>
                                    @endforeach
                                </div>
                                <p class="mt-3 text-[10px] text-indigo-500 font-bold uppercase tracking-wider italic">* Kosongkan untuk memonitor SEMUA event.</p>
                            </div>

                            <div class="pt-6 flex gap-3">
                                <button type="button" wire:click="$set('showModal', false)" class="flex-1 px-6 py-4 bg-gray-100 dark:bg-gray-700 rounded-2xl font-bold text-gray-600 dark:text-gray-300 hover:bg-gray-200 transition-all active:scale-95">Batal</button>
                                <button type="submit" wire:loading.attr="disabled" class="flex-2 px-10 py-4 bg-indigo-600 border border-transparent rounded-2xl font-bold text-white hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-500/20 transition-all shadow-lg shadow-indigo-500/30 active:scale-95">
                                    <span wire:loading.remove>Simpan Webhook</span>
                                    <span wire:loading>Menyimpan...</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
