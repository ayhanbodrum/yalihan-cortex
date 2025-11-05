<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Talep Analiz Raporu - {{ $talep->id }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #2563eb;
            margin: 0;
            font-size: 24px;
        }
        .header .date {
            color: #666;
            margin-top: 5px;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            background-color: #f3f4f6;
            padding: 10px;
            font-weight: bold;
            font-size: 14px;
            border-left: 4px solid #2563eb;
            margin-bottom: 15px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-table td:first-child {
            font-weight: bold;
            width: 200px;
            color: #666;
        }
        .ilan-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .ilan-table th {
            background-color: #2563eb;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        .ilan-table td {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
        }
        .ilan-table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .score {
            font-weight: bold;
            color: #059669;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #666;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Talep Analiz Raporu</h1>
        <div class="date">Oluşturulma Tarihi: {{ $tarih }}</div>
    </div>

    <!-- Talep Bilgileri -->
    <div class="section">
        <div class="section-title">Talep Bilgileri</div>
        <table class="info-table">
            <tr>
                <td>Talep ID</td>
                <td>{{ $talep->id }}</td>
            </tr>
            <tr>
                <td>Talep Sahibi</td>
                <td>{{ $talep->kisi?->tam_ad ?? 'Bilinmiyor' }}</td>
            </tr>
            <tr>
                <td>İletişim</td>
                <td>
                    @if($talep->kisi?->telefon)
                        Tel: {{ $talep->kisi->telefon }}
                    @endif
                    @if($talep->kisi?->email)
                        <br>Email: {{ $talep->kisi->email }}
                    @endif
                </td>
            </tr>
            <tr>
                <td>Lokasyon</td>
                <td>
                    {{ $talep->il?->il_adi ?? '' }} / 
                    {{ $talep->ilce?->ilce_adi ?? '' }} / 
                    {{ $talep->mahalle?->mahalle_adi ?? '' }}
                </td>
            </tr>
            <tr>
                <td>Fiyat Aralığı</td>
                <td>
                    @if($talep->min_fiyat && $talep->max_fiyat)
                        {{ number_format($talep->min_fiyat, 2) }} - {{ number_format($talep->max_fiyat, 2) }} TL
                    @elseif($talep->min_fiyat)
                        Min: {{ number_format($talep->min_fiyat, 2) }} TL
                    @elseif($talep->max_fiyat)
                        Max: {{ number_format($talep->max_fiyat, 2) }} TL
                    @else
                        Belirtilmemiş
                    @endif
                </td>
            </tr>
            <tr>
                <td>Metrekare</td>
                <td>
                    @if($talep->min_metrekare && $talep->max_metrekare)
                        {{ $talep->min_metrekare }} - {{ $talep->max_metrekare }} m²
                    @elseif($talep->min_metrekare)
                        Min: {{ $talep->min_metrekare }} m²
                    @elseif($talep->max_metrekare)
                        Max: {{ $talep->max_metrekare }} m²
                    @else
                        Belirtilmemiş
                    @endif
                </td>
            </tr>
            <tr>
                <td>Oda Sayısı</td>
                <td>{{ $talep->oda_sayisi ?? 'Belirtilmemiş' }}</td>
            </tr>
            <tr>
                <td>Durum</td>
                <td>{{ $talep->status ?? 'Aktif' }}</td>
            </tr>
        </table>
    </div>

    <!-- Eşleşen Emlaklar -->
    @if(isset($analiz_sonucu['eslesmeler']) && count($analiz_sonucu['eslesmeler']) > 0)
    <div class="section">
        <div class="section-title">Eşleşen Emlaklar ({{ count($analiz_sonucu['eslesmeler']) }} adet)</div>
        <table class="ilan-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Başlık</th>
                    <th>Fiyat</th>
                    <th>Lokasyon</th>
                    <th>Metrekare</th>
                    <th>Eşleşme %</th>
                </tr>
            </thead>
            <tbody>
                @foreach($analiz_sonucu['eslesmeler'] as $eslesme)
                @php
                    $ilan = $eslesme['emlak'] ?? null;
                    $score = $eslesme['eslesme_yuzdesi'] ?? 0;
                @endphp
                @if($ilan)
                <tr>
                    <td>{{ $ilan->id ?? '' }}</td>
                    <td>{{ $ilan->baslik ?? 'Başlık Yok' }}</td>
                    <td>
                        @if($ilan->fiyat)
                            {{ number_format($ilan->fiyat, 2) }} {{ $ilan->para_birimi ?? 'TL' }}
                        @else
                            Belirtilmemiş
                        @endif
                    </td>
                    <td>
                        {{ $ilan->il?->il_adi ?? '' }} / 
                        {{ $ilan->ilce?->ilce_adi ?? '' }}
                    </td>
                    <td>{{ $ilan->metrekare ?? '-' }} m²</td>
                    <td class="score">{{ number_format($score, 1) }}%</td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="section">
        <div class="section-title">Eşleşen Emlaklar</div>
        <p>Eşleşen emlak bulunamadı.</p>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Bu rapor Yalıhan Emlak sistemi tarafından otomatik olarak oluşturulmuştur.</p>
        <p>Rapor ID: {{ $talep->id }} - {{ $tarih }}</p>
    </div>
</body>
</html>

