@props([
    'label' => null,
    'name',
    'required' => false,
    'help' => null,
    'value' => null,
    'rows' => 3,
    'wrapperClass' => 'mb-4',
])
<div class="{{ $wrapperClass }}">
    @if ($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ $label }} @if ($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    <textarea
        {{ $attributes->merge(['class' => 'w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-800 text-black dark:text-white placeholder-gray-400 dark:placeholder-gray-500 resize-y']) }}
        id="{{ $name }}" name="{{ $name }}" rows="{{ $rows }}"
        @if ($required) required @endif>
@if (old($name))
{{ old($name) }}@else{{ $value }}
@endif
</textarea>
    @if ($help)
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{!! $help !!}</p>
    @endif
    @error($name)
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>
