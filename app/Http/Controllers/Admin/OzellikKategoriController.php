<?php

namespace App\Http\Controllers\Admin;

use App\Models\Feature;
use App\Models\FeatureCategory;
use App\Models\OzellikKategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class OzellikKategoriController extends AdminController
{
    public function index(Request $request)
    {
        $query = FeatureCategory::query();
        if ($request->filled('q')) {
            $q = (string) $request->get('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('slug', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }
        // Context7: Schema kontrolü (status vs enabled)
        if ($request->filled('status')) {
            $status = (bool) $request->input('status');
            // ✅ Context7: ONLY status field (enabled FORBIDDEN)
            if (Schema::hasColumn('feature_categories', 'status')) {
                $query->where('status', $status);
            }
            // ❌ REMOVED: enabled field fallback (Context7 violation)
        }

        $kategoriler = $query->withCount('features')->orderBy('display_order')->orderBy('name')->paginate(20)->withQueryString();

        // ✅ Context7: View için gerekli değişkenler
        $status = $request->get('status'); // Filter için

        return view('admin.ozellikler.kategoriler.index', compact('kategoriler', 'status'));
    }

    public function create()
    {
        return view('admin.ozellikler.kategoriler.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('feature_categories', 'slug')],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:64'],
            'applies_to' => ['nullable', 'string'],
            'status' => ['required', 'boolean'],
            'display_order' => ['nullable', 'integer'], // ✅ Context7: order → display_order
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        if (! array_key_exists('display_order', $data) || is_null($data['display_order'])) {
            $data['display_order'] = (int) (FeatureCategory::max('display_order') + 1);
        }

        FeatureCategory::create($data);

        return redirect()->route('admin.ozellikler.kategoriler.index')->with('success', 'Kategori oluşturuldu.');
    }

    public function show(int $id)
    {
        $kategori = FeatureCategory::with('features')->findOrFail($id);

        return view('admin.ozellikler.kategoriler.ozellikler', compact('kategori'));
    }

    public function edit(int $id)
    {
        $kategori = FeatureCategory::with('features')->findOrFail($id);

        return view('admin.ozellikler.kategoriler.edit', compact('kategori'));
    }

    public function update(Request $request, int $id)
    {
        $kategori = FeatureCategory::findOrFail($id);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('feature_categories', 'slug')->ignore($kategori->id)],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:64'],
            'applies_to' => ['nullable', 'string'],
            'renk_kodu' => ['nullable', 'string', 'max:16'],
            'veri_tipi' => ['nullable', 'string', 'max:32'],
            'veri_secenekleri' => ['nullable', 'array'],
            'birim' => ['nullable', 'string', 'max:32'],
            'zorunlu' => ['nullable', 'boolean'],
            'arama_filtresi' => ['nullable', 'boolean'],
            'ilan_kartinda_goster' => ['nullable', 'boolean'],
            'detay_sayfasinda_goster' => ['nullable', 'boolean'],
            'uyumlu_emlak_turleri' => ['nullable', 'array'],
            'uyumlu_kategoriler' => ['nullable', 'array'],
            'validasyon_kurallari' => ['nullable', 'array'],
            'varsayilan_deger' => ['nullable'],
            'status' => ['required', 'boolean'],
            'display_order' => ['nullable', 'integer'], // Context7: order → display_order
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
        ]);

        // ✅ Context7 Fix: applies_to STRING → JSON array conversion
        if (! empty($data['applies_to'])) {
            if (is_string($data['applies_to'])) {
                // "konut,arsa" → ["konut", "arsa"]
                $applies = explode(',', $data['applies_to']);
                $data['applies_to'] = json_encode(array_map('trim', $applies));
            }
        } else {
            $data['applies_to'] = null;
        }

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $kategori->update($data);

        return redirect()->route('admin.ozellikler.kategoriler.index')->with('success', 'Kategori güncellendi.');
    }

    public function destroy(int $id)
    {
        $kategori = FeatureCategory::findOrFail($id);
        $kategori->delete();

        return redirect()->route('admin.ozellikler.kategoriler.index')->with('success', 'Kategori silindi.');
    }

    public function kategorisizOzellikler()
    {
        $ozellikler = Feature::whereNull('feature_category_id')->orderBy('name')->paginate(50);

        return view('admin.ozellikler.kategoriler.kategorisiz-ozellikler', compact('ozellikler'));
    }

    public function toggleStatus(int $kategori)
    {
        $model = FeatureCategory::findOrFail($kategori);
        $model->status = ! $model->status;
        $model->save();

        return response()->json(['success' => true, 'status' => $model->status]);
    }

    public function reorder(Request $request)
    {
        $data = $request->validate([
            'items' => ['required', 'array'],
            'items.*.id' => ['required', 'integer', 'exists:feature_categories,id'],
            'items.*.display_order' => ['required', 'integer'],
        ]);
        // ✅ PERFORMANCE FIX: N+1 query önlendi - Gerçek bulk update kullanıldı
        DB::transaction(function () use ($data) {
            $updates = [];
            $ids = [];
            foreach ($data['items'] as $item) {
                $displayOrder = $item['display_order'] ?? 0;
                $id = $item['id'];
                $updates[$id] = $displayOrder;
                $ids[] = $id;
            }

            // ✅ PERFORMANCE FIX: CASE WHEN ile gerçek bulk update (N query → 1 query)
            if (! empty($ids)) {
                $cases = [];
                $bindings = [];
                foreach ($updates as $id => $displayOrder) {
                    $cases[] = 'WHEN ? THEN ?';
                    $bindings[] = $id;
                    $bindings[] = $displayOrder;
                }
                $idsPlaceholder = implode(',', array_fill(0, count($ids), '?'));
                $casesSql = implode(' ', $cases);

                DB::statement(
                    "UPDATE feature_categories
                     SET display_order = CASE id {$casesSql} END
                     WHERE id IN ({$idsPlaceholder})",
                    array_merge($bindings, $ids)
                );
            }
        });

        return response()->json(['success' => true]);
    }

    public function checkSlug(Request $request)
    {
        $slug = (string) $request->get('slug');
        $excludeId = $request->integer('exclude_id');
        $query = OzellikKategori::where('slug', $slug);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        $exists = $query->exists();

        return response()->json(['unique' => ! $exists]);
    }

    public function ozellikler(int $id)
    {
        $kategori = FeatureCategory::with('features')->findOrFail($id);
        $ozellikler = $kategori->features()->orderBy('display_order')->orderBy('name')->paginate(20);

        return view('admin.ozellikler.kategoriler.ozellikler', compact('kategori', 'ozellikler'));
    }

    public function quickUpdate(Request $request, int $id)
    {
        $kategori = OzellikKategori::findOrFail($id);
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'status' => ['sometimes', 'boolean'],
            'display_order' => ['sometimes', 'integer'], // Context7: order → display_order
        ]);
        $kategori->update($data);

        return response()->json(['success' => true]);
    }

    public function duplicate(int $id)
    {
        $kategori = OzellikKategori::findOrFail($id);
        $yeni = $kategori->replicate();
        $yeni->name = $kategori->name.' Kopya';
        $yeni->slug = null;
        $yeni->display_order = (int) (OzellikKategori::max('display_order') + 1);
        $yeni->save();

        return response()->json(['success' => true, 'id' => $yeni->id]);
    }

    public function bulkToggleStatus(Request $request)
    {
        $data = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:ozellik_kategorileri,id'],
            'status' => ['required', 'boolean'],
        ]);
        OzellikKategori::whereIn('id', $data['ids'])->update(['status' => $data['status']]);

        return response()->json(['success' => true]);
    }

    public function bulkDelete(Request $request)
    {
        $data = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:ozellik_kategorileri,id'],
        ]);
        OzellikKategori::whereIn('id', $data['ids'])->delete();

        return response()->json(['success' => true]);
    }

    public function stats()
    {
        $toplam = OzellikKategori::count();
        $active = OzellikKategori::where('status', true)->count();
        $pasif = OzellikKategori::where('status', false)->count();

        return response()->json(compact('toplam', 'active', 'pasif'));
    }
}
