<?php

namespace Database\Seeders;

use App\Models\MedicalHistory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MedicalHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      MedicalHistory::factory()
            ->count(3)
            ->state(['category' => 'social-history'])
            ->create();

        MedicalHistory::factory()
            ->count(3)
            ->state(['category' => 'family-history'])
            ->create();

        \App\Models\MedicalHistory::factory()
            ->count(3)
            ->state(['category' => 'allergy'])
            ->create();
    }
}
