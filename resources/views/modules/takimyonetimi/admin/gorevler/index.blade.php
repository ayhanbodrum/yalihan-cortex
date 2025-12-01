@extends('admin.layouts.admin')

@section('title', 'Görev Yönetimi')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Görev Yönetimi</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Takım görevlerini yönetin ve takip edin</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.takim-yonetimi.gorevler.create') }}"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2-primary inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2-md inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Yeni Görev
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div
            class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800 rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800-elevated p-6 mb-6 bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Durum</label>
                    <select class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 w-full">
                        <option value="">Tümü</option>
                        <option value="beklemede">Beklemede</option>
                        <option value="devam_ediyor">Devam Ediyor</option>
                        <option value="tamamlandi">Tamamlandı</option>
                        <option value="iptal">İptal</option>
                        <option value="askida">Askıda</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Öncelik</label>
                    <select class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 w-full">
                        <option value="">Tümü</option>
                        <option value="dusuk">Düşük</option>
                        <option value="orta">Orta</option>
                        <option value="yuksek">Yüksek</option>
                        <option value="kritik">Kritik</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Atanan
                        Kişi</label>
                    <select class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 w-full">
                        <option value="">Tümü</option>
                        <!-- Users will be populated here -->
                    </select>
                </div>
                <div class="flex items-end">
                    <button class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2-secondary inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2-md w-full">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                            </path>
                        </svg>
                        Filtrele
                    </button>
                </div>
            </div>
        </div>

        <!-- Tasks List -->
        <div
            class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800 rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800-elevated bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700">
            <div class="p-6">
                @if ($gorevler->count() > 0)
                    <div class="space-y-4">
                        @foreach ($gorevler as $gorev)
                            <div
                                class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800 rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800-flat p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:shadow-md transition-shadow">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3">
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                                {{ $gorev->baslik }}
                                            </h3>
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium-{{ $gorev->status === 'tamamlandi' ? 'success' : ($gorev->status === 'devam_ediyor' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst(str_replace('_', ' ', $gorev->status)) }}
                                            </span>
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium-{{ $gorev->oncelik === 'kritik' ? 'danger' : ($gorev->oncelik === 'yuksek' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst($gorev->oncelik) }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                            {{ Str::limit($gorev->aciklama, 100) }}
                                        </p>
                                        <div
                                            class="flex items-center space-x-4 mt-2 text-xs text-gray-500 dark:text-gray-400">
                                            <span>👤 {{ $gorev->admin->name ?? 'Atanmamış' }}</span>
                                            <span>📅
                                                {{ $gorev->created_at ? \Carbon\Carbon::parse($gorev->created_at)->format('d.m.Y') : 'Oluşturulma tarihi yok' }}</span>
                                            <span>⏰
                                                {{ $gorev->deadline ? \Carbon\Carbon::parse($gorev->deadline)->format('d.m.Y') : 'Deadline yok' }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('admin.takim-yonetimi.gorevler.show', $gorev) }}"
                                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2-sm inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2-secondary">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                </path>
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.takim-yonetimi.gorevler.edit', $gorev) }}"
                                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2-sm inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2-primary">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.takim-yonetimi.gorevler.destroy', $gorev) }}"
                                            method="POST" class="inline"
                                            onsubmit="return confirm('Bu görevi silmek istediğinizden emin misiniz?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2-sm inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2-danger">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $gorevler->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                            </path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Görev bulunamadı</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Henüz hiç görev oluşturulmamış.</p>
                        <div class="mt-6">
                            <a href="{{ route('admin.takim-yonetimi.gorevler.create') }}"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2-primary inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2-md inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                İlk Görevi Oluştur
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
