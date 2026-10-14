<?php

use App\Livewire\Agreements\AgreementModal;
use App\Livewire\Appointments\AppointmentModal;
use App\Livewire\Interviews\InterviewModal;
use App\Livewire\Observations\ObservationModal;
use App\Livewire\StudentFiles\StudentFilePage;
use App\Models\Agreement;
use App\Models\Appointment;
use App\Models\Group;
use App\Models\Interview;
use App\Models\Observation;
use App\Models\SchoolCycle;
use App\Models\Student;
use App\Models\StudentFile;
use App\Models\User;
use Livewire\Livewire;

// ── Helpers ───────────────────────────────────────────────────────────────────

function createStudentWithFile(): Student
{
    $cycle = SchoolCycle::create([
        'name'      => 'Test 2026-2027',
        'starts_at' => '2026-08-15',
        'ends_at'   => '2027-06-30',
        'is_active' => true,
    ]);

    $group = Group::create([
        'school_cycle_id' => $cycle->id,
        'grade'           => '4',
        'section'         => 'A',
    ]);

    $student = Student::create([
        'first_name' => 'Alumno',
        'last_name'  => 'Prueba',
        'birthdate'  => '2015-03-10',
    ]);

    $student->enrollments()->create([
        'group_id'        => $group->id,
        'school_cycle_id' => $cycle->id,
        'is_current'      => true,
    ]);

    StudentFile::create([
        'student_id'  => $student->id,
        'file_type'   => 'sin_diagnostico',
        'status'      => 'activo',
        'intake_route'=> 'A',
        'opened_at'   => now(),
    ]);

    return $student->fresh(['studentFile', 'currentEnrollment.group.schoolCycle', 'observations', 'interviews', 'agreements', 'appointments', 'documents', 'enrollments']);
}


function adminUser(): User
{
    return User::where('email', 'admin@felix.edu.mx')->first()
        ?? User::factory()->create([
            'email'    => 'admin@felix.edu.mx',
            'password' => bcrypt('admin1234'),
            'role'     => 'admin_tecnico',
        ]);
}

// ── OBSERVACIONES ─────────────────────────────────────────────────────────────

describe('ObservationModal', function () {
    it('muestra el modal al recibir el evento open-observation-modal', function () {
        $user    = adminUser();
        $student = createStudentWithFile();

        Livewire::actingAs($user)
            ->test(ObservationModal::class)
            ->dispatch('open-observation-modal', studentId: $student->id)
            ->assertSet('show', true)
            ->assertSet('studentId', $student->id);
    });

    it('crea una observación correctamente', function () {
        $user    = adminUser();
        $student = createStudentWithFile();

        Livewire::actingAs($user)
            ->test(ObservationModal::class)
            ->dispatch('open-observation-modal', studentId: $student->id)
            ->set('observations', 'El alumno mostró dificultad para seguir instrucciones grupales durante la actividad de matemáticas.')
            ->set('observation_date', now()->format('Y-m-d'))
            ->call('save')
            ->assertSet('show', false)
            ->assertDispatched('observation-saved')
            ->assertDispatched('toast');

        expect(Observation::where('student_id', $student->id)->count())->toBe(1);
    });

    it('falla con validación si falta el cuerpo de observaciones', function () {
        $user    = adminUser();
        $student = createStudentWithFile();

        Livewire::actingAs($user)
            ->test(ObservationModal::class)
            ->dispatch('open-observation-modal', studentId: $student->id)
            ->set('observations', '')
            ->call('save')
            ->assertHasErrors(['observations']);
    });

    it('carga una observación para editar', function () {
        $user    = adminUser();
        $student = createStudentWithFile();

        $cycle = SchoolCycle::current();
        $obs   = Observation::create([
            'student_file_id'  => $student->studentFile->id,
            'student_id'       => $student->id,
            'school_cycle_id'  => $cycle->id,
            'observation_date' => now()->format('Y-m-d'),
            'observations'     => 'Texto de observación original para edición.',
            'created_by'       => $user->id,
        ]);

        Livewire::actingAs($user)
            ->test(ObservationModal::class)
            ->dispatch('open-observation-edit', observationId: $obs->id)
            ->assertSet('isEditing', true)
            ->assertSet('observations', 'Texto de observación original para edición.');
    });
});

// ── EXPEDIENTE DEL ALUMNO ─────────────────────────────────────────────────────

