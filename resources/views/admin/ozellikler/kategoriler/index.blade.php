@extends('admin.layouts.admin')

@section('title', 'Özellik Kategorileri Yönetimi')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Özellik Kategorileri</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Emlak ilanları için özellik kategorilerini yönetin</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.ozellikler.index') }}" class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Özellikler
                </a>
                <a href="{{ route('admin.ozellikler.kategoriler.create') }}" class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg active:scale-95">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Yeni Kategori
                </a>
            </div>
        </div>

        <!-- Flash Messages -->
        @if (session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="text-green-700">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span class="text-red-700">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- Kategori Tablosu -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            @if ($kategoriler->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Kategori
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Özellik Sayısı
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Uygulama Alanı
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Durum
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    İşlemler
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($kategoriler as $kategori)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-200">
                                    <!-- Kategori Adı -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-lg bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                                                    <span class="text-orange-600 dark:text-orange-400 font-medium text-sm">
                                                        {{ substr($kategori->name, 0, 2) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $kategori->name }}
                                                </div>
                                                @if($kategori->description)
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                                        {{ Str::limit($kategori->description, 50) }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Özellik Sayısı -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                                            {{ $kategori->features_count ?? 0 }} özellik
                                        </span>
                                    </td>

                                    <!-- Uygulama Alanı (applies_to) -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-wrap gap-1">
                                            @php
                                                // ✅ Yalıhan Bekçi: JSON decode handling
                                                $appliesToArray = is_string($kategori->applies_to)
                                                    ? json_decode($kategori->applies_to, true)
                                                    : $kategori->applies_to;
                                            @endphp

                                            @if(is_array($appliesToArray) && count($appliesToArray) > 0)
                                                @foreach($appliesToArray as $type)
                                                    <span class="px-2 py-1 text-xs rounded-full bg-gradient-to-r from-purple-100 to-pink-100 dark:from-purple-900/30 dark:to-pink-900/30 border border-purple-200 dark:border-purple-800 text-purple-700 dark:text-purple-300 font-medium">
                                                        {{ ucfirst($type) }}
                                                    </span>
                                                @endforeach
                                            @else
                                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400">
                                                    Tümü
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Durum -->
                                    {{-- ✅ Context7: "Aktif" kelimesi yasak, "Yayında" kullanılmalı --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ $kategori->status ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' }}">
                                            {{ $kategori->status ? 'Yayında' : 'Taslak' }}
                                        </span>
                                    </td>

                                    <!-- İşlemler -->
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            <a href="{{ route('admin.ozellikler.kategoriler.edit', $kategori->id) }}"
                                               class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Düzenle
                                            </a>

                                            <form action="{{ route('admin.ozellikler.kategoriler.destroy', $kategori->id) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Bu kategoriyi silmek istediğinizden emin misiniz?');"
                                                  class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-red-600 to-pink-600 rounded-lg hover:from-red-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    Sil
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $kategoriler->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Kategori bulunamadı</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Yeni bir kategori oluşturarak başlayın.
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('admin.ozellikler.kategoriler.create') }}" class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg active:scale-95">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Yeni Kategori Oluştur
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
