{{-- permission-manager::roles.permissions --}}
<div>

    {{-- Flash --}}
    @if(session('pm_success'))
    <div class="pm-alert pm-alert-success" x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,4000)">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('pm_success') }}
    </div>
    @endif

    {{-- Page subheader --}}
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:20px;">
        <div style="display:flex;align-items:center;gap:12px;">
            <a href="{{ route('permission-manager.roles') }}" class="pm-btn pm-btn-secondary pm-btn-sm">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Roles
            </a>
            <div>
                <p style="font-size:15px;font-weight:700;color:#0f172a;">
                    {{ $role->name }}
                    <span style="font-size:12px;font-weight:400;color:#64748b;">— Assign Permissions</span>
                </p>
                <p style="font-size:12px;color:#64748b;">
                    {{ count($selectedPermissions) }} / {{ $totalPermissions }} permissions selected
                </p>
            </div>
        </div>

        <div style="display:flex;align-items:center;gap:8px;">
            {{-- Search --}}
            <div class="pm-search-wrap">
                <svg class="pm-search-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input wire:model.live.debounce.300ms="search" type="text"
                       class="pm-search" placeholder="Filter permissions...">
                @if($search)
                <button wire:click="$set('search','')" class="pm-clear-search">✕</button>
                @endif
            </div>
            <button wire:click="selectAll" class="pm-btn pm-btn-secondary pm-btn-sm">Select All</button>
            <button wire:click="deselectAll" class="pm-btn pm-btn-secondary pm-btn-sm">Deselect All</button>
            <button wire:click="save" class="pm-btn pm-btn-primary">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span wire:loading.remove wire:target="save">Save Permissions</span>
                <span wire:loading wire:target="save" style="display:flex;align-items:center;gap:6px;">
                    <span class="pm-spinner" style="width:12px;height:12px;border-color:rgba(255,255,255,.3);border-top-color:white;"></span>
                    Saving...
                </span>
            </button>
        </div>
    </div>

    {{-- Progress bar --}}
    @if($totalPermissions > 0)
    <div style="margin-bottom:20px;">
        <div style="background:#f1f5f9;border-radius:999px;height:6px;overflow:hidden;">
            <div style="height:100%;border-radius:999px;background:linear-gradient(90deg,#4f46e5,#7c3aed);transition:width .4s;width:{{ min(100, round((count($selectedPermissions)/$totalPermissions)*100)) }}%;"></div>
        </div>
        <p style="font-size:11px;color:#94a3b8;margin-top:4px;">
            {{ min(100, round((count($selectedPermissions)/max(1,$totalPermissions))*100)) }}% of all permissions assigned
        </p>
    </div>
    @endif

    {{-- Check if super admin --}}
    @if($role->name === config('permission-manager.super_admin_role'))
    <div class="pm-alert" style="background:#fef3c7;border-color:#fcd34d;color:#92400e;">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        Super Admin role bypasses all permission checks automatically. Individual assignments are optional.
    </div>
    @endif

    {{-- No permissions --}}
    @if(empty($groupedPermissions))
    <div style="text-align:center;padding:60px 20px;color:#64748b;">
        <svg fill="none" viewBox="0 0 24 24" stroke="#cbd5e1" style="width:48px;height:48px;margin:0 auto 12px;display:block;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
        <p style="font-weight:600;margin-bottom:4px;">No permissions found</p>
        <p style="font-size:13px;">
            @if($search) No permissions match "{{ $search }}".
            @else <a href="{{ route('permission-manager.permissions') }}" style="color:#4f46e5;">Create permissions first →</a>
            @endif
        </p>
    </div>
    @else

    {{-- Permission groups --}}
    @foreach($groupedPermissions as $groupName => $groupPerms)
    @php
        $groupIds    = collect($groupPerms)->pluck('id')->map(fn($id)=>(string)$id)->toArray();
        $allChecked  = !array_diff($groupIds, $selectedPermissions);
        $someChecked = !empty(array_intersect($groupIds, $selectedPermissions));
    @endphp

    <div class="pm-perm-group">

        {{-- Group header --}}
        <div class="pm-perm-group-header" wire:click="toggleGroup('{{ $groupName }}')">
            <div class="pm-perm-group-title">
                <input type="checkbox"
                       class="pm-checkbox"
                       wire:click.stop="toggleGroup('{{ $groupName }}')"
                       {{ $allChecked ? 'checked' : '' }}
                       style="{{ $someChecked && !$allChecked ? 'opacity:.5;' : '' }}">
                <span style="text-transform:capitalize;letter-spacing:.3px;">{{ $groupName }}</span>
                <span class="pm-badge {{ $allChecked ? 'pm-badge-success' : ($someChecked ? 'pm-badge-warning' : 'pm-badge-default') }}" style="font-size:10px;">
                    {{ count(array_intersect($groupIds, $selectedPermissions)) }}/{{ count($groupPerms) }}
                </span>
            </div>
            <div style="display:flex;align-items:center;gap:8px;">
                <span class="pm-perm-group-count">{{ count($groupPerms) }} permission{{ count($groupPerms)!==1?'s':'' }}</span>
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width:14px;height:14px;color:#94a3b8;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </div>

        {{-- Permission checkboxes --}}
        <div class="pm-perm-grid">
            @foreach($groupPerms as $permission)
            @php $isChecked = in_array((string)$permission->id, $selectedPermissions); @endphp
            <label class="pm-checkbox-wrap {{ $isChecked ? 'pm-perm-checked' : '' }}"
                   wire:click.prevent="togglePermission({{ $permission->id }})">
                <input type="checkbox"
                       class="pm-checkbox"
                       {{ $isChecked ? 'checked' : '' }}
                       wire:click.stop="togglePermission({{ $permission->id }})">
                <span class="pm-checkbox-label">
                    {{ implode('.', array_slice(explode('.', $permission->name), 1)) ?: $permission->name }}
                </span>
            </label>
            @endforeach
        </div>

    </div>
    @endforeach
    @endif

    {{-- Sticky save bar --}}
    <div style="position:sticky;bottom:0;background:white;border-top:1px solid #e2e8f0;padding:14px 0;margin-top:20px;display:flex;align-items:center;justify-content:space-between;z-index:10;">
        <p style="font-size:13px;color:#64748b;">
            <strong style="color:#0f172a;">{{ count($selectedPermissions) }}</strong> permissions selected
        </p>
        <button wire:click="save" class="pm-btn pm-btn-primary">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span wire:loading.remove wire:target="save">Save Permissions</span>
            <span wire:loading wire:target="save" style="display:flex;align-items:center;gap:6px;">
                <span class="pm-spinner" style="width:12px;height:12px;border-color:rgba(255,255,255,.3);border-top-color:white;"></span>
                Saving...
            </span>
        </button>
    </div>

</div>
