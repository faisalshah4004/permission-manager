<?php
declare(strict_types = 1);
namespace CodeFlexTech\PermissionManager\Http\Livewire;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Class UserRoleManager
 *
 * @package   CodeFlexTech\PermissionManager\Http\Livewire
 *
 * @author    Faisal Shah <faisalshah4004@gmail.com>
 *
 * @copyright 2026 CodeFlexTech.com
 * @version   1.0
 */
class UserRoleManager extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search  = '';
    public int    $perPage = 15;
    public bool $showModal     = false;
    public ?int $editingUserId = null;
    public array  $selectedRoles   = [];
    public string $editingUserName = '';

    /**
     * Function users
     *
     * @return mixed
     */
    #[Computed]
    public function users(): mixed
    {
        $model = config('permission-manager.user_model');

        return $model::query()->with('roles')->when($this->search, fn($q) => $q->where('name', 'like', "%$this->search%")->orWhere('email', 'like', "%$this->search%"))->latest()->simplePaginate($this->perPage);
    }

    /**
     * Function allRoles
     *
     * @return mixed
     */
    #[Computed]
    public function allRoles(): mixed
    {
        return Role::orderBy('name')->get(['id', 'name']);
    }

    /**
     * Function openEdit
     *
     * @param int $userId
     */
    public function openEdit(int $userId): void
    {
        $model = config('permission-manager.user_model');
        $user  = $model::with('roles')->findOrFail($userId);
        $this->editingUserId   = $userId;
        $this->editingUserName = $user->name;
        $this->selectedRoles   = $user->roles->pluck('id')->map(fn($id) => (string)$id)->toArray();
        $this->showModal       = true;
    }

    /**
     * Function save
     */
    public function save(): void
    {
        $model = config('permission-manager.user_model');
        $user  = $model::findOrFail($this->editingUserId);
        $roles = Role::whereIn('id', $this->selectedRoles)->get();
        $user->syncRoles($roles);
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->closeModal();
        session()->flash('pm_success', "Roles for \"$this->editingUserName\" updated successfully.");
    }

    /**
     * Function closeModal
     */
    public function closeModal(): void
    {
        $this->showModal       = false;
        $this->editingUserId   = null;
        $this->editingUserName = '';
        $this->selectedRoles   = [];
    }

    /**
     * Function updatedSearch
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Function render
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function render(): Factory | View
    {
        return view('permission-manager::users.index', [
            'users'    => $this->users,
            'allRoles' => $this->allRoles,
        ]);
    }
}
