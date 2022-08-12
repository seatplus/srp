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
use Seatplus\Web\Http\Middleware\CheckACLPermission;
use Seatplus\Web\Http\Middleware\CheckRequiredScopes;

Route::middleware(['web', 'auth', CheckRequiredScopes::class])
    ->prefix('srp')
    ->group(function () {

        Route::controller(RequestController::class)
            ->group(function () {
                Route::get('', 'index')->name('srp.request');
                Route::post('', 'store')->name('store.srp.request');

                Route::get('/view/{id}', 'viewRequest')->name('view.srp.request');
                Route::delete('/{request}/delete', 'delete')->name('delete.srp.request');
                Route::post('/submit/{id}', 'submitRequest')->name('submit.srp.request');
                Route::post('/review/{id}', 'handleRequest')
                    ->middleware('permission:accept or reject srp requests')
                    ->name('handle.srp.request');
            });

        Route::controller(KillmailController::class)
            ->group(function () {
                Route::get('/{location_id}/items', 'items')->name('get.killmail.items');
                Route::post('/{item_id}/item', 'updateItem')->name('post.killmail.item');
            });

        Route::controller(AdminController::class)
            ->middleware(CheckACLPermission::class . ':accept or reject srp requests')
            ->prefix('admin')
            ->group(function () {
                Route::get('', 'review')->name('review.srp.requests');
                Route::get('/payout', 'payout')->name('payout.srp.requests');
                Route::post('/payout', 'processPayout')->name('process.payout.srp.requests');
                Route::get('/payout/data', 'payoutData')->name('payout.data.srp.requests');
                Route::get('/receipts', 'receipts')->name('receipts.srp.requests');
            });

        Route::get('receipt/{uuid}', [ReceiptController::class, 'index'])->name('view.srp.receipt');
    });
