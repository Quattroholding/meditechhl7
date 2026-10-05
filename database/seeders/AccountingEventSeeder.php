<?php

namespace Database\Seeders;

use App\Models\Integration\AccountingEvent;
use Illuminate\Database\Seeder;

class AccountingEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accountingEvents = [
            [
                'code' => 'INVOICE_CASH',
                'name' => 'Factura de Contado',
                'description' => 'Registro de factura pagada al contado en el sistema contable',
                'module' => 'billing',
            ],
            [
                'code' => 'INVOICE_CREDIT',
                'name' => 'Factura a Crédito',
                'description' => 'Registro de factura a crédito en el sistema contable',
                'module' => 'billing',
            ],
            [
                'code' => 'PAYMENT_RECEIVED',
                'name' => 'Cobro Recibido',
                'description' => 'Registro de cobro de factura pendiente en el sistema contable',
                'module' => 'billing',
            ],
            [
                'code' => 'SUPPLIER_INVOICE',
                'name' => 'Factura de Proveedor',
                'description' => 'Registro de factura recibida de proveedor en el sistema contable',
                'module' => 'payables',
            ],
            [
                'code' => 'SUPPLIER_PAYMENT',
                'name' => 'Pago a Proveedor',
                'description' => 'Registro de pago realizado a proveedor en el sistema contable',
                'module' => 'payables',
            ],
            [
                'code' => 'BANK_TRANSFER',
                'name' => 'Transferencia Bancaria',
                'description' => 'Registro de transferencia bancaria en el sistema contable',
                'module' => 'treasury',
            ],
        ];

        $createdCount = 0;

        foreach ($accountingEvents as $data) {
            // Check if accounting event already exists
            if (AccountingEvent::where('code', $data['code'])->exists()) {
                continue;
            }

            AccountingEvent::create([
                'code' => $data['code'],
                'name' => $data['name'],
                'description' => $data['description'],
                'module' => $data['module'],
                'status' => 'active',
            ]);

            $createdCount++;
        }

        if ($createdCount > 0) {
            $this->command->info("Created {$createdCount} accounting events.");
        }

        $this->command->info('AccountingEventSeeder completed successfully.');
    }
}
