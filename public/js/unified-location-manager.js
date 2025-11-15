/**
 * Unified Location Manager
 * TurkiyeAPI + WikiMapia Integration
 *
 * Features:
 * - İl/İlçe/Mahalle/Belde/Köy cascade
 * - WikiMapia site suggestions
 * - Environmental scoring
 * - Smart recommendations
 *
 * Context7: Enhanced location intelligence
 */

class UnifiedLocationManager {
    constructor(options = {}) {
        this.config = {
            ilSelectId: options.ilSelectId || 'il_id',
            ilceSelectId: options.ilceSelectId || 'ilce_id',
            locationSelectId: options.locationSelectId || 'location_id',
            locationTypeSelectId: options.locationTypeSelectId || 'location_type',
            mapContainerId: options.mapContainerId || 'map',
            onLocationSelect: options.onLocationSelect || null,
            ...options,
        };

        this.state = {
            selectedIl: null,
            selectedIlce: null,
            selectedLocation: null,
            locationType: null,
            currentProfile: null,
            nearbySites: [],
        };

        this.init();
    }

    init() {
        this.bindEvents();
        console.log('✅ Unified Location Manager initialized');
    }

    bindEvents() {
        // İlçe seçilince → Mahalle/Belde/Köy dropdown yükle
        const ilceSelect = document.getElementById(this.config.ilceSelectId);
        if (ilceSelect) {
            ilceSelect.addEventListener('change', (e) => {
                this.loadAllLocationTypes(e.target.value);
            });
        }

        // Lokasyon seçilince → WikiMapia profile yükle
        const locationSelect = document.getElementById(this.config.locationSelectId);
        if (locationSelect) {
            locationSelect.addEventListener('change', (e) => {
                this.onLocationSelected(e.target.value);
            });
        }
    }

    /**
     * Load all location types (Mahalle + Belde + Köy)
     * TurkiyeAPI Integration
     */
    async loadAllLocationTypes(ilceId) {
        if (!ilceId) return;

        try {
            const response = await fetch(`/api/location/all-types/${ilceId}`);
            const result = await response.json();

            if (result.success) {
                this.populateLocationDropdown(result.data);
                this.showLocationStats(result.counts);
            }
        } catch (error) {
            console.error('Load all location types error:', error);
            if (window.toast) {
                window.toast('error', 'Lokasyon verileri yüklenemedi');
            }
        }
    }

    /**
     * Populate location dropdown with optgroups
     */
    populateLocationDropdown(data) {
        const select = document.getElementById(this.config.locationSelectId);
        if (!select) return;

        let html = '<option value="">Konum Seçin...</option>';

        // Mahalleler
        if (data.neighborhoods && data.neighborhoods.length > 0) {
            html += '<optgroup label="📍 Mahalleler">';
            data.neighborhoods.forEach((n) => {
                const population = n.population ? ` (👥 ${this.formatNumber(n.population)})` : '';
                html += `<option value="mahalle_${n.id}" data-type="mahalle" data-population="${n.population || 0}">
                    ${n.name}${population}
                </option>`;
            });
            html += '</optgroup>';
        }

        // Beldeler (TATİL BÖLGELERİ!)
        if (data.towns && data.towns.length > 0) {
            html += '<optgroup label="🏖️ Beldeler (Tatil Bölgeleri)">';
            data.towns.forEach((t) => {
                const population = t.population ? ` (👥 ${this.formatNumber(t.population)})` : '';
                const coastal = t.is_coastal ? ' 🌊' : '';
                html += `<option value="belde_${t.id}" data-type="belde" data-population="${t.population || 0}" data-coastal="${t.is_coastal}">
                    ${t.name}${population}${coastal}
                </option>`;
            });
            html += '</optgroup>';
        }

        // Köyler (KIRSAL EMLAK!)
        if (data.villages && data.villages.length > 0) {
            html += '<optgroup label="🌾 Köyler (Kırsal Bölgeler)">';
            data.villages.forEach((v) => {
                const population = v.population ? ` (👥 ${this.formatNumber(v.population)})` : '';
                html += `<option value="koy_${v.id}" data-type="koy" data-population="${v.population || 0}">
                    ${v.name}${population}
                </option>`;
            });
            html += '</optgroup>';
        }

        select.innerHTML = html;
    }

