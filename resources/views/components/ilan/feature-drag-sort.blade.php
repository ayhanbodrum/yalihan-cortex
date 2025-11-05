@props(['features' => [], 'selected' => [], 'onOrderChange' => null])
<!-- Gelişmiş Sürükle-Bırak Özellik Sıralama ve Gruplama (Context7, MCP, AI uyumlu) -->
@php
    // Grupları belirle
    $groups = collect($selected)->groupBy(fn($f) => $f['group'] ?? 'Genel');
@endphp
<div class="space-y-8">
    @foreach ($groups as $group => $items)
        <div x-data="{
            items: @js(array_values($items->toArray())),
            dragging: null,
            dragStart(idx) { this.dragging = idx },
            dragEnd() { this.dragging = null },
            move(from, to) {
                if (from === to) return;
                const moved = this.items.splice(from, 1)[0];
                this.items.splice(to, 0, moved);
                if (@js($onOrderChange)) $onOrderChange(this.items);
            }
        }" class="bg-gray-50 rounded-xl p-4 shadow-sm">
            <div class="mb-2 flex items-center gap-2">
                <span class="text-base font-semibold text-blue-700">{{ $group }}</span>
                <span class="text-xs text-gray-400">({{ count($items) }} özellik)</span>
            </div>
            <div class="space-y-2">
                <template x-for="(feature, idx) in items" :key="feature.id">
                    <div class="flex items-center gap-3 p-3 rounded-lg shadow bg-white border border-gray-100 cursor-move transition hover:shadow-lg"
                        :class="dragging === idx ? 'ring-2 ring-blue-400' : ''" draggable="true"
                        @dragstart="dragStart(idx)" @dragend="dragEnd()" @dragover.prevent @drop="move(dragging, idx)">
                        <span class="w-6 h-6 flex items-center justify-center text-gray-400"><svg class="w-5 h-5"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 8h16M4 16h16" />
                            </svg></span>
                        <span class="font-medium text-gray-800" x-text="feature.name"></span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium-primary text-xs" x-text="feature.group ?? 'Genel'"></span>
                    </div>
                </template>
            </div>
        </div>
    @endforeach
    <div class="mt-6">
        <button class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg" @click="$dispatch('save-features')">Kaydet</button>
    </div>
</div>
