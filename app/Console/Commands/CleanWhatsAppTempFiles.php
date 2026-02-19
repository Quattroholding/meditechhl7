<?php

namespace App\Console\Commands;

use App\Services\WhatsAppService;
use Illuminate\Console\Command;

class CleanWhatsAppTempFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsapp:clean-temp-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up temporary WhatsApp files older than 24 hours';

    /**
     * Execute the console command.
     */
    public function handle(WhatsAppService $whatsAppService): int
    {
        $this->info('Limpiando archivos temporales de WhatsApp...');

        $deletedCount = $whatsAppService->cleanupOldTempFiles();

        if ($deletedCount > 0) {
            $this->info("Se eliminaron {$deletedCount} archivos temporales.");
        } else {
            $this->info('No se encontraron archivos temporales para eliminar.');
        }

        return Command::SUCCESS;
    }
}
