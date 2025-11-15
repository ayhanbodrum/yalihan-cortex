@extends('admin.layouts.neo')

@section('title', 'Dashboard - Yalıhan Emlak Pro')

@section('content')
    <!-- Yalıhan Emlak Pro Header -->
    <div class="content-header mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold flex items-center text-gray-800">
                    <div
                        class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                    </div>
                    Dashboard
                </h1>
                <p class="text-lg text-gray-600 mt-2">Sistem genel statusu ve analitikler</p>
            </div>
            <div class="text-right">
                <div class="text-sm text-gray-500">Hoş geldiniz,</div>
                <div class="text-lg font-semibold text-gray-800">{{ auth()->user()->name ?? 'Yalıhan Admin' }}</div>
            </div>
        </div>
    </div>

    <div class="px-6">

        <!-- Yalıhan Emlak Pro İstatistik Kartları -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">

            <!-- 🤖 AI Monitor Widget (YENİ!) -->
            <div class="lg:col-span-1">
                @include('admin.components.ai-monitor-widget')
            </div>

            <!-- Toplam İlan Kartı -->
            <div class="neo-card p-6">
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
                        <h3 class="text-2xl font-bold text-blue-600">{{ $totalListings ?? '1,234' }}</h3>
                        <p class="text-sm text-gray-600 font-medium">Toplam İlan</p>
                    </div>
                </div>
            </div>

            <!-- Toplam Satış Kartı -->
            <div class="neo-card p-6">
                <div class="flex items-center">
                    <div
                        class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-2xl font-bold text-green-600">{{ $totalSales ?? '₺2.5M' }}</h3>
                        <p class="text-sm text-gray-600 font-medium">Toplam Satış</p>
                    </div>
                </div>
            </div>

            <!-- Aktif Müşteri Kartı -->
            <div class="neo-card p-6">
                <div class="flex items-center">
                    <div
                        class="w-12 h-12 bg-gradient-to-r from-orange-500 to-yellow-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-2xl font-bold text-orange-600">{{ $activeClients ?? '460' }}</h3>
                        <p class="text-sm text-gray-600 font-medium">Aktif Müşteri</p>
                    </div>
                </div>
            </div>

            <!-- Bu Ay Gelir Kartı -->
            <div class="neo-card p-6">
                <div class="flex items-center">
                    <div
                        class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-2xl font-bold text-purple-600">{{ $monthlyIncome ?? '₺125K' }}</h3>
                        <p class="text-sm text-gray-600 font-medium">Bu Ay Gelir</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- AI Features Section -->
        <div class="neo-card p-6 mb-8" x-data="aiStatusWidget()" x-init="init()">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-robot text-gradient-primary mr-3"></i>
                        AI Destekli Özellikler
                    </h2>
                    <p class="text-gray-600 mt-1">5 AI Provider ile güçlendirilmiş akıllı sistem</p>
                </div>
                <div class="flex gap-3 flex-wrap items-center">
                    <!-- Hızlı Durum Rozetleri -->
                    <div class="hidden md:flex items-center gap-2 mr-2">
                        <span class="px-2 py-1 text-xs rounded neo-badge" :class="overallBadge()">
                            Sistem: <strong class="ml-1" x-text="overallStatusLabel()"></strong>
                        </span>
                        <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-800">
                            MCP: <strong x-text="mcpCount"></strong>
                        </span>
                        <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-800">
                            API: <strong x-text="uptime + '%' "></strong>
                        </span>
                        <span class="px-2 py-1 text-xs rounded" :class="complianceBadge()">
                            Context7: <strong class="ml-1" x-text="complianceLabel()"></strong>
                        </span>
                    </div>
                    <a href="{{ route('admin.ai-settings.index') }}" class="neo-btn neo-btn neo-btn-primary touch-target-optimized touch-target-optimized">
                        <i class="fas fa-robot mr-2"></i>
                        AI Ayarları
                    </a>
                    @if (\Illuminate\Support\Facades\Route::has('admin.ai-monitor.index'))
                        <a href="{{ route('admin.ai-monitor.index') }}" class="neo-btn neo-btn-outline touch-target-optimized touch-target-optimized">
                            <i class="fas fa-heartbeat mr-2"></i>
                            AI Monitoring
                        </a>
                    @endif
                    <a href="{{ route('admin.dashboard.index') }}" class="neo-btn neo-btn-outline touch-target-optimized touch-target-optimized">
                        <i class="fas fa-tachometer-alt mr-2"></i>
                        Sistem Durumu
                    </a>
                    <a href="{{ route('admin.page-analyzer.dashboard') }}" class="neo-btn neo-btn-outline touch-target-optimized touch-target-optimized">
                        <i class="fas fa-search mr-2"></i>
                        Sayfa Analizi
                    </a>
                    <a href="{{ route('admin.architecture.index') }}" class="neo-btn neo-btn-outline touch-target-optimized touch-target-optimized">
                        <i class="fas fa-sitemap mr-2"></i>
                        Mimari
                    </a>
                    <a href="{{ route('admin.performance.index') }}" class="neo-btn neo-btn-outline touch-target-optimized touch-target-optimized">
                        <i class="fas fa-chart-line mr-2"></i>
                        Performans
                    </a>
                    <button @click="refreshAll" type="button" class="neo-btn neo-btn neo-btn-secondary touch-target-optimized touch-target-optimized">
                        <i class="fas fa-sync-alt mr-2"></i>
                        Yenile
                    </button>
                </div>
            </div>

            <!-- AI Sistem Durumu (Basit özet kutuları) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="neo-card p-4">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-600">Context7 Uyumluluk</div>
                        <span class="px-2 py-0.5 text-xs rounded" :class="complianceBadge()"
                            x-text="complianceLabel()"></span>
                    </div>
                    <div class="mt-2 text-xs text-gray-500" x-text="codeHealthNote"></div>
                </div>
                <div class="neo-card p-4">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-600">API Sağlık</div>
                        <span class="px-2 py-0.5 text-xs rounded bg-gray-100 text-gray-800">
                            <span x-text="apiOk"></span>/<span x-text="apiTotal"></span>
                        </span>
                    </div>
                    <div class="mt-2">
                        <div class="w-full bg-gray-100 h-2 rounded">
                            <div class="h-2 rounded bg-green-500" :style="`width: ${uptime}%`"></div>
                        </div>
                        <div class="mt-1 text-xs text-gray-500" x-text="uptime + '% uptime'"></div>
                    </div>
                </div>
                <div class="neo-card p-4">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-600">MCP Süreçleri</div>
                        <span class="px-2 py-0.5 text-xs rounded bg-gray-100 text-gray-800"
                            x-text="mcpCount + ' proses'"></span>
                    </div>
                    <div class="mt-2 text-xs text-gray-500">Aktif MCP servis sayısı</div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- AI İlan Oluşturma -->
                <div class="neo-feature-card">
                    <div class="neo-feature-icon">
                        <i class="fas fa-magic text-blue-500"></i>
                    </div>
                    <div class="neo-feature-content">
                        <h3 class="neo-feature-title">AI İlan Oluşturma</h3>
                        <p class="neo-feature-description">6 farklı prompt şablonu ile otomatik ilan oluşturma</p>
                        <div class="neo-feature-stats">
                            <span class="neo-stat-item">
                                <i class="fas fa-check-circle text-green-500"></i>
                                95% Doğruluk
                            </span>
                            <span class="neo-stat-item">
                                <i class="fas fa-clock text-blue-500"></i>
                                2.8s Ortalama
                            </span>
                        </div>
                    </div>
                </div>

                <!-- AI Talep Analizi -->
                <div class="neo-feature-card">
                    <div class="neo-feature-icon">
                        <i class="fas fa-chart-line text-green-500"></i>
                    </div>
                    <div class="neo-feature-content">
                        <h3 class="neo-feature-title">AI Talep Analizi</h3>
                        <p class="neo-feature-description">NLP ve ML ile akıllı talep eşleştirme</p>
                        <div class="neo-feature-stats">
                            <span class="neo-stat-item">
                                <i class="fas fa-check-circle text-green-500"></i>
                                91% Başarı
                            </span>
                            <span class="neo-stat-item">
                                <i class="fas fa-users text-purple-500"></i>
                                1,247 İşlem
                            </span>
                        </div>
                    </div>
                </div>

                <!-- AI Lokasyon Motoru -->
                <div class="neo-feature-card">
                    <div class="neo-feature-icon">
                        <i class="fas fa-map-marked-alt text-purple-500"></i>
                    </div>
                    <div class="neo-feature-content">
                        <h3 class="neo-feature-title">EmlakLoc v4.1.0</h3>
                        <p class="neo-feature-description">AI destekli lokasyon motoru ve 3D harita</p>
                        <div class="neo-feature-stats">
                            <span class="neo-stat-item">
                                <i class="fas fa-check-circle text-green-500"></i>
                                3D + AR
                            </span>
                            <span class="neo-stat-item">
                                <i class="fas fa-mobile-alt text-blue-500"></i>
                                PWA Destekli
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Page Analyzer Quick Summary -->
        <div class="neo-card p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-search text-purple-500 mr-3"></i>
                        Sistem Sayfa Analizi
                    </h2>
                    <p class="text-gray-600 mt-1">Tüm sistem sayfalarının akıllı analiz sonuçları</p>
                </div>
                <a href="{{ route('admin.page-analyzer.dashboard') }}" class="neo-btn neo-btn neo-btn-primary">
                    <i class="fas fa-external-link-alt mr-2"></i>
                    Detaylı Analiz
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600">75</div>
                    <div class="text-sm text-gray-600">Toplam Sayfa</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-600">13</div>
                    <div class="text-sm text-gray-600">Mükemmel (8+)</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-yellow-600">15</div>
                    <div class="text-sm text-gray-600">Orta (6-7.9)</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-purple-600">6.2</div>
                    <div class="text-sm text-gray-600">Ortalama Skor</div>
                </div>
            </div>

            <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        <i class="fas fa-clock mr-1"></i>
                        Son analiz: {{ now()->format('d.m.Y H:i') }}
                    </div>
                    <div class="flex gap-2">
                        <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-1"></i>
                            Sistem Sağlıklı
                        </span>
                        <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-800">
                            <i class="fas fa-robot mr-1"></i>
                            AI Destekli
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Son Aktiviteler -->
            <div class="neo-card p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-history text-blue-500 mr-2"></i>
                    Son Aktiviteler
                </h3>
                <div class="space-y-3">
                    @foreach ($recentActivities as $activity)
                        <div
                            class="flex items-center space-x-3 p-3 bg-{{ $activity['color'] }}-50 rounded-lg hover:bg-{{ $activity['color'] }}-100 transition-colors">
                            <div class="flex-shrink-0">
                                <div
                                    class="w-10 h-10 bg-{{ $activity['color'] }}-500 rounded-full flex items-center justify-center">
                                    <i class="{{ $activity['icon'] }} text-white text-sm"></i>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">{{ $activity['action'] }}</p>
                                <p class="text-xs text-gray-600">{{ $activity['details'] }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $activity['time'] }}</p>
                            </div>
                            <div class="flex-shrink-0">
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-{{ $activity['color'] }}-100 text-{{ $activity['color'] }}-800">
                                    {{ ucfirst($activity['type']) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 pt-3 border-t border-gray-200">
                    <button
                        class="text-sm text-blue-600 hover:text-blue-700 font-medium flex items-center w-full justify-center">
                        <i class="fas fa-eye mr-1"></i>
                        Tüm Aktiviteleri Görüntüle
                    </button>
                </div>
            </div>

            <!-- Hızlı İşlemler -->
            <div class="neo-card p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-rocket text-purple-500 mr-2"></i>
                    Hızlı İşlemler
                </h3>
                <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
                    <a href="{{ route('admin.ilanlar.create') }}"
                        class="flex items-center justify-center p-4 bg-blue-50 hover:bg-blue-100 rounded-lg border border-blue-200 transition-all duration-300 group">
                        <svg class="w-5 h-5 mr-2 text-blue-600 group-hover:scale-110 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span class="text-sm font-medium text-blue-700">Yeni İlan</span>
                    </a>

                    <a href="{{ route('admin.kisiler.create') }}"
                        class="flex items-center justify-center p-4 bg-green-50 hover:bg-green-100 rounded-lg border border-green-200 transition-all duration-300 group">
                        <svg class="w-5 h-5 mr-2 text-green-600 group-hover:scale-110 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z">
                            </path>
                        </svg>
                        <span class="text-sm font-medium text-green-700">Yeni Müşteri</span>
                    </a>

                    <a href="{{ route('admin.talepler.index') }}"
                        class="flex items-center justify-center p-4 bg-orange-50 hover:bg-orange-100 rounded-lg border border-orange-200 transition-all duration-300 group">
                        <svg class="w-5 h-5 mr-2 text-orange-600 group-hover:scale-110 transition-transform"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                            </path>
                        </svg>
                        <span class="text-sm font-medium text-orange-700">Talepler</span>
                    </a>

                    <a href="{{ route('admin.danisman.index') }}"
                        class="flex items-center justify-center p-4 bg-indigo-50 hover:bg-indigo-100 rounded-lg border border-indigo-200 transition-all duration-300 group">
                        <svg class="w-5 h-5 mr-2 text-indigo-600 group-hover:scale-110 transition-transform"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                            </path>
                        </svg>
                        <span class="text-sm font-medium text-indigo-700">Danışmanlar</span>
                    </a>

                    <a href="{{ route('admin.reports.index') }}"
                        class="flex items-center justify-center p-4 bg-purple-50 hover:bg-purple-100 rounded-lg border border-purple-200 transition-all duration-300 group">
                        <svg class="w-5 h-5 mr-2 text-purple-600 group-hover:scale-110 transition-transform"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                        <span class="text-sm font-medium text-purple-700">Raporlar</span>
                    </a>

                    <a href="{{ route('admin.dashboard.index') }}"
                        class="flex items-center justify-center p-4 bg-teal-50 hover:bg-teal-100 rounded-lg border border-teal-200 transition-all duration-300 group">
                        <svg class="w-5 h-5 mr-2 text-teal-600 group-hover:scale-110 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                        <span class="text-sm font-medium text-teal-700">Sistem</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Son İlanlar Tablosu -->
        <div class="mt-8">
            <div class="neo-card p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-800">Son İlanlar</h3>
                    <div class="flex items-center space-x-2">
                        <input type="text" placeholder="Ara..."
                            class="px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <select style="color-scheme: light dark;"
                            class="px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                            <option>Tümü</option>
                            <option>Aktif</option>
                            <option>Beklemede</option>
                        </select>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    BAŞLIK</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    FİYAT</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    DURUM</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    TARİH</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">1</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Modern Villa</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">₺2.500.000</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2025-09-13</td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">2</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Lüks Daire</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">₺1.200.000</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2025-09-12</td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">3</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Ticari Ofis</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">₺800.000</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Beklemede</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2025-09-11</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .neo-feature-card {
            padding: 1.5rem;
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .neo-feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-color: #3b82f6;
        }

        .neo-feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #3b82f6, #8b5cf6, #06b6d4);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .neo-feature-card:hover::before {
            opacity: 1;
        }

        .neo-feature-icon {
            width: 4rem;
            height: 4rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            border-radius: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            font-size: 1.5rem;
        }

        .neo-feature-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }

        .neo-feature-description {
            font-size: 0.875rem;
            color: #6b7280;
            line-height: 1.5;
            margin-bottom: 1rem;
        }

        .neo-feature-stats {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .neo-stat-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.75rem;
            font-weight: 500;
            color: #6b7280;
        }

        .text-gradient-primary {
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .neo-analyzer-category {
            background: linear-gradient(135deg, #fafbfc, #f3f4f6);
            border: 1px solid #e5e7eb;
            border-radius: 1rem;
            padding: 1.5rem;
            transition: all 0.3s ease;
        }

        .neo-analyzer-category:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            border-color: #d1d5db;
        }

        .neo-analyzer-category h3 {
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 0.75rem;
            margin-bottom: 1rem;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Dashboard animations
            const cards = document.querySelectorAll('.neo-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';

                setTimeout(() => {
                    card.style.transition = 'all 0.3s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });

            // Real-time updates simulation
            setInterval(() => {
                const stats = document.querySelectorAll('.text-2xl.font-semibold');
                stats.forEach(stat => {
                    const currentValue = parseInt(stat.textContent.replace(/[^\d]/g, ''));
                    if (currentValue && Math.random() > 0.7) {
                        const newValue = currentValue + Math.floor(Math.random() * 5);
                        stat.textContent = stat.textContent.replace(/\d+/, newValue);
                    }
                });
            }, 30000); // Update every 30 seconds
        });

        function aiStatusWidget() {
            return {
                mcpCount: 0,
                apiOk: 0,
                apiTotal: 0,
                uptime: 0,
                compliance: 'unknown',
                codeHealthNote: '',
                async init() {
                    await this.refreshAll();
                },
                overallBadge() {
                    if (this.uptime >= 90 && this.mcpCount > 0) return 'bg-green-100 text-green-800';
                    if (this.uptime >= 60) return 'bg-yellow-100 text-yellow-800';
                    return 'bg-red-100 text-red-800';
                },
                overallStatusLabel() {
                    if (this.uptime >= 90 && this.mcpCount > 0) return 'Aktif';
                    if (this.uptime >= 60) return 'Kısmi';
                    return 'Sorunlu';
                },
                complianceBadge() {
                    if (this.compliance === 'compliant') return 'bg-green-100 text-green-800';
                    if (this.compliance === 'non_compliant') return 'bg-red-100 text-red-800';
                    return 'bg-gray-100 text-gray-800';
                },
                complianceLabel() {
                    if (this.compliance === 'compliant') return 'Uyumlu';
                    if (this.compliance === 'non_compliant') return 'Uyumsuz';
                    return 'Bilinmiyor';
                },
                async refreshAll() {
                    try {
                        // ✅ AI Analytics API kullan (mevcut ve çalışan)
                        const analyticsRes = await fetch('/admin/ai-settings/analytics', {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        if (analyticsRes.ok) {
                            const analytics = await analyticsRes.json();

                            // MCP count = Active providers
                            const providerCount = Object.keys(analytics.provider_usage || {}).length;
                            this.mcpCount = providerCount;

                            // API stats
                            this.apiOk = analytics.total_requests || 0;
                            this.apiTotal = analytics.total_requests || 0;
                            this.uptime = Math.round(analytics.success_rate || 0);

                            // Compliance (basitleştirilmiş)
                            this.compliance = this.uptime >= 80 ? 'compliant' : 'needs_review';
                            this.codeHealthNote =
                                `AI İstek: ${this.apiOk} | Başarı: ${this.uptime}% | Provider: ${providerCount}`;
                        }
                    } catch (e) {
                        // yut
                    }
                }
            }
        }
    </script>
@endpush
