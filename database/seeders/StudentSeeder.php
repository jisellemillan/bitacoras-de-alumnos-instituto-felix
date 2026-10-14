<?php

namespace Database\Seeders;

use App\Enums\AgreementStatus;
use App\Enums\IntakeRoute;
use App\Enums\InterviewModality;
use App\Enums\NeedArea;
use App\Enums\StudentStatus;
use App\Models\Agreement;
use App\Models\Appointment;
use App\Models\Enrollment;
use App\Models\Group;
use App\Models\Observation;
use App\Models\Interview;
use App\Models\SchoolCycle;
use App\Models\Student;
use App\Models\StudentFile;
use Illuminate\Database\Seeder;

/**
 * Seeds fictional students and sample case data.
 * NOTE: All names, diagnoses, and observations are completely fictional.
 * No real student data is ever used in seeds or tests.
 */
class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $cycle = SchoolCycle::where('is_active', true)->first();
        $psico = \App\Models\User::where('email', 'psico@felix.edu.mx')->first();

        // Groups for active cycle
        $groups = Group::where('school_cycle_id', $cycle->id)->get()->keyBy(
            fn ($g) => "{$g->grade->value}{$g->section}"
        );

        // ── Fictional students with cases ─────────────────────────────────────
        $studentsData = [
            // [first_name, last_name, grade+section, birthdate, sex, intake_route, need_areas, diagnosis, status]
            ['Alejandro', 'Fuentes Morales', '4A', '2016-03-15', 'masculino',
                IntakeRoute::A, [NeedArea::AtencionFuncionesEjecutivas->value],
                'TDAH presentación combinada', StudentStatus::EnSeguimiento],

            ['Valentina', 'Ríos Castillo', '4B', '2016-07-22', 'femenino',
                IntakeRoute::C, [NeedArea::NeurodesarrolloTEA->value, NeedArea::LenguajeComunicacion->value],
                'TEA nivel 1', StudentStatus::Activo],

            ['Sebastián', 'Ortega Vidal', '5A', '2015-11-08', 'masculino',
                IntakeRoute::B, [NeedArea::AtencionFuncionesEjecutivas->value, NeedArea::Aprendizaje->value],
                'TDAH predominantemente inatento', StudentStatus::EnSeguimiento],

            ['Camila', 'Mendoza Ruiz', '5B', '2015-05-30', 'femenino',
                IntakeRoute::C, [NeedArea::CondicionesMedicasNeurologicas->value],
                'Crisis convulsivas', StudentStatus::Activo],

            ['Diego', 'Hernández Peña', '6A', '2014-09-12', 'masculino',
                IntakeRoute::A, [NeedArea::ConductualEmocional->value, NeedArea::AtencionFuncionesEjecutivas->value],
                'Inmadurez en procesos atencionales', StudentStatus::EnSeguimiento],

            ['Sofía', 'Vargas León', '6B', '2014-01-25', 'femenino',
                IntakeRoute::B, [NeedArea::LenguajeComunicacion->value, NeedArea::Aprendizaje->value],
                'Dislexia', StudentStatus::Activo],

            ['Mateo', 'Castro Núñez', '4A', '2016-06-14', 'masculino',
                IntakeRoute::A, [NeedArea::Aprendizaje->value],
                'Discalculia', StudentStatus::Activo],

            ['Isabella', 'Torres Sánchez', '5A', '2015-08-19', 'femenino',
                IntakeRoute::C, [NeedArea::NeurodesarrolloTEA->value],
                'TEA nivel 2', StudentStatus::Activo],
        ];

        foreach ($studentsData as $data) {
            [$firstName, $lastName, $gradeSection, $birthdate, $sex,
                $route, $needAreas, $diagnosis, $status] = $data;

            $student = Student::create([
                'first_name' => $firstName,
                'last_name'  => $lastName,
                'birthdate'  => $birthdate,
                'sex'        => $sex,
                'is_active'  => true,
            ]);

            // Enroll in active cycle
            $group = $groups[$gradeSection] ?? null;
            if ($group) {
                Enrollment::create([
                    'student_id'      => $student->id,
                    'group_id'        => $group->id,
                    'school_cycle_id' => $cycle->id,
                    'is_current'      => true,
                ]);
            }

            // Create digital file
            $file = StudentFile::create([
                'student_id'         => $student->id,
                'intake_route'       => $route,
                'file_type'          => $route === IntakeRoute::C ? 'con_diagnostico' : 'sin_diagnostico',
                'status'             => $status,
                'need_areas'         => $needAreas,
                'primary_diagnosis'  => $diagnosis,
                'strengths'          => "El alumno muestra motivación y disposición para el trabajo en equipo.",
                'support_areas'      => "Requiere estrategias de organización y atención sostenida.",
                'opened_at'          => now()->subMonths(rand(2, 8)),
                'created_by'         => $psico->id,
                'updated_by'         => $psico->id,
            ]);

            // Add a sample observation
            $obs = Observation::create([
                'student_file_id'         => $file->id,
                'student_id'              => $student->id,
                'school_cycle_id'         => $cycle->id,
                'created_by'              => $psico->id,
                'observation_date'        => now()->subDays(rand(10, 60)),
                'grade_group'             => $gradeSection,
                'observations'            => "Se observa al alumno {$firstName} durante la clase de español. "
                    . "Presenta dificultades para mantener la atención por períodos prolongados. "
                    . "Requiere recordatorios frecuentes para retomar la tarea. "
                    . "La dinámica con sus compañeros es adecuada.",
                'required_prefectura'     => false,
                'required_parent_interview' => true,
                'required_channeling'     => false,
                'agreements_text'         => "Se acuerda: 1) Estrategias de pausas activas en el aula. "
                    . "2) Comunicación semanal con los padres sobre avances.",
                'next_appointment_date'   => now()->addDays(rand(7, 21)),
                'required_presence'       => 'Madre o tutor, Maestro titular',
                'attendees'               => 'Psicopedagógico, Maestro titular',
                'elaborated_by_name'      => $psico->name,
                'is_draft'                => false,
            ]);

            // Add a sample interview
            Interview::create([
                'student_file_id'     => $file->id,
                'student_id'          => $student->id,
                'school_cycle_id'     => $cycle->id,
                'created_by'          => $psico->id,
                'interview_date'      => now()->subDays(rand(15, 45)),
                'grade_group'         => $gradeSection,
                'modality'            => InterviewModality::Presencial,
                'purpose_and_objective' => "Dar a conocer a los padres las observaciones realizadas en el aula "
                    . "y acordar estrategias de apoyo en casa y en la escuela.",
                'comments'            => "Los padres se muestran colaboradores y dispuestos a apoyar. "
                    . "Refieren que en casa también presentan retos similares. "
                    . "Se discuten estrategias de rutina y organización.",
                'attendees'           => 'Madre, Psicopedagógico',
                'conducted_by_name'   => $psico->name,
                'is_draft'            => false,
            ]);

            // Add a sample agreement
            Agreement::create([
                'student_file_id'   => $file->id,
                'student_id'        => $student->id,
                'observation_id'    => $obs->id,
                'created_by'        => $psico->id,
                'description'       => "Implementar estrategias de pausas activas cada 20 minutos durante clase.",
                'responsible_name'  => 'Maestro titular',
                'due_date'          => now()->addDays(rand(14, 30)),
                'status'            => AgreementStatus::Pendiente,
            ]);

            // Create appointment from observation's next_appointment_date
            if ($obs->next_appointment_date) {
                Appointment::create([
                    'student_file_id'    => $file->id,
                    'student_id'         => $student->id,
                    'created_by'         => $psico->id,
                    'observation_id'     => $obs->id,
                    'title'              => "Seguimiento — {$firstName} {$lastName}",
                    'starts_at'          => $obs->next_appointment_date->setTime(10, 0),
                    'ends_at'            => $obs->next_appointment_date->setTime(11, 0),
                    'notes'              => "Seguimiento de acuerdos de observación anterior.",
                    'status'             => 'programada',
                    'required_attendees' => ['madre_tutor', 'maestro_espanol'],
                ]);
            }
        }
    }
}
