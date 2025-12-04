{{-- TKGM Otomatik Doldurma Widget --}}
<div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20
            rounded-xl border-2 border-blue-200 dark:border-blue-800 p-6 shadow-lg"
    x-data="tkgmWidget()" x-init="init()">

    <div class="flex items-center gap-3 mb-4">
        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-blue-600 text-white shadow-md">
            🔍
        </div>
        <div>
            <h4 class="text-lg font-bold text-gray-900 dark:text-white">TKGM Otomatik Doldurma</h4>
            <p class="text-xs text-gray-600 dark:text-gray-400">
                <span class="font-semibold text-blue-600 dark:text-blue-400">📍 Haritadan konum seçin</span> veya
                Ada/Parsel numarasını girin
            </p>
        </div>
    </div>

    {{-- Ada/Parsel Input'ları --}}
    <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
            <label for="ada_no" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                Ada No <span class="text-gray-400 text-xs">(Opsiyonel - Doğrulama için)</span>
            </label>
            <input type="text" id="ada_no" name="ada_no" x-model="adaNo" @blur.debounce.800ms="checkTKGMReady()"
                placeholder="1234"
                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg
                          bg-white dark:bg-gray-800 text-black dark:text-white
                          focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                          transition-all duration-200">
        </div>

        <div>
            <label for="parsel_no" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                Parsel No <span class="text-gray-400 text-xs">(Opsiyonel - Doğrulama için)</span>
            </label>
            <input type="text" id="parsel_no" name="parsel_no" x-model="parselNo"
                @blur.debounce.800ms="checkTKGMReady()" placeholder="5"
                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg
                          bg-white dark:bg-gray-800 text-black dark:text-white
                          focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                          transition-all duration-200">
        </div>
    </div>

    {{-- TKGM Sorgula Butonu --}}
    <button type="button" @click="fetchTKGM()" :disabled="loading || !canFetch"
        class="w-full px-6 py-3 bg-blue-600 dark:bg-blue-500 text-white rounded-lg
                   hover:bg-blue-700 dark:hover:bg-blue-600 hover:scale-105 active:scale-95
                   focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                   disabled:opacity-50 disabled:cursor-not-allowed
                   transition-all duration-200 ease-in-out
                   shadow-md hover:shadow-lg font-medium flex items-center justify-center gap-2">
        <svg x-show="!loading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <svg x-show="loading" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
            </circle>
            <path class="opacity-75" fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
            </path>
        </svg>
        <span
            x-text="loading ? 'TKGM Sorgulanıyor...' : (hasCoordinates ? '🔍 TKGM\'den Otomatik Doldur (Koordinat)' : '🔍 TKGM\'den Otomatik Doldur (Ada/Parsel)')"></span>
    </button>

    {{-- TKGM Sonuçları --}}
    <div x-show="tkgmData && tkgmData !== null" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-4"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        class="mt-6 p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">

        <div class="flex items-center gap-2 mb-3">
            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd" />
            </svg>
            <h5 class="font-bold text-green-800 dark:text-green-300">TKGM'den Gelen Bilgiler</h5>
        </div>

        <div class="grid grid-cols-2 gap-3 text-sm">
            <div>
                <span class="text-gray-600 dark:text-gray-400">Alan:</span>
                <strong class="ml-2 text-gray-900 dark:text-white"
                    x-text="tkgmData && tkgmData.alan_m2 ? tkgmData.alan_m2 + ' m²' : 'N/A'"></strong>
            </div>
            <div>
                <span class="text-gray-600 dark:text-gray-400">İmar Durumu:</span>
                <strong class="ml-2 text-gray-900 dark:text-white"
                    x-text="tkgmData ? (tkgmData.imar_statusu || tkgmData.imar_durumu || 'N/A') : 'N/A'"></strong>
            </div>
            <div>
                <span class="text-gray-600 dark:text-gray-400">KAKS:</span>
                <strong class="ml-2 text-gray-900 dark:text-white"
                    x-text="tkgmData && tkgmData.kaks ? tkgmData.kaks : 'N/A'"></strong>
            </div>
            <div>
                <span class="text-gray-600 dark:text-gray-400">TAKS:</span>
                <strong class="ml-2 text-gray-900 dark:text-white"
                    x-text="tkgmData && tkgmData.taks ? tkgmData.taks : 'N/A'"></strong>
            </div>
            <div>
                <span class="text-gray-600 dark:text-gray-400">Gabari:</span>
                <strong class="ml-2 text-gray-900 dark:text-white"
                    x-text="tkgmData && tkgmData.gabari ? tkgmData.gabari + ' m' : 'N/A'"></strong>
            </div>
            <div>
                <span class="text-gray-600 dark:text-gray-400">Koordinatlar:</span>
                <strong class="ml-2 text-gray-900 dark:text-white"
                    x-text="tkgmData && tkgmData.center_lat && tkgmData.center_lng ?
                                tkgmData.center_lat.toFixed(4) + ', ' + tkgmData.center_lng.toFixed(4) : 'N/A'"></strong>
            </div>
        </div>

        <button type="button" @click="fillForm()"
            class="mt-4 w-full px-4 py-2 bg-green-600 dark:bg-green-500 text-white rounded-lg
                       hover:bg-green-700 dark:hover:bg-green-600 hover:scale-105 active:scale-95
                       focus:ring-2 focus:ring-green-500 focus:ring-offset-2
                       transition-all duration-200 ease-in-out
                       shadow-md hover:shadow-lg font-medium">
            ✅ Formu Otomatik Doldur
        </button>
    </div>

    {{-- Hata Mesajı --}}
    <div x-show="error" x-transition
        class="mt-4 p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                    clip-rule="evenodd" />
            </svg>
            <span class="text-sm text-red-800 dark:text-red-300" x-text="error"></span>
        </div>
    </div>
</div>

<script>
    function tkgmWidget() {
        return {
            adaNo: '',
            parselNo: '',
            loading: false,
            tkgmData: null,
            error: null,
            canFetch: false,
            hasCoordinates: false,
            selectedLat: null,
            selectedLng: null,
            coordinateCheckInterval: null,

            init() {
                window.tkgmWidgetInstance = this;
                this.setupCoordinateListener();
                this.checkCoordinates();
            },

            setupCoordinateListener() {
                const latInput = document.querySelector('[name="enlem"]') || document.querySelector(
                    '[name="latitude"]');
                const lngInput = document.querySelector('[name="boylam"]') || document.querySelector(
                    '[name="longitude"]');

                if (latInput && lngInput) {
                    latInput.addEventListener('input', () => this.checkCoordinates());
                    lngInput.addEventListener('input', () => this.checkCoordinates());
                }
            },

            checkCoordinates() {
                const latInput = document.querySelector('[name="enlem"]') || document.querySelector(
                    '[name="latitude"]');
                const lngInput = document.querySelector('[name="boylam"]') || document.querySelector(
                    '[name="longitude"]');

                if (latInput?.value && lngInput?.value) {
                    this.selectedLat = parseFloat(latInput.value);
                    this.selectedLng = parseFloat(lngInput.value);
                    this.hasCoordinates = true;
                    this.canFetch = true;
                } else {
                    this.hasCoordinates = false;
                    this.checkTKGMReady();
                }
            },

            checkTKGMReady() {
                const ilId = document.getElementById('il_id')?.value;
                const ilceId = document.getElementById('ilce_id')?.value;
                this.canFetch = this.hasCoordinates || !!(this.adaNo && this.parselNo && ilId && ilceId);
            },

            async fetchTKGMByCoordinates(lat, lng) {
                if (!lat || !lng || this.loading) return;

                this.selectedLat = lat;
                this.selectedLng = lng;
                this.hasCoordinates = true;
                await this.performTKGMRequest({
                    lat,
                    lng
                }, true);
            },

            async fetchTKGM() {
                if (!this.canFetch || this.loading) return;

                if (this.hasCoordinates && this.selectedLat && this.selectedLng) {
                    return await this.fetchTKGMByCoordinates(this.selectedLat, this.selectedLng);
                }

                const ilSelect = document.getElementById('il_id');
                const ilceSelect = document.getElementById('ilce_id');
                const ilAdi = ilSelect?.options[ilSelect.selectedIndex]?.text || '';
                const ilceAdi = ilceSelect?.options[ilceSelect.selectedIndex]?.text || '';

                if (!ilAdi || !ilceAdi) {
                    this.error = 'Lütfen önce İl ve İlçe seçin veya haritadan konum seçin';
                    return;
                }

                if (!this.adaNo || !this.parselNo) {
                    this.error = 'Lütfen haritadan konum seçin veya Ada ve Parsel numaralarını girin';
                    return;
                }

                await this.performTKGMRequest({
                    il: ilAdi,
                    ilce: ilceAdi,
                    ada: this.adaNo,
                    parsel: this.parselNo
                }, false);
            },

            async performTKGMRequest(payload, autoFill = false) {
                this.loading = true;
                this.error = null;
                this.tkgmData = null;

                try {
                    const url = window.APIConfig?.properties?.tkgmLookup || '/api/properties/tkgm-lookup';
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ||
                                '',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(payload),
                    });

                    if (!response.ok) {
                        this.handleTKGMError(response);
                        return;
                    }

                    const result = await response.json();

                    if (result.success && result.data) {
                        this.tkgmData = result.data;
                        this.error = null;

                        if (result.data.ada_no) this.adaNo = result.data.ada_no;
                        if (result.data.parsel_no) this.parselNo = result.data.parsel_no;

                        if (autoFill) {
                            setTimeout(() => this.fillForm(), 300);
                        } else {
                            setTimeout(() => this.updateMap(), 500);
                        }
                    } else {
                        this.error = result.message || 'TKGM verisi bulunamadı. Lütfen manuel girebilirsiniz.';
                        this.tkgmData = null;
                    }
                } catch (err) {
                    this.error = 'TKGM bağlantı hatası: ' + (err.message || 'Bilinmeyen hata');
                    this.tkgmData = null;
                } finally {
                    this.loading = false;
                }
            },

            async handleTKGMError(response) {
                if (response.status === 404) {
                    this.error = 'Bu konumda parsel verisi bulunamadı.';
                } else if (response.status === 422) {
                    const errorData = await response.json().catch(() => ({}));
                    this.error = errorData.message || 'Validasyon hatası';
                } else if (response.status === 500) {
                    this.error = 'TKGM servisi şu anda kullanılamıyor. Lütfen daha sonra tekrar deneyin.';
                } else {
                    this.error = `TKGM bağlantı hatası (${response.status}): ${response.statusText}`;
                }
                this.tkgmData = null;
            },

            fillForm() {
                if (!this.tkgmData) return;

                this.setFieldValue('ada_no', this.adaNo);
                this.setFieldValue('parsel_no', this.parselNo);
                this.setFieldValue('alan_m2', this.tkgmData.alan_m2);
                this.setFieldValue('kaks', this.tkgmData.kaks);
                this.setFieldValue('taks', this.tkgmData.taks);
                this.setFieldValue('gabari', this.tkgmData.gabari);

                this.setImarStatusu(this.tkgmData.imar_statusu || this.tkgmData.imar_durumu);
                this.setCoordinates(this.tkgmData.center_lat, this.tkgmData.center_lng);
                this.setCheckboxes();
            },

            setFieldValue(fieldId, value) {
                if (!value) return;
                const field = document.getElementById(fieldId);
                if (field) field.value = value;
            },

            setImarStatusu(imarStatusu) {
                if (!imarStatusu) return;
                const imarSelect = document.getElementById('imar_statusu') || document.getElementById('imar_durumu');
                if (!imarSelect) return;

                const normalized = imarStatusu.toLowerCase().replace(/\s+/g, '_');
                imarSelect.value = normalized;

                if (!imarSelect.value) {
                    if (imarStatusu.toLowerCase().includes('imarlı')) {
                        imarSelect.value = 'imarlı';
                    } else if (imarStatusu.toLowerCase().includes('imarsız') || imarStatusu.toLowerCase().includes(
                            'imar_dışı')) {
                        imarSelect.value = 'imar_dışı';
                    }
                }
            },

            setCoordinates(lat, lng) {
                if (!lat || !lng) return;

                ['enlem', 'latitude'].forEach(id => {
                    const field = document.getElementById(id);
                    if (field) field.value = lat;
                });

                ['boylam', 'longitude'].forEach(id => {
                    const field = document.getElementById(id);
                    if (field) field.value = lng;
                });

                this.updateMap();
            },

            setCheckboxes() {
                const checkboxMap = {
                    'altyapi_elektrik': this.tkgmData.altyapi_elektrik,
                    'altyapi_su': this.tkgmData.altyapi_su,
                    'altyapi_dogalgaz': this.tkgmData.altyapi_dogalgaz,
                    'yola_cephe': this.tkgmData.yola_cephe,
                };

                Object.entries(checkboxMap).forEach(([name, value]) => {
                    if (value) {
                        const checkbox = document.querySelector(`[name="${name}"]`);
                        if (checkbox) checkbox.checked = true;
                    }
                });
            },

            updateMap() {
                if (!this.tkgmData?.center_lat || !this.tkgmData?.center_lng) return;

                const mapContainer = document.getElementById('tkgm-map');
                if (!mapContainer || typeof L === 'undefined') return;

                if (window.tkgmMap) {
                    window.tkgmMap.setView([this.tkgmData.center_lat, this.tkgmData.center_lng], 18);
                    if (window.tkgmMarker) {
                        window.tkgmMarker.setLatLng([this.tkgmData.center_lat, this.tkgmData.center_lng]);
                    } else {
                        window.tkgmMarker = L.marker([this.tkgmData.center_lat, this.tkgmData.center_lng])
                            .addTo(window.tkgmMap)
                            .bindPopup(`Ada: ${this.adaNo}, Parsel: ${this.parselNo}`)
                            .openPopup();
                    }
                } else {
                    this.initTKGMMap();
                }
            },

            initTKGMMap() {
                if (!this.tkgmData?.center_lat || !this.tkgmData?.center_lng) return;

                const mapContainer = document.getElementById('tkgm-map');
                if (!mapContainer || window.tkgmMap || typeof L === 'undefined') return;

                window.tkgmMap = L.map('tkgm-map').setView([this.tkgmData.center_lat, this.tkgmData.center_lng], 18);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors',
                    maxZoom: 19
                }).addTo(window.tkgmMap);

                window.tkgmMarker = L.marker([this.tkgmData.center_lat, this.tkgmData.center_lng])
                    .addTo(window.tkgmMap)
                    .bindPopup(
                        `Ada: ${this.adaNo}, Parsel: ${this.parselNo}<br>Alan: ${this.tkgmData.alan_m2 || 'N/A'} m²`)
                    .openPopup();

                const placeholder = mapContainer.querySelector('.absolute');
                if (placeholder) placeholder.style.display = 'none';
            }
        }
    }
</script>
