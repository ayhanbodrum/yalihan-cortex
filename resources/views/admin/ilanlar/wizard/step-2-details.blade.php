{{-- STEP 2: DETAYLAR (Kategoriye Özel) --}}
<div class="space-y-6">
    {{-- Büyük Harita (Üstte - Step 1 ile Senkronize) --}}
    <div
        class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-lg overflow-hidden">
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
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Harita (Step 1 ile Senkronize)</h3>
                        <p class="text-xs text-gray-600 dark:text-gray-400">Konum seçimi Step 1'deki harita ile
                            senkronize</p>
                    </div>
                </div>
                <div id="step2-map-layer-control" class="flex items-center gap-2">
                    <button type="button" id="step2-map-view-btn" onclick="switchStep2MapView('map')"
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-500 transition-all duration-200">
                        🗺️ Harita
                    </button>
                    <button type="button" id="step2-satellite-view-btn" onclick="switchStep2MapView('satellite')"
                        class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-blue-500 transition-all duration-200 shadow-md">
                        🛰️ Uydu
                    </button>
                </div>
            </div>
        </div>
        <div id="step2-map-container" class="relative" style="height: 700px;">
            <div id="step2-map" class="w-full h-full" style="height: 700px;">
                <div class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 z-10"
                    id="step2-map-loading" role="status" aria-live="polite" aria-busy="true">
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

    <div class="mb-6">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">🔍 Detaylar</h3>
        <p class="text-sm text-gray-600 dark:text-gray-400">Kategoriye özel detay bilgilerini doldurun</p>
    </div>

    {{-- Kategori Kontrolü (Arsa mı, Konut mu?) --}}
    <div x-data="{
        categoryType: '',
        init() {
            // İlk kontrol (biraz gecikme ile - DOM hazır olmalı)
            setTimeout(() => this.checkCategory(), 300);
    
            // Alt kategori select'i izle
            const altKategoriSelect = document.getElementById('alt_kategori_id');
            if (altKategoriSelect) {
                altKategoriSelect.addEventListener('change', () => {
                    setTimeout(() => this.checkCategory(), 100);
                });
            }
    
            // Wizard step değişikliğini dinle
            const stepChangeHandler = (e) => {
                if (e.detail && e.detail.step === 2) {
                    setTimeout(() => this.checkCategory(), 300);
                }
            };
            document.addEventListener('wizard-step-changed', stepChangeHandler);
        },
        checkCategory() {
            const altKategoriSelect = document.getElementById('alt_kategori_id');
            if (!altKategoriSelect) {
                this.categoryType = '';
                return;
            }
    
            const selectedOption = altKategoriSelect.options[altKategoriSelect.selectedIndex];
            if (!selectedOption || !selectedOption.value) {
                this.categoryType = '';
                return;
            }
    
            const categoryName = selectedOption.text.toLowerCase().trim();
            const categorySlug = selectedOption.getAttribute('data-slug') || '';
    
            const arsaKeywords = ['arsa', 'tarla', 'arazi', 'land', 'parsel', 'ada'];
            const isArsa = arsaKeywords.some(keyword =>
                categoryName.includes(keyword) || categorySlug.includes(keyword)
            );
    
            if (isArsa) {
                this.categoryType = 'arsa';
            }
            // Konut kontrolü
            else {
                const konutKeywords = ['konut', 'daire', 'villa', 'residential', 'apartment', 'house'];
                const isKonut = konutKeywords.some(keyword =>
                    categoryName.includes(keyword) || categorySlug.includes(keyword)
                );
    
                if (isKonut) {
                    this.categoryType = 'konut';
                    console.log('✅ Step 2: Konut kategorisi algılandı');
                } else {
                    this.categoryType = '';
                    console.log('⚠️ Step 2: Bilinmeyen kategori:', categoryName);
                }
            }
        }
    }">
        <div x-show="categoryType === 'arsa'" x-transition class="space-y-4">
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-lg p-6 space-y-6">
                <div class="flex items-center justify-between gap-3 mb-2">
                    <div>
                        <h4 class="text-base font-semibold text-gray-900 dark:text-white">Arsa Detayları</h4>
                        <p class="text-xs text-gray-600 dark:text-gray-400">
                            TKGM'den gelen verilerle birlikte parsel ve imar bilgilerini kontrol edin.
                        </p>
                    </div>
                </div>

                <div class="mb-4">
                    @include('admin.ilanlar.wizard.components.tkgm-widget')
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div>
                        <label for="alan_m2" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Alan (m²) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="alan_m2" id="alan_m2" required step="0.01" placeholder="5000.9"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg
                                      bg-white dark:bg-gray-800 text-black dark:text-white
                                      focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                      transition-all duration-200">
                    </div>

                    <div>
                        <label for="imar_statusu" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            İmar Durumu <span class="text-red-500">*</span>
                        </label>
                        <select name="imar_statusu" id="imar_statusu" required
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg
                                      bg-white dark:bg-gray-800 text-black dark:text-white
                                      focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                      transition-all duration-200">
                            <option value="">Seçin</option>
                            <option value="imarlı">İmarlı</option>
                            <option value="imar_dışı">İmar Dışı</option>
                            <option value="imar_uygulama">İmar Uygulama</option>
                            <option value="tapu_tahsis">Tapu Tahsis</option>
                        </select>
                    </div>

                    <div>
                        <label for="kaks" class="block text-sm font-medium text-gray-900 dark:text-white mb-1">
                            KAKS (Kat Alanı Katsayısı)
                        </label>
                        <input type="number" name="kaks" id="kaks" step="0.01" placeholder="0.3"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg
                                      bg-white dark:bg-gray-800 text-black dark:text-white
                                      focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                      transition-all duration-200">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Opsiyonel alan</p>
                    </div>

                    <div>
                        <label for="taks" class="block text-sm font-medium text-gray-900 dark:text-white mb-1">
                            TAKS (Taban Alanı Katsayısı)
                        </label>
                        <input type="number" name="taks" id="taks" step="0.01" placeholder="0.25"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg
                                      bg-white dark:bg-gray-800 text-black dark:text-white
                                      focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                      transition-all duration-200">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Opsiyonel alan</p>
                    </div>

                    <div>
                        <label for="gabari" class="block text-sm font-medium text-gray-900 dark:text-white mb-1">
                            Gabari (m)
                        </label>
                        <input type="number" name="gabari" id="gabari" step="0.1" placeholder="7.5"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg
                                      bg-white dark:bg-gray-800 text-black dark:text-white
                                      focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                      transition-all duration-200">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Opsiyonel alan</p>
                    </div>

                    <div>
                        <label for="ada_no" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Ada No
                        </label>
                        <input type="text" name="ada_no" id="ada_no" placeholder="701"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg
                                      bg-white dark:bg-gray-800 text-black dark:text-white
                                      focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                      transition-all duration-200">
                    </div>

                    <div>
                        <label for="parsel_no" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Parsel No
                        </label>
                        <input type="text" name="parsel_no" id="parsel_no" placeholder="35"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg
                                      bg-white dark:bg-gray-800 text-black dark:text-white
                                      focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                      transition-all duration-200">
                    </div>
                </div>

                <input type="hidden" name="enlem" id="enlem">
                <input type="hidden" name="boylam" id="boylam">

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Altyapı
                        </label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="altyapi_elektrik" value="1"
                                    class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Elektrik</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="altyapi_su" value="1"
                                    class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Su</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="altyapi_dogalgaz" value="1"
                                    class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Doğalgaz</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Yola Cephe
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="yola_cephe" value="1"
                                class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Yola Cephe Var</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="categoryType === 'konut' || categoryType === 'daire' || categoryType === 'villa' || categoryType.includes('konut')"
            x-transition class="space-y-4">
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-lg p-6 space-y-6">
                <div class="flex items-center justify-between gap-3 mb-2">
                    <div>
                        <h4 class="text-base font-semibold text-gray-900 dark:text-white">Konut Detayları</h4>
                        <p class="text-xs text-gray-600 dark:text-gray-400">
                            Oda yapısı, metrekare ve bina bilgilerini eksiksiz doldurun.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div>
                        <label for="oda_sayisi" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Oda Sayısı <span class="text-red-500">*</span>
                        </label>
                        <select name="oda_sayisi" id="oda_sayisi" required
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg
                                       bg-white dark:bg-gray-800 text-black dark:text-white
                                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                       transition-all duration-200">
                            <option value="">Seçin</option>
                            <option value="1+1">1+1</option>
                            <option value="2+1">2+1</option>
                            <option value="3+1">3+1</option>
                            <option value="4+1">4+1</option>
                            <option value="5+1">5+1</option>
                            <option value="6+1">6+1</option>
                        </select>
                    </div>

                    <div>
                        <label for="banyo_sayisi"
                            class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Banyo Sayısı <span class="text-red-500">*</span>
                        </label>
                        <select name="banyo_sayisi" id="banyo_sayisi" required
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg
                                       bg-white dark:bg-gray-800 text-black dark:text-white
                                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                       transition-all duration-200">
                            <option value="">Seçin</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4+</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div>
                        <label for="brut_alan" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Brüt Alan (m²) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="brut_alan" id="brut_alan" required step="0.01"
                            placeholder="150"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg
                                      bg-white dark:bg-gray-800 text-black dark:text-white
                                      focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                      transition-all duration-200">
                    </div>

                    <div>
                        <label for="net_alan" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Net Alan (m²) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="net_alan" id="net_alan" required step="0.01"
                            placeholder="120"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg
                                      bg-white dark:bg-gray-800 text-black dark:text-white
                                      focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                      transition-all duration-200">
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div>
                        <label for="bulundugu_kat"
                            class="block text-sm font-medium text-gray-900 dark:text-white mb-1">
                            Bulunduğu Kat
                        </label>
                        <input type="number" name="bulundugu_kat" id="bulundugu_kat" placeholder="3"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg
                                      bg-white dark:bg-gray-800 text-black dark:text-white
                                      focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                      transition-all duration-200">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Opsiyonel alan</p>
                    </div>

                    <div>
                        <label for="toplam_kat" class="block text-sm font-medium text-gray-900 dark:text-white mb-1">
                            Toplam Kat
                        </label>
                        <input type="number" name="toplam_kat" id="toplam_kat" placeholder="5"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg
                                      bg-white dark:bg-gray-800 text-black dark:text-white
                                      focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                      transition-all duration-200">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Opsiyonel alan</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        Site Özellikleri
                    </label>
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2">
                        <label class="flex items-center">
                            <input type="checkbox" name="site_ozellikleri[]" value="havuz"
                                class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Havuz</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="site_ozellikleri[]" value="otopark"
                                class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Otopark</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="site_ozellikleri[]" value="asansor"
                                class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Asansör</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="site_ozellikleri[]" value="guvenlik"
                                class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Güvenlik</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="!categoryType || (categoryType !== 'arsa' && categoryType !== 'konut' && categoryType !== 'daire' && categoryType !== 'villa' && !categoryType.includes('arsa') && !categoryType.includes('konut'))"
            x-transition
            class="mt-4 px-4 py-3 rounded-xl border border-dashed border-blue-300 dark:border-blue-700 bg-blue-50 dark:bg-blue-900/20 text-sm text-blue-800 dark:text-blue-200 text-center">
            <p>Bu kategori için özel alanlar henüz tanımlanmadı.</p>
            <p class="mt-1">Genel bilgileri Adım 3'te doldurabilirsiniz.</p>
        </div>
    </div>
