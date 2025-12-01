<?php

namespace App\Http\Controllers\Admin;

use App\Models\Feature;
use App\Models\FeatureCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class OzellikController extends AdminController
{
    public function index(Request $request)
    {
        // PHASE 2.2: Tab-based UI - Collect all data

        // Tab 1: Tüm Özellikler
        // Context7: feature_category_id kullanılmalı (category_id yok)
        $query = Feature::with('category')->orderBy('display_order')->orderBy('name');
        if ($request->has('category_id') && $request->category_id) {
            $query->where('feature_category_id', $request->category_id);
        }
        // ✅ Context7 FIX: enabled → status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status == '1' ? true : false);
        }
        // Backward compatibility: accept 'enabled' but map to 'status'
        if ($request->has('enabled') && $request->enabled !== '' && ! $request->has('status')) {
            $query->where('status', $request->enabled == '1' ? true : false);
        }
        $ozellikler = $query->paginate(20, ['*'], 'ozellikler_page');

        // Tab 2: Kategoriler
        $kategoriQuery = FeatureCategory::withCount('features')->orderBy('display_order')->orderBy('name');
        if ($request->filled('kategori_search')) {
            $q = $request->get('kategori_search');
            $kategoriQuery->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('slug', 'like', "%{$q}%");
            });
        }
        $kategoriListesi = $kategoriQuery->paginate(20, ['*'], 'kategoriler_page');

        // Tab 3: Kategorisiz Özellikler
        // Context7: feature_category_id kullanılmalı (category_id yok)
        $kategorisizOzellikler = Feature::whereNull('feature_category_id')
            ->orderBy('name')
            ->paginate(20, ['*'], 'kategorisiz_page');

        // İstatistikler
        // ✅ Context7 FIX: ONLY status column (enabled FORBIDDEN)
        $istatistikler = [
            'toplam' => Feature::count(),
            'aktif' => Feature::where('status', true)->count(),
            'pasif' => Feature::where('status', false)->count(),
            'kategorisiz' => Feature::whereNull('feature_category_id')->count(),
            'kategori_sayisi' => FeatureCategory::count(),
        ];

        // ✅ CACHE: Kategoriler dropdown için cache ekle
        $kategoriler = Cache::remember('feature_category_list', 3600, function () {
            return FeatureCategory::select(['id', 'name', 'slug'])
                ->orderBy('name')
                ->get();
        });

        // Active tab (default: ozellikler)
        $activeTab = $request->get('tab', 'ozellikler');

        return view('admin.ozellikler.index', compact(
            'ozellikler',
            'kategoriListesi',
            'kategorisizOzellikler',
            'istatistikler',
            'kategoriler',
            'activeTab'
        ));
    }

    public function create()
    {
        // ✅ CACHE: Kategoriler dropdown için cache ekle
        $kategoriler = Cache::remember('feature_category_list', 3600, function () {
            return FeatureCategory::select(['id', 'name', 'slug'])
                ->orderBy('name')
                ->get();
        });

        return view('admin.ozellikler.create', compact('kategoriler'));
    }

    public function store(Request $request)
    {
        // ✅ POLYMORPHIC: Updated field names
        // Context7: feature_category_id kullanılmalı (category_id yok)
        // ✅ Context7 FIX: enabled → status
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'feature_category_id' => 'nullable|exists:feature_categories,id',
            'field_type' => 'required|in:text,number,boolean,select,checkbox,radio,textarea',
            'status' => 'required|boolean', // Context7: enabled → status
            'display_order' => 'nullable|integer', // Context7: order → display_order
        ]);

        // Request'ten category_id gelirse feature_category_id'ye map et
        if ($request->has('category_id') && ! $request->has('feature_category_id')) {
            $validated['feature_category_id'] = $request->category_id;
        }

        // Backward compatibility: enabled → status mapping
        if ($request->has('enabled') && ! $request->has('status')) {
            $validated['status'] = $request->boolean('enabled');
        }

        Feature::create($validated);

        // ✅ CACHE: Kategori listesi cache'ini temizle
        Cache::forget('feature_category_list');

        return redirect()->route('admin.ozellikler.index')
            ->with('success', 'Özellik başarıyla oluşturuldu.');
    }

    public function edit($id)
    {
        $ozellik = Feature::findOrFail($id);
        // ✅ CACHE: Kategoriler dropdown için cache ekle
        $kategoriler = Cache::remember('feature_category_list', 3600, function () {
            return FeatureCategory::select(['id', 'name', 'slug'])
                ->orderBy('name')
                ->get();
        });

        return view('admin.ozellikler.edit', compact('ozellik', 'kategoriler'));
    }

    public function update(Request $request, $id)
    {
        $ozellik = Feature::findOrFail($id);

        // ✅ Context7: Legacy alanları doğrulama öncesinde normalize et
        if (! $request->filled('type') && $request->filled('field_type')) {
            $request->merge(['type' => $request->input('field_type')]);
        }

        if (! $request->filled('feature_category_id') && $request->filled('category_id')) {
            $request->merge(['feature_category_id' => $request->input('category_id')]);
        }

        // ✅ Context7 FIX: enabled → status, order → display_order
        // Context7: feature_category_id kullanılmalı (category_id yok)
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'feature_category_id' => 'nullable|exists:feature_categories,id',
            'type' => 'required|in:text,number,boolean,select,checkbox,radio,textarea',
            'status' => 'required|boolean', // Context7: enabled → status
            'display_order' => 'nullable|integer', // ✅ Context7: order → display_order
            'is_required' => 'nullable|boolean',
            'description' => 'nullable|string|max:1000',
            'unit' => 'nullable|string|max:50',
            'options' => 'nullable|array',
            'field_options' => 'nullable|array',
            'field_type' => 'nullable|in:text,number,boolean,select,checkbox,radio,textarea',
        ]);

        // Context7: display_order tek kaynak olarak kullanılır

        // ✅ Context7: Backward compatibility - zorunlu → is_required mapping
        if ($request->has('zorunlu') && ! $request->has('is_required')) {
            $validated['is_required'] = $request->boolean('zorunlu');
        }

        // ✅ Context7: Backward compatibility - aciklama → description mapping
        if ($request->has('aciklama') && ! $request->has('description')) {
            $validated['description'] = $request->input('aciklama');
        }

        // ✅ Context7: field_options → options mapping (if needed)
        if ($request->has('field_options')) {
            $validated['options'] = $request->input('field_options');
            unset($validated['field_options']);
        }

        // ✅ Context7: Legacy field_type → type (tek kaynak)
        if (isset($validated['field_type']) && empty($validated['type'])) {
            $validated['type'] = $validated['field_type'];
        }
        unset($validated['field_type']);

        $ozellik->update($validated);

        // ✅ CACHE: Kategori listesi cache'ini temizle
        Cache::forget('feature_category_list');

        return redirect()->route('admin.ozellikler.edit', $ozellik->id)
            ->with('success', $ozellik->name.' başarıyla güncellendi! ✅');
    }

    public function destroy($id)
    {
        $ozellik = Feature::findOrFail($id);
        $ozellik->delete();

        return redirect()->route('admin.ozellikler.index')
            ->with('success', 'Özellik başarıyla silindi.');
    }

    /**
     * Bulk Actions - Toplu işlemler
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:features,id',
        ]);

        $action = $validated['action'];
        $ids = $validated['ids'];
        $count = count($ids);

        switch ($action) {
            case 'activate':
                // ✅ Context7: enabled → status
                Feature::whereIn('id', $ids)->update(['status' => true]);
                $message = "{$count} özellik başarıyla aktif edildi! ✅";
                break;

            case 'deactivate':
                // ✅ Context7: enabled → status
                Feature::whereIn('id', $ids)->update(['status' => false]);
                $message = "{$count} özellik başarıyla pasif edildi! ⏸️";
                break;

            case 'delete':
                Feature::whereIn('id', $ids)->delete();
                $message = "{$count} özellik başarıyla silindi! 🗑️";
                break;

            default:
                $message = 'Geçersiz işlem!';
        }

        return redirect()->back()->with('success', $message);
    }
}
