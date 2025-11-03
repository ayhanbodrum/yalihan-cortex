<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Photo;
use App\Models\Ilan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

/**
 * Photo Management API Controller
 * Pure API endpoints for photo upload/delete/reorder
 * Context7 compliant!
 */
class PhotoController extends Controller
{
    /**
     * Upload photo (single or multiple)
     * POST /api/admin/photos/upload
     */
    public function upload(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,jpg,png,webp|max:10240', // 10 MB
            'ilan_id' => 'required|exists:ilanlar,id',
            'order' => 'nullable|integer',
            'is_featured' => 'nullable|boolean',
        ]);

        $ilan = Ilan::findOrFail($request->ilan_id);
        $file = $request->file('photo');

        // Generate unique filename
        $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
        
        // Upload paths
        $uploadPath = 'ilanlar/' . $ilan->id . '/photos';
        $thumbnailPath = 'ilanlar/' . $ilan->id . '/thumbnails';

        // Store original
        $path = $file->storeAs($uploadPath, $filename, 'public');

        // Create thumbnail (400x300)
        $thumbnailFilename = 'thumb_' . $filename;
        $img = Image::make($file);
        $img->fit(400, 300);
        
        Storage::disk('public')->put(
            $thumbnailPath . '/' . $thumbnailFilename,
            (string) $img->encode()
        );

        // Get image dimensions
        $imageInfo = getimagesize($file);

        // Create photo record
        $photo = Photo::create([
            'ilan_id' => $ilan->id,
            'path' => $path,
            'thumbnail' => $thumbnailPath . '/' . $thumbnailFilename,
            'category' => 'genel',
            'is_featured' => $request->is_featured ?? false,
            'order' => $request->order ?? Photo::where('ilan_id', $ilan->id)->count(),
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'width' => $imageInfo[0] ?? null,
            'height' => $imageInfo[1] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'photo' => [
                'id' => $photo->id,
                'url' => Storage::url($photo->path),
                'thumbnail' => Storage::url($photo->thumbnail),
                'filename' => $filename,
                'is_featured' => $photo->is_featured,
                'order' => $photo->order,
            ]
        ]);
    }

    /**
     * Get photos for ilan
     * GET /api/admin/ilanlar/{id}/photos
     */
    public function index($ilanId)
    {
        $photos = Photo::where('ilan_id', $ilanId)
            ->orderBy('order')
            ->get()
            ->map(fn($photo) => [
                'id' => $photo->id,
                'url' => Storage::url($photo->path),
                'thumbnail' => Storage::url($photo->thumbnail),
                'is_featured' => $photo->is_featured,
                'order' => $photo->order,
            ]);

        return response()->json([
            'success' => true,
            'photos' => $photos
        ]);
    }

    /**
     * Update photo (featured, order, etc.)
     * PATCH /api/admin/photos/{id}
     */
    public function update(Request $request, $id)
    {
        $photo = Photo::findOrFail($id);

        $request->validate([
            'is_featured' => 'nullable|boolean',
            'order' => 'nullable|integer',
            'category' => 'nullable|string',
        ]);

        // If setting as featured, remove featured from others
        if ($request->has('is_featured') && $request->is_featured) {
            Photo::where('ilan_id', $photo->ilan_id)
                ->update(['is_featured' => false]);
        }

        $photo->update($request->only(['is_featured', 'order', 'category']));

        return response()->json([
            'success' => true,
            'photo' => $photo
        ]);
    }

    /**
     * Delete photo
     * DELETE /api/admin/photos/{id}
     */
    public function destroy($id)
    {
        $photo = Photo::findOrFail($id);

        // Delete files from storage
        if (Storage::disk('public')->exists($photo->path)) {
            Storage::disk('public')->delete($photo->path);
        }
        if ($photo->thumbnail && Storage::disk('public')->exists($photo->thumbnail)) {
            Storage::disk('public')->delete($photo->thumbnail);
        }

        $photo->delete();

        return response()->json([
            'success' => true,
            'message' => 'Fotoğraf silindi'
        ]);
    }

    /**
     * Reorder photos (bulk update)
     * POST /api/admin/ilanlar/{id}/photos/reorder
     */
    public function reorder(Request $request, $ilanId)
    {
        $request->validate([
            'photos' => 'required|array',
            'photos.*.id' => 'required|exists:photos,id',
            'photos.*.order' => 'required|integer',
        ]);

        foreach ($request->photos as $photoData) {
            Photo::where('id', $photoData['id'])
                ->where('ilan_id', $ilanId)
                ->update(['order' => $photoData['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Sıralama güncellendi'
        ]);
    }
}

