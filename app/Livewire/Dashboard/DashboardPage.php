<?php

namespace App\Livewire\Dashboard;

use App\Models\Student;
use App\Models\Group;
use Livewire\Component;

class DashboardPage extends Component
{
    public function render()
    {
        $user = auth()->user();

        $totalAlumnos = $user->hasSectionPermission('alumnos')
            ? Student::count()
            : null;

        $totalGrupos = $user->hasSectionPermission('configuracion')
            ? Group::count()
            : null;

        return view('livewire.dashboard.dashboard-page', [
            'totalAlumnos' => $totalAlumnos,
            'totalGrupos'  => $totalGrupos,
        ]);
    }
}
