<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ComponentMake extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:form-component
                            {name : Component name (e.g., input, select, textarea)}
                            {--force : Overwrite existing component}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Standart form component oluştur (Tailwind + Alpine.js)';

    protected array $templates = [
        'input' => 'input',
        'select' => 'select',
        'textarea' => 'textarea',
        'checkbox' => 'checkbox',
        'radio' => 'radio',
        'toggle' => 'toggle',
        'file' => 'file',
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = Str::lower($this->argument('name'));
        $force = $this->option('force');

        $this->info("🎨 Form Component Oluşturuluyor: {$name}");

        // Component directory
        $componentDir = resource_path('views/components/form');
        if (!File::isDirectory($componentDir)) {
            File::makeDirectory($componentDir, 0755, true);
        }

        // Component file path
        $componentPath = "{$componentDir}/{$name}.blade.php";

        // Check if exists
        if (File::exists($componentPath) && !$force) {
            $this->error("❌ Component zaten var: {$name}");
            $this->comment("💡 Üzerine yazmak için --force kullanın");
            return Command::FAILURE;
        }

        // Get template
        $template = $this->getTemplate($name);

        if (!$template) {
            $this->error("❌ Template bulunamadı: {$name}");
            $this->comment("💡 Mevcut template'ler: " . implode(', ', array_keys($this->templates)));
            return Command::FAILURE;
        }

        // Create component
        File::put($componentPath, $template);

        $this->info("✅ Component oluşturuldu: resources/views/components/form/{$name}.blade.php");
        $this->newLine();
        $this->comment("📖 Kullanım örneği:");
        $this->line($this->getUsageExample($name));

        return Command::SUCCESS;
    }

    /**
     * Get component template
     */
    protected function getTemplate(string $name): ?string
    {
        return match ($name) {
            'input' => $this->getInputTemplate(),
            'select' => $this->getSelectTemplate(),
            'textarea' => $this->getTextareaTemplate(),
            'checkbox' => $this->getCheckboxTemplate(),
            'radio' => $this->getRadioTemplate(),
            'toggle' => $this->getToggleTemplate(),
            'file' => $this->getFileTemplate(),
            default => null,
        };
    }

    /**
     * Get usage example
     */
    protected function getUsageExample(string $name): string
    {
        return match ($name) {
            'input' => '<x-form.input name="title" label="Başlık" required />',
            'select' => '<x-form.select name="category" label="Kategori" :options="$categories" required />',
            'textarea' => '<x-form.textarea name="description" label="Açıklama" rows="5" />',
            'checkbox' => '<x-form.checkbox name="featured" label="Öne Çıkan" />',
            'radio' => '<x-form.radio name="status" label="Durum" :options="[\'active\', \'inactive\']" />',
            'toggle' => '<x-form.toggle name="enabled" label="Aktif" />',
            'file' => '<x-form.file name="images[]" label="Fotoğraflar" multiple accept="image/*" />',
            default => '<x-form.' . $name . ' />',
        };
    }

    /**
     * Input component template
     */
    protected function getInputTemplate(): string
    {
        return <<<'BLADE'
{{--
    Standard Input Component - Yalıhan Emlak

    @props
        - name: string (required)
        - label: string (optional)
        - type: string (default: 'text')
        - placeholder: string (optional)
        - value: mixed (optional)
        - error: string (optional)
        - help: string (optional)
        - required: bool (default: false)
        - disabled: bool (default: false)
        - readonly: bool (default: false)
        - autofocus: bool (default: false)
        - icon: string (optional) - Heroicon name

    @example
        <x-form.input
            name="email"
            type="email"
            label="Email Address"
            placeholder="you@example.com"
            :value="old('email')"
            :error="$errors->first('email')"
            required
        />
--}}

@props([
    'name',
    'label' => null,
    'type' => 'text',
    'placeholder' => '',
    'value' => '',
    'error' => null,
    'help' => null,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'autofocus' => false,
    'icon' => null,
])

<div class="space-y-2">
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        @if($icon)
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                </svg>
            </div>
        @endif

        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $name }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            {{ $readonly ? 'readonly' : '' }}
            {{ $autofocus ? 'autofocus' : '' }}
            {{ $attributes->merge([
                'class' => trim(
                    'w-full px-4 py-3 rounded-lg border transition-colors duration-200 ' .
                    ($icon ? 'pl-10 ' : '') .
                    ($error
                        ? 'border-red-500 focus:border-red-500 focus:ring-2 focus:ring-red-200 dark:focus:ring-red-800'
                        : 'border-gray-300 dark:border-gray-600 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 dark:focus:ring-orange-800'
                    ) . ' ' .
                    'bg-white dark:bg-gray-800 text-gray-900 dark:text-white ' .
                    'placeholder-gray-400 dark:placeholder-gray-500 ' .
                    ($disabled ? 'bg-gray-100 dark:bg-gray-700 cursor-not-allowed opacity-60' : '')
                )
            ]) }}
        >
    </div>

    @if($error)
        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $error }}</p>
    @endif

    @if($help && !$error)
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $help }}</p>
    @endif
