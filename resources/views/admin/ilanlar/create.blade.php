@extends('admin.layouts.neo')

@section('content')
    <div class="space-y-4">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Yeni İlan Oluştur</h1>
                <p class="mt-1.5 text-sm text-gray-600 dark:text-gray-400">İlan bilgilerini doldurun ve yayınlayın</p>
            </div>
            <a href="{{ route('admin.ilanlar.index') }}"
                class="inline-flex items-center px-4 py-2.5 bg-gray-600 text-white font-medium rounded-lg shadow-sm hover:bg-gray-700 hover:shadow-md active:scale-95 focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Geri Dön
            </a>
        </div>

        {{-- Draft Restore Banner (Hidden by default, shown by JavaScript if draft exists) --}}
        <div id="draft-restore-banner"></div>

        {{-- Form Progress Indicator --}}
        <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-3 mb-4">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-sm font-medium text-gray-900 dark:text-white">Form İlerlemesi</span>
                <span id="form-progress-text" class="text-sm text-gray-500 dark:text-gray-400">%0 tamamlandı</span>
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-800 rounded-full h-2">
                <div id="form-progress-bar" class="h-full bg-red-500 rounded-full transition-all duration-500"
                    style="width: 0%"></div>
            </div>
            <div class="flex items-center justify-between mt-1.5">
                <span id="save-indicator" class="text-xs text-gray-400 flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Otomatik kayıt aktif
                </span>
                <span class="text-xs text-gray-400">Her 30 saniyede</span>
            </div>
        </div>

        <!-- Main Form -->
        <form id="ilan-create-form" method="POST" action="{{ route('admin.ilanlar.store') }}" enctype="multipart/form-data"
            x-data="{ selectedSite: null, selectedPerson: null }">
            @csrf

            <div class="space-y-4">
                <!-- Section 1: Temel Bilgiler + AI Yardımcısı -->
                <div
                    class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200">
                    @include('admin.ilanlar.components.basic-info')
                    <div class="border-t border-gray-200 dark:border-gray-700"></div>
                    <div id="ai-assistant-panel" class="p-4">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-purple-600 to-indigo-600 text-white shadow">
                                🤖
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Dijital Danışman</h3>
                                <p class="text-xs text-gray-600 dark:text-gray-400">Seçimlerinize göre başlık, açıklama ve alan önerileri üretir</p>
                            </div>
                            <div class="ml-auto">
                                <button type="button" id="ai-undo-btn" class="px-2 py-1 text-xs rounded bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 border border-gray-300 dark:border-gray-700">Geri Al</button>
                            </div>
                        </div>

                        <div class="flex items-center justify-between mb-3">
                            <div class="text-xs text-gray-600 dark:text-gray-400">
                                <span id="ai-context-status" class="font-semibold">Bağlam Durumu: %0</span>
                                <span id="ai-missing-hints" class="ml-2"></span>
                            </div>
                            <div class="flex items-center gap-2"><div class="w-24 h-2 bg-gray-200 dark:bg-gray-800 rounded overflow-hidden"><div id="ai-readiness-bar-fill" class="h-2 bg-green-500 dark:bg-green-400" style="width:0%"></div></div><div id="ai-readiness-badge" class="text-xs px-2 py-1 rounded bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300">Hazır değil</div></div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <button type="button" id="ai-generate-title" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v8m-4-4h8"/></svg>
                                Başlık Öner
                            </button>
                            <button type="button" id="ai-generate-description" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg hover:from-green-700 hover:to-emerald-700 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h8M7 16h6"/></svg>
                                Açıklama Öner
                            </button>
                            <button type="button" id="ai-price-suggestion" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-yellow-500 to-orange-600 text-white rounded-lg hover:from-yellow-600 hover:to-orange-700 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                Fiyat Öner
                            </button>
                            <button type="button" id="ai-field-suggestion" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-cyan-600 to-blue-600 text-white rounded-lg hover:from-cyan-700 hover:to-blue-700 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h10M7 16h8"/></svg>
                                Alan Önerileri
                            </button>
                        </div>

                        <div id="ai-suggestions" class="mt-4 space-y-2"></div>
                        <div class="flex justify-end mt-2">
                            <button type="button" id="ai-apply-all" class="px-3 py-1.5 text-xs rounded bg-blue-100 text-blue-700 hover:bg-blue-200">Tümünü Uygula</button>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Kategori Sistemi -->
                <div
                    class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200">
                    @include('admin.ilanlar.components.category-system')
                </div>

                <!-- Section 3: Lokasyon ve Harita -->
                <div
                    class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200">
                    @include('admin.ilanlar.components.location-map')
                </div>

                <!-- Section 4: İlan Özellikleri (Field Dependencies) -->
                <div
                    class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200">
                    {{-- Smart Field Organizer (Templates & AI) --}}
                    @include('admin.ilanlar.components.smart-field-organizer')

                    {{-- Field Dependencies (Now with Accordion + Progress!) --}}
                    @include('admin.ilanlar.components.field-dependencies-dynamic')
                </div>

                

                <!-- Section 4.5: Yazlık Amenities (Features/EAV - Sadece Yazlık kategorisi için) -->
                <div class="kategori-specific-section" data-show-for-categories="yazlik" style="display: none;">
                    @include('admin.ilanlar.partials.yazlik-features')
                </div>

                <!-- Section 4.6: Bedroom Layout (Yazlık için kritik!) -->
                <div class="kategori-specific-section" data-show-for-categories="yazlik" style="display: none;">
                    @include('admin.ilanlar.components.bedroom-layout-manager', [
                        'ilan' => new \App\Models\Ilan(),
                    ])
                </div>

                <!-- Section 4.7: Photo Upload (Tüm kategoriler için) -->
                <div
                    class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200">
                    @include('admin.ilanlar.components.photo-upload-manager', [
                        'ilan' => new \App\Models\Ilan(),
                    ])
                </div>

                <!-- Section 4.8: Event/Booking Calendar (Yazlık için) -->
                <div class="kategori-specific-section" data-show-for-categories="yazlik" style="display: none;">
                    @include('admin.ilanlar.components.event-booking-manager', [
                        'ilan' => new \App\Models\Ilan(),
                    ])
                </div>

                <!-- Section 4.9: Season Pricing (Yazlık için) -->
                <div class="kategori-specific-section" data-show-for-categories="yazlik" style="display: none;">
                    @include('admin.ilanlar.components.season-pricing-manager', [
                        'ilan' => new \App\Models\Ilan(),
                    ])
                </div>

                <!-- Section 5: Fiyat Yönetimi -->
                <div
                    class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200">
                    @include('admin.ilanlar.components.price-management')
                </div>

                <!-- Section 6: Kişi Bilgileri (CRM) -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm"
                    x-data="{ selectedPerson: null }">
                    @include('admin.ilanlar.partials.stable._kisi-secimi')
                </div>

                <!-- Section 7: Site/Apartman Bilgileri (Sadece Konut kategorisi için) -->
                <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200 kategori-specific-section"
                    data-show-for-categories="konut" style="display: none;">
                    @include('admin.ilanlar.components.site-apartman-context7')
                </div>

                <!-- Section 8: Anahtar Bilgileri (Sadece Konut kategorisi için) -->
                <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200 kategori-specific-section"
                    data-show-for-categories="konut" style="display: none;">
                    @include('admin.ilanlar.components.key-management')
                </div>

                <!-- 🎨 Section 10: Yayın Durumu (Context7 Optimized) -->
                <div
                    class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200 p-5">
                    <!-- Section Header -->
                    <div class="flex items-center gap-3 mb-4 pb-3 border-b border-gray-200 dark:border-gray-700">
                        <div
                            class="flex items-center justify-center w-9 h-9 rounded-lg bg-gradient-to-br from-green-500 to-emerald-600 text-white shadow-md font-semibold text-sm">
                            10
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Yayın Durumu
                            </h2>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">İlanınızın durumu ve öncelik seviyesi
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Status - Enhanced (Context7 Fixed) -->
                        <div class="group">
                            <label for="status"
                                class="block text-sm font-medium text-gray-900 dark:text-white mb-1.5 flex items-center gap-2">
                                <span
                                    class="flex items-center justify-center w-5 h-5 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 text-xs font-semibold">
                                    1
                                </span>
                                Status
                                <span class="text-red-500 font-semibold">*</span>
                            </label>
                            <div class="relative">
                                <select name="status" id="status" required
                                    class="w-full px-4 py-2.5 text-base
                                       border border-gray-300 dark:border-gray-600
                                       rounded-lg
                                       bg-white dark:bg-gray-800
                                       text-black dark:text-white
                                       focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:focus:border-green-400
                                       transition-all duration-200
                                       hover:border-gray-400 dark:hover:border-gray-500
                                       cursor-pointer
                                       focus:shadow-md
                                       appearance-none">
                                    <option value="">Bir durum seçin...</option>
                                    <option value="Taslak" {{ old('status') == 'Taslak' ? 'selected' : '' }}>📝 Taslak
                                    </option>
                                    <option value="Aktif" {{ old('status', 'Aktif') == 'Aktif' ? 'selected' : '' }}>✅ Aktif
                                    </option>
                                    <option value="Pasif" {{ old('status') == 'Pasif' ? 'selected' : '' }}>⏸️ Pasif
                                    </option>
                                    <option value="Beklemede" {{ old('status') == 'Beklemede' ? 'selected' : '' }}>⏳
                                        Beklemede</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400 group-focus-within:text-green-500 transition-colors"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                            @error('status')
                                <div
                                    class="mt-2 flex items-center gap-2 text-sm text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 px-4 py-2.5 rounded-lg">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Öncelik Seviyesi - Enhanced -->
                        <div class="group">
                            <label for="oncelik"
                                class="block text-sm font-medium text-gray-900 dark:text-white mb-1.5 flex items-center gap-2">
                                <span
                                    class="flex items-center justify-center w-5 h-5 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 text-xs font-semibold">
                                    2
                                </span>
                                Öncelik Seviyesi
                            </label>
                            <div class="relative">
                                <select name="oncelik" id="oncelik"
                                    class="w-full px-4 py-2.5 text-base
                                       border border-gray-300 dark:border-gray-600
                                       rounded-lg
                                       bg-white dark:bg-gray-800
                                       text-black dark:text-white
                                       focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:focus:border-green-400
                                       transition-all duration-200
                                       hover:border-gray-400 dark:hover:border-gray-500
                                       cursor-pointer
                                       focus:shadow-md
                                       appearance-none">
                                    <option value="normal" {{ old('oncelik', 'normal') == 'normal' ? 'selected' : '' }}>📋
                                        Normal</option>
                                    <option value="yuksek" {{ old('oncelik') == 'yuksek' ? 'selected' : '' }}>⭐ Yüksek
                                    </option>
                                    <option value="acil" {{ old('oncelik') == 'acil' ? 'selected' : '' }}>🚨 Acil
                                    </option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400 group-focus-within:text-green-500 transition-colors"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 🎨 Form Actions (Context7 Optimized) -->
            <div class="sticky bottom-4 z-20">
                <div
                    class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm p-4">
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                        <!-- Cancel Button -->
                        <a href="{{ route('admin.ilanlar.index') }}"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5
                              bg-gray-100 dark:bg-gray-800
                              hover:bg-gray-200 dark:hover:bg-gray-700
                              border border-gray-300 dark:border-gray-600
                              text-gray-900 dark:text-white font-medium rounded-lg
                              focus:ring-2 focus:ring-blue-500 focus:outline-none
                              transition-all duration-200
                              group">
                            <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            İptal Et
                        </a>

                        <!-- Action Buttons -->
                        <div class="flex gap-2">
                            <!-- Save Draft -->
                            <button type="button" id="save-draft-btn"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2.5
                                       bg-gray-200 dark:bg-gray-700
                                       hover:bg-gray-300 dark:hover:bg-gray-600
                                       border border-gray-300 dark:border-gray-600
                                       text-gray-800 dark:text-gray-200 font-medium rounded-lg
                                       focus:ring-2 focus:ring-blue-500 focus:outline-none
                                       transition-all duration-200
                                       group">
                                <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                </svg>
                                <span>Taslak Kaydet</span>
                            </button>

                            <!-- Publish Listing -->
                            <button type="submit" id="submit-btn"
                                class="inline-flex items-center justify-center gap-2 px-6 py-2.5
                                       bg-gradient-to-r from-green-500 to-emerald-600
                                       hover:from-green-600 hover:to-emerald-700
                                       text-white font-semibold rounded-lg
                                       shadow-md hover:shadow-lg
                                       focus:ring-2 focus:ring-green-500 focus:outline-none
                                       transition-all duration-200
                                       active:scale-95
                                       disabled:opacity-50 disabled:cursor-not-allowed
                                       group">
                                <svg id="submit-icon" class="w-5 h-5 group-hover:rotate-12 transition-transform"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span id="submit-text">İlanı Yayınla</span>
                                <svg id="submit-spinner" class="hidden w-5 h-5 mr-2 animate-spin" fill="none"
                                    viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    @push('scripts')
        <script>
            (function(){
                document.addEventListener('DOMContentLoaded', function(){
                    var inputs = Array.prototype.slice.call(document.querySelectorAll('input, select, textarea'));
                    inputs.forEach(function(el){
                        var id = el.id;
                        var hasForLabel = id && document.querySelector('label[for="'+id+'"]');
                        var parentLabel = el.closest('label');
                        if (!hasForLabel && !parentLabel) {
                            var name = el.getAttribute('name') || '';
                            var ph = el.getAttribute('placeholder') || '';
                            var text = ph || name || 'Alan';
                            el.setAttribute('aria-label', text);
                        }
                    });
                });
            })();
        </script>
        <script>
            (function(){
                try {
                    var containers = Array.prototype.slice.call(document.querySelectorAll('.context7-live-search'));
                    containers.forEach(function(ct){
                        var input = ct.querySelector('input[type="text"]');
                        var hidden = ct.querySelector('input[type="hidden"]');
                        var results = ct.querySelector('.context7-search-results');
                        var endpoint = ct.getAttribute('data-endpoint');
                        if (!input || !results || !endpoint) return;
                        var listboxId = results.id || ('ls-' + Math.random().toString(36).slice(2));
                        results.id = listboxId;
                        results.setAttribute('role','listbox');
                        input.setAttribute('aria-controls', listboxId);
                        input.setAttribute('aria-expanded','false');
                        var activeIndex = -1;
                        var opts = [];
                        var render = function(items){
                            results.innerHTML = '';
                            opts = items.slice(0, parseInt(ct.getAttribute('data-max-results')||'10'));
                            if (!opts.length) {
                                var empty = document.createElement('div');
                                empty.setAttribute('role','status');
                                empty.setAttribute('aria-live','polite');
                                empty.className = 'px-3 py-2 text-sm text-gray-500';
                                empty.textContent = 'Sonuç yok';
                                results.appendChild(empty);
                                input.setAttribute('aria-expanded','false');
                                activeIndex = -1;
                                return;
                            }
                            opts.forEach(function(it, idx){
                                var li = document.createElement('div');
                                li.className = 'px-3 py-2 cursor-pointer hover:bg-blue-50 dark:hover:bg-blue-900/20';
                                var primary = (it.name||it.full_name||it.email||'Kayıt');
                                var secondary = (it.email||it.phone||'');
                                li.innerHTML = secondary ? ('<div class="text-sm">'+primary+'</div><div class="text-xs text-gray-500">'+secondary+'</div>') : primary;
                                li.id = 'ls-opt-' + idx;
                                li.setAttribute('role','option');
                                li.addEventListener('click', function(){
                                    if (hidden && it.id) hidden.value = it.id;
                                    input.value = primary;
                                    results.innerHTML = '';
                                    input.setAttribute('aria-expanded','false');
                                });
                                results.appendChild(li);
                            });
                            activeIndex = opts.length ? 0 : -1;
                            if (activeIndex >= 0) {
                                input.setAttribute('aria-activedescendant', 'ls-opt-' + activeIndex);
                                Array.prototype.slice.call(results.children).forEach(function(el, i){ el.classList.toggle('bg-blue-50', i===activeIndex); });
                            }
                            input.setAttribute('aria-expanded', opts.length ? 'true' : 'false');
                        };
                        var fetcher;
                        input.addEventListener('input', function(){
                            var q = input.value.trim();
                            if (fetcher) clearTimeout(fetcher);
                            fetcher = setTimeout(function(){
                                if (q.length < 2) { results.innerHTML = ''; return; }
                                fetch(endpoint + '?q=' + encodeURIComponent(q))
                                    .then(function(r){ return r.json(); })
                                    .then(function(json){ render(Array.isArray(json)?json:(json.data||[])); })
                                    .catch(function(){ results.innerHTML = ''; });
                            }, 200);
                        });
                        input.addEventListener('keydown', function(ev){
                            if (!opts || !opts.length) return;
                            if (ev.key === 'ArrowDown') { ev.preventDefault(); activeIndex = Math.min(activeIndex+1, opts.length-1); }
                            if (ev.key === 'ArrowUp') { ev.preventDefault(); activeIndex = Math.max(activeIndex-1, 0); }
                            if (ev.key === 'Enter') {
                                ev.preventDefault();
                                var it = opts[activeIndex];
                                if (it) {
                                    input.value = (it.name||it.full_name||it.email||'Kayıt');
                                    if (hidden && it.id) hidden.value = it.id;
                                    results.innerHTML = '';
                                    input.setAttribute('aria-expanded','false');
                                }
                            }
                            if (ev.key === 'Escape') { results.innerHTML = ''; input.setAttribute('aria-expanded','false'); }
                            Array.prototype.slice.call(results.children).forEach(function(el, i){ el.classList.toggle('bg-blue-50', i===activeIndex); });
                            input.setAttribute('aria-activedescendant', 'ls-opt-' + activeIndex);
                        });
                    });
                } catch(e) {}
            })();
        </script>
        <script>
            (function(){
                try {
                    var dropdown = document.querySelector('div[ x-data] button[aria-label="Tema"]')?.closest('.flex.items-center.gap-2') || null;
                    var menuBtn = document.querySelector('div.relative[x-data] > button');
                    var menu = document.querySelector('div.relative[x-data] > div');
                    if (menuBtn && menu) {
                        menuBtn.setAttribute('aria-haspopup','true');
                        menuBtn.setAttribute('aria-expanded','false');
                        menu.setAttribute('role','menu');
                        Array.prototype.slice.call(menu.querySelectorAll('a, button')).forEach(function(el){ el.setAttribute('role','menuitem'); });
                        document.addEventListener('keydown', function(e){ if (e.key === 'Escape' && menu.style.display !== 'none') { e.preventDefault(); menuBtn.click(); menuBtn.setAttribute('aria-expanded','false'); } });
                        menuBtn.addEventListener('click', function(){ var expanded = menuBtn.getAttribute('aria-expanded') === 'true'; menuBtn.setAttribute('aria-expanded', expanded ? 'false' : 'true'); });
                    }
                } catch(e) {}
            })();
        </script>
        <script>
            (function(){
                try {
                    var appliedList = document.getElementById('ai-applied-list');
                    if (!appliedList) {
                        var container = document.getElementById('ai-assistant-panel');
                        if (container) {
                            appliedList = document.createElement('div');
                            appliedList.id = 'ai-applied-list';
                            appliedList.className = 'mt-2 space-y-1';
                            container.appendChild(appliedList);
                        }
                    }
                    var addApplied = function(type, text){
                        if (!appliedList) return;
                        var row = document.createElement('div');
                        row.className = 'text-xs px-2 py-1 rounded bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200';
                        row.textContent = (type+': '+text).slice(0,140);
                        appliedList.appendChild(row);
                    };
                    var bindApply = function(){
                        Array.prototype.slice.call(document.querySelectorAll('#ai-suggestions button[data-action="apply"]')).forEach(function(b){
                            b.addEventListener('click', function(){ var t = b.getAttribute('data-type')||'öneri'; var v = b.getAttribute('data-value')||b.textContent||''; addApplied(t, v); });
                        });
                    };
                    document.addEventListener('DOMContentLoaded', bindApply);
                    document.addEventListener('click', function(e){ if (e.target && e.target.matches('#ai-suggestions button[data-action="apply"]')) { var t = e.target.getAttribute('data-type')||'öneri'; var v = e.target.getAttribute('data-value')||e.target.textContent||''; addApplied(t, v); } });
                } catch(e) {}
            })();
        </script>
        <script>
            (function(){
                document.addEventListener('keydown', function(ev){
                    try {
                        if (ev.key === '+') { ev.preventDefault(); window.VanillaLocationManager && window.VanillaLocationManager.zoomIn && window.VanillaLocationManager.zoomIn(); }
                        if (ev.key === '-') { ev.preventDefault(); window.VanillaLocationManager && window.VanillaLocationManager.zoomOut && window.VanillaLocationManager.zoomOut(); }
                        if (ev.key.toLowerCase() === 'l') { ev.preventDefault(); window.VanillaLocationManager && window.VanillaLocationManager.getCurrentLocation && window.VanillaLocationManager.getCurrentLocation(); }
                    } catch(e) {}
                });
                var toolbars = Array.prototype.slice.call(document.querySelectorAll('[aria-controls="map"]'));
                toolbars.forEach(function(btn){ var p = btn.closest('div'); if (p) { p.setAttribute('role','toolbar'); p.setAttribute('aria-label','Harita araçları'); } });
            })();
        </script>
        <script>
            (function () {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]')?.value;
                const changeStack = [];

                function collectContext() {
                    const ctx = {};
                    // Kategori/Yayın Tipi (varsayılan seçim alanlarından topla; mevcut isimler kategori sistemine göre değişebilir)
                    ctx.kategori = document.querySelector('[name="kategori"]')?.value || document.querySelector('[data-selected-category]')?.getAttribute('data-selected-category') || '';
                    ctx.altKategori = document.querySelector('[name="alt_kategori"]')?.value || document.querySelector('[data-selected-subcategory]')?.getAttribute('data-selected-subcategory') || '';
                    ctx.yayinTipi = document.querySelector('[name="yayin_tipi"]')?.value || document.querySelector('[data-selected-publication]')?.getAttribute('data-selected-publication') || '';
                    // Lokasyon
                    ctx.il = document.querySelector('[name="il_id"]')?.value || document.querySelector('[data-selected-il]')?.getAttribute('data-selected-il') || '';
                    ctx.ilce = document.querySelector('[name="ilce_id"]')?.value || document.querySelector('[data-selected-ilce]')?.getAttribute('data-selected-ilce') || '';
                    ctx.mahalle = document.querySelector('[name="mahalle_id"]')?.value || document.querySelector('[data-selected-mahalle]')?.getAttribute('data-selected-mahalle') || '';
                    // Alanlar
                    ctx.odaSayisi = document.querySelector('[name="oda_sayisi"]')?.value || '';
                    ctx.metrekare = document.querySelector('[name="metrekare"]')?.value || '';
                    // Fiyat
                    ctx.fiyat = document.querySelector('[name="fiyat"]')?.value || '';
                    ctx.paraBirimi = document.querySelector('[name="para_birimi"]')?.value || '';
                    // Manzara gibi özellikler (checkbox/grup)
                    ctx.manzara = Array.from(document.querySelectorAll('[name="manzara[]"], [data-feature-group="manzara"] input[type="checkbox"]'))
                        .filter(el => el.checked)
                        .map(el => el.value || el.getAttribute('data-feature'));
                    return ctx;
                }

                function showSuggestion(title, body) {
                    const box = document.createElement('div');
                    box.className = 'p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700';
                    box.innerHTML = `<div class="text-sm font-semibold text-gray-900 dark:text-white mb-1">${title}</div><div class="text-sm text-gray-700 dark:text-gray-300">${body}</div>`;
                    document.getElementById('ai-suggestions')?.prepend(box);
                }

                function applyToForm(type, value) {
                    if (!type) return null;
                    let oldVal = null;
                    if (type === 'title') {
                        const el = document.querySelector('[name="ilan_basligi"], [name="title"]');
                        if (el) { oldVal = el.value; el.value = value; }
                    } else if (type === 'description') {
                        const el = document.querySelector('[name="aciklama"], [name="description"]');
                        if (el) { oldVal = el.value; el.value = value; }
                    } else if (type === 'price') {
                        const el = document.querySelector('[name="fiyat"], [name="price"]');
                        if (el) { oldVal = el.value; el.value = value; }
                    } else if (type === 'feature') {
                        const key = typeof value === 'object' ? (value.key || value.value || value.label || '') : String(value || '');
                        const val = typeof value === 'object' ? (value.value ?? '') : '';
                        const group = typeof value === 'object' ? (value.group || '') : '';

                        const fmap = (window.FieldDependenciesManager && (window.FieldDependenciesManager.getFeatureMap ? window.FieldDependenciesManager.getFeatureMap() : window.FieldDependenciesManager.featureMap)) || {};
                        let target = null;
                        if (fmap && key) {
                            const entries = Object.entries(fmap).map(([slug, m]) => ({ slug, m }));
                            const exactSlug = entries.find(e => e.slug && e.slug.toLowerCase() === key.toLowerCase() && (!group || (e.m.group || '') === group));
                            const exactLabel = entries.find(e => e.m.label && e.m.label.toLowerCase() === key.toLowerCase() && (!group || (e.m.group || '') === group));
                            target = exactSlug || exactLabel || null;
                        }

                        if (target && target.m && target.m.id) {
                            const el = document.getElementById(target.m.id);
                            if (el) {
                                if (el.tagName === 'SELECT') {
                                    oldVal = el.value;
                                    let setVal = val || key;
                                    let matched = false;
                                    for (const opt of Array.from(el.options)) {
                                        if (opt.value && opt.value.toLowerCase() === setVal.toLowerCase()) { el.value = opt.value; matched = true; break; }
                                        if (opt.text && opt.text.toLowerCase() === setVal.toLowerCase()) { el.value = opt.value; matched = true; break; }
                                    }
                                    if (!matched) { el.value = setVal; }
                                    el.dispatchEvent(new Event('change', { bubbles: true }));
                                } else if (el.type === 'checkbox' || el.type === 'radio') {
                                    oldVal = el.checked;
                                    el.checked = true;
                                    el.dispatchEvent(new Event('change', { bubbles: true }));
                                } else {
                                    oldVal = el.value;
                                    el.value = val || key;
                                    el.dispatchEvent(new Event('input', { bubbles: true }));
                                }
                                return oldVal;
                            }
                        }

                        const scopeSelector = group ? `[data-feature-group="${group}"]` : '';
                        const candidates = [
                            `${scopeSelector} input[type="checkbox"]`,
                            `${scopeSelector} input[type="radio"]`,
                            `${scopeSelector} select`,
                            `${scopeSelector} input[type="text"]`,
                            `${scopeSelector} input[type="number"]`,
                            `${scopeSelector} textarea`
                        ].map(s => s.trim()).join(', ');

                        const elements = Array.from(document.querySelectorAll(candidates));
                        for (const el of elements) {
                            const featSlug = el.getAttribute('data-feature') || '';
                            const featLabel = el.getAttribute('data-feature-label') || '';
                            const matchesSlug = featSlug && (featSlug.toLowerCase() === key.toLowerCase());
                            const matchesLabel = featLabel && (featLabel.toLowerCase() === key.toLowerCase());
                            if (matchesSlug || matchesLabel) {
                                if (el.tagName === 'SELECT') {
                                    oldVal = el.value;
                                    let setVal = val || key;
                                    let matched = false;
                                    for (const opt of Array.from(el.options)) {
                                        if (opt.value && opt.value.toLowerCase() === setVal.toLowerCase()) { el.value = opt.value; matched = true; break; }
                                        if (opt.text && opt.text.toLowerCase() === setVal.toLowerCase()) { el.value = opt.value; matched = true; break; }
                                    }
                                    if (!matched) { el.value = setVal; }
                                    el.dispatchEvent(new Event('change', { bubbles: true }));
                                } else if (el.type === 'checkbox' || el.type === 'radio') {
                                    oldVal = el.checked;
                                    el.checked = true;
                                    el.dispatchEvent(new Event('change', { bubbles: true }));
                                } else {
                                    oldVal = el.value;
                                    el.value = val || key;
                                    el.dispatchEvent(new Event('input', { bubbles: true }));
                                }
                                break;
                            }
                        }
                    }
                    return oldVal;
                }

                async function logChange(action, field, oldValue, newValue, ctx) {
                    try {
                        await postJSON('/admin/changelog', {
                            action,
                            entity_type: 'ilan',
                            entity_id: document.querySelector('[name="ilan_id"]')?.value || null,
                            field,
                            old_value: oldValue,
                            new_value: newValue,
                            source: 'ai-assistant',
                            context: ctx || collectContext()
                        });
                    } catch (e) {}
                }

                function showActionableSuggestion(type, title, body, payload) {
                    const box = document.createElement('div');
                    box.className = 'p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700';
                    box.innerHTML = `
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="text-sm font-semibold text-gray-900 dark:text-white mb-1">${title}</div>
                                <div class="text-sm text-gray-700 dark:text-gray-300">${body}</div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <button type="button" class="px-2 py-1 text-xs rounded bg-blue-600 text-white hover:bg-blue-700" data-action="apply">Uygula</button>
                                <button type="button" class="px-2 py-1 text-xs rounded bg-indigo-600 text-white hover:bg-indigo-700" data-action="copy">Kopyala</button>
                                <button type="button" class="px-2 py-1 text-xs rounded bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600" data-action="ignore">Görmezden Gel</button>
                            </div>
                        </div>
                    `;
                    box.dataset.type = type || '';
                    const valObj = typeof payload === 'object' ? payload : { value: payload ?? body };
                    box.dataset.value = JSON.stringify(valObj);
                    box.addEventListener('click', async (ev) => {
                        const btn = ev.target.closest('button[data-action]');
                        if (!btn) return;
                        const action = btn.getAttribute('data-action');
                        if (action === 'apply') {
                            const t = box.dataset.type;
                            let parsed = {};
                            try { parsed = JSON.parse(box.dataset.value || '{}'); } catch (e) {}
                            const v = parsed;
                            const oldVal = applyToForm(t, parsed.value ?? parsed.key ?? parsed.label ?? parsed);
                            let fieldName = t;
                            if (t === 'feature') {
                                const key = parsed.key ?? parsed.value ?? parsed.label ?? '';
                                const grp = parsed.group ? parsed.group + ':' : '';
                                fieldName = 'feature:' + grp + key;
                            }
                            changeStack.push({ type: t, key: parsed.key ?? parsed.value ?? parsed.label ?? '', group: parsed.group || '', old: oldVal, new: parsed.value ?? parsed });
                            await logChange('apply_suggestion', fieldName, oldVal, parsed.value ?? true);
                            box.remove();
                        } else if (action === 'ignore') {
                            await logChange('ignore_suggestion', box.dataset.type, null, null);
                            box.remove();
                        } else if (action === 'copy') {
                            const t = box.dataset.type;
                            let parsed = {};
                            try { parsed = JSON.parse(box.dataset.value || '{}'); } catch (e) {}
                            const textToCopy = String(parsed.value ?? parsed.key ?? parsed.label ?? body ?? '');
                            navigator.clipboard && navigator.clipboard.writeText(textToCopy);
                            box.remove();
                        }
                    });
                    document.getElementById('ai-suggestions')?.prepend(box);
                }

                function revertChange(change) {
                    if (!change || !change.type) return;
                    if (change.type === 'title') {
                        const el = document.querySelector('[name="ilan_basligi"], [name="title"]');
                        if (el) el.value = change.old || '';
                        return;
                    }
                    if (change.type === 'description') {
                        const el = document.querySelector('[name="aciklama"], [name="description"]');
                        if (el) el.value = change.old || '';
                        return;
                    }
                    if (change.type === 'price') {
                        const el = document.querySelector('[name="fiyat"], [name="price"]');
                        if (el) el.value = change.old || '';
                        return;
                    }
                    if (change.type === 'feature') {
                        const group = change.group || '';
                        const scopeSelector = group ? `[data-feature-group="${group}"]` : '';
                        const candidates = [
                            `${scopeSelector} input[type="checkbox"]`,
                            `${scopeSelector} input[type="radio"]`,
                            `${scopeSelector} select`,
                            `${scopeSelector} input[type="text"]`,
                            `${scopeSelector} input[type="number"]`,
                            `${scopeSelector} textarea`
                        ].map(s => s.trim()).join(', ');
                        const elements = Array.from(document.querySelectorAll(candidates));
                        for (const el of elements) {
                            const featSlug = el.getAttribute('data-feature') || '';
                            const featLabel = el.getAttribute('data-feature-label') || '';
                            const matches = (featSlug && featSlug.toLowerCase() === String(change.key).toLowerCase()) || (featLabel && featLabel.toLowerCase() === String(change.key).toLowerCase());
                            if (matches) {
                                if (el.tagName === 'SELECT') {
                                    el.value = String(change.old || '');
                                    el.dispatchEvent(new Event('change', { bubbles: true }));
                                } else if (el.type === 'checkbox' || el.type === 'radio') {
                                    el.checked = !!change.old;
                                    el.dispatchEvent(new Event('change', { bubbles: true }));
                                } else {
                                    el.value = String(change.old || '');
                                    el.dispatchEvent(new Event('input', { bubbles: true }));
                                }
                                break;
                            }
                        }
                    }
                }

                async function postJSON(url, payload) {
                    const res = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrf,
                        },
                        body: JSON.stringify(payload)
                    });
                    return res.ok ? res.json() : Promise.reject(new Error('İstek başarısız'));
                }

                function requiredFields(ctx) {
                    const base = [
                        ['Kategori', !!ctx.kategori],
                        ['Alt Kategori', !!ctx.altKategori],
                        ['Yayın Tipi', !!ctx.yayinTipi],
                        ['Lokasyon', !!(ctx.mahalle || ctx.ilce || ctx.il)],
                    ];
                    const slug = (ctx.kategori || '').toLowerCase();
                    const extra = [];
                    if (slug.includes('konut') || slug.includes('daire') || slug.includes('residential')) {
                        extra.push(['Oda Sayısı', !!ctx.odaSayisi]);
                        extra.push(['Metrekare', !!ctx.metrekare]);
                        extra.push(['Isıtma', !!(document.querySelector('[name="isitma"]')?.value || '')]);
                        extra.push(['Bina Yaşı', !!(document.querySelector('[name="bina_yasi"]')?.value || '')]);
                    } else if (slug.includes('arsa') || slug.includes('land')) {
                        extra.push(['Metrekare', !!ctx.metrekare]);
                        extra.push(['İmar Durumu', !!(document.querySelector('[name="imar_durumu"]')?.value || '')]);
                        extra.push(['Tapu Durumu', !!(document.querySelector('[name="tapu_durumu"]')?.value || '')]);
                    } else if (slug.includes('isyeri') || slug.includes('ofis') || slug.includes('office')) {
                        extra.push(['Metrekare', !!ctx.metrekare]);
                        extra.push(['Oda/Bölme', !!(document.querySelector('[name="bolme_sayisi"]')?.value || '')]);
                    } else {
                        extra.push(['Oda Sayısı', !!ctx.odaSayisi]);
                        extra.push(['Metrekare', !!ctx.metrekare]);
                    }
                    return base.concat(extra);
                }

                function updateReadiness() {
                    const ctx = collectContext();
                    const reqs = requiredFields(ctx);
                    const satisfied = reqs.filter(([, ok]) => ok).length;
                    const total = reqs.length;
                    const pct = Math.round((satisfied / total) * 100);
                    const missing = reqs.filter(([label, ok]) => !ok).map(([label]) => label);
                    const slug = (ctx.kategori || '').toLowerCase();
                    let threshold = 70;
                    if (slug.includes('arsa') || slug.includes('land')) threshold = 80;
                    else if (slug.includes('isyeri') || slug.includes('ofis') || slug.includes('office')) threshold = 75;
                    const criticalSet = new Set(
                        slug.includes('arsa') || slug.includes('land')
                            ? ['Metrekare', 'İmar Durumu', 'Tapu Durumu', 'Lokasyon']
                            : slug.includes('isyeri') || slug.includes('ofis') || slug.includes('office')
                            ? ['Metrekare', 'Lokasyon']
                            : ['Oda Sayısı', 'Metrekare', 'Lokasyon']
                    );
                    const missingCritical = missing.filter(l => criticalSet.has(l));

                    const statusEl = document.getElementById('ai-context-status');
                    const hintEl = document.getElementById('ai-missing-hints');
                    const badgeEl = document.getElementById('ai-readiness-badge');
                    const barFillEl = document.getElementById('ai-readiness-bar-fill');
                    if (statusEl) statusEl.textContent = `Bağlam Durumu: %${pct}`;
                    if (hintEl) hintEl.textContent = missing.length ? `Eksik: ${missing.join(', ')}${missingCritical.length ? ' • Kritik: ' + missingCritical.join(', ') : ''}` : '';
                    if (badgeEl) {
                        if (pct >= threshold) {
                            badgeEl.textContent = 'Hazır';
                            badgeEl.className = 'text-xs px-2 py-1 rounded bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300';
                        } else {
                            badgeEl.textContent = 'Hazır değil';
                            badgeEl.className = 'text-xs px-2 py-1 rounded bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300';
                        }
                    }

                    const controls = [
                        document.getElementById('ai-generate-title'),
                        document.getElementById('ai-generate-description'),
                        document.getElementById('ai-price-suggestion'),
                        document.getElementById('ai-field-suggestion'),
                    ].filter(Boolean);

                    if (barFillEl) barFillEl.style.width = String(pct) + '%';
                    const enable = pct >= threshold;
                    controls.forEach(btn => {
                        btn.disabled = !enable;
                        btn.classList.toggle('opacity-50', !enable);
                        btn.classList.toggle('cursor-not-allowed', !enable);
                    });
                }

                ['change', 'input'].forEach(ev => document.addEventListener(ev, updateReadiness));
                document.addEventListener('DOMContentLoaded', updateReadiness);
                updateReadiness();
                document.getElementById('ai-undo-btn')?.addEventListener('click', () => {
                    const last = changeStack.pop();
                    if (last) revertChange(last);
                });
                document.getElementById('ai-apply-all')?.addEventListener('click', () => {
                    const btns = Array.from(document.querySelectorAll('#ai-suggestions button[data-action="apply"]'));
                    btns.forEach(b => b.dispatchEvent(new MouseEvent('click', { bubbles: true })));
                });

                document.addEventListener('keydown', (e) => {
                    if (!e.altKey) return;
                    const k = String(e.key || '').toLowerCase();
                    if (k === 'z') {
                        e.preventDefault();
                        const last = changeStack.pop();
                        if (last) revertChange(last);
                        return;
                    }
                    if (k === 'g') {
                        e.preventDefault();
                        document.getElementById('ai-field-suggestion')?.click();
                        return;
                    }
                    if (k === 'a') {
                        e.preventDefault();
                        const top = document.querySelector('#ai-suggestions [data-action="apply"]');
                        if (top) top.dispatchEvent(new MouseEvent('click', { bubbles: true }));
                        return;
                    }
                });

                const formEl = document.getElementById('ilan-create-form');
                if (formEl) {
                    formEl.addEventListener('submit', (e) => {
                        const fmap = (window.FieldDependenciesManager && (window.FieldDependenciesManager.getFeatureMap ? window.FieldDependenciesManager.getFeatureMap() : window.FieldDependenciesManager.featureMap)) || {};
                        const containerId = 'features-hidden-container';
                        let container = document.getElementById(containerId);
                        if (!container) {
                            container = document.createElement('div');
                            container.id = containerId;
                            container.style.display = 'none';
                            formEl.appendChild(container);
                        }
                        container.innerHTML = '';

                        Object.values(fmap).forEach(m => {
                            const el = document.getElementById(m.id);
                            if (!el || !m.featureId) return;
                            let val = null;
                            if (el.tagName === 'SELECT') {
                                val = el.value;
                            } else if (el.type === 'checkbox' || el.type === 'radio') {
                                val = el.checked ? '1' : null;
                            } else {
                                val = el.value;
                            }
                            if (val !== null && val !== '') {
                                const hidden = document.createElement('input');
                                hidden.type = 'hidden';
                                hidden.name = `features[${m.featureId}]`;
                                hidden.value = String(val);
                                container.appendChild(hidden);
                            }
                        });
                    });
                }

                document.getElementById('ai-generate-title')?.addEventListener('click', async () => {
                    const ctx = collectContext();
                    try {
                        const data = await postJSON('/admin/ilanlar/generate-ai-title', { context: ctx });
                        const text = (data && (data.title || data.data?.title)) || 'Öneri üretilemedi';
                        showActionableSuggestion('title', 'Başlık Önerisi', text, text);
                        // İsteğe bağlı: form alanına yaz
                        const titleInput = document.querySelector('[name="ilan_basligi"]');
                        if (titleInput && text) titleInput.value = text;
                    } catch (e) {
                        showSuggestion('Hata', 'Başlık önerisi alınamadı');
                    }
                });

                document.getElementById('ai-generate-description')?.addEventListener('click', async () => {
                    const ctx = collectContext();
                    try {
                        const data = await postJSON('/admin/ilanlar/generate-ai-description', { context: ctx });
                        const text = (data && (data.description || data.data?.description)) || 'Öneri üretilemedi';
                        showActionableSuggestion('description', 'Açıklama Önerisi', text, text);
                        const descInput = document.querySelector('[name="aciklama"]');
                        if (descInput && text) descInput.value = text;
                    } catch (e) {
                        showSuggestion('Hata', 'Açıklama önerisi alınamadı');
                    }
                });

                document.getElementById('ai-price-suggestion')?.addEventListener('click', async () => {
                    const ctx = collectContext();
                    try {
                        const data = await postJSON('/admin/ilanlar/ai-price-optimization', { context: ctx });
                        const suggestion = data?.data?.optimized || data?.optimized || null;
                        const currency = ctx.paraBirimi || 'TRY';
                        if (suggestion) {
                            showActionableSuggestion('price', 'Fiyat Önerisi', `${suggestion} ${currency}`, String(suggestion));
                            const priceInput = document.querySelector('[name="fiyat"]');
                            if (priceInput) priceInput.value = suggestion;
                        } else {
                            showSuggestion('Fiyat Önerisi', 'Öneri üretilemedi');
                        }
                    } catch (e) {
                        showSuggestion('Hata', 'Fiyat önerisi alınamadı');
                    }
                });

                document.getElementById('ai-field-suggestion')?.addEventListener('click', async () => {
                    const ctx = collectContext();
                    try {
                        const data = await postJSON('/admin/ilanlar/ai-property-suggestions', { context: ctx });
                        const items = data?.data?.suggestions || data?.suggestions || [];
                        if (Array.isArray(items) && items.length) {
                            items.slice(0, 5).forEach(s => {
                                const label = s && typeof s === 'object' ? (s.label || s.key || s.value || '') : String(s || '');
                                const payload = s && typeof s === 'object' ? s : { key: label };
                                showActionableSuggestion('feature', 'Alan Önerisi', label, payload);
                            });
                        } else {
                            showSuggestion('Alan Önerileri', 'Öneri bulunamadı');
                        }
                    } catch (e) {
                        showSuggestion('Hata', 'Alan önerileri alınamadı');
                    }
                });
            })();
        </script>
        <script>
            (function(){
                // Toast shim: window.toast('message','type')
                try {
                    if (typeof window.toast !== 'function') {
                        window.toast = function(msg, type){
                            try {
                                if (window.Toast && typeof window.Toast[(type||'info')] === 'function') {
                                    window.Toast[(type||'info')](String(msg||''));
                                } else if (window.toast && typeof window.toast[(type||'info')] === 'function') {
                                    window.toast[(type||'info')](String(msg||''));
                                } else {
                                    console[(type==='error'?'error':'log')](String(msg||''));
                                }
                            } catch(e){ console.log(String(msg||'')); }
                        };
                    }
                } catch(e) {}

                // AdvancedPriceManager guard: ensure convertedPrices exists and guard update
                try {
                    if (typeof window.advancedPriceManager === 'function') {
                        var _origAPM = window.advancedPriceManager;
                        window.advancedPriceManager = function(){
                            var obj = _origAPM();
                            if (!obj.convertedPrices) { obj.convertedPrices = {TRY:'',USD:'',EUR:'',GBP:''}; }
                            if (!obj.exchangeRates) {
                                obj.exchangeRates = (window.currencyRates || {TRY:1,USD:0,EUR:0,GBP:0});
                            }
                            if (typeof obj.updateConvertedPrices === 'function') {
                                var _origUpd = obj.updateConvertedPrices;
                                obj.updateConvertedPrices = function(){
                                    try { return _origUpd.apply(this, arguments); }
                                    catch(e){
                                        console.warn('updateConvertedPrices guard:', e);
                                        this.convertedPrices = this.convertedPrices || {TRY:'',USD:'',EUR:'',GBP:''};
                                        this.exchangeRates = this.exchangeRates || (window.currencyRates || {TRY:1,USD:0,EUR:0,GBP:0});
                                    }
                                };
                            }
                            return obj;
                        };
                    }
                } catch(e) {}

                // Submit guard: remove required from hidden/disabled controls to avoid focus errors
                try {
                    var form = document.getElementById('ilan-create-form');
                    if (form) {
                        form.addEventListener('submit', function(ev){
                            var controls = Array.prototype.slice.call(form.querySelectorAll('[required]'));
                            controls.forEach(function(el){
                                var hidden = (el.offsetParent === null) || el.closest('[style*="display: none" ]');
                                if (hidden || el.disabled) { el.removeAttribute('required'); }
                            });
                        }, {capture:true});
                    }
                } catch(e) {}
            })();
        </script>
        <script>
            (function(){
                // Currency rates shim: ensure window.currencyRates is set
                function setDefaults(){ window.currencyRates = window.currencyRates || {TRY:1,USD:0,EUR:0,GBP:0}; }
                setDefaults();
                try {
                    fetch('/api/currency/rates', { headers: { 'Accept': 'application/json' }})
                        .then(function(r){ return r.json(); })
                        .then(function(json){
                            var data = Array.isArray(json) ? json : (json.data || json);
                            var rates = {};
                            if (data && typeof data === 'object') {
                                ['TRY','USD','EUR','GBP'].forEach(function(k){
                                    var v = data[k];
                                    if (v && typeof v === 'object' && 'rate' in v) { rates[k] = Number(v.rate)||0; }
                                    else { rates[k] = Number(v)||0; }
                                });
                            }
                            if (Object.keys(rates).length) { window.currencyRates = rates; }
                        })
                        .catch(function(){ /* silent */ });
                } catch(e) {}
                window.getCurrencyRate = function(code){ try { return Number((window.currencyRates||{})[code])||0; } catch(e){ return 0; } };
            })();
        </script>
    @endpush
