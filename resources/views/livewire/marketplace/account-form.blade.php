<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        {{-- Back Button --}}
        <div class="mb-8 px-4 sm:px-0">
            <a href="{{ route('omnichannel.accounts.index') }}" wire:navigate 
               class="inline-flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-indigo-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Daftar Akun
            </a>
        </div>

        {{-- Header --}}
        <div class="px-4 sm:px-0 mb-8">
            <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">
                {{ $isEdit ? 'Edit Koneksi' : 'Tambah Koneksi Marketplace' }}
            </h1>
            <p class="text-gray-500 mt-2">Hubungkan toko Anda dengan marketplace untuk sinkronisasi produk dan pesanan.</p>
        </div>

        {{-- Success Message --}}
        @if (session()->has('message'))
            <div class="mb-6 px-4 sm:px-0">
                <div class="p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800 rounded-2xl flex items-center gap-3 text-emerald-700 dark:text-emerald-400 font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ session('message') }}
                </div>
            </div>
        @endif

        {{-- Form Card --}}
        <div class="bg-white dark:bg-gray-800 shadow-2xl sm:rounded-[3rem] border border-gray-100 dark:border-gray-700/50 overflow-hidden">
            <form wire:submit.prevent="save" class="p-8 sm:p-12 space-y-8">
                {{-- Platform Selection --}}
                <div>
                    <label class="block text-[0.65rem] font-black text-gray-400 uppercase tracking-[0.2em] mb-4 ml-1">Pilih Platform Marketplace</label>
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                        @foreach ($platformOptions as $key => $platform)
                            <label class="relative cursor-pointer group">
                                <input type="radio" wire:model.live="marketplace" value="{{ $key }}" class="sr-only peer">
                                <div class="p-4 flex flex-col items-center justify-center bg-gray-50 dark:bg-gray-900 border-2 rounded-2xl transition-all group-hover:scale-[1.02] group-hover:border-gray-300 dark:group-hover:border-gray-600 @if($marketplace == $key) border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20 ring-2 ring-indigo-500/20 @else border-gray-200 dark:border-gray-700 @endif">
                                    <div class="w-10 h-10 mb-2 flex items-center justify-center">
                                        <img src="{{ $platform['logo'] }}" alt="{{ $platform['name'] }}" class="max-w-full max-h-full object-contain filter {{ $marketplace == $key ? '' : 'grayscale opacity-50' }} transition-all group-hover:grayscale-0 group-hover:opacity-100">
                                    </div>
                                    <span class="text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ $platform['name'] }}</span>
                                    @if($marketplace == $key)
                                        <svg class="w-4 h-4 text-indigo-500 mt-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1.414-1.414L9 10.586 7.707 9.293a1.414-1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                    @endif
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('marketplace') <span class="text-[10px] text-rose-500 mt-2 block font-bold uppercase ml-1">{{ $message }}</span> @enderror
                </div>

                {{-- Account Name --}}
                <div>
                    <label for="name" class="block text-[0.65rem] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-1">Nama Profil Akun</label>
                    <input type="text" id="name" wire:model="name" 
                           class="block w-full px-6 py-4 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all dark:text-white dark:placeholder-gray-400"
                           placeholder="Contoh: Toko Utama Shopee, Official Store Tokopedia">
                    <p class="text-[10px] text-gray-400 mt-2 ml-1">Nama ini untuk memudahkan Anda mengenali akun ini.</p>
                    @error('name') <span class="text-[10px] text-rose-500 mt-1 block font-bold uppercase ml-1">{{ $message }}</span> @enderror
                </div>

                {{-- Divider --}}
                <div class="border-t border-gray-100 dark:border-gray-700"></div>

                {{-- API Credentials Section --}}
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <label class="text-[0.65rem] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">API Credentials</label>
                        <span class="text-[9px] font-bold text-amber-600 bg-amber-50 dark:bg-amber-900/30 px-2 py-1 rounded-full uppercase tracking-wider">Opsional</span>
                    </div>
                    <p class="text-xs text-gray-500 mb-6 ml-1">Credentials akan dienkripsi secara otomatis. Anda bisa menambahkan ini nanti melalui edit akun.</p>

                    <div class="space-y-5">
                        {{-- API Key --}}
                        <div>
                            <label for="api_key" class="block text-xs font-bold text-gray-500 mb-2 ml-1">API Key / Client ID</label>
                            <input type="text" id="api_key" wire:model="api_key" 
                                   class="block w-full px-5 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-mono focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all dark:text-white"
                                   placeholder="Masukkan API Key dari developer portal">
                            @error('api_key') <span class="text-[10px] text-rose-500 mt-1 block font-bold ml-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- API Secret --}}
                        <div>
                            <label for="api_secret" class="block text-xs font-bold text-gray-500 mb-2 ml-1">API Secret / Client Secret</label>
                            <div class="relative">
                                <input type="password" id="api_secret" wire:model="api_secret" 
                                       class="block w-full px-5 py-3 pr-12 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-mono focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all dark:text-white"
                                       placeholder="Masukkan API Secret">
                                <button type="button" onclick="togglePassword('api_secret')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </button>
                            </div>
                            @error('api_secret') <span class="text-[10px] text-rose-500 mt-1 block font-bold ml-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Access Token --}}
                        <div>
                            <label for="access_token" class="block text-xs font-bold text-gray-500 mb-2 ml-1">Access Token</label>
                            <div class="relative">
                                <input type="password" id="access_token" wire:model="access_token" 
                                       class="block w-full px-5 py-3 pr-12 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-mono focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all dark:text-white"
                                       placeholder="Masukkan Access Token dari OAuth">
                                <button type="button" onclick="togglePassword('access_token')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </button>
                            </div>
                            @error('access_token') <span class="text-[10px] text-rose-500 mt-1 block font-bold ml-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Refresh Token --}}
                        <div>
                            <label for="refresh_token" class="block text-xs font-bold text-gray-500 mb-2 ml-1">Refresh Token</label>
                            <div class="relative">
                                <input type="password" id="refresh_token" wire:model="refresh_token" 
                                       class="block w-full px-5 py-3 pr-12 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-mono focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all dark:text-white"
                                       placeholder="Masukkan Refresh Token">
                                <button type="button" onclick="togglePassword('refresh_token')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </button>
                            </div>
                            @error('refresh_token') <span class="text-[10px] text-rose-500 mt-1 block font-bold ml-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- Security Notice --}}
                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 rounded-2xl">
                    <div class="flex gap-3">
                        <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        <div>
                            <p class="text-xs font-bold text-blue-700 dark:text-blue-400 uppercase tracking-wide">Keamanan Data</p>
                            <p class="text-[11px] text-blue-600 dark:text-blue-300 mt-1">Semua credential disimpan dengan enkripsi menggunakan Laravel Encryption. Data sensitif tidak akan tampil di log atau response API.</p>
                        </div>
                    </div>
                </div>

                {{-- Submit Buttons --}}
                <div class="flex flex-col sm:flex-row gap-3 pt-4">
                    <a href="{{ route('omnichannel.accounts.index') }}" wire:navigate 
                       class="flex-1 px-8 py-4 bg-white dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 rounded-2xl font-bold text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 transition-all text-center uppercase tracking-wider">
                        Batalkan
                    </a>
                    <button type="submit" wire:loading.attr="disabled"
                            class="flex-1 px-8 py-4 bg-indigo-600 dark:bg-indigo-500 border border-transparent rounded-2xl font-black text-sm text-white hover:bg-indigo-700 dark:hover:bg-indigo-600 focus:outline-none focus:ring-8 focus:ring-indigo-500/10 transition-all shadow-xl shadow-indigo-500/20 active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed uppercase tracking-wider">
                        <span wire:loading.remove>{{ $isEdit ? 'Simpan Perubahan' : 'Hubungkan Akun' }}</span>
                        <span wire:loading>Menyimpan...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        field.type = field.type === 'password' ? 'text' : 'password';
    }
</script>
@endpush