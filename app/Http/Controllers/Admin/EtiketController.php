<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Etiket;
use Illuminate\Support\Str;

class EtiketController extends AdminController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // ✅ N+1 FIX: Eager loading ekle
        $query = Etiket::withCount('kisiler')->latest();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('color', 'LIKE', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $etiketler = $query->paginate(15);

        if ($request->expectsJson()) {
            return response()->json($etiketler);
        }

        return view('admin.etiket.index', compact('etiketler'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.etiket.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:etiketler,name',
            'description' => 'nullable|string|max:500',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive'
        ]);

        $data = $request->all();

        // Auto-generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $etiket = Etiket::create($data);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Etiket created successfully',
                'data' => $etiket
            ], 201);
        }

        return redirect()
            ->route('admin.etiket.index')
            ->with('success', 'Etiket created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Etiket $etiket)
    {
        $etiket->load('kisiler');

        if (request()->expectsJson()) {
            return response()->json($etiket);
        }

        return view('admin.etiket.show', compact('etiket'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Etiket $etiket)
    {
        return view('admin.etiket.edit', compact('etiket'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Etiket $etiket)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:etiketler,name,' . $etiket->id,
            'description' => 'nullable|string|max:500',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive'
        ]);

        $data = $request->all();

        // Auto-generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $etiket->update($data);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Etiket updated successfully',
                'data' => $etiket->fresh()
            ]);
        }

        return redirect()
            ->route('admin.etiket.index')
            ->with('success', 'Etiket updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Etiket $etiket)
    {
        // Check if etiket is being used by any kisiler
        if ($etiket->kisiler()->count() > 0) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete etiket that is being used by kisiler'
                ], 422);
            }

            return redirect()
                ->route('admin.etiket.index')
                ->with('error', 'Cannot delete etiket that is being used by kisiler');
        }

        $etiket->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Etiket deleted successfully'
            ]);
        }

        return redirect()
            ->route('admin.etiket.index')
            ->with('success', 'Etiket deleted successfully');
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,activate,deactivate',
            'ids' => 'required|array',
            'ids.*' => 'exists:etiketler,id'
        ]);

        $etiketler = Etiket::whereIn('id', $request->ids);

        switch ($request->action) {
            case 'delete':
                $etiketler->delete();
                $message = 'Selected etiketler deleted successfully';
                break;
            case 'activate':
                $etiketler->update(['status' => 'active']);
                $message = 'Selected etiketler activated successfully';
                break;
            case 'deactivate':
                $etiketler->update(['status' => 'inactive']);
                $message = 'Selected etiketler deactivated successfully';
                break;
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        }

        return redirect()
            ->route('admin.etiket.index')
            ->with('success', $message);
    }

    /**
     * Export etiketler
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'excel');
        $etiketler = Etiket::latest()->get();

        switch ($format) {
            case 'excel':
                return $this->exportToExcel($etiketler);
            case 'csv':
                return $this->exportToCsv($etiketler);
            case 'json':
                return $this->exportToJson($etiketler);
            default:
                return response()->json(['error' => 'Unsupported format'], 400);
        }
    }

    /**
     * Export to Excel
     */
    private function exportToExcel($etiketler)
    {
        // Mock implementation
        return response()->json([
            'success' => true,
            'message' => 'Excel export generated',
            'download_url' => '/exports/etiketler-' . date('Y-m-d') . '.xlsx'
        ]);
    }

    /**
     * Export to CSV
     */
    private function exportToCsv($etiketler)
    {
        // Mock implementation
        return response()->json([
            'success' => true,
            'message' => 'CSV export generated',
            'download_url' => '/exports/etiketler-' . date('Y-m-d') . '.csv'
        ]);
    }

    /**
     * Export to JSON
     */
    private function exportToJson($etiketler)
    {
        return response()->json($etiketler);
    }
}
