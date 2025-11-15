@extends('layouts.frontend')

@section('title', 'İlanlar - Yalıhan Emlak')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 shadow-sm transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white transition-colors duration-300">Emlak İlanları</h1>
                <p class="mt-2 text-gray-600 dark:text-gray-300 transition-colors duration-300">Hayalinizdeki evi bulun</p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Filters Sidebar -->
            <div class="lg:w-1/4">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 sticky top-8 border border-gray-200 dark:border-gray-700 transition-all duration-300">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 transition-colors duration-300">Filtreler</h3>

                    <form method="GET" class="space-y-4">
                        <!-- Search -->
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-300">Arama</label>
                            <input type="text" id="search" name="search" value="{{ request('search') }}"
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 transition-all duration-200"
                                placeholder="İlan başlığı veya açıklama..." aria-label="İlan arama">
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="kategori" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-300">Kategori</label>
                            <select id="kategori" name="kategori" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 transition-all duration-200" style="color-scheme: light dark;" aria-label="Kategori seçiniz">
                                <option value="">Tüm Kategoriler</option>
                                @foreach($kategoriler as $kategori)
                                    <option value="{{ $kategori->id }}" {{ request('kategori') == $kategori->id ? 'selected' : '' }}>
                                        {{ $kategori->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- City -->
                        <div>
                            <label for="il" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-300">İl</label>
                            <select id="il" name="il" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 transition-all duration-200" style="color-scheme: light dark;" aria-label="İl seçiniz">
                                <option value="">Tüm İller</option>
                                @foreach($iller as $il)
                                    <option value="{{ $il->id }}" {{ request('il') == $il->id ? 'selected' : '' }}>
                                        {{ $il->il_adi }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Price Range -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-300">Fiyat Aralığı</label>
                            <div class="grid grid-cols-2 gap-2">
                                <input type="number" id="min_fiyat" name="min_fiyat" value="{{ request('min_fiyat') }}"
                                    class="px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 transition-all duration-200"
                                    placeholder="Min" aria-label="Minimum fiyat">
                                <input type="number" id="max_fiyat" name="max_fiyat" value="{{ request('max_fiyat') }}"
                                    class="px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 transition-all duration-200"
                                    placeholder="Max" aria-label="Maksimum fiyat">
                            </div>
                        </div>

                        <!-- Filter Buttons -->
                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 bg-blue-600 dark:bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600 active:scale-95 transition-all duration-200 font-medium shadow-md hover:shadow-lg focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800" aria-label="Filtreleri uygula">
                                Filtrele
                            </button>
                            <a href="{{ route('ilanlar.index') }}" class="flex-1 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 px-4 py-2 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-700 active:scale-95 transition-all duration-200 text-center font-medium shadow-md hover:shadow-lg focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                                Temizle
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Results -->
            <div class="lg:w-3/4">
                <!-- Results Header -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white transition-colors duration-300">
                            {{ $ilanlar->total() }} ilan bulundu
                        </h2>
                        @if(request()->hasAny(['search', 'kategori', 'il', 'min_fiyat', 'max_fiyat']))
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1 transition-colors duration-300">Filtrelenmiş sonuçlar</p>
                        @endif
                    </div>

                    <!-- Sort -->
                    <div>
                        <label for="sort" class="sr-only">Sıralama</label>
                        <select id="sort" class="px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 transition-all duration-200" style="color-scheme: light dark;" aria-label="Sıralama seçiniz">
                            <option>En Yeni</option>
                            <option>En Eski</option>
                            <option>Fiyat (Düşük → Yüksek)</option>
                            <option>Fiyat (Yüksek → Düşük)</option>
                        </select>
                    </div>
                </div>

                <!-- Properties Grid -->
                @if($ilanlar->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        @foreach($ilanlar as $ilan)
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden hover:shadow-md dark:hover:shadow-lg transition-all duration-300 border border-gray-200 dark:border-gray-700 transform hover:-translate-y-1">
                                <!-- Image -->
                                <div class="h-48 bg-gray-200 dark:bg-gray-700 relative overflow-hidden">
                                    @if($ilan->ilanFotograflari && $ilan->ilanFotograflari->count() > 0)
                                        <img src="{{ Storage::url($ilan->ilanFotograflari->first()->dosya_yolu) }}"
                                            alt="{{ $ilan->baslik }}"
                                            class="w-full h-full object-cover transition-transform duration-300 hover:scale-105"
                                            loading="lazy">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-400 dark:text-gray-500">
                                            <i class="fas fa-home text-4xl"></i>
                                        </div>
                                    @endif

                                    <!-- Category Badge -->
                                    <div class="absolute top-3 left-3">
                                        <span class="bg-blue-600 dark:bg-blue-500 text-white px-2 py-1 rounded-full text-xs font-medium shadow-lg">
                                            {{ $ilan->kategori->name ?? 'Genel' }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="p-4">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 line-clamp-2 transition-colors duration-300">
                                        {{ $ilan->baslik }}
                                    </h3>

                                    <p class="text-gray-600 dark:text-gray-300 text-sm mb-3 line-clamp-2 transition-colors duration-300">
                                        {{ Str::limit($ilan->aciklama, 100) }}
                                    </p>

                                    <!-- Location -->
                                    <div class="flex items-center text-gray-500 dark:text-gray-400 text-sm mb-3 transition-colors duration-300">
                                        <i class="fas fa-map-marker-alt mr-1"></i>
                                        <span>
                                            @if($ilan->il)
                                                {{ $ilan->il->il_adi }}
                                                @if($ilan->ilce)
                                                    / {{ $ilan->ilce->ilce_adi }}
                                                @endif
                                            @endif
                                        </span>
                                    </div>

                                    <!-- Price -->
                                    <div class="flex justify-between items-center">
                                        <div class="text-lg font-bold text-blue-600 dark:text-blue-400 transition-colors duration-300">
                                            {{ number_format($ilan->fiyat, 0, ',', '.') }}
                                            <span class="text-sm font-normal">{{ $ilan->para_birimi }}</span>
                                        </div>

                                        <a href="{{ route('ilanlar.show', $ilan->id) }}"
                                            class="bg-blue-600 dark:bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600 active:scale-95 transition-all duration-200 text-sm font-medium shadow-md hover:shadow-lg focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                                            Detay
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $ilanlar->appends(request()->query())->links() }}
                    </div>
                @else
                    <!-- No Results -->
                    <div class="text-center py-12 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 transition-colors duration-300">
                        <i class="fas fa-search text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2 transition-colors duration-300">İlan bulunamadı</h3>
                        <p class="text-gray-600 dark:text-gray-300 transition-colors duration-300">Arama kriterlerinizi değiştirerek tekrar deneyin.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endpush
