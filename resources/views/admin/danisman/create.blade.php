@extends('admin.layouts.neo')

@section('title', 'Yeni Danışman Ekle')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 dark:from-gray-900 dark:to-blue-900">
        {{-- Header --}}
        <div class="bg-gray-50 dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 mb-8 p-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                        👤 Yeni Danışman Ekle
                    </h1>
                    <p class="mt-3 text-lg text-gray-600 dark:text-gray-400">
                        Sisteme yeni danışman kullanıcısı ekleyin
                    </p>
                </div>
                <a href="{{ route('admin.danisman.index') }}" class="neo-btn neo-btn neo-btn-secondary touch-target-optimized touch-target-optimized">
                    ← Geri Dön
                </a>
            </div>
        </div>

        {{-- Form --}}
        <form action="{{ route('admin.danisman.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Temel Bilgiler --}}
            <div class="bg-gray-50 dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-8">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-6 flex items-center">
                    <span class="bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 rounded-full w-10 h-10 flex items-center justify-center text-lg font-bold mr-3">1</span>
                    👤 Temel Bilgiler
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Ad Soyad --}}
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Ad Soyad *
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white @error('name') border-red-500 @enderror"
                            placeholder="Danışman adı soyadı">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            E-posta *
                        </label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white @error('email') border-red-500 @enderror"
                            placeholder="ornek@yalihanemlak.com">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Telefon --}}
                    <div>
                        <label for="telefon" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Telefon *
                        </label>
                        <input type="text" name="telefon" id="telefon" value="{{ old('telefon') }}" required
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white @error('telefon') border-red-500 @enderror"
                            placeholder="0532 123 45 67">
                        @error('telefon')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Şifre --}}
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Şifre *
                        </label>
                        <input type="password" name="password" id="password" required
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white @error('password') border-red-500 @enderror"
                            placeholder="Güvenli şifre oluşturun">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Şifre Tekrar --}}
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Şifre Tekrar *
                        </label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white"
                            placeholder="Şifreyi tekrar girin">
                    </div>
                </div>
            </div>

            {{-- Profesyonel Bilgiler --}}
            <div class="bg-gray-50 dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-8">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-6 flex items-center">
                    <span class="bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300 rounded-full w-10 h-10 flex items-center justify-center text-lg font-bold mr-3">2</span>
                    💼 Profesyonel Bilgiler
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Lisans No --}}
                    <div>
                        <label for="lisans_no" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Lisans Numarası
                        </label>
                        <input type="text" name="lisans_no" id="lisans_no" value="{{ old('lisans_no') }}"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white"
                            placeholder="Gayrimenkul lisans numarası">
                    </div>

                    {{-- Komisyon Oranı --}}
                    <div>
                        <label for="komisyon_orani" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Komisyon Oranı (%)
                        </label>
                        <input type="number" name="komisyon_orani" id="komisyon_orani" value="{{ old('komisyon_orani', 2.5) }}"
                            min="0" max="100" step="0.1"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white"
                            placeholder="2.5">
                    </div>

                    {{-- Uzmanlık Alanı --}}
                    <div class="md:col-span-2">
                        <label for="uzmanlik_alani" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Uzmanlık Alanı
                        </label>
                        <select style="color-scheme: light dark;" name="uzmanlik_alani" id="uzmanlik_alani"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white transition-all duration-200">
                            <option value="">Seçiniz...</option>
                            <option value="Konut" {{ old('uzmanlik_alani') == 'Konut' ? 'selected' : '' }}>Konut</option>
                            <option value="Arsa" {{ old('uzmanlik_alani') == 'Arsa' ? 'selected' : '' }}>Arsa</option>
                            <option value="İşyeri" {{ old('uzmanlik_alani') == 'İşyeri' ? 'selected' : '' }}>İşyeri</option>
                            <option value="Yazlık" {{ old('uzmanlik_alani') == 'Yazlık' ? 'selected' : '' }}>Yazlık</option>
                            <option value="Turistik Tesis" {{ old('uzmanlik_alani') == 'Turistik Tesis' ? 'selected' : '' }}>Turistik Tesis</option>
                            <option value="Genel" {{ old('uzmanlik_alani') == 'Genel' ? 'selected' : '' }}>Genel</option>
                        </select>
                    </div>

                    {{-- Adres --}}
                    <div class="md:col-span-2">
                        <label for="adres" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Adres
                        </label>
                        <textarea name="adres" id="adres" rows="3"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white"
                            placeholder="Ofis adresi...">{{ old('adres') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Durum --}}
            <div class="bg-gray-50 dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-8">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-6 flex items-center">
                    <span class="bg-purple-100 dark:bg-purple-900 text-purple-600 dark:text-purple-300 rounded-full w-10 h-10 flex items-center justify-center text-lg font-bold mr-3">3</span>
                    ⚙️ Durum Ayarları
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Durum --}}
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Durum *
                        </label>
                        <select style="color-scheme: light dark;" name="status" id="status" required
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white transition-all duration-200">
                            <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Pasif</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Form Aksiyonları --}}
            <div class="flex items-center justify-end space-x-4 bg-gray-50 dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-6">
                <a href="{{ route('admin.danisman.index') }}" class="neo-btn neo-btn neo-btn-secondary touch-target-optimized touch-target-optimized">
                    İptal
                </a>
                <button type="submit" class="neo-btn neo-btn neo-btn-primary touch-target-optimized touch-target-optimized">
                    💾 Danışman Oluştur
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            // Form validation
            document.querySelector('form').addEventListener('submit', function(e) {
                const password = document.getElementById('password').value;
                const passwordConfirm = document.getElementById('password_confirmation').value;
                
                if (password !== passwordConfirm) {
                    e.preventDefault();
                    alert('Şifreler eşleşmiyor!');
                    return false;
                }
                
                return true;
            });
        </script>
    @endpush
@endsection

