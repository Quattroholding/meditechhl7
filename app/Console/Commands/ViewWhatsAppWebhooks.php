<?php

namespace App\Console\Commands;

use App\Models\WhatsAppWebhook;
use Illuminate\Console\Command;

class ViewWhatsAppWebhooks extends Command
{
    protected $signature = 'whatsapp:webhooks
                            {--limit=10 : Number of webhooks to display}
                            {--type= : Filter by event type (status, message, error)}
                            {--status= : Filter by status (delivered, read, failed, sent)}
                            {--failed : Show only failed messages}
                            {--message= : Show specific message by ID}';

    protected $description = 'View WhatsApp webhooks received from Meta';

    public function handle(): int
    {
        $this->info('🔔 WhatsApp Webhooks');
        $this->newLine();

        // Show specific message
        if ($messageId = $this->option('message')) {
            return $this->showMessage($messageId);
        }

        // Build query
        $query = WhatsAppWebhook::query();

        if ($type = $this->option('type')) {
            $query->where('event_type', $type);
        }

        if ($status = $this->option('status')) {
            $query->where('status', $status);
        }

        if ($this->option('failed')) {
            $query->where('status', 'failed');
        }

        $limit = (int) $this->option('limit');
        $webhooks = $query->latest()->limit($limit)->get();

        if ($webhooks->isEmpty()) {
            $this->warn('No se encontraron webhooks.');

            return Command::SUCCESS;
        }

        // Display statistics
        $this->displayStatistics();
        $this->newLine();

        // Display webhooks table
        $this->table(
            ['ID', 'Tipo', 'Message ID', 'De', 'Para', 'Status', 'Error', 'Fecha'],
            $webhooks->map(function ($webhook) {
                return [
                    $webhook->id,
                    $webhook->event_type ?? '-',
                    substr($webhook->message_id ?? '-', 0, 20).'...',
                    $webhook->from ?? '-',
                    $webhook->to ?? '-',
                    $this->colorStatus($webhook->status),
                    $webhook->hasError() ? '❌ Sí' : '✅ No',
                    $webhook->created_at->diffForHumans(),
                ];
            })->toArray()
        );

        $this->newLine();
        $this->info("Mostrando {$webhooks->count()} webhooks");

        if ($webhooks->where('status', 'failed')->count() > 0) {
            $this->newLine();
            $this->warn('💡 Usa --failed para ver solo mensajes fallidos');
            $this->warn('💡 Usa --message=ID para ver detalles de un mensaje específico');
        }

        return Command::SUCCESS;
    }

    protected function showMessage(string $messageId): int
    {
        $webhook = WhatsAppWebhook::where('message_id', $messageId)->first();

        if (! $webhook) {
            $this->error("No se encontró webhook con message_id: {$messageId}");

            return Command::FAILURE;
        }

        $this->info("📱 Detalles del Webhook #{$webhook->id}");
        $this->newLine();

        $this->line("<fg=cyan>Event Type:</> {$webhook->event_type}");
        $this->line("<fg=cyan>Message ID:</> {$webhook->message_id}");
        $this->line("<fg=cyan>Phone Number ID:</> {$webhook->phone_number_id}");
        $this->line("<fg=cyan>From:</> {$webhook->from}");
        $this->line("<fg=cyan>To:</> {$webhook->to}");
        $this->line('<fg=cyan>Status:</> '.$this->colorStatus($webhook->status));

        if ($webhook->hasError()) {
            $this->newLine();
            $this->error('❌ ERROR: '.$webhook->getErrorDetails());
        }

        $this->newLine();
        $this->line("<fg=cyan>Recibido:</> {$webhook->webhook_received_at->format('Y-m-d H:i:s')} ({$webhook->webhook_received_at->diffForHumans()})");
        $this->newLine();

        if ($webhook->metadata) {
            $this->info('📊 Metadata:');
            $this->line(json_encode($webhook->metadata, JSON_PRETTY_PRINT));
            $this->newLine();
        }

        if ($this->confirm('¿Ver payload completo?', false)) {
            $this->info('📦 Raw Payload:');
            $this->line(json_encode($webhook->raw_payload, JSON_PRETTY_PRINT));
        }

        return Command::SUCCESS;
    }

    protected function displayStatistics(): void
    {
        $total = WhatsAppWebhook::count();
        $today = WhatsAppWebhook::whereDate('created_at', today())->count();
        $failed = WhatsAppWebhook::where('status', 'failed')->count();

        $this->info('📊 Estadísticas:');
        $this->line("   Total: {$total}");
        $this->line("   Hoy: {$today}");
        $this->line("   Fallidos: {$failed}");

        if ($failed > 0) {
            $failureRate = number_format(($failed / max($total, 1)) * 100, 1);
            $this->warn("   Tasa de fallo: {$failureRate}%");
        }
    }

    protected function colorStatus(?string $status): string
    {
        if (! $status) {
            return '-';
        }

        return match ($status) {
            'sent' => "<fg=blue>{$status}</>",
            'delivered' => "<fg=green>{$status}</>",
            'read' => "<fg=green;options=bold>{$status}</>",
            'failed' => "<fg=red;options=bold>{$status}</>",
            default => $status,
        };
    }
}
