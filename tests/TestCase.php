<?php


namespace Seatplus\Srp\Tests;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
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
use Seatplus\Srp\Tests\Stubs\Kernel;
use Seatplus\Web\Http\Middleware\Authenticate;
use Seatplus\Web\WebServiceProvider;
use Spatie\Permission\PermissionRegistrar;

abstract class TestCase extends OrchestraTestCase
{
    use LazilyRefreshDatabase;

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
        $this->test_user = Event::fakeFor(fn () => User::factory()->create());

        $this->test_character = $this->test_user->characters->first();

        $this->app->instance('path.public', __DIR__ .'/Stubs');

        Permission::findOrCreate('superuser');
    }

    /**
     * Resolve application HTTP Kernel implementation.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function resolveApplicationHttpKernel($app)
    {
        $app->singleton(\Illuminate\Contracts\Http\Kernel::class, Kernel::class);
    }

    /**
     * Get application providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
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
     * @param \Illuminate\Foundation\Application  $app
     */
    private function setupDatabase($app)
    {
        // Path to our migrations to load
        //$this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        $this->artisan('migrate');
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'mysql');

        config(['app.debug' => true]);

        $app['router']->aliasMiddleware('auth', Authenticate::class);

        // Use test User model for users provider
        $app['config']->set('auth.providers.users.model', User::class);

        $app['config']->set('cache.prefix', 'seatplus_tests---');

        //Setup Inertia for package development
        config()->set('inertia.testing.page_paths', array_merge(
            config()->get('inertia.testing.page_paths', []),
            [
                realpath(__DIR__ . '/../src/resources/js/Pages'),
                realpath(__DIR__ . '/../src/resources/js/Shared'),
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
