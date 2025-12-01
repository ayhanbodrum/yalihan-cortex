@php($items = $ilanlar ?? collect())
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
    @forelse($items as $ilan)
        <div class="p-4 bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-start justify-between">
                <div>
                    <a href="{{ route('admin.ilanlar.show', $ilan->id) }}" class="font-semibold text-gray-900 dark:text-white hover:underline">
                        {{ $ilan->baslik }}
                    </a>
                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        {{ number_format($ilan->fiyat) }} {{ $ilan->para_birimi }} · {{ $ilan->status }}
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.ilanlar.edit', $ilan->id) }}" class="text-blue-600 dark:text-blue-400 text-sm hover:underline">Düzenle</a>
                    <a href="{{ route('admin.ilanlar.show', $ilan->id) }}" class="text-gray-700 dark:text-gray-300 text-sm hover:underline">Görüntüle</a>
                </div>
            </div>
            <div class="mt-3 w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                <div class="h-full bg-orange-500" style="width: {{ min(100, max(0, (int) ($ilan->goruntulenme ?? 0))) }}%"></div>
            </div>
        </div>
    @empty
        <div class="text-sm text-gray-600 dark:text-gray-400">Kayıt bulunamadı</div>
    @endforelse
</div>
