<?php

namespace App\Services\Ilan;

use App\Models\Ilan;
use Illuminate\Http\Request;

class IlanExportService
{
    public function getExportQuery(Request $request)
    {
        $query = Ilan::with(['ilanSahibi', 'userDanisman', 'kategori', 'il', 'ilce']);

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('baslik', 'like', "%{$search}%")
                  ->orWhere('aciklama', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('kategori')) {
            $query->where('kategori_id', $request->get('kategori'));
        }

        if ($request->filled('min_fiyat')) {
            $query->where('fiyat', '>=', $request->get('min_fiyat'));
        }

        if ($request->filled('max_fiyat')) {
            $query->where('fiyat', '<=', $request->get('max_fiyat'));
        }

        if ($request->filled('il_id')) {
            $query->where('il_id', $request->get('il_id'));
        }

        if ($request->filled('ilce_id')) {
            $query->where('ilce_id', $request->get('ilce_id'));
        }

        return $query;
    }
}


