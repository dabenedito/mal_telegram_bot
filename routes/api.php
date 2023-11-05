<?php

use App\Http\Controllers\SchedulerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::prefix('config')->group(function () {
    Route::get('webhook', [SchedulerController::class, 'setWebhook']);
});
Route::post('response-handler', [SchedulerController::class, 'botResponses']);
Route::get('authorization', [SchedulerController::class, 'getOAuth2Token']);