describe('StudentFilePage — eliminaciones', function () {
    it('elimina una observación desde el expediente', function () {
        $user    = adminUser();
        $student = createStudentWithFile();

        $cycle = SchoolCycle::current();
        $obs   = Observation::create([
            'student_file_id'  => $student->studentFile->id,
            'student_id'       => $student->id,
            'school_cycle_id'  => $cycle->id,
            'observation_date' => now()->format('Y-m-d'),
            'observations'     => 'Observación para eliminar.',
            'created_by'       => $user->id,
        ]);

        Livewire::actingAs($user)
            ->test(StudentFilePage::class, ['student' => $student])
            ->call('deleteObservation', $obs->id)
            ->assertDispatched('toast');

        expect(Observation::withTrashed()->find($obs->id)->deleted_at)->not->toBeNull();
    });

    it('elimina una entrevista desde el expediente', function () {
        $user    = adminUser();
        $student = createStudentWithFile();

        $cycle = SchoolCycle::current();
        $ent   = Interview::create([
            'student_file_id'        => $student->studentFile->id,
            'student_id'             => $student->id,
            'school_cycle_id'        => $cycle->id,
            'interview_date'         => now()->format('Y-m-d'),
            'modality'               => 'presencial',
            'purpose_and_objective'  => 'Entrevista de prueba para eliminación.',
            'created_by'             => $user->id,
        ]);

        Livewire::actingAs($user)
            ->test(StudentFilePage::class, ['student' => $student])
            ->call('deleteInterview', $ent->id)
            ->assertDispatched('toast');

        expect(Interview::withTrashed()->find($ent->id)->deleted_at)->not->toBeNull();
    });

    it('elimina un acuerdo y actualiza el estatus correctamente', function () {
        $user    = adminUser();
        $student = createStudentWithFile();

        $cycle = SchoolCycle::current();
        $agr   = Agreement::create([
            'student_file_id' => $student->studentFile->id,
            'student_id'      => $student->id,
            'description'     => 'Acuerdo de prueba para cambio de estatus.',
            'status'          => 'pendiente',
            'created_by'      => $user->id,
        ]);

        Livewire::actingAs($user)
            ->test(StudentFilePage::class, ['student' => $student])
            ->call('changeAgreementStatus', $agr->id, 'cumplido')
            ->assertDispatched('toast');

        expect($agr->fresh()->status->value)->toBe('cumplido');
    });

    it('elimina una cita desde el expediente', function () {
        $user    = adminUser();
        $student = createStudentWithFile();

        $appt = Appointment::create([
            'student_file_id' => $student->studentFile->id,
            'student_id'      => $student->id,
            'title'           => 'Cita de prueba',
            'starts_at'       => now()->addDay(),
            'status'          => 'programada',
            'created_by'      => $user->id,
        ]);

        Livewire::actingAs($user)
            ->test(StudentFilePage::class, ['student' => $student])
            ->call('deleteAppointment', $appt->id)
            ->assertDispatched('toast');

        expect(Appointment::withTrashed()->find($appt->id)->deleted_at)->not->toBeNull();
    });
});

// ── MODAL DE ENTREVISTAS ──────────────────────────────────────────────────────

describe('InterviewModal', function () {
    it('crea una entrevista correctamente', function () {
        $user    = adminUser();
        $student = createStudentWithFile();

        Livewire::actingAs($user)
            ->test(InterviewModal::class)
            ->dispatch('open-interview-modal', studentId: $student->id)
            ->set('interview_date', now()->format('Y-m-d'))
            ->set('modality', 'presencial')
            ->set('purpose_and_objective', 'Revisar el avance académico y comportamiento del alumno en clase.')
            ->call('save')
            ->assertSet('show', false)
            ->assertDispatched('interview-saved');

        expect(Interview::where('student_id', $student->id)->count())->toBe(1);
    });

    it('falla si el motivo y objetivo es muy corto', function () {
        $user    = adminUser();
        $student = createStudentWithFile();

        Livewire::actingAs($user)
            ->test(InterviewModal::class)
            ->dispatch('open-interview-modal', studentId: $student->id)
            ->set('purpose_and_objective', 'Corto')
            ->call('save')
            ->assertHasErrors(['purpose_and_objective']);
    });
});

// ── MODAL DE ACUERDOS ─────────────────────────────────────────────────────────

describe('AgreementModal', function () {
    it('crea un acuerdo con estatus pendiente por defecto', function () {
        $user    = adminUser();
        $student = createStudentWithFile();

        Livewire::actingAs($user)
            ->test(AgreementModal::class)
            ->dispatch('open-agreement-modal', studentId: $student->id)
            ->set('description', 'El docente aplicará estrategias diferenciadas en actividades de escritura.')
            ->set('status', 'pendiente')
            ->call('save')
            ->assertSet('show', false)
            ->assertDispatched('agreement-saved');

        expect(Agreement::where('student_id', $student->id)->count())->toBe(1);
    });
});

// ── MODAL DE CITAS ────────────────────────────────────────────────────────────

describe('AppointmentModal', function () {
    it('crea una cita correctamente', function () {
        $user    = adminUser();
        $student = createStudentWithFile();

        Livewire::actingAs($user)
            ->test(AppointmentModal::class)
            ->dispatch('open-appointment-modal', studentId: $student->id)
            ->set('title', 'Seguimiento académico bimestral')
            ->set('starts_at', now()->addDay()->format('Y-m-d\TH:i'))
            ->call('save')
            ->assertSet('show', false)
            ->assertDispatched('appointment-saved');

        expect(Appointment::where('student_id', $student->id)->count())->toBe(1);
    });

    it('agrega y elimina asistentes correctamente', function () {
        $user    = adminUser();
        $student = createStudentWithFile();

        Livewire::actingAs($user)
            ->test(AppointmentModal::class)
            ->dispatch('open-appointment-modal', studentId: $student->id)
            ->set('attendee_input', 'Madre del alumno')
            ->call('addAttendee')
            ->assertSet('required_attendees', ['Madre del alumno'])
            ->set('attendee_input', 'Docente titular')
            ->call('addAttendee')
            ->assertSet('required_attendees', ['Madre del alumno', 'Docente titular'])
            ->call('removeAttendee', 0)
            ->assertSet('required_attendees', ['Docente titular']);
    });
});
