@extends('admin.layouts.neo')

@section('content')
    <div class="space-y-6" x-data="siteOzellikleriManager()">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    🏘️ Site Özellikleri Yönetimi
                </h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Site ve apartmanlar için özellikler ekleyin/düzenleyin</p>
            </div>
            <button @click="showCreateModal = true" class="neo-btn neo-btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Yeni Özellik Ekle
            </button>
        </div>

        <!-- Liste -->
        <div class="neo-card">
            <div class="p-6">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left py-3 px-4 text-gray-700 dark:text-gray-300">Sıra</th>
                            <th class="text-left py-3 px-4 text-gray-700 dark:text-gray-300">Özellik Adı</th>
                            <th class="text-left py-3 px-4 text-gray-700 dark:text-gray-300">Tip</th>
                            <th class="text-left py-3 px-4 text-gray-700 dark:text-gray-300">Açıklama</th>
                            <th class="text-left py-3 px-4 text-gray-700 dark:text-gray-300">Durum</th>
                            <th class="text-right py-3 px-4 text-gray-700 dark:text-gray-300">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ozellikleri as $ozellik)
                            <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="py-3 px-4">
                                    <input type="number" value="{{ $ozellik->order }}"
                                           class="w-16 px-2 py-1 border rounded text-center"
                                           @change="updateOrder({{ $ozellik->id }}, $event.target.value)">
                                </td>
                                <td class="py-3 px-4 font-medium text-gray-900 dark:text-gray-100">
                                    {{ $ozellik->name }}
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-1 text-xs rounded-full
                                        {{ $ozellik->type === 'security' ? 'bg-red-100 text-red-800' : '' }}
                                        {{ $ozellik->type === 'amenity' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $ozellik->type === 'facility' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $ozellik->type === 'transportation' ? 'bg-purple-100 text-purple-800' : '' }}
                                        {{ $ozellik->type === 'maintenance' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                        {{ ucfirst($ozellik->type) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400">
                                    {{ Str::limit($ozellik->description, 50) }}
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $ozellik->status ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $ozellik->status ? 'Aktif' : 'Pasif' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click="editOzellik({{ $ozellik }})"
                                                class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                            Düzenle
                                        </button>
                                        <button @click="deleteOzellik({{ $ozellik->id }}, '{{ $ozellik->name }}')"
                                                class="text-red-600 hover:text-red-800 dark:text-red-400">
                                            Sil
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-gray-500 dark:text-gray-400">
                                    Henüz site özelliği eklenmemiş. "Yeni Özellik Ekle" butonunu kullanın.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <div x-show="showCreateModal || editingOzellik"
             class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
             @click.self="closeModal()">
            <div class="relative top-20 mx-auto p-6 border w-full max-w-md shadow-lg rounded-lg bg-white dark:bg-gray-800">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white" x-text="editingOzellik ? 'Özellik Düzenle' : 'Yeni Özellik Ekle'"></h3>
                    <button @click="closeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="saveOzellik()">
                    <div class="space-y-4">
                        <!-- Name -->
                        <div class="neo-form-group">
                            <label class="neo-label">Özellik Adı *</label>
                            <input type="text" x-model="formData.name" class="neo-input" required placeholder="Örn: Güvenlik, Otopark">
                        </div>

                        <!-- Type -->
                        <div class="neo-form-group">
                            <label class="neo-label">Tip *</label>
                            <select x-model="formData.type" class="neo-input" required>
                                <option value="">Seçin</option>
                                <option value="security">🔒 Güvenlik</option>
                                <option value="amenity">🏢 Tesis</option>
                                <option value="facility">🏞️ Sosyal Alan</option>
                                <option value="transportation">🚗 Ulaşım</option>
                                <option value="maintenance">🔧 Bakım</option>
                            </select>
                        </div>

                        <!-- Description -->
                        <div class="neo-form-group">
                            <label class="neo-label">Açıklama</label>
                            <textarea x-model="formData.description" class="neo-input" rows="3" placeholder="İsteğe bağlı açıklama"></textarea>
                        </div>

                        <!-- Order -->
                        <div class="neo-form-group">
                            <label class="neo-label">Sıra</label>
                            <input type="number" x-model="formData.order" class="neo-input" min="0" placeholder="0">
                        </div>

                        <!-- Status -->
                        <div class="flex items-center">
                            <input type="checkbox" x-model="formData.status" id="ozellik_status" class="mr-2">
                            <label for="ozellik_status" class="text-sm text-gray-700 dark:text-gray-300">Aktif</label>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" @click="closeModal()" class="neo-btn neo-btn-secondary">
                            İptal
                        </button>
                        <button type="submit" class="neo-btn neo-btn-primary">
                            <span x-text="editingOzellik ? 'Güncelle' : 'Ekle'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function siteOzellikleriManager() {
            return {
                showCreateModal: false,
                editingOzellik: null,
                formData: {
                    name: '',
                    type: 'amenity',
                    description: '',
                    order: 0,
                    status: true
                },

                editOzellik(ozellik) {
                    this.editingOzellik = ozellik;
                    this.formData = {
                        name: ozellik.name,
                        type: ozellik.type,
                        description: ozellik.description || '',
                        order: ozellik.order,
                        status: ozellik.status
                    };
                },

                closeModal() {
                    this.showCreateModal = false;
                    this.editingOzellik = null;
                    this.resetForm();
                },

                resetForm() {
                    this.formData = {
                        name: '',
                        type: 'amenity',
                        description: '',
                        order: 0,
                        status: true
                    };
                },

                async saveOzellik() {
                    const url = this.editingOzellik
                        ? `/admin/site-ozellikleri/${this.editingOzellik.id}`
                        : '/admin/site-ozellikleri';

                    const method = this.editingOzellik ? 'PUT' : 'POST';

                    try {
                        const response = await fetch(url, {
                            method: method,
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.formData)
                        });

                        const data = await response.json();

                        if (data.success) {
                            window.toast?.success(data.message);
                            this.closeModal();
                            setTimeout(() => window.location.reload(), 1000);
                        } else {
                            throw new Error(data.message || 'İşlem başarısız');
                        }
                    } catch (error) {
                        console.error('Kaydetme hatası:', error);
                        window.toast?.error('İşlem başarısız: ' + error.message);
                    }
                },

                async deleteOzellik(id, name) {
                    if (!confirm(`"${name}" özelliğini silmek istediğinizden emin misiniz?`)) {
                        return;
                    }

                    try {
                        const response = await fetch(`/admin/site-ozellikleri/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            window.toast?.success(data.message);
                            setTimeout(() => window.location.reload(), 1000);
                        } else {
                            throw new Error(data.message || 'Silme işlemi başarısız');
                        }
                    } catch (error) {
                        console.error('Silme hatası:', error);
                        window.toast?.error('Silme işlemi başarısız: ' + error.message);
                    }
                },

                async updateOrder(id, newOrder) {
                    try {
                        const response = await fetch('/admin/site-ozellikleri/update-order', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                orders: [{ id: id, order: parseInt(newOrder) }]
                            })
                        });

                        const data = await response.json();
                        if (data.success) {
                            window.toast?.success('Sıra güncellendi');
                        }
                    } catch (error) {
                        console.error('Sıra güncelleme hatası:', error);
                    }
                }
            }
        }
    </script>
    @endpush
@endsection
