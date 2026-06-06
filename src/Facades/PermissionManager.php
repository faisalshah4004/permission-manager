<?php
declare(strict_types=1);

namespace CodeFlexTech\PermissionManager\Facades;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @method static bool userHas(string $permission, mixed $user = null)
 * @method static bool userHasRole(string $role, mixed $user = null)
 * @method static bool userHasAny(array $permissions, mixed $user = null)
 * @method static bool userHasAll(array $permissions, mixed $user = null)
 * @method static array getUserPermissions(mixed $user = null)
 * @method static array getUserRoles(mixed $user = null)
 * @method static void clearCache()
 * @method static Collection getAllPermissions()
 * @method static Collection getAllRoles()
 * @method static Collection getGroupedPermissions()
 *
 * @see \CodeFlexTech\PermissionManager\Helpers\PermissionHelper
 */
class PermissionManager extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected static function getFacadeAccessor(): string
    {
        return 'permission-manager';
    }
}
