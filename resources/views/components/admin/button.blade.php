@props([
    'variant' => 'primary',
    'href' => null,
    'type' => 'button',
])

@php
    $base = 'btn';
    $variants = [
        'primary' => 'btn-primary',
        'secondary' => 'btn-secondary',
        'danger' => 'btn-danger',
        'ghost' => 'btn-ghost',
    ];
    $class = trim($base . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($attributes['class'] ?? ''));
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $class]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $class]) }}>
        {{ $slot }}
    </button>
@endif
