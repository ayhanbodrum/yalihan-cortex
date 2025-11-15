@extends('admin.layouts.neo')

@section('title', 'Özellik Detayı')

@section('content_header')
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">Özellik Detayı</h1>
        <a href="{{ route('admin.ozellikler.features.index') }}"
            class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg shadow-sm transition-colors duration-200">
            <i class="fas fa-arrow-left mr-2"></i> Listeye Dön
        </a>
    </div>
@endsection

@section('content')
    <div class="container mx-auto px-4 py-6">
        <!-- Başarı Mesajı -->
        @if (session('success'))
            <div
                class="mb-6 p-4 bg-green-100 dark:bg-green-900 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <!-- Hata Mesajı -->
        @if (session('error'))
            <div
                class="mb-6 p-4 bg-red-100 dark:bg-red-900 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Özellik Bilgileri</h3>
            </div>
            <div class="p-6">
                @if (isset($feature))
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Temel Bilgiler</h4>
                            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                                <div class="mb-4">
                                    <span class="block text-sm font-medium text-gray-700 dark:text-gray-300">Özellik
                                        Adı</span>
                                    <span
                                        class="block mt-1 text-sm text-gray-900 dark:text-white">{{ $feature->translations->first()->name ?? 'Belirtilmemiş' }}</span>
                                </div>
                                <div class="mb-4">
                                    <span class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kategori</span>
                                    <span
                                        class="block mt-1 text-sm text-gray-900 dark:text-white">{{ $feature->category->translations->first()->name ?? 'Belirtilmemiş' }}</span>
                                </div>
                                <div class="mb-4">
                                    <span class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tür</span>
                                    <span class="block mt-1 text-sm text-gray-900 dark:text-white">
                                        @if ($feature->type == 'boolean')
                                            <span
                                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Evet/Hayır</span>
                                        @elseif($feature->type == 'text')
                                            <span
                                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">Metin</span>
                                        @elseif($feature->type == 'number')
                                            <span
                                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">Sayı</span>
                                        @elseif($feature->type == 'select')
                                            <span
                                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">Seçim</span>
                                        @else
                                            <span
                                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300">{{ $feature->type }}</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="mb-4">
                                    <span class="block text-sm font-medium text-gray-700 dark:text-gray-300">Durum</span>
                                    <span class="block mt-1 text-sm text-gray-900 dark:text-white">
                                        @if ($item->status ?? true)
                                            <span
                                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Aktif</span>
                                        @else
                                            <span
                                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">Pasif</span>
                                        @endif
                                    </span>
                                </div>
                                <div>
                                    <span class="block text-sm font-medium text-gray-700 dark:text-gray-300">Zorunlu
                                        mu?</span>
                                    <span class="block mt-1 text-sm text-gray-900 dark:text-white">
                                        @if ($feature->is_required ?? false)
                                            <span
                                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">Evet</span>
                                        @else
                                            <span
                                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300">Hayır</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Detaylar</h4>
                            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                                <div class="mb-4">
                                    <span class="block text-sm font-medium text-gray-700 dark:text-gray-300">Açıklama</span>
                                    <span
                                        class="block mt-1 text-sm text-gray-900 dark:text-white">{{ $feature->translations->first()->description ?? 'Açıklama bulunmuyor.' }}</span>
                                </div>

                                @if ($feature->type == 'select' && !empty($feature->options))
                                    <div>
                                        <span
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Seçenekler</span>
                                        <div class="mt-2 space-y-1">
                                            @foreach (explode(',', $feature->options) as $option)
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 mr-2 mb-2">
                                                    {{ trim($option) }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="mt-6 flex space-x-3">
                                <a href="{{ route('admin.ozellikler.features.edit', $feature->id) }}"
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                        </path>
                                    </svg>
                                    Düzenle
                                </a>
                                <form action="{{ route('admin.ozellikler.features.destroy', $feature->id) }}" method="POST"
                                    class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                        onclick="return confirm('Bu özelliği silmek istediğinize emin misiniz?')">
                                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                        Sil
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Özellik bulunamadı</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">İstenen özellik bulunamadı veya silinmiş
                            olabilir.</p>
                        <div class="mt-6">
                            <a href="{{ route('admin.ozellikler.features.index') }}"
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Özellikler Listesine Dön
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
