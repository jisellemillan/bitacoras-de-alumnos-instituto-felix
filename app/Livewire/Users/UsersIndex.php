<?php

namespace App\Livewire\Users;

use App\Enums\UserRole;
use App\Livewire\Forms\UserForm;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class UsersIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $roleFilter = '';

    public bool $showModal = false;
    public bool $showDetailModal = false;
    public bool $isEditing = false;
    public ?User $viewingUser = null;

    public UserForm $form;

    // Catálogo de secciones del sistema disponibles para asignación
    public array $availableSections = [
        'alumnos' => [
            'label' => 'Alumnos y Expedientes',
            'desc' => 'Directorio escolar, captura y consulta de expedientes digitales',
        ],
        'acuerdos' => [
            'label' => 'Tablero de Acuerdos',
            'desc' => 'Seguimiento de compromisos, fechas límite y cambio de estados',
        ],
        'calendario' => [
            'label' => 'Calendario y Citas',
            'desc' => 'Programación y seguimiento mensual de citas de atención',
        ],
        'reportes' => [
            'label' => 'Centro de Reportes y PDF',
            'desc' => 'Exportación oficial de listas de casos, estadísticas e historiales',
        ],
        'solicitudes_acceso' => [
            'label' => 'Solicitudes de Acceso',
            'desc' => 'Módulo de autorización y petición de consulta a expedientes',
        ],
        'usuarios' => [
            'label' => 'Gestión de Usuarios',
            'desc' => 'Administración de cuentas, contraseñas y asignación de permisos',
        ],
        'configuracion' => [
            'label' => 'Configuración Institucional',
            'desc' => 'Ciclos escolares, grados, grupos y catálogos generales',
        ],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->form->reset();
        $this->isEditing = false;
        $this->form->role = 'docente_titular';
        $this->form->is_active = true;
        // Por defecto, asignar secciones básicas
        $this->form->section_permissions = ['alumnos', 'acuerdos'];
        $this->showModal = true;
    }

    public function openEditModal(int $userId): void
    {
        $user = User::findOrFail($userId);
        $this->form->reset();
        $this->isEditing = true;
        $this->form->id = $user->id;
        $this->form->first_name = $user->first_name ?? '';
        $this->form->last_name = $user->last_name ?? '';
        $this->form->email = $user->email;
        $this->form->role = $user->role->value;
        $this->form->is_active = $user->is_active;
        $this->form->section_permissions = $user->section_permissions ?? [];
        $this->form->password = '';
        $this->form->password_confirmation = '';

        $this->showModal = true;
    }

    public function openDetailModal(int $userId): void
    {
        $this->viewingUser = User::findOrFail($userId);
        $this->showDetailModal = true;
    }

    public function save(): void
    {
        $this->form->validate();

        $fullName = trim($this->form->first_name . ' ' . $this->form->last_name);

        if ($this->isEditing) {
            $user = User::findOrFail($this->form->id);

            $updateData = [
                'name' => $fullName,
                'first_name' => trim($this->form->first_name),
                'last_name' => trim($this->form->last_name),
                'email' => strtolower(trim($this->form->email)),
                'role' => $this->form->role,
                'section_permissions' => $this->form->section_permissions,
                'is_active' => $this->form->is_active,
            ];

            if (!empty($this->form->password)) {
                $updateData['password'] = Hash::make($this->form->password);
            }

            $user->update($updateData);

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'updated',
                'auditable_type' => User::class,
                'auditable_id' => $user->id,
                'auditable_label' => "Actualización de usuario: {$user->name}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
            ]);

            $message = "Usuario {$user->name} actualizado correctamente.";
        } else {
            $user = User::create([
                'name' => $fullName,
                'first_name' => trim($this->form->first_name),
                'last_name' => trim($this->form->last_name),
                'email' => strtolower(trim($this->form->email)),
                'password' => Hash::make($this->form->password),
                'role' => $this->form->role,
                'section_permissions' => $this->form->section_permissions,
                'is_active' => $this->form->is_active,
            ]);

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'created',
                'auditable_type' => User::class,
                'auditable_id' => $user->id,
                'auditable_label' => "Creación de usuario: {$user->name} ({$user->email})",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
            ]);

            $message = "Usuario {$user->name} creado exitosamente con sus permisos asignados.";
        }

        $this->showModal = false;
        $this->dispatch('toast', [
            'type' => 'success',
            'title' => 'Gestión de Usuarios',
            'message' => $message,
        ]);
    }

    public function deleteUser(int $userId): void
    {
        // Regla de seguridad: El administrador no puede eliminarse a sí mismo
        if ($userId === auth()->id()) {
            $this->dispatch('toast', [
                'type' => 'error',
                'title' => 'Acción denegada',
                'message' => 'No es posible eliminar tu propia cuenta de administrador activa.',
            ]);
            return;
        }

        // Regla: No eliminar si es el único administrador
        $user = User::findOrFail($userId);
        if ($user->isAdminTecnico() && User::where('role', UserRole::AdminTecnico->value)->count() <= 1) {
            $this->dispatch('toast', [
                'type' => 'error',
                'title' => 'Acción denegada',
                'message' => 'El sistema debe conservar al menos una cuenta de administrador activo.',
            ]);
            return;
        }

        $userName = $user->name;
        $user->delete();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'deleted',
            'auditable_type' => User::class,
            'auditable_id' => $userId,
            'auditable_label' => "Baja de usuario: {$userName}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);

        $this->dispatch('toast', [
            'type' => 'warning',
            'title' => 'Usuario dado de baja',
            'message' => "Se ha desactivado y dado de baja a {$userName}.",
        ]);
    }

    public function render()
    {
        $query = User::query()->latest();

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            });
        }

        if (!empty($this->roleFilter)) {
            $query->where('role', $this->roleFilter);
        }

        $users = $query->paginate(10);

        return view('livewire.users.users-index', [
            'users' => $users,
            'roles' => UserRole::cases(),
        ]);
    }
}
