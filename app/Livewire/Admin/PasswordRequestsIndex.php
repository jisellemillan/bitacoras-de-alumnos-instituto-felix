<?php

namespace App\Livewire\Admin;

use App\Models\PasswordRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class PasswordRequestsIndex extends Component
{
    use WithPagination;

    public string $filtroEstado = '';
    public ?int $procesandoId = null;
    public ?string $contrasenaNueva = null;
    public bool $mostrarContrasena = false;

    public function updatingFiltroEstado(): void
    {
        $this->resetPage();
    }

    public function procesarSolicitud(int $id): void
    {
        $solicitud = PasswordRequest::findOrFail($id);

        // Evitar reprocesar
        if ($solicitud->isAtendida()) {
            session()->flash('error', 'Esta solicitud ya fue atendida anteriormente.');
            return;
        }

        $user = $solicitud->user;

        if (!$user) {
            session()->flash('error', 'No se encontró el usuario asociado a esta solicitud.');
            return;
        }

        // Generar contraseña segura aleatoria (12 caracteres)
        $nuevaContrasena = Str::password(12, letters: true, numbers: true, symbols: false, spaces: false);

        // Actualizar la contraseña del usuario (se hashea automáticamente por el cast)
        $user->password = Hash::make($nuevaContrasena);
        $user->save();

        // Marcar la solicitud como atendida
        $solicitud->update([
            'estado'      => 'atendida',
            'atendida_por' => auth()->id(),
            'atendida_en' => now(),
        ]);

        // Guardar la contraseña generada para mostrarla UNA SOLA VEZ
        $this->procesandoId = $id;
        $this->contrasenaNueva = $nuevaContrasena;
        $this->mostrarContrasena = true;
    }

    public function cerrarModal(): void
    {
        $this->procesandoId = null;
        $this->contrasenaNueva = null;
        $this->mostrarContrasena = false;
    }

    public function render()
    {
        $query = PasswordRequest::with(['user', 'attendedBy'])
            ->orderByRaw("CASE WHEN estado = 'pendiente' THEN 0 ELSE 1 END")
            ->orderByDesc('created_at');

        if ($this->filtroEstado) {
            $query->where('estado', $this->filtroEstado);
        }

        $solicitudes = $query->paginate(20);
        $totalPendientes = PasswordRequest::where('estado', 'pendiente')->count();

        return view('livewire.admin.password-requests-index', [
            'solicitudes'     => $solicitudes,
            'totalPendientes' => $totalPendientes,
        ])->layout('layouts.app', ['title' => 'Solicitudes de Contraseña']);
    }
}
