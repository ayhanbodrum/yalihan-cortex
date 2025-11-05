{{-- ========================================
     HERO SECTION - EMLAKJET MANTIĞI
     Kategori tabları ve gelişmiş arama
     ======================================== --}}

<section
    class="relative min-h-screen flex items-center justify-center bg-gradient-to-br from-green-600 via-green-700 to-emerald-600 overflow-hidden">
    {{-- Background Image Placeholder --}}
    <div class="absolute inset-0 bg-gradient-to-r from-green-600/90 to-emerald-600/90">
        <div
            class="absolute inset-0 bg-[url('data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 1200 800\"><rect fill=\"%23ffffff\" fill-opacity=\"0.1\" width=\"1200\" height=\"800\"/></svg>')] opacity-20">
        </div>
    </div>

    {{-- Content Container --}}
    <div class="relative z-10 text-center px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        {{-- Main Heading --}}
        <div class="mb-12">
            <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-bold text-white mb-6 leading-tight">
                Bodrum'un En Güzel
                <span class="block text-emerald-200">Emlak Seçenekleri</span>
            </h1>
            <p class="text-lg sm:text-xl md:text-2xl text-green-100 max-w-3xl mx-auto leading-relaxed">
                Hayalinizdeki evi bulmak için modern arama sistemimizi kullanın
            </p>
        </div>

        {{-- Search Tabs --}}
        <div class="mb-8">
            <div class="inline-flex bg-white/20 backdrop-blur-sm rounded-2xl p-2 border border-white/30">
                <button
                    class="px-6 py-3 rounded-xl text-white font-medium transition-all duration-300 hover:bg-white/20"
                    :class="{ 'bg-white/30 shadow-lg': activeTab === 'satilik' }" @click="activeTab = 'satilik'">
                    🏠 Satılık
                </button>
                <button
                    class="px-6 py-3 rounded-xl text-white font-medium transition-all duration-300 hover:bg-white/20"
                    :class="{ 'bg-white/30 shadow-lg': activeTab === 'kiralik' }" @click="activeTab = 'kiralik'">
                    🏡 Kiralık
                </button>
                <button
                    class="px-6 py-3 rounded-xl text-white font-medium transition-all duration-300 hover:bg-white/20"
                    :class="{ 'bg-white/30 shadow-lg': activeTab === 'projeler' }" @click="activeTab = 'projeler'">
                    🏗️ Projeler
                </button>
                <button
                    class="px-6 py-3 rounded-xl text-white font-medium transition-all duration-300 hover:bg-white/20"
                    :class="{ 'bg-white/30 shadow-lg': activeTab === 'ofisler' }" @click="activeTab = 'ofisler'">
                    🏢 Emlak Ofisleri
                </button>
            </div>
        </div>

        {{-- Advanced Search Box --}}
        <div class="bg-white/95 backdrop-blur-lg rounded-3xl p-8 shadow-2xl border border-white/30 max-w-6xl mx-auto">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">🔍 Gelişmiş Emlak Arama</h2>
                <p class="text-gray-600">Aradığınız özellikleri detaylı olarak belirtin</p>
            </div>

            <form action="{{ route('ilanlar.index') }}" method="GET" class="space-y-6" x-data="advancedSearch()">
                {{-- Property Type & Location Row --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2 text-left">🏠 Gayrimenkul
                            Tipi</label>
                        <select name="emlak_turu" class="form-select">
                            <option value="">Seçiniz...</option>
                            <option value="konut">Konut</option>
                            <option value="villa">Villa</option>
                            <option value="arsa">Arsa</option>
                            <option value="ticari">Ticari Emlak</option>
                            <option value="yazlik">Yazlık</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2 text-left">📍 Konum Bilgisi</label>
                        <input type="text" name="location" placeholder="İl, ilçe, mahalle, site, okul, metro..."
                            class="form-input">
                    </div>
                </div>

                {{-- Room Count & Price Row --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2 text-left">🚪 Oda Sayısı</label>
                        <select name="oda_sayisi" class="form-select">
                            <option value="">Oda Giriniz</option>
                            <option value="1+0">1+0</option>
                            <option value="1+1">1+1</option>
                            <option value="2+1">2+1</option>
                            <option value="3+1">3+1</option>
                            <option value="4+1">4+1</option>
                            <option value="5+1">5+1</option>
                            <option value="6+1">6+1</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2 text-left">💰 Fiyat Bilgisi</label>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="number" name="min_fiyat" placeholder="Min Fiyat" class="form-input">
                            <input type="number" name="max_fiyat" placeholder="Max Fiyat" class="form-input">
                        </div>
                    </div>
                </div>

                {{-- Additional Filters Row --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2 text-left">🏢 Bina Yaşı</label>
                        <select name="bina_yasi" class="form-select">
                            <option value="">Seçiniz...</option>
                            <option value="0">Sıfır Bina</option>
                            <option value="1-5">1-5 Yıl</option>
                            <option value="6-10">6-10 Yıl</option>
                            <option value="11-20">11-20 Yıl</option>
                            <option value="20+">20+ Yıl</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2 text-left">📏 Metrekare</label>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="number" name="min_metrekare" placeholder="Min m²" class="form-input">
                            <input type="number" name="max_metrekare" placeholder="Max m²" class="form-input">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2 text-left">🌳 Özellikler</label>
                        <select name="ozellikler" class="form-select">
                            <option value="">Seçiniz...</option>
                            <option value="bahceli">Bahçeli</option>
                            <option value="deniz_manzarali">Deniz Manzaralı</option>
                            <option value="asansorlu">Asansörlü</option>
                            <option value="otoparkli">Otoparklı</option>
                            <option value="güvenlikli">Güvenlikli</option>
                        </select>
                    </div>
                </div>

                {{-- Search Buttons Row --}}
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg px-8 py-4 text-lg">
                        🔍 Ara
                    </button>
                    <button type="button" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-300 bg-white text-gray-700 rounded-lg hover:bg-gray-50 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-500 transition-all duration-200 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700 px-8 py-4 text-lg">
                        🗺️ Haritada Ara
                    </button>
                </div>
            </form>
        </div>

        {{-- Quick Search Links --}}
        <div class="mt-8 flex flex-wrap justify-center gap-4">
            <button
                class="bg-white/20 backdrop-blur-sm text-white px-6 py-3 rounded-full hover:bg-white/30 transition-all duration-300 flex items-center space-x-2">
                <span>⏰</span>
                <span>Satılık Arsa</span>
            </button>
            <button
                class="bg-white/20 backdrop-blur-sm text-white px-6 py-3 rounded-full hover:bg-white/30 transition-all duration-300 flex items-center space-x-2">
                <span>⏰</span>
                <span>Kiralık Ev</span>
            </button>
            <button
                class="bg-white/20 backdrop-blur-sm text-white px-6 py-3 rounded-full hover:bg-white/30 transition-all duration-300 flex items-center space-x-2">
                <span>🏗️</span>
                <span>Yeni Projeler</span>
            </button>
        </div>

        {{-- Company Info --}}
        <div class="mt-12 text-center">
            <div
                class="inline-flex items-center space-x-2 bg-white/20 backdrop-blur-sm rounded-full px-6 py-3 border border-white/30">
                <svg class="w-5 h-5 text-emerald-300" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                        clip-rule="evenodd" />
                </svg>
                <span class="text-white text-sm">Yalıkavak, Bodrum</span>
            </div>
            <div class="mt-4 text-green-100 text-sm">
                <p>📞 0533 209 03 02 | 📧 info@yalihanemlak.com</p>
            </div>
        </div>
    </div>
</section>

<script>
    function advancedSearch() {
        return {
            activeTab: 'satilik',

            init() {
                // URL'den tab bilgisini al
                const urlParams = new URLSearchParams(window.location.search);
                const ilanTuru = urlParams.get('ilan_turu');
                if (ilanTuru === 'satilik') this.activeTab = 'satilik';
                else if (ilanTuru === 'kiralik') this.activeTab = 'kiralik';

                // Tab değişikliğinde form action'ını güncelle
                this.$watch('activeTab', (value) => {
                    const form = this.$el.querySelector('form');
                    if (form) {
                        const url = new URL(form.action);
                        url.searchParams.set('ilan_turu', value);
                        form.action = url.toString();
                    }
                });
            }
        }
    }
</script>
