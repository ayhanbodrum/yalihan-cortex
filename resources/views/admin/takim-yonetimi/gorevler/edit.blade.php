@extends('admin.layouts.neo')

@section('title', 'Görev Düzenle')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <!-- Modern Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900 dark:text-white flex items-center">
                        <div
                            class="w-16 h-16 bg-gradient-to-r from-orange-500 to-red-600 rounded-2xl flex items-center justify-center mr-6 shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                </path>
                            </svg>
                        </div>
                        📝 Görev Düzenle
                    </h1>
                    <p class="text-xl text-gray-600 dark:text-gray-400 mt-3">
                        "{{ $gorev->baslik }}" görevini düzenleyin ve güncelleyin
                    </p>
                </div>
                <div class="flex items-center space-x-3">
                    <x-context7.button variant="secondary" href="{{ route('admin.takim-yonetimi.gorevler.show', $gorev) }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                            </path>
                        </svg>
                        Görevi Görüntüle
                    </x-context7.button>
                    <x-context7.button variant="secondary" href="{{ route('admin.takim-yonetimi.gorevler.index') }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                            </path>
                        </svg>
                        Görevlere Dön
                    </x-context7.button>
                </div>
            </div>
        </div>

        <!-- Form Progress -->
        <div class="mb-6 bg-gradient-to-r from-orange-50 to-red-50 rounded-xl border border-orange-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-orange-700">Düzenleme İlerlemesi</span>
                <span class="text-sm text-orange-600 font-semibold" id="progress-text">0%</span>
            </div>
            <div class="w-full bg-orange-200 rounded-full h-2">
                <div class="bg-gradient-to-r from-orange-500 to-red-600 h-2 rounded-full transition-all duration-1000"
                    id="progress-bar" style="width: 0%"></div>
            </div>
            <div class="mt-2 text-xs text-orange-600">
                <span id="changes-count">0</span> alan değiştirildi
            </div>
        </div>

        <!-- Görev Düzenleme Formu -->
        <form action="{{ route('admin.takim-yonetimi.gorevler.update', $gorev) }}" method="POST" id="gorevEditForm"
            class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Temel Bilgiler Bölümü -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-200 shadow-sm">
                <div class="p-6">
                    <h2 class="text-xl font-bold text-blue-800 mb-6 flex items-center">
                        <svg class="w-6 h-6 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        📝 Temel Bilgiler
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Görev Başlığı -->
                        <div class="lg:col-span-2">
                            <label for="baslik" class="block text-sm font-medium text-blue-700 mb-2">
                                Görev Başlığı <span class="text-red-500">*</span>
                            </label>
                            <input type="text" class="admin-input @error('baslik') border-red-300 @enderror"
                                id="baslik" name="baslik" value="{{ old('baslik', $gorev->baslik) }}"
                                placeholder="Görev başlığını açıklayıcı şekilde girin..." required>
                            @error('baslik')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <small class="text-blue-600 text-xs mt-1 block">Önceki: {{ $gorev->baslik }}</small>
                        </div>

                        <!-- Öncelik -->
                        <div>
                            <label for="oncelik" class="block text-sm font-medium text-blue-700 mb-2">
                                Öncelik <span class="text-red-500">*</span>
                            </label>
                            <select style="color-scheme: light dark;" class="admin-input @error('oncelik') border-red-300 @enderror transition-all duration-200" id="oncelik"
                                name="oncelik" required>
                                <option value="">Öncelik seçin...</option>
                                <option value="dusuk" {{ old('oncelik', $gorev->oncelik) == 'dusuk' ? 'selected' : '' }}>🔵
                                    Düşük</option>
                                <option value="normal" {{ old('oncelik', $gorev->oncelik) == 'normal' ? 'selected' : '' }}>
                                    🟡
                                    Normal</option>
                                <option value="yuksek" {{ old('oncelik', $gorev->oncelik) == 'yuksek' ? 'selected' : '' }}>
                                    🟠
                                    Yüksek</option>
                                <option value="acil" {{ old('oncelik', $gorev->oncelik) == 'acil' ? 'selected' : '' }}>🔴
                                    Acil</option>
                            </select>
                            @error('oncelik')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <small class="text-blue-600 text-xs mt-1 block">Önceki: {!! $gorev->oncelik_etiketi !!}</small>
                        </div>

                        <!-- Görev Açıklaması -->
                        <div class="md:col-span-2 lg:col-span-3">
                            <label for="aciklama" class="block text-sm font-medium text-blue-700 mb-2">
                                Görev Açıklaması
                            </label>
                            <textarea class="admin-input @error('aciklama') border-red-300 @enderror" id="aciklama" name="aciklama" rows="4"
                                placeholder="Görev detaylarını ve gereksinimleri açıklayın...">{{ old('aciklama', $gorev->aciklama) }}</textarea>
                            @error('aciklama')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            @if ($gorev->aciklama)
                                <small class="text-blue-600 text-xs mt-1 block">Önceki açıklama mevcut</small>
                            @endif
                        </div>

                        <!-- Görev Tipi -->
                        <div>
                            <label for="tip" class="block text-sm font-medium text-blue-700 mb-2">
                                Görev Tipi <span class="text-red-500">*</span>
                            </label>
                            <select style="color-scheme: light dark;" class="admin-input @error('tip') border-red-300 @enderror transition-all duration-200" id="tip"
                                name="tip" required>
                                <option value="">Tip seçin...</option>
                                <option value="musteri_takibi"
                                    {{ old('tip', $gorev->tip) == 'musteri_takibi' ? 'selected' : '' }}>👥 Müşteri Takibi
                                </option>
                                <option value="ilan_hazirlama"
                                    {{ old('tip', $gorev->tip) == 'ilan_hazirlama' ? 'selected' : '' }}>🏠 İlan Hazırlama
                                </option>
                                <option value="musteri_ziyareti"
                                    {{ old('tip', $gorev->tip) == 'musteri_ziyareti' ? 'selected' : '' }}>📍 Müşteri
                                    Ziyareti
                                </option>
                                <option value="dokuman_hazirlama"
                                    {{ old('tip', $gorev->tip) == 'dokuman_hazirlama' ? 'selected' : '' }}>📄 Doküman
                                    Hazırlama
                                </option>
                                <option value="diger" {{ old('tip', $gorev->tip) == 'diger' ? 'selected' : '' }}>📋 Diğer
                                </option>
                            </select>
                            @error('tip')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <small class="text-blue-600 text-xs mt-1 block">Önceki:
                                {{ ucfirst(str_replace('_', ' ', $gorev->tip)) }}</small>
                        </div>

                        <!-- Deadline -->
                        <div>
                            <label for="deadline" class="block text-sm font-medium text-blue-700 mb-2">
                                Deadline
                            </label>
                            <input type="datetime-local" class="admin-input @error('deadline') border-red-300 @enderror"
                                id="deadline" name="deadline"
                                value="{{ old('deadline', $gorev->deadline ? $gorev->deadline->format('Y-m-d\TH:i') : '') }}">
                            @error('deadline')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <small class="text-blue-600 text-xs mt-1 block">
                                @if ($gorev->deadline)
                                    Önceki: {{ $gorev->deadline->format('d.m.Y H:i') }}
                                    @if ($gorev->geciktiMi())
                                        <span class="text-red-500">(⚠️ {{ $gorev->gecikme_gunu }} gün gecikti!)</span>
                                    @elseif($gorev->deadlineYaklasiyorMu())
                                        <span class="text-orange-500">(⚠️ Yaklaşıyor!)</span>
                                    @endif
                                @else
                                    Önceki deadline yok
                                @endif
                            </small>
                        </div>

                        <!-- Tahmini Süre -->
                        <div>
                            <label for="tahmini_sure" class="block text-sm font-medium text-blue-700 mb-2">
                                Tahmini Süre (Dakika)
                            </label>
                            <input type="number" class="admin-input @error('tahmini_sure') border-red-300 @enderror"
                                id="tahmini_sure" name="tahmini_sure"
                                value="{{ old('tahmini_sure', $gorev->tahmini_sure) }}" min="1" placeholder="60">
                            @error('tahmini_sure')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <small class="text-blue-600 text-xs mt-1 block">
                                @if ($gorev->tahmini_sure)
                                    Önceki: {{ $gorev->tahmini_sure }} dakika
                                @else
                                    Önceki süre yok
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Atama Bilgileri Bölümü -->
            <div class="bg-gradient-to-r from-purple-50 to-violet-50 rounded-xl border border-purple-200 shadow-sm">
                <div class="p-6">
                    <h2 class="text-xl font-bold text-purple-800 mb-6 flex items-center">
                        <svg class="w-6 h-6 mr-3 text-purple-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        👥 Atama Bilgileri
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Danışman -->
                        <div>
                            <label for="danisman_id" class="block text-sm font-medium text-purple-700 mb-2">
                                Danışman
                            </label>
                            <select style="color-scheme: light dark;" class="admin-input @error('danisman_id') border-red-300 @enderror transition-all duration-200" id="danisman_id"
                                name="danisman_id">
                                <option value="">Danışman seçin...</option>
                                @foreach ($danismanlar as $danisman)
                                    <option value="{{ $danisman->id }}"
                                        {{ old('danisman_id', $gorev->danisman_id) == $danisman->id ? 'selected' : '' }}>
                                        {{ $danisman->name ?? ($danisman->ad ?? 'Danışman') }}
                                    </option>
                                @endforeach
                            </select>
                            @error('danisman_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <small class="text-purple-600 text-xs mt-1 block">
                                @if ($gorev->danisman)
                                    Önceki: {{ $gorev->danisman->name ?? ($gorev->danisman->ad ?? 'Danışman') }}
                                @else
                                    Önceki danışman yok
                                @endif
                            </small>
                        </div>

                        <!-- Müşteri -->
                        <div>
                            <label for="musteri_id" class="block text-sm font-medium text-purple-700 mb-2">
                                Müşteri
                            </label>
                            <select style="color-scheme: light dark;" class="admin-input @error('musteri_id') border-red-300 @enderror transition-all duration-200" id="musteri_id"
                                name="musteri_id">
                                <option value="">Müşteri seçin...</option>
                                @foreach ($musteriler as $musteri)
                                    <option value="{{ $musteri->id }}"
                                        {{ old('musteri_id', $gorev->musteri_id) == $musteri->id ? 'selected' : '' }}>
                                        {{ $musteri->tam_ad ?? ($musteri->firma_adi ?? 'Bilinmeyen') }}
                                    </option>
                                @endforeach
                            </select>
                            @error('musteri_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <small class="text-purple-600 text-xs mt-1 block">
                                @if ($gorev->musteri)
                                    Önceki: {{ $gorev->musteri->tam_ad ?? ($gorev->musteri->firma_adi ?? 'Bilinmeyen') }}
                                @else
                                    Önceki müşteri yok
                                @endif
                            </small>
                        </div>

                        <!-- Proje -->
                        <div>
                            <label for="proje_id" class="block text-sm font-medium text-purple-700 mb-2">
                                Proje
                            </label>
                            <select style="color-scheme: light dark;" class="admin-input @error('proje_id') border-red-300 @enderror transition-all duration-200" id="proje_id"
                                name="proje_id">
                                <option value="">Proje seçin...</option>
                                @foreach ($projeler as $proje)
                                    <option value="{{ $proje->id }}"
                                        {{ old('proje_id', $gorev->proje_id) == $proje->id ? 'selected' : '' }}>
                                        {{ $proje->ad }} ({{ ucfirst($proje->status ?? 'Aktif') }})
                                    </option>
                                @endforeach
                            </select>
                            @error('proje_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <small class="text-purple-600 text-xs mt-1 block">
                                @if ($gorev->proje)
                                    Önceki: {{ $gorev->proje->ad }}
                                @else
                                    Önceki proje yok
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ek Bilgiler Bölümü -->
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border border-green-200 shadow-sm">
                <div class="p-6">
                    <h2 class="text-xl font-bold text-green-800 mb-6 flex items-center">
                        <svg class="w-6 h-6 mr-3 text-green-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        🏷️ Ek Bilgiler
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Etiketler -->
                        <div>
                            <label for="tags" class="block text-sm font-medium text-green-700 mb-2">
                                Etiketler
                            </label>
                            <input type="text" class="admin-input @error('tags') border-red-300 @enderror"
                                id="tags" name="tags"
                                value="{{ old('tags', $gorev->tags ? implode(', ', $gorev->tags) : '') }}"
                                placeholder="müşteri, takip, acil (virgülle ayırın)">
                            @error('tags')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <small class="text-green-600 text-xs mt-1 block">
                                @if ($gorev->tags)
                                    Önceki etiketler: {{ implode(', ', $gorev->tags) }}
                                @else
                                    Önceki etiket yok
                                @endif
                            </small>
                        </div>

                        <!-- Context7 AI Önerileri -->
                        <div>
                            <label class="block text-sm font-medium text-green-700 mb-2">
                                🤖 Context7 AI Önerileri
                            </label>
                            <div class="bg-white rounded-lg p-4 border border-green-200">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm text-green-700">Akıllı öneriler status</span>
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                                    </div>
                                </div>
                                <p class="text-xs text-green-600">Görev düzenleme için AI önerileri alınır</p>
                                <div class="mt-2">
                                    <button type="button" class="inline-flex items-center px-4 py-2 border-2 border-green-500 text-green-700 dark:text-green-400 rounded-lg hover:bg-green-50 dark:hover:bg-green-900 hover:scale-105 transition-all duration-200 btn-sm touch-target-optimized touch-target-optimized"
                                        onclick="getAISuggestions()">
                                        💡 AI Önerilerini Getir
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mevcut Bilgiler ve Özet -->
            <div class="bg-gradient-to-r from-gray-50 to-slate-50 rounded-xl border border-gray-200 shadow-sm">
                <div class="p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                        <svg class="w-6 h-6 mr-3 text-gray-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        📊 Mevcut Görev Özeti
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bg-white rounded-lg p-4 border border-gray-200">
                            <div class="text-sm text-gray-600">Oluşturulma</div>
                            <div class="text-lg font-semibold text-gray-800">{{ $gorev->created_at->format('d.m.Y') }}
                            </div>
                            <div class="text-xs text-gray-500">{{ $gorev->created_at->format('H:i') }}</div>
                        </div>

                        <div class="bg-white rounded-lg p-4 border border-gray-200">
                            <div class="text-sm text-gray-600">Son Güncelleme</div>
                            <div class="text-lg font-semibold text-gray-800">{{ $gorev->updated_at->format('d.m.Y') }}
                            </div>
                            <div class="text-xs text-gray-500">{{ $gorev->updated_at->format('H:i') }}</div>
                        </div>

                        <div class="bg-white rounded-lg p-4 border border-gray-200">
                            <div class="text-sm text-gray-600">Mevcut Durum</div>
                            <div class="text-lg font-semibold">{!! $gorev->status_etiketi !!}</div>
                        </div>

                        <div class="bg-white rounded-lg p-4 border border-gray-200">
                            <div class="text-sm text-gray-600">Mevcut Öncelik</div>
                            <div class="text-lg font-semibold">{!! $gorev->oncelik_etiketi !!}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end gap-4 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.takim-yonetimi.gorevler.show', $gorev) }}" class="inline-flex items-center px-6 py-3 bg-gray-600 text-white font-semibold rounded-lg shadow-md hover:bg-gray-700 hover:scale-105 hover:shadow-lg active:scale-95 focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all duration-200 touch-target-optimized touch-target-optimized">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    İptal
                </a>
                <button type="button" class="inline-flex items-center px-6 py-3 bg-yellow-600 text-white font-semibold rounded-lg hover:bg-yellow-700 hover:scale-105 transition-all duration-200 touch-target-optimized touch-target-optimized" onclick="formuSifirla()">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Sıfırla
                </button>
                <button type="submit" class="inline-flex items-center px-6 py-3 bg-orange-600 text-white font-semibold rounded-lg shadow-md hover:bg-orange-700 hover:scale-105 hover:shadow-lg active:scale-95 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:outline-none transition-all duration-200 touch-target-optimized touch-target-optimized">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Değişiklikleri Kaydet
                </button>
            </div>
        </form>
    @endsection

    @push('scripts')
        <script>
            // Context7 AI Integration for Edit Page
            class Context7GorevEditAI {
                constructor(gorevData) {
                    this.gorevData = gorevData;
                    this.originalValues = {};
                    this.changes = new Set();
                    this.initialize();
                }

                initialize() {
                    this.loadOriginalValues();
                    this.setupEventListeners();
                    this.updateProgress();
                    this.loadMemory();
                }

                loadOriginalValues() {
                    // Orijinal değerleri sakla
                    const fields = ['baslik', 'aciklama', 'tip', 'oncelik', 'deadline', 'tahmini_sure', 'danisman_id',
                        'musteri_id', 'proje_id', 'tags'
                    ];
                    fields.forEach(field => {
                        const element = document.getElementById(field);
                        if (element) {
                            this.originalValues[field] = element.value;
                        }
                    });
                }

                loadMemory() {
                    // Context7 memory'den önceki düzenleme verilerini yükle
                    if (typeof context7 !== 'undefined' && context7.memory) {
                        context7.memory.load('gorev_edit_' + this.gorevData.id).then(data => {
                            if (data) {
                                // Önceki düzenleme verilerini geri yükle
                                Object.keys(data).forEach(key => {
                                    const element = document.getElementById(key);
                                    if (element && data[key] && !element.value) {
                                        element.value = data[key];
                                    }
                                });
                                this.updateProgress();
                            }
                        });
                    }
                }

                setupEventListeners() {
                    // Form alanlarını dinle
                    const fields = ['baslik', 'aciklama', 'tip', 'oncelik', 'deadline', 'tahmini_sure', 'danisman_id',
                        'musteri_id', 'proje_id', 'tags'
                    ];
                    fields.forEach(field => {
                        const element = document.getElementById(field);
                        if (element) {
                            element.addEventListener('input', () => this.checkChanges());
                            element.addEventListener('change', () => this.checkChanges());
                        }
                    });

                    // Tip değişikliğinde öneriler
                    const tipSelect = document.getElementById('tip');
                    if (tipSelect) {
                        tipSelect.addEventListener('change', (e) => this.suggestForTypeChange(e.target.value));
                    }

                    // Deadline kontrolü
                    const deadlineInput = document.getElementById('deadline');
                    if (deadlineInput) {
                        deadlineInput.addEventListener('change', (e) => this.validateDeadline(e.target.value));
                    }

                    // Etiket formatı
                    const tagsInput = document.getElementById('tags');
                    if (tagsInput) {
                        tagsInput.addEventListener('blur', (e) => this.formatTags(e.target));
                    }
                }

                checkChanges() {
                    this.changes.clear();
                    const fields = ['baslik', 'aciklama', 'tip', 'oncelik', 'deadline', 'tahmini_sure', 'danisman_id',
                        'musteri_id', 'proje_id', 'tags'
                    ];

                    fields.forEach(field => {
                        const element = document.getElementById(field);
                        if (element) {
                            const currentValue = element.value;
                            const originalValue = this.originalValues[field];

                            if (currentValue !== originalValue) {
                                this.changes.add(field);
                            }
                        }
                    });

                    this.updateProgress();
                    this.updateChangesCount();
                }

                updateProgress() {
                    const fields = ['baslik', 'aciklama', 'tip', 'oncelik', 'deadline', 'tahmini_sure', 'danisman_id',
                        'musteri_id', 'proje_id', 'tags'
                    ];
                    let filledCount = 0;

                    fields.forEach(field => {
                        const element = document.getElementById(field);
                        if (element && element.value.trim() !== '') {
                            filledCount++;
                        }
                    });

                    const progress = Math.round((filledCount / fields.length) * 100);
                    const progressBar = document.getElementById('progress-bar');
                    const progressText = document.getElementById('progress-text');

                    if (progressBar) {
                        progressBar.style.width = progress + '%';
                    }
                    if (progressText) {
                        progressText.textContent = progress + '%';
                    }

                    // Progress renklendirmesi
                    if (progressBar) {
                        progressBar.className = 'bg-gradient-to-r h-2 rounded-full transition-all duration-1000';
                        if (progress < 30) {
                            progressBar.classList.add('from-red-500', 'to-red-600');
                        } else if (progress < 70) {
                            progressBar.classList.add('from-yellow-500', 'to-yellow-600');
                        } else {
                            progressBar.classList.add('from-green-500', 'to-green-600');
                        }
                    }
                }

                updateChangesCount() {
                    const changesCountElement = document.getElementById('changes-count');
                    if (changesCountElement) {
                        changesCountElement.textContent = this.changes.size;
                    }
                }

                suggestForTypeChange(tip) {
                    const suggestions = {
                        'musteri_takibi': {
                            oncelik: 'normal',
                            tahmini_sure: '120',
                            tags: 'müşteri, takip, iletişim'
                        },
                        'ilan_hazirlama': {
                            oncelik: 'yuksek',
                            tahmini_sure: '180',
                            tags: 'ilan, fotoğraf, açıklama'
                        },
                        'musteri_ziyareti': {
                            oncelik: 'yuksek',
                            tahmini_sure: '240',
                            tags: 'ziyaret, müşteri, yerinde'
                        },
                        'dokuman_hazirlama': {
                            oncelik: 'normal',
                            tahmini_sure: '90',
                            tags: 'doküman, hazırlama, kontrol'
                        }
                    };

                    const suggestion = suggestions[tip];
                    if (suggestion) {
                        this.showAISuggestion(suggestion, tip);
                    }
                }

                showAISuggestion(suggestion, tip) {
                    if (typeof context7 !== 'undefined') {
                        context7.ui.showToast({
                            type: 'info',
                            title: '🤖 AI Düzenleme Önerisi',
                            message: `"${tip.replace('_', ' ').toUpperCase()}" tipi için önerilen: Öncelik: ${suggestion.oncelik}, Süre: ${suggestion.tahmini_sure}dk`,
                            duration: 5000
                        });
                    }
                }

                validateDeadline(deadline) {
                    if (!deadline) return;

                    const deadlineDate = new Date(deadline);
                    const now = new Date();

                    if (deadlineDate <= now) {
                        this.showError('Deadline geçmiş bir tarih olamaz!');
                        document.getElementById('deadline').value = '';
                    }
                }

                formatTags(input) {
                    if (!input.value) return;

                    let tags = input.value.split(',')
                        .map(tag => tag.trim())
                        .filter(tag => tag.length > 0)
                        .join(', ');
                    input.value = tags;
                }

                showError(message) {
                    if (typeof context7 !== 'undefined') {
                        context7.ui.showToast({
                            type: 'error',
                            title: 'Hata',
                            message: message,
                            duration: 3000
                        });
                    } else {
                        alert(message);
                    }
                }

                resetForm() {
                    // Orijinal değerleri geri yükle
                    Object.keys(this.originalValues).forEach(field => {
                        const element = document.getElementById(field);
                        if (element) {
                            element.value = this.originalValues[field];
                        }
                    });

                    this.changes.clear();
                    this.updateProgress();
                    this.updateChangesCount();

                    if (typeof context7 !== 'undefined') {
                        context7.ui.showToast({
                            type: 'success',
                            title: 'Form Sıfırlandı',
                            message: 'Tüm değişiklikler geri alındı',
                            duration: 2000
                        });
                    }
                }

                getAISuggestions() {
                    // AI önerilerini al
                    if (typeof context7 !== 'undefined') {
                        context7.ai.analyzeTask(this.gorevData).then(suggestions => {
                            this.applyAISuggestions(suggestions);
                        });
                    }
                }

                applyAISuggestions(suggestions) {
                    if (suggestions.priority) {
                        const oncelikElement = document.getElementById('oncelik');
                        if (oncelikElement) oncelikElement.value = suggestions.priority;
                    }

                    if (suggestions.estimated_time) {
                        const sureElement = document.getElementById('tahmini_sure');
                        if (sureElement) sureElement.value = suggestions.estimated_time;
                    }

                    if (suggestions.tags) {
                        const tagsElement = document.getElementById('tags');
                        if (tagsElement) tagsElement.value = suggestions.tags;
                    }

                    this.checkChanges();

                    if (typeof context7 !== 'undefined') {
                        context7.ui.showToast({
                            type: 'success',
                            title: 'AI Önerileri Uygulandı',
                            message: 'Görev için optimize öneriler uygulandı',
                            duration: 3000
                        });
                    }
                }
            }

            // Global değişkenler
            let context7GorevEditAI;
            let formChanged = false;

            // Sayfa yüklendiğinde başlat
            document.addEventListener('DOMContentLoaded', function() {
                // Görev verilerini JavaScript'e aktar
                const gorevData = @json($gorev);

                // Context7 AI'yi başlat
                context7GorevEditAI = new Context7GorevEditAI(gorevData);

                // Form değişiklik kontrolü
                setupFormChangeTracking();
            });

            // Form değişiklik takibi
            function setupFormChangeTracking() {
                const form = document.getElementById('gorevEditForm');
                const formInputs = form.querySelectorAll('input, select, textarea');

                formInputs.forEach(input => {
                    input.addEventListener('change', function() {
                        formChanged = true;
                    });

                    input.addEventListener('keyup', function() {
                        formChanged = true;
                    });
                });

                // Sayfa kapatma uyarısı
                window.addEventListener('beforeunload', function(e) {
                    if (formChanged) {
                        e.preventDefault();
                        e.returnValue =
                            'Kaydedilmemiş değişiklikler var. Sayfadan ayrılmak istediğinizden emin misiniz?';
                        return e.returnValue;
                    }
                });

                // Form submit edildiğinde değişiklik bayrağını sıfırla
                form.addEventListener('submit', function() {
                    formChanged = false;
                });
            }

            // Form sıfırlama
            function formuSifirla() {
                if (confirm('Formu sıfırlamak istediğinizden emin misiniz? Tüm değişiklikler kaybolacak.')) {
                    if (context7GorevEditAI) {
                        context7GorevEditAI.resetForm();
                    }
                    formChanged = false;
                }
            }

            // AI önerilerini getir
            function getAISuggestions() {
                if (context7GorevEditAI) {
                    context7GorevEditAI.getAISuggestions();
                }
            }

            // Form validation
            document.getElementById('gorevEditForm').addEventListener('submit', function(e) {
                const baslik = document.getElementById('baslik').value.trim();
                const oncelik = document.getElementById('oncelik').value;
                const tip = document.getElementById('tip').value;

                if (!baslik) {
                    e.preventDefault();
                    if (context7GorevEditAI) {
                        context7GorevEditAI.showError('Görev başlığı zorunludur!');
                    }
                    document.getElementById('baslik').focus();
                    return false;
                }

                if (!oncelik) {
                    e.preventDefault();
                    if (context7GorevEditAI) {
                        context7GorevEditAI.showError('Öncelik seçimi zorunludur!');
                    }
                    document.getElementById('oncelik').focus();
                    return false;
                }

                if (!tip) {
                    e.preventDefault();
                    if (context7GorevEditAI) {
                        context7GorevEditAI.showError('Görev tipi seçimi zorunludur!');
                    }
                    document.getElementById('tip').focus();
                    return false;
                }

                // Başarı mesajı
                if (typeof context7 !== 'undefined') {
                    context7.ui.showToast({
                        type: 'info',
                        title: 'Değişiklikler Kaydediliyor',
                        message: 'Görev bilgileriniz güncelleniyor...',
                        duration: 2000
                    });
                }
            });

            // Sayfa kapatılmadan önce form verilerini kaydet
            window.addEventListener('beforeunload', function() {
                if (typeof context7 !== 'undefined' && context7GorevEditAI) {
                    const gorevData = @json($gorev);
                    const formData = {};
                    const fields = ['baslik', 'aciklama', 'tip', 'oncelik', 'deadline', 'tahmini_sure', 'danisman_id',
                        'musteri_id', 'proje_id', 'tags'
                    ];

                    fields.forEach(field => {
                        const element = document.getElementById(field);
                        if (element) {
                            formData[field] = element.value;
                        }
                    });

                    context7.memory.save('gorev_edit_' + gorevData.id, formData);
                }
            });
        </script>
    @endpush
