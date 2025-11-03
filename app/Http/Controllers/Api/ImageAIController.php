<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AI\ImageBasedAIDescriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ImageAIController extends Controller
{
    protected $imageAIService;

    public function __construct(ImageBasedAIDescriptionService $imageAIService)
    {
        $this->imageAIService = $imageAIService;
    }

    /**
     * Resim analizi ve açıklama üretimi
     */
    public function analyzeImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|file|image|max:10240', // 10MB max
            'options' => 'sometimes|array',
            'options.detail' => 'sometimes|string|in:low,high,auto',
            'options.include_objects' => 'sometimes|boolean',
            'options.include_colors' => 'sometimes|boolean',
            'options.include_architecture' => 'sometimes|boolean',
            'options.include_style' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $image = $request->file('image');
            $options = $request->input('options', []);

            // Resmi geçici olarak kaydet
            $imagePath = $image->store('temp/ai-analysis', 'public');

            // AI analizi yap
            $analysis = $this->imageAIService->analyzeImage($imagePath, $options);

            // Geçici dosyayı sil
            Storage::disk('public')->delete($imagePath);

            if (!$analysis['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'AI analizi başarısız',
                    'error' => $analysis['error']
                ], 500);
            }

            return response()->json([
                'success' => true,
                'data' => $analysis['analysis'],
                'raw_analysis' => $analysis['raw_analysis'] ?? null
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Resim analizi sırasında hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Otomatik etiketleme
     */
    public function generateTags(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|file|image|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $image = $request->file('image');
            $imagePath = $image->store('temp/ai-tags', 'public');

            $tags = $this->imageAIService->generateTags($imagePath);

            Storage::disk('public')->delete($imagePath);

            return response()->json([
                'success' => true,
                'data' => [
                    'tags' => $tags,
                    'tag_count' => count($tags)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Etiketleme sırasında hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Resim kalite analizi
     */
    public function analyzeQuality(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|file|image|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $image = $request->file('image');
            $imagePath = $image->store('temp/ai-quality', 'public');

            $quality = $this->imageAIService->analyzeImageQuality($imagePath);

            Storage::disk('public')->delete($imagePath);

            if (!$quality['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kalite analizi başarısız',
                    'error' => $quality['error']
                ], 500);
            }

            return response()->json([
                'success' => true,
                'data' => $quality
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kalite analizi sırasında hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toplu resim analizi
     */
    public function analyzeBatch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'images' => 'required|array|min:1|max:5',
            'images.*' => 'required|file|image|max:10240',
            'options' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $images = $request->file('images');
            $options = $request->input('options', []);
            $results = [];

            foreach ($images as $index => $image) {
                $imagePath = $image->store("temp/ai-batch-{$index}", 'public');

                $analysis = $this->imageAIService->analyzeImage($imagePath, $options);
                $results[] = [
                    'index' => $index,
                    'filename' => $image->getClientOriginalName(),
                    'analysis' => $analysis
                ];

                Storage::disk('public')->delete($imagePath);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'results' => $results,
                    'total_images' => count($images)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Toplu analiz sırasında hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
