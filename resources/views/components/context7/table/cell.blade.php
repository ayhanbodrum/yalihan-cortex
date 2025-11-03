{{-- ========================================
     CONTEXT7 TABLE CELL COMPONENT
     ========================================
     Context7 Standardı: C7-TABLE-CELL-COMPONENT
     Versiyon: 4.0.0 | Tarih: 15 Eylül 2025
     ======================================== --}}

@props([
    'class' => '',
])

@php
    $baseClasses = 'neo-table-cell px-6 py-4 whitespace-nowrap text-sm text-gray-900';
    $classes = $baseClasses . ' ' . $class;
@endphp

<td {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</td>
