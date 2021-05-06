<?php

use Seatplus\Srp\Http\Controller\RequestController;
use Seatplus\Web\Http\Middleware\CheckRequiredScopes;

Route::middleware(['web', 'auth', CheckRequiredScopes::class])
    ->prefix('srp')
    ->group(function () {
        Route::get('', [RequestController::class, 'index'])->name('srp.request');
        Route::post('', [RequestController::class, 'store'])->name('store.srp.request');

        Route::get('/{id}', [RequestController::class, 'viewRequest'])->name('view.srp.request');
    });
