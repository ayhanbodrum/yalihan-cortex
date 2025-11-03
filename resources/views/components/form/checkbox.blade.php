@props(['name', 'label', 'value' => 1])
{{-- Unified checkbox component. Usage:
<x-form.checkbox name="status" label="Aktif" />
Supports Alpine: <x-form.checkbox name="havuz" label="Havuz" x-model="formData.havuz" />
Pass extra classes via class attribute; id can also be passed. --}}
<label
    class="flex items-center gap-2 px-4 py-2.5 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 hover:border-blue-400 transition-colors cursor-pointer text-sm">
    <input type="checkbox" name="{{ $name }}" value="{{ $value }}"
        @if (!$attributes->has('x-model')) @checked(old($name)) @endif
        {{ $attributes->merge(['class' => 'checkbox-input']) }}>
    <span class="text-gray-700 dark:text-gray-300">{!! $label !!}</span>
</label>
