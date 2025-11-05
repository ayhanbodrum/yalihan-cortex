@extends('admin.layouts.neo')

@section('title', 'Talep Detay - ' . $talep->kisi->ad . ' ' . $talep->kisi->soyad)

@section('content')
    <div class="container mx-auto px-6 py-6">
        <!-- Modern Page Header -->
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <div
                            class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Talep Detayı</h1>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $talep->kisi->ad }}
                                {{ $talep->kisi->soyad }} - AI Destekli Analiz</p>
                        </div>
                    </div>
                    <!-- Breadcrumb -->
                    <nav class="flex items-center text-sm text-gray-500 dark:text-gray-400 mt-2">
                        <a href="{{ route('admin.dashboard') }}"
                            class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors duration-200">Dashboard</a>
                        <span class="px-2">/</span>
                        <a href="{{ route('admin.talep-portfolyo.index') }}"
                            class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors duration-200">Talep-Portföy
                            Eşleştirme</a>
                        <span class="px-2">/</span>
                        <span class="text-gray-900 dark:text-white">Talep Detayı</span>
                    </nav>
                </div>
                <div class="flex flex-wrap gap-3">
                    <!-- AI Analiz Butonu -->
                    <button
                        class="group relative inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-purple-300 dark:focus:ring-purple-800"
                        onclick="talepAnaliz({{ $talep->id }})">
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-purple-600 to-pink-600 rounded-xl blur opacity-0 group-hover:opacity-75 transition-opacity duration-300">
                        </div>
                        <svg class="w-5 h-5 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                        <span class="relative z-10">AI Analiz</span>
                    </button>

                    <!-- Portföy Öner Butonu -->
                    <button
                        class="group relative inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-green-300 dark:focus:ring-green-800"
                        onclick="portfolyoOnerModal()">
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-green-600 to-emerald-600 rounded-xl blur opacity-0 group-hover:opacity-75 transition-opacity duration-300">
                        </div>
                        <svg class="w-5 h-5 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span class="relative z-10">Portföy Öner</span>
                    </button>

                    <!-- Geri Butonu -->
                    <a href="{{ route('admin.talep-portfolyo.index') }}"
                        class="group inline-flex items-center gap-2 px-4 py-2.5 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white font-medium rounded-xl border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600 transform hover:-translate-y-0.5 transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600 shadow-sm hover:shadow-md">
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-700 dark:group-hover:text-gray-300 transition-colors duration-300"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        <span>Geri</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Modern Talep Bilgileri -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Müşteri ve Talep Bilgileri -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                    <div class="p-6">
                        <div class="flex items-center gap-3 mb-6">
                            <div
                                class="w-10 h-10 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Müşteri ve Talep Bilgileri</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Müşteri Bilgileri -->
                            <div class="space-y-4">
                                <h4
                                    class="text-md font-medium text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
                                    Müşteri Bilgileri</h4>
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Ad Soyad:</span>
                                        <span
                                            class="text-sm text-gray-900 dark:text-white font-medium">{{ $talep->kisi->ad }}
                                            {{ $talep->kisi->soyad }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Telefon:</span>
                                        <span
                                            class="text-sm text-gray-900 dark:text-white">{{ $talep->kisi->telefon }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">E-posta:</span>
                                        <span
                                            class="text-sm text-gray-900 dark:text-white">{{ $talep->kisi->email ?? 'Belirtilmemiş' }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Müşteri
                                            ID:</span>
                                        <span
                                            class="text-sm text-gray-900 dark:text-white font-mono">#{{ $talep->kisi->id }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Talep Detayları -->
                            <div class="space-y-4">
                                <h4
                                    class="text-md font-medium text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
                                    Talep Detayları</h4>
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Emlak
                                            Türü:</span>
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">
                                            {{ $talep->talep_tipi }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Fiyat
                                            Aralığı:</span>
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Belirtilmemiş</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Öncelik:</span>
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-400">
                                            {{ ucfirst($talep->status ?? 'Normal') }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Durum:</span>
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                                            {{ $talep->status }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Talep Açıklaması -->
                        <div class="mt-6">
                            <h4
                                class="text-md font-medium text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2 mb-4">
                                Talep Açıklaması</h4>
                            <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-4">
                                <p class="text-sm text-gray-900 dark:text-white leading-relaxed">{{ $talep->aciklama }}
                                </p>
                            </div>
                        </div>

                        @if ($talep->notlar)
                            <div class="mt-6">
                                <h4
                                    class="text-md font-medium text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2 mb-4">
                                    Notlar</h4>
                                <div
                                    class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4 border-l-4 border-yellow-400">
                                    <p class="text-sm text-yellow-800 dark:text-yellow-200 leading-relaxed">
                                        {{ $talep->notlar }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Lokasyon Tercihleri -->
            <div class="space-y-6">
                <div class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800">
                    <div class="p-6">
                        <div class="flex items-center gap-3 mb-6">
                            <div
                                class="w-10 h-10 bg-gradient-to-r from-green-500 to-emerald-500 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Lokasyon Tercihleri</h3>
                        </div>

                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Şehir:</span>
                                <span
                                    class="text-sm text-gray-900 dark:text-white">{{ $talep->il->il_adi ?? 'Belirtilmemiş' }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">İlçe:</span>
                                <span
                                    class="text-sm text-gray-900 dark:text-white">{{ $talep->ilce->ad ?? 'Belirtilmemiş' }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Mahalle:</span>
                                <span
                                    class="text-sm text-gray-900 dark:text-white">{{ $talep->mahalle->ad ?? 'Belirtilmemiş' }}</span>
                            </div>
                        </div>

                        @if ($talep->ozel_tercihler)
                            <div class="mt-6">
                                <h4
                                    class="text-md font-medium text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2 mb-4">
                                    Özel Tercihler</h4>
                                <div class="flex flex-wrap gap-2">
                                    @foreach (explode(',', $talep->ozel_tercihler) as $tercih)
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-400">
                                            {{ trim($tercih) }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Özellik Tercihleri -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                    <div class="p-6">
                        <div class="flex items-center gap-3 mb-6">
                            <div
                                class="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Özellik Tercihleri</h3>
                        </div>

                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Oda Sayısı:</span>
                                <span
                                    class="text-sm text-gray-900 dark:text-white">{{ $talep->oda_sayisi ?? 'Belirtilmemiş' }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Metrekare:</span>
                                <span class="text-sm text-gray-500 dark:text-gray-400">Belirtilmemiş</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Yaş:</span>
                                <span
                                    class="text-sm text-gray-900 dark:text-white">{{ $talep->yas ?? 'Belirtilmemiş' }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Kat:</span>
                                <span
                                    class="text-sm text-gray-900 dark:text-white">{{ $talep->kat ?? 'Belirtilmemiş' }}</span>
                            </div>
                        </div>

                        @if ($talep->manzara)
                            <div class="mt-6">
                                <h4
                                    class="text-md font-medium text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2 mb-4">
                                    Manzara Tercihleri</h4>
                                <div class="flex flex-wrap gap-2">
                                    @foreach (explode(',', $talep->manzara) as $manzara)
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">
                                            {{ trim($manzara) }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if ($talep->ozel_ozellikler)
                            <div class="mt-6">
                                <h4
                                    class="text-md font-medium text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2 mb-4">
                                    Özel Özellikler</h4>
                                <div class="flex flex-wrap gap-2">
                                    @foreach (explode(',', $talep->ozel_ozellikler) as $ozellik)
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                                            {{ trim($ozellik) }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Modern AI Analiz Sonucu -->
        @if ($analizSonucu)
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm mb-8">
                <div class="p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div
                            class="w-10 h-10 bg-gradient-to-r from-purple-600 to-pink-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">AI Analiz Sonucu</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Analiz Tarihi:
                                {{ \Carbon\Carbon::parse($analizSonucu['analiz_tarihi'])->format('d.m.Y H:i') }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Müşteri Profili -->
                        <div class="space-y-6">
                            <div class="flex items-center gap-2 mb-4">
                                <div
                                    class="w-8 h-8 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <h4 class="text-md font-medium text-gray-900 dark:text-white">Müşteri Profili</h4>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-4">
                                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Risk Profili
                                    </div>
                                    <div
                                        class="text-lg font-semibold {{ $analizSonucu['talep_analizi']['musteri_profili']['risk_profili'] == 'düşük' ? 'text-green-600 dark:text-green-400' : ($analizSonucu['talep_analizi']['musteri_profili']['risk_profili'] == 'yüksek' ? 'text-red-600 dark:text-red-400' : 'text-yellow-600 dark:text-yellow-400') }}">
                                        {{ ucfirst($analizSonucu['talep_analizi']['musteri_profili']['risk_profili']) }}
                                    </div>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-4">
                                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Müşteri Segmenti
                                    </div>
                                    <div class="text-lg font-semibold text-blue-600 dark:text-blue-400">
                                        {{ ucfirst($analizSonucu['talep_analizi']['musteri_profili']['musteri_segmenti']) }}
                                    </div>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-4">
                                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Satış
                                        Potansiyeli</div>
                                    <div
                                        class="text-lg font-semibold {{ $analizSonucu['talep_analizi']['musteri_profili']['satis_potansiyeli'] == 'yüksek' ? 'text-green-600 dark:text-green-400' : ($analizSonucu['talep_analizi']['musteri_profili']['satis_potansiyeli'] == 'düşük' ? 'text-red-600 dark:text-red-400' : 'text-yellow-600 dark:text-yellow-400') }}">
                                        {{ ucfirst($analizSonucu['talep_analizi']['musteri_profili']['satis_potansiyeli']) }}
                                    </div>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-4">
                                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Aciliyet
                                        Derecesi</div>
                                    <div
                                        class="text-lg font-semibold {{ $analizSonucu['talep_analizi']['musteri_profili']['aciliyet_derecesi'] == 'acil' ? 'text-red-600 dark:text-red-400' : ($analizSonucu['talep_analizi']['musteri_profili']['aciliyet_derecesi'] == 'yüksek' ? 'text-yellow-600 dark:text-yellow-400' : 'text-blue-600 dark:text-blue-400') }}">
                                        {{ ucfirst($analizSonucu['talep_analizi']['musteri_profili']['aciliyet_derecesi']) }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Talep Uygunluğu -->
                        <div class="space-y-6">
                            <div class="flex items-center gap-2 mb-4">
                                <div
                                    class="w-8 h-8 bg-gradient-to-r from-green-500 to-emerald-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <h4 class="text-md font-medium text-gray-900 dark:text-white">Talep Uygunluğu</h4>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-4">
                                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Genel Uygunluk
                                    </div>
                                    <div class="text-lg font-semibold text-blue-600 dark:text-blue-400">
                                        {{ number_format($analizSonucu['talep_analizi']['talep_analizi']['genel_uygunluk_skoru'], 1) }}/10
                                    </div>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-4">
                                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Fiyat Uygunluğu
                                    </div>
                                    <div class="text-lg font-semibold text-green-600 dark:text-green-400">
                                        {{ ucfirst($analizSonucu['talep_analizi']['talep_analizi']['fiyat_uygunlugu']) }}
                                    </div>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-4">
                                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Lokasyon
                                        Uygunluğu</div>
                                    <div class="text-lg font-semibold text-blue-600 dark:text-blue-400">
                                        {{ ucfirst($analizSonucu['talep_analizi']['talep_analizi']['lokasyon_uygunlugu']) }}
                                    </div>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-4">
                                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Özellik Uygunluğu</div>
                                    <div class="text-lg font-semibold text-amber-600 dark:text-amber-400">
                                        {{ ucfirst($analizSonucu['talep_analizi']['talep_analizi']['ozellik_uygunlugu']) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Eşleşen Portföyler -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm mb-8">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="p-3 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Eşleşen Portföyler</h3>
                            <p class="text-gray-600 dark:text-gray-400">AI tarafından önerilen emlaklar</p>
                        </div>
                    </div>
                    <span class="px-4 py-2 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 rounded-lg font-medium">
                        {{ count($eslesenPortfolyolar) }} portföy bulundu
                    </span>
                </div>
                <div>
                    @if (count($eslesenPortfolyolar) > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                            @foreach ($eslesenPortfolyolar as $eslesme)
                                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-xl transition-all duration-300">
                                    <div class="p-6">
                                        <div class="flex justify-between items-center mb-4">
                                            <h6 class="text-lg font-bold text-gray-900 dark:text-white">Portföy #{{ $eslesme['ilan']->id }}</h6>
                                            <div class="flex gap-2">
                                                @php
                                                    $skorClass = match (true) {
                                                        $eslesme['eslesme_skoru'] >= 8 => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                                        $eslesme['eslesme_skoru'] >= 6 => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                                        $eslesme['eslesme_skoru'] >= 4 => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
                                                        default => 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-400',
                                                    };
                                                @endphp
                                                <span class="px-3 py-1 rounded-full text-xs font-medium {{ $skorClass }}">
                                                    {{ number_format($eslesme['eslesme_skoru'], 1) }}/10
                                                </span>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="mb-4 rounded-xl overflow-hidden" style="height: 200px;">
                                                @if (isset($eslesme['ilan']->fotograflar) && $eslesme['ilan']->fotograflar->count() > 0)
                                                    <img src="{{ $eslesme['ilan']->fotograflar->first()->url }}"
                                                        alt="Portföy" class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full bg-gray-100 dark:bg-gray-800 flex flex-col items-center justify-center">
                                                        <svg class="w-16 h-16 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                                        <p class="text-gray-500 dark:text-gray-400 text-sm">Fotoğraf yok</p>
                                                    </div>
                                                @endif
                                            </div>

                                            <h6 class="text-md font-semibold text-gray-900 dark:text-white mb-4">
                                                {{ $eslesme['ilan']->baslik ?? $eslesme['ilan']->ilan_basligi ?? 'Başlık yok' }}</h6>

                                            <div class="space-y-3">
                                                <div class="flex justify-between items-center mb-2">
                                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Fiyat:</span>
                                                    <span class="text-sm font-semibold text-green-600 dark:text-green-400">
                                                        {{ number_format($eslesme['ilan']->fiyat, 0, ',', '.') }}
                                                        {{ $eslesme['ilan']->para_birimi ?? 'TRY' }}
                                                    </span>
                                                </div>
                                                <div class="flex justify-between items-center mb-2">
                                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Lokasyon:</span>
                                                    <span class="text-sm text-gray-900 dark:text-white">
                                                        {{ $eslesme['ilan']->il->il_adi ?? '' }} / {{ $eslesme['ilan']->ilce->ad ?? '' }}
                                                    </span>
                                                </div>
                                                <div class="flex justify-between items-center mb-2">
                                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Metrekare:</span>
                                                    <span class="text-sm text-gray-900 dark:text-white">
                                                        {{ $eslesme['ilan']->metrekare ?? 'Belirtilmemiş' }} m²
                                                    </span>
                                                </div>
                                                <div class="flex justify-between items-center mb-2">
                                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Oda Sayısı:</span>
                                                    <span class="text-sm text-gray-900 dark:text-white">
                                                        {{ $eslesme['ilan']->oda_sayisi ?? 'Belirtilmemiş' }}
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 mt-4">
                                                <div class="grid grid-cols-3 gap-4 text-center">
                                                    <div>
                                                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Uygunluk</div>
                                                        <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $eslesme['uygunluk_derecesi'] }}</div>
                                                    </div>
                                                    <div>
                                                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Öncelik</div>
                                                        <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $eslesme['oncelik_sirasi'] }}</div>
                                                    </div>
                                                    <div>
                                                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Öneri</div>
                                                        <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $eslesme['oneri_derecesi'] }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                                            <div class="flex gap-2">
                                                <button class="inline-flex items-center justify-center px-3 py-2 text-xs font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-md flex-1"
                                                    onclick="portfolyoOner({{ $eslesme['ilan']->id }}, '{{ $eslesme['oneri_derecesi'] }}')">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Öner
                                                </button>
                                                <a href="{{ route('admin.ilanlar.show', $eslesme['ilan']->id) }}"
                                                    class="inline-flex items-center justify-center px-3 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-sm flex-1">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg> Detay
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-800 dark:to-gray-700 rounded-full flex items-center justify-center mx-auto mb-6">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <h5 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Eşleşen portföy bulunamadı</h5>
                            <p class="text-gray-600 dark:text-gray-400 mb-6">
                                @if ($analizSonucu)
                                    AI analizi yapıldı ancak uygun portföy bulunamadı.
                                @else
                                    Henüz AI analizi yapılmamış.
                                @endif
                            </p>
                            <button class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-md hover:shadow-lg animate-pulse"
                                onclick="talepAnaliz({{ $talep->id }})">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                                {{ $analizSonucu ? 'Yeniden Analiz Et' : 'AI Analiz Yap' }}
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- AI Analiz Modal -->
    <div class="modal fade" id="aiAnalizModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">🤖 AI Talep Analizi</h5>
                    <button type="button" class="btn-close touch-target-optimized touch-target-optimized" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="aiAnalizContent">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Analiz ediliyor...</span>
                            </div>
                            <p class="mt-2">AI analizi yapılıyor, lütfen bekleyin...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-sm" data-bs-dismiss="modal">Kapat</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Portföy Önerisi Modal -->
    <div class="modal fade" id="portfolyoOnerModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">🤝 Portföy Önerisi</h5>
                    <button type="button" class="btn-close touch-target-optimized touch-target-optimized" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="portfolyoOnerForm">
                        <div class="mb-3">
                            <label for="ilan_id" class="admin-label">Portföy Seçin</label>
                            <select style="color-scheme: light dark;" class="admin-input transition-all duration-200" id="ilan_id" name="ilan_id" required>
                                <option value="">Portföy seçin...</option>
                                @foreach ($eslesenPortfolyolar as $eslesme)
                                    <option value="{{ $eslesme['ilan']->id }}"
                                        data-skor="{{ $eslesme['eslesme_skoru'] }}"
                                        data-uygunluk="{{ $eslesme['uygunluk_derecesi'] }}">
                                        #{{ $eslesme['ilan']->id }} - {{ $eslesme['ilan']->ilan_basligi ?? 'Başlık yok' }}
                                        ({{ number_format($eslesme['eslesme_skoru'], 1) }}/10)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="oneri_derecesi" class="admin-label">Öneri Derecesi</label>
                            <select style="color-scheme: light dark;" class="admin-input transition-all duration-200" id="oneri_derecesi" name="oneri_derecesi" required>
                                <option value="kesinlikle_oner">Kesinlikle Öner</option>
                                <option value="oner">Öner</option>
                                <option value="dusun">Düşün</option>
                                <option value="onerme">Önerme</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="notlar" class="admin-label">Notlar</label>
                            <textarea class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 w-full px-3 py-2 rounded-md border border-gray-200 bg-white text-sm placeholder:text-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-800 dark:border-gray-800 dark:text-gray-100 transition-colors" id="notlar" name="notlar" rows="3"
                                placeholder="Müşteriye özel notlar ekleyin..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-sm" data-bs-dismiss="modal">İptal</button>
                    <button type="button" class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg" onclick="portfolyoOnerGonder()">Öneriyi
                        Gönder</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Talep Analizi
        function talepAnaliz(talepId) {
            const modal = new bootstrap.Modal(document.getElementById('aiAnalizModal'));
            modal.show();

            fetch(`{{ url('admin/talep-portfolyo') }}/${talepId}/analiz`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        ai_analiz: true
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('aiAnalizContent').innerHTML = `
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> ${data.message}
                </div>
                <div class="text-center mt-3">
                    <button class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg touch-target-optimized touch-target-optimized" onclick="location.reload()">
                        <i class="fas fa-refresh"></i> Sayfayı Yenile
                    </button>
                </div>
            `;
                    } else {
                        document.getElementById('aiAnalizContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> ${data.message}
                </div>
            `;
                    }
                })
                .catch(error => {
                    console.error('Talep analiz hatası:', error);
                    document.getElementById('aiAnalizContent').innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i> Analiz sırasında hata oluştu
            </div>
        `;
                });
        }

        // Portföy Önerisi Modal
        function portfolyoOnerModal() {
            const modal = new bootstrap.Modal(document.getElementById('portfolyoOnerModal'));
            modal.show();
        }

        // Portföy Önerisi
        function portfolyoOner(ilanId, oneriDerecesi) {
            const modal = new bootstrap.Modal(document.getElementById('portfolyoOnerModal'));
            document.getElementById('ilan_id').value = ilanId;
            document.getElementById('oneri_derecesi').value = oneriDerecesi;
            modal.show();
        }

        // Portföy Önerisi Gönder
        function portfolyoOnerGonder() {
            const form = document.getElementById('portfolyoOnerForm');
            const formData = new FormData(form);

            fetch('{{ route('admin.talep-portfolyo.portfolyo-oner', $talep->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        ilan_id: document.getElementById('ilan_id').value,
                        oneri_derecesi: document.getElementById('oneri_derecesi').value,
                        notlar: document.getElementById('notlar').value
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Portföy önerisi başarıyla gönderildi!');
                        location.reload();
                    } else {
                        alert('Portföy önerisi hatası: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Portföy önerisi hatası:', error);
                    alert('Portföy önerisi sırasında hata oluştu');
                });
        }

        // İlan seçildiğinde öneri derecesini otomatik ayarla
        document.getElementById('ilan_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.dataset.skor) {
                const skor = parseFloat(selectedOption.dataset.skor);
                let oneriDerecesi = 'dusun';

                if (skor >= 8) oneriDerecesi = 'kesinlikle_oner';
                else if (skor >= 6) oneriDerecesi = 'oner';
                else if (skor >= 4) oneriDerecesi = 'dusun';
                else oneriDerecesi = 'onerme';

                document.getElementById('oneri_derecesi').value = oneriDerecesi;
            }
        });
    </script>
@endpush

@push('styles')
    <style>
        .portfolyo-card {
            transition: transform 0.2s, box-shadow 0.2s;
            border: 1px solid #e9ecef;
        }

        .portfolyo-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .portfolyo-image {
            height: 200px;
            overflow: hidden;
            border-radius: 8px;
        }

        .portfolyo-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .no-image-placeholder {
            height: 200px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
            border-radius: 8px;
        }

        .portfolyo-details p {
            margin-bottom: 0.5rem;
            font-size: 14px;
        }

        .eslesme-details {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
        }

        .stat-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1rem;
            background-color: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .stat-label {
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 0.5rem;
        }

        .stat-value {
            font-size: 16px;
            font-weight: 600;
        }

        .card-tools {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
    </style>
@endpush
