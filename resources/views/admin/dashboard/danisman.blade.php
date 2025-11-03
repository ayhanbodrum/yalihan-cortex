@extends('admin.layouts.neo')

@section('title', 'Danışman Dashboard - Yalıhan Emlak')

@push('meta')
    <meta name="description" content="Danışman Dashboard - Kişisel performans ve müşteri yönetimi">
@endpush

@section('content')
    <div x-data="danismanDashboard()" x-init="init()" class="min-h-screen bg-gradient-to-br from-gray-50 to-orange-50">
        <!-- Header -->
        <div class="neo-card mb-8 p-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold bg-gradient-to-r from-orange-600 to-red-600 bg-clip-text text-transparent">
                        🧑‍💼 Danışman Dashboard
                        <span class="ml-3 px-3 py-1 bg-orange-100 text-orange-800 text-sm rounded-full font-medium">CRM
                            Powered</span>
                    </h1>
                    <p class="mt-3 text-lg text-gray-600">
                        Kişisel performans ve müşteri yönetimi
                    </p>
                    <div class="mt-4 flex items-center space-x-4 text-sm text-gray-500">
                        <span class="flex items-center">
                            <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                            Online
                        </span>
                        <span>Son güncellenme: <span x-text="lastUpdated">{{ now()->format('H:i') }}</span></span>
                    </div>
                </div>
                <div class="flex gap-4">
                    <button @click="generateReport()" :disabled="generatingReport" class="neo-btn neo-btn neo-btn-primary touch-target-optimized touch-target-optimized">
                        <svg class="w-5 h-5 mr-2" :class="generatingReport ? 'animate-spin' : ''" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002 2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                        <span x-text="generatingReport ? 'Hazırlanıyor...' : 'Performans Raporum'"></span>
                    </button>
                    <a href="{{ route('admin.ilanlar.create') }}" class="neo-btn neo-btn neo-btn-secondary touch-target-optimized touch-target-optimized">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Yeni İlan Ekle
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="space-y-8">
            <!-- İstatistik Kartları -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- İlanlarım -->
                <div class="neo-card p-6 hover:shadow-lg transition-shadow cursor-pointer"
                    @click="navigateTo('{{ route('admin.ilanlar.index') }}')">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Toplam İlanlarım</p>
                            <p class="text-3xl font-bold text-blue-600" x-text="stats.my_ilanlar">
                                {{ $danismanStats['my_ilanlar'] ?? 0 }}</p>
                            <div class="mt-2 flex items-center text-sm">
                                <span class="text-green-600 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                                    </svg>
                                    +2 bu hafta
                                </span>
                            </div>
                        </div>
                        <div class="p-3 bg-gradient-to-r from-blue-100 to-blue-200 rounded-xl">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Aktif İlanlarım -->
                <div class="neo-card p-6 hover:shadow-lg transition-shadow cursor-pointer"
                    @click="navigateTo('{{ route('admin.ilanlar.index') }}?status=aktif')">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Aktif İlanlarım</p>
                            <p class="text-3xl font-bold text-green-600" x-text="stats.active_ilanlar">
                                {{ $danismanStats['active_ilanlar'] ?? 0 }}</p>
                            <div class="mt-2 flex items-center text-sm">
                                <span class="text-gray-500">
                                    <span x-text="stats.active_ilanlar">{{ $danismanStats['active_ilanlar'] ?? 0 }}</span> /
                                    <span x-text="stats.my_ilanlar">{{ $danismanStats['my_ilanlar'] ?? 0 }}</span> aktif
                                </span>
                            </div>
                        </div>
                        <div class="p-3 bg-gradient-to-r from-green-100 to-green-200 rounded-xl">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Müşterilerim -->
                <div class="neo-card p-6 hover:shadow-lg transition-shadow cursor-pointer"
                    @click="navigateTo('{{ route('admin.kisiler.index') }}')">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Müşterilerim</p>
                            <p class="text-3xl font-bold text-purple-600" x-text="stats.my_musteriler">
                                {{ $danismanStats['my_musteriler'] ?? 0 }}</p>
                            <div class="mt-2 flex items-center text-sm">
                                <span class="text-purple-600 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Son aktivite: 2 saat önce
                                </span>
                            </div>
                        </div>
                        <div class="p-3 bg-gradient-to-r from-purple-100 to-purple-200 rounded-xl">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Taleplerim -->
                <div class="neo-card p-6 hover:shadow-lg transition-shadow cursor-pointer"
                    @click="navigateTo('{{ route('admin.talepler.index') }}')">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Aktif Taleplerim</p>
                            <p class="text-3xl font-bold text-orange-600" x-text="stats.my_talepler">
                                {{ $danismanStats['my_talepler'] ?? 0 }}</p>
                            <div class="mt-2 flex items-center text-sm">
                                <span class="text-orange-600 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    3 yeni talep
                                </span>
                            </div>
                        </div>
                        <div class="p-3 bg-gradient-to-r from-orange-100 to-orange-200 rounded-xl">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Son İlanlarım ve Müşterilerim -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Son İlanlarım -->
                <div class="neo-card p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="stat-card-value flex items-center">
                            <svg class="w-6 h-6 mr-3 text-blue-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                            Son İlanlarım
                        </h2>
                        <a href="{{ route('admin.ilanlar.index') }}" class="neo-btn neo-btn-primary touch-target-optimized touch-target-optimized">
                            Tümünü Gör
                        </a>
                    </div>
                    <div class="space-y-4">
                        @forelse($danismanStats['recent_ilanlar'] ?? [] as $ilan)
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
                                <a href="{{ route('admin.ilanlar.create') }}" class="neo-btn neo-btn-primary mt-4 touch-target-optimized touch-target-optimized">
                                    İlk İlanını Ekle
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Son Müşterilerim -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="stat-card-value flex items-center">
                            <svg class="w-6 h-6 mr-3 text-purple-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                            Son Müşterilerim
                        </h2>
                        <a href="{{ route('admin.kisiler.index') }}" class="neo-btn neo-btn-primary touch-target-optimized touch-target-optimized">
                            Tümünü Gör
                        </a>
                    </div>
                    <div class="space-y-4">
                        @forelse($danismanStats['recent_musteriler'] ?? [] as $musteri)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <h3 class="font-medium text-gray-900">{{ $musteri->ad }} {{ $musteri->soyad }}</h3>
                                    <p class="text-sm text-gray-600">{{ $musteri->email }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-500">{{ $musteri->created_at->diffForHumans() }}</p>
                                    <a href="{{ route('admin.kisiler.show', $musteri->id) }}"
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
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                                <p>Henüz müşteri eklenmemiş</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Hızlı İşlemler -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                <h2 class="stat-card-value mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-3 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

                    <a href="{{ route('admin.kisiler.create') }}"
                        class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                        <svg class="w-8 h-8 text-green-600 mr-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z">
                            </path>
                        </svg>
                        <div>
                            <h3 class="font-medium text-gray-900">Yeni Müşteri</h3>
                            <p class="text-sm text-gray-600">Müşteri ekle</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.talepler.index') }}"
                        class="flex items-center p-4 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors">
                        <svg class="w-8 h-8 text-orange-600 mr-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                            </path>
                        </svg>
                        <div>
                            <h3 class="font-medium text-gray-900">Talepler</h3>
                            <p class="text-sm text-gray-600">Talep yönetimi</p>
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
                            <h3 class="font-medium text-gray-900">Raporlarım</h3>
                            <p class="text-sm text-gray-600">Performans raporları</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.danisman.show', auth()->id()) }}"
                        class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <svg class="w-8 h-8 text-gray-600 mr-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                            </path>
                        </svg>
                        <div>
                            <h3 class="font-medium text-gray-900">Profilim</h3>
                            <p class="text-sm text-gray-600">Profil düzenle</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function danismanDashboard() {
                return {
                    stats: {
                        my_ilanlar: {{ $danismanStats['my_ilanlar'] ?? 0 }},
                        active_ilanlar: {{ $danismanStats['active_ilanlar'] ?? 0 }},
                        my_musteriler: {{ $danismanStats['my_musteriler'] ?? 0 }},
                        my_talepler: {{ $danismanStats['my_talepler'] ?? 0 }}
                    },
                    generatingReport: false,
                    lastUpdated: '{{ now()->format('H:i') }}',

                    init() {
                        this.updateTime();
                        // Auto refresh every 5 minutes
                        setInterval(() => {
                            this.refreshStats();
                        }, 300000);
                    },

                    updateTime() {
                        setInterval(() => {
                            this.lastUpdated = new Date().toLocaleTimeString('tr-TR', {
                                hour: '2-digit',
                                minute: '2-digit'
                            });
                        }, 60000);
                    },

                    async generateReport() {
                        this.generatingReport = true;

                        try {
                            // Simulate report generation
                            await new Promise(resolve => setTimeout(resolve, 2000));

                            // Open report in new tab (or handle as needed)
                            window.open('/admin/danisman/performance-report', '_blank');

                            this.showNotification('Performans raporu oluşturuldu!', 'success');
                        } catch (error) {
                            console.error('Rapor oluşturma hatası:', error);
                            this.showNotification('Rapor oluşturulurken hata oluştu', 'error');
                        } finally {
                            this.generatingReport = false;
                        }
                    },

                    async refreshStats() {
                        try {
                            // Simulate API call for fresh stats
                            const response = await fetch('/admin/danisman-dashboard/stats');
                            if (response.ok) {
                                const data = await response.json();
                                this.stats = data;
                                this.lastUpdated = new Date().toLocaleTimeString('tr-TR', {
                                    hour: '2-digit',
                                    minute: '2-digit'
                                });
                            }
                        } catch (error) {
                            console.error('İstatistik yenileme hatası:', error);
                        }
                    },

                    navigateTo(url) {
                        window.location.href = url;
                    },

                    showNotification(message, type = 'info') {
                        // Basic notification implementation
                        const colors = {
                            success: 'bg-green-500',
                            error: 'bg-red-500',
                            info: 'bg-blue-500'
                        };

                        const notification = document.createElement('div');
                        notification.className =
                            `fixed top-4 right-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-opacity duration-300`;
                        notification.textContent = message;

                        document.body.appendChild(notification);

                        setTimeout(() => {
                            notification.style.opacity = '0';
                            setTimeout(() => {
                                document.body.removeChild(notification);
                            }, 300);
                        }, 3000);
                    }
                }
            }
        </script>
    @endpush
@endsection
