<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Yeni İlan Oluştur</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">İlan bilgilerini doldurun ve yayınlayın</p>
        </div>
        <a href="<?php echo e(route('admin.ilanlar.index')); ?>" class="inline-flex items-center px-6 py-3 bg-gray-600 text-white font-semibold rounded-lg shadow-md hover:bg-gray-700 hover:scale-105 hover:shadow-lg active:scale-95 focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Geri Dön
        </a>
    </div>

    
    <div id="draft-restore-banner"></div>

    
    <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-gray-900 dark:text-white">Form İlerlemesi</span>
            <span id="form-progress-text" class="text-sm text-gray-500 dark:text-gray-400">%0 tamamlandı</span>
        </div>
        <div class="w-full bg-gray-200 dark:bg-gray-800 rounded-full h-2.5">
            <div id="form-progress-bar" 
                 class="h-full bg-red-500 rounded-full transition-all duration-500"
                 style="width: 0%"></div>
        </div>
        <div class="flex items-center justify-between mt-2">
            <span id="save-indicator" class="text-xs text-gray-400 flex items-center">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Otomatik kayıt aktif
            </span>
            <span class="text-xs text-gray-400">Her 30 saniyede</span>
        </div>
    </div>

    <!-- Main Form -->
    <form id="ilan-create-form"
          method="POST"
          action="<?php echo e(route('admin.ilanlar.store')); ?>"
          enctype="multipart/form-data"
          x-data="{ selectedSite: null, selectedPerson: null }">
        <?php echo csrf_field(); ?>

        <div class="space-y-6">
            <!-- Section 1: Temel Bilgiler + AI Yardımcısı -->
            <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200">
                <?php echo $__env->make('admin.ilanlar.components.basic-info', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>

            <!-- Section 2: Kategori Sistemi -->
            <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200">
                <?php echo $__env->make('admin.ilanlar.components.category-system', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>

            <!-- Section 3: Lokasyon ve Harita -->
            <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200">
                <?php echo $__env->make('admin.ilanlar.components.location-map', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>

            <!-- Section 4: İlan Özellikleri (Field Dependencies) -->
            <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200">
                
                <?php echo $__env->make('admin.ilanlar.components.smart-field-organizer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                
                
                <?php echo $__env->make('admin.ilanlar.components.field-dependencies-dynamic', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>

            <!-- Section 4.5: Yazlık Amenities (Features/EAV - Sadece Yazlık kategorisi için) -->
            <div class="kategori-specific-section" data-show-for-categories="yazlik" style="display: none;">
                <?php echo $__env->make('admin.ilanlar.partials.yazlik-features', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>

            <!-- Section 4.6: Bedroom Layout (Yazlık için kritik!) -->
            <div class="kategori-specific-section" data-show-for-categories="yazlik" style="display: none;">
                <?php echo $__env->make('admin.ilanlar.components.bedroom-layout-manager', ['ilan' => new \App\Models\Ilan()], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>

            <!-- Section 4.7: Photo Upload (Tüm kategoriler için) -->
            <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200">
                <?php echo $__env->make('admin.ilanlar.components.photo-upload-manager', ['ilan' => new \App\Models\Ilan()], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>

            <!-- Section 4.8: Event/Booking Calendar (Yazlık için) -->
            <div class="kategori-specific-section" data-show-for-categories="yazlik" style="display: none;">
                <?php echo $__env->make('admin.ilanlar.components.event-booking-manager', ['ilan' => new \App\Models\Ilan()], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>

            <!-- Section 4.9: Season Pricing (Yazlık için) -->
            <div class="kategori-specific-section" data-show-for-categories="yazlik" style="display: none;">
                <?php echo $__env->make('admin.ilanlar.components.season-pricing-manager', ['ilan' => new \App\Models\Ilan()], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>

            <!-- Section 5: Fiyat Yönetimi -->
            <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200">
                <?php echo $__env->make('admin.ilanlar.components.price-management', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>

            <!-- Section 6: Kişi Bilgileri (CRM) -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm" x-data="{ selectedPerson: null }">
                <?php echo $__env->make('admin.ilanlar.partials.stable._kisi-secimi', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>

            <!-- Section 7: Site/Apartman Bilgileri (Sadece Konut kategorisi için) -->
            <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200 kategori-specific-section" data-show-for-categories="konut" style="display: none;">
                <?php echo $__env->make('admin.ilanlar.components.site-apartman-context7', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>

            <!-- Section 8: Anahtar Bilgileri (Sadece Konut kategorisi için) -->
            <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200 kategori-specific-section" data-show-for-categories="konut" style="display: none;">
                <?php echo $__env->make('admin.ilanlar.components.key-management', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>

            <!-- Section 9: İlan Fotoğrafları -->
            <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200">
                <?php echo $__env->make('admin.ilanlar.components.listing-photos', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>

            <!-- 🎨 Section 10: Yayın Durumu (Tailwind Modernized + Context7 Fixed) -->
            <div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-8 hover:shadow-2xl transition-shadow duration-300">
                <!-- Section Header -->
                <div class="flex items-center gap-4 mb-8 pb-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 text-white shadow-lg shadow-green-500/50 font-bold text-lg">
                        10
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Yayın Durumu
                        </h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">İlanınızın durumu ve öncelik seviyesi</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Status - Enhanced (Context7 Fixed) -->
                    <div class="group">
                        <label for="status" class="block text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                            <span class="flex items-center justify-center w-6 h-6 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 text-xs font-bold">
                                1
                            </span>
                            Status
                            <span class="text-red-500 font-bold">*</span>
                        </label>
                        <div class="relative">
                            <select name="status"
                                id="status"
                                required
                                class="w-full px-4 py-2.5
                                       border-2 border-gray-300 dark:border-gray-600
                                       rounded-xl
                                       bg-white dark:bg-gray-800
                                       text-black dark:text-white
                                       focus:ring-4 focus:ring-blue-500 dark:focus:ring-blue-400/20 focus:border-green-500 dark:focus:border-green-400
                                       transition-all duration-200
                                       hover:border-gray-400 dark:hover:border-gray-500
                                       cursor-pointer
                                       shadow-sm hover:shadow-md focus:shadow-lg
                                       appearance-none">
                                <option value="">Bir durum seçin...</option>
                                <option value="draft" <?php echo e(old('status') == 'draft' ? 'selected' : ''); ?>>📝 Draft</option>
                                <option value="active" <?php echo e(old('status', 'active') == 'active' ? 'selected' : ''); ?>>✅ Active</option>
                                <option value="inactive" <?php echo e(old('status') == 'inactive' ? 'selected' : ''); ?>>⏸️ Inactive</option>
                                <option value="pending" <?php echo e(old('status') == 'pending' ? 'selected' : ''); ?>>⏳ Pending</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400 group-focus-within:text-green-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                        <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="mt-2 flex items-center gap-2 text-sm text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 px-4 py-2.5 rounded-lg">
                                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                <?php echo e($message); ?>

                            </div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Öncelik Seviyesi - Enhanced -->
                    <div class="group">
                        <label for="oncelik" class="block text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                            <span class="flex items-center justify-center w-6 h-6 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 text-xs font-bold">
                                2
                            </span>
                            Öncelik Seviyesi
                        </label>
                        <div class="relative">
                            <select name="oncelik"
                                id="oncelik"
                                class="w-full px-4 py-2.5
                                       border-2 border-gray-300 dark:border-gray-600
                                       rounded-xl
                                       bg-white dark:bg-gray-800
                                       text-black dark:text-white
                                       focus:ring-4 focus:ring-blue-500 dark:focus:ring-blue-400/20 focus:border-green-500 dark:focus:border-green-400
                                       transition-all duration-200
                                       hover:border-gray-400 dark:hover:border-gray-500
                                       cursor-pointer
                                       shadow-sm hover:shadow-md focus:shadow-lg
                                       appearance-none">
                                <option value="normal" <?php echo e(old('oncelik', 'normal') == 'normal' ? 'selected' : ''); ?>>📋 Normal</option>
                                <option value="yuksek" <?php echo e(old('oncelik') == 'yuksek' ? 'selected' : ''); ?>>⭐ Yüksek</option>
                                <option value="acil" <?php echo e(old('oncelik') == 'acil' ? 'selected' : ''); ?>>🚨 Acil</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400 group-focus-within:text-green-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 🎨 Form Actions (Tailwind Modernized) -->
        <div class="sticky bottom-6 z-20">
            <div class="bg-gradient-to-r from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-2xl border-2 border-gray-200 dark:border-gray-700 p-6 backdrop-blur-sm">
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
                    <!-- Cancel Button -->
                    <a href="<?php echo e(route('admin.ilanlar.index')); ?>"
                       class="inline-flex items-center justify-center gap-2 px-6 py-3.5
                              bg-gray-50 dark:bg-gray-800
                              hover:bg-gray-50 dark:hover:bg-gray-600
                              border-2 border-gray-300 dark:border-gray-600
                              text-gray-900 dark:text-white font-semibold rounded-xl
                              shadow-sm hover:shadow-lg
                              focus:ring-4 focus:ring-blue-500/20 focus:outline-none
                              transition-all duration-200
                              group">
                        <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        İptal Et
                    </a>

                    <!-- Action Buttons -->
                    <div class="flex gap-3">
                        <!-- Save Draft -->
                        <button type="button" id="save-draft-btn"
                                class="inline-flex items-center justify-center gap-2 px-6 py-3.5
                                       bg-gradient-to-r from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600
                                       hover:from-gray-200 hover:to-gray-300 dark:hover:from-gray-600 dark:hover:to-gray-500
                                       border-2 border-gray-300 dark:border-gray-600
                                       text-gray-800 dark:text-gray-200 font-semibold rounded-xl
                                       shadow-md hover:shadow-xl
                                       focus:ring-4 focus:ring-blue-500/20 focus:outline-none
                                       transition-all duration-200
                                       group">
                            <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                            </svg>
                            <span>Taslak Kaydet</span>
                        </button>

                        <!-- Publish Listing -->
                        <button type="submit"
                                class="inline-flex items-center justify-center gap-2 px-8 py-3.5
                                       bg-gradient-to-r from-blue-600 to-purple-600
                                       hover:from-blue-700 hover:to-purple-700
                                       text-white font-bold rounded-xl
                                       shadow-xl hover:shadow-2xl
                                       focus:ring-4 focus:ring-blue-500/50 focus:outline-none
                                       transition-all duration-200
                                       transform hover:scale-105 active:scale-95
                                       group">
                            <svg class="w-5 h-5 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>İlanı Yayınla</span>
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
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
<script src="<?php echo e(asset('js/context7-live-search-simple.js')); ?>"></script>

<!-- İlan Create Modular JavaScript -->
<?php echo app('Illuminate\Foundation\Vite')(['resources/js/admin/ilan-create.js']); ?>
<script type="module" src="<?php echo e(asset('js/leaflet-draw-loader.js')); ?>"></script>

<!-- Leaflet.js OpenStreetMap -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>

<!-- 🗺️ VanillaLocationManager (location-map component script) -->
<script>
// 🎯 Debug Mode (set to false in production)
const DEBUG_MODE = <?php echo e(config('app.debug') ? 'true' : 'false'); ?>;
const log = (...args) => DEBUG_MODE && console.log(...args);

log('🚀 [DEBUG] VanillaLocationManager script yükleniyor...');

// 🎯 Vanilla JS Location Manager (Context7 ONLY - No Alpine.js)
const VanillaLocationManager = {
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
        this.initMap();
        this.attachEventListeners();
    },

    attachEventListeners() {
        // Event listeners zaten location.js'de var
        // Bu sadece harita initialization için
        log('✅ VanillaLocationManager init tamamlandı');
    },

    initMap() {
        setTimeout(() => {
            if (typeof L === 'undefined') {
                console.warn('⚠️ Leaflet not loaded yet, retrying...');
                setTimeout(() => this.initMap(), 1000);
                return;
            }

            const mapEl = document.getElementById('map');
            if (!mapEl || this.map) return;

            // Create map (Bodrum center)
            this.map = L.map('map').setView([37.0344, 27.4305], 13);

            // Standard Layer - OpenStreetMap
            this.standardLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap',
                maxZoom: 19
            }).addTo(this.map);

            // Satellite Layer - Esri World Imagery
            this.satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                attribution: '© Esri',
                maxZoom: 19
            });

            // Map click handler
            this.map.on('click', (e) => {
                this.setMarker(e.latlng.lat, e.latlng.lng);
            });

            log('✅ OpenStreetMap ready (Standart + Uydu layer)');
        }, 500);
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
            if (btnStandard) btnStandard.className = 'flex items-center justify-center gap-1 px-2.5 py-1.5 rounded-lg transition-all duration-200 text-xs font-semibold text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700';
            if (btnSatellite) btnSatellite.className = 'flex items-center justify-center gap-1 px-2.5 py-1.5 rounded-lg transition-all duration-200 text-xs font-semibold bg-gradient-to-br from-blue-500 to-blue-600 text-white shadow-md hover:shadow-lg';
        } else {
            this.map.removeLayer(this.satelliteLayer);
            this.map.addLayer(this.standardLayer);
            this.useSatellite = false;
            if (btnStandard) btnStandard.className = 'flex items-center justify-center gap-1 px-2.5 py-1.5 rounded-lg transition-all duration-200 text-xs font-semibold bg-gradient-to-br from-blue-500 to-blue-600 text-white shadow-md hover:shadow-lg';
            if (btnSatellite) btnSatellite.className = 'flex items-center justify-center gap-1 px-2.5 py-1.5 rounded-lg transition-all duration-200 text-xs font-semibold text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700';
        }
    },

    setMarker(lat, lng) {
        if (this.marker) {
            this.map.removeLayer(this.marker);
        }
        this.marker = L.marker([lat, lng]).addTo(this.map);
        document.getElementById('enlem').value = lat.toFixed(7);
        document.getElementById('boylam').value = lng.toFixed(7);
        log('📍 Konum seçildi:', lat, lng);
        window.toast?.success('Konum haritada işaretlendi');

        // 🆕 Reverse Geocoding: Koordinatlardan adres getir
        this.reverseGeocode(lat, lng);
    },

    async reverseGeocode(lat, lng) {
        try {
            log('🔍 Reverse geocoding başlıyor:', lat, lng);
            window.toast?.info('Adres bilgisi getiriliyor...', 2000);

            // Nominatim Reverse Geocoding API
            const url = `https://nominatim.openstreetmap.org/reverse?` +
                `lat=${lat}` +
                `&lon=${lng}` +
                `&format=json` +
                `&addressdetails=1` +
                `&accept-language=tr`;

            const response = await fetch(url, {
                headers: {
                    'User-Agent': 'YalihanEmlak/1.0'
                }
            });

            if (!response.ok) {
                throw new Error('Reverse geocoding failed');
            }

            const data = await response.json();
            log('✅ Reverse geocoding response:', data);

            if (data && data.address) {
                // Adres componentlerini al
                const addr = data.address;
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
                if (data.display_name) {
                    log('📍 Tam adres:', data.display_name);
                }

                // 🆕 PHASE 4: İl/İlçe/Mahalle Dropdown'larını Otomatik Seç (Çift Yönlü Sync)
                await this.autoSelectLocationDropdowns(addr);

            } else {
                console.warn('⚠️ Adres bilgisi bulunamadı');
                window.toast?.warning('Bu konum için adres bilgisi bulunamadı');
            }

        } catch (error) {
            console.error('❌ Reverse geocoding error:', error);
            window.toast?.error('Adres bilgisi alınamadı');
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

                // Parse response (check for wrapper)
                const iller = ilData.data || ilData;

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
                        const ilDisplayName = matchedIl.name || matchedIl.il_adi || matchedIl.il || 'Unknown';
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
                                        log('✅ İlçe otomatik seçildi:', option.text, '(ID:', option.value, ')');

                                        // Highlight effect
                                        ilceSelect.classList.add('ring-4', 'ring-blue-500/50');
                                        setTimeout(() => ilceSelect.classList.remove('ring-4', 'ring-blue-500/50'), 1500);

                                        // Change event'ini tetikle (mahalleleri yüklemek için)
                                        ilceSelect.dispatchEvent(new Event('change'));

                                        // Mahallelerin yüklenmesi için bekle
                                        await new Promise(resolve => setTimeout(resolve, 500));

                                        // 3️⃣ Mahalle (Neighborhood) Seçimi
                                        const neighborhoodName = addr.suburb || addr.neighbourhood || addr.quarter;
                                        if (neighborhoodName) {
                                            log('🔍 Mahalle arıyor:', neighborhoodName);

                                            const mahalleSelect = document.getElementById('mahalle_id');
                                            if (mahalleSelect && mahalleSelect.options.length > 1) {
                                                // Dropdown'daki seçeneklerden eşleştir
                                                for (let i = 0; i < mahalleSelect.options.length; i++) {
                                                    const option = mahalleSelect.options[i];
                                                    const optionText = option.text.toLowerCase().trim();
                                                    const searchText = neighborhoodName.toLowerCase().trim();

                                                    if (optionText === searchText ||
                                                        optionText.includes(searchText) ||
                                                        searchText.includes(optionText)) {
                                                        mahalleSelect.value = option.value;
                                                        log('✅ Mahalle otomatik seçildi:', option.text, '(ID:', option.value, ')');

                                                        // Highlight effect
                                                        mahalleSelect.classList.add('ring-4', 'ring-blue-500/50');
                                                        setTimeout(() => mahalleSelect.classList.remove('ring-4', 'ring-blue-500/50'), 1500);

                                                        // Change event'ini tetikle
                                                        mahalleSelect.dispatchEvent(new Event('change'));

                                                        window.toast?.success('🎯 İl/İlçe/Mahalle otomatik seçildi!');
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
            const coords = await this.geocodeLocation(`${neighborhoodName}, ${districtName}, ${provinceName}, Turkey`);

            if (coords) {
                this.map.flyTo([coords.lat, coords.lon], 15, {
                    duration: 1.5,
                    easeLinearity: 0.5
                });

                if (this.marker) {
                    this.map.removeLayer(this.marker);
                }
                this.marker = L.marker([coords.lat, coords.lon])
                    .addTo(this.map)
                    .bindPopup(`📍 ${neighborhoodName}`)
                    .openPopup();

                log('✅ Harita mahalleye odaklandı:', neighborhoodName);
                window.toast?.success(`Harita ${neighborhoodName} mahallesine odaklandı`);
            }
        } catch (error) {
            console.error('❌ Mahalle geocoding hatası:', error);
        }
    },

    async geocodeLocation(query) {
        try {
            const url = `https://nominatim.openstreetmap.org/search?` +
                `q=${encodeURIComponent(query)}` +
                `&format=json` +
                `&limit=1` +
                `&addressdetails=1`;

            const response = await fetch(url, {
                headers: {
                    'User-Agent': 'YalihanEmlak/1.0'
                }
            });

            const data = await response.json();

            if (data && data.length > 0) {
                return {
                    lat: parseFloat(data[0].lat),
                    lon: parseFloat(data[0].lon)
                };
            }

            return null;
        } catch (error) {
            console.error('Geocoding error:', error);
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
            window.toast?.error('Harita yüklenmedi');
            return;
        }

        if (!navigator.geolocation) {
            window.toast?.error('Tarayıcınız konum servisini desteklemiyor');
            return;
        }

        window.toast?.info('Konum alınıyor...');

        navigator.geolocation.getCurrentPosition(
            (position) => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                this.map.flyTo([lat, lng], 15, {
                    duration: 1.5
                });

                this.setMarker(lat, lng);
                window.toast?.success('Mevcut konumunuz işaretlendi');
                log('✅ GPS konumu alındı:', lat, lng);
            },
            (error) => {
                if (error.code === 1) {
                    window.toast?.warning('Konum izni reddedildi. Lütfen tarayıcı ayarlarından izin verin.');
                } else if (error.code === 2) {
                    window.toast?.error('Konum bilgisi alınamadı. GPS kapalı olabilir.');
                } else if (error.code === 3) {
                    window.toast?.error('Konum talebi zaman aşımına uğradı.');
                } else {
                    window.toast?.error('Konum alınamadı.');
                }
                log('⚠️ GPS error (code ' + error.code + '):', error.message);
            },
            {
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
});

// Global access
log('🎯 [DEBUG] VanillaLocationManager tanımlanıyor...');
window.VanillaLocationManager = VanillaLocationManager;
log('✅ Vanilla Location Manager registered globally');
log('🔍 [DEBUG] window.VanillaLocationManager:', typeof window.VanillaLocationManager);

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
    if (!VanillaLocationManager.map) {
        window.toast?.error('Önce haritayı yükleyin');
        return;
    }

    // Mülk konumu var mı kontrol et
    if (!VanillaLocationManager.marker) {
        window.toast?.warning('Önce mülk konumunu işaretleyin (haritaya tıklayın)');
        return;
    }

    window.measuringFor = { name, icon };
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

        marker.bindPopup(`${point.icon} ${point.name}<br><strong>${point.displayDistance} ${point.unit}</strong>`);
        window.distanceMarkers.push(marker);

        // Çizgi çiz (property → target)
        const line = L.polyline(
            [[propertyLat, propertyLng], [targetLat, targetLng]],
            {
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

window.startDrawingBoundary = function() {
    if (!VanillaLocationManager.map) {
        window.toast?.error('Önce haritayı yükleyin');
        return;
    }

    // Leaflet.draw kontrolü (with retry)
    if (typeof L.Control.Draw === 'undefined') {
        console.warn('⚠️ Leaflet.draw henüz yüklenmedi, 1 saniye bekleniyor...');
        window.toast?.info('Çizim aracı yükleniyor, lütfen bekleyin...');

        // 1 saniye sonra tekrar dene
        setTimeout(() => {
            if (typeof L.Control.Draw !== 'undefined') {
                console.log('✅ Leaflet.draw yüklendi, tekrar deniyor...');
                window.startDrawingBoundary();
            } else {
                console.error('❌ Leaflet.draw yüklenemedi!');
                window.toast?.error('Çizim aracı yüklenemedi, sayfayı yenileyin');
            }
        }, 1000);
        return;
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
                areaDisplay.textContent = (area / 10000).toFixed(2) + ' dönüm (' + Math.round(area).toLocaleString() + ' m²)';
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
        if (!rule) return { valid: true };

        // Required check
        if (rule.required && (!value || value.toString().trim() === '')) {
            return { valid: false, message: rule.message };
        }

        // Skip other checks if field is empty and not required
        if (!value) return { valid: true };

        // Min length check
        if (rule.minLength && value.toString().length < rule.minLength) {
            return { valid: false, message: rule.message };
        }

        // Max length check
        if (rule.maxLength && value.toString().length > rule.maxLength) {
            return { valid: false, message: rule.message };
        }

        // Min value check (for numbers)
        if (rule.min !== undefined && parseFloat(value) < rule.min) {
            return { valid: false, message: rule.message };
        }

        return { valid: true };
    },

    showError(fieldName, message) {
        const field = document.getElementById(fieldName);
        if (!field) return;

        // Add error class (Tailwind)
        field.classList.add('border-red-500', 'focus:ring-blue-500 dark:focus:ring-blue-400', 'focus:border-red-500');
        field.classList.remove('border-gray-300', 'focus:ring-blue-500', 'focus:border-blue-500');

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
        setTimeout(() => { field.style.animation = ''; }, 500);
    },

    clearError(fieldName) {
        const field = document.getElementById(fieldName);
        if (!field) return;

        // Remove error class
        field.classList.remove('border-red-500', 'focus:ring-blue-500 dark:focus:ring-blue-400', 'focus:border-red-500');
        field.classList.add('border-gray-300');

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
            firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
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
            indicator.className = `text-sm font-medium ${percentage === 100 ? 'text-green-600' : 'text-blue-600'}`;
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
            if (!ValidationManager.validateAll()) {
                e.preventDefault();
                window.toast?.error('❌ Lütfen tüm gerekli alanları doldurun');

                // Count errors
                const errorCount = document.querySelectorAll('.validation-error').length;
                window.toast?.warning(`⚠️ ${errorCount} alan hatalı veya eksik`);

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

    console.log('✅ Validation Manager initialized (' + Object.keys(ValidationManager.rules).length + ' rules)');
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
                if (categorySlug.includes('konut') || categorySlug.includes('daire') || categorySlug.includes('villa')) {
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
        const timeAgo = hours > 0 
            ? `${hours} saat ${minutes} dakika önce`
            : `${minutes} dakika önce`;
        
        const banner = document.createElement('div');
        banner.className = 'bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 p-4 mb-6 rounded-lg flex items-center justify-between animate-pulse';
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
                field.dispatchEvent(new Event('change', { bubbles: true }));
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
                e.returnValue = 'Kaydedilmemiş değişiklikler var! Sayfadan ayrılmak istediğinize emin misiniz?';
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
            progressBar.className = `h-full rounded-full transition-all duration-500 ${this.getProgressColor(progress)}`;
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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.neo', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/macbookpro/Projects/yalihanemlakwarp/resources/views/admin/ilanlar/create.blade.php ENDPATH**/ ?>