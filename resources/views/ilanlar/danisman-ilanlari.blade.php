@extends('layouts.frontend')

@section('title', $danisman->name . ' - Diğer İlanları')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumb -->
        <nav class="mb-6">
            <ol class="flex items-center space-x-2 text-sm text-gray-500">
                <li><a href="{{ route('home') }}" class="hover:text-primary-600">Ana Sayfa</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li><a href="{{ route('ilanlar.index') }}" class="hover:text-primary-600">İlanlar</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li class="text-gray-900">{{ $danisman->name }} - İlanları</li>
            </ol>
        </nav>

        <!-- Danışman Bilgileri -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center">
                    @if ($danisman->avatar)
                        <img src="{{ asset('storage/' . $danisman->avatar) }}" alt="{{ $danisman->name }}"
                            class="w-16 h-16 rounded-full object-cover">
                    @else
                        <i class="fas fa-user text-primary-600 text-2xl"></i>
                    @endif
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $danisman->name }}</h1>
                    <p class="text-gray-600">Emlak Danışmanı</p>
                    @if ($danisman->telefon)
                        <p class="text-gray-600"><i class="fas fa-phone mr-1"></i> {{ $danisman->telefon }}</p>
                    @endif
                    @if ($danisman->email)
                        <p class="text-gray-600"><i class="fas fa-envelope mr-1"></i> {{ $danisman->email }}</p>
                    @endif
                </div>
                <div class="ml-auto text-right">
                    <div class="text-2xl font-bold text-primary-600">{{ $ilanlar->total() }}</div>
                    <div class="text-sm text-gray-500">Aktif İlan</div>
                </div>
            </div>
        </div>

        <!-- İlanlar -->
        @if ($ilanlar->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach ($ilanlar as $ilan)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                        <!-- Property Image -->
                        <div class="relative h-48 bg-gray-200">
                            <img src="{{ $ilan->kapak_fotografi_url ?? asset('images/default-property.jpg') }}"
                                alt="{{ $ilan->ilan_basligi }}" class="w-full h-full object-cover">

                            <!-- Property Type Badge -->
                            <span
                                class="absolute top-3 left-3 px-2 py-1 text-xs font-semibold rounded-full shadow-sm
                                {{ $ilan->ilan_turu == 'satilik' || $ilan->yayinlama_tipi == 'Satılık' ? 'bg-gradient-to-r from-emerald-500 to-green-600 text-white' : 'bg-gradient-to-r from-indigo-500 to-purple-600 text-white' }}">
                                {{ $ilan->yayinlama_tipi ?? ucfirst($ilan->ilan_turu) }}
                            </span>

                            @if ($ilan->one_cikan)
                                <span
                                    class="absolute top-3 right-3 px-2 py-1 text-xs font-semibold rounded-full bg-orange-500 text-white shadow-sm">
                                    Öne Çıkan
                                </span>
                            @endif
                        </div>

                        <!-- Property Details -->
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
                                {{ $ilan->ilan_basligi ?? ($ilan->baslik ?? 'İlan Başlığı') }}
                            </h3>

                            <p class="text-gray-600 text-sm mb-3 flex items-center">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                {{ $ilan->tam_adres ?? $ilan->adres_mahalle . ', ' . $ilan->adres_ilce . ', ' . $ilan->adres_il }}
                            </p>

                            <div class="flex items-center justify-between mb-3">
                                <x-price-converter :price="$ilan->fiyat" currency="TRY" />
                                <div class="text-sm text-gray-500">
                                    {{ ucfirst($ilan->emlak_turu) }}
                                </div>
                            </div>

                            <!-- Property Features -->
                            @if ($ilan->oda_sayisi || $ilan->banyo_sayisi || $ilan->net_metrekare)
                                <div class="flex items-center justify-between text-sm text-gray-600 mb-4">
                                    @if ($ilan->oda_sayisi)
                                        <span class="flex items-center">
                                            <i class="fas fa-bed mr-1"></i>
                                            {{ $ilan->oda_sayisi }}
                                        </span>
                                    @endif
                                    @if ($ilan->banyo_sayisi)
                                        <span class="flex items-center">
                                            <i class="fas fa-bath mr-1"></i>
                                            {{ $ilan->banyo_sayisi }}
                                        </span>
                                    @endif
                                    @if ($ilan->net_metrekare)
                                        <span class="flex items-center">
                                            <i class="fas fa-vector-square mr-1"></i>
                                            {{ $ilan->net_metrekare }}m²
                                        </span>
                                    @endif
                                </div>
                            @endif

                            <!-- View Details Button -->
                            <a href="{{ route('ilanlar.show', $ilan->id) }}"
                                class="block w-full text-center bg-primary-600 text-white py-2 px-4 rounded-lg hover:bg-primary-700 transition-colors">
                                <i class="fas fa-eye mr-2"></i>
                                Detayları Gör
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if ($ilanlar->hasPages())
                <div class="mt-12">
                    {{ $ilanlar->links() }}
                </div>
            @endif
        @else
            <!-- No Results -->
            <div class="text-center py-16">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                    <i class="fas fa-search text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Bu danışmana ait status ilan bulunamadı</h3>
                <p class="text-gray-600 mb-6">{{ $danisman->name }} şu anda yayında olan bir ilanı bulunmuyor.</p>
                <a href="{{ route('ilanlar.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Tüm İlanlara Dön
                </a>
            </div>
        @endif
    </div>
@endsection
