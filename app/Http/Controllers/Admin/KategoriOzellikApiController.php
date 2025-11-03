<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

class KategoriOzellikApiController extends AdminController
{
    public function index(Request $request)
    {
        return response()->json(['message' => 'Kategori Ozellik API endpoint - to be implemented']);
    }
}
