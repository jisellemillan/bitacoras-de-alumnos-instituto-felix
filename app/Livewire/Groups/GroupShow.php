<?php

namespace App\Livewire\Groups;

use App\Actions\Students\CreateStudentAction;
use App\Actions\Students\UpdateStudentAction;
use App\Enums\IntakeRoute;
use App\Enums\NeedArea;
use App\Enums\StudentStatus;
use App\Livewire\Forms\StudentForm;
use App\Models\Group;
use App\Models\SchoolCycle;
use App\Models\Student;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;

class GroupShow extends Component
{
    use WithPagination, AuthorizesRequests;

    public Group $group;
    public string $search = '';
    public string $statusFilter = '';

    // Estado del modal de registro de alumnos (reutilizado)
    public StudentForm $form;
    public bool $showModal = false;
    public bool $isEditing = false;
    public ?int $selectedStudentId = null;

    public function mount(Group $group): void
    {
        $this->group = $group->load(['schoolCycle', 'teachers']);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->authorize('create', Student::class);
        $this->form->reset();
        $this->isEditing = false;
        $this->selectedStudentId = null;

        // Establecer el grupo actual como predeterminado
        $this->form->group_id = $this->group->id;

        $this->showModal = true;
    }

    public function save(CreateStudentAction $creator, UpdateStudentAction $updater): void
    {
        $this->form->validate();

        $data = [
            'first_name' => $this->form->first_name,
            'last_name' => $this->form->last_name,
            'birthdate' => $this->form->birthdate,
            'sex' => $this->form->sex,
            'curp' => $this->form->curp,
            'group_id' => $this->form->group_id,
            'intake_route' => $this->form->intake_route,
            'status' => $this->form->status,
            'need_areas' => $this->form->need_areas,
            'primary_diagnosis' => $this->form->primary_diagnosis,
            'strengths' => $this->form->strengths,
            'support_areas' => $this->form->support_areas,
            'external_specialist' => $this->form->external_specialist,
            'treatment_medication' => $this->form->treatment_medication,
            'student_file' => [
                'intake_route' => $this->form->intake_route,
                'status' => $this->form->status,
                'need_areas' => $this->form->need_areas,
                'primary_diagnosis' => $this->form->primary_diagnosis,
                'strengths' => $this->form->strengths,
                'support_areas' => $this->form->support_areas,
                'external_specialist' => $this->form->external_specialist,
                'treatment_medication' => $this->form->treatment_medication,
            ],
        ];

        if ($this->isEditing) {
            $student = Student::findOrFail($this->selectedStudentId);
            $this->authorize('update', $student);
            $updater->execute($student, $data, auth()->id());
            $message = 'Alumno y expediente actualizados correctamente.';
        } else {
            $this->authorize('create', Student::class);
            $student = $creator->execute($data, auth()->id());
            $message = "Alumno {$student->full_name} registrado y asignado a este grupo exitosamente.";
        }

        $this->showModal = false;
        $this->resetPage();
        $this->dispatch('toast', [
            'type' => 'success',
            'title' => 'Éxito',
            'message' => $message,
        ]);
    }

    public function render()
    {
        $query = Student::query()
            ->with(['studentFile', 'currentEnrollment.group'])
            ->whereHas('enrollments', function ($q) {
                $q->where('group_id', $this->group->id)
                  ->where('is_current', true);
            })
            ->orderBy('first_name')
            ->orderBy('last_name');

        if (!empty($this->search)) {
            $query->search(trim($this->search));
        }

        if (!empty($this->statusFilter)) {
            $query->whereHas('studentFile', function ($q) {
                $q->where('status', $this->statusFilter);
            });
        }

        $students = $query->paginate(15);

        // Conteo total en el grupo sin paginación ni filtros
        $totalGroupStudents = $this->group->students()
            ->wherePivot('is_current', true)
            ->count();

        // Opciones de grupos disponibles para el selector
        $cycle = SchoolCycle::current();
        $groups = $cycle ? Group::where('school_cycle_id', $cycle->id)->orderBy('grade')->orderBy('section')->get() : collect();
        if ($groups->isEmpty()) {
            $groups = Group::orderBy('grade')->orderBy('section')->get();
        }

        return view('livewire.groups.group-show', [
            'students' => $students,
            'totalGroupStudents' => $totalGroupStudents,
            'statuses' => StudentStatus::cases(),
            'intakeRoutes' => IntakeRoute::cases(),
            'needAreas' => NeedArea::cases(),
            'groups' => $groups,
        ])->layout('layouts.app', ['title' => "Grupo {$this->group->label} — Alumnos"]);
    }
}
