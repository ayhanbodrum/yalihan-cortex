@extends('admin.layouts.neo')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                {{ $kategori->icon ?? '🏠' }}{{ $kategori->name }}
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                Yayın Tipi Yöneticisi - Tek Sayfada Yönetim
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.property-type-manager.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 text-white font-semibold rounded-lg shadow-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200 dark:bg-gray-700 dark:hover:bg-gray-600">
                <i class="fas fa-arrow-left mr-2"></i>
                Geri Dön
            </a>
        </div>
    </div>

    <!-- 1. Ana Kategori > Alt Kategori > Yayın Tipi Hiyerarşisi -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                1️⃣ {{ $kategori->name }} > Alt Kategoriler
            </h2>
            <button onclick="showAddYayinTipiModal()" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-lg shadow-lg hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 active:scale-95 text-sm">
                <i class="fas fa-plus mr-2"></i>
                Yayın Tipi Ekle
            </button>
        </div>

        <!-- Uyarı: Yanlış eklenen yayın tipleri -->
        @if(isset($yanlisEklenenYayinTipleri) && $yanlisEklenenYayinTipleri->isNotEmpty())
        <div class="mb-6 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
            <div class="flex items-start gap-3">
                <i class="fas fa-exclamation-triangle text-yellow-600 dark:text-yellow-400 mt-0.5"></i>
                <div class="flex-1">
                    <h4 class="text-sm font-semibold text-yellow-900 dark:text-yellow-100 mb-2">
                        ⚠️ Yanlış Eklenen Kayıtlar Tespit Edildi
                    </h4>
                    <p class="text-sm text-yellow-800 dark:text-yellow-200 mb-3">
                        Aşağıdaki kayıtlar <strong>alt kategori</strong> olarak eklenmiş ancak <strong>yayın tipi</strong> olmalı:
                    </p>
                    <ul class="list-disc list-inside text-sm text-yellow-800 dark:text-yellow-200 mb-3 space-y-1">
                        @foreach($yanlisEklenenYayinTipleri as $yanlis)
                        <li>
                            <strong>{{ $yanlis->name }}</strong>
                            (ID: {{ $yanlis->id }}, Seviye: {{ $yanlis->seviye }})
                        </li>
                        @endforeach
                    </ul>
                    <p class="text-sm text-yellow-800 dark:text-yellow-200 mb-3">
                        Bu kayıtları silip yukarıdaki <strong>"Yayın Tipi Ekle"</strong> butonunu kullanarak doğru şekilde ekleyin.
                    </p>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.ilan-kategorileri.index') }}?search={{ urlencode($yanlisEklenenYayinTipleri->first()->name) }}"
                           class="text-xs text-yellow-700 dark:text-yellow-300 hover:underline">
                            <i class="fas fa-edit mr-1"></i> Bu Kayıtları Düzenle
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Her Alt Kategori İçin Yayın Tipleri -->
        @if(($altKategoriler ?? collect())->isEmpty())
        <div class="mb-6 last:mb-0 border-b dark:border-gray-700 pb-6 last:border-0 last:pb-0">
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <i class="fas fa-info-circle text-blue-600 dark:text-blue-400 mt-0.5"></i>
                    <div class="flex-1">
                        <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-100 mb-2">
                            Bu kategoriye bağlı alt kategori bulunmuyor
                        </h4>
                        <p class="text-sm text-blue-800 dark:text-blue-200 mb-3">
                            Alt kategori oluşturduktan sonra yayın tipi eşleştirmelerini burada yönetebilirsiniz.
                        </p>

                        <!-- Debug Bilgisi (Sadece geliştirme modunda) -->
                        @if(config('app.debug'))
                        <div class="mt-3 p-3 bg-gray-100 dark:bg-gray-900 rounded text-xs font-mono">
                            <div class="text-gray-700 dark:text-gray-300 mb-1">
                                <strong>Debug Info:</strong>
                            </div>
                            <div class="text-gray-600 dark:text-gray-400 space-y-1">
                                <div>Kategori ID: <span class="font-bold">{{ $kategori->id }}</span></div>
                                <div>Kategori Adı: <span class="font-bold">{{ $kategori->name }}</span></div>
                                <div>Parent ID: <span class="font-bold">{{ $kategori->parent_id ?? 'NULL' }}</span></div>
                                <div>Seviye: <span class="font-bold">{{ $kategori->seviye }}</span></div>
                            </div>
                            <div class="mt-2 pt-2 border-t border-gray-300 dark:border-gray-700">
                                <a href="{{ route('admin.ilan-kategorileri.create') }}?parent_id={{ $kategori->id }}&seviye=1"
                                   class="text-blue-600 dark:text-blue-400 hover:underline">
                                    ➕ Alt Kategori Oluştur
                                </a>
                            </div>
                        </div>
                        @endif

                        <!-- Hızlı Erişim -->
                        <div class="mt-3 flex gap-2">
                            <a href="{{ route('admin.ilan-kategorileri.index') }}"
                               class="text-xs text-blue-600 dark:text-blue-400 hover:underline">
                                <i class="fas fa-list mr-1"></i> Tüm Kategorileri Görüntüle
                            </a>
                            <span class="text-blue-300 dark:text-blue-700">|</span>
                            <a href="{{ route('admin.ilan-kategorileri.create') }}?parent_id={{ $kategori->id }}&seviye=1"
                               class="text-xs text-blue-600 dark:text-blue-400 hover:underline">
                                <i class="fas fa-plus mr-1"></i> Yeni Alt Kategori Ekle
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @foreach($altKategoriler as $altKategori)
        <div class="mb-6 last:mb-0 border-b dark:border-gray-700 pb-6 last:border-0 last:pb-0">
            <!-- Alt Kategori Başlığı -->
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $altKategori->name }}
                </h3>
                <span class="text-xs px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-full">
                    {{ $altKategoriYayinTipleri[$altKategori->id]->count() ?? 0 }} yayın tipi
                </span>
            </div>

            <!-- Bu Alt Kategorinin Yayın Tipleri -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                @foreach($allYayinTipleri as $yayinTipi)
                    @php
                        // ✅ FIX: Pivot tablo kontrolü (alt_kategori_yayin_tipi)
                        $activeIds = $altKategoriYayinTipleri[$altKategori->id] ?? collect([]);
                        $active = $activeIds->contains($yayinTipi->id);
                    @endphp

                    <label class="flex items-center p-3 border rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer {{ $active ? 'bg-green-50 dark:bg-green-900/20 border-green-300 dark:border-green-700' : 'bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600' }}">
                        <input type="checkbox"
                               class="rounded mr-2 yayin-tipi-toggle"
                               data-alt-kategori-id="{{ $altKategori->id }}"
                               data-yayin-tipi-id="{{ $yayinTipi->id }}"
                               data-yayin-tipi-name="{{ $yayinTipi->yayin_tipi }}"
                               {{ $active ? 'checked' : '' }}
                               onchange="toggleYayinTipiRelation(this)">
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $yayinTipi->yayin_tipi }}</span>
                    </label>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>

    <!-- 2. Relations Grid (Gerçek Veriler) -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                    🔗 Alan İlişkileri
                </h2>
                <span class="text-xs px-2 py-1 bg-lime-100 dark:bg-lime-900 text-lime-800 dark:text-lime-200 rounded-full">
                    {{ count($fieldDependencies) }} Alan
                </span>
            </div>
            <a href="{{ route('admin.property-type-manager.field-dependencies', $kategori->id) }}"
               class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white font-semibold rounded-lg shadow-lg hover:from-green-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 active:scale-95 text-sm">
                <i class="fas fa-cog mr-2"></i>
                Alan İlişkilerini Yönet
            </a>
        </div>

        @if(count($fieldDependencies) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                Alan
                            </th>
                            @foreach($allYayinTipleri as $yayinTipi)
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                {{ $yayinTipi->name ?? $yayinTipi->yayin_tipi }}
                            </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($fieldDependencies as $fieldSlug => $fieldData)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-900">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span class="text-xl mr-2">{{ $fieldData['field_icon'] }}</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $fieldData['field_name'] }}
                                    </span>
                                </div>
                            </td>
                            @foreach($allYayinTipleri as $yayinTipi)
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @php
                                    $enabled = $fieldData['yayin_tipleri'][$yayinTipi->id] ?? false;
                                    // ✅ Field dependency ID'yi ID ya da slug ile bul
                                    $yayinTipiKeyId = (string)$yayinTipi->id;
                                    $yayinTipiKeySlug = $yayinTipi->slug ?? $yayinTipi->yayin_tipi;
                                    $fieldDep = \App\Models\KategoriYayinTipiFieldDependency::where('kategori_slug', $kategori->slug)
                                        ->where('field_slug', $fieldSlug)
                                        ->where(function($q) use ($yayinTipiKeyId, $yayinTipiKeySlug) {
                                            $q->where('yayin_tipi', $yayinTipiKeyId)
                                              ->orWhere('yayin_tipi', $yayinTipiKeySlug);
                                        })
                                        ->first();
                                    $fieldDepId = $fieldDep ? $fieldDep->id : null;
                                @endphp
                                <input type="checkbox"
                                       class="rounded field-dependency-toggle"
                                       data-field-id="{{ $fieldDepId }}"
                                       data-field-slug="{{ $fieldSlug }}"
                                       data-field-name="{{ $fieldData['field_name'] }}"
                                       data-field-type="{{ $fieldData['field_type'] }}"
                                       data-field-category="{{ $fieldData['field_category'] ?? 'general' }}"
                                       data-yayin-tipi-id="{{ $yayinTipi->id }}"
                                       data-yayin-tipi-slug="{{ $yayinTipiKeySlug }}"
                                       {{ $enabled ? 'checked' : '' }}
                                       onchange="toggleFieldDependency(this)">
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-8">
                <i class="fas fa-inbox text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                <p class="text-gray-500 dark:text-gray-400 mb-4">
                    Bu kategori için alan ilişkisi tanımlı değil.
                </p>
                <a href="{{ route('admin.property-type-manager.field-dependencies', $kategori->id) }}"
                   class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-lg shadow-lg hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 active:scale-95">
                    <i class="fas fa-plus mr-2"></i>
                    Alan İlişkilerini Tanımla
                </a>
            </div>
        @endif
    </div>

    <!-- 4. Features Toggle -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
            ✨ Özellikler
        </h2>

        @foreach($featureCategories as $category)
        <div class="mb-6 last:mb-0">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                {{ $category->name }}
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                @foreach($category->features as $feature)
                <label class="flex items-center p-3 border rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                    <input type="checkbox"
                            class="rounded mr-2"
                            data-feature-id="{{ $feature->id }}"
                            {{ $feature->status ? 'checked' : '' }}>
                    <span class="text-sm text-gray-900 dark:text-white">{{ $feature->name }}</span>
                </label>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>

    <!-- Save Button -->
    <div class="mt-6 flex justify-between items-center">
        <!-- Bulk Actions -->
        <div class="flex gap-2">
            <button onclick="toggleAllYayinTipleri(true)" class="inline-flex items-center px-3 py-2 bg-gray-600 text-white font-semibold rounded-lg shadow-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200 text-sm dark:bg-gray-700 dark:hover:bg-gray-600">
                <i class="fas fa-check-square mr-2"></i>
                Tümünü Seç
            </button>
            <button onclick="toggleAllYayinTipleri(false)" class="inline-flex items-center px-3 py-2 bg-gray-600 text-white font-semibold rounded-lg shadow-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200 text-sm dark:bg-gray-700 dark:hover:bg-gray-600">
                <i class="fas fa-square mr-2"></i>
                Tümünü Kaldır
            </button>
        </div>

        <!-- Save Button -->
        <button id="saveBtn" onclick="saveChanges()" class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-bold rounded-lg shadow-lg hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 active:scale-95 text-lg">
            <i class="fas fa-save mr-2"></i>
            Tüm Değişiklikleri Kaydet
        </button>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-8 text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-lime-600 mx-auto mb-4"></div>
            <p class="text-gray-900 dark:text-white font-semibold">Kaydediliyor...</p>
        </div>
    </div>

    <!-- Success Toast -->
    <div id="successToast" class="hidden fixed top-4 right-4 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg z-50">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <span>Değişiklikler başarıyla kaydedildi!</span>
        </div>
    </div>

    <!-- Error Toast -->
    <div id="errorToast" class="hidden fixed top-4 right-4 bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg z-50">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <span>Bir hata oluştu!</span>
        </div>
    </div>
</div>

<!-- Modal: Yeni Yayın Tipi Ekle -->
<div id="addYayinTipiModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-8 max-w-md w-full">
        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
            ➕ Yeni Yayın Tipi Ekle
        </h3>

        <form id="addYayinTipiForm" onsubmit="addYayinTipi(event)">
            <!-- Alt Kategori Seçimi -->
            @if(($altKategoriler ?? collect())->isNotEmpty())
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Alt Kategori Seçin
                </label>
                <select id="modalAltKategori" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                    <option value="">Seçin...</option>
                    @foreach($altKategoriler as $altKat)
                        <option value="{{ $altKat->id }}">{{ $altKat->name }}</option>
                    @endforeach
                </select>
            </div>
            @else
            <input type="hidden" id="modalAltKategori" value="">
            <div class="mb-4 text-sm text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded p-3">
                Bu kategori için alt kategori bulunmuyor. Yayın tipi doğrudan ana kategoriye eklenecek.
            </div>
            @endif

            <!-- Yayın Tipi Adı -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Yayın Tipi Adı
                </label>
                <input type="text"
                       id="modalYayinTipi"
                       required
                       placeholder="Örn: Satılık, Kiralık, Kat Karşılığı"
                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
            </div>

            <!-- Butonlar -->
            <div class="flex gap-3">
                <button type="button"
                        onclick="closeAddYayinTipiModal()"
                        class="inline-flex items-center justify-center px-4 py-2 bg-gray-600 text-white font-semibold rounded-lg shadow-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200 flex-1 dark:bg-gray-700 dark:hover:bg-gray-600">
                    İptal
                </button>
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-lg shadow-lg hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 active:scale-95 flex-1">
                    <i class="fas fa-plus mr-2"></i>
                    Ekle
                </button>
            </div>
        </form>
        </div>
    </div>
</div>

@push('scripts')
<script data-timestamp="{{ time() }}">
// 🔄 Property Type Manager - Optimized v5.0 - {{ time() }}
console.log('✅ PropertyTypeManager scripts loaded! v5.0 (Optimized)');

// ============================================================================
// 🎯 UTILITY FUNCTIONS & CONFIGURATION
// ============================================================================

// 🔐 CSRF Token Cache - Tek seferlik al, tekrar kullan
const PropertyTypeManager = {
    csrfToken: null,
    debounceTimers: {},

    // CSRF token'ı initialize et
    init() {
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!this.csrfToken) {
            console.error('❌ CSRF token NOT FOUND!');
            this.showError('CSRF token eksik! Lütfen sayfayı yenileyin (F5).');
        } else {
            console.log('✅ CSRF token cached:', this.csrfToken.substring(0, 15) + '...');
        }
        return this;
    },

    // Generic AJAX request handler
    async request(url, data = {}, method = 'POST') {
        if (!this.csrfToken) {
            throw new Error('CSRF token not initialized');
        }

        const response = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': this.csrfToken
            },
            body: JSON.stringify(data)
        });

        // Content-Type validation
        const contentType = response.headers.get('content-type');
        if (!contentType?.includes('application/json')) {
            const text = await response.text();
            console.error('❌ Non-JSON response:', text.substring(0, 500));
            throw new Error('Server returned HTML instead of JSON');
        }

        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || `HTTP ${response.status}`);
        }

        return response.json();
    },

    // Debounce helper
    debounce(key, callback, delay = 300) {
        clearTimeout(this.debounceTimers[key]);
        this.debounceTimers[key] = setTimeout(callback, delay);
    },

    // Toast notifications
    showSuccess(message) {
        if (window.toast?.success) {
            window.toast.success(message);
        } else {
            const toast = document.getElementById('successToast');
            if (toast) {
                toast.querySelector('span').textContent = message;
                toast.classList.remove('hidden');
                setTimeout(() => toast.classList.add('hidden'), 3000);
            }
        }
    },

    showError(message) {
        if (window.toast?.error) {
            window.toast.error(message);
        } else {
            const toast = document.getElementById('errorToast');
            if (toast) {
                toast.querySelector('span').textContent = message;
                toast.classList.remove('hidden');
                setTimeout(() => toast.classList.add('hidden'), 3000);
            }
        }
    },

    // Loading overlay
    showLoading(show = true) {
        const overlay = document.getElementById('loadingOverlay');
        overlay?.classList.toggle('hidden', !show);
    }
};

