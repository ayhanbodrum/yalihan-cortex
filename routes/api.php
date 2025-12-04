<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Clean Modular Architecture
|--------------------------------------------------------------------------
|
| Context7 Standard: C7-API-ROUTER-2025-12-04
| All API routes organized in modular structure (routes/api/v1/*)
|
| ✅ Production-Ready: Single source of truth, no legacy code
| ✅ Context7 Compliant: Modular structure, clean organization
| ✅ Developer-Friendly: Clear separation of concerns
|
| Removed:
| ❌ routes/api-admin.php (legacy)
| ❌ routes/api-location.php (legacy)
| ✅ Integrated into routes/api/v1/* modules
|
*/

// API v1 routes with versioning prefix
Route::prefix('v1')->group(function () {
    // Health check - always available
    require __DIR__ . '/api/v1/health.php';

    // Modular API routes (v1) - NO name prefix here, let each module define its own
    require __DIR__ . '/api/v1/location.php';
    require __DIR__ . '/api/v1/frontend.php';
    require __DIR__ . '/api/v1/admin.php';
    require __DIR__ . '/api/v1/ai.php';
    require __DIR__ . '/api/v1/common.php';
});

/*
|--------------------------------------------------------------------------
| API Route Modules Structure
|--------------------------------------------------------------------------
|
| Endpoint Format: /api/v1/{module}/{resource}/{action}
|
| Modules (routes/api/v1/):
| - health.php  → GET /api/v1/health (system health)
| - location.php → GET /api/v1/location/* (geography APIs)
| - frontend.php → GET /api/v1/frontend/* (public/frontend APIs)
| - admin.php    → POST /api/v1/admin/* (admin panel APIs)
| - ai.php       → POST /api/v1/ai/* (AI-powered endpoints)
| - common.php   → GET /api/v1/* (shared/common endpoints)
|
| Status: ✅ Clean, Context7-compliant, production-ready
| Last Updated: 2025-12-04
| Version: 1.0.0 (Modular Architecture)
|
| Compliance:
| ✅ Single source of truth (no legacy files)
| ✅ Modular structure (separated by domain)
| ✅ Context7 naming conventions
| ✅ No dead code or duplicate routes
| ✅ Clear documentation
|
*/

