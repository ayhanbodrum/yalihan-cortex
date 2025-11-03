@extends('admin.layouts.neo')

@section('title', 'Raporlar')

@section('content')
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Raporlar</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Sistem raporlarını görüntüleyin ve yönetin</p>
    </div>
        <!-- İstatistik Kartları -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Toplam Rapor -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-200 p-6">
                <div class="flex items-center">
                    <div
                        class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-2xl font-bold text-blue-800">24</h3>
                        <p class="text-sm text-blue-600 font-medium">Toplam Rapor</p>
                    </div>
                </div>
            </div>

            <!-- Bu Ay -->
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border border-green-200 p-6">
                <div class="flex items-center">
                    <div
                        class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-2xl font-bold text-green-800">8</h3>
                        <p class="text-sm text-green-600 font-medium">Bu Ay</p>
                    </div>
                </div>
            </div>

            <!-- Bekleyen -->
            <div class="bg-gradient-to-r from-yellow-50 to-amber-50 rounded-xl border border-yellow-200 p-6">
                <div class="flex items-center">
                    <div
                        class="w-12 h-12 bg-gradient-to-r from-yellow-500 to-amber-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-2xl font-bold text-yellow-800">3</h3>
                        <p class="text-sm text-yellow-600 font-medium">Bekleyen</p>
                    </div>
                </div>
            </div>

            <!-- Tamamlanan -->
            <div class="bg-gradient-to-r from-purple-50 to-violet-50 rounded-xl border border-purple-200 p-6">
                <div class="flex items-center">
                    <div
                        class="w-12 h-12 bg-gradient-to-r from-purple-500 to-violet-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-2xl font-bold text-purple-800">21</h3>
                        <p class="text-sm text-purple-600 font-medium">Tamamlanan</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtreler -->
        <div class="neo-card p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="form-field">
                    <label class="admin-label">Rapor Türü</label>
                    <select class="neo-input w-full">
                        <option value="">Tümü</option>
                        <option value="sales">Satış Raporları</option>
                        <option value="rental">Kiralama Raporları</option>
                        <option value="customer">Müşteri Raporları</option>
                        <option value="financial">Mali Raporlar</option>
                    </select>
                </div>
                <div class="form-field">
                    <label class="admin-label">Tarih Aralığı</label>
                    <select class="neo-input w-full">
                        <option value="">Tümü</option>
                        <option value="today">Bugün</option>
                        <option value="week">Bu Hafta</option>
                        <option value="month">Bu Ay</option>
                        <option value="quarter">Bu Çeyrek</option>
                        <option value="year">Bu Yıl</option>
                    </select>
                </div>
                <div class="form-field">
                    <label class="admin-label">Durum</label>
                    <select class="neo-input w-full">
                        <option value="">Tümü</option>
                        <option value="pending">Bekleyen</option>
                        <option value="processing">İşleniyor</option>
                        <option value="completed">Tamamlanan</option>
                        <option value="failed">Başarısız</option>
                    </select>
                </div>
                <div class="form-field">
                    <label class="admin-label">Arama</label>
                    <input type="text" class="neo-input w-full" placeholder="Rapor adı ara...">
                </div>
            </div>
        </div>

        <!-- Raporlar Tablosu -->
        <div class="neo-card">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Rapor Listesi</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="admin-table-th">
                                Rapor</th>
                            <th class="admin-table-th">Tür
                            </th>
                            <th class="admin-table-th">
                                Durum</th>
                            <th class="admin-table-th">
                                Oluşturulma</th>
                            <th class="admin-table-th">
                                İşlemler</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">Aylık Satış Raporu</div>
                                        <div class="text-sm text-gray-500">Ocak 2024</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Satış
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="status-badge active">
                                    Tamamlandı
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                15 Ocak 2024
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="#" class="text-blue-600 hover:text-blue-900">İndir</a>
                                    <a href="#" class="text-gray-600 hover:text-gray-900">Görüntüle</a>
                                </div>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                            </path>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">Kiralama Analizi</div>
                                        <div class="text-sm text-gray-500">Q4 2023</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="status-badge active">
                                    Kiralama
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="status-badge pending">
                                    İşleniyor
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                10 Ocak 2024
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <span class="text-gray-400">İndir</span>
                                    <a href="#" class="text-gray-600 hover:text-gray-900">Görüntüle</a>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Boş Durum -->
        <div class="text-center py-12 hidden">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                    </path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Henüz rapor bulunmuyor</h3>
            <p class="text-gray-500 mb-6">İlk raporunuzu oluşturmak için aşağıdaki butona tıklayın.</p>
            <button class="neo-btn neo-btn-primary touch-target-optimized touch-target-optimized">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Yeni Rapor Oluştur
            </button>
        </div>
    </div>
@endsection
