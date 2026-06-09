<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('pageTitle', 'Permission Manager') — Permission Manager</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Package CSS --}}
    @if(file_exists(public_path('vendor/permission-manager/permission-manager.css')))
        <link rel="stylesheet" href="{{ asset('vendor/permission-manager/permission-manager.css') }}">
    @else
        <style>{!! file_get_contents(__DIR__.'/../../css/permission-manager.css') !!}</style>
    @endif

    @livewireStyles
</head>
<body class="pm-body">

@php $activeMenu = View::yieldContent('activeMenu'); @endphp

<div class="pm-shell">

    {{-- ── Sidebar ──────────────────────────────────── --}}
    <aside class="pm-sidebar">

        <div class="pm-sidebar-logo">
            <div class="pm-sidebar-logo-icon">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <div>
                <div class="pm-sidebar-logo-text">Permission Manager</div>
                <div class="pm-sidebar-logo-sub">by CodeFlexTech</div>
            </div>
        </div>

        <nav class="pm-nav">
            <div class="pm-nav-label">Management</div>

            <a href="{{ route('permission-manager.index') }}"
               class="pm-nav-item {{ $activeMenu === 'dashboard' ? 'active' : '' }}">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>

            <a href="{{ route('permission-manager.permissions') }}"
               class="pm-nav-item {{ $activeMenu === 'permissions' ? 'active' : '' }}">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
                Permissions
            </a>

            <a href="{{ route('permission-manager.roles') }}"
               class="pm-nav-item {{ $activeMenu === 'roles' ? 'active' : '' }}">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                Roles
            </a>

            <a href="{{ route('permission-manager.users') }}"
               class="pm-nav-item {{ $activeMenu === 'users' ? 'active' : '' }}">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                User Roles
            </a>
        </nav>

        <div class="pm-sidebar-footer">
            <a href="https://codeflextech.com" target="_blank">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                </svg>
                codeflextech/permission-manager
            </a>
        </div>
    </aside>

    {{-- ── Main ──────────────────────────────────────── --}}
    <div class="pm-main">

        <header class="pm-topbar">
            <div>
                <p style="font-size:15px; font-weight:700; color:#0f172a;">
                    @yield('pageTitle', 'Permission Manager')
                </p>
            </div>
            <div style="margin-left:auto; display:flex; align-items:center; gap:12px;">
                <span style="font-size:12px; color:#64748b;">
                    {{ auth()->user()?->name ?? 'Guest' }}
                </span>
                <a href="{{ config('permission-manager.back_to_app_url', url('/')) }}"
                   style="font-size:12px; color:#4f46e5; text-decoration:none; display:flex; align-items:center; gap:4px;">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to App
                </a>
            </div>
        </header>

        <main class="pm-content">
            @yield('content')
        </main>

    </div>
</div>

@livewireScripts
</body>
</html>
