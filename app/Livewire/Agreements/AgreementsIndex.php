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
        $query = Agreement::with(['student.currentEnrollment.group', 'author'])
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('description', 'like', "%{$this->search}%")
                        ->orWhere('responsible_name', 'like', "%{$this->search}%")
                        ->orWhereHas('student', function ($sq) {
                            $sq->where('first_name', 'like', "%{$this->search}%")
                               ->orWhere('last_name', 'like', "%{$this->search}%");
                        });
                });
            })
            ->when($this->statusFilter, function ($q) {
                $q->where('status', $this->statusFilter);
            });

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
