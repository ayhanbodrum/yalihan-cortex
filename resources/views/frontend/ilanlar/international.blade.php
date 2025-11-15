@extends('layouts.frontend')

@section('title', 'Uluslararası Portföy - Yalıhan Emlak')

@section('content')
<div class="relative">
    <div class="absolute inset-x-0 top-0 h-72 bg-gradient-to-b from-blue-600/30 via-indigo-500/10 to-transparent dark:from-blue-900/30 dark:via-indigo-900/10 pointer-events-none"></div>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-6 relative">
        <div class="grid grid-cols-1 lg:grid-cols-[1.2fr_0.8fr] gap-10">
            <div class="space-y-6">
                <div class="inline-flex items-center gap-3 rounded-full bg-blue-600/10 dark:bg-blue-500/10 text-blue-700 dark:text-blue-300 px-4 py-1 text-xs font-semibold">
                    <i class="fas fa-globe"></i>
                    Global Portfolio 2025
                </div>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 dark:text-white leading-tight">
                    Dünyanın dört bir yanındaki premium gayrimenkulleri keşfet
                </h1>
                <p class="text-base sm:text-lg text-gray-600 dark:text-gray-300 max-w-2xl">
                    Satılık, kiralık, yazlık ve vatandaşlık programlarına uygun projeleri tek ekranda topla. AI destekli önerilerle yatırımını hızlandır.
                </p>

                <x-frontend.category-tabs :tabs="$categoryTabs ?? []" :active="$activeTab ?? 'sale'" />

                <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-3xl shadow-md p-6">
                    <form method="GET" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                        <div class="flex flex-col gap-2">
                            <label for="country" class="text-sm font-semibold text-gray-700 dark:text-gray-300">Ülke</label>
                            <select id="country" name="country" class="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-800 dark:text-gray-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 transition-all duration-200" style="color-scheme: light dark;">
                                <option value="">Tüm Ülkeler</option>
                                @foreach(($filters['countries'] ?? []) as $country)
                                    <option value="{{ $country['code'] }}" {{ ($selectedFilters['country'] ?? null) === $country['code'] ? 'selected' : '' }}>
                                        {{ $country['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label for="city" class="text-sm font-semibold text-gray-700 dark:text-gray-300">Şehir</label>
                            <select id="city" name="city" class="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-800 dark:text-gray-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 transition-all duration-200" style="color-scheme: light dark;">
                                <option value="">Tüm Şehirler</option>
                                @foreach(($filters['cities'] ?? []) as $city)
                                    <option value="{{ $city['code'] }}" {{ ($selectedFilters['city'] ?? null) === $city['code'] ? 'selected' : '' }}>
                                        {{ $city['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label for="citizenship" class="text-sm font-semibold text-gray-700 dark:text-gray-300">Vatandaşlık Durumu</label>
                            <select id="citizenship" name="citizenship" class="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-800 dark:text-gray-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 transition-all duration-200" style="color-scheme: light dark;">
                                <option value="">Tümü</option>
                                <option value="eligible" {{ ($selectedFilters['citizenship'] ?? null) === 'eligible' ? 'selected' : '' }}>Uygun Projeler</option>
                                <option value="not-eligible" {{ ($selectedFilters['citizenship'] ?? null) === 'not-eligible' ? 'selected' : '' }}>Uygun Olmayan</option>
                            </select>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Fiyat Aralığı</label>
                            <div class="flex items-center gap-2">
                                <input type="number" name="min_price" value="{{ $selectedFilters['min_price'] ?? '' }}" placeholder="Min" class="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 transition-all duration-200" aria-label="Minimum fiyat">
                                <span class="text-gray-400">-</span>
                                <input type="number" name="max_price" value="{{ $selectedFilters['max_price'] ?? '' }}" placeholder="Max" class="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 transition-all duration-200" aria-label="Maksimum fiyat">
                            </div>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label for="property_type" class="text-sm font-semibold text-gray-700 dark:text-gray-300">Emlak Türü</label>
                            <select id="property_type" name="property_type" class="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-800 dark:text-gray-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 transition-all duration-200" style="color-scheme: light dark;">
                                <option value="">Hepsi</option>
                                @foreach(($filters['types'] ?? []) as $type)
                                    <option value="{{ $type['value'] }}" {{ ($selectedFilters['property_type'] ?? null) === $type['value'] ? 'selected' : '' }}>
                                        {{ $type['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label for="delivery" class="text-sm font-semibold text-gray-700 dark:text-gray-300">Teslim Durumu</label>
                            <select id="delivery" name="delivery" class="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-800 dark:text-gray-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 transition-all duration-200" style="color-scheme: light dark;">
                                <option value="">Tümü</option>
                                <option value="ready" {{ ($selectedFilters['delivery'] ?? null) === 'ready' ? 'selected' : '' }}>Hemen Teslim</option>
                                <option value="under-construction" {{ ($selectedFilters['delivery'] ?? null) === 'under-construction' ? 'selected' : '' }}>Devam Eden</option>
                            </select>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Alan (m²)</label>
                            <div class="flex items-center gap-2">
                                <input type="number" name="min_area" value="{{ $selectedFilters['min_area'] ?? '' }}" placeholder="Min" class="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 transition-all duration-200" aria-label="Minimum alan">
                                <span class="text-gray-400">-</span>
                                <input type="number" name="max_area" value="{{ $selectedFilters['max_area'] ?? '' }}" placeholder="Max" class="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 transition-all duration-200" aria-label="Maksimum alan">
                            </div>
                        </div>

                        <div class="flex items-end gap-3">
                            <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 dark:bg-blue-500 px-5 py-3 text-sm font-semibold text-white shadow-md transition-all duration-200 hover:bg-blue-700 dark:hover:bg-blue-600 hover:shadow-lg active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
                                <i class="fas fa-search"></i>
                                Filtreleri Uygula
                            </button>
                            <a href="{{ route('ilanlar.international') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-200 dark:border-gray-700 px-5 py-3 text-sm font-semibold text-gray-700 dark:text-gray-200 transition-all duration-200 hover:bg-gray-100 dark:hover:bg-gray-800 active:scale-95">
                                <i class="fas fa-undo"></i>
                                Temizle
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="space-y-6">
                <x-frontend.ai-guide-card />

                <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-3xl p-6 shadow-md space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Hızlı Bilgiler</h3>
                    <dl class="grid grid-cols-2 gap-4 text-sm text-gray-600 dark:text-gray-300">
                        <div>
                            <dt class="font-semibold">Toplam Portföy</dt>
                            <dd>{{ number_format($stats['total'] ?? 0) }} ilan</dd>
                        </div>
                        <div>
                            <dt class="font-semibold">Vatandaşlık Uygun</dt>
                            <dd>{{ number_format($stats['citizenship'] ?? 0) }} ilan</dd>
                        </div>
                        <div>
                            <dt class="font-semibold">Aktif Ülkeler</dt>
                            <dd>{{ number_format($stats['countries'] ?? 0) }} ülke</dd>
                        </div>
                        <div>
                            <dt class="font-semibold">Ortalama Fiyat</dt>
                            <dd>{{ $stats['average_price'] ?? '—' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16 space-y-10">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Seçkin Projeler</h2>
                <p class="text-gray-600 dark:text-gray-300 text-sm">AI tarafından önerilen premium portföy</p>
            </div>
            <div class="flex items-center gap-3">
                <label for="sort" class="text-sm font-semibold text-gray-700 dark:text-gray-300">Sırala:</label>
                <select id="sort" name="sort" class="rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2 text-sm text-gray-800 dark:text-gray-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 transition-all duration-200" style="color-scheme: light dark;">
                    <option value="newest">En Yeni</option>
                    <option value="price-asc">Fiyat (Artan)</option>
                    <option value="price-desc">Fiyat (Azalan)</option>
                    <option value="citizenship">Vatandaşlık Uygun</option>
                </select>
            </div>
        </div>

        @if(($featured ?? collect())->count())
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
                @foreach($featured as $property)
                    <x-frontend.property-card-global :property="$property" :currency="$currency ?? 'USD'" :converted-price="$property->converted_price ?? null" />
                @endforeach
            </div>

            @if(is_object($featured) && method_exists($featured, 'links'))
                <div class="mt-10">
                    {{ $featured->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            <div class="rounded-3xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-12 text-center">
                <i class="fas fa-search text-4xl text-gray-300 dark:text-gray-600"></i>
                <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">Henüz portföy bulunamadı</h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">Filtreleri değiştirerek tekrar deneyin veya danışmanlarımızla iletişime geçin.</p>
            </div>
        @endif
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-3xl p-8 shadow-md">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="space-y-3 max-w-2xl">
                    <span class="text-xs font-semibold uppercase text-blue-600 dark:text-blue-400 tracking-wide">Vatandaşlık Programları</span>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Yatırım ile vatandaşlık sağlayan ülkeler</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Gayrimenkul yatırımıyla vatandaşlık veya oturum izni sunan ülkelerin güncel listesi.</p>
                </div>

                <a href="{{ route('contact') }}" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 dark:bg-blue-500 px-6 py-3 text-sm font-semibold text-white shadow-md transition-all duration-200 hover:bg-blue-700 dark:hover:bg-blue-600 hover:shadow-lg active:scale-95">
                    Danışman Desteği Al
                    <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>

            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach(($citizenshipPrograms ?? []) as $program)
                    <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/60 p-6 space-y-3 transition-all duration-200 hover:border-blue-500 hover:shadow-lg">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $program['country'] }}</h3>
                            <span class="text-xs font-semibold inline-flex items-center gap-1 rounded-full bg-blue-600/10 text-blue-600 dark:text-blue-300 px-3 py-1">
                                <i class="fas fa-clock"></i>
                                {{ $program['processing_time'] }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Minimum yatırım: {{ $program['min_investment'] }}</p>
                        <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-300">
                            @foreach($program['highlights'] as $highlight)
                                <li class="flex items-start gap-2">
                                    <i class="fas fa-check mt-1 text-blue-500"></i>
                                    <span>{{ $highlight }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-24">
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-3xl p-8 shadow-md">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Sık Sorulan Sorular</h2>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">Uluslararası gayrimenkul yatırımlarında merak edilen her şey.</p>
                </div>

                <div class="space-y-4">
                    @foreach(($faqs ?? []) as $faq)
                        <details class="group rounded-2xl border border-gray-200 dark:border-gray-800 p-4 transition-all duration-200 hover:border-blue-500">
                            <summary class="flex items-center justify-between text-sm font-semibold text-gray-800 dark:text-gray-200 cursor-pointer">
                                {{ $faq['question'] }}
                                <i class="fas fa-chevron-down text-xs text-gray-400 group-open:rotate-180 transition-transform duration-200"></i>
                            </summary>
                            <p class="mt-3 text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                                {{ $faq['answer'] }}
                            </p>
                        </details>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

