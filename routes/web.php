<?php

use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');
Route::get('/home', fn () => view('home'))->name('home');

Route::middleware(['auth'])->group(function () {
    Route::resource('absences', AbsenceController::class);
    Route::post('/absences/{absence}/accept', [AbsenceController::class, 'accept'])->name('absences.accept');
    Route::post('/absences/{absence}/reject', [AbsenceController::class, 'reject'])->name('absences.reject');

    Route::middleware(['admin'])->group(function () {
        Route::resource('users', UserController::class);
        Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::get('/admin', fn () => view('home'))->name('admin.dashboard');
    });
});
