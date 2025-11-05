@extends('admin.layouts.neo')

@section('title', 'AI Ayarları')

@push('meta')
    <meta name="description"
        content="AI sağlayıcılarını yapılandırın. AnythingLLM, OpenAI, Gemini ve diğer AI provider ayarları.">
    <meta property="og:title" content="AI Ayarları - Yalıhan Emlak">
    <meta property="og:description" content="Yapay zeka sağlayıcılarını yapılandırın ve AI özelliklerini yönetin">
    <meta property="og:type" content="website">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@push('scripts')
    {{-- ✅ YENİ: Vite ile modüler JavaScript (Hybrid Architecture) --}}
    @vite(['resources/js/admin/ai-settings/core.js'])
@endpush

@section('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Modern Header with Better Visual Hierarchy --}}
        <div class="bg-gradient-to-r from-purple-600 via-blue-600 to-cyan-600 text-white rounded-2xl p-8 mb-8 shadow-xl">
            <div class="flex items-center gap-6">
                <div class="w-20 h-20 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold mb-2">🤖 AI Ayarları</h1>
                    <p class="text-blue-100 text-lg">Yapay zeka sağlayıcılarını yapılandırın ve AI özelliklerini optimize
                        edin</p>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-2.5 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-2.5 rounded-lg mb-6">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.ai-settings.update') }}" class="space-y-8" x-data="{ loading: false }"
            @submit="loading = true">
            @csrf
            @method('PUT')

            {{-- Provider Selection System --}}
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800 border-l-4 border-blue-500 overflow-hidden hover:shadow-xl transition-all duration-300">
                <div
                    class="bg-gradient-to-r from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 px-6 py-4 border-b border-blue-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 bg-gradient-to-r from-blue-500 to-cyan-600 rounded-xl flex items-center justify-center shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900 dark:text-white">AI Provider Seçimi</h2>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Kullanmak istediğiniz AI sağlayıcısını
                                    seçin</p>
                            </div>
                        </div>
                        <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">Aktif</span>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach (['google' => ['name' => '🧠 Google Gemini', 'desc' => 'Hızlı ve güçlü', 'badge' => 'Önerilen'], 'openai' => ['name' => '🤖 OpenAI GPT', 'desc' => 'Yaratıcı ve akıllı', 'badge' => 'Popüler'], 'anthropic' => ['name' => '🎭 Anthropic Claude', 'desc' => 'Güvenli ve etik', 'badge' => 'Güvenli'], 'deepseek' => ['name' => '🔍 DeepSeek', 'desc' => 'Araştırma odaklı', 'badge' => 'Yeni'], 'minimax' => ['name' => '🚀 MiniMax', 'desc' => 'Yüksek performanslı, ölçeklenebilir', 'badge' => 'Kurumsal'], 'ollama' => ['name' => '🏠 Ollama Local', 'desc' => 'Gizlilik odaklı, offline AI', 'badge' => 'Güvenli']] as $key => $provider)
                            <div
                                class="provider-option relative {{ ($statistics['active_provider'] ?? '') === $key ? 'selected' : '' }}">
                                <input type="radio" name="ai_provider" id="provider_{{ $key }}"
                                    value="{{ $key }}"
                                    {{ ($statistics['active_provider'] ?? '') === $key ? 'checked' : '' }}
                                    class="peer sr-only">
                                <label for="provider_{{ $key }}"
                                    class="provider-card block p-4 border-2 border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:border-blue-300 dark:hover:border-blue-500 peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 transition-all">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="text-lg font-medium text-gray-900 dark:text-white">
                                            {{ $provider['name'] }}</div>
                                        <span
                                            class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded-full">{{ $provider['badge'] }}</span>
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400 mb-3">{{ $provider['desc'] }}
                                    </div>
                                    <div class="flex items-center gap-2 text-sm" id="{{ $key }}-status">
                                        <div class="w-2 h-2 bg-gray-400 rounded-full status-dot animate-pulse"></div>
                                        <span class="text-gray-500 dark:text-gray-400 status-text">Kontrol
                                            ediliyor...</span>
                                        <!-- Context7: Real-time status -->
                                        <button type="button" data-provider="{{ $key }}"
                                            class="btn-test ml-2 px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition-colors">
                                            Test Et
                                        </button>
                                    </div>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- API Keys Configuration --}}
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800 border-l-4 border-green-500 overflow-hidden hover:shadow-xl transition-all duration-300">
                <div
                    class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 px-6 py-4 border-b border-green-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900 dark:text-white">🔑 API Keys & Configuration</h2>
                                <p class="text-sm text-gray-600 dark:text-gray-400">AI provider API anahtarlarını
                                    yapılandırın</p>
                            </div>
                        </div>
                        <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Secure</span>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-6">
                        {{-- Google Gemini API --}}
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                            <div class="flex items-center gap-3 mb-3">
                                <span class="text-2xl">🧠</span>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Google Gemini</h3>
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">Önerilen</span>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="google_api_key"
                                        class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                        API Key *
                                    </label>
                                    <input type="password" name="google_api_key" id="google_api_key"
                                        value="{{ old('google_api_key', $settings['google_api_key'] ?? '') }}"
                                        class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white {{ $errors->has('google_api_key') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' }}"
                                        placeholder="AIzaSy...">
                                    @error('google_api_key')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="google_model"
                                        class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                        Model
                                    </label>
                                    <select style="color-scheme: light dark;" name="google_model" id="google_model"
                                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white transition-all duration-200">
                                        <option value="gemini-pro"
                                            {{ ($settings['google_model'] ?? 'gemini-pro') === 'gemini-pro' ? 'selected' : '' }}>
                                            Gemini Pro</option>
                                        <option value="gemini-pro-vision"
                                            {{ ($settings['google_model'] ?? '') === 'gemini-pro-vision' ? 'selected' : '' }}>
                                            Gemini Pro Vision</option>
                                        <option value="gemini-1.5-pro"
                                            {{ ($settings['google_model'] ?? '') === 'gemini-1.5-pro' ? 'selected' : '' }}>
                                            Gemini 1.5 Pro</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- OpenAI GPT API --}}
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                            <div class="flex items-center gap-3 mb-3">
                                <span class="text-2xl">🤖</span>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">OpenAI GPT</h3>
                                <span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded-full">Popüler</span>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="openai_api_key"
                                        class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                        API Key *
                                    </label>
                                    <input type="password" name="openai_api_key" id="openai_api_key"
                                        value="{{ old('openai_api_key', $settings['openai_api_key'] ?? '') }}"
                                        class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white {{ $errors->has('openai_api_key') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' }}"
                                        placeholder="sk-...">
                                    @error('openai_api_key')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="openai_model"
                                        class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                        Model
                                    </label>
                                    <select style="color-scheme: light dark;" name="openai_model" id="openai_model"
                                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white transition-all duration-200">
                                        <option value="gpt-4"
                                            {{ ($settings['openai_model'] ?? 'gpt-4') === 'gpt-4' ? 'selected' : '' }}>
                                            GPT-4</option>
                                        <option value="gpt-4-turbo"
                                            {{ ($settings['openai_model'] ?? '') === 'gpt-4-turbo' ? 'selected' : '' }}>
                                            GPT-4 Turbo</option>
                                        <option value="gpt-3.5-turbo"
                                            {{ ($settings['openai_model'] ?? '') === 'gpt-3.5-turbo' ? 'selected' : '' }}>
                                            GPT-3.5 Turbo</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Claude API --}}
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                            <div class="flex items-center gap-3 mb-3">
                                <span class="text-2xl">🎭</span>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Anthropic Claude</h3>
                                <span class="px-2 py-1 bg-orange-100 text-orange-800 text-xs rounded-full">Güvenli</span>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="claude_api_key"
                                        class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                        API Key *
                                    </label>
                                    <input type="password" name="claude_api_key" id="claude_api_key"
                                        value="{{ old('claude_api_key', $settings['claude_api_key'] ?? '') }}"
                                        class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white {{ $errors->has('claude_api_key') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' }}"
                                        placeholder="sk-ant-...">
                                    @error('claude_api_key')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="claude_model"
                                        class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                        Model
                                    </label>
                                    <select style="color-scheme: light dark;" name="claude_model" id="claude_model"
                                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white transition-all duration-200">
                                        <option value="claude-3-opus-20240229"
                                            {{ ($settings['claude_model'] ?? 'claude-3-opus-20240229') === 'claude-3-opus-20240229' ? 'selected' : '' }}>
                                            Claude 3 Opus</option>
                                        <option value="claude-3-sonnet-20240229"
                                            {{ ($settings['claude_model'] ?? '') === 'claude-3-sonnet-20240229' ? 'selected' : '' }}>
                                            Claude 3 Sonnet</option>
                                        <option value="claude-3-haiku-20240307"
                                            {{ ($settings['claude_model'] ?? '') === 'claude-3-haiku-20240307' ? 'selected' : '' }}>
                                            Claude 3 Haiku</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- DeepSeek API --}}
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                            <div class="flex items-center gap-3 mb-3">
                                <span class="text-2xl">🔍</span>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">DeepSeek</h3>
                                <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">Yeni</span>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="deepseek_api_key"
                                        class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                        API Key *
                                    </label>
                                    <input type="password" name="deepseek_api_key" id="deepseek_api_key"
                                        value="{{ old('deepseek_api_key', $settings['deepseek_api_key'] ?? '') }}"
                                        class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white {{ $errors->has('deepseek_api_key') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' }}"
                                        placeholder="sk-...">
                                    @error('deepseek_api_key')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="deepseek_model"
                                        class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                        Model
                                    </label>
                                    <select style="color-scheme: light dark;" name="deepseek_model" id="deepseek_model"
                                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white transition-all duration-200">
                                        <option value="deepseek-chat"
                                            {{ ($settings['deepseek_model'] ?? 'deepseek-chat') === 'deepseek-chat' ? 'selected' : '' }}>
                                            DeepSeek Chat</option>
                                        <option value="deepseek-coder"
                                            {{ ($settings['deepseek_model'] ?? '') === 'deepseek-coder' ? 'selected' : '' }}>
                                            DeepSeek Coder</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- MiniMax API --}}
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                            <div class="flex items-center gap-3 mb-3">
                                <span class="text-2xl">🚀</span>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">MiniMax</h3>
                                <span class="px-2 py-1 bg-orange-100 text-orange-800 text-xs rounded-full">Kurumsal</span>
                                <a href="https://platform.minimax.io/" target="_blank" rel="noopener noreferrer"
                                    class="ml-auto text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 underline">
                                    📚 Dokümantasyon
                                </a>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="minimax_api_key"
                                        class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                        API Key *
                                    </label>
                                    <input type="password" name="minimax_api_key" id="minimax_api_key"
                                        value="{{ old('minimax_api_key', $settings['minimax_api_key'] ?? '') }}"
                                        class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white {{ $errors->has('minimax_api_key') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' }}"
                                        placeholder="MiniMax API Key">
                                    @error('minimax_api_key')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        💡 MiniMax API anahtarınızı <a href="https://platform.minimax.io/" target="_blank" class="text-blue-600 hover:underline">platform.minimax.io</a> adresinden alabilirsiniz
                                    </p>
                                </div>
                                <div>
                                    <label for="minimax_model"
                                        class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                        Model
                                    </label>
                                    <select style="color-scheme: light dark;" name="minimax_model" id="minimax_model"
                                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white transition-all duration-200">
                                        <option value="minimax-m2"
                                            {{ ($settings['minimax_model'] ?? 'minimax-m2') === 'minimax-m2' ? 'selected' : '' }}>
                                            MiniMax M2 (Önerilen)</option>
                                        <option value="minimax-m2-32k"
                                            {{ ($settings['minimax_model'] ?? '') === 'minimax-m2-32k' ? 'selected' : '' }}>
                                            MiniMax M2 32K</option>
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        📊 Context Length: 200k tokens | Max Output: 128k tokens
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Ollama Local --}}
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                            <div class="flex items-center gap-3 mb-3">
                                <span class="text-2xl">🏠</span>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Ollama Local</h3>
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full" id="ollama-status">Local</span>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="ollama_url"
                                        class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                        Ollama URL
                                    </label>
                                    <input type="url" name="ollama_url" id="ollama_url"
                                        value="{{ old('ollama_url', $settings['ollama_url'] ?? 'http://localhost:11434') }}"
                                        class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white {{ $errors->has('ollama_url') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' }}"
                                        placeholder="http://localhost:11434">
                                    @error('ollama_url')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        💡 Local: http://localhost:11434 | Remote: http://51.75.64.121:11434
                                    </p>
                                    <button type="button" onclick="testOllamaConnection()"
                                        class="mt-2 px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded transition-colors">
                                        🔗 Bağlantıyı Test Et
                                    </button>
                                </div>
                                <div>
                                    <label for="ollama_model"
                                        class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                        Model
                                    </label>
                                    <select style="color-scheme: light dark;" name="ollama_model" id="ollama_model"
                                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white transition-all duration-200">
                                        <option value="qwen2.5:3b"
                                            {{ ($settings['ollama_model'] ?? 'qwen2.5:3b') === 'qwen2.5:3b' ? 'selected' : '' }}>
                                            Qwen2.5 3B (Aktif)</option>
                                        <option value="qwen2.5:latest"
                                            {{ ($settings['ollama_model'] ?? '') === 'qwen2.5:latest' ? 'selected' : '' }}>
                                            Qwen2.5 Latest</option>
                                        <option value="gemma2:2b"
                                            {{ ($settings['ollama_model'] ?? '') === 'gemma2:2b' ? 'selected' : '' }}>
                                            Gemma2 2B</option>
                                        <option value="phi3:mini"
                                            {{ ($settings['ollama_model'] ?? '') === 'phi3:mini' ? 'selected' : '' }}>
                                            Phi3 Mini</option>
                                        <option value="nomic-embed-text:latest"
                                            {{ ($settings['ollama_model'] ?? '') === 'nomic-embed-text:latest' ? 'selected' : '' }}>
                                            Nomic Embed Text (Embedding)</option>
                                        <option value="llama2"
                                            {{ ($settings['ollama_model'] ?? '') === 'llama2' ? 'selected' : '' }}>
                                            Llama 2</option>
                                        <option value="llama3"
                                            {{ ($settings['ollama_model'] ?? '') === 'llama3' ? 'selected' : '' }}>Llama 3
                                        </option>
                                        <option value="mistral"
                                            {{ ($settings['ollama_model'] ?? '') === 'mistral' ? 'selected' : '' }}>Mistral
                                        </option>
                                        <option value="codellama"
                                            {{ ($settings['ollama_model'] ?? '') === 'codellama' ? 'selected' : '' }}>
                                            CodeLlama</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 dark:bg-gray-800 rounded-xl shadow p-6">
                <h2 class="text-lg font-semibold mb-4">Varsayılan Öneri Ayarları</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="ai_default_tone"
                            class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Varsayılan Ton *
                        </label>
                        <select class="transition-all duration-200" style="color-scheme: light dark;" name="ai_default_tone" id="ai_default_tone"
                            class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white {{ $errors->has('ai_default_tone') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' }}">
                            <option value="">Ton Seçiniz</option>
                            <option value="seo" {{ ($settings['ai_default_tone'] ?? '') === 'seo' ? 'selected' : '' }}>
                                📊 SEO Odaklı</option>
                            <option value="kurumsal"
                                {{ ($settings['ai_default_tone'] ?? '') === 'kurumsal' ? 'selected' : '' }}>🏢 Kurumsal
                            </option>
                            <option value="hizli_satis"
                                {{ ($settings['ai_default_tone'] ?? '') === 'hizli_satis' ? 'selected' : '' }}>⚡ Hızlı
                                Satış
                            </option>
                            <option value="luks"
                                {{ ($settings['ai_default_tone'] ?? '') === 'luks' ? 'selected' : '' }}>
                                💎 Lüks</option>
                        </select>
                        @error('ai_default_tone')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="ai_default_variant_count"
                            class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Varyant Sayısı *
                        </label>
                        <input type="number" name="ai_default_variant_count" id="ai_default_variant_count"
                            min="1" max="5"
                            value="{{ old('ai_default_variant_count', $settings['ai_default_variant_count'] ?? 3) }}"
                            class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white {{ $errors->has('ai_default_variant_count') ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' }}"
                            placeholder="1-5 arası">
                        @error('ai_default_variant_count')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">A/B Testi</label>
                        <input type="checkbox" name="ai_default_ab_test" value="1"
                            {{ $settings['ai_default_ab_test'] ?? false ? 'checked' : '' }}
                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-800 dark:border-gray-600">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Ek Diller (özet)</label>
                        <div class="flex gap-2">
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">EN</span>
                            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded">RU</span>
                            <span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded">DE</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- AI Test Message Area --}}
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800 border-l-4 border-purple-500 overflow-hidden hover:shadow-xl transition-all duration-300">
                <div
                    class="bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 px-6 py-4 border-b border-purple-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl flex items-center justify-center shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900 dark:text-white">🧪 AI Test Alanı</h2>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Seçili provider'ı test mesajıyla dene
                                </p>
                            </div>
                        </div>
                        <span class="px-3 py-1 bg-purple-100 text-purple-700 text-xs font-semibold rounded-full">Live
                            Test</span>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-6">
                        {{-- Test Message Input --}}
                        <div>
                            <label for="test_message"
                                class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Test Mesajı
                            </label>
                            <textarea id="test_message" rows="4"
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white"
                                placeholder="Örnek: Bodrum'da denize sıfır lüks villa için başlık yaz...">Bodrum'da denize sıfır lüks villa için bir ilan başlığı ve açıklaması yaz.</textarea>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                💡 Seçili AI provider'ı kullanarak test mesajınızı işleyecek
                            </p>
                        </div>

                        {{-- Provider Selection for Test --}}
                        <div>
                            <label for="test_provider_select"
                                class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Test Provider Seç
                            </label>
                            <select style="color-scheme: light dark;" id="test_provider_select"
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white transition-all duration-200">
                                <option value="">Provider Seçiniz</option>
                                <option value="google">🧠 Google Gemini</option>
                                <option value="openai">🤖 OpenAI GPT</option>
                                <option value="anthropic">🎭 Anthropic Claude</option>
                                <option value="deepseek">🔍 DeepSeek</option>
                                <option value="ollama">🏠 Ollama Local</option>
                            </select>
                        </div>

                        {{-- Test Button --}}
                        <div class="flex items-center gap-4">
                            <button type="button" onclick="sendTestMessage()"
                                class="bg-gradient-to-r from-purple-500 to-pink-600 hover:from-purple-600 hover:to-pink-700 text-white font-semibold py-3 px-8 rounded-lg transition-all duration-200 flex items-center gap-2 shadow-lg hover:shadow-xl">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                Mesajı Gönder ve Test Et
                            </button>
                            <button type="button" onclick="clearTestArea()"
                                class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-800 dark:hover:bg-gray-600 text-gray-900 dark:text-white font-semibold py-3 px-6 rounded-lg transition-all duration-200 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Temizle
                            </button>
                        </div>

                        {{-- Test Response Area --}}
                        <div
                            class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                    <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    AI Yanıtı
                                </h3>
                                <span id="test_status"
                                    class="px-3 py-1 bg-gray-200 dark:bg-gray-800 text-gray-600 dark:text-gray-400 text-xs rounded-full">
                                    Bekliyor...
                                </span>
                            </div>
                            <div id="test_response" class="prose dark:prose-invert max-w-none">
                                <p class="text-gray-500 dark:text-gray-400 italic">Test mesajı gönderdikten sonra AI yanıtı
                                    burada görünecek...</p>
                            </div>
                            <div id="test_metrics" class="mt-4 hidden" data-grid-classes="grid grid-cols-3 gap-4">
                                <div
                                    class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 border border-gray-200 dark:border-gray-700">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Yanıt Süresi</p>
                                    <p id="test_response_time"
                                        class="text-lg font-semibold text-purple-600 dark:text-purple-400">-</p>
                                </div>
                                <div
                                    class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 border border-gray-200 dark:border-gray-700">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Tokens</p>
                                    <p id="test_tokens" class="text-lg font-semibold text-blue-600 dark:text-blue-400">-
                                    </p>
                                </div>
                                <div
                                    class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 border border-gray-200 dark:border-gray-700">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Tahmini Maliyet</p>
                                    <p id="test_cost" class="text-lg font-semibold text-green-600 dark:text-green-400">-
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-lg transition-colors duration-200 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Kaydet
                </button>
            </div>
        </form>

        {{-- Context7: AI Usage Analytics Dashboard --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800 border-l-4 border-purple-500 mt-8">
            <div
                class="bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 px-6 py-4 border-b border-purple-100">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 00-2-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white">AI Kullanım İstatistikleri</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Son 30 günün AI provider kullanım analizi
                            </p>
                        </div>
                    </div>
                    <button type="button" onclick="refreshAnalytics()"
                        class="px-3 py-1 bg-purple-100 text-purple-700 text-xs font-semibold rounded-full hover:bg-purple-200 transition-colors">
                        <i class="fas fa-sync-alt mr-1"></i>Yenile
                    </button>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400" id="total-requests">-</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Toplam İstek</div>
                    </div>
                    <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400" id="success-rate">-</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Başarı Oranı</div>
                    </div>
                    <div class="text-center p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                        <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400" id="avg-response">-</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Ort. Yanıt Süresi</div>
                    </div>
                    <div class="text-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                        <div class="text-2xl font-bold text-purple-600 dark:text-purple-400" id="cost-estimate">-</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Tahmini Maliyet</div>
                    </div>
                </div>

                {{-- Provider Usage Breakdown --}}
                <div class="space-y-3">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Provider Kullanım Dağılımı</h3>
                    @foreach (['google' => 'Google Gemini', 'openai' => 'OpenAI GPT', 'anthropic' => 'Claude', 'deepseek' => 'DeepSeek'] as $key => $name)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-3 h-3 bg-{{ $key === 'google' ? 'blue' : ($key === 'openai' ? 'green' : ($key === 'anthropic' ? 'purple' : 'red')) }}-500 rounded-full">
                                </div>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $name }}</span>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    <span id="{{ $key }}-usage-count">-</span> istek
                                </div>
                                <div class="w-24 bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                    <div id="{{ $key }}-usage-bar"
                                        class="bg-{{ $key === 'google' ? 'blue' : ($key === 'openai' ? 'green' : ($key === 'anthropic' ? 'purple' : 'red')) }}-500 h-2 rounded-full"
                                        style="width: 0%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Context7: Enhanced JavaScript for AI Analytics --}}
    <script>
        // AI Analytics functions
        async function refreshAnalytics() {
            try {
                const response = await fetch('/admin/ai-settings/analytics');
                const data = await response.json();

                // Update overview stats
                document.getElementById('total-requests').textContent = data.total_requests || '-';
                document.getElementById('success-rate').textContent = (data.success_rate || 0) + '%';
                document.getElementById('avg-response').textContent = (data.avg_response_time || 0) + 'ms';
                document.getElementById('cost-estimate').textContent = '$' + (data.estimated_cost || '0.00');

                // Update provider usage
                const providers = ['google', 'openai', 'anthropic', 'deepseek'];
                providers.forEach(provider => {
                    const usage = data.provider_usage?.[provider] || 0;
                    const maxUsage = Math.max(...Object.values(data.provider_usage || {}));
                    const percentage = maxUsage > 0 ? (usage / maxUsage) * 100 : 0;

                    document.getElementById(`${provider}-usage-count`).textContent = usage;
                    document.getElementById(`${provider}-usage-bar`).style.width = percentage + '%';
                });

                showToast('success', '✅ İstatistikler güncellendi');
            } catch (error) {
                console.error('Analytics refresh error:', error);
                showToast('error', '❌ İstatistikler yüklenemedi');
            }
        }

        // Initialize analytics on page load
        document.addEventListener('DOMContentLoaded', function() {
            refreshAnalytics();
        });

        // Test Message Functions (inline to avoid loading issues)
        window.sendTestMessage = async function() {
            const message = document.getElementById("test_message").value.trim();
            const provider = document.getElementById("test_provider_select").value;
            const responseDiv = document.getElementById("test_response");
            const statusSpan = document.getElementById("test_status");
            const metricsDiv = document.getElementById("test_metrics");

            if (!message) {
                window.toast?.error("❌ Lütfen test mesajı girin!");
                return;
            }

            if (!provider) {
                window.toast?.error("❌ Lütfen bir provider seçin!");
                return;
            }

            // Show loading
            statusSpan.className =
                "px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 text-xs rounded-full animate-pulse";
            statusSpan.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Gönderiliyor...';
            responseDiv.innerHTML =
                '<div class="flex items-center gap-3 text-gray-600 dark:text-gray-400"><i class="fas fa-spinner fa-spin text-2xl text-purple-500"></i><span>AI yanıtı bekleniyor...</span></div>';
            metricsDiv.classList.add("hidden");

            const startTime = Date.now();

            try {
                const response = await fetch("/admin/ai-settings/test-query", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        provider,
                        message
                    }),
                });

                const data = await response.json();
                const responseTime = Date.now() - startTime;

                if (data.success) {
                    statusSpan.className =
                        "px-3 py-1 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 text-xs rounded-full";
                    statusSpan.innerHTML = '<i class="fas fa-check-circle"></i> Başarılı';
                    responseDiv.innerHTML =
                        `<div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 border-l-4 border-purple-500"><p class="text-gray-900 dark:text-white whitespace-pre-wrap">${data.response || data.message}</p></div>`;
                    metricsDiv.classList.remove("hidden");
                    metricsDiv.classList.add("grid", "grid-cols-3", "gap-4");
                    document.getElementById("test_response_time").textContent = `${responseTime}ms`;
                    document.getElementById("test_tokens").textContent = data.tokens || "-";
                    document.getElementById("test_cost").textContent = data.cost ? `$${data.cost}` : "-";
                    window.toast?.success(`✅ ${provider.toUpperCase()}: Yanıt alındı (${responseTime}ms)`);
                } else {
                    statusSpan.className =
                        "px-3 py-1 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 text-xs rounded-full";
                    statusSpan.innerHTML = '<i class="fas fa-exclamation-circle"></i> Hata';
                    responseDiv.innerHTML =
                        `<div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4 border-l-4 border-red-500"><p class="text-red-700 dark:text-red-300 font-medium">❌ Hata:</p><p class="text-red-600 dark:text-red-400 mt-2">${data.message || "Bilinmeyen hata"}</p></div>`;
                    window.toast?.error(`❌ ${provider.toUpperCase()}: ${data.message}`);
                }
            } catch (error) {
                console.error("Test error:", error);
                statusSpan.className =
                    "px-3 py-1 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 text-xs rounded-full";
                statusSpan.innerHTML = '<i class="fas fa-times-circle"></i> Bağlantı Hatası';
                responseDiv.innerHTML =
                    `<div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4 border-l-4 border-red-500"><p class="text-red-700 dark:text-red-300 font-medium">❌ Bağlantı Hatası:</p><p class="text-red-600 dark:text-red-400 mt-2">Sunucuya bağlanılamadı.</p></div>`;
                window.toast?.error("❌ Bağlantı hatası");
            }
        };

        window.clearTestArea = function() {
            document.getElementById("test_message").value =
                "Bodrum'da denize sıfır lüks villa için bir ilan başlığı ve açıklaması yaz.";
            document.getElementById("test_provider_select").value = "";
            document.getElementById("test_response").innerHTML =
                '<p class="text-gray-500 dark:text-gray-400 italic">Test mesajı gönderdikten sonra AI yanıtı burada görünecek...</p>';
            document.getElementById("test_status").className =
                "px-3 py-1 bg-gray-200 dark:bg-gray-800 text-gray-600 dark:text-gray-400 text-xs rounded-full";
            document.getElementById("test_status").textContent = "Bekliyor...";
            const testMetrics = document.getElementById("test_metrics");
            testMetrics.classList.add("hidden");
            testMetrics.classList.remove("grid", "grid-cols-3", "gap-4");
            window.toast?.info("🔄 Test alanı temizlendi");
        };

        // Ollama Connection Test (Backend Proxy)
        window.testOllamaConnection = async function() {
            const ollamaUrl = document.getElementById('ollama_url').value;
            const statusElement = document.getElementById('ollama-status');

            // Show testing status
            statusElement.className = "px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full";
            statusElement.textContent = "Test ediliyor...";

            try {
                // Use backend proxy instead of direct connection
                const response = await fetch('/admin/ai-settings/test-ollama', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        ollama_url: ollamaUrl
                    })
                });

                const data = await response.json();

                if (data.success) {
                    statusElement.className = "px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full";
                    statusElement.textContent = `Bağlı (${data.models?.length || 0} model)`;
                    window.toast?.success("✅ Ollama bağlantısı başarılı!");
                } else {
                    throw new Error(data.message || 'Bağlantı hatası');
                }
            } catch (error) {
                console.error('Ollama connection test failed:', error);

                // More user-friendly error messages
                let errorMessage = "Bağlantı hatası";
                let toastMessage = "❌ Ollama bağlantısı başarısız";

                if (error.message.includes('timeout')) {
                    errorMessage = "Zaman aşımı";
                    toastMessage = "⏰ Ollama server'ına ulaşılamadı (zaman aşımı)";
                } else if (error.message.includes('connection')) {
                    errorMessage = "Server bulunamadı";
                    toastMessage = "🔍 Ollama server'ı bulunamadı. URL'yi kontrol edin.";
                } else if (error.message.includes('CORS')) {
                    errorMessage = "CORS hatası";
                    toastMessage = "🌐 CORS hatası. Server ayarlarını kontrol edin.";
                } else {
                    toastMessage = `❌ Ollama bağlantısı başarısız: ${error.message}`;
                }

                statusElement.className = "px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full";
                statusElement.textContent = errorMessage;
                window.toast?.error(toastMessage);
            }
        };
    </script>
@endsection
