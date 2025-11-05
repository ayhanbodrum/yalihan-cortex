{{--
    Neo Input Component - Context7 Standard
    Yalıhan Bekçi Onaylı Form Input

    Kullanım:
    <x-w-full px-3 py-2 rounded-md border border-gray-200 bg-white text-sm placeholder:text-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-800 dark:border-gray-800 dark:text-gray-100 transition-colors
        name="baslik"
        label="İlan Başlığı"
        :required="true"
        placeholder="Örn: Merkezi Konumda 3+1 Daire" />
--}}

@props(['name', 'label', 'type' => 'text', 'required' => false, 'placeholder' => '', 'value' => '', 'helpText' => null, 'icon' => null])

<div {{ $attributes->merge(['class' => '']) }}>
    {{-- Label --}}
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-2">
        @if($icon)
            <i class="{{ $icon }} mr-1"></i>
        @endif
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>

    {{-- Input Field --}}
    <input type="{{ $type }}"
           id="{{ $name }}"
           name="{{ $name }}"
           class="w-full px-3 py-2 rounded-md border border-gray-200 bg-white text-sm focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-800 @error($name) border-red-500 @enderror"
           placeholder="{{ $placeholder }}"
           value="{{ old($name, $value) }}"
           {{ $required ? 'required' : '' }}
           {{ $attributes->except(['class']) }}>

    {{-- Help Text --}}
    @if($helpText)
        <p class="text-xs text-gray-500 mt-1">
            <i class="fas fa-info-circle mr-1"></i>{{ $helpText }}
        </p>
    @endif

    {{-- Error Message --}}
    @error($name)
        <p class="text-red-500 text-xs mt-1">
            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
        </p>
    @enderror
</div>
