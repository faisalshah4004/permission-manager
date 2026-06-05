# Changelog

All notable changes to `codeflextech/permission-manager` will be documented here.

## [1.0.0] — 2025

### Added
- Initial release
- `PermissionManager` Livewire component — full CRUD, search, group filter
- `RoleManager` Livewire component — full CRUD with permission/user count display
- `RolePermissionManager` Livewire component — visual grouped checkbox matrix, select/deselect all
- `UserRoleManager` Livewire component — assign multiple roles per user
- Dashboard with stats overview and quick-action cards
- Auto permission group detection from dot-notation names
- Super admin role protection (blocks accidental deletion)
- `PermissionManagerServiceProvider` with auto-discovery
- `PermissionHelper` class with `userHas`, `userHasAny`, `getGroupedPermissions` etc.
- `PermissionManager` Facade
- `permission-manager:install` Artisan command
- `permission-manager:seed` Artisan command with `--fresh` and `--super-admin` options
- `PermissionManagerSeeder` example seeder with 5 default roles
- Standalone CSS (no Tailwind build step required)
- Fully configurable: route prefix, middleware, guard, user model, layout
- Feature test suite with Orchestra Testbench
