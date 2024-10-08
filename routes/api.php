<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransaksiController;
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

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::post('logout', [AuthController::class, 'logout']);
Route::resource('customers', CustomerController::class);
Route::resource('transaksis', TransaksiController::class);
Route::resource('barangs', BarangController::class);

//Chart
Route::get('sales-by-product', [DashboardController::class, 'getDonutChart']);
Route::get('sales-by-date', [DashboardController::class, 'getBarChart']);
Route::get('sales-today', [DashboardController::class, 'totalToday']);

Route::middleware('auth:sanctum')->group(function () {
});
