@extends('admin.layouts.admin')

@section('title', 'İlan Taslağı Detayı #' . $taslak->id)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">📝 İlan Taslağı #{{ $taslak->id }}</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">AI ile oluşturulan ilan taslağı detayları</p>
        </div>
        <a href="{{ route('admin.ai.ilan-taslaklari.index') }}" 
            class="inline-flex items-center px-4 py-2 bg-gray-600 text-white font-semibold rounded-lg hover:bg-gray-700 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-500 focus:outline-none transition-all duration-200">
            Geri Dön
        </a>
    </div>

    <!-- Durum Bilgisi -->
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Durum</h2>
                @php
                    $statusColors = [
                        'draft' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                        'pending_review' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                        'approved' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                        'published' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                    ];
                    $statusColor = $statusColors[$taslak->status] ?? 'bg-gray-100 text-gray-800';
                @endphp
                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $statusColor }}">
                    {{ ucfirst(str_replace('_', ' ', $taslak->status)) }}
                </span>
            </div>
            <div class="flex gap-2">
                @if($taslak->status === 'draft' || $taslak->status === 'pending_review')
                <form method="POST" action="{{ route('admin.ai.ilan-taslaklari.approve', $taslak->id) }}" class="inline">
                    @csrf
                    <button type="submit" 
                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-green-500 focus:outline-none transition-all duration-200">
                        ✅ Onayla
                    </button>
                </form>
                @endif
                @if($taslak->status === 'approved')
                <form method="POST" action="{{ route('admin.ai.ilan-taslaklari.publish', $taslak->id) }}" class="inline">
                    @csrf
                    <button type="submit" 
                        class="inline-flex items-center px-4 py-2 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-purple-500 focus:outline-none transition-all duration-200">
                        🚀 Yayınla
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Bilgiler -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Sol Kolon -->
        <div class="space-y-6">
            <!-- Danışman Bilgisi -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Danışman Bilgisi</h2>
                <div class="space-y-2">
                    <div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">Danışman:</span>
                        <span class="ml-2 text-sm font-medium text-gray-900 dark:text-white">{{ $taslak->danisman->name ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">Oluşturulma:</span>
                        <span class="ml-2 text-sm font-medium text-gray-900 dark:text-white">{{ $taslak->created_at->format('d.m.Y H:i') }}</span>
                    </div>
                    @if($taslak->approved_by)
                    <div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">Onaylayan:</span>
                        <span class="ml-2 text-sm font-medium text-gray-900 dark:text-white">{{ $taslak->approver->name ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">Onay Tarihi:</span>
                        <span class="ml-2 text-sm font-medium text-gray-900 dark:text-white">{{ $taslak->approved_at ? $taslak->approved_at->format('d.m.Y H:i') : 'N/A' }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- AI Bilgisi -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">AI Bilgisi</h2>
                <div class="space-y-2">
                    <div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">Model:</span>
                        <span class="ml-2 text-sm font-medium text-gray-900 dark:text-white">{{ $taslak->ai_model_used ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">Prompt Versiyonu:</span>
                        <span class="ml-2 text-sm font-medium text-gray-900 dark:text-white">{{ $taslak->ai_prompt_version ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">Oluşturulma:</span>
                        <span class="ml-2 text-sm font-medium text-gray-900 dark:text-white">{{ $taslak->ai_generated_at ? $taslak->ai_generated_at->format('d.m.Y H:i') : 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sağ Kolon -->
        <div class="space-y-6">
            <!-- İlan Bilgisi -->
            @if($taslak->ilan_id)
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">İlan Bilgisi</h2>
                <div class="space-y-2">
                    <div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">İlan ID:</span>
                        <a href="{{ route('admin.ilanlar.edit', $taslak->ilan_id) }}" 
                            class="ml-2 text-sm font-medium text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 transition-colors duration-200">
                            #{{ $taslak->ilan_id }}
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- AI Response -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">AI Response</h2>
                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 overflow-x-auto">
                    <pre class="text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ json_encode($taslak->ai_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

