<?php

namespace App\Console\Commands;

use App\Models\Finance\PaymentSchedule;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

#[Signature('supplier-invoices:send-payment-reminders {--days=1 : Días de antigüedad para considerarse vencido}')]
#[Description('Envía recordatorios de pagos vencidos o próximos a vencer')]
class SendPaymentRemindersCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $daysThreshold = (int) $this->option('days');

        $this->info("Buscando pagos vencidos desde hace {$daysThreshold} día(s)...");

        // Buscar pagos no pagados y vencidos
        $overduePayments = PaymentSchedule::where('paid', false)
            ->where('payment_date', '<=', now()->subDays($daysThreshold)->toDateString())
            ->with(['supplierInvoice' => fn ($q) => $q->with('supplier')])
            ->orderBy('payment_date', 'asc')
            ->get();

        if ($overduePayments->isEmpty()) {
            $this->info('No hay pagos vencidos para notificar.');

            return;
        }

        $this->info("Se encontraron {$overduePayments->count()} pagos vencidos");
        $this->newLine();

        $totalDue = 0;

        foreach ($overduePayments as $schedule) {
            $daysOverdue = now()->diffInDays($schedule->payment_date);
            $totalDue += $schedule->amount;

            $this->line(sprintf(
                '  ⏰ %s | Proveedor: %s | Monto: B/. %s | Vencido: %d días',
                $schedule->payment_date->format('d/m/Y'),
                $schedule->supplierInvoice->supplier->legal_name,
                number_format($schedule->amount, 2),
                $daysOverdue
            ));

            Log::warning('PaymentReminder: Pago vencido detectado', [
                'schedule_id' => $schedule->id,
                'invoice_number' => $schedule->supplierInvoice->invoice_number,
                'supplier' => $schedule->supplierInvoice->supplier->legal_name,
                'amount' => $schedule->amount,
                'payment_date' => $schedule->payment_date,
                'days_overdue' => $daysOverdue,
            ]);
        }

        $this->newLine();
        $this->info("Total pendiente: B/. " . number_format($totalDue, 2));
        $this->info("Total pagos vencidos: {$overduePayments->count()}");

        // Aquí se podrían enviar notificaciones por email, SMS, etc.
        // Por ahora solo se registra en logs
        Log::info('SendPaymentReminders: Procesamiento completado', [
            'reminders_sent' => $overduePayments->count(),
            'total_amount' => $totalDue,
        ]);
    }
}
