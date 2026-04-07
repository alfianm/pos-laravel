@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full bg-white dark:bg-slate-950/40 border-slate-100 dark:border-slate-800 rounded-2xl py-3.5 px-5 text-sm font-bold text-slate-900 dark:text-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all border-2 placeholder:text-slate-400/60 disabled:opacity-50 disabled:cursor-not-allowed']) }}>
