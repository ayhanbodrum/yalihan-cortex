@extends('admin.layouts.neo')

@section('title', 'Tip Yönetimi')

@section('content')
    <div class="prose max-w-none p-6">
        <!-- Page Header -->
        <div class="neo-page-header mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Tip Yönetimi</h1>
                    <p class="text-gray-600 mt-2">Emlak tiplerini yönetin ve kategorilendirin</p>
                </div>
            </div>
        </div>

        <!-- Categories Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($kategoriler as $key => $kategori)
                <div class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800">
                    <div class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800-header">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <span class="text-3xl mr-3">{{ $kategori['icon'] }}</span>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        {{ $kategori['name'] }}
                                    </h3>
                                    <p class="text-sm text-gray-600">
                                        {{ $kategori['count'] }} tip
                                    </p>
                                </div>
                            </div>
                            <a href="{{ route('admin.tip-yonetimi.create', $key) }}"
                            class="px-3 py-1 bg-primary-600 hover:bg-primary-700 text-white text-sm rounded-lg transition-colors">
                            <i class="fas fa-plus mr-1"></i> Ekle
                        </a>
                    </div>

                    <!-- Tip Listesi -->
                    <div class="space-y-2 max-h-64 overflow-y-auto">
                        @forelse($kategori['items'] as $tip)
                            <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <span class="text-sm text-gray-900 dark:text-white">{{ $tip->name }}</span>
                                <div class="flex space-x-1">
                                    <a href="{{ route('admin.tip-yonetimi.edit', [$key, $tip->id]) }}"
                                        class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded transition-colors">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button
                                        onclick="deleteTip('{{ $key }}', {{ $tip->id }}, '{{ $tip->name }}')"
                                        class="px-2 py-1 bg-red-500 hover:bg-red-600 text-white text-xs rounded transition-colors">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 italic">Henüz tip eklenmemiş</p>
                        @endforelse
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6 max-w-md w-full">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Tipi Sil</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    "<span id="deleteTipName"></span>" tipini silmek istediğinizden emin misiniz?
                    Bu işlem geri alınamaz.
                </p>
                <div class="flex justify-end space-x-3">
                    <button onclick="closeDeleteModal()"
                        class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg transition-colors">
                        İptal
                    </button>
                    <button onclick="confirmDelete()"
                        class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors">
                        Sil
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        let deleteData = {};

        function deleteTip(kategori, id, name) {
            deleteData = {
                kategori,
                id,
                name
            };
            document.getElementById('deleteTipName').textContent = name;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            deleteData = {};
        }

        function confirmDelete() {
            if (!deleteData.kategori || !deleteData.id) return;

            fetch(`/admin/tip-yonetimi/${deleteData.kategori}/${deleteData.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                    closeDeleteModal();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Bir hata oluştu');
                    closeDeleteModal();
                });
        }
    </script>
@endpush
