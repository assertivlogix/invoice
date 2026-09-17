<?php

use App\Http\Controllers\TrackedPluginController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public telemetry ping endpoint for WordPress plugin installations
Route::post('/telemetry/ping', [TrackedPluginController::class, 'ping'])->name('api.telemetry.ping');
Route::post('/v1/telemetry/ping', [TrackedPluginController::class, 'ping']);
