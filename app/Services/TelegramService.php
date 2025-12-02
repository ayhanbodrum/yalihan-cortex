<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Ilan;
use App\Models\Setting;
use App\Models\Talep;
use App\Models\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * TelegramService
 *
 * Context7 Standard: C7-TELEGRAM-SERVICE-2025-11-30
 *
 * Cortex kritik fırsat bildirimleri için Telegram entegrasyonu
 */
class TelegramService
{
    private string $botToken;
    private ?string $adminChatId;

    public function __construct()
    {
        // Bot Token: .env veya settings tablosundan
        $this->botToken = env('TELEGRAM_BOT_TOKEN', '');

        // Settings tablosundan override etme (eğer varsa)
        try {
            $telegramBotToken = Setting::get('telegram_bot_token');
            if ($telegramBotToken) {
                $this->botToken = $telegramBotToken;
            }
        } catch (\Throwable $e) {
            Log::notice('TelegramService settings override skipped', ['error' => $e->getMessage()]);
        }

        // Admin Chat ID: .env veya settings tablosundan
        $this->adminChatId = env('TELEGRAM_ADMIN_CHAT_ID', null);

        // Settings tablosundan override etme (eğer varsa)
        try {
            $telegramAdminChatId = Setting::get('telegram_admin_chat_id');
            if ($telegramAdminChatId) {
                $this->adminChatId = $telegramAdminChatId;
            }
        } catch (\Throwable $e) {
            Log::notice('TelegramService admin chat ID override skipped', ['error' => $e->getMessage()]);
        }

        // Eğer admin chat ID yoksa, super_admin rolündeki kullanıcıların chat ID'lerini al
        if (empty($this->adminChatId)) {
            try {
                $adminUser = User::where('role_id', 1) // super_admin
                    ->whereNotNull('telegram_chat_id')
                    ->first();

                if ($adminUser && $adminUser->telegram_chat_id) {
                    $this->adminChatId = (string) $adminUser->telegram_chat_id;
                }
            } catch (\Throwable $e) {
                Log::notice('TelegramService admin user lookup skipped', ['error' => $e->getMessage()]);
            }
        }
    }

    /**
     * Kritik fırsat bildirimi gönder
     *
     * Context7 Standard: C7-TELEGRAM-RATE-LIMITING-2025-12-01
     * Rate limiting: Aynı ilan/talep için 1 saat içinde max 1 bildirim
     *
     * @param array $opportunityData
     * @param int $maxRetries Maksimum retry sayısı (varsayılan: 3)
     * @return bool
     */
    public function sendCriticalAlert(array $opportunityData, int $maxRetries = 3): bool
    {
        if (empty($this->botToken)) {
            Log::warning('TelegramService: Bot token eksik, bildirim gönderilemedi.');
            return false;
        }

        if (empty($this->adminChatId)) {
            Log::warning('TelegramService: Admin chat ID eksik, bildirim gönderilemedi.');
            return false;
        }

        // Rate limiting kontrolü: Aynı ilan/talep için 1 saat içinde max 1 bildirim
        $rateLimitKey = $this->getRateLimitKey($opportunityData);
        if (Cache::has($rateLimitKey)) {
            Log::info('TelegramService: Rate limit hit - Bildirim zaten gönderilmiş', [
                'rate_limit_key' => $rateLimitKey,
                'ilan_id' => $opportunityData['ilan_id'] ?? null,
                'talep_id' => $opportunityData['talep_id'] ?? null,
            ]);
            return false; // Zaten bildirim gönderilmiş
        }

        try {
            $message = $this->buildCriticalAlertMessage($opportunityData);

            // Laravel'in yerleşik retry mekanizması: 3 deneme, 200ms bekleme
            // 4xx hatalarında retry yapmaz (client hatası)
            $response = Http::retry(3, 200, function ($exception, $request) {
                // ConnectionException (ağ hatası) durumunda retry yap
                if ($exception instanceof ConnectionException) {
                    return true;
                }

                // RequestException durumunda status code'a bak
                if ($exception instanceof RequestException) {
                    $statusCode = $exception->response?->status();
                    // 5xx hatalarında retry yap, 4xx hatalarında yapma
                    return $statusCode >= 500;
                }

                // Diğer exception'larda retry yap
                return true;
            })
                ->timeout(10) // Telegram 10 saniye cevap vermezse kes
                ->post("https://api.telegram.org/bot{$this->botToken}/sendMessage", [
                    'chat_id' => $this->adminChatId,
                    'text' => $message,
                    'parse_mode' => 'Markdown',
                    'disable_web_page_preview' => false,
                ])
                ->throw(); // Hata durumunda exception fırlat

            // Başarılı - Rate limit kaydet (1 saat)
            Cache::put($rateLimitKey, true, now()->addHour());

            Log::info('TelegramService: Kritik fırsat bildirimi gönderildi', [
                'opportunity_id' => $opportunityData['id'] ?? null,
                'score' => $opportunityData['score'] ?? null,
                'rate_limit_key' => $rateLimitKey,
            ]);

            return true;
        } catch (RequestException $e) {
            // HTTP hatası (4xx, 5xx)
            $statusCode = $e->response?->status() ?? 0;

            if ($statusCode >= 400 && $statusCode < 500) {
                // Client hatası (4xx) - retry yapılmadı (doğru)
                Log::error('TelegramService: Client hatası, retry yapılmadı', [
                    'status' => $statusCode,
                    'response' => $e->response?->body(),
                    'opportunity_data' => $opportunityData,
                ]);
            } else {
                // Server hatası (5xx) - retry yapıldı ama başarısız
                Log::error('TelegramService: Server hatası, tüm retry\'lar tükendi', [
                    'status' => $statusCode,
                    'response' => $e->response?->body(),
                    'opportunity_data' => $opportunityData,
                ]);
            }

            return false;
        } catch (ConnectionException $e) {
            // Ağ bağlantı hatası - retry yapıldı ama başarısız
            Log::error('TelegramService: Bağlantı hatası, tüm retry\'lar tükendi', [
                'error' => $e->getMessage(),
                'opportunity_data' => $opportunityData,
            ]);

            return false;
        } catch (\Exception $e) {
            // Diğer exception'lar
            Log::error('TelegramService: Bildirim gönderme exception', [
                'error' => $e->getMessage(),
                'exception' => get_class($e),
                'opportunity_data' => $opportunityData,
            ]);

            return false;
        }
    }

