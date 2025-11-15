@extends('layouts.frontend')

@section('title', 'Portföy - Yalıhan Emlak')

@section('content')
<div class="relative">
    <div class="absolute inset-x-0 top-0 h-72 bg-gradient-to-b from-blue-600/30 via-indigo-500/10 to-transparent dark:from-blue-900/30 dark:via-indigo-900/10 pointer-events-none"></div>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-6 relative">
        <div class="text-center mb-12">
            <div class="inline-flex items-center gap-3 rounded-full bg-blue-600/10 dark:bg-blue-500/10 text-blue-700 dark:text-blue-300 px-4 py-1 text-xs font-semibold mb-4">
                <i class="fas fa-building"></i>
                Yalıhan Emlak Portföyü
            </div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 dark:text-white leading-tight mb-4">
                Seçkin Gayrimenkul Portföyü
            </h1>
            <p class="text-base sm:text-lg text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                Yalıhan Emlak'ın geniş portföyünden hayalinizdeki evi bulun. Satılık, kiralık ve yatırımlık gayrimenkuller tek platformda.
            </p>
        </div>

        <!-- Stats Section -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-12">
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-md text-center transition-all duration-300 hover:shadow-lg">
                <div class="text-3xl font-bold text-blue-600 dark:text-blue-400 mb-2">
                    {{ number_format($stats['total_properties'] ?? 0, 0, ',', '.') }}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-300 font-medium">Toplam İlan</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-md text-center transition-all duration-300 hover:shadow-lg">
                <div class="text-3xl font-bold text-green-600 dark:text-green-400 mb-2">
                    {{ number_format($stats['active_properties'] ?? 0, 0, ',', '.') }}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-300 font-medium">Aktif İlan</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-md text-center transition-all duration-300 hover:shadow-lg">
                <div class="text-3xl font-bold text-purple-600 dark:text-purple-400 mb-2">
                    {{ number_format($stats['total_value'] ?? 0, 1, ',', '.') }}M
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-300 font-medium">Toplam Değer (₺)</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-md text-center transition-all duration-300 hover:shadow-lg">
                <div class="text-3xl font-bold text-indigo-600 dark:text-indigo-400 mb-2">
                    {{ number_format($stats['locations'] ?? 0, 0, ',', '.') }}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-300 font-medium">Lokasyon</div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-3xl shadow-md p-6 mb-8">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="flex flex-col gap-2">
                    <label for="search" class="text-sm font-semibold text-gray-700 dark:text-gray-300">Arama</label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}"
                        class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 transition-all duration-200"
                        placeholder="Başlık veya açıklama..." aria-label="İlan arama">
                </div>

                <div class="flex flex-col gap-2">
                    <label for="kategori" class="text-sm font-semibold text-gray-700 dark:text-gray-300">Kategori</label>
                    <select id="kategori" name="kategori" class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 transition-all duration-200" style="color-scheme: light dark;" aria-label="Kategori seçiniz">
                        <option value="">Tüm Kategoriler</option>
                        @foreach(($kategoriler ?? []) as $kategori)
                            <option value="{{ $kategori->id }}" {{ request('kategori') == $kategori->id ? 'selected' : '' }}>
                                {{ $kategori->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col gap-2">
                    <label for="il" class="text-sm font-semibold text-gray-700 dark:text-gray-300">İl</label>
                    <select id="il" name="il" class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 transition-all duration-200" style="color-scheme: light dark;" aria-label="İl seçiniz">
                        <option value="">Tüm İller</option>
                        @foreach(($iller ?? []) as $il)
                            <option value="{{ $il->id }}" {{ request('il') == $il->id ? 'selected' : '' }}>
                                {{ $il->il_adi }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 dark:bg-blue-500 px-5 py-2.5 text-sm font-semibold text-white shadow-md transition-all duration-200 hover:bg-blue-700 dark:hover:bg-blue-600 hover:shadow-lg active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
                        <i class="fas fa-search"></i>
                        Filtrele
                    </button>
                    <a href="{{ route('frontend.portfolio.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-200 dark:border-gray-700 px-5 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-200 transition-all duration-200 hover:bg-gray-100 dark:hover:bg-gray-800 active:scale-95">
                        <i class="fas fa-undo"></i>
                        Temizle
                    </a>
                </div>
            </form>
        </div>

        <!-- Portfolio Grid -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $properties->total() }} ilan bulundu
                    </h2>
                    @if(request()->hasAny(['search', 'kategori', 'il']))
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Filtrelenmiş sonuçlar</p>
                    @endif
                </div>
            </div>

            @if($properties->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
                    @foreach($properties as $property)
                        <x-frontend.property-card-global
                            :property="$property"
                            :currency="$currency ?? strtoupper(session('currency', config('currency.default', 'TRY')))"
                            :converted-price="$property->converted_price ?? null" />
                    @endforeach
                </div>

                <!-- Pagination -->
                @if(method_exists($properties, 'links'))
                    <div class="mt-10 flex justify-center">
                        {{ $properties->appends(request()->query())->links('vendor.pagination.tailwind') }}
                    </div>
                @endif
            @else
                <div class="rounded-3xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-12 text-center">
                    <i class="fas fa-search text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Henüz portföy bulunamadı</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Filtreleri değiştirerek tekrar deneyin veya danışmanlarımızla iletişime geçin.</p>
                </div>
            @endif
        </div>
    </section>
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
