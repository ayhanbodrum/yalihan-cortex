@extends('admin.layouts.neo')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                🔗 Alan İlişkileri Yönetimi
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                {{ $kategori->name }} kategorisi için alan tanımlamaları
            </p>
        </div>
        <div class="flex gap-2">
            <button onclick="openAddFieldModal()" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-lg shadow-lg hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 active:scale-95">
                <i class="fas fa-plus mr-2"></i>
                Yeni Alan Ekle
            </button>
            <a href="{{ route('admin.property-type-manager.show', $kategori->id) }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 text-white font-semibold rounded-lg shadow-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 active:scale-95 dark:bg-gray-700 dark:hover:bg-gray-600">
                <i class="fas fa-arrow-left mr-2"></i>
                Geri Dön
            </a>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-6 py-4 rounded-lg">
        <i class="fas fa-check-circle mr-2"></i>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 px-6 py-4 rounded-lg">
        <i class="fas fa-exclamation-circle mr-2"></i>
        {{ session('error') }}
    </div>
    @endif

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-xl p-6 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm mb-1">Toplam Alan</p>
                    <p class="text-3xl font-bold">{{ $fieldDependencies->flatten()->count() }}</p>
                </div>
                <i class="fas fa-list text-4xl opacity-20"></i>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 text-white rounded-xl p-6 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm mb-1">Aktif Alan</p>
                    <p class="text-3xl font-bold">{{ $fieldDependencies->flatten()->where('enabled', true)->count() }}</p>
                </div>
                <i class="fas fa-check-circle text-4xl opacity-20"></i>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-xl p-6 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm mb-1">Yayın Tipi</p>
                    <p class="text-3xl font-bold">{{ $yayinTipleri->count() }}</p>
                </div>
                <i class="fas fa-tags text-4xl opacity-20"></i>
            </div>
        </div>
    </div>

    <!-- Field Dependencies List -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                📋 Alan Listesi
            </h2>
            <div class="flex gap-2">
                <select id="filterYayinTipi" class="text-sm max-w-xs px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                    <option value="">Tüm Yayın Tipleri</option>
                    @foreach($yayinTipleri as $yt)
                    <option value="{{ $yt->yayin_tipi }}">{{ $yt->yayin_tipi }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        @if($fieldDependencies->isEmpty())
        <!-- Empty State -->
        <div class="text-center py-12">
            <i class="fas fa-inbox text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">
                Henüz alan ilişkisi tanımlı değil
            </h3>
            <p class="text-gray-500 dark:text-gray-400 mb-4">
                Bu kategori için alan ilişkilerini tanımlayarak ilan formlarında hangi alanların görüneceğini belirleyin.
            </p>
            <button onclick="openAddFieldModal()" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-lg shadow-lg hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 active:scale-95">
                <i class="fas fa-plus mr-2"></i>
                İlk Alanı Ekle
            </button>
        </div>
        @else
        <!-- Fields Table -->
        <div class="overflow-x-auto">
            @foreach($fieldDependencies as $yayinTipi => $fields)
            <div class="mb-6 last:mb-0 yayin-tipi-group" data-yayin-tipi="{{ $yayinTipi }}">
                <!-- Yayın Tipi Başlığı -->
                <div class="flex items-center justify-between mb-3 pb-2 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                        📌 {{ $yayinTipi }}
                        <span class="ml-2 text-sm font-normal text-gray-500">
                            ({{ $fields->count() }} alan)
                        </span>
                    </h3>
                </div>

                <!-- Fields List -->
                <div class="space-y-2" id="fields-{{ Str::slug($yayinTipi) }}">
                    @foreach($fields->sortBy('order') as $field)
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors field-row"
                         data-field-id="{{ $field->id }}">
                        <!-- Field Info -->
                        <div class="flex items-center gap-4 flex-1">
                            <div class="flex items-center justify-center w-10 h-10 bg-white dark:bg-gray-700 rounded-lg text-xl">
                                {{ $field->field_icon ?? '📋' }}
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <!-- 🆕 Inline Rename Feature -->
                                    <div class="field-name-container flex items-center gap-2 flex-1">
                                        <h4 class="font-semibold text-gray-900 dark:text-white field-name-display"
                                            data-field-id="{{ $field->id }}">
                                            {{ $field->field_name }}
                                        </h4>
                                        <input type="text"
                                               class="hidden field-name-input px-3 py-1 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-green-500"
                                               data-field-id="{{ $field->id }}"
                                               value="{{ $field->field_name }}">
                                        <button type="button"
                                                class="field-rename-btn text-gray-400 hover:text-green-600 dark:hover:text-green-400 transition-colors"
                                                onclick="enableRename({{ $field->id }})"
                                                title="Adı Değiştir">
                                            <i class="fas fa-edit text-sm"></i>
                                        </button>
                                        <button type="button"
                                                class="hidden field-rename-save text-green-600 hover:text-green-700"
                                                onclick="saveRename({{ $field->id }})"
                                                title="Kaydet">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button"
                                                class="hidden field-rename-cancel text-red-600 hover:text-red-700"
                                                onclick="cancelRename({{ $field->id }})"
                                                title="İptal">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <span class="px-2 py-0.5 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs rounded-full">
                                        {{ $field->field_type }}
                                    </span>
                                    @if($field->field_unit)
                                    <span class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs rounded-full">
                                        {{ $field->field_unit }}
                                    </span>
                                    @endif
                                    @if($field->required)
                                    <span class="px-2 py-0.5 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 text-xs rounded-full">
                                        Zorunlu
                                    </span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2 mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    <span>📁 {{ $field->field_category }}</span>
                                    <span>•</span>
                                    <span>🔑 {{ $field->field_slug }}</span>
                                    @if($field->searchable)
                                    <span>•</span>
                                    <span class="text-blue-600 dark:text-blue-400">
                                        <i class="fas fa-search"></i> Aranabilir
                                    </span>
                                    @endif
                                    @if($field->show_in_card)
                                    <span>•</span>
                                    <span class="text-green-600 dark:text-green-400">
                                        <i class="fas fa-id-card"></i> Kartta Göster
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-2">
                            <!-- Enabled Toggle -->
                            @php
                                // Explicit boolean conversion
                                $isEnabled = (bool)($field->enabled ?? false);
                                $enabledValue = $isEnabled ? '1' : '0';
                            @endphp
                            <label class="relative inline-flex items-center cursor-pointer"
                                   title="Enabled: {{ $enabledValue }} (Field ID: {{ $field->id }})">
                                <input type="checkbox"
                                       class="sr-only peer field-toggle"
                                       data-field-id="{{ $field->id }}"
                                       data-field-name="{{ $field->field_name }}"
                                       data-enabled="{{ $enabledValue }}"
                                       {{ $isEnabled ? 'checked' : '' }}
                                       onchange="console.log('🎯 onchange fired for field {{ $field->id }}'); toggleField(this)">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 dark:peer-focus:ring-green-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600"></div>
                                <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">{{ $enabledValue }}</span>
                            </label>

                            <!-- Edit Button -->
                            <button onclick="openEditFieldModal({{ $field->id }}, {{ json_encode($field) }})"
                                    class="px-3 py-1 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-200 border border-blue-200 dark:border-blue-800 rounded-md text-sm flex items-center gap-2"
                                    title="Düzenle">
                                <i class="fas fa-edit"></i>
                                <span>Düzenle</span>
                            </button>

                            <!-- Delete Button -->
                            <form action="{{ route('admin.property-type-manager.field-dependencies.destroy', [$kategori->id, $field->id]) }}"
                                  method="POST"
                                  class="inline-block"
                                  onsubmit="return confirm('Bu alanı silmek istediğinizden emin misiniz?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="p-2 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-200"
                                        title="Sil">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>

                            <!-- Drag Handle (gelecek özellik) -->
                            <div class="p-2 text-gray-400 cursor-move drag-handle" title="Sürükle">
                                <i class="fas fa-grip-vertical"></i>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

<!-- Add Field Modal -->
<div id="addFieldModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
                ➕ Yeni Alan Ekle
            </h3>
            <button onclick="closeAddFieldModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <form action="{{ route('admin.property-type-manager.field-dependencies.store', $kategori->id) }}" method="POST" class="p-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Yayın Tipi -->
                <div class="md:col-span-2">
                    <label for="yayin_tipi" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Yayın Tipi <span class="text-red-500">*</span>
                    </label>
                    <select name="yayin_tipi" id="yayin_tipi" required
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="">Seçiniz...</option>
                        @foreach($yayinTipleri as $yt)
                        <option value="{{ $yt->yayin_tipi }}">{{ $yt->yayin_tipi }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Field Name -->
                <div>
                    <label for="field_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Alan Adı <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="field_name" id="field_name" required
                           placeholder="Günlük Fiyat"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>

                <!-- Field Slug -->
                <div>
                    <label for="field_slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Alan Slug <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="field_slug" id="field_slug" required
                           placeholder="gunluk_fiyat"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>

                <!-- Field Type -->
                <div>
                    <label for="field_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Alan Tipi <span class="text-red-500">*</span>
                    </label>
                    <select name="field_type" id="field_type" required
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="">Seçiniz...</option>
                        <option value="text">Metin (text)</option>
                        <option value="number">Sayı (number)</option>
                        <option value="boolean">Evet/Hayır (boolean)</option>
                        <option value="select">Seçim Listesi (select)</option>
                        <option value="textarea">Uzun Metin (textarea)</option>
                        <option value="date">Tarih (date)</option>
                        <option value="price">Fiyat (price)</option>
                    </select>
                </div>

                <!-- Field Category -->
                <div>
                    <label for="field_category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Alan Kategorisi <span class="text-red-500">*</span>
                    </label>
                    <select name="field_category" id="field_category" required
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="">Seçiniz...</option>
                        <option value="fiyat">💰 Fiyat</option>
                        <option value="ozellik">⭐ Özellik</option>
                        <option value="dokuman">📄 Döküman</option>
                        <option value="sezonluk">🌞 Sezonluk</option>
                        <option value="arsa">🗺️ Arsa</option>
                        <option value="olanaklar">🏊 Olanaklar</option>
                    </select>
                </div>

                <!-- Field Icon -->
                <div>
                    <label for="field_icon" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        İkon (Emoji)
                    </label>
                    <input type="text" name="field_icon" id="field_icon"
                           placeholder="🌞"
                           maxlength="10"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <p class="mt-1 text-xs text-gray-500">Örnek: 🌞, 💰, 🏠, 📐</p>
                </div>

                <!-- Field Unit -->
                <div>
                    <label for="field_unit" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Birim
                    </label>
                    <input type="text" name="field_unit" id="field_unit"
                           placeholder="₺, m², gün"
                           maxlength="20"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>

                <!-- Field Options (JSON) -->
                <div class="md:col-span-2" id="field_options_wrapper" style="display: none;">
                    <label for="field_options" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Seçenekler (JSON formatında)
                    </label>
                    <textarea name="field_options" id="field_options" rows="3"
                              placeholder='["1+1", "2+1", "3+1"]'
                              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"></textarea>
                    <p class="mt-1 text-xs text-gray-500">Sadece "select" tipi için gereklidir</p>
                </div>

                <!-- Order -->
                <div>
                    <label for="order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Sıra
                    </label>
                    <input type="number" name="order" id="order" value="{{ (($fieldDependencies->flatten()->max('order') ?? 0) + 1) }}" min="0"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>

                <!-- Checkboxes -->
                <div class="md:col-span-2 grid grid-cols-2 md:grid-cols-3 gap-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="enabled" value="1" checked
                               class="rounded text-green-600 focus:ring-green-500 mr-2">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Aktif</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" name="required" value="1"
                               class="rounded text-green-600 focus:ring-green-500 mr-2">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Zorunlu</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" name="searchable" value="1"
                               class="rounded text-green-600 focus:ring-green-500 mr-2">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Aranabilir</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" name="show_in_card" value="1"
                               class="rounded text-green-600 focus:ring-green-500 mr-2">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Kartta Göster</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" name="ai_auto_fill" value="1"
                               class="rounded text-green-600 focus:ring-green-500 mr-2">
                        <span class="text-sm text-gray-700 dark:text-gray-300">AI Doldurma</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" name="ai_suggestion" value="1"
                               class="rounded text-green-600 focus:ring-green-500 mr-2">
                        <span class="text-sm text-gray-700 dark:text-gray-300">AI Öneri</span>
                    </label>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <button type="button" onclick="closeAddFieldModal()" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white font-semibold rounded-lg shadow-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 active:scale-95 dark:bg-gray-700 dark:hover:bg-gray-600">
                    <i class="fas fa-times mr-2"></i>
                    İptal
                </button>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-lg shadow-lg hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 active:scale-95">
                    <i class="fas fa-save mr-2"></i>
                    Kaydet
                </button>
            </div>
        </form>
        </div>
    </div>
</div>

<!-- Edit Field Modal -->
<div id="editFieldModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
                ✏️ Alanı Düzenle
            </h3>
            <button onclick="closeEditFieldModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="editFieldForm" method="POST" class="p-6">
            @csrf
            @method('PUT')

            <!-- ⚠️ Identity Fields Warning -->
            <div class="mb-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                <div class="flex items-start gap-2">
                    <i class="fas fa-info-circle text-yellow-600 dark:text-yellow-400 mt-0.5"></i>
                    <div class="text-sm text-yellow-800 dark:text-yellow-200">
                        <strong>Not:</strong> Yayın Tipi, Alan Slug ve Kategori alanları kimlik bilgisidir ve değiştirilemez.
                        Bu alanları değiştirmek isterseniz yeni bir alan oluşturup eskisini silebilirsiniz.
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Identity Fields (Readonly) -->
                <div class="md:col-span-2">
                    <label for="edit_yayin_tipi" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Yayın Tipi <span class="text-yellow-500" title="Bu alan değiştirilemez">🔒</span>
                    </label>
                    <select id="edit_yayin_tipi" disabled
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-900 text-gray-700 dark:text-gray-400 rounded-lg cursor-not-allowed opacity-75">
                        @foreach($yayinTipleri as $yt)
                        <option value="{{ $yt->yayin_tipi }}">{{ $yt->yayin_tipi }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        <i class="fas fa-lock mr-1"></i> Bu alan kimlik bilgisidir ve değiştirilemez
                    </p>
                </div>

                <div>
                    <label for="edit_field_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Alan Adı <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="field_name" id="edit_field_name" required
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>

                <div>
                    <label for="edit_field_slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Alan Slug <span class="text-yellow-500" title="Bu alan değiştirilemez">🔒</span>
                    </label>
                    <input type="text" id="edit_field_slug" readonly
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-900 text-gray-700 dark:text-gray-400 rounded-lg cursor-not-allowed opacity-75">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        <i class="fas fa-lock mr-1"></i> Bu alan kimlik bilgisidir ve değiştirilemez
                    </p>
                </div>

                <div>
                    <label for="edit_field_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Alan Tipi <span class="text-red-500">*</span>
                    </label>
                    <select name="field_type" id="edit_field_type" required
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="text">Metin (text)</option>
                        <option value="number">Sayı (number)</option>
                        <option value="boolean">Evet/Hayır (boolean)</option>
                        <option value="select">Seçim Listesi (select)</option>
                        <option value="textarea">Uzun Metin (textarea)</option>
                        <option value="date">Tarih (date)</option>
                        <option value="price">Fiyat (price)</option>
                    </select>
                </div>

                <div>
                    <label for="edit_field_category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Alan Kategorisi <span class="text-red-500">*</span>
                    </label>
                    <select name="field_category" id="edit_field_category" required
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="fiyat">💰 Fiyat</option>
                        <option value="ozellik">⭐ Özellik</option>
                        <option value="dokuman">📄 Döküman</option>
                        <option value="sezonluk">🌞 Sezonluk</option>
                        <option value="arsa">🗺️ Arsa</option>
                        <option value="olanaklar">🏊 Olanaklar</option>
                    </select>
                </div>

                <div>
                    <label for="edit_field_icon" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        İkon
                    </label>
                    <input type="text" name="field_icon" id="edit_field_icon" maxlength="10"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>

                <div>
                    <label for="edit_field_unit" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Birim
                    </label>
                    <input type="text" name="field_unit" id="edit_field_unit" maxlength="20"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>

                <div>
                    <label for="edit_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Sıra
                    </label>
                    <input type="number" name="order" id="edit_order" min="0"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>

                <div id="edit_field_options_wrapper" class="md:col-span-2" style="display: none;">
                    <label for="edit_field_options" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Seçenekler (JSON)
                    </label>
                    <textarea name="field_options" id="edit_field_options" rows="3"
                              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"></textarea>
                </div>

                <div class="md:col-span-2 grid grid-cols-2 md:grid-cols-3 gap-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="enabled" id="edit_enabled" value="1"
                               class="rounded text-green-600 focus:ring-green-500 mr-2">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Aktif</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" name="required" id="edit_required" value="1"
                               class="rounded text-green-600 focus:ring-green-500 mr-2">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Zorunlu</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" name="searchable" id="edit_searchable" value="1"
                               class="rounded text-green-600 focus:ring-green-500 mr-2">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Aranabilir</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" name="show_in_card" id="edit_show_in_card" value="1"
                               class="rounded text-green-600 focus:ring-green-500 mr-2">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Kartta Göster</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" name="ai_auto_fill" id="edit_ai_auto_fill" value="1"
                               class="rounded text-green-600 focus:ring-green-500 mr-2">
                        <span class="text-sm text-gray-700 dark:text-gray-300">AI Doldurma</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" name="ai_suggestion" id="edit_ai_suggestion" value="1"
                               class="rounded text-green-600 focus:ring-green-500 mr-2">
                        <span class="text-sm text-gray-700 dark:text-gray-300">AI Öneri</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <button type="button" onclick="closeEditFieldModal()" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white font-semibold rounded-lg shadow-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 active:scale-95 dark:bg-gray-700 dark:hover:bg-gray-600">
                    <i class="fas fa-times mr-2"></i>
                    İptal
                </button>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-lg shadow-lg hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 active:scale-95">
                    <i class="fas fa-save mr-2"></i>
                    Güncelle
                </button>
            </div>
        </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
/* 🆕 Inline Rename Styles */
.field-name-input {
    min-width: 200px;
    transition: all 0.2s ease;
}

.field-name-input:focus {
    box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
}

.field-rename-btn {
    opacity: 0.8; /* Her zaman görünür */
    transition: opacity 0.2s ease;
}

.field-row:hover .field-rename-btn {
    opacity: 1;
}

.field-name-container {
    position: relative;
}

/* Rename mode active state */
.field-name-container.editing {
    background-color: rgba(34, 197, 94, 0.05);
    padding: 4px 8px;
    border-radius: 6px;
}
</style>
@endpush

@push('scripts')
<script>
// Modal Functions
function openAddFieldModal() {
    const modal = document.getElementById('addFieldModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeAddFieldModal() {
    const modal = document.getElementById('addFieldModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function openEditFieldModal(fieldId, fieldData) {
    // Populate form
    document.getElementById('editFieldForm').action = "{{ route('admin.property-type-manager.field-dependencies.update', [$kategori->id, '__FIELD_ID__']) }}".replace('__FIELD_ID__', fieldId);

    document.getElementById('edit_yayin_tipi').value = fieldData.yayin_tipi;
    document.getElementById('edit_field_name').value = fieldData.field_name;
    document.getElementById('edit_field_slug').value = fieldData.field_slug;
    document.getElementById('edit_field_type').value = fieldData.field_type;
    document.getElementById('edit_field_category').value = fieldData.field_category;
    document.getElementById('edit_field_icon').value = fieldData.field_icon || '';
    document.getElementById('edit_field_unit').value = fieldData.field_unit || '';
    document.getElementById('edit_order').value = fieldData.order || 0;

    document.getElementById('edit_enabled').checked = fieldData.enabled;
    document.getElementById('edit_required').checked = fieldData.required;
    document.getElementById('edit_searchable').checked = fieldData.searchable;
    document.getElementById('edit_show_in_card').checked = fieldData.show_in_card;
    document.getElementById('edit_ai_auto_fill').checked = fieldData.ai_auto_fill;
    document.getElementById('edit_ai_suggestion').checked = fieldData.ai_suggestion;

    // Options
    if (fieldData.field_type === 'select' && fieldData.field_options) {
        document.getElementById('edit_field_options').value = JSON.stringify(fieldData.field_options, null, 2);
        document.getElementById('edit_field_options_wrapper').style.display = 'block';
    }

    const modal = document.getElementById('editFieldModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeEditFieldModal() {
    const modal = document.getElementById('editFieldModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// Toggle Field (AJAX)
function toggleField(checkbox) {
    // ✅ Function exists check
    if (typeof checkbox === 'undefined' || !checkbox) {
        console.error('❌ toggleField: checkbox parameter is missing!');
        return;
    }

    const fieldId = checkbox.dataset?.fieldId || checkbox.getAttribute('data-field-id');
    const enabled = checkbox.checked;
    const oldValue = checkbox.dataset?.enabled || checkbox.getAttribute('data-enabled');

    console.log('🔵 Toggle clicked:', {
        fieldId: fieldId,
        fieldName: checkbox.dataset?.fieldName || checkbox.getAttribute('data-field-name'),
        oldValue: oldValue,
        newValue: enabled ? '1' : '0',
        checkbox: checkbox
    });

    if (!fieldId) {
        console.error('❌ Field ID is missing!', checkbox);
        checkbox.checked = !enabled; // Revert
        return;
    }

    // Optimistic update: UI'yi hemen güncelle
    checkbox.dataset.enabled = enabled ? '1' : '0';
    if (checkbox.setAttribute) {
        checkbox.setAttribute('data-enabled', enabled ? '1' : '0');
    }

    fetch("{{ route('admin.property-type-manager.toggle-field-dependency') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            field_id: fieldId,
            enabled: enabled
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('✅ Toggle response:', data);

        if (data.success) {
            // ✅ Başarılı: UI zaten güncellendi, sadece toast göster
            window.toast?.success(data.message || 'Güncelleme başarılı');

            // Update checkbox data attribute
            checkbox.dataset.enabled = data.data?.enabled?.toString() || (enabled ? '1' : '0');

            // Update label tooltip
            const label = checkbox.closest('label');
            if (label) {
                label.setAttribute('title', `Enabled: ${data.data?.enabled || (enabled ? '1' : '0')}`);
            }
        } else {
            // ❌ Hata: UI'yi geri al
            console.error('❌ Toggle failed:', data);
            checkbox.checked = !oldValue;
            checkbox.dataset.enabled = !oldValue ? '1' : '0';
            window.toast?.error(data.message || 'Güncelleme başarısız!');
        }
    })
    .catch(error => {
        console.error('❌ Toggle error:', error);
        // ❌ Hata: UI'yi geri al
        checkbox.checked = !oldValue;
        checkbox.dataset.enabled = !oldValue ? '1' : '0';
        window.toast?.error('Bağlantı hatası! Lütfen tekrar deneyin.');
    });
}

// 🆕 Inline Rename Functions
function enableRename(fieldId) {
    const row = document.querySelector(`[data-field-id="${fieldId}"]`);
    const container = row.querySelector('.field-name-container');
    const display = row.querySelector(`.field-name-display[data-field-id="${fieldId}"]`);
    const input = row.querySelector(`.field-name-input[data-field-id="${fieldId}"]`);
    const renameBtn = row.querySelector('.field-rename-btn');
    const saveBtn = row.querySelector('.field-rename-save');
    const cancelBtn = row.querySelector('.field-rename-cancel');

    // Add editing class for visual feedback
    container.classList.add('editing');

    // Hide display, show input
    display.classList.add('hidden');
    input.classList.remove('hidden');
    input.focus();
    input.select();

    // Toggle buttons
    renameBtn.classList.add('hidden');
    saveBtn.classList.remove('hidden');
    cancelBtn.classList.remove('hidden');
}

function saveRename(fieldId) {
    const row = document.querySelector(`[data-field-id="${fieldId}"]`);
    const input = row.querySelector(`.field-name-input[data-field-id="${fieldId}"]`);
    const newName = input.value.trim();

    if (!newName) {
        window.toast?.error('Alan adı boş olamaz!');
        return;
    }

    // AJAX request to update field name
    fetch("{{ route('admin.property-type-manager.field-dependencies.update', [$kategori->id, '__FIELD_ID__']) }}".replace('__FIELD_ID__', fieldId), {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            field_name: newName,
            _method: 'PUT'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update display
            const display = row.querySelector(`.field-name-display[data-field-id="${fieldId}"]`);
            display.textContent = newName;

            // Reset UI
            cancelRename(fieldId);

            window.toast?.success('✅ Alan adı güncellendi!');
        } else {
            window.toast?.error('Hata: ' + (data.message || 'Güncellenemedi'));
        }
    })
    .catch(error => {
        console.error('Rename error:', error);
        window.toast?.error('Bağlantı hatası!');
    });
}

function cancelRename(fieldId) {
    const row = document.querySelector(`[data-field-id="${fieldId}"]`);
    const container = row.querySelector('.field-name-container');
    const display = row.querySelector(`.field-name-display[data-field-id="${fieldId}"]`);
    const input = row.querySelector(`.field-name-input[data-field-id="${fieldId}"]`);
    const renameBtn = row.querySelector('.field-rename-btn');
    const saveBtn = row.querySelector('.field-rename-save');
    const cancelBtn = row.querySelector('.field-rename-cancel');

    // Remove editing class
    container.classList.remove('editing');

    // Reset input value
    input.value = display.textContent;

    // Show display, hide input
    display.classList.remove('hidden');
    input.classList.add('hidden');

    // Toggle buttons
    renameBtn.classList.remove('hidden');
    saveBtn.classList.add('hidden');
    cancelBtn.classList.add('hidden');
}

// Keyboard shortcuts for rename (Enter = save, Esc = cancel)
document.addEventListener('keydown', function(e) {
    const activeInput = document.activeElement;
    if (activeInput && activeInput.classList.contains('field-name-input')) {
        const fieldId = activeInput.dataset.fieldId;

        if (e.key === 'Enter') {
            e.preventDefault();
            saveRename(fieldId);
        } else if (e.key === 'Escape') {
            e.preventDefault();
            cancelRename(fieldId);
        }
    }
});

// Field Type Change - Show/Hide Options
document.getElementById('field_type')?.addEventListener('change', function() {
    const optionsWrapper = document.getElementById('field_options_wrapper');
    if (this.value === 'select') {
        optionsWrapper.style.display = 'block';
    } else {
        optionsWrapper.style.display = 'none';
    }
});

document.getElementById('edit_field_type')?.addEventListener('change', function() {
    const optionsWrapper = document.getElementById('edit_field_options_wrapper');
    if (this.value === 'select') {
        optionsWrapper.style.display = 'block';
    } else {
        optionsWrapper.style.display = 'none';
    }
});

// Auto-generate slug from name
document.getElementById('field_name')?.addEventListener('input', function() {
    const slug = this.value
        .toLowerCase()
        .replace(/ğ/g, 'g')
        .replace(/ü/g, 'u')
        .replace(/ş/g, 's')
        .replace(/ı/g, 'i')
        .replace(/ö/g, 'o')
        .replace(/ç/g, 'c')
        .replace(/[^a-z0-9]+/g, '_')
        .replace(/^_+|_+$/g, '');
    document.getElementById('field_slug').value = slug;
});

// Filter by Yayin Tipi
document.getElementById('filterYayinTipi')?.addEventListener('change', function() {
    const selectedYayinTipi = this.value;
    const groups = document.querySelectorAll('.yayin-tipi-group');

    groups.forEach(group => {
        if (selectedYayinTipi === '' || group.dataset.yayinTipi === selectedYayinTipi) {
            group.style.display = 'block';
        } else {
            group.style.display = 'none';
        }
    });
});
</script>
@endpush
