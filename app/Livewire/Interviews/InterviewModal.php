<?php

namespace App\Livewire\Interviews;

use App\Enums\InterviewModality;
use App\Models\AuditLog;
use App\Models\Interview;
use App\Models\SchoolCycle;
use App\Models\Student;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class InterviewModal extends Component
{
    use AuthorizesRequests;

    public bool $show = false;
    public bool $isEditing = false;
    public ?int $interviewId = null;
    public ?int $studentId = null;

    // Campos del formato físico "Entrevista con padres y/o especialista"
    public string $interview_date = '';
    public string $grade_group = '';
    public string $modality = 'presencial';
    public string $purpose_and_objective = '';
    public string $comments = '';
    public string $attendees = '';
    public string $conducted_by_name = '';

    protected $listeners = [
        'open-interview-modal' => 'openModal',
        'open-interview-edit'  => 'openEditModal',
    ];

    public function rules(): array
    {
        return [
            'interview_date'       => ['required', 'date'],
            'modality'             => ['required', 'in:presencial,en_linea,via_telefonica'],
            'purpose_and_objective'=> ['required', 'string', 'min:10'],
            'grade_group'          => ['nullable', 'string', 'max:20'],
            'comments'             => ['nullable', 'string'],
            'attendees'            => ['nullable', 'string'],
            'conducted_by_name'    => ['nullable', 'string', 'max:150'],
        ];
    }

    public function validationAttributes(): array
    {
        return [
            'interview_date'        => 'fecha de entrevista',
            'modality'              => 'modalidad',
            'purpose_and_objective' => 'motivo y objetivo',
        ];
    }

    public function openModal(int $studentId): void
    {
        $this->reset();
        $this->studentId = $studentId;
        $student = Student::with('currentEnrollment.group')->findOrFail($studentId);

        $this->interview_date    = now()->format('Y-m-d');
        $this->grade_group       = $student->current_group_label;
        $this->conducted_by_name = auth()->user()->name;
        $this->modality          = 'presencial';

        $this->show = true;
    }

    public function openEditModal(int $interviewId): void
    {
        $interview = Interview::findOrFail($interviewId);

        $this->reset();
        $this->isEditing         = true;
        $this->interviewId       = $interview->id;
        $this->studentId         = $interview->student_id;

        $this->interview_date        = $interview->interview_date->format('Y-m-d');
        $this->grade_group           = $interview->grade_group ?? '';
        $this->modality              = $interview->modality->value ?? 'presencial';
        $this->purpose_and_objective = $interview->purpose_and_objective;
        $this->comments              = $interview->comments ?? '';
        $this->attendees             = $interview->attendees ?? '';
        $this->conducted_by_name     = $interview->conducted_by_name ?? '';

        $this->show = true;
    }

    public function save(): void
    {
        $this->validate();
        $student = Student::with('studentFile')->findOrFail($this->studentId);
        $cycle   = SchoolCycle::current();

        $data = [
            'student_file_id'       => $student->studentFile->id,
            'student_id'            => $student->id,
            'school_cycle_id'       => $cycle->id,
            'interview_date'        => $this->interview_date,
            'grade_group'           => $this->grade_group,
            'modality'              => $this->modality,
            'purpose_and_objective' => $this->purpose_and_objective,
            'comments'              => $this->comments,
            'attendees'             => $this->attendees,
            'conducted_by_name'     => $this->conducted_by_name,
            'is_draft'              => false,
        ];

        if ($this->isEditing) {
            $interview = Interview::findOrFail($this->interviewId);
            $interview->update($data);
            $action  = 'updated';
            $label   = "Actualización de entrevista de {$student->full_name}";
            $message = 'Entrevista actualizada con éxito.';
        } else {
            $data['created_by'] = auth()->id();
            $interview = Interview::create($data);
            $action  = 'created';
            $label   = "Registro de entrevista para {$student->full_name}";
            $message = 'Entrevista registrada exitosamente.';
        }

        AuditLog::create([
            'user_id'        => auth()->id(),
            'action'         => $action,
            'auditable_type' => Interview::class,
            'auditable_id'   => $interview->id,
            'auditable_label'=> $label,
            'ip_address'     => request()->ip(),
            'user_agent'     => request()->userAgent(),
            'created_at'     => now(),
        ]);

        $this->show = false;
        $this->dispatch('interview-saved');
        $this->dispatch('toast', [
            'type'    => 'success',
            'title'   => 'Formato de Entrevista',
            'message' => $message,
        ]);
    }

    public function render()
    {
        return view('livewire.interviews.interview-modal', [
            'modalities' => InterviewModality::cases(),
        ]);
    }
}
