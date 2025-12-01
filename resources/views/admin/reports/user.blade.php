@extends('admin.layouts.admin')

@section('title', 'Kullanıcı Raporlar - Yalıhan Emlak')

@section('content')
    <div class="min-h-screen">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm mb-8 p-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold bg-gradient-to-r from-green-600 to-blue-600 bg-clip-text text-transparent">
                        📊 Kullanıcı Raporlar
                    </h1>
                    <p class="mt-3 text-lg text-gray-600">
                        Hesap statusu ve aktivite raporları
                    </p>
                </div>
                <div class="flex gap-4">
                    <button class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg active:scale-95 touch-target-optimized touch-target-optimized" onclick="generateAccountReport()">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002 2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                        Hesap Raporum
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="space-y-8">
            <!-- Hesap Durumu Kartları -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Profil Tamamlanma -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Profil Tamamlanma</p>
                            <p class="text-3xl font-bold text-blue-600">{{ $stats['profile_complete'] ?? 0 }}%</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-full">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $stats['profile_complete'] ?? 0 }}%">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hesap Yaşı -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Hesap Yaşı</p>
                            <p class="text-3xl font-bold text-green-600">{{ $stats['account_age'] ?? 0 }}</p>
                            <p class="text-xs text-gray-500">gün</p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-full">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Hesap Durumu -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Hesap Durumu</p>
                            <p class="text-lg font-bold {{ $stats['is_verified'] ? 'text-green-600' : 'text-yellow-600' }}">
                                {{ $stats['is_verified'] ? 'Doğrulanmış' : 'Beklemede' }}
                            </p>
                        </div>
                        <div class="p-3 {{ $stats['is_verified'] ? 'bg-green-100' : 'bg-yellow-100' }} rounded-full">
                            @if ($stats['is_verified'])
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                            @else
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Son Giriş -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Son Giriş</p>
                            <p class="text-lg font-bold text-gray-900">
                                {{ $stats['last_login'] ? \Carbon\Carbon::parse($stats['last_login'])->diffForHumans() : 'İlk giriş' }}
                            </p>
                        </div>
                        <div class="p-3 bg-gray-100 rounded-full">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hesap Aktivite Özeti -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-8">
                <h2 class="stat-card-value mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002 2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                    Hesap Aktivite Özeti
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-8 h-8 text-blue-600 mr-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                    </path>
                                </svg>
                                <div>
                                    <h3 class="font-medium text-gray-900">Profil Durumu</h3>
                                    <p class="text-sm text-gray-600">{{ $stats['profile_complete'] ?? 0 }}% tamamlandı</p>
                                </div>
                            </div>
                            <span
                                class="text-sm font-medium {{ $stats['profile_complete'] >= 80 ? 'text-green-600' : ($stats['profile_complete'] >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ $stats['profile_complete'] >= 80 ? 'İyi' : ($stats['profile_complete'] >= 50 ? 'Orta' : 'Düşük') }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-8 h-8 text-green-600 mr-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                                <div>
                                    <h3 class="font-medium text-gray-900">Üyelik Süresi</h3>
                                    <p class="text-sm text-gray-600">{{ $stats['account_age'] ?? 0 }} gün</p>
                                </div>
                            </div>
                            <span class="text-sm font-medium text-blue-600">Aktif</span>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-8 h-8 text-purple-600 mr-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                                <div>
                                    <h3 class="font-medium text-gray-900">Hesap Doğrulama</h3>
                                    <p class="text-sm text-gray-600">
                                        {{ $stats['is_verified'] ? 'Doğrulanmış' : 'Beklemede' }}</p>
                                </div>
                            </div>
                            <span
                                class="text-sm font-medium {{ $stats['is_verified'] ? 'text-green-600' : 'text-yellow-600' }}">
                                {{ $stats['is_verified'] ? 'Tamamlandı' : 'Bekliyor' }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-8 h-8 text-orange-600 mr-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                                <div>
                                    <h3 class="font-medium text-gray-900">Son Aktivite</h3>
                                    <p class="text-sm text-gray-600">
                                        {{ $stats['last_login'] ? \Carbon\Carbon::parse($stats['last_login'])->diffForHumans() : 'İlk giriş' }}
                                    </p>
                                </div>
                            </div>
                            <span class="text-sm font-medium text-blue-600">Güncel</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profil Tamamlama Rehberi -->
            @if (($stats['profile_complete'] ?? 0) < 100)
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-8">
                    <h2 class="stat-card-value mb-6 flex items-center">
                        <svg class="w-6 h-6 mr-3 text-yellow-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 19.5c-.77.833.192 2.5 1.732 2.5z">
                            </path>
                        </svg>
                        Profil Tamamlama Rehberi
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                                <div class="flex-shrink-0">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                        </path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-medium text-gray-900">Profil Fotoğrafı</h3>
                                    <p class="text-sm text-gray-600">Profil fotoğrafınızı ekleyin</p>
                                </div>
                            </div>

                            <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                                <div class="flex-shrink-0">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                        </path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-medium text-gray-900">Telefon Numarası</h3>
                                    <p class="text-sm text-gray-600">İletişim bilgilerinizi güncelleyin</p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                                <div class="flex-shrink-0">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2V6">
                                        </path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-medium text-gray-900">Unvan</h3>
                                    <p class="text-sm text-gray-600">Mesleki unvanınızı belirtin</p>
                                </div>
                            </div>

                            <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                                <div class="flex-shrink-0">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-medium text-gray-900">Biyografi</h3>
                                    <p class="text-sm text-gray-600">Kendinizi tanıtın</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 text-center">
                        <a href="{{ url('/profile') }}" class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg active:scale-95 touch-target-optimized touch-target-optimized">
                            Profilimi Tamamla
                        </a>
                    </div>
                </div>
            @endif

            <!-- Hızlı İşlemler -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-8">
                <h2 class="stat-card-value mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z">
                        </path>
                    </svg>
                    Hızlı İşlemler
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <a href="{{ url('/profile') }}"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-300 bg-white text-gray-700 rounded-lg hover:bg-gray-50 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-500 transition-all duration-200 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700 justify-start touch-target-optimized touch-target-optimized">
                        <svg class="w-8 h-8 text-blue-600 mr-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                            </path>
                        </svg>
                        <div>
                            <h3 class="font-medium text-gray-900">Profil Düzenle</h3>
                            <p class="text-sm text-gray-600">Bilgilerinizi güncelleyin</p>
                        </div>
                    </a>

                    <button onclick="exportAccountReport()"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-300 bg-white text-gray-700 rounded-lg hover:bg-gray-50 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-500 transition-all duration-200 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700 justify-start touch-target-optimized touch-target-optimized">
                        <svg class="w-8 h-8 text-green-600 mr-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        <div>
                            <h3 class="font-medium text-gray-900">Hesap Raporu</h3>
                            <p class="text-sm text-gray-600">Hesap bilgilerinizi dışa aktarın</p>
                        </div>
                    </button>

                    <a href="{{ route('ayarlar.index') }}"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-300 bg-white text-gray-700 rounded-lg hover:bg-gray-50 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-500 transition-all duration-200 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700 justify-start touch-target-optimized touch-target-optimized">
                        <svg class="w-8 h-8 text-purple-600 mr-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                            </path>
                        </svg>
                        <div>
                            <h3 class="font-medium text-gray-900">Ayarlar</h3>
                            <p class="text-sm text-gray-600">Hesap ayarları</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function generateAccountReport() {
                // Hesap raporu oluşturma
                console.log('Hesap raporu oluşturuluyor...');
            }

            function exportAccountReport() {
                // Hesap raporunu dışa aktarma
                console.log('Hesap raporu dışa aktarılıyor...');
            }
        </script>
    @endpush
@endsection
