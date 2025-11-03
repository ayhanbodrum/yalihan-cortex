@extends('admin.layouts.neo')

@section('title', 'İlan Kategorileri')
@section('meta_description',
    'İlan kategorilerini yönetin - Ana ve alt kategoriler, durum güncellemeleri, toplu işlemler
    ve kategori istatistikleri.')
@section('meta_keywords',
    'ilan kategorileri, kategori yönetimi, emlak kategorileri, ana kategori, alt kategori, yalıhan
    emlak')

@section('content')
    <div class="neo-container" x-data="kategorilerManager()">
        <div class="neo-header">
            <div class="neo-header-content">
                <h1 class="neo-title">İlan Kategorileri</h1>
                <p class="neo-subtitle">Kategori yönetimi ve düzenleme</p>
            </div>
            <div class="neo-header-actions">
                <a href="{{ route('admin.ilan-kategorileri.create') }}" class="neo-btn neo-btn-primary">
                    <i class="neo-icon neo-icon-plus"></i>
                    Yeni Kategori
                </a>
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

        <div class="neo-card">
            <div class="neo-card-header">
                <h2 class="neo-card-title">Kategori Listesi</h2>
                <div class="neo-card-actions">
                    <a href="{{ route('admin.ilan-kategorileri.export') }}" class="neo-btn neo-btn-secondary">
                        <i class="neo-icon neo-icon-download"></i>
                        Dışa Aktar
                    </a>
                </div>
            </div>

            <div class="neo-card-body">

                <!-- Toplu İşlemler Toolbar -->
                <div x-show="selectedItems.length > 0" x-transition class="neo-bulk-actions-toolbar">
                    <div class="neo-bulk-actions-info">
                        <span x-text="selectedItems.length"></span> öğe seçildi
                    </div>
                    <div class="neo-bulk-actions-buttons touch-target-optimized touch-target-optimized">
                        <button type="button" @click="bulkAction('activate')" class="neo-btn neo-btn-sm neo-btn-success touch-target-optimized touch-target-optimized"
                            :disabled="processing" aria-label="Seçili kategorileri etkinleştir">
                            <i class="neo-icon neo-icon-check"></i>
                            Etkinleştir
                        </button>
                        <button type="button" @click="bulkAction('deactivate')" class="neo-btn neo-btn-sm neo-btn-warning touch-target-optimized touch-target-optimized"
                            :disabled="processing" aria-label="Seçili kategorileri pasifleştir">
                            <i class="neo-icon neo-icon-pause"></i>
                            Pasifleştir
                        </button>
                        <button type="button" @click="bulkAction('delete')" class="neo-btn neo-btn-sm neo-btn-danger touch-target-optimized touch-target-optimized"
                            :disabled="processing" aria-label="Seçili kategorileri sil">
                            <i class="neo-icon neo-icon-trash"></i>
                            Sil
                        </button>
                        <button type="button" @click="clearSelection()" class="neo-btn neo-btn-sm neo-btn neo-btn-secondary touch-target-optimized touch-target-optimized"
                            aria-label="Seçimi temizle">
                            <i class="neo-icon neo-icon-x"></i>
                            Temizle
                        </button>
                    </div>
                </div>

                <form method="GET" class="flex flex-wrap items-end gap-2 mb-4" @submit.prevent="submitFilters()">
                    <div class="flex-1 min-w-[200px] relative">
                        <input type="text" id="search" name="search" value="{{ request('search') }}"
                            placeholder="Kategori ara..." class="w-full px-3 py-1.5 text-sm border rounded-lg"
                            x-model="filters.search" autocomplete="off">
                        <ul id="autocompleteList"
                            class="absolute z-10 left-0 right-0 mt-1 bg-white dark:bg-gray-800 border rounded-lg shadow-lg max-h-48 overflow-auto hidden">
                        </ul>
                    </div>
                    <select id="seviye" name="seviye" class="px-3 py-1.5 text-sm border rounded-lg" x-model="filters.seviye" @change="applyFilters()">
                        <option value="">Tüm Seviyeler</option>
                        <option value="1">Ana Kategori</option>
                        <option value="2">Alt Kategori</option>
                    </select>
                    <select id="status" name="status" class="px-3 py-1.5 text-sm border rounded-lg" x-model="filters.status" @change="applyFilters()">
                        <option value="">All Status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
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
                                `<li class='px-3 py-2 text-sm cursor-pointer hover:bg-blue-50' onclick='selectAutocomplete("${name.replace(/"/g, "&quot;")}")'>${name}</li>`
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
                                                <span class="px-2 py-1 text-xs rounded-full {{ $kategori->seviye == 0 ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
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
                                                {{ $kategori->status ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                            <div class="flex items-center justify-end space-x-2">
                                                <a href="{{ route('admin.ilan-kategorileri.edit', $kategori) }}"
                                                   class="neo-btn neo-btn-sm neo-btn-secondary">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                                    <button type="submit" class="neo-btn neo-btn-sm neo-btn-danger" :disabled="processing">
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
                            <a href="{{ route('admin.ilan-kategorileri.create') }}" class="neo-btn neo-btn-primary">
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
                            this.showSuccess(`Toplu işlem başarıyla tamamlandı`);
                            this.selectedItems = [];
                        } else {
                            throw new Error('İşlem başarısız');
                        }
                    } catch (error) {
                        console.error('Toplu işlem hatası:', error);
                        this.showError('Toplu işlem sırasında hata oluştu');
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
                            this.showSuccess('Kategori başarıyla silindi');
                            setTimeout(() => window.location.reload(), 1000);
                        } else {
                            throw new Error('Silme işlemi başarısız');
                        }
                    } catch (error) {
                        console.error('Silme hatası:', error);
                        this.showError('Kategori silinirken hata oluştu');
                    } finally {
                        this.processing = false;
                    }
                },

                showSuccess(message) {
                    let toast = document.createElement('div');
                    toast.className = 'neo-toast neo-toast-success fixed top-6 right-6 z-50';
                    toast.innerHTML = `<i class='neo-icon neo-icon-check-circle'></i> <span>${message}</span>`;
                    document.body.appendChild(toast);
                    setTimeout(() => toast.remove(), 2500);
                },

                showError(message) {
                    let toast = document.createElement('div');
                    toast.className = 'neo-toast neo-toast-error fixed top-6 right-6 z-50';
                    toast.innerHTML = `<i class='neo-icon neo-icon-alert-circle'></i> <span>${message}</span>`;
                    document.body.appendChild(toast);
                    setTimeout(() => toast.remove(), 3500);
                }
            }
        }
    </script>
@endsection
