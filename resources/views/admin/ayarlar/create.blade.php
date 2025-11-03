@extends('admin.layouts.neo')

@section('title', 'Yeni Ayar Oluştur - Yalıhan Emlak Pro')

@section('content')
    <div class="content-header mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold flex items-center text-gray-800">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-plus text-white text-xl"></i>
                    </div>
                    Yeni Ayar Oluştur
                </h1>
                <p class="text-lg text-gray-600 mt-2">Sistem ayarı ekleyin</p>
            </div>
            <a href="{{ route('admin.ayarlar.index') }}" class="neo-btn neo-btn neo-btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>
                Geri Dön
            </a>
        </div>
    </div>

    <div class="px-6">
        <div class="max-w-2xl mx-auto">
            <form action="{{ route('admin.ayarlar.store') }}" method="POST" class="neo-card p-6">
                @csrf

                <div class="space-y-6">
                    <!-- Ayar Anahtarı - Neo Component -->
                    <x-neo-input
                        name="key"
                        label="Ayar Anahtarı"
                        :required="true"
                        placeholder="ayar_anahtari"
                        helpText="Benzersiz anahtar adı (örn: site_title, max_upload_size)"
                        icon="fas fa-key" />

                    <!-- Ayar Değeri - Textarea geçici standart -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ayar Değeri</label>
                        <textarea name="value" rows="3"
                                  class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200" placeholder="Ayar değerini girin..."></textarea>
                    </div>

                    <!-- Veri Tipi - Neo Component -->
                    <x-neo-select
                        name="type"
                        label="Veri Tipi"
                        :required="true"
                        placeholder="Veri tipi seçin..."
                        icon="fas fa-database">
                        <option value="">Veri tipi seçin...</option>
                        <option value="string">String (Metin)</option>
                        <option value="integer">Integer (Sayı)</option>
                        <option value="boolean">Boolean (Doğru/Yanlış)</option>
                        <option value="json">JSON (Yapılandırılmış Veri)</option>
                    </x-neo-select>

                    <!-- Grup - Neo Component -->
                    <x-neo-input
                        name="group"
                        label="Grup"
                        :required="true"
                        placeholder="genel, email, sistem"
                        helpText="Ayarları gruplamak için (örn: genel, email, sistem)"
                        icon="fas fa-folder" />

                    <!-- Açıklama - Textarea geçici standart -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
                        <textarea name="description" rows="2"
                                  class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200" placeholder="Bu ayarın ne işe yaradığını açıklayın..."></textarea>
                    </div>
                </div>

                <div class="flex justify-end space-x-4 mt-8">
                    <a href="{{ route('admin.ayarlar.index') }}" class="neo-btn neo-btn neo-btn-secondary">
                        <i class="fas fa-times mr-2"></i>
                        İptal
                    </a>
                    <button type="submit" class="neo-btn neo-btn neo-btn-primary">
                        <i class="fas fa-save mr-2"></i>
                        Ayar Oluştur
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
