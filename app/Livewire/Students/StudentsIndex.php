<?php

namespace App\Livewire\Students;

use App\Actions\Students\CreateStudentAction;
use App\Actions\Students\DeleteStudentAction;
use App\Actions\Students\UpdateStudentAction;
use App\Enums\Grade;
use App\Enums\IntakeRoute;
use App\Enums\NeedArea;
use App\Enums\StudentStatus;
use App\Enums\UserRole;
use App\Livewire\Forms\StudentForm;
use App\Models\Group;
use App\Models\SchoolCycle;
use App\Models\Student;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;

class StudentsIndex extends Component
{
    use WithPagination, AuthorizesRequests;

    // Filtros reactivos
    public string $search = '';
    public string $gradeFilter = '';
    public string $groupFilter = '';
    public string $statusFilter = '';

    // Estado del Modal
    public bool $showModal = false;
    public bool $isEditing = false;
    public ?int $selectedStudentId = null;

    // Submodal de creación rápida de grupo sin perder datos
    public bool $showQuickGroupModal = false;
    public int $quick_grade = 4;
    public string $quick_section = 'A';
    public string $quick_name = '';
    public string $quick_description = '';

    // Form Object
    public StudentForm $form;

    // Eventos
    protected $listeners = [
        'student-saved' => '$refresh',
        'student-deleted' => '$refresh',
        'restore-student' => 'restoreStudent',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingGradeFilter(): void
    {
        $this->groupFilter = '';
        $this->resetPage();
    }

    public function openQuickGroupModal(): void
    {
        $this->quick_grade = 4;
        $this->quick_section = 'A';
        $this->quick_name = '';
        $this->quick_description = '';
        $this->showQuickGroupModal = true;
    }

    public function closeQuickGroupModal(): void
    {
        $this->showQuickGroupModal = false;
    }

    public function saveQuickGroup(): void
    {
        $this->validate([
            'quick_grade' => ['required', 'integer', 'between:1,6'],
            'quick_section' => ['required', 'string', 'max:5'],
            'quick_name' => ['nullable', 'string', 'max:100'],
            'quick_description' => ['nullable', 'string', 'max:255'],
        ], [], [
            'quick_grade' => 'grado',
            'quick_section' => 'sección',
            'quick_name' => 'nombre del grupo',
        ]);

        $cycle = SchoolCycle::current() ?? SchoolCycle::latest()->first();
        if (!$cycle) {
            $year = (int) date('Y');
            $cycle = SchoolCycle::create([
                'name' => "{$year}-" . ($year + 1),
                'is_active' => true,
                'starts_at' => now(),
                'ends_at' => now()->addYear(),
            ]);
        }

        $formattedSection = strtoupper(trim($this->quick_section));

        $group = Group::where('school_cycle_id', $cycle->id)
            ->where('grade', $this->quick_grade)
            ->where('section', $formattedSection)
            ->first();

        if ($group) {
            $group->update([
                'name' => $this->quick_name ?: $group->name,
                'description' => $this->quick_description ?: $group->description,
            ]);
        } else {
            $group = Group::create([
                'school_cycle_id' => $cycle->id,
                'grade' => $this->quick_grade,
                'section' => $formattedSection,
                'name' => $this->quick_name ?: null,
                'description' => $this->quick_description ?: null,
            ]);
        }

        // Asignar automáticamente el nuevo grupo al formulario del niño conservando todos sus datos
        $this->form->group_id = $group->id;
        $this->showQuickGroupModal = false;

        $this->dispatch('toast', [
            'type' => 'success',
            'title' => 'Grupo listo',
            'message' => "Se ha creado y seleccionado el grupo {$group->label} sin perder tus datos capturados.",
        ]);
    }

    public function openCreateModal(): void
    {
        $this->authorize('create', Student::class);
        $this->form->reset();
        $this->isEditing = false;
        $this->selectedStudentId = null;

        // Grupo por defecto
        $firstGroup = Group::first();
        if ($firstGroup) {
            $this->form->group_id = $firstGroup->id;
        }

        $this->showModal = true;
    }

    public function openEditModal(int $studentId): void
    {
        $student = Student::with(['studentFile', 'currentEnrollment'])->findOrFail($studentId);
        $this->authorize('update', $student);

        $this->form->reset();
        $this->isEditing = true;
        $this->selectedStudentId = $student->id;

        $this->form->first_name = $student->first_name;
        $this->form->last_name = $student->last_name;
        $this->form->birthdate = $student->birthdate?->format('Y-m-d');
        $this->form->sex = $student->sex;
        $this->form->curp = $student->curp;
        $this->form->group_id = $student->currentEnrollment?->group_id;

        if ($student->studentFile) {
            $this->form->intake_route = $student->studentFile->intake_route?->value ?? 'A';
            $this->form->status = $student->studentFile->status?->value ?? 'activo';
            $this->form->need_areas = $student->studentFile->need_areas ?? [];
            $this->form->primary_diagnosis = $student->studentFile->primary_diagnosis ?? '';
            $this->form->strengths = $student->studentFile->strengths ?? '';
            $this->form->support_areas = $student->studentFile->support_areas ?? '';
            $this->form->external_specialist = $student->studentFile->external_specialist ?? '';
            $this->form->treatment_medication = $student->studentFile->treatment_medication ?? '';
        }

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
            $message = 'Alumno registrado y expediente digital generado al instante.';
        }

        $this->showModal = false;
        $this->dispatch('student-saved');
        $this->dispatch('toast', [
            'type' => 'success',
            'title' => 'Éxito',
            'message' => $message,
        ]);
    }

