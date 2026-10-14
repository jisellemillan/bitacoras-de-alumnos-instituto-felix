<div>
    {{-- ENCABEZADO DE PÁGINA --}}
    <div class="page-header">
        <div class="page-header-info">
            <h1 class="page-title">Gestión de Usuarios y Permisos</h1>
            <p class="text-muted">Administración de cuentas institucionales y control granular de acceso por sección del sistema.</p>
        </div>
        <div class="page-header-actions">
            <button type="button" wire:click="openCreateModal" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span>Nuevo Usuario</span>
            </button>
        </div>
    </div>

    {{-- BARRA DE FILTROS --}}
    <div class="filters-bar">
        <div class="search-bar flex-1 min-w-[240px]">
            <span class="search-bar-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </span>
            <input type="text"
                   wire:model.live.debounce.300ms="search"
                   placeholder="Buscar usuario por nombre o correo..."
                   class="form-input"
                   aria-label="Buscar usuarios">
        </div>

        <div>
            <select wire:model.live="roleFilter" class="filter-select" aria-label="Filtrar por rol">
                <option value="">Todos los roles</option>
                @foreach($roles as $r)
                    <option value="{{ $r->value }}">{{ $r->label() }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- TABLA DE USUARIOS CON MENÚ DE TRES PUNTOS --}}
    <div class="data-table-wrapper mt-4">
        <table class="data-table table-responsive">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Rol Institucional</th>
                    <th>Permisos de Sección</th>
                    <th>Estatus</th>
                    <th class="text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                    <tr>
                        <td data-label="Usuario">
                            <div class="flex items-center gap-3">
                                <div class="student-avatar bg-green-100 text-green-900 font-bold">
                                    {{ strtoupper(substr($u->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-bold text-gray-900">{{ $u->name }}</div>
                                    <div class="text-xs text-muted">{{ $u->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td data-label="Rol">
                            <span class="badge {{ $u->isAdminTecnico() ? 'badge-yellow' : 'badge-green' }}">
                                {{ $u->role->label() }}
                            </span>
                        </td>
                        <td data-label="Permisos">
                            <div class="flex flex-wrap gap-1 max-w-md">
                                @if($u->isAdminTecnico())
                                    <span class="chip bg-amber-50 text-amber-900 border-amber-200 font-bold text-[11px] py-0 px-2">
                                        Acceso Total (Administrador)
                                    </span>
                                @elseif(!empty($u->section_permissions))
                                    @foreach($u->section_permissions as $secKey)
                                        @if(isset($availableSections[$secKey]))
                                            <span class="chip text-[11px] py-0 px-2 bg-slate-100 text-gray-800">
                                                {{ $availableSections[$secKey]['label'] }}
                                            </span>
                                        @endif
                                    @endforeach
                                @else
                                    <span class="text-xs text-red-600 italic">Sin secciones autorizadas</span>
                                @endif
                            </div>
                        </td>
                        <td data-label="Estatus">
                            <span class="badge {{ $u->is_active ? 'badge-green' : 'badge-gray' }}">
                                <span class="badge-dot"></span>
                                {{ $u->is_active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td data-label="Acciones" class="text-right">
                            <x-dropdown-actions
                                :editAction="'openEditModal(' . $u->id . ')'"
                                :deleteAction="$u->id !== auth()->id() ? 'deleteUser(' . $u->id . ')' : null"
                                :deleteConfirm="'¿Estás seguro de dar de baja a ' . $u->name . '?'">
                                <button type="button"
                                        wire:click="openDetailModal({{ $u->id }})"
                                        class="group flex w-full items-center px-4 py-2 text-xs font-medium text-gray-700 hover:bg-green-50 hover:text-green-900 transition-colors text-left"
                                        role="menuitem">
                                    <svg class="mr-2.5 h-4 w-4 text-gray-400 group-hover:text-green-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Ver detalles
                                </button>
                            </x-dropdown-actions>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="empty-state py-8">
                                <div class="empty-state-title">No hay usuarios registrados</div>
                                <div class="empty-state-message">Usa el botón superior para crear una nueva cuenta.</div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination">
            <div class="pagination-info">
                Total de cuentas activas: {{ $users->total() }}
            </div>
            <div>
                {{ $users->links() }}
            </div>
        </div>
    </div>

    {{-- MODAL DE CREACIÓN / EDICIÓN --}}
    @if($showModal)
        <div class="modal-backdrop" x-data @keydown.escape.window="$wire.set('showModal', false)">
            <div class="modal-panel modal-panel-lg">
                <div class="modal-header">
                    <div>
                        <h2 class="modal-title">{{ $isEditing ? 'Editar Cuenta de Usuario' : 'Crear Nueva Cuenta de Usuario' }}</h2>
                        <span class="text-xs text-muted">Definición de credenciales y autorización por módulo</span>
                    </div>
                    <button type="button" wire:click="$set('showModal', false)" class="modal-close" aria-label="Cerrar modal">✕</button>
                </div>

                <form wire:submit="save">
                    <div class="modal-body space-y-4">
                        {{-- BLOQUE 1: DATOS PERSONALES --}}
                        <div class="border-b pb-4">
                            <h3 class="text-xs font-bold text-green-900 uppercase tracking-wider mb-3">1. Datos Personales y Acceso</h3>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label class="form-label required">Nombres</label>
                                    <input type="text" wire:model="form.first_name" class="form-input @error('form.first_name') error @enderror" placeholder="Ej. Roberto">
                                    @error('form.first_name') <span class="form-error">{{ $message }}</span> @enderror
                                </div>

                                <div class="form-group">
                                    <label class="form-label required">Apellidos</label>
                                    <input type="text" wire:model="form.last_name" class="form-input @error('form.last_name') error @enderror" placeholder="Ej. Gómez Bolaños">
                                    @error('form.last_name') <span class="form-error">{{ $message }}</span> @enderror
                                </div>

                                <div class="form-group form-col-full">
                                    <label class="form-label required">Correo Electrónico Institucional</label>
                                    <input type="email" wire:model="form.email" class="form-input @error('form.email') error @enderror" placeholder="ejemplo@felix.edu.mx">
                                    @error('form.email') <span class="form-error">{{ $message }}</span> @enderror
                                </div>

                                <div class="form-group">
                                    <label class="form-label {{ $isEditing ? '' : 'required' }}">
                                        Contraseña {{ $isEditing ? '(dejar en blanco para conservar)' : '' }}
                                    </label>
                                    <input type="password" wire:model="form.password" class="form-input @error('form.password') error @enderror" placeholder="Mínimo 8 caracteres">
                                    @error('form.password') <span class="form-error">{{ $message }}</span> @enderror
                                </div>

                                <div class="form-group">
                                    <label class="form-label {{ $isEditing ? '' : 'required' }}">Confirmar Contraseña</label>
                                    <input type="password" wire:model="form.password_confirmation" class="form-input" placeholder="Repite la contraseña">
                                </div>
                            </div>
                        </div>

                        {{-- BLOQUE 2: ROL Y PERMISOS POR SECCIÓN --}}
                        <div>
                            <h3 class="text-xs font-bold text-green-900 uppercase tracking-wider mb-2">2. Rol y Asignación de Secciones Permitidas</h3>

                            <div class="form-group mb-4">
                                <label class="form-label required">Rol en la Institución</label>
                                <select wire:model="form.role" class="form-select @error('form.role') error @enderror">
                                    @foreach($roles as $r)
                                        <option value="{{ $r->value }}">{{ $r->label() }}</option>
                                    @endforeach
                                </select>
                                @error('form.role') <span class="form-error">{{ $message }}</span> @enderror
                            </div>

                            <label class="form-label font-semibold text-gray-900 block mb-2">
                                Secciones Habilitadas para esta Cuenta:
                            </label>
                            <p class="text-xs text-muted mb-3">Marca las casillas correspondientes a las áreas del sistema a las que tendrá acceso el usuario. El sistema bloqueará cualquier ingreso no autorizado.</p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 bg-slate-50 p-4 rounded-xl border">
                                @foreach($availableSections as $secKey => $secData)
                                    <label class="flex items-start gap-3 p-2 rounded-lg hover:bg-white transition-colors cursor-pointer border border-transparent hover:border-gray-200">
                                        <input type="checkbox"
                                               wire:model="form.section_permissions"
                                               value="{{ $secKey }}"
                                               class="form-check-input mt-1">
                                        <div>
                                            <span class="block text-sm font-bold text-gray-900">{{ $secData['label'] }}</span>
                                            <span class="block text-xs text-muted leading-tight mt-0.5">{{ $secData['desc'] }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            @error('form.section_permissions') <span class="form-error mt-2">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" wire:click="$set('showModal', false)" class="btn btn-ghost">Cancelar</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove>{{ $isEditing ? 'Guardar Cambios' : 'Crear Usuario' }}</span>
                            <span wire:loading>Procesando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- MODAL DE DETALLES DEL USUARIO --}}
    @if($showDetailModal && $viewingUser)
        <div class="modal-backdrop" x-data @keydown.escape.window="$wire.set('showDetailModal', false)">
            <div class="modal-panel modal-panel-sm">
                <div class="modal-header">
                    <h2 class="modal-title">Detalles de Cuenta</h2>
                    <button type="button" wire:click="$set('showDetailModal', false)" class="modal-close">✕</button>
                </div>
                <div class="modal-body space-y-4">
                    <div class="text-center pb-4 border-b">
                        <div class="w-16 h-16 rounded-full bg-green-100 text-green-900 flex items-center justify-center font-bold text-2xl mx-auto mb-2">
                            {{ strtoupper(substr($viewingUser->name, 0, 1)) }}
                        </div>
                        <h3 class="font-bold text-lg text-gray-900">{{ $viewingUser->name }}</h3>
                        <p class="text-xs text-muted">{{ $viewingUser->email }}</p>
                        <span class="badge badge-green mt-2">{{ $viewingUser->role->label() }}</span>
                    </div>

                    <div>
                        <h4 class="text-xs font-bold uppercase text-muted tracking-wider mb-2">Permisos asignados</h4>
                        <div class="space-y-1">
                            @if($viewingUser->isAdminTecnico())
                                <div class="text-xs bg-amber-50 text-amber-900 p-2 rounded border border-amber-200 font-semibold">
                                    Acceso Total Administrativo
                                </div>
                            @elseif(!empty($viewingUser->section_permissions))
                                @foreach($viewingUser->section_permissions as $p)
                                    <div class="text-xs bg-slate-100 p-2 rounded flex items-center gap-2">
                                        <span class="text-green-600 font-bold">✓</span>
                                        <span>{{ $availableSections[$p]['label'] ?? $p }}</span>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-xs text-red-600 italic">No tiene permisos asignados</div>
                            @endif
                        </div>
                    </div>

                    <div class="pt-2 text-xs text-muted border-t">
                        Fecha de registro: {{ $viewingUser->created_at->format('d/m/Y H:i') }}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" wire:click="$set('showDetailModal', false)" class="btn btn-light w-full">Cerrar</button>
                </div>
            </div>
        </div>
    @endif
</div>
