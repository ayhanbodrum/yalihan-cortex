<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Response\ResponseService;
use Illuminate\Http\Request;

class DanismanController extends Controller
{
    public function index(Request $request)
    {
        return ResponseService::success([], 'Danışman endpoint - to be implemented');
    }
}
