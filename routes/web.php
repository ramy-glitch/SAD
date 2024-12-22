<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\WsmController;
use Illuminate\Support\Facades\Session;

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
    Route::post('wsm/names-weights-of-criteria', [WsmController::class, 'storeCriteriaNamesWeights'])->name('store.criteria.names.weights');

    Route::get('/criteria/tables', [WsmController::class, 'showCriteriaTables'])->name('criteria.tables');
    Route::post('/criteria/tables/problem', [WsmController::class, 'showCriteriaTablesProblem'])->name('criteria.tables.problem');

    Route::post('/store-alternative', [WsmController::class, 'storeAlternative'])->name('store.alternative');
    Route::post('/clear-session-data', [WsmController::class, 'clearSessionData'])->name('clear.session.data');
});