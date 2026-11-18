<?php

namespace App\Http\Controllers\Pdf;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReportPdfController extends Controller
{
    public function casesList()
    {
        return response('PDF Casos');
    }

    public function statistics()
    {
        return response('PDF Estadisticas');
    }

    public function studentFile(Request $request, Student $student)
    {
        // Cargar todas las relaciones necesarias para el expediente completo
        $student->load([
            'studentFile',
            'currentEnrollment.group.schoolCycle',
            'enrollments.group.schoolCycle',
            'observations.author',
            'observations.signatures',
            'interviews.author',
            'agreements.author',
            'appointments',
            'documents.uploader',
        ]);

        // Registrar auditoría de la descarga
        AuditLog::create([
            'user_id'         => auth()->id(),
            'action'          => 'downloaded',
            'auditable_type'  => Student::class,
            'auditable_id'    => $student->id,
            'auditable_label' => "Descarga de expediente digital PDF: {$student->full_name}",
            'ip_address'      => $request->ip(),
            'user_agent'      => $request->userAgent(),
            'created_at'      => now(),
        ]);

        // Codificar el logotipo oficial a base64 para que DomPDF lo renderice perfectamente
        $logoPath = public_path('img/logo.png');
        $logoBase64 = null;
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        // Codificar la fotografía del alumno si existe
        $photoBase64 = null;
        if ($student->photo_path) {
            $disk = Storage::disk('public');
            if ($disk->exists($student->photo_path)) {
                $mime = $disk->mimeType($student->photo_path) ?? 'image/jpeg';
                $photoBase64 = "data:{$mime};base64," . base64_encode($disk->get($student->photo_path));
            }
        }

        // Generar el PDF utilizando DomPDF
        $pdf = Pdf::loadView('pdf.student-file', [
            'student'     => $student,
            'file'        => $student->studentFile,
            'group'       => $student->currentEnrollment?->group,
            'cycle'       => $student->currentEnrollment?->schoolCycle,
            'logoBase64'  => $logoBase64,
            'photoBase64' => $photoBase64,
            'generatedAt' => now(),
            'generatedBy' => auth()->user(),
        ]);

        // Configuración de página e impresión
        $pdf->setPaper('letter', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => true,
            'defaultFont'          => 'DejaVu Sans',
        ]);

        // Nombre de archivo sanitizado y profesional
        $fileName = 'Expediente_' . Str::slug($student->first_name . '_' . $student->last_name, '_') . '.pdf';

        return $pdf->download($fileName);
    }
}
