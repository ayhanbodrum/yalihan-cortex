@extends('admin.layouts.neo')

@section('title', 'İlan Yönetimi')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">İlan Yönetimi</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">İlanlarınızı yönetin ve takip edin</p>
        </div>
        <a href="{{ route('admin.ilanlar.create') }}" class="inline-flex items-center px-6 py-3 bg-orange-600 text-white font-semibold rounded-lg shadow-md hover:bg-orange-700 hover:scale-105 hover:shadow-lg active:scale-95 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:outline-none transition-all duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Yeni İlan
        </a>
    </div>

    <!-- İstatistik Kartları -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-2xl font-bold text-blue-600">{{ $stats['total'] ?? $ilanlar->total() }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Toplam İlan</p>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-green-600 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-2xl font-bold text-green-600">{{ $stats['active'] ?? 0 }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Aktif İlanlar</p>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-2xl font-bold text-purple-600">{{ $stats['this_month'] ?? 0 }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Bu Ay</p>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-2xl font-bold text-orange-600">{{ $stats['pending'] ?? 0 }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Bekleyen İlanlar</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtre Sistemi -->
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200 p-6">
        <form method="GET" action="{{ route('admin.ilanlar.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Arama</label>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="İlan başlığı, referans no..."
                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-all duration-200"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Status</label>
                    <select  name="status" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                        <option value="">Tümü</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="inceleme" {{ request('status') === 'inceleme' ? 'selected' : '' }}>Review</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Kategori</label>
                    <select  name="kategori_id" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                        <option value="">Tümü</option>
                        @if(isset($kategoriler))
                            @foreach($kategoriler as $kategori)
                                <option value="{{ $kategori->id }}" {{ request('kategori_id') == $kategori->id ? 'selected' : '' }}>
                                    {{ $kategori->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Kiralama Türü</label>
                    <select name="kiralama_turu" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                        <option value="">Tümü</option>
                        <option value="gunluk" {{ request('kiralama_turu') == 'gunluk' ? 'selected' : '' }}>Günlük</option>
                        <option value="haftalik" {{ request('kiralama_turu') == 'haftalik' ? 'selected' : '' }}>Haftalık</option>
                        <option value="aylik" {{ request('kiralama_turu') == 'aylik' ? 'selected' : '' }}>Aylık</option>
                        <option value="uzun_donem" {{ request('kiralama_turu') == 'uzun_donem' ? 'selected' : '' }}>Uzun Dönem</option>
                        <option value="sezonluk" {{ request('kiralama_turu') == 'sezonluk' ? 'selected' : '' }}>Sezonluk</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Sıralama</label>
                    <select  name="sort" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                        <option value="created_desc" {{ request('sort') === 'created_desc' ? 'selected' : '' }}>En Yeni</option>
                        <option value="created_asc" {{ request('sort') === 'created_asc' ? 'selected' : '' }}>En Eski</option>
                        <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Fiyat (Yüksek-Düşük)</option>
                        <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Fiyat (Düşük-Yüksek)</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-4">
                <a href="{{ route('admin.ilanlar.index') }}" class="inline-flex items-center px-6 py-3 bg-gray-600 text-white font-semibold rounded-lg shadow-md hover:bg-gray-700 hover:scale-105 hover:shadow-lg active:scale-95 focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all duration-200">Temizle</a>
                <button type="submit" class="inline-flex items-center px-6 py-3 bg-orange-600 text-white font-semibold rounded-lg shadow-md hover:bg-orange-700 hover:scale-105 hover:shadow-lg active:scale-95 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:outline-none transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Filtrele
                </button>
            </div>
        </form>
    </div>

    <!-- İlan Listesi -->
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">İlan Listesi</h3>
            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $ilanlar->total() }} ilan</span>
        </div>

        <div class="p-6" x-data="bulkActionsManager()">
            @if($ilanlar->count() > 0)
                {{-- Bulk Actions Toolbar --}}
                <div x-show="selectedIds.length > 0" 
                     x-transition
                     class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 px-6 py-4 flex items-center justify-between mb-4 rounded-lg">
                    
                    <div class="flex items-center text-sm font-medium text-blue-800 dark:text-blue-300">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span x-text="`${selectedIds.length} ilan seçildi`"></span>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        {{-- Activate Button --}}
                        <button type="button"
                                @click="bulkAction('activate')"
                                :disabled="processing"
                                class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 hover:scale-105 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span x-show="!processing">Aktif Yap</span>
                            <span x-show="processing">İşleniyor...</span>
                        </button>
                        
                        {{-- Deactivate Button --}}
                        <button type="button"
                                @click="bulkAction('deactivate')"
                                :disabled="processing"
                                class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white text-sm font-medium rounded-lg hover:bg-yellow-700 hover:scale-105 focus:ring-2 focus:ring-yellow-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Pasif Yap
                        </button>
                        
                        {{-- Delete Button --}}
                        <button type="button"
                                @click="confirmBulkDelete()"
                                :disabled="processing"
                                class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 hover:scale-105 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Sil
                        </button>
                        
                        {{-- Clear Selection --}}
                        <button type="button"
                                @click="clearSelection()"
                                class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white underline">
                            Seçimi Temizle
                        </button>
                    </div>
                </div>

                <div class="w-full overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr>
                                <th class="admin-table-th w-12">
                                    <input type="checkbox" 
                                           id="select-all" 
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                           x-model="selectAll"
                                           @change="toggleSelectAll()">
                                </th>
                                <th class="admin-table-th">İlan</th>
                                <th class="admin-table-th">Tür & Kategori</th>
                                <th class="admin-table-th">Fiyat</th>
                                <th class="admin-table-th">İlan Sahibi</th>
                                <th class="admin-table-th">Danışman</th>
                                <th class="admin-table-th">Status</th>
                                <th class="admin-table-th">Güncellenme</th>
                                <th class="admin-table-th" width="150">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ilanlar as $ilan)
                            <tr>
                                {{-- Checkbox Column --}}
                                <td class="px-6 py-4">
                                    <input type="checkbox" 
                                           class="row-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                           value="{{ $ilan->id }}"
                                           x-model="selectedIds"
                                           @change="updateSelectAll()">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-16 w-16">
                                            @php
                                                $firstPhoto = $ilan->fotograflar?->first();
                                                $photoPath = $firstPhoto?->dosya_yolu;
                                            @endphp
                                            @if($photoPath && file_exists(storage_path('app/public/' . $photoPath)))
                                                <img class="h-16 w-16 rounded-lg object-cover"
                                                     src="{{ asset('storage/' . $photoPath) }}"
                                                     alt="İlan görseli">
                                            @else
                                                <div class="h-16 w-16 rounded-lg bg-gray-200 dark:bg-gray-800 flex items-center justify-center">
                                                    <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                <a href="{{ route('admin.ilanlar.show', $ilan->id) }}"
                                                   class="hover:text-blue-600 dark:hover:text-blue-400">
                                                    {{ $ilan->baslik ?? 'İlan #' . $ilan->id }}
                                                </a>
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                ID: #{{ $ilan->id }}
                                                @if($ilan->il && $ilan->ilce)
                                                    • {{ $ilan->il->il_adi }}, {{ $ilan->ilce->ilce_adi }}
                                                @endif
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
                                        @if($ilan->altKategori)
                                            → {{ $ilan->altKategori->name }}
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                                    {{ number_format($ilan->fiyat ?? 0, 0, ',', '.') }} {{ $ilan->para_birimi ?? 'TRY' }}
                                    @if ($ilan->kiralama_turu)
                                        @switch($ilan->kiralama_turu)
                                            @case('gunluk')
                                                <span class="text-xs text-gray-600 dark:text-gray-400">/Gün</span>
                                            @break
                                            @case('haftalik')
                                                <span class="text-xs text-gray-600 dark:text-gray-400">/Hafta</span>
                                            @break
                                            @case('aylik')
                                            @case('uzun_donem')
                                                <span class="text-xs text-gray-600 dark:text-gray-400">/Ay</span>
                                            @break
                                            @case('sezonluk')
                                                <span class="text-xs text-gray-600 dark:text-gray-400">/Sezon</span>
                                            @break
                                        @endswitch
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                    @if($ilan->ilanSahibi)
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                                                <span class="text-xs font-semibold text-blue-600 dark:text-blue-400">
                                                    {{ substr($ilan->ilanSahibi->ad, 0, 1) }}{{ substr($ilan->ilanSahibi->soyad, 0, 1) }}
                                                </span>
                                            </div>
                                            <div class="ml-2">
                                                <div class="text-sm font-medium">{{ $ilan->ilanSahibi->ad }} {{ $ilan->ilanSahibi->soyad }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $ilan->ilanSahibi->telefon }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                    @if($ilan->userDanisman)
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center">
                                                <span class="text-xs font-semibold text-purple-600 dark:text-purple-400">
                                                    {{ substr($ilan->userDanisman->name, 0, 2) }}
                                                </span>
                                            </div>
                                            <div class="ml-2">
                                                <div class="text-sm font-medium">{{ $ilan->userDanisman->name }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $ilan->userDanisman->email }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    {{-- Inline Status Toggle --}}
                                    <div x-data="statusToggle({{ $ilan->id }}, '{{ $ilan->status ?? 'Taslak' }}')" 
                                         @click.outside="open = false"
                                         class="relative inline-block">
                                        
                                        {{-- Clickable Badge --}}
                                        <button @click="open = !open"
                                                type="button"
                                                :disabled="updating"
                                                class="px-3 py-1 text-xs font-semibold rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all duration-200 cursor-pointer disabled:opacity-50"
                                                :class="getStatusClasses()">
                                            <span x-text="currentStatus"></span>
                                            <svg class="w-3 h-3 ml-1 inline transition-transform duration-200" 
                                                 :class="{'rotate-180': open}"
                                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>
                                        
                                        {{-- Dropdown Menu --}}
                                        <div x-show="open"
                                             x-transition
                                             class="absolute z-50 mt-2 w-48 rounded-lg shadow-lg bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 py-1">
                                            
                                            <button @click="changeStatus('Aktif')"
                                                    type="button"
                                                    class="w-full text-left px-4 py-2 text-sm hover:bg-green-50 dark:hover:bg-green-900/20 flex items-center transition-colors"
                                                    :class="{ 'bg-green-50 dark:bg-green-900/20': currentStatus === 'Aktif' }">
                                                <span class="w-2 h-2 rounded-full bg-green-500 mr-3"></span>
                                                <span class="text-green-700 dark:text-green-300 font-medium">Aktif</span>
                                            </button>
                                            
                                            <button @click="changeStatus('Beklemede')"
                                                    type="button"
                                                    class="w-full text-left px-4 py-2 text-sm hover:bg-yellow-50 dark:hover:bg-yellow-900/20 flex items-center transition-colors"
                                                    :class="{ 'bg-yellow-50 dark:bg-yellow-900/20': currentStatus === 'Beklemede' }">
                                                <span class="w-2 h-2 rounded-full bg-yellow-500 mr-3"></span>
                                                <span class="text-yellow-700 dark:text-yellow-300 font-medium">Beklemede</span>
                                            </button>
                                            
                                            <button @click="changeStatus('Taslak')"
                                                    type="button"
                                                    class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center transition-colors"
                                                    :class="{ 'bg-gray-50 dark:bg-gray-800': currentStatus === 'Taslak' }">
                                                <span class="w-2 h-2 rounded-full bg-gray-500 mr-3"></span>
                                                <span class="text-gray-900 dark:text-white font-medium">Taslak</span>
                                            </button>
                                            
                                            <button @click="changeStatus('Pasif')"
                                                    type="button"
                                                    class="w-full text-left px-4 py-2 text-sm hover:bg-red-50 dark:hover:bg-red-900/20 flex items-center transition-colors"
                                                    :class="{ 'bg-red-50 dark:bg-red-900/20': currentStatus === 'Pasif' }">
                                                <span class="w-2 h-2 rounded-full bg-red-500 mr-3"></span>
                                                <span class="text-red-700 dark:text-red-300 font-medium">Pasif</span>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $ilan->updated_at?->format('d.m.Y H:i') ?? '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('admin.ilanlar.show', $ilan->id) }}"
                                           class="text-blue-600 hover:text-blue-900 dark:text-blue-400"
                                           title="Görüntüle">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.ilanlar.edit', $ilan->id) }}"
                                           class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400"
                                           title="Düzenle">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $ilanlar->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">İlan Bulunamadı</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Arama kriterlerinize uygun ilan bulunmamaktadır.</p>
                    <div class="mt-6">
                        <a href="{{ route('admin.ilanlar.create') }}" class="inline-flex items-center px-6 py-3 bg-orange-600 text-white font-semibold rounded-lg shadow-md hover:bg-orange-700 hover:scale-105 hover:shadow-lg active:scale-95 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:outline-none transition-all duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
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
            
            if (confirm(`${this.selectedIds.length} ilanı silmek istediğinize emin misiniz? Bu işlem geri alınamaz.`)) {
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
                const response = await fetch('{{ route("admin.ilanlar.bulk-action") }}', {
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
                    body: JSON.stringify({ status: newStatus }),
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
@endpush

@endsection
