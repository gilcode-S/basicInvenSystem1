<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\DashboardController;

// Protected routes (require authentication)
Route::middleware(['auth:sanctum'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'getDashboardData']);
    
    // Locations
    Route::apiResource('locations', LocationController::class);
    
    // Items
    Route::apiResource('items', ItemController::class);
    Route::post('/items/{item}/stock', [ItemController::class, 'updateStock']);
    
    // Stocks
    Route::get('/stocks', [StockController::class, 'index']);
    Route::get('/stocks/low', [StockController::class, 'getLowStock']);
    Route::get('/stocks/{itemId}/{locationId}', [StockController::class, 'show']);
    Route::put('/stocks/{itemId}/{locationId}', [StockController::class, 'update']);
    
    // Transfers
    Route::apiResource('transfers', TransferController::class)->except(['update']);
    Route::post('/transfers/{transfer}/complete', [TransferController::class, 'complete']);
    Route::post('/transfers/{transfer}/cancel', [TransferController::class, 'cancel']);
});