@extends('admin.layouts.neo')

@section('title', 'İlan Özellikleri Yönetimi')

@section('content')
    <div class="neo-container">
        <!-- Neo Header -->
        <div class="neo-header">
            <div class="neo-header-content">
                <h1 class="neo-title">İlan Özellikleri</h1>
                <p class="neo-subtitle">İlan formlarında kullanılacak özellikleri yönetin</p>
            </div>
            <div class="neo-header-actions">
                <a href="{{ route('admin.ozellikler.kategoriler.index') }}" class="neo-btn neo-btn-secondary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    Kategoriler
                </a>
                <a href="{{ route('admin.ozellikler.create') }}" class="neo-btn neo-btn-primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Yeni Özellik
                </a>
            </div>
        </div>

        <!-- İstatistik Kartları -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="neo-card bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 border-blue-200 dark:border-blue-800/30">
                <div class="flex items-center justify-between">
                    <div class="p-3 bg-blue-500 rounded-xl text-white shadow-md">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-blue-700 dark:text-blue-300">{{ $istatistikler['toplam'] }}</div>
                        <div class="text-sm text-blue-600 dark:text-blue-400 font-medium">Toplam Özellik</div>
                    </div>
                </div>
            </div>

            <div class="neo-card bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 border-green-200 dark:border-green-800/30">
                <div class="flex items-center justify-between">
                    <div class="p-3 bg-green-500 rounded-xl text-white shadow-md">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-green-700 dark:text-green-300">{{ $istatistikler['aktif'] }}</div>
                        <div class="text-sm text-green-600 dark:text-green-400 font-medium">Aktif</div>
                    </div>
                </div>
            </div>

            <div class="neo-card bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 border-red-200 dark:border-red-800/30">
                <div class="flex items-center justify-between">
                    <div class="p-3 bg-red-500 rounded-xl text-white shadow-md">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-red-700 dark:text-red-300">{{ $istatistikler['pasif'] }}</div>
                        <div class="text-sm text-red-600 dark:text-red-400 font-medium">Pasif</div>
                    </div>
                </div>
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

        <!-- Filtreler -->
        <div class="neo-card mb-6">
            <div class="neo-card-body">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="neo-label">Kategori</label>
                        <select name="feature_category_id" class="neo-input">
                            <option value="">Tüm Kategoriler</option>
                            @foreach($kategoriler as $kategori)
                                <option value="{{ $kategori->id }}" {{ request('feature_category_id') == $kategori->id ? 'selected' : '' }}>
                                    {{ $kategori->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="neo-label">Durum</label>
                        <select name="status" class="neo-input">
                            <option value="">Tümü</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Pasif</option>
                        </select>
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="neo-btn neo-btn-primary w-full">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            Filtrele
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Özellik Tablosu -->
        <div class="neo-card">
            @if ($ozellikler->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Özellik</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Kategori</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tip</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Sıra</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Durum</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($ozellikler as $ozellik)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                    <!-- Özellik Adı -->
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $ozellik->name }}
                                        </div>
                                    </td>

                                    <!-- Kategori -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($ozellik->featureCategory)
                                            <span class="px-2 py-1 text-xs rounded-full bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300">
                                                {{ $ozellik->featureCategory->name }}
                                            </span>
                                        @else
                                            <span class="text-sm text-gray-400">Kategorisiz</span>
                                        @endif
                                    </td>

                                    <!-- Tip -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $ozellik->type }}
                                    </td>

                                    <!-- Sıra -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                        {{ $ozellik->order }}
                                    </td>

                                    <!-- Durum -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $ozellik->status ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' }}">
                                            {{ $ozellik->status ? 'Aktif' : 'Pasif' }}
                                        </span>
                                    </td>

                                    <!-- İşlemler -->
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                        <div class="flex items-center justify-end space-x-2">
                                            <a href="{{ route('admin.ozellikler.edit', $ozellik->id) }}" 
                                               class="neo-btn neo-btn-sm neo-btn-secondary">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Düzenle
                                            </a>
                                            
                                            <form action="{{ route('admin.ozellikler.destroy', $ozellik->id) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('Bu özelliği silmek istediğinizden emin misiniz?');"
                                                  class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="neo-btn neo-btn-sm neo-btn-danger">
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
                    {{ $ozellikler->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Özellik bulunamadı</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Filtreleri değiştirin veya yeni bir özellik oluşturun.
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('admin.ozellikler.create') }}" class="neo-btn neo-btn-primary">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Yeni Özellik Oluştur
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
