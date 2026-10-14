<?php

namespace App\Actions\Students;

use App\Models\AuditLog;
use App\Models\Student;

class DeleteStudentAction
{
    public function execute(Student $student, int $userId): void
    {
        $label = "Baja lógica de alumno: {$student->full_name}";

        $student->delete();

        AuditLog::create([
            'user_id' => $userId,
            'action' => 'deleted',
            'auditable_type' => Student::class,
            'auditable_id' => $student->id,
            'auditable_label' => $label,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }

    public function restore(int $studentId, int $userId): Student
    {
        $student = Student::withTrashed()->findOrFail($studentId);
        $student->restore();

        AuditLog::create([
            'user_id' => $userId,
            'action' => 'updated',
            'auditable_type' => Student::class,
            'auditable_id' => $student->id,
            'auditable_label' => "Restauración de alumno: {$student->full_name}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);

        return $student;
    }
}
