@extends('admin.layouts.admin')

@section('title', 'Sezon Yönetimi')

@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">
    <style>
        .sezon-card {
            transition: all 0.3s ease;
        }

        .sezon-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .sezon-yuksek {
            border-left: 5px solid #ef4444;
        }

        .sezon-orta {
            border-left: 5px solid #f59e0b;
        }

        .sezon-dusuk {
            border-left: 5px solid #22c55e;
        }

        .modal-backdrop {
            background-color: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(5px);
        }

        .sezon-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .sezon-badge.yuksek {
            background-color: #fef2f2;
            color: #dc2626;
        }

        .sezon-badge.orta {
            background-color: #fffbeb;
            color: #d97706;
        }

        .sezon-badge.dusuk {
            background-color: #f0fdf4;
            color: #16a34a;
        }
    </style>
@endsection

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                        🗓️ Sezon Yönetimi
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">
                        Yazlık kiralama sezonlarını ve fiyatlandırma kurallarını yönetin
                    </p>
                </div>

                <button onclick="yeniSezonModal()" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg touch-target-optimized touch-target-optimized">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Yeni Sezon Ekle
                </button>
            </div>

            <!-- Sezon Kartları -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($sezonlar as $sezon)
                    <div
                        class="bg-gray-50 dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden sezon-card sezon-{{ $sezon['sezon_tipi'] ?? 'orta' }}">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                                        {{ $sezon['adi'] ?? $sezon['sezon_adi'] ?? 'Sezon' }}
                                    </h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                        {{ \Carbon\Carbon::parse($sezon['baslangic'] ?? $sezon['baslangic_tarihi'] ?? now())->format('d.m.Y') }} -
                                        {{ \Carbon\Carbon::parse($sezon['bitis'] ?? $sezon['bitis_tarihi'] ?? now())->format('d.m.Y') }}
                                    </p>
                                </div>

                                <span class="sezon-badge {{ $sezon['sezon_tipi'] ?? 'orta' }}">
                                    {{ ucfirst($sezon['sezon_tipi'] ?? 'Orta') }} Sezon
                                </span>
                            </div>

                            <!-- Sezon Detayları -->
                            <div class="space-y-3 mb-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Günlük Fiyat:</span>
                                    <span class="font-semibold text-gray-800 dark:text-gray-200">
                                        {{ number_format($sezon['gunluk_fiyat'] ?? 0) }} ₺
                                    </span>
                                </div>

                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Min. Konaklama:</span>
                                    <span class="font-semibold text-gray-800 dark:text-gray-200">
                                        {{ $sezon['minimum_konaklama_gun'] ?? 3 }} gece
                                    </span>
                                </div>

                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Max. Konaklama:</span>
                                    <span class="font-semibold text-gray-800 dark:text-gray-200">
                                        {{ $sezon['maksimum_konaklama_gun'] ?? 'Sınırsız' }} gece
                                    </span>
                                </div>
                            </div>

                            <!-- Durum ve İşlemler -->
                            <div
                                class="flex justify-between items-center pt-4 border-t border-gray-200 dark:border-gray-700">
                                <span class="text-sm {{ ($sezon['enabled'] ?? true) ? 'text-green-600' : 'text-red-600' }}">
                                    <i class="fas {{ ($sezon['enabled'] ?? true) ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i>
                                    {{ ($sezon['enabled'] ?? true) ? 'Aktif' : 'Pasif' }}
                                </span>

                                <div class="flex space-x-2">
                                    <button onclick="sezonDuzenle({{ $sezon['id'] }})"
                                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-300 bg-white text-gray-700 rounded-lg hover:bg-gray-50 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-500 transition-all duration-200 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700 text-sm px-3 py-1 touch-target-optimized touch-target-optimized">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button onclick="sezonSil({{ $sezon['id'] }}, '{{ $sezon['adi'] ?? 'Sezon' }}')"
                                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2-danger text-sm px-3 py-1 touch-target-optimized touch-target-optimized">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <div class="text-gray-400 dark:text-gray-500">
                            <i class="fas fa-calendar-times text-6xl mb-4"></i>
                            <h3 class="text-xl font-medium text-gray-600 dark:text-gray-400 mb-2">
                                Henüz sezon tanımlanmamış
                            </h3>
                            <p class="text-gray-500 dark:text-gray-600">
                                İlk sezonu ekleyerek başlayın
                            </p>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Sezon İstatistikleri -->
            @if ($sezonlar->count() > 0)
                <div class="mt-8 grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg shadow-lg p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <i class="fas fa-calendar text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Toplam Sezon</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $sezonlar->count() }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg shadow-lg p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <i class="fas fa-check-circle text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Aktif Sezon</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                                    {{ $sezonlar->where('status', true)->count() }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg shadow-lg p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                                <i class="fas fa-clock text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Güncel Sezon</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-white" id="guncelSezon">-</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg shadow-lg p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                                <i class="fas fa-percentage text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Ort. Çarpan</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-white" id="ortalamaCarpan">-</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Yeni Sezon Modal -->
    <div id="yeniSezonModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen">
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg shadow-xl max-w-lg w-full mx-4">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        Yeni Sezon Ekle
                    </h3>

                    <form id="sezonForm" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Sezon Adı <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="sezonAdi" name="sezon_adi" class="admin-input w-full"
                                placeholder="Örn: Yaz Sezonu 2025" required>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                    Başlangıç Tarihi <span class="text-red-500">*</span>
                                </label>
                                <input type="date" id="sezonBaslangic" name="baslangic_tarihi"
                                    class="admin-input w-full" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                    Bitiş Tarihi <span class="text-red-500">*</span>
                                </label>
                                <input type="date" id="sezonBitis" name="bitis_tarihi" class="admin-input w-full"
                                    required>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Sezon Tipi <span class="text-red-500">*</span>
                            </label>
                            <select style="color-scheme: light dark;" id="sezonTipi" name="sezon_tipi" class="admin-input w-full transition-all duration-200" required>
                                <option value="">Seçiniz...</option>
                                <option value="yuksek">Yüksek Sezon</option>
                                <option value="orta">Orta Sezon</option>
                                <option value="dusuk">Düşük Sezon</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                    Fiyat Çarpanı <span class="text-red-500">*</span>
                                </label>
                                <input type="number" id="sezonCarpan" name="fiyat_carpani" class="admin-input w-full"
                                    min="0.1" max="10" step="0.1" value="1.0" required>
                                <p class="text-xs text-gray-500 mt-1">1.0 = Normal fiyat, 1.5 = %50 artış</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                    Minimum Konaklama (Gece)
                                </label>
                                <input type="number" id="sezonMinKonaklama" name="minimum_konaklama_gun"
                                    class="admin-input w-full" min="1" max="30" value="1">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Maksimum Konaklama (Gece)
                            </label>
                            <input type="number" id="sezonMaxKonaklama" name="maksimum_konaklama_gun"
                                class="admin-input w-full" min="1" max="365" value="30">
                            <p class="text-xs text-gray-500 mt-1">0 = Sınırsız</p>
                        </div>

                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" id="sezonAktif" name="status" class="form-checkbox" checked>
                                <span class="ml-2 text-sm text-gray-900 dark:text-white">
                                    Sezon status olsun
                                </span>
                            </label>
                        </div>
                    </form>

                    <div class="flex justify-end space-x-3 mt-6">
                        <button onclick="yeniSezonModalKapat()" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-300 bg-white text-gray-700 rounded-lg hover:bg-gray-50 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-500 transition-all duration-200 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700 touch-target-optimized touch-target-optimized">
                            İptal
                        </button>
                        <button onclick="sezonKaydet()" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg touch-target-optimized touch-target-optimized">
                            Kaydet
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sezon Düzenleme Modal -->
    <div id="sezonDuzenleModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen">
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg shadow-xl max-w-lg w-full mx-4">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        Sezon Düzenle
                    </h3>

                    <form id="sezonDuzenleForm" class="space-y-4">
                        <input type="hidden" id="duzenleSezonId" name="sezon_id">

                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Sezon Adı <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="duzenleSezonAdi" name="sezon_adi" class="admin-input w-full"
                                required>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                    Başlangıç Tarihi <span class="text-red-500">*</span>
                                </label>
                                <input type="date" id="duzenleSezonBaslangic" name="baslangic_tarihi"
                                    class="admin-input w-full" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                    Bitiş Tarihi <span class="text-red-500">*</span>
                                </label>
                                <input type="date" id="duzenleSezonBitis" name="bitis_tarihi"
                                    class="admin-input w-full" required>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Sezon Tipi <span class="text-red-500">*</span>
                            </label>
                            <select style="color-scheme: light dark;" id="duzenleSezonTipi" name="sezon_tipi" class="admin-input w-full transition-all duration-200" required>
                                <option value="yuksek">Yüksek Sezon</option>
                                <option value="orta">Orta Sezon</option>
                                <option value="dusuk">Düşük Sezon</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                    Fiyat Çarpanı <span class="text-red-500">*</span>
                                </label>
                                <input type="number" id="duzenleSezonCarpan" name="fiyat_carpani"
                                    class="admin-input w-full" min="0.1" max="10" step="0.1" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                    Minimum Konaklama (Gece)
                                </label>
                                <input type="number" id="duzenleSezonMinKonaklama" name="minimum_konaklama_gun"
                                    class="admin-input w-full" min="1" max="30">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Maksimum Konaklama (Gece)
                            </label>
                            <input type="number" id="duzenleSezonMaxKonaklama" name="maksimum_konaklama_gun"
                                class="admin-input w-full" min="1" max="365">
                        </div>

                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" id="duzenleSezonAktif" name="status" class="form-checkbox">
                                <span class="ml-2 text-sm text-gray-900 dark:text-white">
                                    Sezon status olsun
                                </span>
                            </label>
                        </div>
                    </form>

                    <div class="flex justify-end space-x-3 mt-6">
                        <button onclick="sezonDuzenleModalKapat()" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-300 bg-white text-gray-700 rounded-lg hover:bg-gray-50 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-500 transition-all duration-200 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700 touch-target-optimized touch-target-optimized">
                            İptal
                        </button>
                        <button onclick="sezonGuncelle()" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg touch-target-optimized touch-target-optimized">
                            Güncelle
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/tr.js"></script>
    <script>
        // Sezon verilerini yükle
        let sezonlar = @json($sezonlar);

        // Modal fonksiyonları
        function yeniSezonModal() {
            document.getElementById('yeniSezonModal').classList.remove('hidden');
            document.getElementById('sezonForm').reset();
        }

        function yeniSezonModalKapat() {
            document.getElementById('yeniSezonModal').classList.add('hidden');
        }

        function sezonDuzenle(sezonId) {
            const sezon = sezonlar.find(s => s.id === sezonId);
            if (!sezon) return;

            // Form alanlarını doldur
            document.getElementById('duzenleSezonId').value = sezon.id;
            document.getElementById('duzenleSezonAdi').value = sezon.sezon_adi;
            document.getElementById('duzenleSezonBaslangic').value = sezon.baslangic_tarihi;
            document.getElementById('duzenleSezonBitis').value = sezon.bitis_tarihi;
            document.getElementById('duzenleSezonTipi').value = sezon.sezon_tipi;
            document.getElementById('duzenleSezonCarpan').value = sezon.fiyat_carpani;
            document.getElementById('duzenleSezonMinKonaklama').value = sezon.minimum_konaklama_gun;
            document.getElementById('duzenleSezonMaxKonaklama').value = sezon.maksimum_konaklama_gun;
            document.getElementById('duzenleSezonAktif').checked = sezon.status;

            document.getElementById('sezonDuzenleModal').classList.remove('hidden');
        }

        function sezonDuzenleModalKapat() {
            document.getElementById('sezonDuzenleModal').classList.add('hidden');
        }

        // Sezon kaydetme
        async function sezonKaydet() {
            const form = document.getElementById('sezonForm');
            const formData = new FormData(form);

            try {
                const response = await fetch('/admin/takvim/sezonlar', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    yeniSezonModalKapat();
                    location.reload();
                } else {
                    alert('Hata: ' + result.message);
                }
            } catch (error) {
                console.error('Sezon kaydetme hatası:', error);
                alert('Sezon kaydedilirken hata oluştu');
            }
        }

        // Sezon güncelleme
        async function sezonGuncelle() {
            const form = document.getElementById('sezonDuzenleForm');
            const formData = new FormData(form);
            const sezonId = document.getElementById('duzenleSezonId').value;

            try {
                const response = await fetch(`/admin/takvim/sezonlar/${sezonId}`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    sezonDuzenleModalKapat();
                    location.reload();
                } else {
                    alert('Hata: ' + result.message);
                }
            } catch (error) {
                console.error('Sezon güncelleme hatası:', error);
                alert('Sezon güncellenirken hata oluştu');
            }
        }

        // Sezon silme
        async function sezonSil(sezonId, sezonAdi) {
            if (!confirm(`"${sezonAdi}" sezonunu silmek istediğinizden emin misiniz?`)) {
                return;
            }

            try {
                const response = await fetch(`/admin/takvim/sezonlar/${sezonId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                });

                const result = await response.json();

                if (result.success) {
                    location.reload();
                } else {
                    alert('Hata: ' + result.message);
                }
            } catch (error) {
                console.error('Sezon silme hatası:', error);
                alert('Sezon silinirken hata oluştu');
            }
        }

        // İstatistikleri güncelle
        function updateStatistics() {
            if (sezonlar.length === 0) return;

            // Güncel sezon
            const today = new Date();
            const currentSeason = sezonlar.find(sezon => {
                const start = new Date(sezon.baslangic_tarihi);
                const end = new Date(sezon.bitis_tarihi);
                return today >= start && today <= end && sezon.status;
            });

            document.getElementById('guncelSezon').textContent =
                currentSeason ? currentSeason.sezon_adi : 'Yok';

            // Ortalama çarpan
            const activeSeasons = sezonlar.filter(s => s.active);
            const avgMultiplier = activeSeasons.length > 0 ?
                (activeSeasons.reduce((sum, s) => sum + parseFloat(s.fiyat_carpani), 0) / activeSeasons.length).toFixed(2) :
                '0.00';

            document.getElementById('ortalamaCarpan').textContent = avgMultiplier;
        }

        // Sayfa yüklendiğinde istatistikleri güncelle
        document.addEventListener('DOMContentLoaded', function() {
            updateStatistics();
        });
    </script>
@endsection
