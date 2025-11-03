@extends('admin.layouts.neo')

@section('title', 'Talep Analiz Detayı')

@section('content')
    <div class="px-4">
        <div class="flex flex-wrap -mx-4">
            <div class="w-full px-4 md:w-1/3">
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h3 class="admin-h3">Müşteri Talebi #{{ $talep->id }}</h3>
                    </div>
                    <div class="admin-card-body">
                        <h6>{{ $talep->kullanici->tam_ad }} - {{ $talep->kullanici->telefon }}</h6>
                        <hr>
                        <p><strong>Talep Türü:</strong> {{ $talep->tip }}</p>
                        <p><strong>Bölge:</strong> {{ $talep->il }} / {{ $talep->ilce }}</p>
                        <p><strong>Fiyat Aralığı:</strong>
                            @if ($talep->min_fiyat && $talep->max_fiyat)
                                {{ number_format($talep->min_fiyat) }} TL - {{ number_format($talep->max_fiyat) }} TL
                            @elseif($talep->max_fiyat)
                                Max: {{ number_format($talep->max_fiyat) }} TL
                            @elseif($talep->min_fiyat)
                                Min: {{ number_format($talep->min_fiyat) }} TL
                            @else
                                Belirtilmemiş
                            @endif
                        </p>
                        <p><strong>Oda Sayısı:</strong> {{ $talep->oda_sayisi ?: 'Belirtilmemiş' }}</p>
                        <p><strong>Metraj:</strong> {{ $talep->metraj ?: 'Belirtilmemiş' }} m²</p>
                        <p><strong>Ek Notlar:</strong> {{ $talep->notlar ?: 'Bulunmuyor' }}</p>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>AI Eşleşme Analizi</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @forelse($potansiyelEslesmeler as $eslesme)
                                @php
                                    $eslesmeClass = '';
                                    if ($eslesme['eslesme_yuzdesi'] >= 80) {
                                        $eslesmeClass = 'ai-match-high';
                                    } elseif ($eslesme['eslesme_yuzdesi'] >= 50) {
                                        $eslesmeClass = 'ai-match-medium';
                                    } else {
                                        $eslesmeClass = 'ai-match-low';
                                    }
                                @endphp
                                <div class="col-md-6 mb-3">
                                    <div class="card {{ $eslesmeClass }}">
                                        <div class="card-body">
                                            <h6>{{ $eslesme['emlak']->baslik }}</h6>
                                            <div class="progress mb-2">
                                                <div class="progress-bar" role="progressbar"
                                                    style="width: {{ $eslesme['eslesme_yuzdesi'] }}%"
                                                    aria-valuenow="{{ $eslesme['eslesme_yuzdesi'] }}" aria-valuemin="0"
                                                    aria-valuemax="100">
                                                    {{ number_format($eslesme['eslesme_yuzdesi']) }}%
                                                </div>
                                            </div>
                                            <p><strong>Fiyat:</strong> {{ number_format($eslesme['emlak']->fiyat) }} TL</p>
                                            <p><strong>Konum:</strong> {{ $eslesme['emlak']->il }} /
                                                {{ $eslesme['emlak']->ilce }}</p>
                                            <p><strong>Özellikler:</strong> {{ $eslesme['emlak']->oda_sayisi }} Oda,
                                                {{ $eslesme['emlak']->metraj }} m²</p>
                                            <a href="{{ route('admin.emlaklar.show', $eslesme['emlak']->id) }}"
                                                class="btn btn-sm neo-btn neo-btn-primary touch-target-optimized touch-target-optimized">Detaylar</a>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="alert alert-warning">
                                        Bu talep için uygun eşleşme bulunamadı.
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
