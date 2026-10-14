<?php

namespace App\Livewire\Groups;

use App\Enums\Grade;
use App\Models\Group;
use App\Models\SchoolCycle;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class GroupsIndex extends Component
{
    use WithPagination;

    // Filtros
    public string $search = '';
    public string $gradeFilter = '';
    public string $cycleFilter = '';

    // Modal estado
    public bool $showModal = false;
    public bool $isEditing = false;
    public ?int $selectedGroupId = null;

    // Campos del formulario
    public int $grade = 4;
    public string $section = 'A';
    public string $name = '';
    public string $description = '';
    public ?int $school_cycle_id = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingGradeFilter(): void
    {
        $this->resetPage();
    }

    public function updatingCycleFilter(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->resetValidation();
        $this->isEditing = false;
        $this->selectedGroupId = null;

        $cycle = SchoolCycle::current() ?? SchoolCycle::latest()->first();

        $this->grade = 4;
        $this->section = 'A';
        $this->name = '';
        $this->description = '';
        $this->school_cycle_id = $cycle?->id;

        $this->showModal = true;
    }

    public function openEditModal(int $groupId): void
    {
        $this->resetValidation();
        $group = Group::findOrFail($groupId);

        $this->isEditing = true;
        $this->selectedGroupId = $group->id;
        $this->grade = $group->grade->value;
        $this->section = $group->section;
        $this->name = $group->name ?? '';
        $this->description = $group->description ?? '';
        $this->school_cycle_id = $group->school_cycle_id;

        $this->showModal = true;
    }

    public function save(): void
    {
        $formattedSection = strtoupper(trim($this->section));

        $this->validate([
            'grade' => ['required', 'integer', 'between:1,6'],
            'section' => [
                'required',
                'string',
                'max:5',
                Rule::unique('grupos')
                    ->where(fn ($q) => $q->where('school_cycle_id', $this->school_cycle_id)->where('grade', $this->grade))
                    ->ignore($this->selectedGroupId),
            ],
            'name' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
            'school_cycle_id' => ['required', 'exists:ciclos_escolares,id'],
        ], [
            'section.unique' => "Ya existe la sección {$formattedSection} para este grado y ciclo escolar.",
        ], [
            'grade' => 'grado',
            'section' => 'sección',
            'name' => 'nombre del grupo',
            'description' => 'descripción',
            'school_cycle_id' => 'ciclo escolar',
        ]);

        $data = [
            'school_cycle_id' => $this->school_cycle_id,
            'grade' => $this->grade,
            'section' => $formattedSection,
            'name' => $this->name ? trim($this->name) : null,
            'description' => $this->description ? trim($this->description) : null,
        ];

        if ($this->isEditing) {
            $group = Group::findOrFail($this->selectedGroupId);
            $group->update($data);
            $message = "Grupo {$group->label} actualizado correctamente.";
        } else {
            $group = Group::create($data);
            $message = "Grupo {$group->label} creado exitosamente.";
        }

        $this->showModal = false;
        $this->dispatch('toast', [
            'type' => 'success',
            'title' => 'Gestión de Grupos',
            'message' => $message,
        ]);
    }

    public function deleteGroup(int $groupId): void
    {
        $group = Group::withCount('students')->findOrFail($groupId);

        // Validar que no tenga niños asignados
        if ($group->students_count > 0) {
            $this->dispatch('toast', [
                'type' => 'error',
                'title' => 'No se puede eliminar el grupo',
                'message' => "El grupo {$group->label} tiene {$group->students_count} alumno(s) asignado(s). Debes reasignar a los alumnos a otro grupo antes de poder eliminarlo.",
            ]);
            return;
        }

        $label = $group->label;
        $group->delete();

        $this->dispatch('toast', [
            'type' => 'warning',
            'title' => 'Grupo eliminado',
            'message' => "El grupo {$label} ha sido eliminado.",
        ]);
    }

    public function render()
    {
        $cycles = SchoolCycle::orderByDesc('name')->get();
        $currentCycle = SchoolCycle::current() ?? $cycles->first();

        $query = Group::query()
            ->with('schoolCycle')
            ->withCount(['students' => function ($q) {
                $q->where('inscripciones.is_current', true);
            }]);

        if (!empty($this->cycleFilter)) {
            $query->where('school_cycle_id', $this->cycleFilter);
        } elseif ($currentCycle && empty($this->search) && empty($this->gradeFilter)) {
            // Por defecto mostrar el ciclo actual
            $query->where('school_cycle_id', $currentCycle->id);
        }

        if (!empty($this->gradeFilter)) {
            $query->where('grade', (int) $this->gradeFilter);
        }

        if (!empty($this->search)) {
            $term = trim($this->search);
            $query->where(function ($q) use ($term) {
                $q->where('section', 'like', "%{$term}%")
                  ->orWhere('name', 'like', "%{$term}%")
                  ->orWhere('description', 'like', "%{$term}%");
            });
        }

        $groups = $query->orderBy('grade')->orderBy('section')->paginate(12);

        return view('livewire.groups.groups-index', [
            'groups' => $groups,
            'cycles' => $cycles,
            'currentCycle' => $currentCycle,
        ])->layout('layouts.app', ['title' => 'Gestión de Grupos']);
    }
}
