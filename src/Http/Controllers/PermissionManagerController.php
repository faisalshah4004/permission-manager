<?php
declare(strict_types=1);

namespace CodeFlexTech\PermissionManager\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionManagerController extends Controller
{
    public function index(): View
    {
        $stats = [
            'roles'       => Role::count(),
            'permissions' => Permission::count(),
            'users'       => (config('permission-manager.user_model'))::count(),
        ];

        return view('permission-manager::layouts.app', [
            'slot'       => view('permission-manager::dashboard', compact('stats')),
            'pageTitle'  => 'Permission Manager',
            'activeMenu' => 'dashboard',
        ]);
    }

    public function permissions(): View
    {
        return view('permission-manager::layouts.app', [
            'slot'       => view('permission-manager::permissions.index'),
            'pageTitle'  => 'Permissions',
            'activeMenu' => 'permissions',
        ]);
    }

    public function roles(): View
    {
        return view('permission-manager::layouts.app', [
            'slot'       => view('permission-manager::roles.index'),
            'pageTitle'  => 'Roles',
            'activeMenu' => 'roles',
        ]);
    }

    public function rolePermissions(int $role): View
    {
        $roleModel = Role::findOrFail($role);

        return view('permission-manager::layouts.app', [
            'slot'       => view('permission-manager::roles.permissions', compact('roleModel')),
            'pageTitle'  => "Permissions — {$roleModel->name}",
            'activeMenu' => 'roles',
        ]);
    }

    public function users(): View
    {
        return view('permission-manager::layouts.app', [
            'slot'       => view('permission-manager::users.index'),
            'pageTitle'  => 'User Roles',
            'activeMenu' => 'users',
        ]);
    }
}
