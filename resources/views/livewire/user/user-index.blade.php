<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight tracking-tight">Manajemen User</h1>
            <p class="text-sm text-gray-500 mt-1 uppercase tracking-widest font-bold text-[10px]">Kelola akses staf dan pengaturan cabang.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('settings.users.create') }}" wire:navigate 
               class="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-xl font-bold text-sm hover:bg-indigo-700 shadow-lg shadow-indigo-500/20 transition-all active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Tambah User
            </a>
        </div>
    </div>

    <!-- User Table -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-xl shadow-gray-200/20 overflow-hidden">
        <div class="p-6 border-b border-gray-50">
            <div class="relative w-full sm:w-80">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-4.35-4.35m1.85-4.65a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"></path></svg>
                </span>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama atau email..." 
                       class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 transition-all">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-6 py-4 text-[0.65rem] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50">User Info</th>
                        <th class="px-6 py-4 text-[0.65rem] font-black text-gray-400 uppercase tracking_widest border-b border-gray-50">Role</th>
                        <th class="px-6 py-4 text-[0.65rem] font-black text-gray-400 uppercase tracking_widest border-b border-gray-50">Cabang Aktif</th>
                        <th class="px-6 py-4 text-[0.65rem] font-black text-gray-400 uppercase tracking_widest border-b border-gray-50 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($users as $user)
                        <tr class="hover:bg-indigo-50/30 transition-colors group">
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center font-black text-gray-400 border border-gray-200 uppercase">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-black text-gray-900 tracking-tight leading-none">{{ $user->name }}</p>
                                        <p class="text-[10px] text-gray-400 mt-1.5 font-bold tracking-tight">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <span class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-full text-[10px] font-black uppercase tracking-widest">
                                    {{ $user->roles->first()?->name ?? 'Staff' }}
                                </span>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full {{ $user->activeBranch ? 'bg-emerald-500' : 'bg-gray-300' }}"></span>
                                    <span class="text-xs font-bold text-gray-600">{{ $user->activeBranch->name ?? 'Belum Ditentukan' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('settings.users.edit', $user->id) }}" wire:navigate 
                                       class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all active:scale-90">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                    <button onclick="confirm('Yakin ingin menghapus user ini?') || event.stopImmediatePropagation()" 
                                            wire:click="deletingUser('{{ $user->id }}')" 
                                            class="p-2 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all active:scale-90">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-20 text-center">
                                <p class="text-gray-900 font-bold">Tidak ada data user.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-50">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    {{-- Toasts --}}
    @if (session()->has('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" 
             class="fixed bottom-6 right-6 px-6 py-4 bg-emerald-600 text-white rounded-2xl shadow-2xl flex items-center gap-3 animate-fade-in-up z-50">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
            <span class="font-bold tracking-tight">{{ session('success') }}</span>
        </div>
    @endif
    @if (session()->has('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" 
             class="fixed bottom-6 right-6 px-6 py-4 bg-rose-600 text-white rounded-2xl shadow-2xl flex items-center gap-3 animate-fade-in-up z-50">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
            <span class="font-bold tracking-tight">{{ session('error') }}</span>
        </div>
    @endif
</div>
