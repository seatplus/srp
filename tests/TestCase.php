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

namespace Seatplus\Srp\Tests;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Inertia\Inertia;
use Inertia\ServiceProvider as InertiaServiceProviderAlias;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Seatplus\Auth\AuthenticationServiceProvider;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\EveapiServiceProvider;
use Seatplus\Srp\SrpServiceProvider;
use Seatplus\Web\Http\Middleware\Authenticate;
use Seatplus\Web\Tests\Stubs\Kernel;
use Seatplus\Web\WebServiceProvider;
use Spatie\Permission\PermissionRegistrar;

abstract class TestCase extends OrchestraTestCase
{
    protected User $test_user;

    protected $test_character;

    protected function setUp(): void
    {
        parent::setUp();

        //Setup Inertia Root View
        Inertia::setRootView('web::app');

        //Do not use the queue
        Queue::fake();

        // setup database
        $this->setupDatabase($this->app);

        /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
        $this->test_user = Event::fakeFor(function () {
            return User::factory()->create();
        });

        $this->test_character = $this->test_user->characters->first();

        $this->app->instance('path.public', __DIR__.'/../src/public');

        Permission::findOrCreate('superuser');
    }

    /**
     * Resolve application HTTP Kernel implementation.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function resolveApplicationHttpKernel($app)
    {
        $app->singleton('Illuminate\Contracts\Http\Kernel', Kernel::class);
    }

    /**
     * Get application providers.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            SrpServiceProvider::class,
            WebServiceProvider::class,
            EveapiServiceProvider::class,
            InertiaServiceProviderAlias::class,
            AuthenticationServiceProvider::class,
        ];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    private function setupDatabase($app)
    {
        // Path to our migrations to load
        //$this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        $this->artisan('migrate', ['--database' => 'testbench']);
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Use memory SQLite, cleans it self up
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        config(['app.debug' => true]);

        $app['router']->aliasMiddleware('auth', Authenticate::class);

        // Use test User model for users provider
        $app['config']->set('auth.providers.users.model', User::class);

        $app['config']->set('cache.prefix', 'seatplus_tests---');

        //Setup Inertia for package development
        config()->set('inertia.testing.page_paths', array_merge(
            config()->get('inertia.testing.page_paths', []),
            [
                realpath(__DIR__.'/../src/resources/js/Pages'),
                realpath(__DIR__.'/../src/resources/js/Shared'),
            ],
        ));
    }

    protected function givePermissionsToTestUser(array $array)
    {
        foreach ($array as $string) {
            $permission = Permission::findOrCreate($string);

            $this->test_user->givePermissionTo($permission);
        }

        // now re-register all the roles and permissions
        $this->app->make(PermissionRegistrar::class)->registerPermissions();
    }
}
