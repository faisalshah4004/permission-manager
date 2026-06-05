<?php

use CodeFlexTech\PermissionManager\Http\Controllers\PermissionManagerController;
use Illuminate\Support\Facades\Route;

$prefix     = config('permission-manager.route_prefix', 'permission-manager');
$middleware = config('permission-manager.middleware', ['web', 'auth']);

Route::prefix($prefix)
    ->middleware($middleware)
    ->name('permission-manager.')
    ->group(function () {
        // ── Dashboard ─────────────────────────────────
        Route::get('/',           [PermissionManagerController::class, 'index'])      ->name('index');

        // ── Permissions ───────────────────────────────
        Route::get('/permissions',[PermissionManagerController::class, 'permissions'])->name('permissions');

        // ── Roles ─────────────────────────────────────
        Route::get('/roles',      [PermissionManagerController::class, 'roles'])      ->name('roles');

        // ── Role → Permissions assignment ─────────────
        Route::get('/roles/{role}/permissions', [PermissionManagerController::class, 'rolePermissions'])->name('role-permissions');

        // ── Users → Roles assignment ──────────────────
        Route::get('/users',      [PermissionManagerController::class, 'users'])      ->name('users');
    });
