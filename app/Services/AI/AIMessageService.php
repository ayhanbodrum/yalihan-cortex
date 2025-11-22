<?php

namespace App\Services\AI;

use App\Models\AIMessage;
use App\Models\AIConversation;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * AI Mesaj Servisi
 *
 * Context7 Standardı: C7-AI-MESSAGE-SERVICE-2025-11-20
 *
 * AI tarafından mesaj taslağı üretme ve yönetme servisi.
 * n8n + AnythingLLM entegrasyonu ile çalışır.
 */
class AIMessageService
{
    /**
     * n8n webhook URL
     */
    protected string $n8nWebhookUrl;

    public function __construct()
    {
        $this->n8nWebhookUrl = config('services.n8n.webhook_url', '');
    }

    /**
     * Cevap taslağı üret
     *
     * @param int $communicationId İletişim ID
     * @return AIMessage
     */
    public function generateDraftReply(int $communicationId): AIMessage
    {
        try {
            // İletişim bilgilerini al
            $communication = \App\Models\Communication::findOrFail($communicationId);

            // Portföy bilgilerini topla (eğer ilan/kisi ilişkisi varsa)
            $portfolioData = $this->collectPortfolioData($communication);

            // n8n webhook'a istek gönder
            $response = Http::timeout(60)->post($this->n8nWebhookUrl . '/ai/mesaj-taslagi', [
                'communication_id' => $communicationId,
                'message' => $communication->message,
                'channel' => $communication->channel,
                'sender_name' => $communication->sender_name,
                'sender_phone' => $communication->sender_phone,
                'ai_analysis' => $communication->ai_analysis,
                'portfolio_data' => $portfolioData,
            ]);

            if (!$response->successful()) {
                throw new \Exception('n8n webhook request failed: ' . $response->status());
            }

            $aiResponse = $response->json();

            // Conversation oluştur veya bul
            $conversation = $this->getOrCreateConversation($communication);

            // DB'ye kaydet (status=draft)
            $message = AIMessage::create([
                'conversation_id' => $conversation->id,
                'communication_id' => $communicationId,
                'channel' => $communication->channel,
                'role' => 'assistant',
                'content' => $aiResponse['content'] ?? $aiResponse['message'] ?? '',
                'status' => 'draft',
                'ai_model_used' => $aiResponse['model'] ?? $aiResponse['ai_model_used'] ?? 'anythingllm',
                'ai_prompt_version' => $aiResponse['ai_prompt_version'] ?? '1.0.0',
                'ai_generated_at' => now(),
            ]);

            Log::info('AI mesaj taslağı oluşturuldu', [
                'message_id' => $message->id,
                'communication_id' => $communicationId,
                'conversation_id' => $conversation->id,
            ]);

            return $message;
        } catch (\Exception $e) {
            Log::error('AI mesaj taslağı oluşturma hatası', [
                'error' => $e->getMessage(),
                'communication_id' => $communicationId,
            ]);

            throw $e;
        }
    }

    /**
     * Portföy verilerini topla
     */
    protected function collectPortfolioData($communication): array
    {
        $data = [];

        // İlan ilişkisi varsa
        if ($communication->communicable_type === 'App\Models\Ilan') {
            $ilan = $communication->communicable;
            if ($ilan) {
                $data['ilan'] = [
                    'id' => $ilan->id,
                    'baslik' => $ilan->baslik,
                    'fiyat' => $ilan->fiyat,
                    'kategori' => $ilan->kategori->name ?? null,
                ];
            }
        }

        // Kişi ilişkisi varsa
        if ($communication->communicable_type === 'App\Models\Kisi') {
            $kisi = $communication->communicable;
            if ($kisi) {
                $data['kisi'] = [
                    'id' => $kisi->id,
                    'adi' => $kisi->adi,
                    'telefon' => $kisi->telefon,
                ];
            }
        }

        return $data;
    }

