@php
    use Illuminate\Support\Facades\Storage;
@endphp

@extends('admin.layouts.neo')

@section('title', $danisman->name . ' - Danışman Düzenle')

@section('content')
    <!-- Neo Page Header -->
    <div class="mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
                    <div class="w-10 h-10 bg-primary-500 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    {{ $danisman->name }} - Düzenle
                </h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">
                    Danışman profil bilgilerini güncelleyin
                </p>
            </div>
            <div class="flex items-center space-x-3">
                <x-neo.button href="{{ route('admin.danisman.show', $danisman) }}" variant="secondary"
                    icon="<svg class='w-4 h-4' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M15 12a3 3 0 11-6 0 3 3 0 016 0z' /><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z' /></svg>">
                    Görüntüle
                </x-neo.button>
                <x-neo.button href="{{ route('admin.danisman.index') }}" variant="secondary"
                    icon="<svg class='w-4 h-4' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M10 19l-7-7m0 0l7-7m-7 7h18' /></svg>">
                    Geri
                </x-neo.button>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.danisman.update', $danisman) }}" enctype="multipart/form-data"
        class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Ana Content -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Temel Bilgiler -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            Temel Bilgiler
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="name"
                                    class="block text-sm font-medium text-gray-900 dark:text-white mb-1">
                                    Ad Soyad <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="name" name="name"
                                    value="{{ old('name', $danisman->name) }}"
                                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 @error('name') border-red-500 @enderror" required
                                    placeholder="Tam ad ve soyad">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email"
                                    class="block text-sm font-medium text-gray-900 dark:text-white mb-1">
                                    E-posta Adresi <span class="text-red-500">*</span>
                                </label>
                                <input type="email" id="email" name="email"
                                    value="{{ old('email', $danisman->email) }}"
                                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 @error('email') border-red-500 @enderror" required
                                    placeholder="ornek@email.com">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="phone_number"
                                    class="block text-sm font-medium text-gray-900 dark:text-white mb-1">
                                    Telefon Numarası
                                </label>
                                <input type="tel" id="phone_number" name="phone_number"
                                    value="{{ old('phone_number', $danisman->phone_number) }}"
                                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 @error('phone_number') border-red-500 @enderror"
                                    placeholder="0532 000 0000">
                                @error('phone_number')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="title"
                                    class="block text-sm font-medium text-gray-900 dark:text-white mb-1">
                                    Ünvan
                                </label>
                                <input type="text" id="title" name="title"
                                    value="{{ old('title', $danisman->title) }}"
                                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 @error('title') border-red-500 @enderror"
                                    placeholder="Emlak Danışmanı">
                                @error('title')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="position"
                                    class="block text-sm font-medium text-gray-900 dark:text-white mb-1">
                                    Pozisyon
                                </label>
                                <input type="text" id="position" name="position"
                                    value="{{ old('position', $danisman->position) }}"
                                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 @error('position') border-red-500 @enderror"
                                    placeholder="Pozisyon bilgisi">
                                @error('position')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="department"
                                    class="block text-sm font-medium text-gray-900 dark:text-white mb-1">
                                    Departman
                                </label>
                                <input type="text" id="department" name="department"
                                    value="{{ old('department', $danisman->department) }}"
                                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 @error('department') border-red-500 @enderror"
                                    placeholder="Departman bilgisi">
                                @error('department')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Uzmanlık Alanları -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            Uzmanlık Alanları
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="expertise_summary"
                                    class="block text-sm font-medium text-gray-900 dark:text-white mb-1">
                                    Uzmanlık Özeti
                                </label>
                                <textarea id="expertise_summary" name="expertise_summary" rows="3"
                                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 resize-vertical @error('expertise_summary') border-red-500 @enderror"
                                    placeholder="Uzmanlık alanlarınızı açıklayın...">{{ old('expertise_summary', $danisman->expertise_summary) }}</textarea>
                                @error('expertise_summary')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="bio"
                                    class="block text-sm font-medium text-gray-900 dark:text-white mb-1">
                                    Biyografi
                                </label>
                                <textarea id="bio" name="bio" rows="3" class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-black dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 resize-vertical @error('bio') border-red-500 @enderror"
                                    placeholder="Kendiniz hakkında bilgi verin...">{{ old('bio', $danisman->bio) }}</textarea>
                                @error('bio')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="office_address"
                                    class="block text-sm font-medium text-gray-900 dark:text-white mb-1">
                                    Ofis Adresi
                                </label>
                                <textarea id="office_address" name="office_address" rows="2"
                                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 resize-vertical @error('office_address') border-red-500 @enderror" placeholder="Ofis adresinizi girin...">{{ old('office_address', $danisman->office_address) }}</textarea>
                                @error('office_address')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="whatsapp_number"
                                    class="block text-sm font-medium text-gray-900 dark:text-white mb-1">
                                    WhatsApp Numarası
                                </label>
                                <input type="tel" id="whatsapp_number" name="whatsapp_number"
                                    value="{{ old('whatsapp_number', $danisman->whatsapp_number) }}"
                                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 @error('whatsapp_number') border-red-500 @enderror"
                                    placeholder="0532 000 0000">
                                @error('whatsapp_number')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Sidebar -->
            <div class="space-y-6">

                <!-- Profil Fotoğrafı -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            Profil Fotoğrafı
                        </h3>

                        @php
                            $pp = $danisman->profile_photo_path;
                            $ppUrl = ($pp && Storage::exists($pp)) ? Storage::url($pp) : null;
                        @endphp
                        @if ($ppUrl)
                            <div class="mb-4">
                                <img src="{{ $ppUrl }}" alt="{{ $danisman->name }}"
                                    class="w-24 h-24 rounded-lg object-cover border-2 border-gray-200"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                <div class="w-24 h-24 rounded-lg border-2 border-gray-200 bg-gray-100 flex items-center justify-center text-gray-500 text-xs" style="display: none;">
                                    <div class="text-center">
                                        <div class="text-2xl mb-1">📷</div>
                                        <div>Fotoğraf</div>
                                        <div>Yüklenemedi</div>
                                    </div>
                                </div>
                            </div>
                        @elseif ($danisman->profile_photo_path)
                            <div class="mb-4">
                                <div class="w-24 h-24 rounded-lg border-2 border-gray-200 bg-gray-100 flex items-center justify-center text-gray-500 text-xs">
                                    <div class="text-center">
                                        <div class="text-2xl mb-1">⚠️</div>
                                        <div>Fotoğraf</div>
                                        <div>Bulunamadı</div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div>
                            <label for="profile_photo"
                                class="block text-sm font-medium text-gray-900 dark:text-white mb-1">
                                Yeni Fotoğraf
                            </label>
                            <input type="file" id="profile_photo" name="profile_photo" accept="image/*"
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 @error('profile_photo') border-red-500 @enderror">
                            @error('profile_photo')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Durum -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            Durum
                        </h3>

                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="checkbox" name="status" value="1"
                                    {{ old('status', $danisman->status) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-900 dark:text-white">Aktif</span>
                            </label>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-3">
            <x-neo.button href="{{ route('admin.danisman.index') }}" variant="ghost">
                İptal
            </x-neo.button>
            <x-neo.button type="submit" variant="primary"
                icon="<svg class='w-4 h-4' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5 13l4 4L19 7' /></svg>">
                Güncelle
            </x-neo.button>
        </div>
    </form>

@endsection
