<?php

namespace App\Actions\Students;

use App\Models\AuditLog;
use App\Models\Enrollment;
use App\Models\SchoolCycle;
use App\Models\Student;
use App\Models\StudentFile;
use Illuminate\Support\Facades\DB;

class UpdateStudentAction
{
    public function execute(Student $student, array $data, int $userId): Student
    {
        return DB::transaction(function () use ($student, $data, $userId) {
            $oldStudentData = $student->toArray();

            $student->update([
                'first_name' => trim($data['first_name']),
                'last_name' => trim($data['last_name']),
                'birthdate' => $data['birthdate'] ?? null,
                'sex' => $data['sex'] ?? null,
                'curp' => !empty($data['curp']) ? strtoupper(trim($data['curp'])) : null,
                'photo_path' => $data['photo_path'] ?? $student->photo_path,
                'is_active' => $data['is_active'] ?? $student->is_active,
            ]);

            // Actualizar o crear inscripción de grupo si cambió
            $cycle = SchoolCycle::current();
            if (!empty($data['group_id'])) {
                $group = Group::find($data['group_id']);
                $cycleId = $cycle?->id ?? $group?->school_cycle_id;
                if ($cycleId) {
                    Enrollment::updateOrCreate(
                        [
                            'student_id' => $student->id,
                            'school_cycle_id' => $cycleId,
                        ],
                        [
                            'group_id' => $data['group_id'],
                            'is_current' => true,
                        ]
                    );
                }
            }

            // Actualizar expediente si vienen campos
            if ($student->studentFile && isset($data['student_file'])) {
                $fileData = $data['student_file'];
                $student->studentFile->update([
                    'intake_route' => $fileData['intake_route'] ?? $student->studentFile->intake_route,
                    'status' => $fileData['status'] ?? $student->studentFile->status,
                    'need_areas' => $fileData['need_areas'] ?? $student->studentFile->need_areas,
                    'primary_diagnosis' => $fileData['primary_diagnosis'] ?? $student->studentFile->primary_diagnosis,
                    'strengths' => $fileData['strengths'] ?? $student->studentFile->strengths,
                    'support_areas' => $fileData['support_areas'] ?? $student->studentFile->support_areas,
                    'external_specialist' => $fileData['external_specialist'] ?? $student->studentFile->external_specialist,
                    'treatment_medication' => $fileData['treatment_medication'] ?? $student->studentFile->treatment_medication,
                    'updated_by' => $userId,
                ]);
            }

            AuditLog::create([
                'user_id' => $userId,
                'action' => 'updated',
                'auditable_type' => Student::class,
                'auditable_id' => $student->id,
                'auditable_label' => "Actualización de alumno: {$student->full_name}",
                'old_values' => $oldStudentData,
                'new_values' => $student->toArray(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
            ]);

            return $student;
        });
    }
}
