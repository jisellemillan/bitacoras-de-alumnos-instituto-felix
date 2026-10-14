<!DOCTYPE html>
<html lang="es-MX" x-data="{ sidebarOpen: window.innerWidth >= 768, mobileMenuOpen: false }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title . ' — ' : '' }}Monitor Psicopedagógico · Instituto Félix</title>
    <meta name="description" content="Sistema de gestión psicopedagógica - Instituto Félix de Jesús Rougier">

    {{-- Fuentes y CSS --}}
    @vite(['resources/css/main.css', 'resources/js/app.js'])

    {{-- Livewire styles --}}
    @livewireStyles
</head>
<body>

{{-- ── Sidebar overlay (mobile) ────────────────────────── --}}
<div class="sidebar-overlay"
     :class="{ active: mobileMenuOpen }"
     x-show="mobileMenuOpen"
     x-transition:enter="transition-opacity duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click="mobileMenuOpen = false"
     style="display:none"
></div>

{{-- ── Sidebar ─────────────────────────────────────────── --}}
<aside class="sidebar"
       :class="{ 'collapsed': !sidebarOpen && window.innerWidth >= 768, 'mobile-open': mobileMenuOpen }"
       aria-label="Menú principal">

    {{-- Brand --}}
    <a href="{{ route('dashboard') }}" class="sidebar-brand" tabindex="0">
        <div class="sidebar-brand-logo" aria-hidden="true">F</div>
        <span class="sidebar-brand-name">Monitor<br>Psicopedagógico</span>
    </a>

    {{-- Navigation --}}
    <nav class="sidebar-nav" role="navigation">

        {{-- Principal --}}
        <span class="sidebar-section-label">Principal</span>

        <a href="{{ route('dashboard') }}"
           class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
           title="Inicio">
            <span class="sidebar-link-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            </span>
            <span class="sidebar-link-text">Inicio</span>
        </a>

        @if(auth()->user()->hasSectionPermission('alumnos'))
        <a href="{{ route('students.index') }}"
           class="sidebar-link {{ request()->routeIs('students.*') ? 'active' : '' }}"
           title="Alumnos">
            <span class="sidebar-link-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </span>
            <span class="sidebar-link-text">Alumnos</span>
        </a>
        @endif

        {{-- Gestión --}}
        <span class="sidebar-section-label" style="margin-top:.75rem">Gestión</span>

        @if(auth()->user()->hasSectionPermission('acuerdos'))
        <a href="{{ route('agreements.index') }}"
           class="sidebar-link {{ request()->routeIs('agreements.*') ? 'active' : '' }}"
           title="Acuerdos">
            <span class="sidebar-link-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </span>
            <span class="sidebar-link-text">Acuerdos</span>
        </a>
        @endif

        @if(auth()->user()->hasSectionPermission('calendario'))
        <a href="{{ route('calendar.index') }}"
           class="sidebar-link {{ request()->routeIs('calendar.*') ? 'active' : '' }}"
           title="Calendario">
            <span class="sidebar-link-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke-linecap="round" stroke-linejoin="round"/><line x1="16" y1="2" x2="16" y2="6" stroke-linecap="round"/><line x1="8" y1="2" x2="8" y2="6" stroke-linecap="round"/><line x1="3" y1="10" x2="21" y2="10" stroke-linecap="round"/></svg>
            </span>
            <span class="sidebar-link-text">Calendario</span>
        </a>
        @endif

        @if(auth()->user()->hasSectionPermission('reportes'))
        <a href="{{ route('reports.index') }}"
           class="sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }}"
           title="Reportes">
            <span class="sidebar-link-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </span>
            <span class="sidebar-link-text">Reportes</span>
        </a>
        @endif

        {{-- Solicitudes de acceso --}}
        @if(auth()->user()->hasSectionPermission('solicitudes_acceso'))
        <a href="{{ route('access-requests.index') }}"
           class="sidebar-link {{ request()->routeIs('access-requests.*') ? 'active' : '' }}"
           title="Solicitudes de acceso">
            <span class="sidebar-link-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
            </span>
            <span class="sidebar-link-text">Accesos</span>
        </a>
        @endif

        {{-- Administración --}}
        @if(auth()->user()->hasSectionPermission('usuarios') || auth()->user()->hasSectionPermission('configuracion'))
        <span class="sidebar-section-label" style="margin-top:.75rem">Administración</span>

        @if(auth()->user()->hasSectionPermission('usuarios'))
        <a href="{{ route('users.index') }}"
           class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}"
           title="Gestión de Usuarios">
            <span class="sidebar-link-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </span>
            <span class="sidebar-link-text">Gestión de Usuarios</span>
        </a>
        @endif

        @if(auth()->user()->hasSectionPermission('configuracion'))
        <a href="{{ route('groups.index') }}"
           class="sidebar-link {{ request()->routeIs('groups.*') ? 'active' : '' }}"
           title="Gestión de Grupos">
            <span class="sidebar-link-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            </span>
            <span class="sidebar-link-text">Gestión de Grupos</span>
        </a>

        <a href="{{ route('settings.index') }}"
           class="sidebar-link {{ request()->routeIs('settings.*') ? 'active' : '' }}"
           title="Configuración">
            <span class="sidebar-link-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </span>
            <span class="sidebar-link-text">Configuración</span>
        </a>
        @endif
        @endif

    </nav>

    {{-- User footer --}}
    <div class="sidebar-footer">
        <a href="{{ route('profile.edit') }}" class="sidebar-user" title="{{ auth()->user()->name }}">
            <div class="sidebar-avatar" aria-hidden="true">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name">{{ auth()->user()->name }}</div>
                <div class="sidebar-user-role">{{ auth()->user()->role->label() }}</div>
            </div>
        </a>
    </div>

    {{-- Botón colapsar (desktop) --}}
    <button class="sidebar-collapse-btn"
            @click="sidebarOpen = !sidebarOpen"
            :aria-label="sidebarOpen ? 'Colapsar menú' : 'Expandir menú'"
            aria-controls="sidebar"
            x-show="window.innerWidth >= 768">
        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
             :style="sidebarOpen ? '' : 'transform:rotate(180deg)'">
            <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
    </button>

