<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Season;
use App\Models\Ilan;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * Season Pricing Management API Controller
 * Pure API endpoints for seasonal pricing
 * Context7 compliant (using DB schema as-is)
 */
class SeasonController extends Controller
{
    /**
     * Get seasons for ilan
     * GET /api/admin/ilanlar/{id}/seasons
     */
    public function index($ilanId)
    {
        $seasons = Season::where('ilan_id', $ilanId)
            ->orderBy('start_date')
            ->get()
            ->map(fn($season) => [
                'id' => $season->id,
                'season_type' => $season->type,
                'name' => $season->name,
                'start_date' => $season->start_date,
                'end_date' => $season->end_date,
                'daily_price' => $season->daily_price,
                'weekly_price' => $season->weekly_price,
                'monthly_price' => $season->monthly_price,
                'minimum_stay' => $season->minimum_stay,
                'maximum_stay' => $season->maximum_stay,
                'is_active' => $season->is_active,
            ]);

        return response()->json([
            'success' => true,
            'seasons' => $seasons
        ]);
    }

    /**
     * Create new season
     * POST /api/admin/seasons
     */
    public function store(Request $request)
    {
        $request->validate([
            'ilan_id' => 'required|exists:ilanlar,id',
            'season_type' => 'required|in:yaz,kis,ara_sezon,bayram,ozel',
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'daily_price' => 'required|numeric|min:0',
            'weekly_price' => 'nullable|numeric|min:0',
            'monthly_price' => 'nullable|numeric|min:0',
            'minimum_stay' => 'nullable|integer|min:1',
            'maximum_stay' => 'nullable|integer|min:1',
            'is_active' => 'nullable|boolean',
        ]);

        $season = Season::create([
            'ilan_id' => $request->ilan_id,
            'name' => $request->name,
            'type' => $request->season_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'daily_price' => $request->daily_price,
            'weekly_price' => $request->weekly_price,
            'monthly_price' => $request->monthly_price,
            'minimum_stay' => $request->minimum_stay ?? 1,
            'maximum_stay' => $request->maximum_stay,
            'is_active' => $request->is_active ?? true,
            'currency' => 'TRY',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sezon oluşturuldu',
            'season' => $season
        ], 201);
    }

    /**
     * Update season
     * PATCH /api/admin/seasons/{id}
     */
    public function update(Request $request, $id)
    {
        $season = Season::findOrFail($id);

        $request->validate([
            'season_type' => 'nullable|in:yaz,kis,ara_sezon,bayram,ozel',
            'name' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'daily_price' => 'nullable|numeric|min:0',
            'weekly_price' => 'nullable|numeric|min:0',
            'monthly_price' => 'nullable|numeric|min:0',
            'minimum_stay' => 'nullable|integer|min:1',
            'maximum_stay' => 'nullable|integer|min:1',
            'is_active' => 'nullable|boolean',
        ]);

        $updateData = [];
        if ($request->has('season_type')) $updateData['type'] = $request->season_type;
        if ($request->has('name')) $updateData['name'] = $request->name;
        if ($request->has('start_date')) $updateData['start_date'] = $request->start_date;
        if ($request->has('end_date')) $updateData['end_date'] = $request->end_date;
        if ($request->has('daily_price')) $updateData['daily_price'] = $request->daily_price;
        if ($request->has('weekly_price')) $updateData['weekly_price'] = $request->weekly_price;
        if ($request->has('monthly_price')) $updateData['monthly_price'] = $request->monthly_price;
        if ($request->has('minimum_stay')) $updateData['minimum_stay'] = $request->minimum_stay;
        if ($request->has('maximum_stay')) $updateData['maximum_stay'] = $request->maximum_stay;
        if ($request->has('is_active')) $updateData['is_active'] = $request->is_active;

        $season->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Sezon güncellendi',
            'season' => $season
        ]);
    }

    /**
     * Delete season
     * DELETE /api/admin/seasons/{id}
     */
    public function destroy($id)
    {
        $season = Season::findOrFail($id);
        $season->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sezon silindi'
        ]);
    }

    /**
     * Get price for date range
     * POST /api/admin/seasons/calculate-price
     */
    public function calculatePrice(Request $request)
    {
        $request->validate([
            'ilan_id' => 'required|exists:ilanlar,id',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
        ]);

        $checkIn = Carbon::parse($request->check_in);
        $checkOut = Carbon::parse($request->check_out);
        $nights = $checkIn->diffInDays($checkOut);

        // Find applicable season
        $season = Season::where('ilan_id', $request->ilan_id)
            ->where('is_active', true)
            ->where('start_date', '<=', $request->check_in)
            ->where('end_date', '>=', $request->check_out)
            ->first();

        if (!$season) {
            return response()->json([
                'success' => false,
                'message' => 'Bu tarihler için sezon fiyatı bulunamadı'
            ], 404);
        }

        $totalPrice = $season->daily_price * $nights;

        // Weekly discount
        if ($nights >= 7 && $season->weekly_price) {
            $weeks = floor($nights / 7);
            $remainingNights = $nights % 7;
            $totalPrice = ($weeks * $season->weekly_price) + ($remainingNights * $season->daily_price);
        }

        // Monthly discount
        if ($nights >= 30 && $season->monthly_price) {
            $months = floor($nights / 30);
            $remainingNights = $nights % 30;
            $totalPrice = ($months * $season->monthly_price) + ($remainingNights * $season->daily_price);
        }

        return response()->json([
            'success' => true,
            'season' => $season->name,
            'nights' => $nights,
            'daily_price' => $season->daily_price,
            'total_price' => $totalPrice,
            'currency' => 'TRY'
        ]);
    }
}

