@extends('admin.layouts.neo')

@section('title', 'Ayarlar')

@section('content')
    <div class="prose max-w-none p-6">
        <!-- Page Header -->
        <div class="mb-6 pb-4 border-b border-gray-200 dark:border-gray-700 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Ayarlar</h1>
                    <p class="text-gray-600 mt-2">Sistem ayarlarını yönetin ve yapılandırın</p>
                </div>
            </div>
        </div>

        <!-- Settings Content -->
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800">
            <ul class="flex border-b mb-6">
                <li class="mr-6"><a class="tab-link active" href="#genel">Genel</a></li>
                <li class="mr-6"><a class="tab-link" href="#bildirim">Bildirimler</a></li>
                <li class="mr-6"><a class="tab-link" href="#portal">Portal Entegrasyonları</a></li>
                <li class="mr-6">
                    <a href="{{ route('admin.ai-settings.index') }}" 
                       class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        AI & Yapay Zeka
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </li>
                <li class="mr-6"><a class="tab-link" href="#fiyat">Fiyatlandırma</a></li>
                <li class="mr-6"><a class="tab-link" href="#qrcode">QR Kod</a></li>
                <li class="mr-6"><a class="tab-link" href="#navigation">Navigasyon</a></li>
                <li><a class="tab-link" href="#kullanici">Kullanıcı Yönetimi</a></li>
            </ul>
            <form method="POST" action="{{ route('admin.ayarlar.bulk-update') }}">
                @csrf
                @method('POST')
                <div id="genel" class="tab-content">
                    <h2 class="text-lg font-semibold mb-2">Genel Ayarlar</h2>
                    <div class="mb-4">
                        <label class="block mb-1 font-medium text-gray-900 dark:text-white">Site Başlığı</label>
                        <input type="text" 
                               name="site_title" 
                               class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               value="{{ $settings['site_title'] ?? '' }}">
                    </div>
                    <div class="mb-4">
                        <label class="block mb-1 font-medium text-gray-900 dark:text-white">Varsayılan Para Birimi</label>
                        <select style="color-scheme: light dark;" 
                                name="default_currency" 
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                            <option value="TRY" @if (($settings['default_currency'] ?? '') == 'TRY') selected @endif>₺ Türk Lirası</option>
                            <option value="USD" @if (($settings['default_currency'] ?? '') == 'USD') selected @endif>$ Dolar</option>
                            <option value="EUR" @if (($settings['default_currency'] ?? '') == 'EUR') selected @endif>€ Euro</option>
                        </select>
                    </div>
                </div>
                <div id="bildirim" class="tab-content hidden">
                    <h2 class="text-lg font-semibold mb-2">Bildirim Ayarları</h2>
                    <div class="mb-4">
                        <label class="block mb-1 font-medium">E-posta Bildirimleri</label>
                        <input type="checkbox" name="email_notifications" @if ($settings['email_notifications'] ?? false) checked @endif>
                        <span class="ml-2">Aktif</span>
                    </div>
                    <div class="mb-4">
                        <label class="block mb-1 font-medium">SMS Bildirimleri</label>
                        <input type="checkbox" name="sms_notifications" @if ($settings['sms_notifications'] ?? false) checked @endif>
                        <span class="ml-2">Aktif</span>
                    </div>
                </div>
                <div id="portal" class="tab-content hidden">
                    <h2 class="text-lg font-semibold mb-2">Portal Entegrasyonları</h2>
                    <div class="mb-4">
                        <label class="block mb-1 font-medium text-gray-900 dark:text-white">Sahibinden.com API Anahtarı</label>
                        <input type="text" 
                               name="sahibinden_api_key" 
                               class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               value="{{ $settings['sahibinden_api_key'] ?? '' }}">
                    </div>
                    <div class="mb-4">
                        <label class="block mb-1 font-medium text-gray-900 dark:text-white">Hepsiemlak API Anahtarı</label>
                        <input type="text" 
                               name="hepsiemlak_api_key" 
                               class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               value="{{ $settings['hepsiemlak_api_key'] ?? '' }}">
                    </div>
                </div>
                {{-- ✅ AI & Yapay Zeka sekmesi kaldırıldı - Duplikasyon önlendi --}}
                {{-- AI ayarları artık /admin/ai-settings sayfasında yönetiliyor --}}
                {{-- Context7 Standard: C7-SINGLE-SOURCE-OF-TRUTH-2025-11-05 --}}
                <div id="fiyat" class="tab-content hidden">
                    <h2 class="text-lg font-semibold mb-2">Fiyatlandırma Ayarları</h2>
                    <div class="mb-4">
                        <label class="block mb-1 font-medium text-gray-900 dark:text-white">Fiyat Yuvarlama</label>
                        <select style="color-scheme: light dark;" 
                                name="price_rounding" 
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                            <option value="none" @if (($settings['price_rounding'] ?? '') == 'none') selected @endif>Yok</option>
                            <option value="nearest_1000" @if (($settings['price_rounding'] ?? '') == 'nearest_1000') selected @endif>En Yakın 1.000
                            </option>
                            <option value="nearest_10000" @if (($settings['price_rounding'] ?? '') == 'nearest_10000') selected @endif>En Yakın
                                10.000</option>
                        </select>
                    </div>
                </div>
                
                {{-- QR Code Settings Tab --}}
                <div id="qrcode" class="tab-content hidden">
                    <h2 class="text-lg font-semibold mb-4">QR Kod Ayarları</h2>
                    
                    <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                        <h3 class="text-md font-semibold text-blue-900 dark:text-blue-100 mb-2">📱 QR Kod Özellikleri</h3>
                        <p class="text-sm text-blue-700 dark:text-blue-300">
                            İlanlar için QR kod oluşturma ve yönetim ayarları. QR kodlar mobil cihazlarla hızlı erişim sağlar.
                        </p>
                    </div>
                    
                    <div class="mb-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="qrcode_enabled" value="1"
                                @if (($settings['qrcode_enabled'] ?? 'true') == 'true' || ($settings['qrcode_enabled'] ?? true) === true) checked @endif
                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <span class="ml-2 font-medium">QR Kod Özelliğini Aktif Et</span>
                        </label>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 ml-6">
                            QR kod özelliğini tüm sistemde aktif/pasif yapar
                        </p>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block mb-1 font-medium">Varsayılan QR Kod Boyutu (Piksel)</label>
                        <input type="number" name="qrcode_default_size" min="100" max="1000" step="50"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                            value="{{ $settings['qrcode_default_size'] ?? '300' }}">
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Önerilen: 200 (küçük), 300 (orta), 400 (büyük)
                        </p>
                    </div>
                    
                    <div class="mb-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="qrcode_show_on_cards" value="1"
                                @if (($settings['qrcode_show_on_cards'] ?? 'true') == 'true' || ($settings['qrcode_show_on_cards'] ?? true) === true) checked @endif
                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <span class="ml-2 font-medium">İlan Kartlarında QR Kod Göster</span>
                        </label>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 ml-6">
                            İlan listesi kartlarında QR kod butonu gösterilir
                        </p>
                    </div>
                    
                    <div class="mb-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="qrcode_show_on_detail" value="1"
                                @if (($settings['qrcode_show_on_detail'] ?? 'true') == 'true' || ($settings['qrcode_show_on_detail'] ?? true) === true) checked @endif
                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <span class="ml-2 font-medium">İlan Detay Sayfasında QR Kod Göster</span>
                        </label>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 ml-6">
                            İlan detay sayfalarında QR kod widget'ı gösterilir
                        </p>
                    </div>
                </div>
                
                {{-- Navigation Settings Tab --}}
                <div id="navigation" class="tab-content hidden">
                    <h2 class="text-lg font-semibold mb-4">Navigasyon Ayarları</h2>
                    
                    <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                        <h3 class="text-md font-semibold text-green-900 dark:text-green-100 mb-2">🧭 İlan Navigasyon Özellikleri</h3>
                        <p class="text-sm text-green-700 dark:text-green-300">
                            İlanlar arasında gezinme, önceki/sonraki ilan ve benzer ilanlar özellikleri.
                        </p>
                    </div>
                    
                    <div class="mb-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="navigation_enabled" value="1"
                                @if (($settings['navigation_enabled'] ?? 'true') == 'true' || ($settings['navigation_enabled'] ?? true) === true) checked @endif
                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <span class="ml-2 font-medium">Navigasyon Özelliğini Aktif Et</span>
                        </label>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 ml-6">
                            İlan navigasyon özelliğini tüm sistemde aktif/pasif yapar
                        </p>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block mb-1 font-medium">Varsayılan Navigasyon Modu</label>
                        <select name="navigation_default_mode" 
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                            <option value="default" @if (($settings['navigation_default_mode'] ?? 'default') == 'default') selected @endif>
                                Varsayılan (Tüm ilanlar)
                            </option>
                            <option value="category" @if (($settings['navigation_default_mode'] ?? '') == 'category') selected @endif>
                                Kategori Bazlı
                            </option>
                            <option value="location" @if (($settings['navigation_default_mode'] ?? '') == 'location') selected @endif>
                                Konum Bazlı
                            </option>
                        </select>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Önceki/sonraki ilan gösterim yöntemi
                        </p>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block mb-1 font-medium">Benzer İlanlar Gösterim Sayısı</label>
                        <input type="number" name="navigation_similar_limit" min="1" max="12" step="1"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                            value="{{ $settings['navigation_similar_limit'] ?? '4' }}">
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Benzer ilanlar bölümünde gösterilecek ilan sayısı (1-12 arası)
                        </p>
                    </div>
                    
                    <div class="mb-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="navigation_show_similar" value="1"
                                @if (($settings['navigation_show_similar'] ?? 'true') == 'true' || ($settings['navigation_show_similar'] ?? true) === true) checked @endif
                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <span class="ml-2 font-medium">Benzer İlanlar Bölümünü Göster</span>
                        </label>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 ml-6">
                            İlan detay sayfalarında benzer ilanlar bölümü gösterilir
                        </p>
                    </div>
                </div>
                
                <div id="kullanici" class="tab-content hidden">
                    <h2 class="text-lg font-semibold mb-2">Kullanıcı Yönetimi</h2>
                    <div class="mb-4">
                        <label class="block mb-1 font-medium">Yeni Kullanıcı Kaydı</label>
                        <input type="checkbox" name="user_registration"
                            @if ($settings['user_registration'] ?? false) checked @endif>
                        <span class="ml-2">Açık</span>
                    </div>
                    <div class="mb-4">
                        <label class="block mb-1 font-medium">Şifre Güçlülük Zorunluluğu</label>
                        <input type="checkbox" name="password_strength"
                            @if ($settings['password_strength'] ?? false) checked @endif>
                        <span class="ml-2">Zorunlu</span>
                    </div>
                </div>
                <div class="mt-6">
                    <button type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Kaydet</button>
                    @if (session('success'))
                        <span class="ml-4 text-green-600">{{ session('success') }}</span>
                    @endif
                </div>
            </form>
        </div>
    </div>
    <script>
        // Basit sekme geçişi
        document.querySelectorAll('.tab-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelectorAll('.tab-link').forEach(l => l.classList.remove('active'));
                this.classList.add('active');
                document.querySelectorAll('.tab-content').forEach(tab => tab.classList.add('hidden'));
                document.querySelector(this.getAttribute('href')).classList.remove('hidden');
            });
        });
    </script>
@endsection
