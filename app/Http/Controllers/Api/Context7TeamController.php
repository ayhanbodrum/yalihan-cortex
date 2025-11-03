<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Context7TeamController extends Controller
{
    public function getGorevler(Request $request)
    {
        return response()->json(['message' => 'Get gorevler endpoint - to be implemented']);
    }

    public function getGorevDetay(Request $request, $id)
    {
        return response()->json(['message' => 'Get gorev detail endpoint - to be implemented']);
    }

    public function createGorev(Request $request)
    {
        return response()->json(['message' => 'Create gorev endpoint - to be implemented']);
    }

    public function updateGorev(Request $request, $id)
    {
        return response()->json(['message' => 'Update gorev endpoint - to be implemented']);
    }

    public function updateGorevStatus(Request $request, $id)
    {
        return response()->json(['message' => 'Update gorev status endpoint - to be implemented']);
    }

    public function getTakimUyeleri(Request $request)
    {
        return response()->json(['message' => 'Get takim uyeleri endpoint - to be implemented']);
    }

    public function getTakimStats(Request $request)
    {
        return response()->json(['message' => 'Get takim stats endpoint - to be implemented']);
    }

    public function getGorevOncelikleri(Request $request)
    {
        return response()->json(['message' => 'Get gorev oncelikleri endpoint - to be implemented']);
    }

    public function getGorevStatuslari(Request $request)
    {
        return response()->json(['message' => 'Get gorev statuslari endpoint - to be implemented']);
    }

    public function getGorevTipleri(Request $request)
    {
        return response()->json(['message' => 'Get gorev tipleri endpoint - to be implemented']);
    }
}