</div>

{{-- Step 2 Harita Senkronizasyon Script --}}
@push('scripts')
    <script>
        (function() {
            let step2Map = null;
            let step2Marker = null;
            let step2MapLayers = null;

            function initStep2Map() {
                if (window.step2Map) {
                    setTimeout(() => {
                        if (window.step2Map) window.step2Map.invalidateSize();
                    }, 200);
                    return;
                }
                const mapElement = document.getElementById('step2-map');
                const loadingElement = document.getElementById('step2-map-loading');

                if (!mapElement) return;

                if (typeof L === 'undefined') {
                    if (loadingElement) {
                        loadingElement.innerHTML =
                            '<div class="text-center text-red-500 p-4">Harita kütüphanesi yüklenemedi</div>';
                    }
                    setTimeout(initStep2Map, 500);
                    return;
                }

                if (loadingElement) loadingElement.style.display = 'none';

                // Step 1'deki harita varsa ondan bilgileri al, yoksa varsayılan Bodrum
                let center = [37.0344, 27.4305];
                let zoom = 12;

                if (window.wizardMap && window.wizardMarker) {
                    const wizardCenter = window.wizardMap.getCenter();
                    center = [wizardCenter.lat, wizardCenter.lng];
                    zoom = window.wizardMap.getZoom();
                }

                const map = L.map('step2-map', {
                    center: center,
                    zoom: zoom,
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

                // ✅ Uydu modunda başlat
                satelliteLayer.addTo(map);

                step2MapLayers = {
                    map: osmLayer,
                    satellite: satelliteLayer,
                    current: 'satellite'
                };

                // Step 1'deki marker pozisyonunu al
                let markerPosition = center;
                if (window.wizardMarker) {
                    const wizardPos = window.wizardMarker.getLatLng();
                    markerPosition = [wizardPos.lat, wizardPos.lng];
                }

                const marker = L.marker(markerPosition, {
                    draggable: true
                }).addTo(map);

                // Marker sürüklendiğinde Step 1'deki haritayı güncelle
                marker.on('dragend', function(e) {
                    const lat = e.target.getLatLng().lat;
                    const lng = e.target.getLatLng().lng;

                    // Step 1'deki haritayı güncelle
                    if (window.wizardMap && window.wizardMarker) {
                        window.wizardMap.setView([lat, lng], window.wizardMap.getZoom());
                        window.wizardMarker.setLatLng([lat, lng]);
                    }

                    // Koordinatları kaydet
                    const latInput = document.querySelector('[name="enlem"]') || document.querySelector(
                        '[name="latitude"]');
                    const lngInput = document.querySelector('[name="boylam"]') || document.querySelector(
                        '[name="longitude"]');
                    if (latInput) latInput.value = lat.toFixed(6);
                    if (lngInput) lngInput.value = lng.toFixed(6);
                });

                // Haritaya tıklandığında marker'ı taşı
                map.on('click', function(e) {
                    const lat = e.latlng.lat;
                    const lng = e.latlng.lng;
                    marker.setLatLng([lat, lng]);

                    // Step 1'deki haritayı güncelle
                    if (window.wizardMap && window.wizardMarker) {
                        window.wizardMap.setView([lat, lng], map.getZoom());
                        window.wizardMarker.setLatLng([lat, lng]);
                    }

                    // Koordinatları kaydet
                    const latInput = document.querySelector('[name="enlem"]') || document.querySelector(
                        '[name="latitude"]');
                    const lngInput = document.querySelector('[name="boylam"]') || document.querySelector(
                        '[name="longitude"]');
                    if (latInput) latInput.value = lat.toFixed(6);
                    if (lngInput) lngInput.value = lng.toFixed(6);
                });

                // Step 1'deki harita değişikliklerini dinle (çift yönlü senkronizasyon)
                function syncFromStep1() {
                    if (window.wizardMap && step2Map && !step2Map._updating) {
                        step2Map._updating = true;
                        const center = window.wizardMap.getCenter();
                        step2Map.setView([center.lat, center.lng], window.wizardMap.getZoom());
                        if (step2Marker && window.wizardMarker) {
                            const pos = window.wizardMarker.getLatLng();
                            step2Marker.setLatLng([pos.lat, pos.lng]);
                        }
                        setTimeout(() => {
                            step2Map._updating = false;
                        }, 100);
                    }
                }

                // Step 1 haritası varsa event listener ekle
                if (window.wizardMap) {
                    window.wizardMap.off('move syncFromStep1');
                    window.wizardMap.off('zoom syncFromStep1');
                    window.wizardMap.on('move', syncFromStep1);
                    window.wizardMap.on('zoom', syncFromStep1);

                    if (window.wizardMarker) {
                        window.wizardMarker.off('dragend syncFromStep1');
                        window.wizardMarker.on('dragend', function(e) {
                            if (step2Marker && step2Map) {
                                const pos = e.target.getLatLng();
                                step2Marker.setLatLng([pos.lat, pos.lng]);
                                step2Map.setView([pos.lat, pos.lng], step2Map.getZoom());

                                // Koordinatları kaydet
                                const latInput = document.querySelector('[name="enlem"]') || document
                                    .querySelector('[name="latitude"]');
                                const lngInput = document.querySelector('[name="boylam"]') || document
                                    .querySelector('[name="longitude"]');
                                if (latInput) latInput.value = pos.lat.toFixed(6);
                                if (lngInput) lngInput.value = pos.lng.toFixed(6);
                            }
                        });
                    }
                } else {
                    // Step 1 haritası henüz yüklenmemişse, yüklendiğinde dinle
                    const checkWizardMap = setInterval(() => {
                        if (window.wizardMap) {
                            clearInterval(checkWizardMap);
                            window.wizardMap.on('move', syncFromStep1);
                            window.wizardMap.on('zoom', syncFromStep1);
                            if (window.wizardMarker) {
                                window.wizardMarker.on('dragend', function(e) {
                                    if (step2Marker && step2Map) {
                                        const pos = e.target.getLatLng();
                                        step2Marker.setLatLng([pos.lat, pos.lng]);
                                        step2Map.setView([pos.lat, pos.lng], step2Map.getZoom());
                                    }
                                });
                            }
                        }
                    }, 500);
                    setTimeout(() => clearInterval(checkWizardMap), 10000);
                }

                step2Map = map;
                step2Marker = marker;
                window.step2Map = map;
                window.step2Marker = marker;
                window.step2MapLayers = step2MapLayers;

                setTimeout(() => {
                    map.invalidateSize();
                }, 200);
            }

            window.switchStep2MapView = function(viewType) {
                if (!window.step2Map || !window.step2MapLayers) return;

                const map = window.step2Map;
                const layers = window.step2MapLayers;
                const mapViewBtn = document.getElementById('step2-map-view-btn');
                const satelliteViewBtn = document.getElementById('step2-satellite-view-btn');

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

            // Step 2'ye geçildiğinde haritayı başlat
            document.addEventListener('wizard-step-changed', function(e) {
                if (e.detail && e.detail.step === 2) {
                    setTimeout(initStep2Map, 500);
                }
            });

            // İlk yüklemede Step 2'deyse başlat
            if (document.querySelector('[x-show*="currentStep === 2"]')) {
                setTimeout(initStep2Map, 1000);
            }
        })();
    </script>
@endpush
