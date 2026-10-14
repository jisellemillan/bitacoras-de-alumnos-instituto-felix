<?php

namespace Database\Seeders;

use App\Models\SchoolCycle;
use Illuminate\Database\Seeder;

class SchoolCycleSeeder extends Seeder
{
    public function run(): void
    {
        SchoolCycle::create([
            'name'      => '2025-2026',
            'starts_at' => '2025-08-18',
            'ends_at'   => '2026-06-12',
            'is_active' => false,
        ]);

        SchoolCycle::create([
            'name'      => '2026-2027',
            'starts_at' => '2026-08-17',
            'ends_at'   => '2027-06-11',
            'is_active' => true,
        ]);
    }
}
