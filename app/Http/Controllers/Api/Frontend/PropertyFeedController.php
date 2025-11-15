<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use App\Services\Frontend\PropertyFeedService;
use App\Services\Response\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PropertyFeedController extends Controller
{
    public function __construct(private readonly PropertyFeedService $propertyFeedService)
    {
    }

    public function featured(Request $request): JsonResponse
    {
        $limit = (int) $request->query('limit', 6);
        $currency = $request->query('currency');

        $properties = $this->propertyFeedService->getFeatured($limit, $currency);

        return ResponseService::success([
            'data' => $properties,
            'meta' => [
                'count' => $properties->count(),
                'limit' => $limit,
            ],
        ], 'Öne çıkan ilanlar başarıyla getirildi');
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 12);
        $currency = $request->query('currency');

        $filters = [
            'category' => $request->query('category'),
            'district' => $request->query('district'),
            'neighborhood' => $request->query('neighborhood'),
            'min_price' => $request->query('min_price'),
            'max_price' => $request->query('max_price'),
        ];

        $paginator = $this->propertyFeedService->paginate($filters, $perPage, $currency);

        return ResponseService::success([
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'last_page' => $paginator->lastPage(),
                'total' => $paginator->total(),
                'has_more' => $paginator->hasMorePages(),
            ],
        ], 'İlanlar başarıyla getirildi');
    }

    public function show(int $propertyId, Request $request): JsonResponse
    {
        $currency = $request->query('currency');

        $property = $this->propertyFeedService->find($propertyId, $currency);

        if (!$property) {
            return ResponseService::notFound('İlan bulunamadı');
        }

        return ResponseService::success([
            'data' => $property,
        ], 'İlan detayları başarıyla getirildi');
    }
}
