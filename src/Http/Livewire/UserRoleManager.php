<?php
declare(strict_types=1);

namespace CodeFlexTech\PermissionManager\Http\Livewire;

use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class UserRoleManager extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search  = '';
    public int    $perPage = 15;

    public bool $showModal = false;
    public ?int $editingUserId = null;

    public array  $selectedRoles    = [];
    public string $editingUserName  = '';

    #[Computed]
    public function users()
    {
        $model = config('permission-manager.user_model');

        return $model::query()
            ->with('roles')
            ->when($this->search, fn($q) =>
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%")
            )
            ->latest()
            ->paginate($this->perPage);
    }

    #[Computed]
    public function allRoles()
    {
        return Role::orderBy('name')->get(['id', 'name']);
    }

    public function openEdit(int $userId): void
    {
        $model = config('permission-manager.user_model');
        $user  = $model::with('roles')->findOrFail($userId);

        $this->editingUserId   = $userId;
        $this->editingUserName = $user->name;
        $this->selectedRoles   = $user->roles->pluck('id')->map(fn($id) => (string) $id)->toArray();
        $this->showModal       = true;
    }

    public function save(): void
    {
        $model = config('permission-manager.user_model');
        $user  = $model::findOrFail($this->editingUserId);

        $user->syncRoles($this->selectedRoles);
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->closeModal();
        session()->flash('pm_success', "Roles for \"{$this->editingUserName}\" updated successfully.");
    }

    public function closeModal(): void
    {
        $this->showModal       = false;
        $this->editingUserId   = null;
        $this->editingUserName = '';
        $this->selectedRoles   = [];
    }

    public function updatedSearch(): void { $this->resetPage(); }

    public function render()
    {
        return view('permission-manager::users.index', [
            'users'    => $this->users,
            'allRoles' => $this->allRoles,
        ]);
    }
}
