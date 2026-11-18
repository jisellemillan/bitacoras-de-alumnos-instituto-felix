<div>
    {{-- ── MODALES DE FASE 3 ────────────────────────────────────────────── --}}
    <livewire:observations.observation-modal />
    <livewire:interviews.interview-modal />
    <livewire:agreements.agreement-modal />
    <livewire:appointments.appointment-modal />
    <livewire:documents.document-modal />

    {{-- ── MIGA DE PAN / BREADCRUMBS JERÁRQUICOS ─────────────────────── --}}
    @php
        $targetGroup = $fromGroupId ? \App\Models\Group::find($fromGroupId) : $student->currentEnrollment?->group;
    @endphp
    <nav class="mb-3 flex items-center gap-2 text-xs text-muted" aria-label="Ruta de navegación">
        @if($targetGroup)
            <a href="{{ route('groups.index') }}" class="hover:text-green-800 transition-colors font-medium flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                Gestión de Grupos
            </a>
            <span>/</span>
            <a href="{{ route('groups.show', $targetGroup) }}" class="hover:text-green-800 transition-colors font-medium">
                Grupo {{ $targetGroup->label }}
            </a>
            <span>/</span>
            <span class="text-green-950 font-bold">{{ $student->full_name }}</span>
        @else
            <a href="{{ route('students.index') }}" class="hover:text-green-800 transition-colors font-medium flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Directorio de Alumnos
            </a>
            <span>/</span>
            <span class="text-green-950 font-bold">{{ $student->full_name }}</span>
        @endif
    </nav>

    {{-- ENCABEZADO DE PERFIL DEL EXPEDIENTE --}}
    <div class="file-profile-header">
        <div class="file-profile-avatar">
            {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-3 flex-wrap">
                <h1 class="file-profile-name text-white">{{ $student->full_name }}</h1>
                @if($file && $file->status)
                    @php $c = $file->status->color(); @endphp
                    <span class="badge {{ $c === 'green' ? 'badge-green bg-white text-green-900' : 'badge-yellow' }}">
                        <span class="badge-dot"></span>
                        {{ $file->status->label() }}
                    </span>
                @endif
                @if($file && $file->intake_route)
                    <span class="badge badge-yellow font-bold">
                        Ruta {{ $file->intake_route->value }}
                    </span>
                @endif
            </div>

            <div class="file-profile-meta mt-2">
                <span><strong>Grupo:</strong> {{ $student->current_group_label }}</span>
                <span>·</span>
                <span><strong>CURP:</strong> {{ $student->curp ?? 'Sin registrar' }}</span>
                <span>·</span>
                <span><strong>Edad:</strong> {{ $student->age ? $student->age . ' años' : 'N/D' }}</span>
                <span>·</span>
                <span><strong>Apertura de expediente:</strong> {{ $file?->opened_at?->format('d/m/Y') ?? 'N/D' }}</span>
            </div>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            @if($targetGroup)
                <a href="{{ route('groups.show', $targetGroup) }}"
                   class="btn btn-sm font-bold shadow-sm inline-flex items-center gap-1.5 transition-all text-emerald-950 bg-white hover:bg-emerald-50 border border-emerald-200 hover:shadow">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                    <span>Volver a Grupo {{ $targetGroup->label }}</span>
                </a>
            @else
                <a href="{{ route('students.index') }}"
                   class="btn btn-sm font-bold shadow-sm inline-flex items-center gap-1.5 transition-all text-emerald-950 bg-white hover:bg-emerald-50 border border-emerald-200 hover:shadow">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                    <span>Volver al listado</span>
                </a>
            @endif
            <a href="{{ route('reports.pdf.student', $student) }}"
               target="_blank"
               class="btn btn-secondary btn-sm font-bold shadow-sm inline-flex items-center gap-1.5 hover:shadow">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Descargar expediente PDF</span>
            </a>
        </div>
    </div>

    {{-- NAVEGACIÓN POR PESTAÑAS --}}
    <div class="tabs-nav mb-4 bg-white px-4 rounded-xl border">
        <button type="button" wire:click="setTab('resumen')" class="tab-btn {{ $activeTab === 'resumen' ? 'active' : '' }}">
            <span>Resumen Clínico</span>
        </button>

        <button type="button" wire:click="setTab('observaciones')" class="tab-btn {{ $activeTab === 'observaciones' ? 'active' : '' }}">
            <span>Observaciones</span>
            <span class="tab-count">{{ $observations->count() }}</span>
        </button>

        <button type="button" wire:click="setTab('entrevistas')" class="tab-btn {{ $activeTab === 'entrevistas' ? 'active' : '' }}">
            <span>Entrevistas</span>
            <span class="tab-count">{{ $interviews->count() }}</span>
        </button>

        <button type="button" wire:click="setTab('acuerdos')" class="tab-btn {{ $activeTab === 'acuerdos' ? 'active' : '' }}">
            <span>Acuerdos</span>
            <span class="tab-count">{{ $agreements->count() }}</span>
        </button>

        <button type="button" wire:click="setTab('citas')" class="tab-btn {{ $activeTab === 'citas' ? 'active' : '' }}">
            <span>Citas</span>
            <span class="tab-count">{{ $appointments->count() }}</span>
        </button>

        <button type="button" wire:click="setTab('documentos')" class="tab-btn {{ $activeTab === 'documentos' ? 'active' : '' }}">
            <span>Documentos</span>
            <span class="tab-count">{{ $documents->count() }}</span>
        </button>

        <button type="button" wire:click="setTab('historial')" class="tab-btn {{ $activeTab === 'historial' ? 'active' : '' }}">
            <span>Historial de Ciclos</span>
            <span class="tab-count">{{ $enrollments->count() }}</span>
        </button>
    </div>

    {{-- CONTENIDO DE PESTAÑAS --}}
    <div class="tab-content">

        {{-- ── PESTAÑA: RESUMEN CLÍNICO ────────────────────────────────────── --}}
        @if($activeTab === 'resumen')
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-2 space-y-6">
                    <div class="card">
                        <div class="card-header bg-green-50/50">
                            <h2 class="card-title text-green-900">Diagnóstico Principal y Caso Particular</h2>
                            <span class="badge badge-green font-semibold">Cifrado confidencial</span>
                        </div>
                        <div class="card-body">
                            <div class="text-lg font-bold text-gray-900 mb-2">
                                {{ $file?->primary_diagnosis ?: 'Sin diagnóstico formal registrado.' }}
                            </div>
                            <p class="text-sm text-muted">
                                <strong>Tipo de caso:</strong>
                                {{ $file?->file_type === 'con_diagnostico' ? 'Caso con diagnóstico previo externo' : 'Caso detectado en seguimiento escolar' }}
                            </p>

                            <div class="mt-4 pt-4 border-t">
                                <h3 class="text-xs font-bold text-muted uppercase tracking-wider mb-2">Áreas de Necesidad Identificadas</h3>
                                <div class="flex flex-wrap gap-2">
                                    @forelse($file?->getNeedAreaLabels() ?? [] as $label)
                                        <span class="chip font-medium text-xs py-1 px-3 bg-green-50 border-green-200 text-green-900">
                                            {{ $label }}
                                        </span>
                                    @empty
                                        <span class="text-sm text-muted italic">No se han registrado áreas específicas.</span>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title text-sm">Fortalezas del Alumno</h3>
                            </div>
                            <div class="card-body text-sm text-gray-700">
                                {{ $file?->strengths ?: 'Aún no se han capturado fortalezas del estudiante.' }}
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title text-sm">Áreas de Apoyo en Aula</h3>
                            </div>
                            <div class="card-body text-sm text-gray-700">
                                {{ $file?->support_areas ?: 'Sin especificaciones de apoyo registradas.' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="card">
                        <div class="card-header bg-amber-50/50">
                            <h3 class="card-title text-sm text-amber-900">Seguimiento Médico y Especialista</h3>
                        </div>
                        <div class="card-body space-y-4 text-sm">
                            <div>
                                <span class="text-xs font-bold text-muted block uppercase">Especialista Externo</span>
                                <div class="font-medium text-gray-800 mt-1">
                                    {{ $file?->external_specialist ?: 'Ninguno asignado' }}
                                </div>
                            </div>

                            <div class="pt-2 border-t">
                                <span class="text-xs font-bold text-muted block uppercase">Tratamiento o Medicación</span>
                                <div class="font-medium text-gray-800 mt-1">
                                    {{ $file?->treatment_medication ?: 'Sin tratamiento farmacológico reportado' }}
                                </div>
                            </div>

                            <div class="pt-2 border-t">
                                <span class="text-xs font-bold text-muted block uppercase">Ruta de Ingreso</span>
                                <div class="mt-1">
                                    <span class="badge badge-yellow">{{ $file?->intake_route?->label() ?? 'Sin ruta' }}</span>
                                    <p class="text-xs text-muted mt-1">{{ $file?->intake_route?->description() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="privacy-notice">
                        <strong>Nota de Privacidad y Confidencialidad:</strong>
                        Este expediente contiene información clínica sensible protegida. Todo acceso, modificación o exportación es registrado en la bitácora institucional con fecha, hora y usuario.
                    </div>
                </div>
            </div>
        @endif

        {{-- ── PESTAÑA: OBSERVACIONES ──────────────────────────────────────── --}}
        @if($activeTab === 'observaciones')
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Registros del Formato "Observación de Alumno"</h3>
                    <button type="button"
                            wire:click="$dispatch('open-observation-modal', { studentId: {{ $student->id }} })"
                            class="btn btn-primary btn-sm">
                        + Nueva Observación
                    </button>
                </div>
                <div class="card-body">
                    @forelse($observations as $obs)
                        <div class="observation-row">
                            <div class="observation-row-header">
                                <div>
                                    <span class="font-bold text-green-900 text-sm">
                                        Observación en Aula ({{ $obs->grade_group ?? $student->current_group_label }})
                                    </span>
                                    <span class="text-xs text-muted block">
                                        Mexicali, B.C., a {{ $obs->observation_date->format('d/m/Y') }}
                                        · Elaborado por: {{ $obs->elaborated_by_name ?? $obs->author->name }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if($obs->required_prefectura)   <span class="badge badge-red">Prefectura</span>    @endif
                                    @if($obs->required_parent_interview) <span class="badge badge-yellow">Entrevista papás</span> @endif
                                    @if($obs->required_channeling)   <span class="badge badge-blue">Canalización</span> @endif

                                    <x-dropdown-actions>
                                        <x-slot name="items">
                                            <button type="button"
                                                    wire:click="$dispatch('open-observation-edit', { observationId: {{ $obs->id }} })"
                                                    class="dropdown-item">
                                                ✏️ Editar
                                            </button>
                                            <button type="button"
                                                    wire:click="deleteObservation({{ $obs->id }})"
                                                    wire:confirm="¿Seguro que deseas eliminar esta observación? Esta acción no se puede deshacer."
                                                    class="dropdown-item dropdown-item-danger">
                                                🗑 Eliminar
                                            </button>
                                        </x-slot>
                                    </x-dropdown-actions>
                                </div>
                            </div>
                            <div class="observation-row-text whitespace-pre-line bg-slate-50 p-3 rounded-lg border mb-2">
                                {{ $obs->observations }}
                            </div>
                            @if($obs->agreements_text)
                                <div class="text-xs text-green-900 bg-green-50 p-2 rounded border border-green-200">
                                    <strong>Acuerdos:</strong> {{ $obs->agreements_text }}
                                </div>
                            @endif
                            @if($obs->next_appointment_date)
                                <div class="text-xs text-amber-800 bg-amber-50 p-2 rounded border border-amber-200 mt-1">
                                    <strong>Próxima cita:</strong> {{ $obs->next_appointment_date->format('d/m/Y') }}
                                </div>
                            @endif

                            {{-- Firmas --}}
                            @if($obs->signatures->where('is_present', true)->count() > 0)
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach($obs->signatures->where('is_present', true) as $sig)
                                        <span class="chip text-xs bg-white border-green-200 text-green-900">
                                            ✅ {{ $sig->signer_name ?: $sig->signature_role->label() }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="empty-state py-8">
                            <div class="empty-state-title">Sin observaciones registradas</div>
                            <div class="empty-state-message">Las observaciones del docente o psicopedagógico aparecerán aquí.</div>
                            <button type="button"
                                    wire:click="$dispatch('open-observation-modal', { studentId: {{ $student->id }} })"
                                    class="btn btn-primary mt-3">
                                + Registrar primera observación
                            </button>
                        </div>
                    @endforelse
                </div>
            </div>
        @endif

        {{-- ── PESTAÑA: ENTREVISTAS ────────────────────────────────────────── --}}
        @if($activeTab === 'entrevistas')
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Entrevistas con Padres y/o Especialista</h3>
                    <button type="button"
                            wire:click="$dispatch('open-interview-modal', { studentId: {{ $student->id }} })"
                            class="btn btn-primary btn-sm">
                        + Nueva Entrevista
                    </button>
                </div>
                <div class="card-body">
                    @forelse($interviews as $ent)
                        <div class="observation-row">
                            <div class="observation-row-header">
                                <div>
                                    <span class="font-bold text-blue-900 text-sm">
                                        Entrevista ({{ $ent->modality?->label() ?? 'Presencial' }})
                                    </span>
                                    <span class="text-xs text-muted block">
                                        Fecha: {{ $ent->interview_date->format('d/m/Y') }}
                                        · Por: {{ $ent->conducted_by_name ?? $ent->author->name }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="badge badge-blue">{{ $ent->modality?->label() ?? 'Presencial' }}</span>
                                    <x-dropdown-actions>
                                        <x-slot name="items">
                                            <button type="button"
                                                    wire:click="$dispatch('open-interview-edit', { interviewId: {{ $ent->id }} })"
                                                    class="dropdown-item">
                                                ✏️ Editar
                                            </button>
                                            <button type="button"
                                                    wire:click="deleteInterview({{ $ent->id }})"
                                                    wire:confirm="¿Seguro que deseas eliminar esta entrevista?"
                                                    class="dropdown-item dropdown-item-danger">
                                                🗑 Eliminar
                                            </button>
                                        </x-slot>
                                    </x-dropdown-actions>
                                </div>
                            </div>

                            <div class="text-sm font-semibold text-gray-800 mb-1">Motivo y Objetivo:</div>
                            <div class="text-sm bg-slate-50 p-3 rounded-lg border mb-3 text-gray-700">
                                {{ $ent->purpose_and_objective }}
                            </div>

                            @if($ent->comments)
                                <div class="text-sm font-semibold text-gray-800 mb-1">Comentarios y Acuerdos:</div>
                                <div class="text-sm bg-slate-50 p-3 rounded-lg border text-gray-700">
                                    {{ $ent->comments }}
                                </div>
                            @endif

                            @if($ent->attendees)
                                <div class="text-xs text-muted mt-2">
                                    <strong>Asistentes:</strong> {{ $ent->attendees }}
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="empty-state py-8">
                            <div class="empty-state-title">Sin entrevistas registradas</div>
                            <div class="empty-state-message">No se cuenta con entrevistas documentadas para este alumno.</div>
                            <button type="button"
                                    wire:click="$dispatch('open-interview-modal', { studentId: {{ $student->id }} })"
                                    class="btn btn-primary mt-3">
                                + Registrar primera entrevista
                            </button>
                        </div>
                    @endforelse
                </div>
            </div>
        @endif

        {{-- ── PESTAÑA: ACUERDOS ───────────────────────────────────────────── --}}
        @if($activeTab === 'acuerdos')
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Compromisos y Acuerdos del Alumno</h3>
                    <button type="button"
                            wire:click="$dispatch('open-agreement-modal', { studentId: {{ $student->id }} })"
                            class="btn btn-primary btn-sm">
                        + Nuevo Acuerdo
                    </button>
                </div>
                <div class="card-body">
                    <div class="space-y-3">
                        @forelse($agreements as $agr)
                            <div class="p-3 bg-white border rounded-xl flex items-start justify-between gap-4">
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-semibold text-gray-900">{{ $agr->description }}</div>
                                    <div class="text-xs text-muted mt-1">
                                        Responsable: <strong>{{ $agr->responsible_name ?? 'Docente titular' }}</strong>
                                        · Fecha compromiso: {{ $agr->due_date?->format('d/m/Y') ?? 'Sin fecha' }}
                                    </div>
                                    {{-- Cambio rápido de estatus --}}
                                    <div class="flex flex-wrap gap-1 mt-2">
                                        @foreach(\App\Enums\AgreementStatus::cases() as $st)
                                            <button type="button"
                                                    wire:click="changeAgreementStatus({{ $agr->id }}, '{{ $st->value }}')"
                                                    class="text-[10px] px-2 py-0.5 rounded-full border font-medium transition-all
                                                        {{ $agr->status === $st
                                                            ? 'bg-green-600 border-green-600 text-white'
                                                            : 'border-gray-300 text-gray-500 hover:border-green-400 hover:text-green-700' }}">
                                                {{ $st->label() }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <span class="badge
                                        {{ $agr->status->color() === 'green' ? 'badge-green' :
                                           ($agr->status->color() === 'yellow' ? 'badge-yellow' :
                                           ($agr->status->color() === 'blue' ? 'badge-blue' : 'badge-gray')) }}">
                                        {{ $agr->status->label() }}
                                    </span>
                                    <x-dropdown-actions>
                                        <x-slot name="items">
                                            <button type="button"
                                                    wire:click="$dispatch('open-agreement-edit', { agreementId: {{ $agr->id }} })"
                                                    class="dropdown-item">
                                                ✏️ Editar
                                            </button>
                                            <button type="button"
                                                    wire:click="deleteAgreement({{ $agr->id }})"
                                                    wire:confirm="¿Seguro que deseas eliminar este acuerdo?"
                                                    class="dropdown-item dropdown-item-danger">
                                                🗑 Eliminar
                                            </button>
                                        </x-slot>
                                    </x-dropdown-actions>
                                </div>
                            </div>
                        @empty
                            <div class="empty-state py-8">
                                <div class="empty-state-title">No hay acuerdos pendientes</div>
                                <div class="empty-state-message">Los acuerdos derivados de observaciones o entrevistas se listan aquí.</div>
                                <button type="button"
                                        wire:click="$dispatch('open-agreement-modal', { studentId: {{ $student->id }} })"
                                        class="btn btn-primary mt-3">
                                    + Registrar primer acuerdo
                                </button>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif

        {{-- ── PESTAÑA: CITAS Y CALENDARIO ────────────────────────────────── --}}
        @if($activeTab === 'citas')
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Citas Programadas</h3>
                    <button type="button"
                            wire:click="$dispatch('open-appointment-modal', { studentId: {{ $student->id }} })"
                            class="btn btn-primary btn-sm">
                        + Agendar Cita
                    </button>
                </div>
                <div class="card-body">
                    <div class="space-y-3">
                        @forelse($appointments as $app)
                            <div class="p-4 border rounded-xl flex items-start justify-between gap-4 bg-slate-50">
                                <div class="flex-1">
                                    <div class="font-bold text-green-900 text-sm">{{ $app->title }}</div>
                                    <div class="text-xs text-muted mt-1">
                                        Inicio: <strong>{{ $app->starts_at->format('d/m/Y H:i') }}</strong> hrs
                                        @if($app->ends_at)
                                            · Fin: {{ $app->ends_at->format('d/m/Y H:i') }} hrs
                                        @endif
                                    </div>
                                    @if($app->location)
                                        <div class="text-xs text-muted mt-0.5">📍 {{ $app->location }}</div>
                                    @endif
                                    @if($app->required_attendees && count($app->required_attendees))
                                        <div class="flex flex-wrap gap-1 mt-2">
                                            @foreach($app->required_attendees as $att)
                                                <span class="chip text-xs bg-white border-gray-200 text-gray-700">{{ $att }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                    @if($app->notes)
                                        <div class="text-xs text-gray-600 mt-2 italic">{{ $app->notes }}</div>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <span class="badge {{ match($app->status) {
                                        'programada'   => 'badge-green',
                                        'completada'   => 'badge-blue',
                                        'cancelada'    => 'badge-gray',
                                        'reprogramada' => 'badge-yellow',
                                        default        => 'badge-green',
                                    } }}">
                                        {{ ucfirst($app->status ?? 'Programada') }}
                                    </span>
                                    <x-dropdown-actions>
                                        <x-slot name="items">
                                            <button type="button"
                                                    wire:click="$dispatch('open-appointment-edit', { appointmentId: {{ $app->id }} })"
                                                    class="dropdown-item">
                                                ✏️ Editar
                                            </button>
                                            <button type="button"
                                                    wire:click="deleteAppointment({{ $app->id }})"
                                                    wire:confirm="¿Seguro que deseas eliminar esta cita?"
                                                    class="dropdown-item dropdown-item-danger">
                                                🗑 Eliminar
                                            </button>
                                        </x-slot>
                                    </x-dropdown-actions>
                                </div>
                            </div>
                        @empty
                            <div class="empty-state py-8">
                                <div class="empty-state-title">Sin citas próximas</div>
                                <div class="empty-state-message">No hay citas en agenda para este alumno.</div>
                                <button type="button"
                                        wire:click="$dispatch('open-appointment-modal', { studentId: {{ $student->id }} })"
                                        class="btn btn-primary mt-3">
                                    + Agendar primera cita
                                </button>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif

        {{-- ── PESTAÑA: DOCUMENTOS ─────────────────────────────────────────── --}}
        @if($activeTab === 'documentos')
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Expedientes y Dictámenes Externos Adjuntos</h3>
                    <button type="button"
                            wire:click="$dispatch('open-document-modal', { studentId: {{ $student->id }} })"
                            class="btn btn-primary btn-sm">
                        + Adjuntar Documento
                    </button>
                </div>
                <div class="card-body">
                    @forelse($documents as $doc)
                        <div class="p-3 border rounded-xl flex items-center justify-between mb-2 bg-white">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center text-lg flex-shrink-0">
                                    {{ match(true) {
                                        str_contains($doc->mime_type ?? '', 'pdf')  => '📄',
                                        str_contains($doc->mime_type ?? '', 'image')=> '🖼',
                                        str_contains($doc->mime_type ?? '', 'word') => '📝',
                                        default => '📎',
                                    } }}
                                </div>
                                <div class="min-w-0">
                                    <div class="font-semibold text-sm text-gray-900 truncate">{{ $doc->title }}</div>
                                    <div class="text-xs text-muted">
                                        {{ $doc->file_size_formatted }}
                                        · Subido el {{ $doc->created_at->format('d/m/Y') }}
                                        · Por: {{ $doc->uploader->name }}
                                    </div>
                                    @if($doc->description)
                                        <div class="text-xs text-gray-500 mt-0.5 truncate">{{ $doc->description }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <span class="badge badge-gray">{{ ucfirst(str_replace('_', ' ', $doc->category ?? 'Documento')) }}</span>
                                <x-dropdown-actions>
                                    <x-slot name="items">
                                        <button type="button"
                                                wire:click="$dispatch('open-document-edit', { documentId: {{ $doc->id }} })"
                                                class="dropdown-item">
                                            ✏️ Editar información
                                        </button>
                                        <button type="button"
                                                wire:click="deleteDocument({{ $doc->id }})"
                                                wire:confirm="¿Seguro que deseas eliminar este documento? El archivo será eliminado permanentemente."
                                                class="dropdown-item dropdown-item-danger">
                                            🗑 Eliminar
                                        </button>
                                    </x-slot>
                                </x-dropdown-actions>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state py-8">
                            <div class="empty-state-title">No hay archivos adjuntos</div>
                            <div class="empty-state-message">Los archivos se almacenan en almacenamiento privado confidencial.</div>
                            <button type="button"
                                    wire:click="$dispatch('open-document-modal', { studentId: {{ $student->id }} })"
                                    class="btn btn-primary mt-3">
                                + Adjuntar primer documento
                            </button>
                        </div>
                    @endforelse
                </div>
            </div>
        @endif

        {{-- ── PESTAÑA: HISTORIAL ENTRE CICLOS ESCOLARES ──────────────────── --}}
        @if($activeTab === 'historial')
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Trazabilidad Histórica Entre Ciclos Escolares</h3>
                </div>
                <div class="card-body">
                    <div class="space-y-4">
                        @foreach($enrollments as $enr)
                            <div class="p-4 border rounded-xl flex items-center justify-between {{ $enr->is_current ? 'bg-green-50/60 border-green-200' : 'bg-white' }}">
                                <div>
                                    <div class="font-bold text-sm text-gray-900">
                                        Ciclo Escolar {{ $enr->schoolCycle->name }}
                                        @if($enr->is_current)
                                            <span class="badge badge-green ml-2">Ciclo Actual</span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-muted mt-1">
                                        Grupo: <strong>{{ $enr->group->label }}</strong>
                                        ({{ $enr->group->grade->label() }} Sección {{ $enr->group->section }})
                                    </div>
                                </div>
                                <div class="text-xs text-muted">
                                    Registrado: {{ $enr->created_at->format('d/m/Y') }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
