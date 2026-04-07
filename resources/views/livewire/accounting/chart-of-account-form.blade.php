<div>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('accounting.chart-of-accounts.index') }}" wire:navigate
                class="mr-4 text-gray-400 hover:text-gray-600">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
            </a>
            <h2 class="text-xl font-semibold text-gray-800">
                {{ $account ? 'Edit Chart of Account' : 'Add Chart of Account' }}
            </h2>
        </div>
    </x-slot>

    <div class="mt-6 max-w-3xl">
        <form wire:submit="save" class="rounded-lg bg-white p-6 shadow">
            <div class="space-y-6">
                <!-- Account Code -->
                <div>
                    <label for="account_code" class="block text-sm font-medium text-gray-700">Account Code <span
                            class="text-red-500">*</span></label>
                    <div class="mt-1">
                        <input wire:model="account_code" type="text" id="account_code"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            placeholder="e.g., 1001">
                    </div>
                    @error('account_code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Account Name -->
                <div>
                    <label for="account_name" class="block text-sm font-medium text-gray-700">Account Name <span
                            class="text-red-500">*</span></label>
                    <div class="mt-1">
                        <input wire:model="account_name" type="text" id="account_name"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            placeholder="e.g., Cash in Bank">
                    </div>
                    @error('account_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div>
                    <label for="account_category_id" class="block text-sm font-medium text-gray-700">Category <span
                            class="text-red-500">*</span></label>
                    <div class="mt-1">
                        <select wire:model="account_category_id" id="account_category_id"
                            class="block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option value="">Select Category</option>
                            @foreach($categories as $type => $group)
                                <optgroup label="{{ $type }}">
                                    @foreach($group as $category)
                                        <option value="{{ $category->id }}">{{ $category->code }} - {{ $category->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                    @error('account_category_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Parent Account -->
                <div>
                    <label for="parent_id" class="block text-sm font-medium text-gray-700">Parent Account</label>
                    <div class="mt-1">
                        <select wire:model="parent_id" id="parent_id"
                            class="block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option value="">None (Top Level)</option>
                            @foreach($potentialParents as $parent)
                                <option value="{{ $parent->id }}">{{ $parent->account_code }} - {{ $parent->account_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Select a parent to create a sub-account</p>
                    @error('parent_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Normal Balance -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Normal Balance <span
                            class="text-red-500">*</span></label>
                    <div class="mt-2 flex gap-4">
                        <label class="flex items-center">
                            <input wire:model="normal_balance" type="radio" value="debit"
                                class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600">
                            <span class="ml-2 text-sm text-gray-700">Debit</span>
                        </label>
                        <label class="flex items-center">
                            <input wire:model="normal_balance" type="radio" value="credit"
                                class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600">
                            <span class="ml-2 text-sm text-gray-700">Credit</span>
                        </label>
                    </div>
                    @error('normal_balance')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <div class="mt-1">
                        <textarea wire:model="description" id="description" rows="3"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            placeholder="Optional description..."></textarea>
                    </div>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Active -->
                <div class="flex items-center">
                    <input wire:model="is_active" type="checkbox" id="is_active"
                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                    <label for="is_active" class="ml-2 text-sm font-medium text-gray-700">Active</label>
                </div>
            </div>

            <div class="mt-8 flex items-center justify-end gap-3 border-t border-gray-200 pt-6">
                <a href="{{ route('accounting.chart-of-accounts.index') }}" wire:navigate
                    class="rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:text-gray-900">Cancel</a>
                <button type="submit"
                    class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    {{ $account ? 'Update Account' : 'Create Account' }}
                </button>
            </div>
        </form>
    </div>
</div>