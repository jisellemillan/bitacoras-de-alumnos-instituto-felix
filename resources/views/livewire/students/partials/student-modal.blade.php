{{-- MODAL REACTIVO DE ALTA / EDICIÓN DE ALUMNO (COMPARTIDO) --}}
@if($showModal)
    <div class="modal-backdrop" x-data @keydown.escape.window="$wire.set('showModal', false)">
        <div class="modal-panel modal-panel-lg">
            <div class="modal-header">
                <div>
                    <h2 class="modal-title">{{ $isEditing ? 'Editar Expediente de Alumno' : 'Registrar Nuevo Alumno y Apertura de Expediente' }}</h2>
                    <span class="text-xs text-muted">Instituto Félix de Jesús Rougier</span>
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
                                    @if(method_exists($this, 'openQuickGroupModal'))
                                        <button type="button" wire:click="openQuickGroupModal" class="text-xs text-green-700 hover:text-green-900 font-bold inline-flex items-center gap-1 hover:underline">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                            Crear nuevo grupo
                                        </button>
                                    @endif
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
