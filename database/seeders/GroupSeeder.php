<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Group;
use App\Models\SchoolCycle;
use App\Models\User;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    public function run(): void
    {
        $cycle2526 = SchoolCycle::where('name', '2025-2026')->first();
        $cycle2627 = SchoolCycle::where('name', '2026-2027')->first();

        $teachers = [
            'docente.4a@felix.edu.mx',
            'docente.5b@felix.edu.mx',
            'docente.6a@felix.edu.mx',
        ];

        $espanol = User::where('email', 'espanol@felix.edu.mx')->first();
        $ingles  = User::where('email', 'ingles@felix.edu.mx')->first();

        // Groups for 2025-2026
        foreach ([$cycle2526, $cycle2627] as $cycle) {
            $groupDefs = [
                [4, 'A', 'docente.4a@felix.edu.mx'],
                [4, 'B', 'docente.4a@felix.edu.mx'],
                [5, 'A', 'docente.5b@felix.edu.mx'],
                [5, 'B', 'docente.5b@felix.edu.mx'],
                [6, 'A', 'docente.6a@felix.edu.mx'],
                [6, 'B', 'docente.6a@felix.edu.mx'],
            ];

            foreach ($groupDefs as [$grade, $section, $teacherEmail]) {
                $group = Group::create([
                    'school_cycle_id' => $cycle->id,
                    'grade'           => $grade,
                    'section'         => $section,
                ]);

                $teacher = User::where('email', $teacherEmail)->first();
                if ($teacher) {
                    $group->teachers()->attach($teacher->id, ['assignment_type' => 'titular']);
                }

                // Attach special teachers to all groups
                if ($espanol) {
                    $group->teachers()->attach($espanol->id, ['assignment_type' => 'espanol']);
                }
                if ($ingles) {
                    $group->teachers()->attach($ingles->id, ['assignment_type' => 'ingles']);
                }
            }
        }
    }
}
