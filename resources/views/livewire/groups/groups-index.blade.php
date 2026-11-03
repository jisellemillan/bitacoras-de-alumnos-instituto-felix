<div>
    {{-- ENCABEZADO DE PÁGINA --}}
    <div class="page-header">
        <div>
            <div class="page-pretitle">ADMINISTRACIÓN ESCOLAR</div>
            <h1 class="page-title">Gestión de Grupos</h1>
            <p class="page-subtitle">Organización de grados, secciones y distribución de alumnos por ciclo escolar.</p>
        </div>
        <div class="page-actions">
            <button type="button" wire:click="openCreateModal" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                <span>Nuevo Grupo</span>
            </button>
        </div>
    </div>


    {{-- BARRA DE FILTROS Y BÚSQUEDA --}}
    <div class="table-card mb-6">
        <div class="p-4 flex flex-col md:flex-row gap-4 items-stretch md:items-center justify-between bg-white border-b">
            <div class="flex-1 flex flex-col sm:flex-row gap-3">
                {{-- Búsqueda libre --}}
                <div class="relative flex-1">
                    <input type="text"
                           wire:model.live.debounce.300ms="search"
                           placeholder="Buscar por sección o nombre del grupo..."
                           class="form-input text-sm pl-9 w-full">
                    <svg class="w-4 h-4 text-muted absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <circle cx="11" cy="11" r="8" stroke-width="2"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65" stroke-width="2"/>
                    </svg>
                </div>

                {{-- Filtro por grado --}}
                <div class="w-full sm:w-44">
                    <select wire:model.live="gradeFilter" class="form-select text-sm w-full">
                        <option value="">Todos los grados</option>
                        <option value="1">1° de Primaria</option>
                        <option value="2">2° de Primaria</option>
                        <option value="3">3° de Primaria</option>
                        <option value="4">4° de Primaria</option>
                        <option value="5">5° de Primaria</option>
                        <option value="6">6° de Primaria</option>
                    </select>
                </div>

                {{-- Filtro por ciclo escolar --}}
                <div class="w-full sm:w-48">
                    <select wire:model.live="cycleFilter" class="form-select text-sm w-full">
                        <option value="">Ciclo actual por defecto</option>
                        @foreach($cycles as $c)
                            <option value="{{ $c->id }}">Ciclo {{ $c->name }} {{ $c->is_active ? '(Activo)' : '' }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            @if($search || $gradeFilter || $cycleFilter)
                <button type="button" wire:click="$set('search', ''); $set('gradeFilter', ''); $set('cycleFilter', '');" class="btn btn-ghost btn-sm text-xs text-muted">
                    Limpiar filtros
                </button>
            @endif
        </div>

        {{-- TABLA DE GRUPOS --}}
        <table class="data-table">
            <thead>
                <tr>
                    <th>Grupo / Sección</th>
                    <th>Grado Escolar</th>
                    <th>Nombre / Distintivo</th>
                    <th>Ciclo Escolar</th>
                    <th class="text-center">Alumnos Asignados</th>
                    <th>Descripción</th>
                    <th class="text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($groups as $grp)
                    <tr>
                        <td data-label="Grupo">
                            <a href="{{ route('groups.show', $grp) }}" class="flex items-center gap-3 group/grp hover:opacity-95">
                                <div class="w-10 h-10 rounded-xl bg-green-100 text-green-900 font-bold flex items-center justify-center text-sm shadow-xs border border-green-200 group-hover/grp:bg-[#1F5C2E] group-hover/grp:text-white transition-colors">
                                    {{ $grp->grade->label() }}{{ $grp->section }}
                                </div>
                                <div>
                                    <span class="font-bold text-green-950 group-hover/grp:text-green-700 transition-colors block">
                                        Grupo {{ $grp->label }}
                                    </span>
                                    <span class="text-xs text-muted">{{ $grp->grade->label() }} de Primaria · Sección {{ $grp->section }}</span>
                                </div>
                            </a>
                        </td>
                        <td data-label="Grado Escolar">
                            <span class="font-medium text-sm text-gray-800">{{ $grp->grade->label() }}</span>
                        </td>
                        <td data-label="Nombre / Distintivo">
                            @if($grp->name)
                                <span class="font-semibold text-sm text-green-800">{{ $grp->name }}</span>
                            @else
                                <span class="text-xs text-muted italic">Sin distintivo</span>
                            @endif
                        </td>
                        <td data-label="Ciclo Escolar">
                            <span class="badge badge-gray font-medium">
                                {{ $grp->schoolCycle?->name ?? '—' }}
                            </span>
                        </td>
                        <td data-label="Alumnos" class="text-center">
                            <a href="{{ route('groups.show', $grp) }}" class="inline-block hover:scale-105 transition-transform" title="Ver alumnos de este grupo">
                                @if($grp->students_count > 0)
                                    <span class="badge badge-green font-bold">
                                        {{ $grp->students_count }} {{ $grp->students_count === 1 ? 'alumno' : 'alumnos' }} →
                                    </span>
                                @else
                                    <span class="badge badge-gray text-xs">
                                        0 alumnos
                                    </span>
                                @endif
                            </a>
                        </td>
                        <td data-label="Descripción">
                            <span class="text-xs text-muted max-w-xs block truncate" title="{{ $grp->description }}">
                                {{ $grp->description ?: '—' }}
                            </span>
                        </td>
                        <td data-label="Acciones" class="text-right">
                            <x-dropdown-actions
                                :viewUrl="route('groups.show', $grp)"
                                :editAction="'openEditModal(' . $grp->id . ')'"
                                :deleteAction="'deleteGroup(' . $grp->id . ')'"
                                :deleteConfirm="'¿Estás seguro de eliminar el grupo ' . $grp->label . '? Esta acción no se puede deshacer.'"
                            />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state py-8">
                                <div class="empty-state-title">No se encontraron grupos</div>
                                <div class="empty-state-message">Verifica los filtros seleccionados o registra un nuevo grupo escolar con el botón superior.</div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- PAGINACIÓN --}}
        <div class="pagination">
            <div class="pagination-info">
                Mostrando {{ $groups->firstItem() ?? 0 }} a {{ $groups->lastItem() ?? 0 }} de {{ $groups->total() }} grupos
            </div>
            <div>
                {{ $groups->links() }}
            </div>
        </div>
    </div>

    {{-- MODAL DE CREACIÓN / EDICIÓN DE GRUPO --}}
    @if($showModal)
        <div class="modal-backdrop" x-data @keydown.escape.window="$wire.set('showModal', false)">
            <div class="modal-panel modal-panel-sm">
                <div class="modal-header">
                    <div>
                        <h2 class="modal-title">{{ $isEditing ? 'Editar Grupo Escolar' : 'Crear Nuevo Grupo Escolar' }}</h2>
                        <span class="text-xs text-muted">Instituto Félix de Jesús Rougier</span>
                    </div>
                    <button type="button" wire:click="$set('showModal', false)" class="modal-close" aria-label="Cerrar modal">✕</button>
                </div>

                <form wire:submit="save">
                    <div class="modal-body space-y-4">
                        <div class="form-group">
                            <label class="form-label required">Ciclo Escolar</label>
                            <select wire:model="school_cycle_id" class="form-select @error('school_cycle_id') error @enderror">
                                @foreach($cycles as $c)
                                    <option value="{{ $c->id }}">Ciclo {{ $c->name }} {{ $c->is_active ? '(Activo)' : '' }}</option>
                                @endforeach
                            </select>
                            @error('school_cycle_id') <span class="form-error">{{ $message }}</span> @enderror
                        </div>


                    <div class="form-group mt-3 mb-3">
                        <label class="form-label font-semibold text-gray-900 block mb-2">Docentes Asignados</label>
                        <div class="grid grid-cols-1 gap-2 max-h-40 overflow-y-auto bg-slate-50 p-3 rounded-xl border border-gray-200">
                            @forelse($teachers as $t)
                                <label class="flex items-center gap-3 p-1.5 rounded-lg hover:bg-white transition-colors cursor-pointer border border-transparent hover:border-gray-200">
                                    <input type="checkbox" 
                                        wire:model="teacher_ids" 
                                        value="{{ $t->id }}" 
                                        class="form-check-input">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-gray-800">{{ $t->name }}</span>
                                        <span class="text-[10px] text-muted">{{ $t->email }}</span>
                                    </div>
                                </label>
                            @empty
                                <span class="text-xs text-muted italic">No hay docentes titulares o especiales disponibles.</span>
                            @endforelse
                        </div>
                        @error('teacher_ids') <span class="form-error mt-1">{{ $message }}</span> @enderror
                    </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="form-group">
                                <label class="form-label required">Grado</label>
                                <select wire:model="grade" class="form-select @error('grade') error @enderror">
                                    <option value="1">1° de Primaria</option>
                                    <option value="2">2° de Primaria</option>
                                    <option value="3">3° de Primaria</option>
                                    <option value="4">4° de Primaria</option>
                                    <option value="5">5° de Primaria</option>
                                    <option value="6">6° de Primaria</option>
                                </select>
                                @error('grade') <span class="form-error">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label required">Sección / Letra</label>
                                <input type="text"
                                       wire:model="section"
                                       class="form-input uppercase @error('section') error @enderror"
                                       placeholder="Ej. A, B, C"
                                       maxlength="5">
                                @error('section') <span class="form-error">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Nombre o Distintivo (Opcional)</label>
                            <input type="text"
                                   wire:model="name"
                                   class="form-input @error('name') error @enderror"
                                   placeholder="Ej. Matutino, Bilingüe, Aula 104">
                            @error('name') <span class="form-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Descripción o Notas (Opcional)</label>
                            <textarea wire:model="description"
                                      rows="2"
                                      class="form-textarea @error('description') error @enderror"
                                      placeholder="Ubicación del aula, docentes a cargo o notas..."></textarea>
                            @error('description') <span class="form-error">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" wire:click="$set('showModal', false)" class="btn btn-ghost">Cancelar</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove>{{ $isEditing ? 'Guardar Cambios' : 'Crear Grupo' }}</span>
                            <span wire:loading>Guardando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
