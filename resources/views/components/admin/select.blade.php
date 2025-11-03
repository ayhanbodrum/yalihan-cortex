@props([
    'label' => null,
    'name',
    'required' => false,
    'help' => null,
    'value' => null,
    'error' => null,
    'wrapperClass' => 'mb-4',
    'options' => [],
    'placeholder' => null,
])

<div class="{{ $wrapperClass }}">
    @if ($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ $label }} @if ($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    <select
        {{ $attributes->merge(['class' => 'w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white']) }}
        id="{{ $name }}" name="{{ $name }}"
        @if ($required) required @endif>
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach ($options as $key => $option)
            @if (is_array($option))
                <option value="{{ $key }}" {{ old($name, $value) == $key ? 'selected' : '' }}>
                    {{ $option['label'] ?? $option['name'] ?? $option }}
                </option>
            @else
                <option value="{{ $key }}" {{ old($name, $value) == $key ? 'selected' : '' }}>
                    {{ $option }}
                </option>
            @endif
        @endforeach
    </select>
    @if ($help)
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{!! $help !!}</p>
    @endif
    @error($name)
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>
