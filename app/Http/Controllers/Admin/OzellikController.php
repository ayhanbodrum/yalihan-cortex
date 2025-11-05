<?php

namespace App\Http\Controllers\Admin;

use App\Models\Feature;
use App\Models\FeatureCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class OzellikController extends AdminController
{
    public function index(Request $request)
    {
        // PHASE 2.2: Tab-based UI - Collect all data
        
        // Tab 1: Tüm Özellikler
        // Context7: feature_category_id kullanılmalı (category_id yok)
        $query = Feature::with('category')->orderBy('order')->orderBy('name');
        if ($request->has('category_id') && $request->category_id) {
            $query->where('feature_category_id', $request->category_id);
        }
        if ($request->has('enabled') && $request->enabled !== '') {
            $query->where('enabled', $request->enabled == '1' ? true : false);
        }
        $ozellikler = $query->paginate(20, ['*'], 'ozellikler_page');

        // Tab 2: Kategoriler
        $kategoriQuery = FeatureCategory::withCount('features')->orderBy('order')->orderBy('name');
        if ($request->filled('kategori_search')) {
            $q = $request->get('kategori_search');
            $kategoriQuery->where(function($sub) use ($q) {
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
        // Context7: Schema kontrolü ile enabled/status ve feature_category_id
        $enabledColumn = Schema::hasColumn('features', 'status') ? 'status' : 'enabled';
        $istatistikler = [
            'toplam' => Feature::count(),
            'aktif' => Feature::where($enabledColumn, true)->count(),
            'pasif' => Feature::where($enabledColumn, false)->count(),
            'kategorisiz' => Feature::whereNull('feature_category_id')->count(),
            'kategori_sayisi' => FeatureCategory::count(),
        ];

        $kategoriler = FeatureCategory::orderBy('name')->get();

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
        $kategoriler = FeatureCategory::orderBy('name')->get();
        return view('admin.ozellikler.create', compact('kategoriler'));
    }

    public function store(Request $request)
    {
        // ✅ POLYMORPHIC: Updated field names
        // Context7: feature_category_id kullanılmalı (category_id yok)
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'feature_category_id' => 'nullable|exists:feature_categories,id',
            'field_type' => 'required|in:text,number,boolean,select,checkbox,radio,textarea',
            'enabled' => 'required|boolean',
            'order' => 'nullable|integer',
        ]);
        
        // Request'ten category_id gelirse feature_category_id'ye map et
        if ($request->has('category_id') && !$request->has('feature_category_id')) {
            $validated['feature_category_id'] = $request->category_id;
        }

        Feature::create($validated);

        return redirect()->route('admin.ozellikler.index')
            ->with('success', 'Özellik başarıyla oluşturuldu.');
    }

    public function edit($id)
    {
        $ozellik = Feature::findOrFail($id);
        $kategoriler = FeatureCategory::orderBy('name')->get();

        return view('admin.ozellikler.edit', compact('ozellik', 'kategoriler'));
    }

    public function update(Request $request, $id)
    {
        $ozellik = Feature::findOrFail($id);

        // ✅ POLYMORPHIC: Updated field names
        // Context7: feature_category_id kullanılmalı (category_id yok)
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'feature_category_id' => 'nullable|exists:feature_categories,id',
            'field_type' => 'required|in:text,number,boolean,select,checkbox,radio,textarea',
            'enabled' => 'required|boolean',
            'order' => 'nullable|integer',
        ]);

        $ozellik->update($validated);

        return redirect()->route('admin.ozellikler.edit', $ozellik->id)
            ->with('success', $ozellik->name . ' başarıyla güncellendi! ✅');
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
            'ids.*' => 'exists:features,id'
        ]);

        $action = $validated['action'];
        $ids = $validated['ids'];
        $count = count($ids);

        switch ($action) {
            case 'activate':
                // ✅ POLYMORPHIC: enabled field
                Feature::whereIn('id', $ids)->update(['enabled' => true]);
                $message = "{$count} özellik başarıyla aktif edildi! ✅";
                break;

            case 'deactivate':
                // ✅ POLYMORPHIC: enabled field
                Feature::whereIn('id', $ids)->update(['enabled' => false]);
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
