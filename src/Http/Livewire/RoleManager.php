<?php
declare(strict_types=1);

namespace CodeFlexTech\PermissionManager\Http\Livewire;

use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class RoleManager extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search  = '';
    public int    $perPage = 15;

    public bool $showModal   = false;
    public bool $showConfirm = false;
    public ?int $editingId   = null;
    public ?int $deletingId  = null;

    public string $name      = '';
    public string $guardName = 'web';

    protected function rules(): array
    {
        $unique = 'required|string|max:255|unique:roles,name';
        if ($this->editingId) {
            $unique .= ',' . $this->editingId;
        }
        return [
            'name'      => $unique,
            'guardName' => 'required|string|max:50',
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'Role name is required.',
            'name.unique'   => 'This role already exists.',
        ];
    }

    #[Computed]
    public function roles()
    {
        return Role::query()
            ->withCount(['permissions', 'users'])
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('name')
            ->paginate($this->perPage);
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->guardName = config('permission-manager.guard', 'web');
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $role = Role::findOrFail($id);

        $this->editingId = $id;
        $this->name      = $role->name;
        $this->guardName = $role->guard_name;
        $this->showModal = true;
        $this->resetValidation();
    }

    public function save(): void
    {
        $this->validate();

        if ($this->editingId) {
            Role::findOrFail($this->editingId)->update([
                'name'       => $this->name,
                'guard_name' => $this->guardName,
            ]);
            $msg = 'Role updated successfully.';
        } else {
            Role::create([
                'name'       => $this->name,
                'guard_name' => $this->guardName,
            ]);
            $msg = 'Role created successfully.';
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $this->closeModal();
        $this->resetPage();
        session()->flash('pm_success', $msg);
    }

    public function confirmDelete(int $id): void
    {
        // Protect super admin role
        $role = Role::find($id);
        if ($role?->name === config('permission-manager.super_admin_role')) {
            session()->flash('pm_error', 'Cannot delete the super admin role.');
            return;
        }
        $this->deletingId  = $id;
        $this->showConfirm = true;
    }

    public function delete(): void
    {
        if ($this->deletingId) {
            Role::destroy($this->deletingId);
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
            session()->flash('pm_success', 'Role deleted successfully.');
        }
        $this->showConfirm = false;
        $this->deletingId  = null;
        $this->resetPage();
    }

    public function closeModal(): void   { $this->showModal = false;   $this->resetForm(); }
    public function closeConfirm(): void { $this->showConfirm = false; $this->deletingId = null; }

    public function resetForm(): void
    {
        $this->editingId = null;
        $this->name      = '';
        $this->guardName = config('permission-manager.guard', 'web');
        $this->resetValidation();
    }

    public function updatedSearch(): void { $this->resetPage(); }

    public function render()
    {
        return view('permission-manager::roles.index', [
            'roles' => $this->roles,
        ]);
    }
}
