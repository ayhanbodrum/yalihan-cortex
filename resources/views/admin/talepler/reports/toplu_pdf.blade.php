<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Toplu Talep Analiz Raporu</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
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
            font-size: 22px;
        }
        .header .info {
            color: #666;
            margin-top: 10px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .table th {
            background-color: #2563eb;
            color: white;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
        }
        .table td {
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 10px;
        }
        .table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #666;
            font-size: 9px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Toplu Talep Analiz Raporu</h1>
        <div class="info">
            <p><strong>Toplam Talep:</strong> {{ $talepler->count() }} adet</p>
            <p><strong>Oluşturulma Tarihi:</strong> {{ $tarih }}</p>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Talep Sahibi</th>
                <th>İl</th>
                <th>İlçe</th>
                <th>Min Fiyat</th>
                <th>Max Fiyat</th>
                <th>Metrekare</th>
                <th>Oda</th>
                <th>Durum</th>
            </tr>
        </thead>
        <tbody>
            @foreach($talepler as $talep)
            <tr>
                <td>{{ $talep->id }}</td>
                <td>{{ $talep->kisi?->tam_ad ?? 'Bilinmiyor' }}</td>
                <td>{{ $talep->il?->il_adi ?? '' }}</td>
                <td>{{ $talep->ilce?->ilce_adi ?? '' }}</td>
                <td>{{ $talep->min_fiyat ? number_format($talep->min_fiyat, 0) : '-' }}</td>
                <td>{{ $talep->max_fiyat ? number_format($talep->max_fiyat, 0) : '-' }}</td>
                <td>
                    @if($talep->min_metrekare && $talep->max_metrekare)
                        {{ $talep->min_metrekare }}-{{ $talep->max_metrekare }}
                    @else
                        -
                    @endif
                </td>
                <td>{{ $talep->oda_sayisi ?? '-' }}</td>
                <td>{{ $talep->status ?? 'Aktif' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Bu rapor Yalıhan Emlak sistemi tarafından otomatik olarak oluşturulmuştur.</p>
        <p>{{ $tarih }}</p>
    </div>
</body>
</html>

