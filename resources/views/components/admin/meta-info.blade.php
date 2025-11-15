@props([
    'title' => null,
    'meta' => [],
    'showPerPage' => false,
    'perPageOptions' => [20, 50, 100],
    'listId' => null,
    'listEndpoint' => null,
])
@php
    $total = $meta['total'] ?? null;
    $currentPage = $meta['current_page'] ?? ($meta['currentPage'] ?? null);
    $lastPage = $meta['last_page'] ?? ($meta['lastPage'] ?? null);
    $perPage = $meta['per_page'] ?? ($meta['perPage'] ?? null);
@endphp
<div data-meta="true" class="px-6 py-2" @isset($listId) data-list-id="{{ $listId }}" @endisset @isset($listEndpoint) data-list-endpoint="{{ $listEndpoint }}" @endisset>
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            @if($title)
                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $title }}</span>
            @endif
            <span id="meta-total" class="text-sm text-gray-700 dark:text-gray-300">Toplam: {{ $total ?? '-' }}</span>
            <span id="meta-page" class="text-sm text-gray-700 dark:text-gray-300">📄 Sayfa: {{ $currentPage ?? 1 }} / {{ $lastPage ?? 1 }}</span>
        </div>
        <div class="flex items-center gap-2">
            @if($showPerPage)
                <label for="per_page_select" class="sr-only">Sayfa başına</label>
                <select id="per_page_select" data-per-page class="border rounded px-2 py-1 text-sm">
                    @foreach($perPageOptions as $opt)
                        <option value="{{ $opt }}" @if($perPage == $opt) selected @endif>{{ $opt }}</option>
                    @endforeach
                </select>
            @endif
            @isset($actions)
                {{ $actions }}
            @else
                {{ $slot }}
            @endisset
        </div>
    </div>
    <div id="meta-status" role="status" aria-busy="false" aria-live="polite" class="text-sm text-gray-600 dark:text-gray-300"></div>
</div>