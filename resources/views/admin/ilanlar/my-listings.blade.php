@extends('admin.layouts.admin')

@php
    // ✅ Context7: Storage facade - tek seferlik kullanım (duplicate use statement hatası önlendi)
    use Illuminate\Support\Facades\Storage;
@endphp

@section('title', 'İlanlarım')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 dark:from-gray-900 dark:to-gray-800">
        <!-- Modern Header -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 mb-8 p-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1
                        class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                        🏠 İlanlarım
                    </h1>
                    <p class="mt-3 text-lg text-gray-600 dark:text-gray-300">
                        Tüm ilanlarınızı yönetin ve performanslarını takip edin
                    </p>
                </div>
                <div class="flex gap-4">
                    <button
                        class="inline-flex items-center px-6 py-3 bg-gray-600 text-white font-semibold rounded-lg shadow-md hover:bg-gray-700 hover:scale-105 hover:shadow-lg active:scale-95 focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all duration-200"
                        onclick="refreshListings()">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.001 0 01-15.357-2m15.357 2H15">
                            </path>
                        </svg>
                        Yenile
                    </button>
                    <a href="{{ route('admin.my-listings.export', ['format' => 'excel']) }}" id="export-excel-btn"
                        class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-semibold rounded-lg shadow-md hover:bg-green-700 hover:scale-105 hover:shadow-lg active:scale-95 focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg id="export-excel-icon" class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        <svg id="export-excel-spinner" class="hidden animate-spin w-5 h-5 mr-2" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span id="export-excel-text">Excel İndir</span>
                    </a>
                    <a href="{{ route('admin.ilanlar.create') }}"
                        class="inline-flex items-center px-6 py-3 bg-orange-600 text-white font-semibold rounded-lg shadow-md hover:bg-orange-700 hover:scale-105 hover:shadow-lg active:scale-95 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:outline-none transition-all duration-200 touch-target-optimized touch-target-optimized">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Yeni İlan
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="stat-card">
                <div class="stat-value text-blue-600">{{ $stats['total_listings'] ?? 0 }}</div>
                <div class="stat-label">Toplam İlan</div>
            </div>
            <div class="stat-card">
                <div class="stat-value text-green-600">{{ $stats['active_listings'] ?? 0 }}</div>
                <div class="stat-label">Aktif İlanlar</div>
            </div>
            <div class="stat-card">
                <div class="stat-value text-purple-600">{{ $stats['pending_listings'] ?? 0 }}</div>
                <div class="stat-label">Bekleyen</div>
            </div>
            <div class="stat-card">
                <div class="stat-value text-orange-600">{{ $stats['total_views'] ?? 0 }}</div>
                <div class="stat-label">Toplam Görüntülenme</div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 mb-8 p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="status-filter"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Durum</label>
                    <select id="status-filter" aria-label="Durum filtresi"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-blue-500 focus:ring-blue-500 transition-all duration-200">
                        <option value="">Tümü</option>
                        <option value="Aktif">Aktif</option>
                        <option value="Beklemede">Beklemede</option>
                        <option value="Pasif">Pasif</option>
                        <option value="Taslak">Taslak</option>
                    </select>
                </div>
                <div>
                    <label for="category-filter"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Kategori</label>
                    <select id="category-filter" aria-label="Kategori filtresi"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-blue-500 focus:ring-blue-500 transition-all duration-200">
                        <option value="">Tümü</option>
                        @foreach ($categories ?? [] as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="search-input"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Arama</label>
                    <input type="text" id="search-input" aria-label="İlan ara" placeholder="İlan ara..."
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500 transition-all duration-200">
                </div>
                <div class="flex items-end">
                    <button id="filter-button" onclick="applyFilters()"
                        class="inline-flex items-center justify-center px-6 py-3 bg-orange-600 text-white font-semibold rounded-lg shadow-md hover:bg-orange-700 hover:scale-105 hover:shadow-lg active:scale-95 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:outline-none transition-all duration-200 w-full disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100">
                        <svg id="filter-spinner" class="hidden animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <svg id="filter-icon" class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <span id="filter-text">Filtrele</span>
                    </button>
                </div>
            </div>
            <form id="my-listings-search" class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4" novalidate>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Yetkili Kişi</label>
                    <input type="text" name="owner" placeholder="Ad Soyad"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-blue-500 focus:ring-blue-500" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Telefon</label>
                    <input type="text" name="phone" inputmode="numeric" pattern="[0-9\s()+-]{10,}"
                        placeholder="05xx xxx xx xx"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-blue-500 focus:ring-blue-500" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Portal</label>
                    <div class="flex gap-2">
                        <select name="portal"
                            class="rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Seçiniz</option>
                            <option value="sahibinden">Sahibinden</option>
                            <option value="emlakjet">Emlakjet</option>
                            <option value="hepsiemlak">Hepsiemlak</option>
                            <option value="zingat">Zingat</option>
                        </select>
                        <input type="text" name="portal_id" placeholder="İlan No 163868-6"
                            class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-blue-500 focus:ring-blue-500" />
                    </div>
                </div>
                <div class="flex items-end">
                    <button type="submit"
                        class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 hover:scale-105 hover:shadow-lg active:scale-95 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:outline-none transition-all duration-200 w-full">
                        Ara
                    </button>
                </div>
                <div class="md:col-span-4 text-sm text-gray-600 dark:text-gray-300" id="my-listings-search-status"></div>
            </form>
        </div>

        <!-- AI Quick Actions Bar -->
        <div class="bg-gradient-to-r from-purple-50 to-indigo-50 dark:from-purple-900/20 dark:to-indigo-900/20 rounded-lg border border-purple-200 dark:border-purple-800 shadow-sm p-4 mb-6"
            x-data="aiQuickActionsMyListings()" x-show="selectedIds.length > 0" x-transition>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white">AI Özellikleri</h4>
                        <p class="text-xs text-gray-600 dark:text-gray-400" x-text="`${selectedIds.length} ilan seçildi`">
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button @click="analyzeListings('comprehensive')" :disabled="processing"
                        class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 hover:scale-105 focus:ring-2 focus:ring-purple-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200">
                        <svg x-show="!processing" class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <svg x-show="processing" class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span x-text="processing ? 'Analiz ediliyor...' : 'Toplu Analiz'"></span>
                    </button>
                    <button @click="suggestPrices()" :disabled="processing"
                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 hover:scale-105 focus:ring-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Fiyat Önerisi
                    </button>
                    <button @click="optimizeTitles()" :disabled="processing"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 hover:scale-105 focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Başlık Optimizasyonu
                    </button>
                </div>
            </div>
        </div>

        <!-- View Mode Toggle & Bulk Actions -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 mb-6 p-4"
            x-data="myListingsManager()">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <!-- View Mode Toggle -->
                    <div class="flex items-center gap-2 bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                        <button @click="viewMode = 'table'"
                            :class="viewMode === 'table' ?
                                'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm' :
                                'text-gray-600 dark:text-gray-400'"
                            class="px-3 py-1.5 rounded-md text-sm font-medium transition-all duration-200">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Tablo
                        </button>
                        <button @click="viewMode = 'grid'"
                            :class="viewMode === 'grid' ?
                                'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm' :
                                'text-gray-600 dark:text-gray-400'"
                            class="px-3 py-1.5 rounded-md text-sm font-medium transition-all duration-200">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                            Grid
                        </button>
                    </div>

                    <!-- Bulk Actions Toolbar -->
                    <div x-show="selectedIds.length > 0" x-transition class="flex items-center gap-2">
                        <span class="text-sm text-gray-600 dark:text-gray-400"
                            x-text="`${selectedIds.length} seçildi`"></span>
                        <button @click="bulkAction('activate')" :disabled="processing"
                            class="px-3 py-1.5 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 disabled:opacity-50">
                            Aktif Yap
                        </button>
                        <button @click="bulkAction('deactivate')" :disabled="processing"
                            class="px-3 py-1.5 bg-yellow-600 text-white text-sm rounded-lg hover:bg-yellow-700 disabled:opacity-50">
                            Pasif Yap
                        </button>
                        <button @click="confirmBulkDelete()" :disabled="processing"
                            class="px-3 py-1.5 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 disabled:opacity-50">
                            Sil
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Listings Table -->
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200 flex items-center">
                    <svg class="w-6 h-6 mr-3 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                        </path>
                    </svg>
                    İlan Listesi
                </h2>
            </div>

            <!-- Table View -->
            <div x-show="viewMode === 'table'" class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="admin-table-th dark:text-gray-200 w-12">
                                <input type="checkbox" id="select-all-listings"
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" x-model="selectAll"
                                    @change="toggleSelectAll()">
                            </th>
                            <th class="admin-table-th dark:text-gray-200">İlan</th>
                            <th class="admin-table-th dark:text-gray-200">Kategori</th>
                            <th class="admin-table-th dark:text-gray-200">Durum</th>
                            <th class="admin-table-th dark:text-gray-200">Fiyat</th>
                            <th class="admin-table-th dark:text-gray-200">Görüntülenme</th>
                            <th class="admin-table-th dark:text-gray-200">Tarih</th>
                            <th class="admin-table-th dark:text-gray-200">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"
                        id="listings-table-body">
                        @forelse($listings ?? [] as $listing)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox"
                                        class="row-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                        value="{{ $listing->id }}" x-model="selectedIds" @change="updateSelectAll()">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-12 w-12">
                                            <img class="h-12 w-12 rounded-lg object-cover"
                                                src="{{ $listing->fotograflar->first() ? Storage::url($listing->fotograflar->first()->dosya_yolu) : asset('images/default-property.jpg') }}"
                                                alt="{{ $listing->baslik ?? 'İlan' }}" loading="lazy">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ Str::limit($listing->baslik ?? 'Başlık Yok', 40) }}
                                                @if (($listing->ilan_turu ?? null) === 'kiralik')
                                                    <div
                                                        class="mt-1 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M4 7h16M4 12h16M4 17h16" />
                                                        </svg>
                                                        Demirbaş: {{ optional($listing->demirbaslar)->count() ?? 0 }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                #{{ $listing->id }}
                                                @if ($listing->referans_no)
                                                    <div class="text-xs text-gray-400 dark:text-gray-500 mt-1"
                                                        title="{{ $listing->referans_no }}">
                                                        Ref: {{ Str::limit($listing->referans_no, 30) }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="space-y-1">
                                        @if ($listing->referans_no)
                                            <div class="flex items-center gap-2">
                                                <code
                                                    class="px-2 py-1 bg-gray-100 text-gray-800 text-xs font-mono rounded">
                                                    {{ $listing->referans_no }}
                                                </code>
                                                <button onclick="copyToClipboard('{{ $listing->referans_no }}', 'Ref No')"
                                                    class="text-gray-400 hover:text-gray-600 transition-colors"
                                                    title="Kopyala">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </div>
                                        @else
                                            <button onclick="generateRef({{ $listing->id }})"
                                                class="text-xs px-2 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition-colors">
                                                Ref Oluştur
                                            </button>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="space-y-1 text-xs">
                                        @if ($listing->sahibinden_id)
                                            <div class="flex items-center gap-1">
                                                <span class="text-gray-600">SH:</span>
                                                <code class="text-gray-800">{{ $listing->sahibinden_id }}</code>
                                                <button
                                                    onclick="copyToClipboard('{{ $listing->sahibinden_id }}', 'Sahibinden ID')"
                                                    class="text-gray-400 hover:text-gray-600">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </div>
                                        @endif
                                        @if ($listing->emlakjet_id)
                                            <div class="flex items-center gap-1">
                                                <span class="text-gray-600">EJ:</span>
                                                <code class="text-gray-800">{{ $listing->emlakjet_id }}</code>
                                                <button
                                                    onclick="copyToClipboard('{{ $listing->emlakjet_id }}', 'Emlakjet ID')"
                                                    class="text-gray-400 hover:text-gray-600">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </div>
                                        @endif
                                        @if ($listing->hepsiemlak_id)
                                            <div class="flex items-center gap-1">
                                                <span class="text-gray-600">HE:</span>
                                                <code class="text-gray-800">{{ $listing->hepsiemlak_id }}</code>
                                                <button
                                                    onclick="copyToClipboard('{{ $listing->hepsiemlak_id }}', 'Hepsiemlak ID')"
                                                    class="text-gray-400 hover:text-gray-600">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </div>
                                        @endif
                                        @if (!$listing->sahibinden_id && !$listing->emlakjet_id && !$listing->hepsiemlak_id)
                                            <span class="text-gray-400 text-xs">-</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ optional($listing->altKategori)->name ?? (optional($listing->anaKategori)->name ?? 'Kategori Yok') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if (($listing->status ?? 'Taslak') === 'Aktif')
                                        <span class="status-badge active">
                                            <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3"></circle>
                                            </svg>
                                            Aktif
                                        </span>
                                    @elseif(($listing->status ?? 'Taslak') === 'Beklemede')
                                        <span class="status-badge pending">
                                            <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3"></circle>
                                            </svg>
                                            Beklemede
                                        </span>
                                    @elseif(($listing->status ?? 'Taslak') === 'Pasif')
                                        <span class="status-badge draft">
                                            <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3"></circle>
                                            </svg>
                                            Pasif
                                        </span>
                                    @else
                                        <span class="status-badge draft">
                                            <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3"></circle>
                                            </svg>
                                            Taslak
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ number_format($listing->fiyat ?? 0) }} {{ $listing->para_birimi ?? 'TRY' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ number_format($listing->goruntulenme ?? 0) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $listing->created_at ? $listing->created_at->format('d.m.Y') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2" x-data="{ open: false }">
                                        <a href="{{ route('admin.ilanlar.edit', $listing->id) }}"
                                            class="text-blue-600 hover:text-blue-900 transition-colors duration-200"
                                            title="Düzenle">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.ilanlar.show', $listing->id) }}"
                                            class="text-green-600 hover:text-green-900 transition-colors duration-200"
                                            title="Görüntüle">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                </path>
                                            </svg>
                                        </a>
                                        <!-- Quick Actions Dropdown -->
                                        <div class="relative">
                                            <button @click="open = !open" @click.outside="open = false"
                                                class="text-gray-600 hover:text-gray-900 transition-colors duration-200"
                                                title="Hızlı İşlemler">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                                </svg>
                                            </button>
                                            <div x-show="open" x-transition
                                                class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50">
                                                <button @click="quickEdit({{ $listing->id }})"
                                                    class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">
                                                    Hızlı Düzenle
                                                </button>
                                                <button @click="duplicateListing({{ $listing->id }})"
                                                    class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">
                                                    Kopyala
                                                </button>
                                                <button @click="quickPreview({{ $listing->id }})"
                                                    class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">
                                                    Önizleme
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <div class="text-gray-500 dark:text-gray-400">
                                        <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                            </path>
                                        </svg>
                                        <p class="text-lg font-medium">Henüz ilan bulunmuyor</p>
                                        <p class="text-sm">İlk ilanınızı oluşturmak için "Yeni İlan" butonuna tıklayın</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Grid View -->
            <div x-show="viewMode === 'grid'" class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="listings-grid-container">
                    @forelse($listings ?? [] as $listing)
                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200 overflow-hidden">
                            <div class="relative">
                                <img class="w-full h-48 object-cover"
                                    src="{{ $listing->fotograflar->first() ? Storage::url($listing->fotograflar->first()->dosya_yolu) : asset('images/default-property.jpg') }}"
                                    alt="{{ $listing->baslik ?? 'İlan' }}" loading="lazy">
                                <div class="absolute top-2 right-2">
                                    <input type="checkbox"
                                        class="row-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                        value="{{ $listing->id }}" x-model="selectedIds" @change="updateSelectAll()">
                                </div>
                            </div>
                            <div class="p-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                    {{ Str::limit($listing->baslik ?? 'Başlık Yok', 40) }}
                                </h3>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-2xl font-bold text-orange-600">
                                        {{ number_format($listing->fiyat ?? 0) }} {{ $listing->para_birimi ?? 'TRY' }}
                                    </span>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                        {{ optional($listing->altKategori)->name ?? (optional($listing->anaKategori)->name ?? 'Kategori Yok') }}
                                    </span>
                                </div>
                                <div
                                    class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400 mb-4">
                                    <span>👁️ {{ number_format($listing->goruntulenme ?? 0) }}</span>
                                    <span>{{ $listing->created_at ? $listing->created_at->format('d.m.Y') : 'N/A' }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.ilanlar.edit', $listing->id) }}"
                                        class="flex-1 px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 text-center">
                                        Düzenle
                                    </a>
                                    <a href="{{ route('admin.ilanlar.show', $listing->id) }}"
                                        class="flex-1 px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 text-center">
                                        Görüntüle
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-12">
                            <div class="text-gray-500 dark:text-gray-400">
                                <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                    </path>
                                </svg>
                                <p class="text-lg font-medium">Henüz ilan bulunmuyor</p>
                                <p class="text-sm">İlk ilanınızı oluşturmak için "Yeni İlan" butonuna tıklayın</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Pagination -->
        @if (isset($listings) && $listings->hasPages())
            <div class="mt-8">
                {{ $listings->links() }}
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/admin/my-listings-search.js'])
@endpush

@push('styles')
    <style>
        /* Modern Dashboard Styles */
        .btn-modern {
            @apply inline-flex items-center px-6 py-3 rounded-xl font-semibold transition-all duration-200 transform hover:scale-105 active:scale-95 shadow-lg;
        }

        .btn-modern-primary {
            @apply bg-gradient-to-r from-blue-600 to-purple-600 text-white hover:from-blue-700 hover:to-purple-700 shadow-blue-500/25;
        }

        .btn-modern-secondary {
            @apply bg-gradient-to-r from-gray-600 to-gray-700 text-white hover:from-gray-700 hover:to-gray-800 shadow-gray-500/25;
        }

        .stat-card {
            @apply text-center p-6 bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 hover:shadow-lg transition-shadow duration-200;
        }

        .stat-value {
            @apply text-3xl font-bold mb-2;
        }

        .stat-label {
            @apply text-gray-600 dark:text-gray-400 font-medium;
        }
    </style>
@endpush

@push('scripts')
    <script>
        function refreshListings() {
            location.reload();
        }

        // ✅ AJAX Filter Implementation (No Page Reload) - Context7: Loading state eklendi
        async function applyFilters() {
            const status = document.getElementById('status-filter').value;
            const category = document.getElementById('category-filter').value;
            const search = document.getElementById('search-input').value;

            // ✅ Loading state: Butonu devre dışı bırak ve spinner göster
            const filterButton = document.getElementById('filter-button');
            const filterSpinner = document.getElementById('filter-spinner');
            const filterIcon = document.getElementById('filter-icon');
            const filterText = document.getElementById('filter-text');

            filterButton.disabled = true;
            filterSpinner.classList.remove('hidden');
            filterIcon.classList.add('hidden');
            filterText.textContent = 'Filtreleniyor...';

            try {
                // Show loading state
                const tbody = document.getElementById('listings-table-body');
                tbody.innerHTML =
                    '<tr><td colspan="8" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400"><svg class="animate-spin h-8 w-8 mx-auto mb-2 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Yükleniyor...</td></tr>';

                const response = await fetch('{{ route('admin.my-listings.search') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        status,
                        category,
                        search
                    })
                });

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const data = await response.json();

                if (data.success) {
                    updateTableWithListings(data.data);
                    window.toast?.success('Filtreleme başarılı');
                } else {
                    throw new Error(data.message || 'Filtreleme başarısız');
                }
            } catch (error) {
                console.error('Filter error:', error);
                window.toast?.error('Filtreleme başarısız: ' + error.message);
                // Fallback to page reload on error
                location.reload();
            } finally {
                // ✅ Loading state: Butonu tekrar aktif et ve spinner gizle
                filterButton.disabled = false;
                filterSpinner.classList.add('hidden');
                filterIcon.classList.remove('hidden');
                filterText.textContent = 'Filtrele';
            }
        }

        function updateTableWithListings(paginatedData) {
            const tbody = document.getElementById('listings-table-body');
            tbody.innerHTML = '';

            if (!paginatedData.data || paginatedData.data.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="text-gray-500 dark:text-gray-400">
                                <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <p class="text-lg font-medium">İlan bulunamadı</p>
                                <p class="text-sm">Filtreleri değiştirmeyi deneyin</p>
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }

            paginatedData.data.forEach(listing => {
                const row = createListingRow(listing);
                tbody.appendChild(row);
            });

            // Update pagination if needed
            updatePagination(paginatedData);
        }

        function createListingRow(listing) {
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50 transition-colors duration-200';

            // Format price - Context7: Doğru field isimleri
            const price = new Intl.NumberFormat('tr-TR').format(listing.fiyat || 0);
            const views = new Intl.NumberFormat('tr-TR').format(listing.goruntulenme || 0);

            // Category name (with fallback)
            const categoryName = listing.alt_kategori?.name || listing.ana_kategori?.name || 'Kategori Yok';

            // Status badge - Context7: Database değerleri (Aktif, Pasif, Beklemede, Taslak)
            let statusHTML = '';
            if (listing.status === 'Aktif') {
                statusHTML =
                    `<span class="status-badge active"><svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"></circle></svg>Aktif</span>`;
            } else if (listing.status === 'Beklemede') {
                statusHTML =
                    `<span class="status-badge pending"><svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"></circle></svg>Beklemede</span>`;
            } else if (listing.status === 'Pasif') {
                statusHTML =
                    `<span class="status-badge draft"><svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"></circle></svg>Pasif</span>`;
            } else {
                statusHTML =
                    `<span class="status-badge draft"><svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"></circle></svg>Taslak</span>`;
            }

            // Format date
            const date = listing.created_at ? new Date(listing.created_at).toLocaleDateString('tr-TR') : 'N/A';

            // Featured image - Context7: İlişki kullanımı
            const featuredImage = listing.fotograflar?.[0]?.dosya_yolu ?
                `/storage/${listing.fotograflar[0].dosya_yolu}` :
                '{{ asset('images/default-property.jpg') }}';

            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap">
                    <input type="checkbox"
                           class="row-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                           value="${listing.id}"
                           x-model="selectedIds"
                           @change="updateSelectAll()">
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-12 w-12">
                            <img class="h-12 w-12 rounded-lg object-cover"
                                 src="${featuredImage}"
                                 alt="${listing.baslik || 'İlan'}"
                                 loading="lazy">
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                ${listing.baslik ? listing.baslik.substring(0, 40) : 'Başlık Yok'}${listing.baslik && listing.baslik.length > 40 ? '...' : ''}
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                #${listing.id}
                            </div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                        ${categoryName}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    ${statusHTML}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                    ${price} ${listing.para_birimi || 'TRY'}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                    ${views}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                    ${date}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex space-x-2">
                        <a href="/admin/ilanlar/${listing.id}/edit"
                           class="text-blue-600 hover:text-blue-900 transition-colors duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </a>
                        <a href="/admin/ilanlar/${listing.id}"
                           class="text-green-600 hover:text-green-900 transition-colors duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </a>
                    </div>
                </td>
            `;

            return row;
        }

        function updatePagination(paginatedData) {
            // TODO: Implement pagination update if needed
            // For now, we'll just log it
            console.log('Pagination:', {
                current_page: paginatedData.current_page,
                last_page: paginatedData.last_page,
                total: paginatedData.total
            });
        }

        // Enter tuşu ile arama
        document.getElementById('search-input')?.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                applyFilters();
            }
        });

        // ✅ Export Excel loading state
        document.getElementById('export-excel-btn')?.addEventListener('click', function(e) {
            const btn = this;
            const icon = document.getElementById('export-excel-icon');
            const spinner = document.getElementById('export-excel-spinner');
            const text = document.getElementById('export-excel-text');

            // Loading state
            btn.classList.add('disabled:opacity-50', 'disabled:cursor-not-allowed');
            btn.style.pointerEvents = 'none';
            icon.classList.add('hidden');
            spinner.classList.remove('hidden');
            text.textContent = 'İndiriliyor...';

            // 10 saniye sonra otomatik olarak geri dön (fallback)
            setTimeout(() => {
                btn.classList.remove('disabled:opacity-50', 'disabled:cursor-not-allowed');
                btn.style.pointerEvents = '';
                icon.classList.remove('hidden');
                spinner.classList.add('hidden');
                text.textContent = 'Excel İndir';
            }, 10000);
        });

        // Kopyalama fonksiyonu
        function copyToClipboard(text, label) {
            navigator.clipboard.writeText(text).then(function() {
                window.toast?.success(`${label} kopyalandı: ${text}`);
            }, function(err) {
                console.error('Kopyalama hatası:', err);
                window.toast?.error('Kopyalama başarısız');
            });
        }

        // Ref numarası oluştur
        async function generateRef(ilanId) {
            try {
                const response = await fetch('/api/reference/generate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({
                        ilan_id: ilanId
                    })
                });

                const data = await response.json();

                if (data.success) {
                    window.toast?.success('Ref numarası oluşturuldu');
                    location.reload(); // Sayfayı yenile
                } else {
                    throw new Error(data.message || 'Ref numarası oluşturulamadı');
                }
            } catch (error) {
                console.error('Ref oluşturma hatası:', error);
                window.toast?.error('Ref numarası oluşturulamadı: ' + error.message);
            }
        }

        // My Listings Manager (Alpine.js Component)
        function myListingsManager() {
            return {
                viewMode: 'table', // 'table' or 'grid'
                selectedIds: [],
                selectAll: false,
                processing: false,

                init() {
                    // Load view mode from localStorage
                    const savedViewMode = localStorage.getItem('my-listings-view-mode');
                    if (savedViewMode) {
                        this.viewMode = savedViewMode;
                    }

                    // Watch view mode changes and save to localStorage
                    this.$watch('viewMode', (value) => {
                        localStorage.setItem('my-listings-view-mode', value);
                    });
                },

                toggleSelectAll() {
                    const checkboxes = document.querySelectorAll('.row-checkbox');
                    if (this.selectAll) {
                        this.selectedIds = Array.from(checkboxes).map(cb => parseInt(cb.value));
                    } else {
                        this.selectedIds = [];
                    }
                    checkboxes.forEach(cb => cb.checked = this.selectAll);
                },

                updateSelectAll() {
                    const checkboxes = document.querySelectorAll('.row-checkbox');
                    const checkedCount = this.selectedIds.length;
                    this.selectAll = checkedCount === checkboxes.length && checkboxes.length > 0;
                },

                async bulkAction(action) {
                    if (this.selectedIds.length === 0) {
                        window.toast?.error('Lütfen en az bir ilan seçin');
                        return;
                    }

                    this.processing = true;
                    try {
                        const response = await fetch('{{ route('admin.my-listings.bulk.action') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                action: action,
                                ids: this.selectedIds
                            })
                        });

                        const data = await response.json();
                        if (data.success) {
                            window.toast?.success(data.message);
                            location.reload();
                        } else {
                            throw new Error(data.message || 'İşlem başarısız');
                        }
                    } catch (error) {
                        console.error('Bulk action error:', error);
                        window.toast?.error('İşlem başarısız: ' + error.message);
                    } finally {
                        this.processing = false;
                    }
                },

                confirmBulkDelete() {
                    if (this.selectedIds.length === 0) {
                        window.toast?.error('Lütfen en az bir ilan seçin');
                        return;
                    }

                    if (confirm(
                            `${this.selectedIds.length} ilanı silmek istediğinize emin misiniz? Bu işlem geri alınamaz.`)) {
                        this.bulkAction('delete');
                    }
                },

                quickEdit(ilanId) {
                    window.location.href = `/admin/ilanlar/${ilanId}/edit`;
                },

                async duplicateListing(ilanId) {
                    if (confirm('Bu ilanı kopyalamak istediğinize emin misiniz?')) {
                        try {
                            const response = await fetch(`{{ route('admin.ilanlar.duplicate', ['ilan' => ':id']) }}`
                                .replace(':id', ilanId), {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                                        'Accept': 'application/json'
                                    }
                                });

                            const data = await response.json();
                            if (data.success) {
                                window.toast?.success('İlan kopyalandı');
                                if (data.redirect_url) {
                                    window.location.href = data.redirect_url;
                                } else {
                                    location.reload();
                                }
                            } else {
                                throw new Error(data.message || 'Kopyalama başarısız');
                            }
                        } catch (error) {
                            console.error('Duplicate error:', error);
                            window.toast?.error('Kopyalama başarısız: ' + error.message);
                        }
                    }
                },

                quickPreview(ilanId) {
                    window.open(`/admin/ilanlar/${ilanId}`, '_blank');
                }
            };
        }

        // AI Quick Actions for My Listings (Alpine.js Component)
        function aiQuickActionsMyListings() {
            return {
                processing: false,
                selectedIds: [],
                results: null,
                showResults: false,

                get selectedIds() {
                    const manager = Alpine.$data(document.querySelector('[x-data*="myListingsManager"]'));
                    return manager?.selectedIds || [];
                },

                async analyzeListings(type = 'comprehensive') {
                    const ids = this.selectedIds;
                    if (ids.length === 0) {
                        if (window.toast) {
                            window.toast.error('Lütfen en az bir ilan seçin');
                        }
                        return;
                    }

                    this.processing = true;
                    this.showResults = false;

                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                        const response = await fetch('{{ route('admin.ilanlar.ai.bulk-analyze') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                ilan_ids: ids,
                                type: type
                            })
                        });

                        if (!response.ok) {
                            throw new Error('Analiz başarısız');
                        }

                        const data = await response.json();
                        this.results = data.results;
                        this.showResults = true;

                        if (window.toast) {
                            window.toast.success(`${data.count} ilan analiz edildi`);
                        }
                    } catch (error) {
                        console.error('AI Analysis error:', error);
                        if (window.toast) {
                            window.toast.error('AI analiz sırasında bir hata oluştu');
                        }
                    } finally {
                        this.processing = false;
                    }
                },

                async suggestPrices() {
                    await this.analyzeListings('price');
                },

                async optimizeTitles() {
                    await this.analyzeListings('title');
                }
            };
        }
    </script>
@endpush
