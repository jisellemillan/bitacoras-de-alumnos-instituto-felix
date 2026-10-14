<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\StudentFile;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StudentFilePolicy
{
    use HandlesAuthorization;

    public function view(User $user, StudentFile $file): bool
    {
        return $user->can('view', $file->student);
    }

    public function update(User $user, StudentFile $file): bool
    {
        return $user->role === UserRole::Psicopedagogia;
    }

    public function delete(User $user, StudentFile $file): bool
    {
        return $user->role === UserRole::Psicopedagogia;
    }
}
