<?php

declare(strict_types=1);

namespace App\Services\Telegram\Processors;

use App\Models\Kisi;
use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * ContactProcessor
 *
 * Context7 Standard: C7-TELEGRAM-CONTACT-2025-12-01
 *
 * Telegram'dan gelen kişi kartlarını (contact) işleyip CRM'e ekler.
 */
class ContactProcessor
{
    /**
     * Kişi kartını işle
     *
     * @param User $user Danışman
     * @param array $contactData Telegram contact verisi
     * @return void
     */
    public function handle(User $user, array $contactData): void
    {
        try {
            // Typing indicator göster
            $this->sendChatAction($user->telegram_id, 'typing');

            $phoneNumber = $this->normalizePhoneNumber($contactData['phone_number'] ?? '');
            $firstName = $contactData['first_name'] ?? '';
            $lastName = $contactData['last_name'] ?? '';

            if (empty($phoneNumber)) {
                $this->sendMessage($user->telegram_id, "❌ Telefon numarası bulunamadı. Lütfen geçerli bir kişi kartı gönderin.");
                return;
            }

            // Mevcut kişiyi ara
            $existingKisi = Kisi::where('telefon', $phoneNumber)
                ->orWhere('telefon', 'like', '%' . substr($phoneNumber, -10) . '%') // Son 10 haneyi kontrol et
                ->first();

            if ($existingKisi) {
                $message = "ℹ️ Bu numara zaten *{$existingKisi->tam_ad}* adına kayıtlı.\n\n";
                $message .= "📞 *Telefon:* {$existingKisi->telefon}\n";
                $message .= "📊 *Durum:* {$existingKisi->status}\n";
                $message .= "🔗 [CRM'de Gör](https://panel.yalihanemlak.com.tr/admin/kisiler/{$existingKisi->id})";

                $this->sendMessage($user->telegram_id, $message);
                return;
            }

            // Yeni kişi oluştur
            $kisi = Kisi::create([
                'ad' => $firstName ?: 'Bilinmeyen',
                'soyad' => $lastName ?: '',
                'telefon' => $phoneNumber,
                'status' => 'active', // Context7: status field
                'kisi_tipi' => 'lead', // Aday müşteri
                'danisman_id' => $user->id,
                'kaynak' => 'telegram_contact',
                'lead_source' => 'telegram',
            ]);

            Log::info('ContactProcessor: Yeni kişi oluşturuldu', [
                'kisi_id' => $kisi->id,
                'user_id' => $user->id,
                'phone' => $phoneNumber,
            ]);

            $message = "✅ *{$kisi->tam_ad}* sisteme 'Aday Müşteri' olarak eklendi.\n\n";
            $message .= "Hemen not eklemek için sesli mesaj gönderebilirsiniz.\n\n";
            $message .= "🔗 [CRM'de Gör](https://panel.yalihanemlak.com.tr/admin/kisiler/{$kisi->id})";

            $this->sendMessage($user->telegram_id, $message);
        } catch (\Exception $e) {
            Log::error('ContactProcessor: Hata', [
                'user_id' => $user->id ?? null,
                'contact_data' => $contactData,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->sendMessage($user->telegram_id ?? '', "❌ Kişi eklenirken hata oluştu: " . $e->getMessage());
        }
    }

    /**
     * Telefon numarasını normalize et
     * +90 555 123 45 67 -> 905551234567
     */
    private function normalizePhoneNumber(string $phone): string
    {
        // Sadece rakamları al
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Türkiye formatı: +90 ile başlıyorsa 0 ekle
        if (str_starts_with($phone, '90') && strlen($phone) == 12) {
            $phone = '0' . substr($phone, 2);
        }

        return $phone;
    }

    /**
     * Chat action gönder
     */
    private function sendChatAction(?string $chatId, string $action = 'typing'): void
    {
        if (!$chatId) {
            return;
        }

        try {
            $telegramService = app(\App\Modules\TakimYonetimi\Services\TelegramBotService::class);
            $telegramService->sendChatAction((int) $chatId, $action);
        } catch (\Exception $e) {
            Log::error('ContactProcessor: Chat action gönderme hatası', [
                'chat_id' => $chatId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Mesaj gönder
     */
    private function sendMessage(?string $chatId, string $text): void
    {
        if (!$chatId) {
            return;
        }

        try {
            $telegramService = app(\App\Modules\TakimYonetimi\Services\TelegramBotService::class);
            $telegramService->sendMessage((int) $chatId, $text);
        } catch (\Exception $e) {
            Log::error('ContactProcessor: Mesaj gönderme hatası', [
                'chat_id' => $chatId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
