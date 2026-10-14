<?php

namespace Database\Seeders;

use App\Enums\Grade;
use App\Enums\IntakeRoute;
use App\Enums\NeedArea;
use App\Enums\StudentStatus;
use App\Enums\UserRole;
use App\Models\AccessGrant;
use App\Models\AccessRequest;
use App\Models\Agreement;
use App\Models\Appointment;
use App\Models\Enrollment;
use App\Models\Group;
use App\Models\Interview;
use App\Models\Observation;
use App\Models\SchoolCycle;
use App\Models\Student;
use App\Models\StudentFile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SchoolCycleSeeder::class,
            UserSeeder::class,
            GroupSeeder::class,
            // StudentSeeder::class, // Omitido por defecto: permite iniciar el sistema con 0 alumnos y capturarlos manualmente
        ]);
    }
}
