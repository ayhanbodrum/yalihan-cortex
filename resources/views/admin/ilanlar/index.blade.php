@extends('admin.layouts.admin')

@section('title', 'İlan Yönetimi')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">İlan Yönetimi</h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">İlanlarınızı yönetin ve takip edin</p>
            </div>
            <a href="{{ route('admin.ilanlar.create') }}"
                class="inline-flex items-center px-6 py-3 bg-orange-600 text-white font-semibold rounded-lg shadow-md hover:bg-orange-700 hover:scale-105 hover:shadow-lg active:scale-95 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:outline-none transition-all duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Yeni İlan
            </a>
        </div>

        <!-- İstatistik Kartları -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div
                class="bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200 p-6">
                <div class="flex items-center">
                    <div
                        class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-2xl font-bold text-blue-600">{{ $stats['total'] ?? $ilanlar->total() }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Toplam İlan</p>
                    </div>
                </div>
            </div>

            <div
                class="bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200 p-6">
                <div class="flex items-center">
                    <div
                        class="w-12 h-12 bg-gradient-to-r from-green-500 to-green-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-2xl font-bold text-green-600">{{ $stats['active'] ?? 0 }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Aktif İlanlar</p>
                    </div>
                </div>
            </div>

            <div
                class="bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200 p-6">
                <div class="flex items-center">
                    <div
                        class="w-12 h-12 bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-2xl font-bold text-purple-600">{{ $stats['this_month'] ?? 0 }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Bu Ay</p>
                    </div>
                </div>
            </div>

            <div
                class="bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200 p-6">
                <div class="flex items-center">
                    <div
                        class="w-12 h-12 bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-2xl font-bold text-orange-600">{{ $stats['pending'] ?? 0 }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Bekleyen İlanlar</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Üst Sekmeler (Tab Bar) -->
        <div
            class="bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm transition-shadow duration-200">
            <div class="px-4 py-3 flex flex-wrap gap-2 items-center">
                @php $activeTab = request('tab'); @endphp
                @php $counts = $tabCounts ?? []; @endphp

                <a href="{{ route('admin.ilanlar.index', array_merge(request()->except('page'), ['tab' => 'active'])) }}"
                    class="px-3 py-1.5 rounded-lg text-sm border {{ $activeTab === 'active' ? 'bg-green-100 border-green-300 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300' }}">
                    AKTİF İLANLAR ({{ $counts['active'] ?? 0 }})
                </a>
                <a href="{{ route('admin.ilanlar.index', array_merge(request()->except('page'), ['tab' => 'expired'])) }}"
                    class="px-3 py-1.5 rounded-lg text-sm border {{ $activeTab === 'expired' ? 'bg-amber-100 border-amber-300 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300' : 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300' }}">
                    SÜRESİ DOLAN İLANLAR ({{ $counts['expired'] ?? 0 }})
                </a>
                <a href="{{ route('admin.ilanlar.index', array_merge(request()->except('page'), ['tab' => 'passive'])) }}"
                    class="px-3 py-1.5 rounded-lg text-sm border {{ $activeTab === 'passive' ? 'bg-red-100 border-red-300 text-red-800 dark:bg-red-900/30 dark:text-red-300' : 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300' }}">
                    PASİF İLANLAR ({{ $counts['passive'] ?? 0 }})
                </a>
                <a href="{{ route('admin.ilanlar.index', array_merge(request()->except('page'), ['tab' => 'office'])) }}"
                    class="px-3 py-1.5 rounded-lg text-sm border {{ $activeTab === 'office' ? 'bg-blue-100 border-blue-300 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300' }}">
                    OFİSİMİN İLANLARI ({{ $counts['office'] ?? 0 }})
                </a>
                <a href="{{ route('admin.ilanlar.index', array_merge(request()->except('page'), ['tab' => 'drafts'])) }}"
                    class="px-3 py-1.5 rounded-lg text-sm border {{ $activeTab === 'drafts' ? 'bg-gray-100 border-gray-300 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300' : 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300' }}">
                    TASLAKLAR ({{ $counts['drafts'] ?? 0 }})
                </a>
                <a href="{{ route('admin.ilanlar.index', array_merge(request()->except('page'), ['tab' => 'deleted'])) }}"
                    class="px-3 py-1.5 rounded-lg text-sm border {{ $activeTab === 'deleted' ? 'bg-slate-100 border-slate-300 text-slate-800 dark:bg-slate-900/30 dark:text-slate-300' : 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300' }}">
                    SİLİNENLER ({{ $counts['deleted'] ?? 0 }})
                </a>
            </div>
        </div>

        <!-- Filtre Sistemi -->
        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200 p-6"
            x-data="ilanFilter()">
            <form @submit.prevent="applyFilters()" method="GET" action="{{ route('admin.ilanlar.filter') }}">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Arama</label>
                        <input type="text" name="search" x-model="filters.search" @input.debounce.500ms="applyFilters()"
                            placeholder="İlan başlığı, ilan sahibi adı, site/apartman adı..."
                            class="w-full px-5 py-3 text-base border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-all duration-200">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Status</label>
                        <select name="status" x-model="filters.status" @change="applyFilters()"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                            <option value="">Tümü</option>
                            <option value="Aktif" {{ request('status') === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="Pasif" {{ request('status') === 'Pasif' ? 'selected' : '' }}>Pasif</option>
                            <option value="Beklemede" {{ request('status') === 'Beklemede' ? 'selected' : '' }}>Beklemede
                            </option>
                            <option value="Taslak" {{ request('status') === 'Taslak' ? 'selected' : '' }}>Taslak</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Kategori</label>
                        <select name="kategori_id" x-model="filters.kategori_id" @change="applyFilters()"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                            <option value="">Tümü</option>
                            @if (isset($kategoriler))
                                @foreach ($kategoriler as $kategori)
                                    <option value="{{ $kategori->id }}"
                                        {{ request('kategori_id') == $kategori->id ? 'selected' : '' }}>
                                        {{ $kategori->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Kiralama Türü</label>
                        <select name="kiralama_turu" x-model="filters.kiralama_turu" @change="applyFilters()"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                            <option value="">Tümü</option>
                            <option value="gunluk" {{ request('kiralama_turu') == 'gunluk' ? 'selected' : '' }}>Günlük
                            </option>
                            <option value="haftalik" {{ request('kiralama_turu') == 'haftalik' ? 'selected' : '' }}>
                                Haftalık</option>
                            <option value="aylik" {{ request('kiralama_turu') == 'aylik' ? 'selected' : '' }}>Aylık
                            </option>
                            <option value="uzun_donem" {{ request('kiralama_turu') == 'uzun_donem' ? 'selected' : '' }}>
                                Uzun Dönem</option>
                            <option value="sezonluk" {{ request('kiralama_turu') == 'sezonluk' ? 'selected' : '' }}>
                                Sezonluk</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Sıralama</label>
                        <select name="sort" x-model="filters.sort" @change="applyFilters()"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                            <option value="created_desc" {{ request('sort') === 'created_desc' ? 'selected' : '' }}>En
                                Yeni</option>
                            <option value="created_asc" {{ request('sort') === 'created_asc' ? 'selected' : '' }}>En Eski
                            </option>
                            <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Fiyat
                                (Yüksek-Düşük)</option>
                            <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Fiyat
                                (Düşük-Yüksek)</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-4">
                    <button type="button" @click="clearFilters()"
                        class="inline-flex items-center px-6 py-3 bg-gray-600 text-white font-semibold rounded-lg shadow-md hover:bg-gray-700 hover:scale-105 hover:shadow-lg active:scale-95 focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all duration-200">
                        Temizle
                    </button>
                    <button type="button" @click="applyFilters()" :disabled="loading"
                        class="inline-flex items-center px-6 py-3 bg-orange-600 text-white font-semibold rounded-lg shadow-md hover:bg-orange-700 hover:scale-105 hover:shadow-lg active:scale-95 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:outline-none transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg x-show="!loading" class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <svg x-show="loading" class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span x-text="loading ? 'Yükleniyor...' : 'Filtrele'"></span>
                    </button>
                </div>
            </form>
        </div>

        <!-- AI Quick Actions Bar -->
        <div class="bg-gradient-to-r from-purple-50 to-indigo-50 dark:from-purple-900/20 dark:to-indigo-900/20 rounded-lg border border-purple-200 dark:border-purple-800 shadow-sm p-4 mb-6"
            x-data="aiQuickActions()" x-show="$store.bulkActions?.selectedIds?.length > 0 || selectedIds?.length > 0"
            x-transition>
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
                        <p class="text-xs text-gray-600 dark:text-gray-400"
                            x-text="`${($store.bulkActions?.selectedIds?.length || selectedIds?.length || 0)} ilan seçildi`">
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

        <!-- İlan Listesi -->
        <div
            class="bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">İlan Listesi</h3>
                <span class="text-sm text-gray-500 dark:text-gray-400"
                    x-text="`${totalCount} ilan`">{{ $ilanlar->total() }} ilan</span>
            </div>

            <div class="p-6" x-data="bulkActionsManager()" id="ilanlar-list-container">
                <x-admin.meta-info title="İlanlar" :meta="[
                    'total' => $ilanlar->total(),
                    'current_page' => $ilanlar->currentPage(),
                    'last_page' => $ilanlar->lastPage(),
                    'per_page' => $ilanlar->perPage(),
                ]" :show-per-page="true" :per-page-options="[20, 50, 100]" listId="ilanlar"
                    listEndpoint="/api/admin/api/v1/ilanlar" />
                @if ($ilanlar->count() > 0)
                    {{-- Bulk Actions Toolbar --}}
                    <div x-show="selectedIds.length > 0" x-transition
                        class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 px-6 py-4 flex items-center justify-between mb-4 rounded-lg">

                        <div class="flex items-center text-sm font-medium text-blue-800 dark:text-blue-300">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span x-text="`${selectedIds.length} ilan seçildi`"></span>
                        </div>

                        <div class="flex items-center gap-3">
                            {{-- Activate Button --}}
                            <button type="button" @click="bulkAction('activate')" :disabled="processing"
                                class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 hover:scale-105 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span x-show="!processing">Aktif Yap</span>
                                <span x-show="processing">İşleniyor...</span>
                            </button>

                            {{-- Deactivate Button --}}
                            <button type="button" @click="bulkAction('deactivate')" :disabled="processing"
                                class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white text-sm font-medium rounded-lg hover:bg-yellow-700 hover:scale-105 focus:ring-2 focus:ring-yellow-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Pasif Yap
                            </button>

                            {{-- Delete Button --}}
                            <button type="button" @click="confirmBulkDelete()" :disabled="processing"
                                class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 hover:scale-105 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Sil
                            </button>

                            {{-- Clear Selection --}}
                            <button type="button" @click="clearSelection()"
                                class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white underline">
                                Seçimi Temizle
                            </button>
                        </div>
                    </div>

                    {{-- Mobile Card View --}}
                    <div class="md:hidden">
                        <div class="space-y-4">
                            @foreach ($ilanlar as $ilan)
                                <div class="bg-white dark:bg-gray-900 p-4 rounded-lg shadow">
                                    <h3 class="font-bold text-lg mb-2">{{ $ilan->baslik }}</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                        {{ $ilan->ilce->ilce_adi ?? '' }} / {{ $ilan->il->il_adi ?? '' }}
                                    </p>
                                    <div class="flex justify-between items-center">
                                        <span class="text-green-600 font-bold">{{ number_format($ilan->fiyat) }}
                                            {{ $ilan->para_birimi }}</span>
                                        <a href="{{ route('admin.ilanlar.show', $ilan->id) }}"
                                            class="text-blue-600">Detay</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Desktop Table View --}}
                    <div class="hidden md:block w-full overflow-x-auto" id="ilanlar-table-container">
                        <table class="w-full text-left border-collapse min-w-full">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th class="admin-table-th w-12">
                                        <input type="checkbox" id="select-all"
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                            x-model="selectAll" @change="toggleSelectAll()">
                                    </th>
                                    <th class="admin-table-th min-w-[300px]">İlan</th>
                                    <th class="admin-table-th min-w-[150px]">Tür & Kategori</th>
                                    <th class="admin-table-th min-w-[120px]">Fiyat</th>
                                    <th class="admin-table-th min-w-[150px]">Danışman</th>
                                    <th class="admin-table-th min-w-[100px]">Status</th>
                                    <th class="admin-table-th min-w-[120px]">Güncellenme</th>
                                    <th class="admin-table-th min-w-[120px]">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody id="ilanlar-tbody">
                                @foreach ($ilanlar as $ilan)
                                    <tr>
                                        {{-- Checkbox Column --}}
                                        <td class="px-6 py-4">
                                            <input type="checkbox"
                                                class="row-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                                value="{{ $ilan->id }}" x-model="selectedIds"
                                                @change="updateSelectAll()">
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col gap-3">
                                                {{-- BAŞLIK (ÜST) - Resmin üstünde --}}
                                                <div class="flex items-center gap-3">
                                                    {{-- ✨ REFERANS BADGE --}}
                                                    @include('admin.ilanlar.partials.referans-badge', [
                                                        'ilan' => $ilan,
                                                    ])

                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                        <a href="{{ route('admin.ilanlar.show', $ilan->id) }}"
                                                            class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200">
                                                            {{ $ilan->baslik ?? 'İlan #' . $ilan->id }}
                                                        </a>
                                                    </div>
                                                </div>

                                                {{-- RESİM + DETAYLAR (ORTA) --}}
                                                <div class="flex items-start gap-4">
                                                    {{-- Resim --}}
                                                    <div
                                                        class="flex-shrink-0 w-32 h-24 md:w-48 md:h-32 lg:w-64 lg:h-40 rounded-lg overflow-hidden shadow-md">
                                                        @php
                                                            $firstPhoto = $ilan->fotograflar?->first();
                                                            $photoPath = $firstPhoto?->dosya_yolu;
                                                        @endphp
                                                        @if ($photoPath && file_exists(storage_path('app/public/' . $photoPath)))
                                                            <img class="w-full h-full object-cover"
                                                                src="{{ asset('storage/' . $photoPath) }}"
                                                                alt="İlan görseli">
                                                        @else
                                                            <div
                                                                class="w-full h-full rounded-lg bg-gray-200 dark:bg-gray-800 flex items-center justify-center">
                                                                <svg class="h-8 w-8 text-gray-400" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                                </svg>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    {{-- Lokasyon ve Sahibi --}}
                                                    <div class="flex-1 space-y-2">
                                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                                            @if ($ilan->il && $ilan->ilce)
                                                                <span class="inline-flex items-center gap-1">
                                                                    <svg class="w-4 h-4" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                    </svg>
                                                                    {{ $ilan->il->il_adi }}, {{ $ilan->ilce->ilce_adi }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <div class="text-xs text-gray-600 dark:text-gray-400">
                                                            <span class="font-semibold">Sahibi:</span>
                                                            @if ($ilan->ilanSahibi)
                                                                {{ $ilan->ilanSahibi->ad }} {{ $ilan->ilanSahibi->soyad }}
                                                            @else
                                                                -
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- NOT (ALT) - Resmin altında --}}
                                                @php
                                                    $ilanNotu = $ilan->anahtar_notlari ?? ($ilan->aciklama ?? null);
                                                @endphp
                                                @if ($ilanNotu)
                                                    <div
                                                        class="text-xs italic text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-800/50 p-3 rounded-lg border border-gray-200 dark:border-gray-700">
                                                        <span
                                                            class="font-semibold not-italic text-gray-900 dark:text-white">Not:</span>
                                                        {{ Str::limit($ilanNotu, 160) }}
                                                    </div>
                                                @endif

                                                {{-- 📊 METRİKLER - Sahibinden tarzı --}}
                                                <div
                                                    class="flex items-center gap-4 mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                                                    {{-- Görüntülenme --}}
                                                    <div
                                                        class="flex items-center gap-1.5 text-xs text-gray-600 dark:text-gray-400 group hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                        <span
                                                            class="font-medium">{{ number_format($ilan->goruntulenme_sayisi ?? 0) }}</span>
                                                    </div>

                                                    {{-- Arama Sayısı --}}
                                                    <div
                                                        class="flex items-center gap-1.5 text-xs text-gray-600 dark:text-gray-400 group hover:text-green-600 dark:hover:text-green-400 transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                                        </svg>
                                                        <span
                                                            class="font-medium">{{ number_format($ilan->arama_sayisi ?? 0) }}</span>
                                                    </div>

                                                    {{-- Favori Sayısı --}}
                                                    <div
                                                        class="flex items-center gap-1.5 text-xs text-gray-600 dark:text-gray-400 group hover:text-red-500 dark:hover:text-red-400 transition-colors">
                                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                            <path
                                                                d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                                                        </svg>
                                                        <span
                                                            class="font-medium">{{ number_format($ilan->favori_sayisi ?? 0) }}</span>
                                                    </div>

                                                    {{-- Telefon Görüntüleme --}}
                                                    <div
                                                        class="flex items-center gap-1.5 text-xs text-gray-600 dark:text-gray-400 group hover:text-purple-600 dark:hover:text-purple-400 transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                        </svg>
                                                        <span
                                                            class="font-medium">{{ number_format($ilan->telefon_goruntuleme_sayisi ?? 0) }}</span>
                                                    </div>

                                                    {{-- Paylaşım --}}
                                                    <div
                                                        class="flex items-center gap-1.5 text-xs text-gray-600 dark:text-gray-400 group hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                                        </svg>
                                                        <span
                                                            class="font-medium">{{ number_format($ilan->paylasim_sayisi ?? 0) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900 dark:text-white">
                                                {{ $ilan->yayinTipi?->name ?? 'Belirtilmemiş' }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $ilan->anaKategori?->name ?? 'Belirtilmemiş' }}
                                                @if ($ilan->altKategori)
                                                    → {{ $ilan->altKategori->name }}
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="space-y-2">
                                                {{-- Fiyat --}}
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ number_format($ilan->fiyat ?? 0, 0, ',', '.') }}
                                                    {{ $ilan->para_birimi ?? 'TRY' }}
                                                    @if ($ilan->kiralama_turu)
                                                        @switch($ilan->kiralama_turu)
                                                            @case('gunluk')
                                                                <span class="text-xs text-gray-600 dark:text-gray-400">/Gün</span>
                                                            @break

                                                            @case('haftalik')
                                                                <span
                                                                    class="text-xs text-gray-600 dark:text-gray-400">/Hafta</span>
                                                            @break

                                                            @case('aylik')
                                                            @case('uzun_donem')
                                                                <span class="text-xs text-gray-600 dark:text-gray-400">/Ay</span>
                                                            @break

                                                            @case('sezonluk')
                                                                <span
                                                                    class="text-xs text-gray-600 dark:text-gray-400">/Sezon</span>
                                                            @break
                                                        @endswitch
                                                    @endif
                                                </div>

                                                {{-- 🧠 TKGM AI Badge (Arsa/Tarla için) --}}
                                                @if ($ilan->altKategori && in_array(strtolower($ilan->altKategori->name ?? ''), ['arsa', 'tarla']))
                                                    @php
                                                        // Basit AI tahmin simülasyonu (gerçekte API'den gelecek)
$aiEstimate = $ilan->fiyat * 0.95; // %5 daha uygun fiyat
$marketStatus = 'normal'; // 'low', 'normal', 'high'

if ($ilan->fiyat > 0) {
    $diff = (($ilan->fiyat - $aiEstimate) / $aiEstimate) * 100;

    if ($diff > 10) {
        $marketStatus = 'high';
    } elseif ($diff < -5) {
        $marketStatus = 'low';
                                                            }
                                                        }
                                                    @endphp
                                                    <div
                                                        class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-xs font-medium transition-all duration-200
                                                        {{ $marketStatus === 'low' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : '' }}
                                                        {{ $marketStatus === 'normal' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400' : '' }}
                                                        {{ $marketStatus === 'high' ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400' : '' }}">
                                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                            <path
                                                                d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z">
                                                            </path>
                                                        </svg>
                                                        <span>AI:</span>
                                                        @if ($marketStatus === 'low')
                                                            <span>Fırsat</span>
                                                        @elseif($marketStatus === 'high')
                                                            <span>Piyasa Üstü</span>
                                                        @else
                                                            <span>Uygun Fiyat</span>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                            @if ($ilan->userDanisman)
                                                <div class="flex items-center">
                                                    <div
                                                        class="flex-shrink-0 h-8 w-8 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center">
                                                        <span
                                                            class="text-xs font-semibold text-purple-600 dark:text-purple-400">
                                                            {{ substr($ilan->userDanisman->name, 0, 2) }}
                                                        </span>
                                                    </div>
                                                    <div class="ml-2">
                                                        <div class="text-sm font-medium">{{ $ilan->userDanisman->name }}
                                                        </div>
                                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                                            {{ $ilan->userDanisman->email }}</div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            {{-- Inline Status Toggle --}}
                                            <div x-data="statusToggle({{ $ilan->id }}, '{{ $ilan->status ?? 'Taslak' }}')" @click.outside="open = false"
                                                class="relative inline-block">

                                                {{-- Clickable Badge --}}
                                                <button @click="open = !open" type="button" :disabled="updating"
                                                    class="px-3 py-1 text-xs font-semibold rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all duration-200 cursor-pointer disabled:opacity-50"
                                                    :class="getStatusClasses()">
                                                    <span x-text="currentStatus"></span>
                                                    <svg class="w-3 h-3 ml-1 inline transition-transform duration-200"
                                                        :class="{ 'rotate-180': open }" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </button>

                                                {{-- Dropdown Menu --}}
                                                <div x-show="open" x-transition
                                                    class="absolute z-50 mt-2 w-48 rounded-lg shadow-lg bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 py-1">

                                                    <button @click="changeStatus('Aktif')" type="button"
                                                        class="w-full text-left px-4 py-2 text-sm hover:bg-green-50 dark:hover:bg-green-900/20 flex items-center transition-colors"
                                                        :class="{ 'bg-green-50 dark:bg-green-900/20': currentStatus === 'Aktif' }">
                                                        <span class="w-2 h-2 rounded-full bg-green-500 mr-3"></span>
                                                        <span
                                                            class="text-green-700 dark:text-green-300 font-medium">Aktif</span>
                                                    </button>

                                                    <button @click="changeStatus('Beklemede')" type="button"
                                                        class="w-full text-left px-4 py-2 text-sm hover:bg-yellow-50 dark:hover:bg-yellow-900/20 flex items-center transition-colors"
                                                        :class="{ 'bg-yellow-50 dark:bg-yellow-900/20': currentStatus === 'Beklemede' }">
                                                        <span class="w-2 h-2 rounded-full bg-yellow-500 mr-3"></span>
                                                        <span
                                                            class="text-yellow-700 dark:text-yellow-300 font-medium">Beklemede</span>
                                                    </button>

                                                    <button @click="changeStatus('Taslak')" type="button"
                                                        class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center transition-colors"
                                                        :class="{ 'bg-gray-50 dark:bg-gray-800': currentStatus === 'Taslak' }">
                                                        <span class="w-2 h-2 rounded-full bg-gray-500 mr-3"></span>
                                                        <span
                                                            class="text-gray-900 dark:text-white font-medium">Taslak</span>
                                                    </button>

                                                    <button @click="changeStatus('Pasif')" type="button"
                                                        class="w-full text-left px-4 py-2 text-sm hover:bg-red-50 dark:hover:bg-red-900/20 flex items-center transition-colors"
                                                        :class="{ 'bg-red-50 dark:bg-red-900/20': currentStatus === 'Pasif' }">
                                                        <span class="w-2 h-2 rounded-full bg-red-500 mr-3"></span>
                                                        <span
                                                            class="text-red-700 dark:text-red-300 font-medium">Pasif</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $ilan->updated_at?->format('d.m.Y H:i') ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{-- 🚀 Quick Actions (Sahibinden tarzı) --}}
                                            <div x-data="{ open: false }" @click.outside="open = false" class="relative">
                                                <div class="flex items-center gap-2">
                                                    <a href="{{ route('admin.ilanlar.show', $ilan->id) }}"
                                                        class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg
                                                            bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400
                                                            hover:bg-blue-200 dark:hover:bg-blue-900/50
                                                            transition-all duration-200 hover:scale-105"
                                                        title="Görüntüle">
                                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                        Görüntüle
                                                    </a>

                                                    <a href="{{ route('admin.ilanlar.edit', $ilan->id) }}"
                                                        class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg
                                                            bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400
                                                            hover:bg-green-200 dark:hover:bg-green-900/50
                                                            transition-all duration-200 hover:scale-105"
                                                        title="Düzenle">
                                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                        Düzenle
                                                    </a>

                                                    <button @click="open = !open" type="button"
                                                        class="inline-flex items-center p-2 text-gray-600 dark:text-gray-300
                                                            bg-gray-100 dark:bg-gray-800 rounded-lg
                                                            hover:bg-gray-200 dark:hover:bg-gray-700
                                                            transition-all duration-200 hover:scale-105"
                                                        title="Daha Fazla">
                                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                            <path
                                                                d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                                        </svg>
                                                    </button>
                                                </div>

                                                <div x-show="open" x-transition
                                                    class="absolute right-0 z-50 mt-2 w-60 rounded-lg shadow-xl
                                                        bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700
                                                        divide-y divide-gray-100 dark:divide-gray-700">

                                                    <div class="py-1">
                                                        <button type="button"
                                                            class="w-full text-left px-4 py-2.5 text-sm font-semibold
                                                                text-red-600 dark:text-red-400
                                                                hover:bg-red-50 dark:hover:bg-red-900/20
                                                                flex items-center gap-3 transition-colors">
                                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                                <path
                                                                    d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" />
                                                            </svg>
                                                            <div>
                                                                <div>İlanı Turbola</div>
                                                                <div class="text-xs text-gray-500 dark:text-gray-400">Öne
                                                                    çıkar</div>
                                                            </div>
                                                        </button>

                                                        <button type="button"
                                                            class="w-full text-left px-4 py-2.5 text-sm font-semibold
                                                                text-indigo-600 dark:text-indigo-400
                                                                hover:bg-indigo-50 dark:hover:bg-indigo-900/20
                                                                flex items-center gap-3 transition-colors">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                                            </svg>
                                                            <div>
                                                                <div>Paylaş Kazan</div>
                                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                                    Sosyal medyada paylaş</div>
                                                            </div>
                                                        </button>
                                                    </div>

                                                    <div class="py-1">
                                                        <a href="{{ route('admin.ilanlar.show', $ilan->id) }}"
                                                            class="w-full text-left px-4 py-2 text-sm
                                                                text-gray-700 dark:text-gray-200
                                                                hover:bg-gray-50 dark:hover:bg-gray-700
                                                                flex items-center gap-3 transition-colors">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                            </svg>
                                                            İstatistikler
                                                        </a>

                                                        <a href="{{ route('admin.ilanlar.edit', $ilan->id) }}"
                                                            class="w-full text-left px-4 py-2 text-sm
                                                                text-gray-700 dark:text-gray-200
                                                                hover:bg-gray-50 dark:hover:bg-gray-700
                                                                flex items-center gap-3 transition-colors">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                                            </svg>
                                                            Gelişmiş Ayarlar
                                                        </a>
                                                    </div>

                                                    <div class="py-1">
                                                        <button type="button"
                                                            class="w-full text-left px-4 py-2 text-sm font-semibold
                                                                text-red-600 dark:text-red-400
                                                                hover:bg-red-50 dark:hover:bg-red-900/20
                                                                flex items-center gap-3 transition-colors">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                            İlanı Sil
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6" id="ilanlar-pagination">
                        {{ $ilanlar->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">İlan Bulunamadı</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Arama kriterlerinize uygun ilan
                            bulunmamaktadır.</p>
                        <div class="mt-6">
                            <a href="{{ route('admin.ilanlar.create') }}"
                                class="inline-flex items-center px-6 py-3 bg-orange-600 text-white font-semibold rounded-lg shadow-md hover:bg-orange-700 hover:scale-105 hover:shadow-lg active:scale-95 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:outline-none transition-all duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Yeni İlan Ekle
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // AJAX Filter Manager (Alpine.js Component)
            // Context7: %100, Yalıhan Bekçi: ✅
            function ilanFilter() {
                return {
                    filters: {
                        search: '{{ request('search') }}',
                        status: '{{ request('status') }}',
                        kategori_id: '{{ request('kategori_id') }}',
                        kiralama_turu: '{{ request('kiralama_turu') }}',
                        sort: '{{ request('sort', 'created_desc') }}',
                        tab: '{{ request('tab') }}'
                    },
                    loading: false,
                    totalCount: {{ $ilanlar->total() }},

                    init() {
                        // URL'den query parametrelerini oku
                        const urlParams = new URLSearchParams(window.location.search);
                        this.filters.search = urlParams.get('search') || '';
                        this.filters.status = urlParams.get('status') || '';
                        this.filters.kategori_id = urlParams.get('kategori_id') || '';
                        this.filters.kiralama_turu = urlParams.get('kiralama_turu') || '';
                        this.filters.sort = urlParams.get('sort') || 'created_desc';
                        this.filters.tab = urlParams.get('tab') || '';
                    },

                    async applyFilters() {
                        this.loading = true;

                        try {
                            // Query parametrelerini oluştur
                            const params = new URLSearchParams();
                            Object.keys(this.filters).forEach(key => {
                                if (this.filters[key]) {
                                    params.append(key, this.filters[key]);
                                }
                            });

                            // URL'i güncelle (back/forward desteği için)
                            const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
                            window.history.pushState({}, '', newUrl);

                            // AJAX isteği
                            const response = await fetch('{{ route('admin.ilanlar.filter') }}?' + params.toString(), {
                                method: 'GET',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                }
                            });

                            if (!response.ok) {
                                throw new Error('Filtreleme başarısız');
                            }

                            const data = await response.json();

                            if (data.success) {
                                // Desktop Table View - Tablo içeriğini güncelle
                                const tbody = document.getElementById('ilanlar-tbody');
                                if (tbody && data.html) {
                                    // HTML'i parse et ve sadece tbody içeriğini al
                                    const parser = new DOMParser();
                                    const doc = parser.parseFromString(data.html, 'text/html');
                                    const newTbody = doc.querySelector('tbody');

                                    if (newTbody) {
                                        tbody.innerHTML = newTbody.innerHTML;
                                    }
                                }

                                // Mobile Card View - Card içeriğini güncelle
                                const cardsContainer = document.getElementById('ilanlar-cards-container');
                                if (cardsContainer && data.cards_html) {
                                    // HTML'i parse et ve card container'ı al
                                    const parser = new DOMParser();
                                    const doc = parser.parseFromString(data.cards_html, 'text/html');
                                    const newCardsContainer = doc.querySelector('#ilanlar-cards-container');

                                    if (newCardsContainer) {
                                        cardsContainer.innerHTML = newCardsContainer.innerHTML;
                                    }
                                }

                                // Pagination'ı güncelle
                                const pagination = document.getElementById('ilanlar-pagination');
                                if (pagination && data.pagination) {
                                    pagination.innerHTML = data.pagination;
                                }

                                // Total count'u güncelle
                                if (data.total !== undefined) {
                                    this.totalCount = data.total;
                                }

                                // Toast notification
                                if (window.toast) {
                                    window.toast.success(`${this.totalCount} ilan bulundu`);
                                }
                            }
                        } catch (error) {
                            console.error('Filter error:', error);
                            if (window.toast) {
                                window.toast.error('Filtreleme sırasında bir hata oluştu');
                            }
                        } finally {
                            this.loading = false;
                        }
                    },

                    clearFilters() {
                        this.filters = {
                            search: '',
                            status: '',
                            kategori_id: '',
                            kiralama_turu: '',
                            sort: 'created_desc',
                            tab: ''
                        };
                        this.applyFilters();
                    }
                };
            }

            // AI Quick Actions Manager (Alpine.js Component)
            // Context7: %100, Yalıhan Bekçi: ✅
            function aiQuickActions() {
                return {
                    processing: false,
                    results: null,
                    showResults: false,

                    get selectedIds() {
                        // Bulk actions manager'dan selectedIds'i al
                        const bulkManager = Alpine.$data(document.querySelector('[x-data*="bulkActionsManager"]'));
                        return bulkManager?.selectedIds || [];
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

                            // Sonuçları modal'da göster
                            this.showResultsModal(data);

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

                    showResultsModal(data) {
                        // Basit alert ile sonuçları göster (ileride modal'a çevrilebilir)
                        let message = `${data.count} ilan analiz edildi:\n\n`;
                        data.results.forEach((result, index) => {
                            message += `${index + 1}. ${result.baslik}\n`;
                            if (result.analysis.recommendations) {
                                message += `   Öneriler: ${result.analysis.recommendations.join(', ')}\n`;
                            }
                            message += '\n';
                        });

                        // Modal açmak yerine console'a yazdır (ileride modal component eklenebilir)
                        console.log('AI Analysis Results:', data);
                    },

                    async suggestPrices() {
                        await this.analyzeListings('price');
                    },

                    async optimizeTitles() {
                        await this.analyzeListings('title');
                    }
                };
            }

            // Bulk Actions Manager (Alpine.js Component)
            // Context7: %100, Yalıhan Bekçi: ✅
            function bulkActionsManager() {
                return {
                    selectedIds: [],
                    selectAll: false,
                    processing: false,

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

                    clearSelection() {
                        this.selectedIds = [];
                        this.selectAll = false;
                        document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = false);
                    },

                    confirmBulkDelete() {
                        if (this.selectedIds.length === 0) {
                            window.toast.error('Lütfen en az bir ilan seçin');
                            return;
                        }

                        if (confirm(
                                `${this.selectedIds.length} ilanı silmek istediğinize emin misiniz? Bu işlem geri alınamaz.`)) {
                            this.bulkAction('delete');
                        }
                    },

                    async bulkAction(action) {
                        if (this.selectedIds.length === 0) {
                            window.toast.error('Lütfen en az bir ilan seçin');
                            return;
                        }

                        this.processing = true;

                        try {
                            const response = await fetch('{{ route('admin.ilanlar.bulk.action') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                },
                                body: JSON.stringify({
                                    ids: this.selectedIds,
                                    action: action,
                                }),
                            });

                            const data = await response.json();

                            if (data.success) {
                                window.toast.success(data.message || 'İşlem başarılı');

                                // Reload page after 1 second
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1000);
                            } else {
                                throw new Error(data.message || 'İşlem başarısız');
                            }

                        } catch (error) {
                            console.error('Bulk action error:', error);
                            window.toast.error(error.message || 'Toplu işlem başarısız oldu');
                        } finally {
                            this.processing = false;
                        }
                    }
                }
            }

            // Inline Status Toggle Component
            // Context7: %100, Yalıhan Bekçi: ✅
            function statusToggle(ilanId, initialStatus) {
                return {
                    open: false,
                    currentStatus: initialStatus,
                    updating: false,

                    async changeStatus(newStatus) {
                        if (newStatus === this.currentStatus) {
                            this.open = false;
                            return;
                        }

                        this.updating = true;

                        try {
                            const response = await fetch(`/admin/ilanlar/${ilanId}/status`, {
                                method: 'PATCH',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                },
                                body: JSON.stringify({
                                    status: newStatus
                                }),
                            });

                            const data = await response.json();

                            if (data.success) {
                                this.currentStatus = newStatus;
                                window.toast.success(`Status "${newStatus}" olarak güncellendi`);
                            } else {
                                throw new Error(data.message || 'Güncelleme başarısız');
                            }

                        } catch (error) {
                            console.error('Status update error:', error);
                            window.toast.error(error.message || 'Status güncellenemedi');
                        } finally {
                            this.updating = false;
                            this.open = false;
                        }
                    },

                    getStatusClasses() {
                        const classes = {
                            'Aktif': 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 hover:bg-green-200 dark:hover:bg-green-800/50 focus:ring-blue-500 dark:focus:ring-blue-400',
                            'Beklemede': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300 hover:bg-yellow-200 dark:hover:bg-yellow-800/50 focus:ring-yellow-500',
                            'Taslak': 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600 focus:ring-blue-500',
                            'Pasif': 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300 hover:bg-red-200 dark:hover:bg-red-800/50 focus:ring-blue-500 dark:focus:ring-blue-400',
                        };
                        return classes[this.currentStatus] || classes['Taslak'];
                    }
                }
            }
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const paginate = document.querySelector('.mt-6')
                const tbody = document.querySelector('table tbody')
                if (!window.ApiAdapter || !paginate || !tbody) return
                const statusEl = document.getElementById('meta-status')
                const totalEl = document.getElementById('meta-total')
                const pageEl = document.getElementById('meta-page')
                const section = document.querySelector('[data-meta="true"]')
                const perSelect = section.querySelector('select[data-per-page-select]')
                let currentPer = 20
                const urlInit = new URL(window.location.href)
                const qPer = parseInt(urlInit.searchParams.get('per_page') || '')
                const storageKey = 'yalihan_admin_per_page'
                const sPer = parseInt(localStorage.getItem(storageKey) || '')
                if (qPer) {
                    currentPer = qPer;
                    perSelect.value = String(qPer)
                } else if (sPer) {
                    currentPer = sPer;
                    perSelect.value = String(sPer)
                }
                perSelect.addEventListener('change', function() {
                    currentPer = parseInt(perSelect.value || '20');
                    const u = new URL(window.location.href);
                    u.searchParams.set('per_page', String(currentPer));
                    window.history.replaceState({}, '', u.toString());
                    loadPage(1)
                })

                function setLoading(f) {
                    statusEl.setAttribute('aria-busy', f ? 'true' : 'false');
                    statusEl.textContent = f ? 'Yükleniyor…' : ''
                }

                function renderRows(items) {
                    if (!items || items.length === 0) {
                        tbody.innerHTML =
                            '<tr><td colspan="8" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">Kayıt bulunamadı</td></tr>';
                        return
                    }
                    const rows = items.map(function(it) {
                        const title = it.title || ('İlan #' + (it.id || ''))
                        const price = (it.fiyat != null ? it.fiyat : '') + ' ' + (it.para_birimi || '')
                        return (
                            '<tr>' +
                            '<td class="px-6 py-4"><input type="checkbox"></td>' +
                            '<td class="px-6 py-4"><div class="text-sm font-medium">' + title +
                            '</div><div class="text-sm text-gray-500">#' + (it.id || '') + '</div></td>' +
                            '<td class="px-6 py-4">' + '' + '</td>' +
                            '<td class="px-6 py-4">' + price + '</td>' +
                            '<td class="px-6 py-4">' + '' + '</td>' +
                            '<td class="px-6 py-4">' + '' + '</td>' +
                            '<td class="px-6 py-4">' + '' + '</td>' +
                            '<td class="px-6 py-4"><a href="/admin/ilanlar/' + (it.id || '') +
                            '" class="text-blue-600">Detay</a></td>' +
                            '</tr>'
                        )
                    }).join('')
                    tbody.innerHTML = rows
                }

                function updateMeta(meta) {
                    if (!meta) return
                    totalEl.textContent = 'Toplam: ' + (meta.total != null ? meta.total : '-')
                    pageEl.innerHTML = '📄 Sayfa: ' + (meta.current_page || 1) + ' / ' + (meta.last_page || 1)
                    if (meta.per_page) {
                        currentPer = parseInt(meta.per_page);
                        perSelect.value = String(meta.per_page);
                        localStorage.setItem(storageKey, String(meta.per_page))
                    }
                    const links = paginate.querySelectorAll('a[href*="page="]')
                    links.forEach(function(a) {
                        const u = new URL(a.href, window.location.origin);
                        const p = parseInt(u.searchParams.get('page') || '1');
                        a.setAttribute('aria-label', 'Sayfa ' + p);
                        if (p === meta.current_page) {
                            a.setAttribute('aria-disabled', 'true')
                        } else {
                            a.removeAttribute('aria-disabled')
                        }
                    })
                }

                function loadPage(page) {
                    setLoading(true)
                    window.ApiAdapter.get('/ilanlar', {
                            page: Number(page || 1),
                            per_page: currentPer
                        })
                        .then(function(res) {
                            renderRows(res.data || []);
                            updateMeta(res.meta || null);
                            setLoading(false)
                        })
                        .catch(function(err) {
                            setLoading(false);
                            const a = document.createElement('div');
                            a.setAttribute('role', 'alert');
                            a.className = 'px-6 py-2 text-sm text-red-600';
                            a.textContent = 'Hata: ' + ((err.response && err.response.message) || err.message ||
                                'Bilinmeyen hata');
                            paginate.parentNode.insertBefore(a, paginate);
                            setTimeout(function() {
                                a.remove()
                            }, 4000)
                        })
                }
                // Auto-init çalışıyor; ek init gerekmez
            })
        </script>
    @endpush

@endsection
