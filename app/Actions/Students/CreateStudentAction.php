<?php

namespace App\Actions\Students;

use App\Enums\StudentStatus;
use App\Models\AuditLog;
use App\Models\Enrollment;
use App\Models\Group;
use App\Models\SchoolCycle;
use App\Models\Student;
use App\Models\StudentFile;
use Illuminate\Support\Facades\DB;

class CreateStudentAction
{
    public function execute(array $data, int $userId): Student
    {
        return DB::transaction(function () use ($data, $userId) {
            $student = Student::create([
                'first_name' => trim($data['first_name']),
                'last_name' => trim($data['last_name']),
                'birthdate' => $data['birthdate'] ?? null,
                'sex' => $data['sex'] ?? null,
                'curp' => !empty($data['curp']) ? strtoupper(trim($data['curp'])) : null,
                'photo_path' => $data['photo_path'] ?? null,
                'is_active' => true,
            ]);

            // Asignación de grupo y ciclo activo
            $cycle = SchoolCycle::current();
            if (!empty($data['group_id'])) {
                $group = Group::find($data['group_id']);
                $cycleId = $cycle?->id ?? $group?->school_cycle_id;
                if ($cycleId) {
                    Enrollment::create([
                        'student_id' => $student->id,
                        'group_id' => $data['group_id'],
                        'school_cycle_id' => $cycleId,
                        'is_current' => true,
                    ]);
                }
            }

            // Creación automática del expediente digital único
            $file = StudentFile::create([
                'student_id' => $student->id,
                'intake_route' => $data['intake_route'] ?? null,
                'file_type' => !empty($data['primary_diagnosis']) ? 'con_diagnostico' : 'sin_diagnostico',
                'status' => StudentStatus::Activo,
                'need_areas' => $data['need_areas'] ?? [],
                'primary_diagnosis' => $data['primary_diagnosis'] ?? null,
                'strengths' => $data['strengths'] ?? null,
                'support_areas' => $data['support_areas'] ?? null,
                'external_specialist' => $data['external_specialist'] ?? null,
                'treatment_medication' => $data['treatment_medication'] ?? null,
                'opened_at' => now(),
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            // Auditoría
            AuditLog::create([
                'user_id' => $userId,
                'action' => 'created',
                'auditable_type' => Student::class,
                'auditable_id' => $student->id,
                'auditable_label' => "Registro de nuevo alumno: {$student->full_name}",
                'new_values' => [
                    'student' => $student->toArray(),
                    'file' => $file->toArray(),
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
            ]);

            return $student;
        });
    }
}
