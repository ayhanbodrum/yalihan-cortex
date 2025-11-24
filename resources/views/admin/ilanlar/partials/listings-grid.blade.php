<tbody id="ilanlar-tbody">
    @foreach($ilanlar as $ilan)
    <tr>
        {{-- Checkbox Column --}}
        <td class="px-6 py-4">
            <input type="checkbox"
                   class="row-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                   value="{{ $ilan->id }}"
                   x-model="selectedIds"
                   @change="updateSelectAll()">
        </td>
        <td class="px-6 py-4">
            <div class="flex items-start">
                <div class="flex-shrink-0 w-64 h-40 rounded-lg overflow-hidden">
                    @php
                        $firstPhoto = $ilan->fotograflar?->first();
                        $photoPath = $firstPhoto?->dosya_yolu;
                    @endphp
                    @if($photoPath && file_exists(storage_path('app/public/' . $photoPath)))
                        <img class="w-full h-full object-cover"
                             src="{{ asset('storage/' . $photoPath) }}"
                             alt="İlan görseli">
                    @else
                        <div class="w-full h-full rounded-lg bg-gray-200 dark:bg-gray-800 flex items-center justify-center">
                            <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif
                </div>
                <div class="ml-4 flex-1">
                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                        <a href="{{ route('admin.ilanlar.show', $ilan->id) }}"
                           class="hover:text-blue-600 dark:hover:text-blue-400">
                            {{ $ilan->baslik ?? 'İlan #' . $ilan->id }}
                        </a>
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        ID: #{{ $ilan->id }}
                        @if($ilan->il && $ilan->ilce)
                            • {{ $ilan->il->il_adi }}, {{ $ilan->ilce->ilce_adi }}
                        @endif
                    </div>
                    <div class="mt-2 text-xs text-gray-600 dark:text-gray-400">
                        <span class="font-semibold">Sahibi:</span>
                        @if($ilan->ilanSahibi)
                            {{ $ilan->ilanSahibi->ad }} {{ $ilan->ilanSahibi->soyad }}
                        @else
                            -
                        @endif
                    </div>
                    @php
                        $ilanNotu = $ilan->anahtar_notlari ?? $ilan->aciklama ?? null;
                    @endphp
                    @if($ilanNotu)
                        <div class="mt-2 text-xs italic text-gray-700 dark:text-gray-300">
                            <span class="font-semibold not-italic">Not:</span>
                            {{ Str::limit($ilanNotu, 160) }}
                        </div>
                    @endif
                </div>
            </div>
        </td>
        <td class="px-6 py-4">
            <div class="text-sm text-gray-900 dark:text-white">
                {{ $ilan->yayinTipi?->name ?? 'Belirtilmemiş' }}
            </div>
            <div class="text-sm text-gray-500 dark:text-gray-400">
                {{ $ilan->anaKategori?->name ?? 'Belirtilmemiş' }}
                @if($ilan->altKategori)
                    → {{ $ilan->altKategori->name }}
                @endif
            </div>
        </td>
        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
            {{ number_format($ilan->fiyat ?? 0, 0, ',', '.') }} {{ $ilan->para_birimi ?? 'TRY' }}
            @if ($ilan->kiralama_turu)
                @switch($ilan->kiralama_turu)
                    @case('gunluk')
                        <span class="text-xs text-gray-600 dark:text-gray-400">/Gün</span>
                    @break
                    @case('haftalik')
                        <span class="text-xs text-gray-600 dark:text-gray-400">/Hafta</span>
                    @break
                    @case('aylik')
                    @case('uzun_donem')
                        <span class="text-xs text-gray-600 dark:text-gray-400">/Ay</span>
                    @break
                    @case('sezonluk')
                        <span class="text-xs text-gray-600 dark:text-gray-400">/Sezon</span>
                    @break
                @endswitch
            @endif
        </td>
        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
            @if($ilan->userDanisman)
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-8 w-8 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center">
                        <span class="text-xs font-semibold text-purple-600 dark:text-purple-400">
                            {{ substr($ilan->userDanisman->name, 0, 2) }}
                        </span>
                    </div>
                    <div class="ml-2">
                        <div class="text-sm font-medium">{{ $ilan->userDanisman->name }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $ilan->userDanisman->email }}</div>
                    </div>
                </div>
            @else
                <span class="text-gray-400">-</span>
            @endif
        </td>
        <td class="px-6 py-4">
            {{-- Inline Status Toggle --}}
            <div x-data="statusToggle({{ $ilan->id }}, '{{ $ilan->status ?? 'Taslak' }}')"
                 @click.outside="open = false"
                 class="relative inline-block">

                {{-- Clickable Badge --}}
                <button @click="open = !open"
                        type="button"
                        :disabled="updating"
                        class="px-3 py-1 text-xs font-semibold rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all duration-200 cursor-pointer disabled:opacity-50"
                        :class="getStatusClasses()">
                    <span x-text="currentStatus"></span>
                    <svg class="w-3 h-3 ml-1 inline transition-transform duration-200"
                         :class="{'rotate-180': open}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                {{-- Dropdown Menu --}}
                <div x-show="open"
                     x-transition
                     class="absolute z-50 mt-2 w-48 rounded-lg shadow-lg bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 py-1">

                    <button @click="changeStatus('Aktif')"
                            type="button"
                            class="w-full text-left px-4 py-2 text-sm hover:bg-green-50 dark:hover:bg-green-900/20 flex items-center transition-colors"
                            :class="{ 'bg-green-50 dark:bg-green-900/20': currentStatus === 'Aktif' }">
                        <span class="w-2 h-2 rounded-full bg-green-500 mr-3"></span>
                        <span class="text-green-700 dark:text-green-300 font-medium">Aktif</span>
                    </button>

                    <button @click="changeStatus('Beklemede')"
                            type="button"
                            class="w-full text-left px-4 py-2 text-sm hover:bg-yellow-50 dark:hover:bg-yellow-900/20 flex items-center transition-colors"
                            :class="{ 'bg-yellow-50 dark:bg-yellow-900/20': currentStatus === 'Beklemede' }">
                        <span class="w-2 h-2 rounded-full bg-yellow-500 mr-3"></span>
                        <span class="text-yellow-700 dark:text-yellow-300 font-medium">Beklemede</span>
                    </button>

                    <button @click="changeStatus('Taslak')"
                            type="button"
                            class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center transition-colors"
                            :class="{ 'bg-gray-50 dark:bg-gray-800': currentStatus === 'Taslak' }">
                        <span class="w-2 h-2 rounded-full bg-gray-500 mr-3"></span>
                        <span class="text-gray-900 dark:text-white font-medium">Taslak</span>
                    </button>

                    <button @click="changeStatus('Pasif')"
                            type="button"
                            class="w-full text-left px-4 py-2 text-sm hover:bg-red-50 dark:hover:bg-red-900/20 flex items-center transition-colors"
                            :class="{ 'bg-red-50 dark:bg-red-900/20': currentStatus === 'Pasif' }">
                        <span class="w-2 h-2 rounded-full bg-red-500 mr-3"></span>
                        <span class="text-red-700 dark:text-red-300 font-medium">Pasif</span>
                    </button>
                </div>
            </div>
        </td>
        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
            {{ $ilan->updated_at?->format('d.m.Y H:i') ?? '-' }}
        </td>
        <td class="px-6 py-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.ilanlar.show', $ilan->id) }}"
                   class="text-blue-600 hover:text-blue-900 dark:text-blue-400"
                   title="Görüntüle">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </a>
                <a href="{{ route('admin.ilanlar.edit', $ilan->id) }}"
                   class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400"
                   title="Düzenle">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </a>
            </div>
        </td>
    </tr>
    @endforeach
</tbody>

