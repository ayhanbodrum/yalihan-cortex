@extends('admin.layouts.neo')

@section('title', 'İlan Özellikleri Yönetimi')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8" x-data="{
        activeTab: '{{ $activeTab ?? 'ozellikler' }}',
        setTab(tab) {
            this.activeTab = tab;
            window.location.hash = tab;
        }
    }" x-init="// URL hash'den tab'ı al
    if (window.location.hash) {
        activeTab = window.location.hash.substring(1);
    }">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    🏷️ İlan Özellikleri Yönetimi
                </h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    İlan formlarında kullanılacak özellikleri ve kategorilerini tek sayfada yönetin
                </p>
            </div>

            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="text-sm opacity-90 mb-1">Toplam Özellik</div>
                    <div class="text-3xl font-bold">{{ $istatistikler['toplam'] }}</div>
                </div>
                {{-- ✅ Context7: "Aktif" kelimesi yasak, "Yayında" kullanılmalı --}}
                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="text-sm opacity-90 mb-1">Yayında</div>
                    <div class="text-3xl font-bold">{{ $istatistikler['aktif'] }}</div>
                </div>
                {{-- ✅ Context7: "Pasif" kelimesi yasak, "Taslak" kullanılmalı --}}
                <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="text-sm opacity-90 mb-1">Taslak</div>
                    <div class="text-3xl font-bold">{{ $istatistikler['pasif'] }}</div>
                </div>
                <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="text-sm opacity-90 mb-1">Kategorisiz</div>
                    <div class="text-3xl font-bold">{{ $istatistikler['kategorisiz'] }}</div>
                </div>
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="text-sm opacity-90 mb-1">Kategori</div>
                    <div class="text-3xl font-bold">{{ $istatistikler['kategori_sayisi'] }}</div>
                </div>
            </div>

            {{-- Tab Navigation (PHASE 2.2: Tab-based UI!) --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">

                {{-- Tab Headers --}}
                <div class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                    <nav class="flex -mb-px">
                        <button @click="setTab('ozellikler')"
                            :class="activeTab === 'ozellikler' ? 'border-blue-500 text-blue-600 dark:text-blue-400' :
                                'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                            class="group inline-flex items-center gap-2 px-6 py-4 border-b-2 font-semibold text-sm transition-colors">
                            <span class="text-lg">📋</span>
                            Tüm Özellikler
                            <span
                                class="ml-2 px-2 py-0.5 rounded-full text-xs bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                {{ $istatistikler['toplam'] }}
                            </span>
                        </button>

                        <button @click="setTab('kategoriler')"
                            :class="activeTab === 'kategoriler' ? 'border-purple-500 text-purple-600 dark:text-purple-400' :
                                'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                            class="group inline-flex items-center gap-2 px-6 py-4 border-b-2 font-semibold text-sm transition-colors">
                            <span class="text-lg">🏷️</span>
                            Kategoriler
                            <span
                                class="ml-2 px-2 py-0.5 rounded-full text-xs bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200">
                                {{ $istatistikler['kategori_sayisi'] }}
                            </span>
                        </button>

                        <button @click="setTab('kategorisiz')"
                            :class="activeTab === 'kategorisiz' ? 'border-yellow-500 text-yellow-600 dark:text-yellow-400' :
                                'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                            class="group inline-flex items-center gap-2 px-6 py-4 border-b-2 font-semibold text-sm transition-colors">
                            <span class="text-lg">⚠️</span>
                            Kategorisiz
                            <span
                                class="ml-2 px-2 py-0.5 rounded-full text-xs bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">
                                {{ $istatistikler['kategorisiz'] }}
                            </span>
                        </button>
                    </nav>
                </div>

                {{-- Tab Content --}}
                <div class="p-6">

                    {{-- TAB 1: Tüm Özellikler --}}
                    <div x-show="activeTab === 'ozellikler'" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform scale-95"
                        x-transition:enter-end="opacity-100 transform scale-100">

                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                                📋 Tüm Özellikler
                            </h2>
                            <a href="{{ route('admin.ozellikler.create') }}"
                                class="px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-lg hover:scale-105 transition-all shadow-lg">
                                + Yeni Özellik
                            </a>
                        </div>

                        @if ($ozellikler->isEmpty())
                            <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                                <div class="text-4xl mb-2">📭</div>
                                <p>Henüz özellik bulunmuyor</p>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-50 dark:bg-gray-900">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-bold text-gray-900 dark:text-white uppercase">
                                                Özellik</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-bold text-gray-900 dark:text-white uppercase">
                                                Kategori</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-bold text-gray-900 dark:text-white uppercase">
                                                Tip</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-bold text-gray-900 dark:text-white uppercase">
                                                Durum</th>
                                            <th
                                                class="px-6 py-3 text-right text-xs font-bold text-gray-900 dark:text-white uppercase">
                                                İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach ($ozellikler as $ozellik)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                                <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-white">
                                                    {{ $ozellik->name }}
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                                    {{ $ozellik->category->name ?? 'Kategorisiz' }}
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                                    {{ $ozellik->type ?? ($ozellik->field_type ?? 'text') }}
                                                </td>
                                                {{-- ✅ Context7: "Aktif" kelimesi yasak, "Yayında" kullanılmalı --}}
                                                <td class="px-6 py-4">
                                                    <span
                                                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                    {{ $ozellik->status ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                                        {{ $ozellik->status ? 'Yayında' : 'Taslak' }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 text-right text-sm">
                                                    <a href="{{ route('admin.ozellikler.edit', $ozellik) }}"
                                                        class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                                        Düzenle
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4">
                                {{ $ozellikler->appends(['tab' => 'ozellikler'])->links() }}
                            </div>
                        @endif
                    </div>

                    {{-- TAB 2: Kategoriler --}}
                    <div x-show="activeTab === 'kategoriler'" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform scale-95"
                        x-transition:enter-end="opacity-100 transform scale-100">

                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                                🏷️ Özellik Kategorileri
                            </h2>
                            <a href="{{ route('admin.ozellikler.kategoriler.create') }}"
                                class="px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-semibold rounded-lg hover:scale-105 transition-all shadow-lg">
                                + Yeni Kategori
                            </a>
                        </div>

                        @if ($kategoriListesi->isEmpty())
                            <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                                <div class="text-4xl mb-2">📭</div>
                                <p>Henüz kategori bulunmuyor</p>
                            </div>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach ($kategoriListesi as $kategori)
                                    <div
                                        class="group bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 overflow-hidden">
                                        {{-- Icon Header --}}
                                        <div
                                            class="relative h-32 bg-gradient-to-br from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 flex items-center justify-center">
                                            @php
                                                $iconClass = $kategori->icon ?? 'fas fa-list';
                                                $isFontAwesome =
                                                    str_starts_with($iconClass, 'fas ') ||
                                                    str_starts_with($iconClass, 'far ') ||
                                                    str_starts_with($iconClass, 'fab ') ||
                                                    str_starts_with($iconClass, 'fal ');
                                            @endphp
                                            @if ($isFontAwesome)
                                                <i
                                                    class="{{ $iconClass }} text-5xl text-blue-600 dark:text-blue-400 group-hover:scale-110 transition-transform duration-300"></i>
                                            @else
                                                <span
                                                    class="text-5xl group-hover:scale-110 transition-transform duration-300">{{ $kategori->icon ?? '📦' }}</span>
                                            @endif
                                            {{-- Gradient Overlay --}}
                                            <div
                                                class="absolute inset-0 bg-gradient-to-t from-white/50 dark:from-gray-800/50 to-transparent">
                                            </div>
                                        </div>

                                        {{-- Content --}}
                                        <div class="p-6">
                                            <div class="mb-3">
                                                <h3
                                                    class="text-xl font-bold text-gray-900 dark:text-white mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                                    {{ $kategori->name }}
                                                </h3>
                                                <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                                                    {{ $kategori->description ?? 'Açıklama yok' }}
                                                </p>
                                            </div>

                                            {{-- Stats & Action --}}
                                            <div
                                                class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-700">
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                                                    <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                                    </svg>
                                                    {{ $kategori->features_count ?? 0 }} özellik
                                                </span>
                                                <a href="{{ route('admin.ozellikler.kategoriler.show', $kategori) }}"
                                                    class="inline-flex items-center text-sm font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 group-hover:gap-2 gap-1 transition-all duration-200">
                                                    Detay
                                                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M9 5l7 7-7 7" />
                                                    </svg>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-6">
                                {{ $kategoriListesi->appends(['tab' => 'kategoriler'])->links() }}
                            </div>
                        @endif
                    </div>

                    {{-- TAB 3: Kategorisiz Özellikler --}}
                    <div x-show="activeTab === 'kategorisiz'" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform scale-95"
                        x-transition:enter-end="opacity-100 transform scale-100">

                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                                    ⚠️ Kategorisiz Özellikler
                                </h2>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    Bu özellikler henüz bir kategoriye atanmamış
                                </p>
                            </div>
                        </div>

                        @if ($kategorisizOzellikler->isEmpty())
                            <div class="text-center py-12">
                                <div class="text-6xl mb-4">✅</div>
                                <h3 class="text-xl font-bold text-green-600 dark:text-green-400 mb-2">
                                    Harika! Tüm özellikler kategorize edilmiş
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400">
                                    Kategorisiz özellik bulunmuyor
                                </p>
                            </div>
                        @else
                            <div
                                class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4 mb-6">
                                <div class="flex items-center gap-2">
                                    <span class="text-yellow-600 dark:text-yellow-400">⚠️</span>
                                    <span class="text-sm text-yellow-700 dark:text-yellow-300 font-semibold">
                                        {{ $kategorisizOzellikler->total() }} özellik kategoriye atanmayı bekliyor
                                    </span>
                                </div>
                            </div>

                            <div class="space-y-3">
                                @foreach ($kategorisizOzellikler as $ozellik)
                                    <div
                                        class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1">
                                                <h4 class="font-semibold text-gray-900 dark:text-white">
                                                    {{ $ozellik->name }}
                                                </h4>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                    Tip: {{ $ozellik->type ?? ($ozellik->field_type ?? 'text') }}
                                                </p>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('admin.ozellikler.edit', $ozellik) }}"
                                                    class="px-3 py-1.5 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                                                    Kategoriye Ata
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-4">
                                {{ $kategorisizOzellikler->appends(['tab' => 'kategorisiz'])->links() }}
                            </div>
                        @endif
                    </div>

                </div>
            </div>

        </div>
    </div>
@endsection
