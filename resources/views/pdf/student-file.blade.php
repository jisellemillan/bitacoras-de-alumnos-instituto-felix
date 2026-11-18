<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Expediente - {{ $student->full_name }}</title>
    <style>
        @page {
            margin: 1.2cm 1.2cm 1.5cm 1.2cm;
        }

        body {
            font-family: 'DejaVu Sans', Helvetica, Arial, sans-serif;
            font-size: 8.5pt;
            color: #1A2E1C;
            line-height: 1.4;
            background-color: #FFFFFF;
        }

        /* ─── Encabezado Institucional ─── */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2.5px solid #1F5C2E;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }

        .header-logo {
            width: 65px;
            vertical-align: middle;
        }

        .header-logo img {
            max-width: 60px;
            max-height: 60px;
            border-radius: 6px;
        }

        .header-title-box {
            vertical-align: middle;
            padding-left: 10px;
        }

        .inst-name {
            font-size: 13pt;
            font-weight: bold;
            color: #1F5C2E;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin: 0;
            padding: 0;
        }

        .inst-sub {
            font-size: 8.5pt;
            color: #4A5E4C;
            font-weight: 600;
            margin-top: 1px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .doc-title {
            font-size: 11pt;
            color: #0F3818;
            font-weight: bold;
            margin-top: 3px;
            letter-spacing: 0.03em;
        }

        .header-meta {
            text-align: right;
            vertical-align: middle;
            font-size: 7.5pt;
            color: #556B58;
            line-height: 1.35;
        }

        .header-badge {
            display: inline-block;
            background-color: #E8F5E9;
            color: #1B5E20;
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 8pt;
            border: 1px solid #A5D6A7;
            margin-top: 3px;
        }

        /* ─── Secciones ─── */
        .section-title {
            background-color: #1F5C2E;
            color: #FFFFFF;
            font-size: 8.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 4px 8px;
            margin-top: 10px;
            margin-bottom: 6px;
            border-radius: 3px;
        }

        /* ─── Tablas de Datos de Clave-Valor ─── */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }

        .data-table td {
            padding: 3.5px 6px;
            font-size: 8pt;
            vertical-align: top;
            border: 1px solid #DDE5DB;
        }

        .label-cell {
            background-color: #F4F8F4;
            color: #1F5C2E;
            font-weight: bold;
            width: 22%;
        }

        .value-cell {
            color: #1A2E1C;
            width: 28%;
        }

        .value-cell-wide {
            color: #1A2E1C;
            width: 78%;
        }

        /* ─── Cajas de Texto y Párrafos ─── */
        .box-container {
            border: 1px solid #DDE5DB;
            background-color: #FCFDFC;
            border-radius: 4px;
            padding: 6px 8px;
            margin-bottom: 6px;
        }

        .box-title {
            font-size: 8pt;
            font-weight: bold;
            color: #1F5C2E;
            margin-bottom: 2px;
        }

        .box-content {
            font-size: 8pt;
            color: #2D3E2F;
            line-height: 1.45;
        }

        /* ─── Tablas Listadas (Observaciones, Entrevistas, etc.) ─── */
        .list-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            font-size: 7.5pt;
        }

        .list-table th {
            background-color: #EBF2EC;
            color: #1F5C2E;
            font-weight: bold;
            text-align: left;
            padding: 4px 6px;
            border: 1px solid #D1DDD2;
            text-transform: uppercase;
            font-size: 7pt;
        }

        .list-table td {
            padding: 4px 6px;
            border: 1px solid #E2EAE3;
            vertical-align: top;
            color: #1A2E1C;
        }

        .list-table tr:nth-child(even) td {
            background-color: #F9FBF9;
        }

        .empty-text {
            font-style: italic;
            color: #7A8E7C;
            padding: 6px;
            font-size: 7.5pt;
        }

        /* ─── Badges y Etiquetas ─── */
        .badge {
            display: inline-block;
            padding: 1.5px 5px;
            border-radius: 3px;
            font-size: 7pt;
            font-weight: bold;
        }

        .badge-green { background-color: #E8F5E9; color: #1B5E20; border: 0.5px solid #A5D6A7; }
        .badge-yellow { background-color: #FFF9C4; color: #F57F17; border: 0.5px solid #FFF176; }
        .badge-gray { background-color: #ECEFF1; color: #37474F; border: 0.5px solid #CFD8DC; }
        .badge-route { background-color: #E0F2FE; color: #0369A1; border: 0.5px solid #BAE6FD; font-weight: bold; }

        .chip {
            display: inline-block;
            background-color: #F1F5F2;
            color: #1F5C2E;
            border: 0.5px solid #CCD8CD;
            border-radius: 3px;
            padding: 1px 4px;
            font-size: 7pt;
            margin-right: 3px;
            margin-bottom: 2px;
        }

        /* ─── Firmas ─── */
        .signatures-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            page-break-inside: avoid;
        }

        .signatures-table td {
            width: 50%;
            text-align: center;
            vertical-align: bottom;
            padding: 0 20px;
        }

        .signature-line {
            border-top: 1.2px solid #1F5C2E;
            padding-top: 4px;
            font-size: 8pt;
            font-weight: bold;
            color: #1F5C2E;
        }

        .signature-sub {
            font-size: 7pt;
            color: #556B58;
            margin-top: 1px;
        }

        /* ─── Pie de Página Fijo ─── */
        .pdf-footer {
            position: fixed;
            bottom: -20px;
            left: 0;
            right: 0;
            border-top: 0.8px solid #DDE5DB;
            padding-top: 4px;
            font-size: 6.5pt;
            color: #718774;
            display: table;
            width: 100%;
        }

        .footer-left {
            display: table-cell;
            width: 60%;
            text-align: left;
        }

        .footer-right {
            display: table-cell;
            width: 40%;
            text-align: right;
        }

        .page-number:before {
            content: "Página " counter(page);
        }
    </style>
</head>
<body>

    {{-- PIE DE PÁGINA FIJO --}}
    <div class="pdf-footer">
        <div class="footer-left">
            <strong>Instituto Félix</strong> · Sistema de Bitácora y Seguimiento Psicopedagógico | <em>Documento Confidencial</em>
        </div>
        <div class="footer-right">
            Expediente: {{ $student->full_name }} | <span class="page-number"></span>
        </div>
    </div>

    {{-- ENCABEZADO INSTITUCIONAL --}}
    <table class="header-table">
        <tr>
            <td class="header-logo">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" alt="Logo Instituto Félix">
                @endif
            </td>
            <td class="header-title-box">
                <div class="inst-name">Instituto Félix</div>
                <div class="inst-sub">Departamento Psicopedagógico</div>
                <div class="doc-title">Expediente del Alumno</div>
            </td>
            <td class="header-meta">
                <div><strong>Fecha de emisión:</strong> {{ $generatedAt->format('d/m/Y H:i') }} hrs</div>
                <div><strong>Generado por:</strong> {{ $generatedBy->name }}</div>
                <div><strong>Ciclo Escolar:</strong> {{ $cycle?->name ?? 'Ciclo Actual' }}</div>
                @if($file && $file->status)
                    <div class="header-badge">{{ $file->status->label() }}</div>
                @endif
            </td>
        </tr>
    </table>

    {{-- 1. IDENTIFICACIÓN DEL ALUMNO --}}
    <div class="section-title">1. Datos Generales de Identificación</div>
    <table class="data-table">
        <tr>
            <td class="label-cell">Nombre Completo:</td>
            <td class="value-cell" style="font-weight: bold; font-size: 8.5pt;">{{ $student->full_name }}</td>
            <td class="label-cell">CURP:</td>
            <td class="value-cell" style="font-family: monospace;">{{ $student->curp ?: 'No registrado' }}</td>
        </tr>
        <tr>
            <td class="label-cell">Fecha de Nacimiento:</td>
            <td class="value-cell">
                {{ $student->birthdate ? $student->birthdate->format('d/m/Y') : 'No registrada' }}
            </td>
            <td class="label-cell">Edad Actual:</td>
            <td class="value-cell">
                {{ $student->age ? $student->age . ' años' : 'No registrada' }}
            </td>
        </tr>
        <tr>
            <td class="label-cell">Sexo / Género:</td>
            <td class="value-cell">{{ $student->sex ? ucfirst($student->sex) : 'No especificado' }}</td>
            <td class="label-cell">Grupo Escolar:</td>
            <td class="value-cell" style="font-weight: bold; color: #1F5C2E;">
                {{ $group ? $group->label : 'Sin grupo asignado' }}
            </td>
        </tr>
        <tr>
            <td class="label-cell">Grado y Sección:</td>
            <td class="value-cell">
                @if($group)
                    {{ $group->grade->label() }} · Sección {{ $group->section }} {{ $group->name ? "({$group->name})" : '' }}
                @else
                    Sin grupo asignado
                @endif
            </td>
            <td class="label-cell">Estatus de Alumno:</td>
            <td class="value-cell">
                <span class="badge {{ $student->is_active ? 'badge-green' : 'badge-gray' }}">
                    {{ $student->is_active ? 'Alumno Activo' : 'Baja Escolar' }}
                </span>
            </td>
        </tr>
    </table>

    {{-- 2. EXPEDIENTE PSICOPEDAGÓGICO DIGITAL --}}
    <div class="section-title">2. Diagnóstico y Expediente Psicopedagógico</div>
    <table class="data-table">
        <tr>
            <td class="label-cell">Estatus Expediente:</td>
            <td class="value-cell">
                @if($file && $file->status)
                    <span class="badge {{ $file->status->value === 'activo' ? 'badge-green' : ($file->status->value === 'en_seguimiento' ? 'badge-yellow' : 'badge-gray') }}">
                        {{ $file->status->label() }}
                    </span>
                @else
                    <span class="empty-text">Sin estatus</span>
                @endif
            </td>
            <td class="label-cell">Ruta de Ingreso:</td>
            <td class="value-cell">
                @if($file && $file->intake_route)
                    <span class="badge badge-route">Ruta {{ $file->intake_route->value }}</span>
                    <span style="font-size: 7pt; color: #444;">({{ $file->intake_route->label() }})</span>
                @else
                    <span class="empty-text">Sin ruta asignada</span>
                @endif
            </td>
        </tr>
        <tr>
            <td class="label-cell">Fecha de Apertura:</td>
            <td class="value-cell">{{ $file?->opened_at ? $file->opened_at->format('d/m/Y') : 'Fecha no especificada' }}</td>
            <td class="label-cell">Tipo de Expediente:</td>
            <td class="value-cell">{{ $file?->file_type === 'con_diagnostico' ? 'Con Diagnóstico Formal' : 'Detección Inicial / En Acompañamiento' }}</td>
        </tr>
        <tr>
            <td class="label-cell">Diagnóstico Principal:</td>
            <td class="value-cell-wide" colspan="3" style="font-weight: bold; color: #0F3818;">
                {{ $file?->primary_diagnosis ?: 'Sin diagnóstico formal registrado.' }}
            </td>
        </tr>
        <tr>
            <td class="label-cell">Áreas de Necesidad:</td>
            <td class="value-cell-wide" colspan="3">
                @if($file && !empty($file->need_areas))
                    @foreach($file->getNeedAreaLabels() as $area)
                        <span class="chip">{{ $area }}</span>
                    @endforeach
                @else
                    <span class="empty-text">No se registraron áreas de necesidad específicas.</span>
                @endif
            </td>
        </tr>
    </table>

    {{-- Fortalezas y Apoyos --}}
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 6px;">
        <tr>
            <td style="width: 50%; padding-right: 4px; vertical-align: top;">
                <div class="box-container">
                    <div class="box-title">Fortalezas y Habilidades del Alumno:</div>
                    <div class="box-content">{{ $file?->strengths ?: 'Sin información capturada.' }}</div>
                </div>
            </td>
            <td style="width: 50%; padding-left: 4px; vertical-align: top;">
                <div class="box-container">
                    <div class="box-title">Áreas de Apoyo y Ajustes Razonables:</div>
                    <div class="box-content">{{ $file?->support_areas ?: 'Sin ajustes requeridos registrados.' }}</div>
                </div>
            </td>
        </tr>
        <tr>
            <td style="width: 50%; padding-right: 4px; vertical-align: top;">
                <div class="box-container">
                    <div class="box-title">Especialista Externo de Referencia:</div>
                    <div class="box-content">{{ $file?->external_specialist ?: 'Ninguno o sin registrar.' }}</div>
                </div>
            </td>
            <td style="width: 50%; padding-left: 4px; vertical-align: top;">
                <div class="box-container">
                    <div class="box-title">Tratamiento o Medicación:</div>
                    <div class="box-content">{{ $file?->treatment_medication ?: 'Sin tratamiento o medicación indicada.' }}</div>
                </div>
            </td>
        </tr>
    </table>

    {{-- 3. BITÁCORA DE OBSERVACIONES --}}
    <div class="section-title">3. Registro de Observaciones Psicopedagógicas ({{ $student->observations->count() }})</div>
    @if($student->observations->isNotEmpty())
        <table class="list-table">
            <thead>
                <tr>
                    <th style="width: 14%;">Fecha</th>
                    <th style="width: 22%;">Registrado Por</th>
                    <th style="width: 40%;">Observaciones</th>
                    <th style="width: 24%;">Acuerdos / Compromisos</th>
                </tr>
            </thead>
            <tbody>
                @foreach($student->observations->take(10) as $obs)
                    <tr>
                        <td style="font-weight: bold;">{{ $obs->observation_date->format('d/m/Y') }}</td>
                        <td>{{ $obs->author?->name ?? ($obs->elaborated_by_name ?: 'Personal Institucional') }}</td>
                        <td>{{ Str::limit($obs->observations, 160) }}</td>
                        <td>{{ $obs->agreements_text ? Str::limit($obs->agreements_text, 100) : '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="box-container">
            <span class="empty-text">No se han registrado observaciones en la bitácora de este alumno.</span>
        </div>
    @endif

    {{-- 4. ENTREVISTAS --}}
    <div class="section-title">4. Registro de Entrevistas Realizadas ({{ $student->interviews->count() }})</div>
    @if($student->interviews->isNotEmpty())
        <table class="list-table">
            <thead>
                <tr>
                    <th style="width: 13%;">Fecha</th>
                    <th style="width: 25%;">Asistentes / Tutores</th>
                    <th style="width: 20%;">Modalidad / Conductor</th>
                    <th style="width: 24%;">Propósito / Objetivo</th>
                    <th style="width: 18%;">Comentarios</th>
                </tr>
            </thead>
            <tbody>
                @foreach($student->interviews->take(8) as $intv)
                    <tr>
                        <td style="font-weight: bold;">{{ $intv->interview_date ? $intv->interview_date->format('d/m/Y') : '—' }}</td>
                        <td>{{ $intv->attendees ?: 'Tutores / Familiares' }}</td>
                        <td>{{ $intv->modality?->label() ?? 'Presencial' }} <br><span style="font-size: 6.5pt; color: #555;">{{ $intv->conducted_by_name ?: ($intv->author?->name ?? 'Institución') }}</span></td>
                        <td>{{ Str::limit($intv->purpose_and_objective, 120) }}</td>
                        <td>{{ $intv->comments ? Str::limit($intv->comments, 90) : '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="box-container">
            <span class="empty-text">No se han registrado entrevistas con tutores o especialistas para este expediente.</span>
        </div>
    @endif

    {{-- 5. ACUERDOS Y COMPROMISOS --}}
    <div class="section-title">5. Acuerdos y Compromisos ({{ $student->agreements->count() }})</div>
    @if($student->agreements->isNotEmpty())
        <table class="list-table">
            <thead>
                <tr>
                    <th style="width: 14%;">Fecha Registro</th>
                    <th style="width: 48%;">Descripción del Acuerdo</th>
                    <th style="width: 16%;">Fecha Compromiso</th>
                    <th style="width: 10%;">Estatus</th>
                    <th style="width: 12%;">Registrado Por</th>
                </tr>
            </thead>
            <tbody>
                @foreach($student->agreements->take(8) as $agr)
                    <tr>
                        <td>{{ $agr->created_at ? $agr->created_at->format('d/m/Y') : '—' }}</td>
                        <td>{{ $agr->description }}</td>
                        <td>{{ $agr->due_date ? $agr->due_date->format('d/m/Y') : 'Sin fecha límite' }}</td>
                        <td>
                            @php
                                $statusVal = is_object($agr->status) ? $agr->status->value : $agr->status;
                                $statusLabel = is_object($agr->status) ? $agr->status->label() : ucfirst(str_replace('_', ' ', $agr->status ?? 'Pendiente'));
                                $statusBadgeClass = match($statusVal) {
                                    'cumplido' => 'badge-green',
                                    'en_proceso' => 'badge-route',
                                    'pendiente' => 'badge-yellow',
                                    default => 'badge-gray',
                                };
                            @endphp
                            <span class="badge {{ $statusBadgeClass }}">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td>{{ $agr->responsible_name ?: ($agr->responsible?->name ?? ($agr->author?->name ?? 'Institución')) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="box-container">
            <span class="empty-text">No hay acuerdos formales registrados para este expediente.</span>
        </div>
    @endif

    {{-- 6. CITAS Y SEGUIMIENTO --}}
    @if($student->appointments->isNotEmpty())
        <div class="section-title">6. Citas y Sesiones de Seguimiento ({{ $student->appointments->count() }})</div>
        <table class="list-table">
            <thead>
                <tr>
                    <th style="width: 18%;">Fecha y Hora</th>
                    <th style="width: 32%;">Motivo / Título</th>
                    <th style="width: 25%;">Lugar</th>
                    <th style="width: 25%;">Estatus</th>
                </tr>
            </thead>
            <tbody>
                @foreach($student->appointments->take(6) as $appt)
                    <tr>
                        <td style="font-weight: bold;">{{ $appt->starts_at ? $appt->starts_at->format('d/m/Y H:i') : 'Por definir' }}</td>
                        <td>{{ $appt->title ?: ($appt->notes ?: 'Cita de seguimiento') }}</td>
                        <td>{{ $appt->location ?: 'Instalaciones Escolares' }}</td>
                        <td>{{ ucfirst($appt->status ?? 'Programada') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- FIRMAS INSTITUCIONALES --}}
    <table class="signatures-table">
        <tr>
            <td>
                <div class="signature-line">Departamento Psicopedagógico</div>
                <div class="signature-sub">Instituto Félix</div>
            </td>
            <td>
                <div class="signature-line">Dirección / Coordinación Escolar</div>
                <div class="signature-sub">Instituto Félix</div>
            </td>
        </tr>
    </table>

</body>
</html>
