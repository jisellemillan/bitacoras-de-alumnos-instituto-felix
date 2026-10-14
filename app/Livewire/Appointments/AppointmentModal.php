<?php

namespace App\Livewire\Appointments;

use App\Models\Appointment;
use App\Models\AuditLog;
use App\Models\Group;
use App\Models\SchoolCycle;
use App\Models\Student;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class AppointmentModal extends Component
{
    use AuthorizesRequests;

    public bool $show = false;
    public bool $isEditing = false;
    public ?int $appointmentId = null;
    public ?int $studentId = null;
    public string $filterGroupId = '';

    // Campos de la cita
    public string $title = '';
    public string $starts_at = '';
    public string $ends_at = '';
    public string $location = '';
    public string $notes = '';
    public string $status = 'programada';
    public array $required_attendees = [];
    public string $attendee_input = '';

    protected $listeners = [
        'open-appointment-modal' => 'openModal',
        'open-appointment-edit'  => 'openEditModal',
    ];

    public function rules(): array
    {
        return [
            'studentId'  => ['required', 'exists:alumnos,id'],
            'title'      => ['required', 'string', 'min:3', 'max:200'],
            'starts_at'  => ['required', 'date'],
            'ends_at'    => ['nullable', 'date', 'after_or_equal:starts_at'],
            'location'   => ['nullable', 'string', 'max:200'],
            'notes'      => ['nullable', 'string'],
            'status'     => ['required', 'in:programada,completada,cancelada,reprogramada'],
        ];
    }

    public function validationAttributes(): array
    {
        return [
            'studentId' => 'alumno',
            'title'     => 'título de la cita',
            'starts_at' => 'fecha y hora de inicio',
            'ends_at'   => 'fecha y hora de fin',
        ];
    }

    public function openModal(?int $studentId = null, ?string $defaultDate = null): void
    {
        $this->reset();
        $this->studentId  = $studentId;
        $this->status     = 'programada';
        $this->filterGroupId = '';

        if ($studentId) {
            $student = Student::with('currentEnrollment')->find($studentId);
            if ($student && $student->currentEnrollment?->group_id) {
                $this->filterGroupId = (string) $student->currentEnrollment->group_id;
            }
        }

        if ($defaultDate) {
            $baseTime = \Carbon\Carbon::parse($defaultDate);
            if ($baseTime->format('H:i') === '00:00') {
                $baseTime = $baseTime->setTime(9, 0);
            }
            $this->starts_at = $baseTime->format('Y-m-d\TH:i');
            $this->ends_at   = $baseTime->copy()->addHour()->format('Y-m-d\TH:i');
        } else {
            $this->starts_at  = now()->addDay()->setTime(9, 0)->format('Y-m-d\TH:i');
            $this->ends_at    = now()->addDay()->setTime(10, 0)->format('Y-m-d\TH:i');
        }

        $this->show = true;
    }

    public function openEditModal(int $appointmentId): void
    {
        $appt = Appointment::findOrFail($appointmentId);

        $this->reset();
        $this->isEditing        = true;
        $this->appointmentId    = $appt->id;
        $this->studentId        = $appt->student_id;
        $this->title            = $appt->title;
        $this->starts_at        = $appt->starts_at->format('Y-m-d\TH:i');
        $this->ends_at          = $appt->ends_at?->format('Y-m-d\TH:i') ?? '';
        $this->location         = $appt->location ?? '';
        $this->notes            = $appt->notes ?? '';
        $this->status           = $appt->status ?? 'programada';
        $this->required_attendees = $appt->required_attendees ?? [];

        $this->show = true;
    }

    public function addAttendee(): void
    {
        $trimmed = trim($this->attendee_input);
        if ($trimmed && !in_array($trimmed, $this->required_attendees)) {
            $this->required_attendees[] = $trimmed;
        }
        $this->attendee_input = '';
    }

    public function removeAttendee(int $index): void
    {
        array_splice($this->required_attendees, $index, 1);
        $this->required_attendees = array_values($this->required_attendees);
    }

    public function save(): void
    {
        $this->validate();
        $student = Student::with('studentFile')->findOrFail($this->studentId);

        $studentFile = $student->studentFile ?: $student->studentFile()->create([
            'file_type' => 'sin_diagnostico',
            'status' => 'activo',
            'opened_at' => now(),
            'created_by' => auth()->id(),
        ]);

        $data = [
            'student_file_id'   => $studentFile->id,
            'student_id'        => $student->id,
            'title'             => $this->title,
            'starts_at'         => $this->starts_at,
            'ends_at'           => $this->ends_at ?: null,
            'location'          => $this->location ?: null,
            'notes'             => $this->notes ?: null,
            'status'            => $this->status,
            'required_attendees'=> $this->required_attendees,
        ];

        if ($this->isEditing) {
            $appt = Appointment::findOrFail($this->appointmentId);
            $appt->update($data);
            $action  = 'updated';
            $label   = "Actualización de cita de {$student->full_name}";
            $message = 'Cita actualizada con éxito.';
        } else {
            $data['created_by'] = auth()->id();
            $appt = Appointment::create($data);
            $action  = 'created';
            $label   = "Agendación de cita para {$student->full_name}";
            $message = 'Cita agendada exitosamente.';
        }

        AuditLog::create([
            'user_id'         => auth()->id(),
            'action'          => $action,
            'auditable_type'  => Appointment::class,
            'auditable_id'    => $appt->id,
            'auditable_label' => $label,
            'ip_address'      => request()->ip(),
            'user_agent'      => request()->userAgent(),
            'created_at'      => now(),
        ]);

        $this->show = false;
        $this->dispatch('appointment-saved');
        $this->dispatch('toast', [
            'type'    => 'success',
            'title'   => 'Cita',
            'message' => $message,
        ]);
    }

    public function render()
    {
        $cycle = SchoolCycle::current();
        $groups = $cycle
            ? Group::where('school_cycle_id', $cycle->id)->orderBy('grade')->orderBy('section')->get()
            : collect();
        if ($groups->isEmpty()) {
            $groups = Group::orderBy('grade')->orderBy('section')->get();
        }

        $studentQuery = Student::query()
            ->with('currentEnrollment.group')
            ->orderBy('first_name')
            ->orderBy('last_name');

        if (!empty($this->filterGroupId)) {
            $studentQuery->whereHas('enrollments', function ($q) {
                $q->where('is_current', true)->where('group_id', (int) $this->filterGroupId);
            });
        }

        return view('livewire.appointments.appointment-modal', [
            'students' => $studentQuery->get(),
            'groups'   => $groups,
        ]);
    }
}
