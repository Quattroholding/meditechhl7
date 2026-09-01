<?php

namespace App\Console\Commands;

use App\Models\Client;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('clients:virtual-appointments {client_id?} {--enable} {--disable}')]
#[Description('Enable or disable virtual appointments for a client')]
class EnableVirtualAppointments extends Command
{
    public function handle()
    {
        $clientId = $this->argument('client_id');

        if (! $clientId) {
            // Listar clientes disponibles
            $clients = Client::all();
            if ($clients->isEmpty()) {
                $this->info('No clients found.');

                return;
            }

            $this->info('Available clients:');
            foreach ($clients as $client) {
                $status = $client->enable_virtual_appointments ? '✓ Enabled' : '✗ Disabled';
                $this->line("  ID: {$client->id} | Name: {$client->name} | Virtual Appointments: {$status}");
            }
            $this->line('');
            $this->line('Usage: php artisan clients:virtual-appointments <client_id> [--enable|--disable]');

            return;
        }

        $client = Client::find($clientId);
        if (! $client) {
            $this->error("Client with ID {$clientId} not found.");

            return;
        }

        if ($this->option('enable')) {
            $client->update(['enable_virtual_appointments' => true]);
            $this->info("✓ Virtual appointments enabled for {$client->name}");
        } elseif ($this->option('disable')) {
            $client->update(['enable_virtual_appointments' => false]);
            $this->info("✗ Virtual appointments disabled for {$client->name}");
        } else {
            $status = $client->enable_virtual_appointments ? 'enabled' : 'disabled';
            $this->info("Virtual appointments are currently {$status} for {$client->name}");
            $this->line('Use --enable or --disable to change.');
        }
    }
}
