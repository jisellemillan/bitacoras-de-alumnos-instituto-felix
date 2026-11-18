<div>
    {{-- ENCABEZADO DE PÁGINA --}}
    <div class="page-header">
        <div>
            <div class="page-pretitle">ADMINISTRACIÓN</div>
            <h1 class="page-title">Solicitudes de Contraseña</h1>
            <p class="page-subtitle">Gestiona las solicitudes de recuperación de contraseña de los usuarios del sistema.</p>
        </div>
        @if($totalPendientes > 0)
            <div class="page-actions">
                <span class="badge badge-red font-bold text-sm">
                    <span class="badge-dot"></span>
                    {{ $totalPendientes }} {{ $totalPendientes === 1 ? 'solicitud pendiente' : 'solicitudes pendientes' }}
                </span>
            </div>
        @endif
    </div>

    {{-- FILTROS --}}
    <div class="table-card mb-6">
        <div class="p-4 flex flex-col sm:flex-row gap-3 items-stretch sm:items-center bg-white border-b">
            <div class="flex-1 flex gap-3">
                <div class="w-full sm:w-48">
                    <select wire:model.live="filtroEstado" class="form-select text-sm w-full">
                        <option value="">Todas las solicitudes</option>
                        <option value="pendiente">Pendientes</option>
                        <option value="atendida">Atendidas</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- TABLA DE SOLICITUDES --}}
        <table class="data-table">
            <thead>
                <tr>
                    <th>Correo electrónico</th>
                    <th>Usuario</th>
                    <th>Fecha de solicitud</th>
                    <th>Estado</th>
                    <th>Atendida por</th>
                    <th class="text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($solicitudes as $sol)
                    <tr>
                        <td data-label="Correo">
                            <span class="font-mono text-sm font-medium text-gray-800">{{ $sol->email }}</span>
                        </td>
                        <td data-label="Usuario">
                            @if($sol->user)
                                <div>
                                    <span class="font-semibold text-sm text-gray-900 block">
                                        {{ $sol->user->first_name }} {{ $sol->user->last_name }}
                                    </span>
                                    <span class="text-xs text-muted">{{ $sol->user->role->label() }}</span>
                                </div>
                            @else
                                <span class="text-xs text-muted italic">Usuario no encontrado</span>
                            @endif
                        </td>
                        <td data-label="Fecha">
                            <div>
                                <span class="text-sm text-gray-800 block">{{ $sol->created_at->format('d/m/Y') }}</span>
                                <span class="text-xs text-muted">{{ $sol->created_at->format('H:i') }} hrs</span>
                            </div>
                        </td>
                        <td data-label="Estado">
                            @if($sol->isPendiente())
                                <span class="badge badge-yellow">
                                    <span class="badge-dot"></span>
                                    Pendiente
                                </span>
                            @else
                                <span class="badge badge-green">
                                    <span class="badge-dot"></span>
                                    Atendida
                                </span>
                            @endif
                        </td>
                        <td data-label="Atendida por">
                            @if($sol->attendedBy)
                                <div>
                                    <span class="text-sm text-gray-800 block">{{ $sol->attendedBy->name }}</span>
                                    @if($sol->atendida_en)
                                        <span class="text-xs text-muted">{{ $sol->atendida_en->format('d/m/Y H:i') }}</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-xs text-muted">—</span>
                            @endif
                        </td>
                        <td data-label="Acciones" class="text-right">
                            @if($sol->isPendiente())
                                <button type="button"
                                        wire:click="procesarSolicitud({{ $sol->id }})"
                                        wire:confirm="¿Generar una nueva contraseña para {{ $sol->email }}? Esta acción actualizará la contraseña del usuario de inmediato."
                                        class="btn btn-primary btn-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                    </svg>
                                    Generar contraseña
                                </button>
                            @else
                                <span class="text-xs text-muted italic">Atendida</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state py-12">
                                <div class="w-14 h-14 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center mx-auto mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                    </svg>
                                </div>
                                <div class="empty-state-title">No hay solicitudes</div>
                                <div class="empty-state-message">
                                    @if($filtroEstado)
                                        No hay solicitudes con el filtro seleccionado.
                                    @else
                                        Cuando un usuario solicite recuperar su contraseña, aparecerá aquí.
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- PAGINACIÓN --}}
        <div class="pagination">
            <div class="pagination-info">
                Mostrando {{ $solicitudes->firstItem() ?? 0 }} a {{ $solicitudes->lastItem() ?? 0 }} de {{ $solicitudes->total() }} solicitudes
            </div>
            <div>{{ $solicitudes->links() }}</div>
        </div>
    </div>

    {{-- MODAL: Contraseña generada --}}
    @if($mostrarContrasena && $contrasenaNueva)
        <div style="position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:1000;display:flex;align-items:center;justify-content:center;padding:1rem;">
            <div style="background:white;border-radius:1rem;padding:2rem;max-width:480px;width:100%;box-shadow:0 20px 60px rgba(0,0,0,0.3);">
                {{-- Ícono de éxito --}}
                <div style="text-align:center;margin-bottom:1.5rem;">
                    <div style="width:60px;height:60px;border-radius:50%;background:#dcfce7;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="#16a34a" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <h2 style="font-size:1.2rem;font-weight:700;color:#14532d;margin-bottom:0.5rem;">Contraseña generada</h2>
                    <p style="font-size:0.875rem;color:#6b7280;">
                        La contraseña del usuario ha sido actualizada. 
                        Entrégala al usuario de forma segura. <strong>Solo se mostrará una vez.</strong>
                    </p>
                </div>

                {{-- Contraseña generada --}}
                <div style="background:#f0fdf4;border:2px solid #86efac;border-radius:0.75rem;padding:1.25rem;margin-bottom:1.5rem;text-align:center;">
                    <div style="font-size:0.75rem;color:#15803d;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.5rem;">Nueva contraseña</div>
                    <div style="font-family:monospace;font-size:1.5rem;font-weight:700;color:#14532d;letter-spacing:0.1em;user-select:all;cursor:text;" 
                         x-data
                         x-on:click="navigator.clipboard.writeText('{{ $contrasenaNueva }}').then(() => alert('Contraseña copiada al portapapeles'))">
                        {{ $contrasenaNueva }}
                    </div>
                    <div style="font-size:0.75rem;color:#6b7280;margin-top:0.5rem;">Haz clic en la contraseña para copiarla</div>
                </div>

                {{-- Aviso --}}
                <div style="background:#fefce8;border:1px solid #fde047;border-radius:0.5rem;padding:0.75rem 1rem;margin-bottom:1.5rem;font-size:0.8rem;color:#713f12;">
                    ⚠️ Esta ventana es la única oportunidad para ver esta contraseña. Al cerrarla no podrá recuperarse. Si necesitas cambiarla de nuevo, procesa una nueva solicitud.
                </div>

                <button type="button"
                        wire:click="cerrarModal"
                        style="width:100%;padding:0.75rem;background:#1a1a2e;color:white;border:none;border-radius:0.5rem;font-size:0.875rem;font-weight:600;cursor:pointer;">
                    Ya entregué la contraseña — Cerrar
                </button>
            </div>
        </div>
    @endif
</div>
