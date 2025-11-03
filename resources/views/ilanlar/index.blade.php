@extends('layouts.frontend')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/professional-design-system.css') }}">
@endpush

@section('title', 'İlanlar - Yalıhan Emlak')
@section('description', 'Bodrum\'da satılık ve kiralık ev, villa, arsa ilanları. Güvenilir emlak danışmanlığı hizmeti.')

@section('content')
    {{-- KAYAK.com.tr Inspired Listings Page --}}
    <div class="min-h-screen bg-gray-50">

        {{-- Hero Section --}}
        <section class="relative bg-gradient-to-br from-blue-600 via-blue-700 to-blue-800 text-white py-20">
            <div class="absolute inset-0 bg-black opacity-20"></div>
            <div class="container mx-auto px-4 lg:px-8 relative z-10">
                <div class="text-center">
                    <h1 class="text-4xl lg:text-6xl font-bold mb-6">
                        <span class="bg-gradient-to-r from-white to-blue-100 bg-clip-text text-transparent">
                            Emlak İlanları
                        </span>
                    </h1>
                    <p class="text-xl lg:text-2xl text-blue-100 mb-8 max-w-3xl mx-auto">
                        Bodrum'un en güzel lokasyonlarında, size özel seçilmiş emlak fırsatları
                    </p>

                    {{-- Search Bar --}}
                    <div class="max-w-4xl mx-auto">
                        <div class="bg-white rounded-2xl p-2 shadow-2xl">
                            <div class="flex flex-col lg:flex-row gap-2">
                                <div class="flex-1">
                                    <input type="text" placeholder="Lokasyon, mahalle veya proje adı yazın..."
                                        class="w-full px-6 py-4 text-gray-900 placeholder-gray-500 border-0 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                </div>
                                <div class="flex gap-2">
                                    <select
                                        class="px-4 py-4 text-gray-900 border-0 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                        <option>Satılık</option>
                                        <option>Kiralık</option>
                                    </select>
                                    <select
                                        class="px-4 py-4 text-gray-900 border-0 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                        <option>Ev</option>
                                        <option>Villa</option>
                                        <option>Arsa</option>
                                    </select>
                                    <button
                                        class="bg-orange-500 hover:bg-orange-600 text-white px-8 py-4 rounded-xl font-semibold transition-colors duration-200 flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                        Ara
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Filters Section --}}
        <section class="py-8 bg-white border-b border-gray-200">
            <div class="container mx-auto px-4 lg:px-8">
                <div class="flex flex-wrap gap-4 items-center justify-between">
                    <div class="flex flex-wrap gap-4">
                        <button
                            class="px-6 py-3 bg-blue-600 text-white rounded-full font-semibold hover:bg-blue-700 transition-colors">
                            Tümü ({{ $totalCount ?? 0 }})
                        </button>
                        <button
                            class="px-6 py-3 bg-gray-100 text-gray-700 rounded-full font-semibold hover:bg-gray-200 transition-colors">
                            Satılık ({{ $filteredCount ?? 0 }})
                        </button>
                        <button
                            class="px-6 py-3 bg-gray-100 text-gray-700 rounded-full font-semibold hover:bg-gray-200 transition-colors">
                            Kiralık
                        </button>
                    </div>

                    <div class="flex items-center gap-4">
                        <select
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option>En Yeni</option>
                            <option>Fiyat (Düşük → Yüksek)</option>
                            <option>Fiyat (Yüksek → Düşük)</option>
                            <option>Metrekare (Büyük → Küçük)</option>
                        </select>

                        <div class="flex border border-gray-300 rounded-lg overflow-hidden">
                            <button class="p-2 bg-blue-600 text-white">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z">
                                    </path>
                                </svg>
                            </button>
                            <button class="p-2 text-gray-600 hover:bg-gray-100">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Listings Grid --}}
        <section class="py-12">
            <div class="container mx-auto px-4 lg:px-8">

                @if (isset($ilanlar) && $ilanlar->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                        @foreach ($ilanlar as $ilan)
                            {{-- Property Card --}}
                            <div
                                class="bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden group">
                                {{-- Image --}}
                                <div class="relative h-64 overflow-hidden">
                                    <img src="{{ $ilan->resim ?? '/images/placeholder-property.jpg' }}"
                                        alt="{{ $ilan->baslik ?? 'Emlak İlanı' }}"
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">

                                    {{-- Badge --}}
                                    <div class="absolute top-4 left-4">
                                        <span class="bg-orange-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                                            {{ $ilan->ilan_turu ?? 'Satılık' }}
                                        </span>
                                    </div>

                                    {{-- Price --}}
                                    <div class="absolute bottom-4 right-4">
                                        <div class="bg-white/90 backdrop-blur-sm rounded-lg px-4 py-2.5">
                                            <span class="text-2xl font-bold text-gray-900">
                                                {{ number_format($ilan->fiyat ?? 0, 0, ',', '.') }} ₺
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Content --}}
                                <div class="p-6">
                                    <h3 class="text-xl font-bold text-gray-900 mb-2 line-clamp-2">
                                        {{ $ilan->baslik ?? 'Emlak İlanı' }}
                                    </h3>

                                    <div class="flex items-center text-gray-600 mb-4">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                            </path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <span class="text-sm">
                                            {{ $ilan->mahalle->ad ?? '' }} {{ $ilan->ilce->ad ?? '' }},
                                            {{ $ilan->il->il_adi ?? '' }}
                                        </span>
                                    </div>

                                    {{-- Features --}}
                                    <div class="flex items-center justify-between text-sm text-gray-600 mb-4">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z">
                                                </path>
                                            </svg>
                                            {{ $ilan->metrekare ?? '0' }} m²
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                                </path>
                                            </svg>
                                            {{ $ilan->oda_sayisi ?? '0' }} Oda
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0V6a2 2 0 012-2h4a2 2 0 012 2v1m-6 0h6m-6 0l-3 3m3-3l3 3">
                                                </path>
                                            </svg>
                                            {{ $ilan->banyo_sayisi ?? '0' }} Banyo
                                        </div>
                                    </div>

                                    {{-- Agent Info --}}
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div
                                                class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                    </path>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900">
                                                    {{ $ilan->danisman->ad ?? 'Danışman' }}
                                                    {{ $ilan->danisman->soyad ?? '' }}
                                                </p>
                                                <p class="text-xs text-gray-600">Emlak Danışmanı</p>
                                            </div>
                                        </div>

                                        <button
                                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
                                            Detay
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-12 flex justify-center">
                        {{ $ilanlar->links() }}
                    </div>
                @else
                    {{-- No Results --}}
                    <div class="text-center py-20">
                        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">İlan Bulunamadı</h3>
                        <p class="text-gray-600 mb-8">Arama kriterlerinize uygun ilan bulunamadı. Filtreleri değiştirmeyi
                            deneyin.</p>
                        <button
                            class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-semibold transition-colors">
                            Filtreleri Temizle
                        </button>
                    </div>
                @endif
            </div>
        </section>
    </div>

    {{-- Custom CSS for KAYAK.com.tr inspired design --}}
    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .group:hover .group-hover\:scale-110 {
            transform: scale(1.1);
        }

        /* KAYAK.com.tr Color Scheme */
        :root {
            --kayak-blue: #1E40AF;
            --kayak-orange: #F59E0B;
            --kayak-gray: #374151;
            --kayak-light-gray: #F3F4F6;
        }
    </style>
@endsection
