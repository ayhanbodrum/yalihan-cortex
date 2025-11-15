{{--
    İlan Başarı Modal - Referans Numarası ile
    Context7 Standardı: C7-SUCCESS-MODAL-2025-10-11

    Kullanım:
    <x-ilan-success-modal :ilan="$ilan" />
--}}

@props(['ilan'])

@php
    $referansService = app(\App\Services\IlanReferansService::class);
    $successData = $referansService->getSuccessMessage($ilan);
@endphp

<div x-data="{ show: {{ $successData['show_modal'] ? 'true' : 'false' }} }"
     x-show="show"
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">

    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"
         @click="show = false"></div>

    {{-- Modal --}}
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-2xl w-full p-8 transform transition-all"
             @click.away="show = false">

            {{-- Close Button --}}
            <button @click="show = false"
                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            {{-- Success Icon --}}
            <div class="text-center mb-6">
                <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-100 dark:bg-green-900 mb-4">
                    <svg class="h-12 w-12 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>

                <h2 class="text-3xl font-bold text-gray-900 dark:text-white">
                    {{ $successData['title'] }}
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    {{ $successData['message'] }}
                </p>
            </div>

            {{-- Referans Numarası Card --}}
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        🏷️ Referans Numarası
                    </h3>
                    <button onclick="copyToClipboard('{{ $successData['referans_no'] }}')"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2-sm inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-300 bg-white text-gray-700 rounded-lg hover:bg-gray-50 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-500 transition-all duration-200 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700">
                        📋 Kopyala
                    </button>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border-2 border-blue-300 dark:border-blue-700">
                    <div class="font-mono text-2xl font-bold text-blue-600 dark:text-blue-400 text-center">
                        {{ $successData['referans_no'] }}
                    </div>
                </div>

                <p class="mt-3 text-xs text-gray-600 dark:text-gray-400 text-center">
                    Bu numara ile ilanınızı kolayca bulabilirsiniz
                </p>
            </div>

            {{-- Dosya Adı Card --}}
            <div class="bg-gradient-to-r from-green-50 to-teal-50 dark:from-green-900/20 dark:to-teal-900/20 rounded-xl p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        📁 Önerilen Dosya Adı
                    </h3>
                    <button onclick="copyToClipboard('{{ $successData['dosya_adi'] }}')"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2-sm inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2-success">
                        📋 Kopyala
                    </button>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border-2 border-green-300 dark:border-green-700">
                    <div class="font-medium text-green-700 dark:text-green-400 break-words text-center">
                        {{ $successData['dosya_adi'] }}
                    </div>
                </div>

                <p class="mt-3 text-xs text-gray-600 dark:text-gray-400 text-center">
                    💡 Bu adı bilgisayarınızdaki klasöre vererek dosyaları organize edebilirsiniz
                </p>
            </div>

            {{-- İlan Detayları --}}
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">Kategori</div>
                    <div class="font-semibold text-gray-900 dark:text-white">
                        {{ $ilan->kategori?->name ?? 'Belirtilmemiş' }}
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">Lokasyon</div>
                    <div class="font-semibold text-gray-900 dark:text-white">
                        {{ $ilan->ilce?->ilce_adi ?? $ilan->il?->il_adi ?? 'Belirtilmemiş' }}
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">Fiyat</div>
                    <div class="font-semibold text-gray-900 dark:text-white">
                        {{ number_format($ilan->fiyat, 0, ',', '.') }} ₺
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">Durum</div>
                    <div class="font-semibold text-gray-900 dark:text-white">
                        {{ $ilan->status ?? 'Taslak' }}
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex gap-3">
                <a href="{{ route('admin.ilanlar.show', $ilan->id) }}"
                   class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg">
                    👁️ İlanı Görüntüle
                </a>

                <a href="{{ route('admin.ilanlar.edit', $ilan->id) }}"
                   class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-300 bg-white text-gray-700 rounded-lg hover:bg-gray-50 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-500 transition-all duration-200 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700">
                    ✏️ İlanı Düzenle
                </a>

                <a href="{{ route('admin.ilanlar.create') }}"
                   class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2-success">
                    ➕ Yeni İlan Ekle
                </a>
            </div>

            {{-- Keyboard Hint --}}
            <div class="mt-4 text-center text-xs text-gray-500 dark:text-gray-400">
                <kbd class="px-2 py-1 bg-gray-200 dark:bg-gray-800 rounded">ESC</kbd> tuşu ile kapatabilirsiniz
            </div>
        </div>
    </div>
</div>

{{-- Copy to Clipboard Script --}}
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        window.toast.success('📋 Kopyalandı: ' + text);
    }).catch(err => {
        window.toast.error('Kopyalama başarısız');
    });
}

// ESC tuşu ile kapat
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        Alpine.store('modal')?.close();
    }
});
</script>

<style>
/* Smooth transitions */
[x-cloak] { display: none !important; }

.inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2-sm {
    @apply px-3 py-1.5 text-sm;
}

kbd {
    @apply font-mono text-xs;
}
</style>
