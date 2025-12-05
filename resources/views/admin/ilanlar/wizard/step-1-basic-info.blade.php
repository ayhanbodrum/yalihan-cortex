{{-- STEP 1: TEMEL BİLGİLER --}}
<div class="space-y-6">
    <div class="mb-6">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">📝 Temel Bilgiler</h3>
        <p class="text-sm text-gray-600 dark:text-gray-400">İlanınızın temel bilgilerini doldurun</p>
    </div>

    {{-- Kategori Sistemi --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div>
            <label for="ana_kategori_id" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                Ana Kategori <span class="text-red-500">*</span>
            </label>
            <select name="ana_kategori_id" id="ana_kategori_id" required onchange="loadAltKategoriler(this.value)"
                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg
                           bg-white dark:bg-gray-800 text-black dark:text-white
                           focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                           transition-all duration-200">
                <option value="">Ana Kategori Seçin</option>
                @foreach ($anaKategoriler ?? [] as $kategori)
                    <option value="{{ $kategori->id }}" {{ old('ana_kategori_id') == $kategori->id ? 'selected' : '' }}>
                        {{ $kategori->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="alt_kategori_id" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                Alt Kategori <span class="text-red-500">*</span>
            </label>
            <select name="alt_kategori_id" id="alt_kategori_id" required onchange="loadYayinTipleri(this.value)"
                disabled
                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg
                           bg-white dark:bg-gray-800 text-black dark:text-white
                           focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                           disabled:opacity-50 disabled:cursor-not-allowed
                           transition-all duration-200">
                <option value="">Önce Ana Kategori Seçin</option>
            </select>
        </div>

        <div>
            <label for="yayin_tipi_id" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                Yayın Tipi <span class="text-red-500">*</span>
            </label>
            <select name="yayin_tipi_id" id="yayin_tipi_id" required disabled
                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg
                           bg-white dark:bg-gray-800 text-black dark:text-white
                           focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                           disabled:opacity-50 disabled:cursor-not-allowed
                           transition-all duration-200">
                <option value="">Önce Alt Kategori Seçin</option>
            </select>
        </div>
    </div>

    {{-- Başlık --}}
    <div>
        <label for="baslik" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
            Başlık <span class="text-red-500">*</span>
        </label>
        <div class="flex gap-2">
            <input type="text" name="baslik" id="baslik" required value="{{ old('baslik') }}"
                placeholder="Örn: Muğla Bodrum'da Denize Sıfır Lüks Villa"
                class="flex-1 px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg
                          bg-white dark:bg-gray-800 text-black dark:text-white
                          focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                          transition-all duration-200">
            <button type="button" onclick="generateTitleWithAI()"
                class="px-4 py-2.5 bg-purple-600 dark:bg-purple-500 text-white rounded-lg
                       hover:bg-purple-700 dark:hover:bg-purple-600
                       hover:scale-105 active:scale-95
                       focus:ring-2 focus:ring-purple-500 focus:ring-offset-2
                       transition-all duration-200 ease-in-out
                       shadow-md hover:shadow-lg font-medium">
                🤖 AI ile Üret
            </button>
        </div>
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
            Kategori ve lokasyon bilgilerinize göre AI ile başlık önerisi alabilirsiniz
        </p>
    </div>

    {{-- Fiyat ve Para Birimi --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div>
            <label for="fiyat" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                Fiyat <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <input type="text" name="fiyat" id="fiyat" required
                    value="{{ old('fiyat') ? number_format(old('fiyat'), 0, ',', '.') : '' }}" placeholder="5.000.000"
                    oninput="formatPriceInput(this)" onblur="updatePriceText()"
                    class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg
                              bg-white dark:bg-gray-800 text-black dark:text-white
                              focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                              transition-all duration-200">
                <input type="hidden" name="fiyat_raw" id="fiyat_raw" value="{{ old('fiyat') }}">
            </div>
            {{-- Yazıyla Fiyat Gösterimi --}}
            <div id="price_text_display"
                class="mt-2 px-3 py-2 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                <p class="text-sm font-medium text-blue-900 dark:text-blue-200">
                    <span class="font-semibold">Yazıyla:</span>
                    <span id="price_text_value" class="ml-2 text-blue-700 dark:text-blue-300">Fiyat giriniz</span>
                </p>
            </div>
        </div>

        <div>
            <label for="para_birimi" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                Para Birimi <span class="text-red-500">*</span>
            </label>
            <select name="para_birimi" id="para_birimi" required onchange="updatePriceText()"
                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg
                           bg-white dark:bg-gray-800 text-black dark:text-white
                           focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                           transition-all duration-200">
                <option value="TRY" {{ old('para_birimi', 'TRY') == 'TRY' ? 'selected' : '' }}>₺ Türk Lirası (TRY)
                </option>
                <option value="USD" {{ old('para_birimi') == 'USD' ? 'selected' : '' }}>$ Amerikan Doları (USD)
                </option>
                <option value="EUR" {{ old('para_birimi') == 'EUR' ? 'selected' : '' }}>€ Euro (EUR)</option>
                <option value="GBP" {{ old('para_birimi') == 'GBP' ? 'selected' : '' }}>£ İngiliz Sterlini (GBP)
                </option>
            </select>
        </div>
    </div>

    {{-- Konum Bilgileri Formu --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-lg p-6 mb-6">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Konum Bilgileri</h3>
        </div>

        <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- İl --}}
                <div>
                    <label for="il_id" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            </svg>
                            <span>İl <span class="text-red-500">*</span></span>
                        </div>
                    </label>
                    <select name="il_id" id="il_id" required onchange="loadIlceler(this.value)"
                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg
                                       bg-white dark:bg-gray-800 text-black dark:text-white
                                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                       transition-all duration-200">
                        <option value="">İl Seçin</option>
                        @foreach ($iller ?? [] as $il)
                            <option value="{{ $il->id }}" {{ old('il_id') == $il->id ? 'selected' : '' }}>
                                {{ $il->il_adi ?? $il->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- İlçe --}}
                <div>
                    <label for="ilce_id" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span>İlçe <span class="text-red-500">*</span></span>
                        </div>
                    </label>
                    <select name="ilce_id" id="ilce_id" required onchange="loadMahalleler(this.value)" disabled
                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg
                                       bg-white dark:bg-gray-800 text-black dark:text-white
                                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                       disabled:opacity-50 disabled:cursor-not-allowed
                                       transition-all duration-200">
                        <option value="">Önce İl Seçin</option>
                    </select>
                </div>

                {{-- Mahalle --}}
                <div>
                    <label for="mahalle_id" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span>Mahalle</span>
                        </div>
                    </label>
                    <select name="mahalle_id" id="mahalle_id" disabled
                        onchange="updateMarkerFromMahalle(this.value, this.options[this.selectedIndex])"
                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg
                                       bg-white dark:bg-gray-800 text-black dark:text-white
                                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                       disabled:opacity-50 disabled:cursor-not-allowed
                                       transition-all duration-200">
                        <option value="">Önce İlçe Seçin</option>
                    </select>
                </div>
            </div>

            {{-- Adres --}}
            <div>
                <label for="adres" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Adres <span class="text-red-500">*</span></span>
                    </div>
                </label>
                <div class="relative">
                    <textarea name="adres" id="adres" rows="3"
                        placeholder="Sokak, cadde, mahalle ve bina numarası bilgilerini girin"
                        class="w-full px-4 py-2.5 pr-12 border border-gray-300 dark:border-gray-600 rounded-lg
                                   bg-white dark:bg-gray-800 text-black dark:text-white
                                   placeholder-gray-400 dark:placeholder-gray-500
                                   focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                   hover:border-gray-400 dark:hover:border-gray-500
                                   transition-all duration-200 ease-in-out
                                   resize-y min-h-[80px]
                                   shadow-sm hover:shadow-md focus:shadow-lg">{{ old('adres') }}</textarea>
                    <div class="absolute top-3 right-3 text-gray-400 dark:text-gray-500 pointer-events-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                        </svg>
                    </div>
                </div>
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Haritada marker'ı sürüklediğinizde adres otomatik doldurulacak</span>
                </p>
            </div>
        </div>
    </div>

</div>

{{-- GeoJSON Yükleme Kartı (Step 1) --}}
<div x-data="geojsonUploader()" x-init="init()"
    class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-lg overflow-hidden mt-6">
    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">TKGM GeoJSON Yükle</h3>
                <p class="text-xs text-gray-600 dark:text-gray-400">TKGM'den indirdiğiniz JSON/GeoJSON dosyasını
                    yükleyin, harita otomatik güncellenecek</p>
            </div>
        </div>
    </div>
    <div class="p-4">
        <div class="flex items-center gap-4">
            <input type="file" id="step1_geojson_upload" accept=".json,.geojson" class="hidden"
                @change="handleGeoJsonUpload($event)">
            <label for="step1_geojson_upload"
                class="flex-1 px-6 py-3 bg-blue-600 dark:bg-blue-500 text-white rounded-lg
                       hover:bg-blue-700 dark:hover:bg-blue-600 hover:scale-105 active:scale-95
                       focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                       transition-all duration-200 ease-in-out
                       shadow-md hover:shadow-lg font-medium flex items-center justify-center gap-2 cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                <span>JSON / GeoJSON Yükle</span>
            </label>
        </div>
        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Dosya yüklendiğinde harita otomatik olarak parsel konumuna gidecek ve İl/İlçe/Mahalle alanları doldurulacak
        </p>
        <div x-show="error"
            class="mt-3 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
            <p class="text-sm text-red-700 dark:text-red-300" x-text="error"></p>
        </div>
        <div x-show="success"
            class="mt-3 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
            <p class="text-sm text-green-700 dark:text-green-300">✅ GeoJSON başarıyla yüklendi! Harita güncellendi.</p>
        </div>
    </div>
</div>

{{-- Harita Görünümü (Altta - Tam Genişlik) --}}
<div
    class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-lg overflow-hidden mt-6">
    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Harita</h3>
                    <p class="text-xs text-gray-600 dark:text-gray-400">Haritaya tıklayarak konum seçin - TKGM verileri
                        otomatik gelecek</p>
                </div>
            </div>
            <div id="map-layer-control" class="flex items-center gap-2">
                <button type="button" id="map-view-btn" onclick="switchMapView('map')"
                    class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-blue-500 transition-all duration-200 shadow-md">
                    🗺️ Harita
                </button>
                <button type="button" id="satellite-view-btn" onclick="switchMapView('satellite')"
                    class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-500 transition-all duration-200">
                    🛰️ Uydu
                </button>
            </div>
        </div>
    </div>
    <div id="wizard-map-container" class="relative" style="height: 600px;">
        {{-- Harita buraya eklenecek --}}
        <div id="wizard-map" data-lat-field="enlem" data-lng-field="boylam" data-address-field="adres"
            class="w-full h-full" style="height: 600px;">
            {{-- Loading state --}}
            <div class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 z-10"
                id="wizard-map-loading" role="status" aria-live="polite" aria-busy="true">
                <div class="text-center">
                    <div
                        class="inline-flex items-center justify-center w-16 h-16 rounded-xl bg-white dark:bg-gray-800 shadow-xl mb-3">
                        <svg class="w-8 h-8 text-blue-500 animate-pulse" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                        </svg>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 font-medium">Harita yükleniyor...</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Yakın Lokasyonlar (POI) Kartı --}}
<div x-data="poiWidget()" x-init="init()"
    class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-lg overflow-hidden mt-6">
    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Yakın Lokasyonlar (POI)</h3>
                    <p class="text-xs text-gray-600 dark:text-gray-400">Fotoğraflarda ve videoda gösterilecek yakın
                        noktaları seçin</p>
                </div>
            </div>
            <button type="button" @click="loadPOIs()" :disabled="loading"
                class="px-4 py-2 bg-purple-600 dark:bg-purple-500 text-white text-sm font-medium rounded-lg
                       hover:bg-purple-700 dark:hover:bg-purple-600 hover:scale-105 active:scale-95
                       focus:ring-2 focus:ring-purple-500 transition-all duration-200 shadow-md
                       disabled:opacity-50 disabled:cursor-not-allowed">
                <span x-show="!loading">🔄 Yenile</span>
                <span x-show="loading" class="flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    Yükleniyor...
                </span>
            </button>
        </div>
    </div>

    <div class="p-4">
        {{-- Kategori Filtreleri --}}
        <div x-show="pois.length > 0 || loading" class="mb-4">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Kategoriler:</span>
                <button type="button" @click="selectedCategories = []; loadPOIs()"
                    :class="selectedCategories.length === 0 ?
                        'px-3 py-1.5 bg-purple-600 text-white rounded-lg text-xs font-medium' :
                        'px-3 py-1.5 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-xs font-medium hover:bg-gray-300 dark:hover:bg-gray-600'">
                    Tümü
                </button>
                <template x-for="category in availableCategories" :key="category.type">
                    <button type="button" @click="toggleCategory(category.type)"
                        :class="selectedCategories.includes(category.type) ?
                            'px-3 py-1.5 bg-purple-600 text-white rounded-lg text-xs font-medium' :
                            'px-3 py-1.5 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-xs font-medium hover:bg-gray-300 dark:hover:bg-gray-600'">
                        <span x-text="category.label"></span>
                        <span x-show="getCategoryCount(category.type) > 0"
                            class="ml-1.5 px-1.5 py-0.5 bg-white/20 rounded text-[10px]"
                            x-text="getCategoryCount(category.type)"></span>
                    </button>
                </template>
            </div>
        </div>

        <template x-if="pois.length === 0 && !loading">
            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                </svg>
                <p class="text-sm">Önce haritada konum seçin, sonra yakın lokasyonları yükleyin</p>
            </div>
        </template>

        <template x-if="loading">
            <div class="text-center py-8">
                <div class="inline-flex items-center gap-2 text-gray-600 dark:text-gray-400">
                    <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span>Yakın lokasyonlar yükleniyor...</span>
                </div>
            </div>
        </template>

        <div x-show="pois.length > 0" class="space-y-3 max-h-[400px] overflow-y-auto">
            <template x-for="poi in filteredPOIs" :key="poi.id">
                <label
                    class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700
                           hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer
                           transition-all duration-200">
                    <input type="checkbox" :value="poi.id" x-model="selectedPOIs" @change="updatePOIInput()"
                        class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500
                               dark:bg-gray-700 dark:border-gray-600">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-medium text-gray-900 dark:text-white" x-text="poi.name"></span>
                            <span class="text-xs text-gray-500 dark:text-gray-400"
                                x-text="poi.distance_m + ' m'"></span>
                        </div>
                        <div class="flex items-center gap-2 mt-1">
                            <span
                                class="text-xs px-2 py-0.5 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 rounded"
                                x-text="poi.category"></span>
                            <span class="text-xs text-gray-500 dark:text-gray-400"
                                x-text="poi.walking_minutes + ' dk yürüyüş'"></span>
                        </div>
                    </div>
                </label>
            </template>
        </div>

        <input type="hidden" name="environment_pois" id="environment_pois" :value="JSON.stringify(selectedPOIData)">
    </div>
</div>

{{-- Harita Entegrasyonu Script --}}
@push('scripts')
    <script>
        (function() {
            let wizardMap = null;
            let wizardMarker = null;

            // ✅ Context7: Koordinat kaydetme helper
            function saveCoordinates(lat, lng) {
                const form = document.getElementById('ilan-wizard-form');
                if (!form) return;

                let latInput = document.querySelector('[name="enlem"]') || document.querySelector('[name="latitude"]');
                let lngInput = document.querySelector('[name="boylam"]') || document.querySelector(
                    '[name="longitude"]');

                if (!latInput) {
                    latInput = document.createElement('input');
                    latInput.type = 'hidden';
                    latInput.name = 'enlem';
                    form.appendChild(latInput);
                }

                if (!lngInput) {
                    lngInput = document.createElement('input');
                    lngInput.type = 'hidden';
                    lngInput.name = 'boylam';
                    form.appendChild(lngInput);
                }

                latInput.value = lat.toFixed(6);
                lngInput.value = lng.toFixed(6);
            }

            // ✅ Context7: Reverse geocoding helper
            function reverseGeocode(lat, lng, callback) {
                const url = window.APIConfig?.geo?.reverseGeocode || '/api/geo/reverse-geocode';
                fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            latitude: lat,
                            longitude: lng
                        })
                    })
                    .then(res => res.json())
                    .then(result => {
                        const data = result.success ? result.data : result;
                        if (data?.address) {
                            const addr = data.address;
                            const parts = [];
                            if (addr.road) parts.push(addr.road);
                            if (addr.house_number) parts.push('No:' + addr.house_number);
                            if (addr.suburb || addr.neighbourhood) parts.push(addr.suburb || addr.neighbourhood);
                            if (addr.town || addr.city_district) parts.push(addr.town || addr.city_district);
                            if (addr.province || addr.state) parts.push(addr.province || addr.state);
                            callback(parts.join(', '));
                        }
                    })
                    .catch(() => callback(null));
            }

            function initWizardMap() {
                const mapElement = document.getElementById('wizard-map');
                const loadingElement = document.getElementById('wizard-map-loading');

                if (!mapElement) return;

                if (typeof L === 'undefined') {
                    if (loadingElement) {
                        loadingElement.innerHTML =
                            '<div class="text-center text-red-500 p-4">Harita kütüphanesi yüklenemedi</div>';
                    }
                    setTimeout(initWizardMap, 500);
                    return;
                }

                if (loadingElement) loadingElement.style.display = 'none';

                const map = L.map('wizard-map', {
                    center: [37.0344, 27.4305],
                    zoom: 12,
                    zoomControl: true
                });

                const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors',
                    maxZoom: 19
                });

                const satelliteLayer = L.tileLayer(
                    'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                        attribution: '© Esri, Maxar, GeoEye, Earthstar Geographics, CNES/Airbus DS, USDA, USGS, AeroGRID, IGN, IGP, and the GIS User Community',
                        maxZoom: 19
                    });

                osmLayer.addTo(map);

                window.wizardMapLayers = {
                    map: osmLayer,
                    satellite: satelliteLayer,
                    current: 'map'
                };

                const marker = L.marker([37.0344, 27.4305], {
                    draggable: true
                }).addTo(map);

                map.on('click', function(e) {
                    const lat = e.latlng.lat;
                    const lng = e.latlng.lng;
                    marker.setLatLng([lat, lng]);
                    saveCoordinates(lat, lng);

                    // Step 2'deki haritayı güncelle (senkronize)
                    if (window.step2Map && window.step2Marker) {
                        window.step2Map.setView([lat, lng], map.getZoom());
                        window.step2Marker.setLatLng([lat, lng]);
                    }

                    if (window.tkgmWidgetInstance) {
                        window.tkgmWidgetInstance.fetchTKGMByCoordinates(lat, lng);
                    }

                    reverseGeocode(lat, lng, (address) => {
                        if (address) {
                            marker.bindPopup(address).openPopup();
                        } else {
                            marker.bindPopup(`Koordinat: ${lat.toFixed(6)}, ${lng.toFixed(6)}`)
                                .openPopup();
                        }
                    });
                });

                marker.on('dragend', function(e) {
                    const lat = e.target.getLatLng().lat;
                    const lng = e.target.getLatLng().lng;
                    saveCoordinates(lat, lng);

                    // Step 2'deki haritayı güncelle (senkronize)
                    if (window.step2Map && window.step2Marker) {
                        window.step2Map.setView([lat, lng], map.getZoom());
                        window.step2Marker.setLatLng([lat, lng]);
                    }

                    reverseGeocode(lat, lng, (address) => {
                        if (address) {
                            const adresField = document.getElementById('adres');
                            if (adresField) {
                                adresField.value = address;
                                adresField.classList.add('ring-2', 'ring-green-500');
                                setTimeout(() => adresField.classList.remove('ring-2',
                                    'ring-green-500'), 2000);
                            }
                        }
                    });
                });

                ['il_id', 'ilce_id', 'mahalle_id'].forEach(id => {
                    const select = document.getElementById(id);
                    if (select) {
                        select.addEventListener('change', () => updateWizardMapLocation(map, marker));
                    }
                });

                wizardMap = map;
                wizardMarker = marker;
                window.wizardMap = map;
                window.wizardMarker = marker;

                // Step 2'deki harita değişikliklerini dinle (çift yönlü senkronizasyon)
                if (window.step2Map) {
                    window.step2Map.on('move', function() {
                        if (map && !map._updating) {
                            map._updating = true;
                            const center = window.step2Map.getCenter();
                            map.setView([center.lat, center.lng], window.step2Map.getZoom());
                            setTimeout(() => {
                                map._updating = false;
                            }, 100);
                        }
                    });

                    window.step2Map.on('zoom', function() {
                        if (map && !map._updating) {
                            map._updating = true;
                            const center = window.step2Map.getCenter();
                            map.setView([center.lat, center.lng], window.step2Map.getZoom());
                            setTimeout(() => {
                                map._updating = false;
                            }, 100);
                        }
                    });

                    if (window.step2Marker) {
                        window.step2Marker.on('dragend', function(e) {
                            const pos = e.target.getLatLng();
                            marker.setLatLng([pos.lat, pos.lng]);
                            map.setView([pos.lat, pos.lng], map.getZoom());
                            saveCoordinates(pos.lat, pos.lng);
                        });
                    }
                }
            }

            function updateWizardMapLocation(map, marker) {
                const ilSelect = document.getElementById('il_id');
                const ilceSelect = document.getElementById('ilce_id');
                const mahalleSelect = document.getElementById('mahalle_id');

                if (!ilSelect?.value || !ilceSelect?.value || !map) return;

                const ilText = ilSelect.options[ilSelect.selectedIndex].text;
                const ilceText = ilceSelect.options[ilceSelect.selectedIndex].text;
                const mahalleText = mahalleSelect?.value ? mahalleSelect.options[mahalleSelect.selectedIndex].text : '';

                const searchQuery = `${ilText}, ${ilceText}${mahalleText ? ', ' + mahalleText : ''}, Türkiye`;
                const zoomLevel = mahalleText ? 14 : 12;

                const geocodeUrl = window.APIConfig?.geo?.geocode || '/api/geo/geocode';
                fetch(geocodeUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            query: searchQuery,
                            limit: 1
                        })
                    })
                    .then(res => res.json())
                    .then(result => {
                        const data = result.success ? result.data : result;
                        if (data?.length > 0) {
                            const lat = parseFloat(data[0].lat);
                            const lng = parseFloat(data[0].lon);
                            map.setView([lat, lng], zoomLevel);
                            if (marker) {
                                marker.setLatLng([lat, lng]);
                            } else {
                                L.marker([lat, lng], {
                                    draggable: true
                                }).addTo(map);
                            }
                        }
                    })
                    .catch(() => {});
            }

            window.switchMapView = function(viewType) {
                if (!window.wizardMap || !window.wizardMapLayers) return;

                const map = window.wizardMap;
                const layers = window.wizardMapLayers;
                const mapViewBtn = document.getElementById('map-view-btn');
                const satelliteViewBtn = document.getElementById('satellite-view-btn');

                map.removeLayer(layers.current === 'map' ? layers.map : layers.satellite);

                if (viewType === 'satellite') {
                    layers.satellite.addTo(map);
                    layers.current = 'satellite';
                    mapViewBtn?.classList.replace('bg-blue-600', 'bg-gray-200');
                    mapViewBtn?.classList.replace('text-white', 'text-gray-700');
                    satelliteViewBtn?.classList.replace('bg-gray-200', 'bg-blue-600');
                    satelliteViewBtn?.classList.replace('text-gray-700', 'text-white');
                } else {
                    layers.map.addTo(map);
                    layers.current = 'map';
                    satelliteViewBtn?.classList.replace('bg-blue-600', 'bg-gray-200');
                    satelliteViewBtn?.classList.replace('text-white', 'text-gray-700');
                    mapViewBtn?.classList.replace('bg-gray-200', 'bg-blue-600');
                    mapViewBtn?.classList.replace('text-gray-700', 'text-white');
                }
            };

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => setTimeout(initWizardMap, 500));
            } else {
                setTimeout(initWizardMap, 500);
            }
        })();
    </script>
