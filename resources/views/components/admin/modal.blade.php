@props([
    'open' => false,
    'title' => null,
    'size' => 'md',
    'bind' => null,
])

@php
    $isBound = !empty($bind);
@endphp

<div @if ($isBound) x-show="{{ $bind }}"
    @else
        x-data="{ open: {{ $open ? 'true' : 'false' }} }" x-show="open" @endif
    x-cloak x-transition.opacity class="fixed inset-0 z-50">
    <div class="fixed inset-0 bg-black/40"
        @click="@if ($isBound) {{ $bind }} = false @else open = false @endif"></div>
    <div class="relative mx-auto mt-20 bg-white dark:bg-gray-900 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700"
        :class="{
            'w-full max-w-md': '{{ $size }}' === 'sm',
            'w-full max-w-lg': '{{ $size }}' === 'md',
            'w-full max-w-2xl': '{{ $size }}' === 'lg',
        }"
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-lg font-semibold">{{ $title }}</h3>
            <button class="btn btn-ghost px-2 py-1"
                @click="@if ($isBound) {{ $bind }} = false @else open = false @endif">✕</button>
        </div>
        <div class="p-6">
            {{ $slot }}
        </div>
        @isset($footer)
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 rounded-b-xl">
                {{ $footer }}
            </div>
        @endisset
    </div>
</div>
