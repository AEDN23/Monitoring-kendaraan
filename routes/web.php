<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\FuelTransactionController;
use App\Http\Controllers\FuelTypeController;
use App\Http\Controllers\MaintenanceItemController;
use App\Http\Controllers\VehicleMaintenanceScheduleController;
use App\Http\Controllers\MaintenanceRecordController;
use App\Http\Controllers\ReportController;

// Redirect login/register langsung ke dashboard
Route::get('/login', fn () => redirect('/'))->name('login');
Route::get('/register', fn () => redirect('/'))->name('register');

// Dashboard utama
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Kendaraan
Route::resource('vehicles', VehicleController::class);

// BBM
Route::resource('fuel-transactions', FuelTransactionController::class)->parameters(['fuel-transactions' => 'fuelTransaction']);

// Pengaturan Master
Route::resource('fuel-types', FuelTypeController::class)->parameters(['fuel-types' => 'fuelType']);
Route::resource('maintenance-items', MaintenanceItemController::class)->parameters(['maintenance-items' => 'maintenanceItem']);

// Jadwal perawatan
Route::get('/schedules/{vehicle?}', [VehicleMaintenanceScheduleController::class, 'index'])->name('schedules.index');
Route::prefix('vehicles/{vehicle}')->name('schedules.')->group(function () {
    Route::get('/schedules/create', [VehicleMaintenanceScheduleController::class, 'create'])->name('create');
    Route::post('/schedules', [VehicleMaintenanceScheduleController::class, 'store'])->name('store');
    Route::get('/schedules/{schedule}/edit', [VehicleMaintenanceScheduleController::class, 'edit'])->name('edit');
    Route::put('/schedules/{schedule}', [VehicleMaintenanceScheduleController::class, 'update'])->name('update');
    Route::delete('/schedules/{schedule}', [VehicleMaintenanceScheduleController::class, 'destroy'])->name('destroy');
});

// Riwayat servis
Route::resource('maintenance-records', MaintenanceRecordController::class)->parameters(['maintenance-records' => 'maintenanceRecord']);

// Laporan
Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
