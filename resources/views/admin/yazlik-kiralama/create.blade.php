@extends('admin.layouts.neo')

@section('title', 'Yeni Yazlık İlanı')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    🏖️ Yeni Yazlık İlanı Oluştur
                </h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Yazlık kiralama ilanı oluştur ve rezervasyon yönetimi kur
                </p>
            </div>
            
            <a href="{{ route('admin.yazlik-kiralama.index') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-all">
                ← İptal / Geri Dön
            </a>
        </div>

        <form action="{{ route('admin.yazlik-kiralama.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Temel Bilgiler --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
                    📝 Temel Bilgiler
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Başlık --}}
                    <div class="md:col-span-2">
                        <label for="baslik" class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                            İlan Başlığı *
                        </label>
                        <input type="text" 
                               name="baslik" 
                               id="baslik"
                               required
                               placeholder="Örn: Yalıkavak'ta Deniz Manzaralı Lüks Villa"
                               class="w-full px-4 py-2 bg-white dark:bg-gray-900 border-2 border-gray-300 dark:border-gray-600 rounded-lg text-black dark:text-white font-semibold placeholder-gray-600 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                        @error('baslik')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Açıklama --}}
                    <div class="md:col-span-2">
                        <label for="aciklama" class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                            Açıklama *
                        </label>
                        <textarea name="aciklama" 
                                  id="aciklama"
                                  rows="4"
                                  required
                                  placeholder="Yazlık hakkında detaylı açıklama..."
                                  class="w-full px-4 py-2 bg-white dark:bg-gray-900 border-2 border-gray-300 dark:border-gray-600 rounded-lg text-black dark:text-white font-semibold placeholder-gray-600 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"></textarea>
                        @error('aciklama')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Fiyat --}}
                    <div>
                        <label for="fiyat" class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                            Günlük Fiyat *
                        </label>
                        <input type="number" 
                               name="fiyat" 
                               id="fiyat"
                               required
                               min="0"
                               step="0.01"
                               placeholder="0.00"
                               class="w-full px-4 py-2 bg-white dark:bg-gray-900 border-2 border-gray-300 dark:border-gray-600 rounded-lg text-black dark:text-white font-semibold placeholder-gray-600 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                    </div>

                    {{-- Para Birimi --}}
                    <div>
                        <label for="doviz" class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                            Para Birimi *
                        </label>
                        <select name="doviz" 
                                id="doviz"
                                required
                                class="w-full px-4 py-2 bg-white dark:bg-gray-900 border-2 border-gray-300 dark:border-gray-600 rounded-lg text-black dark:text-white font-semibold focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                            <option value="TRY">TRY (₺)</option>
                            <option value="USD">USD ($)</option>
                            <option value="EUR">EUR (€)</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Component Integration: Photo Upload --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
                    📸 Fotoğraflar
                </h2>
                @include('admin.ilanlar.components.photo-upload-manager', ['ilan' => new \App\Models\Ilan()])
            </div>

            {{-- Component Integration: Bedroom Layout --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
                    🛏️ Yatak Odası Düzeni
                </h2>
                @include('admin.ilanlar.components.bedroom-layout-manager', ['ilan' => new \App\Models\Ilan()])
            </div>

            {{-- Component Integration: Event/Booking Manager --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
                    📅 Rezervasyon Yönetimi
                </h2>
                @include('admin.ilanlar.components.event-booking-manager', ['ilan' => new \App\Models\Ilan()])
            </div>

            {{-- Component Integration: Season Pricing Manager --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
                    💰 Sezonluk Fiyatlandırma
                </h2>
                @include('admin.ilanlar.components.season-pricing-manager', ['ilan' => new \App\Models\Ilan()])
            </div>

            {{-- Form Actions --}}
            <div class="flex items-center justify-end gap-4">
                <a href="{{ route('admin.yazlik-kiralama.index') }}" 
                   class="px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-all">
                    İptal
                </a>
                <button type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-lg hover:scale-105 active:scale-95 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all shadow-lg">
                    💾 Kaydet
                </button>
            </div>
        </form>

    </div>
</div>
@endsection

