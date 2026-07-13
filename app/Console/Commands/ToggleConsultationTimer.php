<?php

namespace App\Console\Commands;

use App\Models\Client;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('consultation:toggle-timer {client_id?} {--enable} {--disable}')]
#[Description('Enable or disable consultation timer for a specific client')]
class ToggleConsultationTimer extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $clientId = $this->argument('client_id');

        // Si no se proporciona client_id, mostrar lista de clientes
        if (! $clientId) {
            $this->info('Available clients:');
            Client::all()->each(function ($client) {
                $status = $client->show_consultation_timer ? '✓ Enabled' : '✗ Disabled';
                $this->line("ID: {$client->id} - {$client->name} - Timer: {$status}");
            });

            return self::SUCCESS;
        }

        $client = Client::find($clientId);

        if (! $client) {
            $this->error("Client with ID {$clientId} not found.");

            return self::FAILURE;
        }

        // Determinar la acción
        if ($this->option('enable')) {
            $client->show_consultation_timer = true;
            $action = 'enabled';
        } elseif ($this->option('disable')) {
            $client->show_consultation_timer = false;
            $action = 'disabled';
        } else {
            // Toggle si no se especifica enable/disable
            $client->show_consultation_timer = ! $client->show_consultation_timer;
            $action = $client->show_consultation_timer ? 'enabled' : 'disabled';
        }

        $client->save();

        $this->info("Consultation timer {$action} for client: {$client->name}");

        return self::SUCCESS;
    }
}