// Initialize on load
PropertyTypeManager.init();

// ============================================================================
// 🎯 MAIN TOGGLE FUNCTIONS (Optimized)
// ============================================================================

// Yayın Tipi Toggle (Alt Kategori ↔ Yayın Tipi İlişkisi)
async function toggleYayinTipiRelation(checkbox) {
    const { altKategoriId, yayinTipiId, yayinTipiName } = checkbox.dataset;
    const enabled = checkbox.checked;
    const label = checkbox.closest('label');

    // Loading state
    checkbox.disabled = true;
    label?.classList.add('opacity-50', 'cursor-wait');

    try {
        const data = await PropertyTypeManager.request(
            '{{ route("admin.property-type-manager.toggle-yayin-tipi", $kategori->id) }}',
            {
                alt_kategori_id: altKategoriId,
                yayin_tipi_id: yayinTipiId,
                enabled: enabled
            }
        );

        if (data.success) {
            // Visual feedback - Optimized class toggle
            const classes = {
                active: ['bg-green-50', 'dark:bg-green-900/20', 'border-green-300', 'dark:border-green-700'],
                inactive: ['bg-gray-50', 'dark:bg-gray-700', 'border-gray-300', 'dark:border-gray-600']
            };

            if (label) {
                label.classList.remove(...(enabled ? classes.inactive : classes.active));
                label.classList.add(...(enabled ? classes.active : classes.inactive));
            }

            PropertyTypeManager.showSuccess(`${yayinTipiName} ${enabled ? 'etkinleştirildi' : 'devre dışı bırakıldı'}`);
            console.log('✅ Yayın tipi ilişkisi güncellendi:', data);
        }
    } catch (error) {
        console.error('❌ Toggle hatası:', error);
        checkbox.checked = !enabled; // Revert
        PropertyTypeManager.showError(error.message || 'Güncelleme başarısız!');
    } finally {
        // Reset loading state
        checkbox.disabled = false;
        label?.classList.remove('opacity-50', 'cursor-wait');
    }
}

