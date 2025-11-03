@extends('admin::layouts.master')

@section('title', 'Dashboard')

@section('content')
<div class="container px-6 py-8 mx-auto">
    <!-- İstatistik Kartları -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Toplam İlanlar -->
        <div class="p-4 transition-shadow bg-white rounded-lg shadow-sm hover:shadow-lg dark:bg-gray-800">
            <div class="flex items-start justify-between">
                <div class="flex flex-col space-y-2">
                    <span class="text-gray-400 dark:text-gray-300">Toplam İlanlar</span>
                    <span class="text-2xl font-semibold text-gray-800 dark:text-white">{{ number_format($ilanSayisi) }}</span>
                </div>
                <div class="p-3 rounded-full bg-orange-100 dark:bg-orange-900">
                    <svg class="w-6 h-6 text-orange-500 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>
            <div class="flex items-center mt-4 text-xs space-x-2">
                <span class="text-green-500 flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"></path>
                    </svg>
                    {{ $bugunEklenenIlan }} bugün
                </span>
            </div>
        </div>

        <!-- Toplam Müşteriler -->
        <div class="p-4 transition-shadow bg-white rounded-lg shadow-sm hover:shadow-lg dark:bg-gray-800">
            <div class="flex items-start justify-between">
                <div class="flex flex-col space-y-2">
                    <span class="text-gray-400 dark:text-gray-300">Toplam Müşteriler</span>
                    <span class="text-2xl font-semibold text-gray-800 dark:text-white">{{ number_format($kisiSayisi) }}</span>
                </div>
                <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900">
                    <svg class="w-6 h-6 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="flex items-center mt-4 text-xs space-x-2">
                <span class="text-green-500 flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"></path>
                    </svg>
                    {{ $bugunEklenenKisi }} bugün
                </span>
            </div>
        </div>

        <!-- Talepler -->
        <div class="p-4 transition-shadow bg-white rounded-lg shadow-sm hover:shadow-lg dark:bg-gray-800">
            <div class="flex items-start justify-between">
                <div class="flex flex-col space-y-2">
                    <span class="text-gray-400 dark:text-gray-300">Talepler</span>
                    <span class="text-2xl font-semibold text-gray-800 dark:text-white">{{ number_format($talepSayisi) }}</span>
                </div>
                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900">
                    <svg class="w-6 h-6 text-green-500 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Kullanıcılar -->
        <div class="p-4 transition-shadow bg-white rounded-lg shadow-sm hover:shadow-lg dark:bg-gray-800">
            <div class="flex items-start justify-between">
                <div class="flex flex-col space-y-2">
                    <span class="text-gray-400 dark:text-gray-300">Kullanıcılar</span>
                    <span class="text-2xl font-semibold text-gray-800 dark:text-white">{{ number_format($kullaniciSayisi) }}</span>
                </div>
                <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900">
                    <svg class="w-6 h-6 text-purple-500 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik ve Son İşlemler -->
    <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-2">
        <!-- Grafik -->
        <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-800">
            <h2 class="text-lg font-semibold text-gray-700 dark:text-white mb-4">Son 7 Günlük Aktivite</h2>
            <div class="h-64">
                <canvas id="activity-chart"></canvas>
            </div>
        </div>

        <!-- Son İlanlar -->
        <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-800">
            <h2 class="text-lg font-semibold text-gray-700 dark:text-white mb-4">Son Eklenen İlanlar</h2>
            <div class="overflow-x-auto">
                <table class="w-full whitespace-nowrap">
                    <thead>
                        <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                            <th class="px-4 py-3">İlan</th>
                            <th class="px-4 py-3">Fiyat</th>
                            <th class="px-4 py-3">Danışman</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y dark:divide-gray-700">
                        @forelse($sonIlanlar as $ilan)
                        <tr class="text-gray-700 dark:text-gray-400">
                            <td class="px-4 py-3">
                                <div class="flex items-center text-sm">
                                    <p class="font-semibold">{{ $ilan->adres_il }} / {{ $ilan->adres_ilce }}</p>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ number_format($ilan->fiyat) }} {{ $ilan->para_birimi }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ $ilan->danisman_adi }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-4 py-3 text-sm text-center text-gray-500">
                                Henüz ilan eklenmemiş.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Verileri PHP'den al
        const ilanVerileri = JSON.parse('@json($gunlukIlanSayisi)');
        const kisiVerileri = JSON.parse('@json($gunlukKisiSayisi)');
        
        // Etiketleri ve veri değerlerini ayrıştır
        const labels = ilanVerileri.map(item => item.tarih);
        const ilanSayilari = ilanVerileri.map(item => item.sayi);
        const kisiSayilari = kisiVerileri.map(item => item.sayi);
        
        // Grafik oluştur
        const ctx = document.getElementById('activity-chart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Yeni İlanlar',
                        data: ilanSayilari,
                        borderColor: '#F97316',
                        backgroundColor: 'rgba(249, 115, 22, 0.1)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Yeni Müşteriler',
                        data: kisiSayilari,
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.3,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