    /**
     * Show location statistics
     */
    showLocationStats(counts) {
        const statsContainer = document.getElementById('location-stats');
        if (!statsContainer) return;

        statsContainer.innerHTML = `
            <div class="grid grid-cols-3 gap-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">${counts.neighborhoods}</div>
                    <div class="text-xs text-gray-600 dark:text-gray-400">Mahalle</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-orange-600">${counts.towns}</div>
                    <div class="text-xs text-gray-600 dark:text-gray-400">Belde</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">${counts.villages}</div>
                    <div class="text-xs text-gray-600 dark:text-gray-400">Köy</div>
                </div>
            </div>
        `;
    }

    /**
     * When location is selected
     */
    onLocationSelected(value) {
        if (!value) return;

        const select = document.getElementById(this.config.locationSelectId);
        const option = select.querySelector(`option[value="${value}"]`);

        if (option) {
            const type = option.dataset.type;
            const population = option.dataset.population;
            const coastal = option.dataset.coastal === 'true';

            this.state.selectedLocation = {
                value,
                type,
                name: option.textContent.trim(),
                population: parseInt(population) || 0,
                is_coastal: coastal,
            };

            this.showLocationInfo(this.state.selectedLocation);

            // Callback
            if (this.config.onLocationSelect) {
                this.config.onLocationSelect(this.state.selectedLocation);
            }
        }
    }

    /**
     * Show selected location info
     */
    showLocationInfo(location) {
        const infoContainer = document.getElementById('selected-location-info');
        if (!infoContainer) return;

        let badges = '';
        if (location.type === 'belde')
            badges += '<span class="badge bg-orange-100 text-orange-700">🏖️ Tatil Bölgesi</span>';
        if (location.is_coastal)
            badges += '<span class="badge bg-blue-100 text-blue-700">🌊 Kıyı</span>';
        if (location.type === 'koy')
            badges += '<span class="badge bg-green-100 text-green-700">🌾 Kırsal</span>';

        infoContainer.innerHTML = `
            <div class="bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800">
                <h4 class="font-semibold text-gray-900 dark:text-white mb-2">📍 Seçili Konum</h4>
                <div class="space-y-2 text-sm">
                    <div><strong>${location.name}</strong> ${badges}</div>
                    ${location.population > 0 ? `<div>👥 Nüfus: ${this.formatNumber(location.population)}</div>` : ''}
                </div>
            </div>
        `;
    }

    /**
     * Load location profile with WikiMapia
     */
    async loadLocationProfile(lat, lon, districtId) {
        try {
            const response = await fetch('/api/location/profile', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                },
                body: JSON.stringify({
                    lat,
                    lon,
                    district_id: districtId,
                }),
            });

            const result = await response.json();

