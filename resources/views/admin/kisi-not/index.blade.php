@extends('admin.layouts.neo')

@section('title', 'Müşteri Notları - Yalıhan Emlak Pro')

@section('content')
    <div class="content-header mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold flex items-center text-gray-800">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-sticky-note text-white text-xl"></i>
                    </div>
                    Müşteri Notları 📝
                </h1>
                <p class="text-lg text-gray-600 mt-2">Kişi notlarını yönetin ve takip edin</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.kisi-not.create') }}" class="neo-btn neo-btn neo-btn-primary">
                    <i class="fas fa-plus mr-2"></i>
                    Yeni Not
                </a>
                <button onclick="exportNotes()" class="neo-btn neo-btn neo-btn-secondary">
                    <i class="fas fa-download mr-2"></i>
                    Dışa Aktar
                </button>
            </div>
        </div>
    </div>

    <div class="px-6">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="neo-card p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-sticky-note text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Toplam Not</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_notlar'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="neo-card p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Aktif Notlar</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['active_notlar'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="neo-card p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-tags text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Kategoriler</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['kategoriler_count'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="neo-card p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-line text-orange-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Bu Ay</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['this_month_notes'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="neo-card p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Filtreler</h3>
            <form method="GET" action="{{ route('admin.kisi-not.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Arama</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200" placeholder="Not başlığı veya içerik...">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <select style="color-scheme: light dark;" name="kategori" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                        <option value="">Tüm Kategoriler</option>
                        @foreach($kategoriler ?? [] as $key => $value)
                            <option value="{{ $key }}" {{ request('kategori') == $key ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Önem Derecesi</label>
                    <select style="color-scheme: light dark;" name="onem_derecesi" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                        <option value="">Tümü</option>
                        <option value="dusuk" {{ request('onem_derecesi') == 'dusuk' ? 'selected' : '' }}>Düşük</option>
                        <option value="orta" {{ request('onem_derecesi') == 'orta' ? 'selected' : '' }}>Orta</option>
                        <option value="yuksek" {{ request('onem_derecesi') == 'yuksek' ? 'selected' : '' }}>Yüksek</option>
                        <option value="kritik" {{ request('onem_derecesi') == 'kritik' ? 'selected' : '' }}>Kritik</option>
                    </select>
                </div>

                <div class="flex items-end space-x-2">
                    <button type="submit" class="neo-btn neo-btn neo-btn-primary flex-1">
                        <i class="fas fa-search mr-2"></i>
                        Filtrele
                    </button>
                    <a href="{{ route('admin.kisi-not.index') }}" class="neo-btn neo-btn neo-btn-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </form>
        </div>

        <!-- Notes Table -->
        <div class="neo-card">
            <div class="neo-card-header">
                <h3 class="text-lg font-semibold text-gray-800">Müşteri Notları</h3>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500">{{ count($notlar ?? []) }} not bulundu</span>
                </div>
            </div>

            <div class="neo-table-responsive">
                <table class="neo-table">
                    <thead>
                        <tr>
                            <th class="neo-table-th">
                                <input type="checkbox" class="w-5 h-5 text-blue-600 bg-gray-50 dark:bg-gray-800 border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-all duration-200 cursor-pointer" onchange="toggleAllCheckboxes(this)">
                            </th>
                            <th class="neo-table-th">Kişi</th>
                            <th class="neo-table-th">Başlık</th>
                            <th class="neo-table-th">Kategori</th>
                            <th class="neo-table-th">Önem</th>
                            <th class="neo-table-th">Durum</th>
                            <th class="neo-table-th">Tarih</th>
                            <th class="neo-table-th">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notlar ?? [] as $not)
                            <tr class="neo-table-tr hover:bg-gray-50">
                                <td class="neo-table-td">
                                    <input type="checkbox" class="w-5 h-5 text-blue-600 bg-gray-50 dark:bg-gray-800 border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-all duration-200 cursor-pointer note-checkbox" value="{{ $not['id'] }}">
                                </td>
                                <td class="neo-table-td">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                                            <i class="fas fa-user text-gray-600 text-sm"></i>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $not['kisi_adi'] ?? 'Bilinmeyen' }}</div>
                                            <div class="text-sm text-gray-500">ID: {{ $not['kisi_id'] ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="neo-table-td">
                                    <div class="max-w-xs">
                                        <div class="font-medium text-gray-900 truncate">{{ $not['baslik'] ?? 'Başlıksız' }}</div>
                                        <div class="text-sm text-gray-500 truncate">{{ Str::limit($not['icerik'] ?? '', 50) }}</div>
                                    </div>
                                </td>
                                <td class="neo-table-td">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                        {{ ucfirst($not['kategori'] ?? 'genel') }}
                                    </span>
                                </td>
                                <td class="neo-table-td">
                                    @php
                                        $importance = $not['onem_derecesi'] ?? 'orta';
                                        $importanceColors = [
                                            'dusuk' => 'bg-gray-100 text-gray-800',
                                            'orta' => 'bg-yellow-100 text-yellow-800',
                                            'yuksek' => 'bg-orange-100 text-orange-800',
                                            'kritik' => 'bg-red-100 text-red-800'
                                        ];
                                        $importanceLabels = [
                                            'dusuk' => 'Düşük',
                                            'orta' => 'Orta',
                                            'yuksek' => 'Yüksek',
                                            'kritik' => 'Kritik'
                                        ];
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $importanceColors[$importance] ?? $importanceColors['orta'] }}">
                                        {{ $importanceLabels[$importance] ?? 'Orta' }}
                                    </span>
                                </td>
                                <td class="neo-table-td">
                                    @if($not['is_completed'] ?? false)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check mr-1"></i>
                                            Tamamlandı
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-clock mr-1"></i>
                                            Devam Ediyor
                                        </span>
                                    @endif
                                </td>
                                <td class="neo-table-td">
                                    <div class="text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($not['created_at'] ?? now())->format('d.m.Y') }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($not['created_at'] ?? now())->format('H:i') }}
                                    </div>
                                </td>
                                <td class="neo-table-td">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('admin.kisi-not.show', $not['id']) }}"
                                           class="text-blue-600 hover:text-blue-800" title="Görüntüle">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.kisi-not.edit', $not['id']) }}"
                                           class="text-yellow-600 hover:text-yellow-800" title="Düzenle">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="deleteNote({{ $not['id'] }})"
                                                class="text-red-600 hover:text-red-800" title="Sil">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="neo-table-td text-center py-12">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-sticky-note text-4xl text-gray-300 mb-4"></i>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">Henüz not bulunmuyor</h3>
                                        <p class="text-gray-500 mb-4">İlk notunuzu oluşturmak için yukarıdaki butonu kullanın.</p>
                                        <a href="{{ route('admin.kisi-not.create') }}" class="neo-btn neo-btn neo-btn-primary">
                                            <i class="fas fa-plus mr-2"></i>
                                            Yeni Not Oluştur
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="fixed bottom-4 right-4" id="bulkActionsPanel" style="display: none;">
            <div class="neo-card p-4 shadow-lg">
                <div class="flex items-center space-x-3">
                    <span class="text-sm font-medium text-gray-700" id="selectedCount">0 seçildi</span>
                    <div class="flex space-x-2">
                        <button onclick="bulkAction('complete')" class="neo-btn neo-btn-sm neo-btn-success">
                            <i class="fas fa-check mr-1"></i>
                            Tamamla
                        </button>
                        <button onclick="bulkAction('delete')" class="neo-btn neo-btn-sm neo-btn-danger">
                            <i class="fas fa-trash mr-1"></i>
                            Sil
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function toggleAllCheckboxes(masterCheckbox) {
    const checkboxes = document.querySelectorAll('.note-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = masterCheckbox.checked;
    });
    updateBulkActions();
}

