<?php

namespace App\Livewire\Agreements;

use App\Enums\AgreementStatus;
use App\Models\Agreement;
use App\Models\AuditLog;
use App\Models\SchoolCycle;
use App\Models\Student;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class AgreementModal extends Component
{
    use AuthorizesRequests;

    public bool $show = false;
    public bool $isEditing = false;
    public ?int $agreementId = null;
    public ?int $studentId = null;

    // Campos del acuerdo
    public string $description = '';
    public string $responsible_name = '';
    public string $due_date = '';
    public string $status = 'pendiente';

    protected $listeners = [
        'open-agreement-modal'  => 'openModal',
        'open-agreement-edit'   => 'openEditModal',
    ];

    public function rules(): array
    {
        return [
            'description'      => ['required', 'string', 'min:5'],
            'responsible_name' => ['nullable', 'string', 'max:150'],
            'due_date'         => ['nullable', 'date'],
            'status'           => ['required', 'in:pendiente,en_proceso,cumplido,cancelado'],
        ];
    }

    public function validationAttributes(): array
    {
        return [
            'description'      => 'descripción del acuerdo',
            'responsible_name' => 'responsable',
            'due_date'         => 'fecha compromiso',
        ];
    }

    public function openModal(int $studentId): void
    {
        $this->reset();
        $this->studentId = $studentId;
        $this->status    = 'pendiente';
        $this->due_date  = now()->addWeeks(2)->format('Y-m-d');
        $this->show      = true;
    }

    public function openEditModal(int $agreementId): void
    {
        $agreement = Agreement::findOrFail($agreementId);

        $this->reset();
        $this->isEditing        = true;
        $this->agreementId      = $agreement->id;
        $this->studentId        = $agreement->student_id;
        $this->description      = $agreement->description;
        $this->responsible_name = $agreement->responsible_name ?? '';
        $this->due_date         = $agreement->due_date?->format('Y-m-d') ?? '';
        $this->status           = $agreement->status->value;

        $this->show = true;
    }

    public function save(): void
    {
        $this->validate();
        $student = Student::with('studentFile')->findOrFail($this->studentId);
        $cycle   = SchoolCycle::current();

        $data = [
            'student_file_id'  => $student->studentFile->id,
            'student_id'       => $student->id,
            'description'      => $this->description,
            'responsible_name' => $this->responsible_name ?: null,
            'due_date'         => $this->due_date ?: null,
            'status'           => $this->status,
        ];

        if ($this->isEditing) {
            $agreement = Agreement::findOrFail($this->agreementId);
            $agreement->update($data);
            $action  = 'updated';
            $label   = "Actualización de acuerdo de {$student->full_name}";
            $message = 'Acuerdo actualizado con éxito.';
        } else {
            $data['created_by'] = auth()->id();
            $agreement = Agreement::create($data);
            $action  = 'created';
            $label   = "Registro de acuerdo para {$student->full_name}";
            $message = 'Acuerdo registrado exitosamente.';
        }

        AuditLog::create([
            'user_id'         => auth()->id(),
            'action'          => $action,
            'auditable_type'  => Agreement::class,
            'auditable_id'    => $agreement->id,
            'auditable_label' => $label,
            'ip_address'      => request()->ip(),
            'user_agent'      => request()->userAgent(),
            'created_at'      => now(),
        ]);

        $this->show = false;
        $this->dispatch('agreement-saved');
        $this->dispatch('toast', [
            'type'    => 'success',
            'title'   => 'Acuerdo',
            'message' => $message,
        ]);
    }

    public function render()
    {
        return view('livewire.agreements.agreement-modal', [
            'statuses' => AgreementStatus::cases(),
        ]);
    }
}
