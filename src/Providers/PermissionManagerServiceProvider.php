<?php
declare(strict_types=1);

namespace CodeFlexTech\PermissionManager\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use CodeFlexTech\PermissionManager\Commands\InstallCommand;
use CodeFlexTech\PermissionManager\Commands\SeedPermissionsCommand;
use CodeFlexTech\PermissionManager\Helpers\PermissionHelper;
use CodeFlexTech\PermissionManager\Http\Livewire\PermissionManager;
use CodeFlexTech\PermissionManager\Http\Livewire\RoleManager;
use CodeFlexTech\PermissionManager\Http\Livewire\RolePermissionManager;
use CodeFlexTech\PermissionManager\Http\Livewire\UserRoleManager;

/**
 * Class PermissionManagerServiceProvider
 *
 * @package   CodeFlexTech\PermissionManager\Providers
 *
 * @author    Faisal Shah <faisalshah4004@gmail.com>
 *
 * @copyright 2026 CodeFlexTech.com
 * @version   1.0
 */
class PermissionManagerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // ── Merge config ──────────────────────────────────
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/permission-manager.php',
            'permission-manager'
        );

        // ── Bind helper as singleton ──────────────────────
        $this->app->singleton('permission-manager', fn() => new PermissionHelper());
    }

    public function boot(): void
    {
        // ── Publishable: config ───────────────────────────
        $this->publishes([
            __DIR__ . '/../../config/permission-manager.php' => config_path('permission-manager.php'),
        ], 'permission-manager-config');

        // ── Publishable: views ────────────────────────────
        $this->publishes([
            __DIR__ . '/../../resources/views' => resource_path('views/vendor/permission-manager'),
        ], 'permission-manager-views');

        // ── Publishable: CSS assets ───────────────────────
        $this->publishes([
            __DIR__ . '/../../resources/css' => public_path('vendor/permission-manager'),
        ], 'permission-manager-assets');

        // ── Load views ────────────────────────────────────
        $this->loadViewsFrom(
            __DIR__ . '/../../resources/views',
            'permission-manager'
        );

        // ── Load routes ───────────────────────────────────
        $this->loadRoutesFrom(__DIR__ . '/../../routes/web.php');

        // ── Register Artisan commands ─────────────────────
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
                SeedPermissionsCommand::class,
            ]);
        }

        // ── Register Livewire components ──────────────────
        Livewire::component('pm-permission-manager',         PermissionManager::class);
        Livewire::component('pm-role-manager',               RoleManager::class);
        Livewire::component('pm-role-permission-manager',    RolePermissionManager::class);
        Livewire::component('pm-user-role-manager',          UserRoleManager::class);
    }
}
