<div>
    {{-- ENCABEZADO DE PÁGINA --}}
    <div class="page-header">
        <div>
            <div class="page-pretitle !text-red-900">ADMINISTRACIÓN ESCOLAR</div>
            <h1 class="page-title !text-red-900">Gestión de Periodos</h1>
            <p class="page-subtitle">Administra los ciclos escolares del instituto. Solo puede existir un periodo activo a la vez.</p>
        </div>
        <div class="page-actions">
            <button type="button" wire:click="openCreateModal" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                <span>Nuevo Periodo</span>
            </button>
        </div>
    </div>

    {{-- TABLA DE PERIODOS --}}
    <div class="table-card mb-6">

        {{-- Periodo activo destacado (banner informativo) --}}
        @php $activeCycle = $cycles->firstWhere('is_active', true); @endphp
        @if($activeCycle)
            <div class="p-4 bg-green-50 border-b border-green-200 flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-green-500 animate-pulse"></span>
                    <span class="text-sm font-semibold text-green-800">Periodo activo:</span>
                </span>
                <span class="text-sm font-bold text-green-900">{{ $activeCycle->name }}</span>
                @if($activeCycle->starts_at || $activeCycle->ends_at)
                    <span class="text-xs text-green-700">
                        ({{ $activeCycle->starts_at?->format('d/m/Y') ?? '—' }}
                        → {{ $activeCycle->ends_at?->format('d/m/Y') ?? '—' }})
                    </span>
                @endif
            </div>
        @else
            <div class="p-4 bg-amber-50 border-b border-amber-200 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="text-amber-600 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                <span class="text-sm text-amber-800 font-medium">No hay ningún periodo activo. Activa uno para que el sistema funcione correctamente.</span>
            </div>
        @endif

        <table class="data-table">
            <thead class="border-b-2 border-red-800">
                <tr>
                    <th class="!bg-red-50 !text-red-900">Periodo / Nombre</th>
                    <th class="!bg-red-50 !text-red-900">Fecha de Inicio</th>
                    <th class="!bg-red-50 !text-red-900">Fecha de Fin</th>
                    <th class="!bg-red-50 !text-red-900 text-center">Estado</th>
                    <th class="!bg-red-50 !text-red-900 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cycles as $cycle)
                    <tr @class(['bg-green-50/40' => $cycle->is_active])>
                        <td data-label="Periodo">
                            <div class="flex items-center gap-3">
                                <div @class([
                                    'w-10 h-10 rounded-xl font-bold flex items-center justify-center text-xs shadow-xs border',
                                    'bg-green-100 text-green-900 border-green-200' => $cycle->is_active,
                                    'bg-gray-100 text-gray-600 border-gray-200'    => ! $cycle->is_active,
                                ])>
                                    {{ substr($cycle->name, 0, 4) }}
                                </div>
                                <div>
                                    <span @class([
                                        'font-bold block',
                                        'text-green-900' => $cycle->is_active,
                                        'text-gray-800'  => ! $cycle->is_active,
                                    ])>
                                        {{ $cycle->name }}
                                    </span>
                                    @if($cycle->is_active)
                                        <span class="text-xs text-green-700 font-medium">Periodo vigente</span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <td data-label="Fecha inicio">
                            <span class="text-sm text-gray-700">
                                {{ $cycle->starts_at?->format('d \d\e F \d\e Y') ?? '—' }}
                            </span>
                        </td>

                        <td data-label="Fecha fin">
                            <span class="text-sm text-gray-700">
                                {{ $cycle->ends_at?->format('d \d\e F \d\e Y') ?? '—' }}
                            </span>
                        </td>

                        <td data-label="Estado" class="text-center">
                            @if($cycle->is_active)
                                <span class="badge badge-green inline-flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                    Activo
                                </span>
                            @else
                                <span class="badge badge-gray">
                                    Inactivo
                                </span>
                            @endif
                        </td>

                        <td data-label="Acciones" class="text-right">
                            <div class="flex items-center justify-end gap-2 flex-wrap">

                                {{-- Botón Activar (solo si no está activo) --}}
                                @if(! $cycle->is_active)
                                    <button type="button"
                                            wire:click="activateCycle({{ $cycle->id }})"
                                            wire:confirm="¿Activar el periodo {{ $cycle->name }}? El periodo activo actual pasará a Inactivo."
                                            class="btn btn-ghost btn-sm text-green-700 hover:bg-green-50 border border-green-200"
                                            title="Activar este periodo">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Activar
                                    </button>
                                @else
                                    <button type="button"
                                            wire:click="deactivateCycle({{ $cycle->id }})"
                                            wire:confirm="¿Desactivar el periodo {{ $cycle->name }}? El sistema quedará sin un periodo activo."
                                            class="btn btn-ghost btn-sm text-amber-700 hover:bg-amber-50 border border-amber-200"
                                            title="Desactivar este periodo">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                                        Desactivar
                                    </button>
                                @endif

                                {{-- Botón Editar --}}
                                <button type="button"
                                        wire:click="openEditModal({{ $cycle->id }})"
                                        class="btn btn-ghost btn-sm"
                                        title="Editar periodo">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Editar
                                </button>

                                {{-- Botón Eliminar (siempre visible; la lógica bloquea en backend) --}}
                                <button type="button"
                                        wire:click="deleteCycle({{ $cycle->id }})"
                                        wire:confirm="¿Estás segura de eliminar el periodo {{ $cycle->name }}? Esta acción no se puede deshacer."
                                        class="btn btn-ghost btn-sm text-red-600 hover:bg-red-50"
                                        title="Eliminar periodo">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path stroke-linecap="round" stroke-linejoin="round" d="M19 6l-1 14H6L5 6"/><path stroke-linecap="round" stroke-linejoin="round" d="M10 11v6M14 11v6"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 6V4h6v2"/></svg>
                                    Eliminar
                                </button>

                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="empty-state py-10">
                                <div class="empty-state-title">No hay periodos registrados</div>
                                <div class="empty-state-message">Crea el primer ciclo escolar con el botón "Nuevo Periodo".</div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- MODAL CREAR / EDITAR --}}
    @if($showModal)
        <div class="modal-backdrop" x-data @keydown.escape.window="$wire.set('showModal', false)">
            <div class="modal-panel modal-panel-sm">
                <div class="modal-header">
                    <div>
                        <h2 class="modal-title">{{ $isEditing ? 'Editar Periodo' : 'Nuevo Periodo Escolar' }}</h2>
                        <span class="text-xs text-muted">Instituto Félix de Jesús Rougier</span>
                    </div>
                    <button type="button" wire:click="$set('showModal', false)" class="modal-close" aria-label="Cerrar modal">✕</button>
                </div>

                <form wire:submit="save">
                    <div class="modal-body space-y-4">

                        {{-- Año y número de periodo --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div class="form-group">
                                <label class="form-label required">Año</label>
                                <input type="number"
                                       wire:model.live="year"
                                       class="form-input @error('year') error @enderror"
                                       placeholder="Ej. 2026"
                                       min="2000"
                                       max="2100"
                                       step="1">
                                @error('year') <span class="form-error">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label required">Periodo</label>
                                <select wire:model.live="period_number" class="form-select @error('period_number') error @enderror">
                                    <option value="1">1 — Primer periodo</option>
                                    <option value="2">2 — Segundo periodo</option>
                                    <option value="3">3 — Tercer periodo</option>
                                </select>
                                @error('period_number') <span class="form-error">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- Vista previa del nombre generado --}}
                        @if($year !== '')
                            <div class="p-3 bg-red-50 border border-red-200 rounded-xl flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="text-red-700 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 110 20A10 10 0 0112 2z"/></svg>
                                <span class="text-sm text-red-800">El periodo se guardará como: <strong>{{ $year }}-{{ $period_number }}</strong></span>
                            </div>
                        @endif

                        {{-- Fechas --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div class="form-group">
                                <label class="form-label">Fecha de inicio <span class="text-xs text-muted">(opcional)</span></label>
                                <input type="date"
                                       wire:model="starts_at"
                                       class="form-input @error('starts_at') error @enderror">
                                @error('starts_at') <span class="form-error">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">Fecha de fin <span class="text-xs text-muted">(opcional)</span></label>
                                <input type="date"
                                       wire:model="ends_at"
                                       class="form-input @error('ends_at') error @enderror">
                                @error('ends_at') <span class="form-error">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- Estado activo --}}
                        <div class="form-group">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox"
                                       wire:model="is_active"
                                       class="form-check-input">
                                <div>
                                    <span class="font-semibold text-sm text-gray-800">Marcar como periodo activo</span>
                                    <p class="text-xs text-muted mt-0.5">Si activas este periodo, el que estaba activo pasará automáticamente a Inactivo.</p>
                                </div>
                            </label>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" wire:click="$set('showModal', false)" class="btn btn-ghost">Cancelar</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove>{{ $isEditing ? 'Guardar Cambios' : 'Crear Periodo' }}</span>
                            <span wire:loading>Guardando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
