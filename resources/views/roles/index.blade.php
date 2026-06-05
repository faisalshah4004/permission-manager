{{-- permission-manager::roles.index --}}
<div>

    {{-- Flash --}}
    @if(session('pm_success'))
    <div class="pm-alert pm-alert-success" x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,4000)">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('pm_success') }}
    </div>
    @endif
    @if(session('pm_error'))
    <div class="pm-alert pm-alert-error">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('pm_error') }}
    </div>
    @endif

    {{-- Toolbar --}}
    <div class="pm-toolbar">
        <div style="display:flex;align-items:center;gap:10px;">
            <div class="pm-search-wrap">
                <svg class="pm-search-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input wire:model.live.debounce.350ms="search" type="text" class="pm-search" placeholder="Search roles...">
                @if($search)
                <button wire:click="$set('search','')" class="pm-clear-search">✕</button>
                @endif
            </div>
            <select wire:model.live="perPage" class="pm-select" style="width:80px;">
                <option value="10">10</option>
                <option value="15">15</option>
                <option value="25">25</option>
            </select>
        </div>
        <button wire:click="openCreate" class="pm-btn pm-btn-primary">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Role
        </button>
    </div>

    {{-- Results --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
        <p style="font-size:13px;color:#64748b;">
            Showing <strong>{{ $roles->firstItem() ?? 0 }}–{{ $roles->lastItem() ?? 0 }}</strong>
            of <strong>{{ $roles->total() }}</strong> roles
        </p>
        <div wire:loading style="display:flex;align-items:center;gap:8px;font-size:12px;color:#64748b;">
            <div class="pm-spinner"></div> Loading...
        </div>
    </div>

    {{-- Table --}}
    <div class="pm-table-wrap">
        <table class="pm-table">
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th>Role Name</th>
                    <th>Guard</th>
                    <th>Permissions</th>
                    <th>Users</th>
                    <th style="width:200px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roles as $role)
                @php $isSuperAdmin = $role->name === config('permission-manager.super_admin_role'); @endphp
                <tr>
                    <td style="color:#94a3b8;font-size:12px;">
                        {{ ($roles->currentPage()-1)*$roles->perPage()+$loop->iteration }}
                    </td>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div style="width:30px;height:30px;border-radius:8px;background:#ede9fe;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;color:#4f46e5;flex-shrink:0;">
                                {{ strtoupper(substr($role->name, 0, 2)) }}
                            </div>
                            <div>
                                <p style="font-weight:700;font-size:13px;color:#0f172a;">{{ $role->name }}</p>
                                @if($isSuperAdmin)
                                    <span class="pm-badge pm-badge-warning" style="font-size:10px;">Super Admin</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td><span class="pm-badge pm-badge-default">{{ $role->guard_name }}</span></td>
                    <td>
                        @if($isSuperAdmin)
                            <span class="pm-badge pm-badge-success">All permissions</span>
                        @else
                            <span class="pm-badge pm-badge-primary">{{ $role->permissions_count }}</span>
                        @endif
                    </td>
                    <td><span class="pm-badge pm-badge-default">{{ $role->users_count }}</span></td>
                    <td>
                        <div style="display:flex;gap:6px;flex-wrap:wrap;">
                            {{-- Manage permissions --}}
                            <a href="{{ route('permission-manager.role-permissions', $role->id) }}"
                               class="pm-btn pm-btn-secondary pm-btn-sm">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                Permissions
                            </a>
                            {{-- Edit --}}
                            <button wire:click="openEdit({{ $role->id }})"
                                    class="pm-btn pm-btn-secondary pm-btn-sm pm-btn-icon" title="Edit">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            {{-- Delete (protect super admin) --}}
                            @if(!$isSuperAdmin)
                            <button wire:click="confirmDelete({{ $role->id }})"
                                    class="pm-btn pm-btn-sm pm-btn-icon" style="background:#fee2e2;color:#dc2626;border-color:#fca5a5;" title="Delete">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="pm-table-empty">
                        <svg fill="none" viewBox="0 0 24 24" stroke="#cbd5e1" style="width:40px;height:40px;margin:0 auto 8px;display:block;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        No roles found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($roles->hasPages())
    <div class="pm-pagination">
        <span class="pm-pagination-info">Page {{ $roles->currentPage() }} of {{ $roles->lastPage() }}</span>
        {{ $roles->links() }}
    </div>
    @endif

    {{-- ══ CREATE / EDIT MODAL ══════════════════════════ --}}
    @if($showModal)
    <div class="pm-modal-backdrop" wire:click.self="closeModal">
        <div class="pm-modal">
            <div class="pm-modal-header">
                <div>
                    <div class="pm-modal-title">{{ $editingId ? 'Edit Role' : 'Add Role' }}</div>
                    <div class="pm-modal-subtitle">{{ $editingId ? 'Update role name.' : 'Create a new role for your application.' }}</div>
                </div>
                <button wire:click="closeModal" class="pm-modal-close">✕</button>
            </div>
            <form wire:submit="save">
                <div class="pm-modal-body">

                    <div class="pm-form-group">
                        <label class="pm-label">Role Name <span>*</span></label>
                        <input wire:model="name" type="text"
                               class="pm-input {{ $errors->has('name') ? 'error' : '' }}"
                               placeholder="e.g. admin, editor, accountant">
                        @error('name') <p class="pm-error-msg">⚠ {{ $message }}</p> @enderror
                        <p class="pm-hint">Use lowercase with underscores: <code>super_admin</code>, <code>sales_person</code></p>
                    </div>

                    <div class="pm-form-group">
                        <label class="pm-label">Guard Name <span>*</span></label>
                        <select wire:model="guardName" class="pm-select">
                            <option value="web">web</option>
                            <option value="api">api</option>
                        </select>
                    </div>

                </div>
                <div class="pm-modal-footer">
                    <button type="button" wire:click="closeModal" class="pm-btn pm-btn-secondary">Cancel</button>
                    <button type="submit" class="pm-btn pm-btn-primary">
                        <span wire:loading.remove wire:target="save">{{ $editingId ? 'Update Role' : 'Create Role' }}</span>
                        <span wire:loading wire:target="save" style="display:flex;align-items:center;gap:6px;">
                            <span class="pm-spinner" style="width:12px;height:12px;"></span> Saving...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- ══ DELETE CONFIRM MODAL ═══════════════════════════ --}}
    @if($showConfirm)
    <div class="pm-modal-backdrop" wire:click.self="closeConfirm">
        <div class="pm-modal" style="max-width:380px;">
            <div class="pm-modal-body" style="text-align:center;padding:28px 24px;">
                <div style="width:52px;height:52px;border-radius:50%;background:#fee2e2;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
                    <svg fill="none" viewBox="0 0 24 24" stroke="#dc2626" style="width:26px;height:26px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
                <p style="font-size:15px;font-weight:700;margin-bottom:6px;">Delete Role?</p>
                <p style="font-size:13px;color:#64748b;margin-bottom:20px;">
                    All users assigned to this role will lose it. This cannot be undone.
                </p>
                <div style="display:flex;gap:10px;">
                    <button wire:click="closeConfirm" class="pm-btn pm-btn-secondary" style="flex:1;">Cancel</button>
                    <button wire:click="delete" class="pm-btn pm-btn-danger" style="flex:1;">
                        <span wire:loading.remove wire:target="delete">Yes, Delete</span>
                        <span wire:loading wire:target="delete">Deleting...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>
