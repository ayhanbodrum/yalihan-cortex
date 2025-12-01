<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ilan;
use App\Models\Photo;
use App\Services\Response\ResponseService;
use App\Traits\ValidatesApiRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Photo Management API Controller
 * Pure API endpoints for photo upload/delete/reorder
 * Context7 compliant!
 */
class PhotoController extends Controller
{
    use ValidatesApiRequests;

    /**
     * Upload photo (single or multiple)
     * POST /api/admin/photos/upload
     */
    public function upload(Request $request)
    {
        // ✅ REFACTORED: Using ValidatesApiRequests trait
        $validated = $this->validateRequestWithResponse($request, [
            'photo' => 'required|image|mimes:jpeg,jpg,png,webp|max:10240', // 10 MB
            'ilan_id' => 'required|exists:ilanlar,id',
            'display_order' => 'nullable|integer',
            'is_featured' => 'nullable|boolean',
        ]);

        if ($validated instanceof \Illuminate\Http\JsonResponse) {
            return $validated;
        }

        $ilan = Ilan::findOrFail($request->ilan_id);
        $file = $request->file('photo');

        // Generate unique filename
        $filename = Str::random(40).'.'.$file->getClientOriginalExtension();

        // Upload paths
        $uploadPath = 'ilanlar/'.$ilan->id.'/photos';

        // Store original
        $path = $file->storeAs($uploadPath, $filename, 'public');

        // Create photo record
        $photo = Photo::create([
            'ilan_id' => $ilan->id,
            'dosya_yolu' => $path, // ✅ Context7: Tablodaki gerçek kolon adı
            'dosya_adi' => $filename,
            'dosya_boyutu' => (string) $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'kapak_fotografi' => $request->is_featured ?? false, // ✅ Context7: Tablodaki gerçek kolon adı
            'sira' => $request->display_order ?? $request->order ?? Photo::where('ilan_id', $ilan->id)->count(), // Context7: display_order preferred
        ]);

        // ✅ REFACTORED: Using ResponseService
        return ResponseService::success([
            'photo' => [
                'id' => $photo->id,
                'url' => Storage::url($photo->dosya_yolu),
                'thumbnail' => Storage::url($photo->dosya_yolu), // Thumbnail için aynı dosya_yolu kullanılıyor
                'filename' => $filename,
                'is_featured' => $photo->kapak_fotografi,
                'display_order' => $photo->sira, // Context7: API response'da display_order kullan
            ],
        ], 'Fotoğraf başarıyla yüklendi');
    }

    /**
     * Get photos for ilan
     * GET /api/admin/ilanlar/{id}/photos
     */
    public function index($ilanId)
    {
        $photos = Photo::where('ilan_id', $ilanId)
            ->orderBy('sira') // ✅ Context7: Tablodaki gerçek kolon adı
            ->get()
            ->map(fn ($photo) => [
                'id' => $photo->id,
                'url' => Storage::url($photo->dosya_yolu),
                'thumbnail' => Storage::url($photo->dosya_yolu), // Thumbnail için aynı dosya_yolu kullanılıyor
                'is_featured' => $photo->kapak_fotografi,
                'display_order' => $photo->sira, // Context7: API response'da display_order kullan
            ]);

        // ✅ REFACTORED: Using ResponseService
        return ResponseService::success(['photos' => $photos], 'Fotoğraflar başarıyla getirildi');
    }

    /**
     * Update photo (featured, order, etc.)
     * PATCH /api/admin/photos/{id}
     */
    public function update(Request $request, $id)
    {
        $photo = Photo::findOrFail($id);

        // ✅ REFACTORED: Using ValidatesApiRequests trait
        $validated = $this->validateRequestWithResponse($request, [
            'is_featured' => 'nullable|boolean',
            'display_order' => 'nullable|integer',
        ]);

        if ($validated instanceof \Illuminate\Http\JsonResponse) {
            return $validated;
        }

        // If setting as featured, remove featured from others
        if ($request->has('is_featured') && $request->is_featured) {
            Photo::where('ilan_id', $photo->ilan_id)
                ->update(['kapak_fotografi' => false]); // ✅ Context7: Tablodaki gerçek kolon adı
        }

        $photo->update([
            'kapak_fotografi' => $request->is_featured ?? $photo->kapak_fotografi,
            'sira' => $request->display_order ?? $request->order ?? $photo->sira, // Context7: display_order preferred
        ]);

        // ✅ REFACTORED: Using ResponseService
        return ResponseService::success(['photo' => $photo], 'Fotoğraf başarıyla güncellendi');
    }

    /**
     * Delete photo
     * DELETE /api/admin/photos/{id}
     */
    public function destroy($id)
    {
        $photo = Photo::findOrFail($id);

        // Delete files from storage
        if (Storage::disk('public')->exists($photo->dosya_yolu)) {
            Storage::disk('public')->delete($photo->dosya_yolu);
        }

        $photo->delete();

        // ✅ REFACTORED: Using ResponseService
        return ResponseService::success(null, 'Fotoğraf silindi');
    }

    /**
     * Reorder photos (bulk update)
     * POST /api/admin/ilanlar/{id}/photos/reorder
     */
    public function reorder(Request $request, $ilanId)
    {
        // ✅ REFACTORED: Using ValidatesApiRequests trait
        $validated = $this->validateRequestWithResponse($request, [
            'photos' => 'required|array',
            'photos.*.id' => 'required|exists:ilan_fotograflari,id',
            'photos.*.display_order' => 'required|integer',
        ]);

        if ($validated instanceof \Illuminate\Http\JsonResponse) {
            return $validated;
        }

        foreach ($request->photos as $photoData) {
            Photo::where('id', $photoData['id'])
                ->where('ilan_id', $ilanId)
                ->update(['sira' => $photoData['display_order'] ?? $photoData['order'] ?? 0]); // Context7: display_order preferred
        }

        // ✅ REFACTORED: Using ResponseService
        return ResponseService::success(null, 'Sıralama güncellendi');
    }
}
