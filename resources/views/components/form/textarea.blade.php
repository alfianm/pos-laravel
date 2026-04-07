@props([
    'label' => null,
    'placeholder' => '',
    'error' => null,
    'helpText' => null,
    'rows' => 3,
    'model' => null,
])

<div class="space-y-1.5">
    @if($label)
        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider ml-1">
            {{ $label }}
        </label>
    @endif
    
    <textarea 
        @if($model) wire:model="{{ $model }}" @endif
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        {{ $attributes->whereDoesntStartWith(['model', 'label', 'error', 'helpText', 'rows'])->class([
            'block w-full rounded-2xl border-2 transition-all duration-300 resize-none',
            'bg-white dark:bg-slate-900/50',
            'border-slate-200 dark:border-slate-700/50',
            'text-slate-900 dark:text-white',
            'text-sm font-medium',
            'placeholder:text-slate-400/60',
            'px-4 py-3.5',
            'focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10',
            'dark:focus:border-indigo-400 dark:focus:ring-indigo-400/10',
            $error ? 'border-rose-500 focus:border-rose-500 focus:ring-rose-500/10' : '',
        ]) }}
    >{{ $slot }}</textarea>
    
    @if($helpText && !$error)
        <p class="text-xs text-slate-400 ml-1">{{ $helpText }}</p>
    @endif
    
    @if($error)
        <p class="text-xs text-rose-500 ml-1 font-medium flex items-center gap-1">
            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            {{ $error }}
        </p>
    @endif
</div>
