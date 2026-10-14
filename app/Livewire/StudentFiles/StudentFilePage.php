<?php

namespace App\Livewire\StudentFiles;

use App\Models\Agreement;
use App\Models\Appointment;
use App\Models\AuditLog;
use App\Models\Document;
use App\Models\Interview;
use App\Models\Observation;
use App\Models\Student;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class StudentFilePage extends Component
{
    use AuthorizesRequests;

    public Student $student;
    public string $activeTab = 'resumen';
    public ?int $fromGroupId = null;

    protected $queryString = [
        'activeTab' => ['as' => 'tab'],
        'fromGroupId' => ['as' => 'from_group', 'except' => null],
    ];

    protected $listeners = [
        'observation-saved'   => 'refreshStudent',
        'interview-saved'     => 'refreshStudent',
        'agreement-saved'     => 'refreshStudent',
        'appointment-saved'   => 'refreshStudent',
        'document-saved'      => 'refreshStudent',
    ];

    public function mount(Student $student): void
    {
        $this->authorize('view', $student);
        $this->fromGroupId = request()->query('from_group') ? (int) request()->query('from_group') : null;
        $this->loadStudent($student->id);

        AuditLog::create([
            'user_id'         => auth()->id(),
            'action'          => 'viewed',
            'auditable_type'  => Student::class,
            'auditable_id'    => $student->id,
            'auditable_label' => "Consulta de expediente: {$student->full_name}",
            'ip_address'      => request()->ip(),
            'user_agent'      => request()->userAgent(),
            'created_at'      => now(),
        ]);
    }

    private function loadStudent(int $id): void
    {
        $this->student = Student::with([
            'studentFile',
            'currentEnrollment.group.schoolCycle',
            'enrollments.group.schoolCycle',
            'observations.author',
            'observations.signatures',
            'interviews.author',
            'agreements.author',
            'appointments',
            'documents.uploader',
        ])->findOrFail($id);
    }

    public function refreshStudent(): void
    {
        $this->loadStudent($this->student->id);
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    // ─── OBSERVACIONES ────────────────────────────────────────────────────────

    public function deleteObservation(int $id): void
    {
        $obs = Observation::findOrFail($id);
        $this->authorize('delete', $obs);

        AuditLog::create([
            'user_id'         => auth()->id(),
            'action'          => 'deleted',
            'auditable_type'  => Observation::class,
            'auditable_id'    => $obs->id,
            'auditable_label' => "Eliminación de observación de {$this->student->full_name}",
            'ip_address'      => request()->ip(),
            'user_agent'      => request()->userAgent(),
            'created_at'      => now(),
        ]);

        $obs->delete();
        $this->refreshStudent();
        $this->dispatch('toast', ['type' => 'success', 'title' => 'Eliminado', 'message' => 'Observación eliminada.']);
    }

    // ─── ENTREVISTAS ───────────────────────────────────────────────────────────

    public function deleteInterview(int $id): void
    {
        $interview = Interview::findOrFail($id);

        AuditLog::create([
            'user_id'         => auth()->id(),
            'action'          => 'deleted',
            'auditable_type'  => Interview::class,
            'auditable_id'    => $interview->id,
            'auditable_label' => "Eliminación de entrevista de {$this->student->full_name}",
            'ip_address'      => request()->ip(),
            'user_agent'      => request()->userAgent(),
            'created_at'      => now(),
        ]);

        $interview->delete();
        $this->refreshStudent();
        $this->dispatch('toast', ['type' => 'success', 'title' => 'Eliminado', 'message' => 'Entrevista eliminada.']);
    }

    // ─── ACUERDOS ─────────────────────────────────────────────────────────────

    public function deleteAgreement(int $id): void
    {
        $agreement = Agreement::findOrFail($id);

        AuditLog::create([
            'user_id'         => auth()->id(),
            'action'          => 'deleted',
            'auditable_type'  => Agreement::class,
            'auditable_id'    => $agreement->id,
            'auditable_label' => "Eliminación de acuerdo de {$this->student->full_name}",
            'ip_address'      => request()->ip(),
            'user_agent'      => request()->userAgent(),
            'created_at'      => now(),
        ]);

        $agreement->delete();
        $this->refreshStudent();
        $this->dispatch('toast', ['type' => 'success', 'title' => 'Eliminado', 'message' => 'Acuerdo eliminado.']);
    }

    public function changeAgreementStatus(int $id, string $status): void
    {
        $agreement = Agreement::findOrFail($id);
        $agreement->update(['status' => $status]);
        $this->refreshStudent();
        $this->dispatch('toast', ['type' => 'success', 'title' => 'Actualizado', 'message' => 'Estatus del acuerdo actualizado.']);
    }

    // ─── CITAS ─────────────────────────────────────────────────────────────────

    public function deleteAppointment(int $id): void
    {
        $appt = Appointment::findOrFail($id);

        AuditLog::create([
            'user_id'         => auth()->id(),
            'action'          => 'deleted',
            'auditable_type'  => Appointment::class,
            'auditable_id'    => $appt->id,
            'auditable_label' => "Cancelación de cita de {$this->student->full_name}",
            'ip_address'      => request()->ip(),
            'user_agent'      => request()->userAgent(),
            'created_at'      => now(),
        ]);

        $appt->delete();
        $this->refreshStudent();
        $this->dispatch('toast', ['type' => 'success', 'title' => 'Eliminado', 'message' => 'Cita eliminada.']);
    }

    // ─── DOCUMENTOS ───────────────────────────────────────────────────────────

    public function deleteDocument(int $id): void
    {
        $doc = Document::findOrFail($id);

        if ($doc->file_path && Storage::disk('private')->exists($doc->file_path)) {
            Storage::disk('private')->delete($doc->file_path);
        }

        AuditLog::create([
            'user_id'         => auth()->id(),
            'action'          => 'deleted',
            'auditable_type'  => Document::class,
            'auditable_id'    => $doc->id,
            'auditable_label' => "Eliminación de documento de {$this->student->full_name}",
            'ip_address'      => request()->ip(),
            'user_agent'      => request()->userAgent(),
            'created_at'      => now(),
        ]);

        $doc->delete();
        $this->refreshStudent();
        $this->dispatch('toast', ['type' => 'success', 'title' => 'Eliminado', 'message' => 'Documento eliminado.']);
    }

    public function render()
    {
        return view('livewire.student-files.student-file-page', [
            'file'        => $this->student->studentFile,
            'observations'=> $this->student->observations,
            'interviews'  => $this->student->interviews,
            'agreements'  => $this->student->agreements,
            'appointments'=> $this->student->appointments,
            'documents'   => $this->student->documents,
            'enrollments' => $this->student->enrollments,
        ]);
    }
}
