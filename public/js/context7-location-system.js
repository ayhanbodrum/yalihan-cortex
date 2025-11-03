/**
 * 🗺️ Context7 Location System - İzolasyon Sistemi
 *
 * Tüm projede kullanılan adres/konum fonksiyonlarını tek merkezden yönetir.
 * Her sayfada aynı kodu yazmak yerine bu sistemi import et ve kullan.
 *
 * @version 1.0.0
 * @date 2025-10-23
 * @context7 Standard Location Management
 */

class Context7LocationSystem {
    constructor(options = {}) {
        this.options = {
            enableToast: options.enableToast !== false,
            enableCache: options.enableCache !== false,
            cacheTimeout: options.cacheTimeout || 300000, // 5 dakika
            debug: options.debug || false,
            autoInit: options.autoInit !== false,
            ...options
        };

        // Cache storage
        this.cache = {
            ulkeler: null,
            iller: null,
            ilceler: null,
            mahalleler: null,
            timestamp: {}
        };

        // State
        this.state = {
            selectedUlke: null,
            selectedIl: null,
            selectedIlce: null,
            selectedMahalle: null
        };

        // Event listeners
        this.listeners = {
            ulkeChange: [],
            ilChange: [],
            ilceChange: [],
            mahalleChange: []
        };

        if (this.options.autoInit) {
            this.init();
        }
    }

    /**
     * Initialize the location system
     */
    async init() {
        this.log('✅ Context7 Location System initialized');

        if (this.options.preloadAll) {
            await this.preloadAll();
        }
    }

    /**
     * Preload all location data
     */
    async preloadAll() {
        this.log('🔄 Preloading all location data...');

        await Promise.all([
            this.loadUlkeler(),
            this.loadIller(),
            this.loadAllIlceler(),
            this.loadAllMahalleler()
        ]);

        this.log('✅ All location data preloaded');
    }

    /**
     * Load countries (Ülkeler)
     */
    async loadUlkeler() {
        return this.fetchWithCache('ulkeler', '/admin/adres-yonetimi/ulkeler', data => data.ulkeler);
    }

    /**
     * Load provinces (İller)
     * @param {number} ulkeId - Optional country ID to filter
     */
    async loadIller(ulkeId = null) {
        const endpoint = ulkeId
            ? `/admin/adres-yonetimi/iller/${ulkeId}`
            : '/admin/adres-yonetimi/iller';

        return this.fetchWithCache('iller', endpoint, data => data.iller);
    }

    /**
     * Load all districts (Tüm İlçeler)
     */
    async loadAllIlceler() {
        return this.fetchWithCache('ilceler', '/api/ilceler', data => data.data || data.ilceler);
    }

    /**
     * Load districts by province (İle göre İlçeler)
     * @param {number} ilId - Province ID
     */
    async loadIlceler(ilId) {
        if (!ilId) {
            throw new Error('ilId is required');
        }

        const cacheKey = `ilceler_${ilId}`;
        return this.fetchWithCache(cacheKey, `/api/ilceler/${ilId}`, data => data.data || data.ilceler);
    }

    /**
     * Load all neighborhoods (Tüm Mahalleler)
     */
    async loadAllMahalleler() {
        return this.fetchWithCache('mahalleler', '/api/mahalleler', data => data.data || data.mahalleler);
    }

    /**
     * Load neighborhoods by district (İlçeye göre Mahalleler)
     * @param {number} ilceId - District ID
     */
    async loadMahalleler(ilceId) {
        if (!ilceId) {
            throw new Error('ilceId is required');
        }

        const cacheKey = `mahalleler_${ilceId}`;
        return this.fetchWithCache(cacheKey, `/api/mahalleler/${ilceId}`, data => data.data || data.mahalleler);
    }

