@props(['align' => 'right', 'width' => '48', 'contentClasses' => 'p-2 bg-white dark:bg-slate-900'])

@php
$alignmentClasses = match ($align) {
    'left' => 'ltr:origin-top-left rtl:origin-top-right start-0',
    'top' => 'origin-top',
    default => 'ltr:origin-top-right rtl:origin-top-left end-0',
};

$widthClass = match ($width) {
    '48' => 'w-48',
    default => $width,
};
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
    <div @click="open = ! open">
        {{ $trigger }}
    </div>

    <div x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
            class="absolute z-50 mt-4 {{ $widthClass }} rounded-2xl shadow-2xl premium-shadow border border-slate-100 dark:border-slate-800 {{ $alignmentClasses }}"
            style="display: none;"
            @click="open = false">
        <div class="rounded-2xl {{ $contentClasses }}">
            {{ $content }}
        </div>
    </div>
</div>
