@extends('admin.layouts.neo')

@section('title', 'Admin Raporlar - Yalıhan Emlak')

@section('content')
    <div class="min-h-screen">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm mb-8 p-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1
                        class="text-4xl font-bold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">
                        📊 Admin Raporlar
                    </h1>
                    <p class="mt-3 text-lg text-gray-600">
                        Sistem geneli raporlar ve analitikler
                    </p>
                </div>
                <div class="flex gap-4">
                    <button class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg active:scale-95 touch-target-optimized touch-target-optimized" onclick="generateSystemReport()">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002 2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                        Sistem Raporu
                    </button>
                    <button class="neo-btn neo-btn-secondary touch-target-optimized touch-target-optimized" onclick="exportAllReports()">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        Tümünü Dışa Aktar
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="space-y-8">
            <!-- İstatistik Kartları -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Toplam İlanlar -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Toplam İlanlar</p>
                            <p class="text-3xl font-bold text-blue-600">{{ $stats['total_ilanlar'] ?? 0 }}</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-full">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Aktif İlanlar -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Aktif İlanlar</p>
                            <p class="text-3xl font-bold text-green-600">{{ $stats['active_ilanlar'] ?? 0 }}</p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-full">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Toplam Müşteriler -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Toplam Müşteriler</p>
                            <p class="text-3xl font-bold text-purple-600">{{ $stats['total_kisiler'] ?? 0 }}</p>
                        </div>
                        <div class="p-3 bg-purple-100 rounded-full">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Toplam Danışmanlar -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Toplam Danışmanlar</p>
                            <p class="text-3xl font-bold text-orange-600">{{ $stats['total_danismanlar'] ?? 0 }}</p>
                        </div>
                        <div class="p-3 bg-orange-100 rounded-full">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Aylık İstatistikler -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Bu Ay İlanlar -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Bu Ay İlanlar</h3>
                        <span class="stat-card-value text-blue-600">{{ $stats['monthly_ilanlar'] ?? 0 }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full"
                            style="width: {{ min(($stats['monthly_ilanlar'] ?? 0) * 10, 100) }}%"></div>
                    </div>
                </div>

                <!-- Bu Ay Müşteriler -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Bu Ay Müşteriler</h3>
                        <span class="stat-card-value text-green-600">{{ $stats['monthly_kisiler'] ?? 0 }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full"
                            style="width: {{ min(($stats['monthly_kisiler'] ?? 0) * 10, 100) }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Rapor Kategorileri -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-8">
                <h2 class="stat-card-value mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002 2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                    Rapor Kategorileri
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- İlan Raporları -->
                    <a href="{{ route('admin.reports.ilanlar') }}" class="group">
                        <div
                            class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 hover:from-blue-100 hover:to-blue-200 transition-all duration-300 border border-blue-200">
                            <div class="flex items-center justify-between mb-4">
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                    </path>
                                </svg>
                                <svg class="w-5 h-5 text-blue-600 group-hover:translate-x-1 transition-transform"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">İlan Raporları</h3>
                            <p class="text-sm text-gray-600">İlan performansı ve detaylı analizler</p>
                        </div>
                    </a>

                    <!-- Müşteri Raporları -->
                    <a href="{{ route('admin.reports.musteriler') }}" class="group">
                        <div
                            class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 hover:from-green-100 hover:to-green-200 transition-all duration-300 border border-green-200">
                            <div class="flex items-center justify-between mb-4">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                                <svg class="w-5 h-5 text-green-600 group-hover:translate-x-1 transition-transform"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Müşteri Raporları</h3>
                            <p class="text-sm text-gray-600">Müşteri analizi ve segmentasyon</p>
                        </div>
                    </a>

                    <!-- Performans Raporları -->
                    <a href="{{ route('admin.reports.performance') }}" class="group">
                        <div
                            class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-6 hover:from-purple-100 hover:to-purple-200 transition-all duration-300 border border-purple-200">
                            <div class="flex items-center justify-between mb-4">
                                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6">
                                    </path>
                                </svg>
                                <svg class="w-5 h-5 text-purple-600 group-hover:translate-x-1 transition-transform"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Performans Raporları</h3>
                            <p class="text-sm text-gray-600">Sistem ve danışman performansı</p>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Hızlı Export İşlemleri -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                <h2 class="stat-card-value mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-3 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    Hızlı Export İşlemleri
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <button onclick="exportReport('ilanlar', 'excel')"
                        class="neo-btn neo-btn-secondary w-full justify-center touch-target-optimized touch-target-optimized">
                        <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        <span class="font-medium text-gray-900">İlanlar Excel</span>
                    </button>

                    <button onclick="exportReport('musteriler', 'excel')"
                        class="neo-btn neo-btn-secondary w-full justify-center touch-target-optimized touch-target-optimized">
                        <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        <span class="font-medium text-gray-900">Müşteriler Excel</span>
                    </button>

                    <button onclick="exportReport('ilanlar', 'pdf')"
                        class="neo-btn neo-btn-secondary w-full justify-center touch-target-optimized touch-target-optimized">
                        <svg class="w-6 h-6 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                            </path>
                        </svg>
                        <span class="font-medium text-gray-900">İlanlar PDF</span>
                    </button>

                    <button onclick="exportReport('performance', 'pdf')"
                        class="neo-btn neo-btn-secondary w-full justify-center touch-target-optimized touch-target-optimized">
                        <svg class="w-6 h-6 text-purple-600 mr-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                            </path>
                        </svg>
                        <span class="font-medium text-gray-900">Performans PDF</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function generateSystemReport() {
                // Sistem raporu oluşturma
                console.log('Sistem raporu oluşturuluyor...');
            }

            function exportAllReports() {
                // Tüm raporları dışa aktarma
                console.log('Tüm raporlar dışa aktarılıyor...');
            }

            function exportReport(type, format) {
                const form = document.createElement('form');
                form.method = 'POST';

                // Set action based on format
                if (format === 'excel') {
                    form.action = '{{ route('admin.reports.export.excel') }}';
                } else {
                    form.action = '{{ route('admin.reports.export.pdf') }}';
                }

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';

                const typeInput = document.createElement('input');
                typeInput.type = 'hidden';
                typeInput.name = 'type';
                typeInput.value = type;

                const formatInput = document.createElement('input');
                formatInput.type = 'hidden';
                formatInput.name = 'format';
                formatInput.value = format;

                form.appendChild(csrfToken);
                form.appendChild(typeInput);
                form.appendChild(formatInput);

                document.body.appendChild(form);
                form.submit();
                document.body.removeChild(form);
            }
        </script>
    @endpush
@endsection
