<div>
    {{-- Encabezado de página --}}
    <div class="page-header">
        <div class="page-header-info">
            <h1 class="page-title">Panel Principal</h1>
            <p class="text-muted">Resumen general del ciclo escolar activo.</p>
        </div>
    </div>

    {{-- Tarjetas de indicadores --}}
    @if($totalAlumnos !== null || $totalGrupos !== null)
    <div class="dashboard-stats">

        @if($totalAlumnos !== null)
        <a href="{{ route('students.index') }}" class="stat-card stat-card--link" title="Ir a Alumnos">
            <div class="stat-card-icon green">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                     stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>
            <div class="stat-card-info">
                <div class="stat-card-value">{{ $totalAlumnos }}</div>
                <div class="stat-card-label">Total de alumnos</div>
                <div class="stat-card-link-hint">Ver todos →</div>
            </div>
        </a>
        @endif

        @if($totalGrupos !== null)
        <a href="{{ route('groups.index') }}" class="stat-card stat-card--link " title="Ir a Grupos">
            <div class="stat-card-icon !bg-red-100 !text-red-800 ">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                     stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
            </div>
            <div class="stat-card-info ">
                <div class="stat-card-value">{{ $totalGrupos }}</div>
                <div class="stat-card-label">Total de grupos</div>
                <div class="stat-card-link-hint">Ver todos →</div>
            </div>
        </a>
        @endif

    </div>
    @endif
</div>