@endsection

@push('scripts')
    <!-- Alpine.js Global Store -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('formData', {
                kategori_id: null,
                ana_kategori_id: null,
                alt_kategori_id: null,
                yayin_tipi_id: null,
                para_birimi: 'TRY',
                status: 'active',
                selectedSite: null,
                selectedPerson: null
            });
        });
    </script>

    <!-- Context7 Live Search (Kişi ve Site/Apartman araması için) -->
    <script src="{{ asset('js/context7-live-search-simple.js') }}"></script>

    <!-- İlan Create Modular JavaScript -->
    @vite(['resources/js/admin/ilan-create.js'])
    <script type="module" src="{{ asset('js/leaflet-draw-loader.js') }}"></script>
    @push('scripts')
    <script>
    (function(){
      if(!window.toast){
        window.toast = {
          error: function(msg){ if(window.showToast){ window.showToast('error', msg); } else { console.error(msg); } },
          success: function(msg){ if(window.showToast){ window.showToast('success', msg); } else { console.log(msg); } }
        };
      }
      const form = document.querySelector('form');
      if(form){
        form.addEventListener('submit', function(){
          const nodes = form.querySelectorAll('input,select,textarea');
          nodes.forEach(function(el){
            const hidden = (el.offsetParent === null) || el.closest('[hidden]') || el.closest('.hidden');
            if(hidden){ el.removeAttribute('required'); el.setAttribute('disabled','disabled'); }
          });
        });
      }
    })();
    </script>
    @endpush

    <!-- Test Script (Sadece development için) -->
    @if (config('app.debug'))
        <script src="{{ asset('js/test-form-selections.js') }}"></script>
    @endif

    <!-- Leaflet.js OpenStreetMap -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <!-- 🗺️ VanillaLocationManager (location-map component script) -->
    <script>
        // 🎯 Debug Mode (set to false in production)
        const DEBUG_MODE = {{ config('app.debug') ? 'true' : 'false' }};
        const log = (...args) => DEBUG_MODE && console.log(...args);

        log('🚀 [DEBUG] VanillaLocationManager script yükleniyor...');

        // 🎯 Vanilla JS Location Manager (Context7 ONLY - No Alpine.js)
        const VanillaLocationManager = {
            config: {
                latField: 'enlem',
                lngField: 'boylam',
                addressField: 'adres',
                structuredFields: {
                    street: 'sokak',
                    avenue: 'cadde',
                    boulevard: 'bulvar',
                    building: 'bina_no',
                    postalCode: 'posta_kodu'
                }
            },
            selectedIl: '',
            selectedIlce: '',
            selectedMahalle: '',
            ilceler: [],
            mahalleler: [],
            map: null,
            marker: null,
            standardLayer: null,
            satelliteLayer: null,
            useSatellite: false,
            isSilentUpdate: false, // 🔧 Flag to prevent map refocus during reverse geocoding

            init() {
                log('📍 Vanilla Location Manager initialized (Context7)');
                this.setConfigFromDataset();
                this.initMap();
                this.attachEventListeners();
            },

            setConfigFromDataset() {
                const mapEl = document.getElementById('map');
                if (!mapEl) {
                    return;
                }

                this.config.latField = mapEl.dataset.latField || this.config.latField;
                this.config.lngField = mapEl.dataset.lngField || this.config.lngField;
                this.config.addressField = mapEl.dataset.addressField || this.config.addressField;

                if (mapEl.dataset.structuredFields) {
                    try {
                        const parsed = JSON.parse(mapEl.dataset.structuredFields);
                        this.config.structuredFields = {
                            ...this.config.structuredFields,
                            ...parsed
                        };
                    } catch (error) {
                        console.warn('Structured fields JSON parse failed:', error);
                    }
                }
            },

            getLatInput() {
                return document.getElementById(this.config.latField);
            },

            getLngInput() {
                return document.getElementById(this.config.lngField);
            },

            getAddressInput() {
                return document.getElementById(this.config.addressField);
            },

            getStructuredField(key) {
                const fieldId = this.config.structuredFields?.[key];
                return fieldId ? document.getElementById(fieldId) : null;
            },

            attachEventListeners() {
                // Event listeners zaten location.js'de var
                // Bu sadece harita initialization için
                log('✅ VanillaLocationManager init tamamlandı');
            },

            async initMap() {
                log('🗺️ Harita yükleme başlıyor...');

                // ✅ PROMISE-BASED: Leaflet'in yüklenmesini bekle (max 10 saniye)
                try {
                    await this.waitForLeaflet();
                } catch (error) {
                    console.error('❌ Leaflet yüklenemedi:', error);
                    this.showMapError('Harita kütüphanesi yüklenemedi. Lütfen sayfayı yenileyin.');
                    return;
                }

                const mapEl = document.getElementById('map');
                if (!mapEl) {
                    console.error('❌ Map element (#map) bulunamadı!');
                    return;
                }

                if (this.map) {
                    log('⚠️ Harita zaten yüklü');
                    return;
                }

                try {
                    // Create map (Bodrum center)
                    this.map = L.map('map').setView([37.0344, 27.4305], 13);

                    // Standard Layer - OpenStreetMap
                    this.standardLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap',
                        maxZoom: 19
                    }).addTo(this.map);

                    // Satellite Layer - Esri World Imagery
                    this.satelliteLayer = L.tileLayer(
                        'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                            attribution: '© Esri',
                            maxZoom: 19
                        });

                    // Map click handler
                    this.map.on('click', (e) => {
                        this.setMarker(e.latlng.lat, e.latlng.lng);
                    });

                    log('✅ OpenStreetMap ready (Standart + Uydu layer)');
                    window.toast?.success('🗺️ Harita yüklendi');

                    // ✅ Varolan koordinatları göster
                    this.loadExistingCoordinates();

                } catch (error) {
                    console.error('❌ Harita init hatası:', error);
                    this.showMapError('Harita başlatılamadı: ' + error.message);
                }
            },

            waitForLeaflet() {
                return new Promise((resolve, reject) => {
                    if (typeof L !== 'undefined') {
                        log('✅ Leaflet zaten yüklü');
                        resolve();
                        return;
                    }

                    log('⏳ Leaflet yüklenmesi bekleniyor...');
                    let attempts = 0;
                    const maxAttempts = 50; // 50 x 200ms = 10 saniye

                    const checkInterval = setInterval(() => {
                        attempts++;

                        if (typeof L !== 'undefined') {
                            clearInterval(checkInterval);
                            log('✅ Leaflet yüklendi (attempt ' + attempts + ')');
                            resolve();
                        } else if (attempts >= maxAttempts) {
                            clearInterval(checkInterval);
                            reject(new Error('Timeout: Leaflet 10 saniyede yüklenemedi'));
                        }
                    }, 200);
                });
            },

            loadExistingCoordinates() {
                const enlemInput = document.getElementById('enlem');
                const boylamInput = document.getElementById('boylam');

                if (enlemInput && boylamInput && enlemInput.value && boylamInput.value) {
                    const lat = parseFloat(enlemInput.value);
                    const lng = parseFloat(boylamInput.value);

                    if (!isNaN(lat) && !isNaN(lng) && lat !== 0 && lng !== 0) {
                        log('✅ Varolan koordinatlar bulundu:', lat, lng);
                        setTimeout(() => {
                            this.setMarker(lat, lng);
                            this.map.setView([lat, lng], 15);
                            window.toast?.info('📍 Kaydedilmiş konum yüklendi');
                        }, 500);
                    }
                }
            },

            showMapError(message) {
                const mapEl = document.getElementById('map');
                if (!mapEl) return;

                mapEl.innerHTML = `
            <div class="flex items-center justify-center h-full min-h-[400px] bg-red-50 dark:bg-red-900/20 border-2 border-red-300 dark:border-red-700 rounded-lg">
                <div class="text-center p-6">
                    <svg class="w-16 h-16 text-red-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <h3 class="text-lg font-bold text-red-800 dark:text-red-200 mb-2">Harita Yüklenemedi</h3>
                    <p class="text-sm text-red-600 dark:text-red-400 mb-4">${message}</p>
                    <button onclick="location.reload()"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700
                                   transition-all duration-200 shadow-md hover:shadow-lg">
                        Sayfayı Yenile
                    </button>
                </div>
            </div>
        `;

                window.toast?.error('Harita yüklenemedi');
            },

            setMapType(type) {
                if (!this.map || !this.standardLayer || !this.satelliteLayer) {
                    console.warn('⚠️ Map layers not initialized yet');
                    return;
                }

                const btnStandard = document.getElementById('btn-map-standard');
                const btnSatellite = document.getElementById('btn-map-satellite');

                if (type === 'satellite') {
                    this.map.removeLayer(this.standardLayer);
                    this.map.addLayer(this.satelliteLayer);
                    this.useSatellite = true;
                    if (btnStandard) btnStandard.className =
                        'flex items-center justify-center gap-1 px-2.5 py-1.5 rounded-lg transition-all duration-200 text-xs font-semibold text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700';
                    if (btnSatellite) btnSatellite.className =
                        'flex items-center justify-center gap-1 px-2.5 py-1.5 rounded-lg transition-all duration-200 text-xs font-semibold bg-gradient-to-br from-blue-500 to-blue-600 text-white shadow-md hover:shadow-lg';
                } else {
                    this.map.removeLayer(this.satelliteLayer);
                    this.map.addLayer(this.standardLayer);
                    this.useSatellite = false;
                    if (btnStandard) btnStandard.className =
                        'flex items-center justify-center gap-1 px-2.5 py-1.5 rounded-lg transition-all duration-200 text-xs font-semibold bg-gradient-to-br from-blue-500 to-blue-600 text-white shadow-md hover:shadow-lg';
                    if (btnSatellite) btnSatellite.className =
                        'flex items-center justify-center gap-1 px-2.5 py-1.5 rounded-lg transition-all duration-200 text-xs font-semibold text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700';
                }
            },

            setMarker(lat, lng, skipReverseGeocode = false) {
                if (!this.map) {
                    console.error('❌ Map not initialized');
                    return;
                }

                // ✅ Context7: Aynı koordinatlara tekrar marker set etme (gereksiz reverse geocoding önle)
                if (this.marker) {
                    const currentPos = this.marker.getLatLng();
                    const latDiff = Math.abs(currentPos.lat - lat);
                    const lngDiff = Math.abs(currentPos.lng - lng);

                    // 10 metre tolerans (yaklaşık 0.0001 derece)
                    if (latDiff < 0.0001 && lngDiff < 0.0001 && !skipReverseGeocode) {
                        log('⏭️ Aynı konuma marker set ediliyor, reverse geocoding atlandı');
                        skipReverseGeocode = true;
                    }

                    this.map.removeLayer(this.marker);
                }

                // ✅ DRAGGABLE MARKER: Sürüklenebilir işaretçi
                this.marker = L.marker([lat, lng], {
                    draggable: true,
                    autoPan: true,
                    title: 'Konumu değiştirmek için sürükleyin'
                }).addTo(this.map);

                // ✅ Marker sürüklendiğinde koordinatları güncelle
                this.marker.on('dragend', (e) => {
                    const position = e.target.getLatLng();
                    const newLat = position.lat.toFixed(7);
                    const newLng = position.lng.toFixed(7);

                    document.getElementById('enlem').value = newLat;
                    document.getElementById('boylam').value = newLng;

                    log('✅ Marker sürüklendi:', newLat, newLng);
                    window.toast?.success('📍 Konum güncellendi');

                    // Reverse geocoding yap
                    this.reverseGeocode(position.lat, position.lng);
                });

                // ✅ Popup ekle
                this.marker.bindPopup(`
            <div class="text-center">
                <strong>📍 Mülk Konumu</strong><br>
                <small>${lat.toFixed(6)}, ${lng.toFixed(6)}</small><br>
                <em class="text-xs text-gray-500">Sürükleyerek değiştirin</em>
            </div>
        `);

                // Koordinat input'larını güncelle
                document.getElementById('enlem').value = lat.toFixed(7);
                document.getElementById('boylam').value = lng.toFixed(7);

                log('📍 Konum seçildi:', lat, lng);
                window.toast?.success('Konum haritada işaretlendi');

                // 🆕 Reverse Geocoding: Koordinatlardan adres getir
                if (!skipReverseGeocode) {
                    this.reverseGeocode(lat, lng);
                }
            },

            // ✅ Context7: Adres field'larını doldur (tekrar kullanılabilir fonksiyon)
            fillAddressFields(addr, displayName = '') {
                if (!addr) return;

                const parts = [];

                // 🆕 PHASE 1: Address Components'leri ayrı ayrı field'lara yaz
                const sokakField = document.getElementById('sokak');
                const caddeField = document.getElementById('cadde');
                const bulvarField = document.getElementById('bulvar');
                const binaNoField = document.getElementById('bina_no');
                const postaKoduField = document.getElementById('posta_kodu');

                // Sokak/Cadde/Bulvar ayırımı
                if (addr.road) {
                    const road = addr.road;
                    if (road.toLowerCase().includes('bulvar')) {
                        if (bulvarField) bulvarField.value = road;
                    } else if (road.toLowerCase().includes('cadde')) {
                        if (caddeField) caddeField.value = road;
                    } else {
                        if (sokakField) sokakField.value = road;
                    }
                    parts.push(road);
                }

                // Bina numarası
                if (addr.house_number) {
                    if (binaNoField) binaNoField.value = addr.house_number;
                    parts.push('No:' + addr.house_number);
                }

                // Posta kodu
                if (addr.postcode) {
                    if (postaKoduField) postaKoduField.value = addr.postcode;
                    parts.push('(' + addr.postcode + ')');
                }

                // Mahalle
                if (addr.suburb) parts.push(addr.suburb);
                else if (addr.neighbourhood) parts.push(addr.neighbourhood);
                else if (addr.quarter) parts.push(addr.quarter);

                // İlçe
                if (addr.town) parts.push(addr.town);
                else if (addr.city_district) parts.push(addr.city_district);

                // İl
                if (addr.province || addr.state) parts.push(addr.province || addr.state);

                // Adresi birleştir
                const fullAddress = parts.join(', ');

                // Adres field'ına yaz
                const adresField = document.getElementById('adres');
                if (adresField) {
                    adresField.value = fullAddress;
                    log('✅ Adres otomatik dolduruldu:', fullAddress);
                    log('✅ Address components:', {
                        sokak: sokakField?.value,
                        cadde: caddeField?.value,
                        bulvar: bulvarField?.value,
                        bina_no: binaNoField?.value,
                        posta_kodu: postaKoduField?.value
                    });
                    window.toast?.success('Adres ve detaylar otomatik dolduruldu!');

                    // Textarea'yı highlight et (visual feedback)
                    adresField.classList.add('ring-4', 'ring-green-500/50');
                    setTimeout(() => {
                        adresField.classList.remove('ring-4', 'ring-green-500/50');
                    }, 2000);
                }

                // Display name'i de göster (optional)
                if (displayName) {
                    log('📍 Tam adres:', displayName);
                }
            },

            async reverseGeocode(lat, lng) {
                // ✅ LOADING STATE: Adres textarea'yı loading yap
                const adresField = document.getElementById('adres');
                let originalPlaceholder = '';
                let originalValue = '';

                if (adresField) {
                    originalPlaceholder = adresField.placeholder;
                    originalValue = adresField.value;
                    adresField.disabled = true;
                    adresField.classList.add('opacity-50', 'cursor-wait', 'animate-pulse');
                    adresField.placeholder = '⏳ Adres bilgisi getiriliyor...';
                }

                try {
                    // ✅ Context7: Koordinat cache kontrolü (aynı koordinatlar için tekrar çağrılmasını önle)
                    const cacheKey = `geocode_${lat.toFixed(7)}_${lng.toFixed(7)}`;
                    const cached = sessionStorage.getItem(cacheKey);

                    if (cached) {
                        const cachedData = JSON.parse(cached);
                        const cacheAge = Date.now() - cachedData.timestamp;
                        const maxAge = 3600000; // 1 saat

                        if (cacheAge < maxAge) {
                            log('✅ Reverse geocoding cache\'den alındı:', lat, lng);
                            this.fillAddressFields(cachedData.address, cachedData.display_name);
                            // ✅ Restore adres field
                            if (adresField) {
                                adresField.disabled = false;
                                adresField.classList.remove('opacity-50', 'cursor-wait', 'animate-pulse');
                                adresField.placeholder = originalPlaceholder;
                            }
                            return;
                        }
                    }

                    log('🔍 Reverse geocoding başlıyor:', lat, lng);

                    // ✅ RATE LIMITING: Nominatim max 1 request/second
                    const lastCall = this.lastGeocodeCall || 0;
                    const now = Date.now();
                    const timeSinceLastCall = now - lastCall;

                    if (timeSinceLastCall < 1000) {
                        const waitTime = 1000 - timeSinceLastCall;
                        log(`⏳ Rate limit protection: ${waitTime}ms bekleniyor...`);
                        await new Promise(resolve => setTimeout(resolve, waitTime));
                    }

                    this.lastGeocodeCall = Date.now();

                    window.toast?.info('Adres bilgisi getiriliyor...', 2000);

                    // Nominatim Reverse Geocoding API
                    const url = `https://nominatim.openstreetmap.org/reverse?` +
                        `lat=${lat}` +
                        `&lon=${lng}` +
                        `&format=json` +
                        `&addressdetails=1` +
                        `&accept-language=tr`;

                    // ✅ RETRY LOGIC: 3 deneme
                    let response;
                    let lastError;

                    for (let attempt = 1; attempt <= 3; attempt++) {
                        try {
                            log(`🔄 Geocoding attempt ${attempt}/3...`);

                            response = await fetch(url, {
                                headers: {
                                    'User-Agent': 'YalihanEmlak/1.0'
                                }
                            });

                            if (response.ok) {
                                log(`✅ Geocoding başarılı (attempt ${attempt})`);
                                break;
                            }

                            lastError = `HTTP ${response.status}`;

                            // Son deneme değilse bekle
                            if (attempt < 3) {
                                const backoff = attempt * 1000; // 1s, 2s
                                log(`⏳ ${backoff}ms bekleniyor (backoff)...`);
                                await new Promise(resolve => setTimeout(resolve, backoff));
                            }

                        } catch (fetchError) {
                            lastError = fetchError.message;
                            log(`⚠️ Fetch error (attempt ${attempt}):`, fetchError);

                            if (attempt < 3) {
                                await new Promise(resolve => setTimeout(resolve, attempt * 1000));
                            }
                        }
                    }

                    if (!response || !response.ok) {
                        throw new Error(`Geocoding failed after 3 attempts: ${lastError}`);
                    }

                    const data = await response.json();
                    log('✅ Reverse geocoding response:', data);

                    if (data && data.address) {
                        // ✅ Context7: Cache'e kaydet
                        sessionStorage.setItem(cacheKey, JSON.stringify({
                            address: data.address,
                            display_name: data.display_name || '',
                            timestamp: Date.now()
                        }));

                        // ✅ Context7: Adres field'larını doldur (refactored function)
                        this.fillAddressFields(data.address, data.display_name);

                        // 🆕 PHASE 4: İl/İlçe/Mahalle Dropdown'larını Otomatik Seç (Çift Yönlü Sync)
                        // ✅ Context7: Sadece dropdown'lar boşsa otomatik seç (kullanıcı değişikliğini koru)
                        const ilSelect = document.getElementById('il_id');
                        const ilceSelect = document.getElementById('ilce_id');
                        const mahalleSelect = document.getElementById('mahalle_id');

                        if ((!ilSelect || !ilSelect.value) && (!ilceSelect || !ilceSelect.value) && (!
                                mahalleSelect || !mahalleSelect.value)) {
                            await this.autoSelectLocationDropdowns(data.address);
                        } else {
                            log('⏭️ Dropdown\'lar dolu, otomatik seçim atlandı (kullanıcı seçimi korunuyor)');
                        }

                    } else {
                        console.warn('⚠️ Adres bilgisi bulunamadı');
                        window.toast?.warning('Bu konum için adres bilgisi bulunamadı');
                    }

                } catch (error) {
                    console.error('❌ Reverse geocoding error:', error);
                    window.toast?.error('Adres bilgisi alınamadı');
                } finally {
                    // ✅ RESTORE LOADING STATE: Adres field'ı restore et
                    if (adresField) {
                        adresField.disabled = false;
                        adresField.classList.remove('opacity-50', 'cursor-wait', 'animate-pulse');
                        adresField.placeholder = originalPlaceholder;
                        // Eğer değer değişmediyse eski değeri geri yükle
                        if (!adresField.value && originalValue) {
                            adresField.value = originalValue;
                        }
                    }
                }
            },

            async autoSelectLocationDropdowns(addr) {
                try {
                    log('🔄 Dropdown otomatik seçimi başlıyor...');

                    // 🔧 Silent update flag (prevent map refocus loop)
                    this.isSilentUpdate = true;

                    // 1️⃣ İl (Province) Seçimi
                    const provinceName = addr.province || addr.state;
                    if (provinceName) {
                        log('🔍 İl arıyor:', provinceName);

                        // Tüm illeri çek
                        const ilResponse = await fetch('/api/location/provinces');
                        const ilData = await ilResponse.json();

                        // ✅ Context7: API response format'ı (direkt array - adres-yonetimi ile uyumlu)
                        const iller = Array.isArray(ilData.data) ? ilData.data : (Array.isArray(ilData) ? ilData :
                        []);

                        if (!Array.isArray(iller)) {
                            console.error('❌ API response is not an array:', ilData);
                            return;
                        }

                        log('✅ İller yüklendi:', iller.length, 'adet');

                        // İl adını eşleştir (fuzzy match) - field name: 'name' or 'il_adi' or 'il'
                        const matchedIl = iller.find(il => {
                            const ilName = (il.name || il.il_adi || il.il || '').toLowerCase().trim();
                            const searchName = provinceName.toLowerCase().trim();
                            return ilName === searchName ||
                                ilName.includes(searchName) ||
                                searchName.includes(ilName);
                        });

                        if (matchedIl) {
                            const ilSelect = document.getElementById('il_id');
                            if (ilSelect) {
                                ilSelect.value = matchedIl.id;
                                const ilDisplayName = matchedIl.name || matchedIl.il_adi || matchedIl.il ||
                                    'Unknown';
                                log('✅ İl otomatik seçildi:', ilDisplayName, '(ID:', matchedIl.id, ')');

                                // Highlight effect
                                ilSelect.classList.add('ring-4', 'ring-blue-500/50');
                                setTimeout(() => ilSelect.classList.remove('ring-4', 'ring-blue-500/50'), 1500);

                                // Change event'ini tetikle (ilçeleri yüklemek için)
                                ilSelect.dispatchEvent(new Event('change'));

                                // İlçelerin yüklenmesi için bekle
                                await new Promise(resolve => setTimeout(resolve, 500));

                                // 2️⃣ İlçe (District) Seçimi
                                const districtName = addr.town || addr.city_district;
                                if (districtName) {
                                    log('🔍 İlçe arıyor:', districtName);

                                    const ilceSelect = document.getElementById('ilce_id');
                                    if (ilceSelect && ilceSelect.options.length > 1) {
                                        // Dropdown'daki seçeneklerden eşleştir
                                        for (let i = 0; i < ilceSelect.options.length; i++) {
                                            const option = ilceSelect.options[i];
                                            const optionText = option.text.toLowerCase().trim();
                                            const searchText = districtName.toLowerCase().trim();

                                            if (optionText === searchText ||
                                                optionText.includes(searchText) ||
                                                searchText.includes(optionText)) {
                                                ilceSelect.value = option.value;
                                                log('✅ İlçe otomatik seçildi:', option.text, '(ID:', option.value,
                                                    ')');

                                                // Highlight effect
                                                ilceSelect.classList.add('ring-4', 'ring-blue-500/50');
                                                setTimeout(() => ilceSelect.classList.remove('ring-4',
                                                    'ring-blue-500/50'), 1500);

                                                // Change event'ini tetikle (mahalleleri yüklemek için)
                                                ilceSelect.dispatchEvent(new Event('change'));

                                                // Mahallelerin yüklenmesi için bekle
                                                await new Promise(resolve => setTimeout(resolve, 500));

                                                // 3️⃣ Mahalle (Neighborhood) Seçimi
                                                const neighborhoodName = addr.suburb || addr.neighbourhood || addr
                                                    .quarter;
                                                if (neighborhoodName) {
                                                    log('🔍 Mahalle arıyor:', neighborhoodName);

                                                    const mahalleSelect = document.getElementById('mahalle_id');
                                                    if (mahalleSelect && mahalleSelect.options.length > 1) {
                                                        // Dropdown'daki seçeneklerden eşleştir
                                                        for (let i = 0; i < mahalleSelect.options.length; i++) {
                                                            const option = mahalleSelect.options[i];
                                                            const optionText = option.text.toLowerCase().trim();
                                                            const searchText = neighborhoodName.toLowerCase()
                                                                .trim();

                                                            if (optionText === searchText ||
                                                                optionText.includes(searchText) ||
                                                                searchText.includes(optionText)) {
                                                                mahalleSelect.value = option.value;
                                                                log('✅ Mahalle otomatik seçildi:', option.text,
                                                                    '(ID:', option.value, ')');

                                                                // Highlight effect
                                                                mahalleSelect.classList.add('ring-4',
                                                                    'ring-blue-500/50');
                                                                setTimeout(() => mahalleSelect.classList.remove(
                                                                    'ring-4', 'ring-blue-500/50'), 1500);

                                                                // Change event'ini tetikle
                                                                mahalleSelect.dispatchEvent(new Event('change'));

                                                                window.toast?.success(
                                                                    '🎯 İl/İlçe/Mahalle otomatik seçildi!');
                                                                break;
                                                            }
                                                        }
                                                    }
                                                }

                                                break;
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            console.warn('⚠️ İl eşleşmesi bulunamadı:', provinceName);
                        }
                    }

                } catch (error) {
                    console.error('❌ Dropdown otomatik seçim hatası:', error);
                    // Hata olsa da adres doldurma devam etsin
                } finally {
                    // 🔧 Silent update flag'i kaldır (2000ms - cascade loading için yeterli süre)
                    setTimeout(() => {
                        this.isSilentUpdate = false;
                        log('✅ Silent update tamamlandı, harita kontrolü tekrar aktif');
                    }, 2000);
                }
            },

            async focusMapOnProvince(provinceName) {
                if (!provinceName) return;
                if (!this.map) {
                    log('⏳ Harita henüz hazır değil, bekleniyor...');
                    return;
                }

                // 🔧 Silent update sırasında haritayı hareket ettirme
                if (this.isSilentUpdate) {
                    log('⏭️ Silent update aktif, harita focus atlandı');
                    return;
                }

                try {
                    log('🔍 İl arıyor:', provinceName);
                    const coords = await this.geocodeLocation(`${provinceName}, Turkey`);

                    if (coords) {
                        this.map.flyTo([coords.lat, coords.lon], 10, {
                            duration: 1.5,
                            easeLinearity: 0.5
                        });
                        log('✅ Harita ile odaklandı:', provinceName);
                        window.toast?.success(`Harita ${provinceName} iline odaklandı`);
                    }
                } catch (error) {
                    console.error('❌ İl geocoding hatası:', error);
                }
            },

            async focusMapOnDistrict(districtName, provinceName) {
                if (!districtName || !provinceName) return;
                if (!this.map) {
                    log('⏳ Harita henüz hazır değil, bekleniyor...');
                    return;
                }

                // 🔧 Silent update sırasında haritayı hareket ettirme
                if (this.isSilentUpdate) {
                    log('⏭️ Silent update aktif, harita focus atlandı');
                    return;
                }

                try {
                    log('🔍 İlçe arıyor:', districtName, provinceName);
                    const coords = await this.geocodeLocation(`${districtName}, ${provinceName}, Turkey`);

                    if (coords) {
                        this.map.flyTo([coords.lat, coords.lon], 13, {
                            duration: 1.5,
                            easeLinearity: 0.5
                        });
                        log('✅ Harita ilçeye odaklandı:', districtName);
                        window.toast?.success(`Harita ${districtName} ilçesine odaklandı`);
                    }
                } catch (error) {
                    console.error('❌ İlçe geocoding hatası:', error);
                }
            },

            async focusMapOnNeighborhood(neighborhoodName, districtName, provinceName) {
                if (!neighborhoodName || !districtName || !provinceName) return;
                if (!this.map) {
                    log('⏳ Harita henüz hazır değil, bekleniyor...');
                    return;
                }

                // 🔧 Silent update sırasında haritayı hareket ettirme
                if (this.isSilentUpdate) {
                    log('⏭️ Silent update aktif, harita focus atlandı');
                    return;
                }

                try {
                    log('🔍 Mahalle arıyor:', neighborhoodName, districtName, provinceName);
                    const coords = await this.geocodeLocation(
                        `${neighborhoodName}, ${districtName}, ${provinceName}, Turkey`);

                    if (coords) {
                        this.map.flyTo([coords.lat, coords.lon], 15, {
                            duration: 1.5,
                            easeLinearity: 0.5
                        });

                        if (this.marker) {
                            this.map.removeLayer(this.marker);
                        }
                        // ✅ DRAGGABLE MARKER: Mahalle marker'ı da draggable olmalı
                        this.marker = L.marker([coords.lat, coords.lon], {
                                draggable: true,
                                autoPan: true,
                                title: 'Konumu değiştirmek için sürükleyin'
                            }).addTo(this.map)
                            .bindPopup(`📍 ${neighborhoodName}`)
                            .openPopup();

                        // ✅ Marker dragend event handler ekle
                        this.marker.on('dragend', (e) => {
                            const position = e.target.getLatLng();
                            const newLat = position.lat.toFixed(7);
                            const newLng = position.lng.toFixed(7);

                            document.getElementById('enlem').value = newLat;
                            document.getElementById('boylam').value = newLng;

                            log('✅ Marker sürüklendi:', newLat, newLng);
                            window.toast?.success('📍 Konum güncellendi');

                            // Reverse geocoding yap
                            this.reverseGeocode(position.lat, position.lng);
                        });

                        log('✅ Harita mahalleye odaklandı:', neighborhoodName);
                        window.toast?.success(`Harita ${neighborhoodName} mahallesine odaklandı`);
                    }
                } catch (error) {
                    console.error('❌ Mahalle geocoding hatası:', error);
                }
            },

            async geocodeLocation(query) {
                try {
                    // ✅ RATE LIMITING: Nominatim max 1 request/second
                    const lastCall = this.lastGeocodeCall || 0;
                    const now = Date.now();
                    const timeSinceLastCall = now - lastCall;

                    if (timeSinceLastCall < 1000) {
                        const waitTime = 1000 - timeSinceLastCall;
                        log(`⏳ Geocoding rate limit: ${waitTime}ms bekleniyor...`);
                        await new Promise(resolve => setTimeout(resolve, waitTime));
                    }

                    this.lastGeocodeCall = Date.now();

                    const url = `https://nominatim.openstreetmap.org/search?` +
                        `q=${encodeURIComponent(query)}` +
                        `&format=json` +
                        `&limit=1` +
                        `&addressdetails=1`;

                    // ✅ RETRY LOGIC: 3 deneme
                    let response;
                    let lastError;

                    for (let attempt = 1; attempt <= 3; attempt++) {
                        try {
                            log(`🔄 Geocoding attempt ${attempt}/3: ${query}`);

                            response = await fetch(url, {
                                headers: {
                                    'User-Agent': 'YalihanEmlak/1.0'
                                }
                            });

                            if (response.ok) {
                                log(`✅ Geocoding başarılı (attempt ${attempt})`);
                                break;
                            }

                            lastError = `HTTP ${response.status}`;

                            // Son deneme değilse bekle
                            if (attempt < 3) {
                                const backoff = attempt * 1000; // 1s, 2s
                                log(`⏳ ${backoff}ms bekleniyor (backoff)...`);
                                await new Promise(resolve => setTimeout(resolve, backoff));
                            }

                        } catch (fetchError) {
                            lastError = fetchError.message;
                            log(`⚠️ Fetch error (attempt ${attempt}):`, fetchError);

                            if (attempt < 3) {
                                await new Promise(resolve => setTimeout(resolve, attempt * 1000));
                            }
                        }
                    }

                    if (!response || !response.ok) {
                        throw new Error(`Geocoding failed after 3 attempts: ${lastError}`);
                    }

                    const data = await response.json();

                    if (data && data.length > 0) {
                        return {
                            lat: parseFloat(data[0].lat),
                            lon: parseFloat(data[0].lon)
                        };
                    }

                    return null;
                } catch (error) {
                    console.error('❌ Geocoding error:', error);
                    window.toast?.error('Konum bilgisi alınamadı: ' + error.message);
                    return null;
                }
            },

            // 🔧 Zoom Controls
            zoomIn() {
                if (this.map) {
                    this.map.zoomIn();
                    window.toast?.success('Harita yakınlaştırıldı');
                }
            },

            zoomOut() {
                if (this.map) {
                    this.map.zoomOut();
                    window.toast?.success('Harita uzaklaştırıldı');
                }
            },

            // 🔧 GPS / Current Location
            getCurrentLocation() {
                if (!this.map) {
                    window.toast?.error('❌ Harita yüklenmedi');
                    return;
                }

                if (!navigator.geolocation) {
                    window.toast?.error('❌ Tarayıcınız konum servisini desteklemiyor');
                    return;
                }

                // ✅ Loading indicator
                const gpsButton = document.querySelector('[onclick*="getCurrentLocation"]');
                if (gpsButton) {
                    gpsButton.disabled = true;
                    gpsButton.classList.add('opacity-50', 'cursor-wait', 'animate-pulse');
                    gpsButton.innerHTML = gpsButton.innerHTML.replace('📍', '⏳');
                }

                window.toast?.info('📡 GPS konumu alınıyor...');

                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        const accuracy = position.coords.accuracy;

                        this.map.flyTo([lat, lng], 15, {
                            duration: 1.5
                        });

                        this.setMarker(lat, lng);

                        // ✅ Success feedback
                        window.toast?.success(`✅ GPS konumu alındı (±${Math.round(accuracy)}m doğruluk)`);
                        log('✅ GPS konumu alındı:', lat, lng, 'accuracy:', accuracy);

                        // ✅ GPS button'ı restore et
                        const gpsButton = document.querySelector('[onclick*="getCurrentLocation"]');
                        if (gpsButton) {
                            gpsButton.disabled = false;
                            gpsButton.classList.remove('opacity-50', 'cursor-wait', 'animate-pulse');
                            gpsButton.innerHTML = gpsButton.innerHTML.replace('⏳', '📍');
                        }
                    },
                    (error) => {
                        // ✅ GPS button'ı restore et
                        const gpsButton = document.querySelector('[onclick*="getCurrentLocation"]');
                        if (gpsButton) {
                            gpsButton.disabled = false;
                            gpsButton.classList.remove('opacity-50', 'cursor-wait', 'animate-pulse');
                            gpsButton.innerHTML = gpsButton.innerHTML.replace('⏳', '📍');
                        }

                        // ✅ User-friendly error messages
                        if (error.code === 1) {
                            window.toast?.warning(
                                '⚠️ Konum izni reddedildi. Lütfen tarayıcı ayarlarından izin verin.');
                        } else if (error.code === 2) {
                            window.toast?.error('❌ Konum bilgisi alınamadı. GPS kapalı olabilir.');
                        } else if (error.code === 3) {
                            window.toast?.error('⏱️ Konum talebi zaman aşımına uğradı. Tekrar deneyin.');
                        } else {
                            window.toast?.error('❌ Konum alınamadı: ' + error.message);
                        }
                        log('⚠️ GPS error (code ' + error.code + '):', error.message);
                    }, {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    }
                );
            }
        };

        // DOMContentLoaded'da init et
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                VanillaLocationManager.init();
            }, 100);

            // ✅ Koordinat input'larına event listener ekle (Input → Map Sync)
            const enlemInput = document.getElementById('enlem');
            const boylamInput = document.getElementById('boylam');

            if (enlemInput && boylamInput) {
                // Input değişince haritayı güncelle
                function syncCoordsToMap() {
                    const lat = parseFloat(enlemInput.value);
                    const lng = parseFloat(boylamInput.value);

                    if (!isNaN(lat) && !isNaN(lng) && lat !== 0 && lng !== 0) {
                        if (VanillaLocationManager.map) {
                            // skipReverseGeocode = true (input'tan geliyorsa tekrar geocoding yapma)
                            VanillaLocationManager.setMarker(lat, lng, true);
                            VanillaLocationManager.map.setView([lat, lng], 15);
                            log('✅ Input → Map sync:', lat, lng);
                        } else {
                            log('⏳ Harita henüz hazır değil, 2 saniye sonra tekrar dene...');
                            setTimeout(syncCoordsToMap, 2000);
                        }
                    }
                }

                // Input blur'da kontrol et
                enlemInput.addEventListener('blur', syncCoordsToMap);
                boylamInput.addEventListener('blur', syncCoordsToMap);

                log('✅ Koordinat input listeners eklendi');
            }
        });

        // Global access
        log('🎯 [DEBUG] VanillaLocationManager tanımlanıyor...');
        window.VanillaLocationManager = VanillaLocationManager;
        log('✅ Vanilla Location Manager registered globally');
        log('🔍 [DEBUG] window.VanillaLocationManager:', typeof window.VanillaLocationManager);

        // ✅ DEBUG TOOLS: Harita durumu kontrolü için global fonksiyon
        window.mapStatus = () => {
            const manager = window.VanillaLocationManager;
            if (!manager) {
                return {
                    status: 'error',
                    message: 'VanillaLocationManager bulunamadı'
                };
            }

            const map = manager.map;
            const marker = manager.marker;
            const lastGeocodeCall = manager.lastGeocodeCall || 0;
            const now = Date.now();
            const timeSinceLastCall = now - lastGeocodeCall;
            const rateLimitWait = Math.max(0, 1000 - timeSinceLastCall);

            return {
                status: 'ok',
                map: {
                    initialized: !!map,
                    center: map ? map.getCenter() : null,
                    zoom: map ? map.getZoom() : null,
                    bounds: map ? map.getBounds() : null
                },
                marker: {
                    placed: !!marker,
                    position: marker ? marker.getLatLng() : null,
                    draggable: marker ? marker.draggable : false
                },
                geocoding: {
                    lastCall: lastGeocodeCall ? new Date(lastGeocodeCall).toISOString() : 'Never',
                    timeSinceLastCall: timeSinceLastCall,
                    rateLimitWait: rateLimitWait,
                    canCallNow: rateLimitWait === 0
                },
                config: {
                    latField: manager.config?.latField || 'enlem',
                    lngField: manager.config?.lngField || 'boylam',
                    addressField: manager.config?.addressField || 'adres',
                    isSilentUpdate: manager.isSilentUpdate || false
                }
            };
        };

        log('✅ Debug tool: window.mapStatus() hazır');

        // Harita tipi değiştirme fonksiyonu (global scope)
        window.setMapType = function(type) {
            VanillaLocationManager.setMapType(type);
        };

        // 🆕 PHASE 2: Distance Calculator System
        window.distancePoints = [];
        window.distanceMarkers = [];
        window.distanceLines = [];
        window.measuringFor = null;

        window.addDistancePoint = function(name, icon) {
            // ✅ Harita kontrolü
            if (!VanillaLocationManager.map) {
                window.toast?.error('❌ Harita yüklenmedi! Lütfen sayfayı yenileyin.');
                console.error('❌ Map not initialized in addDistancePoint');
                return;
            }

            // ✅ Mülk konumu kontrolü (marker veya koordinat)
            const enlem = document.getElementById('enlem')?.value;
            const boylam = document.getElementById('boylam')?.value;

            if ((!VanillaLocationManager.marker) && (!enlem || !boylam)) {
                window.toast?.warning('⚠️ Önce mülk konumunu işaretleyin (haritaya tıklayın)');
                return;
            }

            // ✅ Koordinatlar var ama marker yoksa, marker oluştur
            if (!VanillaLocationManager.marker && enlem && boylam) {
                const lat = parseFloat(enlem);
                const lng = parseFloat(boylam);
                if (!isNaN(lat) && !isNaN(lng)) {
                    VanillaLocationManager.setMarker(lat, lng, true);
                    log('✅ Marker otomatik oluşturuldu (mesafe ölçümü için)');
                }
            }

            window.measuringFor = {
                name,
                icon
            };
            window.toast?.info(`${icon} ${name} için haritada bir noktaya tıklayın`);
            log('📏 Mesafe ölçümü başladı:', name);

            // Harita tıklama event'ine temp listener ekle
            const tempClickHandler = function(e) {
                // ✅ Guard: measuringFor null check
                if (!window.measuringFor) {
                    console.warn('⚠️ measuringFor is null, aborting');
                    return;
                }

                const propertyLat = parseFloat(document.getElementById('enlem').value);
                const propertyLng = parseFloat(document.getElementById('boylam').value);
                const targetLat = e.latlng.lat;
                const targetLng = e.latlng.lng;

                // Mesafe hesapla (Haversine formula)
                const distance = calculateDistance(propertyLat, propertyLng, targetLat, targetLng);

                // Distance point kaydet (with local copy to avoid race condition)
                const measuring = window.measuringFor;
                const point = {
                    name: measuring.name,
                    icon: measuring.icon,
                    lat: targetLat,
                    lng: targetLng,
                    distance: Math.round(distance),
                    unit: distance >= 1000 ? 'km' : 'm',
                    displayDistance: distance >= 1000 ? (distance / 1000).toFixed(1) : Math.round(distance)
                };

                window.distancePoints.push(point);

                // Haritaya marker ekle (measuring kullan, not window.measuringFor)
                const marker = L.marker([targetLat, targetLng], {
                    icon: L.divIcon({
                        html: `<div class="flex items-center justify-center w-8 h-8 bg-purple-600 text-white rounded-full shadow-lg border-2 border-white text-sm">${measuring.icon}</div>`,
                        className: 'distance-marker',
                        iconSize: [32, 32],
                        iconAnchor: [16, 32]
                    })
                }).addTo(VanillaLocationManager.map);

                marker.bindPopup(
                    `${point.icon} ${point.name}<br><strong>${point.displayDistance} ${point.unit}</strong>`);
                window.distanceMarkers.push(marker);

                // Çizgi çiz (property → target)
                const line = L.polyline(
                    [
                        [propertyLat, propertyLng],
                        [targetLat, targetLng]
                    ], {
                        color: '#9333ea',
                        weight: 3,
                        opacity: 0.7,
                        dashArray: '10, 10'
                    }
                ).addTo(VanillaLocationManager.map);

                window.distanceLines.push(line);

                // UI'ı güncelle
                updateDistanceList();

                // JSON field'ını güncelle
                document.getElementById('nearby_distances').value = JSON.stringify(window.distancePoints);

                log('✅ Mesafe eklendi:', point);
                window.toast?.success(`${point.icon} ${point.name}: ${point.displayDistance} ${point.unit}`);

                // Temp listener'ı kaldır
                VanillaLocationManager.map.off('click', tempClickHandler);
                window.measuringFor = null;
            };

            // Temp listener ekle
            VanillaLocationManager.map.once('click', tempClickHandler);
        };

        window.removeDistancePoint = function(index) {
            // Marker'ı kaldır
            if (window.distanceMarkers[index]) {
                VanillaLocationManager.map.removeLayer(window.distanceMarkers[index]);
            }

            // Çizgiyi kaldır
            if (window.distanceLines[index]) {
                VanillaLocationManager.map.removeLayer(window.distanceLines[index]);
            }

            // Array'den sil
            window.distancePoints.splice(index, 1);
            window.distanceMarkers.splice(index, 1);
            window.distanceLines.splice(index, 1);

            // UI güncelle
            updateDistanceList();

            // JSON field güncelle
            document.getElementById('nearby_distances').value = JSON.stringify(window.distancePoints);

            window.toast?.info('Mesafe noktası silindi');
        };

        function updateDistanceList() {
            const container = document.getElementById('distance-list');
            if (!container) return;

            if (window.distancePoints.length === 0) {
                container.innerHTML = `
            <div class="text-center text-xs text-gray-500 dark:text-gray-400 py-4">
                <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
                Yukarıdaki butonlara tıklayın, haritada noktayı işaretleyin
            </div>
        `;
                return;
            }

            let html = '';
            window.distancePoints.forEach((point, index) => {
                html += `
            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border border-purple-200 dark:border-purple-800/30 shadow-sm">
                <div class="flex items-center gap-3">
                    <span class="text-lg">${point.icon}</span>
                    <div>
                        <div class="font-medium text-sm text-gray-900 dark:text-white">${point.name}</div>
                        <div class="text-xs text-purple-600 dark:text-purple-400 font-bold">${point.displayDistance} ${point.unit}</div>
                    </div>
                </div>
                <button type="button" onclick="removeDistancePoint(${index})"
                    class="p-1.5 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-lg transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        `;
            });

            container.innerHTML = html;
        }

        function calculateDistance(lat1, lon1, lat2, lon2) {
            // Haversine formula (mesafe hesaplama)
            const R = 6371e3; // Earth radius in meters
            const φ1 = lat1 * Math.PI / 180;
            const φ2 = lat2 * Math.PI / 180;
            const Δφ = (lat2 - lat1) * Math.PI / 180;
            const Δλ = (lon2 - lon1) * Math.PI / 180;

            const a = Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
                Math.cos(φ1) * Math.cos(φ2) *
                Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

            return R * c; // Distance in meters
        }

        // 🆕 PHASE 3: Property Boundary Drawing System
        window.propertyBoundary = null;
        window.drawnItems = null;
        window.drawControl = null;

        window.startDrawingBoundary = async function() {
            // ✅ Harita kontrolü
            if (!VanillaLocationManager.map) {
                window.toast?.error('❌ Harita yüklenmedi! Lütfen sayfayı yenileyin.');
                console.error('❌ Map not initialized in startDrawingBoundary');
                return;
            }

            // ✅ Leaflet.draw kontrolü ve dinamik yükleme
            if (typeof L.Control.Draw === 'undefined') {
                console.warn('⚠️ Leaflet.draw yükleniyor...');
                window.toast?.info('🎨 Çizim aracı yükleniyor, lütfen bekleyin...');

                try {
                    await loadLeafletDraw();
                    console.log('✅ Leaflet.draw başarıyla yüklendi');
                    window.toast?.success('✅ Çizim aracı hazır!');
                    // Tekrar çağır
                    window.startDrawingBoundary();
                    return;
                } catch (error) {
                    console.error('❌ Leaflet.draw yüklenemedi:', error);
                    window.toast?.error('❌ Çizim aracı yüklenemedi. Lütfen sayfayı yenileyin.');
                    return;
                }
            }

            // Mevcut boundary varsa temizle
            if (window.propertyBoundary) {
                clearBoundary();
            }

            // FeatureGroup oluştur (drawn items için)
            if (!window.drawnItems) {
                window.drawnItems = new L.FeatureGroup();
                VanillaLocationManager.map.addLayer(window.drawnItems);
            }

            // Draw control ekle
            window.drawControl = new L.Control.Draw({
                draw: {
                    polygon: {
                        shapeOptions: {
                            color: '#10b981',
                            fillColor: '#10b981',
                            fillOpacity: 0.3,
                            weight: 3
                        },
                        showArea: true,
                        metric: true
                    },
                    polyline: false,
                    rectangle: false,
                    circle: false,
                    marker: false,
                    circlemarker: false
                },
                edit: {
                    featureGroup: window.drawnItems,
                    remove: true
                }
            });

            VanillaLocationManager.map.addControl(window.drawControl);

            // Polygon çizim event'i
            VanillaLocationManager.map.on(L.Draw.Event.CREATED, function(e) {
                const layer = e.layer;
                window.drawnItems.addLayer(layer);
                window.propertyBoundary = layer;

                // GeoJSON al
                const geojson = layer.toGeoJSON();
                document.getElementById('boundary_geojson').value = JSON.stringify(geojson);

                // Alan hesapla (m²)
                const area = L.GeometryUtil.geodesicArea(layer.getLatLngs()[0]);
                document.getElementById('boundary_area').value = Math.round(area);

                // UI güncelle
                const infoDiv = document.getElementById('boundary-info');
                const areaDisplay = document.getElementById('boundary-area-display');

                if (infoDiv) infoDiv.classList.remove('hidden');
                if (areaDisplay) {
                    if (area >= 10000) {
                        areaDisplay.textContent = (area / 10000).toFixed(2) + ' dönüm (' + Math.round(area)
                            .toLocaleString() + ' m²)';
                    } else {
                        areaDisplay.textContent = Math.round(area).toLocaleString() + ' m²';
                    }
                }

                log('✅ Mülk sınırı çizildi. Alan:', Math.round(area), 'm²');
                window.toast?.success(`Sınır çizildi! Alan: ${Math.round(area).toLocaleString()} m²`);

                // Draw control'ü kaldır (tek polygon)
                if (window.drawControl) {
                    VanillaLocationManager.map.removeControl(window.drawControl);
                    window.drawControl = null;
                }
            });

            window.toast?.info('📐 Polygon çizimi başladı! Haritada noktaları işaretleyin');
            log('📐 Boundary drawing mode aktif');
        };

        window.clearBoundary = function() {
            if (window.drawnItems) {
                window.drawnItems.clearLayers();
            }

            if (window.drawControl) {
                VanillaLocationManager.map.removeControl(window.drawControl);
                window.drawControl = null;
            }

            window.propertyBoundary = null;
            document.getElementById('boundary_geojson').value = '';
            document.getElementById('boundary_area').value = '';

            const infoDiv = document.getElementById('boundary-info');
            if (infoDiv) infoDiv.classList.add('hidden');

            window.toast?.info('Sınır temizlendi');
            log('🗑️ Boundary cleared');
        };

        // Leaflet.GeometryUtil (alan hesaplama için)
        if (typeof L.GeometryUtil === 'undefined') {
            L.GeometryUtil = {
                geodesicArea: function(latLngs) {
                    const pointsCount = latLngs.length;
                    let area = 0.0;
                    const d2r = Math.PI / 180;
                    let p1 = latLngs[pointsCount - 1];

                    for (let i = 0; i < pointsCount; i++) {
                        const p2 = latLngs[i];
                        area += (p2.lng - p1.lng) * d2r * (2 + Math.sin(p1.lat * d2r) + Math.sin(p2.lat * d2r));
                        p1 = p2;
                    }

                    area = area * 6378137.0 * 6378137.0 / 2.0;
                    return Math.abs(area);
                }
            };
        }

        // searchNearby fonksiyonu kaldırıldı - Mesafe Ölçüm sistemi kullanın

        // ============================================
        // 🎨 LEAFLET.DRAW DİNAMİK YÜKLEME FONKSİYONU (Context7 Optimized)
        // ============================================
        function loadLeafletDraw() {
            return new Promise((resolve, reject) => {
                log('📦 Leaflet.draw yükleniyor...');

                // ✅ Context7: Multiple CDN fallback
                const cdnUrls = [
                    'https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js',
                    'https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js',
                    'https://cdn.jsdelivr.net/npm/leaflet-draw@1.0.4/dist/leaflet.draw.js'
                ];

                const cssUrls = [
                    'https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css',
                    'https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css',
                    'https://cdn.jsdelivr.net/npm/leaflet-draw@1.0.4/dist/leaflet.draw.css'
                ];

                // CSS yükle (ilk CDN'den dene)
                let cssLoaded = false;
                const loadCSS = (index = 0) => {
                    if (index >= cssUrls.length) {
                        console.warn('⚠️ Leaflet.draw CSS yüklenemedi, devam ediliyor...');
                        cssLoaded = true;
                        tryLoadJS();
                        return;
                    }

                    const link = document.createElement('link');
                    link.rel = 'stylesheet';
                    link.href = cssUrls[index];
                    link.onload = () => {
                        log('✅ Leaflet.draw CSS yüklendi (CDN ' + (index + 1) + ')');
                        cssLoaded = true;
                        tryLoadJS();
                    };
                    link.onerror = () => {
                        log('⚠️ CSS CDN ' + (index + 1) + ' başarısız, sonraki deneniyor...');
                        loadCSS(index + 1);
                    };
                    document.head.appendChild(link);
                };

                // JS yükle (fallback ile)
                let jsLoaded = false;
                const tryLoadJS = (index = 0) => {
                    if (index >= cdnUrls.length) {
                        console.error('❌ Tüm Leaflet.draw CDN\'leri başarısız');
                        reject(new Error('Leaflet.draw script yüklenemedi (tüm CDN\'ler denendi)'));
                        return;
                    }

                    // CSS yüklenmesini bekle
                    if (!cssLoaded && index === 0) {
                        setTimeout(() => tryLoadJS(index), 100);
                        return;
                    }

                    const script = document.createElement('script');
                    script.src = cdnUrls[index];
                    script.onload = () => {
                        log('✅ Leaflet.draw JS yüklendi (CDN ' + (index + 1) + ')');
                        jsLoaded = true;
                        resolve();
                    };
                    script.onerror = () => {
                        log('⚠️ JS CDN ' + (index + 1) + ' başarısız, sonraki deneniyor...');
                        tryLoadJS(index + 1);
                    };
                    document.body.appendChild(script);
                };

                // CSS yüklemeyi başlat
                loadCSS();
            });
        }

        // ============================================
        // 🗺️ HARİTA DURUM MONİTORİNG (Debug için)
        // ============================================
        // ✅ window.mapStatus() fonksiyonu yukarıda tanımlandı (Context7 standartlarına uygun)
        // Console'da kullanım: window.mapStatus() veya console.table(window.mapStatus())
    </script>

    <!-- Save Draft Handler -->
    <script>
        document.getElementById('save-draft-btn')?.addEventListener('click', function() {
            if (window.StableCreateCore && window.StableCreateCore.saveDraft) {
                window.StableCreateCore.saveDraft();
            }
        });
    </script>

    <!-- ✅ PHASE 1 - CLIENT-SIDE VALIDATION SYSTEM -->
    <script>
        // 🎯 Real-time Validation Manager (Context7)
        const ValidationManager = {
            rules: {
                baslik: {
                    required: true,
                    minLength: 10,
                    maxLength: 200,
                    message: 'Başlık 10-200 karakter arası olmalıdır'
                },
                aciklama: {
                    required: true,
                    minLength: 50,
                    maxLength: 5000,
                    message: 'Açıklama 50-5000 karakter arası olmalıdır'
                },
                ana_kategori_id: {
                    required: true,
                    message: 'Ana kategori seçmelisiniz'
                },
                alt_kategori_id: {
                    required: true,
                    message: 'Alt kategori seçmelisiniz'
                },
                yayin_tipi_id: {
                    required: true,
                    message: 'Yayın tipi seçmelisiniz'
                },
                fiyat: {
                    required: true,
                    min: 0,
                    message: 'Geçerli bir fiyat girmelisiniz'
                },
                il_id: {
                    required: true,
                    message: 'İl seçmelisiniz'
                },
                ilce_id: {
                    required: true,
                    message: 'İlçe seçmelisiniz'
                },
                adres: {
                    required: true,
                    minLength: 10,
                    message: 'Adres en az 10 karakter olmalıdır'
                }
            },

            validate(fieldName, value) {
                const rule = this.rules[fieldName];
                if (!rule) return {
                    valid: true
                };

                // Required check
                if (rule.required && (!value || value.toString().trim() === '')) {
                    return {
                        valid: false,
                        message: rule.message
                    };
                }

                // Skip other checks if field is empty and not required
                if (!value) return {
                    valid: true
                };

                // Min length check
                if (rule.minLength && value.toString().length < rule.minLength) {
                    return {
                        valid: false,
                        message: rule.message
                    };
                }

                // Max length check
                if (rule.maxLength && value.toString().length > rule.maxLength) {
                    return {
                        valid: false,
                        message: rule.message
                    };
                }

                // Min value check (for numbers)
                if (rule.min !== undefined && parseFloat(value) < rule.min) {
                    return {
                        valid: false,
                        message: rule.message
                    };
                }

                return {
                    valid: true
                };
            },

            showError(fieldName, message) {
                const field = document.getElementById(fieldName);
                if (!field) return;

                // Add error class (Tailwind) - only base classes, no pseudo-classes
                field.classList.add('border-red-500');
                field.classList.remove('border-gray-300');

                // Remove any existing focus classes and add error focus class
                // Note: Tailwind pseudo-classes (focus:, dark:) cannot be added via classList
                // They must be in the HTML template or handled via CSS

                // Add error border on focus via inline style or data attribute
                field.setAttribute('data-error', 'true');

                // Show error message
                let errorDiv = field.parentElement.querySelector('.validation-error');
                if (!errorDiv) {
                    errorDiv = document.createElement('div');
                    errorDiv.className = 'validation-error text-red-600 text-sm mt-1 flex items-center gap-1';
                    errorDiv.innerHTML = `
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span></span>
            `;
                    field.parentElement.appendChild(errorDiv);
                }
                errorDiv.querySelector('span').textContent = message;

                // Shake animation
                field.style.animation = 'shake 0.5s';
                setTimeout(() => {
                    field.style.animation = '';
                }, 500);
            },

            clearError(fieldName) {
                const field = document.getElementById(fieldName);
                if (!field) return;

                // Remove error class - only base classes
                field.classList.remove('border-red-500');
                field.classList.add('border-gray-300');
                field.removeAttribute('data-error');

                // Hide error message
                const errorDiv = field.parentElement.querySelector('.validation-error');
                if (errorDiv) {
                    errorDiv.remove();
                }
            },

            validateAll() {
                let isValid = true;
                let firstErrorField = null;

                Object.keys(this.rules).forEach(fieldName => {
                    const field = document.getElementById(fieldName);
                    if (!field) return;

                    const result = this.validate(fieldName, field.value);

                    if (!result.valid) {
                        this.showError(fieldName, result.message);
                        if (!firstErrorField) {
                            firstErrorField = field;
                        }
                        isValid = false;
                    } else {
                        this.clearError(fieldName);
                    }
                });

                // Scroll to first error
                if (firstErrorField) {
                    firstErrorField.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    firstErrorField.focus();
                }

                return isValid;
            },

            getCompletionPercentage() {
                const totalFields = Object.keys(this.rules).length;
                let completedFields = 0;

                Object.keys(this.rules).forEach(fieldName => {
                    const field = document.getElementById(fieldName);
                    if (!field) return;

                    const result = this.validate(fieldName, field.value);
                    if (result.valid && field.value) {
                        completedFields++;
                    }
                });

                return Math.round((completedFields / totalFields) * 100);
            },

            updateProgressIndicator() {
                const percentage = this.getCompletionPercentage();
                const indicator = document.getElementById('form-progress-indicator');

                if (indicator) {
                    indicator.textContent = `Form Completion: ${percentage}%`;
                    indicator.className =
                        `text-sm font-medium ${percentage === 100 ? 'text-green-600' : 'text-blue-600'}`;
                }
            }
        };

        // Attach validation listeners on DOM ready
        document.addEventListener('DOMContentLoaded', () => {
            console.log('✅ Validation Manager initializing...');

            Object.keys(ValidationManager.rules).forEach(fieldName => {
                const field = document.getElementById(fieldName);
                if (!field) return;

                // Real-time validation on blur
                field.addEventListener('blur', (e) => {
                    const result = ValidationManager.validate(fieldName, e.target.value);

                    if (!result.valid) {
                        ValidationManager.showError(fieldName, result.message);
                    } else {
                        ValidationManager.clearError(fieldName);
                    }

                    ValidationManager.updateProgressIndicator();
                });

                // Clear error on input
                field.addEventListener('input', () => {
                    ValidationManager.clearError(fieldName);
                    ValidationManager.updateProgressIndicator();
                });

                // For select elements, also listen to change
                if (field.tagName === 'SELECT') {
                    field.addEventListener('change', () => {
                        ValidationManager.clearError(fieldName);
                        ValidationManager.updateProgressIndicator();
                    });
                }
            });

            // Validate on form submit
            const form = document.querySelector('form[action*="ilanlar"]');
            if (form) {
                form.addEventListener('submit', (e) => {
                    // ✅ Loading state ekle
                    const submitBtn = document.getElementById('submit-btn');
                    const submitIcon = document.getElementById('submit-icon');
                    const submitText = document.getElementById('submit-text');
                    const submitSpinner = document.getElementById('submit-spinner');

                    if (submitBtn && submitText && submitSpinner && submitIcon) {
                        submitBtn.disabled = true;
                        submitIcon.classList.add('hidden');
                        submitSpinner.classList.remove('hidden');
                        submitText.textContent = 'Kaydediliyor...';
                    }

                    if (!ValidationManager.validateAll()) {
                        e.preventDefault();
                        window.toast?.error('❌ Lütfen tüm gerekli alanları doldurun');

                        // Count errors
                        const errorCount = document.querySelectorAll('.validation-error').length;
                        window.toast?.warning(`⚠️ ${errorCount} alan hatalı veya eksik`);

                        // Loading state'i geri al
                        if (submitBtn && submitText && submitSpinner && submitIcon) {
                            submitBtn.disabled = false;
                            submitIcon.classList.remove('hidden');
                            submitSpinner.classList.add('hidden');
                            submitText.textContent = 'İlanı Yayınla';
                        }

                        return false;
                    }

                    // Show success feedback
                    window.toast?.success('✅ Form doğrulandı, kaydediliyor...');
                });
            }

            // Initial progress indicator update
            setTimeout(() => {
                ValidationManager.updateProgressIndicator();
            }, 500);

            console.log('✅ Validation Manager initialized (' + Object.keys(ValidationManager.rules).length +
                ' rules)');
        });

        // Add shake animation CSS
        const style = document.createElement('style');
        style.textContent = `
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
        20%, 40%, 60%, 80% { transform: translateX(5px); }
    }

    .validation-error {
        animation: slideDown 0.3s ease-out;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
`;
        document.head.appendChild(style);

        // ===================================
        // Kategori-Specific Section Visibility
        // Context7: Arsa için Site/Apartman ve Anahtar gizle
        // ===================================
        document.addEventListener('DOMContentLoaded', function() {
            const mainCategorySelect = document.querySelector('select[name="kategori_id"]');

            if (mainCategorySelect) {
                mainCategorySelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const categorySlug = selectedOption.getAttribute('data-slug') || '';

                    // Tüm kategori-specific section'ları al
                    const specificSections = document.querySelectorAll('.kategori-specific-section');

                    specificSections.forEach(section => {
                        const showFor = section.getAttribute('data-show-for-categories') || '';

                        // Konut kategorisi ise göster, değilse gizle
                        if (categorySlug.includes('konut') || categorySlug.includes('daire') ||
                            categorySlug.includes('villa')) {
                            section.style.display = 'block';
                        } else {
                            section.style.display = 'none';
                        }
                    });

                    console.log('✅ Kategori değişti:', categorySlug);
                });

                // Sayfa yüklendiğinde de kontrol et
                mainCategorySelect.dispatchEvent(new Event('change'));
            }
        });

        // ===================================
        // Draft Auto-save System
        // Context7: %100, Yalıhan Bekçi: ✅
        // ===================================
        const DraftAutoSave = {
            formId: 'ilan-create-form',
            interval: null,
            saveIntervalSeconds: 30,
            hasChanges: false,

            init() {
                this.checkForDraft();
                this.startAutoSave();
                this.preventDataLoss();
                this.trackChanges();
                this.updateProgressBar(); // Initial progress
            },

            checkForDraft() {
                const draft = this.loadDraft();

                if (draft && draft.timestamp) {
                    const draftAge = Date.now() - draft.timestamp;
                    const hours = Math.floor(draftAge / (1000 * 60 * 60));
                    const minutes = Math.floor((draftAge % (1000 * 60 * 60)) / (1000 * 60));

                    this.showRestoreButton(draft, hours, minutes);
                }
            },

            showRestoreButton(draft, hours, minutes) {
                const timeAgo = hours > 0 ?
                    `${hours} saat ${minutes} dakika önce` :
                    `${minutes} dakika önce`;

                const banner = document.createElement('div');
                banner.className =
                    'bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 p-4 mb-6 rounded-lg flex items-center justify-between animate-pulse';
                banner.innerHTML = `
            <div class="flex items-center">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="text-sm font-medium text-blue-800 dark:text-blue-300">
                        💾 Kaydedilmemiş taslak bulundu
                    </p>
                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                        Son kayıt: ${timeAgo}
                    </p>
                </div>
            </div>
            <div class="flex gap-2">
                <button type="button" onclick="DraftAutoSave.restoreDraft()"
                        class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 hover:scale-105 focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                    Geri Yükle
                </button>
                <button type="button" onclick="DraftAutoSave.discardDraft()"
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-800 text-gray-900 dark:text-white text-sm font-medium rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                    Sil
                </button>
            </div>
        `;

                const container = document.getElementById('draft-restore-banner');
                if (container) {
                    container.appendChild(banner);
                }
            },

            startAutoSave() {
                this.interval = setInterval(() => {
                    if (this.hasChanges) {
                        this.saveDraft();
                    }
                }, this.saveIntervalSeconds * 1000);

                console.log('✅ Auto-save başlatıldı (her 30 saniyede)');
            },

            saveDraft() {
                try {
                    const form = document.getElementById(this.formId);
                    if (!form) return;

                    const formData = new FormData(form);
                    const data = {};

                    formData.forEach((value, key) => {
                        if (value && value !== '') {
                            data[key] = value;
                        }
                    });

                    const draft = {
                        data: data,
                        timestamp: Date.now(),
                        version: '1.0',
                    };

                    localStorage.setItem('ilan_draft', JSON.stringify(draft));

                    console.log('✅ Draft saved:', new Date().toLocaleTimeString());

                    this.showSaveIndicator();
                    this.hasChanges = false; // Reset after save

                } catch (error) {
                    console.error('❌ Draft save error:', error);
                }
            },

            loadDraft() {
                try {
                    const draftJson = localStorage.getItem('ilan_draft');
                    return draftJson ? JSON.parse(draftJson) : null;
                } catch (error) {
                    console.error('❌ Draft load error:', error);
                    return null;
                }
            },

            restoreDraft() {
                const draft = this.loadDraft();
                if (!draft || !draft.data) return;

                let restoredCount = 0;

                Object.entries(draft.data).forEach(([key, value]) => {
                    const field = document.querySelector(`[name="${key}"]`);
                    if (field) {
                        if (field.type === 'checkbox') {
                            field.checked = value === 'on' || value === '1' || value === 1;
                        } else if (field.type === 'radio') {
                            if (field.value === value) {
                                field.checked = true;
                            }
                        } else {
                            field.value = value;
                        }

                        // Trigger change event (Alpine.js reactivity)
                        field.dispatchEvent(new Event('change', {
                            bubbles: true
                        }));
                        restoredCount++;
                    }
                });

                window.toast.success(`Taslak geri yüklendi (${restoredCount} alan)`);
                document.getElementById('draft-restore-banner').innerHTML = '';
                this.updateProgressBar();
            },

            discardDraft() {
                localStorage.removeItem('ilan_draft');
                document.getElementById('draft-restore-banner').innerHTML = '';
                window.toast.success('Taslak silindi');
            },

            clearDraft() {
                localStorage.removeItem('ilan_draft');
                this.hasChanges = false;
                console.log('✅ Draft cleared');
            },

            preventDataLoss() {
                window.addEventListener('beforeunload', (e) => {
                    if (this.hasChanges) {
                        e.preventDefault();
                        e.returnValue =
                            'Kaydedilmemiş değişiklikler var! Sayfadan ayrılmak istediğinize emin misiniz?';
                    }
                });
            },

            trackChanges() {
                const form = document.getElementById(this.formId);
                if (!form) return;

                form.addEventListener('input', () => {
                    this.hasChanges = true;
                });

                form.addEventListener('change', () => {
                    this.hasChanges = true;
                    this.updateProgressBar();
                });

                form.addEventListener('submit', () => {
                    this.hasChanges = false;
                    this.clearDraft();

                    // ✅ Loading state (zaten yukarıda eklenmiş)
                });
            },

            showSaveIndicator() {
                const indicator = document.getElementById('save-indicator');
                if (!indicator) return;

                indicator.innerHTML = `
            <svg class="w-3 h-3 mr-1 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span class="text-green-600">Kaydedildi ✓</span>
        `;

                setTimeout(() => {
                    indicator.innerHTML = `
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Otomatik kayıt aktif
            `;
                }, 2000);
            },

            getProgress() {
                const form = document.getElementById(this.formId);
                if (!form) return 0;

                const requiredFields = form.querySelectorAll('[required]');
                if (requiredFields.length === 0) return 0;

                const filledFields = Array.from(requiredFields).filter(field => {
                    if (field.type === 'checkbox') return field.checked;
                    return field.value && field.value.trim() !== '';
                });

                return Math.round((filledFields.length / requiredFields.length) * 100);
            },

            updateProgressBar() {
                const progress = this.getProgress();
                const progressBar = document.getElementById('form-progress-bar');
                const progressText = document.getElementById('form-progress-text');

                if (progressBar) {
                    progressBar.style.width = `${progress}%`;
                    progressBar.className =
                        `h-full rounded-full transition-all duration-500 ${this.getProgressColor(progress)}`;
                }

                if (progressText) {
                    progressText.textContent = `%${progress} tamamlandı`;
                }
            },

            getProgressColor(progress) {
                if (progress < 33) return 'bg-red-500';
                if (progress < 66) return 'bg-yellow-500';
                return 'bg-green-500';
            }
        };

        // Initialize Draft Auto-save on page load
        document.addEventListener('DOMContentLoaded', () => {
            DraftAutoSave.init();
            console.log('✅ Draft Auto-save initialized');
        });
    </script>
@endpush
