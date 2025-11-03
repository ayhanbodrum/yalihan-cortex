<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Search users (specifically for danisman role)
     * Context7: C7-USER-SEARCH-2025-10-30
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $query = $request->get('q', '');
            $limit = min($request->get('limit', 20), 50);
            $role = $request->get('role', 'danisman'); // Default: danisman

            $usersQuery = User::query()
                ->select(['id', 'name', 'email', 'status', 'created_at'])
                ->where('status', 1); // Active users

            // Role filter (if roles table exists)
            // For now, we'll use a simple name/email search
            // TODO: Implement proper role filtering with roles table

            // Search filter
            if (!empty($query)) {
                $usersQuery->where(function ($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                      ->orWhere('email', 'LIKE', "%{$query}%");
                });
            }

            // Order by name
            $usersQuery->orderBy('name');

            $users = $usersQuery->limit($limit)->get();

            // Add formatted display text (Context7 Live Search compatibility)
            $users->each(function ($user) {
                $user->text = $user->name; // For dropdown display
                $user->kisi_tipi = 'Sistem Danışmanı'; // For compatibility with kişiler
            });

            return response()->json([
                'success' => true,
                'data' => $users,
                'count' => $users->count(),
                'query' => $query,
                'source' => 'users' // To differentiate from kisiler
            ]);

        } catch (\Exception $e) {
            Log::error('User search error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Danışman araması sırasında hata oluştu',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get all active danismanlar
     */
    public function danismanlar(Request $request): JsonResponse
    {
        try {
            $danismanlar = User::where('status', 1)
                ->orderBy('name')
                ->select(['id', 'name', 'email'])
                ->get();

            $danismanlar->each(function ($user) {
                $user->text = $user->name;
            });

            return response()->json([
                'success' => true,
                'data' => $danismanlar,
                'count' => $danismanlar->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Danismanlar list error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Danışmanlar listesi alınamadı'
            ], 500);
        }
    }
}