</aside>

{{-- ── Header ──────────────────────────────────────────── --}}
<header class="app-header" :class="{ 'sidebar-collapsed': !sidebarOpen && window.innerWidth >= 768 }">
    <div class="header-left">
        {{-- Hamburger (mobile) --}}
        <button class="header-hamburger"
                @click="mobileMenuOpen = !mobileMenuOpen"
                aria-label="Abrir menú"
                aria-expanded="false">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>

        <h1 class="header-page-title">{{ $title ?? 'Monitor Psicopedagógico' }}</h1>
    </div>

    <div class="header-actions">
        {{-- Ciclo activo --}}
        @php $cycle = \App\Models\SchoolCycle::current(); @endphp
        @if($cycle)
        <span class="header-cycle-badge">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            Ciclo {{ $cycle->name }}
        </span>
        @endif

        {{-- Campana de alertas --}}
        @cannot('isAdminTecnico')
        <livewire:shared.notifications-bell />
        @endcannot

        {{-- Menú perfil --}}
        <div x-data="{ open: false }" class="relative">
            <button class="header-profile-btn" @click="open = !open" aria-label="Menú de usuario" aria-expanded="false">
                <div class="header-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
            </button>
            <div x-show="open" @click.outside="open = false" x-transition
                 style="position:absolute;right:0;top:calc(100% + 8px);width:220px;background:white;border:1px solid var(--color-border);border-radius:var(--radius-xl);box-shadow:var(--shadow-lg);z-index:60;overflow:hidden;display:none">
                <div style="padding:var(--space-3) var(--space-4);border-bottom:1px solid var(--color-border)">
                    <div style="font-size:var(--text-sm);font-weight:600;color:var(--color-text)">{{ auth()->user()->name }}</div>
                    <div style="font-size:var(--text-xs);color:var(--color-muted)">{{ auth()->user()->email }}</div>
                </div>
                <a href="{{ route('profile.edit') }}" style="display:flex;align-items:center;gap:var(--space-3);padding:var(--space-3) var(--space-4);font-size:var(--text-sm);color:var(--color-text);text-decoration:none;transition:background-color var(--transition-fast)">
                    Mi perfil
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" style="width:100%;display:flex;align-items:center;gap:var(--space-3);padding:var(--space-3) var(--space-4);font-size:var(--text-sm);color:var(--color-error);background:none;border:none;cursor:pointer;border-top:1px solid var(--color-border);text-align:left">
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>

{{-- ── Contenido principal ──────────────────────────────── --}}
<main class="app-main" :class="{ 'sidebar-collapsed': !sidebarOpen && window.innerWidth >= 768 }">
    <div class="page-container">

        {{-- Flash messages / toasts via Livewire --}}
        <livewire:shared.toast-container />

        {{ $slot }}
    </div>
</main>

@livewireScripts
</body>
</html>
