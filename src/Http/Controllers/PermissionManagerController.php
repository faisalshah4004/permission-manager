<?php
declare(strict_types=1);

namespace CodeFlexTech\PermissionManager\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Class PermissionManagerController
 *
 * @package   CodeFlexTech\PermissionManager\Http\Controllers
 *
 * @author    Faisal Shah <faisalshah4004@gmail.com>
 *
 * @copyright 2026 CodeFlexTech.com
 * @version   1.0
 */
class PermissionManagerController extends Controller
{
    /**
     * Function index
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(): View
    {
        $stats = [
            'roles'       => Role::count(),
            'permissions' => Permission::count(),
            'users'       => (config('permission-manager.user_model'))::count(),
        ];

        return view('permission-manager::dashboard', compact('stats'));
    }

    /**
     * Function permissions
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function permissions(): View
    {
        return view('permission-manager::permissions.page');
    }

    /**
     * Function roles
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function roles(): View
    {
        return view('permission-manager::roles.page');
    }

    /**
     * Function rolePermissions
     *
     * @param int $role
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function rolePermissions(int $role): View
    {
        $roleModel = Role::findOrFail($role);
        return view('permission-manager::roles.permissions-page', compact('roleModel'));
    }

    /**
     * Function users
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function users(): View
    {
        return view('permission-manager::users.page');
    }
}
