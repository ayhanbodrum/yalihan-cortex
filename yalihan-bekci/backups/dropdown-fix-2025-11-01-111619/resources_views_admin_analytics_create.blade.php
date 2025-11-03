@extends('admin.layouts.neo')

@section('title', 'Yeni Analitik Raporu Oluştur')

@section('content')
    <div class="content-header mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                    <div
                        class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-plus text-white text-xl"></i>
                    </div>
                    Yeni Analitik Raporu
                </h1>
                <p class="text-lg text-gray-600 mt-2">Özel analitik raporu oluşturun</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.analytics.index') }}" class="neo-btn neo-btn neo-btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Geri Dön
                </a>
            </div>
        </div>
    </div>

    <div class="neo-card p-8">
        <form action="{{ route('admin.analytics.store') }}" method="POST" id="analyticsForm">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Sol Kolon -->
                <div class="space-y-6">
                    <!-- Rapor Adı -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Rapor Adı <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Örn: Aylık Performans Raporu" required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Rapor Tipi -->
                    <div>
                        <label for="report_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Rapor Tipi <span class="text-red-500">*</span>
                        </label>
                        <select id="report_type" name="report_type" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Rapor tipi seçin</option>
                            @foreach ($reportTypes as $key => $label)
                                <option value="{{ $key }}" {{ old('report_type') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('report_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tarih Aralığı -->
                    <div>
                        <label for="date_range" class="block text-sm font-medium text-gray-700 mb-2">
                            Tarih Aralığı <span class="text-red-500">*</span>
                        </label>
                        <select id="date_range" name="date_range" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Tarih aralığı seçin</option>
                            @foreach ($dateRanges as $key => $label)
                                <option value="{{ $key }}" {{ old('date_range') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('date_range')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Sağ Kolon -->
                <div class="space-y-6">
                    <!-- Metrikler -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Dahil Edilecek Metrikler <span class="text-red-500">*</span>
                        </label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="metrics[]" value="views"
                                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                    {{ in_array('views', old('metrics', [])) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-700">Görüntülemeler</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="metrics[]" value="users"
                                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                    {{ in_array('users', old('metrics', [])) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-700">Kullanıcılar</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="metrics[]" value="conversions"
                                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                    {{ in_array('conversions', old('metrics', [])) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-700">Dönüşümler</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="metrics[]" value="revenue"
                                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                    {{ in_array('revenue', old('metrics', [])) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-700">Gelir</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="metrics[]" value="bounce_rate"
                                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                    {{ in_array('bounce_rate', old('metrics', [])) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-700">Çıkış Oranı</span>
                            </label>
                        </div>
                        @error('metrics')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Açıklama -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Açıklama
                        </label>
                        <textarea id="description" name="description" rows="4"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Rapor hakkında kısa açıklama...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-4 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.analytics.index') }}" class="neo-btn neo-btn neo-btn-secondary">
                    İptal
                </a>
                <button type="submit" class="neo-btn neo-btn neo-btn-primary" id="submitBtn">
                    <i class="fas fa-save mr-2"></i>
                    Raporu Oluştur
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            document.getElementById('analyticsForm').addEventListener('submit', function() {
                const submitBtn = document.getElementById('submitBtn');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Oluşturuluyor...';
            });
        </script>
    @endpush
@endsection
