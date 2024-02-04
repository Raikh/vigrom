<?php

use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\WalletController;
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

Route::get('/health', [HealthController::class, 'test']);
Route::prefix('/wallet')
    ->group(static function (): void {
        Route::get('/{walletId}', [WalletController::class, 'getWallet']);
        Route::post('/transaction', [WalletController::class, 'addOperation']);
    });

