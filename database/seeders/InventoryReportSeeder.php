<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Client;
use App\Models\InventoryItem;
use App\Models\InventoryReport;
use Illuminate\Database\Seeder;

class InventoryReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first branch (which has a client_id)
        $branch = Branch::withoutGlobalScopes()->first();

        if (! $branch) {
            $this->command->warn('No branch found. Please create one first.');

            return;
        }

        $client = Client::withoutGlobalScopes()->find($branch->client_id);

        if (! $client) {
            $this->command->warn('No client found. Please create one first.');

            return;
        }

        // Get all inventory items (from any client, we'll create reports for current client)
        $items = InventoryItem::withoutGlobalScopes()->get();

        if ($items->isEmpty()) {
            $this->command->warn('No inventory items found. Run InventoryItemSeeder first.');

            return;
        }

        $createdCount = 0;

        foreach ($items as $item) {
            // Check if inventory report already exists
            $exists = InventoryReport::where('inventory_item_id', $item->id)
                ->where('branch_id', $branch->id)
                ->whereNull('practitioner_id')
                ->exists();

            if ($exists) {
                continue;
            }

            // Create shared branch inventory with stock
            InventoryReport::create([
                'inventory_item_id' => $item->id,
                'status' => 'active',
                'client_id' => $client->id,
                'branch_id' => $branch->id,
                'practitioner_id' => null, // Shared inventory
                'consulting_room_id' => null,
                'quantity_on_hand' => rand(50, 200), // Random stock between 50-200
                'quantity_reserved' => 0,
                'lot_number' => null,
                'serial_number' => null,
                'expiration_date' => null,
                'reported_datetime' => now(),
                'reported_by_practitioner_id' => null,
            ]);

            $createdCount++;
        }

        $this->command->info("Created {$createdCount} inventory reports for branch: {$branch->name}");
    }
}
