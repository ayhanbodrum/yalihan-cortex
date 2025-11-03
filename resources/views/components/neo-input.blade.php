{{--
    Neo Input Component - Context7 Standard
    Yalıhan Bekçi Onaylı Form Input

    Kullanım:
    <x-neo-input
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
           class="neo-form-input @error($name) border-red-500 @enderror"
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
