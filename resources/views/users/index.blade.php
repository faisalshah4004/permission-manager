{{-- permission-manager::users.index --}}
<div>

    {{-- Flash --}}
    @if(session('pm_success'))
    <div class="pm-alert pm-alert-success" x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,4000)">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('pm_success') }}
    </div>
    @endif

    {{-- Toolbar --}}
    <div class="pm-toolbar">
        <div style="display:flex;align-items:center;gap:10px;">
            <div class="pm-search-wrap">
                <svg class="pm-search-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input wire:model.live.debounce.350ms="search" type="text"
                       class="pm-search" placeholder="Search users...">
                @if($search)
                <button wire:click="$set('search','')" class="pm-clear-search">✕</button>
                @endif
            </div>
            <select wire:model.live="perPage" class="pm-select" style="width:80px;">
                <option value="10">10</option>
                <option value="15">15</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>
    </div>

    {{-- Results info --}}
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:10px;">
        <p style="font-size:13px; color:#64748b;">
            Showing <strong>{{ $this->users()->firstItem() ?? 0 }}–{{ $this->users()->lastItem() ?? 0 }}</strong> users
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
                    <th>User</th>
                    <th>Email</th>
                    <th>Assigned Roles</th>
                    <th style="width:100px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td style="color:#94a3b8;font-size:12px;">
                        {{ ($users->currentPage()-1)*$users->perPage()+$loop->iteration }}
                    </td>
                    <td>
                        <div style="display:flex;align-items:center;gap:9px;">
                            <div class="pm-avatar">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                            <p style="font-weight:600;font-size:13px;color:#0f172a;">{{ $user->name }}</p>
                        </div>
                    </td>
                    <td style="font-size:13px;color:#64748b;">{{ $user->email }}</td>
                    <td>
                        <div style="display:flex;flex-wrap:wrap;gap:5px;">
                            @forelse($user->roles as $role)
                                <span class="pm-badge {{ $role->name === config('permission-manager.super_admin_role') ? 'pm-badge-warning' : 'pm-badge-primary' }}">
                                    {{ $role->name }}
                                </span>
                            @empty
                                <span class="pm-badge pm-badge-default">No roles</span>
                            @endforelse
                        </div>
                    </td>
                    <td>
                        <button wire:click="openEdit({{ $user->id }})"
                                class="pm-btn pm-btn-secondary pm-btn-sm">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Edit Roles
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="pm-table-empty">
                        <svg fill="none" viewBox="0 0 24 24" stroke="#cbd5e1" style="width:40px;height:40px;margin:0 auto 8px;display:block;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        No users found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($this->users()->hasPages())
        <div class="pm-pagination">
            <span class="pm-pagination-info">Current Page: {{ $this->users()->currentPage() }}</span>
            <div class="pm-pagination-nav">
                {{ $this->users()->links() }}
            </div>
        </div>
    @endif

    {{-- ══ EDIT USER ROLES MODAL ══════════════════════════ --}}
    @if($showModal)
    <div class="pm-modal-backdrop" wire:click.self="closeModal">
        <div class="pm-modal">
            <div class="pm-modal-header">
                <div>
                    <div class="pm-modal-title">Edit Roles — {{ $editingUserName }}</div>
                    <div class="pm-modal-subtitle">Check the roles to assign to this user.</div>
                </div>
                <button wire:click="closeModal" class="pm-modal-close">✕</button>
            </div>

            <div class="pm-modal-body">

                {{-- Role checkboxes --}}
                <div style="display:flex;flex-direction:column;gap:4px;max-height:320px;overflow-y:auto;">
                    @foreach($allRoles as $role)
                    @php $isChecked = in_array((string)$role->id, $selectedRoles); @endphp
                    <label style="display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:8px;cursor:pointer;border:1px solid {{ $isChecked ? '#c7d2fe' : '#e2e8f0' }};background:{{ $isChecked ? '#ede9fe' : 'white' }};transition:all .15s;"
                           wire:click.prevent="
                               @if($isChecked)
                                   $set('selectedRoles', {{ json_encode(array_values(array_filter($selectedRoles, fn($r) => $r !== (string)$role->id))) }})
                               @else
                                   $set('selectedRoles', {{ json_encode(array_values(array_unique(array_merge($selectedRoles, [(string)$role->id])))) }})
                               @endif
                           ">
                        <input type="checkbox"
                               class="pm-checkbox"
                               {{ $isChecked ? 'checked' : '' }}>
                        <div style="flex:1;">
                            <p style="font-size:13px;font-weight:600;color:{{ $isChecked ? '#3730a3' : '#0f172a' }};">
                                {{ $role->name }}
                                @if($role->name === config('permission-manager.super_admin_role'))
                                    <span class="pm-badge pm-badge-warning" style="font-size:10px;margin-left:6px;">Super Admin</span>
                                @endif
                            </p>
                        </div>
                        @if($isChecked)
                        <svg fill="none" viewBox="0 0 24 24" stroke="#4f46e5" style="width:16px;height:16px;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        @endif
                    </label>
                    @endforeach
                </div>

                @if($allRoles->isEmpty())
                <p style="text-align:center;color:#64748b;font-size:13px;padding:20px 0;">
                    No roles available.
                    <a href="{{ route('permission-manager.roles') }}" style="color:#4f46e5;">Create roles first →</a>
                </p>
                @endif

            </div>

            <div class="pm-modal-footer">
                <span style="font-size:12px;color:#94a3b8;">{{ count($selectedRoles) }} role(s) selected</span>
                <button type="button" wire:click="closeModal" class="pm-btn pm-btn-secondary">Cancel</button>
                <button wire:click="save" class="pm-btn pm-btn-primary">
                    <span wire:loading.remove wire:target="save">Save Roles</span>
                    <span wire:loading wire:target="save">
                        <span style="display:flex;align-items:center;gap:6px;">
                        <span class="pm-spinner" style="width:12px;height:12px;border-color:rgba(255,255,255,.3);border-top-color:white;"></span>Saving...</span>
                    </span>
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