            if (result.success) {
                this.state.currentProfile = result.data;
                this.displayProfile(result.data);
            }
        } catch (error) {
            console.error('Load location profile error:', error);
        }
    }

    /**
     * Display location profile
     */
    displayProfile(profile) {
        const container = document.getElementById('location-profile');
        if (!container) return;

        let html = `
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 space-y-6">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    🗺️ Lokasyon Profili
                </h3>
        `;

        // Scores
        if (profile.scores) {
            html += `
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-lg p-4 text-center">
                        <div class="text-3xl font-bold text-green-600">${profile.scores.walkability}</div>
                        <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">Yürünebilirlik</div>
                        <div class="text-xs text-gray-400">/100</div>
                    </div>

                    <div class="bg-gradient-to-br from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 rounded-lg p-4 text-center">
                        <div class="text-3xl font-bold text-blue-600">${profile.scores.convenience}</div>
                        <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">Kolaylık</div>
                    </div>

                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-lg p-4 text-center">
                        <div class="text-3xl font-bold text-purple-600">${profile.scores.family_friendly}</div>
                        <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">Aile Uygunluğu</div>
                    </div>

                    <div class="bg-gradient-to-br from-orange-50 to-red-50 dark:from-orange-900/20 dark:to-red-900/20 rounded-lg p-4 text-center">
                        <div class="text-3xl font-bold text-orange-600">${profile.scores.beach_proximity}</div>
                        <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">Plaja Yakınlık</div>
                    </div>
                </div>
            `;
        }

        // Environment summary
        if (profile.environment) {
            html += this.renderEnvironmentSummary(profile.environment);
        }

        // Suggestions
        if (profile.suggestions && profile.suggestions.length > 0) {
            html += this.renderSuggestions(profile.suggestions);
        }

        html += '</div>';
        container.innerHTML = html;
    }

    /**
     * Render environment summary
     */
    renderEnvironmentSummary(env) {
        let html =
            '<div class="space-y-2"><h4 class="font-semibold text-gray-900 dark:text-white">📊 Çevresel Özellikler</h4>';

        const categories = [
            { key: 'shopping', icon: '🛒', label: 'Alışveriş' },
            { key: 'education', icon: '🏫', label: 'Eğitim' },
            { key: 'health', icon: '🏥', label: 'Sağlık' },
            { key: 'social', icon: '🏊', label: 'Sosyal' },
            { key: 'transport', icon: '🚇', label: 'Ulaşım' },
            { key: 'food', icon: '🍽️', label: 'Yeme-İçme' },
        ];

        categories.forEach((cat) => {
            const places = env[cat.key] || [];
            const nearest = places[0];

            if (nearest) {
                html += `
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex items-center gap-2">
                            <span>${cat.icon}</span>
                            <span class="font-medium">${cat.label}</span>
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            ${nearest.name} (${nearest.distance}m)
                        </div>
                    </div>
                `;
            }
        });

        html += '</div>';
        return html;
    }

    /**
     * Render suggestions
     */
    renderSuggestions(suggestions) {
        let html =
            '<div class="space-y-2"><h4 class="font-semibold text-gray-900 dark:text-white">💡 Akıllı Öneriler</h4>';

        suggestions.forEach((s) => {
            const bgColor =
                s.type === 'positive'
                    ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800'
                    : s.type === 'warning'
                      ? 'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-200 dark:border-yellow-800'
                      : 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800';

            html += `
                <div class="${bgColor} border rounded-lg p-3 flex items-start gap-2">
                    <span>${s.icon}</span>
                    <span class="text-sm">${s.text}</span>
                </div>
            `;
        });

        html += '</div>';
        return html;
    }

    /**
     * Load nearby sites from WikiMapia
     */
    async loadNearbySites(lat, lon) {
        try {
            const response = await fetch('/api/location/nearest-sites', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                },
                body: JSON.stringify({ lat, lon, limit: 5 }),
            });

            const result = await response.json();

            if (result.success && result.data.length > 0) {
                this.state.nearbySites = result.data;
                this.displayNearbySites(result.data);
            }
        } catch (error) {
            console.error('Load nearby sites error:', error);
        }
    }

    /**
     * Display nearby sites
     */
    displayNearbySites(sites) {
        const container = document.getElementById('nearby-sites');
        if (!container) return;

        let html = `
            <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4 border border-purple-200 dark:border-purple-800">
                <h4 class="font-semibold text-gray-900 dark:text-white mb-3">🏘️ Yakındaki Siteler</h4>
                <div class="space-y-2">
        `;

        sites.forEach((site) => {
            html += `
                <button
                    type="button"
                    onclick="unifiedLocationManager.selectSite(${site.wikimapia_id}, '${site.name}')"
                    class="w-full p-3 bg-white dark:bg-gray-800 rounded-lg border-2 border-gray-200 dark:border-gray-700 hover:border-purple-500 dark:hover:border-purple-400 transition-all text-left group">
                    <div class="font-semibold text-gray-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400">${site.name}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        📏 ${site.distance}m mesafede
                    </div>
                </button>
            `;
        });

        html += '</div></div>';
        container.innerHTML = html;
    }

    /**
     * Select a site from WikiMapia
     */
    selectSite(wikimapiaId, siteName) {
        // Site bilgilerini forma doldur
        const siteNameInput =
            document.getElementById('site_name') || document.getElementById('wikimapia_site_name');
        if (siteNameInput) {
            siteNameInput.value = siteName;
        }

        const wikimapiaIdInput = document.getElementById('wikimapia_place_id');
        if (wikimapiaIdInput) {
            wikimapiaIdInput.value = wikimapiaId;
        }

        if (window.toast) {
            window.toast('success', `✅ ${siteName} seçildi!`);
        }

        // Highlight selected
        document.querySelectorAll('#nearby-sites button').forEach((btn) => {
            btn.classList.remove('border-purple-500', 'bg-purple-50');
        });
        event.target.closest('button').classList.add('border-purple-500', 'bg-purple-50');
    }

    /**
     * Format number with thousands separator
     */
    formatNumber(num) {
        return new Intl.NumberFormat('tr-TR').format(num);
    }
}

// Global instance
let unifiedLocationManager;

// Auto-initialize if elements exist
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('il_id') && document.getElementById('ilce_id')) {
        unifiedLocationManager = new UnifiedLocationManager({
            onLocationSelect: (location) => {
                console.log('📍 Location selected:', location);
            },
        });
    }
});
