<?php

use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SubmissionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('detail/{journal_id}', [HomeController::class, 'detail']);
Route::get('/submission', [SubmissionController::class, 'index']);
Route::post('/submission', [SubmissionController::class, 'store']);
Route::get('/login', [LoginController::class, 'index']);
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::group(['prefix' => 'admin', 'middleware' => ['auth.admin']], function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard/add', [DashboardController::class, 'add']);
    Route::post('dashboard/add', [DashboardController::class, 'store']);
    Route::get('dashboard/settings', [SettingsController::class, 'index']);
    Route::post('dashboard/settings', [SettingsController::class, 'update']);
    Route::get('dashboard/{journal_id}', [DashboardController::class, 'edit']);
    Route::post('dashboard/{journal_id}', [DashboardController::class, 'update']);
    Route::get('dashboard/{journal_id}/delete', [DashboardController::class, 'delete']);
    Route::get('delete-file/{file_id}', [FileController::class, 'delete']);

    Route::get('logout', [LoginController::class, 'logout']);
});

Route::get('readfile/{file_id}', [FileController::class, 'index']);
