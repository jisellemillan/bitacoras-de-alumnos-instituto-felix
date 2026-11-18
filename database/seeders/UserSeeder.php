<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Administrador técnico del sistema
        User::updateOrCreate(
            ['email' => 'admin@felix.edu.mx'],
            [
                'name'       => 'Administrador General',
                'first_name' => 'Administrador',
                'last_name'  => 'General',
                'password'   => Hash::make('admin1234'),
                'role'       => UserRole::AdminTecnico,
                'is_active'  => true,
            ]
        );

        // Cuenta inicial de Psicopedagogía (puede acceder a grupos, alumnos y expedientes)
        User::updateOrCreate(
            ['email' => 'psicopedagogia@felix.edu.mx'],
            [
                'name'                => 'Dept. Psicopedagógico',
                'first_name'          => 'Departamento',
                'last_name'           => 'Psicopedagógico',
                'password'            => Hash::make('psico1234'),
                'role'                => UserRole::Psicopedagogia,
                'is_active'           => true,
                'section_permissions' => ['alumnos', 'acuerdos', 'calendario', 'reportes'],
            ]
        );
    }
}
