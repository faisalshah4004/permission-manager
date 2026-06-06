<?php
declare(strict_types=1);

namespace CodeFlexTech\PermissionManager\Tests;

use CodeFlexTech\PermissionManager\Providers\PermissionManagerServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\Permission\PermissionServiceProvider;

abstract class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom([
            '--database' => 'testing',
        ]);
    }

    /**
     * Function getPackageProviders
     *
     * @param $app
     *
     * @return string[]
     */
    protected function getPackageProviders($app): array
    {
        return [
            LivewireServiceProvider::class,
            PermissionServiceProvider::class,
            PermissionManagerServiceProvider::class,
        ];
    }

    /**
     * Function getEnvironmentSetUp
     *
     * @param $app
     */
    protected function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');
        config()->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
        config()->set('auth.providers.users.model', \App\Models\User::class);
        config()->set('permission-manager.user_model', \App\Models\User::class);
        config()->set('permission-manager.super_admin_role', 'super_admin');
        config()->set('permission-manager.guard', 'web');
        config()->set('permission-manager.route_prefix', 'permission-manager');
        config()->set('permission-manager.middleware', ['web', 'auth']);
    }

    /**
     * Create a user with admin role for testing.
     */
    protected function createAdminUser(): mixed
    {
        $userModel = config('permission-manager.user_model');
        return $userModel::factory()->create();
    }
}