function updateBulkActions() {
    const checkedBoxes = document.querySelectorAll('.note-checkbox:checked');
    const bulkPanel = document.getElementById('bulkActionsPanel');
    const countSpan = document.getElementById('selectedCount');

    if (checkedBoxes.length > 0) {
        bulkPanel.style.display = 'block';
        countSpan.textContent = `${checkedBoxes.length} seçildi`;
    } else {
        bulkPanel.style.display = 'none';
    }
}

function bulkAction(action) {
    const checkedBoxes = document.querySelectorAll('.note-checkbox:checked');
    const noteIds = Array.from(checkedBoxes).map(cb => cb.value);

    if (noteIds.length === 0) {
        alert('Lütfen en az bir not seçin.');
        return;
    }

    if (confirm(`Seçili ${noteIds.length} not üzerinde ${action} işlemi yapılacak. Emin misiniz?`)) {
        // Bulk action implementation
        fetch('{{ route("admin.kisi-not.bulk") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                action: action,
                note_ids: noteIds
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(error => {
            showToast('İşlem sırasında hata oluştu', 'error');
        });
    }
}

function deleteNote(noteId) {
    if (confirm('Bu notu silmek istediğinizden emin misiniz?')) {
        fetch(`{{ route('admin.kisi-not.index') }}/${noteId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(error => {
            showToast('Silme işlemi sırasında hata oluştu', 'error');
        });
    }
}

function exportNotes() {
    const format = prompt('Dışa aktarım formatını seçin:\n1 - Excel (.xlsx)\n2 - CSV (.csv)\n3 - JSON (.json)', '1');

    if (format) {
        const formats = { '1': 'xlsx', '2': 'csv', '3': 'json' };
        const selectedFormat = formats[format] || 'xlsx';

        window.open(`{{ route('admin.kisi-not.export') }}?format=${selectedFormat}`, '_blank');
    }
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 px-4 py-2 rounded-lg text-white z-50 ${
        type === 'success' ? 'bg-green-500' :
        type === 'error' ? 'bg-red-500' : 'bg-blue-500'
    }`;
    toast.textContent = message;

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// Add event listeners
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.note-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });
});
</script>
@endpush
