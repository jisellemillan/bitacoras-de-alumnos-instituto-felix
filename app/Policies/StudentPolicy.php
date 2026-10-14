<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Student;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StudentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        // Administrador técnico o cualquier usuario con permiso de la sección alumnos
        if ($user->isAdminTecnico()) {
            return true;
        }

        return $user->canAccessPsychContent() && $user->hasSectionPermission('alumnos');
    }

    public function view(User $user, Student $student): bool
    {
        if ($user->isAdminTecnico()) {
            return true;
        }

        if (! $user->canAccessPsychContent()) {
            return false;
        }

        // Psicopedagogía y Dirección tienen acceso global
        if ($user->role === UserRole::Psicopedagogia || $user->role === UserRole::Direccion) {
            return true;
        }

        // Docente titular o especial: solo si el alumno pertenece a uno de sus grupos asignados
        $userGroupIds = $user->groups()->pluck('grupos.id')->toArray();
        $studentGroupIds = $student->enrollments()->where('is_current', true)->pluck('group_id')->toArray();

        if (! empty(array_intersect($userGroupIds, $studentGroupIds))) {
            return true;
        }

        // O si tiene una autorización de acceso explícita aprobada y vigente
        return $user->accessRequests()
            ->where('student_id', $student->id)
            ->where('status', 'aprobado')
            ->whereHas('grant', fn ($q) => $q->where('is_active', true)->where(fn ($sub) => $sub->whereNull('expires_at')->orWhere('expires_at', '>', now())))
            ->exists();
    }

    public function create(User $user): bool
    {
        return $user->role === UserRole::Psicopedagogia || $user->isAdminTecnico();
    }

    public function update(User $user, Student $student): bool
    {
        return $user->role === UserRole::Psicopedagogia || $user->isAdminTecnico();
    }

    public function delete(User $user, Student $student): bool
    {
        return $user->role === UserRole::Psicopedagogia || $user->isAdminTecnico();
    }
}