    /**
     * Rate limit key oluştur
     *
     * Context7 Standard: C7-TELEGRAM-RATE-LIMITING-2025-12-01
     * Key format: telegram:alert:{ilan_id}:{talep_id}
     *
     * @param array $opportunityData
     * @return string
     */
    private function getRateLimitKey(array $opportunityData): string
    {
        $ilanId = $opportunityData['ilan_id'] ?? 'unknown';
        $talepId = $opportunityData['talep_id'] ?? 'unknown';

        return "telegram:alert:{$ilanId}:{$talepId}";
    }

    /**
     * Kritik fırsat mesaj şablonunu oluştur
     *
     * @param array $opportunityData
     * @return string
     */
    private function buildCriticalAlertMessage(array $opportunityData): string
    {
        $score = $opportunityData['score'] ?? 0;
        $type = $opportunityData['type'] ?? 'unknown'; // 'ilan_match' veya 'talep_match'
        $ilanId = $opportunityData['ilan_id'] ?? null;
        $talepId = $opportunityData['talep_id'] ?? null;
        $ilanBaslik = $opportunityData['ilan_baslik'] ?? 'Bilinmeyen İlan';
        $talepBaslik = $opportunityData['talep_baslik'] ?? 'Bilinmeyen Talep';

        // İlan veya Talep bilgilerini çek
        $ilan = null;
        $talep = null;
        $musteriAdi = 'Bilinmeyen';
        $danismanAdi = 'Bilinmeyen';
        $riskDurumu = '';
        $danismanYuku = '';

        if ($ilanId) {
            $ilan = Ilan::with(['kisi', 'danisman'])->find($ilanId);
            if ($ilan) {
                $musteriAdi = $ilan->kisi?->tam_ad ?? 'Bilinmeyen';
                $danismanAdi = $ilan->danisman?->name ?? 'Bilinmeyen';

                // Danışman yükü analizi (basit)
                $danismanGorevSayisi = $ilan->danisman?->gorevler()->whereIn('status', ['bekliyor', 'devam_ediyor'])->count() ?? 0;
                if ($danismanGorevSayisi > 10) {
                    $danismanYuku = '🔴 Yüksek';
                } elseif ($danismanGorevSayisi > 5) {
                    $danismanYuku = '🟡 Orta';
                } else {
                    $danismanYuku = '🟢 Normal';
                }
            }
        }

        if ($talepId) {
            $talep = Talep::with(['kisi', 'danisman'])->find($talepId);
            if ($talep) {
                $musteriAdi = $talep->kisi?->tam_ad ?? 'Bilinmeyen';
                $danismanAdi = $talep->danisman?->name ?? 'Bilinmeyen';

                // Müşteri risk analizi (basit - son iletişim tarihine göre)
                $sonIletisim = $talep->updated_at ?? $talep->created_at;
                $gunFarki = now()->diffInDays($sonIletisim);
                if ($gunFarki > 20) {
                    $riskDurumu = '❄️ Yüksek (20+ gün aranmadı)';
                } elseif ($gunFarki > 10) {
                    $riskDurumu = '🟡 Orta (10+ gün aranmadı)';
                } else {
                    $riskDurumu = '🟢 Düşük';
                }
            }
        }

        // Mesaj şablonu
        $message = "🚨 *CORTEX KRİTİK FIRSAT ALARMI* 🚨\n\n";
        $message .= "📈 *Eşleşme Skoru:* %{$score}\n\n";

        if ($type === 'ilan_match' && $ilan) {
            $message .= "🏠 *Mülk:* [{$ilanBaslik}]\n";
            $message .= "📍 *Lokasyon:* " . ($ilan->ilce?->name ?? 'Bilinmeyen') . " / " . ($ilan->mahalle?->name ?? 'Bilinmeyen') . "\n";
            if ($ilan->ada_no && $ilan->parsel_no) {
                $message .= "📋 *Ada/Parsel:* {$ilan->ada_no}/{$ilan->parsel_no}\n";
            }
            $message .= "\n";
        } elseif ($type === 'talep_match' && $talep) {
            $message .= "👤 *Müşteri:* {$musteriAdi}\n";
            $message .= "📋 *Talep:* {$talepBaslik}\n";
            $message .= "📍 *Aranan Lokasyon:* " . ($talep->ilce?->name ?? 'Bilinmeyen') . " / " . ($talep->mahalle?->name ?? 'Bilinmeyen') . "\n";
            $message .= "\n";
        }

        $message .= "👤 *Müşteri:* {$musteriAdi}";
        if ($riskDurumu) {
            $message .= " (Risk: {$riskDurumu})";
        }
        $message .= "\n";

        $message .= "👨‍💼 *Danışman:* {$danismanAdi}";
        if ($danismanYuku) {
            $message .= " (Yük: {$danismanYuku})";
        }
        $message .= "\n\n";

        // AI Analizi
        $message .= "💡 *AI Analizi:*\n";
        if ($type === 'ilan_match') {
            $message .= "Bu ilan, aktif taleplerle %{$score} uyum gösteriyor. ";
            if ($score >= 95) {
                $message .= "Mükemmel eşleşme! Acil müdahale önerilir.";
            } elseif ($score >= 90) {
                $message .= "Yüksek uyum! Hızlıca değerlendirilmeli.";
            }
        } elseif ($type === 'talep_match') {
            $message .= "Bu müşteri için %{$score} uyumlu ilan bulundu. ";
            if ($riskDurumu && str_contains($riskDurumu, 'Yüksek')) {
                $message .= "Müşteri uzun süredir aranmadı, kaybetmemek için ACİL müdahale önerilir.";
            } else {
                $message .= "Hızlıca değerlendirilmeli.";
            }
        }
        $message .= "\n\n";

        // Detay linki
        $baseUrl = config('app.url');
        if ($ilanId) {
            $detailUrl = "{$baseUrl}/admin/ilanlar/{$ilanId}/edit";
            $message .= "[📊 Detayları Gör]({$detailUrl})";
        } elseif ($talepId) {
            $detailUrl = "{$baseUrl}/admin/talepler/{$talepId}";
            $message .= "[📊 Detayları Gör]({$detailUrl})";
        }

        return $message;
    }

