<?php

use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockMovementController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/stock_movements/send-adjustment', [StockMovementController::class, 'sendAdjustment']);
Route::post('/stock_movements/receive-adjustment', [StockMovementController::class, 'receiveAdjustment']);

// Route::post('/products/send-product', [ProductController::class, 'sendProduct']);
// Route::post('/products/receive-product', [ProductController::class, 'receiveProduct']);
