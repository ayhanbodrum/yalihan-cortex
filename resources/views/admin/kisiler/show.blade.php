@extends('admin.layouts.neo')

@section('title', 'Müşteri Detayı')

@section('content')
    <div class="content-header mb-8">
        <div class="container-fluid">
            <div class="flex justify-between items-center">
                <div class="space-y-2">
                    <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-200 flex items-center">
                        <div
                            class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        {{ $kisi->tam_ad }}
                    </h1>
                    <p class="text-lg text-gray-600 dark:text-gray-400">
                        Müşteri detayları ve talepleri
                    </p>
                </div>
                <div class="flex items-center space-x-3">
                    <x-neo.button variant="primary" href="{{ route('admin.kisiler.edit', $kisi) }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Düzenle
                    </x-neo.button>

                    <x-neo.dropdown>
                        <x-neo.dropdown-item href="{{ route('admin.talepler.create', ['kisi_id' => $kisi->id]) }}"
                            icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>'>
                            Yeni Talep Oluştur
                        </x-neo.dropdown-item>
                        <x-neo.dropdown-item href="{{ route('admin.ilanlar.create') }}"
                            icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path></svg>'>
                            İlan Ekle
                        </x-neo.dropdown-item>
                        <x-neo.dropdown-item variant="danger"
                            onclick="if(confirm('Bu müşteriyi silmek istediğinizden emin misiniz?')) { document.getElementById('delete-form').submit(); }"
                            icon='<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>'>
                            Müşteriyi Sil
                        </x-neo.dropdown-item>
                    </x-neo.dropdown>

                    <form id="delete-form" action="{{ route('admin.kisiler.destroy', $kisi) }}" method="POST"
                        class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>

                    <x-neo.button variant="secondary" href="{{ route('admin.kisiler.index') }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Geri Dön
                    </x-neo.button>
                </div>
            </div>
        </div>
    </div>

    <!-- Müşteri Bilgileri -->
    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm mb-8">
        <div class="p-6">
            <h2 class="text-xl font-bold text-blue-800 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                👤 Müşteri Bilgileri
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div class="flex items-center">
                        <span class="w-24 font-medium text-gray-700">Ad Soyad:</span>
                        <span class="text-gray-900 font-semibold">{{ $kisi->ad }} {{ $kisi->soyad }}</span>
                    </div>
                    <div class="flex items-center">
                        <span class="w-24 font-medium text-gray-700">Telefon:</span>
                        <span class="text-gray-900 font-semibold">{{ $kisi->telefon }}</span>
                    </div>
                    <div class="flex items-center">
                        <span class="w-24 font-medium text-gray-700">E-posta:</span>
                        <span class="text-gray-900 font-semibold">{{ $kisi->email ?? '-' }}</span>
                    </div>
                    <div class="flex items-center">
                        <span class="w-24 font-medium text-gray-700">Adres:</span>
                        <span class="text-gray-900 font-semibold">
                            @if ($kisi->il)
                                {{ $kisi->il->il_adi ?? $kisi->il }}
                                @if ($kisi->ilce)
                                    , {{ $kisi->ilce->ilce_adi ?? $kisi->ilce }}
                                @endif
                                @if ($kisi->mahalle)
                                    , {{ $kisi->mahalle->mahalle_adi ?? $kisi->mahalle }}
                                @endif
                            @else
                                {{ $kisi->adres ?? '-' }}
                            @endif
                        </span>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center">
                        <span class="w-24 font-medium text-gray-700">Durum:</span>
                        <span
                            class="px-3 py-1 inline-flex text-sm font-semibold rounded-full
                            {{ $kisi->status == 'Aktif'
                                ? 'bg-green-100 text-green-800 border border-green-200'
                                : ($kisi->status == 'Pasif'
                                    ? 'bg-gray-100 text-gray-800 border border-gray-200'
                                    : ($kisi->status == 'Potansiyel'
                                        ? 'bg-blue-100 text-blue-800 border border-blue-200'
                                        : 'bg-yellow-100 text-yellow-800 border border-yellow-200')) }}">
                            {{ $kisi->status }}
                        </span>
                    </div>
                    <div class="flex items-center">
                        <span class="w-24 font-medium text-gray-700">Kaynak:</span>
                        <span class="text-gray-900 font-semibold">{{ $kisi->kaynak ?? '-' }}</span>
                    </div>
                    <div class="flex items-center">
                        <span class="w-24 font-medium text-gray-700">Danışman:</span>
                        <span class="text-gray-900 font-semibold">
                            @if ($kisi->danisman_verisi)
                                {{ $kisi->danisman_verisi->name }}
                                <span
                                    class="text-xs text-gray-500 ml-1">({{ $kisi->danisman_verisi->source === 'user_model' ? 'User Modeli' : 'Danışman Modeli' }})</span>
                            @else
                                Atanmamış
                            @endif
                        </span>
                    </div>
                    <div class="flex items-center">
                        <span class="w-24 font-medium text-gray-700">Kayıt Tarihi:</span>
                        <span class="text-gray-900 font-semibold">{{ $kisi->created_at->format('d.m.Y H:i') }}</span>
                    </div>
                </div>
            </div>

            <!-- Etiketler -->
            @if ($kisi->etiketler && $kisi->etiketler->count() > 0)
                <div class="mt-6 pt-6 border-t border-blue-200">
                    <h3 class="text-lg font-semibold text-blue-700 mb-3">Etiketler</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($kisi->etiketler as $etiket)
                            @php
                                $kisiTagBgColorVal = $etiket->renk ? $etiket->renk . '20' : '#e5e7eb';
                                $kisiTagTextColorVal = $etiket->renk ? $etiket->renk : '#374151';
                                $kisiTagStyles = [
                                    "background-color: {$kisiTagBgColorVal}",
                                    "color: {$kisiTagTextColorVal}",
                                ];
                            @endphp
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full border"
                                @style($kisiTagStyles)>
                                {{ $etiket->ad }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Notlar -->
            @if ($kisi->notlar)
                <div class="mt-6 pt-6 border-t border-blue-200">
                    <h3 class="text-lg font-semibold text-blue-700 mb-3">Notlar</h3>
                    <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $kisi->notlar }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Müşteri Talepleri -->
    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm mb-8">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-green-800 flex items-center">
                    <svg class="w-6 h-6 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    📋 Müşteri Talepleri
                </h2>
                <x-neo.button variant="primary" href="{{ route('admin.talepler.create', ['kisi_id' => $kisi->id]) }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Yeni Talep
                </x-neo.button>
            </div>

            @if ($kisi->talepler && $kisi->talepler->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-green-200">
                        <thead class="bg-green-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">
                                    Talep</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">
                                    Türü</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">
                                    Konum</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">
                                    Durum</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-green-800 uppercase tracking-wider">
                                    Tarih</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-green-800 uppercase tracking-wider">
                                    İşlemler</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-green-200">
                            @foreach ($kisi->talepler as $talep)
                                <tr class="hover:bg-green-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $talep->baslik }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ $talep->ilan_turu }} {{ $talep->emlak_turu }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ is_object($talep->il) ? $talep->il->il_adi : $talep->il ?? '' }}{{ $talep->ilce ? ', ' . (is_object($talep->ilce) ? $talep->ilce->ilce_adi : $talep->ilce) : '' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ $talep->status == 'Yeni'
                                                ? 'bg-blue-100 text-blue-800 border border-blue-200'
                                                : ($talep->status == 'İşleniyor'
                                                    ? 'bg-yellow-100 text-yellow-800 border border-yellow-200'
                                                    : ($talep->status == 'Beklemede'
                                                        ? 'bg-purple-100 text-purple-800 border border-purple-200'
                                                        : ($talep->status == 'Tamamlandı'
                                                            ? 'bg-green-100 text-green-800 border border-green-200'
                                                            : 'bg-red-100 text-red-800 border border-red-200'))) }}">
                                            {{ $talep->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $talep->created_at->format('d.m.Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            <x-neo.button variant="ghost" size="sm"
                                                href="{{ route('admin.talepler.show', $talep) }}">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                    </path>
                                                </svg>
                                                Detay
                                            </x-neo.button>
                                            <x-neo.button variant="ghost" size="sm"
                                                href="{{ route('admin.talepler.edit', $talep) }}">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                    </path>
                                                </svg>
                                                Düzenle
                                            </x-neo.button>
                                            <x-neo.button variant="ghost" size="sm"
                                                href="{{ route('admin.talepler.eslesen', $talep) }}">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                                    </path>
                                                </svg>
                                                Eşleşen
                                            </x-neo.button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Talep bulunamadı</h3>
                    <p class="mt-1 text-sm text-gray-500">Bu müşteri için henüz talep oluşturulmamış.</p>
                    <div class="mt-6">
                        <x-neo.button variant="primary"
                            href="{{ route('admin.talepler.create', ['kisi_id' => $kisi->id]) }}">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Yeni Talep Oluştur
                        </x-neo.button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Sahip Olduğu Gayrimenkuller -->
    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-purple-800 flex items-center">
                    <svg class="w-6 h-6 mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z" />
                    </svg>
                    🏠 Sahip Olduğu Gayrimenkuller
                </h2>
                <x-neo.button variant="primary" href="{{ route('admin.ilanlar.create') }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Yeni İlan Ekle
                </x-neo.button>
            </div>

            @if (isset($kisiGayrimenkulleri) && $kisiGayrimenkulleri->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-purple-200">
                        <thead class="bg-purple-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-purple-800 uppercase tracking-wider">
                                    İlan</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-purple-800 uppercase tracking-wider">
                                    Tür</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-purple-800 uppercase tracking-wider">
                                    Konum</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-purple-800 uppercase tracking-wider">
                                    Fiyat</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-purple-800 uppercase tracking-wider">
                                    Durum</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-purple-800 uppercase tracking-wider">
                                    Tarih</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-purple-800 uppercase tracking-wider">
                                    İşlemler</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-purple-200">
                            @foreach ($kisiGayrimenkulleri as $ilan)
                                <tr class="hover:bg-purple-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if ($ilan->kapak_fotografi_url)
                                                <img src="{{ $ilan->kapak_fotografi_url }}" alt="Kapak Fotoğrafı"
                                                    class="w-12 h-12 rounded-lg object-cover mr-3">
                                            @else
                                                <div
                                                    class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center mr-3">
                                                    <svg class="w-6 h-6 text-gray-400" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z" />
                                                    </svg>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $ilan->ilan_basligi }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    ID: {{ $ilan->id }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center space-x-2">
                                            <span
                                                class="px-2 py-1 text-xs font-medium rounded-full
                                                {{ $ilan->ilan_turu == 'Satılık' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-blue-100 text-blue-800 border border-blue-200' }}">
                                                {{ $ilan->ilan_turu }}
                                            </span>
                                            <span
                                                class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800 border border-gray-200">
                                                {{ $ilan->emlak_turu ?? 'Belirtilmemiş' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            @if ($ilan->il)
                                                {{ $ilan->il->il_adi ?? $ilan->il }}
                                            @endif
                                            @if ($ilan->ilce)
                                                , {{ $ilan->ilce->name ?? $ilan->ilce }}
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ number_format($ilan->fiyat, 0, ',', '.') }} ₺
                                        </div>
                                        @if ($ilan->net_metrekare)
                                            <div class="text-xs text-gray-500">
                                                {{ number_format($ilan->fiyat / $ilan->net_metrekare, 0, ',', '.') }} ₺/m²
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 py-1 text-xs font-medium rounded-full
                                            {{ $ilan->status == 'Aktif'
                                                ? 'bg-green-100 text-green-800 border border-green-200'
                                                : ($ilan->status == 'Pasif'
                                                    ? 'bg-red-100 text-red-800 border border-red-200'
                                                    : ($ilan->status == 'Taslak'
                                                        ? 'bg-yellow-100 text-yellow-800 border border-yellow-200'
                                                        : 'bg-gray-100 text-gray-800 border border-gray-200')) }}">
                                            {{ $ilan->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ $ilan->created_at->format('d.m.Y') }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $ilan->created_at->diffForHumans() }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            <x-neo.button variant="ghost" size="sm"
                                                href="{{ route('admin.ilanlar.show', $ilan) }}">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                    </path>
                                                </svg>
                                                Görüntüle
                                            </x-neo.button>
                                            <x-neo.button variant="ghost" size="sm"
                                                href="{{ route('admin.ilanlar.edit', $ilan) }}">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                    </path>
                                                </svg>
                                                Düzenle
                                            </x-neo.button>
                                            <x-neo.button variant="ghost" size="sm"
                                                href="{{ route('admin.ilanlar.show', $ilan) }}">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                                    </path>
                                                </svg>
                                                Analiz
                                            </x-neo.button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Gayrimenkul bulunamadı</h3>
                    <p class="mt-1 text-sm text-gray-500">Bu kişi henüz gayrimenkul ilanı eklememiş.</p>
                    <div class="mt-6">
                        <x-neo.button variant="primary" href="{{ route('admin.ilanlar.create') }}">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            İlk İlanı Ekle
                        </x-neo.button>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('styles')
@endpush