</div>
BLADE;
    }

    /**
     * Select component template
     */
    protected function getSelectTemplate(): string
    {
        return <<<'BLADE'
{{--
    Standard Select Component - Yalıhan Emlak

    @props
        - name: string (required)
        - label: string (optional)
        - options: array (required)
        - value: mixed (optional)
        - error: string (optional)
        - help: string (optional)
        - required: bool (default: false)
        - disabled: bool (default: false)
        - placeholder: string (optional)
        - searchable: bool (default: false)
--}}

@props([
    'name',
    'label' => null,
    'options' => [],
    'value' => '',
    'error' => null,
    'help' => null,
    'required' => false,
    'disabled' => false,
    'placeholder' => 'Seçin...',
    'searchable' => false,
])

<div class="space-y-2" @if($searchable) x-data="{ open: false, search: '', selected: '{{ old($name, $value) }}' }" @endif>
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <select
        name="{{ $name }}"
        id="{{ $name }}"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->merge([
            'class' => trim(
                'w-full px-4 py-3 pr-10 rounded-lg border transition-colors duration-200 appearance-none ' .
                ($error
                    ? 'border-red-500 focus:border-red-500 focus:ring-2 focus:ring-red-200'
                    : 'border-gray-300 dark:border-gray-600 focus:border-orange-500 focus:ring-2 focus:ring-orange-200'
                ) . ' ' .
                'bg-white dark:bg-gray-800 text-gray-900 dark:text-white ' .
                ($disabled ? 'bg-gray-100 dark:bg-gray-700 cursor-not-allowed opacity-60' : '')
            )
        ]) }}
    >
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach($options as $key => $option)
            <option
                value="{{ is_array($option) ? ($option['value'] ?? $key) : $key }}"
                {{ old($name, $value) == (is_array($option) ? ($option['value'] ?? $key) : $key) ? 'selected' : '' }}
            >
                {{ is_array($option) ? ($option['label'] ?? $option['name'] ?? $option) : $option }}
            </option>
        @endforeach
    </select>

    @if($error)
        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $error }}</p>
    @endif

    @if($help && !$error)
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $help }}</p>
    @endif
</div>
BLADE;
    }

    /**
     * Textarea component template
     */
    protected function getTextareaTemplate(): string
    {
        return <<<'BLADE'
{{--
    Standard Textarea Component - Yalıhan Emlak
--}}

@props([
    'name',
    'label' => null,
    'rows' => 5,
    'placeholder' => '',
    'value' => '',
    'error' => null,
    'help' => null,
    'required' => false,
    'disabled' => false,
    'maxlength' => null,
])

<div class="space-y-2">
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <textarea
        name="{{ $name }}"
        id="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $maxlength ? "maxlength={$maxlength}" : '' }}
        {{ $attributes->merge([
            'class' => trim(
                'w-full px-4 py-3 rounded-lg border transition-colors duration-200 resize-y ' .
                ($error
                    ? 'border-red-500 focus:border-red-500 focus:ring-2 focus:ring-red-200'
                    : 'border-gray-300 dark:border-gray-600 focus:border-orange-500 focus:ring-2 focus:ring-orange-200'
                ) . ' ' .
                'bg-white dark:bg-gray-800 text-gray-900 dark:text-white ' .
                'placeholder-gray-400 dark:placeholder-gray-500 ' .
                ($disabled ? 'bg-gray-100 dark:bg-gray-700 cursor-not-allowed opacity-60' : '')
            )
        ]) }}
    >{{ old($name, $value) }}</textarea>

    @if($maxlength)
        <div class="flex justify-end">
            <span class="text-xs text-gray-500">
                <span x-data="{ count: 0 }" x-init="count = $el.parentElement.previousElementSibling.value.length; $el.parentElement.previousElementSibling.addEventListener('input', e => count = e.target.value.length)">
                    <span x-text="count"></span>
                </span>
                / {{ $maxlength }}
            </span>
        </div>
    @endif

    @if($error)
        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $error }}</p>
    @endif

    @if($help && !$error)
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $help }}</p>
    @endif
</div>
BLADE;
    }

    /**
     * Checkbox component template
     */
    protected function getCheckboxTemplate(): string
    {
        return <<<'BLADE'
{{--
    Standard Checkbox Component - Yalıhan Emlak
--}}

@props([
    'name',
    'label' => null,
    'checked' => false,
    'value' => '1',
    'error' => null,
    'help' => null,
    'disabled' => false,
])

<div class="space-y-2">
    <div class="flex items-start">
        <div class="flex items-center h-5">
            <input
                type="checkbox"
                name="{{ $name }}"
                id="{{ $name }}"
                value="{{ $value }}"
                {{ old($name, $checked) ? 'checked' : '' }}
                {{ $disabled ? 'disabled' : '' }}
                {{ $attributes->merge([
                    'class' => 'w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-orange-500 focus:ring-2 focus:ring-orange-200 dark:focus:ring-orange-800 transition-colors duration-200' . ($disabled ? ' opacity-60 cursor-not-allowed' : '')
                ]) }}
            >
        </div>

        @if($label)
            <label for="{{ $name }}" class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300 {{ $disabled ? 'opacity-60' : '' }}">
                {{ $label }}
            </label>
        @endif
    </div>

    @if($error)
        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $error }}</p>
    @endif

    @if($help && !$error)
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $help }}</p>
    @endif
</div>
BLADE;
    }

    protected function getRadioTemplate(): string { return "Radio template"; }
    protected function getToggleTemplate(): string { return "Toggle template"; }
    protected function getFileTemplate(): string { return "File template"; }
}
