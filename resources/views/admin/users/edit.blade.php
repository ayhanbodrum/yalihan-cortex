@extends('admin.layouts.admin')

@section('title', 'Kullanıcı Düzenle')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Kullanıcı Düzenle</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $user->name }} kullanıcısının bilgilerini
                    güncelleyin</p>
            </div>
            <a href="{{ route('admin.kullanicilar.index') }}"
                class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Geri Dön
            </a>
        </div>

        <!-- Form Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <form method="POST" action="{{ route('admin.kullanicilar.update', $user) }}" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <!-- Basic Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">İsim
                            *</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                            class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 @error('name') border-red-500 @enderror"
                            required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">E-posta
                            *</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                            class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 @error('email') border-red-500 @enderror"
                            required>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Password -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Yeni
                            Şifre</label>
                        <input type="password" id="password" name="password"
                            class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 @error('password') border-red-500 @enderror">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Boş bırakırsanız şifre değişmez</p>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="password_confirmation"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Şifre Tekrarı</label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200">
                    </div>
                </div>

                <!-- Role & Status -->
                <div class="space-y-6">
                    <div class="space-y-2">
                        <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Rol
                            *</label>
                        @php
                            $roles = \Spatie\Permission\Models\Role::all(['id', 'name']);
                            $currentRole = $user->getRoleNames()->first();
                            $roleDescriptions = [
                                'superadmin' => [
                                    'icon' => '👑',
                                    'name' => 'Super Admin',
                                    'desc' => 'Tüm yetkilere sahip süper kullanıcı',
                                    'color' => 'purple',
                                ],
                                'admin' => [
                                    'icon' => '👑',
                                    'name' => 'Admin',
                                    'desc' => 'Tüm yetkilere sahip yönetici',
                                    'color' => 'purple',
                                ],
                                'danisman' => [
                                    'icon' => '👤',
                                    'name' => 'Danışman',
                                    'desc' => 'İlan ekleme, düzenleme ve müşteri yönetimi',
                                    'color' => 'blue',
                                ],
                                'editor' => [
                                    'icon' => '✏️',
                                    'name' => 'Editör',
                                    'desc' => 'İçerik düzenleme ve yayınlama',
                                    'color' => 'green',
                                ],
                                'musteri' => [
                                    'icon' => '👁️',
                                    'name' => 'Müşteri',
                                    'desc' => 'Sadece görüntüleme yetkisi',
                                    'color' => 'gray',
                                ],
                            ];
                            $roleBorderClass = $errors->has('role')
                                ? 'border-2 border-red-500 dark:border-red-500'
                                : 'border border-gray-300 dark:border-gray-600';
                        @endphp
                        <select style="color-scheme: light dark;" name="role" id="role" required
                            class="w-full px-4 py-2.5 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent cursor-pointer transition-colors duration-200 {{ $roleBorderClass }}">
                            <option value="">-- Rol Seçiniz --</option>
                            @foreach ($roles as $role)
                                @php
                                    $roleInfo = $roleDescriptions[$role->name] ?? [
                                        'icon' => '👤',
                                        'name' => ucfirst($role->name),
                                        'desc' => 'Kullanıcı rolü',
                                        'color' => 'gray',
                                    ];
                                @endphp
                                <option value="{{ $role->name }}"
                                    {{ old('role', $currentRole) == $role->name ? 'selected' : '' }}>
                                    {{ $roleInfo['icon'] }} {{ $roleInfo['name'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('role')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        @if (!$currentRole)
                            <p class="mt-1 text-xs text-amber-600 dark:text-amber-400">
                                ⚠️ Bu kullanıcının henüz rolü yok. Lütfen bir rol seçin.
                            </p>
                        @endif
                    </div>

                    {{-- Rol Açıklamaları --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach ($roles as $role)
                            @php
                                $roleInfo = $roleDescriptions[$role->name] ?? [
                                    'icon' => '👤',
                                    'name' => ucfirst($role->name),
                                    'desc' => 'Kullanıcı rolü',
                                    'color' => 'gray',
                                ];
                                $colorClasses = [
                                    'purple' =>
                                        'bg-purple-50 dark:bg-purple-900/20 border-purple-200 dark:border-purple-700 text-purple-700 dark:text-purple-300 text-purple-600 dark:text-purple-400',
                                    'blue' =>
                                        'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-700 text-blue-700 dark:text-blue-300 text-blue-600 dark:text-blue-400',
                                    'green' =>
                                        'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-700 text-green-700 dark:text-green-300 text-green-600 dark:text-green-400',
                                    'gray' =>
                                        'bg-gray-50 dark:bg-gray-900/20 border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-gray-600 dark:text-gray-400',
                                ];
                                $classes = $colorClasses[$roleInfo['color']] ?? $colorClasses['gray'];
                                $parts = explode(' ', $classes);
                            @endphp
                            <div
                                class="{{ $parts[0] }} {{ $parts[1] }} rounded-lg p-4 border {{ $parts[2] }} {{ $parts[3] }}">
                                <h3 class="font-semibold {{ $parts[4] }} {{ $parts[5] }} mb-2">
                                    {{ $roleInfo['icon'] }} {{ $roleInfo['name'] }}</h3>
                                <p class="text-sm {{ $parts[6] }} {{ $parts[7] }}">{{ $roleInfo['desc'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Status -->
                <div class="space-y-2">
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Durum</label>
                    <label class="inline-flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" id="status" name="status" value="1"
                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"
                            {{ old('status', $user->status) ? 'checked' : '' }}>
                        <span class="text-sm text-gray-900 dark:text-white">Kullanıcı Aktif</span>
                    </label>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- User Info -->
                <div
                    class="bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-4">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Kullanıcı ID</p>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $user->id }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Kayıt Tarihi</p>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $user->created_at->format('d.m.Y') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Son Güncelleme</p>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $user->updated_at->format('d.m.Y') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Mevcut Durum</p>
                            <p class="font-semibold">
                                @if ($user->status)
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
                    <a href="{{ route('admin.kullanicilar.index') }}"
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-sm">
                        İptal
                    </a>
                    <button type="submit" id="user-edit-submit-btn"
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100"
                        onsubmit="const btn = document.getElementById('user-edit-submit-btn'); const icon = document.getElementById('user-edit-submit-icon'); const text = document.getElementById('user-edit-submit-text'); const spinner = document.getElementById('user-edit-submit-spinner'); if(btn && icon && text && spinner) { btn.disabled = true; icon.classList.add('hidden'); spinner.classList.remove('hidden'); text.textContent = 'Güncelleniyor...'; }">
                        <svg id="user-edit-submit-icon" class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <svg id="user-edit-submit-spinner" class="hidden w-4 h-4 mr-2 animate-spin" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span id="user-edit-submit-text">Güncelle</span>
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
