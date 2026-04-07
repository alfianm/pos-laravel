@props([
    'label' => null,
    'error' => null,
    'prefix' => null,
    'suffix' => null,
    'model' => null,
    'type' => 'text',
    'isCurrency' => false,
])

<div class="space-y-1.5" x-data>
    @if($label)
        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider ml-1">
            {{ $label }}
        </label>
    @endif
    
    <div class="relative group">
        @if($prefix)
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <span class="text-sm font-bold text-slate-500 dark:text-slate-400">{!! $prefix !!}</span>
            </div>
        @endif
        
        <input 
            @if($model && !$isCurrency) wire:model="{{ $model }}" @endif
            @if($isCurrency) 
                x-data="moneyMask('{{ $model }}')"
                x-model="displayValue"
            @endif
            type="{{ $isCurrency ? 'text' : $type }}"
            {{ $attributes->whereDoesntStartWith(['model', 'label', 'error', 'prefix', 'suffix', 'type', 'isCurrency'])->class([
                'block w-full rounded-2xl border-2 transition-all duration-300',
                'bg-white dark:bg-slate-900/50',
                'border-slate-200 dark:border-slate-700/50',
                'text-slate-900 dark:text-white',
                'text-sm font-bold',
                'placeholder:text-slate-400/60',
                'focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10',
                'dark:focus:border-indigo-400 dark:focus:ring-indigo-400/10',
                $prefix ? 'pl-14' : 'pl-4',
                $suffix ? 'pr-14' : 'pr-4',
                'py-3.5',
                $error ? 'border-rose-500 focus:border-rose-500 focus:ring-rose-500/10' : '',
            ]) }}
        >
        
        @if($suffix)
            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                <span class="text-sm font-bold text-indigo-500 dark:text-indigo-400">{!! $suffix !!}</span>
            </div>
        @endif
        
        @if($error)
            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-rose-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
            </div>
        @endif
    </div>
    
    @if($error)
        <p class="text-xs text-rose-500 ml-1 font-medium flex items-center gap-1">
            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            {{ $error }}
        </p>
    @endif
</div>
