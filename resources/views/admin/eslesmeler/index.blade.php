@extends('admin.layouts.neo')

@section('title', 'Eşleştirme Yönetimi')

@section('content')
    <!-- Context7 AI Önerileri Banner -->
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800 mb-6 p-6 bg-gradient-to-r from-green-50 to-blue-50 border border-green-200">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-blue-600 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-green-800">🤖 AI Eşleştirme Analizi</h3>
                    <p class="text-sm text-green-600">Context7 Intelligence ile akıllı eşleştirme ve öneriler</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">✓ Context7 Uyumlu</span>
                <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">AI Aktif</span>
            </div>
        </div>
    </div>

    <!-- Context7 Header -->
    <div class="content-header mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2 flex items-center">
            <div
                class="w-12 h-12 bg-gradient-to-r from-purple-500 to-violet-600 rounded-xl flex items-center justify-center mr-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1">
                    </path>
                </svg>
            </div>
            🔗 Eşleştirme Yönetimi
        </h1>
        <p class="text-lg text-gray-600 mt-2">Müşteri talepleri ile ilanları eşleştirin ve akıllı öneriler alın</p>
        <div class="flex items-center space-x-3 mt-4">
            <a href="{{ route('admin.eslesmeler.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg touch-target-optimized touch-target-optimized">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                    </path>
                </svg>
                Yeni Eşleştirme
            </a>
        </div>
    </div>

    <div class="px-6">
        <!-- 📊 İstatistikler -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-200 shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div
                            class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-sm font-medium text-blue-800">Toplam Eşleştirme</h4>
                        <p class="text-2xl font-bold text-blue-900">{{ $eslesmeler->total() ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border border-green-200 shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div
                            class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-600 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-sm font-medium text-green-800">Aktif Eşleştirme</h4>
                        <p class="text-2xl font-bold text-green-900">
                            {{ $eslesmeler->where('status', 'active')->count() ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-purple-50 to-violet-50 rounded-xl border border-purple-200 shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div
                            class="w-12 h-12 bg-gradient-to-r from-purple-500 to-violet-600 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-sm font-medium text-purple-800">Yüksek Skor</h4>
                        <p class="text-2xl font-bold text-purple-900">
                            {{ $eslesmeler->where('skor', '>=', 80)->count() ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-orange-50 to-amber-50 rounded-xl border border-orange-200 shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div
                            class="w-12 h-12 bg-gradient-to-r from-orange-500 to-amber-600 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-sm font-medium text-orange-800">Bekleyen</h4>
                        <p class="text-2xl font-bold text-orange-900">
                            {{ $eslesmeler->where('status', 'beklemede')->count() ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 🔍 Filtreler -->
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800 mb-8">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                    <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center w-6 h-6 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    🔍 Filtreler ve Arama
                </h2>

                <form method="GET" action="{{ route('admin.eslesmeler.index') }}" class="mt-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="form-field">
                            <label for="status" class="emlakpro-filter-label">📊 Durum</label>
                            <select style="color-scheme: light dark;" id="status" name="status" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                                <option value="">Tümü</option>
                                <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }}>Aktif
                                </option>
                                <option value="beklemede"
                                    {{ ($filters['status'] ?? '') === 'beklemede' ? 'selected' : '' }}>Beklemede</option>
                                <option value="tamamlandi"
                                    {{ ($filters['status'] ?? '') === 'tamamlandi' ? 'selected' : '' }}>Tamamlandı</option>
                                <option value="iptal" {{ ($filters['status'] ?? '') === 'iptal' ? 'selected' : '' }}>İptal
                                </option>
                            </select>
                        </div>

                        <div class="form-field">
                            <label for="oncelik" class="emlakpro-filter-label">⚡ Öncelik</label>
                            <select style="color-scheme: light dark;" id="oncelik" name="oncelik" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                                <option value="">Tümü</option>
                                <option value="yuksek" {{ ($filters['oncelik'] ?? '') === 'yuksek' ? 'selected' : '' }}>
                                    Yüksek</option>
                                <option value="orta" {{ ($filters['oncelik'] ?? '') === 'orta' ? 'selected' : '' }}>Orta
                                </option>
                                <option value="dusuk" {{ ($filters['oncelik'] ?? '') === 'dusuk' ? 'selected' : '' }}>
                                    Düşük</option>
                            </select>
                        </div>

                        <div class="form-field">
                            <label for="kategori_id" class="emlakpro-filter-label">🏠 Kategori</label>
                            <select style="color-scheme: light dark;" id="kategori_id" name="kategori_id" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                                <option value="">Tümü</option>
                                @foreach ($kategoriler ?? [] as $kategori)
                                    <option value="{{ $kategori->id }}"
                                        {{ ($filters['kategori_id'] ?? '') == $kategori->id ? 'selected' : '' }}>
                                        {{ $kategori->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-field">
                            <label for="sort" class="emlakpro-filter-label">📈 Sıralama</label>
                            <select style="color-scheme: light dark;" id="sort" name="sort" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                                <option value="created_desc"
                                    {{ ($filters['sort'] ?? '') === 'created_desc' ? 'selected' : '' }}>En Yeni</option>
                                <option value="skor_desc"
                                    {{ ($filters['sort'] ?? '') === 'skor_desc' ? 'selected' : '' }}>Yüksek Skor</option>
                                <option value="skor_asc" {{ ($filters['sort'] ?? '') === 'skor_asc' ? 'selected' : '' }}>
                                    Düşük Skor</option>
                                <option value="oncelik_desc"
                                    {{ ($filters['sort'] ?? '') === 'oncelik_desc' ? 'selected' : '' }}>Yüksek Öncelik
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4 mt-4">
                        <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg touch-target-optimized touch-target-optimized">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Filtrele
                        </button>
                        <a href="{{ route('admin.eslesmeler.index') }}"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-300 bg-white text-gray-700 rounded-lg hover:bg-gray-50 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-500 transition-all duration-200 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700 touch-target-optimized touch-target-optimized">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Temizle
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- 📋 Eşleştirme Listesi -->
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center">
                        <svg class="w-6 h-6 mr-3 text-gray-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        📋 Eşleştirme Listesi
                    </h2>
                    <div class="flex items-center space-x-3">
                        <span class="text-sm text-gray-500">{{ $eslesmeler->total() ?? 0 }} eşleştirme bulundu</span>
                    </div>
                </div>
            </div>

            <div class="p-6">
                @if (isset($eslesmeler) && $eslesmeler->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="admin-table-th">
                                        Müşteri</th>
                                    <th
                                        class="admin-table-th">
                                        Talep</th>
                                    <th
                                        class="admin-table-th">
                                        İlan</th>
                                    <th
                                        class="admin-table-th">
                                        Eşleşme Skoru</th>
                                    <th
                                        class="admin-table-th">
                                        Durum</th>
                                    <th
                                        class="admin-table-th">
                                        Öncelik</th>
                                    <th
                                        class="admin-table-th">
                                        Tarih</th>
                                    <th class="admin-table-th"
                                        width="150">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($eslesmeler as $eslesme)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div
                                                        class="h-10 w-10 rounded-full bg-gradient-to-r from-purple-500 to-pink-600 flex items-center justify-center text-white font-semibold">
                                                        {{ strtoupper(substr($eslesme->kisi->ad ?? 'M', 0, 1)) }}{{ strtoupper(substr($eslesme->kisi->soyad ?? 'S', 0, 1)) }}
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $eslesme->kisi->ad ?? 'N/A' }}
                                                        {{ $eslesme->kisi->soyad ?? '' }}</div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $eslesme->kisi->telefon ?? 'Telefon yok' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">#{{ $eslesme->talep->id ?? 'N/A' }}</div>
                                            <div class="text-sm text-gray-500">
                                                {{ $eslesme->talep->kategori->name ?? 'Kategori yok' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">#{{ $eslesme->ilan->id ?? 'N/A' }}</div>
                                            <div class="text-sm text-gray-500">
                                                {{ $eslesme->ilan->baslik ?? 'Başlık yok' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-1 bg-gray-200 rounded-full h-2 mr-2">
                                                    <div class="bg-gradient-to-r from-green-400 to-green-600 h-2 rounded-full"
                                                        style="width: {{ $eslesme->skor ?? 0 }}%"></div>
                                                </div>
                                                <span
                                                    class="text-sm font-medium text-gray-900">{{ $eslesme->skor ?? 0 }}%</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <x-neo.status-badge :value="ucfirst($eslesme->status ?? 'Bilinmiyor')" />
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <x-neo.status-badge :value="ucfirst($eslesme->oncelik ?? 'Düşük')" category="priority" />
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $eslesme->created_at ? $eslesme->created_at->format('d.m.Y H:i') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-2">
                                                <a href="#"
                                                    class="text-blue-600 hover:text-blue-900 transition-colors"
                                                    title="Görüntüle">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </a>
                                                <a href="#"
                                                    class="text-yellow-600 hover:text-yellow-900 transition-colors"
                                                    title="Düzenle">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Sayfalama -->
                    <div class="mt-6">
                        {{ $eslesmeler->appends(request()->query())->links() }}
                    </div>
                @else
                    <x-neo.empty-state title="Henüz eşleştirme bulunmuyor" description="İlk eşleştirmenizi oluşturarak başlayın."
                        :actionHref="route('admin.eslesmeler.create')" actionText="İlk Eşleştirmeyi Oluştur" />
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Eşleştirme filtreleme ve arama işlemleri
        document.addEventListener('DOMContentLoaded', function() {
            // Otomatik filtreleme
            const filterSelects = document.querySelectorAll(
                'select[name="status"], select[name="oncelik"], select[name="kategori_id"], select[name="sort"]');
            filterSelects.forEach(select => {
                select.addEventListener('change', function() {
                    this.form.submit();
                });
            });
        });
    </script>
@endpush
