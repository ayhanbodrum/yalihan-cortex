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
        <a href="{{ route('admin.ilanlar.create') }}" class="neo-btn neo-btn-primary">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Yeni İlan
        </a>
    </div>

    <!-- İstatistik Kartları -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="neo-card p-6">
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

        <div class="neo-card p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-green-600 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-2xl font-bold text-green-600">{{ $stats['active'] ?? 0 }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Active Listings</p>
                </div>
            </div>
        </div>

        <div class="neo-card p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-2xl font-bold text-purple-600">{{ $stats['this_month'] ?? 0 }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">This Month</p>
                </div>
            </div>
        </div>

        <div class="neo-card p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-2xl font-bold text-orange-600">{{ $stats['pending'] ?? 0 }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Pending Listings</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtre Sistemi -->
    <div class="neo-card p-6">
        <form method="GET" action="{{ route('admin.ilanlar.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div>
                    <label class="neo-label">Arama</label>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="İlan başlığı, referans no..."
                        class="neo-input"
                    >
                </div>

                <div>
                    <label class="neo-label">Status</label>
                    <select name="status" class="neo-select">
                        <option value="">Tümü</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="inceleme" {{ request('status') === 'inceleme' ? 'selected' : '' }}>Review</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                </div>

                <div>
                    <label class="neo-label">Kategori</label>
                    <select name="kategori_id" class="neo-select">
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
                    <label class="neo-label">Sıralama</label>
                    <select name="sort" class="neo-select">
                        <option value="created_desc" {{ request('sort') === 'created_desc' ? 'selected' : '' }}>En Yeni</option>
                        <option value="created_asc" {{ request('sort') === 'created_asc' ? 'selected' : '' }}>En Eski</option>
                        <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Fiyat (Yüksek-Düşük)</option>
                        <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Fiyat (Düşük-Yüksek)</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-4">
                <a href="{{ route('admin.ilanlar.index') }}" class="neo-btn neo-btn-secondary">Temizle</a>
                <button type="submit" class="neo-btn neo-btn-primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Filtrele
                </button>
            </div>
        </form>
    </div>

    <!-- İlan Listesi -->
    <div class="neo-card">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">İlan Listesi</h3>
            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $ilanlar->total() }} ilan</span>
        </div>

        <div class="p-6">
            @if($ilanlar->count() > 0)
                <div class="neo-table-responsive">
                    <table class="neo-table">
                        <thead>
                            <tr>
                                <th class="admin-table-th">İlan</th>
                                <th class="admin-table-th">Tür & Kategori</th>
                                <th class="admin-table-th">Fiyat</th>
                                <th class="admin-table-th">Status</th>
                                <th class="admin-table-th">Tarih</th>
                                <th class="admin-table-th" width="150">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ilanlar as $ilan)
                            <tr>
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
                                                <div class="h-16 w-16 rounded-lg bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
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
                                    {{ number_format($ilan->fiyat ?? 0, 0, ',', '.') }} {{ $ilan->para_birimi ?? 'TL' }}
                                </td>
                                <td class="px-6 py-4">
                                    <x-neo.status-badge :status="$ilan->status ?? 'draft'" />
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $ilan->created_at?->format('d.m.Y') ?? '-' }}
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
                        <a href="{{ route('admin.ilanlar.create') }}" class="neo-btn neo-btn-primary">
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
@endsection
