<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SwmController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/loginpage', [AuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/login', [AuthController::class, 'login'])->name('admin.loginf');
Route::post('logout', [AuthController::class, 'logout'])->name('admin.logout');


Route::middleware('auth:admin')->group(function () {
    Route::get('dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('swm', [SwmController::class, 'index'])->name('swm.index');
});
