<div>
    @if($show)
        <div class="modal-backdrop" x-data @keydown.escape.window="$wire.set('show', false)">
            <div class="modal-panel">
                <div class="modal-header bg-amber-50/60 border-b">
                    <div>
                        <div class="text-[11px] font-bold tracking-widest text-[#1F5C2E] uppercase">INSTITUTO FÉLIX DE JESÚS ROUGIER · CIENCIA Y PUREZA</div>
                        <h2 class="modal-title text-xl text-amber-950 font-bold mt-0.5">
                            {{ $isEditing ? 'Editar Acuerdo' : 'Nuevo Acuerdo / Compromiso' }}
                        </h2>
                    </div>
                    <button type="button" wire:click="$set('show', false)" class="modal-close" aria-label="Cerrar modal">✕</button>
                </div>

                <form wire:submit="save">
                    <div class="modal-body space-y-5">
                        {{-- DESCRIPCIÓN DEL ACUERDO --}}
                        <div class="form-group">
                            <label class="form-label required font-bold text-sm text-amber-950">
                                Descripción del Acuerdo o Compromiso:
                            </label>
                            <textarea wire:model="description"
                                      rows="4"
                                      class="form-textarea form-textarea-lg @error('description') error @enderror"
                                      placeholder="Describa el acuerdo establecido, estrategia de apoyo o compromiso acordado..."></textarea>
                            @error('description') <span class="form-error">{{ $message }}</span> @enderror
                        </div>

                        {{-- RESPONSABLE Y FECHA --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="form-group">
                                <label class="form-label font-bold text-xs text-gray-800">Persona Responsable:</label>
                                <input type="text" wire:model="responsible_name"
                                       class="form-input"
                                       placeholder="Docente titular, padre de familia, especialista...">
                            </div>

                            <div class="form-group">
                                <label class="form-label font-bold text-xs text-gray-800">Fecha Compromiso:</label>
                                <input type="date" wire:model="due_date"
                                       class="form-input @error('due_date') error @enderror">
                                @error('due_date') <span class="form-error">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- ESTATUS --}}
                        <div class="form-group">
                            <label class="form-label required font-bold text-xs text-gray-800">Estatus Actual del Acuerdo:</label>
                            <div class="flex flex-wrap gap-3">
                                @foreach($statuses as $st)
                                    <label class="flex items-center gap-2 px-4 py-2.5 rounded-xl border-2 cursor-pointer transition-all
                                        {{ $status === $st->value
                                            ? 'border-amber-500 bg-amber-50'
                                            : 'border-gray-200 bg-white hover:border-amber-300' }}">
                                        <input type="radio" wire:model.live="status"
                                               value="{{ $st->value }}"
                                               class="hidden">
                                        <span class="badge
                                            {{ $st->color() === 'yellow' ? 'badge-yellow' :
                                               ($st->color() === 'blue' ? 'badge-blue' :
                                               ($st->color() === 'green' ? 'badge-green' : 'badge-gray')) }}">
                                            {{ $st->label() }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                            @error('status') <span class="form-error">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" wire:click="$set('show', false)" class="btn btn-ghost">Cancelar</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove>{{ $isEditing ? 'Guardar Cambios' : 'Registrar Acuerdo' }}</span>
                            <span wire:loading>Guardando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