@endpush

{{-- JavaScript Functions (Cascade Dropdowns) --}}
<script>
    // Load Alt Kategoriler
    function loadAltKategoriler(anaKategoriId) {
        const altKategoriSelect = document.getElementById('alt_kategori_id');
        const yayinTipiSelect = document.getElementById('yayin_tipi_id');

        if (!anaKategoriId) {
            altKategoriSelect.innerHTML = '<option value="">Önce Ana Kategori Seçin</option>';
            yayinTipiSelect.innerHTML = '<option value="">Önce Alt Kategori Seçin</option>';
            altKategoriSelect.disabled = true;
            yayinTipiSelect.disabled = true;
            return;
        }

        altKategoriSelect.disabled = true;
        altKategoriSelect.innerHTML = '<option value="">Yükleniyor...</option>';

        // ✅ Context7: Merkezi API config kullan
        const subcategoriesUrl = window.APIConfig?.categories?.subcategories ?
            window.APIConfig.categories.subcategories(anaKategoriId) :
            `/api/v1/categories/sub/${anaKategoriId}`;

        console.log('🔍 Alt kategori yükleme başlatıldı:', {
            anaKategoriId,
            url: subcategoriesUrl
        });

        fetch(subcategoriesUrl)
            .then(res => {
                if (!res.ok) {
                    throw new Error(`HTTP error! status: ${res.status}`);
                }
                return res.json();
            })
            .then(data => {
                console.log('📦 API Response:', data);
                altKategoriSelect.innerHTML = '<option value="">Alt Kategori Seçin</option>';

                // ✅ ResponseService format: {success: true, data: {subcategories: [...], count: ...}}
                // ✅ Backward compatibility: Eski formatları da destekle
                let categories = [];

                if (data.success && data.data) {
                    // ResponseService format
                    categories = data.data.subcategories || data.data.alt_kategoriler || data.data.data || [];
                    console.log('✅ ResponseService format algılandı, subcategories:', categories.length);
                } else if (data.subcategories) {
                    // Direkt subcategories format
                    categories = data.subcategories;
                    console.log('✅ Direkt subcategories format algılandı:', categories.length);
                } else if (data.data && Array.isArray(data.data)) {
                    // Array format
                    categories = data.data;
                    console.log('✅ Array format algılandı:', categories.length);
                } else if (Array.isArray(data)) {
                    // Direkt array format
                    categories = data;
                    console.log('✅ Direkt array format algılandı:', categories.length);
                }

                if (Array.isArray(categories) && categories.length > 0) {
                    console.log('✅ Alt kategoriler yüklendi:', categories.length, 'adet');
                    categories.forEach(kategori => {
                        const option = document.createElement('option');
                        option.value = kategori.id;
                        option.textContent = kategori.name;
                        // Slug bilgisini data attribute olarak ekle (Step 2 için)
                        if (kategori.slug) {
                            option.setAttribute('data-slug', kategori.slug);
                        }
                        altKategoriSelect.appendChild(option);
                    });
                    altKategoriSelect.disabled = false;
                } else {
                    altKategoriSelect.innerHTML = '<option value="">Alt kategori bulunamadı</option>';
                    console.warn('⚠️ Alt kategoriler boş:', data);
                }
            })
            .catch(error => {
                console.error('Alt kategoriler yüklenemedi:', error);
                altKategoriSelect.innerHTML = '<option value="">Hata oluştu</option>';
            });
    }

    // Load Yayın Tipleri
    function loadYayinTipleri(altKategoriId) {
        const yayinTipiSelect = document.getElementById('yayin_tipi_id');

        if (!altKategoriId) {
            yayinTipiSelect.innerHTML = '<option value="">Önce Alt Kategori Seçin</option>';
            yayinTipiSelect.disabled = true;
            return;
        }

        yayinTipiSelect.disabled = true;
        yayinTipiSelect.innerHTML = '<option value="">Yükleniyor...</option>';

        // ✅ Context7: Merkezi API config kullan
        const publicationTypesUrl = window.APIConfig?.categories?.publicationTypes ?
            window.APIConfig.categories.publicationTypes(altKategoriId) :
            `/api/v1/categories/publication-types/${altKategoriId}`;

        fetch(publicationTypesUrl)
            .then(res => res.json())
            .then(data => {
                yayinTipiSelect.innerHTML = '<option value="">Yayın Tipi Seçin</option>';

                // Response format: {success: true, types: [...]} veya {success: true, data: {types: [...]}}
                const types = data.types || data.data?.types || data.data || (Array.isArray(data) ? data : []);

                if (Array.isArray(types) && types.length > 0) {
                    types.forEach(tip => {
                        const option = document.createElement('option');
                        option.value = tip.id;
                        option.textContent = tip.name || tip.yayin_tipi;
                        yayinTipiSelect.appendChild(option);
                    });
                    yayinTipiSelect.disabled = false;
                } else {
                    yayinTipiSelect.innerHTML = '<option value="">Yayın tipi bulunamadı</option>';
                    console.warn('Yayın tipleri boş:', data);
                }
            })
            .catch(error => {
                console.error('Yayın tipleri yüklenemedi:', error);
                yayinTipiSelect.innerHTML = '<option value="">Hata oluştu</option>';
            });
    }

    // Load İlçeler
    function loadIlceler(ilId) {
        const ilceSelect = document.getElementById('ilce_id');
        const mahalleSelect = document.getElementById('mahalle_id');

        if (!ilId) {
            ilceSelect.innerHTML = '<option value="">Önce İl Seçin</option>';
            mahalleSelect.innerHTML = '<option value="">Önce İlçe Seçin</option>';
            ilceSelect.disabled = true;
            mahalleSelect.disabled = true;
            return;
        }

        ilceSelect.disabled = true;
        ilceSelect.innerHTML = '<option value="">Yükleniyor...</option>';

        // ✅ Context7: Merkezi API config kullan
        const districtsUrl = window.APIConfig?.location?.districts ?
            window.APIConfig.location.districts(ilId) :
            `/api/location/districts/${ilId}`;

        fetch(districtsUrl)
            .then(res => {
                if (!res.ok) {
                    throw new Error(`HTTP ${res.status}`);
                }
                return res.json();
            })
            .then(data => {
                ilceSelect.innerHTML = '<option value="">İlçe Seçin</option>';
                // ResponseService format: {success: true, data: [...]}
                const districts = data.data || data.districts || data.ilceler || (Array.isArray(data) ? data : []);
                if (data.success === false) {
                    ilceSelect.innerHTML = '<option value="">' + (data.message || 'İlçe bulunamadı') + '</option>';
                    console.warn('İlçeler API hatası:', data.message);
                    return;
                }

                if (Array.isArray(districts) && districts.length > 0) {
                    districts.forEach(ilce => {
                        const option = document.createElement('option');
                        option.value = ilce.id;
                        option.textContent = ilce.ilce_adi || ilce.name || ilce.district_name;
                        ilceSelect.appendChild(option);
                    });
                    ilceSelect.disabled = false;
                } else {
                    ilceSelect.innerHTML = '<option value="">İlçe bulunamadı</option>';
                }
            })
            .catch(error => {
                console.error('İlçeler yüklenemedi:', error);
                ilceSelect.innerHTML = '<option value="">Hata oluştu</option>';
            });
    }

    // Load Mahalleler
    function loadMahalleler(ilceId) {
        const mahalleSelect = document.getElementById('mahalle_id');

        if (!ilceId) {
            mahalleSelect.innerHTML = '<option value="">Önce İlçe Seçin</option>';
            mahalleSelect.disabled = true;
            return;
        }

        mahalleSelect.disabled = true;
        mahalleSelect.innerHTML = '<option value="">Yükleniyor...</option>';

        // ✅ Context7: Merkezi API config kullan
        const neighborhoodsUrl = window.APIConfig?.location?.neighborhoods ?
            window.APIConfig.location.neighborhoods(ilceId) :
            `/api/location/neighborhoods/${ilceId}`;

        fetch(neighborhoodsUrl)
            .then(res => {
                if (!res.ok) {
                    throw new Error(`HTTP ${res.status}`);
                }
                return res.json();
            })
            .then(data => {
                mahalleSelect.innerHTML = '<option value="">Mahalle Seçin</option>';

                if (data.success === false) {
                    mahalleSelect.innerHTML = '<option value="">' + (data.message || 'Mahalle bulunamadı') +
                        '</option>';
                    console.warn('Mahalleler API hatası:', data.message);
                    return;
                }

                // ResponseService format: {success: true, data: [...]}
                const neighborhoods = data.data || data.neighborhoods || data.mahalleler || (Array.isArray(data) ?
                    data : []);
                if (Array.isArray(neighborhoods) && neighborhoods.length > 0) {
                    neighborhoods.forEach(mahalle => {
                        const option = document.createElement('option');
                        option.value = mahalle.id;
                        option.textContent = mahalle.mahalle_adi || mahalle.name || mahalle
                            .neighborhood_name;
                        // ✅ Koordinat bilgisini data attribute olarak sakla
                        if (mahalle.lat && mahalle.lng) {
                            option.setAttribute('data-lat', mahalle.lat);
                            option.setAttribute('data-lng', mahalle.lng);
                        }
                        mahalleSelect.appendChild(option);
                    });
                    mahalleSelect.disabled = false;
                } else {
                    mahalleSelect.innerHTML = '<option value="">Mahalle bulunamadı</option>';
                }
            })
            .catch(error => {
                console.error('Mahalleler yüklenemedi:', error);
                mahalleSelect.innerHTML = '<option value="">Hata oluştu</option>';
            });
    }

    // ✅ Mahalle seçildiğinde marker'ı güncelle
    async function updateMarkerFromMahalle(mahalleId, selectedOption) {
        if (!mahalleId || !selectedOption) {
            return;
        }

        // Önce data attribute'lardan koordinatları kontrol et
        let lat = selectedOption.getAttribute('data-lat');
        let lng = selectedOption.getAttribute('data-lng');

        // Eğer data attribute'da yoksa API'den çek
        if (!lat || !lng) {
            try {
                const response = await fetch(`/api/v1/location/neighborhood/${mahalleId}/coordinates`);
                const result = await response.json();

                if (result.success && result.data) {
                    lat = result.data.lat;
                    lng = result.data.lng;
                    console.log(`✅ Mahalle koordinatları API'den alındı: ${lat}, ${lng}`);
                } else {
                    console.warn('⚠️ Mahalle koordinatları bulunamadı');
                    return;
                }
            } catch (error) {
                console.error('❌ Mahalle koordinatları yüklenemedi:', error);
                return;
            }
        }

        // Koordinatları float'a çevir
        lat = parseFloat(lat);
        lng = parseFloat(lng);

        if (isNaN(lat) || isNaN(lng)) {
            console.warn('⚠️ Geçersiz koordinatlar:', lat, lng);
            return;
        }

        // Marker'ı güncelle
        if (window.wizardMap && window.wizardMarker) {
            window.wizardMarker.setLatLng([lat, lng]);
            window.wizardMap.setView([lat, lng], 16, {
                animate: true,
                duration: 0.5
            });

            // Koordinatları form alanlarına kaydet
            saveCoordinates(lat, lng);

            // Popup güncelle
            const mahalleName = selectedOption.textContent;
            window.wizardMarker.bindPopup(
                `📍 ${mahalleName}<br><small>Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}</small>`)
            .openPopup();

            console.log(`✅ Marker güncellendi: ${mahalleName} (${lat}, ${lng})`);
        } else {
            console.warn('⚠️ Harita veya marker bulunamadı');
        }
    }

    // AI Title Generation
    async function generateTitleWithAI() {
        const anaKategoriId = document.getElementById('ana_kategori_id')?.value;
        const altKategoriId = document.getElementById('alt_kategori_id')?.value;
        const ilId = document.getElementById('il_id')?.value;
        const ilceId = document.getElementById('ilce_id')?.value;
        const mahalleId = document.getElementById('mahalle_id')?.value;
        const yayinTipiId = document.getElementById('yayin_tipi_id')?.value;

        const anaKategori = document.getElementById('ana_kategori_id')?.selectedOptions[0]?.text;
        const altKategori = document.getElementById('alt_kategori_id')?.selectedOptions[0]?.text;
        const il = document.getElementById('il_id')?.selectedOptions[0]?.text;
        const ilce = document.getElementById('ilce_id')?.selectedOptions[0]?.text;
        const mahalle = mahalleId ? document.getElementById('mahalle_id')?.selectedOptions[0]?.text : '';
        const yayinTipi = yayinTipiId ? document.getElementById('yayin_tipi_id')?.selectedOptions[0]?.text : '';

        if (!anaKategoriId || !altKategoriId || !ilId || !ilceId) {
            alert('Lütfen önce kategori ve lokasyon bilgilerini seçin');
            return;
        }

        const baslikInput = document.getElementById('baslik');
        if (!baslikInput) return;

        // Loading state
        const originalValue = baslikInput.value;
        baslikInput.disabled = true;
        baslikInput.value = 'AI başlık üretiliyor...';
        baslikInput.classList.add('opacity-50');

        try {
            // ✅ Context7: Merkezi API config kullan
            const generateTitleUrl = window.APIConfig?.admin?.generateAiTitle ||
                '/admin/ilanlar/generate-ai-title';

            const response = await fetch(generateTitleUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    context: {
                        kategori: altKategori,
                        il: il,
                        ilce: ilce,
                        mahalle: mahalle, // ✅ FIX: Mahalle bilgisi eklendi
                        anaKategori: anaKategori,
                        yayinTipi: yayinTipi, // ✅ FIX: Yayın tipi eklendi (Satılık/Kiralık)
                    },
                    kategori_id: altKategoriId,
                    il_id: ilId,
                    ilce_id: ilceId,
                    mahalle_id: mahalleId, // ✅ FIX: Mahalle ID eklendi
                    yayin_tipi: yayinTipi, // ✅ FIX: Yayın tipi eklendi
                }),
            });

            const result = await response.json();

            // ✅ FIX: 500 hatası alsa bile fallback başlıkları kullan
            if (result.success && result.title) {
                baslikInput.value = result.title;
                baslikInput.classList.remove('opacity-50');
                baslikInput.classList.add('border-green-500');

                // Success animation
                setTimeout(() => {
                    baslikInput.classList.remove('border-green-500');
                }, 2000);

                // Show alternatives if available
                if (result.alternatives && result.alternatives.length > 0) {
                    console.log('Alternatif başlıklar:', result.alternatives);
                }
            } else if (result.title && result.title !== 'Başlık üretilemedi') {
                // ✅ FIX: Fallback başlık varsa kullan (Ollama timeout olsa bile)
                baslikInput.value = result.title;
                baslikInput.classList.remove('opacity-50');
                baslikInput.classList.add('border-yellow-500');

                // Warning animation (fallback kullanıldı)
                setTimeout(() => {
                    baslikInput.classList.remove('border-yellow-500');
                }, 2000);

                if (result.alternatives && result.alternatives.length > 0) {
                    console.log('Alternatif başlıklar (fallback):', result.alternatives);
                }
            } else {
                baslikInput.value = originalValue;
                alert('Başlık üretilemedi: ' + (result.message || 'Bilinmeyen hata'));
            }
        } catch (error) {
            console.error('AI başlık üretimi hatası:', error);
            baslikInput.value = originalValue;
            alert('AI başlık üretimi sırasında hata oluştu');
        } finally {
            baslikInput.disabled = false;
            baslikInput.classList.remove('opacity-50');
        }
    }

    // Map Picker
    function openMapPicker() {
        // TODO: Harita modal aç
        console.log('Harita seçici açılıyor...');
    }

    // ✅ Fiyat Formatlama ve Yazıya Çevirme
    function formatPriceInput(input) {
        // Sadece rakamları al
        let value = input.value.replace(/[^\d]/g, '');

        // Binlik ayırıcı ekle
        if (value) {
            value = parseInt(value).toLocaleString('tr-TR');
        }

        // Input'a formatlanmış değeri yaz
        input.value = value;

        // Raw değeri hidden input'a kaydet
        const rawValue = value.replace(/\./g, '');
        const fiyatRawInput = document.getElementById('fiyat_raw');
        if (fiyatRawInput) {
            fiyatRawInput.value = rawValue;
        }

        // Yazıyla gösterimi güncelle
        updatePriceText();
    }

    async function updatePriceText() {
        const fiyatInput = document.getElementById('fiyat');
        const fiyatRaw = document.getElementById('fiyat_raw');
        const paraBirimi = document.getElementById('para_birimi')?.value || 'TRY';
        const priceTextDisplay = document.getElementById('price_text_display');
        const priceTextValue = document.getElementById('price_text_value');

        if (!fiyatInput || !priceTextDisplay || !priceTextValue) return;

        const rawValue = fiyatRaw?.value || fiyatInput.value.replace(/\./g, '');

        if (!rawValue || parseFloat(rawValue) <= 0) {
            priceTextValue.textContent = 'Fiyat giriniz';
            priceTextDisplay.classList.remove('bg-green-50', 'dark:bg-green-900/20', 'border-green-200',
                'dark:border-green-800');
            priceTextDisplay.classList.add('bg-blue-50', 'dark:bg-blue-900/20', 'border-blue-200',
                'dark:border-blue-800');
            return;
        }

        try {
            // Backend'e yazıya çevirme isteği gönder
            // ✅ Context7: Merkezi API config kullan
            const convertPriceUrl = window.APIConfig?.admin?.convertPriceToText ||
                '/admin/ilanlar/convert-price-to-text';
            const response = await fetch(convertPriceUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    price: parseFloat(rawValue),
                    currency: paraBirimi,
                }),
            });

            const result = await response.json();

            if (result.success && result.price_text) {
                priceTextValue.textContent = result.price_text;
                priceTextDisplay.classList.remove('bg-blue-50', 'dark:bg-blue-900/20', 'border-blue-200',
                    'dark:border-blue-800');
                priceTextDisplay.classList.add('bg-green-50', 'dark:bg-green-900/20', 'border-green-200',
                    'dark:border-green-800');
            } else {
                priceTextValue.textContent = 'Yazıya çevrilemedi';
            }
        } catch (error) {
            console.error('Fiyat yazıya çevirme hatası:', error);
            // Fallback: Client-side basit çeviri
            if (priceTextValue) {
                priceTextValue.textContent = formatPriceToTextSimple(parseFloat(rawValue), paraBirimi);
            }
        }
    }

    // Basit client-side fiyat yazıya çevirme (fallback)
    function formatPriceToTextSimple(price, currency) {
        const currencyNames = {
            'TRY': 'Türk Lirası',
            'USD': 'Amerikan Doları',
            'EUR': 'Euro',
            'GBP': 'İngiliz Sterlini',
        };

        if (price >= 1000000) {
            const milyon = Math.floor(price / 1000000);
            const kalan = price % 1000000;
            if (kalan > 0) {
                return `${milyon} Milyon ${kalan.toLocaleString('tr-TR')} ${currencyNames[currency] || 'Türk Lirası'}`;
            }
            return `${milyon} Milyon ${currencyNames[currency] || 'Türk Lirası'}`;
        } else if (price >= 1000) {
            const bin = Math.floor(price / 1000);
            const kalan = price % 1000;
            if (kalan > 0) {
                return `${bin} Bin ${kalan.toLocaleString('tr-TR')} ${currencyNames[currency] || 'Türk Lirası'}`;
            }
            return `${bin} Bin ${currencyNames[currency] || 'Türk Lirası'}`;
        }

        return `${price.toLocaleString('tr-TR')} ${currencyNames[currency] || 'Türk Lirası'}`;
    }

    // ✅ POI Widget - Context7 Standard
    window.poiWidget = function() {
        return {
            pois: [],
            selectedPOIs: [],
            selectedCategories: [],
            loading: false,
            error: null,
            availableCategories: [{
                    type: 'okul',
                    label: '🏫 Eğitim',
                    icon: 'school'
                },
                {
                    type: 'market',
                    label: '🛒 Alışveriş',
                    icon: 'shopping-cart'
                },
                {
                    type: 'hastane',
                    label: '🏥 Sağlık',
                    icon: 'hospital'
                },
                {
                    type: 'otel',
                    label: '🏨 Konaklama',
                    icon: 'hotel'
                },
                {
                    type: 'sahil',
                    label: '🏖️ Sahil & Deniz',
                    icon: 'beach'
                },
                {
                    type: 'park',
                    label: '🌳 Park & Yeşil Alan',
                    icon: 'tree'
                },
                {
                    type: 'ulasim',
                    label: '🚌 Ulaşım',
                    icon: 'bus'
                },
            ],

            get filteredPOIs() {
                if (this.selectedCategories.length === 0) {
                    return this.pois;
                }
                return this.pois.filter(poi => this.selectedCategories.includes(poi.type));
            },

            getCategoryCount(type) {
                return this.pois.filter(poi => poi.type === type).length;
            },

            toggleCategory(type) {
                const index = this.selectedCategories.indexOf(type);
                if (index > -1) {
                    this.selectedCategories.splice(index, 1);
                } else {
                    this.selectedCategories.push(type);
                }
            },

            init() {
                // Haritada konum seçildiğinde POI'leri otomatik yükle
                document.addEventListener('wizard-map-marker-moved', (e) => {
                    if (e.detail && e.detail.lat && e.detail.lng) {
                        setTimeout(() => this.loadPOIs(e.detail.lat, e.detail.lng), 500);
                    }
                });

                // Form submit'te seçili POI'leri kaydet
                const form = document.getElementById('ilan-wizard-form');
                if (form) {
                    form.addEventListener('submit', () => {
                        this.updatePOIInput();
                    });
                }
            },

            async loadPOIs(lat = null, lng = null) {
                // Koordinatları haritadan veya form alanlarından al
                if (!lat || !lng) {
                    const latInput = document.querySelector('[name="enlem"]') || document.querySelector(
                        '[name="latitude"]');
                    const lngInput = document.querySelector('[name="boylam"]') || document.querySelector(
                        '[name="longitude"]');

                    if (!latInput || !lngInput || !latInput.value || !lngInput.value) {
                        this.error = 'Önce haritada konum seçin';
                        return;
                    }
                    lat = parseFloat(latInput.value);
                    lng = parseFloat(lngInput.value);
                }

                this.loading = true;
                this.error = null;

                try {
                    // Kategori filtresi varsa types parametresine ekle
                    let typesParam = '';
                    if (this.selectedCategories.length > 0) {
                        typesParam = '&types=' + this.selectedCategories.join(',');
                    }

                    const url = window.APIConfig?.environment?.pois ?
                        window.APIConfig.environment.pois(lat, lng, 2000, this.selectedCategories.length > 0 ?
                            this.selectedCategories : null) :
                        `/api/environment/pois?lat=${lat}&lng=${lng}&radius=2000${typesParam}`;

                    const response = await fetch(url, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }

                    const data = await response.json();

                    if (data.success && data.data && Array.isArray(data.data.pois)) {
                        this.pois = data.data.pois;
                        this.updateMapMarkers();
                    } else {
                        throw new Error('Geçersiz API yanıtı');
                    }
                } catch (error) {
                    console.error('POI yükleme hatası:', error);
                    this.error = 'Yakın lokasyonlar yüklenirken hata oluştu: ' + error.message;
                    this.pois = [];
                } finally {
                    this.loading = false;
                }
            },

            updateMapMarkers() {
                // Haritada POI marker'larını göster (wizard haritası varsa)
                if (window.wizardMap && this.pois.length > 0) {
                    // Mevcut POI marker'larını temizle
                    if (window.poiMarkers) {
                        window.poiMarkers.forEach(marker => window.wizardMap.removeLayer(marker));
                    }
                    window.poiMarkers = [];

                    // Yeni marker'ları ekle
                    this.pois.forEach(poi => {
                        if (window.L) {
                            const marker = window.L.marker([poi.lat, poi.lng], {
                                icon: window.L.divIcon({
                                    className: 'poi-marker',
                                    html: `<div class="poi-marker-content" style="background: ${this.selectedPOIs.includes(poi.id) ? '#9333ea' : '#6b7280'}; color: white; padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: bold; white-space: nowrap;">${poi.name}</div>`,
                                    iconSize: [100, 20],
                                })
                            }).addTo(window.wizardMap);

                            marker.bindPopup(`
                                <div class="text-sm">
                                    <strong>${poi.name}</strong><br>
                                    <span class="text-gray-600">${poi.category}</span><br>
                                    <span class="text-purple-600">${poi.distance_m} m (${poi.walking_minutes} dk yürüyüş)</span>
                                </div>
                            `);

                            window.poiMarkers.push(marker);
                        }
                    });
                }
            },

            updatePOIInput() {
                // Seçili POI'leri JSON olarak hidden input'a kaydet
                const selectedData = this.pois
                    .filter(poi => this.selectedPOIs.includes(poi.id))
                    .map(poi => ({
                        id: poi.id,
                        name: poi.name,
                        type: poi.type,
                        category: poi.category,
                        lat: poi.lat,
                        lng: poi.lng,
                        distance_m: poi.distance_m,
                        walking_minutes: poi.walking_minutes,
                    }));

                const input = document.getElementById('environment_pois');
                if (input) {
                    input.value = JSON.stringify(selectedData);
                }

                // Harita marker'larını güncelle
                this.updateMapMarkers();
            },

            get selectedPOIData() {
                return this.pois
                    .filter(poi => this.selectedPOIs.includes(poi.id))
                    .map(poi => ({
                        id: poi.id,
                        name: poi.name,
                        type: poi.type,
                        category: poi.category,
                        lat: poi.lat,
                        lng: poi.lng,
                        distance_m: poi.distance_m,
                        walking_minutes: poi.walking_minutes,
                    }));
            },
        };
    };

    // ✅ GeoJSON Uploader Widget - Step 1
    window.geojsonUploader = function() {
        return {
            error: null,
            success: false,

            init() {
                // Step 2'ye geçildiğinde GeoJSON verisini aktar
                document.addEventListener('wizard-step-changed', (e) => {
                    if (e.detail.step === 2) {
                        this.transferDataToStep2();
                    }
                });
            },

            handleGeoJsonUpload(event) {
                const file = event.target.files?.[0];
                if (!file) return;

                if (file.size > 2 * 1024 * 1024) {
                    this.error = 'Dosya boyutu 2MB\'den küçük olmalıdır';
                    event.target.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = (e) => {
                    try {
                        const text = e.target.result;
                        const data = JSON.parse(text);
                        this.applyGeoJsonData(data);
                        this.error = null;
                        this.success = true;
                        setTimeout(() => this.success = false, 5000);
                    } catch (err) {
                        this.error = 'Geçerli bir JSON / GeoJSON dosyası yükleyin: ' + err.message;
                        this.success = false;
                    }
                };

                reader.readAsText(file);
            },

            applyGeoJsonData(data) {
                let feature = null;

                if (data?.type === 'FeatureCollection' && Array.isArray(data.features) && data.features.length) {
                    feature = data.features[0];
                } else if (data?.type === 'Feature') {
                    feature = data;
                }

                if (!feature || !feature.geometry) {
                    this.error = 'GeoJSON içinde geçerli bir Feature bulunamadı';
                    return;
                }

                const props = feature.properties || {};

                // İl/İlçe/Mahalle alanlarını doldur
                this.fillLocationFields(props);

                // Koordinatları kaydet ve haritayı güncelle
                this.updateMapFromGeoJSON(feature);

                // GeoJSON verisini global'e kaydet (Step 2'de kullanılacak)
                window.uploadedGeoJsonData = {
                    feature: feature,
                    properties: props,
                    timestamp: Date.now()
                };
            },

            fillLocationFields(props) {
                // İl seçimi (Il veya Ilce varsa)
                if (props.Il || props.il) {
                    const ilAdi = props.Il || props.il;
                    const ilSelect = document.getElementById('il_id');
                    if (ilSelect) {
                        for (let option of ilSelect.options) {
                            if (option.text.trim() === ilAdi.trim()) {
                                ilSelect.value = option.value;
                                ilSelect.dispatchEvent(new Event('change'));
                                break;
                            }
                        }
                    }
                }

                // İlçe seçimi (Ilce varsa)
                if (props.Ilce || props.ilce) {
                    setTimeout(() => {
                        const ilceAdi = props.Ilce || props.ilce;
                        const ilceSelect = document.getElementById('ilce_id');
                        if (ilceSelect && !ilceSelect.disabled) {
                            for (let option of ilceSelect.options) {
                                if (option.text.trim() === ilceAdi.trim()) {
                                    ilceSelect.value = option.value;
                                    ilceSelect.dispatchEvent(new Event('change'));
                                    break;
                                }
                            }
                        }
                    }, 500);
                }

                // Mahalle seçimi (Mahalle varsa)
                if (props.Mahalle || props.mahalle) {
                    setTimeout(() => {
                        const mahalleAdi = props.Mahalle || props.mahalle;
                        const mahalleSelect = document.getElementById('mahalle_id');
                        if (mahalleSelect && !mahalleSelect.disabled) {
                            for (let option of mahalleSelect.options) {
                                if (option.text.trim() === mahalleAdi.trim()) {
                                    mahalleSelect.value = option.value;
                                    break;
                                }
                            }
                        }
                    }, 1000);
                }
            },

            updateMapFromGeoJSON(feature) {
                if (!window.L || !window.wizardMap) {
                    console.warn('Harita henüz yüklenmedi, bekleniyor...');
                    setTimeout(() => this.updateMapFromGeoJSON(feature), 500);
                    return;
                }

                try {
                    // Mevcut GeoJSON layer'ı temizle
                    if (window.step1GeoJsonLayer) {
                        window.wizardMap.removeLayer(window.step1GeoJsonLayer);
                    }

                    // Yeni layer ekle
                    const layer = window.L.geoJSON(feature, {
                        style: {
                            color: '#2563eb',
                            weight: 3,
                            fillColor: '#3b82f6',
                            fillOpacity: 0.25,
                        },
                    }).addTo(window.wizardMap);

                    window.step1GeoJsonLayer = layer;

                    // Haritayı parsel sınırlarına göre ayarla
                    const bounds = layer.getBounds();
                    if (bounds.isValid && bounds.isValid()) {
                        window.wizardMap.fitBounds(bounds, {
                            padding: [50, 50]
                        });

                        const center = bounds.getCenter();

                        // Koordinatları form alanlarına kaydet
                        const latInput = document.querySelector('[name="enlem"]') || document.querySelector(
                            '[name="latitude"]');
                        const lngInput = document.querySelector('[name="boylam"]') || document.querySelector(
                            '[name="longitude"]');
                        if (latInput) latInput.value = center.lat.toFixed(6);
                        if (lngInput) lngInput.value = center.lng.toFixed(6);

                        // Marker'ı güncelle
                        if (window.wizardMarker) {
                            window.wizardMarker.setLatLng([center.lat, center.lng]);
                        } else {
                            window.wizardMarker = window.L.marker([center.lat, center.lng], {
                                draggable: true
                            }).addTo(window.wizardMap);
                        }

                        // POI'leri otomatik yükle
                        setTimeout(() => {
                            if (window.poiWidget && typeof window.poiWidget === 'function') {
                                const poiInstance = Alpine.$data(document.querySelector(
                                    '[x-data*="poiWidget"]'));
                                if (poiInstance && poiInstance.loadPOIs) {
                                    poiInstance.loadPOIs(center.lat, center.lng);
                                }
                            }
                        }, 1000);
                    }
                } catch (e) {
                    console.error('GeoJSON haritaya eklenemedi:', e);
                    this.error = 'Harita güncellenirken hata oluştu: ' + e.message;
                }
            },

            transferDataToStep2() {
                // Step 2'ye geçildiğinde GeoJSON verisini aktar
                if (window.uploadedGeoJsonData && window.uploadedGeoJsonData.feature) {
                    // Step 2'deki TKGM widget'a veri aktar
                    document.dispatchEvent(new CustomEvent('step1-geojson-ready', {
                        detail: window.uploadedGeoJsonData
                    }));
                }
            },
        };
    };
</script>
