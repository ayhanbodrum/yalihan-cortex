@props([
    'name' => 'ozellikler',
    'options' => [], // ['value' => 'emoji Label']
    'columns' => 'grid-cols-2 md:grid-cols-3 lg:grid-cols-4',
    'alpineArray' => null, // örn: formData.ozellikler
])
<div class="form-field">
    <div class="grid {{ $columns }} gap-4">
        @foreach ($options as $val => $label)
            <label
                class="flex items-center p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors cursor-pointer text-sm">
                <input type="checkbox" name="{{ $name }}[]" value="{{ $val }}"
                    @if ($alpineArray) x-model="{{ $alpineArray }}" @endif class="checkbox-input">
                <span class="ml-3 text-gray-700 dark:text-gray-300">{!! $label !!}</span>
            </label>
        @endforeach
    </div>
</div>
