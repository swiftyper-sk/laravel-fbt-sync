<?php

use Illuminate\Support\Facades\Route;
use Swiftyper\fbt\Controllers\IntlController;

Route::group([
    'prefix' => 'intl',
    'as' => 'intl.',
], function () {
    Route::post('sync', [IntlController::class, 'sync'])->name('sync');
    Route::post('upload', [IntlController::class, 'upload'])->name('upload');
    Route::post('deploy', [IntlController::class, 'deploy'])->name('deploy');
});
