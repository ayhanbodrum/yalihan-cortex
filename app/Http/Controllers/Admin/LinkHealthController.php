<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route as RouteFacade;
use Illuminate\Routing\Controller as BaseController;

class LinkHealthController extends BaseController
{
    public function index(Request $request)
    {
        $targets = [
            ['name' => 'Dashboard', 'route' => 'admin.dashboard.index'],
            ['name' => 'Tüm İlanlar', 'route' => 'admin.ilanlar.index'],
            ['name' => 'Kişiler', 'route' => 'admin.kisiler.index'],
            ['name' => 'Adres Yönetimi', 'route' => 'admin.adres-yonetimi.index'],
            ['name' => 'Analytics', 'route' => 'admin.analytics.index'],
            ['name' => 'Raporlar', 'route' => 'admin.reports.index'],
        ];

        $base = config('app.url') ?: $request->getSchemeAndHttpHost();
        $results = [];

        foreach ($targets as $t) {
            $url = RouteFacade::has($t['route']) ? route($t['route']) : null;
            if (!$url) {
                $results[] = [
                    'name' => $t['name'],
                    'route' => $t['route'],
                    'url' => null,
                    'ok' => false,
                    'status' => null,
                    'error' => 'Route not found'
                ];
                continue;
            }
            try {
                $response = Http::timeout(5)->withHeaders(['Accept' => 'text/html'])->get($url);
                $results[] = [
                    'name' => $t['name'],
                    'route' => $t['route'],
                    'url' => $url,
                    'ok' => $response->successful(),
                    'status' => $response->status(),
                    'error' => $response->successful() ? null : ($response->body() ? 'HTTP Error' : 'Unknown')
                ];
            } catch (\Throwable $e) {
                $results[] = [
                    'name' => $t['name'],
                    'route' => $t['route'],
                    'url' => $url,
                    'ok' => false,
                    'status' => null,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return view('admin.reports.link-health', [
            'results' => $results,
            'checked_at' => now()->format('d.m.Y H:i'),
        ]);
    }
}