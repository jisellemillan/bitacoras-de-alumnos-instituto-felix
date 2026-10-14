<?php

namespace App\Livewire\Observations;

use App\Enums\SignatureRole;
use App\Models\AuditLog;
use App\Models\Observation;
use App\Models\ObservationSignature;
use App\Models\SchoolCycle;
use App\Models\Student;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class ObservationModal extends Component
{
    use AuthorizesRequests;

    public bool $show = false;
    public bool $isEditing = false;
    public ?int $observationId = null;
    public ?int $studentId = null;

    // Campos exactos del formato físico "Observación de alumno"
    public string $observation_date = '';
    public string $grade_group = '';
    public string $observations = '';
    public bool $required_prefectura = false;
    public bool $required_parent_interview = false;
    public bool $required_channeling = false;
    public string $agreements_text = '';
    public ?string $next_appointment_date = null;
    public string $required_presence = '';
    public string $attendees = '';
    public string $elaborated_by_name = '';

    // Firmas (8 roles físicos)
    public array $signatures = [];

    protected $listeners = [
        'open-observation-modal' => 'openModal',
        'open-observation-edit' => 'openEditModal',
    ];

    public function rules(): array
    {
        return [
            'observation_date' => ['required', 'date'],
            'observations' => ['required', 'string', 'min:5'],
            'grade_group' => ['nullable', 'string', 'max:20'],
            'required_prefectura' => ['boolean'],
            'required_parent_interview' => ['boolean'],
            'required_channeling' => ['boolean'],
            'agreements_text' => ['nullable', 'string'],
            'next_appointment_date' => ['nullable', 'date'],
            'required_presence' => ['nullable', 'string'],
            'attendees' => ['nullable', 'string'],
            'elaborated_by_name' => ['nullable', 'string', 'max:150'],
        ];
    }

    public function validationAttributes(): array
    {
        return [
            'observation_date' => 'fecha de observación',
            'observations' => 'cuerpo de observaciones',
            'agreements_text' => 'acuerdos',
            'next_appointment_date' => 'próxima cita',
        ];
    }

    public function openModal(int $studentId): void
    {
        $this->reset();
        $this->studentId = $studentId;
        $student = Student::with('currentEnrollment.group')->findOrFail($studentId);

        $this->observation_date = now()->format('Y-m-d');
        $this->grade_group = $student->current_group_label;
        $this->elaborated_by_name = auth()->user()->name;

        // Inicializar firmas vacías para los 8 roles
        foreach (SignatureRole::cases() as $role) {
            $this->signatures[$role->value] = [
                'name' => '',
                'present' => false,
            ];
        }

        $this->show = true;
    }

    public function openEditModal(int $observationId): void
    {
        $obs = Observation::with('signatures')->findOrFail($observationId);
        $this->authorize('update', $obs);

        $this->reset();
        $this->isEditing = true;
        $this->observationId = $obs->id;
        $this->studentId = $obs->student_id;

        $this->observation_date = $obs->observation_date->format('Y-m-d');
        $this->grade_group = $obs->grade_group ?? '';
        $this->observations = $obs->observations;
        $this->required_prefectura = $obs->required_prefectura;
        $this->required_parent_interview = $obs->required_parent_interview;
        $this->required_channeling = $obs->required_channeling;
        $this->agreements_text = $obs->agreements_text ?? '';
        $this->next_appointment_date = $obs->next_appointment_date?->format('Y-m-d');
        $this->required_presence = $obs->required_presence ?? '';
        $this->attendees = $obs->attendees ?? '';
        $this->elaborated_by_name = $obs->elaborated_by_name ?? '';

        $existingSigs = $obs->signatures->keyBy(fn ($s) => $s->signature_role->value);
        foreach (SignatureRole::cases() as $role) {
            $sig = $existingSigs->get($role->value);
            $this->signatures[$role->value] = [
                'name' => $sig?->signer_name ?? '',
                'present' => $sig?->is_present ?? false,
            ];
        }

        $this->show = true;
    }

    public function save(): void
    {
        $this->validate();
        $student = Student::with('studentFile')->findOrFail($this->studentId);
        $cycle = SchoolCycle::current();

        $data = [
            'student_file_id' => $student->studentFile->id,
            'student_id' => $student->id,
            'school_cycle_id' => $cycle->id,
            'observation_date' => $this->observation_date,
            'grade_group' => $this->grade_group,
            'observations' => $this->observations,
            'required_prefectura' => $this->required_prefectura,
            'required_parent_interview' => $this->required_parent_interview,
            'required_channeling' => $this->required_channeling,
            'agreements_text' => $this->agreements_text,
            'next_appointment_date' => $this->next_appointment_date,
            'required_presence' => $this->required_presence,
            'attendees' => $this->attendees,
            'elaborated_by_name' => $this->elaborated_by_name,
            'is_draft' => false,
        ];

        if ($this->isEditing) {
            $obs = Observation::findOrFail($this->observationId);
            $this->authorize('update', $obs);
            $obs->update($data);

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'updated',
                'auditable_type' => Observation::class,
                'auditable_id' => $obs->id,
                'auditable_label' => "Actualización de observación de {$student->full_name}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
            ]);
            $msg = 'Observación actualizada con éxito.';
        } else {
            $data['created_by'] = auth()->id();
            $obs = Observation::create($data);

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'created',
                'auditable_type' => Observation::class,
                'auditable_id' => $obs->id,
                'auditable_label' => "Registro de observación física para {$student->full_name}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
            ]);
            $msg = 'Observación registrada exitosamente.';
        }

        // Guardar/actualizar firmas
        foreach ($this->signatures as $roleVal => $sigData) {
            ObservationSignature::updateOrCreate(
                [
                    'observation_id' => $obs->id,
                    'signature_role' => $roleVal,
                ],
                [
                    'signer_name' => $sigData['name'] ?? null,
                    'is_present' => (bool) ($sigData['present'] ?? false),
                ]
            );
        }

        $this->show = false;
        $this->dispatch('observation-saved');
        $this->dispatch('toast', [
            'type' => 'success',
            'title' => 'Formato de Observación',
            'message' => $msg,
        ]);
    }

    public function render()
    {
        return view('livewire.observations.observation-modal', [
            'roles' => SignatureRole::cases(),
        ]);
    }
}
