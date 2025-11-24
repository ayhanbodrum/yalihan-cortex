<div id="fragment-root">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
        <h2 class="text-xl font-bold whitespace-nowrap">AI Portföy Önerileri</h2>
    </div>
    <div class="p-6">
        <ul class="space-y-3">
            @forelse($oneriler as $i)
                <li class="flex items-center justify-between">
                    <span class="text-sm text-gray-900 dark:text-white">{{ $i->baslik }}</span>
                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ number_format($i->fiyat ?? 0) }} {{ $i->para_birimi }}</span>
                </li>
            @empty
                <li class="text-sm text-gray-500 dark:text-gray-400">Öneri bulunamadı</li>
            @endforelse
        </ul>
    </div>
</div>