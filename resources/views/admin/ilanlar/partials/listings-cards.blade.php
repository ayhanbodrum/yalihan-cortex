{{-- Mobile Card View for İlanlar --}}
{{-- Context7: Responsive Design - Mobile optimized card layout --}}
{{-- Note: bulkActionsManager is inherited from parent container --}}
<div id="ilanlar-cards-container" class="grid grid-cols-1 gap-4 md:hidden">
    @foreach($ilanlar as $ilan)
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200 overflow-hidden">
        {{-- Card Header with Image --}}
        <div class="relative">
            @php
                $firstPhoto = $ilan->fotograflar?->first();
                $photoPath = $firstPhoto?->dosya_yolu;
            @endphp
            @if($photoPath && file_exists(storage_path('app/public/' . $photoPath)))
                <img class="w-full h-48 object-cover"
                     src="{{ asset('storage/' . $photoPath) }}"
                     alt="{{ $ilan->baslik ?? 'İlan görseli' }}">
            @else
                <div class="w-full h-48 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 flex items-center justify-center">
                    <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            @endif

            {{-- Status Badge Overlay --}}
            <div class="absolute top-2 right-2">
                <span class="px-2 py-1 text-xs font-semibold rounded-full
                    @if($ilan->status === 'Aktif') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300
                    @elseif($ilan->status === 'Pasif') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300
                    @elseif($ilan->status === 'Beklemede') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300
                    @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                    @endif">
                    {{ $ilan->status ?? 'Taslak' }}
                </span>
            </div>

            {{-- Checkbox Overlay --}}
            <div class="absolute top-2 left-2">
                <input type="checkbox"
                       class="row-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-5 h-5 bg-white shadow-sm"
                       value="{{ $ilan->id }}"
                       x-model="selectedIds"
                       @change="updateSelectAll()">
            </div>
        </div>

        {{-- Card Body --}}
        <div class="p-4">
            {{-- Title --}}
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 line-clamp-2">
                <a href="{{ route('admin.ilanlar.show', $ilan->id) }}"
                   class="hover:text-blue-600 dark:hover:text-blue-400">
                    {{ $ilan->baslik ?? 'İlan #' . $ilan->id }}
                </a>
            </h3>

            {{-- Price --}}
            <div class="mb-3">
                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                    {{ number_format($ilan->fiyat ?? 0, 0, ',', '.') }} {{ $ilan->para_birimi ?? 'TRY' }}
                </div>
                @if ($ilan->kiralama_turu)
                    <span class="text-xs text-gray-600 dark:text-gray-400">
                        @switch($ilan->kiralama_turu)
                            @case('gunluk') /Gün @break
                            @case('haftalik') /Hafta @break
                            @case('aylik') /Ay @break
                            @case('uzun_donem') /Ay @break
                            @case('sezonluk') /Sezon @break
                        @endswitch
                    </span>
                @endif
            </div>

            {{-- Info Grid --}}
            <div class="grid grid-cols-2 gap-3 mb-3 text-sm">
                {{-- Location --}}
                <div class="flex items-center text-gray-600 dark:text-gray-400">
                    <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="truncate">
                        @if($ilan->il && $ilan->ilce)
                            {{ $ilan->il->il_adi }}, {{ $ilan->ilce->ilce_adi }}
                        @else
                            Belirtilmemiş
                        @endif
                    </span>
                </div>

                {{-- Category --}}
                <div class="flex items-center text-gray-600 dark:text-gray-400">
                    <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    <span class="truncate">
                        {{ $ilan->anaKategori?->name ?? 'Belirtilmemiş' }}
                    </span>
                </div>

                {{-- Owner --}}
                <div class="flex items-center text-gray-600 dark:text-gray-400">
                    <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span class="truncate">
                        @if($ilan->ilanSahibi)
                            {{ $ilan->ilanSahibi->ad }} {{ $ilan->ilanSahibi->soyad }}
                        @else
                            -
                        @endif
                    </span>
                </div>

                {{-- Updated Date --}}
                <div class="flex items-center text-gray-600 dark:text-gray-400">
                    <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="truncate">
                        {{ $ilan->updated_at?->format('d.m.Y') ?? '-' }}
                    </span>
                </div>
            </div>

            {{-- Description/Note --}}
            @php
                $ilanNotu = $ilan->anahtar_notlari ?? $ilan->aciklama ?? null;
            @endphp
            @if($ilanNotu)
                <div class="mb-3 text-xs text-gray-600 dark:text-gray-400 line-clamp-2">
                    {{ Str::limit($ilanNotu, 100) }}
                </div>
            @endif

            {{-- Action Buttons --}}
            <div class="flex items-center gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('admin.ilanlar.show', $ilan->id) }}"
                   class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Görüntüle
                </a>
                <a href="{{ route('admin.ilanlar.edit', $ilan->id) }}"
                   class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-yellow-600 text-white text-sm font-medium rounded-lg hover:bg-yellow-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Düzenle
                </a>
            </div>
        </div>
    </div>
    @endforeach
</div>

