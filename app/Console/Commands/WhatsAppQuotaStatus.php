<?php

namespace App\Console\Commands;

use App\Models\WhatsAppConversationUsage;
use Illuminate\Console\Command;

class WhatsAppQuotaStatus extends Command
{
    protected $signature = 'whatsapp:quota-status';

    protected $description = 'Ver estado de la cuota mensual gratuita de WhatsApp (1000 conversaciones)';

    public function handle(): int
    {
        $currentMonth = now()->format('F Y');
        $used = WhatsAppConversationUsage::getCurrentMonthBusinessCount();
        $remaining = WhatsAppConversationUsage::getRemainingQuota();
        $total = 1000;

        $this->info("📊 Estado de Cuota de WhatsApp - {$currentMonth}");
        $this->newLine();

        // Progress bar
        $percentage = ($used / $total) * 100;
        $barLength = 50;
        $filled = (int) (($used / $total) * $barLength);
        $bar = str_repeat('█', $filled).str_repeat('░', $barLength - $filled);

        $this->line("  [{$bar}] {$percentage}%");
        $this->newLine();

        // Stats
        $this->table(
            ['Métrica', 'Valor'],
            [
                ['Conversaciones Usadas', "<fg=yellow>{$used}</> / {$total}"],
                ['Conversaciones Restantes', "<fg=green>{$remaining}</>"],
                ['Estado', $remaining > 100 ? '<fg=green>✓ Disponible</>' : '<fg=red>⚠ Límite Cercano</>'],
            ]
        );

        if ($remaining == 0) {
            $this->newLine();
            $this->warn('⚠️  Cuota agotada: Las prescripciones se enviarán por EMAIL hasta el próximo mes.');
        } elseif ($remaining < 100) {
            $this->newLine();
            $this->warn("⚠️  Solo quedan {$remaining} conversaciones gratuitas este mes.");
        }

        return self::SUCCESS;
    }
}
