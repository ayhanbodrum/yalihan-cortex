@extends('admin.layouts.neo')

@section('title', 'Yeni Kullanıcı Ekle')

@section('content')
@if(session('success'))
    <div class="neo-alert neo-alert-success mb-4">
        <i class="neo-icon neo-icon-check-circle"></i>
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="neo-alert neo-alert-danger mb-4">
        <i class="neo-icon neo-icon-x-circle"></i>
        {{ session('error') }}
    </div>
@endif

    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-purple-50 dark:from-gray-900 dark:to-purple-900">
        {{-- Header --}}
        <div class="bg-gray-50 dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 mb-8 p-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1
                        class="text-4xl font-bold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">
                        👥 Yeni Kullanıcı Ekle
                    </h1>
                    <p class="mt-3 text-lg text-gray-600 dark:text-gray-400">
                        Sisteme yeni kullanıcı ekleyin
                    </p>
                </div>
                <a href="{{ route('admin.kullanicilar.index') }}" class="neo-btn inline-flex items-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-sm touch-target-optimized touch-target-optimized">
                    ← Geri Dön
                </a>
            </div>
        </div>

        {{-- Form --}}
        <form action="{{ route('admin.kullanicilar.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Temel Bilgiler --}}
            <div class="bg-gray-50 dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-8">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-6 flex items-center">
                    <span
                        class="bg-purple-100 dark:bg-purple-900 text-purple-600 dark:text-purple-300 rounded-full w-10 h-10 flex items-center justify-center text-lg font-bold mr-3">1</span>
                    👤 Temel Bilgiler
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Ad Soyad --}}
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Ad Soyad *
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white @error('name') border-red-500 @enderror"
                            placeholder="Kullanıcı adı soyadı">
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
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white @error('email') border-red-500 @enderror"
                            placeholder="ornek@yalihanemlak.com">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Telefon --}}
                    <div>
                        <label for="telefon" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Telefon
                        </label>
                        <input type="text" name="telefon" id="telefon" value="{{ old('telefon') }}"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white @error('telefon') border-red-500 @enderror"
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
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white @error('password') border-red-500 @enderror"
                            placeholder="En az 8 karakter">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Şifre Tekrar --}}
                    <div>
                        <label for="password_confirmation"
                            class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Şifre Tekrar *
                        </label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white"
                            placeholder="Şifreyi tekrar girin">
                    </div>
                </div>
            </div>

            {{-- Rol ve Yetki Bilgileri --}}
            <div class="bg-gray-50 dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-8">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-6 flex items-center">
                    <span
                        class="bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-300 rounded-full w-10 h-10 flex items-center justify-center text-lg font-bold mr-3">2</span>
                    🔐 Rol ve Yetki Bilgileri
                </h2>

                <div class="space-y-6">
                    {{-- Rol Seçimi --}}
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Rol *
                        </label>
                        <select style="color-scheme: light dark;" name="role" id="role" required
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white @error('role') border-red-500 @enderror transition-all duration-200">
                            <option value="">Rol Seçiniz</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>👑 Admin</option>
                            <option value="danisman" {{ old('role') == 'danisman' ? 'selected' : '' }}>👤 Danışman</option>
                            <option value="editor" {{ old('role') == 'editor' ? 'selected' : '' }}>✏️ Editör</option>
                            <option value="viewer" {{ old('role') == 'viewer' ? 'selected' : '' }}>👁️ İzleyici</option>
                        </select>
                        @error('role')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Rol Açıklamaları --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div
                            class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4 border border-purple-200 dark:border-purple-700">
                            <h3 class="font-semibold text-purple-700 dark:text-purple-300 mb-2">👑 Admin</h3>
                            <p class="text-sm text-purple-600 dark:text-purple-400">Tüm yetkilere sahip süper kullanıcı</p>
                        </div>
                        <div
                            class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-700">
                            <h3 class="font-semibold text-blue-700 dark:text-blue-300 mb-2">👤 Danışman</h3>
                            <p class="text-sm text-blue-600 dark:text-blue-400">İlan ekleme, düzenleme ve müşteri yönetimi
                            </p>
                        </div>
                        <div
                            class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4 border border-green-200 dark:border-green-700">
                            <h3 class="font-semibold text-green-700 dark:text-green-300 mb-2">✏️ Editör</h3>
                            <p class="text-sm text-green-600 dark:text-green-400">İçerik düzenleme ve yayınlama</p>
                        </div>
                        <div
                            class="bg-gray-50 dark:bg-gray-900/20 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-2">👁️ İzleyici</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Sadece görüntüleme yetkisi</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Durum ve Ayarlar --}}
            <div class="bg-gray-50 dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-8">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-6 flex items-center">
                    <span
                        class="bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300 rounded-full w-10 h-10 flex items-center justify-center text-lg font-bold mr-3">3</span>
                    ⚙️ Durum ve Ayarlar
                </h2>

                <div class="space-y-6">
                    {{-- Aktif Durum --}}
                    <div
                        class="flex items-center justify-between bg-green-50 dark:bg-green-900/20 rounded-lg p-4 border border-green-200 dark:border-green-700">
                        <div>
                            <label for="enabled" class="font-medium text-gray-900 dark:text-white">
                                Kullanıcı Aktif
                            </label>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                Kullanıcı sisteme giriş yapabilir
                            </p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="enabled" id="enabled" value="1"
                                {{ old('enabled', true) ? 'checked' : '' }} class="sr-only peer">
                            <div
                                class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 dark:peer-focus:ring-green-800 rounded-full peer dark:bg-gray-800 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all dark:border-gray-600 peer-checked:bg-green-600">
                            </div>
                        </label>
                    </div>

                    {{-- Email Doğrulanmış --}}
                    <div
                        class="flex items-center justify-between bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-700">
                        <div>
                            <label for="email_verified" class="font-medium text-gray-900 dark:text-white">
                                E-posta Doğrulanmış
                            </label>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                E-posta adresi doğrulanmış olarak işaretle
                            </p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="email_verified" id="email_verified" value="1"
                                {{ old('email_verified', true) ? 'checked' : '' }} class="sr-only peer">
                            <div
                                class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-800 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Form Actions --}}
            <div
                class="flex items-center justify-between bg-gray-50 dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-6">
                <a href="{{ route('admin.kullanicilar.index') }}" class="neo-btn inline-flex items-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-sm touch-target-optimized touch-target-optimized">
                    ← İptal
                </a>
                <button type="submit" class="neo-btn inline-flex items-center px-4 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg active:scale-95 touch-target-optimized touch-target-optimized">
                    <i class="fas fa-save mr-2"></i>
                    Kaydet
                </button>
            </div>
        </form>
    </div>
@endsection
