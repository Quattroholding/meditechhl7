<?php

namespace App\Console\Commands;

use App\Models\ReferralCode;
use App\Models\SubscriptionDiscount;
use Illuminate\Console\Command;

class UpdateReferralCodesDiscount extends Command
{
    protected $signature = 'referrals:update-discount-values';

    protected $description = 'Update existing referral codes and discounts to use the new $4.99 value';

    public function handle(): int
    {
        $this->info('Updating referral codes and discounts to $4.99...');

        // 1. Actualizar códigos de referido con discount_value = 0
        $referralCodesUpdated = ReferralCode::where('discount_value', 0)
            ->update([
                'discount_value' => 4.99,
                'updated_at' => now(),
            ]);

        $this->info("✓ Updated {$referralCodesUpdated} referral codes from \$0 to \$4.99");

        // 2. Actualizar descuentos de suscripción ya aplicados con valor 0 que vienen de referidos
        $discountsUpdated = SubscriptionDiscount::where('source', 'referral')
            ->where('discount_value', 0)
            ->where('is_active', true)
            ->where('invoices_applied', 0) // Solo los que NO se han usado aún
            ->update([
                'discount_value' => 4.99,
                'updated_at' => now(),
            ]);

        $this->info("✓ Updated {$discountsUpdated} pending referral discounts from \$0 to \$4.99");

        // 3. Recalcular facturas pendientes que tienen descuentos actualizados
        $affectedClients = SubscriptionDiscount::where('source', 'referral')
            ->where('discount_value', 4.99)
            ->where('invoices_applied', 0)
            ->pluck('client_id')
            ->unique();

        if ($affectedClients->isNotEmpty()) {
            $this->info("\nClients with updated discounts who have pending invoices:");

            foreach ($affectedClients as $clientId) {
                $client = \App\Models\Client::find($clientId);
                if ($client) {
                    $pendingInvoices = $client->invoices()
                        ->where('status', 'pending')
                        ->get();

                    if ($pendingInvoices->isNotEmpty()) {
                        $this->warn("  Client #{$clientId} ({$client->name}) has {$pendingInvoices->count()} pending invoice(s)");
                        $this->info("    Run: php artisan invoices:recalculate {$clientId}");
                    }
                }
            }
        }

        $this->newLine();
        $this->info('✓ Referral codes and discounts updated successfully!');
        $this->info('  - Referral codes updated: '.$referralCodesUpdated);
        $this->info('  - Active discounts updated: '.$discountsUpdated);

        return self::SUCCESS;
    }
}
