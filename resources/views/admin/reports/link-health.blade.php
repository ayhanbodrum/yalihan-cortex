@extends('admin.layouts.neo')

@section('title', 'Link Sağlık Raporu')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Link Sağlık Raporu</h1>
    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Admin panelindeki kritik linklerin durumu</p>
    <div class="mt-3 text-xs text-gray-500">Kontrol zamanı: {{ $checked_at }}</div>
    <div class="mt-4">
        <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-800 rounded-lg hover:bg-gray-200 transition-all">
            <span>← Raporlar</span>
        </a>
    </div>
    <div class="mt-4">
        <form method="GET" action="{{ route('admin.reports.link-health') }}">
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all">
                Yeniden Tara
            </button>
        </form>
    </div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Kritik Linkler</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="admin-table-th">Ad</th>
                        <th class="admin-table-th">Route</th>
                        <th class="admin-table-th">URL</th>
                        <th class="admin-table-th">Durum</th>
                        <th class="admin-table-th">Kod</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($results as $r)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $r['name'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $r['route'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-700">
                                @if($r['url'])
                                    <a href="{{ $r['url'] }}" target="_blank" rel="noopener noreferrer" class="hover:underline">{{ $r['url'] }}</a>
                                @else
                                    <span class="text-gray-400">Tanımsız</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($r['ok'])
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">OK</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Hata</span>
                                    @if($r['error'])
                                        <div class="mt-1 text-xs text-red-600">{{ $r['error'] }}</div>
                                    @endif
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $r['status'] ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection