@extends('admin.layouts.neo')

@section('title', 'AI Monitoring Dashboard')

@section('content')
    <div class="neo-container neo-mx-auto p-6" x-data="monitorUI()" x-init="init()">
        <!-- Header -->
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <h1 class="neo-text-2xl neo-font-bold">AI Monitoring</h1>
                <span class="text-sm neo-text-gray-500">(Yerel Geliştirici İzleme)</span>
            </div>
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2">
                    <span class="text-sm">Genel Durum:</span>
                    <span :class="overallBadgeClass()"
                        class="neo-badge rounded-lg neo-px-2 neo-py-1 text-xs neo-transition-all neo-duration-300">
                        <span x-show="overall?.level === 'green'">🟢 İyi</span>
                        <span x-show="overall?.level === 'yellow'">🟡 Uyarı</span>
                        <span x-show="overall?.level === 'red'">🔴 Kritik</span>
                        <span x-show="!overall?.level || overall?.level === 'unknown'">⚪ Bilinmiyor</span>
                    </span>
                </div>
                <div class="neo-hidden md:flex items-center gap-2">
                    <label class="text-xs flex items-center gap-1">
                        <input type="checkbox" x-model="autoRefresh" class="w-5 h-5 text-blue-600 bg-gray-50 dark:bg-gray-800 border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-all duration-200 cursor-pointer neo-h-3.5 neo-w-3.5" />
                        Otomatik
                    </label>
                    <select style="color-scheme: light dark;" x-model.number="refreshInterval" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 neo-h-7 text-xs transition-all duration-200">
                        <option :value="15000">15s</option>
                        <option :value="30000">30s</option>
                        <option :value="60000">60s</option>
                    </select>
                    <span class="text-xs neo-text-gray-500" x-show="lastUpdated" x-text="'Son: ' + lastUpdated"></span>
                </div>
                <button @click="refreshAll()" class="neo-btn neo-btn neo-btn-primary text-xs touch-target-optimized touch-target-optimized">Yenile</button>
            </div>
        </div>

        <!-- Overview + Mini Usage Chart -->
        <div class="neo-grid neo-grid-cols-1 lg:neo-grid-cols-3 gap-6 mb-6">
            <div class="neo-card p-4 lg:neo-col-span-2">
                <div class="neo-grid neo-grid-cols-4 gap-4">
                    <div
                        class="neo-bg-gradient-to-r neo-from-blue-50 neo-to-blue-100 rounded-lg p-3 neo-transition-all neo-duration-300 hover:neo-shadow-md">
                        <div class="text-xs neo-text-blue-600 flex items-center gap-1">
                            <svg class="neo-w-3.5 neo-h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z" />
                            </svg>
                            Aktif MCP
                        </div>
                        <div class="neo-text-2xl neo-font-semibold neo-text-blue-800" x-text="overall?.mcp_count ?? 0">
                        </div>
                    </div>
                    <div
                        class="neo-bg-gradient-to-r neo-from-green-50 neo-to-green-100 rounded-lg p-3 neo-transition-all neo-duration-300 hover:neo-shadow-md">
                        <div class="text-xs neo-text-green-600 flex items-center gap-1">
                            <svg class="neo-w-3.5 neo-h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                            API OK
                        </div>
                        <div class="neo-text-2xl neo-font-semibold neo-text-green-800" x-text="overall?.api_ok ?? 0"></div>
                    </div>
                    <div
                        class="neo-bg-gradient-to-r neo-from-purple-50 neo-to-purple-100 rounded-lg p-3 neo-transition-all neo-duration-300 hover:neo-shadow-md">
                        <div class="text-xs neo-text-purple-600 flex items-center gap-1">
                            <svg class="neo-w-3.5 neo-h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            Uptime %
                        </div>
                        <div class="neo-text-2xl neo-font-semibold neo-text-purple-800" x-text="uptimePercent() + '%'">
                        </div>
                    </div>
                    <div
                        class="neo-bg-gradient-to-r neo-from-orange-50 neo-to-orange-100 rounded-lg p-3 neo-transition-all neo-duration-300 hover:neo-shadow-md">
                        <div class="text-xs neo-text-orange-600 flex items-center gap-1">
                            <svg class="neo-w-3 neo-h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                    clip-rule="evenodd" />
                            </svg>
                            Ort. Latency
                        </div>
                        <div class="neo-text-2xl neo-font-semibold neo-text-orange-800" x-text="avgLatency() + 'ms'"></div>
                    </div>
                </div>
            </div>
            <div class="neo-card p-4">
                <div class="flex items-center justify-between mb-2">
                    <h2 class="neo-font-semibold">MCP Kullanım Mini-Chart</h2>
                    <button @click="refreshMcp()" class="neo-btn neo-btn neo-btn-secondary text-xs touch-target-optimized touch-target-optimized">Yenile</button>
                </div>
                <div class="neo-space-y-2" x-show="overall?.mcp_usage">
                    <template x-for="[key, count] in sortedUsage()" :key="key">
                        <div>
                            <div class="flex justify-between text-xs neo-text-gray-600">
                                <span class="neo-font-mono" x-text="key"></span>
                                <span x-text="count"></span>
                            </div>
                            <div class="neo-w-full neo-h-2 neo-bg-gray-200 rounded-lg">
                                <div class="neo-h-2 rounded-lg" :class="mcpTypeColor(key)"
                                    :style="{ width: usageWidth(count) }">
                                </div>
                            </div>
                        </div>
                    </template>
                    <div x-show="!overall?.mcp_usage" class="text-xs neo-text-gray-400">Veri yok</div>
                </div>
            </div>
        </div>

        <!-- MCP + API -->
        <div class="neo-grid neo-grid-cols-1 md:neo-grid-cols-2 gap-6">
            <!-- MCP Table -->
            <div class="neo-card p-4">
                <div class="flex items-center justify-between mb-2">
                    <h2 class="neo-font-semibold">MCP Server Durumu</h2>
                    <button @click="refreshMcp()" class="neo-btn neo-btn neo-btn-secondary text-xs touch-target-optimized touch-target-optimized">Yenile</button>
                </div>
                <div class="neo-overflow-x-auto">
                    <!-- Skeleton Loader -->
                    <div x-show="loadingMcp" class="neo-space-y-2 mb-2">
                        <div class="neo-animate-pulse neo-h-6 neo-bg-gray-100 rounded-lg"></div>
                        <div class="neo-animate-pulse neo-h-6 neo-bg-gray-100 rounded-lg"></div>
                        <div class="neo-animate-pulse neo-h-6 neo-bg-gray-100 rounded-lg"></div>
                    </div>
                    <table class="neo-table neo-w-full text-xs">
                        <thead>
                            <tr>
                                <th>Kullanıcı</th>
                                <th>PID</th>
                                <th>CPU</th>
                                <th>MEM</th>
                                <th>Komut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-if="mcp.length === 0">
                                <tr>
                                    <td colspan="5" class="neo-text-center neo-text-gray-400">Kayıt yok</td>
                                </tr>
                            </template>
                            <template x-for="proc in mcp" :key="proc.pid">
                                <tr>
                                    <td x-text="proc.user"></td>
                                    <td x-text="proc.pid"></td>
                                    <td x-text="proc.cpu"></td>
                                    <td x-text="proc.mem"></td>
                                    <td class="neo-break-all" x-text="proc.command"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- API Health -->
            <div class="neo-card p-4">
                <div class="flex items-center justify-between mb-2">
                    <h2 class="neo-font-semibold">API Health Check</h2>
                    <button @click="refreshApis()" class="neo-btn neo-btn neo-btn-secondary text-xs touch-target-optimized touch-target-optimized">Yenile</button>
                </div>
                <ul class="neo-space-y-2">
                    <template x-for="(status, name) in apis" :key="name">
                        <li
                            class="flex items-center justify-between text-sm neo-bg-gray-50 rounded-lg neo-px-2 neo-py-1">
                            <span class="neo-font-mono" x-text="name"></span>
                            <div class="flex items-center gap-2">
                                <span :class="statusBadgeClass(status?.status)"
                                    x-text="status?.status || 'UNKNOWN'"></span>
                                </span>
                                <span class="text-xs neo-text-gray-500" x-show="status?.latency_ms"
                                    x-text="(status?.latency_ms || '') + 'ms'"></span>
                                <span class="text-xs neo-text-gray-500" x-show="status?.http_code"
                                    x-text="'HTTP ' + (status?.http_code || '')"></span>
                            </div>
                        </li>
                    </template>
                    <li x-show="Object.keys(apis).length===0" class="neo-text-center neo-text-gray-400 text-sm">Veri
                        yok
                    </li>
                </ul>
            </div>
        </div>

        <!-- Ekosistem Analizi -->
        <div class="neo-grid neo-grid-cols-1 lg:neo-grid-cols-3 gap-6 neo-mt-6">
            <!-- Context7 Uyumluluk Durumu -->
            <div class="neo-card p-4">
                <div class="flex items-center justify-between mb-2">
                    <h2 class="neo-font-semibold flex items-center gap-2">
                        <svg class="neo-w-3.5 neo-h-3.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        Context7 Uyumluluk
                    </h2>
                    <button @click="refreshCodeHealth()" class="neo-btn neo-btn neo-btn-secondary text-xs touch-target-optimized touch-target-optimized">Kontrol Et</button>
                </div>
                <div class="neo-space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm">Uyumluluk:</span>
                        <span class="neo-badge neo-px-2 neo-py-1" :class="complianceBadgeClass()">
                            @{{ codeHealth?.compliance_status === 'compliant' ? 'Uyumlu' : 'Uyumsuz' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm">Sağlık Skoru:</span>
                        <span class="neo-badge neo-px-2 neo-py-1"
                            :class="codeHealthBadgeClass()">@{{ codeHealth?.health_score ?? 0 }}%</span>
                    </div>

                    <!-- Context7 Önerileri -->
                    <div x-show="codeHealth?.suggestions?.length > 0" class="neo-mt-3">
                        <div class="text-xs neo-font-semibold neo-text-orange-700 mb-1">📋 Öneriler:</div>
                        <template x-for="suggestion in (codeHealth?.suggestions || [])" :key="suggestion">
                            <div class="text-xs neo-bg-orange-50 neo-text-orange-700 rounded-lg p-2 mb-1">
                                @{{ suggestion }}
                            </div>
                        </template>
                    </div>

                    <!-- Sorunlar Listesi -->
                    <div x-show="codeHealth?.issues?.length > 0" class="neo-space-y-1 neo-mt-2">
                        <template x-for="issue in (codeHealth?.issues || [])" :key="issue.type">
                            <div class="text-xs neo-bg-gray-50 rounded-lg p-2">
                                <div class="flex justify-between neo-items-start">
                                    <div class="flex-1">
                                        <span class="neo-font-semibold">@{{ issueTypeLabel(issue.type) }}</span>
                                        <div class="neo-text-gray-500 neo-mt-1" x-show="issue.description">
                                            @{{ issue.description || '' }}</div>
                                        <div x-show="issue.suggestion" class="neo-text-blue-600 neo-mt-1 text-xs">
                                            💡 @{{ issue.suggestion }}
                                        </div>
                                    </div>
                                    <span class="neo-font-semibold neo-ml-2"
                                        :class="severityClass(issue.severity)">@{{ issue.count }}</span>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Aksiyon Gerekli Uyarısı -->
                    <div x-show="codeHealth?.action_required"
                        class="neo-bg-red-50 neo-border neo-border-red-200 rounded-lg p-2 neo-mt-3">
                        <div class="flex items-center gap-2">
                            <svg class="neo-w-3.5 neo-h-3.5 neo-text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="text-xs neo-text-red-700 neo-font-semibold">Acil aksiyon gerekli!</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Duplike Dosyalar -->
            <div class="neo-card p-4">
                <div class="flex items-center justify-between mb-2">
                    <h2 class="neo-font-semibold flex items-center gap-2">
                        <svg class="neo-w-3.5 neo-h-3.5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M8 2a1 1 0 000 2h2a1 1 0 100-2H8z" />
                            <path
                                d="M3 5a2 2 0 012-2 3 3 0 003 3h6a3 3 0 003-3 2 2 0 012 2v6h-4.586l1.293-1.293a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L10.414 13H15v3a2 2 0 01-2 2H5a2 2 0 01-2-2V5zM15 11.586l-1.293 1.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 00-1.414 1.414L15 8.414V11.586z" />
                        </svg>
                        Duplike Dosyalar
                    </h2>
                    <button @click="refreshDuplicates()" class="neo-btn neo-btn neo-btn-secondary text-xs touch-target-optimized touch-target-optimized">Yenile</button>
                </div>
                <div class="neo-space-y-2 neo-max-h-48 neo-overflow-y-auto">
                    <template x-for="dup in duplicateFiles" :key="dup.name">
                        <div class="text-xs neo-bg-yellow-50 rounded-lg p-2">
                            <div class="neo-font-semibold neo-text-yellow-800">@{{ dup.name }}</div>
                            <div class="neo-text-yellow-600">@{{ dup.count }} dosya</div>
                        </div>
                    </template>
                    <div x-show="duplicateFiles.length === 0" class="text-xs neo-text-gray-400">Duplike dosya yok
                    </div>
                </div>
            </div>

            <!-- Çakışan Rotalar -->
            <div class="neo-card p-4">
                <div class="flex items-center justify-between mb-2">
                    <h2 class="neo-font-semibold flex items-center gap-2">
                        <svg class="neo-w-3.5 neo-h-3.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                        Rota Çakışmaları
                    </h2>
                    <button @click="refreshConflicts()" class="neo-btn neo-btn neo-btn-secondary text-xs touch-target-optimized touch-target-optimized">Yenile</button>
                </div>
                <div class="neo-space-y-2 neo-max-h-48 neo-overflow-y-auto">
                    <template x-for="conflict in conflictingRoutes" :key="conflict.uri_methods">
                        <div class="text-xs neo-bg-red-50 rounded-lg p-2">
                            <div class="neo-font-semibold neo-text-red-800">@{{ conflict.uri_methods }}</div>
                            <div class="neo-text-red-600">@{{ conflict.count }} çakışma</div>
                        </div>
                    </template>
                    <div x-show="conflictingRoutes.length === 0" class="text-xs neo-text-gray-400">Çakışma yok</div>
                </div>
            </div>

            <!-- Sayfa Sağlığı -->
            <div class="neo-card p-4 lg:neo-col-span-3">
                <div class="flex items-center justify-between mb-2">
                    <h2 class="neo-font-semibold flex items-center gap-2">
                        <svg class="neo-w-3.5 neo-h-3.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-9V7a1 1 0 112 0v2h2a1 1 0 110 2h-2v2a1 1 0 11-2 0v-2H7a1 1 0 110-2h2z"
                                clip-rule="evenodd" />
                        </svg>
                        Sayfa Sağlığı
                    </h2>
                    <button @click="refreshPagesHealth()" class="neo-btn neo-btn neo-btn-secondary text-xs touch-target-optimized touch-target-optimized">Yenile</button>
                </div>
                <div class="neo-overflow-x-auto">
                    <table class="neo-table neo-w-full text-xs">
                        <thead>
                            <tr>
                                <th>Sayfa</th>
                                <th>URL</th>
                                <th>Durum</th>
                                <th>HTTP</th>
                                <th>Latency</th>
                                <th>İşaretler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="row in pagesHealth" :key="row.url">
                                <tr>
                                    <td class="neo-font-semibold">@{{ row.name }}</td>
                                    <td class="neo-font-mono neo-text-[11px] neo-max-w-[360px] neo-truncate"
                                        :title="row.url">@{{ row.url }}</td>
                                    <td><span :class="statusBadgeClass(row.status)">@{{ row.status }}</span></td>
                                    <td>@{{ row.http_code ?? '—' }}</td>
                                    <td>@{{ row.latency_ms ?? '—' }}ms</td>
                                    <td>
                                        <span x-show="row.markers_found"
                                            class="neo-badge neo-bg-green-200 neo-text-green-800 neo-mr-1">Bulundu</span>
                                        <span x-show="!row.markers_found"
                                            class="neo-badge neo-bg-yellow-200 neo-text-yellow-800 neo-mr-1">Eksik</span>
                                        <template x-for="m in (row.missing_markers || [])" :key="m">
                                            <span
                                                class="neo-badge neo-bg-gray-200 neo-text-gray-800 neo-mr-1">@{{ m }}</span>
                                        </template>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="pagesHealth.length === 0">
                                <td colspan="6" class="neo-text-center neo-text-gray-400">Kayıt yok</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Logs -->
        <div class="neo-grid neo-grid-cols-1 md:neo-grid-cols-2 gap-6 neo-mt-6">
            <!-- Context7 Öğretim ve Öneri Paneli -->
            <div x-show="codeHealth?.action_required || codeHealth?.suggestions?.length > 0" class="neo-mt-6">
                <div class="neo-card p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="neo-font-bold neo-text-lg flex items-center gap-2">
                            🎓 Context7 Öğretim ve Öneriler
                        </h2>
                        <button @click="runContext7Fix()" class="neo-btn neo-btn neo-btn-primary text-sm touch-target-optimized touch-target-optimized">
                            🔧 Otomatik Düzelt
                        </button>
                    </div>

                    <!-- Önemli Kurallar Hatırlatması -->
                    <div class="neo-bg-blue-50 neo-border neo-border-blue-200 rounded-lg-lg p-4 mb-4">
                        <h3 class="neo-font-semibold neo-text-blue-800 mb-2">📚 Context7 Temel Kuralları:</h3>
                        <ul class="neo-space-y-2">
                            <li class="flex neo-items-start gap-2 text-sm neo-text-blue-700">
                                <span>❌</span>
                                <div>
                                    <strong>Yasaklı Alan Adları:</strong>
                                    <code class="neo-bg-blue-100 neo-px-1 rounded-lg">status</code>,
                                    <code class="neo-bg-blue-100 neo-px-1 rounded-lg">is_active</code>,
                                    <code class="neo-bg-blue-100 neo-px-1 rounded-lg">aktif</code>,
                                    <code class="neo-bg-blue-100 neo-px-1 rounded-lg">ad_soyad</code>,
                                    <code class="neo-bg-blue-100 neo-px-1 rounded-lg">region_id</code>
                                </div>
                            </li>
                            <li class="flex neo-items-start gap-2 text-sm neo-text-blue-700">
                                <span>✅</span>
                                <div>
                                    <strong>Doğru Alan Adları:</strong>
                                    <code class="neo-bg-green-100 neo-px-1 rounded-lg">status</code>,
                                    <code class="neo-bg-green-100 neo-px-1 rounded-lg">tam_ad</code>,
                                    <code class="neo-bg-green-100 neo-px-1 rounded-lg">il_id</code>
                                </div>
                            </li>
                            <li class="flex neo-items-start gap-2 text-sm neo-text-blue-700">
                                <span>🚫</span>
                                <div><strong>AI asla kendi kafasına göre tablo/kolon yaratamaz</strong> - sadece mevcut
                                    şemayı kullanır</div>
                            </li>
                            <li class="flex neo-items-start gap-2 text-sm neo-text-blue-700">
                                <span>📖</span>
                                <div><strong>Master dosyalara sadakat zorunlu</strong> - Context7 kuralları referans
                                    alınmalı</div>
                            </li>
                        </ul>
                    </div>

                    <!-- Otomatik Düzeltme Komutları -->
                    <div class="neo-bg-gray-50 rounded-lg-lg p-4 mb-4">
                        <h3 class="neo-font-semibold neo-text-gray-800 mb-2">⚡ Hızlı Düzeltme Komutları:</h3>
                        <div class="neo-grid neo-grid-cols-1 md:neo-grid-cols-2 gap-3">
                            <button @click="copyCommand('./scripts/context7-check.sh --auto-fix')"
                                class="neo-text-left neo-bg-gray-800 neo-text-green-400 neo-font-mono text-xs p-3 rounded-lg hover:neo-bg-gray-700 neo-transition-colors">
                                ./scripts/context7-check.sh --auto-fix
                            </button>
                            <button @click="copyCommand('php artisan context7:validate-migration --all')"
                                class="neo-text-left neo-bg-gray-800 neo-text-green-400 neo-font-mono text-xs p-3 rounded-lg hover:neo-bg-gray-700 neo-transition-colors">
                                php artisan context7:validate-migration --all
                            </button>
                            <button @click="copyCommand('php artisan view:clear && php artisan config:clear')"
                                class="neo-text-left neo-bg-gray-800 neo-text-green-400 neo-font-mono text-xs p-3 rounded-lg hover:neo-bg-gray-700 neo-transition-colors">
                                php artisan view:clear && php artisan config:clear
                            </button>
                            <button @click="copyCommand('grep -r \"status\\|is_active\" app/ resources/')"
                                class="neo-text-left neo-bg-gray-800 neo-text-green-400 neo-font-mono text-xs p-3 rounded-lg hover:neo-bg-gray-700 neo-transition-colors">
                                grep -r "status|is_active" app/ resources/
                            </button>
                        </div>
                    </div>

                    <!-- Öneriler Listesi -->
                    <div x-show="codeHealth?.suggestions?.length > 0" class="neo-space-y-2">
                        <h3 class="neo-font-semibold neo-text-gray-800 mb-2">💡 Akıllı Öneriler:</h3>
                        <template x-for="(suggestion, index) in (codeHealth?.suggestions || [])" :key="index">
                            <div
                                class="flex neo-items-start gap-3 neo-bg-yellow-50 neo-border neo-border-yellow-200 rounded-lg p-3">
                                <span class="neo-text-yellow-600 neo-font-bold text-sm">@{{ index + 1 }}.</span>
                                <div class="flex-1">
                                    <p class="text-sm neo-text-yellow-800">@{{ suggestion }}</p>
                                </div>
                                <button @click="applySuggestion(index)"
                                    class="neo-btn neo-btn-outline neo-btn-sm text-xs touch-target-optimized touch-target-optimized">
                                    Uygula
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Logs -->
            <div class="neo-grid neo-grid-cols-1 md:neo-grid-cols-2 gap-6 neo-mt-6">
                <div class="neo-card p-4">
                    <div class="flex items-center justify-between mb-2">
                        <h2 class="neo-font-semibold">Self-Healing Log (Son 10)</h2>
                        <div class="flex items-center gap-2">
                            <input x-model.trim="filterText" type="text" placeholder="Filtrele..."
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 neo-h-8 text-xs" maxlength="60"
                                pattern="[a-zA-ZğüşıöçĞÜŞİÖÇ0-9\s\-_]+"
                                title="Sadece harf, rakam ve temel karakterler kullanın" />
                            <button @click="refreshSelf()" class="neo-btn neo-btn neo-btn-secondary text-xs touch-target-optimized touch-target-optimized">Yenile</button>
                        </div>
                    </div>
                    <pre class="neo-bg-gray-50 p-2 rounded-lg text-xs neo-overflow-x-auto neo-h-56"><template x-for="(line, idx) in filteredSelfHealing()" :key="idx">@{{ line + '\n' }}</template></pre>
                </div>
                <div class="neo-card p-4">
                    <div class="flex items-center justify-between mb-2">
                        <h2 class="neo-font-semibold">Son 10 Hata</h2>
                        <button @click="refreshErrors()" class="neo-btn neo-btn neo-btn-secondary text-xs touch-target-optimized touch-target-optimized">Yenile</button>
                    </div>
                    <pre class="neo-bg-gray-50 p-2 rounded-lg text-xs neo-overflow-x-auto neo-h-56"><template x-for="(line, idx) in recentErrors" :key="idx">@{{ line + '\n' }}</template></pre>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
        <script>
            // Alpine.js component function - defined globally
            window.monitorUI = function() {
                return {
                    // State
                    mcp: {!! json_encode(($mcpStatus ?? collect())->values(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!},
                    apis: {!! json_encode($apiStatus ?? [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!},
                    selfHealing: {!! json_encode(($selfHealing ?? collect())->values(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!},
                    recentErrors: {!! json_encode(($recentErrors ?? collect())->values(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!},
                    overall: {!! json_encode($overall ?? [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!},
                    filterText: '',
                    autoRefresh: false,
                    refreshInterval: 30000,
                    timerId: null,
                    lastUpdated: '',
                    loadingMcp: false,
                    codeHealth: null,
                    duplicateFiles: [],
                    conflictingRoutes: [],
                    pagesHealth: [],

                    init() {
                        // İlk açılışta verileri getir
                        this.refreshAll();
                        this.refreshCodeHealth();
                        this.refreshDuplicates();
                        this.refreshConflicts();
                        this.refreshPagesHealth();
                        // Otomatik yenileme izleyicisi
                        this.$watch('autoRefresh', (val) => this.toggleTimer(val));
                        this.$watch('refreshInterval', () => this.toggleTimer(this.autoRefresh));
                    },

                    // UI Helpers
                    overallBadgeClass() {
                        const level = this.overall?.level || 'unknown';
                        if (level === 'green') return 'neo-bg-green-200';
                        if (level === 'yellow') return 'neo-bg-yellow-200';
                        if (level === 'red') return 'neo-bg-red-200';
                        return 'neo-bg-gray-200';
                    },
                    statusBadgeClass(st) {
                        const s = (st || '').toUpperCase();
                        if (s === 'OK') return 'neo-badge rounded-lg neo-px-2 neo-py-0.5 neo-bg-green-200';
                        if (s === 'ERROR') return 'neo-badge rounded-lg neo-px-2 neo-py-0.5 neo-bg-yellow-200';
                        return 'neo-badge rounded-lg neo-px-2 neo-py-0.5 neo-bg-red-200';
                    },
                    usageWidth(count) {
                        const max = Math.max(1, ...Object.values(this.overall?.mcp_usage || {
                            other: 1
                        }).map(v => Number(v) || 0));
                        const val = Math.min(max, Number(count) || 0);
                        return (val / max * 100).toFixed(0) + '%';
                    },
                    filteredSelfHealing() {
                        const q = (this.filterText || '').toLowerCase();
                        if (!q) return this.selfHealing;
                        return this.selfHealing.filter(l => (l || '').toLowerCase().includes(q));
                    },

                    // Networking
                    async refreshAll() {
                        await Promise.all([
                            this.refreshMcp(),
                            this.refreshApis(),
                            this.refreshSelf(),
                            this.refreshErrors(),
                        ]);
                        this.lastUpdated = new Date().toLocaleTimeString();
                    },
                    async refreshMcp() {
                        try {
                            this.loadingMcp = true;
                            const res = await fetch('{{ route('admin.ai-monitor.mcp') }}', {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });
                            if (res.ok) {
                                const json = await res.json();
                                this.mcp = Array.isArray(json?.data) ? json.data : [];
                                if (json?.overview) this.overall = json.overview;
                            }
                        } catch (e) {
                            /* yut */
                        } finally {
                            this.loadingMcp = false;
                        }
                    },
                    async refreshApis() {
                        try {
                            const res = await fetch('{{ route('admin.ai-monitor.apis') }}', {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });
                            if (res.ok) {
                                const json = await res.json();
                                this.apis = json?.data || {};
                            }
                        } catch (e) {
                            /* yut */
                        }
                    },
                    async refreshSelf() {
                        try {
                            const res = await fetch('{{ route('admin.ai-monitor.self') }}', {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });
                            if (res.ok) {
                                const json = await res.json();
                                this.selfHealing = Array.isArray(json?.data) ? json.data : [];
                            }
                        } catch (e) {
                            /* yut */
                        }
                    },
                    async refreshErrors() {
                        try {
                            const res = await fetch('{{ route('admin.ai-monitor.errors') }}', {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });
                            if (res.ok) {
                                const json = await res.json();
                                this.recentErrors = Array.isArray(json?.data) ? json.data : [];
                            }
                        } catch (e) {
                            /* yut */
                        }
                    },
                    toggleTimer(enable) {
                        if (this.timerId) {
                            clearInterval(this.timerId);
                            this.timerId = null;
                        }
                        if (enable) {
                            this.timerId = setInterval(() => this.refreshAll(), this.refreshInterval);
                        }
                    },
                    sortedUsage() {
                        const obj = this.overall?.mcp_usage || {};
                        return Object.entries(obj)
                            .filter(([, v]) => Number(v) > 0)
                            .sort((a, b) => Number(b[1]) - Number(a[1]));
                    },
                    mcpTypeColor(key) {
                        const colors = {
                            'context7': 'neo-bg-teal-500',
                            'puppeteer': 'neo-bg-purple-500',
                            'memory': 'neo-bg-indigo-500',
                            'filesystem': 'neo-bg-green-500',
                            'yalihan-bekci': 'neo-bg-blue-500',
                            'laravel': 'neo-bg-orange-500',
                            'git': 'neo-bg-gray-500',
                            'ollama': 'neo-bg-red-500'
                        };
                        return colors[key] || 'neo-bg-blue-500';
                    },
                    uptimePercent() {
                        const total = this.overall?.api_total || 0;
                        const ok = this.overall?.api_ok || 0;
                        return total > 0 ? Math.round((ok / total) * 100) : 0;
                    },
                    avgLatency() {
                        const apis = Object.values(this.apis);
                        const latencies = apis.filter(a => a?.latency_ms).map(a => Number(a.latency_ms) || 0);
                        return latencies.length > 0 ? Math.round(latencies.reduce((a, b) => a + b, 0) /
                                latencies.length) :
                            0;
                    },
                    // Context7 Öğretim Fonksiyonları
                    async runContext7Fix() {
                        try {
                            window.toast?.info('Context7 otomatik düzeltme başlatılıyor...');

                            const res = await fetch('/admin/ai-monitor/run-context7-fix', {
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]')
                                        .getAttribute(
                                            'content')
                                }
                            });

                            if (res.ok) {
                                const json = await res.json();
                                if (json.success) {
                                    window.toast?.success(json.message ||
                                        'Context7 otomatik düzeltme başlatıldı');
                                    // Düzeltme sonrası tekrar kontrol et
                                    setTimeout(() => this.refreshCodeHealth(), 3000);
                                } else {
                                    window.toast?.error(json.message || 'Düzeltme başarısız');
                                }
                            } else {
                                window.toast?.warning('Manuel düzeltme gerekli - terminal kullanın');
                            }
                        } catch (e) {
                            window.toast?.warning(
                                'Context7 script çalıştırın: ./scripts/context7-check.sh --auto-fix');
                        }
                    },
                    async copyCommand(command) {
                        try {
                            await navigator.clipboard.writeText(command);
                            window.toast?.success(`✓ Komut panoya kopyalandı`);
                        } catch (e) {
                            // Fallback için textarea kullan
                            const textarea = document.createElement('textarea');
                            textarea.value = command;
                            document.body.appendChild(textarea);
                            textarea.select();
                            document.execCommand('copy');
                            document.body.removeChild(textarea);
                            window.toast?.success(`✓ Komut panoya kopyalandı`);
                        }
                    },
                    async applySuggestion(index) {
                        const suggestion = this.codeHealth?.suggestions?.[index];
                        if (!suggestion) return;

                        // Sadece bilgilendirme - otomatik değişiklik yapmaz
                        window.toast?.info(`Öneri ${index + 1}: Manuel kontrol gerekli`);

                        // Context7 kurallarına göre sadece öneride bulunur
                        if (suggestion.includes('Context7')) {
                            this.copyCommand('./scripts/context7-check.sh --auto-fix');
                        } else if (suggestion.includes('duplik')) {
                            window.toast?.warning('Duplike kod manuel olarak kontrol edilmelidir');
                        } else if (suggestion.includes('unused')) {
                            window.toast?.info('Kullanılmayan dosyalar manuel olarak silinmelidir');
                        } else {
                            window.toast?.info('Bu öneri manuel olarak değerlendirilmelidir');
                        }
                    },
                    // Ecosystem Analysis Methods
                    async refreshCodeHealth() {
                        try {
                            const res = await fetch('{{ route('admin.ai-monitor.code-health') }}', {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });
                            if (res.ok) {
                                const json = await res.json();
                                this.codeHealth = json.data || null;
                            }
                        } catch (e) {
                            console.warn('Code health fetch error:', e);
                        }
                    },
                    async refreshDuplicates() {
                        try {
                            const res = await fetch('{{ route('admin.ai-monitor.duplicates') }}', {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });
                            if (res.ok) {
                                const json = await res.json();
                                this.duplicateFiles = json.data || [];
                            }
                        } catch (e) {
                            console.warn('Duplicates fetch error:', e);
                        }
                    },
                    async refreshConflicts() {
                        try {
                            const res = await fetch('{{ route('admin.ai-monitor.conflicts') }}', {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });
                            if (res.ok) {
                                const json = await res.json();
                                this.conflictingRoutes = json.data || [];
                            }
                        } catch (e) {
                            console.warn('Conflicts fetch error:', e);
                        }
                    },
                    async refreshPagesHealth() {
                        try {
                            const res = await fetch('{{ route('admin.ai-monitor.pages-health') }}', {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });
                            if (res.ok) {
                                const json = await res.json();
                                this.pagesHealth = json.data || [];
                            }
                        } catch (e) {
                            console.warn('Pages health fetch error:', e);
                        }
                    },
                    codeHealthBadgeClass() {
                        const score = this.codeHealth?.health_score || 0;
                        if (score >= 80) return 'neo-bg-green-200 neo-text-green-800';
                        if (score >= 60) return 'neo-bg-yellow-200 neo-text-yellow-800';
                        return 'neo-bg-red-200 neo-text-red-800';
                    },
                    issueTypeLabel(type) {
                        const labels = {
                            'context7_violations': 'Context7 Kural İhlalleri',
                            'master_document_violations': 'Master Doküman Uyumsuzlukları',
                            'duplicate_functions': 'Duplike Fonksiyonlar',
                            'unused_files': 'Kullanılmayan Dosyalar',
                            'missing_dependencies': 'Eksik Bağımlılıklar',
                            'route_conflicts': 'Rota Çakışmaları',
                            'database_inconsistencies': 'Database Tutarsızlıkları',
                            'migration_violation': 'Migration Kural İhlali',
                            'database_error': 'Database Hatası'
                        };
                        return labels[type] || type;
                    },
                    complianceBadgeClass() {
                        const status = this.codeHealth?.compliance_status;
                        if (status === 'compliant') return 'neo-bg-green-200 neo-text-green-800';
                        return 'neo-bg-red-200 neo-text-red-800';
                    },
                    severityClass(severity) {
                        switch (severity) {
                            case 'critical':
                                return 'neo-text-red-700';
                            case 'high':
                                return 'neo-text-orange-700';
                            case 'medium':
                                return 'neo-text-yellow-700';
                            case 'low':
                                return 'neo-text-gray-700';
                            default:
                                return 'neo-text-gray-600';
                        }
                    }
                }
            }
            }
        </script>
    @endpush
