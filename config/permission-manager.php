<?php
declare(strict_types = 1);

use App\Models\User;

return [

    /*
    |--------------------------------------------------------------------------
    | Route Prefix
    |--------------------------------------------------------------------------
    | The prefix for all permission manager routes.
    | Default: /permission-manager
    */
    'route_prefix' => 'permission-manager',

    /*
    |--------------------------------------------------------------------------
    | Route Middleware
    |--------------------------------------------------------------------------
    | Middleware applied to all package routes.
    | Add your auth + role guards here.
    */
    'middleware' => ['web', 'auth'],

    /*
    |--------------------------------------------------------------------------
    | Guard Name
    |--------------------------------------------------------------------------
    | The guard used by spatie/laravel-permission.
    */
    'guard' => 'web',

    /*
    |--------------------------------------------------------------------------
    | Super Admin Role
    |--------------------------------------------------------------------------
    | Users with this role bypass all permission checks inside the package UI.
    | Set to null to disable.
    */
    'super_admin_role' => 'super_admin',

    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    | The user model used in your application.
    */
    'user_model' => User::class,

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    | The blade layout component the package views will extend.
    | Set to null to use the package's built-in standalone layout.
    | Example: 'layouts.app' or 'components.layouts.app'
    */
    'layout' => null,

    /*
    |--------------------------------------------------------------------------
    | Permissions per Page
    |--------------------------------------------------------------------------
    */
    'per_page' => 15,

    /*
    |--------------------------------------------------------------------------
    | Allow Permission Creation
    |--------------------------------------------------------------------------
    | Set to false if you want permissions seeded only (not created via UI).
    */
    'allow_create_permissions' => true,

    /*
    |--------------------------------------------------------------------------
    | Permission Groups
    |--------------------------------------------------------------------------
    | Optional: group permissions by prefix for better organization in the UI.
    | e.g. 'clients.*', 'invoices.*', 'reports.*'
    | Leave empty to auto-detect groups from permission names.
    */
    'permission_groups' => [],

];
