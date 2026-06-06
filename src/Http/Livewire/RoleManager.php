<?php
declare(strict_types=1);

namespace CodeFlexTech\PermissionManager\Http\Livewire;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\Paginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Class RoleManager
 *
 * @package   CodeFlexTech\PermissionManager\Http\Livewire
 *
 * @author    Faisal Shah <faisalshah4004@gmail.com>
 *
 * @copyright 2026 CodeFlexTech.com
 * @version   1.0
 */
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

    /**
     * Function rules
     *
     * @return string[]
     */
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

    /**
     * Function messages
     *
     * @return string[]
     */
    protected function messages(): array
    {
        return [
            'name.required' => 'Role name is required.',
            'name.unique'   => 'This role already exists.',
        ];
    }

    /**
     * Function roles
     *
     * @return \Illuminate\Pagination\Paginator
     */
    #[Computed]
    public function roles(): Paginator
    {
        return Role::query()
            ->withCount(['permissions', 'users'])
            ->when($this->search, fn($q) => $q->where('name', 'like', "%$this->search%"))
            ->orderBy('name')
            ->simplePaginate($this->perPage);
    }

    /**
     * Function openCreate
     */
    public function openCreate(): void
    {
        $this->resetForm();
        $this->guardName = config('permission-manager.guard', 'web');
        $this->showModal = true;
    }

    /**
     * Function openEdit
     *
     * @param int $id
     */
    public function openEdit(int $id): void
    {
        $role = Role::findOrFail($id);

        $this->editingId = $id;
        $this->name      = $role->name;
        $this->guardName = $role->guard_name;
        $this->showModal = true;
        $this->resetValidation();
    }

    /**
     * Function save
     */
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

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->closeModal();
        $this->resetPage();
        session()->flash('pm_success', $msg);
    }

    /**
     * Function confirmDelete
     *
     * @param int $id
     */
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

    /**
     * Function delete
     */
    public function delete(): void
    {
        if ($this->deletingId) {
            Role::destroy($this->deletingId);
            app()[PermissionRegistrar::class]->forgetCachedPermissions();
            session()->flash('pm_success', 'Role deleted successfully.');
        }
        $this->showConfirm = false;
        $this->deletingId  = null;
        $this->resetPage();
    }

    /**
     * Function closeModal
     */
    public function closeModal(): void   { $this->showModal = false;   $this->resetForm(); }

    /**
     * Function closeConfirm
     */
    public function closeConfirm(): void { $this->showConfirm = false; $this->deletingId = null; }

    /**
     * Function resetForm
     */
    public function resetForm(): void
    {
        $this->editingId = null;
        $this->name      = '';
        $this->guardName = config('permission-manager.guard', 'web');
        $this->resetValidation();
    }

    /**
     * Function updatedSearch
     */
    public function updatedSearch(): void { $this->resetPage(); }

    /**
     * Function render
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function render(): Factory | View
    {
        return view('permission-manager::roles.index', [
            'roles' => $this->roles,
        ]);
    }
}
