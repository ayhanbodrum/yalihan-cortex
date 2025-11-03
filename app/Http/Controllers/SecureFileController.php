<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SecureFileController extends Controller
{
    public function serveSecureFile(Request $request, $encodedPath)
    {
        return response()->json(['message' => 'Secure file endpoint - to be implemented']);
    }

    public function deleteSecureFile(Request $request, $encodedPath)
    {
        return response()->json(['message' => 'Delete secure file endpoint - to be implemented']);
    }
}
