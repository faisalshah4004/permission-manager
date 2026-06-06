<?php
declare(strict_types=1);

namespace CodeFlexTech\PermissionManager\Helpers;

use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Class PermissionHelper
 *
 * @package   CodeFlexTech\PermissionManager\Helpers
 *
 * @author    Faisal Shah <faisalshah4004@gmail.com>
 *
 * @copyright 2026 CodeFlexTech.com
 * @version   1.0
 */
class PermissionHelper
{
    /**
     * Check if user (or current auth user) has a permission.
     */
    public function userHas(string $permission, mixed $user = null): bool
    {
        $user ??= auth()->user();
        if (!$user) return false;

        // Super admin bypasses everything
        $superAdminRole = config('permission-manager.super_admin_role');
        if ($superAdminRole && $user->hasRole($superAdminRole)) {
            return true;
        }

        return $user->can($permission);
    }

    /**
     * Check if user has a role.
     */
    public function userHasRole(string $role, mixed $user = null): bool
    {
        $user ??= auth()->user();
        return $user?->hasRole($role) ?? false;
    }

    /**
     * Check if user has ANY of the given permissions.
     */
    public function userHasAny(array $permissions, mixed $user = null): bool
    {
        $user ??= auth()->user();
        if (!$user) return false;

        $superAdminRole = config('permission-manager.super_admin_role');
        if ($superAdminRole && $user->hasRole($superAdminRole)) {
            return true;
        }

        return $user->hasAnyPermission($permissions);
    }

    /**
     * Check if user has ALL of the given permissions.
     */
    public function userHasAll(array $permissions, mixed $user = null): bool
    {
        $user ??= auth()->user();
        if (!$user) return false;

        $superAdminRole = config('permission-manager.super_admin_role');
        if ($superAdminRole && $user->hasRole($superAdminRole)) {
            return true;
        }

        return $user->hasAllPermissions($permissions);
    }

    /**
     * Get all permission names for a user.
     */
    public function getUserPermissions(mixed $user = null): array
    {
        $user ??= auth()->user();
        if (!$user) return [];

        return $user->getAllPermissions()->pluck('name')->toArray();
    }

    /**
     * Get all role names for a user.
     */
    public function getUserRoles(mixed $user = null): array
    {
        $user ??= auth()->user();
        if (!$user) return [];

        return $user->getRoleNames()->toArray();
    }

    /**
     * Get all permissions grouped by module prefix.
     */
    public function getGroupedPermissions(): Collection
    {
        return Permission::all()
            ->groupBy(fn($p) => explode('.', $p->name)[0]);
    }

    /**
     * Get all permissions.
     */
    public function getAllPermissions(): Collection
    {
        return Permission::orderBy('name')->get();
    }

    /**
     * Get all roles with permission counts.
     */
    public function getAllRoles(): Collection
    {
        return Role::withCount('permissions')->orderBy('name')->get();
    }

    /**
     * Clear Spatie permission cache.
     */
    public function clearCache(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Sync permissions to a role by name.
     */
    public function syncRolePermissions(string $roleName, array $permissions): void
    {
        $role = Role::findByName($roleName, config('permission-manager.guard', 'web'));
        $role->syncPermissions($permissions);
        $this->clearCache();
    }

    /**
     * Give a single permission to a role.
     */
    public function givePermissionToRole(string $roleName, string $permission): void
    {
        $role = Role::findByName($roleName, config('permission-manager.guard', 'web'));
        $role->givePermissionTo($permission);
        $this->clearCache();
    }

    /**
     * Revoke a permission from a role.
     */
    public function revokePermissionFromRole(string $roleName, string $permission): void
    {
        $role = Role::findByName($roleName, config('permission-manager.guard', 'web'));
        $role->revokePermissionTo($permission);
        $this->clearCache();
    }

    /**
     * Get permission stats summary.
     */
    public function stats(): array
    {
        $userModel = config('permission-manager.user_model');

        return [
            'total_permissions' => Permission::count(),
            'total_roles'       => Role::count(),
            'total_users'       => (new $userModel)->count(),
            'groups'            => Permission::all()
                ->map(fn($p) => explode('.', $p->name)[0])
                ->unique()
                ->values()
                ->toArray(),
        ];
    }
}
