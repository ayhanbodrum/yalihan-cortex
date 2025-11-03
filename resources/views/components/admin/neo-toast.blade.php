{{--
    Neo Design System Toast Component
    Context7 uyumlu bildirim component'i

    Kullanım:
    <x-admin.neo-toast />  <!-- Session mesajları için -->
    <x-admin.neo-toast type="success" message="İşlem başarılı!" />  <!-- Manuel -->

    @context7-compliant true
    @neo-design-system true
--}}

@props([
    'type' => session('toast_type', 'info'),
    'message' => session('success') ?? session('error') ?? session('warning') ?? session('info') ?? '',
    'dismissible' => true,
    'duration' => 5000,
    'position' => 'top-right'
])

@php
    $typeClasses = [
        'success' => 'neo-toast-success',
        'error' => 'neo-toast-error',
        'warning' => 'neo-toast-warning',
        'info' => 'neo-toast-info'
    ];

    $iconClasses = [
        'success' => 'neo-icon-check-circle text-green-600 dark:text-green-400',
        'error' => 'neo-icon-alert-circle text-red-600 dark:text-red-400',
        'warning' => 'neo-icon-alert-triangle text-yellow-600 dark:text-yellow-400',
        'info' => 'neo-icon-info text-blue-600 dark:text-blue-400'
    ];

    $positionClasses = [
        'top-right' => 'top-4 right-4',
        'top-left' => 'top-4 left-4',
        'bottom-right' => 'bottom-4 right-4',
        'bottom-left' => 'bottom-4 left-4',
        'top-center' => 'top-4 left-1/2 -translate-x-1/2',
        'bottom-center' => 'bottom-4 left-1/2 -translate-x-1/2'
    ];
@endphp

@if($message)
    <div x-data="{
        show: true,
        duration: {{ $duration }},
        init() {
            // Auto-dismiss
            if (this.duration > 0) {
                setTimeout(() => {
                    this.close();
                }, this.duration);
            }
        },
        close() {
            this.show = false;
            // Event dispatch
            this.$dispatch('toast-closed', { message: '{{ addslashes($message) }}', type: '{{ $type }}' });
        }
    }"
         x-show="show"
         x-transition:enter="transform transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-x-full"
         x-transition:enter-end="opacity-100 translate-x-0"
         x-transition:leave="transform transition ease-in duration-300"
         x-transition:leave-start="opacity-100 translate-x-0"
         x-transition:leave-end="opacity-0 translate-x-full"
         class="fixed {{ $positionClasses[$position] ?? $positionClasses['top-right'] }} z-[9999] pointer-events-auto"
         role="alert"
         aria-live="assertive"
         aria-atomic="true">

        <div class="neo-toast {{ $typeClasses[$type] ?? $typeClasses['info'] }} min-w-[320px] max-w-md">
            <div class="flex items-start gap-3 w-full">
                <div class="flex-shrink-0 mt-0.5">
                    <i class="neo-icon {{ $iconClasses[$type] ?? $iconClasses['info'] }}" aria-hidden="true"></i>
                </div>

                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white break-words">
                        {{ $message }}
                    </p>

                    @if(isset($slot) && $slot != '')
                        <div class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                            {{ $slot }}
                        </div>
                    @endif
                </div>

                @if($dismissible)
                    <button @click="close()"
                            type="button"
                            class="flex-shrink-0 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition-colors"
                            aria-label="Bildirimi kapat">
                        <i class="neo-icon neo-icon-x" aria-hidden="true"></i>
                    </button>
                @endif
            </div>
        </div>
    </div>
@endif

{{-- Session mesajları için meta tag'ler ekle (JavaScript için) --}}
@if(session('success'))
    <meta name="toast-success" content="{{ session('success') }}">
@endif

@if(session('error'))
    <meta name="toast-error" content="{{ session('error') }}">
@endif

@if(session('warning'))
    <meta name="toast-warning" content="{{ session('warning') }}">
@endif

@if(session('info'))
    <meta name="toast-info" content="{{ session('info') }}">
@endif