// Field Dependency Toggle (Alan İlişkileri)
async function toggleFieldDependency(checkbox) {
    const { fieldId, fieldSlug, fieldName, fieldType, fieldCategory, yayinTipiId, yayinTipiSlug } = checkbox.dataset;
    const enabled = checkbox.checked;
    const upsertMode = !fieldId;

    // Loading state
    checkbox.disabled = true;

    try {
        const payload = upsertMode ? {
            kategori_slug: '{{ $kategori->slug }}',
            field_slug: fieldSlug,
            field_name: fieldName || 'Field',
            field_type: fieldType || 'text',
            field_category: fieldCategory || 'general',
            yayin_tipi_id: yayinTipiId,
            yayin_tipi: yayinTipiSlug,
            enabled: enabled
        } : {
            field_id: parseInt(fieldId),
            enabled: enabled
        };

        const data = await PropertyTypeManager.request(
            '{{ route("admin.property-type-manager.toggle-field-dependency") }}',
            payload
        );

        if (data.success) {
            // Upsert mode: field_id'yi DOM'a kaydet
            if (upsertMode && data.data?.field_id) {
                checkbox.setAttribute('data-field-id', data.data.field_id);
            }

            PropertyTypeManager.showSuccess('Alan ilişkisi güncellendi');
            console.log('✅ Field dependency güncellendi:', data);
        }
    } catch (error) {
        console.error('❌ Toggle hatası:', error);
        checkbox.checked = !enabled; // Revert
        PropertyTypeManager.showError(error.message || 'Alan ilişkisi güncellenemedi!');
    } finally {
        checkbox.disabled = false;
    }
}

