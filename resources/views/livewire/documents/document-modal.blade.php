<div>
    @if($show)
        <div class="modal-backdrop" x-data @keydown.escape.window="$wire.set('show', false)">
            <div class="modal-panel">
                <div class="modal-header bg-slate-50/80 border-b">
                    <div>
                        <div class="text-[11px] font-bold tracking-widest text-[#1F5C2E] uppercase">INSTITUTO FÉLIX DE JESÚS ROUGIER · CIENCIA Y PUREZA</div>
                        <h2 class="modal-title text-xl text-gray-900 font-bold mt-0.5">
                            {{ $isEditing ? 'Editar Documento' : 'Adjuntar Documento al Expediente' }}
                        </h2>
                    </div>
                    <button type="button" wire:click="$set('show', false)" class="modal-close" aria-label="Cerrar modal">✕</button>
                </div>

                <form wire:submit="save">
                    <div class="modal-body space-y-5">
                        {{-- AVISO CONFIDENCIALIDAD --}}
                        <div class="p-3 bg-red-50 rounded-xl border border-red-200 text-xs text-red-800">
                            <strong>🔒 Almacenamiento Privado:</strong> Los documentos se guardan en almacenamiento privado institucional cifrado. Solo usuarios autorizados pueden acceder.
                        </div>

                        {{-- TÍTULO --}}
                        <div class="form-group">
                            <label class="form-label required font-bold text-sm">Título del Documento:</label>
                            <input type="text" wire:model="title"
                                   class="form-input @error('title') error @enderror"
                                   placeholder="Ej. Dictamen neurológico 2024, Evaluación psicológica CRIT...">
                            @error('title') <span class="form-error">{{ $message }}</span> @enderror
                        </div>

                        {{-- CATEGORÍA --}}
                        <div class="form-group">
                            <label class="form-label required font-bold text-xs">Categoría del Documento:</label>
                            <select wire:model="category" class="form-select">
                                @foreach($categories as $val => $label)
                                    <option value="{{ $val }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- DESCRIPCIÓN --}}
                        <div class="form-group">
                            <label class="form-label font-bold text-xs">Descripción breve (opcional):</label>
                            <textarea wire:model="description"
                                      rows="2"
                                      class="form-textarea"
                                      placeholder="Institución emisora, fecha de elaboración, observaciones relevantes..."></textarea>
                        </div>

                        {{-- ARCHIVO --}}
                        <div class="form-group">
                            <label class="form-label {{ !$isEditing ? 'required' : '' }} font-bold text-xs">
                                {{ $isEditing ? 'Reemplazar Archivo (opcional):' : 'Seleccionar Archivo:' }}
                            </label>
                            <input type="file" wire:model="file"
                                   accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                   class="form-input py-1.5 @error('file') error @enderror">
                            @error('file') <span class="form-error">{{ $message }}</span> @enderror
                            <span class="form-hint">Formatos permitidos: PDF, JPG, PNG, DOC, DOCX. Máximo 10 MB.</span>

                            {{-- Preview de carga --}}
                            <div wire:loading wire:target="file" class="mt-2 text-xs text-green-700 font-medium">
                                ⬆ Subiendo archivo...
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" wire:click="$set('show', false)" class="btn btn-ghost">Cancelar</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="save">{{ $isEditing ? 'Guardar Cambios' : 'Subir Documento' }}</span>
                            <span wire:loading wire:target="save">Guardando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