    public function deleteStudent(int $studentId, DeleteStudentAction $deleter): void
    {
        $student = Student::findOrFail($studentId);
        $this->authorize('delete', $student);

        $deleter->execute($student, auth()->id());

        $this->dispatch('student-deleted');
        $this->dispatch('toast', [
            'type' => 'warning',
            'title' => 'Alumno dado de baja',
            'message' => "Se ha dado de baja a {$student->full_name}.",
            'undoAction' => "restore-student:{$studentId}",
        ]);
    }

    public function restoreStudent(int $studentId, DeleteStudentAction $deleter): void
    {
        $this->authorize('create', Student::class);
        $student = $deleter->restore($studentId, auth()->id());

        $this->dispatch('student-saved');
        $this->dispatch('toast', [
            'type' => 'success',
            'title' => 'Restaurado',
            'message' => "Se ha restaurado el registro de {$student->full_name}.",
        ]);
    }

    public function render()
    {
        $this->authorize('viewAny', Student::class);
        $user = auth()->user();
        $cycle = SchoolCycle::current();

        $query = Student::query()
            ->with(['studentFile', 'currentEnrollment.group'])
            ->latest();

        // Control de visibilidad según rol de usuario
        if ($user->role === UserRole::DocenteTitular || $user->role === UserRole::DocenteEspecial) {
            $myGroupIds = $user->groups()->pluck('grupos.id')->toArray();
            $query->whereHas('enrollments', function ($q) use ($myGroupIds) {
                $q->where('is_current', true)->whereIn('group_id', $myGroupIds);
            });
        }

        // Filtro de búsqueda
        if (!empty($this->search)) {
            $query->search($this->search);
        }

        // Filtro por grado
        if (!empty($this->gradeFilter)) {
            $query->inGrade((int) $this->gradeFilter);
        }

        // Filtro por grupo específico
        if (!empty($this->groupFilter)) {
            $query->inGroup((int) $this->groupFilter);
        }

        // Filtro por estatus de expediente
        if (!empty($this->statusFilter)) {
            $query->whereHas('studentFile', function ($q) {
                $q->where('status', $this->statusFilter);
            });
        }

        $students = $query->paginate(10);

        // Opciones de grupos disponibles para el ciclo activo o generales
        $groups = $cycle ? Group::where('school_cycle_id', $cycle->id)->orderBy('grade')->orderBy('section')->get() : collect();
        if ($groups->isEmpty()) {
            $groups = Group::orderBy('grade')->orderBy('section')->get();
        }

        return view('livewire.students.students-index', [
            'students' => $students,
            'groups' => $groups,
            'intakeRoutes' => IntakeRoute::cases(),
            'needAreas' => NeedArea::cases(),
            'statuses' => StudentStatus::cases(),
        ]);
    }
}
