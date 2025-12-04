<div x-data="videoStatusWidget({{ $ilan->id }}, '{{ route('api.ai.start-video-render', ['ilanId' => $ilan->id]) }}', '{{ route('api.ai.video-status', ['ilanId' => $ilan->id]) }}')"
    x-init="init()"
    class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 border border-gray-200 dark:border-gray-600 flex flex-col justify-between">
    <div class="flex items-center justify-between mb-2">
        <div>
            <div class="text-sm font-semibold text-gray-800 dark:text-gray-100">Video Durumu</div>
            <div class="text-xs text-gray-500 dark:text-gray-400" x-text="statusLabel"></div>
        </div>
        <span class="px-2 py-1 rounded-full text-xs font-medium"
            :class="{
                'bg-gray-200 text-gray-800 dark:bg-gray-900 dark:text-gray-200': status === 'none',
                'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-200': status === 'queued' || status === 'rendering',
                'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200': status === 'completed',
                'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-200': status === 'failed',
            }"
            x-text="status"></span>
    </div>

    <div class="flex-1 mt-2">
        <template x-if="status === 'none'">
            <button @click="start()"
                class="w-full inline-flex items-center justify-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200">
                <span x-show="!loading">Video Oluştur</span>
                <span x-show="loading">Kuyruğa Alınıyor...</span>
            </button>
        </template>

        <template x-if="status === 'queued' || status === 'rendering'">
            <div class="space-y-2">
                <div class="w-full bg-gray-200 dark:bg-gray-800 rounded-full h-2 overflow-hidden">
                    <div class="h-2 bg-blue-500 dark:bg-blue-400 rounded-full transition-all duration-500"
                        :style="`width: ${progress}%`"></div>
                </div>
                <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                    <span x-text="progress + '%'"></span>
                    <span>Video işleniyor...</span>
                </div>
            </div>
        </template>

        <template x-if="status === 'completed' && url">
            <a :href="url" target="_blank" rel="noopener"
                class="w-full inline-flex items-center justify-center px-3 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-emerald-500 transition-all duration-200">
                Videoyu Aç / İndir
            </a>
        </template>

        <template x-if="status === 'failed'">
            <div class="space-y-2">
                <div class="text-xs text-red-600 dark:text-red-400">Video oluşturma başarısız oldu. Tekrar
                    deneyebilirsiniz.</div>
                <button @click="start()"
                    class="w-full inline-flex items-center justify-center px-3 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-red-500 transition-all duration-200">
                    Yeniden Dene
                </button>
            </div>
        </template>
    </div>
</div>

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
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content'),
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
</script>


