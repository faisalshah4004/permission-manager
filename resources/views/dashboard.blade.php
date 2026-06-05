{{-- permission-manager::dashboard --}}

@php
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

$userModel     = config('permission-manager.user_model');
$superAdminRole= config('permission-manager.super_admin_role');

$recentRoles = Role::withCount('permissions')->latest()->take(5)->get();
$recentPerms = Permission::latest()->take(8)->get();

// Auto-detected groups
$groups = Permission::all()
    ->map(fn($p) => explode('.', $p->name)[0])
    ->unique()->sort()->values();
@endphp

{{-- Stats --}}
<div class="pm-stats">

    <div class="pm-stat-card">
        <div class="pm-stat-icon" style="background:#ede9fe;">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#4f46e5" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
        </div>
        <div class="pm-stat-label">Total Roles</div>
        <div class="pm-stat-value">{{ $stats['roles'] }}</div>
    </div>

    <div class="pm-stat-card">
        <div class="pm-stat-icon" style="background:#dbeafe;">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#2563eb" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
            </svg>
        </div>
        <div class="pm-stat-label">Permissions</div>
        <div class="pm-stat-value">{{ $stats['permissions'] }}</div>
    </div>

    <div class="pm-stat-card">
        <div class="pm-stat-icon" style="background:#d1fae5;">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#059669" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <div class="pm-stat-label">Total Users</div>
        <div class="pm-stat-value">{{ $stats['users'] }}</div>
    </div>

    <div class="pm-stat-card">
        <div class="pm-stat-icon" style="background:#fef3c7;">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#d97706" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
        </div>
        <div class="pm-stat-label">Groups</div>
        <div class="pm-stat-value">{{ $groups->count() }}</div>
    </div>

</div>

{{-- Quick actions --}}
<div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:24px;">
    <a href="{{ route('permission-manager.permissions') }}" class="pm-card" style="text-decoration:none; display:flex; align-items:center; gap:14px; padding:16px; transition: box-shadow .15s;" onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,.1)'" onmouseout="this.style.boxShadow=''">
        <div class="pm-stat-icon" style="background:#ede9fe; margin-bottom:0; flex-shrink:0;">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#4f46e5" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
        </div>
        <div>
            <div style="font-size:13px; font-weight:700; color:#0f172a;">Manage Permissions</div>
            <div style="font-size:12px; color:#64748b;">Create, edit and delete permissions</div>
        </div>
    </a>
    <a href="{{ route('permission-manager.roles') }}" class="pm-card" style="text-decoration:none; display:flex; align-items:center; gap:14px; padding:16px; transition: box-shadow .15s;" onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,.1)'" onmouseout="this.style.boxShadow=''">
        <div class="pm-stat-icon" style="background:#dbeafe; margin-bottom:0; flex-shrink:0;">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#2563eb" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        </div>
        <div>
            <div style="font-size:13px; font-weight:700; color:#0f172a;">Manage Roles</div>
            <div style="font-size:12px; color:#64748b;">Create roles and assign permissions</div>
        </div>
    </a>
    <a href="{{ route('permission-manager.users') }}" class="pm-card" style="text-decoration:none; display:flex; align-items:center; gap:14px; padding:16px; transition: box-shadow .15s;" onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,.1)'" onmouseout="this.style.boxShadow=''">
        <div class="pm-stat-icon" style="background:#d1fae5; margin-bottom:0; flex-shrink:0;">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#059669" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <div>
            <div style="font-size:13px; font-weight:700; color:#0f172a;">Assign User Roles</div>
            <div style="font-size:12px; color:#64748b;">Assign and revoke roles per user</div>
        </div>
    </a>
    <div class="pm-card" style="display:flex; align-items:center; gap:14px; padding:16px;">
        <div class="pm-stat-icon" style="background:#fef3c7; margin-bottom:0; flex-shrink:0;">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#d97706" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
        </div>
        <div>
            <div style="font-size:13px; font-weight:700; color:#0f172a;">Permission Groups</div>
            <div style="font-size:12px; color:#64748b;">{{ $groups->implode(', ') ?: 'No groups yet' }}</div>
        </div>
    </div>
</div>

{{-- Recent roles --}}
<div class="pm-card">
    <div class="pm-card-header">
        <span class="pm-card-title">Recent Roles</span>
        <a href="{{ route('permission-manager.roles') }}" style="font-size:12px; color:#4f46e5; text-decoration:none;">View all →</a>
    </div>
    <div class="pm-table-wrap" style="border:none; border-radius:0;">
        <table class="pm-table">
            <thead>
                <tr>
                    <th>Role Name</th>
                    <th>Guard</th>
                    <th>Permissions</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentRoles as $role)
                <tr>
                    <td>
                        <span style="font-weight:600;">{{ $role->name }}</span>
                        @if($role->name === $superAdminRole)
                            <span class="pm-badge pm-badge-warning" style="margin-left:6px;">Super Admin</span>
                        @endif
                    </td>
                    <td><span class="pm-badge pm-badge-default">{{ $role->guard_name }}</span></td>
                    <td><span class="pm-badge pm-badge-primary">{{ $role->permissions_count }}</span></td>
                    <td>
                        <a href="{{ route('permission-manager.role-permissions', $role->id) }}"
                           class="pm-btn pm-btn-secondary pm-btn-sm">
                            Manage Permissions
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="pm-table-empty">No roles yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
