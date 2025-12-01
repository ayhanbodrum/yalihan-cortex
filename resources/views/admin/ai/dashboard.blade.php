@extends('admin.layouts.admin')

@section('title', 'AI Command Center')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">
                        🤖 AI Command Center
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400">
                        Yapay zeka sistem durumu, fırsat akışı ve aktivite istatistikleri
                    </p>
                </div>
                <div class="flex space-x-3">
                    <button onclick="refreshDashboard()" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all duration-200 shadow-md hover:shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Yenile
                    </button>
                </div>
            </div>
        </div>

        <!-- Bölüm A: Canlı Sistem Durumu -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Cortex Brain -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 hover:shadow-lg transition-all duration-200">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Cortex Brain</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $systemHealth['cortex_brain']['description'] }}</p>
                    </div>
                    <div class="relative">
                        <div class="w-4 h-4 rounded-full {{ $systemHealth['cortex_brain']['status'] === 'online' ? 'bg-green-500' : 'bg-red-500' }} animate-pulse"></div>
                        <div class="absolute inset-0 w-4 h-4 rounded-full {{ $systemHealth['cortex_brain']['status'] === 'online' ? 'bg-green-500' : 'bg-red-500' }} opacity-75 animate-ping"></div>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium {{ $systemHealth['cortex_brain']['status'] === 'online' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                        {{ ucfirst($systemHealth['cortex_brain']['status']) }}
                    </span>
                    <a href="{{ $systemHealth['cortex_brain']['url'] }}" target="_blank" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">
                        {{ parse_url($systemHealth['cortex_brain']['url'], PHP_URL_HOST) }}
                    </a>
                </div>
            </div>

            <!-- LLM Engine -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 hover:shadow-lg transition-all duration-200">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">LLM Engine</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $systemHealth['llm_engine']['description'] }}</p>
                    </div>
                    <div class="relative">
                        <div class="w-4 h-4 rounded-full {{ $systemHealth['llm_engine']['status'] === 'online' ? 'bg-green-500' : 'bg-red-500' }} animate-pulse"></div>
                        <div class="absolute inset-0 w-4 h-4 rounded-full {{ $systemHealth['llm_engine']['status'] === 'online' ? 'bg-green-500' : 'bg-red-500' }} opacity-75 animate-ping"></div>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium {{ $systemHealth['llm_engine']['status'] === 'online' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                        {{ ucfirst($systemHealth['llm_engine']['status']) }}
                    </span>
                    @if($systemHealth['llm_engine']['response_time'])
                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $systemHealth['llm_engine']['response_time'] }}ms
                        </span>
                    @endif
                </div>
            </div>

            <!-- Knowledge Base -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 hover:shadow-lg transition-all duration-200">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Knowledge Base</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $systemHealth['knowledge_base']['description'] }}</p>
                    </div>
                    <div class="relative">
                        <div class="w-4 h-4 rounded-full {{ $systemHealth['knowledge_base']['status'] === 'online' ? 'bg-green-500' : ($systemHealth['knowledge_base']['status'] === 'not_configured' ? 'bg-yellow-500' : 'bg-red-500') }} animate-pulse"></div>
                        <div class="absolute inset-0 w-4 h-4 rounded-full {{ $systemHealth['knowledge_base']['status'] === 'online' ? 'bg-green-500' : ($systemHealth['knowledge_base']['status'] === 'not_configured' ? 'bg-yellow-500' : 'bg-red-500') }} opacity-75 animate-ping"></div>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium {{ $systemHealth['knowledge_base']['status'] === 'online' ? 'text-green-600 dark:text-green-400' : ($systemHealth['knowledge_base']['status'] === 'not_configured' ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400') }}">
                        {{ ucfirst(str_replace('_', ' ', $systemHealth['knowledge_base']['status'])) }}
                    </span>
                    @if($systemHealth['knowledge_base']['response_time'])
                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $systemHealth['knowledge_base']['response_time'] }}ms
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Bölüm B & C: Fırsat Akışı ve AI Aktivitesi -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Bölüm B: Fırsat Akışı (Sol Geniş Alan) -->
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">⚡ Fırsat Akışı</h2>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Son 24 saat</span>
                </div>

                @if(count($opportunityStream) > 0)
                    <div class="space-y-4">
                        @foreach($opportunityStream as $opportunity)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-all duration-200 {{ $opportunity['score'] >= 90 ? 'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-300 dark:border-yellow-700' : '' }}">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="text-xs font-semibold px-2 py-1 rounded {{ $opportunity['score'] >= 90 ? 'bg-yellow-500 text-white' : 'bg-green-500 text-white' }}">
                                                Skor: {{ $opportunity['score'] }}
                                            </span>
                                            @if($opportunity['score'] >= 90)
                                                <span class="text-xs font-semibold px-2 py-1 rounded bg-red-500 text-white animate-pulse">
                                                    ⚠️ ACİL
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">
                                            <span class="font-medium text-gray-900 dark:text-white">
                                                @if($opportunity['type'] === 'ilan_match')
                                                    İlan Eşleşmesi:
                                                @else
                                                    Talep Eşleşmesi:
                                                @endif
                                            </span>
                                            {{ $opportunity['ilan_baslik'] ?? $opportunity['talep_baslik'] ?? 'Bilinmeyen' }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            ⏰ {{ $opportunity['time_ago'] }}
                                        </p>
                                    </div>
                                    <div class="flex flex-col gap-2 ml-4">
                                        @if($opportunity['type'] === 'ilan_match' && isset($opportunity['ilan_id']))
                                            <a href="{{ route('admin.ilanlar.edit', $opportunity['ilan_id']) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md bg-blue-600 hover:bg-blue-700 text-white transition-all duration-200">
                                                Detay Gör
                                            </a>
                                        @elseif($opportunity['type'] === 'talep_match' && isset($opportunity['talep_id']))
                                            <a href="{{ route('admin.talepler.show', $opportunity['talep_id']) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md bg-blue-600 hover:bg-blue-700 text-white transition-all duration-200">
                                                Detay Gör
                                            </a>
                                        @endif
                                        <button onclick="assignToConsultant({{ $opportunity['ilan_id'] ?? $opportunity['talep_id'] }}, '{{ $opportunity['type'] }}')" class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md bg-green-600 hover:bg-green-700 text-white transition-all duration-200">
                                            Danışmana Ata
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 text-gray-400 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400">Son 24 saatte yüksek skorlu eşleşme bulunamadı.</p>
                    </div>
                @endif
            </div>

            <!-- Bölüm C: AI Aktivitesi (Sağ Dar Alan) -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">📊 AI Aktivitesi</h2>

                <div class="space-y-4">
                    <!-- İmar Analizi -->
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-indigo-50 dark:bg-indigo-900/20">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">İmar Analizi</span>
                            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ $usageStats['imar_analizi'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Bugün yapılan analiz</p>
                    </div>

                    <!-- İlan Açıklaması -->
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-green-50 dark:bg-green-900/20">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">İlan Açıklaması</span>
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </div>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $usageStats['ilan_aciklama'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Bugün üretilen açıklama</p>
                    </div>

                    <!-- Fiyat Hesaplama -->
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-purple-50 dark:bg-purple-900/20">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Fiyat Hesaplama</span>
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $usageStats['fiyat_hesaplama'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Bugün hesaplanan fiyat</p>
                    </div>

                    <!-- Token Kullanımı -->
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-orange-50 dark:bg-orange-900/20">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Token Kullanımı</span>
                            <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $usageStats['formatted_tokens'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Bugün harcanan token</p>
                    </div>

                    <!-- Başarı Oranı -->
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-blue-50 dark:bg-blue-900/20">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Başarı Oranı</span>
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $usageStats['success_rate'] }}%</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $usageStats['total_requests'] }} toplam istek</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bölüm D: Queue Worker & Telegram Status -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
            <!-- Queue Worker Status -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">🔄 Queue Worker</h2>
                    <div class="relative">
                        <div class="w-4 h-4 rounded-full {{ $queueStatus['status'] === 'running' ? 'bg-green-500' : ($queueStatus['status'] === 'stopped' ? 'bg-red-500' : 'bg-yellow-500') }} animate-pulse"></div>
                        <div class="absolute inset-0 w-4 h-4 rounded-full {{ $queueStatus['status'] === 'running' ? 'bg-green-500' : ($queueStatus['status'] === 'stopped' ? 'bg-red-500' : 'bg-yellow-500') }} opacity-75 animate-ping"></div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Durum</span>
                        <span class="text-sm font-semibold {{ $queueStatus['status'] === 'running' ? 'text-green-600 dark:text-green-400' : ($queueStatus['status'] === 'stopped' ? 'text-red-600 dark:text-red-400' : 'text-yellow-600 dark:text-yellow-400') }}">
                            {{ $queueStatus['status'] === 'running' ? 'Çalışıyor' : ($queueStatus['status'] === 'stopped' ? 'Durdurulmuş' : 'Bilinmiyor') }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Bekleyen İşler</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $queueStatus['pending_jobs'] }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Son 5 Dakikada İşlenen</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $queueStatus['processed_last_5min'] }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Başarısız (24 saat)</span>
                        <span class="text-sm font-semibold {{ $queueStatus['failed_last_24h'] > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                            {{ $queueStatus['failed_last_24h'] }}
                        </span>
                    </div>

                    @if(isset($queueStatus['error']))
                        <div class="mt-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg">
                            <p class="text-xs text-red-600 dark:text-red-400">{{ $queueStatus['error'] }}</p>
                        </div>
                    @endif

                    @if($queueStatus['status'] === 'stopped')
                        <div class="mt-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg">
                            <p class="text-xs text-yellow-600 dark:text-yellow-400">
                                ⚠️ Queue worker çalışmıyor. Bildirimler işlenmeyecek!
                            </p>
                            <p class="text-xs text-yellow-600 dark:text-yellow-400 mt-1">
                                Başlatmak için: <code class="bg-yellow-100 dark:bg-yellow-800 px-1 rounded">php artisan queue:work --queue=cortex-notifications</code>
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Telegram Notification Stats -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">📱 Telegram Bildirimleri</h2>
                    <div class="relative">
                        <div class="w-4 h-4 rounded-full {{ $telegramStats['is_configured'] ? 'bg-green-500' : 'bg-yellow-500' }} animate-pulse"></div>
                        <div class="absolute inset-0 w-4 h-4 rounded-full {{ $telegramStats['is_configured'] ? 'bg-green-500' : 'bg-yellow-500' }} opacity-75 animate-ping"></div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Yapılandırma</span>
                        <span class="text-sm font-semibold {{ $telegramStats['is_configured'] ? 'text-green-600 dark:text-green-400' : 'text-yellow-600 dark:text-yellow-400' }}">
                            {{ $telegramStats['is_configured'] ? 'Hazır' : 'Eksik' }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Bugün Gönderilen</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $telegramStats['sent_today'] }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Son 24 Saat</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $telegramStats['sent_last_24h'] }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Başarısız (24 saat)</span>
                        <span class="text-sm font-semibold {{ $telegramStats['failed_last_24h'] > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                            {{ $telegramStats['failed_last_24h'] }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Başarı Oranı</span>
                        <span class="text-sm font-semibold {{ $telegramStats['success_rate'] >= 95 ? 'text-green-600 dark:text-green-400' : ($telegramStats['success_rate'] >= 80 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400') }}">
                            {{ $telegramStats['success_rate'] }}%
                        </span>
                    </div>

                    @if(!$telegramStats['is_configured'])
                        <div class="mt-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg">
                            <p class="text-xs text-yellow-600 dark:text-yellow-400">
                                ⚠️ Telegram bot yapılandırılmamış.
                            </p>
                            <p class="text-xs text-yellow-600 dark:text-yellow-400 mt-1">
                                .env dosyasında <code class="bg-yellow-100 dark:bg-yellow-800 px-1 rounded">TELEGRAM_BOT_TOKEN</code> ve <code class="bg-yellow-100 dark:bg-yellow-800 px-1 rounded">TELEGRAM_ADMIN_CHAT_ID</code> tanımlayın.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function refreshDashboard() {
            window.location.reload();
        }

        function assignToConsultant(id, type) {
            // TODO: Danışmana atama modalı aç
            window.Toast.fire({
                icon: 'info',
                title: 'Danışmana atama özelliği yakında eklenecek.'
            });
        }
    </script>
@endsection



