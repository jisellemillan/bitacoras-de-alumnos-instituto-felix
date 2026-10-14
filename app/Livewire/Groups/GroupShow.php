<?php

namespace App\Livewire\Groups;

use App\Enums\StudentStatus;
use App\Models\Group;
use App\Models\Student;
use Livewire\Component;
use Livewire\WithPagination;

class GroupShow extends Component
{
    use WithPagination;

    public Group $group;
    public string $search = '';
    public string $statusFilter = '';

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

        return view('livewire.groups.group-show', [
            'students' => $students,
            'totalGroupStudents' => $totalGroupStudents,
            'statuses' => StudentStatus::cases(),
        ])->layout('layouts.app', ['title' => "Grupo {$this->group->label} — Alumnos"]);
    }
}
