<div>
    @if($show)
        <div class="modal-backdrop" x-data @keydown.escape.window="$wire.set('show', false)">
            <div class="modal-panel modal-panel-xl">
                {{-- Encabezado fiel al formato físico del Instituto Félix --}}
                <div class="modal-header bg-blue-50/60 border-b">
                    <div>
                        <div class="text-[11px] font-bold tracking-widest text-[#1F5C2E] uppercase">INSTITUTO FÉLIX DE JESÚS ROUGIER · CIENCIA Y PUREZA</div>
                        <h2 class="modal-title text-xl text-blue-950 font-bold mt-0.5">
                            {{ $isEditing ? 'Editar Entrevista' : 'Formato: Entrevista con Padres y/o Especialista' }}
                        </h2>
                    </div>
                    <button type="button" wire:click="$set('show', false)" class="modal-close" aria-label="Cerrar modal">✕</button>
                </div>

                <form wire:submit="save">
                    <div class="modal-body space-y-5">
                        {{-- ENCABEZADO: FECHA, GRADO Y MODALIDAD --}}
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 bg-slate-50 rounded-xl border">
                            <div class="form-group">
                                <label class="form-label required font-bold text-xs">Fecha de la Entrevista:</label>
                                <input type="date" wire:model="interview_date"
                                       class="form-input @error('interview_date') error @enderror">
                                @error('interview_date') <span class="form-error">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label font-bold text-xs">Grado y Grupo:</label>
                                <input type="text" wire:model="grade_group"
                                       class="form-input" placeholder="Ej. 5°B">
                            </div>

                            <div class="form-group">
                                <label class="form-label required font-bold text-xs">Modalidad:</label>
                                <select wire:model="modality"
                                        class="form-select @error('modality') error @enderror">
                                    @foreach($modalities as $mod)
                                        <option value="{{ $mod->value }}">{{ $mod->label() }}</option>
                                    @endforeach
                                </select>
                                @error('modality') <span class="form-error">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- MOTIVO Y OBJETIVO --}}
                        <div class="form-group">
                            <label class="form-label required font-bold text-sm text-blue-950">
                                Motivo y Objetivo de la Entrevista:
                            </label>
                            <textarea wire:model="purpose_and_objective"
                                      rows="4"
                                      class="form-textarea form-textarea-lg @error('purpose_and_objective') error @enderror"
                                      placeholder="Describa el motivo de la cita y el objetivo a alcanzar en la entrevista..."></textarea>
                            @error('purpose_and_objective')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- COMENTARIOS Y ACUERDOS --}}
                        <div class="form-group">
                            <label class="form-label font-bold text-sm text-blue-950">
                                Comentarios, Conclusiones y Acuerdos:
                            </label>
                            <textarea wire:model="comments"
                                      rows="4"
                                      class="form-textarea"
                                      placeholder="Resumen de lo tratado, acuerdos a los que se llegó, compromisos adquiridos..."></textarea>
                        </div>

                        {{-- ASISTENTES Y QUIEN CONDUCE --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="form-group">
                                <label class="form-label font-bold text-xs text-gray-800">
                                    Asistentes (padres, tutores, especialistas):
                                </label>
                                <input type="text" wire:model="attendees"
                                       class="form-input"
                                       placeholder="Ej. Madre del alumno, Dr. Ramírez (especialista)">
                                <span class="form-hint">Separe varios nombres con coma.</span>
                            </div>

                            <div class="form-group">
                                <label class="form-label font-bold text-xs text-gray-800">
                                    Entrevista conducida por:
                                </label>
                                <input type="text" wire:model="conducted_by_name"
                                       class="form-input"
                                       placeholder="Nombre del psicopedagogo o docente">
                            </div>
                        </div>

                        {{-- NOTA SOBRE SEGUIMIENTO --}}
                        <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-xs text-amber-800">
                            <strong>Nota:</strong> Si se acordó una próxima cita al finalizar la entrevista, regístrela en la pestaña de <strong>Citas</strong> para que quede asentada en el calendario institucional.
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" wire:click="$set('show', false)" class="btn btn-ghost">Cancelar</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove>{{ $isEditing ? 'Guardar Cambios' : 'Registrar Entrevista' }}</span>
                            <span wire:loading>Guardando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
