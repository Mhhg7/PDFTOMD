<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\FormController;
use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

/* Public website: one page; site.js routes by #hash. */
Route::get('/', [SiteController::class, 'index'])->name('site');
Route::post('/api/forms/{kind}', [FormController::class, 'store'])
    ->whereIn('kind', ['partner', 'inquiry', 'medical', 'apply', 'newsletter'])
    ->middleware('throttle:forms')->name('forms.store');

/* Dashboard */
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [Admin\AuthController::class, 'show'])->name('login');
    Route::post('login', [Admin\AuthController::class, 'login'])->middleware('throttle:login');

    Route::middleware('auth')->group(function () {
        Route::post('logout', [Admin\AuthController::class, 'logout'])->name('logout');
        Route::get('/', [Admin\DashboardController::class, 'index'])->name('home');

        Route::resource('pages', Admin\PageController::class)->except('show');

        Route::get('text', [Admin\TextController::class, 'index'])->name('text');
        Route::post('text', [Admin\TextController::class, 'update'])->name('text.update');

        Route::get('settings', [Admin\SettingsController::class, 'edit'])->name('settings');
        Route::post('settings', [Admin\SettingsController::class, 'update'])->name('settings.update');

        Route::get('media', [Admin\MediaController::class, 'index'])->name('media.index');
        Route::get('media.json', [Admin\MediaController::class, 'json'])->name('media.json');
        Route::post('media', [Admin\MediaController::class, 'store'])->name('media.store');
        Route::put('media/{media}', [Admin\MediaController::class, 'update'])->name('media.update');
        Route::delete('media/{media}', [Admin\MediaController::class, 'destroy'])->name('media.destroy');

        Route::get('submissions', [Admin\SubmissionController::class, 'index'])->name('submissions.index');
        Route::get('submissions/export', [Admin\SubmissionController::class, 'export'])->name('submissions.export');
        Route::get('submissions/{submission}', [Admin\SubmissionController::class, 'show'])->name('submissions.show');
        Route::post('submissions/{submission}/toggle', [Admin\SubmissionController::class, 'toggle'])->name('submissions.toggle');
        Route::get('submissions/{submission}/file', [Admin\SubmissionController::class, 'download'])->name('submissions.file');
        Route::delete('submissions/{submission}', [Admin\SubmissionController::class, 'destroy'])->name('submissions.destroy');

        Route::resource('users', Admin\UserController::class)->except('show');

        Route::get('r/{resource}', [Admin\ResourceController::class, 'index'])->name('r.index');
        Route::get('r/{resource}/create', [Admin\ResourceController::class, 'create'])->name('r.create');
        Route::post('r/{resource}', [Admin\ResourceController::class, 'store'])->name('r.store');
        Route::get('r/{resource}/{id}', [Admin\ResourceController::class, 'edit'])->whereNumber('id')->name('r.edit');
        Route::put('r/{resource}/{id}', [Admin\ResourceController::class, 'update'])->whereNumber('id')->name('r.update');
        Route::delete('r/{resource}/{id}', [Admin\ResourceController::class, 'destroy'])->whereNumber('id')->name('r.destroy');
    });
});
