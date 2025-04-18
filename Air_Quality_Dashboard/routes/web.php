<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\DataSimulationController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SensorController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Public Routes
Route::get('/', function() {
    return view('simplified-dashboard');
});
Route::get('/historical-data', [PublicController::class, 'historicalData']);

// Data API Routes
Route::get('/api/sensors', [PublicController::class, 'getSensors']);
Route::get('/api/sensors/{id}/readings', [PublicController::class, 'getSensorReadings']);

// Authentication Routes
Route::middleware(['auth'])->group(function () {
    // Admin Dashboard Routes
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    
    // Sensor Management Routes
    Route::resource('admin/sensors', SensorController::class, ['as' => 'admin']);
    Route::post('/admin/sensors/{id}/toggle-active', [SensorController::class, 'toggleActive'])->name('admin.sensors.toggle-active');
    
    // Data Simulation Routes
    Route::get('/admin/data-simulation', [DataSimulationController::class, 'index'])->name('admin.data-simulation');
    Route::post('/admin/data-simulation/start', [DataSimulationController::class, 'start'])->name('admin.data-simulation.start');
    Route::post('/admin/data-simulation/stop', [DataSimulationController::class, 'stop'])->name('admin.data-simulation.stop');
    Route::post('/admin/data-simulation/generate', [DataSimulationController::class, 'generate'])->name('admin.data-simulation.generate');
});
