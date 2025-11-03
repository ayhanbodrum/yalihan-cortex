@props([
    'align' => 'right',
    'icon' => true,
    'label' => null,
])

<div class="relative" x-data="{ open: false }" @keydown.escape.stop="open = false">
    <button type="button" class="btn btn-ghost px-2 py-1" :aria-expanded="open.toString()" aria-haspopup="menu"
        @click="open = !open" @keydown.enter.prevent="open = true" @keydown.space.prevent="open = true"
        @keydown.arrow-down.prevent="open = true; $nextTick(() => $refs.first?.focus())">
        @if ($icon)
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 6h.01M12 12h.01M12 18h.01" />
            </svg>
        @else
            {{ $label }}
        @endif
    </button>
    <div x-show="open" @click.away="open = false" x-transition role="menu"
        class="absolute mt-2 w-44 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg z-50"
        :class="{
            'right-0': '{{ $align }}'
            === 'right',
            'left-0': '{{ $align }}'
            === 'left'
        }">
        <div class="py-1" @keydown.arrow-down.prevent="($focus.wrap().next())"
            @keydown.arrow-up.prevent="($focus.wrap().previous())">
            {{ $slot }}
        </div>
    </div>
</div>
