<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yeni Rezervasyon Talebi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #2563eb;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f9fafb;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        .section {
            margin-bottom: 25px;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #2563eb;
        }
        .section-title {
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 15px;
            font-size: 18px;
        }
        .info-row {
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: bold;
            color: #666;
            display: inline-block;
            width: 150px;
        }
        .info-value {
            color: #333;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 20px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🏖️ Yeni Rezervasyon Talebi</h1>
    </div>

    <div class="content">
        <div class="section">
            <div class="section-title">📋 Rezervasyon Bilgileri</div>

            <div class="info-row">
                <span class="info-label">Rezervasyon No:</span>
                <span class="info-value">{{ $booking['booking_reference'] ?? 'BK-' . now()->format('Ymd') }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">Villa:</span>
                <span class="info-value">{{ $booking['villa_title'] ?? $villa->baslik ?? 'Bilinmiyor' }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">Lokasyon:</span>
                <span class="info-value">{{ $booking['villa_location'] ?? 'Bilinmiyor' }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">Giriş Tarihi:</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($booking['check_in'])->format('d.m.Y') }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">Çıkış Tarihi:</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($booking['check_out'])->format('d.m.Y') }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">Gece Sayısı:</span>
                <span class="info-value">{{ $booking['nights'] ?? 1 }} gece</span>
            </div>

            <div class="info-row">
                <span class="info-label">Misafir Sayısı:</span>
                <span class="info-value">{{ $booking['guests'] ?? 1 }} kişi</span>
            </div>

            <div class="info-row">
                <span class="info-label">Toplam Fiyat:</span>
                <span class="info-value">{{ number_format($booking['total_price'] ?? 0, 2) }} TL</span>
            </div>
        </div>

        <div class="section">
            <div class="section-title">👤 Misafir Bilgileri</div>

            <div class="info-row">
                <span class="info-label">Ad Soyad:</span>
                <span class="info-value">{{ $booking['guest_name'] ?? 'Bilinmiyor' }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">Telefon:</span>
                <span class="info-value">{{ $booking['guest_phone'] ?? 'Belirtilmemiş' }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">Email:</span>
                <span class="info-value">{{ $booking['guest_email'] ?? 'Belirtilmemiş' }}</span>
            </div>

            @if(!empty($booking['guest_message']))
            <div class="info-row">
                <span class="info-label">Mesaj:</span>
                <div class="info-value" style="margin-top: 10px; padding: 10px; background-color: #f3f4f6; border-radius: 4px;">
                    {{ $booking['guest_message'] }}
                </div>
            </div>
            @endif
        </div>

        <div style="text-align: center; margin-top: 30px;">
            <a href="{{ config('app.url') }}/admin/booking-requests" class="button">
                Rezervasyonu Görüntüle
            </a>
        </div>
    </div>

    <div class="footer">
        <p>Bu email Yalıhan Emlak sistemi tarafından otomatik olarak gönderilmiştir.</p>
        <p>{{ config('app.name') }} - {{ now()->format('d.m.Y H:i') }}</p>
    </div>
</body>
</html>