    /**
     * Birden fazla admin'e bildirim gönder
     *
     * @param array $opportunityData
     * @return int Gönderilen bildirim sayısı
     */
    public function sendCriticalAlertToAllAdmins(array $opportunityData): int
    {
        if (empty($this->botToken)) {
            Log::warning('TelegramService: Bot token eksik, bildirim gönderilemedi.');
            return 0;
        }

        $sentCount = 0;

        try {
            // Tüm super_admin ve admin kullanıcılarını bul
            $adminUsers = User::whereIn('role_id', [1, 2]) // super_admin, admin
                ->whereNotNull('telegram_chat_id')
                ->get();

            foreach ($adminUsers as $admin) {
                if ($this->sendCriticalAlertToChatId($opportunityData, (string) $admin->telegram_chat_id)) {
                    $sentCount++;
                }
            }
        } catch (\Exception $e) {
            Log::error('TelegramService: Toplu bildirim gönderme hatası', [
                'error' => $e->getMessage(),
            ]);
        }

        return $sentCount;
    }

    /**
     * Belirli bir chat ID'ye kritik fırsat bildirimi gönder
     *
     * @param array $opportunityData
     * @param string $chatId
     * @return bool
     */
    private function sendCriticalAlertToChatId(array $opportunityData, string $chatId): bool
    {
        if (empty($this->botToken)) {
            return false;
        }

        try {
            $message = $this->buildCriticalAlertMessage($opportunityData);

            $response = Http::timeout(5)
                ->post("https://api.telegram.org/bot{$this->botToken}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => $message,
                    'parse_mode' => 'Markdown',
                    'disable_web_page_preview' => false,
                ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('TelegramService: Chat ID\'ye bildirim gönderme hatası', [
                'chat_id' => $chatId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
