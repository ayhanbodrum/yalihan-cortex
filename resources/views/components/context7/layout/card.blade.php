@props([
    'title' => null,
    'subtitle' => null,
    'actions' => null,
    'variant' => 'default', // default, elevated, outlined, filled
    'size' => 'md', // sm, md, lg, xl
    'padding' => 'md', // none, sm, md, lg, xl
    'shadow' => true,
    'rounded' => 'lg',
])

@php
    $cardClasses = [
        'bg-white',
        'border',
        'border-gray-200',
        'transition-all',
        'duration-200',
        'hover:shadow-lg',
        'group',
    ];

    // Variant classes
    $variantClasses = [
        'default' => 'bg-white border-gray-200',
        'elevated' => 'bg-white border-gray-200 shadow-lg',
        'outlined' => 'bg-transparent border-2 border-gray-300',
        'filled' => 'bg-gray-50 border-gray-200',
    ];

    // Size classes
    $sizeClasses = [
        'sm' => 'text-sm',
        'md' => 'text-base',
        'lg' => 'text-lg',
        'xl' => 'text-xl',
    ];

    // Padding classes
    $paddingClasses = [
        'none' => 'p-0',
        'sm' => 'p-3',
        'md' => 'p-6',
        'lg' => 'p-8',
        'xl' => 'p-12',
    ];

    // Rounded classes
    $roundedClasses = [
        'none' => 'rounded-none',
        'sm' => 'rounded-sm',
        'md' => 'rounded-lg',
        'lg' => 'rounded-lg',
        'xl' => 'rounded-xl',
        '2xl' => 'rounded-2xl',
        'full' => 'rounded-full',
    ];

    $cardClasses = array_merge($cardClasses, [
        $variantClasses[$variant] ?? $variantClasses['default'],
        $sizeClasses[$size] ?? $sizeClasses['md'],
        $paddingClasses[$padding] ?? $paddingClasses['md'],
        $roundedClasses[$rounded] ?? $roundedClasses['lg'],
    ]);

    if ($shadow) {
        $cardClasses[] = 'shadow-md';
    }
@endphp

<div {{ $attributes->merge(['class' => implode(' ', $cardClasses)]) }}>
    @if ($title || $subtitle || $actions)
        <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-100">
            <div class="flex-1">
                @if ($title)
                    <h3 class="text-lg font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">
                        {{ $title }}
                    </h3>
                @endif
                @if ($subtitle)
                    <p class="text-sm text-gray-500 mt-1">
                        {{ $subtitle }}
                    </p>
                @endif
            </div>
            @if ($actions)
                <div class="flex items-center space-x-2">
                    {{ $actions }}
                </div>
            @endif
        </div>
    @endif

    <div class="content">
        {{ $slot }}
    </div>
</div>
