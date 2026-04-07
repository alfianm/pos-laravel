<a {{ $attributes->merge(['class' => 'block w-full px-5 py-3 text-start text-sm font-bold text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-slate-800/50 rounded-xl transition-all duration-200 active:scale-[0.98]']) }}>
    {{ $slot }}
</a>
