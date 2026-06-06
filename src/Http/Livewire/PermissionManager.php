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
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

/**
 * Class PermissionManager
 *
 * @package   CodeFlexTech\PermissionManager\Http\Livewire
 *
 * @author    Faisal Shah <faisalshah4004@gmail.com>
 *
 * @copyright 2026 CodeFlexTech.com
 * @version   1.0
 */
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

    /**
     * Function rules
     *
     * @return string[]
     */
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

    /**
     * Function messages
     *
     * @return string[]
     */
    protected function messages(): array
    {
        return [
            'name.required' => 'Permission name is required.',
            'name.unique'   => 'This permission already exists.',
        ];
    }

    /**
     * Function permissions
     *
     * @return \Illuminate\Pagination\Paginator
     */
    #[Computed]
    public function permissions(): Paginator
    {
        return Permission::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%$this->search%"))
            ->when($this->group,  fn($q) => $q->where('name', 'like', "$this->group.%"))
            ->orderBy('name')
            ->simplePaginate($this->perPage);
    }

    /**
     * Function groups
     *
     * @return array
     */
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
        $permission = Permission::findOrFail($id);

        $this->editingId = $id;
        $this->name      = $permission->name;
        $this->guardName = $permission->guard_name;
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
        $this->deletingId  = $id;
        $this->showConfirm = true;
    }

    /**
     * Function delete
     */
    public function delete(): void
    {
        if ($this->deletingId) {
            Permission::destroy($this->deletingId);
            app()[PermissionRegistrar::class]->forgetCachedPermissions();
            session()->flash('pm_success', 'Permission deleted successfully.');
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
     * Function updatedGroup
     */
    public function updatedGroup(): void  { $this->resetPage(); }

    /**
     * Function render
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function render(): Factory | View
    {
        return view('permission-manager::permissions.index', [
            'permissions' => $this->permissions,
            'groups'      => $this->groups,
        ]);
    }
}
