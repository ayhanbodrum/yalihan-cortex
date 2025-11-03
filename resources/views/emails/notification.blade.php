<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $notification->title }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }

        .container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }

        .content {
            padding: 30px;
        }

        .notification-type {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 20px;
        }

        .type-success {
            background-color: #dcfce7;
            color: #166534;
        }

        .type-warning {
            background-color: #fef3c7;
            color: #92400e;
        }

        .type-error {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .type-info {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .priority {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            margin-left: 10px;
        }

        .priority-urgent {
            background-color: #fecaca;
            color: #991b1b;
        }

        .priority-high {
            background-color: #fed7aa;
            color: #9a3412;
        }

        .priority-normal {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .priority-low {
            background-color: #f3f4f6;
            color: #374151;
        }

        .message {
            background-color: #f8f9fa;
            border-left: 4px solid #f97316;
            padding: 20px;
            margin: 20px 0;
            border-radius: 0 4px 4px 0;
        }

        .footer {
            background-color: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }

        .footer p {
            margin: 0;
            font-size: 14px;
            color: #6b7280;
        }

        .button {
            display: inline-block;
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 20px 0;
        }

        .sender-info {
            background-color: #f3f4f6;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }

        .sender-info strong {
            color: #374151;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🔔 Yalıhan Emlak Bildirimi</h1>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Notification Type & Priority -->
            <div>
                <span class="notification-type type-{{ $notification->type }}">
                    @switch($notification->type)
                        @case('success')
                            ✅ Başarı
                        @break

                        @case('warning')
                            ⚠️ Uyarı
                        @break

                        @case('error')
                            ❌ Hata
                        @break

                        @default
                            ℹ️ Bilgi
                    @endswitch
                </span>
                <span class="priority priority-{{ $notification->priority }}">
                    @switch($notification->priority)
                        @case('urgent')
                            🚨 Acil
                        @break

                        @case('high')
                            ⬆️ Yüksek
                        @break

                        @case('normal')
                            📋 Normal
                        @break

                        @default
                            📝 Düşük
                    @endswitch
                </span>
            </div>

            <!-- Title -->
            <h2 style="color: #1f2937; margin: 20px 0 10px 0;">{{ $notification->title }}</h2>

            <!-- Message -->
            <div class="message">
                <p style="margin: 0; font-size: 16px;">{{ $notification->message }}</p>
            </div>

            <!-- Sender Info -->
            @if ($sender)
                <div class="sender-info">
                    <strong>Gönderen:</strong> {{ $sender->name }}<br>
                    <strong>E-posta:</strong> {{ $sender->email }}<br>
                    <strong>Tarih:</strong> {{ $notification->created_at->format('d.m.Y H:i') }}
                </div>
            @endif

            <!-- Action Button -->
            <div style="text-align: center;">
                <a href="{{ url('/admin/notifications') }}" class="button">
                    Bildirimleri Görüntüle
                </a>
            </div>

            <!-- Additional Data -->
            @if ($notification->data && count($notification->data) > 0)
                <div style="margin-top: 30px;">
                    <h3 style="color: #374151; font-size: 16px;">Ek Bilgiler:</h3>
                    <div
                        style="background-color: #f9fafb; padding: 15px; border-radius: 6px; font-family: monospace; font-size: 14px;">
                        @foreach ($notification->data as $key => $value)
                            <strong>{{ $key }}:</strong>
                            {{ is_array($value) ? json_encode($value) : $value }}<br>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>
                Bu e-posta Yalıhan Emlak bildirim sistemi tarafından gönderilmiştir.<br>
                <strong>Yalıhan Emlak</strong> - Yalıkavak, Bodrum/Muğla<br>
                📞 0533 209 03 02 | 📧 info@yalihanemlak.com
            </p>
        </div>
    </div>
</body>

</html>
