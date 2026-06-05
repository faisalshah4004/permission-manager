<?php
declare(strict_types=1);

namespace CodeFlexTech\PermissionManager\Tests\Feature;

use CodeFlexTech\PermissionManager\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionManagerTest extends TestCase
{
    use RefreshDatabase;

    // ── Routes load ───────────────────────────────────────
    public function test_dashboard_redirects_guest(): void
    {
        $response = $this->get(route('permission-manager.index'));
        $response->assertRedirect('/login');
    }

    public function test_dashboard_loads_for_auth_user(): void
    {
        $user = $this->createAdminUser();
        $response = $this->actingAs($user)->get(route('permission-manager.index'));
        $response->assertOk();
    }

    public function test_permissions_page_loads(): void
    {
        $user = $this->createAdminUser();
        $response = $this->actingAs($user)->get(route('permission-manager.permissions'));
        $response->assertOk();
    }

    public function test_roles_page_loads(): void
    {
        $user = $this->createAdminUser();
        $response = $this->actingAs($user)->get(route('permission-manager.roles'));
        $response->assertOk();
    }

    public function test_users_page_loads(): void
    {
        $user = $this->createAdminUser();
        $response = $this->actingAs($user)->get(route('permission-manager.users'));
        $response->assertOk();
    }

    // ── Permission CRUD ───────────────────────────────────
    public function test_permission_can_be_created(): void
    {
        Permission::create(['name' => 'clients.view', 'guard_name' => 'web']);
        $this->assertDatabaseHas('permissions', ['name' => 'clients.view']);
    }

    public function test_permission_can_be_deleted(): void
    {
        $permission = Permission::create(['name' => 'clients.delete', 'guard_name' => 'web']);
        $permission->delete();
        $this->assertDatabaseMissing('permissions', ['name' => 'clients.delete']);
    }

    // ── Role CRUD ─────────────────────────────────────────
    public function test_role_can_be_created(): void
    {
        Role::create(['name' => 'editor', 'guard_name' => 'web']);
        $this->assertDatabaseHas('roles', ['name' => 'editor']);
    }

    public function test_role_permissions_can_be_synced(): void
    {
        $role = Role::create(['name' => 'manager', 'guard_name' => 'web']);
        $p1   = Permission::create(['name' => 'invoices.view', 'guard_name' => 'web']);
        $p2   = Permission::create(['name' => 'invoices.create', 'guard_name' => 'web']);

        $role->syncPermissions([$p1->id, $p2->id]);

        $this->assertCount(2, $role->permissions);
        $this->assertTrue($role->hasPermissionTo('invoices.view'));
        $this->assertTrue($role->hasPermissionTo('invoices.create'));
    }

    // ── Helper facade ─────────────────────────────────────
    public function test_helper_userHas_returns_false_for_guest(): void
    {
        $helper = app('permission-manager');
        $this->assertFalse($helper->userHas('clients.view'));
    }

    public function test_helper_super_admin_bypasses_permission_check(): void
    {
        config(['permission-manager.super_admin_role' => 'super_admin']);

        $user = $this->createAdminUser();
        $role = Role::create(['name' => 'super_admin', 'guard_name' => 'web']);
        $user->assignRole($role);

        $helper = app('permission-manager');
        $this->assertTrue($helper->userHas('any.permission', $user));
    }

    public function test_getGroupedPermissions_groups_by_prefix(): void
    {
        Permission::create(['name' => 'clients.view',   'guard_name' => 'web']);
        Permission::create(['name' => 'clients.create', 'guard_name' => 'web']);
        Permission::create(['name' => 'invoices.view',  'guard_name' => 'web']);

        $helper  = app('permission-manager');
        $grouped = $helper->getGroupedPermissions();

        $this->assertArrayHasKey('clients',  $grouped->toArray());
        $this->assertArrayHasKey('invoices', $grouped->toArray());
        $this->assertCount(2, $grouped['clients']);
    }

    // ── Seeder command ────────────────────────────────────
    public function test_seed_command_creates_permissions(): void
    {
        $this->artisan('permission-manager:seed')
             ->assertExitCode(0);

        $this->assertGreaterThan(0, Permission::count());
    }
}
