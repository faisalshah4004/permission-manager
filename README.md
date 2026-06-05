# codeflextech/permission-manager

A plug-and-play **Roles & Permissions management UI** for any Laravel project using `spatie/laravel-permission` + Livewire 3.

Built by [CodeFlexTech](https://codeflextech.com).

---

## Features

- ✅ **Permission CRUD** — create, edit, delete with dot-notation grouping (`module.action`)
- ✅ **Role CRUD** — create, edit, delete with user/permission counts
- ✅ **Role → Permissions** — visual grouped checkbox matrix with select/deselect all per group
- ✅ **User → Roles** — assign/revoke multiple roles per user
- ✅ **Dashboard** — stats overview + quick action cards
- ✅ **Auto permission groups** — auto-detects groups from `module.action` naming
- ✅ **Super admin protection** — blocks deletion of super admin role
- ✅ **Zero Tailwind build required** — ships its own standalone CSS
- ✅ **Livewire 3** — real-time search, modals, loading states, no full page reloads
- ✅ **Configurable** — layout, middleware, guard, user model, route prefix

---

## Requirements

| Dependency | Version |
|---|---|
| PHP | ^8.2 |
| Laravel | ^11.0 or ^12.0 |
| spatie/laravel-permission | ^6.0 |
| livewire/livewire | ^3.0 |

---

## Installation

### Step 1 — Install via Composer

```bash
composer require codeflextech/permission-manager
```

### Step 2 — Publish assets

```bash
# Publish config
php artisan vendor:publish --tag=permission-manager-config

# Publish CSS (required — serves the UI styles)
php artisan vendor:publish --tag=permission-manager-assets

# Optional: publish views to customize
php artisan vendor:publish --tag=permission-manager-views
```

### Step 3 — Make sure spatie migrations are run

```bash
php artisan migrate
```

### Step 4 — Add HasRoles trait to your User model

```php
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
}
```

### Step 5 — Visit the UI

```
http://your-app.test/permission-manager
```

---

## Configuration

After publishing, edit `config/permission-manager.php`:

```php
return [
    // URL prefix — default: /permission-manager
    'route_prefix' => 'permission-manager',

    // Middleware on all routes
    'middleware' => ['web', 'auth'],

    // Spatie guard
    'guard' => 'web',

    // Role name that bypasses permission checks
    'super_admin_role' => 'super_admin',

    // Your User model
    'user_model' => \App\Models\User::class,

    // Set to your app layout component to embed inside your app UI
    // e.g. 'layouts.app' — leave null to use package's standalone layout
    'layout' => null,

    // Rows per page
    'per_page' => 15,

    // Disable permission creation via UI (seed-only mode)
    'allow_create_permissions' => true,
];
```

---

## Middleware / Access Control

By default only `auth` middleware is applied. To restrict to admins only, update config:

```php
'middleware' => ['web', 'auth', 'role:super_admin'],
```

Or use a gate:

```php
'middleware' => ['web', 'auth', 'can:manage-permissions'],
```

---

## Permission Naming Convention

This package works best with dot-notation:

```
module.action

Examples:
  clients.view
  clients.create
  clients.edit
  clients.delete
  invoices.create
  invoices.export
  reports.sales
  reports.due_payments
  organizations.edit
```

The UI auto-groups by the prefix before the first `.`.

---

## Using Permissions in Your App

```php
// Check permission
$user->can('clients.create');
@can('clients.create') ... @endcan

// Check role
$user->hasRole('admin');
@role('admin') ... @endrole

// Assign role
$user->assignRole('admin');

// Sync roles
$user->syncRoles(['admin', 'editor']);

// Sync permissions to role
$role->syncPermissions(['clients.view', 'clients.create']);
```

---

## Seeding Permissions

Recommended: seed permissions in a seeder, use the UI only for role assignment.

```php
class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Clients
            'clients.view', 'clients.create', 'clients.edit', 'clients.delete',
            // Invoices
            'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.delete',
            // Reports
            'reports.sales', 'reports.payments', 'reports.due',
            // Settings
            'roles.manage', 'permissions.manage', 'organizations.edit',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create super admin role with all permissions
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->syncPermissions(Permission::all());
    }
}
```

---

## Routes

| Method | URL | Name | Description |
|--------|-----|------|-------------|
| GET | /permission-manager | permission-manager.index | Dashboard |
| GET | /permission-manager/permissions | permission-manager.permissions | Permissions list |
| GET | /permission-manager/roles | permission-manager.roles | Roles list |
| GET | /permission-manager/roles/{id}/permissions | permission-manager.role-permissions | Assign permissions to role |
| GET | /permission-manager/users | permission-manager.users | Assign roles to users |

---

## Customizing Views

```bash
php artisan vendor:publish --tag=permission-manager-views
```

Views are published to `resources/views/vendor/permission-manager/`.

---

## Changelog

### v1.0.0
- Initial release
- Permission CRUD with group auto-detection
- Role CRUD with super admin protection
- Role → Permission grouped matrix
- User → Role assignment
- Standalone CSS, no Tailwind build required
- Livewire 3 real-time UI

---

## License

MIT — free to use in any project.

---

**Made with ❤️ by [CodeFlexTech](https://codeflextech.com)**
