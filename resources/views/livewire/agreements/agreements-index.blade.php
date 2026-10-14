<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Seguimiento de Acuerdos y Compromisos</h1>
            <p class="text-sm text-gray-500 mt-1">Gestión psicopedagógica y acuerdos derivados de observaciones y entrevistas</p>
        </div>
        <div class="flex items-center gap-2">
            <div class="bg-gray-100 p-1 rounded-xl flex items-center border">
                <button type="button" wire:click="$set('viewMode', 'kanban')"
                        class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors {{ $viewMode === 'kanban' ? 'bg-white shadow text-green-900' : 'text-gray-500 hover:text-gray-700' }}">
                    Tablero Kanban
                </button>
                <button type="button" wire:click="$set('viewMode', 'list')"
                        class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors {{ $viewMode === 'list' ? 'bg-white shadow text-green-900' : 'text-gray-500 hover:text-gray-700' }}">
                    Listado
                </button>
            </div>
        </div>
    </div>

    {{-- Filtros y Búsqueda --}}
    <div class="card p-4 bg-white">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="sm:col-span-2">
                <input type="text" wire:model.live.debounce.300ms="search"
                       class="form-input text-sm w-full"
                       placeholder="Buscar por descripción, alumno o responsable...">
            </div>
            <div>
                <select wire:model.live="statusFilter" class="form-select text-sm w-full">
                    <option value="">Todos los estados</option>
                    @foreach($statuses as $st)
                        <option value="{{ $st->value }}">{{ $st->label() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Vista Tablero Kanban --}}
    @if($viewMode === 'kanban')
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($statuses as $status)
                @php
                    $statusKey = $status->value;
                    $items = $groupedAgreements[$statusKey] ?? collect();
                    $colorClass = match($statusKey) {
                        'pendiente' => 'border-t-4 border-amber-500 bg-amber-50/20',
                        'en_proceso' => 'border-t-4 border-blue-500 bg-blue-50/20',
                        'cumplido' => 'border-t-4 border-green-600 bg-green-50/20',
                        'cancelado' => 'border-t-4 border-gray-400 bg-gray-50/20',
                        default => 'border-t-4 border-gray-300'
                    };
                @endphp
                <div class="rounded-xl border bg-white shadow-sm flex flex-col {{ $colorClass }}">
                    <div class="p-3 border-b flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-xs uppercase tracking-wider text-gray-800">{{ $status->label() }}</span>
                            <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-white border text-gray-700">{{ $items->count() }}</span>
                        </div>
                    </div>
                    <div class="p-3 space-y-3 flex-1 overflow-y-auto max-h-[650px]">
                        @forelse($items as $agr)
                            <div class="p-3 bg-white rounded-xl border shadow-sm hover:shadow-md transition-shadow relative space-y-2">
                                <div class="flex items-start justify-between gap-2">
                                    <a href="{{ route('students.show', $agr->student) }}" class="text-xs font-bold text-green-900 hover:underline">
                                        {{ $agr->student->full_name }}
                                        <span class="text-[10px] text-gray-500 block">Grado: {{ $agr->student->current_group_label }}</span>
                                    </a>
                                    <x-dropdown-actions>
                                        <x-slot name="items">
                                            <a href="{{ route('students.show', ['student' => $agr->student, 'tab' => 'acuerdos']) }}" class="dropdown-item">
                                                Ver en expediente
                                            </a>
                                            <button type="button"
                                                    wire:click="deleteAgreement({{ $agr->id }})"
                                                    wire:confirm="¿Seguro que deseas eliminar este acuerdo?"
                                                    class="dropdown-item dropdown-item-danger">
                                                Eliminar
                                            </button>
                                        </x-slot>
                                    </x-dropdown-actions>
                                </div>

                                <p class="text-xs text-gray-800 line-clamp-3">{{ $agr->description }}</p>

                                <div class="pt-2 border-t text-[11px] text-gray-500 space-y-1">
                                    <div class="flex items-center justify-between">
                                        <span>Resp: <strong>{{ $agr->responsible_name ?? 'Titular' }}</strong></span>
                                        @if($agr->due_date)
                                            <span class="{{ $agr->due_date->isPast() && $agr->status->value !== 'cumplido' ? 'text-red-600 font-bold' : '' }}">
                                                📅 {{ $agr->due_date->format('d/m/Y') }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="pt-1 flex flex-wrap gap-1">
                                        @foreach($statuses as $changeSt)
                                            @if($changeSt->value !== $statusKey)
                                                <button type="button"
                                                        wire:click="updateStatus({{ $agr->id }}, '{{ $changeSt->value }}')"
                                                        class="text-[9px] px-1.5 py-0.5 rounded border bg-gray-50 hover:bg-green-50 hover:text-green-800 transition-colors">
                                                    → {{ $changeSt->label() }}
                                                </button>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="py-8 text-center text-xs text-gray-400 italic">
                                Sin acuerdos en este estado
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    @else
        {{-- Vista Listado / Tabla con menú ⋮ --}}
        <div class="card overflow-hidden bg-white border rounded-xl">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Alumno</th>
                            <th>Compromiso / Acuerdo</th>
                            <th>Responsable</th>
                            <th>Fecha Compromiso</th>
                            <th>Estado</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($agreements as $agr)
                            <tr>
                                <td>
                                    <a href="{{ route('students.show', $agr->student) }}" class="font-bold text-green-900 hover:underline">
                                        {{ $agr->student->full_name }}
                                    </a>
                                    <span class="text-xs text-gray-500 block">{{ $agr->student->current_group_label }}</span>
                                </td>
                                <td>
                                    <div class="text-xs text-gray-800 max-w-md">{{ $agr->description }}</div>
                                </td>
                                <td>
                                    <span class="text-xs text-gray-700">{{ $agr->responsible_name ?? 'Docente titular' }}</span>
                                </td>
                                <td>
                                    @if($agr->due_date)
                                        <span class="text-xs {{ $agr->due_date->isPast() && $agr->status->value !== 'cumplido' ? 'text-red-600 font-bold' : 'text-gray-600' }}">
                                            {{ $agr->due_date->format('d/m/Y') }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400">Sin fecha</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ match($agr->status->value) {
                                        'pendiente' => 'badge-yellow',
                                        'en_proceso' => 'badge-blue',
                                        'cumplido' => 'badge-green',
                                        'cancelado' => 'badge-gray',
                                        default => 'badge-gray'
                                    } }}">
                                        {{ $agr->status->label() }}
                                    </span>
                                </td>
                                <td class="text-right">
                                    <x-dropdown-actions>
                                        <x-slot name="items">
                                            <a href="{{ route('students.show', ['student' => $agr->student, 'tab' => 'acuerdos']) }}" class="dropdown-item">
                                                Ver expediente
                                            </a>
                                            <button type="button"
                                                    wire:click="deleteAgreement({{ $agr->id }})"
                                                    wire:confirm="¿Seguro que deseas eliminar este acuerdo?"
                                                    class="dropdown-item dropdown-item-danger">
                                                Eliminar
                                            </button>
                                        </x-slot>
                                    </x-dropdown-actions>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-8 text-gray-400 text-sm">
                                    No se encontraron acuerdos con los filtros aplicados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(method_exists($agreements, 'hasPages') && $agreements->hasPages())
                <div class="p-4 border-t">
                    {{ $agreements->links() }}
                </div>
            @endif
        </div>
    @endif
</div>
