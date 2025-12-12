<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\CarController;
use App\Http\Controllers\Api\V1\BrandController;
use App\Http\Controllers\Api\V1\CarExpenseController;
use App\Http\Controllers\Api\V1\CarSaleController;
use App\Http\Controllers\Api\V1\FinancialAccountController;
use App\Http\Controllers\Api\V1\BankLoanController;
use App\Http\Controllers\Api\V1\DashboardController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    // Inventory
    Route::apiResource('brands', BrandController::class);
    Route::apiResource('cars', CarController::class);
    Route::post('cars/{car}/expenses', [CarExpenseController::class, 'store']);
    Route::post('cars/{car}/sale', [CarSaleController::class, 'store']);

    // Finance
    Route::apiResource('financial-accounts', FinancialAccountController::class);
    Route::apiResource('bank-loans', BankLoanController::class);
    Route::post('bank-loans/{loan}/disburse', [BankLoanController::class, 'disburse']);
    Route::post('bank-loans/{loan}/payments', [BankLoanController::class, 'addPayment']);

    // Dashboards
    Route::get('dashboards/summary', [DashboardController::class, 'summary']);
    Route::get('dashboards/sales-by-brand', [DashboardController::class, 'salesByBrand']);
    Route::get('dashboards/monthly-stats', [DashboardController::class, 'monthlyStats']);
});
