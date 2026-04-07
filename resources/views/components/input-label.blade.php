@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500 mb-2 ml-1']) }}>
    {{ $value ?? $slot }}
</label>
