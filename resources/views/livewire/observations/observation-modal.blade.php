<div>
    @if($show)
        <div class="modal-backdrop" x-data @keydown.escape.window="$wire.set('show', false)">
            <div class="modal-panel modal-panel-xl">
                {{-- Encabezado fiel al formato físico del Instituto Félix --}}
                <div class="modal-header bg-green-50/60 border-b">
                    <div>
                        <div class="text-[11px] font-bold tracking-widest text-[#1F5C2E] uppercase">INSTITUTO FÉLIX DE JESÚS ROUGIER · CIENCIA Y PUREZA</div>
                        <h2 class="modal-title text-xl text-green-950 font-bold mt-0.5">
                            {{ $isEditing ? 'Editar Formato de Observación de Alumno' : 'Formato Físico: Observación de Alumno' }}
                        </h2>
                    </div>
                    <button type="button" wire:click="$set('show', false)" class="modal-close" aria-label="Cerrar modal">✕</button>
                </div>

                <form wire:submit="save">
                    <div class="modal-body space-y-5">
                        {{-- ENCABEZADO Y FECHA --}}
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 bg-slate-50 rounded-xl border">
                            <div class="form-group sm:col-span-2">
                                <label class="form-label font-bold text-xs">Mexicali, B.C., a fecha:</label>
                                <input type="date" wire:model="observation_date" class="form-input @error('observation_date') error @enderror">
                                @error('observation_date') <span class="form-error">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label font-bold text-xs">Grado y Grupo:</label>
                                <input type="text" wire:model="grade_group" class="form-input" placeholder="Ej. 5°B">
                            </div>
                        </div>

                        {{-- OBSERVACIONES (TEXTO PRINCIPAL) --}}
                        <div class="form-group">
                            <label class="form-label required font-bold text-sm text-green-950">Observaciones en Aula:</label>
                            <textarea wire:model="observations"
                                      rows="5"
                                      class="form-textarea form-textarea-lg @error('observations') error @enderror"
                                      placeholder="Describa a detalle las conductas, dinámicas y situaciones observadas..."></textarea>
                            @error('observations') <span class="form-error">{{ $message }}</span> @enderror
                        </div>

                        {{-- FUE NECESARIO: CASILLAS FÍSICAS --}}
                        <div class="p-4 bg-green-50/80 rounded-xl border border-green-200">
                            <label class="block text-xs font-bold text-green-950 uppercase tracking-wider mb-2">
                                Fue necesario:
                            </label>
                            <div class="flex flex-wrap gap-6">
                                <label class="form-check cursor-pointer">
                                    <input type="checkbox" wire:model="required_prefectura" class="form-check-input">
                                    <span class="form-check-label font-medium text-sm">Enviar a prefectura</span>
                                </label>

                                <label class="form-check cursor-pointer">
                                    <input type="checkbox" wire:model="required_parent_interview" class="form-check-input">
                                    <span class="form-check-label font-medium text-sm">Entrevista con papás</span>
                                </label>

                                <label class="form-check cursor-pointer">
                                    <input type="checkbox" wire:model="required_channeling" class="form-check-input">
                                    <span class="form-check-label font-medium text-sm">Canalización</span>
                                </label>
                            </div>
                        </div>

                        {{-- ACUERDOS Y PRÓXIMA CITA --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="form-group md:col-span-2">
                                <label class="form-label font-bold text-sm text-green-950">Acuerdos Establecidos:</label>
                                <textarea wire:model="agreements_text" rows="3" class="form-textarea" placeholder="Compromisos y estrategias acordadas..."></textarea>
                            </div>

                            <div class="form-group">
                                <label class="form-label font-bold text-sm text-green-950">Próxima Cita (si aplica):</label>
                                <input type="date" wire:model="next_appointment_date" class="form-input">
                                <span class="form-hint mt-1">Se agendará automáticamente en el calendario institucional.</span>
                            </div>
                        </div>

                        {{-- CONVOCADOS Y ASISTENTES --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="form-group">
                                <label class="form-label font-bold text-xs text-gray-800">Es necesaria la presencia de:</label>
                                <input type="text" wire:model="required_presence" class="form-input" placeholder="Madre o tutor, Maestro de inglés...">
                            </div>

                            <div class="form-group">
                                <label class="form-label font-bold text-xs text-gray-800">Si están presentes (asistentes):</label>
                                <input type="text" wire:model="attendees" class="form-input" placeholder="Nombres de quienes acudieron a la reunión">
                            </div>
                        </div>

                        {{-- SECCIÓN DE FIRMAS FÍSICAS (8 ROLES DEL FORMATO) --}}
                        <div class="border-t pt-4">
                            <h3 class="text-xs font-bold text-green-950 uppercase tracking-wider mb-3">Bloque Oficial de Firmas y Asistencia</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 bg-slate-50 p-4 rounded-xl border max-h-56 overflow-y-auto">
                                @foreach($roles as $role)
                                    <div class="p-2.5 bg-white rounded-lg border flex items-center justify-between gap-3">
                                        <div class="flex-1 min-w-0">
                                            <span class="block text-xs font-semibold text-gray-800 truncate">{{ $role->label() }}</span>
                                            <input type="text"
                                                   wire:model="signatures.{{ $role->value }}.name"
                                                   class="form-input text-xs py-1 px-2 mt-1 min-h-[32px]"
                                                   placeholder="Nombre de quien firma">
                                        </div>
                                        <label class="form-check flex-shrink-0 cursor-pointer pt-4" title="Marcar si asistió">
                                            <input type="checkbox" wire:model="signatures.{{ $role->value }}.present" class="form-check-input">
                                            <span class="text-[11px] text-muted">Presente</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- MAESTRO QUE ELABORA --}}
                        <div class="form-group">
                            <label class="form-label font-bold text-xs text-gray-800">Nombre y Firma del Maestro que Elabora:</label>
                            <input type="text" wire:model="elaborated_by_name" class="form-input" placeholder="Nombre completo del docente">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" wire:click="$set('show', false)" class="btn btn-ghost">Cancelar</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove>{{ $isEditing ? 'Guardar Cambios' : 'Registrar Formato de Observación' }}</span>
                            <span wire:loading>Guardando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
