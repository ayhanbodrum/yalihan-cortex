{{-- ========================================
     CONTEXT7 TABLE COMPONENT
     ========================================
     Context7 Standardı: C7-TABLE-COMPONENT
     Versiyon: 4.0.0 | Tarih: 15 Eylül 2025
     ======================================== --}}

@props([
    'striped' => false,
    'hoverable' => true,
    'responsive' => true,
    'class' => '',
])

@php
    $baseClasses = 'neo-table min-w-full divide-y divide-gray-200 bg-white shadow-sm rounded-lg overflow-hidden';

    if ($striped) {
        $baseClasses .= ' table-striped';
    }

    if ($hoverable) {
        $baseClasses .= ' hoverable';
    }

    $classes = $baseClasses . ' ' . $class;
@endphp

@if ($responsive)
    <div class="neo-table-responsive overflow-x-auto">
@endif

<table {{ $attributes->merge(['class' => $classes]) }}>
    @if (isset($header))
        <thead class="neo-table-header bg-gray-50">
            {{ $header }}
        </thead>
    @endif

    <tbody class="neo-table-body bg-white divide-y divide-gray-200">
        {{ $slot }}
    </tbody>

    @if (isset($footer))
        <tfoot class="neo-table-footer bg-gray-50">
            {{ $footer }}
        </tfoot>
    @endif
</table>

@if ($responsive)
    </div>
@endif
