<?php
declare(strict_types=1);

namespace CodeFlexTech\PermissionManager\Http\Livewire;

use Livewire\Attributes\Computed;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionManager extends Component
{
    public int    $roleId;
    public string $search = '';

    // Selected permission IDs
    public array $selectedPermissions = [];

    public function mount(int $roleId): void
    {
        $this->roleId = $roleId;

        // Pre-fill currently assigned permissions
        $this->selectedPermissions = Role::findOrFail($roleId)
            ->permissions
            ->pluck('id')
            ->map(fn($id) => (string) $id)
            ->toArray();
    }

    #[Computed]
    public function role(): Role
    {
        return Role::with('permissions')->findOrFail($this->roleId);
    }

    #[Computed]
    public function groupedPermissions(): array
    {
        $permissions = Permission::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('name')
            ->get();

        // Group by prefix (before first '.')
        $grouped = [];
        foreach ($permissions as $permission) {
            $parts = explode('.', $permission->name);
            $group = count($parts) > 1 ? $parts[0] : 'general';
            $grouped[$group][] = $permission;
        }

        ksort($grouped);
        return $grouped;
    }

    #[Computed]
    public function totalPermissions(): int
    {
        return Permission::count();
    }

    // ── Toggle single permission ──────────────────────
    public function togglePermission(int $permissionId): void
    {
        $id = (string) $permissionId;
        if (in_array($id, $this->selectedPermissions)) {
            $this->selectedPermissions = array_values(
                array_filter($this->selectedPermissions, fn($p) => $p !== $id)
            );
        } else {
            $this->selectedPermissions[] = $id;
        }
    }

    // ── Toggle entire group ───────────────────────────
    public function toggleGroup(string $group): void
    {
        $groupIds = collect($this->groupedPermissions[$group] ?? [])
            ->pluck('id')
            ->map(fn($id) => (string) $id)
            ->toArray();

        $allSelected = empty(array_diff($groupIds, $this->selectedPermissions));

        if ($allSelected) {
            // Deselect all in group
            $this->selectedPermissions = array_values(
                array_filter($this->selectedPermissions, fn($p) => !in_array($p, $groupIds))
            );
        } else {
            // Select all in group
            $this->selectedPermissions = array_values(
                array_unique(array_merge($this->selectedPermissions, $groupIds))
            );
        }
    }

    // ── Select / deselect all ─────────────────────────
    public function selectAll(): void
    {
        $this->selectedPermissions = Permission::pluck('id')
            ->map(fn($id) => (string) $id)
            ->toArray();
    }

    public function deselectAll(): void
    {
        $this->selectedPermissions = [];
    }

    // ── Save ──────────────────────────────────────────
    public function save(): void
    {
        $role = Role::findOrFail($this->roleId);
        $role->syncPermissions($this->selectedPermissions);

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        session()->flash('pm_success', "Permissions for role \"{$role->name}\" updated successfully.");
    }

    public function updatedSearch(): void
    {
        // reset computed cache
        unset($this->groupedPermissions);
    }

    public function render()
    {
        return view('permission-manager::roles.permissions', [
            'role'               => $this->role,
            'groupedPermissions' => $this->groupedPermissions,
            'totalPermissions'   => $this->totalPermissions,
        ]);
    }
}
