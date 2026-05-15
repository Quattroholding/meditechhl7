<?php

namespace Database\Factories;

use App\Models\InventoryItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InventoryItem>
 */
class InventoryItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $itemTypes = ['supply', 'consumable', 'equipment', 'medication', 'device'];
        $itemType = fake()->randomElement($itemTypes);

        $items = [
            'supply' => [
                'Guantes desechables' => ['unit' => 'box', 'cost' => 15.00, 'price' => 25.00],
                'Cubrebocas' => ['unit' => 'box', 'cost' => 12.00, 'price' => 20.00],
                'Gasas estériles' => ['unit' => 'unit', 'cost' => 0.50, 'price' => 1.00],
                'Alcohol' => ['unit' => 'ml', 'cost' => 0.05, 'price' => 0.10],
                'Vendajes' => ['unit' => 'unit', 'cost' => 2.00, 'price' => 4.00],
            ],
            'consumable' => [
                'Jeringa 5ml' => ['unit' => 'unit', 'cost' => 0.80, 'price' => 2.00],
                'Jeringa 10ml' => ['unit' => 'unit', 'cost' => 1.00, 'price' => 2.50],
                'Agujas 21G' => ['unit' => 'unit', 'cost' => 0.30, 'price' => 0.75],
                'Catéter IV' => ['unit' => 'unit', 'cost' => 3.00, 'price' => 6.00],
            ],
            'equipment' => [
                'Termómetro digital' => ['unit' => 'unit', 'cost' => 15.00, 'price' => 30.00],
                'Estetoscopio' => ['unit' => 'unit', 'cost' => 50.00, 'price' => 100.00],
            ],
            'medication' => [
                'Paracetamol 500mg' => ['unit' => 'box', 'cost' => 5.00, 'price' => 10.00],
                'Ibuprofeno 400mg' => ['unit' => 'box', 'cost' => 6.00, 'price' => 12.00],
            ],
            'device' => [
                'Oxímetro de pulso' => ['unit' => 'unit', 'cost' => 40.00, 'price' => 80.00],
                'Tensiómetro digital' => ['unit' => 'unit', 'cost' => 60.00, 'price' => 120.00],
            ],
        ];

        $selectedItems = $items[$itemType];
        $itemName = fake()->randomElement(array_keys($selectedItems));
        $itemData = $selectedItems[$itemName];

        return [
            'status' => 'active',
            'category' => [[
                'coding' => [[
                    'system' => 'http://terminology.hl7.org/CodeSystem/supply-item-type',
                    'code' => $itemType,
                    'display' => ucfirst($itemType),
                ]],
                'text' => ucfirst($itemType),
            ]],
            'code' => [[
                'coding' => [[
                    'system' => 'urn:meditech:inventory',
                    'code' => fake()->unique()->numerify('ITEM-#####'),
                ]],
            ]],
            'name' => $itemName,
            'description' => fake()->sentence(),
            'sku' => fake()->unique()->numerify('SKU-####-####'),
            'barcode' => fake()->optional()->ean13(),
            'item_type' => $itemType,
            'base_cost' => $itemData['cost'],
            'base_price' => $itemData['price'],
            'currency' => 'USD',
            'unit_of_measure' => $itemData['unit'],
            'requires_prescription' => fake()->boolean(20),
            'track_by_lot' => fake()->boolean(30),
            'track_by_serial' => fake()->boolean(10),
            'expiration_tracking' => fake()->boolean(40),
            'reorder_point' => fake()->numberBetween(10, 50),
            'reorder_quantity' => fake()->numberBetween(50, 200),
            'characteristic' => null,
            'client_id' => \App\Models\Client::factory(),
        ];
    }
}
