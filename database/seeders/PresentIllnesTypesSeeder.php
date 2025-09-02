<?php

namespace Database\Seeders;

use App\Models\PresentIllnesType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PresentIllnesTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cl = DB::connection('scb')->table('consultation_lists')->get();

        foreach ($cl as $c) {
            PresentIllnesType::create(['type' => $c->type, 'value' => $c->value, 'value_esp' => $c->esp_value, 'order' => $c->order, 'scb_id' => $c->id]);
        }
    }
}
