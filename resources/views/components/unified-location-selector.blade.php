@props([
    'level' => 'basic', // basic|advanced
    'type' => 'admin', // admin|property
    'features' => [], // crud|search|nearby|map
    'selectedCountry' => '',
    'selectedProvince' => '',
    'selectedDistrict' => '',
    'selectedNeighborhood' => '',
    'countries' => [],
    'required' => false,
    'namePrefix' => '',
    'class' => '',
    'theme' => 'green' // green|blue|purple|orange
])

<div class="unified-location-selector {{ $class }}"
     x-data="unifiedLocationSelector({
        level: @js($level),
        type: @js($type),
        features: @js($features),
        selectedCountry: @js($selectedCountry),
        selectedProvince: @js($selectedProvince),
        selectedDistrict: @js($selectedDistrict),
        selectedNeighborhood: @js($selectedNeighborhood),
        namePrefix: @js($namePrefix),
        theme: @js($theme)
     })"
     x-init="init()">

    @if ($level === 'advanced')
        <!-- Level Badge -->
        <div class="neo-location-level-badge mb-4">
            <span class="level-badge bg-gradient-to-r from-purple-500 to-indigo-600 text-white px-3 py-1 rounded-full text-sm font-medium">
                🚀 Gelişmiş Konum Seçici
            </span>
        </div>

        <!-- Smart Search Input -->
        <div class="neo-location-search mb-6">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                🏠 Akıllı Adres Arama @if ($required) <span class="text-red-500">*</span> @endif
            </label>

            <div class="relative">
                <input type="text"
                       x-model="smartSearch"
                       @input="performSmartSearch($event.target.value)"
                       @focus="showSuggestions = true"
                       class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200-smart w-full px-4 py-4 pr-12 border-2 border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 focus:border-{{ $theme }}-500 focus:ring-2 focus:ring-{{ $theme }}-200 transition-all"
                       placeholder="🏠 Bodrum Merkez, Gümbet, Bitez... veya tam adres yazın"
                       autocomplete="off">

                <!-- Search Icon -->
                <div class="absolute inset-y-0 right-0 flex items-center pr-4">
                    <svg x-show="!isSearching" class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <svg x-show="isSearching" class="animate-spin w-5 h-5 text-{{ $theme }}-500" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>

            <!-- Smart Suggestions Dropdown -->
            <div x-show="showSuggestions && (suggestions.length > 0 || recentSearches.length > 0)"
                 x-transition
                 class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-xl shadow-xl max-h-80 overflow-y-auto">

                <!-- Recent Searches -->
                <div x-show="recentSearches.length > 0 && smartSearch.length === 0" class="p-3 border-b border-gray-200 dark:border-gray-600">
                    <div class="flex items-center mb-2">
                        <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Son Aramalar</span>
                    </div>
                    <template x-for="recent in recentSearches" :key="recent.id">
                        <button @click="selectLocation(recent)"
                                class="w-full text-left p-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg"
                                x-text="recent.name"></button>
                    </template>
                </div>

                <!-- Search Results -->
                <div x-show="suggestions.length > 0" class="p-2">
                    <template x-for="suggestion in suggestions" :key="suggestion.id">
                        <div @click="selectLocation(suggestion)"
                             class="flex items-center gap-2 p-2 rounded-md hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer flex items-center p-3 hover:bg-{{ $theme }}-50 dark:hover:bg-{{ $theme }}-900/20 cursor-pointer rounded-lg transition-colors">
                            <div class="w-10 h-10 bg-{{ $theme }}-500 rounded-full flex items-center justify-center mr-3 text-white font-semibold text-sm"
                                 x-text="suggestion.icon || '📍'"></div>
                            <div class="flex-1">
                                <div class="font-medium text-gray-900 dark:text-white" x-text="suggestion.name"></div>
                                <div class="text-sm text-gray-500 dark:text-gray-400" x-text="suggestion.fullAddress"></div>
                            </div>
                            <div class="text-{{ $theme }}-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- No Results -->
                <div x-show="suggestions.length === 0 && smartSearch.length > 2 && !isSearching"
                     class="p-6 text-center text-gray-500 dark:text-gray-400">
                    <svg class="mx-auto h-8 w-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <div>Sonuç bulunamadı</div>
                    <button @click="createCustomLocation()" class="mt-2 text-{{ $theme }}-600 hover:text-{{ $theme }}-700 text-sm">
                        Manuel konum ekle
                    </button>
                </div>
            </div>
        </div>

        <!-- Popular Locations Grid -->
        <div x-show="popularLocations.length > 0" class="neo-popular-locations mb-6">
            <h3 class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                🔥 Popüler Lokasyonlar
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <template x-for="location in popularLocations" :key="location.id">
                    <button @click="selectLocation(location)"
                            class="neo-location-card p-4 bg-gradient-to-br from-{{ $theme }}-50 to-{{ $theme }}-100 dark:from-{{ $theme }}-900/20 dark:to-{{ $theme }}-800/20 border-2 border-transparent hover:border-{{ $theme }}-300 rounded-xl transition-all transform hover:scale-105">
                        <div class="text-2xl mb-2" x-text="location.icon"></div>
                        <div class="font-semibold text-gray-900 dark:text-white text-sm" x-text="location.name"></div>
                        <div class="text-xs text-gray-500 dark:text-gray-400" x-text="location.type"></div>
                    </button>
                </template>
            </div>
        </div>
    @endif

    @if ($level === 'basic' || in_array('hierarchy', $features))
        <!-- Traditional Hierarchy Selector -->
        <div class="neo-hierarchy-selector">
            <h3 class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                @if ($level === 'basic')
                    📋 Standart Konum Seçimi
                @else
                    📍 Detaylı Konum Bilgileri
                @endif
            </h3>

            <!-- Loading Indicator -->
            <div x-show="isLoading" x-transition class="mb-4 flex items-center space-x-2 text-{{ $theme }}-600">
                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-sm">Konum bilgileri yükleniyor...</span>
            </div>

            <!-- Error Message -->
            <div x-show="errorMessage" x-text="errorMessage" x-transition
                 class="mb-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 text-sm rounded-lg">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Ülke -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        🌍 Ülke @if ($required) <span class="text-red-500">*</span> @endif
                    </label>
                    <select x-model="selectedCountry"
                            @change="countryChanged()"
                            name="{{ $namePrefix }}ulke_id"
                            class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 cursor-pointer appearance-none w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-{{ $theme }}-500 focus:border-{{ $theme }}-500 transition-colors"
                            @if ($required) required @endif>
                        <option value="">Ülke Seçin</option>
                        @foreach ($countries as $country)
                            <option value="{{ $country->id }}" {{ $selectedCountry == $country->id ? 'selected' : '' }}>
                                {{ $country->name ?? $country->ulke_adi }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- İl -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        🏙️ İl @if ($required) <span class="text-red-500">*</span> @endif
                    </label>
                    <select x-model="selectedProvince"
                            @change="provinceChanged()"
                            name="{{ $namePrefix }}il_id"
                            class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 cursor-pointer appearance-none w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-{{ $theme }}-500 focus:border-{{ $theme }}-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                            :disabled="!selectedCountry || provinces.length === 0"
                            @if ($required) required @endif>
                        <option value="">İl Seçin</option>
                        <template x-for="province in provinces" :key="province.id">
                            <option :value="province.id" x-text="province.name || province.il_adi"></option>
                        </template>
                    </select>
                </div>

                <!-- İlçe -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        🏘️ İlçe @if ($required) <span class="text-red-500">*</span> @endif
                    </label>
                    <select x-model="selectedDistrict"
                            @change="districtChanged()"
                            name="{{ $namePrefix }}ilce_id"
                            class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 cursor-pointer appearance-none w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-{{ $theme }}-500 focus:border-{{ $theme }}-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                            :disabled="!selectedProvince || districts.length === 0"
                            @if ($required) required @endif>
                        <option value="">İlçe Seçin</option>
                        <template x-for="district in districts" :key="district.id">
                            <option :value="district.id" x-text="district.name || district.ilce_adi"></option>
                        </template>
                    </select>
                </div>

                <!-- Mahalle -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        🏠 Mahalle
                    </label>
                    <select x-model="selectedNeighborhood"
                            @change="neighborhoodChanged()"
                            name="{{ $namePrefix }}mahalle_id"
                            class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 cursor-pointer appearance-none w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-{{ $theme }}-500 focus:border-{{ $theme }}-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                            :disabled="!selectedDistrict || neighborhoods.length === 0">
                        <option value="">Mahalle Seçin</option>
                        <template x-for="neighborhood in neighborhoods" :key="neighborhood.id">
                            <option :value="neighborhood.id" x-text="neighborhood.name || neighborhood.mahalle_adi"></option>
                        </template>
                    </select>
                </div>
            </div>
        </div>
    @endif

    @if ($level === 'advanced' && in_array('nearby', $features))
        <!-- Nearby Services Section -->
        <div x-show="selectedLocation" class="neo-nearby-services mt-8 p-6 bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-blue-900/20 dark:to-indigo-800/20 rounded-xl border border-blue-200 dark:border-blue-800">
            <div class="neo-services-header mb-4">
                <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-200 mb-2">🎯 Yakınında Neler Var?</h3>
                <div class="neo-distance-filter flex space-x-2">
                    <button @click="nearbyRadius = 500"
                            :class="nearbyRadius === 500 ? 'bg-blue-500 text-white' : 'bg-white text-blue-500'"
                            class="px-3 py-1 rounded-full text-sm font-medium border border-blue-300 transition-colors">
                        500m
                    </button>
                    <button @click="nearbyRadius = 1000"
                            :class="nearbyRadius === 1000 ? 'bg-blue-500 text-white' : 'bg-white text-blue-500'"
                            class="px-3 py-1 rounded-full text-sm font-medium border border-blue-300 transition-colors">
                        1km
                    </button>
                    <button @click="nearbyRadius = 2000"
                            :class="nearbyRadius === 2000 ? 'bg-blue-500 text-white' : 'bg-white text-blue-500'"
                            class="px-3 py-1 rounded-full text-sm font-medium border border-blue-300 transition-colors">
                        2km
                    </button>
                </div>
            </div>

            <div x-show="nearbyServices.length > 0" class="neo-services-grid grid grid-cols-2 md:grid-cols-4 gap-4">
                <template x-for="service in nearbyServices" :key="service.type">
                    <div class="neo-service-item bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                        <div class="text-center">
                            <div class="text-2xl mb-2" x-text="service.icon"></div>
                            <div class="font-semibold text-sm text-gray-900 dark:text-white" x-text="service.name"></div>
                            <div class="text-xs text-gray-500 dark:text-gray-400" x-text="service.distance + 'm'"></div>
                            <div class="text-xs text-blue-600 dark:text-blue-400" x-text="service.count + ' adet'"></div>
                        </div>
                    </div>
                </template>
            </div>

            <div x-show="nearbyServices.length === 0" class="text-center py-6 text-gray-500 dark:text-gray-400">
                <div class="text-4xl mb-2">🔍</div>
                <div>Yakındaki hizmetler yükleniyor...</div>
            </div>
        </div>
    @endif

    <!-- Hidden Inputs -->
    <input type="hidden" name="{{ $namePrefix }}latitude" x-model="coordinates.latitude">
    <input type="hidden" name="{{ $namePrefix }}longitude" x-model="coordinates.longitude">
    <input type="hidden" name="{{ $namePrefix }}formatted_address" x-model="formattedAddress">
</div>

@push('scripts')
<script>
function unifiedLocationSelector(config) {
    return {
        // Config
        level: config.level,
        type: config.type,
        features: config.features,
        theme: config.theme,
        namePrefix: config.namePrefix,

        // State
        selectedCountry: config.selectedCountry || '',
        selectedProvince: config.selectedProvince || '',
        selectedDistrict: config.selectedDistrict || '',
        selectedNeighborhood: config.selectedNeighborhood || '',

        // Smart search
        smartSearch: '',
        showSuggestions: false,
        suggestions: [],
        recentSearches: [],
        popularLocations: [],
        selectedLocation: null,

        // Hierarchy data
        provinces: [],
        districts: [],
        neighborhoods: [],

        // Loading states
        isLoading: false,
        isSearching: false,
        errorMessage: '',

        // Nearby services
        nearbyServices: [],
        nearbyRadius: 1000,

        // Coordinates
        coordinates: {
            latitude: '',
            longitude: ''
        },
        formattedAddress: '',

        init() {
            this.loadPopularLocations();
            this.loadRecentSearches();

            if (this.selectedCountry) {
                this.loadProvinces();
            }
            if (this.selectedProvince) {
                this.loadDistricts();
            }
            if (this.selectedDistrict) {
                this.loadNeighborhoods();
            }
        },

        loadPopularLocations() {
            this.popularLocations = [
                { id: 'bodrum-merkez', name: 'Bodrum Merkez', type: 'Merkez', icon: '🏖️' },
                { id: 'gumbet', name: 'Gümbet', type: 'Sahil', icon: '🏊' },
                { id: 'bitez', name: 'Bitez', type: 'Sakin', icon: '🌅' },
                { id: 'ortakent', name: 'Ortakent', type: 'Gelişen', icon: '🏡' }
            ];
        },

        loadRecentSearches() {
            const stored = localStorage.getItem('neo_location_recent_searches');
            this.recentSearches = stored ? JSON.parse(stored) : [];
        },

        async performSmartSearch(query) {
            if (query.length < 3) {
                this.suggestions = [];
                return;
            }

            this.isSearching = true;
            try {
                const response = await fetch(`/api/locations/smart-search?q=${encodeURIComponent(query)}`);
                const data = await response.json();
                this.suggestions = data.suggestions || [];
            } catch (error) {
                console.error('Smart search error:', error);
                this.suggestions = [];
            } finally {
                this.isSearching = false;
            }
        },

        selectLocation(location) {
            this.selectedLocation = location;
            this.smartSearch = location.name;
            this.showSuggestions = false;
            this.formattedAddress = location.fullAddress || location.name;

            if (location.coordinates) {
                this.coordinates.latitude = location.coordinates.lat;
                this.coordinates.longitude = location.coordinates.lng;
            }

            this.saveToRecentSearches(location);
            this.triggerLocationUpdate();

            if (this.level === 'advanced' && this.features.includes('nearby')) {
                this.loadNearbyServices();
            }
        },

        saveToRecentSearches(location) {
            this.recentSearches = this.recentSearches.filter(item => item.id !== location.id);
            this.recentSearches.unshift(location);
            this.recentSearches = this.recentSearches.slice(0, 5);
            localStorage.setItem('neo_location_recent_searches', JSON.stringify(this.recentSearches));
        },

        // Hierarchy methods
        async countryChanged() {
            this.selectedProvince = '';
            this.selectedDistrict = '';
            this.selectedNeighborhood = '';
            this.provinces = [];
            this.districts = [];
            this.neighborhoods = [];

            if (this.selectedCountry) {
                await this.loadProvinces();
            }
            this.triggerLocationUpdate();
        },

        async provinceChanged() {
            this.selectedDistrict = '';
            this.selectedNeighborhood = '';
            this.districts = [];
            this.neighborhoods = [];

            if (this.selectedProvince) {
                await this.loadDistricts();
            }
            this.triggerLocationUpdate();
        },

        async districtChanged() {
            this.selectedNeighborhood = '';
            this.neighborhoods = [];

            if (this.selectedDistrict) {
                await this.loadNeighborhoods();
            }
            this.triggerLocationUpdate();
        },

        neighborhoodChanged() {
            this.triggerLocationUpdate();
        },

        async loadProvinces() {
            if (!this.selectedCountry) return;

            this.isLoading = true;
            try {
                const response = await fetch(`/api/locations/provinces?country_id=${this.selectedCountry}`);
                const data = await response.json();
                this.provinces = data.success ? data.provinces : [];
            } catch (error) {
                console.error('Provinces loading error:', error);
                this.showError('İller yüklenirken hata oluştu');
            } finally {
                this.isLoading = false;
            }
        },

        async loadDistricts() {
            if (!this.selectedProvince) return;

            this.isLoading = true;
            try {
                const response = await fetch(`/api/locations/districts?province_id=${this.selectedProvince}`);
                const data = await response.json();
                this.districts = data.success ? data.districts : [];
            } catch (error) {
                console.error('Districts loading error:', error);
                this.showError('İlçeler yüklenirken hata oluştu');
            } finally {
                this.isLoading = false;
            }
        },

        async loadNeighborhoods() {
            if (!this.selectedDistrict) return;

            this.isLoading = true;
            try {
                const response = await fetch(`/api/locations/neighborhoods?district_id=${this.selectedDistrict}`);
                const data = await response.json();
                this.neighborhoods = data.success ? data.neighborhoods : [];
            } catch (error) {
                console.error('Neighborhoods loading error:', error);
                this.showError('Mahalleler yüklenirken hata oluştu');
            } finally {
                this.isLoading = false;
            }
        },

        async loadNearbyServices() {
            if (!this.selectedLocation || !this.coordinates.latitude) return;

            try {
                const response = await fetch(`/api/locations/nearby-services?lat=${this.coordinates.latitude}&lng=${this.coordinates.longitude}&radius=${this.nearbyRadius}`);
                const data = await response.json();
                this.nearbyServices = data.services || [];
            } catch (error) {
                console.error('Nearby services error:', error);
            }
        },

        showError(message) {
            this.errorMessage = message;
            setTimeout(() => {
                this.errorMessage = '';
            }, 5000);
        },

        triggerLocationUpdate() {
            const locationData = {
                country: this.selectedCountry,
                province: this.selectedProvince,
                district: this.selectedDistrict,
                neighborhood: this.selectedNeighborhood,
                coordinates: this.coordinates,
                formattedAddress: this.formattedAddress
            };

            window.dispatchEvent(new CustomEvent('unifiedLocationChanged', {
                detail: locationData
            }));

            // Legacy compatibility
            if (window.updateLocationMap) {
                window.updateLocationMap(locationData);
            }
        },

        createCustomLocation() {
            // Implementation for custom location creation
            console.log('Create custom location:', this.smartSearch);
        }
    }
}
</script>
@endpush
