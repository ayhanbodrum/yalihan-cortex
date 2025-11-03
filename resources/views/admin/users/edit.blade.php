@extends('admin.layouts.neo')

@section('title', 'Kullanıcı Düzenle')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Kullanıcı Düzenle</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $user->name }} kullanıcısının bilgilerini güncelleyin</p>
        </div>
        <a href="{{ route('admin.kullanicilar.index') }}" class="neo-btn neo-btn-secondary">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Geri Dön
        </a>
    </div>

    <!-- Form Card -->
    <div class="neo-card">
        <form method="POST" action="{{ route('admin.kullanicilar.update', $user) }}" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Basic Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="neo-form-group">
                    <label for="name" class="neo-label">İsim *</label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="{{ old('name', $user->name) }}" 
                        class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 @error('name') border-red-500 @enderror" 
                        required
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="neo-form-group">
                    <label for="email" class="neo-label">E-posta *</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="{{ old('email', $user->email) }}" 
                        class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 @error('email') border-red-500 @enderror" 
                        required
                    >
                    @error('email')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Password -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="neo-form-group">
                    <label for="password" class="neo-label">Yeni Şifre</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 @error('password') border-red-500 @enderror"
                    >
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Boş bırakırsanız şifre değişmez</p>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="neo-form-group">
                    <label for="password_confirmation" class="neo-label">Şifre Tekrarı</label>
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200"
                    >
                </div>
            </div>

            <!-- Role & Status -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="neo-form-group">
                    <label for="role_id" class="neo-label">Rol *</label>
                    <select style="color-scheme: light dark;" 
                        id="role_id" 
                        name="role_id" 
                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 @error('role_id') border-red-500 @enderror transition-all duration-200" 
                        required
                    >
                        <option value="">Rol Seçiniz</option>
                        @php
                            $currentRole = $user->roles->first()?->name ?? '';
                            $roleMap = [
                                'super_admin' => 1,
                                'admin' => 2,
                                'danisman' => 3,
                                'user' => 4
                            ];
                            $selectedRoleId = $roleMap[$currentRole] ?? '';
                        @endphp
                        <option value="1" {{ old('role_id', $selectedRoleId) == 1 ? 'selected' : '' }}>Super Admin</option>
                        <option value="2" {{ old('role_id', $selectedRoleId) == 2 ? 'selected' : '' }}>Admin</option>
                        <option value="3" {{ old('role_id', $selectedRoleId) == 3 ? 'selected' : '' }}>Danışman</option>
                        <option value="4" {{ old('role_id', $selectedRoleId) == 4 ? 'selected' : '' }}>Kullanıcı</option>
                    </select>
                    @error('role_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="neo-form-group">
                    <label for="status" class="neo-label">Durum</label>
                    <label class="inline-flex items-center gap-3 cursor-pointer">
                        <input 
                            type="checkbox" 
                            id="status" 
                            name="status" 
                            value="1" 
                            class="neo-switch" 
                            {{ old('status', $user->status) ? 'checked' : '' }}
                        >
                        <span class="text-sm text-gray-900 dark:text-white">Kullanıcı Aktif</span>
                    </label>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- User Info -->
            <div class="neo-card bg-gray-50 dark:bg-gray-800/50 p-4">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400">Kullanıcı ID</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $user->id }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 dark:text-gray-400">Kayıt Tarihi</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $user->created_at->format('d.m.Y') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 dark:text-gray-400">Son Güncelleme</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $user->updated_at->format('d.m.Y') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 dark:text-gray-400">Mevcut Durum</p>
                        <p class="font-semibold">
                            @if($user->status)
                                <span class="text-green-600 dark:text-green-400">✓ Aktif</span>
                            @else
                                <span class="text-red-600 dark:text-red-400">✗ Pasif</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('admin.kullanicilar.index') }}" class="neo-btn neo-btn-secondary">
                    İptal
                </a>
                <button type="submit" class="neo-btn neo-btn-primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Güncelle
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password confirmation validation
    const password = document.getElementById('password');
    const passwordConfirm = document.getElementById('password_confirmation');

    if (password && passwordConfirm) {
        passwordConfirm.addEventListener('input', function() {
            if (password.value && password.value !== this.value) {
                this.setCustomValidity('Şifreler eşleşmiyor');
            } else {
                this.setCustomValidity('');
            }
        });
    }
});
</script>
@endpush
