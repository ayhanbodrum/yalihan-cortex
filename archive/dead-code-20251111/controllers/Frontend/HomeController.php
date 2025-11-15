<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Ilan;
use App\Services\Frontend\PropertyFeedService;

class HomeController extends Controller
{
    public function __construct(private readonly PropertyFeedService $propertyFeedService)
    {
    }

    public function __invoke()
    {
        $featuredProperties = $this->propertyFeedService->getFeatured(6);

        $stats = [
            'active_listings' => Ilan::where('status', 'Aktif')->count(),
            'experience_years' => 20,
            'happy_customers' => Ilan::count() * 2 + 500,
        ];

        return view('yaliihan-home-clean', [
            'featuredProperties' => $featuredProperties,
            'stats' => $stats,
        ]);
    }
}
