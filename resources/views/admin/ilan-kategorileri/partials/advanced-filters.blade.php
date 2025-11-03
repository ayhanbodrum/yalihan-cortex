{{-- İlan Kategorileri - Gelişmiş Filtreleme Sistemi --}}
<div class="bg-gray-50 dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mb-6" id="advanced-filters">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                🔍 Gelişmiş Filtreleme
            </h3>
            <button type="button" id="toggle-filters" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
        </div>
    </div>

    <div class="px-6 py-4 space-y-4" id="filter-content">
        <form id="category-filters" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

                {{-- Arama Kutusu --}}
                <div class="space-y-2">
                    <label for="search" class="block text-sm font-medium text-gray-900 dark:text-white">
                        🔍 Kategori Ara
                    </label>
                    <div class="relative">
                        <input type="text"
                               id="search"
                               name="search"
                               class="w-full px-4 py-2 pr-10 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white"
                               placeholder="Kategori adında ara...">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Seviye Filtresi --}}
                <div class="space-y-2">
                    <label for="level_filter" class="block text-sm font-medium text-gray-900 dark:text-white">
                        📊 Seviye
                    </label>
                    <select style="color-scheme: light dark;" id="level_filter" name="level" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white transition-all duration-200">
                        <option value="">Tüm Seviyeler</option>
                        <option value="0">🏠 Ana Kategoriler</option>
                        <option value="1">🔸 Alt Kategoriler</option>
                        <option value="2">🏷️ Yayın Tipleri</option>
                    </select>
                </div>

                {{-- Aktiflik Durumu --}}
                <div class="space-y-2">
                    <label for="status_filter" class="block text-sm font-medium text-gray-900 dark:text-white">
                        ⚡ Durum
                    </label>
                    <select style="color-scheme: light dark;" id="status_filter" name="status" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white transition-all duration-200">
                        <option value="">Tüm Durumlar</option>
                        <option value="1">✅ Aktif</option>
                        <option value="0">❌ Pasif</option>
                    </select>
                </div>

                {{-- İlan Sayısı Aralığı --}}
                <div class="space-y-2">
                    <label for="listing_count_filter" class="block text-sm font-medium text-gray-900 dark:text-white">
                        📈 İlan Sayısı
                    </label>
                    <select style="color-scheme: light dark;" id="listing_count_filter" name="listing_count" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white transition-all duration-200">
                        <option value="">Tüm Sayılar</option>
                        <option value="0">📭 İlan Yok (0)</option>
                        <option value="1-10">📊 Az (1-10)</option>
                        <option value="11-50">📈 Orta (11-50)</option>
                        <option value="51-100">📊 Çok (51-100)</option>
                        <option value="100+">🔥 Çok Yüksek (100+)</option>
                    </select>
                </div>
            </div>

            {{-- Sıralama Seçenekleri --}}
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center space-x-4">
                    <div class="space-y-2">
                        <label for="sort_by" class="block text-sm font-medium text-gray-900 dark:text-white">
                            🔄 Sıralama
                        </label>
                        <select style="color-scheme: light dark;" id="sort_by" name="sort_by" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white transition-all duration-200">
                            <option value="order">🎯 Manuel Sıra</option>
                            <option value="name">🔤 Alfabetik</option>
                            <option value="created_at">📅 Oluşturma Tarihi</option>
                            <option value="listing_count">📊 İlan Sayısı</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label for="sort_direction" class="block text-sm font-medium text-gray-900 dark:text-white">
                            ↕️ Yön
                        </label>
                        <select style="color-scheme: light dark;" id="sort_direction" name="sort_direction" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white transition-all duration-200">
                            <option value="asc">⬆️ Artan</option>
                            <option value="desc">⬇️ Azalan</option>
                        </select>
                    </div>
                </div>

                {{-- Filter Actions --}}
                <div class="flex items-center space-x-2">
                    <button type="button" id="clear-filters" class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        🧹 Temizle
                    </button>
                    <button type="button" id="save-filter-preset" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors">
                        💾 Kaydet
                    </button>
                </div>
            </div>

            {{-- Aktif Filtreler Göstergesi --}}
            <div id="active-filters" class="hidden">
                <div class="flex flex-wrap items-center gap-2 pt-2">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Aktif filtreler:</span>
                    <div id="filter-tags" class="flex flex-wrap gap-2"></div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Saved Filter Presets --}}
