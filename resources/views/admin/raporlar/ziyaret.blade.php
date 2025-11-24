@extends('admin.layouts.admin')

@section('content')
<div class="max-w-screen-xl mx-auto p-6">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-semibold">Ziyaret Raporu (Son {{ $days }} Gün)</h1>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.reports.visits', ['days' => 7]) }}" class="px-3 py-1.5 border rounded {{ $days==7?'bg-gray-100':'' }}">7 Gün</a>
            <a href="{{ route('admin.reports.visits', ['days' => 15]) }}" class="px-3 py-1.5 border rounded {{ $days==15?'bg-gray-100':'' }}">15 Gün</a>
            <a href="{{ route('admin.reports.visits', ['days' => 30]) }}" class="px-3 py-1.5 border rounded {{ $days==30?'bg-gray-100':'' }}">30 Gün</a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="p-4 rounded-lg border bg-white dark:bg-gray-900">
            <div class="text-sm text-gray-500">Toplam Görüntülenme</div>
            <div class="text-3xl font-bold">{{ number_format($totalViews) }}</div>
        </div>
        <div class="p-4 rounded-lg border bg-white dark:bg-gray-900">
            <div class="text-sm text-gray-500">Yayındaki İlan Sayısı</div>
            <div class="text-3xl font-bold">{{ number_format($publicListings) }}</div>
        </div>
        <div class="p-4 rounded-lg border bg-white dark:bg-gray-900">
            <div class="text-sm text-gray-500">Cihaz Dağılımı</div>
            @php $desktop = $device->firstWhere('cihaz','desktop')->total ?? 0; $mobile = $device->firstWhere('cihaz','mobile')->total ?? 0; $sum = max(1, $desktop+$mobile); @endphp
            <div class="mt-2 flex items-center gap-2">
                <div class="w-full h-3 bg-gray-200 rounded">
                    <div class="h-3 bg-blue-500 rounded" style="width: {{ round(($desktop/$sum)*100) }}%"></div>
                </div>
                <span class="text-xs">Desktop {{ round(($desktop/$sum)*100) }}%</span>
            </div>
            <div class="mt-2 flex items-center gap-2">
                <div class="w-full h-3 bg-gray-200 rounded">
                    <div class="h-3 bg-green-500 rounded" style="width: {{ round(($mobile/$sum)*100) }}%"></div>
                </div>
                <span class="text-xs">Mobile {{ round(($mobile/$sum)*100) }}%</span>
            </div>
        </div>
    </div>

    <div class="p-4 rounded-lg border bg-white dark:bg-gray-900 mb-6">
        <div class="text-sm text-gray-500 mb-3">Günlük Ziyaret Sayısı</div>
        @php $max = max(1, $daily->max('total')); @endphp
        <div class="grid grid-cols-{{ max(7, $daily->count()) }} gap-2">
            @foreach($daily as $d)
                <div class="flex flex-col items-center">
                    <div class="w-6 h-32 bg-gray-200 rounded relative">
                        <div class="absolute bottom-0 left-0 right-0 bg-emerald-500 rounded" style="height: {{ round(($d->total/$max)*100) }}%"></div>
                    </div>
                    <div class="mt-1 text-xs text-gray-500">{{ \Carbon\Carbon::parse($d->tarih)->format('d M') }}</div>
                    <div class="text-xs font-semibold">{{ $d->total }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="p-4 rounded-lg border bg-white dark:bg-gray-900">
        <div class="flex items-center justify-between mb-3">
            <div class="text-sm text-gray-500">En Çok Görüntülenen İlanlar</div>
            <div class="text-xs text-gray-400">Dönem: Son {{ $days }} Gün</div>
        </div>
        <div class="divide-y">
            @foreach($topListings as $item)
                <div class="py-2 flex items-center justify-between">
                    <div>
                        <div class="font-medium">{{ $item->ilan?->baslik ?? 'İlan' }}</div>
                        <div class="text-xs text-gray-500">ID: {{ $item->ilan_id }} · {{ $item->ilan?->para_birimi }} {{ number_format($item->ilan?->fiyat ?? 0) }}</div>
                    </div>
                    <div class="text-sm font-semibold">{{ $item->views }} görüntüleme</div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection