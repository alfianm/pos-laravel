<x-app-layout>
    <x-slot name="header">
        {{ __('Pengaturan Akun') }}
    </x-slot>

    <div class="space-y-12 pb-24">
        <div class="max-w-4xl space-y-10">
            <!-- Profile Info -->
            <div class="bg-white dark:bg-slate-900 shadow-2xl premium-shadow sm:rounded-[4rem] border border-slate-100 dark:border-slate-800 p-8 lg:p-16">
                <div class="max-w-2xl">
                    <livewire:profile.update-profile-information-form />
                </div>
            </div>

            <!-- Update Password -->
            <div class="bg-white dark:bg-slate-900 shadow-2xl premium-shadow sm:rounded-[4rem] border border-slate-100 dark:border-slate-800 p-8 lg:p-16">
                <div class="max-w-2xl">
                    <livewire:profile.update-password-form />
                </div>
            </div>

            <!-- Delete Account -->
            <div class="bg-rose-50/50 dark:bg-rose-950/10 shadow-xl sm:rounded-[4rem] border-2 border-dashed border-rose-100 dark:border-rose-900/40 p-8 lg:p-16">
                <div class="max-w-2xl">
                    <livewire:profile.delete-user-form />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
