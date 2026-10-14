<div>
    <div class="page-header">
        <div>
            <div class="page-pretitle">PANEL DE CONTROL</div>
            <h1 class="page-title">Configuración Institucional</h1>
            <p class="page-subtitle">Parámetros del sistema, ciclos escolares y estructura académica del Instituto Félix.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        {{-- Tarjeta Gestión de Grupos --}}
        <a href="{{ route('groups.index') }}" class="card p-6 hover:shadow-lg transition-all border border-green-100 hover:border-green-300 group block">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-green-100 text-green-800 flex items-center justify-center group-hover:scale-105 transition-transform">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
                <div>
                    <h2 class="font-bold text-gray-900 group-hover:text-green-800 transition-colors text-base">Gestión de Grupos Escolares</h2>
                    <p class="text-xs text-muted mt-1">Administra grados de 1° a 6°, secciones, nombres distintivos y conteo de alumnos.</p>
                    <span class="inline-flex items-center gap-1 text-xs font-bold text-green-700 mt-3 group-hover:underline">
                        Ver {{ $totalGroups }} grupos registrados →
                    </span>
                </div>
            </div>
        </a>

        {{-- Tarjeta Gestión de Usuarios --}}
        @if(auth()->user()->hasSectionPermission('usuarios'))
        <a href="{{ route('users.index') }}" class="card p-6 hover:shadow-lg transition-all border border-blue-100 hover:border-blue-300 group block">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-800 flex items-center justify-center group-hover:scale-105 transition-transform">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <div>
                    <h2 class="font-bold text-gray-900 group-hover:text-blue-800 transition-colors text-base">Cuentas y Permisos</h2>
                    <p class="text-xs text-muted mt-1">Control de acceso granular por módulo, contraseñas y roles institucionales.</p>
                    <span class="inline-flex items-center gap-1 text-xs font-bold text-blue-700 mt-3 group-hover:underline">
                        Ver {{ $totalUsers }} cuentas activas →
                    </span>
                </div>
            </div>
        </a>
        @endif

        {{-- Tarjeta Ciclo Escolar Activo --}}
        <div class="card p-6 border border-amber-100 bg-amber-50/30">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div>
                    <h2 class="font-bold text-gray-900 text-base">Ciclo Escolar Vigente</h2>
                    <p class="text-xs text-muted mt-1">Periodo académico activo para la inscripción de estudiantes.</p>
                    <div class="mt-2">
                        @if($currentCycle)
                            <span class="badge badge-green font-bold text-xs">Ciclo {{ $currentCycle->name }} (Activo)</span>
                        @else
                            <span class="badge badge-yellow text-xs">Sin ciclo activo configurado</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
