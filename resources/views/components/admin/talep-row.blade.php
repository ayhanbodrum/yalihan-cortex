@props(['talep'])
<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors duration-150">
    <td class="px-6 py-4 whitespace-nowrap">
        <input type="checkbox"
            class="talep-checkbox w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 dark:border-gray-600"
            value="{{ $talep->id }}">
    </td>
    <td class="px-6 py-4 whitespace-nowrap">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center text-white font-semibold">
                <span>{{ strtoupper(substr($talep->kisi->ad ?? '?', 0, 1)) }}</span>
            </div>
            <div>
                <div class="font-medium text-gray-900 dark:text-white">
                    {{ $talep->kisi->ad ?? '—' }} {{ $talep->kisi->soyad ?? '' }}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                    {{ $talep->kisi->telefon ?? '—' }}
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-500 mt-1">ID: #{{ $talep->id }}</div>
            </div>
        </div>
    </td>
    <td class="px-6 py-4">
        <div class="max-w-xs">
            <div class="text-sm font-medium text-gray-900 dark:text-white mb-1">
                {{ Str::limit($talep->aciklama ?? 'Açıklama yok', 60) }}
            </div>
            <div class="text-xs text-gray-500 dark:text-gray-500">
                {{ $talep->created_at ? $talep->created_at->format('d.m.Y H:i') : '—' }}
            </div>
        </div>
    </td>
    <td class="px-6 py-4 whitespace-nowrap">
        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
            </svg>
            {{ ucfirst($talep->talep_tipi ?? 'Genel') }}
        </span>
    </td>
    <td class="px-6 py-4 whitespace-nowrap">
        @php
            $currentStatus = $talep->status ?? 'normal';
            $statusStyle = match ($currentStatus) {
                'acil' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                'beklemede' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                default => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
            };
            $dotStyle = match ($currentStatus) {
                'acil' => 'bg-red-500',
                'beklemede' => 'bg-yellow-500',
                default => 'bg-green-500',
            };
        @endphp
        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $statusStyle }}">
            <div class="w-2 h-2 rounded-full mr-2 {{ $dotStyle }}"></div>
            {{ ucfirst($talep->status ?? 'Aktif') }}
        </span>
    </td>
    <td class="px-6 py-4 whitespace-nowrap">
        <div class="flex items-center gap-2">
            <button class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 transition-colors duration-150" onclick="talepAnaliz({{ $talep->id }})">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                AI Analiz
            </button>
            <button class="inline-flex items-center px-4 py-2.5 text-xs font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-600 focus:ring-2 focus:ring-blue-500 transition-all duration-150" onclick="portfoyOner({{ $talep->id }})">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                </svg>
                Öneriler
            </button>
            <a class="inline-flex items-center px-4 py-2.5 text-xs font-medium text-blue-700 bg-blue-50 rounded-lg hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-300 dark:hover:bg-blue-900/50 focus:ring-2 focus:ring-blue-500 transition-all duration-150" href="{{ route('admin.talep-portfolyo.show', $talep->id) }}">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                Detay
            </a>
        </div>
    </td>
</tr>