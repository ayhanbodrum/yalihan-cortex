@props([
    'mapId' => 'map',
    'latField' => 'enlem',
    'lngField' => 'boylam',
    'addressField' => 'adres',
    'structuredFields' => [
        'street' => 'sokak',
        'avenue' => 'cadde',
        'boulevard' => 'bulvar',
        'building' => 'bina_no',
        'postalCode' => 'posta_kodu',
    ],
    'height' => '500px',
])

<div class="relative group">
    <div id="{{ $mapId }}" data-lat-field="{{ $latField }}" data-lng-field="{{ $lngField }}"
        data-address-field="{{ $addressField }}" data-structured-fields='@json($structuredFields)'
        class="w-full rounded-2xl border-4 border-white dark:border-gray-700 overflow-hidden shadow-2xl ring-4 ring-green-500/10"
        role="application" aria-label="Harita" style="height: {{ $height }};">
        <div class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800"
            role="status" aria-live="polite" aria-busy="true">
            <div class="text-center">
                <div
                    class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-white dark:bg-gray-800 shadow-xl mb-4">
                    <svg class="w-10 h-10 text-green-500 animate-pulse" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                    </svg>
                </div>
                <p class="text-gray-600 dark:text-gray-400 font-medium">Harita yükleniyor...</p>
                <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">OpenStreetMap</p>
            </div>
        </div>
    </div>

    {{-- Harita Kontrol Paneli - Minimal (Sadece Standart/Uydu Toggle) --}}
    <div class="absolute top-4 right-4 pointer-events-auto z-[9999]">
        {{-- Harita Tipi Seçici (Minimal) --}}
        <div
            class="bg-white/95 dark:bg-gray-800/95 backdrop-blur-md rounded-xl shadow-xl border-2 border-gray-200 dark:border-gray-700 p-1.5">
            <div class="flex gap-1" role="toolbar" aria-label="Harita görünüm seçici">
                <button type="button"
                    id="btn-map-standard"
                    data-map-type="standard"
                    class="flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg transition-all duration-200 text-xs font-bold bg-gradient-to-br from-blue-500 to-blue-600 text-white shadow-lg hover:shadow-xl hover:scale-105 active:scale-95 motion-reduce:transform-none motion-reduce:transition-none"
                    title="Standart Harita" aria-label="Standart Harita" aria-controls="{{ $mapId }}"
                    aria-pressed="true">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                    </svg>
                    <span class="hidden sm:inline">Standart</span>
                </button>
                <button type="button"
                    id="btn-map-satellite"
                    data-map-type="satellite"
                    class="flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg transition-all duration-200 text-xs font-bold text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 hover:scale-105 active:scale-95 motion-reduce:transform-none motion-reduce:transition-none"
                    title="Uydu Görüntüsü" aria-label="Uydu Görüntüsü" aria-controls="{{ $mapId }}"
                    aria-pressed="false">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="hidden sm:inline">Uydu</span>
                </button>
            </div>
        </div>
    </div>
    <script>
        (function() {
            const el = document.getElementById(@json($mapId));
            if (!el) return;
            
            // ✅ Map type toggle buttons - Event listeners (Context7: No inline onclick)
            const initMapTypeButtons = () => {
                const standardBtn = document.getElementById('btn-map-standard');
                const satelliteBtn = document.getElementById('btn-map-satellite');
                
                if (!standardBtn || !satelliteBtn) return;
                
                const handleMapTypeChange = (type) => {
                    if (!window.VanillaLocationManager || typeof window.VanillaLocationManager.setMapType !== 'function') {
                        console.warn('⚠️ VanillaLocationManager not ready yet');
                        return;
                    }
                    
                    try {
                        const isStandard = type === 'standard';
                        
                        // Update aria-pressed attributes
                        standardBtn.setAttribute('aria-pressed', isStandard ? 'true' : 'false');
                        satelliteBtn.setAttribute('aria-pressed', isStandard ? 'false' : 'true');
                        
                        // Base classes (same for both buttons)
                        const baseClasses = 'flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg transition-all duration-200 text-xs font-bold shadow-lg hover:shadow-xl hover:scale-105 active:scale-95 motion-reduce:transform-none motion-reduce:transition-none';
                        
                        // Active state: blue gradient
                        const activeClasses = baseClasses + ' bg-gradient-to-br from-blue-500 to-blue-600 text-white';
                        // Inactive state: gray
                        const inactiveClasses = baseClasses + ' bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600';
                        
                        // Apply styles based on state
                        if (isStandard) {
                            standardBtn.className = activeClasses;
                            satelliteBtn.className = inactiveClasses;
                        } else {
                            satelliteBtn.className = activeClasses;
                            standardBtn.className = inactiveClasses;
                        }
                        
                        window.VanillaLocationManager.setMapType(type);
                    } catch (error) {
                        console.error('❌ Error updating map type:', error);
                    }
                };
                
                standardBtn.addEventListener('click', () => handleMapTypeChange('standard'));
                satelliteBtn.addEventListener('click', () => handleMapTypeChange('satellite'));
            };
            
            // ✅ Initialize map type buttons when VanillaLocationManager is ready
            const waitForVanillaLocationManager = () => {
                if (window.VanillaLocationManager) {
                    initMapTypeButtons();
                } else {
                    // Retry after 100ms if not ready yet
                    setTimeout(waitForVanillaLocationManager, 100);
                }
            };
            
            const init = () => {
                if (window.VanillaLocationManager && typeof window.VanillaLocationManager.initMap === 'function') {
                    window.VanillaLocationManager.initMap(@json($mapId));
                } else {
                    el.dispatchEvent(new CustomEvent('map:visible', {
                        detail: {
                            id: @json($mapId)
                        }
                    }));
                }
            };
            
            // ✅ Initialize button listeners on DOMContentLoaded
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', waitForVanillaLocationManager);
            } else {
                // DOM already loaded, start immediately
                waitForVanillaLocationManager();
            }
            
            if ('IntersectionObserver' in window) {
                const io = new IntersectionObserver((entries) => {
                    entries.forEach(e => {
                        if (e.isIntersecting) {
                            init();
                            io.disconnect();
                        }
                    });
                }, {
                    rootMargin: '0px',
                    threshold: 0.1
                });
                io.observe(el);
            } else {
                init();
            }
        })();
    </script>
</div>
