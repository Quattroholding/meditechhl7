<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\ClientPreference;
use Illuminate\Database\Seeder;

class ClientLanguagePreferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Setting default language preference (Spanish) for all existing clients...');

        $clients = Client::all();
        $count = 0;

        foreach ($clients as $client) {
            try {
                ClientPreference::setLanguage($client->id, 'es');
                $count++;
            } catch (\Exception $e) {
                $this->command->error("Failed to set language for client {$client->id}: {$e->getMessage()}");
            }
        }

        $this->command->info("Successfully set language preference for {$count} clients.");
    }
}
