<div class="space-y-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
            <i class="fas fa-briefcase mr-2"></i>
            Portföy ({{ $performans['status_ilan'] }} Aktif İlan)
        </h3>
    </div>

    @if($portfoy && $portfoy->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($portfoy as $ilan)
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden">
                    @php
                        $foto = $ilan->ilanFotograflari->first();
                        $fotoUrl = $foto ? asset('storage/' . $foto->dosya_yolu) : asset('images/placeholder-property.jpg');
                    @endphp
                    <div class="relative h-48 bg-gray-200 dark:bg-gray-700">
                        <img src="{{ $fotoUrl }}" alt="{{ $ilan->baslik }}" class="w-full h-full object-cover">
                        <div class="absolute top-2 right-2">
                            <x-neo.status-badge :value="$ilan->status" />
                        </div>
                    </div>
                    <div class="p-4">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2 line-clamp-2">
                            {{ $ilan->baslik }}
                        </h4>
                        <p class="text-lg font-bold text-blue-600 dark:text-blue-400 mb-2">
                            {{ number_format($ilan->fiyat, 0, ',', '.') }} {{ $ilan->para_birimi ?? 'TL' }}
                        </p>
                        <div class="flex items-center gap-4 text-xs text-gray-600 dark:text-gray-400 mb-3">
                            @if($ilan->oda_sayisi)
                                <span><i class="fas fa-bed mr-1"></i> {{ $ilan->oda_sayisi }} Oda</span>
                            @endif
                            @if($ilan->banyo_sayisi)
                                <span><i class="fas fa-bath mr-1"></i> {{ $ilan->banyo_sayisi }} Banyo</span>
                            @endif
                            @if($ilan->brut_m2)
                                <span><i class="fas fa-ruler-combined mr-1"></i> {{ $ilan->brut_m2 }} m²</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.ilanlar.show', $ilan->id) }}" 
                               class="flex-1 text-center px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 text-sm font-medium">
                                Detay
                            </a>
                            <a href="{{ route('ilanlar.show', $ilan->id) }}" target="_blank"
                               class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 text-sm">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $portfoy->appends(request()->query())->links() }}
        </div>
    @else
        <div class="text-center py-12 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
            <i class="fas fa-briefcase text-4xl text-gray-400 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Henüz portföy bulunmuyor</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Bu danışmana ait aktif ilan bulunmamaktadır.</p>
        </div>
    @endif
</div>