    /**
     * Fetch data with caching support
     * @private
     */
    async fetchWithCache(key, url, dataExtractor) {
        // Check cache
        if (this.options.enableCache && this.isCacheValid(key)) {
            this.log(`📦 Cache hit: ${key}`);
            return this.cache[key];
        }

        try {
            this.log(`🔄 Fetching: ${url}`);
            const response = await fetch(url);
            const data = await response.json();

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${data.message || 'Unknown error'}`);
            }

            const extracted = dataExtractor(data);

            // Update cache
            if (this.options.enableCache) {
                this.cache[key] = extracted;
                this.cache.timestamp[key] = Date.now();
            }

            this.log(`✅ Loaded ${extracted?.length || 0} items for ${key}`);
            return extracted;

        } catch (error) {
            this.error(`Error loading ${key}:`, error);
            this.showToast(`${key} yüklenemedi`, 'error');
            return [];
        }
    }

    /**
     * Check if cache is valid
     * @private
     */
    isCacheValid(key) {
        if (!this.cache[key] || !this.cache.timestamp[key]) {
            return false;
        }

        const age = Date.now() - this.cache.timestamp[key];
        return age < this.options.cacheTimeout;
    }

    /**
     * Clear cache
     */
    clearCache(key = null) {
        if (key) {
            delete this.cache[key];
            delete this.cache.timestamp[key];
            this.log(`🗑️ Cache cleared: ${key}`);
        } else {
            this.cache = {
                ulkeler: null,
                iller: null,
                ilceler: null,
                mahalleler: null,
                timestamp: {}
            };
            this.log('🗑️ All cache cleared');
        }
    }

    /**
     * Cascade: Select country
     */
    async selectUlke(ulkeId) {
        this.state.selectedUlke = ulkeId;
        this.state.selectedIl = null;
        this.state.selectedIlce = null;
        this.state.selectedMahalle = null;

        this.emit('ulkeChange', ulkeId);

        if (ulkeId) {
            return await this.loadIller(ulkeId);
        }
        return [];
    }

    /**
     * Cascade: Select province
     */
    async selectIl(ilId) {
        this.state.selectedIl = ilId;
        this.state.selectedIlce = null;
        this.state.selectedMahalle = null;

        this.emit('ilChange', ilId);

        if (ilId) {
            return await this.loadIlceler(ilId);
        }
        return [];
    }

    /**
     * Cascade: Select district
     */
    async selectIlce(ilceId) {
        this.state.selectedIlce = ilceId;
        this.state.selectedMahalle = null;

        this.emit('ilceChange', ilceId);

        if (ilceId) {
            return await this.loadMahalleler(ilceId);
        }
        return [];
    }

    /**
     * Cascade: Select neighborhood
     */
    selectMahalle(mahalleId) {
        this.state.selectedMahalle = mahalleId;
        this.emit('mahalleChange', mahalleId);
    }

    /**
     * Event system: Add listener
     */
    on(event, callback) {
        if (!this.listeners[event]) {
            this.listeners[event] = [];
        }
        this.listeners[event].push(callback);
    }

    /**
     * Event system: Remove listener
     */
    off(event, callback) {
        if (!this.listeners[event]) return;
        this.listeners[event] = this.listeners[event].filter(cb => cb !== callback);
    }

    /**
     * Event system: Emit event
     * @private
     */
    emit(event, data) {
        if (!this.listeners[event]) return;
        this.listeners[event].forEach(callback => callback(data));
    }

    /**
     * Get current state
     */
    getState() {
        return { ...this.state };
    }

    /**
     * Reset state
     */
    resetState() {
        this.state = {
            selectedUlke: null,
            selectedIl: null,
            selectedIlce: null,
            selectedMahalle: null
        };
        this.log('🔄 State reset');
    }

    /**
     * Populate dropdown (Helper function)
     */
    populateDropdown(selectElement, items, config = {}) {
        const {
            valueField = 'id',
            textField = 'name',
            placeholder = 'Seçiniz...',
            includeEmpty = true,
            emptyValue = ''
        } = config;

        if (typeof selectElement === 'string') {
            selectElement = document.querySelector(selectElement);
        }

        if (!selectElement) {
            this.error('Select element not found');
            return;
        }

        // Clear existing options
        selectElement.innerHTML = '';

        // Add empty option
        if (includeEmpty) {
            const emptyOption = document.createElement('option');
            emptyOption.value = emptyValue;
            emptyOption.textContent = placeholder;
            selectElement.appendChild(emptyOption);
        }

        // Add items
        items.forEach(item => {
            const option = document.createElement('option');
            option.value = item[valueField];
            option.textContent = item[textField];
            selectElement.appendChild(option);
        });

        this.log(`✅ Populated dropdown with ${items.length} items`);
    }

    /**
     * Setup cascade dropdowns (Helper function)
     */
    setupCascadeDropdowns(config) {
        const {
            ulkeSelect,
            ilSelect,
            ilceSelect,
            mahalleSelect
        } = config;

        // Ülke change
        if (ulkeSelect) {
            const ulkeEl = typeof ulkeSelect === 'string'
                ? document.querySelector(ulkeSelect)
                : ulkeSelect;

            ulkeEl?.addEventListener('change', async (e) => {
                const ulkeId = e.target.value;
                const iller = await this.selectUlke(ulkeId);

                if (ilSelect) {
                    this.populateDropdown(ilSelect, iller, {
                        textField: 'il_adi',
                        placeholder: 'İl seçiniz...'
                    });
                }

                // Reset child dropdowns
                if (ilceSelect) this.populateDropdown(ilceSelect, [], { placeholder: 'Önce il seçiniz...' });
                if (mahalleSelect) this.populateDropdown(mahalleSelect, [], { placeholder: 'Önce ilçe seçiniz...' });
            });
        }

        // İl change
        if (ilSelect) {
            const ilEl = typeof ilSelect === 'string'
                ? document.querySelector(ilSelect)
                : ilSelect;

            ilEl?.addEventListener('change', async (e) => {
                const ilId = e.target.value;
                const ilceler = await this.selectIl(ilId);

                if (ilceSelect) {
                    this.populateDropdown(ilceSelect, ilceler, {
                        textField: 'ilce_adi',
                        placeholder: 'İlçe seçiniz...'
                    });
                }

                // Reset child dropdown
                if (mahalleSelect) this.populateDropdown(mahalleSelect, [], { placeholder: 'Önce ilçe seçiniz...' });
            });
        }

        // İlçe change
        if (ilceSelect) {
            const ilceEl = typeof ilceSelect === 'string'
                ? document.querySelector(ilceSelect)
                : ilceSelect;

            ilceEl?.addEventListener('change', async (e) => {
                const ilceId = e.target.value;
                const mahalleler = await this.selectIlce(ilceId);

                if (mahalleSelect) {
                    this.populateDropdown(mahalleSelect, mahalleler, {
                        textField: 'mahalle_adi',
                        placeholder: 'Mahalle seçiniz...'
                    });
                }
            });
        }

        this.log('✅ Cascade dropdowns configured');
    }

    /**
     * Show toast notification
     * @private
     */
    showToast(message, type = 'info') {
        if (!this.options.enableToast) return;

        if (window.toast && typeof window.toast[type] === 'function') {
            window.toast[type](message);
        } else {
            console.log(`[${type.toUpperCase()}] ${message}`);
        }
    }

    /**
     * Debug logging
     * @private
     */
    log(...args) {
        if (this.options.debug) {
            console.log('[Context7 Location]', ...args);
        }
    }

    /**
     * Error logging
     * @private
     */
    error(...args) {
        console.error('[Context7 Location ERROR]', ...args);
    }
}

// Export for use
window.Context7LocationSystem = Context7LocationSystem;

// Auto-initialize global instance if needed
if (!window.locationSystem) {
    window.locationSystem = new Context7LocationSystem({
        debug: true,
        enableCache: true,
        autoInit: false // Manuel init için false
    });
}

console.log('✅ Context7 Location System loaded (v1.0.0)');

