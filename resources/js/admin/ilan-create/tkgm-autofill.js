// ============================================================================
// 🗺️ TKGM AUTO-FILL MODULE
// ============================================================================
// Context7 Standardı: C7-TKGM-AUTOFILL-2025-12-03
// Yalıhan Bekçi: Gemini AI Önerisi - TKGM entegrasyonu
//
// Amaç: Ada/Parsel girildiğinde arsa bilgilerini otomatik doldurmak
// Reverse Engineering: AraziPro.com.tr analizi
// Hedef: %500 form verimliliği artışı
// ============================================================================

console.log('🗺️ TKGM Auto-fill module loaded');

/**
 * TKGM Auto-fill Manager
 * Ada/Parsel blur event → API call → Form auto-fill
 */
class TKGMAutoFillManager {
    constructor() {
        this.isProcessing = false;
        this.controller = null; // AbortController for timeout
        this.timeout = 6000; // 6 seconds

        this.init();
    }

    /**
     * ✅ tkgm-7: Initialize blur event listeners
     */
    init() {
        console.log('🚀 TKGM Auto-fill initializing...');

        // Find Ada and Parsel inputs
        const adaInput =
            document.querySelector('[name="ada_no"]') || document.getElementById('ada_no');
        const parselInput =
            document.querySelector('[name="parsel_no"]') || document.getElementById('parsel_no');

        if (!adaInput || !parselInput) {
            console.warn('⚠️ Ada/Parsel inputs not found. TKGM auto-fill disabled.');
            return;
        }

        // ✅ tkgm-7: Blur event listeners
        adaInput.addEventListener('blur', () => this.handleBlur());
        parselInput.addEventListener('blur', () => this.handleBlur());

        console.log('✅ TKGM Auto-fill event listeners attached');
    }

    /**
     * ✅ tkgm-7 & tkgm-8: Handle blur event
     */
    async handleBlur() {
        // Prevent duplicate calls
        if (this.isProcessing) {
            console.log('⏳ TKGM query already in progress...');
            return;
        }

        // Get form values
        const il = this.getIlName();
        const ilce = this.getIlceName();
        const ada =
            document.querySelector('[name="ada_no"]')?.value ||
            document.getElementById('ada_no')?.value;
        const parsel =
            document.querySelector('[name="parsel_no"]')?.value ||
            document.getElementById('parsel_no')?.value;

        // Validate inputs
        if (!il || !ilce || !ada || !parsel) {
            console.log('ℹ️ İl, İlçe, Ada veya Parsel eksik. TKGM sorgusu yapılamıyor.');
            return;
        }

        // Start processing
        this.isProcessing = true;

        try {
            // ✅ tkgm-8: Show loading state
            this.showLoading('TKGM verisi kontrol ediliyor...');

            // ✅ tkgm-8: Create AbortController for 6 sec timeout
            this.controller = new AbortController();
            const timeoutId = setTimeout(() => this.controller.abort(), this.timeout);

            // Make API request
            const response = await fetch('/api/v1/properties/tkgm-lookup', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN':
                        document.querySelector('meta[name="csrf-token"]')?.content || '',
                    Accept: 'application/json',
                },
                body: JSON.stringify({ il, ilce, ada, parsel }),
                signal: this.controller.signal,
            });

