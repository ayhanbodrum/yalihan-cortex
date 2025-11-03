@props([
    'label' => null,
    'name',
    'checked' => false,
    'help' => null,
    'wrapperClass' => 'mb-4',
])
<div class="{{ $wrapperClass }}">
    <label class="flex items-center space-x-2 cursor-pointer select-none">
        <input type="checkbox" id="{{ $name }}" name="{{ $name }}" value="1"
            class="h-4 w-4 text-indigo-600 focus:ring-blue-500 border-gray-300 rounded" @checked(old($name, $checked))>
        @if ($label)
            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $label }}</span>
        @endif
    </label>
    @if ($help)
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{!! $help !!}</p>
    @endif
    @error($name)
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>
