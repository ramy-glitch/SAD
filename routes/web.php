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
    
    Route::post('/criteria-submit', [CriteriaController::class, 'submit'])->name('criteria.submit');
    Route::post('/store-criteria-names-weights', [CriteriaController::class, 'storeCriteriaNamesWeights'])->name('criteria.storeNamesWeights');
});
