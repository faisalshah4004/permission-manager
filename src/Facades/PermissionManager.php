<?php
declare(strict_types=1);

namespace CodeFlexTech\PermissionManager\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static bool userHas(string $permission, mixed $user = null)
 * @method static bool userHasRole(string $role, mixed $user = null)
 * @method static bool userHasAny(array $permissions, mixed $user = null)
 * @method static bool userHasAll(array $permissions, mixed $user = null)
 * @method static array getUserPermissions(mixed $user = null)
 * @method static array getUserRoles(mixed $user = null)
 * @method static void clearCache()
 * @method static \Illuminate\Support\Collection getAllPermissions()
 * @method static \Illuminate\Support\Collection getAllRoles()
 * @method static \Illuminate\Support\Collection getGroupedPermissions()
 *
 * @see \CodeFlexTech\PermissionManager\Helpers\PermissionHelper
 */
class PermissionManager extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'permission-manager';
    }
}
