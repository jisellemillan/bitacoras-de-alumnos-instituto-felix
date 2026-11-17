<?php

namespace App\Livewire\Appointments;

use App\Models\Appointment;
use App\Models\AuditLog;
use Carbon\Carbon;
use Livewire\Component;

class CalendarPage extends Component
{
    public string $currentMonth;
    public string $statusFilter = '';

    protected $listeners = [
        'appointment-saved' => '$refresh',
    ];

    protected $queryString = [
        'currentMonth' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function mount(): void
    {
        if (empty($this->currentMonth)) {
            $this->currentMonth = now()->format('Y-m');
        }
    }

    public function previousMonth(): void
    {
        $this->currentMonth = Carbon::createFromFormat('Y-m', $this->currentMonth)->subMonth()->format('Y-m');
    }

    public function nextMonth(): void
    {
        $this->currentMonth = Carbon::createFromFormat('Y-m', $this->currentMonth)->addMonth()->format('Y-m');
    }

    public function today(): void
    {
        $this->currentMonth = now()->format('Y-m');
    }

    public function updateStatus(int $appointmentId, string $status): void
    {
        $appointment = Appointment::with('student')->findOrFail($appointmentId);
        $appointment->update(['status' => $status]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'updated',
            'auditable_type' => Appointment::class,
            'auditable_id' => $appointment->id,
            'auditable_label' => "Estado de cita cambiado a '{$status}' para {$appointment->student->full_name}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'title' => 'Cita Actualizada',
            'message' => 'El estado de la cita ha sido actualizado.',
        ]);
    }

    public function deleteAppointment(int $appointmentId): void
    {
        $appointment = Appointment::with('student')->findOrFail($appointmentId);
        $appointment->delete();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'deleted',
            'auditable_type' => Appointment::class,
            'auditable_id' => $appointment->id,
            'auditable_label' => "Cita eliminada para {$appointment->student->full_name}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'title' => 'Cita Eliminada',
            'message' => 'La cita ha sido eliminada del calendario.',
        ]);
    }

   public function render()
    {
        $date = \Carbon\Carbon::createFromFormat('Y-m', $this->currentMonth);
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();
        $user = auth()->user();

        // Consultas base
        $query = Appointment::with(['student.currentEnrollment.group', 'author'])
            ->whereBetween('starts_at', [$startOfMonth->copy()->startOfWeek(), $endOfMonth->copy()->endOfWeek()])
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter));

        $upcomingQuery = Appointment::with(['student.currentEnrollment.group'])
            ->where('starts_at', '>=', now());

        // RESTRICCIÓN PARA DOCENTES (Ver citas solo de alumnos permitidos)
       // RESTRICCIÓN PARA DOCENTES (Ver citas solo de alumnos permitidos y grupos nativos)
        if ($user->hasRole(\App\Enums\UserRole::DocenteTitular) || $user->hasRole(\App\Enums\UserRole::DocenteEspecial)) {
            
            $nativeGroupIds = \Illuminate\Support\Facades\DB::table('grupo_usuario')->where('user_id', $user->id)->pluck('group_id');
            $grantedGroupIds = \App\Models\AccessGrant::where('user_id', $user->id)->whereNotNull('group_id')->pluck('group_id');
            
            $allowedGroupIds = $nativeGroupIds->merge($grantedGroupIds)->unique();
            $allowedStudentIds = \App\Models\AccessGrant::where('user_id', $user->id)->whereNotNull('student_id')->pluck('student_id');

            $studentFilter = function($q) use ($allowedGroupIds, $allowedStudentIds) {
                $q->whereIn('id', $allowedStudentIds)
                  ->orWhereHas('enrollments', function($eq) use ($allowedGroupIds) {
                      $eq->where('is_current', true)->whereIn('group_id', $allowedGroupIds);
                  });
            };

            $query->whereHas('student', $studentFilter);
            $upcomingQuery->whereHas('student', $studentFilter);
        }

        $appointments = $query->orderBy('starts_at')->get();
        $upcomingAppointments = $upcomingQuery->orderBy('starts_at')->take(5)->get();

        // Estructura de cuadrícula de calendario
        $days = [];
        $currentDay = $startOfMonth->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
        $calendarEnd = $endOfMonth->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);

        while ($currentDay <= $calendarEnd) {
            $dayKey = $currentDay->format('Y-m-d');
            $days[] = [
                'date' => $currentDay->copy(),
                'isCurrentMonth' => $currentDay->month === $date->month,
                'isToday' => $currentDay->isToday(),
                'appointments' => $appointments->filter(fn ($a) => $a->starts_at->format('Y-m-d') === $dayKey),
            ];
            $currentDay->addDay();
        }

        return view('livewire.appointments.calendar-page', [
            'days' => $days,
            'monthName' => $date->translatedFormat('F Y'),
            'upcomingAppointments' => $upcomingAppointments,
        ]);
    }
}
