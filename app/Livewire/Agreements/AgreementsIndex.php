<?php

namespace App\Livewire\Agreements;

use App\Enums\AgreementStatus;
use App\Models\Agreement;
use App\Models\AuditLog;
use Livewire\Component;
use Livewire\WithPagination;

class AgreementsIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public string $viewMode = 'kanban'; // 'kanban' o 'list'

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'viewMode' => ['except' => 'kanban'],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updateStatus(int $agreementId, string $newStatus): void
    {
        $agreement = Agreement::with('student')->findOrFail($agreementId);
        $oldStatus = $agreement->status->value;
        $agreement->update(['status' => $newStatus]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'updated',
            'auditable_type' => Agreement::class,
            'auditable_id' => $agreement->id,
            'auditable_label' => "Cambio de estado de acuerdo ({$oldStatus} -> {$newStatus}) para {$agreement->student->full_name}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'title' => 'Acuerdo Actualizado',
            'message' => 'Se ha actualizado el estado del compromiso.',
        ]);
    }

    public function deleteAgreement(int $agreementId): void
    {
        $agreement = Agreement::with('student')->findOrFail($agreementId);
        $agreement->delete();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'deleted',
            'auditable_type' => Agreement::class,
            'auditable_id' => $agreement->id,
            'auditable_label' => "Eliminación de acuerdo para {$agreement->student->full_name}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'title' => 'Acuerdo Eliminado',
            'message' => 'El compromiso ha sido eliminado correctamente.',
        ]);
    }

    public function render()
    {
        $user = auth()->user();
        $rolUsuario = $user->role->value ?? $user->role;

        $query = Agreement::with(['student.currentEnrollment.group', 'author']);

        // APLICAR CANDADO ESTRICTO PARA DOCENTES
        // Solo pueden ver acuerdos de alumnos que estén en sus grupos asignados
        // o que tengan un permiso temporal ('AccessGrant') aprobado.
        if (in_array($rolUsuario, ['docente_titular', 'docente_especial'])) {
            
            // 1. Obtener IDs de alumnos por permisos temporales aprobados
            $grantedStudentIds = \App\Models\AccessGrant::where('user_id', $user->id)
                ->where('is_active', true)
                ->whereNotNull('student_id')
                ->pluck('student_id')
                ->toArray();

            // 2. Obtener IDs de los grupos que el docente tiene asignados
            $myGroupIds = $user->groups()->pluck('grupos.id')->toArray();

            // 3. Filtrar los acuerdos para que solo traiga los de esos alumnos
            $query->whereHas('student', function($q) use ($myGroupIds, $grantedStudentIds) {
                $q->where(function($subQ) use ($myGroupIds, $grantedStudentIds) {
                    // Condición A: El alumno está en uno de los grupos actuales del maestro
                    $subQ->whereHas('enrollments', function ($eq) use ($myGroupIds) {
                        $eq->whereIn('group_id', $myGroupIds)
                           ->where('is_current', true);
                    })
                    // Condición B: O el maestro tiene permiso explícito para ese alumno
                    ->orWhereIn('alumnos.id', $grantedStudentIds);
                });
            });
        }

        // Aplicar búsqueda (ya existente)
        $query->when($this->search, function ($q) {
            $q->where(function ($sub) {
                $sub->where('description', 'like', "%{$this->search}%")
                    ->orWhere('responsible_name', 'like', "%{$this->search}%")
                    ->orWhereHas('student', function ($sq) {
                        $sq->where('first_name', 'like', "%{$this->search}%")
                           ->orWhere('last_name', 'like', "%{$this->search}%");
                    });
            });
        });

        // Aplicar filtro por estado (ya existente)
        $query->when($this->statusFilter, function ($q) {
            $q->where('status', $this->statusFilter);
        });

        // Retornar resultados según el modo de vista (ya existente)
        if ($this->viewMode === 'kanban') {
            $agreements = (clone $query)->orderBy('due_date')->get();
            $groupedAgreements = [
                'pendiente' => $agreements->where('status', AgreementStatus::Pendiente),
                'en_proceso' => $agreements->where('status', AgreementStatus::EnProceso),
                'cumplido' => $agreements->where('status', AgreementStatus::Cumplido),
                'cancelado' => $agreements->where('status', AgreementStatus::Cancelado),
            ];

            return view('livewire.agreements.agreements-index', [
                'groupedAgreements' => $groupedAgreements,
                'statuses' => AgreementStatus::cases(),
            ]);
        }

        return view('livewire.agreements.agreements-index', [
            'agreements' => $query->orderBy('due_date')->paginate(15),
            'statuses' => AgreementStatus::cases(),
        ]);
    }
}
