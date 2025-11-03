@extends('admin.layouts.neo')

@section('title', 'Admin Dashboard - Yalıhan Emlak')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50">
        <!-- Header -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 mb-8 p-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1
                        class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                        👑 Admin Dashboard
                    </h1>
                    <p class="mt-3 text-lg text-gray-600">
                        Sistem genel statusu ve yönetim paneli
                    </p>
                </div>
                <div class="flex gap-4">
                    <button class="neo-btn neo-btn neo-btn-primary touch-target-optimized touch-target-optimized" onclick="generatePerformanceReport()">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002 2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                        Performans Raporu
                    </button>
                    <button class="neo-btn neo-btn neo-btn-secondary touch-target-optimized touch-target-optimized" onclick="optimizeSystem()">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Sistemi Optimize Et
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="space-y-8">
            <!-- İstatistik Kartları -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Toplam İlanlar -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Toplam İlanlar</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $quickStats['total_ilanlar'] ?? 0 }}</p>
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
                            <p class="text-3xl font-bold text-green-600">{{ $quickStats['active_ilanlar'] ?? 0 }}</p>
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

                <!-- Toplam Kullanıcılar -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Toplam Kullanıcılar</p>
                            <p class="text-3xl font-bold text-purple-600">{{ $quickStats['total_users'] ?? 0 }}</p>
                        </div>
                        <div class="p-3 bg-purple-100 rounded-full">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
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
                            <p class="text-3xl font-bold text-orange-600">{{ $quickStats['total_danismanlar'] ?? 0 }}</p>
                        </div>
                        <div class="p-3 bg-orange-100 rounded-full">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sistem Durumu -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                <h2 class="stat-card-value mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                    Sistem Durumu
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach ($quickStats['system_status'] ?? [] as $service => $status)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <span class="font-medium text-gray-700 capitalize">{{ $service }}</span>
                            <span
                                class="flex items-center text-sm {{ $status === 'online' ? 'text-green-600' : 'text-red-600' }}">
                                <div
                                    class="w-2 h-2 rounded-full mr-2 {{ $status === 'online' ? 'bg-green-500' : 'bg-red-500' }}">
                                </div>
                                {{ $status === 'online' ? 'Online' : 'Offline' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Son İlanlar -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="stat-card-value flex items-center">
                        <svg class="w-6 h-6 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                        Son Eklenen İlanlar
                    </h2>
                    <a href="{{ route('admin.ilanlar.index') }}" class="neo-btn neo-btn neo-btn-primary touch-target-optimized touch-target-optimized">
                        Tümünü Gör
                    </a>
                </div>
                <div class="space-y-4">
                    @forelse($quickStats['recent_ilanlar'] ?? [] as $ilan)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <h3 class="font-medium text-gray-900">{{ $ilan->ilan_basligi }}</h3>
                                <p class="text-sm text-gray-600">{{ number_format($ilan->fiyat) }} TL</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-500">{{ $ilan->created_at->diffForHumans() }}</p>
                                <a href="{{ route('admin.ilanlar.show', $ilan->id) }}"
                                    class="text-blue-600 hover:text-blue-800 text-sm">
                                    Görüntüle
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                            <p>Henüz ilan eklenmemiş</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Hızlı İşlemler -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                <h2 class="stat-card-value mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z">
                        </path>
                    </svg>
                    Hızlı İşlemler
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <a href="{{ route('admin.ilanlar.create') }}"
                        class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                        <svg class="w-8 h-8 text-blue-600 mr-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <div>
                            <h3 class="font-medium text-gray-900">Yeni İlan</h3>
                            <p class="text-sm text-gray-600">İlan ekle</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.kullanicilar.index') }}"
                        class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                        <svg class="w-8 h-8 text-green-600 mr-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                        <div>
                            <h3 class="font-medium text-gray-900">Kullanıcılar</h3>
                            <p class="text-sm text-gray-600">Kullanıcı yönetimi</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.danisman.index') }}"
                        class="flex items-center p-4 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors">
                        <svg class="w-8 h-8 text-orange-600 mr-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                        <div>
                            <h3 class="font-medium text-gray-900">Danışmanlar</h3>
                            <p class="text-sm text-gray-600">Danışman yönetimi</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.reports.index') }}"
                        class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                        <svg class="w-8 h-8 text-purple-600 mr-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002 2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                        <div>
                            <h3 class="font-medium text-gray-900">Raporlar</h3>
                            <p class="text-sm text-gray-600">Sistem raporları</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.ayarlar.index') }}"
                        class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <svg class="w-8 h-8 text-gray-600 mr-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                            </path>
                        </svg>
                        <div>
                            <h3 class="font-medium text-gray-900">Ayarlar</h3>
                            <p class="text-sm text-gray-600">Sistem ayarları</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function generatePerformanceReport() {
                // Performans raporu oluşturma
                console.log('Performans raporu oluşturuluyor...');
            }

            function optimizeSystem() {
                // Sistem optimizasyonu
                console.log('Sistem optimize ediliyor...');
            }
        </script>
    @endpush
@endsection
