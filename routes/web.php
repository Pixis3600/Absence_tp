<?php

use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');
Route::get('/home', fn () => view('home'))->name('home');
Route::get('/locale/{locale}', function (string $locale) {
    abort_unless(in_array($locale, ['fr', 'en'], true), 404);

    session(['app_locale' => $locale]);
    App::setLocale($locale);

    return back();
})->name('locale.switch');

Route::middleware(['auth'])->group(function () {
    Route::resource('absences', AbsenceController::class);
    Route::post('/absences/{absence}/accept', [AbsenceController::class, 'accept'])->name('absences.accept');
    Route::post('/absences/{absence}/reject', [AbsenceController::class, 'reject'])->name('absences.reject');
    Route::post('/absences/test-mail', [AbsenceController::class, 'sendTestMail'])->name('absences.test-mail');

    Route::middleware(['admin'])->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('roles', RoleController::class)->except(['show']);
        Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::get('/admin', fn () => view('home'))->name('admin.dashboard');
    });
});
