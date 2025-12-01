@extends('admin.layouts.admin')

@section('title', 'Kanban Board - Görev Yönetimi')

@section('content')
    <div class="p-6">
        <!-- Page Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Kanban Board</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">Görevleri statüsüne ve personele göre görselleştirin</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.takim.gorevler.index') }}"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200">
                        ← Görev Listesi
                    </a>
                </div>
            </div>
        </div>

        <!-- Personel Filtreleme ve Yeni Görev Butonu -->
        <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between gap-4 flex-wrap">
                <form method="GET" action="{{ route('admin.takim.gorevler.board') }}"
                    class="flex items-center gap-3 flex-1 min-w-[200px]">
                    <label for="user_id" class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">
                        Personel Filtresi:
                    </label>
                    <select name="user_id" id="user_id"
                        class="flex-1 max-w-xs px-4 py-2 text-sm border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        <option value="">Tüm Personel</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" {{ $selectedUserId == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 shadow-sm hover:shadow-md">
                        Filtrele
                    </button>
                </form>
                <a href="{{ route('admin.takim.gorevler.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 shadow-md hover:shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Yeni Görev Ekle
                </a>
            </div>
        </div>

        <!-- Kanban Board - 3 Sütunlu Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- 🔴 YAPILACAKLAR Sütunu -->
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="w-3 h-3 bg-red-500 rounded-full"></span>
                        Yapılacaklar
                    </h2>
                    <span
                        class="px-2 py-1 text-xs font-medium bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 rounded-full">
                        {{ $bekleyenler->count() }}
                    </span>
                </div>
                <div class="space-y-3 min-h-[400px]">
                    @forelse($bekleyenler as $gorev)
                        @include('admin.takim.gorevler.partials.gorev-card', ['gorev' => $gorev])
                    @empty
                        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            <p class="text-sm">Görev bulunmuyor</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- 🟡 İŞLEMDE Sütunu -->
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="w-3 h-3 bg-yellow-500 rounded-full"></span>
                        İşlemde
                    </h2>
                    <span
                        class="px-2 py-1 text-xs font-medium bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 rounded-full">
                        {{ $islemdekiler->count() }}
                    </span>
                </div>
                <div class="space-y-3 min-h-[400px]">
                    @forelse($islemdekiler as $gorev)
                        @include('admin.takim.gorevler.partials.gorev-card', ['gorev' => $gorev])
                    @empty
                        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            <p class="text-sm">Görev bulunmuyor</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- 🟢 TAMAMLANDI Sütunu -->
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                        Tamamlandı
                    </h2>
                    <span
                        class="px-2 py-1 text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-full">
                        {{ $tamamlananlar->count() }}
                    </span>
                </div>
                <div class="space-y-3 min-h-[400px]">
                    @forelse($tamamlananlar as $gorev)
                        @include('admin.takim.gorevler.partials.gorev-card', ['gorev' => $gorev])
                    @empty
                        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            <p class="text-sm">Görev bulunmuyor</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Status değiştirme fonksiyonu (Dropdown ile)
            function changeStatus(gorevId, newStatus) {
                if (!confirm('Görev durumunu değiştirmek istediğinize emin misiniz?')) {
                    return;
                }

                updateStatus(gorevId, newStatus);
            }

            // Durumu İlerlet fonksiyonu (Hızlı aksiyon butonu)
            function advanceStatus(gorevId, newStatus) {
                const statusLabels = {
                    'devam_ediyor': 'İşleme almak',
                    'tamamlandi': 'Tamamlamak'
                };

                if (!confirm(`Görevi ${statusLabels[newStatus] || 'güncellemek'} istediğinize emin misiniz?`)) {
                    return;
                }

                updateStatus(gorevId, newStatus);
            }

            // Ortak status güncelleme fonksiyonu
            function updateStatus(gorevId, newStatus) {
                fetch(`{{ url('/admin/takim-yonetimi/gorevler') }}/${gorevId}/status-guncelle`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            status: newStatus
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Context7: ResponseService format kontrolü
                        if (data.success) {
                            // Sayfayı yenile
                            window.location.reload();
                        } else {
                            alert(data.message || 'Durum güncellenirken bir hata oluştu.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Durum güncellenirken bir hata oluştu.');
                    });
            }
        </script>
    @endpush
@endsection