// ============================================================================
// 🎯 MODAL MANAGEMENT
// ============================================================================

function showAddYayinTipiModal() {
    const modal = document.getElementById('addYayinTipiModal');
    modal?.classList.remove('hidden');
    modal?.classList.add('flex');
    // Focus on input
    setTimeout(() => document.getElementById('modalYayinTipi')?.focus(), 100);
}

function closeAddYayinTipiModal() {
    const modal = document.getElementById('addYayinTipiModal');
    modal?.classList.add('hidden');
    modal?.classList.remove('flex');
    document.getElementById('addYayinTipiForm')?.reset();
}

// Yeni Yayın Tipi Ekle
async function addYayinTipi(e) {
    e.preventDefault();

    const name = document.getElementById('modalYayinTipi')?.value?.trim();
    if (!name) {
        PropertyTypeManager.showError('Yayın tipi adı gerekli');
        return;
    }

    PropertyTypeManager.showLoading(true);

    try {
        const data = await PropertyTypeManager.request(
            "{{ route('admin.property-type-manager.create-yayin-tipi', $kategori->id) }}",
            { name }
        );

        if (data.success) {
            PropertyTypeManager.showSuccess('Yayın tipi eklendi! Sayfa yenileniyor...');
            setTimeout(() => location.reload(), 1000);
        }
    } catch (error) {
        PropertyTypeManager.showLoading(false);
        PropertyTypeManager.showError(error.message || 'Ekleme başarısız!');
    }
}

