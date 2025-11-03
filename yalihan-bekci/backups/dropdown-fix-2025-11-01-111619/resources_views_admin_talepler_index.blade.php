@extends('admin.layouts.neo')

@section('title', 'AI Destekli Talep Yönetimi')

@push('styles')
    <style>
        .ai-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        .status-active {
            @apply bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200;
        }

        .status-pending {
            @apply bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200;
        }

        .status-matched {
            @apply bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200;
        }

        .status-closed {
            @apply bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200;
        }
    </style>
@endpush

@section('content')
    <div x-data="taleplerData()" class="space-y-6">

        {{-- Header Section --}}
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="neo-icon-container bg-gradient-to-br from-blue-500 to-purple-600">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293L18.707 8.707A1 1 0 0119 9.414V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <h1 class="neo-title">🤖 AI Destekli Talep Yönetimi</h1>
                    <p class="neo-subtitle">Context7 Intelligence ile akıllı talep analizi ve eşleştirme</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                {{-- AI Batch Analysis Button --}}
                <button @click="showBatchAnalysisModal = true"
                    class="neo-btn neo-btn neo-btn-secondary flex items-center gap-2 ai-badge touch-target-optimized touch-target-optimized">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span>Toplu AI Analizi</span>
                </button>

                {{-- Create New Demand --}}
                <a href="{{ route('admin.talepler.create') }}" class="neo-btn neo-btn neo-btn-primary touch-target-optimized touch-target-optimized">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Yeni Talep Ekle
                </a>
            </div>
        </div>

        {{-- AI Stats Dashboard --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="neo-card p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Toplam Talepler</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $talepler->total() }}</p>
                    </div>
                    <div class="neo-icon-container bg-blue-500">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h10a2 2 0 012 2v14a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2 text-sm text-green-600 dark:text-green-400">
                    ↑ %12 bu ay
                </div>
            </div>

            <div class="neo-card p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">AI Eşleştirme Oranı</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">%87</p>
                    </div>
                    <div class="neo-icon-container ai-badge">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2 text-sm text-green-600 dark:text-green-400">
                    ↑ %5 geçen hafta
                </div>
            </div>

            <div class="neo-card p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Ortalama Yanıt Süresi</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">2.4s</p>
                    </div>
                    <div class="neo-icon-container bg-green-500">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2 text-sm text-green-600 dark:text-green-400">
                    ↓ %15 optimization
                </div>
            </div>

            <div class="neo-card p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Başarılı Eşleşme</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $talepler->where('status', 'eslestirildi')->count() }}</p>
                    </div>
                    <div class="neo-icon-container bg-purple-500">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2 text-sm text-green-600 dark:text-green-400">
                    ↑ %23 bu hafta
                </div>
            </div>
        </div>

        {{-- Advanced Filters --}}
        <div class="neo-card p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900 dark:text-white">Akıllı Filtreler</h3>
                <button @click="showAdvancedFilters = !showAdvancedFilters"
                    class="text-sm text-blue-600 hover:text-blue-500">
                    <span x-text="showAdvancedFilters ? 'Gizle' : 'Gelişmiş Filtreler'">
                </button>
            </div>

            <form method="GET" action="{{ route('admin.talepler.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    {{-- Smart Search --}}
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="AI destekli akıllı arama..." class="neo-input pl-10">
                    </div>

                    {{-- Status Filter --}}
                    <select name="status" class="neo-select">
                        <option value="">Tüm Durumlar</option>
                        @foreach ($statuslar as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Category Filter --}}
                    <select name="alt_kategori_id" class="neo-select">
                        <option value="">Tüm Kategoriler</option>
                        @foreach ($kategoriler as $kategori)
                            <option value="{{ $kategori->id }}"
                                {{ request('alt_kategori_id') == $kategori->id ? 'selected' : '' }}>
                                {{ $kategori->name }}
                            </option>
                        @endforeach
                    </select>

                    {{-- AI Priority Filter --}}
                    <select name="ai_priority" class="neo-select">
                        <option value="">AI Önceliği</option>
                        <option value="high" {{ request('ai_priority') == 'high' ? 'selected' : '' }}>Yüksek</option>
                        <option value="medium" {{ request('ai_priority') == 'medium' ? 'selected' : '' }}>Orta</option>
                        <option value="low" {{ request('ai_priority') == 'low' ? 'selected' : '' }}>Düşük</option>
                    </select>
                </div>

                <div x-show="showAdvancedFilters" x-transition
                    class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    {{-- Price Range --}}
                    <input type="number" name="min_fiyat" value="{{ request('min_fiyat') }}" placeholder="Min Fiyat"
                        class="neo-input">
                    <input type="number" name="max_fiyat" value="{{ request('max_fiyat') }}" placeholder="Max Fiyat"
                        class="neo-input">

                    {{-- Date Range --}}
                    <input type="date" name="baslangic_tarihi" value="{{ request('baslangic_tarihi') }}"
                        class="neo-input">
                    <input type="date" name="bitis_tarihi" value="{{ request('bitis_tarihi') }}" class="neo-input">

                    {{-- Location Filters --}}
                    <select name="ulke_id" class="neo-select">
                        <option value="">Ülke Seçin</option>
                        @foreach ($ulkeler as $ulke)
                            <option value="{{ $ulke->id }}" {{ request('ulke_id') == $ulke->id ? 'selected' : '' }}>
                                {{ $ulke->name }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Request Type --}}
                    <select name="talep_tipi" class="neo-select">
                        <option value="">Talep Tipi</option>
                        @foreach ($talepTipleri as $tip)
                            <option value="{{ $tip }}" {{ request('talep_tipi') == $tip ? 'selected' : '' }}>
                                {{ $tip }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit"
                            :disabled="filterLoading"
                            @click="filterLoading = true"
                            class="neo-btn neo-btn neo-btn-primary touch-target-optimized touch-target-optimized">
                        <svg class="w-4 h-4" :class="filterLoading ? 'animate-spin' : ''" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Filtrele
                    </button>
                    <a href="{{ route('admin.talepler.index') }}" class="neo-btn neo-btn neo-btn-secondary touch-target-optimized touch-target-optimized">
                        Temizle
                    </a>
                    <button @click="runAISearch()" type="button" class="neo-btn ai-badge text-white touch-target-optimized touch-target-optimized">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        AI Önerileri
                    </button>
                </div>
            </form>
        </div>

        {{-- Results Section --}}
        @if ($talepler->count() > 0)
            {{-- Modern Cards Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach ($talepler as $talep)
                    <div class="neo-card group hover:shadow-lg transition-all duration-300">
                        {{-- Card Header --}}
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-start justify-between">
                                <div class="flex items-start gap-3">
                                    <div
                                        class="neo-icon-container bg-gradient-to-br from-blue-500 to-purple-600 text-white">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900 dark:text-white">
                                            @if ($talep->kisi)
                                                {{ $talep->kisi->tam_ad }}
                                            @else
                                                Talep #{{ $talep->id }}
                                            @endif
                                        </h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $talep->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>

                                {{-- Status Badge --}}
                                <div class="flex items-center gap-2">
                                    <span
                                        class="px-2 py-1 text-xs font-semibold rounded-full status-{{ strtolower($talep->status ?? 'active') }}">
                                        {{ $talep->status ?? 'Aktif' }}
                                    </span>
                                    @if($talep->kisi && $talep->kisi->email && \App\Models\Kisi::where('email', $talep->kisi->email)->count() > 1)
                                        <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-medium rounded-full flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 18.5c-.77.833.192 2.5 1.732 2.5z" />
                                            </svg>
                                            Mükerrer
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Card Body --}}
                        <div class="p-6">
                            {{-- Description --}}
                            <p class="text-gray-700 dark:text-gray-300 mb-4 line-clamp-3">
                                {{ Str::limit($talep->aciklama, 150) }}
                            </p>

                            {{-- Key Details --}}
                            <div class="space-y-2 mb-4">
                                @if ($talep->kategori)
                                    <div class="flex items-center gap-2 text-sm">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        <span class="text-gray-600 dark:text-gray-400">{{ $talep->kategori->name }}</span>
                                    </div>
                                @endif

                                @if ($talep->min_fiyat || $talep->max_fiyat)
                                    <div class="flex items-center gap-2 text-sm">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8c-1.657 0-3-1.343-3-3s1.343-3 3-3 3 1.343 3 3-1.343 3-3 3z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 14l9-5-9-5-9 5 9 5z" />
                                        </svg>
                                        <span class="text-gray-600 dark:text-gray-400">
                                            ₺{{ number_format($talep->min_fiyat ?? 0) }} -
                                            ₺{{ number_format($talep->max_fiyat ?? 0) }}
                                        </span>
                                    </div>
                                @endif

                                @if ($talep->ulke || $talep->il || $talep->ilce)
                                    <div class="flex items-center gap-2 text-sm">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span class="text-gray-600 dark:text-gray-400">
                                            {{ $talep->ilce->ilce_adi ?? ($talep->il->il_adi ?? ($talep->ulke->ulke_adi ?? 'Lokasyon belirtilmemiş')) }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            {{-- AI Insights --}}
                            <div
                                class="bg-gradient-to-r from-purple-50 to-blue-50 dark:from-purple-900/20 dark:to-blue-900/20 rounded-lg p-3 mb-4">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-4 h-4 ai-badge rounded-full flex items-center justify-center">
                                        <svg class="w-2 h-2 text-white" fill="currentColor">
                                            <circle cx="1" cy="1" r="1" />
                                        </svg>
                                    </div>
                                    <span class="text-xs font-medium text-purple-700 dark:text-purple-300">AI
                                        Insights</span>
                                </div>
                                <div class="text-xs text-purple-600 dark:text-purple-400 space-y-1">
                                    <div>• Uyumluluk Skoru: <span class="font-semibold">%87</span></div>
                                    <div>• Tahmini Satış Süresi: <span class="font-semibold">18 gün</span></div>
                                    <div>• Eşleşen İlan: <span class="font-semibold">12 adet</span></div>
                                </div>
                            </div>
                        </div>

                        {{-- Card Footer Actions --}}
                        <div
                            class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    {{-- AI Analysis Button --}}
                                    <button @click="analyzeWithAI({{ $talep->id }})"
                                        class="text-xs px-3 py-1.5 ai-badge text-white rounded-full hover:opacity-80 transition-opacity">
                                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                        AI Analiz
                                    </button>

                                    {{-- Match Properties Button --}}
                                    <button @click="findMatches({{ $talep->id }})"
                                        class="text-xs px-3 py-1.5 bg-green-500 text-white rounded-full hover:bg-green-600 transition-colors">
                                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Eşleştir
                                    </button>
                                </div>

                                <div class="flex items-center gap-1">
                                    {{-- View Details --}}
                                    <a href="{{ route('admin.talepler.show', $talep) }}"
                                        class="neo-btn neo-btn-sm neo-btn neo-btn-secondary touch-target-optimized touch-target-optimized">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>

                                    {{-- Edit --}}
                                    <a href="{{ route('admin.talepler.edit', $talep) }}"
                                        class="neo-btn neo-btn-sm neo-btn neo-btn-secondary touch-target-optimized touch-target-optimized">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="neo-card p-4">
                {{ $talepler->appends(request()->query())->links('pagination::tailwind') }}
            </div>
        @else
            {{-- Empty State --}}
            <div class="neo-card p-12 text-center">
                <div class="neo-icon-container bg-gray-100 dark:bg-gray-800 mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293L18.707 8.707A1 1 0 0119 9.414V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                    Henüz talep bulunmuyor
                </h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    İlk talebi ekleyerek AI destekli eşleştirme sistemini kullanmaya başlayın.
                </p>
                <a href="{{ route('admin.talepler.create') }}" class="neo-btn neo-btn neo-btn-primary touch-target-optimized touch-target-optimized">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    İlk Talebi Ekle
                </a>
            </div>
        @endif

        {{-- Neo AI Analysis Modal --}}
        <div x-show="showAnalysisModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95" class="fixed inset-0 z-50 overflow-y-auto"
            style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showAnalysisModal = false"></div>
                <div
                    class="neo-modal neo-modal-lg bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-2xl w-full relative border border-gray-200 dark:border-gray-700">
                    {{-- Neo Modal Header --}}
                    <div class="neo-modal-header px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="neo-icon-container w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">🤖 AI Talep Analizi</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Akıllı analiz ve eşleştirme
                                        önerileri</p>
                                </div>
                            </div>
                            <button @click="showAnalysisModal = false"
                                class="neo-btn neo-btn-ghost p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors touch-target-optimized touch-target-optimized">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Neo Modal Body --}}
                    <div class="neo-modal-body px-6 py-6">
                        <div x-show="isAnalyzing" class="text-center py-12">
                            <div class="neo-loading-container">
                                <div
                                    class="neo-spinner w-12 h-12 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin mx-auto mb-4">
                                </div>
                                <div class="neo-loading-text">
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">AI Analiz
                                        Yapılıyor</h4>
                                    <p class="text-gray-600 dark:text-gray-400">Talep detayları analiz ediliyor ve
                                        eşleştirme önerileri hazırlanıyor...</p>
                                </div>
                            </div>
                        </div>

                        <div x-show="!isAnalyzing && analysisResult" class="neo-analysis-results space-y-6">
                            {{-- Analysis Results will be populated via JavaScript --}}
                            <div x-html="analysisResult" class="neo-content"></div>
                        </div>
                    </div>

                    {{-- Neo Modal Footer --}}
                    <div
                        class="neo-modal-footer px-6 py-4 bg-gradient-to-r from-gray-50 to-blue-50 dark:from-gray-700 dark:to-blue-900/20 rounded-b-2xl border-t border-gray-200 dark:border-gray-600">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                <span class="inline-flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    AI destekli analiz
                                </span>
                            </div>
                            <div class="flex items-center gap-3">
                                <button @click="showAnalysisModal = false" class="neo-btn neo-btn neo-btn-secondary touch-target-optimized touch-target-optimized">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Kapat
                                </button>
                                <button class="neo-btn neo-btn neo-btn-primary touch-target-optimized touch-target-optimized">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    Eşleştirmeleri Görüntüle
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Neo Batch Analysis Modal --}}
        <div x-show="showBatchAnalysisModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95" class="fixed inset-0 z-50 overflow-y-auto"
            style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showBatchAnalysisModal = false"></div>
                <div
                    class="neo-modal neo-modal-xl bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-4xl w-full relative border border-gray-200 dark:border-gray-700">
                    {{-- Neo Batch Analysis Modal Header --}}
                    <div class="neo-modal-header px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="neo-icon-container w-10 h-10 bg-gradient-to-r from-green-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">🚀 Toplu AI Analizi</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Tüm talepleri akıllı analiz ile
                                        işle</p>
                                </div>
                            </div>
                            <button @click="showBatchAnalysisModal = false"
                                class="neo-btn neo-btn-ghost p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors touch-target-optimized touch-target-optimized">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="px-6 py-6">
                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            Tüm status talepleri AI ile analiz ederek eşleştirme oranını artırmak ister misiniz?
                        </p>
                        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="font-medium text-blue-900 dark:text-blue-200">İşlem Detayları</span>
                            </div>
                            <ul class="text-sm text-blue-800 dark:text-blue-300 space-y-1">
                                <li>• {{ $talepler->total() }} talep analiz edilecek</li>
                                <li>• Tahmini süre: ~3-5 dakika</li>
                                <li>• Otomatik eşleştirme önerileri üretilecek</li>
                                <li>• E-posta ile sonuçlar bildirilecek</li>
                            </ul>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 rounded-b-xl">
                        <div class="flex items-center justify-end gap-3">
                            <button @click="showBatchAnalysisModal = false" class="neo-btn neo-btn neo-btn-secondary touch-target-optimized touch-target-optimized">
                                İptal
                            </button>
                            <button @click="runBatchAnalysis()" class="neo-btn ai-badge text-white touch-target-optimized touch-target-optimized">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                Analizi Başlat
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function taleplerData() {
                return {
                    showAdvancedFilters: false,
                    showAnalysisModal: false,
                    showBatchAnalysisModal: false,
                    isAnalyzing: false,
                    analysisResult: null,
                    selectedTalepId: null,
                    loading: false, // 🆕 USTA Auto-Fix: Loading state eklendi
                    filterLoading: false, // 🆕 Filter için loading

                    init() {
                        console.log('AI Destekli Talepler Sistemi başlatıldı');
                    },

                    async analyzeWithAI(talepId) {
                        this.selectedTalepId = talepId;
                        this.showAnalysisModal = true;
                        this.isAnalyzing = true;
                        this.analysisResult = null;

                        try {
                            const response = await fetch(`/api/ai/analyze-demand/${talepId}`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json'
                                }
                            });

                            if (response.ok) {
                                const data = await response.json();
                                this.analysisResult = this.formatAnalysisResult(data);
                            } else {
                                this.analysisResult = '<div class="text-red-600">Analiz sırasında bir hata oluştu.</div>';
                            }
                        } catch (error) {
                            console.error('AI Analysis Error:', error);
                            this.analysisResult = '<div class="text-red-600">Bağlantı hatası oluştu.</div>';
                        } finally {
                            this.isAnalyzing = false;
                        }
                    },

                    async findMatches(talepId) {
                        try {
                            const response = await fetch(`/api/ai/find-matching-properties/${talepId}`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json'
                                }
                            });

                            if (response.ok) {
                                const data = await response.json();
                                // Redirect to matches page or show results
                                window.location.href = `/admin/talepler/${talepId}/matches`;
                            } else {
                                alert('Eşleştirme sırasında bir hata oluştu.');
                            }
                        } catch (error) {
                            console.error('Matching Error:', error);
                            alert('Bağlantı hatası oluştu.');
                        }
                    },

                    async runAISearch() {
                        // AI destekli akıllı arama
                        const searchInput = document.querySelector('input[name="search"]');
                        if (searchInput && searchInput.value.trim()) {
                            // Add AI enhancement to search
                            const form = searchInput.closest('form');
                            const hiddenInput = document.createElement('input');
                            hiddenInput.type = 'hidden';
                            hiddenInput.name = 'ai_enhanced';
                            hiddenInput.value = '1';
                            form.appendChild(hiddenInput);
                            form.submit();
                        } else {
                            alert('Lütfen arama terimi girin.');
                        }
                    },

                    async runBatchAnalysis() {
                        this.showBatchAnalysisModal = false;

                        try {
                            const response = await fetch('/api/ai/batch-analysis', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json'
                                }
                            });

                            if (response.ok) {
                                alert('Toplu analiz başlatıldı! Sonuçlar e-posta ile bildirilecektir.');
                            } else {
                                alert('Toplu analiz başlatılamadı.');
                            }
                        } catch (error) {
                            console.error('Batch Analysis Error:', error);
                            alert('Bağlantı hatası oluştu.');
                        }
                    },

                    formatAnalysisResult(data) {
                        return `
                <div class="space-y-4">
                    <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                        <h4 class="font-semibold text-green-900 dark:text-green-200 mb-2">AI Analiz Sonuçları</h4>
                        <div class="text-sm text-green-800 dark:text-green-300 space-y-2">
                            <div><strong>Kategori:</strong> ${data.category || 'Belirlenmedi'}</div>
                            <div><strong>Öncelik Skoru:</strong> ${data.priority_score || '0'}/100</div>
                            <div><strong>Tahmini Bütçe:</strong> ₺${data.estimated_budget || '0'}</div>
                            <div><strong>Eşleşen İlan Sayısı:</strong> ${data.matching_properties || '0'}</div>
                        </div>
                    </div>
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                        <h4 class="font-semibold text-blue-900 dark:text-blue-200 mb-2">AI Önerileri</h4>
                        <ul class="text-sm text-blue-800 dark:text-blue-300 space-y-1">
                            ${data.recommendations ? data.recommendations.map(rec => `<li>• ${rec}</li>`).join('') : '<li>• Henüz öneri bulunmuyor</li>'}
                        </ul>
                    </div>
                </div>
            `;
                    }
                }
            }
        </script>
    @endpush
@endsection
