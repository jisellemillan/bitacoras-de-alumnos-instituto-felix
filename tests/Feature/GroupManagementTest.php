<?php

use App\Enums\Grade;
use App\Enums\UserRole;
use App\Livewire\Appointments\AppointmentModal;
use App\Livewire\Groups\GroupsIndex;
use App\Livewire\Students\StudentsIndex;
use App\Models\Enrollment;
use App\Models\Group;
use App\Models\SchoolCycle;
use App\Models\Student;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->cycle = SchoolCycle::firstOrCreate(
        ['name' => '2025-2026'],
        ['is_active' => true, 'starts_at' => now(), 'ends_at' => now()->addYear()]
    );

    $this->admin = User::firstOrCreate(
        ['email' => 'admin@felix.edu.mx'],
        [
            'name' => 'Administrador Félix',
            'first_name' => 'Administrador',
            'last_name' => 'Félix',
            'password' => bcrypt('admin1234'),
            'role' => UserRole::AdminTecnico,
            'is_active' => true,
        ]
    );
});

describe('Gestión de Grupos y Creación Rápida', function () {
    it('permite crear un grupo rápido desde el modal de alumno sin perder los datos previamente escritos', function () {
        $this->actingAs($this->admin);

        Livewire::test(StudentsIndex::class)
            ->call('openCreateModal')
            ->set('form.first_name', 'Mateo')
            ->set('form.last_name', 'Valenzuela')
            ->set('form.curp', 'VALM201501HDFRRN01')
            // Abrir submodal de grupo
            ->call('openQuickGroupModal')
            ->assertSet('showQuickGroupModal', true)
            ->set('quick_grade', 5)
            ->set('quick_section', 'C')
            ->set('quick_name', 'Grupo C Experimental')
            ->set('quick_description', 'Aula 205')
            ->call('saveQuickGroup')
            ->assertSet('showQuickGroupModal', false)
            // Verificar que los datos del alumno no se perdieron
            ->assertSet('form.first_name', 'Mateo')
            ->assertSet('form.last_name', 'Valenzuela')
            ->assertSet('form.curp', 'VALM201501HDFRRN01')
            // Y que el nuevo grupo quedó seleccionado en form.group_id
            ->assertOk();

        $group = Group::where('grade', Grade::Quinto)->where('section', 'C')->first();
        expect($group)->not->toBeNull()
            ->and($group->name)->toBe('Grupo C Experimental')
            ->and($group->description)->toBe('Aula 205');

        // Guardar el alumno con el grupo recién creado
        Livewire::test(StudentsIndex::class)
            ->set('form.first_name', 'Mateo')
            ->set('form.last_name', 'Valenzuela')
            ->set('form.curp', 'VALM201501HDFRRN01')
            ->set('form.group_id', $group->id)
            ->call('save')
            ->assertDispatched('student-saved');

        $student = Student::where('first_name', 'Mateo')->where('last_name', 'Valenzuela')->first();
        expect($student)->not->toBeNull()
            ->and($student->current_group_label)->toContain('5°C');
    });

    it('permite listar, crear y editar grupos desde GroupsIndex', function () {
        $this->actingAs($this->admin);

        Livewire::test(GroupsIndex::class)
            ->assertSee('Gestión de Grupos')
            ->call('openCreateModal')
            ->assertSet('showModal', true)
            ->set('grade', 3)
            ->set('section', 'B')
            ->set('name', '3° Bilingüe')
            ->set('description', 'Grupo vespertino')
            ->set('school_cycle_id', $this->cycle->id)
            ->call('save')
            ->assertSet('showModal', false)
            ->assertDispatched('toast');

        $createdGroup = Group::where('grade', 3)->where('section', 'B')->first();
        expect($createdGroup)->not->toBeNull()
            ->and($createdGroup->name)->toBe('3° Bilingüe');

        // Editar el grupo
        Livewire::test(GroupsIndex::class)
            ->call('openEditModal', $createdGroup->id)
            ->assertSet('isEditing', true)
            ->assertSet('name', '3° Bilingüe')
            ->set('name', '3° Bilingüe Avanzado')
            ->call('save')
            ->assertSet('showModal', false);

        expect($createdGroup->fresh()->name)->toBe('3° Bilingüe Avanzado');
    });

    it('bloquea la eliminación de un grupo si tiene alumnos asignados', function () {
        $this->actingAs($this->admin);

        $group = Group::create([
            'school_cycle_id' => $this->cycle->id,
            'grade' => 4,
            'section' => 'Z',
            'name' => 'Grupo con alumnos',
        ]);

        $student = Student::create([
            'first_name' => 'Sofía',
            'last_name' => 'Navarro',
            'is_active' => true,
        ]);

        Enrollment::create([
            'student_id' => $student->id,
            'group_id' => $group->id,
            'school_cycle_id' => $this->cycle->id,
            'is_current' => true,
        ]);

        // Intentar eliminar el grupo
        Livewire::test(GroupsIndex::class)
            ->call('deleteGroup', $group->id)
            ->assertDispatched('toast');

        // El grupo sigue existiendo en BD porque tiene alumnos
        expect(Group::find($group->id))->not->toBeNull();

        // Eliminar la inscripción y ahora sí permitir eliminar el grupo
        Enrollment::where('group_id', $group->id)->delete();

        Livewire::test(GroupsIndex::class)
            ->call('deleteGroup', $group->id)
            ->assertDispatched('toast');

        expect(Group::find($group->id))->toBeNull();
    });

    it('permite filtrar alumnos por grupo al agendar una cita en AppointmentModal', function () {
        $this->actingAs($this->admin);

        $groupA = Group::create([
            'school_cycle_id' => $this->cycle->id,
            'grade' => 4,
            'section' => 'X',
        ]);

        $groupB = Group::create([
            'school_cycle_id' => $this->cycle->id,
            'grade' => 5,
            'section' => 'Y',
        ]);

        $studentA = Student::create([
            'first_name' => 'Carlos',
            'last_name' => 'Grupo X',
            'is_active' => true,
        ]);
        Enrollment::create([
            'student_id' => $studentA->id,
            'group_id' => $groupA->id,
            'school_cycle_id' => $this->cycle->id,
            'is_current' => true,
        ]);

        $studentB = Student::create([
            'first_name' => 'Diana',
            'last_name' => 'Grupo Y',
            'is_active' => true,
        ]);
        Enrollment::create([
            'student_id' => $studentB->id,
            'group_id' => $groupB->id,
            'school_cycle_id' => $this->cycle->id,
            'is_current' => true,
        ]);

        // Sin filtro, muestra ambos
        Livewire::test(AppointmentModal::class)
            ->call('openModal')
            ->assertSee('Carlos Grupo X')
            ->assertSee('Diana Grupo Y')
            // Filtrar solo por grupo A
            ->set('filterGroupId', (string) $groupA->id)
            ->assertSee('Carlos Grupo X')
            ->assertDontSee('Diana Grupo Y');
    });

    it('muestra únicamente a los estudiantes de un grupo en GroupShow y mensaje cuando está vacío', function () {
        $this->actingAs($this->admin);

        $groupA = Group::create([
            'school_cycle_id' => $this->cycle->id,
            'grade' => 4,
            'section' => 'W',
            'name' => 'Grupo W Alfa',
        ]);

        $groupB = Group::create([
            'school_cycle_id' => $this->cycle->id,
            'grade' => 4,
            'section' => 'V',
        ]);

        $studentA = Student::create(['first_name' => 'Ernesto', 'last_name' => 'Alfa', 'is_active' => true]);
        Enrollment::create(['student_id' => $studentA->id, 'group_id' => $groupA->id, 'school_cycle_id' => $this->cycle->id, 'is_current' => true]);

        $studentB = Student::create(['first_name' => 'Lucía', 'last_name' => 'Beta', 'is_active' => true]);
        Enrollment::create(['student_id' => $studentB->id, 'group_id' => $groupB->id, 'school_cycle_id' => $this->cycle->id, 'is_current' => true]);

        // Ver Grupo A: debe mostrar solo Ernesto Alfa
        Livewire::test(\App\Livewire\Groups\GroupShow::class, ['group' => $groupA])
            ->assertSee('Grupo 4°W (Grupo W Alfa)')
            ->assertSee('Ernesto Alfa')
            ->assertDontSee('Lucía Beta')
            ->assertSee('Volver a grupos');

        // Grupo sin alumnos: muestra empty state
        $emptyGroup = Group::create([
            'school_cycle_id' => $this->cycle->id,
            'grade' => 6,
            'section' => 'Z',
        ]);

        Livewire::test(\App\Livewire\Groups\GroupShow::class, ['group' => $emptyGroup])
            ->assertSee('Este grupo todavía no tiene alumnos asignados')
            ->assertSee('Volver a la lista de grupos');
    });

    it('permite navegar al expediente del estudiante desde GroupShow con contexto de regreso y breadcrumbs', function () {
        $this->actingAs($this->admin);

        $group = Group::create([
            'school_cycle_id' => $this->cycle->id,
            'grade' => 4,
            'section' => 'T',
        ]);

        $student = Student::create(['first_name' => 'Valeria', 'last_name' => 'Ríos', 'is_active' => true]);
        Enrollment::create(['student_id' => $student->id, 'group_id' => $group->id, 'school_cycle_id' => $this->cycle->id, 'is_current' => true]);

        // Acceder al expediente pasando from_group
        Livewire::withQueryParams(['from_group' => $group->id])
            ->test(\App\Livewire\StudentFiles\StudentFilePage::class, ['student' => $student])
            ->assertSee('Gestión de Grupos')
            ->assertSee("Grupo {$group->label}")
            ->assertSee("Volver a Grupo {$group->label}")
            ->assertSee('Valeria Ríos');

        // Alumno sin grupo asignado
        $lonelyStudent = Student::create(['first_name' => 'Raúl', 'last_name' => 'Sin Grupo', 'is_active' => true]);
        Livewire::withQueryParams([])
            ->test(\App\Livewire\StudentFiles\StudentFilePage::class, ['student' => $lonelyStudent])
            ->assertSee('Directorio de Alumnos')
            ->assertSee('Volver al listado')
            ->assertSee('Raúl Sin Grupo');
    });
});