// Modal: Outside click & ESC key handler
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('addYayinTipiModal');
    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeAddYayinTipiModal();
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeAddYayinTipiModal();
            }
        });
    }
});

// ============================================================================
// 🎯 BULK OPERATIONS
// ============================================================================

// Bulk Toggle - Debounced
function toggleAllYayinTipleri(checked) {
    PropertyTypeManager.debounce('bulkToggle', () => {
        const checkboxes = document.querySelectorAll('.yayin-tipi-toggle');
        const count = Array.from(checkboxes).filter(cb => cb.checked !== checked).length;

        if (count === 0) {
            PropertyTypeManager.showSuccess('Tüm değerler zaten bu durumda');
            return;
        }

        PropertyTypeManager.showLoading(true);

        let completed = 0;
        checkboxes.forEach(cb => {
            if (cb.checked !== checked) {
                cb.checked = checked;
                toggleYayinTipiRelation(cb).finally(() => {
                    completed++;
                    if (completed === count) {
                        PropertyTypeManager.showLoading(false);
                        PropertyTypeManager.showSuccess(`${count} değişiklik tamamlandı`);
                    }
                });
            }
        });
    }, 100);
}

// Toplu Kaydetme (Bulk Save)
async function saveChanges() {
    PropertyTypeManager.showLoading(true);

    try {
        // Tüm değişiklikleri topla
        const changes = {
            yayin_tipleri: [],
            field_dependencies: [],
            features: []
        };

        // Yayın tipleri
        document.querySelectorAll('[data-alt-kategori-id][data-yayin-tipi]').forEach(cb => {
            if (cb.checked !== (cb.dataset.active === 'true')) {
                changes.yayin_tipleri.push({
                    kategori_id: cb.dataset.altKategoriId,
                    yayin_tipi: cb.dataset.yayinTipi,
                    status: cb.checked
                });
            }
        });

        // Alan ilişkileri
        document.querySelectorAll('[data-field-slug][data-yayin-tipi]').forEach(cb => {
            changes.field_dependencies.push({
                kategori_slug: '{{ $kategori->slug }}',
                yayin_tipi: cb.dataset.yayinTipi,
                field_slug: cb.dataset.fieldSlug,
                field_name: cb.dataset.fieldName || 'Field',
                field_type: cb.dataset.fieldType || 'text',
                field_category: cb.dataset.fieldCategory || 'general',
                enabled: cb.checked
            });
        });

        // Özellikler
        document.querySelectorAll('[data-feature-id]').forEach(cb => {
            changes.features.push({
                id: cb.dataset.featureId,
                enabled: cb.checked
            });
        });

        const totalChanges = changes.yayin_tipleri.length +
                           changes.field_dependencies.length +
                           changes.features.length;

        if (totalChanges === 0) {
            PropertyTypeManager.showLoading(false);
            PropertyTypeManager.showSuccess('Değişiklik yok');
            return;
        }

        const data = await PropertyTypeManager.request(
            '{{ route("admin.property-type-manager.bulk-save", $kategori->id) }}',
            changes
        );

        if (data.success) {
            PropertyTypeManager.showSuccess(`${totalChanges} değişiklik kaydedildi! Sayfa yenileniyor...`);
            setTimeout(() => location.reload(), 2000);
        }
    } catch (error) {
        PropertyTypeManager.showLoading(false);
        PropertyTypeManager.showError(error.message || 'Kaydetme başarısız!');
        console.error('❌ Bulk save error:', error);
    }
}
</script>
@endpush
@endsection
