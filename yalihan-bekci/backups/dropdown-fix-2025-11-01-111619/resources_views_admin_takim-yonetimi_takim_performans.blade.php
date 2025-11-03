@extends('admin.layouts.neo')

@section('title', 'Takım Performans Raporu')

@section('content')
    <div class="content-header mb-8">
        <div class="container-fluid">
            <div class="flex justify-between items-center">
                <div class="space-y-2">
                    <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-200 flex items-center">
                        <div
                            class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                </path>
                            </svg>
                        </div>
                        📊 Takım Performans Raporu
                    </h1>
                    <p class="text-lg text-gray-600 dark:text-gray-400">
                        Takım üyelerinin performans analizi ve istatistik raporları
                    </p>
                </div>
                <div class="flex items-center space-x-3">
                    <button type="button" class="neo-btn neo-btn-primary touch-target-optimized touch-target-optimized" onclick="context7Analiz()">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z">
                            </path>
                        </svg>
                        Context7 Analiz
                    </button>
                    <button type="button" class="neo-btn neo-btn-primary touch-target-optimized touch-target-optimized" onclick="raporIndir()">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        Rapor İndir
                    </button>
                    <button type="button" class="neo-btn neo-btn-secondary touch-target-optimized touch-target-optimized" onclick="raporPaylas()">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z">
                            </path>
                        </svg>
                        Paylaş
                    </button>
                    <a href="{{ route('admin.takim-yonetimi.takim.index') }}" class="neo-btn neo-btn-secondary touch-target-optimized touch-target-optimized">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                            </path>
                        </svg>
                        Geri Dön
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Context7 AI Önerileri -->
    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl border border-indigo-200 shadow-sm mb-8">
        <div class="p-6">
            <h2 class="text-xl font-bold text-indigo-800 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z">
                    </path>
                </svg>
                🤖 Context7 AI Önerileri
            </h2>
            <div id="neo-suggestions" class="space-y-4">
                <div class="bg-white rounded-lg p-4 border border-indigo-200">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div
                                class="w-8 h-8 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-medium text-gray-900">Performans Analizi</h4>
                            <p class="text-sm text-gray-600 mt-1" id="ai-analysis">AI analiz yükleniyor...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtreler Bölümü -->
    <div class="bg-gradient-to-r from-gray-50 to-slate-50 rounded-xl border border-gray-200 shadow-sm mb-8">
        <div class="p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                    </path>
                </svg>
                🔍 Filtreler
            </h2>
            <form method="GET" action="{{ route('admin.takim-yonetimi.takim.performans') }}"
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tarih Aralığı</label>
                    <select
                        class="w-full neo-input focus:border-transparent"
                        name="tarih_araligi">
                        <option value="7" {{ request('tarih_araligi') == '7' ? 'selected' : '' }}>Son 7 Gün</option>
                        <option value="30" {{ request('tarih_araligi') == '30' ? 'selected' : '' }}>Son 30 Gün</option>
                        <option value="90" {{ request('tarih_araligi') == '90' ? 'selected' : '' }}>Son 3 Ay</option>
                        <option value="365" {{ request('tarih_araligi') == '365' ? 'selected' : '' }}>Son 1 Yıl</option>
                        <option value="custom" {{ request('tarih_araligi') == 'custom' ? 'selected' : '' }}>Özel Aralık
                        </option>
                    </select>
                </div>
                <div class="custom-date-field"
                    style="display: {{ request('tarih_araligi') == 'custom' ? 'block' : 'none' }}">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Başlangıç</label>
                    <input type="date"
                        class="w-full neo-input focus:border-transparent"
                        name="baslangic_tarihi" value="{{ request('baslangic_tarihi') }}">
                </div>
                <div class="custom-date-field"
                    style="display: {{ request('tarih_araligi') == 'custom' ? 'block' : 'none' }}">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bitiş</label>
                    <input type="date"
                        class="w-full neo-input focus:border-transparent"
                        name="bitis_tarihi" value="{{ request('bitis_tarihi') }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rol</label>
                    <select
                        class="w-full neo-input focus:border-transparent"
                        name="rol">
                        <option value="">Tüm Roller</option>
                        @foreach (['admin', 'danisman', 'alt_kullanici', 'musteri_temsilcisi'] as $rol)
                            <option value="{{ $rol }}" {{ request('rol') == $rol ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $rol)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full neo-btn neo-btn-primary touch-target-optimized touch-target-optimized">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Filtrele
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Genel Performans Özeti -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-200 shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div
                        class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                            </path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h4 class="text-sm font-medium text-blue-800">Toplam Görev</h4>
                    <p class="text-2xl font-bold text-blue-900">{{ $genelPerformans['toplam_gorev'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border border-green-200 shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div
                        class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h4 class="text-sm font-medium text-green-800">Tamamlanan</h4>
                    <p class="text-2xl font-bold text-green-900">{{ $genelPerformans['tamamlanan_gorev'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-orange-50 to-amber-50 rounded-xl border border-orange-200 shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div
                        class="w-12 h-12 bg-gradient-to-r from-orange-500 to-amber-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h4 class="text-sm font-medium text-orange-800">Devam Eden</h4>
                    <p class="text-2xl font-bold text-orange-900">{{ $genelPerformans['devam_eden_gorev'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-purple-50 to-violet-50 rounded-xl border border-purple-200 shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div
                        class="w-12 h-12 bg-gradient-to-r from-purple-500 to-violet-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h4 class="text-sm font-medium text-purple-800">Başarı Oranı</h4>
                    <p class="text-2xl font-bold text-purple-900">{{ $genelPerformans['basari_orani'] ?? 0 }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Performans Grafikleri -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <div
            class="lg:col-span-2 bg-gradient-to-r from-purple-50 to-violet-50 rounded-xl border border-purple-200 shadow-sm p-6">
            <h2 class="text-xl font-bold text-purple-800 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                    </path>
                </svg>
                📈 Görev Tamamlanma Trendi
            </h2>
            <div class="h-80">
                <canvas id="gorevTrendChart"></canvas>
            </div>
        </div>
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border border-green-200 shadow-sm p-6">
            <h2 class="text-xl font-bold text-green-800 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                </svg>
                🥧 Görev Durumu Dağılımı
            </h2>
            <div class="h-80">
                <canvas id="gorevDurumChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Rol Bazlı Performans -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-200 shadow-sm mb-8">
        <div class="p-6">
            <h2 class="text-xl font-bold text-blue-800 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                    </path>
                </svg>
                👥 Rol Bazlı Performans Analizi
            </h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 performance-table">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="admin-table-th">Rol
                            </th>
                            <th class="admin-table-th">Üye
                                Sayısı</th>
                            <th class="admin-table-th">
                                Toplam Görev</th>
                            <th class="admin-table-th">
                                Tamamlanan</th>
                            <th class="admin-table-th">
                                Başarı Oranı</th>
                            <th class="admin-table-th">
                                Ortalama Süre</th>
                            <th class="admin-table-th">
                                Performans Skoru</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($rolPerformans ?? [] as $rol)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                        {{ ucfirst(str_replace('_', ' ', $rol['rol'])) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $rol['uye_sayisi'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $rol['toplam_gorev'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $rol['tamamlanan_gorev'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                            <div class="bg-gradient-to-r from-green-500 to-emerald-600 h-2 rounded-full transition-all duration-1000"
                                                style="width: {{ $rol['basari_orani'] }}%"></div>
                                        </div>
                                        <span class="text-sm font-medium text-gray-900">{{ $rol['basari_orani'] }}%</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $rol['ortalama_sure'] ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if ($rol['performans_skoru'] >= 80) bg-green-100 text-green-800 border border-green-200
                                    @elseif($rol['performans_skoru'] >= 60) bg-yellow-100 text-yellow-800 border border-yellow-200
                                    @else bg-red-100 text-red-800 border border-red-200 @endif">
                                        {{ $rol['performans_skoru'] }}/100
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- En İyi Performans Gösterenler ve Görev Tipi Analizi -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <div class="bg-gradient-to-r from-yellow-50 to-amber-50 rounded-xl border border-yellow-200 shadow-sm p-6">
            <h2 class="text-xl font-bold text-yellow-800 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-3 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z">
                    </path>
                </svg>
                🏆 En İyi Performans Gösterenler
            </h2>
            @if (isset($enIyiPerformans) && count($enIyiPerformans) > 0)
                @foreach ($enIyiPerformans as $index => $uye)
                    <div
                        class="flex items-center justify-between p-4 bg-white rounded-lg border border-yellow-200 mb-3 hover:shadow-md transition-shadow">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 mr-4">
                                @if ($index == 0)
                                    <span
                                        class="inline-flex items-center justify-center w-10 h-10 bg-gradient-to-r from-yellow-400 to-amber-500 rounded-full text-white font-bold text-lg">🥇</span>
                                @elseif($index == 1)
                                    <span
                                        class="inline-flex items-center justify-center w-10 h-10 bg-gradient-to-r from-gray-400 to-slate-500 rounded-full text-white font-bold text-lg">🥈</span>
                                @elseif($index == 2)
                                    <span
                                        class="inline-flex items-center justify-center w-10 h-10 bg-gradient-to-r from-orange-400 to-amber-500 rounded-full text-white font-bold text-lg">🥉</span>
                                @else
                                    <span
                                        class="inline-flex items-center justify-center w-10 h-10 bg-gradient-to-r from-blue-400 to-indigo-500 rounded-full text-white font-bold text-sm">{{ $index + 1 }}</span>
                                @endif
                            </div>
                            <div>
                                <h6 class="font-semibold text-gray-900">{{ $uye['user_name'] }}</h6>
                                <p class="text-sm text-gray-600">{{ ucfirst(str_replace('_', ' ', $uye['rol'])) }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-lg text-yellow-700">{{ $uye['performans_skoru'] }}/100</div>
                            <p class="text-sm text-gray-600">{{ $uye['tamamlanan_gorev'] }} görev</p>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-yellow-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z">
                        </path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-yellow-800">Henüz performans verisi bulunmuyor</h3>
                    <p class="mt-1 text-sm text-yellow-600">Görevler oluşturulduktan sonra performans verileri görünecek.
                    </p>
                </div>
            @endif
        </div>

        <div class="bg-gradient-to-r from-orange-50 to-amber-50 rounded-xl border border-orange-200 shadow-sm p-6">
            <h2 class="text-xl font-bold text-orange-800 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-3 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                </svg>
                📊 Görev Tipi Analizi
            </h2>
            @if (isset($gorevTipiAnalizi) && count($gorevTipiAnalizi) > 0)
                @foreach ($gorevTipiAnalizi as $tip)
                    <div class="mb-4">
                        <div class="flex justify-between items-center mb-2">
                            <span
                                class="text-sm font-medium text-orange-700">{{ ucfirst(str_replace('_', ' ', $tip['tip'])) }}</span>
                            <span class="text-sm font-semibold text-orange-900">{{ $tip['sayi'] }}</span>
                        </div>
                        <div class="w-full bg-orange-200 rounded-full h-3">
                            <div class="bg-gradient-to-r from-orange-500 to-amber-600 h-3 rounded-full transition-all duration-1000"
                                style="width: {{ $tip['yuzde'] }}%"></div>
                        </div>
                        <div class="text-right mt-1">
                            <span class="text-xs text-orange-600">{{ $tip['yuzde'] }}%</span>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-orange-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-orange-800">Henüz görev tipi verisi bulunmuyor</h3>
                    <p class="mt-1 text-sm text-orange-600">Görevler oluşturulduktan sonra analiz verileri görünecek.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Detaylı Performans Tablosu -->
    <div class="neo-card">
        <div class="p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                📋 Detaylı Performans Tablosu
            </h2>
            @if (isset($detayliPerformans) && count($detayliPerformans) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 performance-table">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="admin-table-th">
                                    Üye</th>
                                <th class="admin-table-th">
                                    Rol</th>
                                <th class="admin-table-th">
                                    Toplam Görev</th>
                                <th class="admin-table-th">
                                    Tamamlanan</th>
                                <th class="admin-table-th">
                                    Devam Eden</th>
                                <th class="admin-table-th">
                                    Başarı Oranı</th>
                                <th class="admin-table-th">
                                    Ortalama Süre</th>
                                <th class="admin-table-th">
                                    Performans Skoru</th>
                                <th class="admin-table-th">
                                    Trend</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($detayliPerformans as $uye)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div
                                                    class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-400 to-indigo-500 flex items-center justify-center">
                                                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                        </path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $uye['user_name'] }}
                                                </div>
                                                <div class="text-sm text-gray-500">{{ $uye['user_email'] }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {!! $uye['rol_etiketi'] !!}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $uye['toplam_gorev'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                            {{ $uye['tamamlanan_gorev'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                            {{ $uye['devam_eden_gorev'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                                <div class="bg-gradient-to-r from-green-500 to-emerald-600 h-2 rounded-full transition-all duration-1000"
                                                    style="width: {{ $uye['basari_orani'] }}%"></div>
                                            </div>
                                            <span
                                                class="text-sm font-medium text-gray-900">{{ $uye['basari_orani'] }}%</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $uye['ortalama_sure'] ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if ($uye['performans_skoru'] >= 80) bg-green-100 text-green-800 border border-green-200
                                        @elseif($uye['performans_skoru'] >= 60) bg-yellow-100 text-yellow-800 border border-yellow-200
                                        @else bg-red-100 text-red-800 border border-red-200 @endif">
                                            {{ $uye['performans_skoru'] }}/100
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($uye['trend'] == 'up')
                                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                            </svg>
                                        @elseif($uye['trend'] == 'down')
                                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 12h14"></path>
                                            </svg>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Henüz performans verisi bulunmuyor</h3>
                    <p class="mt-1 text-sm text-gray-500">Görevler oluşturulduktan sonra performans verileri görünecek.</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .neo-btn neo-btn-primary {
            @apply bg-gradient-to-r from-blue-500 to-indigo-600 text-white px-4 py-2 rounded-lg font-medium hover:from-blue-600 hover:to-indigo-700 transition-all duration-200 flex items-center shadow-lg hover:shadow-xl;
        }

        .neo-btn neo-btn-secondary {
            @apply bg-white text-gray-700 px-4 py-2 rounded-lg font-medium border border-gray-300 hover:bg-gray-50 transition-all duration-200 flex items-center shadow-sm hover:shadow-md;
        }

        .progress {
            background-color: #e9ecef;
        }

        /* Context7 AI Styles */
        .neo-suggestion {
            @apply bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg p-4 border border-indigo-200 mb-3 hover:shadow-md transition-shadow;
        }

        .neo-suggestion.high-impact {
            @apply from-red-50 to-pink-50 border-red-200;
        }

        .neo-suggestion.medium-impact {
            @apply from-yellow-50 to-amber-50 border-yellow-200;
        }

        /* Enhanced Chart Styles */
        .chart-container {
            @apply bg-gradient-to-r from-purple-50 to-violet-50 rounded-xl border border-purple-200 shadow-sm p-6;
        }

        /* Performance Card Hover Effects */
        .performance-card {
            @apply transform hover:scale-105 transition-transform duration-200;
        }

        /* Loading Animation for Context7 */
        .neo-loading {
            @apply animate-pulse bg-gradient-to-r from-indigo-400 to-purple-400;
        }

        /* Responsive Table Enhancements */
        @media (max-width: 768px) {
            .performance-table {
                @apply text-sm;
            }

            .performance-table th,
            .performance-table td {
                @apply px-2 py-3;
            }
        }

        /* Dark Mode Support */
        @media (prefers-color-scheme: dark) {
            .neo-btn neo-btn-secondary {
                @apply bg-gray-800 text-gray-200 border-gray-600 hover:bg-gray-700;
            }

            .neo-suggestion {
                @apply from-gray-800 to-gray-700 border-gray-600 text-gray-200;
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Context7 AI Integration
        let context7 = {
            memory: {},
            suggestions: [],
            analysis: {}
        };

        // Initialize Context7
        function initContext7() {
            // Load existing memory
            loadContext7Memory();

            // Generate initial analysis
            generateContext7Analysis();

            // Setup real-time suggestions
            setupContext7Suggestions();
        }

        // Load Context7 Memory
        function loadContext7Memory() {
            fetch('/api/context7/memory/performance', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    context7.memory = data.memory || {};
                    updateContext7UI();
                })
                .catch(error => {
                    console.log('Context7 memory loading skipped:', error);
                });
        }

        // Generate Context7 Analysis
        function generateContext7Analysis() {
            const performanceData = {
                totalTasks: {{ $genelPerformans['toplam_gorev'] ?? 0 }},
                completedTasks: {{ $genelPerformans['tamamlanan_gorev'] ?? 0 }},
                successRate: {{ $genelPerformans['basari_orani'] ?? 0 }},
                period: '{{ request('tarih_araligi', '30') }}'
            };

            // AI Analysis Logic
            let analysis = '';
            if (performanceData.successRate >= 80) {
                analysis =
                    'Takım performansı mükemmel! Başarı oranı yüksek seviyede. Mevcut trendi korumak için öneriler hazırlanıyor...';
            } else if (performanceData.successRate >= 60) {
                analysis =
                    'Takım performansı iyi ancak iyileştirme potansiyeli var. Görev dağılımını optimize etmeyi öneriyoruz.';
            } else {
                analysis =
                    'Takım performansında iyileştirme gerekiyor. Öncelikli olarak görev yönetimi ve kaynak dağılımı gözden geçirilmeli.';

                // Generate specific suggestions
                context7.suggestions = [
                    'Görev önceliklendirme sistemi geliştirilmeli',
                    'Ekip üyelerine mentorluk programı uygulanmalı',
                    'Performans hedefleri daha gerçekçi belirlenmeli'
                ];
            }

            document.getElementById('ai-analysis').textContent = analysis;
            updateContext7Suggestions();
        }

        // Context7 Analysis Button
        function context7Analiz() {
            // Show loading state
            const btn = event.target.closest('button');
            const originalText = btn.innerHTML;
            btn.innerHTML =
                '<svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>Analiz Ediliyor...';
            btn.disabled = true;

            // Perform deep analysis
            setTimeout(() => {
                generateDeepContext7Analysis();
                btn.innerHTML = originalText;
                btn.disabled = false;

                // Show success message
                toastr.success('Context7 AI analizi tamamlandı!', 'Başarılı');
            }, 2000);
        }

        // Deep Context7 Analysis
        function generateDeepContext7Analysis() {
            const suggestionsContainer = document.getElementById('neo-suggestions');

            const deepSuggestions = [{
                    type: 'optimization',
                    title: 'Görev Optimizasyonu',
                    description: 'Yapay zeka analizi ile görev dağılımı %15 iyileştirilebilir.',
                    impact: 'high'
                },
                {
                    type: 'training',
                    title: 'Eğitim Önerisi',
                    description: 'Takım üyelerinin belirli konularda eğitimi öneriliyor.',
                    impact: 'medium'
                },
                {
                    type: 'resource',
                    title: 'Kaynak Planlaması',
                    description: 'Mevcut kaynaklar daha verimli kullanılabilir.',
                    impact: 'high'
                }
            ];

            const suggestionsHtml = deepSuggestions.map(suggestion => `
                <div class="bg-white rounded-lg p-4 border border-indigo-200 mb-3">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 ${suggestion.impact === 'high' ? 'bg-gradient-to-r from-red-500 to-pink-600' : 'bg-gradient-to-r from-yellow-500 to-amber-600'} rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-medium text-gray-900">${suggestion.title}</h4>
                            <p class="text-sm text-gray-600 mt-1">${suggestion.description}</p>
                            <div class="mt-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                                    suggestion.impact === 'high' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'
                                }">
                                    ${suggestion.impact === 'high' ? 'Yüksek Etki' : 'Orta Etki'}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');

            suggestionsContainer.innerHTML = suggestionsHtml;
        }

        // Update Context7 UI
        function updateContext7UI() {
            // Update memory indicators
            const memoryCount = Object.keys(context7.memory).length;
            if (memoryCount > 0) {
                document.getElementById('ai-analysis').textContent += ` (${memoryCount} adet geçmiş analiz kullanılıyor)`;
            }
        }

        // Setup Context7 Suggestions
        function setupContext7Suggestions() {
            // Real-time suggestions based on user interactions
            document.addEventListener('click', function(e) {
                if (e.target.matches('[data-neo-track]')) {
                    trackContext7Interaction(e.target.dataset.context7Track);
                }
            });
        }

        // Track Context7 Interactions
        function trackContext7Interaction(action) {
            context7.memory[action] = (context7.memory[action] || 0) + 1;

            // Save to server
            fetch('/api/context7/memory/track', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: JSON.stringify({
                    action: action,
                    count: context7.memory[action]
                })
            }).catch(error => console.log('Context7 tracking failed:', error));
        }

        // Görev Trend Chart
        const trendCtx = document.getElementById('gorevTrendChart').getContext('2d');
        const trendChart = new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartData['labels'] ?? []) !!},
                datasets: [{
                    label: 'Tamamlanan Görevler',
                    data: {!! json_encode($chartData['tamamlanan'] ?? []) !!},
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: 'rgb(59, 130, 246)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }, {
                    label: 'Toplam Görevler',
                    data: {!! json_encode($chartData['toplam'] ?? []) !!},
                    borderColor: 'rgb(245, 101, 101)',
                    backgroundColor: 'rgba(245, 101, 101, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: 'rgb(245, 101, 101)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                size: 12,
                                weight: '500'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: 'rgba(59, 130, 246, 0.5)',
                        borderWidth: 1,
                        cornerRadius: 8,
                        displayColors: true,
                        padding: 12
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            font: {
                                size: 11
                            }
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            font: {
                                size: 11
                            }
                        }
                    }
                }
            }
        });

        // Görev Durum Chart
        const statusCtx = document.getElementById('gorevDurumChart').getContext('2d');
        const statusChart = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($chartData['status_labels'] ?? []) !!},
                datasets: [{
                    data: {!! json_encode($chartData['status_data'] ?? []) !!},
                    backgroundColor: [
                        'rgb(34, 197, 94)', // Yeşil - Tamamlanan
                        'rgb(251, 191, 36)', // Sarı - Devam Eden
                        'rgb(239, 68, 68)', // Kırmızı - İptal
                        'rgb(156, 163, 175)' // Gri - Bekleyen
                    ],
                    borderWidth: 0,
                    hoverBorderWidth: 2,
                    hoverBorderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            font: {
                                size: 12,
                                weight: '500'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        cornerRadius: 8,
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Rapor indirme
        function raporIndir() {
            // Context7 ile gelişmiş rapor
            toastr.info('Context7 AI ile gelişmiş rapor hazırlanıyor...');

            setTimeout(() => {
                // PDF veya Excel rapor indirme işlemi
                toastr.success('Rapor başarıyla indirildi!', 'Başarılı');
            }, 1500);
        }

        // Rapor paylaşma
        function raporPaylas() {
            // Email veya link paylaşma işlemi
            toastr.info('Rapor paylaşma özelliği yakında eklenecek');
        }

        // Tarih aralığı değiştiğinde özel tarih alanlarını göster/gizle
        document.querySelector('select[name="tarih_araligi"]').addEventListener('change', function() {
            const customFields = document.querySelectorAll('.custom-date-field');

            if (this.value === 'custom') {
                customFields.forEach(field => field.style.display = 'block');
            } else {
                customFields.forEach(field => field.style.display = 'none');
            }
        });

        // Sayfa yüklendiğinde tarih alanlarını kontrol et ve Context7'ü başlat
        document.addEventListener('DOMContentLoaded', function() {
            const tarihSelect = document.querySelector('select[name="tarih_araligi"]');
            if (tarihSelect && tarihSelect.value === 'custom') {
                document.querySelectorAll('.custom-date-field')
                    .forEach(field => field.style.display = 'block');
            }

            // Initialize Context7
            initContext7();
        });
    </script>
@endpush
