@props([
    'searchPlaceholder' => 'Ara...',
    'showFilters' => true,
    'showLabels' => true,
    'showStatus' => true,
    'showDateRange' => false,
    'showBulkActions' => true,
    'model' => null,
    'labels' => [],
    'statuses' => ['Aktif', 'Pasif', 'Potansiyel', 'Yeni'],
    'dateRange' => false,
    'searchType' => 'all',
])

<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
    <!-- Ana Arama Çubuğu -->
    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center space-x-4">
            <!-- Arama İkonu -->
            <div class="flex-shrink-0">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>

            <!-- Hızlı Arama Input -->
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" data-quick-search
                    data-search-type="{{ $searchType }}" placeholder="{{ $searchPlaceholder }}"
                    class="w-full px-4 py-2.5 border-0 focus:ring-0 focus:outline-none bg-transparent text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
                    x-model="searchQuery" @input.debounce.300ms="performSearch()" autocomplete="off">
            </div>

            <!-- Temizle Butonu -->
            <div class="flex-shrink-0" x-show="searchQuery.length > 0">
                <button type="button" @click="clearSearch()"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Filtreler -->
    @if ($showFilters)
        <div class="p-4 border-b border-gray-200 dark:border-gray-700" x-show="showFilters">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

                <!-- Durum Filtresi -->
                @if ($showStatus)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            📊 Durum
                        </label>
                        <select name="status"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white">
                            <option value="">Tümü</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status }}"
                                    {{ request('status') == $status ? 'selected' : '' }}>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- Etiket Filtresi -->
                @if ($showLabels && count($labels) > 0)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            🏷️ Etiketler
                        </label>
                        <select name="label"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white">
                            <option value="">Tümü</option>
                            @foreach ($labels as $label)
                                <option value="{{ $label->id }}"
                                    {{ request('label') == $label->id ? 'selected' : '' }}>
                                    {{ $label->ad }} ({{ $label->kullanim_sayisi }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- Tarih Aralığı -->
                @if ($showDateRange)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            📅 Tarih Aralığı
                        </label>
                        <div class="flex space-x-2">
                            <input type="date" name="start_date" value="{{ request('start_date') }}"
                                class="flex-1 px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white">
                            <input type="date" name="end_date" value="{{ request('end_date') }}"
                                class="flex-1 px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white">
                        </div>
                    </div>
                @endif

                <!-- Sıralama -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        🔄 Sıralama
                    </label>
                    <select name="sort"
                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white">
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Ad (A-Z)
                        </option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Ad (Z-A)
                        </option>
                        <option value="created_desc" {{ request('sort') == 'created_desc' ? 'selected' : '' }}>En Yeni
                        </option>
                        <option value="created_asc" {{ request('sort') == 'created_asc' ? 'selected' : '' }}>En Eski
                        </option>
                    </select>
                </div>
            </div>

            <!-- Filtre Butonları -->
            <div class="flex justify-between items-center mt-4">
                <div class="flex space-x-2">
                    <button type="submit" class="neo-btn neo-btn-primary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Ara
                    </button>
                    <a href="{{ request()->url() }}" class="neo-btn neo-btn-secondary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                            </path>
                        </svg>
                        Temizle
                    </a>
                </div>

                <!-- Filtre Toggle -->
                <button type="button" @click="showFilters = !showFilters"
                    class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                    <span x-text="showFilters ? 'Filtreleri Gizle' : 'Filtreleri Göster'"></span>
                    <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
            </div>
        </div>
    @endif

    <!-- Toplu İşlemler -->
    @if ($showBulkActions)
        <div class="p-4 bg-gray-50 dark:bg-gray-800" x-show="selectedItems.length > 0">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600 dark:text-gray-400">
                        <span x-text="selectedItems.length"></span> öğe seçildi
                    </span>

                    <div class="flex space-x-2">
                        <button type="button" @click="bulkAssignLabels()" class="neo-btn neo-btn-secondary btn-sm">
                            🏷️ Etiket Ekle
                        </button>
                        <button type="button" @click="bulkUpdateStatus()" class="neo-btn neo-btn-secondary btn-sm">
                            📊 Durum Güncelle
                        </button>
                        <button type="button" @click="bulkDelete()" class="btn-danger btn-sm">
                            🗑️ Sil
                        </button>
                    </div>
                </div>

                <button type="button" @click="clearSelection()"
                    class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                    Seçimi Temizle
                </button>
            </div>
        </div>
    @endif
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('advancedSearch', () => ({
            searchQuery: '{{ request('search') }}',
            showFilters: false,
            selectedItems: [],

            performSearch() {
                // AJAX ile arama yap
                const url = new URL(window.location);
                url.searchParams.set('search', this.searchQuery);
                window.location = url;
            },

            clearSearch() {
                this.searchQuery = '';
                this.performSearch();
            },

            bulkAssignLabels() {
                // Toplu etiket atama
                if (this.selectedItems.length > 0) {
                    // Modal aç veya AJAX isteği gönder
                    console.log('Etiket atanacak öğeler:', this.selectedItems);
                }
            },

            bulkUpdateStatus() {
                // Toplu status güncelleme
                if (this.selectedItems.length > 0) {
                    console.log('Durum güncellenecek öğeler:', this.selectedItems);
                }
            },

            bulkDelete() {
                // Toplu silme
                if (this.selectedItems.length > 0 && confirm(
                        'Seçili öğeleri silmek istediğinizden emin misiniz?')) {
                    console.log('Silinecek öğeler:', this.selectedItems);
                }
            },

            clearSelection() {
                this.selectedItems = [];
            }
        }));
    });
</script>
