<?php

namespace App\Http\Controllers\Api;

use App\Events\IlanCreated;
use App\Http\Controllers\Controller;
use App\Models\Ilan;
use App\Services\AI\AIContractService;
use App\Services\AI\AIIlanTaslagiService;
use App\Services\AI\AIMessageService;
use App\Services\Logging\LogService;
use App\Services\Response\ResponseService;
use Illuminate\Http\Request;
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

            LogService::info('n8n webhook: AI ilan taslağı kaydedildi', [
                'taslak_id' => $taslak->id,
                'danisman_id' => $request->danisman_id,
            ], LogService::CHANNEL_API);

            return ResponseService::success([
                'taslak_id' => $taslak->id,
                'status' => $taslak->status,
            ], 'AI ilan taslağı başarıyla oluşturuldu');
        } catch (\Exception $e) {
            LogService::error('n8n webhook: AI ilan taslağı hatası', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
            ], $e, LogService::CHANNEL_API);

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

            LogService::info('n8n webhook: AI mesaj taslağı kaydedildi', [
                'message_id' => $message->id,
                'communication_id' => $request->communication_id,
            ], LogService::CHANNEL_API);

            return ResponseService::success([
                'message_id' => $message->id,
                'status' => $message->status,
            ], 'AI mesaj taslağı başarıyla oluşturuldu');
        } catch (\Exception $e) {
            LogService::error('n8n webhook: AI mesaj taslağı hatası', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
            ], $e, LogService::CHANNEL_API);

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

            LogService::info('n8n webhook: AI sözleşme taslağı kaydedildi', [
                'draft_id' => $draft->id,
                'contract_type' => $request->contract_type,
            ], LogService::CHANNEL_API);

            return ResponseService::success([
                'draft_id' => $draft->id,
                'status' => $draft->status,
            ], 'AI sözleşme taslağı başarıyla oluşturuldu');
        } catch (\Exception $e) {
            LogService::error('n8n webhook: AI sözleşme taslağı hatası', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
            ], $e, LogService::CHANNEL_API);

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
        LogService::info('n8n webhook test', [
            'request' => $request->all(),
            'headers' => $request->headers->all(),
        ], LogService::CHANNEL_API);

        return ResponseService::success([
            'message' => 'n8n webhook test başarılı',
            'timestamp' => now()->toISOString(),
            'received_data' => $request->all(),
        ], 'Webhook test başarılı');
    }

    /**
     * Emsal Arama (Market Analysis)
     *
     * n8n'den gelen emsal arama isteklerini işler.
     * Gelen kriterlere göre Ilan tablosunda emsal arama yapar.
     *
     * POST /api/v1/webhook/n8n/analyze-market
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function analyzeMarket(Request $request)
    {
        try {
            $validated = Validator::make($request->all(), [
                'location' => 'required|string|max:255',
                'm2' => 'required|numeric|min:1',
                'type' => 'required|string|in:tarla,arsa,konut,isyeri,villa,yazlik',
            ]);

            if ($validated->fails()) {
                return ResponseService::validationError($validated->errors()->toArray());
            }

            $location = $request->input('location');
            $m2 = $request->input('m2');
            $type = $request->input('type');

            LogService::api(
                'n8n_webhook_analyze_market',
                [
                    'location' => $location,
                    'm2' => $m2,
                    'type' => $type,
                ]
            );

            // Emsal arama: Benzer konum, benzer m2, benzer tip
            // %20 esneme payı ile m2 aralığı
            $m2Min = $m2 * 0.8;
            $m2Max = $m2 * 1.2;

            $emsalIlans = Ilan::query()
                ->where('status', 'Aktif')
                ->whereNull('deleted_at')
                ->where(function ($query) use ($location) {
                    $query->where('ilce_id', 'like', "%{$location}%")
                        ->orWhere('mahalle_id', 'like', "%{$location}%")
                        ->orWhere('tam_adres', 'like', "%{$location}%");
                })
                ->whereBetween('metrekare', [$m2Min, $m2Max])
                ->whereHas('altKategori', function ($query) use ($type) {
                    $query->where('slug', 'like', "%{$type}%");
                })
                ->with(['il:id,il_adi', 'ilce:id,ilce_adi', 'mahalle:id,mahalle_adi'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($ilan) {
                    return [
                        'id' => $ilan->id,
                        'baslik' => $ilan->baslik,
                        'fiyat' => $ilan->fiyat,
                        'para_birimi' => $ilan->para_birimi,
                        'metrekare' => $ilan->metrekare,
                        'location' => [
                            'il' => $ilan->il->il_adi ?? null,
                            'ilce' => $ilan->ilce->ilce_adi ?? null,
                            'mahalle' => $ilan->mahalle->mahalle_adi ?? null,
                        ],
                        'tarih' => $ilan->created_at->format('Y-m-d'),
                        'fiyat_per_m2' => $ilan->metrekare > 0 ? round($ilan->fiyat / $ilan->metrekare, 2) : null,
                    ];
                });

            return ResponseService::success([
                'emsal_count' => $emsalIlans->count(),
                'emsal_ilans' => $emsalIlans,
                'search_criteria' => [
                    'location' => $location,
                    'm2' => $m2,
                    'm2_range' => [$m2Min, $m2Max],
                    'type' => $type,
                ],
            ], 'Emsal arama başarıyla tamamlandı');
        } catch (\Exception $e) {
            LogService::error(
                'n8n webhook: Emsal arama hatası',
                [
                    'error' => $e->getMessage(),
                    'request' => $request->all(),
                ],
                $e,
                LogService::CHANNEL_API
            );

            return ResponseService::serverError('Emsal arama sırasında hata oluştu', $e);
        }
    }

    /**
     * Taslak İlan Oluştur (Telegram'dan gelen ham metin)
     *
     * n8n'den gelen Telegram mesajını taslak ilan olarak kaydeder.
     *
     * POST /api/v1/webhook/n8n/create-draft-listing
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function createDraftListing(Request $request)
    {
        try {
            $validated = Validator::make($request->all(), [
                'text' => 'required|string|min:10',
                'danisman_id' => 'nullable|integer|exists:users,id',
                'source' => 'nullable|string|in:telegram,whatsapp,instagram,email',
            ]);

            if ($validated->fails()) {
                return ResponseService::validationError($validated->errors()->toArray());
            }

            $text = $request->input('text');
            $danismanId = $request->input('danisman_id', auth()->id());
            $source = $request->input('source', 'telegram');

            LogService::api(
                'n8n_webhook_create_draft_listing',
                [
                    'danisman_id' => $danismanId,
                    'source' => $source,
                    'text_length' => strlen($text),
                ]
            );

            // AIIlanTaslagiService kullanarak taslak oluştur
            $taslak = $this->ilanTaslagiService->generateDraft([
                'raw_text' => $text,
                'source' => $source,
                'extracted_data' => [], // n8n'den gelecek parse edilmiş veri
            ], $danismanId);

            return ResponseService::success([
                'taslak_id' => $taslak->id,
                'status' => $taslak->status,
                'message' => 'Taslak ilan başarıyla oluşturuldu',
            ], 'Taslak ilan başarıyla oluşturuldu');
        } catch (\Exception $e) {
            LogService::error(
                'n8n webhook: Taslak ilan oluşturma hatası',
                [
                    'error' => $e->getMessage(),
                    'request' => $request->all(),
                ],
                $e,
                LogService::CHANNEL_API
            );

            return ResponseService::serverError('Taslak ilan oluşturulamadı', $e);
        }
    }

    /**
     * Tersine Eşleştirme Tetikle (Reverse Match Trigger)
     *
     * n8n'den gelen ilan_id ile tersine eşleştirme işlemini manuel tetikler.
     *
     * POST /api/v1/webhook/n8n/trigger-reverse-match
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function triggerReverseMatch(Request $request)
    {
        try {
            $validated = Validator::make($request->all(), [
                'ilan_id' => 'required|integer|exists:ilanlar,id',
            ]);

            if ($validated->fails()) {
                return ResponseService::validationError($validated->errors()->toArray());
            }

            $ilanId = $request->input('ilan_id');

            LogService::api(
                'n8n_webhook_trigger_reverse_match',
                [
                    'ilan_id' => $ilanId,
                ]
            );

            // İlan'ı bul
            $ilan = Ilan::findOrFail($ilanId);

            // Sadece 'Aktif' ilanlar için event fire et
            if ($ilan->status !== 'Aktif') {
                return ResponseService::error(
                    'Sadece aktif ilanlar için tersine eşleştirme yapılabilir',
                    400,
                    [],
                    'INVALID_STATUS'
                );
            }

            // IlanCreated event'ini manuel tetikle
            event(new IlanCreated($ilan));

            return ResponseService::success([
                'ilan_id' => $ilan->id,
                'ilan_baslik' => $ilan->baslik,
                'status' => $ilan->status,
                'message' => 'Tersine eşleştirme işlemi tetiklendi. Queue\'da işlenecek.',
            ], 'Tersine eşleştirme başarıyla tetiklendi');
        } catch (\Exception $e) {
            LogService::error(
                'n8n webhook: Tersine eşleştirme tetikleme hatası',
                [
                    'error' => $e->getMessage(),
                    'request' => $request->all(),
                ],
                $e,
                LogService::CHANNEL_API
            );

            return ResponseService::serverError('Tersine eşleştirme tetiklenemedi', $e);
        }
    }
}
