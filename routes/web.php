<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\WsmController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/loginpage', [AuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/login', [AuthController::class, 'login'])->name('admin.loginf');
Route::post('logout', [AuthController::class, 'logout'])->name('admin.logout');

Route::middleware('auth:admin')->group(function () {
    Route::get('dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('wsm', [WsmController::class, 'index'])->name('wsm.index');
    
    Route::post('wsm/number-of-criteria', [WsmController::class, 'criteriaNum'])->name('criteria.submit');
    Route::post('wsm/names-weights-of-criteria', [WsmController::class, 'storeCriteriaNamesWeights'])->name('criteria.storeNamesWeights');

    // Route to display the criteria tables view
    Route::get('/criteria/tables', [WsmController::class, 'criteriaTables'])->name('criteria.tables');
});
