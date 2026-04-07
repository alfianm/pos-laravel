<div>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ $journalEntryId ? 'Edit Jurnal Entry' : 'Buat Jurnal Entry' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow sm:rounded-lg">
                <form wire:submit="save" class="space-y-6">
                    <!-- Header Info -->
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                        <div>
                            <x-input-label for="entryDate" value="Tanggal Entry" />
                            <x-text-input wire:model="entryDate" id="entryDate" type="date" class="mt-1 block w-full"
                                required />
                            <x-input-error :messages="$errors->get('entryDate')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="referenceNumber" value="No Referensi (Opsional)" />
                            <x-text-input wire:model="referenceNumber" id="referenceNumber" type="text"
                                class="mt-1 block w-full" maxlength="50" />
                            <x-input-error :messages="$errors->get('referenceNumber')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="branchId" value="Cabang" />
                            <select wire:model="branchId" id="branchId"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">-- Pilih Cabang --</option>
                                @foreach(\App\Models\Branch::where('tenant_id', Auth::user()?->tenant_id)->get() as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('branchId')" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="description" value="Deskripsi" />
                        <textarea wire:model="description" id="description" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required maxlength="500"></textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <!-- Journal Lines -->
                    <div class="border-t pt-6">
                        <div class="mb-4 flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900">Detail Jurnal</h3>
                            <x-primary-button type="button" wire:click="addLine" class="text-xs">
                                + Tambah Baris
                            </x-primary-button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-3 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                            Akun</th>
                                        <th
                                            class="px-3 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                            Deskripsi</th>
                                        <th
                                            class="px-3 py-2 text-right text-xs font-medium uppercase tracking-wider text-gray-500">
                                            Debit</th>
                                        <th
                                            class="px-3 py-2 text-right text-xs font-medium uppercase tracking-wider text-gray-500">
                                            Kredit</th>
                                        <th
                                            class="px-3 py-2 text-center text-xs font-medium uppercase tracking-wider text-gray-500">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @foreach($lines as $index => $line)
                                        <tr>
                                            <td class="px-3 py-2">
                                                <select wire:model="lines.{{ $index }}.account_id"
                                                    class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                    required>
                                                    <option value="">-- Pilih Akun --</option>
                                                    @foreach($this->accounts as $account)
                                                        <option value="{{ $account->id }}">
                                                            {{ $account->account_code }} - {{ $account->account_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error("lines.{$index}.account_id")
                                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                @enderror
                                            </td>
                                            <td class="px-3 py-2">
                                                <input wire:model="lines.{{ $index }}.description" type="text"
                                                    class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                    placeholder="Deskripsi baris" maxlength="255">
                                            </td>
                                            <td class="px-3 py-2">
                                                <input wire:model="lines.{{ $index }}.debit" type="number" step="0.01"
                                                    min="0"
                                                    class="block w-full rounded-md border-gray-300 text-right text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                    placeholder="0.00">
                                            </td>
                                            <td class="px-3 py-2">
                                                <input wire:model="lines.{{ $index }}.credit" type="number" step="0.01"
                                                    min="0"
                                                    class="block w-full rounded-md border-gray-300 text-right text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                    placeholder="0.00">
                                            </td>
                                            <td class="px-3 py-2 text-center">
                                                <button type="button" wire:click="removeLine({{ $index }})"
                                                    class="text-red-600 hover:text-red-900" {{ count($lines) <= 2 ? 'disabled' : '' }}>
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td colspan="2" class="px-3 py-3 text-right font-medium text-gray-900">Total:
                                        </td>
                                        <td
                                            class="px-3 py-3 text-right font-medium {{ $this->totalDebit == $this->totalCredit ? 'text-green-600' : 'text-red-600' }}">
                                            {{ number_format($this->totalDebit, 2) }}
                                        </td>
                                        <td
                                            class="px-3 py-3 text-right font-medium {{ $this->totalDebit == $this->totalCredit ? 'text-green-600' : 'text-red-600' }}">
                                            {{ number_format($this->totalCredit, 2) }}
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- Balance Status -->
                        <div class="mt-4 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                @if($this->isBalanced && $this->totalDebit > 0)
                                    <span
                                        class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                                        ✓ Seimbang
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">
                                        ✗ Tidak Seimbang
                                    </span>
                                @endif
                                <span class="text-sm text-gray-500">
                                    Selisih: {{ number_format(abs($this->totalDebit - $this->totalCredit), 2) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-4 border-t pt-6">
                        <a href="{{ route('accounting.journal-entries.index') }}" wire:navigate
                            class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25">
                            Batal
                        </a>
                        <x-primary-button type="submit">
                            {{ $journalEntryId ? 'Update' : 'Simpan' }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>