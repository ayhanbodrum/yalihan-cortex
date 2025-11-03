@extends('admin.layouts.neo')

@section('title', 'Kategori Düzenle')

@section('content')
    <div class="neo-container" x-data="kategoriForm()">
        <div class="neo-header">
            <div class="neo-header-content">
                <h1 class="neo-title">Kategori Düzenle</h1>
            </div>
            <div class="neo-header-actions">
                <a href="{{ route('admin.ilan-kategorileri.index') }}" class="neo-btn neo-btn-secondary">
                    ← Geri Dön
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <span class="text-green-700">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <span class="text-red-700">{{ session('error') }}</span>
            </div>
        @endif

        <div class="neo-card">
            <div class="neo-card-body">
                <form method="POST" action="{{ route('admin.ilan-kategorileri.update', $kategori) }}" @submit.prevent="submitForm">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="neo-form-group">
                            <label for="name" class="neo-label">Kategori Adı *</label>
                            <input type="text" id="name" name="name" class="neo-input" value="{{ old('name', $kategori->name) }}" required placeholder="Örn: Daire, Villa">
                            @error('name')
                                <div class="neo-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="neo-form-group">
                            <label for="seviye" class="neo-label">Seviye *</label>
                            <select id="seviye" name="seviye" class="neo-select" required @change="updateParentOptions()">
                                <option value="">Seçiniz</option>
                                <option value="0" {{ old('seviye', $kategori->seviye) == 0 ? 'selected' : '' }}>Ana Kategori</option>
                                <option value="1" {{ old('seviye', $kategori->seviye) == 1 ? 'selected' : '' }}>Alt Kategori</option>
                                <option value="2" {{ old('seviye', $kategori->seviye) == 2 ? 'selected' : '' }}>Yayın Tipi</option>
                            </select>
                            @error('seviye')
                                <div class="neo-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="neo-form-group md:col-span-2" id="parent-field" x-show="parentRequired" x-cloak>
                            <label for="parent_id" class="neo-label">Üst Kategori <span x-show="parentRequired" x-cloak>*</span></label>
                            <select id="parent_id" name="parent_id" class="neo-select">
                                <option value="">Seçiniz</option>
                                @foreach ($parentCategories as $anaKategori)
                                    @if($anaKategori->id != $kategori->id)
                                        <option value="{{ $anaKategori->id }}" {{ old('parent_id', $kategori->parent_id) == $anaKategori->id ? 'selected' : '' }}>
                                            {{ $anaKategori->name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            @error('parent_id')
                                <div class="neo-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="neo-form-group">
                            <label for="order" class="neo-label">Sıra</label>
                            <input type="number" id="order" name="order" class="neo-input" value="{{ old('order', $kategori->order) }}" min="0">
                            @error('order')
                                <div class="neo-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="neo-form-group">
                        <label class="neo-label">Status</label>
                        <div class="flex items-center space-x-4">
                            <label class="flex items-center">
                                <input type="radio" name="status" value="1" class="neo-radio" {{ old('status', $kategori->status) == 1 ? 'checked' : '' }}>
                                <span class="ml-2">Active</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="status" value="0" class="neo-radio" {{ old('status', $kategori->status) == 0 ? 'checked' : '' }}>
                                <span class="ml-2">Inactive</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('admin.ilan-kategorileri.index') }}" class="neo-btn neo-btn-secondary">İptal</a>
                        <button type="submit" class="neo-btn neo-btn-primary" :disabled="loading">
                            <span x-show="!loading">Kaydet</span>
                            <span x-show="loading">Kaydediliyor...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function kategoriForm() {
            return {
                loading: false,
                parentRequired: {{ $kategori->seviye > 0 ? 'true' : 'false' }},
                updateParentOptions() {
                    const seviye = document.getElementById('seviye').value;

                    if (seviye == '1' || seviye == '2') {
                        this.parentRequired = true;
                    } else {
                        this.parentRequired = false;
                        document.getElementById('parent_id').value = '';
                    }
                },
                submitForm(event) {
                    if (this.parentRequired && !document.getElementById('parent_id').value) {
                        event.preventDefault();
                        alert('Üst Kategori seçmelisiniz!');
                        return false;
                    }

                    this.loading = true;
                    event.target.submit();
                }
            }
        }
    </script>
@endsection
