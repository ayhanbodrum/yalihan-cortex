@extends('admin.layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Sistem genel durumu ve istatistikler</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
            <!-- Toplam İlanlar -->
            <div
                class="bg-gray-50 dark:bg-gray-800 overflow-hidden shadow rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div
                                class="w-12 h-12 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H7a2 2 0 00-2-2z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Toplam İlan</dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900 dark:text-white">
                                        {{ $quickStats['total_ilanlar'] }}
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Aktif İlanlar -->
            <div
                class="bg-gray-50 dark:bg-gray-800 overflow-hidden shadow rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div
                                class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Aktif İlan</dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900 dark:text-white">
                                        {{ $quickStats['active_ilanlar'] }}
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Toplam Kullanıcılar -->
            <div
                class="bg-gray-50 dark:bg-gray-800 overflow-hidden shadow rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Toplam Kullanıcı
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900 dark:text-white">
                                        {{ $quickStats['total_kullanicilar'] }}
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Toplam Danışmanlar -->
            <div
                class="bg-gray-50 dark:bg-gray-800 overflow-hidden shadow rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div
                                class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Toplam Danışman
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900 dark:text-white">
                                        {{ $quickStats['total_danismanlar'] }}
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TCMB Exchange Rate Widget --}}
        <div class="mb-8">
            <x-admin.exchange-rate-widget />
        </div>

        {{-- 🧠 TKGM Learning Engine - Market Analysis Widget --}}
        <div class="mb-10">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Market Analysis (TKGM Learning Engine)
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Hotspot ve özet istatistikler (dinamik API)</p>
                </div>
                <div class="flex items-center gap-3">
                    <select id="mi-province"
                        class="rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-700 dark:text-gray-200 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all duration-200">
                        <option value="48">Muğla (48)</option>
                        <option value="34">İstanbul (34)</option>
                        <option value="06">Ankara (06)</option>
                    </select>
                    <button id="mi-refresh"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg
                        bg-blue-600 text-white hover:bg-blue-700 hover:scale-105 active:scale-95
                        focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900
                        transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582M20 20v-5h-.581M5.545 9A7.5 7.5 0 0119.5 12M18.455 15A7.5 7.5 0 014.5 12" />
                        </svg>
                        Yenile
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Stats --}}
                <div
                    class="lg:col-span-1 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow p-6 hover:shadow-lg transition-all duration-200">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19V6a2 2 0 012-2h6a2 2 0 012 2v13M9 19H5a2 2 0 01-2-2v-5a2 2 0 012-2h4m0 7h6" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Özet İstatistik</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">Market Snapshot</p>
                        </div>
                    </div>
                    <dl class="space-y-3 text-sm">
                        <div class="flex items-center justify-between">
                            <dt class="text-gray-600 dark:text-gray-400">Ortalama Fiyat</dt>
                            <dd id="mi-avg-price" class="font-semibold text-gray-900 dark:text-white">-</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-gray-600 dark:text-gray-400">Medyan Fiyat</dt>
                            <dd id="mi-median-price" class="font-semibold text-gray-900 dark:text-white">-</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-gray-600 dark:text-gray-400">Satış Hızı</dt>
                            <dd id="mi-sales-velocity" class="font-semibold text-gray-900 dark:text-white">-</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-gray-600 dark:text-gray-400">Dönüşüm Oranı</dt>
                            <dd id="mi-conversion" class="font-semibold text-gray-900 dark:text-white">-</dd>
                        </div>
                    </dl>
                    <p id="mi-status" class="mt-4 text-xs text-gray-500 dark:text-gray-400">API çağrısı bekleniyor…</p>
                </div>

                {{-- Hotspots --}}
                <div
                    class="lg:col-span-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow p-6 hover:shadow-lg transition-all duration-200">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Yatırım Hotspot</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">En iyi 5 mahalle</p>
                        </div>
                        <span id="mi-hotspots-badge"
                            class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300">-</span>
                    </div>
                    <div id="mi-hotspots-list" class="space-y-3">
                        <div class="h-10 rounded-lg bg-gray-100 dark:bg-gray-700 animate-pulse"></div>
                        <div class="h-10 rounded-lg bg-gray-100 dark:bg-gray-700 animate-pulse"></div>
                        <div class="h-10 rounded-lg bg-gray-100 dark:bg-gray-700 animate-pulse"></div>
                    </div>
                    <p id="mi-hotspots-status" class="mt-4 text-xs text-gray-500 dark:text-gray-400">API çağrısı
                        bekleniyor…</p>
                </div>
            </div>
        </div>

        <!-- Tables Grid -->
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2 mb-8">
            <!-- Son Eklenen İlanlar -->
            <div class="bg-gray-50 dark:bg-gray-800 shadow rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Son Eklenen İlanlar</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    #</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Başlık</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Fiyat</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Durum</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Tarih</th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-50 dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($quickStats['recent_ilanlar'] ?? [] as $ilan)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ $ilan->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ Str::limit($ilan->baslik, 30) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ number_format($ilan->fiyat) }} {{ $ilan->para_birimi }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $ilanAktif =
                                                $ilan->status === true ||
                                                $ilan->status === 'Aktif' ||
                                                $ilan->status === 1;
                                        @endphp
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $ilanAktif ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-400' }}">
                                            {{ is_bool($ilan->status) ? ($ilan->status ? 'Aktif' : 'Pasif') : $ilan->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ optional($ilan->created_at)->format('d.m.Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5"
                                        class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                        Henüz ilan eklenmemiş
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Son Kullanıcılar -->
            <div class="bg-gray-50 dark:bg-gray-800 shadow rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Son Kullanıcılar</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    #</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Ad</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Email</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Durum</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Kayıt</th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-50 dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($quickStats['recent_kullanicilar'] ?? [] as $user)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ $user->id }}</td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $user->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $user->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->status ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-400' }}">
                                            {{ $user->status ? 'Aktif' : 'Pasif' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ optional($user->created_at)->format('d.m.Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5"
                                        class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                        Henüz kullanıcı eklenmemiş
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sistem Durumu -->
        <div class="bg-gray-50 dark:bg-gray-800 shadow rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Sistem Durumu</h3>
            </div>
            <div class="px-6 py-4">
                <dl class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                    @foreach ($quickStats['system_status'] as $service => $status)
                        <div class="px-4 py-5 bg-gray-50 dark:bg-gray-900 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate capitalize">
                                {{ $service }}</dt>
                            <dd class="mt-1 flex items-center">
                                <span
                                    class="flex items-center text-sm font-semibold {{ $status == 'online' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    <span
                                        class="w-2 h-2 rounded-full mr-2 {{ $status == 'online' ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                    {{ $status == 'online' ? 'Online' : 'Offline' }}
                                </span>
                            </dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const provinceSelect = document.getElementById('mi-province');
            const refreshBtn = document.getElementById('mi-refresh');
            const statusEl = document.getElementById('mi-status');
            const hotspotsStatusEl = document.getElementById('mi-hotspots-status');
            const badgeEl = document.getElementById('mi-hotspots-badge');
            const listEl = document.getElementById('mi-hotspots-list');

            const avgPriceEl = document.getElementById('mi-avg-price');
            const medianPriceEl = document.getElementById('mi-median-price');
            const velocityEl = document.getElementById('mi-sales-velocity');
            const conversionEl = document.getElementById('mi-conversion');

            const formatCurrency = (value) => {
                if (value === null || value === undefined || isNaN(value)) return '-';
                return new Intl.NumberFormat('tr-TR', {
                    style: 'currency',
                    currency: 'TRY',
                    maximumFractionDigits: 0
                }).format(value);
            };

            const formatPercent = (value) => {
                if (value === null || value === undefined || isNaN(value)) return '-';
                return `${value.toFixed(1)}%`;
            };

            const setLoading = (isLoading) => {
                refreshBtn.disabled = isLoading;
                refreshBtn.classList.toggle('opacity-60', isLoading);
                statusEl.textContent = isLoading ? 'API çağrısı yapılıyor…' : 'Güncel veri';
                hotspotsStatusEl.textContent = isLoading ? 'Hotspot bekleniyor…' : 'Güncel veri';
            };

            const renderHotspots = (items = []) => {
                listEl.innerHTML = '';
                if (!items.length) {
                    listEl.innerHTML =
                        '<p class="text-sm text-gray-500 dark:text-gray-400">Hotspot bulunamadı</p>';
                    badgeEl.textContent = '0';
                    return;
                }
                badgeEl.textContent = items.length;
                items.slice(0, 5).forEach((item, idx) => {
                    const li = document.createElement('div');
                    li.className =
                        'flex items-center justify-between rounded-lg border border-gray-200 dark:border-gray-700 px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200';
                    li.innerHTML = `
                    <div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">${idx + 1}. ${item?.mahalle_adi ?? 'Mahalle'}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">İlçe: ${item?.ilce_adi ?? '-'} • Güven: ${formatPercent(item?.confidence_level ?? 0)}</p>
                    </div>
                    <span class="text-sm font-semibold text-blue-600 dark:text-blue-300">${formatCurrency(item?.avg_price ?? 0)}</span>
                `;
                    listEl.appendChild(li);
                });
            };

            const fetchData = async () => {
                const ilId = provinceSelect.value || 48;
                setLoading(true);
                try {
                    // Stats
                    const statsUrl = window.APIConfig.getUrl(window.APIConfig.marketAnalysis.stats);
                    const statsRes = await fetch(statsUrl);
                    const statsJson = await statsRes.json();
                    if (statsJson && statsJson.data) {
                        avgPriceEl.textContent = formatCurrency(statsJson.data.avg_price ?? statsJson.data
                            .average_price ?? null);
                        medianPriceEl.textContent = formatCurrency(statsJson.data.median_price ?? null);
                        velocityEl.textContent = statsJson.data.sales_velocity ?
                            `${statsJson.data.sales_velocity} gün` : '-';
                        conversionEl.textContent = statsJson.data.conversion_rate ? formatPercent(statsJson
                            .data.conversion_rate) : '-';
                    } else {
                        avgPriceEl.textContent = medianPriceEl.textContent = velocityEl.textContent =
                            conversionEl.textContent = '-';
                    }

                    // Hotspots
                    const hotspotsUrl = window.APIConfig.getUrl(window.APIConfig.marketAnalysis.hotspots(
                        ilId));
                    const hsRes = await fetch(hotspotsUrl);
                    const hsJson = await hsRes.json();
                    renderHotspots(hsJson?.data ?? []);
                } catch (e) {
                    console.error('Market analysis fetch error', e);
                    statusEl.textContent = 'Hata: Veri alınamadı';
                    hotspotsStatusEl.textContent = 'Hata: Hotspot alınamadı';
                } finally {
                    setLoading(false);
                }
            };

            refreshBtn?.addEventListener('click', (e) => {
                e.preventDefault();
                fetchData();
            });
            provinceSelect?.addEventListener('change', fetchData);

            fetchData();
        });
    </script>
@endpush
