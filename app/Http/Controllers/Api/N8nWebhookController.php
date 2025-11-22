<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AI\AIIlanTaslagiService;
use App\Services\AI\AIMessageService;
use App\Services\AI\AIContractService;
use App\Services\Response\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * n8n Webhook Controller
 *
 * Context7 Standardı: C7-N8N-WEBHOOK-CONTROLLER-2025-11-20
 *
 * n8n workflow'larından gelen webhook isteklerini işler.
 * AnythingLLM entegrasyonu için endpoint'ler sağlar.
 */
class N8nWebhookController extends Controller
{
    protected AIIlanTaslagiService $ilanTaslagiService;
    protected AIMessageService $messageService;
    protected AIContractService $contractService;

    public function __construct(
        AIIlanTaslagiService $ilanTaslagiService,
        AIMessageService $messageService,
        AIContractService $contractService
    ) {
        $this->ilanTaslagiService = $ilanTaslagiService;
        $this->messageService = $messageService;
        $this->contractService = $contractService;
    }

    /**
     * AI İlan Taslağı Webhook
     *
     * n8n'den gelen ilan taslağı isteklerini işler.
     * AnythingLLM'den gelen response'u DB'ye kaydeder.
     *
     * POST /api/webhook/n8n/ai/ilan-taslagi
     */
    public function ilanTaslagi(Request $request)
    {
        try {
            $validated = Validator::make($request->all(), [
                'danisman_id' => 'required|integer|exists:users,id',
                'data' => 'required|array',
                'ai_response' => 'required|array',
                'ai_model_used' => 'nullable|string',
                'ai_prompt_version' => 'nullable|string',
            ]);

            if ($validated->fails()) {
                return ResponseService::validationError($validated->errors()->toArray());
            }

            // AI response'u DB'ye kaydet
            $taslak = \App\Models\AIIlanTaslagi::create([
                'danisman_id' => $request->input('danisman_id'),
                'status' => 'draft',
                'ai_response' => $request->input('ai_response'),
                'ai_model_used' => $request->input('ai_model_used', 'anythingllm'),
                'ai_prompt_version' => $request->input('ai_prompt_version', '1.0'),
                'ai_generated_at' => now(),
            ]);

            Log::info('n8n webhook: AI ilan taslağı kaydedildi', [
                'taslak_id' => $taslak->id,
                'danisman_id' => $request->danisman_id,
            ]);

            return ResponseService::success([
                'taslak_id' => $taslak->id,
                'status' => $taslak->status,
            ], 'AI ilan taslağı başarıyla oluşturuldu');
        } catch (\Exception $e) {
            Log::error('n8n webhook: AI ilan taslağı hatası', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
            ]);

            return ResponseService::serverError('AI ilan taslağı oluşturulamadı', $e);
        }
    }

    /**
     * AI Mesaj Taslağı Webhook
     *
     * n8n'den gelen mesaj taslağı isteklerini işler.
     *
     * POST /api/webhook/n8n/ai/mesaj-taslagi
     */
    public function mesajTaslagi(Request $request)
    {
        try {
            $validated = Validator::make($request->all(), [
                'communication_id' => 'required|integer|exists:communications,id',
                'channel' => 'required|string|in:telegram,whatsapp,instagram,email,web',
                'content' => 'required|string',
                'ai_model_used' => 'nullable|string',
            ]);

            if ($validated->fails()) {
                return ResponseService::validationError($validated->errors()->toArray());
            }

            // AI response'u DB'ye kaydet
            $message = \App\Models\AIMessage::create([
                'communication_id' => $request->input('communication_id'),
                'channel' => $request->input('channel'),
                'role' => 'assistant',
                'content' => $request->input('content'),
                'status' => 'draft',
                'ai_model_used' => $request->input('ai_model_used', 'anythingllm'),
                'ai_generated_at' => now(),
            ]);

            Log::info('n8n webhook: AI mesaj taslağı kaydedildi', [
                'message_id' => $message->id,
                'communication_id' => $request->communication_id,
            ]);

            return ResponseService::success([
                'message_id' => $message->id,
                'status' => $message->status,
            ], 'AI mesaj taslağı başarıyla oluşturuldu');
        } catch (\Exception $e) {
            Log::error('n8n webhook: AI mesaj taslağı hatası', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
            ]);

            return ResponseService::serverError('AI mesaj taslağı oluşturulamadı', $e);
        }
    }

    /**
     * AI Sözleşme Taslağı Webhook
     *
     * n8n'den gelen sözleşme taslağı isteklerini işler.
     *
     * POST /api/webhook/n8n/ai/sozlesme-taslagi
     */
    public function sozlesmeTaslagi(Request $request)
    {
        try {
            $validated = Validator::make($request->all(), [
                'contract_type' => 'required|string|in:kira,satis',
                'property_id' => 'nullable|integer|exists:ilanlar,id',
                'kisi_id' => 'nullable|integer|exists:kisiler,id',
                'content' => 'required|string',
                'ai_model_used' => 'nullable|string',
            ]);

            if ($validated->fails()) {
                return ResponseService::validationError($validated->errors()->toArray());
            }

            // AI response'u DB'ye kaydet
            $draft = \App\Models\AIContractDraft::create([
                'contract_type' => $request->input('contract_type'),
                'property_id' => $request->input('property_id'),
                'kisi_id' => $request->input('kisi_id'),
                'status' => 'draft',
                'content' => $request->input('content'),
                'ai_model_used' => $request->input('ai_model_used', 'anythingllm'),
                'ai_generated_at' => now(),
            ]);

            Log::info('n8n webhook: AI sözleşme taslağı kaydedildi', [
                'draft_id' => $draft->id,
                'contract_type' => $request->contract_type,
            ]);

            return ResponseService::success([
                'draft_id' => $draft->id,
                'status' => $draft->status,
            ], 'AI sözleşme taslağı başarıyla oluşturuldu');
        } catch (\Exception $e) {
            Log::error('n8n webhook: AI sözleşme taslağı hatası', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
            ]);

            return ResponseService::serverError('AI sözleşme taslağı oluşturulamadı', $e);
        }
    }

    /**
     * Test Webhook
     *
     * n8n entegrasyonunu test etmek için basit bir endpoint.
     *
     * POST /api/webhook/n8n/test
     */
    public function test(Request $request)
    {
        Log::info('n8n webhook test', [
            'request' => $request->all(),
            'headers' => $request->headers->all(),
        ]);

        return ResponseService::success([
            'message' => 'n8n webhook test başarılı',
            'timestamp' => now()->toISOString(),
            'received_data' => $request->all(),
        ], 'Webhook test başarılı');
    }
}
