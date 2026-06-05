<?php
declare(strict_types=1);

namespace CodeFlexTech\PermissionManager\Http\Livewire;

use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;

class PermissionManager extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search  = '';
    public string $group   = '';
    public int    $perPage = 15;

    public bool $showModal   = false;
    public bool $showConfirm = false;
    public ?int $editingId   = null;
    public ?int $deletingId  = null;

    public string $name       = '';
    public string $guardName  = 'web';

    protected function rules(): array
    {
        $unique = 'required|string|max:255|unique:permissions,name';
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
            'name.required' => 'Permission name is required.',
            'name.unique'   => 'This permission already exists.',
        ];
    }

    #[Computed]
    public function permissions()
    {
        return Permission::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->group,  fn($q) => $q->where('name', 'like', "{$this->group}.%"))
            ->orderBy('name')
            ->paginate($this->perPage);
    }

    #[Computed]
    public function groups(): array
    {
        $configured = config('permission-manager.permission_groups', []);
        if (!empty($configured)) {
            return $configured;
        }

        // Auto-detect groups from permission names (prefix before '.')
        return Permission::select('name')
            ->get()
            ->map(fn($p) => explode('.', $p->name)[0])
            ->unique()
            ->sort()
            ->values()
            ->toArray();
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->guardName = config('permission-manager.guard', 'web');
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $permission = Permission::findOrFail($id);

        $this->editingId = $id;
        $this->name      = $permission->name;
        $this->guardName = $permission->guard_name;
        $this->showModal = true;
        $this->resetValidation();
    }

    public function save(): void
    {
        $this->validate();

        if ($this->editingId) {
            Permission::findOrFail($this->editingId)->update([
                'name'       => $this->name,
                'guard_name' => $this->guardName,
            ]);
            $msg = 'Permission updated successfully.';
        } else {
            Permission::create([
                'name'       => $this->name,
                'guard_name' => $this->guardName,
            ]);
            $msg = 'Permission created successfully.';
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $this->closeModal();
        $this->resetPage();
        session()->flash('pm_success', $msg);
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId  = $id;
        $this->showConfirm = true;
    }

    public function delete(): void
    {
        if ($this->deletingId) {
            Permission::destroy($this->deletingId);
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
            session()->flash('pm_success', 'Permission deleted successfully.');
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
    public function updatedGroup(): void  { $this->resetPage(); }

    public function render()
    {
        return view('permission-manager::permissions.index', [
            'permissions' => $this->permissions,
            'groups'      => $this->groups,
        ]);
    }
}
