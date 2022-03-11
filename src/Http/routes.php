<?php

/*
 * MIT License
 *
 * Copyright (c) 2021 seatplus
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 */

use Illuminate\Support\Facades\Route;
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
        Route::delete('/{request}/delete', [RequestController::class, 'delete'])->name('delete.srp.request');
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
