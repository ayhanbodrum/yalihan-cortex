@extends('admin.layouts.neo')

@section('title', 'İlan Kategorileri')
@section('meta_description',
    'İlan kategorilerini yönetin - Ana ve alt kategoriler, durum güncellemeleri, toplu işlemler
    ve kategori istatistikleri.')
@section('meta_keywords',
    'ilan kategorileri, kategori yönetimi, emlak kategorileri, ana kategori, alt kategori, yalıhan
    emlak')

@section('content')
    <div class="container mx-auto px-4 py-6" x-data="kategorilerManager()">
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">İlan Kategorileri</h1>
                    <p class="text-gray-600 dark:text-gray-400">Kategori yönetimi ve düzenleme</p>
                </div>
                <div>
                    <a href="{{ route('admin.ilan-kategorileri.create') }}" class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Yeni Kategori
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800/30 rounded-lg p-3">
                <div class="text-xs text-blue-600 dark:text-blue-400 mb-1">Toplam</div>
                <div class="text-xl font-bold text-blue-700 dark:text-blue-300">{{ $istatistikler['toplam'] }}</div>
            </div>
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800/30 rounded-lg p-3">
                <div class="text-xs text-green-600 dark:text-green-400 mb-1">Ana</div>
                <div class="text-xl font-bold text-green-700 dark:text-green-300">{{ $istatistikler['ana_kategoriler'] }}</div>
            </div>
            <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/30 rounded-lg p-3">
                <div class="text-xs text-amber-600 dark:text-amber-400 mb-1">Alt</div>
                <div class="text-xl font-bold text-amber-700 dark:text-amber-300">{{ $istatistikler['alt_kategoriler'] }}</div>
            </div>
            <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800/30 rounded-lg p-3">
                <div class="text-xs text-purple-600 dark:text-purple-400 mb-1">Active</div>
                <div class="text-xl font-bold text-purple-700 dark:text-purple-300">{{ $istatistikler['aktif'] }}</div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Kategori Listesi</h2>
                <div>
                    <a href="{{ route('admin.ilan-kategorileri.export') }}" class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Dışa Aktar
                    </a>
                </div>
            </div>

            <div class="p-6">

                <!-- Toplu İşlemler Toolbar -->
                <div x-show="selectedItems.length > 0" x-transition class="mb-4 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800/30 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div class="text-sm font-medium text-blue-900 dark:text-blue-100">
                            <span x-text="selectedItems.length"></span> öğe seçildi
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="bulkAction('activate')" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-gradient-to-r from-green-600 to-emerald-600 rounded-lg hover:from-green-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed"
                                :disabled="processing" aria-label="Seçili kategorileri etkinleştir">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Etkinleştir
                            </button>
                            <button type="button" @click="bulkAction('deactivate')" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-gradient-to-r from-amber-600 to-orange-600 rounded-lg hover:from-amber-700 hover:to-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-all duration-200 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed"
                                :disabled="processing" aria-label="Seçili kategorileri pasifleştir">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Pasifleştir
                            </button>
                            <button type="button" @click="bulkAction('delete')" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-gradient-to-r from-red-600 to-pink-600 rounded-lg hover:from-red-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed"
                                :disabled="processing" aria-label="Seçili kategorileri sil">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Sil
                            </button>
                            <button type="button" @click="clearSelection()" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-sm"
                                aria-label="Seçimi temizle">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                Temizle
                            </button>
                        </div>
                    </div>
                </div>

                <form method="GET" class="flex flex-wrap items-end gap-2 mb-4" @submit.prevent="submitFilters()">
                    <div class="flex-1 min-w-[200px] relative">
                        <input type="text" id="search" name="search" value="{{ request('search') }}"
                            placeholder="Kategori ara..." class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 hover:border-blue-400"
                            x-model="filters.search" autocomplete="off">
                        <ul id="autocompleteList"
                            class="absolute z-10 left-0 right-0 mt-1 bg-gray-50 dark:bg-gray-800 border rounded-lg shadow-lg max-h-48 overflow-auto hidden">
                        </ul>
                    </div>
                    <select style="color-scheme: light dark;" id="seviye" name="seviye" class="px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-all duration-200 cursor-pointer hover:border-blue-400" x-model="filters.seviye" @change="applyFilters()">
                        <option value="">Tüm Seviyeler</option>
                        <option value="ana">Ana Kategori</option>
                        <option value="alt">Alt Kategori</option>
                    </select>
                    <select style="color-scheme: light dark;" id="status" name="status" class="px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-all duration-200 cursor-pointer hover:border-blue-400" x-model="filters.status" @change="applyFilters()">
                        <option value="">Tüm Durumlar</option>
                        <option value="1">Aktif</option>
                        <option value="0">Pasif</option>
                    </select>
                    <button type="button" @click="clearFilters()" class="px-3 py-1.5 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg" :disabled="loading">
                        Temizle
                    </button>
                </form>
                @push('scripts')
                    <script>
                        window._kategoriAdlari = [@foreach ($kategoriler as $kategori)@if ($kategori->name)"{{ addslashes($kategori->name) }}",@endif @endforeach];
                        function showAutocomplete() {
                            const input = document.getElementById('search');
                            const list = document.getElementById('autocompleteList');
                            if (!input || !list) return;
                            const val = input.value.toLowerCase();
                            const matches = window._kategoriAdlari.filter(name => name.toLowerCase().includes(val)).slice(0, 10);
                            if (matches.length === 0 || val.length === 0) {
                                list.innerHTML = '';
                                list.classList.add('hidden');
                                return;
                            }
                            list.innerHTML = matches.map(name =>
                                `<li class='px-4 py-2.5 text-sm cursor-pointer hover:bg-blue-50' onclick='selectAutocomplete("${name.replace(/"/g, "&quot;")}")'>${name}</li>`
                            ).join('');
                            list.classList.remove('hidden');
                        }
                        function hideAutocomplete() {
                            const list = document.getElementById('autocompleteList');
                            if (list) list.classList.add('hidden');
                        }
                        function selectAutocomplete(name) {
                            const input = document.getElementById('search');
                            input.value = name;
                            input.dispatchEvent(new Event('input'));
                            hideAutocomplete();
                        }
                        document.getElementById('search')?.addEventListener('focus', showAutocomplete);
                        document.getElementById('search')?.addEventListener('blur', () => setTimeout(hideAutocomplete, 150));
                    </script>
                @endpush

                @if ($kategoriler->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                        <input type="checkbox" id="select-all" class="rounded" @change="toggleSelectAll()">
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Kategori</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Üst Kategori</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($kategoriler as $kategori)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
                                        :class="{ 'bg-blue-50': selectedItems.includes({{ $kategori->id }}) }">
                                        <td class="px-6 py-4">
                                            <input type="checkbox" id="kategori-{{ $kategori->id }}" value="{{ $kategori->id }}" class="rounded" @change="toggleItemSelection({{ $kategori->id }})">
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $kategori->name }}</span>
                                                <span class="px-2 py-1 text-xs rounded-full {{ $kategori->seviye == 0 ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-400' }}">
                                                    {{ $kategori->seviye == 0 ? 'Ana' : 'Alt' }}
                                                </span>
                                                @if($kategori->ilanlar_count > 0)
                                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                                        {{ $kategori->ilanlar_count }}
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $kategori->parent?->name ?? '—' }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $kategori->status ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' }}">
                                                {{ $kategori->status ? 'Aktif' : 'Pasif' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                            <div class="flex items-center justify-end space-x-2">
                                                <a href="{{ route('admin.ilan-kategorileri.edit', $kategori) }}"
                                                   class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-sm">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                    Düzenle
                                                </a>

                                                <form action="{{ route('admin.ilan-kategorileri.destroy', $kategori) }}"
                                                      method="POST"
                                                      onsubmit="return confirm('{{ addslashes($kategori->name) }} kategorisini silmek istediğinize emin misiniz?');"
                                                      class="inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-gradient-to-r from-red-600 to-pink-600 rounded-lg hover:from-red-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed" :disabled="processing">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $kategoriler->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Kategori bulunamadı</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Filtreleri değiştirin veya yeni bir kategori oluşturun.
                        </p>
                        <div class="mt-6">
                            <a href="{{ route('admin.ilan-kategorileri.create') }}" class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg">
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

    <script>
        function kategorilerManager() {
            return {
                contentLoaded: false,
                loading: false,
                processing: false,
                selectedItems: [],
                showAIAnalysis: false,
                analysisType: 'optimization',
                propertyType: 'all',
                aiResults: [],
                filters: {
                    search: '{{ request('search') }}',
                    seviye: '{{ request('seviye') }}',
                    status: '{{ request('status') }}'
                },

                get isAllSelected() {
                    const totalItems = {{ $kategoriler->count() }};
                    return totalItems > 0 && this.selectedItems.length === totalItems;
                },

                get isPartiallySelected() {
                    return this.selectedItems.length > 0 && !this.isAllSelected;
                },

                init() {
                    // Initialize with any existing filters
                    this.applyInitialFilters();
                },

                toggleSelectAll() {
                    if (this.isAllSelected) {
                        this.selectedItems = [];
                    } else {
                        this.selectedItems = [
                            @foreach ($kategoriler as $kategori)
                                {{ $kategori->id }},
                            @endforeach
                        ];
                    }
                },

                toggleItemSelection(itemId) {
                    const index = this.selectedItems.indexOf(itemId);
                    if (index > -1) {
                        this.selectedItems.splice(index, 1);
                    } else {
                        this.selectedItems.push(itemId);
                    }
                },

                clearSelection() {
                    this.selectedItems = [];
                },

                async applyFilters() {
                    this.loading = true;

                    const params = new URLSearchParams();
                    if (this.filters.search) params.append('search', this.filters.search);
                    if (this.filters.seviye) params.append('seviye', this.filters.seviye);
                    if (this.filters.status !== '') params.append('status', this.filters.status);

                    const url = `{{ route('admin.ilan-kategorileri.index') }}?${params.toString()}`;

                    try {
                        window.location.href = url;
                    } catch (error) {
                        console.error('Filter uygulanırken hata:', error);
                        this.showError('Filtreler uygulanırken hata oluştu');
                    } finally {
                        this.loading = false;
                    }
                },

                clearFilters() {
                    this.filters = {
                        search: '',
                        seviye: '',
                        status: ''
                    };
                    this.applyFilters();
                },

                submitFilters() {
                    this.applyFilters();
                },

                applyInitialFilters() {
                    // Any initial filter setup if needed
                },

                async bulkAction(action) {
                    if (this.selectedItems.length === 0) {
                        this.showError('Lütfen en az bir kategori seçin');
                        return;
                    }
                    const actionMessages = {
                        activate: 'etkinleştirmek',
                        deactivate: 'pasifleştirmek',
                        delete: 'silmek'
                    };
                    if (!confirm(
                            `Seçili ${this.selectedItems.length} kategoriyi ${actionMessages[action]} istediğinizden emin misiniz?`
                        )) {
                        return;
                    }
                    this.processing = true;
                    try {
                        const formData = new FormData();
                        formData.append('_token', '{{ csrf_token() }}');
                        formData.append('action', action);
                        this.selectedItems.forEach(id => {
                            formData.append('kategori_ids[]', id);
                        });
                        const response = await fetch('{{ route('admin.ilan-kategorileri.bulk-action') }}', {
                            method: 'POST',
                            body: formData
                        });
                        if (response.ok) {
                            // Backend'den güncellenen ID'ler dönerse satırları vurgula
                            let updatedIds = [];
                            try {
                                const json = await response.json();
                                if (json && json.updated_ids) updatedIds = json.updated_ids;
                            } catch {}
                            if (updatedIds.length > 0) {
                                updatedIds.forEach(id => {
                                    const row = document.querySelector(`tr[data-kategori-id='${id}']`);
                                    if (row) {
                                        row.classList.add('bg-green-50', 'dark:bg-green-900/20');
                                        setTimeout(() => row.classList.remove('bg-green-50',
                                            'dark:bg-green-900/20'), 2000);
                                    }
                                });
                            }
                            window.toast.success('Toplu işlem başarıyla tamamlandı');
                            this.selectedItems = [];
                        } else {
                            throw new Error('İşlem başarısız');
                        }
                    } catch (error) {
                        console.error('Toplu işlem hatası:', error);
                        window.toast.error('Toplu işlem sırasında hata oluştu');
                    } finally {
                        this.processing = false;
                    }
                },

                async deleteKategori(id, name) {
                    if (!confirm(`"${name}" kategorisini silmek istediğinizden emin misiniz?`)) {
                        return;
                    }

                    this.processing = true;

                    try {
                        const formData = new FormData();
                        formData.append('_token', '{{ csrf_token() }}');
                        formData.append('_method', 'DELETE');

                        const response = await fetch(`/admin/ilan-kategorileri/${id}`, {
                            method: 'POST',
                            body: formData
                        });

                        if (response.ok) {
                            window.toast.success('Kategori başarıyla silindi');
                            setTimeout(() => window.location.reload(), 1000);
                        } else {
                            throw new Error('Silme işlemi başarısız');
                        }
                    } catch (error) {
                        console.error('Silme hatası:', error);
                        window.toast.error('Kategori silinirken hata oluştu');
                    } finally {
                        this.processing = false;
                    }
                }
            }
        }
    </script>
@endsection
