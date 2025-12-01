<?php

namespace App\Http\Controllers\Admin;

use App\Models\FeatureCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FeatureCategoryController extends AdminController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // ✅ N+1 FIX: Eager loading with select optimization
        $categories = FeatureCategory::select(['id', 'name', 'slug', 'description', 'icon', 'display_order', 'status', 'created_at', 'updated_at'])
            ->with(['features:id,name,feature_category_id,status'])
            ->orderBy('display_order')
            ->paginate(15);

        if ($request->expectsJson()) {
            return response()->json($categories);
        }

        // ✅ Context7: View adı düzeltildi (admin.feature-categories → admin.ozellikler.kategoriler)
        return view('admin.ozellikler.kategoriler.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // ✅ Context7: View adı düzeltildi (admin.feature-categories → admin.ozellikler.kategoriler)
        return view('admin.ozellikler.kategoriler.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:feature_categories,slug',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'display_order' => 'nullable|integer|min:0', // ✅ Context7: order → display_order
            'status' => 'required|boolean', // ✅ Context7: boolean status (active/inactive değil!)
            'applies_to' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'seo_keywords' => 'nullable|string|max:500',
        ]);

        $data = $request->all();

        // Auto-generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Set display_order if not provided
        if (empty($data['display_order'])) {
            $data['display_order'] = FeatureCategory::max('display_order') + 1;
        }

        $category = FeatureCategory::create($data);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Feature category created successfully',
                'data' => $category,
            ], 201);
        }

        return redirect()
            ->route('admin.ozellikler.kategoriler.index')
            ->with('success', 'Feature category created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(FeatureCategory $featureCategory)
    {
        $featureCategory->load('features', 'translations');

        if (request()->expectsJson()) {
            return response()->json($featureCategory);
        }

        // ✅ Context7: View adı düzeltildi (admin.feature-categories → admin.ozellikler.kategoriler)
        return view('admin.ozellikler.kategoriler.show', compact('featureCategory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FeatureCategory $featureCategory)
    {
        // ✅ Context7: View adı düzeltildi (admin.feature-categories → admin.ozellikler.kategoriler)
        return view('admin.ozellikler.kategoriler.edit', compact('featureCategory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FeatureCategory $featureCategory)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:feature_categories,slug,'.$featureCategory->id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'display_order' => 'nullable|integer|min:0', // ✅ Context7: order → display_order
            'status' => 'required|boolean', // ✅ Context7: boolean status (active/inactive değil!)
            'applies_to' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'seo_keywords' => 'nullable|string|max:500',
        ]);

        $data = $request->all();

        // Auto-generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $featureCategory->update($data);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Feature category updated successfully',
                'data' => $featureCategory->fresh(),
            ]);
        }

        return redirect()
            ->route('admin.ozellikler.kategoriler.index')
            ->with('success', 'Feature category updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FeatureCategory $featureCategory)
    {
        // ✅ PERFORMANCE FIX: N+1 query önlendi - withCount() kullan
        $featureCategory->loadCount('features');

        // Check if category has features
        if ($featureCategory->features_count > 0) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete category with existing features',
                ], 422);
            }

            return redirect()
                ->route('admin.ozellikler.kategoriler.index')
                ->with('error', 'Cannot delete category with existing features');
        }

        $featureCategory->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Feature category deleted successfully',
            ]);
        }

        return redirect()
            ->route('admin.ozellikler.kategoriler.index')
            ->with('success', 'Feature category deleted successfully');
    }
}
