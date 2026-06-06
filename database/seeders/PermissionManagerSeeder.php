<?php
declare(strict_types = 1);

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * PermissionManagerSeeder
 *
 * Copy this file to database/seeders/ in your Laravel project.
 * Customize the $permissions array and $roles to fit your app.
 *
 * Run: php artisan db:seed --class=PermissionManagerSeeder
 */
class PermissionManagerSeeder extends Seeder
{
    /**
     * Define all permissions using dot notation: module.action
     * The module prefix becomes the group label in the UI.
     */
    private array $permissions = [
        // ── Dashboard ─────────────────────────────────────
        'dashboard.view',

        // ── Clients ───────────────────────────────────────
        'clients.view',
        'clients.create',
        'clients.edit',
        'clients.delete',
        'clients.export',

        // ── Companies ─────────────────────────────────────
        'companies.view',
        'companies.create',
        'companies.edit',
        'companies.delete',

        // ── Partners ──────────────────────────────────────
        'partners.view',
        'partners.create',
        'partners.edit',
        'partners.delete',

        // ── Invoices ──────────────────────────────────────
        'invoices.view',
        'invoices.create',
        'invoices.edit',
        'invoices.delete',
        'invoices.export',

        // ── Payments ──────────────────────────────────────
        'payments.view',
        'payments.create',
        'payments.edit',
        'payments.delete',

        // ── Reports ───────────────────────────────────────
        'reports.sales',
        'reports.payments',
        'reports.due',
        'reports.clients',
        'reports.salesperson',
        'reports.pending-docs',
        'reports.compare',

        // ── Users ─────────────────────────────────────────
        'users.view',
        'users.create',
        'users.edit',
        'users.delete',

        // ── Roles & Permissions ───────────────────────────
        'roles.view',
        'roles.create',
        'roles.edit',
        'roles.delete',
        'permissions.view',
        'permissions.create',
        'permissions.edit',
        'permissions.delete',

        // ── Organizations ─────────────────────────────────
        'organizations.view',
        'organizations.edit',

        // ── Service Types ─────────────────────────────────
        'service-types.view',
        'service-types.create',
        'service-types.edit',
        'service-types.delete',

        // ── Required Documents ────────────────────────────
        'required-documents.view',
        'required-documents.create',
        'required-documents.edit',
        'required-documents.delete',
    ];

    /**
     * Define roles and which permissions they get.
     * 'all' means every permission above.
     */
    private array $roles = [
        'super_admin' => 'all',

        'admin' => [
            'dashboard.view',
            'clients.view', 'clients.create', 'clients.edit', 'clients.delete',
            'companies.view', 'companies.create', 'companies.edit', 'companies.delete',
            'partners.view', 'partners.create', 'partners.edit', 'partners.delete',
            'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.delete', 'invoices.export',
            'payments.view', 'payments.create', 'payments.edit',
            'reports.sales', 'reports.payments', 'reports.due', 'reports.clients',
            'reports.salesperson', 'reports.pending-docs', 'reports.compare',
            'users.view', 'users.create', 'users.edit',
            'roles.view', 'permissions.view',
            'service-types.view', 'service-types.create', 'service-types.edit',
            'required-documents.view', 'required-documents.create', 'required-documents.edit',
            'organizations.view', 'organizations.edit',
        ],

        'accountant' => [
            'dashboard.view',
            'clients.view',
            'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.export',
            'payments.view', 'payments.create', 'payments.edit',
            'reports.sales', 'reports.payments', 'reports.due', 'reports.compare',
        ],

        'sales_person' => [
            'dashboard.view',
            'clients.view', 'clients.create', 'clients.edit',
            'companies.view',
            'partners.view',
            'invoices.view', 'invoices.create',
            'payments.view',
            'reports.clients',
        ],

        'viewer' => [
            'dashboard.view',
            'clients.view',
            'companies.view',
            'invoices.view',
            'payments.view',
            'reports.sales', 'reports.clients',
        ],
    ];

    public function run(): void
    {
        // ── Clear cache first ─────────────────────────────
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // ── Create all permissions ────────────────────────
        $this->command->info('Creating permissions...');
        foreach ($this->permissions as $permission) {
            Permission::firstOrCreate([
                'name'       => $permission,
                'guard_name' => 'web',
            ]);
        }
        $this->command->info('  ✅ ' . count($this->permissions) . ' permissions created/verified');

        // ── Create roles and assign permissions ───────────
        $this->command->info('Creating roles...');
        $allPermissions = Permission::where('guard_name', 'web')->get();

        foreach ($this->roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate([
                'name'       => $roleName,
                'guard_name' => 'web',
            ]);

            if ($rolePermissions === 'all') {
                $role->syncPermissions($allPermissions);
                $this->command->info("  ✅ {$roleName} → all permissions");
            } else {
                $role->syncPermissions($rolePermissions);
                $this->command->info("  ✅ {$roleName} → " . count($rolePermissions) . ' permissions');
            }
        }

        // ── Clear cache again ─────────────────────────────
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->command->info('');
        $this->command->info('✅ Permissions seeded successfully.');
    }
}
