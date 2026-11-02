<div class="p-6 bg-white rounded-lg shadow-sm">
    <h2 class="text-xl font-bold text-gray-800 mb-6">Gestión de Accesos</h2>

    @if (session()->has('message'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-md">
            {{ session('message') }}
        </div>
    @endif

    @if(!$esAprobador)
        <div class="mb-8 p-6 border border-gray-200 rounded-lg bg-gray-50">
            <h3 class="text-lg font-bold text-gray-800 mb-2">Solicitar Nuevo Acceso</h3>
            <p class="text-sm text-gray-600 mb-6">
                Nota: Los accesos para Coordinación Académica y Dirección Escolar se asignarán estrictamente en modo de solo lectura. 
                Los Docentes obtendrán permisos de edición sobre el grupo o alumno solicitado una vez aprobados.
            </p>
            
            <form wire:submit="enviarSolicitud" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tipo de acceso</label>
                    <select wire:model.live="tipoAcceso" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#1F5C2E] focus:ring-[#1F5C2E]">
                        <option value="grupo">Grupo Completo</option>
                        <option value="alumno">Alumno Específico</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Seleccionar Objetivo</label>
                    <select wire:model="objetivoId" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#1F5C2E] focus:ring-[#1F5C2E]">
                        <option value="">-- Selecciona --</option>
                        @if($tipoAcceso === 'grupo')
                            @foreach($gruposDisponibles as $grupo)
                                <option value="{{ $grupo->id }}">{{ $grupo->full_label ?? $grupo->name }}</option>
                            @endforeach
                        @else
                            @foreach($alumnosDisponibles as $alumno)
                                <option value="{{ $alumno->id }}">{{ $alumno->full_name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Motivo de la solicitud</label>
                    <input type="text" wire:model="motivo" placeholder="Ej. Suplencia, Revisión técnica..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#1F5C2E] focus:ring-[#1F5C2E]">
                </div>

                <div>
                    <button type="submit" class="w-full bg-[#1F5C2E] text-white px-4 py-2 rounded-md hover:bg-[#154220] transition">
                        Enviar Solicitud
                    </button>
                </div>
            </form>
        </div>

        <h3 class="text-lg font-bold text-gray-800 mb-4">Mis Solicitudes</h3>
        <div class="overflow-x-auto border border-gray-200 rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Objetivo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Motivo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
    <tbody class="bg-white divide-y divide-gray-200">
        @forelse($misSolicitudes as $solicitud)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ $solicitud->student ? $solicitud->student->full_name : ($solicitud->group ? ($solicitud->group->full_label ?? $solicitud->group->name) : 'N/A') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $solicitud->reason }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                        {{ $solicitud->status === 'aprobado' ? 'bg-green-100 text-green-800' : ($solicitud->status === 'rechazado' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                        {{ ucfirst($solicitud->status) }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $solicitud->created_at->format('d/m/Y') }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                    @if($solicitud->status === 'aprobado')
                        @if($solicitud->group_id)
                            <a href="{{ route('groups.show', $solicitud->group_id) }}" class="text-[#1F5C2E] hover:text-[#154220] font-bold underline">Ver Grupo</a>
                        @elseif($solicitud->student_id)
                            <a href="{{ route('students.show', $solicitud->student_id) }}" class="text-[#1F5C2E] hover:text-[#154220] font-bold underline">Ver Alumno</a>
                        @endif
                    @else
                        <span class="text-gray-400">Sin acceso</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No has realizado ninguna solicitud.</td>
            </tr>
        @endforelse
    </tbody>
            </table>
        </div>

    @else
        <h3 class="text-lg font-bold text-gray-800 mb-4">Gestión de Solicitudes Pendientes</h3>
        
        <div class="overflow-x-auto border border-gray-200 sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Solicitante</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rol</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Objetivo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Motivo</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($solicitudesPendientes as $solicitud)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $solicitud->requester->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $solicitud->requester->role_label ?? $solicitud->requester->role }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $solicitud->student ? $solicitud->student->full_name : ($solicitud->group ? ($solicitud->group->full_label ?? $solicitud->group->name) : 'N/A') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $solicitud->reason }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button wire:click="procesarSolicitud({{ $solicitud->id }}, 'aprobado')" class="text-green-600 hover:text-green-900 mr-4 font-bold">Aceptar</button>
                                <button wire:click="procesarSolicitud({{ $solicitud->id }}, 'rechazado')" class="text-red-600 hover:text-red-900 font-bold">Rechazar</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No hay solicitudes pendientes por revisar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>