<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Http;

use App\Http\Controllers\Remote\OrdersController;
use App\Http\Controllers\Remote\SalesController;
use App\Http\Controllers\Remote\StocksController;
use App\Http\Controllers\Remote\IncomesController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix('remote-api')
    ->middleware('remoteApiKey')
    ->group(function () {
        Route::get('orders',  [OrdersController::class, 'index']);
        Route::get('sales',   [SalesController::class, 'index']);
        Route::get('stocks',  [StocksController::class, 'index']);
        Route::get('incomes', [IncomesController::class, 'index']);
    });