<?php

use App\Http\Controllers\TempPODTLController;
use App\Http\Controllers\TempPOMSTController;
use App\Http\Controllers\TempRODTLController;
use App\Http\Controllers\TempROMSTController;
use App\Models\TempPODTL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::prefix('purchase-order')->group(function() {
    Route::prefix('temp-po-mst')->controller(TempPOMSTController::class)->group(function(){
        Route::get('/', 'getAllTempPOMST');
        Route::get('/{fc_pono}', 'getDetailTempPOMST');
        Route::post('/', 'createTempPOMST');
        Route::put('/{fc_pono}', 'setDetailTempPOMST');
        Route::put('/{fc_pono}/submit', 'submitTempPOMST');
    });
    Route::prefix('temp-po-dtl')->controller(TempPODTLController::class)->group(function() {
        Route::post('/{fc_pono}', 'addTempPODTL');
        Route::delete('/{fc_pono}', 'removeTempPODTL');
        Route::get('/{fc_pono}', 'getAllTempPODTLbyPONO');
    });
});

Route::prefix('receiving-order')->group(function (){
    Route::prefix('temp-ro-mst')->controller(TempROMSTController::class)->group(function() {
        Route::get('/', 'getAllTempROMST');
        Route::get('/{fc_pono}', 'getDetailTempROMST');
        Route::post('/', 'createTempROMST');
        Route::put('/{fc_rono}', 'setDetailTempROMST');
        Route::put('/{fc_rono}/submit', 'submitTempROMST');
    });
    Route::prefix('temp-ro-dtl')->controller(TempRODTLController::class)->group(function() {
        Route::post('/{fc_rono}', 'addTempRODTL');
        Route::delete('/{fc_rono}', 'removeTempRODTL');
        Route::put('/{fc_rono}', 'updateTempRODTL');
        Route::get('/{fc_rono}', 'getAllTempRODTLbyRONO');
    });
});
