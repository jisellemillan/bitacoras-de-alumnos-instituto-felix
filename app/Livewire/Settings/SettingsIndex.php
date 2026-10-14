<?php

namespace App\Livewire\Settings;

use App\Models\Group;
use App\Models\SchoolCycle;
use App\Models\Student;
use App\Models\User;
use Livewire\Component;

class SettingsIndex extends Component
{
    public function render()
    {
        $currentCycle = SchoolCycle::current();
        $totalGroups = Group::count();
        $totalStudents = Student::count();
        $totalUsers = User::count();

        return view('livewire.settings.settings-index', [
            'currentCycle' => $currentCycle,
            'totalGroups' => $totalGroups,
            'totalStudents' => $totalStudents,
            'totalUsers' => $totalUsers,
        ])->layout('layouts.app', ['title' => 'Configuración Institucional']);
    }
}
