<?php

namespace App\Livewire\AccessRequests;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\AccessRequest;
use App\Models\AccessGrant;
use App\Models\Group;
use App\Models\Student;

class AccessRequestsIndex extends Component
{
    public $esAprobador = false;
    
    // Formulario
    public $tipoAcceso = 'grupo';
    public $objetivoId = '';
    public $motivo = '';

    public function mount()
    {
        $user = Auth::user();
        $this->esAprobador = $user->isPsicopedagogia() || $user->isAdminTecnico();
    }

    public function enviarSolicitud()
    {
        $this->validate([
            'objetivoId' => 'required',
            'motivo' => 'required|string|max:255',
        ]);

        AccessRequest::create([
            'requester_id' => Auth::id(),
            'student_id' => $this->tipoAcceso === 'alumno' ? $this->objetivoId : null,
            'group_id' => $this->tipoAcceso === 'grupo' ? $this->objetivoId : null,
            'reason' => $this->motivo,
            'status' => 'pendiente',
        ]);

        $this->reset(['objetivoId', 'motivo']);
        session()->flash('message', 'Solicitud enviada correctamente.');
    }

    public function procesarSolicitud($solicitudId, $estado)
    {
        $solicitud = AccessRequest::findOrFail($solicitudId);
        
        $solicitud->update([
            'status' => $estado,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        if ($estado === 'aprobado') {
            AccessGrant::create([
                'access_request_id' => $solicitud->id,
                'user_id' => $solicitud->requester_id,
                'student_id' => $solicitud->student_id,
                'group_id' => $solicitud->group_id,
                'is_active' => true,
            ]);
        }
    }

    public function render()
    {
        return view('livewire.access-requests.access-requests-index', [
            'gruposDisponibles' => Group::with('schoolCycle')->get(),
            'alumnosDisponibles' => Student::active()->get(),
            'misSolicitudes' => AccessRequest::with(['student', 'group.schoolCycle'])
                ->where('requester_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->get(),
            'solicitudesPendientes' => AccessRequest::with(['requester', 'student', 'group.schoolCycle'])
                ->pending()
                ->get(),
        ]);
    }
}