<div class="mb-4" id="filter-presets">
    <div class="flex items-center space-x-2">
        <span class="text-sm text-gray-600 dark:text-gray-400">🔖 Hızlı Filtreler:</span>
        <button type="button" class="filter-preset px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-full transition-colors" data-preset="empty-categories">
            📭 Boş Kategoriler
        </button>
        <button type="button" class="filter-preset px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-full transition-colors" data-preset="inactive-categories">
            ❌ Pasif Kategoriler
        </button>
        <button type="button" class="filter-preset px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-full transition-colors" data-preset="popular-categories">
            🔥 Popüler Kategoriler
        </button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search');
    const levelFilter = document.getElementById('level_filter');
    const statusFilter = document.getElementById('status_filter');
    const listingCountFilter = document.getElementById('listing_count_filter');
    const sortBy = document.getElementById('sort_by');
    const sortDirection = document.getElementById('sort_direction');
    const clearFiltersBtn = document.getElementById('clear-filters');
    const savePresetBtn = document.getElementById('save-filter-preset');
    const toggleFiltersBtn = document.getElementById('toggle-filters');
    const filterContent = document.getElementById('filter-content');

    // Real-time search with debounce
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            applyFilters();
        }, 300);
    });

    // Filter change events
    [levelFilter, statusFilter, listingCountFilter, sortBy, sortDirection].forEach(element => {
        element.addEventListener('change', applyFilters);
    });

    // Clear filters
    clearFiltersBtn.addEventListener('click', function() {
        document.getElementById('category-filters').reset();
        applyFilters();
        updateActiveFilters();
    });

    // Toggle filters panel
    toggleFiltersBtn.addEventListener('click', function() {
        filterContent.classList.toggle('hidden');
        const icon = this.querySelector('svg path');
        if (filterContent.classList.contains('hidden')) {
            icon.setAttribute('d', 'M9 5l7 7-7 7');
        } else {
            icon.setAttribute('d', 'M19 9l-7 7-7-7');
        }
    });

    // Preset filters
    document.querySelectorAll('.filter-preset').forEach(btn => {
        btn.addEventListener('click', function() {
            const preset = this.dataset.preset;
            applyPreset(preset);
        });
    });

    function applyFilters() {
        const formData = new FormData(document.getElementById('category-filters'));
        const params = new URLSearchParams(formData);

        // AJAX request to filter categories
        fetch(`{{ route('admin.ilan-kategorileri.index') }}?${params.toString()}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            // Update category list
            document.getElementById('sortable-categories').innerHTML = data.html;
            // Update stats cards
            updateStatsCards(data.stats);
            // Update active filters display
            updateActiveFilters();
            // Reinitialize drag & drop
            initializeDragDrop();
        })
        .catch(error => {
            console.error('Filter error:', error);
            showNotification('error', 'Filtreleme sırasında bir hata oluştu.');
        });
    }

    function updateActiveFilters() {
        const activeFiltersContainer = document.getElementById('active-filters');
        const filterTags = document.getElementById('filter-tags');
        filterTags.innerHTML = '';

        let hasActiveFilters = false;

        // Search filter
        if (searchInput.value.trim()) {
            addFilterTag('Arama', searchInput.value.trim());
            hasActiveFilters = true;
        }

        // Level filter
        if (levelFilter.value) {
            const levelText = levelFilter.options[levelFilter.selectedIndex].text;
            addFilterTag('Seviye', levelText);
            hasActiveFilters = true;
        }

        // Status filter
        if (statusFilter.value) {
            const statusText = statusFilter.options[statusFilter.selectedIndex].text;
            addFilterTag('Durum', statusText);
            hasActiveFilters = true;
        }

        // Listing count filter
        if (listingCountFilter.value) {
            const countText = listingCountFilter.options[listingCountFilter.selectedIndex].text;
            addFilterTag('İlan Sayısı', countText);
            hasActiveFilters = true;
        }

        activeFiltersContainer.classList.toggle('hidden', !hasActiveFilters);
    }

    function addFilterTag(label, value) {
        const tag = document.createElement('span');
        tag.className = 'inline-flex items-center px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full';
        tag.innerHTML = `
            ${label}: ${value}
            <button type="button" class="ml-1 text-blue-600 hover:text-blue-800" onclick="this.parentElement.remove(); updateActiveFilters();">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        `;
        document.getElementById('filter-tags').appendChild(tag);
    }

    function applyPreset(preset) {
        // Clear current filters
        document.getElementById('category-filters').reset();

        switch (preset) {
            case 'empty-categories':
                listingCountFilter.value = '0';
                break;
            case 'inactive-categories':
                statusFilter.value = '0';
                break;
            case 'popular-categories':
                listingCountFilter.value = '51-100';
                sortBy.value = 'listing_count';
                sortDirection.value = 'desc';
                break;
        }

        applyFilters();
    }

    function updateStatsCards(stats) {
        // Update statistics cards with filtered results
        if (stats) {
            document.querySelector('.admin-blue .text-3xl').textContent = stats.total || 0;
            document.querySelector('.admin-green .text-3xl').textContent = stats.main_categories || 0;
            document.querySelector('.admin-purple .text-3xl').textContent = stats.sub_categories || 0;
            document.querySelector('.admin-orange .text-3xl').textContent = stats.publishing_types || 0;
        }
    }

    // Initialize filters on page load
    updateActiveFilters();
});

function showNotification(type, message) {
    // Your existing notification function
    console.log(`${type}: ${message}`);
}
</script>
