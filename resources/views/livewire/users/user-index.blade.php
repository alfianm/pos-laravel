<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 px-4 sm:px-0">
            <div>
                <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">User Management</h2>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Kelola user dan role.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('roles.index') }}" wire:navigate class="inline-flex items-center px-4 py-2.5 bg-white dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 rounded-2xl font-bold text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 transition-all whitespace-nowrap">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-2.332 9-7.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    Manage Roles
                </a>
                <button wire:click="openCreateModal" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 border border-transparent rounded-2xl font-bold text-sm text-white hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-500/20 transition-all shadow-lg shadow-indigo-500/30 active:scale-95 whitespace-nowrap">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    Tambah User
                </button>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="mb-6 px-4 sm:px-0 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800 rounded-2xl flex items-center gap-3 text-emerald-700 dark:text-emerald-400 font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                {{ session('message') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 px-4 sm:px-0 p-4 bg-rose-50 dark:bg-rose-900/20 border border-rose-100 dark:border-rose-800 rounded-2xl flex items-center gap-3 text-rose-700 dark:text-rose-400 font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                {{ session('error') }}
            </div>
        @endif

        <div class="mb-6 px-4 sm:px-0">
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700 p-4">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </span>
                            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama atau email..." class="block w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all dark:text-white dark:placeholder-gray-400">
                        </div>
                    </div>
                    <div class="w-full md:w-48">
                        <select wire:model.live="role_filter" class="block w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all dark:text-white">
                            <option value="">Semua Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-2xl sm:rounded-[2.5rem] border border-gray-100 dark:border-gray-700/50">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 dark:bg-gray-900/40">
                            <th class="px-8 py-5 text-[0.65rem] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700">User</th>
                            <th class="px-8 py-5 text-[0.65rem] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700">Email</th>
                            <th class="px-8 py-5 text-[0.65rem] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700">Role</th>
                            <th class="px-8 py-5 text-[0.65rem] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700">Branch</th>
                            <th class="px-8 py-5 text-[0.65rem] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                        @forelse ($users as $user)
                            <tr class="group hover:bg-indigo-50/30 dark:hover:bg-indigo-900/10 transition-all">
                                <td class="px-8 py-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm shadow-lg">
                                            {{ substr($user->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900 dark:text-white">{{ $user->name }}</p>
                                            @if($user->id === auth()->id())
                                                <span class="text-[9px] font-bold text-indigo-600 bg-indigo-50 dark:bg-indigo-900/30 px-1.5 py-0.5 rounded">You</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $user->email }}</p>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($user->roles as $role)
                                            <span class="px-2 py-0.5 text-[9px] font-bold rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400">
                                                {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ $user->activeBranch?->name ?? '-' }}</span>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button wire:click="openRolesModal('{{ $user->id }}')" class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-xl transition-all" title="Assign Roles">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-2.332 9-7.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                        </button>
                                        <button wire:click="openEditModal('{{ $user->id }}')" class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-xl transition-all" title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                        </button>
                                        @if($user->id !== auth()->id())
                                            <button wire:click="delete('{{ $user->id }}')" wire:confirm="Yakin ingin menghapus user ini?" class="p-2 text-gray-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/30 rounded-xl transition-all" title="Delete">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-8 py-20 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-900 rounded-3xl flex items-center justify-center text-gray-400 mb-4">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                        </div>
                                        <p class="text-gray-500 font-bold uppercase tracking-widest text-[10px]">Belum ada user</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="px-8 py-4 border-t border-gray-100 dark:border-gray-700">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Modal Create/Edit User --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" wire:keydown.escape="closeModal">
            <div class="flex min-h-screen items-center justify-center p-4">
                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" wire:click="closeModal"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl w-full max-w-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-8">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-black text-gray-900 dark:text-white">{{ $editId ? 'Edit User' : 'Tambah User Baru' }}</h3>
                            <button wire:click="closeModal" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <form wire:submit.prevent="save" class="space-y-5">
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Nama</label>
                                <input type="text" wire:model="name" class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all dark:text-white">
                                @error('name') <span class="text-[10px] text-rose-500 mt-1 block font-bold">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Email</label>
                                <input type="email" wire:model="email" class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all dark:text-white">
                                @error('email') <span class="text-[10px] text-rose-500 mt-1 block font-bold">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Password{{ $editId ? ' (kosongkan jika tidak diubah)' : '' }}</label>
                                <input type="password" wire:model="password" class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all dark:text-white">
                                @error('password') <span class="text-[10px] text-rose-500 mt-1 block font-bold">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Konfirmasi Password</label>
                                <input type="password" wire:model="password_confirmation" class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all dark:text-white">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Branch</label>
                                <select wire:model="branch_id" class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all dark:text-white">
                                    <option value="">Pilih Branch</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Roles</label>
                                <div class="grid grid-cols-2 gap-2 max-h-40 overflow-y-auto p-2 bg-gray-50 dark:bg-gray-900 rounded-xl">
                                    @foreach($roles as $role)
                                        <label class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 cursor-pointer">
                                            <input type="checkbox" value="{{ $role->name }}" wire:model="selectedRoles" class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="flex gap-3 pt-4">
                                <button type="button" wire:click="closeModal" class="flex-1 px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-xl font-bold text-sm hover:bg-gray-200 dark:hover:bg-gray-600 transition-all">
                                    Batal
                                </button>
                                <button type="submit" class="flex-1 px-6 py-3 bg-indigo-600 text-white rounded-xl font-bold text-sm hover:bg-indigo-700 transition-all">
                                    {{ $editId ? 'Simpan' : 'Tambah User' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Assign Roles --}}
    @if($showRolesModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" wire:keydown.escape="closeRolesModal">
            <div class="flex min-h-screen items-center justify-center p-4">
                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" wire:click="closeRolesModal"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl w-full max-w-md border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-8">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-xl font-black text-gray-900 dark:text-white">Assign Roles</h3>
                                <p class="text-sm text-gray-500 mt-1">User: <span class="font-bold">{{ $name }}</span></p>
                            </div>
                            <button wire:click="closeRolesModal" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <div class="space-y-2 max-h-64 overflow-y-auto">
                            @foreach($roles as $role)
                                <label class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-all border border-gray-100 dark:border-gray-700">
                                    <input type="checkbox" value="{{ $role->name }}" wire:model="selectedRoles" class="w-5 h-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <div>
                                        <p class="font-bold text-gray-900 dark:text-white">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</p>
                                        <p class="text-xs text-gray-400">{{ $role->permissions->count() }} permissions</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        <div class="flex gap-3 pt-6 mt-6 border-t border-gray-100 dark:border-gray-700">
                            <button type="button" wire:click="closeRolesModal" class="flex-1 px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-xl font-bold text-sm hover:bg-gray-200 dark:hover:bg-gray-600 transition-all">
                                Batal
                            </button>
                            <button type="button" wire:click="saveRoles" class="flex-1 px-6 py-3 bg-indigo-600 text-white rounded-xl font-bold text-sm hover:bg-indigo-700 transition-all">
                                Simpan Roles
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>