@extends('admin.layouts.neo')

@section('title', 'İlan Raporları')

@section('content')
    <!-- Context7 Header -->
    <div class="content-header mb-8">
        <h1 class="text-3xl font-bold flex items-center">
            <div
                class="w-12 h-12 bg-gradient-to-r from-orange-500 to-amber-600 rounded-xl flex items-center justify-center mr-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                    </path>
                </svg>
            </div>
            📊 İlan Raporları
        </h1>
        <p class="text-lg text-gray-600 mt-2">İlan analizi ve performans raporları</p>
    </div>

    <div class="px-6">
        <!-- İstatistik Kartları -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- Toplam İlan Kartı -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-200 p-6">
                <div class="flex items-center">
                    <div
                        class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-2xl font-bold text-blue-800">{{ $stats['total_listings'] ?? 0 }}</h3>
                        <p class="text-sm text-blue-600 font-medium">Toplam İlan</p>
                    </div>
                </div>
            </div>

            <!-- Aktif İlan Kartı -->
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border border-green-200 p-6">
                <div class="flex items-center">
                    <div
                        class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-2xl font-bold text-green-800">{{ $stats['active_listings'] ?? 0 }}</h3>
                        <p class="text-sm text-green-600 font-medium">Aktif İlan</p>
                    </div>
                </div>
            </div>

            <!-- Yeni İlan Kartı -->
            <div class="bg-gradient-to-r from-orange-50 to-amber-50 rounded-xl border border-orange-200 p-6">
                <div class="flex items-center">
                    <div
                        class="w-12 h-12 bg-gradient-to-r from-orange-500 to-amber-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-2xl font-bold text-orange-800">{{ $stats['new_listings'] ?? 0 }}</h3>
                        <p class="text-sm text-orange-600 font-medium">Bu Ay Yeni</p>
                    </div>
                </div>
            </div>

            <!-- Satılan İlan Kartı -->
            <div class="bg-gradient-to-r from-purple-50 to-violet-50 rounded-xl border border-purple-200 p-6">
                <div class="flex items-center">
                    <div
                        class="w-12 h-12 bg-gradient-to-r from-purple-500 to-violet-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-2xl font-bold text-purple-800">{{ $stats['sold_listings'] ?? 0 }}</h3>
                        <p class="text-sm text-purple-600 font-medium">Satılan</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtreler -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">🔍 Rapor Filtreleri</h2>
            <form method="GET" action="{{ route('admin.reports.ilanlar') }}"
                class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">İlan Tipi</label>
                    <select style="color-scheme: light dark;" name="ilan_tipi" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 w-full transition-all duration-200">
                        <option value="">Tümü</option>
                        <option value="satilik" {{ request('ilan_tipi') == 'satilik' ? 'selected' : '' }}>Satılık</option>
                        <option value="kiralik" {{ request('ilan_tipi') == 'kiralik' ? 'selected' : '' }}>Kiralık</option>
                        <option value="arsa" {{ request('ilan_tipi') == 'arsa' ? 'selected' : '' }}>Arsa</option>
                        <option value="isyeri" {{ request('ilan_tipi') == 'isyeri' ? 'selected' : '' }}>İşyeri</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Durum</label>
                    <select style="color-scheme: light dark;" name="status" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 w-full transition-all duration-200">
                        <option value="">Tümü</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="pasif" {{ request('status') == 'pasif' ? 'selected' : '' }}>Pasif</option>
                        <option value="satildi" {{ request('status') == 'satildi' ? 'selected' : '' }}>Satıldı</option>
                        <option value="kiralandi" {{ request('status') == 'kiralandi' ? 'selected' : '' }}>Kiralandı
                        </option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tarih Aralığı</label>
                    <select style="color-scheme: light dark;" name="date_range" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 w-full transition-all duration-200">
                        <option value="">Tümü</option>
                        <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Bugün</option>
                        <option value="week" {{ request('date_range') == 'week' ? 'selected' : '' }}>Bu Hafta</option>
                        <option value="month" {{ request('date_range') == 'month' ? 'selected' : '' }}>Bu Ay</option>
                        <option value="quarter" {{ request('date_range') == 'quarter' ? 'selected' : '' }}>Bu Çeyrek
                        </option>
                        <option value="year" {{ request('date_range') == 'year' ? 'selected' : '' }}>Bu Yıl</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit" class="neo-btn neo-btn-primary w-full touch-target-optimized touch-target-optimized">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Filtrele
                    </button>
                </div>
            </form>
        </div>

        <!-- Rapor İçeriği -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-800">📈 İlan Analizi</h2>
                    <div class="flex space-x-2">
                        <button onclick="exportToExcel()" class="neo-btn neo-btn-secondary touch-target-optimized touch-target-optimized">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            Excel Export
                        </button>
                        <button onclick="exportToPDF()" class="neo-btn neo-btn-secondary touch-target-optimized touch-target-optimized">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                                </path>
                            </svg>
                            PDF Export
                        </button>
                    </div>
                </div>

                <!-- İlan Listesi -->
                @if (isset($listings) && $listings->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="admin-table-th">
                                        İlan</th>
                                    <th class="admin-table-th">
                                        Tip</th>
                                    <th class="admin-table-th">
                                        Durum</th>
                                    <th class="admin-table-th">
                                        Fiyat</th>
                                    <th class="admin-table-th">
                                        Lokasyon</th>
                                    <th class="admin-table-th">
                                        Tarih</th>
                                    <th class="admin-table-th">
                                        İşlemler</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($listings as $listing)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div
                                                        class="h-10 w-10 rounded-full bg-gradient-to-r from-orange-500 to-amber-600 flex items-center justify-center">
                                                        <span
                                                            class="text-sm font-medium text-white">{{ substr($listing->title, 0, 2) }}</span>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ Str::limit($listing->title, 30) }}</div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $listing->property_type ?? 'Belirtilmemiş' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <x-neo.status-badge :value="ucfirst($listing->ilan_tipi ?? 'Belirtilmemiş')" category="type" />
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <x-neo.status-badge :value="ucfirst($listing->status ?? 'Belirtilmemiş')" />
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <div class="font-medium">
                                                {{ number_format($listing->price ?? 0, 0, ',', '.') }} ₺</div>
                                            <div class="text-gray-500">{{ $listing->currency ?? 'TL' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $listing->location ?? 'Belirtilmemiş' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $listing->created_at ? $listing->created_at->format('d.m.Y') : 'Belirtilmemiş' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('admin.ilanlar.show', $listing) }}"
                                                    class="text-blue-600 hover:text-blue-900">Görüntüle</a>
                                                <a href="{{ route('admin.ilanlar.edit', $listing) }}"
                                                    class="text-green-600 hover:text-green-900">Düzenle</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $listings->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <div
                            class="w-16 h-16 bg-gradient-to-r from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">İlan bulunamadı</h3>
                        <p class="text-gray-500">Seçilen kriterlere uygun ilan bulunmuyor.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function exportToExcel() {
            // Excel export functionality
            window.location.href = '{{ route('admin.reports.ilanlar') }}?export=excel&' + new URLSearchParams(window
                .location.search).toString();
        }

        function exportToPDF() {
            // PDF export functionality
            window.location.href = '{{ route('admin.reports.ilanlar') }}?export=pdf&' + new URLSearchParams(window.location
                .search).toString();
        }
    </script>
@endpush
