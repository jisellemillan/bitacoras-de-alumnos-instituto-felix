<?php

use App\Livewire\Appointments\AppointmentModal;
use App\Livewire\Appointments\CalendarPage;
use App\Livewire\Students\StudentsIndex;
use App\Models\Appointment;
use App\Models\Group;
use App\Models\SchoolCycle;
use App\Models\Student;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    // Configurar ciclo activo y grupo para pruebas
    $this->cycle = SchoolCycle::firstOrCreate(
        ['name' => '2026-2027'],
        ['starts_at' => '2026-08-15', 'ends_at' => '2027-06-30', 'is_active' => true]
    );

    $this->group = Group::firstOrCreate(
        ['school_cycle_id' => $this->cycle->id, 'grade' => '4', 'section' => 'A'],
        ['school_cycle_id' => $this->cycle->id, 'grade' => '4', 'section' => 'A']
    );

    $this->admin = User::firstOrCreate(
        ['email' => 'admin@felix.edu.mx'],
        [
            'name'       => 'Administrador General',
            'first_name' => 'Administrador',
            'last_name'  => 'General',
            'password'   => bcrypt('admin1234'),
            'role'       => 'admin_tecnico',
            'is_active'  => true,
        ]
    );
});

describe('Flujo de Login y Autenticación', function () {
    it('permite login con credenciales válidas y redirige sin error', function () {
        Livewire::test('pages.auth.login')
            ->set('form.email', 'admin@felix.edu.mx')
            ->set('form.password', 'admin1234')
            ->call('login')
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticatedAs($this->admin);
    });

    it('rechaza login con credenciales erróneas', function () {
        Livewire::test('pages.auth.login')
            ->set('form.email', 'admin@felix.edu.mx')
            ->set('form.password', 'clave_incorrecta')
            ->call('login')
            ->assertHasErrors(['form.email']);

        $this->assertGuest();
    });
});

describe('Flujo de Alumnos (Registro manual y persistencia en BD)', function () {
    it('permite crear un alumno desde StudentsIndex y lo persiste en la base de datos', function () {
        $countInitial = Student::count();

        Livewire::actingAs($this->admin)
            ->test(StudentsIndex::class)
            ->call('openCreateModal')
            ->assertSet('showModal', true)
            ->set('form.first_name', 'Mateo')
            ->set('form.last_name', 'García Robledo')
            ->set('form.birthdate', '2015-05-20')
            ->set('form.group_id', $this->group->id)
            ->set('form.intake_route', 'A')
            ->set('form.primary_diagnosis', 'Diagnóstico preliminar TDAH')
            ->call('save')
            ->assertSet('showModal', false)
            ->assertDispatched('student-saved')
            ->assertDispatched('toast');

        expect(Student::count())->toBe($countInitial + 1);

        $student = Student::where('first_name', 'Mateo')->first();
        expect($student)->not->toBeNull()
            ->and($student->last_name)->toBe('García Robledo')
            ->and($student->studentFile)->not->toBeNull()
            ->and($student->currentEnrollment)->not->toBeNull()
            ->and($student->currentEnrollment->group_id)->toBe($this->group->id);
    });
});

describe('Flujo de Citas en el Calendario', function () {
    it('permite agendar una cita asociando un alumno desde AppointmentModal y persiste en BD', function () {
        // Crear alumno para la cita
        $student = Student::create([
            'first_name' => 'Sofía',
            'last_name'  => 'Navarro Solís',
            'birthdate'  => '2014-11-12',
            'is_active'  => true,
        ]);
        $student->studentFile()->create([
            'file_type' => 'sin_diagnostico',
            'status'    => 'activo',
            'opened_at' => now(),
            'created_by'=> $this->admin->id,
        ]);

        Livewire::actingAs($this->admin)
            ->test(AppointmentModal::class)
            ->call('openModal', null, '2026-10-25')
            ->assertSet('show', true)
            ->set('studentId', $student->id)
            ->set('title', 'Revisión trimestral psicopedagógica')
            ->set('starts_at', '2026-10-25T10:00')
            ->set('ends_at', '2026-10-25T11:00')
            ->set('location', 'Cubículo Psicopedagogía')
            ->set('status', 'programada')
            ->call('save')
            ->assertSet('show', false)
            ->assertDispatched('appointment-saved')
            ->assertDispatched('toast');

        $appt = Appointment::where('student_id', $student->id)->first();
        expect($appt)->not->toBeNull()
            ->and($appt->title)->toBe('Revisión trimestral psicopedagógica')
            ->and($appt->starts_at->format('Y-m-d H:i'))->toBe('2026-10-25 10:00');
    });

    it('la cita creada aparece y se lista en CalendarPage', function () {
        $student = Student::create([
            'first_name' => 'Camila',
            'last_name'  => 'Ruiz',
            'birthdate'  => '2015-01-10',
            'is_active'  => true,
        ]);
        $file = $student->studentFile()->create([
            'file_type' => 'sin_diagnostico',
            'status'    => 'activo',
            'opened_at' => now(),
            'created_by'=> $this->admin->id,
        ]);

        $appt = Appointment::create([
            'student_file_id' => $file->id,
            'student_id'      => $student->id,
            'created_by'      => $this->admin->id,
            'title'           => 'Cita de bienvenida',
            'starts_at'       => now()->addDays(2),
            'status'          => 'programada',
        ]);

        Livewire::actingAs($this->admin)
            ->test(CalendarPage::class)
            ->assertSee('Cita de bienvenida')
            ->assertSee('Camila');
    });
});