            clearTimeout(timeoutId);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success && data.data) {
                // ✅ tkgm-9: Auto-fill form
                this.autoFillForm(data.data);

                // ✅ tkgm-10: Add map marker
                this.addMapMarker(data.data);

                // Success message
                const cacheStatus = data.data.cache_status === 'hit' ? ' (Cache)' : '';
                this.showSuccess(`✅ TKGM verileri yüklendi${cacheStatus}`);
            } else {
                this.showWarning('⚠️ Parsel bilgisi bulunamadı. Lütfen manuel girin.');
            }
        } catch (error) {
            if (error.name === 'AbortError') {
                // ✅ tkgm-8: Timeout handler
                this.showWarning('⚠️ Servis gecikmesi: Lütfen manuel girin (6s timeout)');
            } else {
                console.error('❌ TKGM API error:', error);
                this.showError('❌ TKGM bağlantı hatası. Lütfen manuel girin.');
            }
        } finally {
            this.isProcessing = false;
            this.hideLoading();
        }
    }

    /**
     * ✅ tkgm-9: Auto-fill form fields
     */
    autoFillForm(data) {
        console.log('📝 Auto-filling form with TKGM data:', data);

        // Arsa specific fields (16 fields)
        const fieldMap = {
            // Temel bilgiler
            alan_m2: data.alan_m2,
            nitelik: data.nitelik,
            imar_statusu: data.imar_statusu || data.imar_durumu,

            // İmar bilgileri
            kaks: data.kaks,
            taks: data.taks,
            gabari: data.gabari,

            // Koordinatlar
            latitude: data.center_lat || data.enlem,
            longitude: data.center_lng || data.boylam,
            enlem: data.center_lat || data.enlem,
            boylam: data.center_lng || data.boylam,

            // Altyapı (checkboxes)
            yola_cephe: data.yola_cephe,
            altyapi_elektrik: data.altyapi_elektrik,
            altyapi_su: data.altyapi_su,
            altyapi_dogalgaz: data.altyapi_dogalgaz,

            // Diğer
            tapu_durumu: data.tapu_durumu,
            yol_durumu: data.yol_durumu,
        };

        let filledCount = 0;

        for (const [fieldName, value] of Object.entries(fieldMap)) {
            if (value === null || value === undefined) continue;

            const input =
                document.querySelector(`[name="${fieldName}"]`) ||
                document.getElementById(fieldName);

            if (!input) {
                console.log(`⚠️ Field not found: ${fieldName}`);
                continue;
            }

            // Handle different input types
            if (input.type === 'checkbox') {
                input.checked = !!value;
            } else if (input.tagName === 'SELECT') {
                // Select dropdown
                const option = Array.from(input.options).find(
                    (opt) => opt.value === value || opt.text === value
                );
                if (option) {
                    input.value = option.value;
                }
            } else {
                // Text/number input
                input.value = value;
            }

            // Trigger change event for Alpine.js/other listeners
            input.dispatchEvent(new Event('change', { bubbles: true }));

            filledCount++;
        }

        console.log(`✅ ${filledCount} alan otomatik dolduruldu`);
    }

    /**
     * ✅ tkgm-10: Add marker to map
     */
    addMapMarker(data) {
        const lat = data.center_lat || data.enlem;
        const lng = data.center_lng || data.boylam;

        if (!lat || !lng) {
            console.log('⚠️ GPS koordinatları bulunamadı, harita marker eklenemedi');
            return;
        }

        // Check if Leaflet map exists (from location.js)
        if (typeof window.leafletMap !== 'undefined' && window.leafletMap) {
            try {
                // Remove existing marker
                if (window.currentMarker) {
                    window.leafletMap.removeLayer(window.currentMarker);
                }

                // Add new marker
                window.currentMarker = L.marker([lat, lng]).addTo(window.leafletMap);
                window.currentMarker
                    .bindPopup(`Ada ${data.ada_no} Parsel ${data.parsel_no}`)
                    .openPopup();

                // Center map on marker
                window.leafletMap.setView([lat, lng], 16);

                console.log(`✅ Harita marker eklendi: ${lat}, ${lng}`);
            } catch (error) {
                console.error('❌ Harita marker error:', error);
            }
        } else if (typeof VanillaLocationManager !== 'undefined') {
            // Try VanillaLocationManager instance
            const locationManager = window.locationManagerInstance;
            if (locationManager && locationManager.map) {
                locationManager.setMarker([lat, lng]);
                locationManager.map.setView([lat, lng], 16);
                console.log(`✅ Harita marker eklendi (VanillaLocationManager): ${lat}, ${lng}`);
            }
        } else {
            console.log('⚠️ Harita instance bulunamadı');
        }
    }

    /**
     * Get İl name from select dropdown
     */
    getIlName() {
        const ilSelect =
            document.querySelector('[name="il_id"]') || document.getElementById('il_id');
        if (!ilSelect) return null;
        return ilSelect.options[ilSelect.selectedIndex]?.text || null;
    }

    /**
     * Get İlçe name from select dropdown
     */
    getIlceName() {
        const ilceSelect =
            document.querySelector('[name="ilce_id"]') || document.getElementById('ilce_id');
        if (!ilceSelect) return null;
        return ilceSelect.options[ilceSelect.selectedIndex]?.text || null;
    }

    /**
     * ✅ tkgm-8: Show loading animation
     */
    showLoading(message = 'Yükleniyor...') {
        // Create loading overlay if not exists
        let overlay = document.getElementById('tkgm-loading-overlay');

        if (!overlay) {
            overlay = document.createElement('div');
            overlay.id = 'tkgm-loading-overlay';
            overlay.className =
                'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
            overlay.innerHTML = `
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-xl flex items-center space-x-4">
                    <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-gray-900 dark:text-white font-medium" id="tkgm-loading-message">${message}</span>
                </div>
            `;
            document.body.appendChild(overlay);
        } else {
            overlay.style.display = 'flex';
            overlay.querySelector('#tkgm-loading-message').textContent = message;
        }
    }

    /**
     * Hide loading animation
     */
    hideLoading() {
        const overlay = document.getElementById('tkgm-loading-overlay');
        if (overlay) {
            overlay.style.display = 'none';
        }
    }

    /**
     * Show success toast
     */
    showSuccess(message) {
        this.showToast(message, 'success');
    }

    /**
     * Show warning toast
     */
    showWarning(message) {
        this.showToast(message, 'warning');
    }

    /**
     * Show error toast
     */
    showError(message) {
        this.showToast(message, 'error');
    }

    /**
     * Generic toast notification (Tailwind CSS)
     */
    showToast(message, type = 'info') {
        const colors = {
            success: 'bg-green-500',
            warning: 'bg-yellow-500',
            error: 'bg-red-500',
            info: 'bg-blue-500',
        };

        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 ${colors[type]} text-white px-6 py-4 rounded-lg shadow-lg z-50 
                          transform transition-all duration-300 ease-in-out`;
        toast.textContent = message;

        document.body.appendChild(toast);

        // Animate in
        setTimeout(() => toast.classList.add('translate-x-0'), 10);

        // Remove after 5 seconds
        setTimeout(() => {
            toast.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    }
}

// ============================================================================
// AUTO-INITIALIZE
// ============================================================================

// Wait for DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.tkgmAutoFillManager = new TKGMAutoFillManager();
    });
} else {
    // DOM already ready
    window.tkgmAutoFillManager = new TKGMAutoFillManager();
}
