@props(['messages'])

@if ($messages)
    <div {{ $attributes->merge(['class' => 'mt-2 space-y-1']) }}>
        @foreach ((array) $messages as $message)
            <div class="text-rose-500 text-[10px] font-black italic ml-4 flex items-center gap-1 uppercase tracking-widest">
                <span>×</span> {{ $message }}
            </div>
        @endforeach
    </div>
@endif
