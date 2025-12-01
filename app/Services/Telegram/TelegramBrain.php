<?php

declare(strict_types=1);

namespace App\Services\Telegram;

use App\Models\User;
use App\Services\Telegram\Processors\AuthProcessor;
use App\Services\Telegram\Processors\ContactProcessor;
use App\Services\Telegram\Processors\PortfolioProcessor;
use App\Services\Telegram\Processors\TaskProcessor;
use App\Services\VoiceCommandProcessor;
use Illuminate\Support\Facades\Log;

/**
 * TelegramBrain
 *
 * Context7 Standard: C7-TELEGRAM-CORTEX-2025-12-01
 *
 * Telegram mesajlarını karşılayan ve dağıtan ana servis.
 * Cortex Architecture'ın merkezi yönlendirici servisi.
 */
class TelegramBrain
{
    private AuthProcessor $authProcessor;
    private TaskProcessor $taskProcessor;
    private PortfolioProcessor $portfolioProcessor;
    private ContactProcessor $contactProcessor;
    private VoiceCommandProcessor $voiceCommandProcessor;

    public function __construct(
        AuthProcessor $authProcessor,
        TaskProcessor $taskProcessor,
        PortfolioProcessor $portfolioProcessor,
        ContactProcessor $contactProcessor,
        VoiceCommandProcessor $voiceCommandProcessor
    ) {
        $this->authProcessor = $authProcessor;
        $this->taskProcessor = $taskProcessor;
        $this->portfolioProcessor = $portfolioProcessor;
        $this->contactProcessor = $contactProcessor;
        $this->voiceCommandProcessor = $voiceCommandProcessor;
    }

    /**
     * Gelen webhook update'ini işle
     *
     * @param array $update Telegram webhook update data
     * @return void
     */
    public function handle(array $update): void
    {
        try {
            if (!isset($update['message'])) {
                return;
            }

            $message = $update['message'];
            $chatId = (string) ($message['chat']['id'] ?? '');
            $text = $message['text'] ?? '';
            $from = $message['from'] ?? [];

            Log::info('TelegramBrain: Mesaj alındı', [
                'chat_id' => $chatId,
                'has_text' => !empty($text),
                'has_voice' => isset($message['voice']),
                'has_location' => isset($message['location']),
                'has_contact' => isset($message['contact']),
            ]);

            // 1. Kimlik Kontrolü
            $user = User::where('telegram_id', $chatId)->first();

            if (!$user) {
                // Kullanıcı yoksa -> Eşleştirme Modülü
                $this->authProcessor->handle($chatId, $message);
                return;
            }

            // 2. Kullanıcı varsa -> Mesaj tipine göre işle

            // Contact (Kişi Kartı) mesaj
            if (isset($message['contact'])) {
                $this->sendChatAction($chatId, 'typing');
                $this->contactProcessor->handle($user, $message['contact']);
                return;
            }

            // Voice mesaj
            if (isset($message['voice'])) {
                $this->sendChatAction($chatId, 'upload_voice');
                // Voice-to-CRM işlemi (mevcut sistem)
                $this->handleVoiceMessage($chatId, $message['voice'], $from, $user);
                return;
            }

            // Location mesaj
            if (isset($message['location'])) {
                $this->sendChatAction($chatId, 'find_location');
                $lat = $message['location']['latitude'] ?? null;
                $lon = $message['location']['longitude'] ?? null;
                if ($lat && $lon) {
                    $this->portfolioProcessor->findNearMe($user, $lat, $lon);
                }
                return;
            }

            // Komut işleme
            if (str_starts_with($text, '/')) {
                $this->handleCommand($chatId, $text, $user);
                return;
            }

            // Normal mesaj
            $this->handleNormalMessage($chatId, $text, $user);
        } catch (\Exception $e) {
            Log::error('TelegramBrain: Hata', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Komut işle
     */
    private function handleCommand(string $chatId, string $text, User $user): void
    {
        $this->sendChatAction($chatId, 'typing');

        $command = strtolower(trim($text));

        switch ($command) {
            case '/ozet':
                $this->taskProcessor->dailySummary($user);
                break;

            case '/gorevler':
                $this->taskProcessor->pendingTasks($user);
                break;

            case '/yardim':
            case '/help':
                $this->sendHelpMessage($chatId);
                break;

            default:
                $this->sendMessage($chatId, "❌ Bilinmeyen komut: {$text}\n\n/yardim yazarak mevcut komutları görebilirsiniz.");
                break;
        }
    }

    /**
     * Normal mesaj işle
     */
    private function handleNormalMessage(string $chatId, string $text, User $user): void
    {
        // Şimdilik basit bir yanıt
        $this->sendMessage($chatId, "💡 Komutlar için /yardim yazabilirsiniz.");
    }

    /**
     * Voice mesaj işle (mevcut Voice-to-CRM sistemi)
     */
    private function handleVoiceMessage(string $chatId, array $voice, array $from, User $user): void
    {
        // Bu işlem mevcut TelegramBotService'te var
        // Şimdilik basit bir yanıt
        $this->sendMessage($chatId, "🎤 Sesli not alınıyor... (Voice-to-CRM işlemi devam ediyor)");
    }

    /**
     * Yardım mesajı gönder
     */
    private function sendHelpMessage(string $chatId): void
    {
        $message = "📚 *Yalıhan Cortex Bot - Yardım Menüsü*\n\n";
        $message .= "🔹 *Komutlar:*\n";
        $message .= "• `/ozet` - Günlük özet (randevular, acil işler)\n";
        $message .= "• `/gorevler` - Bekleyen görevleriniz\n";
        $message .= "• `/yardim` - Bu yardım menüsü\n\n";
        $message .= "🎤 *Sesli Not:*\n";
        $message .= "Sesli mesaj göndererek CRM notu oluşturabilirsiniz.\n\n";
        $message .= "📍 *Konum:*\n";
        $message .= "Konum paylaşarak yakınınızdaki ilanları görebilirsiniz.\n\n";
        $message .= "👤 *Kişi Kartı:*\n";
        $message .= "Kişi kartı paylaşarak CRM'e otomatik ekleyebilirsiniz.\n\n";
        $message .= "💡 *Daha fazla bilgi için:*\n";
        $message .= "Panel: https://panel.yalihanemlak.com.tr";

        $this->sendMessage($chatId, $message);
    }

    /**
     * Chat action gönder (typing indicator)
     */
    private function sendChatAction(string $chatId, string $action = 'typing'): void
    {
        try {
            $telegramService = app(\App\Modules\TakimYonetimi\Services\TelegramBotService::class);
            $telegramService->sendChatAction((int) $chatId, $action);
        } catch (\Exception $e) {
            Log::error('TelegramBrain: Chat action gönderme hatası', [
                'chat_id' => $chatId,
                'action' => $action,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Mesaj gönder (TelegramService üzerinden)
     */
    private function sendMessage(string $chatId, string $text): void
    {
        try {
            $telegramService = app(\App\Modules\TakimYonetimi\Services\TelegramBotService::class);
            $telegramService->sendMessage((int) $chatId, $text);
        } catch (\Exception $e) {
            Log::error('TelegramBrain: Mesaj gönderme hatası', [
                'chat_id' => $chatId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
