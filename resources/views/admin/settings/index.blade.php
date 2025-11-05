@extends('admin.layouts.neo')

@section('title', 'Ayarlar')

@section('content')
    <div class="prose max-w-none p-6">
        <!-- Page Header -->
        <div class="neo-page-header mb-8">
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
                <li class="mr-6"><a class="tab-link" href="#ai">AI & Yapay Zeka</a></li>
                <li class="mr-6"><a class="tab-link" href="#fiyat">Fiyatlandırma</a></li>
                <li><a class="tab-link" href="#kullanici">Kullanıcı Yönetimi</a></li>
            </ul>
            <form method="POST" action="{{ route('admin.settings.update') }}">
                @csrf
                <div id="genel" class="tab-content">
                    <h2 class="text-lg font-semibold mb-2">Genel Ayarlar</h2>
                    <div class="mb-4">
                        <label class="block mb-1 font-medium">Site Başlığı</label>
                        <input type="text" name="site_title" class="admin-input w-full"
                            value="{{ $settings['site_title'] ?? '' }}">
                    </div>
                    <div class="mb-4">
                        <label class="block mb-1 font-medium">Varsayılan Para Birimi</label>
                        <select style="color-scheme: light dark;" name="default_currency" class="admin-input w-full transition-all duration-200">
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
                        <label class="block mb-1 font-medium">Sahibinden.com API Anahtarı</label>
                        <input type="text" name="sahibinden_api_key" class="admin-input w-full"
                            value="{{ $settings['sahibinden_api_key'] ?? '' }}">
                    </div>
                    <div class="mb-4">
                        <label class="block mb-1 font-medium">Hepsiemlak API Anahtarı</label>
                        <input type="text" name="hepsiemlak_api_key" class="admin-input w-full"
                            value="{{ $settings['hepsiemlak_api_key'] ?? '' }}">
                    </div>
                </div>
                <div id="ai" class="tab-content hidden">
                    <h2 class="text-lg font-semibold mb-2">AI & Yapay Zeka Ayarları</h2>

                    {{-- AI Provider Status --}}
                    @if (isset($settings['ai_provider_status']))
                        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                            <h3 class="text-md font-medium mb-3">🤖 AI Sağlayıcı Durumu</h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @foreach ($settings['ai_provider_status'] as $provider => $status)
                                    <div
                                        class="text-center p-3 rounded-lg {{ $status['status_provider'] ? 'bg-green-100 border-2 border-green-500' : 'bg-gray-100' }}">
                                        <div class="font-medium capitalize">{{ $provider }}</div>
                                        <div class="text-sm">
                                            @if ($status['configured'])
                                                <span class="text-green-600">✅ Yapılandırıldı</span>
                                            @else
                                                <span class="text-red-600">❌ Eksik</span>
                                            @endif
                                        </div>
                                        @if ($status['status_provider'])
                                            <div class="text-xs text-green-600 font-bold">AKTİF</div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="mb-4">
                        <label class="block mb-1 font-medium">AI Sağlayıcısı</label>
                        <select style="color-scheme: light dark;" name="ai_provider" class="admin-input w-full transition-all duration-200">
                            <option value="deepseek" @if (($settings['ai_provider'] ?? 'deepseek') == 'deepseek') selected @endif>DeepSeek</option>
                            <option value="openai" @if (($settings['ai_provider'] ?? '') == 'openai') selected @endif>OpenAI GPT</option>
                            <option value="google" @if (($settings['ai_provider'] ?? '') == 'google') selected @endif>Google Gemini</option>
                            <option value="anthropic" @if (($settings['ai_provider'] ?? '') == 'anthropic') selected @endif>Claude (Anthropic)
                            </option>
                            <option value="ollama" @if (($settings['ai_provider'] ?? '') == 'ollama') selected @endif>Ollama (Local)
                            </option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block mb-1 font-medium">OpenAI API Anahtarı</label>
                        <input type="text" name="openai_api_key" class="admin-input w-full"
                            value="{{ $settings['openai_api_key'] ?? '' }}" placeholder="sk-...">
                    </div>
                    <div class="mb-4">
                        <label class="block mb-1 font-medium">DeepSeek API Anahtarı</label>
                        <input type="text" name="deepseek_api_key" class="admin-input w-full"
                            value="{{ $settings['deepseek_api_key'] ?? '' }}" placeholder="sk-...">
                    </div>
                    <div class="mb-4">
                        <label class="block mb-1 font-medium">Google Gemini API Anahtarı</label>
                        <input type="text" name="google_api_key" class="admin-input w-full"
                            value="{{ $settings['google_api_key'] ?? '' }}" placeholder="AIza...">
                    </div>
                    <div class="mb-4">
                        <label class="block mb-1 font-medium">Anthropic Claude API Anahtarı</label>
                        <input type="text" name="anthropic_api_key" class="admin-input w-full"
                            value="{{ $settings['anthropic_api_key'] ?? '' }}" placeholder="sk-ant-...">
                    </div>
                    <div class="mb-4">
                        <label class="block mb-1 font-medium">Otomatik İlan Açıklaması</label>
                        <input type="checkbox" name="ai_auto_description"
                            @if ($settings['ai_auto_description'] ?? false) checked @endif>
                        <span class="ml-2">Aktif</span>
                    </div>
                    <div class="mb-4">
                        <label class="block mb-1 font-medium">Akıllı Etiket Önerileri</label>
                        <input type="checkbox" name="ai_smart_tags" @if ($settings['ai_smart_tags'] ?? false) checked @endif>
                        <span class="ml-2">Aktif</span>
                    </div>
                </div>
                <div id="fiyat" class="tab-content hidden">
                    <h2 class="text-lg font-semibold mb-2">Fiyatlandırma Ayarları</h2>
                    <div class="mb-4">
                        <label class="block mb-1 font-medium">Fiyat Yuvarlama</label>
                        <select style="color-scheme: light dark;" name="price_rounding" class="admin-input w-full transition-all duration-200">
                            <option value="none" @if (($settings['price_rounding'] ?? '') == 'none') selected @endif>Yok</option>
                            <option value="nearest_1000" @if (($settings['price_rounding'] ?? '') == 'nearest_1000') selected @endif>En Yakın 1.000
                            </option>
                            <option value="nearest_10000" @if (($settings['price_rounding'] ?? '') == 'nearest_10000') selected @endif>En Yakın
                                10.000</option>
                        </select>
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
