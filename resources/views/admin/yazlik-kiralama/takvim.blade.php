@extends('admin.layouts.admin')

@section('title', 'Takvim - Rezervasyon Yönetimi')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8"
     x-data="{
         currentMonth: {{ $currentMonth ?? date('n') }},
         currentYear: {{ $currentYear ?? date('Y') }},
         viewMode: 'month', // month, week, day
         selectedEvent: null,
         showEventModal: false
     }">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    📅 Rezervasyon Takvimi
                </h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Yazlık kiralama rezervasyonlarını takvim görünümünde yönetin
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.yazlik-kiralama.bookings') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    📋 Liste Görünümü
                </a>
                <a href="{{ route('admin.yazlik-kiralama.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-all">
                    ← Geri Dön
                </a>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            {{-- Total Events --}}
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90 mb-1">Toplam Etkinlik</p>
                        <p class="text-3xl font-bold">{{ $stats['total_events'] ?? 0 }}</p>
                    </div>
                    <div class="text-4xl opacity-80">📊</div>
                </div>
            </div>

            {{-- This Week --}}
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90 mb-1">Bu Hafta</p>
                        <p class="text-3xl font-bold">{{ $stats['this_week'] ?? 0 }}</p>
                    </div>
                    <div class="text-4xl opacity-80">📆</div>
                </div>
            </div>

            {{-- Upcoming --}}
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90 mb-1">Gelecek</p>
                        <p class="text-3xl font-bold">{{ $stats['upcoming'] ?? 0 }}</p>
                    </div>
                    <div class="text-4xl opacity-80">🔮</div>
                </div>
            </div>
        </div>

        {{-- Calendar Controls --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <div class="flex items-center justify-between flex-wrap gap-4">
                {{-- Month/Year Navigation --}}
                <div class="flex items-center gap-3">
                    <button @click="currentMonth--; if(currentMonth < 1) { currentMonth = 12; currentYear--; }"
                            class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                        ← Önceki
                    </button>

                    <div class="text-center px-4">
                        <p class="text-lg font-bold text-gray-900 dark:text-white">
                            <span x-text="['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'][currentMonth - 1]"></span>
                            <span x-text="currentYear"></span>
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Ay/Yıl</p>
                    </div>

                    <button @click="currentMonth++; if(currentMonth > 12) { currentMonth = 1; currentYear++; }"
                            class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                        Sonraki →
                    </button>

                    <button @click="currentMonth = {{ date('n') }}; currentYear = {{ date('Y') }};"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Bugün
                    </button>
                </div>

                {{-- View Mode Toggle --}}
                <div class="flex items-center gap-2 bg-gray-100 dark:bg-gray-900 rounded-lg p-1">
                    <button @click="viewMode = 'month'"
                            :class="viewMode === 'month' ? 'bg-white dark:bg-gray-700 shadow' : ''"
                            class="px-4 py-2 rounded-lg text-sm font-semibold text-gray-700 dark:text-gray-300 transition-all">
                        Ay
                    </button>
                    <button @click="viewMode = 'week'"
                            :class="viewMode === 'week' ? 'bg-white dark:bg-gray-700 shadow' : ''"
                            class="px-4 py-2 rounded-lg text-sm font-semibold text-gray-700 dark:text-gray-300 transition-all">
                        Hafta
                    </button>
                    <button @click="viewMode = 'day'"
                            :class="viewMode === 'day' ? 'bg-white dark:bg-gray-700 shadow' : ''"
                            class="px-4 py-2 rounded-lg text-sm font-semibold text-gray-700 dark:text-gray-300 transition-all">
                        Gün
                    </button>
                </div>
            </div>
        </div>

        {{-- Calendar View --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">

            {{-- Month View --}}
            <div x-show="viewMode === 'month'" class="p-6">
                {{-- Calendar Grid --}}
                <div class="grid grid-cols-7 gap-2">
                    {{-- Day Headers --}}
                    @foreach(['Pzt', 'Sal', 'Çar', 'Per', 'Cum', 'Cmt', 'Paz'] as $day)
                        <div class="text-center py-3 text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">
                            {{ $day }}
                        </div>
                    @endforeach

                    {{-- Calendar Days (Mock - 35 days) --}}
                    @for($i = 1; $i <= 35; $i++)
                        <div class="aspect-square border border-gray-200 dark:border-gray-700 rounded-lg p-2 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors cursor-pointer"
                             @click="alert('Gün {{ $i }} detayları')">
                            <div class="text-sm font-semibold text-gray-900 dark:text-white mb-1">
                                {{ $i <= 30 ? $i : ($i - 30) }}
                            </div>

                            {{-- Events on this day (Mock) --}}
                            @if($i % 5 === 0)
                                <div class="space-y-1">
                                    <div class="text-xs bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-2 py-1 rounded truncate">
                                        Rezervasyon
                                    </div>
                                </div>
                            @endif

                            @if($i % 7 === 0)
                                <div class="space-y-1 mt-1">
                                    <div class="text-xs bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 px-2 py-1 rounded truncate">
                                        Check-in
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endfor
                </div>
            </div>

            {{-- Week View --}}
            <div x-show="viewMode === 'week'" class="p-6">
                <div class="text-center text-gray-500 dark:text-gray-400 py-12">
                    <div class="text-4xl mb-4">📆</div>
                    <p class="text-lg font-semibold">Hafta Görünümü</p>
                    <p class="text-sm">Yakında eklenecek...</p>
                </div>
            </div>

            {{-- Day View --}}
            <div x-show="viewMode === 'day'" class="p-6">
                <div class="text-center text-gray-500 dark:text-gray-400 py-12">
                    <div class="text-4xl mb-4">📅</div>
                    <p class="text-lg font-semibold">Gün Görünümü</p>
                    <p class="text-sm">Yakında eklenecek...</p>
                </div>
            </div>
        </div>

        {{-- Events List Below Calendar --}}
        <div class="mt-8 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                📋 Yaklaşan Etkinlikler
            </h3>

            @if(isset($events) && count($events) > 0)
                <div class="space-y-3">
                    @foreach($events as $event)
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors">
                            <div class="flex items-center gap-4">
                                <div class="text-3xl">
                                    @if($event['type'] === 'meeting') 🤝
                                    @elseif($event['type'] === 'viewing') 🏠
                                    @elseif($event['type'] === 'call') 📞
                                    @elseif($event['type'] === 'followup') 🔄
                                    @else 📌
                                    @endif
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-white">
                                        {{ $event['title'] }}
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ \Carbon\Carbon::parse($event['date'])->format('d.m.Y') }} - {{ $event['time'] }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-500">
                                        📍 {{ $event['location'] }}
                                    </p>
                                </div>
                            </div>

                            <button @click="alert('Etkinlik detayı: {{ $event['title'] }}')"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                Detay
                            </button>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    <div class="text-4xl mb-2">📭</div>
                    <p>Yaklaşan etkinlik bulunmuyor</p>
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
