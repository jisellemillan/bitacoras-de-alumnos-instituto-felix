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
        // Única cuenta administrativa institucional
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
    }
}
