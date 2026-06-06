<?php
declare(strict_types=1);

namespace CodeFlexTech\PermissionManager\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Class SeedPermissionsCommand
 *
 * @package   CodeFlexTech\PermissionManager\Commands
 *
 * @author    Faisal Shah <faisalshah4004@gmail.com>
 *
 * @copyright 2026 CodeFlexTech.com
 * @version   1.0
 */
class SeedPermissionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission-manager:seed
                            {--guard=web : The guard name}
                            {--super-admin= : Role name to assign all permissions}
                            {--fresh : Clear all existing permissions and roles first}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed permissions from config or a provided list';

    /**
     * Function handle
     *
     * @return int
     */
    public function handle(): int
    {
        $guard       = $this->option('guard')       ?? 'web';
        $superAdmin  = $this->option('super-admin') ?? config('permission-manager.super_admin_role');
        $fresh       = $this->option('fresh');

        if ($fresh) {
            if (!$this->confirm('This will DELETE all existing permissions and roles. Continue?')) {
                $this->line('Aborted.');
                return self::SUCCESS;
            }
            Permission::truncate();
            Role::truncate();
            $this->warn('  🗑  Existing permissions and roles cleared.');
        }

        // ── Get permissions from config or prompt ─────────
        $configGroups = config('permission-manager.permission_groups', []);

        if (empty($configGroups)) {
            $this->info('No permission_groups defined in config. Using default set.');
            $permissions = $this->defaultPermissions();
        } else {
            $permissions = $configGroups;
        }

        // ── Create permissions ────────────────────────────
        $this->info('');
        $this->info('Creating permissions...');
        $bar = $this->output->createProgressBar(count($permissions));
        $bar->start();

        $created = 0;
        $skipped = 0;

        foreach ($permissions as $permission) {
            [$exists] = [Permission::where('name', $permission)->where('guard_name', $guard)->exists()];
            if (!$exists) {
                Permission::create(['name' => $permission, 'guard_name' => $guard]);
                $created++;
            } else {
                $skipped++;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->info('');
        $this->line("  ✅  Created: $created | Skipped (exists): $skipped");

        // ── Create super admin role ───────────────────────
        if ($superAdmin) {
            $this->info('');
            $this->info("Creating super admin role: $superAdmin...");

            $role = Role::firstOrCreate([
                'name'       => $superAdmin,
                'guard_name' => $guard,
            ]);

            $role->syncPermissions(Permission::where('guard_name', $guard)->get());
            $this->line("  ✅  Role '$superAdmin' has all " . Permission::count() . ' permissions');
        }

        // ── Clear cache ───────────────────────────────────
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->info('');
        $this->info('✅  Permissions seeded successfully.');
        $this->info('');

        return self::SUCCESS;
    }

    /**
     * Function defaultPermissions
     *
     * @return string[]
     */
    private function defaultPermissions(): array
    {
        return [
            // Clients
            'clients.view', 'clients.create', 'clients.edit', 'clients.delete',
            // Companies
            'companies.view', 'companies.create', 'companies.edit', 'companies.delete',
            // Partners
            'partners.view', 'partners.create', 'partners.edit', 'partners.delete',
            // Invoices
            'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.delete', 'invoices.export',
            // Payments
            'payments.view', 'payments.create', 'payments.edit', 'payments.delete',
            // Reports
            'reports.sales', 'reports.payments', 'reports.due', 'reports.clients',
            'reports.salesperson', 'reports.pending-docs', 'reports.pending-work',
            // Users
            'users.view', 'users.create', 'users.edit', 'users.delete',
            // Roles & Permissions
            'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
            'permissions.view', 'permissions.create', 'permissions.edit', 'permissions.delete',
            // Organizations
            'organizations.view', 'organizations.edit',
            // Service Types
            'service-types.view', 'service-types.create', 'service-types.edit', 'service-types.delete',
            // Required Documents
            'required-documents.view', 'required-documents.create', 'required-documents.edit', 'required-documents.delete',
            // Dashboard
            'dashboard.view',
        ];
    }
}
