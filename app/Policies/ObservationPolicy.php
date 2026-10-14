<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Observation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ObservationPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Observation $obs): bool
    {
        return $user->can('view', $obs->student);
    }

    public function create(User $user): bool
    {
        // Psicopedagogía y docentes pueden crear observaciones
        return in_array($user->role, [
            UserRole::Psicopedagogia,
            UserRole::DocenteTitular,
            UserRole::DocenteEspecial,
        ]);
    }

    public function update(User $user, Observation $obs): bool
    {
        if ($user->role === UserRole::Psicopedagogia) {
            return true;
        }

        // El docente solo edita sus propias observaciones
        return $obs->created_by === $user->id;
    }

    public function delete(User $user, Observation $obs): bool
    {
        if ($user->role === UserRole::Psicopedagogia) {
            return true;
        }

        return $obs->created_by === $user->id;
    }
}
