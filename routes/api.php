<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Mortezaa97\Factors\Http\Controllers\FactorController;
use Mortezaa97\Factors\Http\Controllers\FactorHasItemController;
use Mortezaa97\Factors\Http\Controllers\FactorLabelController;

Route::prefix('api')->middleware('api')->group(function () {
    Route::apiResource('factors', FactorController::class);
    Route::apiResource('factor-items', FactorHasItemController::class);
});

// Web routes for labels and invoices
Route::middleware('web')->group(function () {
    Route::get('factors/{factor}/labels', [FactorLabelController::class, 'show'])->name('factors.labels');
    Route::get('factors/{factor}/invoice', [FactorLabelController::class, 'invoice'])->name('factors.invoice');
});
