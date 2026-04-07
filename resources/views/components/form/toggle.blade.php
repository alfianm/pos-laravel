@props([
    'label',
    'checked' => false,
    'model' => null,
    'note' => null,
])

<div class="flex flex-col gap-1">
    <div class="flex items-center gap-4">
        <label class="relative inline-flex items-center cursor-pointer group shrink-0">
            <input 
                @if($model) wire:model="{{ $model }}" @endif
                type="checkbox" 
                class="sr-only peer"
                @if($checked) checked @endif
            >
            
            <div class="w-12 h-7 rounded-full peer transition-all duration-300 
                bg-slate-200 dark:bg-slate-700 
                peer-checked:bg-indigo-500 dark:peer-checked:bg-indigo-500
                peer-focus:ring-4 peer-focus:ring-indigo-500/20">
            </div>
            
            <div class="absolute left-1 top-1 w-5 h-5 bg-white rounded-full shadow-md transition-all duration-300 
                peer-checked:translate-x-5
                peer-checked:bg-white
                dark:peer-checked:bg-white">
            </div>
        </label>
        
        @if($label)
            <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">
                {{ $label }}
            </span>
        @endif
    </div>
    
    @if($note)
        <p class="text-[10px] text-slate-400 font-medium leading-relaxed mt-1">
            {{ $note }}
        </p>
    @endif
</div>
