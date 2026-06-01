<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Client;
use App\Models\InventoryItem;
use Illuminate\Database\Seeder;

class InventoryItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first branch (which has a client_id)
        $branch = Branch::withoutGlobalScopes()->whereId(2)->first();

        if (! $branch) {
            $this->command->warn('No branch found. Creating inventory for first client.');
            $client = Client::withoutGlobalScopes()->first();
        } else {
            $client = Client::withoutGlobalScopes()->find($branch->client_id);
        }

        if (! $client) {
            $this->command->warn('No client found. Please create a client first.');

            return;
        }

        $items = [
            // Guantes y protección
            [
                'name' => 'Guantes desechables de látex (caja 100 pcs)',
                'description' => 'Guantes desechables de látex, ambidiestros, talla M',
                'sku' => 'GLOVE-LAT-M-100',
                'barcode' => '7501234567890',
                'item_type' => 'supply',
                'base_cost' => 15.00,
                'base_price' => 25.00,
                'unit_of_measure' => 'box',
                'reorder_point' => 5,
                'reorder_quantity' => 20,
            ],
            [
                'name' => 'Cubrebocas tricapa (caja 50 pcs)',
                'description' => 'Cubrebocas desechable tricapa con filtro',
                'sku' => 'MASK-3L-50',
                'barcode' => '7501234567891',
                'item_type' => 'supply',
                'base_cost' => 12.00,
                'base_price' => 20.00,
                'unit_of_measure' => 'box',
                'reorder_point' => 10,
                'reorder_quantity' => 30,
            ],

            // Material de curación
            [
                'name' => 'Gasas estériles 10x10 cm',
                'description' => 'Gasas estériles de algodón, paquete individual',
                'sku' => 'GAUZE-10X10',
                'barcode' => '7501234567892',
                'item_type' => 'supply',
                'base_cost' => 0.50,
                'base_price' => 1.00,
                'unit_of_measure' => 'unit',
                'reorder_point' => 100,
                'reorder_quantity' => 500,
            ],
            [
                'name' => 'Alcohol etílico 70% (1 litro)',
                'description' => 'Alcohol etílico al 70% para desinfección',
                'sku' => 'ALC-70-1L',
                'barcode' => '7501234567893',
                'item_type' => 'supply',
                'base_cost' => 8.00,
                'base_price' => 15.00,
                'unit_of_measure' => 'unit',
                'expiration_tracking' => true,
                'reorder_point' => 10,
                'reorder_quantity' => 30,
            ],
            [
                'name' => 'Vendaje elástico 10cm x 4.5m',
                'description' => 'Vendaje elástico autoadherible',
                'sku' => 'BAND-10CM',
                'barcode' => '7501234567894',
                'item_type' => 'supply',
                'base_cost' => 2.00,
                'base_price' => 4.00,
                'unit_of_measure' => 'unit',
                'reorder_point' => 50,
                'reorder_quantity' => 150,
            ],

            // Jeringas y agujas
            [
                'name' => 'Jeringa desechable 5ml con aguja',
                'description' => 'Jeringa desechable estéril de 5ml con aguja 21G',
                'sku' => 'SYR-5ML-21G',
                'barcode' => '7501234567895',
                'item_type' => 'consumable',
                'base_cost' => 0.80,
                'base_price' => 2.00,
                'unit_of_measure' => 'unit',
                'reorder_point' => 200,
                'reorder_quantity' => 500,
            ],
            [
                'name' => 'Jeringa desechable 10ml con aguja',
                'description' => 'Jeringa desechable estéril de 10ml con aguja 21G',
                'sku' => 'SYR-10ML-21G',
                'barcode' => '7501234567896',
                'item_type' => 'consumable',
                'base_cost' => 1.00,
                'base_price' => 2.50,
                'unit_of_measure' => 'unit',
                'reorder_point' => 200,
                'reorder_quantity' => 500,
            ],
            [
                'name' => 'Agujas desechables 21G x 100',
                'description' => 'Agujas desechables estériles calibre 21G, caja de 100',
                'sku' => 'NEEDLE-21G-100',
                'barcode' => '7501234567897',
                'item_type' => 'consumable',
                'base_cost' => 15.00,
                'base_price' => 30.00,
                'unit_of_measure' => 'box',
                'reorder_point' => 5,
                'reorder_quantity' => 20,
            ],

            // Equipos médicos
            [
                'name' => 'Termómetro digital',
                'description' => 'Termómetro digital de lectura rápida',
                'sku' => 'THERM-DIG-001',
                'barcode' => '7501234567898',
                'item_type' => 'equipment',
                'base_cost' => 15.00,
                'base_price' => 30.00,
                'unit_of_measure' => 'unit',
                'track_by_serial' => true,
                'reorder_point' => 5,
                'reorder_quantity' => 10,
            ],
            [
                'name' => 'Oxímetro de pulso portátil',
                'description' => 'Oxímetro de pulso digital portátil',
                'sku' => 'OXI-PORT-001',
                'barcode' => '7501234567899',
                'item_type' => 'device',
                'base_cost' => 40.00,
                'base_price' => 80.00,
                'unit_of_measure' => 'unit',
                'track_by_serial' => true,
                'reorder_point' => 3,
                'reorder_quantity' => 5,
            ],
            [
                'name' => 'Tensiómetro digital de brazo',
                'description' => 'Tensiómetro digital automático de brazo',
                'sku' => 'BP-DIG-001',
                'barcode' => '7501234567900',
                'item_type' => 'device',
                'base_cost' => 60.00,
                'base_price' => 120.00,
                'unit_of_measure' => 'unit',
                'track_by_serial' => true,
                'reorder_point' => 3,
                'reorder_quantity' => 5,
            ],

            // Medicamentos OTC comunes
            [
                'name' => 'Paracetamol 500mg (caja 20 tabletas)',
                'description' => 'Paracetamol 500mg, caja con 20 tabletas',
                'sku' => 'MED-PARA-500-20',
                'barcode' => '7501234567901',
                'item_type' => 'medication',
                'base_cost' => 5.00,
                'base_price' => 10.00,
                'unit_of_measure' => 'box',
                'expiration_tracking' => true,
                'track_by_lot' => true,
                'reorder_point' => 20,
                'reorder_quantity' => 100,
            ],
            [
                'name' => 'Ibuprofeno 400mg (caja 20 tabletas)',
                'description' => 'Ibuprofeno 400mg, caja con 20 tabletas',
                'sku' => 'MED-IBU-400-20',
                'barcode' => '7501234567902',
                'item_type' => 'medication',
                'base_cost' => 6.00,
                'base_price' => 12.00,
                'unit_of_measure' => 'box',
                'expiration_tracking' => true,
                'track_by_lot' => true,
                'reorder_point' => 20,
                'reorder_quantity' => 100,
            ],
            [
                'name' => 'Suero fisiológico 0.9% (500ml)',
                'description' => 'Solución salina 0.9% para uso intravenoso',
                'sku' => 'MED-SAL-500',
                'barcode' => '7501234567903',
                'item_type' => 'medication',
                'base_cost' => 3.00,
                'base_price' => 6.00,
                'unit_of_measure' => 'unit',
                'expiration_tracking' => true,
                'track_by_lot' => true,
                'reorder_point' => 30,
                'reorder_quantity' => 100,
            ],

            // Material de oficina médico
            [
                'name' => 'Abatelenguas de madera (caja 100 pcs)',
                'description' => 'Abatelenguas desechables de madera',
                'sku' => 'TONG-WOOD-100',
                'barcode' => '7501234567904',
                'item_type' => 'supply',
                'base_cost' => 8.00,
                'base_price' => 15.00,
                'unit_of_measure' => 'box',
                'reorder_point' => 10,
                'reorder_quantity' => 30,
            ],
            [
                'name' => 'Hisopos de algodón estéril (caja 100 pcs)',
                'description' => 'Hisopos de algodón estériles',
                'sku' => 'SWAB-100',
                'barcode' => '7501234567905',
                'item_type' => 'supply',
                'base_cost' => 10.00,
                'base_price' => 18.00,
                'unit_of_measure' => 'box',
                'reorder_point' => 10,
                'reorder_quantity' => 30,
            ],
        ];

        foreach ($items as $item) {
            InventoryItem::create(array_merge($item, [
                'status' => 'active',
                'client_id' => $client->id,
                'category' => [[
                    'coding' => [[
                        'system' => 'http://terminology.hl7.org/CodeSystem/supply-item-type',
                        'code' => $item['item_type'],
                        'display' => ucfirst($item['item_type']),
                    ]],
                    'text' => ucfirst($item['item_type']),
                ]],
                'code' => [[
                    'coding' => [[
                        'system' => 'urn:meditech:inventory',
                        'code' => $item['sku'],
                    ]],
                ]],
                'currency' => 'USD',
            ]));
        }

        $this->command->info('Created '.count($items).' inventory items for client: '.$client->name);
    }
}
