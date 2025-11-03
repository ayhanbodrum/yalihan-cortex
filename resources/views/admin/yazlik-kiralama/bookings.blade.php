@extends('admin.layouts.neo')

@section('title', 'Rezervasyon Yönetimi')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    📅 Rezervasyon Yönetimi
                </h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Yazlık kiralama rezervasyonlarını yönetin
                </p>
            </div>
            
            <a href="{{ route('admin.yazlik-kiralama.index') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-all">
                ← Geri Dön
            </a>
        </div>

        {{-- Filters --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6"
             x-data="{ 
                 showFilters: false,
                 status: '{{ request('status') }}',
                 dateRange: '{{ request('date_range') }}'
             }">
            
            {{-- Filter Toggle --}}
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    🔍 Filtreler
                </h2>
                <button @click="showFilters = !showFilters"
                        class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                    <span x-text="showFilters ? 'Gizle' : 'Göster'"></span>
                </button>
            </div>

            {{-- Filter Form --}}
            <form method="GET" 
                  action="{{ route('admin.yazlik-kiralama.bookings', $id ?? null) }}"
                  x-show="showFilters"
                  x-transition:enter="transition ease-out duration-200"
                  x-transition:enter-start="opacity-0 transform scale-95"
                  x-transition:enter-end="opacity-100 transform scale-100"
                  class="grid grid-cols-1 md:grid-cols-3 gap-4">
                
                {{-- Status Filter --}}
                <div>
                    <label for="status" class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                        Durum
                    </label>
                    <select name="status" 
                            id="status"
                            x-model="status"
                            class="w-full px-4 py-2 bg-white dark:bg-gray-900 border-2 border-gray-300 dark:border-gray-600 rounded-lg text-black dark:text-white font-semibold focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                        <option value="">Tümü</option>
                        <option value="pending">Beklemede</option>
                        <option value="confirmed">Onaylandı</option>
                        <option value="cancelled">İptal Edildi</option>
                        <option value="completed">Tamamlandı</option>
                    </select>
                </div>

                {{-- Date Range Filter --}}
                <div>
                    <label for="date_range" class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                        Tarih Aralığı
                    </label>
                    <input type="text" 
                           name="date_range" 
                           id="date_range"
                           x-model="dateRange"
                           placeholder="2025-01-01 - 2025-12-31"
                           class="w-full px-4 py-2 bg-white dark:bg-gray-900 border-2 border-gray-300 dark:border-gray-600 rounded-lg text-black dark:text-white font-semibold placeholder-gray-600 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                </div>

                {{-- Submit Button --}}
                <div class="flex items-end">
                    <button type="submit"
                            class="w-full px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-lg hover:scale-105 active:scale-95 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all shadow-lg">
                        Filtrele
                    </button>
                </div>
            </form>
        </div>

        {{-- Bookings List --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            
            @if($bookings->isEmpty())
                {{-- Empty State --}}
                <div class="text-center py-16">
                    <div class="text-6xl mb-4">📅</div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                        Rezervasyon Bulunamadı
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Henüz hiç rezervasyon yapılmamış veya filtrelere uygun rezervasyon yok.
                    </p>
                </div>
            @else
                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">
                                    ID
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">
                                    İlan
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">
                                    Check-in
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">
                                    Check-out
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">
                                    Misafir
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">
                                    Durum
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">
                                    İşlemler
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($bookings as $booking)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
                                    x-data="{ 
                                        bookingId: {{ $booking->id }},
                                        status: '{{ $booking->status }}',
                                        updating: false
                                    }">
                                    {{-- ID --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                        #{{ $booking->id }}
                                    </td>

                                    {{-- İlan --}}
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                        <div class="font-semibold">{{ $booking->ilan_baslik }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            İlan ID: {{ $booking->ilan_id }}
                                        </div>
                                    </td>

                                    {{-- Check-in --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ \Carbon\Carbon::parse($booking->check_in)->format('d.m.Y') }}
                                    </td>

                                    {{-- Check-out --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ \Carbon\Carbon::parse($booking->check_out)->format('d.m.Y') }}
                                    </td>

                                    {{-- Misafir --}}
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                        <div class="font-semibold">{{ $booking->guest_name ?? 'N/A' }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $booking->guest_email ?? 'N/A' }}
                                        </div>
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                            @if($booking->status === 'confirmed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                            @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                            @elseif($booking->status === 'cancelled') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                            @elseif($booking->status === 'completed') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                            @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                            @endif">
                                            @if($booking->status === 'confirmed') ✓ Onaylandı
                                            @elseif($booking->status === 'pending') ⏳ Beklemede
                                            @elseif($booking->status === 'cancelled') ✕ İptal
                                            @elseif($booking->status === 'completed') ✓ Tamamlandı
                                            @else {{ $booking->status }}
                                            @endif
                                        </span>
                                    </td>

                                    {{-- Actions --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="flex items-center gap-2">
                                            <button @click="alert('Detay modal açılacak')"
                                                    class="px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                                Detay
                                            </button>
                                            <button @click="alert('Durum güncelleme modal açılacak')"
                                                    class="px-3 py-1 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                                                Düzenle
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
                    {{ $bookings->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
@endsection

