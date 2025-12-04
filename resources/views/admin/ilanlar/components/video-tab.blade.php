{{-- Video Sekmesi - AraziPro Tasarımı Referanslı --}}
<div class="space-y-6">
    {{-- Ana Grid: Sol Panel (Video Kayıt Kartı) + Sağ Panel (Harita) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- SOL PANEL: Video Kayıt Kartı (AraziPro Referanslı) --}}
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-lg p-6">
                {{-- Başlık --}}
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Video Kayıt</h3>
                </div>

                {{-- Çözünürlük Seçenekleri (Statik Bilgi) --}}
                <div class="mb-6">
                    <div class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Çözünürlük:</div>
                    <div class="space-y-2">
                        <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                            <div class="w-2 h-2 rounded-full bg-gray-400"></div>
                            <span>720p (Hızlı)</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-900 dark:text-gray-100 font-medium">
                            <div class="w-2 h-2 rounded-full bg-blue-600"></div>
                            <span>1080p (Kaliteli)</span>
                        </div>
                    </div>
                </div>

                {{-- Video Oluştur Butonu (Büyük Kırmızı) --}}
                <div x-data="videoStatusWidget({{ $ilan->id }}, '{{ route('api.ai.start-video-render', ['ilanId' => $ilan->id]) }}', '{{ route('api.ai.video-status', ['ilanId' => $ilan->id]) }}')" x-init="init()" class="mb-6">
                    <template x-if="status === 'none'">
                        <button @click="start()" :disabled="loading"
                            class="w-full inline-flex items-center justify-center gap-2 px-6 py-4 bg-red-600 hover:bg-red-700 text-white text-base font-semibold rounded-xl shadow-lg hover:shadow-xl hover:scale-105 active:scale-95 focus:ring-4 focus:ring-red-500/50 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200">
                            <svg x-show="!loading" class="w-5 h-5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            <svg x-show="loading" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span x-text="loading ? 'Kuyruğa Alınıyor...' : 'Sesli Video Kaydı Başlat'"></span>
                        </button>
                    </template>

                    <template x-if="status === 'queued' || status === 'rendering'">
                        <div class="space-y-3">
                            <div
                                class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden shadow-inner">
                                <div class="h-3 bg-gradient-to-r from-blue-500 to-blue-600 dark:from-blue-400 dark:to-blue-500 rounded-full transition-all duration-500 shadow-sm"
                                    :style="`width: ${progress}%`"></div>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400 font-medium"
                                    x-text="progress + '%'"></span>
                                <span class="text-gray-500 dark:text-gray-500"
                                    x-text="status === 'queued' ? 'Kuyrukta bekleniyor...' : 'Video işleniyor...'"></span>
                            </div>
                        </div>
                    </template>

                    <template x-if="status === 'completed' && url">
                        <a :href="url" target="_blank" rel="noopener"
                            class="w-full inline-flex items-center justify-center gap-2 px-6 py-4 bg-emerald-600 hover:bg-emerald-700 text-white text-base font-semibold rounded-xl shadow-lg hover:shadow-xl hover:scale-105 active:scale-95 focus:ring-4 focus:ring-emerald-500/50 transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            <span>Videoyu Aç / İndir</span>
                        </a>
                    </template>

                    <template x-if="status === 'failed'">
                        <div class="space-y-3">
                            <div
                                class="text-sm text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 p-3 rounded-lg">
                                Video oluşturma başarısız oldu. Tekrar deneyebilirsiniz.
                            </div>
                            <button @click="start()"
                                class="w-full inline-flex items-center justify-center gap-2 px-6 py-4 bg-red-600 hover:bg-red-700 text-white text-base font-semibold rounded-xl shadow-lg hover:shadow-xl hover:scale-105 active:scale-95 focus:ring-4 focus:ring-red-500/50 transition-all duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                <span>Yeniden Dene</span>
                            </button>
                        </div>
                    </template>
                </div>

                {{-- Özellikler Listesi (AraziPro Referanslı) --}}
                <div class="space-y-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex items-start gap-3 text-sm text-gray-600 dark:text-gray-400">
                        <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span><strong class="text-gray-900 dark:text-white">TKGM + POI + Yalihan Cortex</strong> Video
                            Script</span>
                    </div>
                    <div class="flex items-start gap-3 text-sm text-gray-600 dark:text-gray-400">
                        <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span><strong class="text-gray-900 dark:text-white">Sesli anlatım</strong> + TTS
                            (ElevenLabs)</span>
                    </div>
                    <div class="flex items-start gap-3 text-sm text-gray-600 dark:text-gray-400">
                        <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span><strong class="text-gray-900 dark:text-white">1920×1080</strong> • 60 FPS • ~45
                            saniye</span>
                    </div>
                    <div class="flex items-start gap-3 text-sm text-gray-600 dark:text-gray-400">
                        <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Arsa merkezinde <strong class="text-gray-900 dark:text-white">3 tur 360° dönüş</strong>
                            (gelecek özellik)</span>
                    </div>
                    <div class="flex items-start gap-3 text-sm text-gray-600 dark:text-gray-400">
                        <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span><strong class="text-gray-900 dark:text-white">1 saniyelik smooth fade</strong>
                            geçişleri</span>
                    </div>
                    <div class="flex items-start gap-3 text-sm text-amber-600 dark:text-amber-400">
                        <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <span>Düşük sistemde capture süresi uzayabilir (video hep ~45s)</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- SAĞ PANEL: Harita Görünümü (2/3 genişlik) --}}
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-lg overflow-hidden relative"
                style="height: 600px;">
                {{-- Harita Container --}}
                <div class="absolute inset-0">
                    @include('admin.ilanlar.components.location-map', [
                        'ilan' => $ilan,
                        'iller' => $iller ?? collect(),
                        'ilceler' => $ilceler ?? collect(),
                        'mahalleler' => $mahalleler ?? collect(),
                    ])
                </div>

                {{-- Lokasyon Bilgileri Overlay (AraziPro Referanslı) - Üst Sol --}}
                <div class="absolute top-4 left-4 flex flex-col gap-2 z-10">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 px-3 py-2">
                        <div class="flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span class="font-medium text-gray-900 dark:text-white">
                                {{ optional($ilan->il)->il_adi }}{{ optional($ilan->ilce)->ilce_adi ? ' / ' . $ilan->ilce->ilce_adi : '' }}
                            </span>
                        </div>
                    </div>
                    @if ($ilan->mahalle)
                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 px-3 py-2">
                            <div class="flex items-center gap-2 text-sm">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span
                                    class="font-medium text-gray-900 dark:text-white">{{ $ilan->mahalle->name }}</span>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Danışman Kartı Overlay (AraziPro Referanslı) - Alt Sol --}}
                @if ($ilan->userDanisman)
                    <div
                        class="absolute bottom-4 left-4 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 p-4 z-10 max-w-xs">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                    {{ $ilan->userDanisman->name }}
                                </div>
                                @if ($ilan->userDanisman->phone_number)
                                    <a href="tel:{{ $ilan->userDanisman->phone_number }}"
                                        class="inline-flex items-center gap-1 text-sm text-blue-600 dark:text-blue-400 hover:underline mt-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                        {{ $ilan->userDanisman->phone_number }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ALT BÖLÜM: Ek Özellikler --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Sosyal Medya Gönderisi Oluştur --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-lg p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Sosyal Medya Gönderisi</h3>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Bu ilan için Instagram, Facebook ve LinkedIn'e uygun sosyal medya gönderisi oluşturun.
            </p>
            <button onclick="generateSocialPost({{ $ilan->id }})"
                class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-purple-600 hover:bg-purple-700 text-white text-sm font-semibold rounded-lg shadow-md hover:shadow-lg hover:scale-105 active:scale-95 focus:ring-2 focus:ring-purple-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                <span>Sosyal Medya Gönderisi Oluştur</span>
            </button>
        </div>

        {{-- Pazar Analizi Metni Oluştur --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-lg p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Pazar Analizi</h3>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                TKGM verileri ve bölge analizi kullanarak profesyonel pazar analizi metni oluşturun.
            </p>
            <button onclick="generateMarketAnalysis({{ $ilan->id }})"
                class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-md hover:shadow-lg hover:scale-105 active:scale-95 focus:ring-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                <span>Pazar Analizi Metni Oluştur</span>
            </button>
        </div>
    </div>
</div>

{{-- Video Status Widget Script (Mevcut widget'tan kopyalandı) --}}
<script>
    function videoStatusWidget(ilanId, startUrl, statusUrl) {
        return {
            status: 'none',
            progress: 0,
            url: '',
            loading: false,
            statusLabel: '',
            init() {
                this.fetchStatus();
                setInterval(() => this.fetchStatus(), 5000);
            },
            fetchStatus() {
                fetch(statusUrl, {
                    headers: {
                        'Accept': 'application/json'
                    }
                }).then(r => r.json()).then(data => {
                    if (!data || data.success === false) return;
                    const payload = data.data || data;
                    this.status = payload.video_status || 'none';
                    this.progress = payload.video_last_frame || 0;
                    this.url = payload.video_url || '';
                    this.updateLabel();
                }).catch(() => {});
            },
            start() {
                this.loading = true;
                fetch(startUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content'),
                        'Accept': 'application/json'
                    }
                }).then(r => r.json()).then(data => {
                    this.loading = false;
                    if (!data || data.success === false) return;
                    const payload = data.data || data;
                    this.status = payload.video_status || 'queued';
                    this.progress = 0;
                    this.updateLabel();
                }).catch(() => {
                    this.loading = false;
                });
            },
            updateLabel() {
                if (this.status === 'none') this.statusLabel = 'Video henüz oluşturulmadı.';
                else if (this.status === 'queued') this.statusLabel = 'Video kuyruğa alındı.';
                else if (this.status === 'rendering') this.statusLabel = 'Video işleniyor...';
                else if (this.status === 'completed') this.statusLabel = 'Video hazır.';
                else if (this.status === 'failed') this.statusLabel = 'Video oluşturma başarısız.';
            }
        }
    }

    // Placeholder functions for social media and market analysis
    function generateSocialPost(ilanId) {
        alert('Sosyal medya gönderisi oluşturma özelliği yakında eklenecek. İlan ID: ' + ilanId);
    }

    function generateMarketAnalysis(ilanId) {
        alert('Pazar analizi metni oluşturma özelliği yakında eklenecek. İlan ID: ' + ilanId);
    }
</script>
