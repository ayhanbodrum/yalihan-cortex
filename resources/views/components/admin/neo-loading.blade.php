{{--
    Neo Design System Loading Component
    Context7 uyumlu loading göstergeleri

    Kullanım:
    <x-admin.neo-loading />  <!-- Varsayılan spinner -->
    <x-admin.neo-loading type="dots" />  <!-- Dots loader -->
    <x-admin.neo-loading type="bar" />  <!-- Progress bar -->
    <x-admin.neo-loading overlay="true" />  <!-- Full overlay -->

    @context7-compliant true
    @neo-design-system true
--}}

@props([
    'type' => 'spinner',
    'size' => 'md',
    'color' => 'primary',
    'overlay' => false,
    'message' => 'Yükleniyor...',
    'fullscreen' => false,
])

@php
    $sizeClasses = [
        'sm' => 'w-4 h-4',
        'md' => 'w-6 h-6',
        'lg' => 'w-8 h-8',
        'xl' => 'w-12 h-12',
    ];

    $colorClasses = [
        'primary' => 'text-blue-600 dark:text-blue-400',
        'success' => 'text-green-600 dark:text-green-400',
        'warning' => 'text-yellow-600 dark:text-yellow-400',
        'danger' => 'text-red-600 dark:text-red-400',
        'gray' => 'text-gray-600 dark:text-gray-400',
    ];
@endphp

{{-- Full Screen Overlay --}}
@if ($fullscreen)
    <div class="fixed inset-0 bg-white/90 dark:bg-gray-900/90 backdrop-blur-sm z-[9999] flex items-center justify-center"
        role="status" aria-live="polite">
        <div class="text-center">
            @include('components.admin.partials.loading-' . $type, [
                'size' => 'xl',
                'color' => $color,
            ])
            <p class="mt-4 text-lg font-medium text-gray-700 dark:text-gray-300">
                {{ $message }}
            </p>
        </div>
    </div>
@elseif($overlay)
    {{-- Overlay Loading --}}
    <div class="neo-loading-overlay" role="status" aria-live="polite">
        <div class="neo-loading-spinner">
            @include('components.admin.partials.loading-' . $type, [
                'size' => $size,
                'color' => $color,
            ])
            @if ($message)
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ $message }}
                </p>
            @endif
        </div>
    </div>
@else
    {{-- Inline Loading --}}
    <div class="inline-flex items-center gap-2" role="status" aria-live="polite">
        @if ($type === 'spinner')
            <svg class="neo-spin {{ $sizeClasses[$size] ?? $sizeClasses['md'] }} {{ $colorClasses[$color] ?? $colorClasses['primary'] }}"
                fill="none" viewBox="0 0 24 24" aria-hidden="true">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>
        @elseif($type === 'dots')
            <div class="neo-loading-dots" aria-hidden="true">
                <div class="neo-loading-dot" style="background-color: currentColor"></div>
                <div class="neo-loading-dot" style="background-color: currentColor"></div>
                <div class="neo-loading-dot" style="background-color: currentColor"></div>
            </div>
        @elseif($type === 'bar')
            <div class="w-full h-1 bg-gray-200 dark:bg-gray-800 rounded-full overflow-hidden" aria-hidden="true">
                <div class="neo-loading-bar {{ $colorClasses[$color] ?? $colorClasses['primary'] }}"></div>
            </div>
        @elseif($type === 'pulse')
            <div class="neo-skeleton neo-skeleton-pulse {{ $sizeClasses[$size] ?? $sizeClasses['md'] }} rounded-full"
                aria-hidden="true"></div>
        @endif

        @if ($message && !$overlay && !$fullscreen)
            <span class="text-sm text-gray-600 dark:text-gray-400">{{ $message }}</span>
        @endif

        <span class="sr-only">{{ $message }}</span>
    </div>
@endif
