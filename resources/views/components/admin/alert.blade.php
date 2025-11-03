@props([
    'type' => 'info',
    'message' => null,
])
@php
    $map = [
        'success' => 'alert alert-success',
        'info' => 'alert alert-info',
        'warning' => 'alert alert-warning',
        'danger' => 'alert alert-danger',
        'error' => 'alert alert-danger',
    ];
    $cls = $map[$type] ?? $map['info'];
@endphp
<div {{ $attributes->merge(['class' => $cls]) }}>
    {{ $message ?? $slot }}
</div>
