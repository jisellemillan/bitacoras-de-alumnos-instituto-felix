<div>
    @if($show)
        <div class="modal-backdrop" x-data @keydown.escape.window="$wire.set('show', false)">
            <div class="modal-panel">
                <div class="modal-header bg-green-50/60 border-b">
                    <div>
                        <div class="text-[11px] font-bold tracking-widest text-[#1F5C2E] uppercase">INSTITUTO FÉLIX DE JESÚS ROUGIER · CIENCIA Y PUREZA</div>
                        <h2 class="modal-title text-xl text-green-950 font-bold mt-0.5">
                            {{ $isEditing ? 'Editar Cita' : 'Agendar Nueva Cita' }}
                        </h2>
                    </div>
                    <button type="button" wire:click="$set('show', false)" class="modal-close" aria-label="Cerrar modal">✕</button>
                </div>

                <form wire:submit="save">
                    <div class="modal-body space-y-5">
                        {{-- ALUMNO ASOCIADO Y FILTRO POR GRUPO --}}
                        <div class="form-group">
                            <div class="flex items-center justify-between mb-1.5 flex-wrap gap-2">
                                <label class="form-label required font-bold text-sm mb-0">Alumno Asociado:</label>
                                @if(!$isEditing)
                                    <div class="flex items-center gap-1.5">
                                        <label for="filterGroupSelect" class="text-xs text-muted font-medium">Filtrar por grupo:</label>
                                        <select id="filterGroupSelect" wire:model.live="filterGroupId" class="form-select form-select-sm text-xs py-1 px-2.5 rounded-lg border-gray-300 bg-gray-50 font-medium">
                                            <option value="">Todos los grupos</option>
                                            @foreach($groups as $grp)
                                                <option value="{{ $grp->id }}">Grupo {{ $grp->label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                            </div>

                            @if($isEditing)
                                <div class="p-2.5 bg-gray-50 border rounded-lg text-sm font-semibold text-gray-800">
                                    {{ $students->firstWhere('id', $studentId)?->full_name ?? 'Alumno no encontrado' }}
                                </div>
                            @else
                                <select wire:model="studentId" class="form-select @error('studentId') error @enderror">
                                    <option value="">Seleccione el alumno ({{ $students->count() }} alumnos disponibles)...</option>
                                    @foreach($students as $st)
                                        <option value="{{ $st->id }}">{{ $st->full_name }} — Grupo: {{ $st->current_group_label }}</option>
                                    @endforeach
                                </select>
                                @error('studentId') <span class="form-error">{{ $message }}</span> @enderror
                            @endif
                        </div>

                        {{-- TÍTULO DE LA CITA --}}
                        <div class="form-group">
                            <label class="form-label required font-bold text-sm">Título de la Cita:</label>
                            <input type="text" wire:model="title"
                                   class="form-input @error('title') error @enderror"
                                   placeholder="Ej. Seguimiento académico, Entrevista de revisión, Observación en aula...">
                            @error('title') <span class="form-error">{{ $message }}</span> @enderror
                        </div>

                        {{-- FECHAS Y HORARIO --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="form-group">
                                <label class="form-label required font-bold text-xs">Fecha y Hora de Inicio:</label>
                                <input type="datetime-local" wire:model="starts_at"
                                       class="form-input @error('starts_at') error @enderror">
                                @error('starts_at') <span class="form-error">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label font-bold text-xs">Fecha y Hora de Fin:</label>
                                <input type="datetime-local" wire:model="ends_at"
                                       class="form-input @error('ends_at') error @enderror">
                                @error('ends_at') <span class="form-error">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- LUGAR --}}
                        <div class="form-group">
                            <label class="form-label font-bold text-xs">Lugar / Ubicación:</label>
                            <input type="text" wire:model="location"
                                   class="form-input"
                                   placeholder="Ej. Dirección, Salón de psicopedagogía, Plataforma Teams...">
                        </div>

                        {{-- ESTATUS --}}
                        <div class="form-group">
                            <label class="form-label required font-bold text-xs">Estatus de la Cita:</label>
                            <select wire:model="status" class="form-select">
                                <option value="programada">Programada</option>
                                <option value="completada">Completada</option>
                                <option value="reprogramada">Reprogramada</option>
                                <option value="cancelada">Cancelada</option>
                            </select>
                        </div>

                        {{-- ASISTENTES REQUERIDOS (chips dinámicos) --}}
                        <div class="form-group">
                            <label class="form-label font-bold text-xs">Personas Convocadas:</label>
                            <div class="flex gap-2">
                                <input type="text" wire:model="attendee_input"
                                       class="form-input flex-1"
                                       placeholder="Nombre del convocado..."
                                       wire:keydown.enter.prevent="addAttendee">
                                <button type="button" wire:click="addAttendee"
                                        class="btn btn-outline btn-sm whitespace-nowrap">
                                    + Agregar
                                </button>
                            </div>
                            @if(count($required_attendees))
                                <div class="flex flex-wrap gap-2 mt-2">
                                    @foreach($required_attendees as $idx => $attendee)
                                        <span class="chip flex items-center gap-1 bg-green-50 border-green-200 text-green-900">
                                            {{ $attendee }}
                                            <button type="button" wire:click="removeAttendee({{ $idx }})"
                                                    class="ml-1 text-green-700 hover:text-red-600 font-bold text-xs">
                                                ✕
                                            </button>
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- NOTAS ADICIONALES --}}
                        <div class="form-group">
                            <label class="form-label font-bold text-xs">Notas y Observaciones:</label>
                            <textarea wire:model="notes"
                                      rows="3"
                                      class="form-textarea"
                                      placeholder="Indicaciones previas, documentación requerida, recordatorios..."></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" wire:click="$set('show', false)" class="btn btn-ghost">Cancelar</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove>{{ $isEditing ? 'Guardar Cambios' : 'Agendar Cita' }}</span>
                            <span wire:loading>Guardando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
