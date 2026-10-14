<div class="space-y-6">
    {{-- Modal para creación y edición de citas --}}
    <livewire:appointments.appointment-modal />

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Calendario Institucional de Citas</h1>
            <p class="text-sm text-gray-500 mt-1">Agenda de entrevistas con padres, seguimiento psicopedagógico y reuniones de equipo</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button"
                    wire:click="$dispatch('open-appointment-modal')"
                    class="btn btn-primary btn-sm flex items-center gap-1.5 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span>Nueva Cita</span>
            </button>
            <button type="button" wire:click="today" class="btn btn-outline btn-sm">
                Hoy
            </button>
            <div class="flex items-center bg-white border rounded-lg shadow-sm">
                <button type="button" wire:click="previousMonth" class="px-3 py-1.5 hover:bg-gray-50 text-gray-600 rounded-l-lg border-r">
                    ◀
                </button>
                <span class="px-4 py-1.5 font-bold text-sm text-green-950 uppercase tracking-wide">
                    {{ $monthName }}
                </span>
                <button type="button" wire:click="nextMonth" class="px-3 py-1.5 hover:bg-gray-50 text-gray-600 rounded-r-lg border-l">
                    ▶
                </button>
            </div>
        </div>
    </div>

    {{-- Filtro de Estado --}}
    <div class="flex items-center justify-between bg-white p-3 rounded-xl border">
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold text-gray-600 uppercase">Filtrar por:</span>
            <select wire:model.live="statusFilter" class="form-select text-xs py-1">
                <option value="">Todas las citas</option>
                <option value="programada">Programadas</option>
                <option value="completada">Completadas</option>
                <option value="reprogramada">Reprogramadas</option>
                <option value="cancelada">Canceladas</option>
            </select>
        </div>
        <div class="flex items-center gap-4 text-xs">
            <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-green-600"></span> Programada</div>
            <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span> Completada</div>
            <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Reprogramada</div>
            <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-gray-400"></span> Cancelada</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        {{-- Calendario Mensual --}}
        <div class="lg:col-span-3 bg-white rounded-xl border shadow-sm overflow-hidden">
            {{-- Días de la semana --}}
            <div class="grid grid-cols-7 bg-gray-50 border-b text-center py-2.5 text-xs font-bold text-gray-600 uppercase tracking-wider">
                <div>Lun</div>
                <div>Mar</div>
                <div>Mié</div>
                <div>Jue</div>
                <div>Vie</div>
                <div>Sáb</div>
                <div>Dom</div>
            </div>

            {{-- Días del mes --}}
            <div class="grid grid-cols-7 auto-rows-fr divide-x divide-y divide-gray-100">
                @foreach($days as $day)
                    <div class="min-h-[110px] p-2 flex flex-col {{ $day['isCurrentMonth'] ? 'bg-white' : 'bg-gray-50/60 text-gray-400' }} {{ $day['isToday'] ? 'ring-2 ring-inset ring-green-600 bg-green-50/20' : '' }}">
                        <div class="flex items-center justify-between mb-1">
                            <button type="button"
                                    wire:click="$dispatch('open-appointment-modal', { defaultDate: '{{ $day['date']->format('Y-m-d') }}' })"
                                    class="text-xs font-bold hover:underline cursor-pointer {{ $day['isToday'] ? 'w-6 h-6 rounded-full bg-green-700 text-white flex items-center justify-center' : 'text-gray-700' }}"
                                    title="Click para agendar cita este día">
                                {{ $day['date']->format('j') }}
                            </button>
                            @if($day['appointments']->count())
                                <span class="text-[10px] font-semibold text-gray-400">{{ $day['appointments']->count() }}</span>
                            @endif
                        </div>

                        {{-- Lista de citas en el día --}}
                        <div class="space-y-1 overflow-y-auto max-h-[85px]">
                            @foreach($day['appointments'] as $app)
                                @php
                                    $badgeColor = match($app->status) {
                                        'programada' => 'bg-green-100 text-green-800 border-green-200 hover:bg-green-200',
                                        'completada' => 'bg-blue-100 text-blue-800 border-blue-200 hover:bg-blue-200',
                                        'reprogramada' => 'bg-amber-100 text-amber-800 border-amber-200 hover:bg-amber-200',
                                        'cancelada' => 'bg-gray-100 text-gray-600 border-gray-200 hover:bg-gray-200',
                                        default => 'bg-green-100 text-green-800 border-green-200 hover:bg-green-200'
                                    };
                                @endphp
                                <button type="button"
                                        wire:click="$dispatch('open-appointment-edit', { appointmentId: {{ $app->id }} })"
                                        class="w-full text-left p-1 rounded border text-[11px] leading-tight {{ $badgeColor }} truncate group relative cursor-pointer transition-colors"
                                        title="{{ $app->title }} ({{ $app->student->full_name }}) — Click para editar">
                                    <span class="font-bold">{{ $app->starts_at->format('H:i') }}</span>
                                    <span class="truncate block font-medium">{{ $app->student->first_name }}: {{ $app->title }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Panel lateral: Próximas Citas y Acciones --}}
        <div class="space-y-6">
            <div class="card bg-white p-4">
                <h2 class="font-bold text-sm text-green-950 uppercase tracking-wider mb-3">Próximas Citas en Agenda</h2>
                <div class="space-y-3">
                    @forelse($upcomingAppointments as $upApp)
                        <div class="p-3 rounded-xl border bg-slate-50 space-y-1 text-xs">
                            <div class="flex items-start justify-between gap-1">
                                <a href="{{ route('students.show', ['student' => $upApp->student, 'tab' => 'citas']) }}" class="font-bold text-green-900 hover:underline">
                                    {{ $upApp->student->full_name }}
                                </a>
                                <x-dropdown-actions>
                                    <x-slot name="items">
                                        <a href="{{ route('students.show', ['student' => $upApp->student, 'tab' => 'citas']) }}" class="dropdown-item">
                                            Ver en expediente
                                        </a>
                                        <button type="button"
                                                wire:click="$dispatch('open-appointment-edit', { appointmentId: {{ $upApp->id }} })"
                                                class="dropdown-item">
                                            Editar cita
                                        </button>
                                        <button type="button"
                                                wire:click="updateStatus({{ $upApp->id }}, 'completada')"
                                                class="dropdown-item">
                                            Marcar completada
                                        </button>
                                        <button type="button"
                                                wire:click="deleteAppointment({{ $upApp->id }})"
                                                wire:confirm="¿Seguro que deseas eliminar esta cita?"
                                                class="dropdown-item dropdown-item-danger">
                                            Eliminar
                                        </button>
                                    </x-slot>
                                </x-dropdown-actions>
                            </div>
                            <div class="font-medium text-gray-800">{{ $upApp->title }}</div>
                            <div class="text-[11px] text-gray-500">
                                📅 {{ $upApp->starts_at->format('d/m/Y H:i') }} hrs
                            </div>
                            @if($upApp->location)
                                <div class="text-[11px] text-gray-500">📍 {{ $upApp->location }}</div>
                            @endif
                        </div>
                    @empty
                        <div class="py-6 text-center text-xs text-gray-400 italic">
                            No hay citas próximas agendadas.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="card bg-green-50/50 p-4 border border-green-200">
                <h3 class="font-bold text-xs text-green-900 uppercase tracking-wider mb-1">Citas desde Expedientes</h3>
                <p class="text-xs text-gray-600">
                    Las citas se programan automáticamente desde el expediente del alumno (pestaña Citas o formato de Observación).
                </p>
                <div class="mt-3">
                    <a href="{{ route('students.index') }}" class="btn btn-outline btn-sm w-full text-center">
                        Ir al listado de alumnos →
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
