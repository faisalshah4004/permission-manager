<?php
declare(strict_types=1);

namespace CodeFlexTech\PermissionManager\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class InstallCommand extends Command
{
    protected $signature   = 'permission-manager:install';
    protected $description = 'Install the CodeFlexTech Permission Manager package';

    public function handle(): int
    {
        $this->info('');
        $this->info('╔══════════════════════════════════════════╗');
        $this->info('║   CodeFlexTech Permission Manager v1.0   ║');
        $this->info('╚══════════════════════════════════════════╝');
        $this->info('');

        // ── 1. Publish config ─────────────────────────────
        $this->step('Publishing config...');
        Artisan::call('vendor:publish', [
            '--tag'   => 'permission-manager-config',
            '--force' => false,
        ]);
        $this->line('  ✅  config/permission-manager.php');

        // ── 2. Publish CSS assets ─────────────────────────
        $this->step('Publishing assets...');
        Artisan::call('vendor:publish', [
            '--tag'   => 'permission-manager-assets',
            '--force' => true,
        ]);
        $this->line('  ✅  public/vendor/permission-manager/permission-manager.css');

        // ── 3. Check spatie migrations ────────────────────
        $this->step('Checking Spatie permission migrations...');
        if (!$this->migrationExists()) {
            $this->warn('  ⚠  Spatie permission migration not found.');
            $this->warn('     Run: php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"');
        } else {
            $this->line('  ✅  Spatie migrations detected');
        }

        // ── 4. Check User model for HasRoles ─────────────
        $this->step('Checking User model...');
        $userModel = config('permission-manager.user_model', 'App\\Models\\User');
        $userFile  = app_path('Models/User.php');

        if (file_exists($userFile)) {
            $content = file_get_contents($userFile);
            if (str_contains($content, 'HasRoles')) {
                $this->line('  ✅  HasRoles trait detected in User model');
            } else {
                $this->warn('  ⚠  HasRoles trait NOT found in User model.');
                $this->warn('     Add: use Spatie\\Permission\\Traits\\HasRoles;');
                $this->warn('     And: use HasRoles; inside the class');
            }
        }

        // ── 5. Check Livewire ─────────────────────────────
        $this->step('Checking Livewire...');
        if (class_exists(\Livewire\Livewire::class)) {
            $this->line('  ✅  Livewire detected');
        } else {
            $this->error('  ✗  Livewire not found. Run: composer require livewire/livewire');
            return self::FAILURE;
        }

        // ── 6. Done ───────────────────────────────────────
        $this->info('');
        $this->info('✅  Installation complete!');
        $this->info('');
        $this->table(
            ['What', 'URL'],
            [
                ['Dashboard',   url(config('permission-manager.route_prefix', 'permission-manager'))],
                ['Permissions', url(config('permission-manager.route_prefix', 'permission-manager') . '/permissions')],
                ['Roles',       url(config('permission-manager.route_prefix', 'permission-manager') . '/roles')],
                ['Users',       url(config('permission-manager.route_prefix', 'permission-manager') . '/users')],
            ]
        );
        $this->info('');
        $this->comment('Next steps:');
        $this->line('  1. Run: php artisan migrate');
        $this->line('  2. Run: php artisan db:seed --class=PermissionManagerSeeder (optional)');
        $this->line('  3. Visit: ' . url(config('permission-manager.route_prefix', 'permission-manager')));
        $this->info('');

        return self::SUCCESS;
    }

    private function step(string $message): void
    {
        $this->line('');
        $this->line("  <fg=blue>→</> {$message}");
    }

    private function migrationExists(): bool
    {
        $path  = database_path('migrations');
        $files = scandir($path) ?: [];

        foreach ($files as $file) {
            if (str_contains($file, 'create_permission_tables')) {
                return true;
            }
        }

        return false;
    }
}
