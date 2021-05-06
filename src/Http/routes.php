<?php

use Seatplus\Srp\Http\Controller\AdminController;
use Seatplus\Srp\Http\Controller\KillmailController;
use Seatplus\Srp\Http\Controller\ReceiptController;
use Seatplus\Srp\Http\Controller\RequestController;
use Seatplus\Web\Http\Middleware\CheckRequiredScopes;

Route::middleware(['web', 'auth', CheckRequiredScopes::class])
    ->prefix('srp')
    ->group(function () {
        Route::get('', [RequestController::class, 'index'])->name('srp.request');
        Route::post('', [RequestController::class, 'store'])->name('store.srp.request');

        Route::get('/view/{id}', [RequestController::class, 'viewRequest'])->name('view.srp.request');
        Route::post('/submit/{id}', [RequestController::class, 'submitRequest'])->name('submit.srp.request');
        Route::post('/review/{id}', [RequestController::class, 'handleRequest'])
            ->middleware('permission:accept or reject srp requests')
            ->name('handle.srp.request');


        Route::get('/{location_id}/items', [KillmailController::class, 'items'])->name('get.killmail.items');
        Route::post('/{item_id}/item', [KillmailController::class, 'updateItem'])->name('post.killmail.item');

        Route::middleware('permission:accept or reject srp requests')
            ->prefix('admin')
            ->group(function () {

                Route::get('', [AdminController::class, 'review'])->name('review.srp.requests');
                Route::get('/payout', [AdminController::class, 'payout'])->name('payout.srp.requests');
                Route::post('/payout', [AdminController::class, 'processPayout'])->name('process.payout.srp.requests');
                Route::get('/payout/data', [AdminController::class, 'payoutData'])->name('payout.data.srp.requests');
                Route::get('/receipts', [AdminController::class, 'receipts'])->name('receipts.srp.requests');
            });

        Route::get('receipt/{uuid}', [ReceiptController::class, 'index'])->name('view.srp.receipt');
    });