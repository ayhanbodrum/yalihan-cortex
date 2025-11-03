@extends('admin.layouts.neo')

@section('title', 'Müşteri Raporları')

@section('content')
    <!-- Context7 Header -->
    <div class="content-header mb-8">
        <h1 class="text-3xl font-bold flex items-center">
            <div
                class="w-12 h-12 bg-gradient-to-r from-purple-500 to-violet-600 rounded-xl flex items-center justify-center mr-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                    </path>
                </svg>
            </div>
            📊 Müşteri Raporları
        </h1>
        <p class="text-lg text-gray-600 mt-2">Müşteri analizi ve performans raporları</p>
    </div>

    <div class="px-6">
        <!-- İstatistik Kartları -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- Toplam Müşteri Kartı -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-200 p-6">
                <div class="flex items-center">
                    <div
                        class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-2xl font-bold text-blue-800">{{ $stats['total_customers'] ?? 0 }}</h3>
                        <p class="text-sm text-blue-600 font-medium">Toplam Müşteri</p>
                    </div>
                </div>
            </div>

            <!-- Aktif Müşteri Kartı -->
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
                        <h3 class="text-2xl font-bold text-green-800">{{ $stats['active_customers'] ?? 0 }}</h3>
                        <p class="text-sm text-green-600 font-medium">Aktif Müşteri</p>
                    </div>
                </div>
            </div>

            <!-- Yeni Müşteri Kartı -->
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
                        <h3 class="text-2xl font-bold text-orange-800">{{ $stats['new_customers'] ?? 0 }}</h3>
                        <p class="text-sm text-orange-600 font-medium">Bu Ay Yeni</p>
                    </div>
                </div>
            </div>

            <!-- Potansiyel Müşteri Kartı -->
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
                        <h3 class="text-2xl font-bold text-purple-800">{{ $stats['potential_customers'] ?? 0 }}</h3>
                        <p class="text-sm text-purple-600 font-medium">Potansiyel</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtreler -->
        <div class="neo-card p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">🔍 Rapor Filtreleri</h2>
            <form method="GET" action="{{ route('admin.reports.musteriler') }}"
                class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Müşteri Tipi</label>
                    <select name="musteri_tipi" class="neo-input w-full">
                        <option value="">Tümü</option>
                        <option value="ev_sahibi" {{ request('musteri_tipi') == 'ev_sahibi' ? 'selected' : '' }}>Ev Sahibi
                        </option>
                        <option value="satici" {{ request('musteri_tipi') == 'satici' ? 'selected' : '' }}>Satıcı</option>
                        <option value="alici" {{ request('musteri_tipi') == 'alici' ? 'selected' : '' }}>Alıcı</option>
                        <option value="kiraci" {{ request('musteri_tipi') == 'kiraci' ? 'selected' : '' }}>Kiracı</option>
                        <option value="kiralayan" {{ request('musteri_tipi') == 'kiralayan' ? 'selected' : '' }}>Kiralayan
                        </option>
                        <option value="yatirimci" {{ request('musteri_tipi') == 'yatirimci' ? 'selected' : '' }}>Yatırımcı
                        </option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Durum</label>
                    <select name="status" class="neo-input w-full">
                        <option value="">Tümü</option>
                        <option value="Yeni" {{ request('status') == 'Yeni' ? 'selected' : '' }}>Yeni</option>
                        <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Pasif" {{ request('status') == 'Pasif' ? 'selected' : '' }}>Pasif</option>
                        <option value="Potansiyel" {{ request('status') == 'Potansiyel' ? 'selected' : '' }}>Potansiyel
                        </option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tarih Aralığı</label>
                    <select name="date_range" class="neo-input w-full">
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
        <div class="neo-card">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-800">📈 Müşteri Analizi</h2>
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

                <!-- Müşteri Listesi -->
                @if (isset($customers) && $customers->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="admin-table-th">
                                        Müşteri</th>
                                    <th class="admin-table-th">
                                        Tip</th>
                                    <th class="admin-table-th">
                                        Durum</th>
                                    <th class="admin-table-th">
                                        İletişim</th>
                                    <th class="admin-table-th">
                                        Kayıt Tarihi</th>
                                    <th class="admin-table-th">
                                        İşlemler</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($customers as $customer)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div
                                                        class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-indigo-600 flex items-center justify-center">
                                                        <span
                                                            class="text-sm font-medium text-white">{{ substr($customer->ad, 0, 1) }}{{ substr($customer->soyad, 0, 1) }}</span>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $customer->ad }}
                                                        {{ $customer->soyad }}</div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $customer->tc_kimlik ?? 'TC Kimlik Yok' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <x-neo.status-badge :value="ucfirst(str_replace('_', ' ', $customer->musteri_tipi ?? 'Belirtilmemiş'))" category="type" />
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <x-neo.status-badge :value="$customer->status ?? 'Belirtilmemiş'" />
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <div>{{ $customer->telefon ?? 'Telefon Yok' }}</div>
                                            <div class="text-gray-500">{{ $customer->email ?? 'E-posta Yok' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $customer->created_at ? $customer->created_at->format('d.m.Y') : 'Belirtilmemiş' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('admin.kisiler.show', $customer) }}"
                                                    class="text-blue-600 hover:text-blue-900">Görüntüle</a>
                                                <a href="{{ route('admin.kisiler.edit', $customer) }}"
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
                        {{ $customers->appends(request()->query())->links() }}
                    </div>
                @else
                    <x-neo.empty-state title="Müşteri bulunamadı" description="Seçilen kriterlere uygun müşteri bulunmuyor." />
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function exportToExcel() {
            // Excel export functionality
            window.location.href = '{{ route('admin.reports.musteriler') }}?export=excel&' + new URLSearchParams(window
                .location.search).toString();
        }

        function exportToPDF() {
            // PDF export functionality
            window.location.href = '{{ route('admin.reports.musteriler') }}?export=pdf&' + new URLSearchParams(window
                .location.search).toString();
        }
    </script>
@endpush
