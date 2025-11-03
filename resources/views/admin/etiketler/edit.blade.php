@extends('admin.layouts.neo')

@section('title', 'Etiket Düzenle')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="admin-h1">{{ $etiket->ad }} Etiketini Düzenle</h1>
        <div class="flex space-x-2">
            <a href="{{ route('admin.etiketler.index') }}" class="neo-btn neo-btn-secondary touch-target-optimized touch-target-optimized">
                <i class="bi bi-arrow-left mr-2"></i> Geri Dön
            </a>
            <a href="{{ route('admin.etiketler.show', $etiket) }}" class="neo-btn neo-btn-secondary touch-target-optimized touch-target-optimized">
                <i class="bi bi-eye mr-2"></i> Görüntüle
            </a>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-h3">
                <i class="bi bi-tag mr-2"></i> Etiket Bilgileri
            </h2>
        </div>
        <div class="admin-card-body">
            @if ($errors->any())
                <div class="admin-alert admin-alert-danger mb-6">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.etiketler.update', $etiket) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-6">
                    <label for="ad" class="admin-label">Etiket Adı <span class="text-red-600">*</span></label>
                    <input type="text" name="ad" id="ad" value="{{ old('ad', $etiket->ad) }}" required
                        class="admin-input @error('ad') border-red-500 @enderror">
                    <p class="admin-help-text">Etiket için benzersiz bir isim girin.</p>
                    @error('ad')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="renk" class="admin-label">Renk Kodu</label>
                    <div class="flex items-center space-x-3">
                        <input type="color" name="renk" id="renk"
                            value="{{ old('renk', $etiket->renk ?? '#3490dc') }}" class="admin-color-input">
                        <input type="text" name="renk_kod" id="renk_kod"
                            value="{{ old('renk', $etiket->renk ?? '#3490dc') }}" class="admin-input w-32" maxlength="7"
                            placeholder="#3490dc">
                    </div>
                    <p class="admin-help-text">Etiket için renk seçimi opsiyoneldir. Hexadecimal renk kodu kullanın (örn:
                        #3490dc).</p>
                    @error('renk')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="admin-label">Kullanım Bilgisi</label>
                    <div class="admin-info-box">
                        <i class="bi bi-info-circle text-blue-500 mr-2"></i>
                        Bu etiket şu anda <strong>{{ $etiket->kisiler->count() }}</strong> müşteri tarafından kullanılıyor.
                    </div>
                </div>

                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.etiketler.index') }}" class="neo-btn neo-btn-secondary touch-target-optimized touch-target-optimized">
                        <i class="bi bi-x-circle mr-2"></i> İptal
                    </a>
                    <button type="submit" class="neo-btn neo-btn-primary touch-target-optimized touch-target-optimized">
                        <i class="bi bi-check-circle mr-2"></i> Güncelle
                    </button>
                </div>
            </form>
        </div>
    @endsection

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const colorInput = document.getElementById('renk');
                const codeInput = document.getElementById('renk_kod');

                // Renk değiştiğinde kod inputunu güncelle
                colorInput.addEventListener('input', function() {
                    codeInput.value = colorInput.value;
                });

                // Kod inputu değiştiğinde renk inputunu güncelle
                codeInput.addEventListener('input', function() {
                    // Hexadecimal renk kodu kontrolü yap
                    if (/^#[0-9A-F]{6}$/i.test(codeInput.value)) {
                        colorInput.value = codeInput.value;
                    }
                });
            });
        </script>
    @endpush
