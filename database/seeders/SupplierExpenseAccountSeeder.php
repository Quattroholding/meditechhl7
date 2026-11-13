<?php

namespace Database\Seeders;

use App\Models\Accounting\AccountingAccount;
use App\Models\Finance\Supplier;
use Illuminate\Database\Seeder;

class SupplierExpenseAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Configures expense accounts for suppliers based on their type.
     */
    public function run(): void
    {
        // Mapeo de palabras clave en nombres de proveedores a cuentas de gasto
        $supplierAccountMapping = [
            'IDAAN' => '6140',      // Agua
            'NATURGY' => '6140',    // Agua (default para NATURGY)
            'ENSA' => '6145',       // Electricidad
            'AES' => '6145',        // Electricidad
        ];

        // Procesar cada proveedor
        Supplier::whereNull('expense_account_id')
            ->where('status', 'active')
            ->get()
            ->each(function (Supplier $supplier) use ($supplierAccountMapping) {
                $accountCode = null;

                // Buscar coincidencia en el mapeo
                foreach ($supplierAccountMapping as $keyword => $code) {
                    if (stripos($supplier->commercial_name ?? $supplier->legal_name, $keyword) !== false) {
                        $accountCode = $code;
                        break;
                    }
                }

                // Si se encontró una coincidencia, asignar la cuenta
                if ($accountCode) {
                    $account = AccountingAccount::where('code', $accountCode)
                        ->where('client_id', $supplier->client_id)
                        ->where('status', 'active')
                        ->first();

                    if ($account) {
                        $supplier->update(['expense_account_id' => $account->id]);
                        echo "✓ Proveedor: {$supplier->legal_name} → Cuenta: {$code}\n";
                    } else {
                        echo "⚠ Cuenta {$accountCode} no encontrada para cliente {$supplier->client_id}\n";
                    }
                }
            });

        echo "\n✅ Configuración de cuentas de gasto completada\n";
    }
}
