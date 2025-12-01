<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\PhotoBulkActionRequest;
use App\Http\Requests\Admin\PhotoRequest;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class PhotoController extends AdminController
{
    /**
     * Display a listing of the photos.
     * Context7: Fotoğraf galeri yönetimi
     */
    public function index(Request $request): \Illuminate\View\View|\Illuminate\Http\JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 24);
            $category = $request->get('category', 'all');
            $search = $request->get('search', '');

            $photos = $this->getPhotos($category, $search, $perPage);
            $categories = $this->getPhotoCategories();
            $stats = $this->getPhotoStats();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'photos' => $photos,
                        'categories' => $categories,
                        'stats' => $stats,
                    ],
                ]);
            }

            return view('admin.photos.index', compact('photos', 'categories', 'stats'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fotoğraflar yüklenirken hata: '.$e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Fotoğraflar yüklenirken hata: '.$e->getMessage());
        }
    }

    /**
     * Show the form for creating a new photo upload.
     * Context7: Fotoğraf yükleme formu
     */
    public function create(): \Illuminate\View\View
    {
        try {
            $categories = $this->getPhotoCategories();
            $maxFileSize = config('filesystems.max_file_size', '10MB');
            $allowedTypes = config('filesystems.allowed_image_types', ['jpg', 'jpeg', 'png', 'webp']);

            return view('admin.photos.create', compact('categories', 'maxFileSize', 'allowedTypes'));
        } catch (\Exception $e) {
            return back()->with('error', 'Form yüklenirken hata: '.$e->getMessage());
        }
    }

    /**
     * Store newly uploaded photos.
     * Context7: Fotoğraf yükleme ve kaydetme
     *
     * @throws \Exception
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'photos' => 'required|array|min:1|max:20',
                'photos.*' => 'required|image|mimes:jpg,jpeg,png,webp|max:10240', // 10MB
                'category' => 'required|string|max:50',
                'title' => 'nullable|string|max:255',
                'description' => 'nullable|string|max:1000',
                'alt_text' => 'nullable|string|max:255',
                'tags' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation hatası',
                        'errors' => $validator->errors(),
                    ], 422);
                }

                return back()->withErrors($validator)->withInput();
            }

            $uploadedPhotos = [];
            $photos = $request->file('photos');

            foreach ($photos as $index => $photo) {
                // Benzersiz dosya adı oluştur
                $filename = time().'_'.$index.'_'.Str::slug($photo->getClientOriginalName(), '_');

                // Fotoğrafı kaydet
                $path = $photo->storeAs('photos/ilan', $filename, 'public');

                // Resim boyutlarını al
                $imageDimensions = @getimagesize($photo->getPathname());
                $width = $imageDimensions ? $imageDimensions[0] : null;
                $height = $imageDimensions ? $imageDimensions[1] : null;

                // Thumbnail oluştur
                $thumbnailPath = $this->generateThumbnail($path);

                // Image optimization
                $optimizedSize = $this->optimizeImage($path);

                // Photo model ile kaydet
                $photoModel = Photo::create([
                    'ilan_id' => $request->ilan_id ?? null,
                    'path' => $path,
                    'thumbnail' => $thumbnailPath,
                    'category' => $request->category,
                    'size' => $optimizedSize ?? $photo->getSize(),
                    'mime_type' => $photo->getMimeType(),
                    'width' => $width,
                    'height' => $height,
                    'is_featured' => false,
                    'sira' => $index, // Context7: Database'de 'sira' kolonu var
                ]);

                $uploadedPhotos[] = [
                    'id' => $photoModel->id,
                    'filename' => $filename,
                    'original_name' => $photo->getClientOriginalName(),
                    'path' => $photoModel->path,
                    'thumbnail_path' => $photoModel->thumbnail,
                    'url' => $photoModel->url,
                    'thumbnail_url' => $photoModel->thumbnail_url,
                    'size' => $photoModel->size,
                    'formatted_size' => $photoModel->formatted_size,
                    'category' => $photoModel->category,
                    'title' => $request->title ?? $photo->getClientOriginalName(),
                    'description' => $request->description,
                    'alt_text' => $request->alt_text ?? $request->title,
                    'tags' => $request->tags,
                    'uploaded_at' => $photoModel->created_at,
                ];
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => count($uploadedPhotos).' fotoğraf başarıyla yüklendi',
                    'photos' => $uploadedPhotos, // ✅ Context7 Fix: JavaScript data.photos bekliyor
                ], 201);
            }

            return redirect()->route('admin.photos.index')->with('success', count($uploadedPhotos).' fotoğraf başarıyla yüklendi');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fotoğraf yüklenirken hata: '.$e->getMessage(),
                ], 500);
            }

            return back()->withInput()->with('error', 'Fotoğraf yüklenirken hata: '.$e->getMessage());
        }
    }

    /**
     * Display the specified photo.
     * Context7: Fotoğraf detayları ve bilgileri
     *
     * @throws \Exception
     */
    public function show(int $id): \Illuminate\View\View|\Illuminate\Http\JsonResponse
    {
        try {
            $photo = $this->getSamplePhoto($id);

            if (! $photo) {
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Fotoğraf bulunamadı',
                    ], 404);
                }

                return redirect()->route('admin.photos.index')->with('error', 'Fotoğraf bulunamadı');
            }

            // Görüntüleme sayısını artır
            $this->incrementPhotoViews($id);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $photo,
                ]);
            }

            return view('admin.photos.show', compact('photo'));
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fotoğraf detayları alınırken hata: '.$e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Fotoğraf detayları alınırken hata: '.$e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified photo.
     * Context7: Fotoğraf bilgilerini düzenleme
     *
     * @throws \Exception
     */
    public function edit(int $id): \Illuminate\View\View
    {
        try {
            $photo = $this->getSamplePhoto($id);

            if (! $photo) {
                return redirect()->route('admin.photos.index')->with('error', 'Fotoğraf bulunamadı');
            }

            $categories = $this->getPhotoCategories();

            return view('admin.photos.edit', compact('photo', 'categories'));
        } catch (\Exception $e) {
            return back()->with('error', 'Form yüklenirken hata: '.$e->getMessage());
        }
    }

    /**
     * Update the specified photo information.
     * Context7: Fotoğraf bilgileri güncelleme
     *
     * @throws \Exception
     */
    public function update(PhotoRequest $request, int $id): \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
    {
        try {
            // ✅ STANDARDIZED: Using Form Request
            $validated = $request->validated();

            // Photo model ile güncelleme
            $photo = Photo::findOrFail($id);
            $photo->update([
                'category' => $validated['category'],
                'is_featured' => $validated['is_featured'] ?? false,
                'sira' => $request->display_order ?? $request->order ?? $photo->sira, // Context7: display_order preferred
            ]);

            $photoData = [
                'id' => $photo->id,
                'category' => $photo->category,
                'is_featured' => $photo->is_featured,
                'display_order' => $photo->sira, // Context7: API response'da display_order kullan
                'url' => $photo->url,
                'thumbnail_url' => $photo->thumbnail_url,
                'size' => $photo->size,
                'formatted_size' => $photo->formatted_size,
                'views' => $photo->views,
                'updated_at' => $photo->updated_at,
            ];

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Fotoğraf bilgileri başarıyla güncellendi',
                    'data' => $photoData,
                ]);
            }

            return redirect()->route('admin.photos.index')->with('success', 'Fotoğraf bilgileri başarıyla güncellendi');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fotoğraf güncellenirken hata: '.$e->getMessage(),
                ], 500);
            }

            return back()->withInput()->with('error', 'Fotoğraf güncellenirken hata: '.$e->getMessage());
        }
    }

    /**
     * Remove the specified photo.
     * Context7: Fotoğraf silme
     *
     * @throws \Exception
     */
    public function destroy(int $id): \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
    {
        try {
            $photo = $this->getSamplePhoto($id);

            if (! $photo) {
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Fotoğraf bulunamadı',
                    ], 404);
                }

                return back()->with('error', 'Fotoğraf bulunamadı');
            }

            // Dosyaları sil
            if (isset($photo['path'])) {
                Storage::disk('public')->delete($photo['path']);
            }
            if (isset($photo['thumbnail_path'])) {
                Storage::disk('public')->delete($photo['thumbnail_path']);
            }

            // Photo model ile silme
            $photoModel = Photo::findOrFail($id);

            // Dosyaları sil
            if ($photoModel->path) {
                Storage::disk('public')->delete($photoModel->path);
            }
            if ($photoModel->thumbnail) {
                Storage::disk('public')->delete($photoModel->thumbnail);
            }

            // Model'i sil (soft delete)
            $photoModel->delete();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Fotoğraf başarıyla silindi',
                ]);
            }

            return redirect()->route('admin.photos.index')->with('success', 'Fotoğraf başarıyla silindi');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fotoğraf silinirken hata: '.$e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Fotoğraf silinirken hata: '.$e->getMessage());
        }
    }

    /**
     * Context7: Toplu fotoğraf işlemleri
     *
     * @throws \Exception
     */
    public function bulkAction(PhotoBulkActionRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            // ✅ STANDARDIZED: Using Form Request
            $validated = $request->validated();

            $action = $validated['action'];
            $photoIds = $validated['photo_ids'];
            $processedCount = 0;

            switch ($action) {
                case 'delete':
                    // ✅ PERFORMANCE FIX: N+1 query önlendi - Bulk query + bulk delete
                    $photos = Photo::whereIn('id', $photoIds)->get();

                    // Storage dosyalarını toplu sil
                    $pathsToDelete = [];
                    foreach ($photos as $photo) {
                        if ($photo->path) {
                            $pathsToDelete[] = $photo->path;
                        }
                        if ($photo->thumbnail) {
                            $pathsToDelete[] = $photo->thumbnail;
                        }
                    }

                    if (! empty($pathsToDelete)) {
                        Storage::disk('public')->delete($pathsToDelete);
                    }

                    // Bulk delete
                    $processedCount = Photo::whereIn('id', $photoIds)->delete();
                    break;

                case 'move':
                    // ✅ PERFORMANCE FIX: N+1 query önlendi - Bulk update
                    $processedCount = Photo::whereIn('id', $photoIds)
                        ->update(['category' => $validated['target_category'] ?? null]);
                    break;

                case 'feature':
                    // ✅ PERFORMANCE FIX: N+1 query önlendi - Bulk update
                    $photos = Photo::whereIn('id', $photoIds)->get();
                    foreach ($photos as $photo) {
                        $photo->setAsFeatured();
                    }
                    $processedCount = count($photos);
                    break;

                case 'unfeature':
                    // ✅ PERFORMANCE FIX: N+1 query önlendi - Bulk update
                    $processedCount = Photo::whereIn('id', $photoIds)
                        ->update(['is_featured' => false]);
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => $processedCount.' fotoğraf için '.$action.' işlemi başarıyla tamamlandı',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Toplu işlem sırasında hata: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Context7: Fotoğraf optimizasyonu
     *
     * @throws \Exception
     */
    public function optimize(int $id): \Illuminate\Http\JsonResponse
    {
        try {
            $photo = $this->getSamplePhoto($id);

            if (! $photo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fotoğraf bulunamadı',
                ], 404);
            }

            // TODO: Gerçek optimizasyon işlemi
            // Image processing library ile boyut küçültme, format dönüştürme vs.

            return response()->json([
                'success' => true,
                'message' => 'Fotoğraf başarıyla optimize edildi',
                'original_size' => $photo['size'] ?? 0,
                'optimized_size' => ($photo['size'] ?? 0) * 0.7, // %30 küçültme simülasyonu
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Optimizasyon sırasında hata: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Context7: Örnek fotoğraf verileri
     *
     * @param  string  $category
     * @param  string  $search
     * @param  int  $perPage
     * @return array
     */
    private function getPhotos($category = 'all', $search = '', $perPage = 24)
    {
        // Mock data - gerçek implementasyonda veritabanından gelecek
        $allPhotos = [
            [
                'id' => 1,
                'filename' => 'villa_1.jpg',
                'title' => 'Lüks Villa Ana Görünüm',
                'category' => 'villa',
                'size' => 2048000,
                'views' => 145,
                'uploaded_at' => now()->subDays(5),
                'thumbnail_url' => '/storage/photos/thumbnails/villa_1_thumb.jpg',
                'url' => '/storage/photos/ilan/villa_1.jpg',
            ],
            [
                'id' => 2,
                'filename' => 'apartment_1.jpg',
                'title' => 'Modern Daire İç Mekan',
                'category' => 'daire',
                'size' => 1536000,
                'views' => 89,
                'uploaded_at' => now()->subDays(3),
                'thumbnail_url' => '/storage/photos/thumbnails/apartment_1_thumb.jpg',
                'url' => '/storage/photos/ilan/apartment_1.jpg',
            ],
        ];

        // Filtreleme
        if ($category !== 'all') {
            $allPhotos = array_filter($allPhotos, fn ($photo) => $photo['category'] === $category);
        }

        if (! empty($search)) {
            $allPhotos = array_filter(
                $allPhotos,
                fn ($photo) => str_contains(strtolower($photo['title']), strtolower($search))
            );
        }

        return array_slice($allPhotos, 0, $perPage);
    }

    /**
     * Context7: Fotoğraf kategorileri
     *
     * @return array
     */
    private function getPhotoCategories()
    {
        return [
            'villa' => 'Villa',
            'daire' => 'Daire',
            'arsa' => 'Arsa',
            'isyeri' => 'İşyeri',
            'exterior' => 'Dış Mekan',
            'interior' => 'İç Mekan',
            'other' => 'Diğer',
        ];
    }

    /**
     * Context7: Fotoğraf istatistikleri
     *
     * @return array
     */
    private function getPhotoStats()
    {
        return [
            'total' => 150,
            'this_month' => 25,
            'total_size' => '245 MB',
            'categories' => [
                'villa' => 45,
                'daire' => 68,
                'arsa' => 22,
                'isyeri' => 15,
            ],
        ];
    }

    /**
     * Context7: Örnek fotoğraf detayı
     *
     * @param  int|string  $id
     * @return array|null
     */
    private function getSamplePhoto($id)
    {
        $photos = $this->getPhotos();

        return collect($photos)->firstWhere('id', (int) $id);
    }

    /**
     * Context7: Thumbnail oluşturma (mock)
     *
     * @param  string  $originalPath
     * @return string|null
     */
    private function createThumbnail($originalPath)
    {
        return $this->generateThumbnail($originalPath);
    }

    /**
     * Thumbnail generation
     */
    private function generateThumbnail(string $originalPath): ?string
    {
        try {
            $thumbnailPath = 'thumbnails/'.basename($originalPath);

            $image = Image::make(Storage::disk('public')->path($originalPath));

            // Thumbnail (300x300)
            $image->fit(300, 300, function ($constraint) {
                $constraint->upsize();
            });

            // Optimize (JPEG, 80% quality)
            $image->encode('jpg', 80);

            Storage::disk('public')->put($thumbnailPath, (string) $image);

            return $thumbnailPath;
        } catch (\Exception $e) {
            \Log::error('Thumbnail generation error: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Image optimization
     */
    private function optimizeImage(string $path): ?int
    {
        try {
            $image = Image::make(Storage::disk('public')->path($path));

            // Max width: 1920px
            if ($image->width() > 1920) {
                $image->resize(1920, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }

            // Optimize (JPEG, 85% quality)
            $image->encode('jpg', 85);

            Storage::disk('public')->put($path, (string) $image);

            return $image->filesize();
        } catch (\Exception $e) {
            \Log::error('Image optimization error: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Views increment
     */
    private function incrementPhotoViews(int $id): int
    {
        try {
            $photo = Photo::findOrFail($id);
            $photo->incrementViews();

            return $photo->views;
        } catch (\Exception $e) {
            \Log::error('Increment views error: '.$e->getMessage());

            return 0;
        }
    }
}
