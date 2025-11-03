@props([
    'type' => 'info',
    'message' => '',
])

@php
    $bg =
        [
            'success' => 'bg-green-600',
            'error' => 'bg-red-600',
            'warning' => 'bg-yellow-600',
            'info' => 'bg-blue-600',
        ][$type] ?? 'bg-blue-600';
@endphp

<div x-data="{ visible: true }" x-show="visible" x-transition.opacity class="fixed top-4 right-4 z-50">
    <div class="text-white px-4 py-2 rounded-lg shadow-lg {{ $bg }}">
        {{ $message ?: $slot }}
    </div>
</div>