    /**
     * Conversation oluştur veya bul
     */
    protected function getOrCreateConversation($communication): AIConversation
    {
        // Sender ID'ye göre conversation bul
        $conversation = AIConversation::where('channel', $communication->channel)
            ->whereJsonContains('messages', ['sender_id' => $communication->sender_id])
            ->first();

        if (!$conversation) {
            // Yeni conversation oluştur
            $conversation = AIConversation::create([
                'user_id' => $communication->created_by,
                'channel' => $communication->channel,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $communication->message,
                        'sender_id' => $communication->sender_id,
                        'timestamp' => $communication->created_at->toIso8601String(),
                    ],
                ],
                'status' => 'active',
            ]);
        } else {
            // Mevcut conversation'a mesaj ekle
            $messages = $conversation->messages ?? [];
            $messages[] = [
                'role' => 'user',
                'content' => $communication->message,
                'sender_id' => $communication->sender_id,
                'timestamp' => $communication->created_at->toIso8601String(),
            ];
            $conversation->update(['messages' => $messages]);
        }

        return $conversation;
    }

    /**
     * Mesajı onayla
     *
     * @param int $messageId Mesaj ID
     * @param int $userId Onaylayan kullanıcı ID
     * @return AIMessage
     */
    public function approveMessage(int $messageId, int $userId): AIMessage
    {
        $message = AIMessage::findOrFail($messageId);

        $message->update([
            'status' => 'approved',
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);

        Log::info('AI mesaj taslağı onaylandı', [
            'message_id' => $messageId,
            'user_id' => $userId,
        ]);

        return $message;
    }

    /**
     * Mesajı gönder
     *
     * @param int $messageId Mesaj ID
     * @return AIMessage
     */
    public function sendMessage(int $messageId): AIMessage
    {
        $message = AIMessage::findOrFail($messageId);

        if ($message->status !== 'approved') {
            throw new \Exception('Mesaj onaylanmamış, gönderilemez');
        }

        // Channel'a göre gönderim yap
        $sent = false;
        switch ($message->channel) {
            case 'telegram':
                $sent = $this->sendTelegramMessage($message);
                break;
            case 'whatsapp':
                // TODO: WhatsApp entegrasyonu
                break;
            case 'instagram':
                // TODO: Instagram entegrasyonu
                break;
            case 'email':
                $sent = $this->sendEmailMessage($message);
                break;
            case 'web':
                // Web form mesajları için özel işlem gerekmez
                $sent = true;
                break;
        }

        if ($sent) {
            $message->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            // Communication'ı replied olarak işaretle
            if ($message->communication) {
                $message->communication->markAsReplied();
            }

            Log::info('AI mesaj gönderildi', [
                'message_id' => $messageId,
                'channel' => $message->channel,
            ]);
        }

        return $message;
    }

    /**
     * Telegram mesajı gönder
     */
    protected function sendTelegramMessage(AIMessage $message): bool
    {
        try {
            $communication = $message->communication;
            if (!$communication || !$communication->sender_id) {
                return false;
            }

            $telegramService = app(\App\Modules\TakimYonetimi\Services\TelegramBotService::class);
            return $telegramService->sendMessage(
                (int) $communication->sender_id,
                $message->content
            );
        } catch (\Exception $e) {
            Log::error('Telegram mesaj gönderme hatası', [
                'error' => $e->getMessage(),
                'message_id' => $message->id,
            ]);
            return false;
        }
    }

    /**
     * Email mesajı gönder
     */
    protected function sendEmailMessage(AIMessage $message): bool
    {
        try {
            $communication = $message->communication;
            if (!$communication || !$communication->sender_email) {
                return false;
            }

            // Laravel Mail kullanarak gönder
            \Illuminate\Support\Facades\Mail::raw($message->content, function ($mail) use ($communication) {
                $mail->to($communication->sender_email)
                    ->subject('Yalıhan Emlak - Mesajınıza Cevap');
            });

            return true;
        } catch (\Exception $e) {
            Log::error('Email mesaj gönderme hatası', [
                'error' => $e->getMessage(),
                'message_id' => $message->id,
            ]);
            return false;
        }
    }
}

