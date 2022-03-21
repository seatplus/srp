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

namespace Seatplus\Srp;

use Illuminate\Support\ServiceProvider;

class SrpServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function boot()
    {
        // Publish the JS & CSS,
        $this->addPublications();

        // Add routes
        $this->loadRoutesFrom(__DIR__ . '/Http/routes.php');

        //Add Migrations
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations/');

        // Add translations
        //$this->loadTranslationsFrom(__DIR__ . '/resources/lang', 'web');
    }

    public function register()
    {
        $this->mergeConfigurations();
    }

    private function mergeConfigurations()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/package.sidebar.php',
            'package.sidebar'
        );

        $this->mergeConfigFrom(
            __DIR__ . '/config/package.permissions.php',
            'web.permissions'
        );
    }

    private function addPublications()
    {
        /*
         * to publish assets one can run:
         * php artisan vendor:publish --tag=web --force
         * or use Laravel Mix to copy the folder to public repo of core.
         */
        $this->publishes([
            __DIR__ . '/resources/js' => resource_path('js'),
        ], 'web');
    }
}
