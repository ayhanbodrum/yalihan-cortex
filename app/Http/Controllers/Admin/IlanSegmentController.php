<?php

namespace App\Http\Controllers\Admin;

use App\Enums\IlanSegment;
use App\Models\Ilan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * İlan Segment Yönetimi Controller
 * Context7: Sequential workflow management for property listings
 */
class IlanSegmentController extends AdminController
{
    /**
     * Yeni ilan oluşturma başlangıcı
     */
    public function create(Request $request)
    {
        return $this->show($request, null, null);
    }

    /**
     * Yeni ilan segment görüntüleme
     */
    public function showCreate(Request $request, $segment)
    {
        return $this->show($request, null, $segment);
    }

    /**
     * Yeni ilan segment kaydetme
     */
    public function storeCreate(Request $request, $segment)
    {
        return $this->store($request, null, $segment);
    }

    /**
     * Mevcut ilan segment düzenleme
     */
    public function showEdit(Request $request, $ilanId, $segment)
    {
        return $this->show($request, $ilanId, $segment);
    }

    /**
     * Segment tabanlı ilan oluşturma/düzenleme
     */
    public function show(Request $request, $ilanId = null, $segment = null)
    {
        try {
            $ilan = $ilanId ? Ilan::findOrFail($ilanId) : new Ilan();

            // Varsayılan segment
            if (!$segment) {
                $segment = IlanSegment::PORTFOLIO_INFO;
            } else {
                $segment = IlanSegment::from($segment);
            }

            // Segment sıralaması
            $segments = IlanSegment::getOrder();
            $currentIndex = array_search($segment, $segments);

            // İlerleme durumu
            $progress = $this->calculateProgress($ilan, $segment);

            return view('admin.ilanlar.segments.show', [
                'ilan' => $ilan,
                'currentSegment' => $segment,
                'segments' => $segments,
                'currentIndex' => $currentIndex,
                'progress' => $progress,
                'segmentData' => $this->getSegmentData($ilan, $segment),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Segment verilerini kaydet
     */
    public function store(Request $request, $ilanId = null, $segment = null)
    {
        $segment = IlanSegment::from($segment);

        // Segment'e özel validasyon
        $validator = $this->getSegmentValidator($segment, $request);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // İlan oluştur veya güncelle
        $ilan = $ilanId ? Ilan::findOrFail($ilanId) : new Ilan();

        // Segment verilerini işle
        $this->processSegmentData($ilan, $segment, $request);

        $ilan->save();

        // Sonraki segment'e yönlendir
        $nextSegment = $segment->getNext();

        if ($nextSegment) {
            if ($ilan->id) {
                return redirect()->route('admin.ilanlar.segments.show', [
                    'ilan' => $ilan->id,
                    'segment' => $nextSegment->value
                ])->with('success', 'Segment kaydedildi. Sonraki adıma geçiliyor...');
            } else {
                return redirect()->route('admin.ilanlar.segments.create', [
                    'segment' => $nextSegment->value
                ])->with('success', 'Segment kaydedildi. Sonraki adıma geçiliyor...');
            }
        }

        // Tüm segmentler tamamlandı
        return redirect()->route('admin.ilanlar.show', $ilan->id)
            ->with('success', 'İlan başarıyla oluşturuldu!');
    }

    /**
     * Segment'e özel validasyon
     */
    private function getSegmentValidator(IlanSegment $segment, Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $rules = [];

        switch ($segment) {
            case IlanSegment::PORTFOLIO_INFO:
                $rules = [
                    'baslik' => 'required|string|max:255',
                    'fiyat' => 'required|numeric|min:0',
                    'para_birimi' => 'required|string|in:TRY,USD,EUR',
                    'emlak_turu' => 'required|string|in:konut,ticari,arsa',
                    'ilan_turu' => 'required|string|in:satilik,kiralik',
                    'brut_metrekare' => 'required|numeric|min:0',
                    'ada_no' => 'nullable|string',
                    'parsel_no' => 'nullable|string',
                ];
                break;

            case IlanSegment::DOCUMENTS_NOTES:
                $rules = [
                    'documents' => 'nullable|array',
                    'documents.*' => 'file|mimes:pdf,jpg,jpeg,png|max:10240',
                    'notes' => 'nullable|string|max:1000',
                ];
                break;

            case IlanSegment::PORTAL_LISTING:
                $rules = [
                    'portal_descriptions' => 'nullable|array',
                    'portal_descriptions.*' => 'string|max:2000',
                    'portal_sync' => 'nullable|boolean',
                ];
                break;

            case IlanSegment::SUITABLE_BUYERS:
                $rules = [
                    'buyer_ids' => 'nullable|array',
                    'buyer_ids.*' => 'exists:users,id',
                ];
                break;

            case IlanSegment::TRANSACTION_CLOSURE:
                $rules = [
                    'transaction_type' => 'nullable|string|in:sold,rented,cancelled',
                    'transaction_date' => 'nullable|date',
                    'final_price' => 'nullable|numeric|min:0',
                ];
                break;
        }

        return Validator::make($request->all(), $rules);
    }

    /**
     * Segment verilerini işle
     */
    private function processSegmentData(Ilan $ilan, IlanSegment $segment, Request $request): void
    {
        switch ($segment) {
            case IlanSegment::PORTFOLIO_INFO:
                $ilan->fill($request->only([
                    'baslik',
                    'fiyat',
                    'para_birimi',
                    'emlak_turu',
                    'ilan_turu',
                    'brut_metrekare',
                    'ada_no',
                    'parsel_no'
                ]));
                $ilan->status = 'draft';
                break;

            case IlanSegment::DOCUMENTS_NOTES:
                // Döküman yükleme işlemi
                if ($request->hasFile('documents')) {
                    $this->uploadDocuments($ilan, $request->file('documents'));
                }
                $ilan->notes = $request->input('notes');
                break;

            case IlanSegment::PORTAL_LISTING:
                $ilan->portal_descriptions = $request->input('portal_descriptions');
                $ilan->portal_sync_enabled = $request->boolean('portal_sync');
                break;

            case IlanSegment::SUITABLE_BUYERS:
                $ilan->suitable_buyers = $request->input('buyer_ids', []);
                break;

            case IlanSegment::TRANSACTION_CLOSURE:
                $ilan->transaction_type = $request->input('transaction_type');
                $ilan->transaction_date = $request->input('transaction_date');
                $ilan->final_price = $request->input('final_price');
                $ilan->status = 'completed';
                break;
        }
    }

    /**
     * İlerleme durumunu hesapla
     */
    private function calculateProgress(Ilan $ilan, IlanSegment $currentSegment): array
    {
        $segments = IlanSegment::getOrder();
        $progress = [];

        foreach ($segments as $segment) {
            $isCompleted = $this->isSegmentCompleted($ilan, $segment);
            $isCurrent = $segment === $currentSegment;
            $isAccessible = $this->isSegmentAccessible($ilan, $segment);

            $progress[$segment->value] = [
                'completed' => $isCompleted,
                'current' => $isCurrent,
                'accessible' => $isAccessible,
                'title' => $segment->getTitle(),
                'icon' => $segment->getIcon(),
            ];
        }

        return $progress;
    }

    /**
     * Segment tamamlanmış mı?
     */
    private function isSegmentCompleted(Ilan $ilan, IlanSegment $segment): bool
    {
        if ($ilan->id === null) {
            return false;
        }

        return match ($segment) {
            IlanSegment::PORTFOLIO_INFO => !empty($ilan->baslik) && !empty($ilan->fiyat),
            IlanSegment::DOCUMENTS_NOTES => !empty($ilan->notes) || $ilan->documents()->exists(),
            IlanSegment::PORTAL_LISTING => !empty($ilan->portal_descriptions),
            IlanSegment::SUITABLE_BUYERS => !empty($ilan->suitable_buyers),
            IlanSegment::TRANSACTION_CLOSURE => !empty($ilan->transaction_type),
        };
    }

    /**
     * Segment erişilebilir mi?
     */
    private function isSegmentAccessible(Ilan $ilan, IlanSegment $segment): bool
    {
        // İlk segment her zaman erişilebilir
        if ($segment === IlanSegment::PORTFOLIO_INFO) {
            return true;
        }

        // Önceki segment tamamlanmış olmalı
        $previousSegment = $segment->getPrevious();
        if ($previousSegment) {
            return $this->isSegmentCompleted($ilan, $previousSegment);
        }

        return false;
    }

    /**
     * Segment verilerini getir
     */
    private function getSegmentData(Ilan $ilan, IlanSegment $segment): array
    {
        return match ($segment) {
            IlanSegment::PORTFOLIO_INFO => [
                'baslik' => $ilan->baslik,
                'fiyat' => $ilan->fiyat,
                'para_birimi' => $ilan->para_birimi,
                'emlak_turu' => $ilan->emlak_turu,
                'ilan_turu' => $ilan->ilan_turu,
                'brut_metrekare' => $ilan->brut_metrekare,
                'ada_no' => $ilan->ada_no,
                'parsel_no' => $ilan->parsel_no,
            ],
            IlanSegment::DOCUMENTS_NOTES => [
                'notes' => $ilan->notes,
                'documents' => $ilan->documents ?? [],
            ],
            IlanSegment::PORTAL_LISTING => [
                'portal_descriptions' => $ilan->portal_descriptions ?? [],
                'portal_sync' => $ilan->portal_sync_enabled ?? false,
            ],
            IlanSegment::SUITABLE_BUYERS => [
                'buyer_ids' => $ilan->suitable_buyers ?? [],
            ],
            IlanSegment::TRANSACTION_CLOSURE => [
                'transaction_type' => $ilan->transaction_type,
                'transaction_date' => $ilan->transaction_date,
                'final_price' => $ilan->final_price,
            ],
        };
    }

    /**
     * Döküman yükleme
     */
    private function uploadDocuments(Ilan $ilan, array $files): void
    {
        foreach ($files as $file) {
            $path = $file->store('ilan-documents/' . $ilan->id, 'public');

            $ilan->documents()->create([
                'filename' => $file->getClientOriginalName(),
                'path' => $path,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
            ]);
        }
    }
}
