{{-- permission-manager::permissions.index --}}
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
        <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">

            {{-- Search --}}
            <div class="pm-search-wrap">
                <svg class="pm-search-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input wire:model.live.debounce.350ms="search" type="text"
                       class="pm-search" placeholder="Search permissions...">
                @if($search)
                <button wire:click="$set('search','')" class="pm-clear-search">✕</button>
                @endif
            </div>

            {{-- Group filter --}}
            <select wire:model.live="group" class="pm-select" style="width:160px;">
                <option value="">All Groups</option>
                @foreach($groups as $g)
                    <option value="{{ $g }}">{{ ucfirst($g) }}</option>
                @endforeach
            </select>

            {{-- Per page --}}
            <select wire:model.live="perPage" class="pm-select" style="width:80px;">
                <option value="10">10</option>
                <option value="15">15</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>

        @if(config('permission-manager.allow_create_permissions', true))
        <button wire:click="openCreate" class="pm-btn pm-btn-primary">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Permission
        </button>
        @endif
    </div>

    {{-- Results info --}}
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:10px;">
        <p style="font-size:13px; color:#64748b;">
            Showing <strong>{{ $permissions->firstItem() ?? 0 }}–{{ $permissions->lastItem() ?? 0 }}</strong> permissions
        </p>

        <div wire:loading wire:target="search">
            <div style="display:flex; align-items:center; gap:8px; font-size:12px; color:#64748b;">
                <div class="pm-spinner"></div> Loading...
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="pm-table-wrap">
        <table class="pm-table">
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th>Permission Name</th>
                    <th>Group</th>
                    <th>Guard</th>
                    <th>Roles</th>
                    <th style="width:120px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($permissions as $permission)
                <tr>
                    <td style="color:#94a3b8; font-size:12px;">
                        {{ ($permissions->currentPage()-1) * $permissions->perPage() + $loop->iteration }}
                    </td>
                    <td>
                        <span style="font-family:monospace; font-size:13px; font-weight:600; color:#1e1b4b; background:#ede9fe; padding:2px 8px; border-radius:4px;">
                            {{ $permission->name }}
                        </span>
                    </td>
                    <td>
                        @php $group = explode('.', $permission->name)[0]; @endphp
                        <span class="pm-badge pm-badge-default">{{ $group }}</span>
                    </td>
                    <td>
                        <span class="pm-badge pm-badge-default">{{ $permission->guard_name }}</span>
                    </td>
                    <td>
                        <span class="pm-badge pm-badge-primary">
                            {{ $permission->roles->count() }} role(s)
                        </span>
                    </td>
                    <td>
                        <div style="display:flex; gap:6px;">
                            <button wire:click="openEdit({{ $permission->id }})"
                                    class="pm-btn pm-btn-secondary pm-btn-sm pm-btn-icon" title="Edit">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <button wire:click="confirmDelete({{ $permission->id }})"
                                    class="pm-btn pm-btn-sm pm-btn-icon" style="background:#fee2e2; color:#dc2626; border-color:#fca5a5;" title="Delete">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="pm-table-empty">
                        <svg fill="none" viewBox="0 0 24 24" stroke="#cbd5e1" style="width:40px;height:40px;margin:0 auto 8px;display:block;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                        No permissions found.
                        @if($search) Try a different search term. @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($permissions->hasPages())
        <div class="pm-pagination">
            <span class="pm-pagination-info">Current Page: {{ $permissions->currentPage() }}</span>
            <div class="pm-pagination-nav">
                {{ $permissions->links() }}
            </div>
        </div>
    @endif

    {{-- ══ CREATE / EDIT MODAL ══════════════════════════ --}}
    @if($showModal)
    <div class="pm-modal-backdrop" wire:click.self="closeModal">
        <div class="pm-modal">
            <div class="pm-modal-header">
                <div>
                    <div class="pm-modal-title">{{ $editingId ? 'Edit Permission' : 'Add Permission' }}</div>
                    <div class="pm-modal-subtitle">
                        {{ $editingId ? 'Update permission name or guard.' : 'Use dot notation: module.action (e.g. clients.create)' }}
                    </div>
                </div>
                <button wire:click="closeModal" class="pm-modal-close">✕</button>
            </div>
            <form wire:submit="save">
                <div class="pm-modal-body">

                    {{-- Name --}}
                    <div class="pm-form-group">
                        <label class="pm-label">Permission Name <span>*</span></label>
                        <input wire:model="name" type="text"
                               class="pm-input {{ $errors->has('name') ? 'error' : '' }}"
                               placeholder="e.g. clients.create">
                        @error('name') <p class="pm-error-msg">⚠ {{ $message }}</p> @enderror
                        <p class="pm-hint">Convention: <code>module.action</code> — e.g. <code>invoices.delete</code>, <code>reports.view</code></p>
                    </div>

                    {{-- Guard --}}
                    <div class="pm-form-group">
                        <label class="pm-label">Guard Name <span>*</span></label>
                        <select wire:model="guardName" class="pm-select {{ $errors->has('guardName') ? 'error' : '' }}">
                            <option value="web">web</option>
                            <option value="api">api</option>
                        </select>
                        @error('guardName') <p class="pm-error-msg">⚠ {{ $message }}</p> @enderror
                    </div>

                </div>
                <div class="pm-modal-footer">
                    <button type="button" wire:click="closeModal" class="pm-btn pm-btn-secondary">Cancel</button>
                    <button type="submit" class="pm-btn pm-btn-primary">
                        <span wire:loading.remove wire:target="save">
                            {{ $editingId ? 'Update' : 'Create Permission' }}
                        </span>
                        <span wire:loading wire:target="save">
                            <span style="display:flex;align-items:center;gap:6px;">
                            <span class="pm-spinner" style="width:12px;height:12px;"></span> Saving...
                        </span>
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
            <div class="pm-modal-body" style="text-align:center; padding:28px 24px;">
                <div style="width:52px;height:52px;border-radius:50%;background:#fee2e2;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
                    <svg fill="none" viewBox="0 0 24 24" stroke="#dc2626" style="width:26px;height:26px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
                <p style="font-size:15px;font-weight:700;color:#0f172a;margin-bottom:6px;">Delete Permission?</p>
                <p style="font-size:13px;color:#64748b;margin-bottom:20px;">
                    This will remove the permission from all roles. This cannot be undone.
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
