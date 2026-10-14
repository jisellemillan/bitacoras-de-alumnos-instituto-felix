<div>
    {{-- MIGA DE PAN / BREADCRUMBS --}}
    <nav class="mb-4 flex items-center gap-2 text-xs text-muted" aria-label="Ruta de navegación">
        <a href="{{ route('groups.index') }}" class="hover:text-green-800 transition-colors flex items-center gap-1 font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            Gestión de Grupos
        </a>
        <span>/</span>
        <span class="text-green-950 font-bold">Grupo {{ $group->label }}</span>
    </nav>

    {{-- ENCABEZADO DEL GRUPO CON BOTÓN DE REGRESO --}}
    <div class="card p-6 mb-6 bg-gradient-to-r from-white to-green-50/40 border border-green-100">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 rounded-2xl bg-[#1F5C2E] text-white font-bold flex items-center justify-center text-xl shadow-md shrink-0">
                    {{ $group->grade->label() }}{{ $group->section }}
                </div>
                <div>
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h1 class="text-2xl font-bold text-green-950 font-heading">
                            Grupo {{ $group->label }}
                        </h1>
                        <span class="badge badge-green font-bold">
                            {{ $totalGroupStudents }} {{ $totalGroupStudents === 1 ? 'alumno inscrito' : 'alumnos inscritos' }}
                        </span>
                        @if($group->schoolCycle)
                            <span class="badge badge-gray font-medium">
                                Ciclo {{ $group->schoolCycle->name }}
                            </span>
                        @endif
                    </div>

                    <div class="text-xs text-muted mt-1.5 flex items-center gap-2 flex-wrap">
                        <span><strong>Grado:</strong> {{ $group->grade->label() }} de Primaria</span>
                        <span>·</span>
                        <span><strong>Sección:</strong> {{ $group->section }}</span>
                        @if($group->name)
                            <span>·</span>
                            <span><strong>Distintivo:</strong> {{ $group->name }}</span>
                        @endif
                    </div>

                    @if($group->description)
                        <p class="text-xs text-gray-600 mt-2 max-w-2xl bg-white/70 p-2 rounded-lg border border-gray-100">
                            {{ $group->description }}
                        </p>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('groups.index') }}" class="btn btn-outline btn-sm">
                    ← Volver a grupos
                </a>
                @if(auth()->user()->hasSectionPermission('alumnos'))
                    <a href="{{ route('students.index') }}" class="btn btn-primary btn-sm">
                        + Registrar alumno
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- BARRA DE FILTROS Y BÚSQUEDA DENTRO DEL GRUPO --}}
    <div class="table-card mb-6">
        <div class="p-4 flex flex-col sm:flex-row gap-3 items-stretch sm:items-center justify-between bg-white border-b">
            <div class="flex-1 flex flex-col sm:flex-row gap-3">
                {{-- Búsqueda de alumnos en este grupo --}}
                <div class="relative flex-1">
                    <input type="text"
                           wire:model.live.debounce.300ms="search"
                           placeholder="Buscar por nombre, apellidos o CURP en este grupo..."
                           class="form-input text-sm pl-9 w-full">
                    <svg class="w-4 h-4 text-muted absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <circle cx="11" cy="11" r="8" stroke-width="2"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65" stroke-width="2"/>
                    </svg>
                </div>

                {{-- Filtro por estatus de expediente --}}
                <div class="w-full sm:w-48">
                    <select wire:model.live="statusFilter" class="form-select text-sm w-full">
                        <option value="">Todos los estatus</option>
                        @foreach($statuses as $st)
                            <option value="{{ $st->value }}">{{ $st->label() }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            @if($search || $statusFilter)
                <button type="button" wire:click="$set('search', ''); $set('statusFilter', '');" class="btn btn-ghost btn-sm text-xs text-muted">
                    Limpiar filtros
                </button>
            @endif
        </div>

        {{-- TABLA DE ESTUDIANTES DEL GRUPO --}}
        <table class="data-table">
            <thead>
                <tr>
                    <th>Alumno</th>
                    <th>CURP / Edad</th>
                    <th>Diagnóstico Principal</th>
                    <th>Ruta de Ingreso</th>
                    <th>Áreas de Necesidad</th>
                    <th>Estatus</th>
                    <th class="text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $st)
                    <tr>
                        <td data-label="Alumno">
                            <div class="flex items-center gap-3">
                                <div class="student-avatar">
                                    {{ strtoupper(substr($st->first_name, 0, 1) . substr($st->last_name, 0, 1)) }}
                                </div>
                                <div>
                                    <a href="{{ route('students.show', ['student' => $st->id, 'from_group' => $group->id]) }}"
                                       class="font-bold text-green-950 hover:text-green-700 block transition-colors">
                                        {{ $st->full_name }}
                                    </a>
                                    <span class="text-xs text-muted">
                                        Expediente digital activo
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td data-label="CURP / Edad">
                            <span class="text-xs font-mono font-medium text-gray-800 block">{{ $st->curp ?? 'Sin CURP' }}</span>
                            <span class="text-xs text-muted">{{ $st->age ? $st->age . ' años' : 'Edad no registrada' }}</span>
                        </td>
                        <td data-label="Diagnóstico">
                            @if($st->studentFile && $st->studentFile->primary_diagnosis)
                                <div class="font-medium text-sm text-green-950">{{ $st->studentFile->primary_diagnosis }}</div>
                                <span class="text-xs text-muted">{{ $st->studentFile->file_type === 'con_diagnostico' ? 'Diagnóstico formal' : 'En detección' }}</span>
                            @else
                                <span class="text-xs text-muted italic">Sin diagnóstico registrado</span>
                            @endif
                        </td>
                        <td data-label="Ruta">
                            @if($st->studentFile && $st->studentFile->intake_route)
                                @php $r = $st->studentFile->intake_route->value; @endphp
                                <span class="badge {{ $r === 'A' ? 'badge-route-a' : ($r === 'B' ? 'badge-route-b' : 'badge-route-c') }}"
                                      title="{{ $st->studentFile->intake_route->description() }}">
                                    Ruta {{ $r }}
                                </span>
                            @else
                                <span class="text-xs text-muted">—</span>
                            @endif
                        </td>
                        <td data-label="Áreas">
                            <div class="flex flex-wrap gap-1 max-w-xs">
                                @if($st->studentFile && !empty($st->studentFile->need_areas))
                                    @foreach(array_slice($st->studentFile->getNeedAreaLabels(), 0, 2) as $label)
                                        <span class="chip text-[11px] py-0 px-2">{{ $label }}</span>
                                    @endforeach
                                    @if(count($st->studentFile->need_areas) > 2)
                                        <span class="text-[10px] text-muted font-bold">+{{ count($st->studentFile->need_areas) - 2 }}</span>
                                    @endif
                                @else
                                    <span class="text-xs text-muted">—</span>
                                @endif
                            </div>
                        </td>
                        <td data-label="Estatus">
                            @if($st->studentFile && $st->studentFile->status)
                                @php $stColor = $st->studentFile->status->color(); @endphp
                                <span class="badge {{ $stColor === 'green' ? 'badge-green' : ($stColor === 'yellow' ? 'badge-yellow' : 'badge-gray') }}">
                                    <span class="badge-dot"></span>
                                    {{ $st->studentFile->status->label() }}
                                </span>
                            @endif
                        </td>
                        <td data-label="Acciones" class="text-right">
                            <a href="{{ route('students.show', ['student' => $st->id, 'from_group' => $group->id]) }}"
                               class="btn btn-outline btn-sm inline-flex items-center gap-1.5 hover:bg-green-50 hover:text-green-800">
                                <span>Ver expediente</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state py-12">
                                <div class="w-14 h-14 rounded-full bg-green-100 text-green-800 flex items-center justify-center mx-auto mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <div class="empty-state-title">Este grupo todavía no tiene alumnos asignados</div>
                                <div class="empty-state-message max-w-md mx-auto">
                                    Actualmente no hay estudiantes inscritos en el Grupo {{ $group->label }}. Puedes asignar alumnos desde el registro general de estudiantes o editando un expediente existente.
                                </div>
                                <div class="mt-4 flex items-center justify-center gap-3">
                                    <a href="{{ route('groups.index') }}" class="btn btn-ghost btn-sm">
                                        ← Volver a la lista de grupos
                                    </a>
                                    @if(auth()->user()->hasSectionPermission('alumnos'))
                                        <a href="{{ route('students.index') }}" class="btn btn-primary btn-sm">
                                            Ir al Directorio de Alumnos
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- PAGINACIÓN --}}
        <div class="pagination">
            <div class="pagination-info">
                Mostrando {{ $students->firstItem() ?? 0 }} a {{ $students->lastItem() ?? 0 }} de {{ $students->total() }} alumnos en este grupo
            </div>
            <div>
                {{ $students->links() }}
            </div>
        </div>
    </div>
</div>
