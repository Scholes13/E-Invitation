<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DoorprizeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Doorprize API endpoints
Route::get('/doorprize/participants', [DoorprizeController::class, 'getParticipants']);

// QR Code preview API endpoint
Route::get('/custom-qr/{id}/preview', 'App\Http\Controllers\CustomQrController@apiPreview');
