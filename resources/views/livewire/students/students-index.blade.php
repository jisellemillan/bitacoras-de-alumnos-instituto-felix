<div>
    {{-- Encabezado de página --}}
    <div class="page-header">
        <div class="page-header-info">
            <h1 class="page-title">Gestión de Alumnos y Expedientes</h1>
            <p class="text-muted">Directorio general de alumnos, diagnóstico inicial y trazabilidad psicopedagógica.</p>
        </div>
        <div class="page-header-actions">
            @can('create', App\Models\Student::class)
                <button type="button" wire:click="openCreateModal" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    <span>Agregar Alumno</span>
                </button>
            @endcan
        </div>
    </div>

    {{-- Barra de filtros reactivos --}}
    <div class="filters-bar">
        {{-- Búsqueda en vivo --}}
        <div class="search-bar flex-1 min-w-[240px]">
            <span class="search-bar-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </span>
            <input type="text"
                   wire:model.live.debounce.300ms="search"
                   placeholder="Buscar por nombre, apellido o CURP..."
                   class="form-input"
                   aria-label="Buscar alumnos">
        </div>

        {{-- Filtro por Grado --}}
        <div>
            <select wire:model.live="gradeFilter" class="filter-select" aria-label="Filtrar por grado">
                <option value="">Todos los grados</option>
                <option value="1">1° de Prescolar</option>
                <option value="2">2° de Prescolar</option>
                <option value="3">3° de Prescolar</option>
                <option value="4">1° de Primaria</option>
                <option value="5">2° de Primaria</option>
                <option value="6">3° de Primaria</option>
                <option value="7">4° de Primaria</option>
                <option value="8">5° de Primaria</option>
                <option value="9">6° de Primaria</option>
                <option value="10">1° de Secundaria</option>
                <option value="11">2° de Secundaria</option>
                <option value="12">3° de Secundaria</option>
            </select>
        </div>

        {{-- Filtro por Grupo --}}
        <div>
            <select wire:model.live="groupFilter" class="filter-select" aria-label="Filtrar por grupo">
                <option value="">Todos los grupos</option>
                @foreach($groups as $grp)
                    @if(empty($gradeFilter) || $grp->grade->value == $gradeFilter)
                        <option value="{{ $grp->id }}">{{ $grp->label }}</option>
                    @endif
                @endforeach
            </select>
        </div>

        {{-- Filtro por Estado de Expediente --}}
        <div>
            <select wire:model.live="statusFilter" class="filter-select" aria-label="Filtrar por estatus">
                <option value="">Todos los estados</option>
                <option value="activo">Expediente Activo</option>
                <option value="en_seguimiento">En Seguimiento</option>
                <option value="cerrado">Cerrado</option>
            </select>
        </div>
    </div>

    {{-- Indicador de carga en vivo --}}
    <div wire:loading.delay class="py-2 text-xs font-semibold text-green-700 flex items-center gap-2">
        <span class="wire-spinner"></span>
        <span>Actualizando resultados en tiempo real...</span>
    </div>

    {{-- Tabla de Alumnos --}}
    <div class="data-table-wrapper mt-4">
        <table class="data-table table-responsive">
            <thead>
                <tr>
                    <th>Alumno</th>
                    <th>Grado y Grupo</th>
                    <th>Diagnóstico / Caso</th>
                    <th>Ruta</th>
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
                                    <a href="{{ route('students.show', $st) }}" class="font-bold text-green-900 hover:text-green-700 block">
                                        {{ $st->full_name }}
                                    </a>
                                    <span class="text-xs text-muted">
                                        {{ $st->curp ?? 'Sin CURP' }}
                                        @if($st->age) · {{ $st->age }} años @endif
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td data-label="Grado y Grupo">
                            <span class="badge badge-gray font-medium">
                                {{ $st->current_group_label }}
                            </span>
                        </td>
                        <td data-label="Diagnóstico / Caso">
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
                                <span class="badge {{ $r === 'A' ? 'badge-route-a' : ($r === 'B' ? 'badge-route-b' : 'badge-route-c') }}" title="{{ $st->studentFile->intake_route->description() }}">
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
                            <x-dropdown-actions
                                :viewUrl="route('students.show', $st)"
                                :editAction="'openEditModal(' . $st->id . ')'"
                                :deleteAction="'deleteStudent(' . $st->id . ')'"
                                :deleteConfirm="'¿Estás seguro de dar de baja lógica al alumno ' . $st->full_name . '? Podrás restaurarlo posteriormente.'"
                            />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <svg class="empty-state-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                <div class="empty-state-title">No se encontraron alumnos</div>
                                <div class="empty-state-message">Verifica los filtros seleccionados o registra un nuevo estudiante con el botón superior.</div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Paginación reactiva --}}
        <div class="pagination">
            <div class="pagination-info">
                Mostrando {{ $students->firstItem() ?? 0 }} a {{ $students->lastItem() ?? 0 }} de {{ $students->total() }} alumnos
            </div>
            <div>
                {{ $students->links() }}
            </div>
        </div>
    </div>

    {{-- MODAL REACTIVO DE ALTA / EDICIÓN --}}
    @if($showModal)
        <div class="modal-backdrop" x-data @keydown.escape.window="$wire.set('showModal', false)">
            <div class="modal-panel modal-panel-lg">
                <div class="modal-header">
                    <div>
                        <h2 class="modal-title">{{ $isEditing ? 'Editar Expediente de Alumno' : 'Registrar Nuevo Alumno y Apertura de Expediente' }}</h2>
                        <span class="text-xs text-muted">Instituto Félix de Jesús Rougier </span>
                    </div>
                    <button type="button" wire:click="$set('showModal', false)" class="modal-close" aria-label="Cerrar modal">✕</button>
                </div>

                <form wire:submit="save">
                    <div class="modal-body">
                        {{-- SECCIÓN 1: DATOS GENERALES DEL ALUMNO --}}
                        <div class="border-b pb-4 mb-4">
                            <h3 class="text-sm font-bold text-green-800 uppercase tracking-wider mb-3">1. Datos del Alumno</h3>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label class="form-label required">Nombre(s)</label>
                                    <input type="text" wire:model="form.first_name" class="form-input @error('form.first_name') error @enderror" placeholder="Ej. Juan Carlos">
                                    @error('form.first_name') <span class="form-error">{{ $message }}</span> @enderror
                                </div>

                                <div class="form-group">
                                    <label class="form-label required">Apellidos</label>
                                    <input type="text" wire:model="form.last_name" class="form-input @error('form.last_name') error @enderror" placeholder="Ej. Pérez Gómez">
                                    @error('form.last_name') <span class="form-error">{{ $message }}</span> @enderror
                                </div>

                                <div class="form-group">
                                    <div class="flex items-center justify-between mb-1">
                                        <label class="form-label required mb-0">Grado y Grupo Asignado</label>
                                        <button type="button" wire:click="openQuickGroupModal" class="text-xs text-green-700 hover:text-green-900 font-bold inline-flex items-center gap-1 hover:underline">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                            Crear nuevo grupo
                                        </button>
                                    </div>
                                    <select wire:model="form.group_id" class="form-select @error('form.group_id') error @enderror">
                                        <option value="">Seleccione grupo...</option>
                                        @foreach($groups as $grp)
                                            <option value="{{ $grp->id }}">{{ $grp->label }} ({{ $grp->grade->label() }} Sección {{ $grp->section }})</option>
                                        @endforeach
                                    </select>
                                    @error('form.group_id') <span class="form-error">{{ $message }}</span> @enderror
                                </div>

                                <div class="form-group">
                                    <label class="form-label">CURP</label>
                                    <input type="text" wire:model="form.curp" class="form-input uppercase @error('form.curp') error @enderror" placeholder="18 caracteres" maxlength="18">
                                    @error('form.curp') <span class="form-error">{{ $message }}</span> @enderror
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Fecha de Nacimiento</label>
                                    <input type="date" wire:model="form.birthdate" class="form-input @error('form.birthdate') error @enderror">
                                    @error('form.birthdate') <span class="form-error">{{ $message }}</span> @enderror
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Sexo</label>
                                    <select wire:model="form.sex" class="form-select @error('form.sex') error @enderror">
                                        <option value="">Seleccione...</option>
                                        <option value="masculino">Masculino</option>
                                        <option value="femenino">Femenino</option>
                                        <option value="otro">Otro</option>
                                    </select>
                                    @error('form.sex') <span class="form-error">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- SECCIÓN 2: EXPEDIENTE PSICOPEDAGÓGICO --}}
                        <div class="border-b pb-4 mb-4">
                            <h3 class="text-sm font-bold text-green-800 uppercase tracking-wider mb-3">2. Expediente Psicopedagógico y Ruta de Ingreso</h3>

                            <div class="form-grid">
                                <div class="form-group">
                                    <label class="form-label">Ruta de Ingreso del Caso</label>
                                    <select wire:model="form.intake_route" class="form-select @error('form.intake_route') error @enderror">
                                        @foreach($intakeRoutes as $rt)
                                            <option value="{{ $rt->value }}">{{ $rt->label() }}</option>
                                        @endforeach
                                    </select>
                                    <span class="form-hint mt-1">
                                        @if($form->intake_route === 'A') Detección inicial por el maestro de grupo.
                                        @elseif($form->intake_route === 'B') Los padres solicitan acompañamiento.
                                        @elseif($form->intake_route === 'C') Entrega de diagnóstico por especialista externo.
                                        @endif
                                    </span>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Estatus del Expediente</label>
                                    <select wire:model="form.status" class="form-select">
                                        @foreach($statuses as $st)
                                            <option value="{{ $st->value }}">{{ $st->label() }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-col-full form-group">
                                    <label class="form-label">Diagnóstico Principal o Caso Particular</label>
                                    <input type="text" wire:model="form.primary_diagnosis" class="form-input" placeholder="Ej. TDAH presentación combinada, Dislexia, Inmadurez atencional...">
                                    <span class="form-hint">Este campo se guarda cifrado para máxima confidencialidad.</span>
                                </div>

                                <div class="form-col-full form-group">
                                    <label class="form-label">Áreas de Necesidad (Selección múltiple)</label>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-2 bg-slate-50 p-3 rounded-lg border">
                                        @foreach($needAreas as $na)
                                            <label class="form-check">
                                                <input type="checkbox" wire:model="form.need_areas" value="{{ $na->value }}" class="form-check-input">
                                                <span class="form-check-label text-xs">{{ $na->label() }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- SECCIÓN 3: ESTRATEGIAS Y ANTECEDENTES --}}
                        <div>
                            <h3 class="text-sm font-bold text-green-800 uppercase tracking-wider mb-3">3. Fortalezas y Estrategias</h3>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label class="form-label">Fortalezas del Alumno</label>
                                    <textarea wire:model="form.strengths" class="form-textarea" rows="2" placeholder="Habilidades, disposición, trabajo colaborativo..."></textarea>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Áreas de Apoyo Requeridas</label>
                                    <textarea wire:model="form.support_areas" class="form-textarea" rows="2" placeholder="Acompañamiento requerido en aula, pausas activas..."></textarea>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Especialista Externo (si aplica)</label>
                                    <input type="text" wire:model="form.external_specialist" class="form-input" placeholder="Nombre y contacto de neurólogo/psicólogo externo">
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Tratamiento o Medicación (Cifrado)</label>
                                    <input type="text" wire:model="form.treatment_medication" class="form-input" placeholder="Dosis y horario confidencial">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" wire:click="$set('showModal', false)" class="btn btn-ghost">Cancelar</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove>{{ $isEditing ? 'Guardar Cambios' : 'Registrar Alumno' }}</span>
                            <span wire:loading>Guardando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- SUBMODAL: CREACIÓN RÁPIDA DE GRUPO (CONSERVA DATOS DEL FORMULARIO) --}}
    @if($showQuickGroupModal)
        <div class="modal-backdrop" style="z-index: 80;" x-data @keydown.escape.window="$wire.closeQuickGroupModal()">
            <div class="modal-panel modal-panel-sm" style="box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.45);">
                <div class="modal-header bg-green-50/70 border-b">
                    <div>
                        <h3 class="modal-title text-base font-bold text-green-900">Crear Nuevo Grupo Escolar</h3>
                        <p class="text-xs text-muted">Se agregará y seleccionará sin perder los datos del alumno</p>
                    </div>
                    <button type="button" wire:click="closeQuickGroupModal" class="modal-close">✕</button>
                </div>

                <form wire:submit="saveQuickGroup">
                    <div class="modal-body space-y-3">
                        <div class="form-group">
                            <label class="form-label required font-bold text-xs">Grado Escolar</label>
                            <select wire:model="quick_grade" class="form-select @error('quick_grade') error @enderror">
                                
                                <option value="1">1° de Prescolar</option>
                                <option value="2">2° de Prescolar</option>
                                <option value="3">3° de Prescolar</option>
                                <option value="4">1° de Primaria</option>
                                <option value="5">2° de Primaria</option>
                                <option value="6">3° de Primaria</option>
                                <option value="7">4° de Primaria</option>
                                <option value="8">5° de Primaria</option>
                                <option value="9">6° de Primaria</option>
                                <option value="10">1° de Secundaria</option>
                                <option value="11">2° de Secundaria</option>
                                <option value="12">3° de Secundaria</option>
                            </select>
                            @error('quick_grade') <span class="form-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label required font-bold text-xs">Sección / Letra</label>
                            <input type="text" wire:model="quick_section" class="form-input uppercase @error('quick_section') error @enderror" placeholder="Ej. A, B, C" maxlength="5">
                            @error('quick_section') <span class="form-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label font-bold text-xs">Nombre o Distintivo (Opcional)</label>
                            <input type="text" wire:model="quick_name" class="form-input @error('quick_name') error @enderror" placeholder="Ej. Matutino, Bilingüe, Aula 104">
                            @error('quick_name') <span class="form-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label font-bold text-xs">Descripción (Opcional)</label>
                            <input type="text" wire:model="quick_description" class="form-input @error('quick_description') error @enderror" placeholder="Notas del grupo...">
                            @error('quick_description') <span class="form-error">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" wire:click="closeQuickGroupModal" class="btn btn-ghost btn-sm">Cancelar</button>
                        <button type="submit" class="btn btn-primary btn-sm" wire:loading.attr="disabled">
                            <span wire:loading.remove>Guardar Grupo y Seleccionar</span>
                            <span wire:loading>Guardando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
