<?php

namespace App\Services\AI;

use App\Models\AIIlanTaslagi;
use App\Models\Ilan;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * AI İlan Taslağı Servisi
 *
 * Context7 Standardı: C7-AI-ILAN-TASLAGI-SERVICE-2025-11-20
 *
 * AI tarafından ilan taslağı üretme ve yönetme servisi.
 * n8n + AnythingLLM entegrasyonu ile çalışır.
 */
class AIIlanTaslagiService
{
    /**
     * n8n webhook URL
     */
    protected string $n8nWebhookUrl;

    /**
     * Cache TTL (saniye)
     */
    protected int $cacheTTL = 3600;

    public function __construct()
    {
        $this->n8nWebhookUrl = config('services.n8n.webhook_url', '');
    }

    /**
     * İlan taslağı üret
     *
     * @param array $data İlan verileri
     * @param int $danismanId Danışman ID
     * @return AIIlanTaslagi
     */
    public function generateDraft(array $data, int $danismanId): AIIlanTaslagi
    {
        try {
            // n8n webhook'a istek gönder
            $response = Http::timeout(30)->post($this->n8nWebhookUrl . '/ai/ilan-taslagi', [
                'danisman_id' => $danismanId,
                'data' => $data,
            ]);

            if (!$response->successful()) {
                throw new \Exception('n8n webhook request failed: ' . $response->status());
            }

            $aiResponse = $response->json();

            // DB'ye kaydet (status=draft)
            $taslak = AIIlanTaslagi::create([
                'danisman_id' => $danismanId,
                'status' => 'draft',
                'ai_response' => $aiResponse,
                'ai_model_used' => $aiResponse['model'] ?? 'anythingllm',
                'ai_prompt_version' => $aiResponse['prompt_version'] ?? '1.0',
                'ai_generated_at' => now(),
            ]);

            Log::info('AI ilan taslağı oluşturuldu', [
                'taslak_id' => $taslak->id,
                'danisman_id' => $danismanId,
            ]);

            return $taslak;
        } catch (\Exception $e) {
            Log::error('AI ilan taslağı oluşturma hatası', [
                'error' => $e->getMessage(),
                'danisman_id' => $danismanId,
            ]);

            throw $e;
        }
    }

    /**
     * Taslağı onayla
     *
     * @param int $taslakId Taslak ID
     * @param int $userId Onaylayan kullanıcı ID
     * @return bool
     */
    public function approve(int $taslakId, int $userId): bool
    {
        $taslak = AIIlanTaslagi::findOrFail($taslakId);

        if (!$taslak->approve($userId)) {
            return false;
        }

        Log::info('AI ilan taslağı onaylandı', [
            'taslak_id' => $taslakId,
            'user_id' => $userId,
        ]);

        return true;
    }

    /**
     * Taslağı ilan'a dönüştür
     *
     * @param int $taslakId Taslak ID
     * @param int $userId Kullanıcı ID
     * @return Ilan
     */
    public function convertToIlan(int $taslakId, int $userId): Ilan
    {
        $taslak = AIIlanTaslagi::findOrFail($taslakId);

        if (!$taslak->isApproved()) {
            throw new \Exception('Taslak onaylanmamış, ilan oluşturulamaz');
        }

        $aiResponse = $taslak->ai_response;

        // İlan oluştur
        $ilan = Ilan::create([
            'baslik' => $aiResponse['baslik'] ?? 'AI Üretilmiş İlan',
            'aciklama' => $aiResponse['aciklama'] ?? '',
            'fiyat' => $aiResponse['fiyat'] ?? 0,
            'para_birimi' => $aiResponse['para_birimi'] ?? 'TRY',
            'danisman_id' => $taslak->danisman_id,
            'status' => 'Aktif',
            // Diğer alanlar...
        ]);

        // Taslağı güncelle
        $taslak->update([
            'ilan_id' => $ilan->id,
            'status' => 'published',
        ]);

        Log::info('AI ilan taslağı ilan\'a dönüştürüldü', [
            'taslak_id' => $taslakId,
            'ilan_id' => $ilan->id,
            'user_id' => $userId,
        ]);

        return $ilan;
    }

    /**
     * Danışmana ait taslakları getir
     *
     * @param int $danismanId Danışman ID
     * @param string|null $status Status filtresi
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getDraftsByDanisman(int $danismanId, ?string $status = null)
    {
        $query = AIIlanTaslagi::where('danisman_id', $danismanId);

        if ($status) {
            $query->where('status', $status);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }
}

