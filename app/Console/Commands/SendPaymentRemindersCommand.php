<?php

namespace App\Console\Commands;

use App\Models\Finance\PaymentSchedule;
use App\Models\User;
use App\Notifications\SupplierPaymentReminder;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

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

        // Buscar pagos no pagados y vencidos, agrupados por cliente
        $overduePayments = PaymentSchedule::where('paid', false)
            ->where('payment_date', '<=', now()->subDays($daysThreshold)->toDateString())
            ->with(['supplierInvoice' => fn ($q) => $q->with(['supplier', 'client'])])
            ->orderBy('payment_date', 'asc')
            ->get();

        if ($overduePayments->isEmpty()) {
            $this->info('No hay pagos vencidos para notificar.');

            return;
        }

        $this->info("Se encontraron {$overduePayments->count()} pagos vencidos");
        $this->newLine();

        // Agrupar por cliente
        $byClient = $overduePayments->groupBy('supplierInvoice.client_id');
        $totalNotifications = 0;

        foreach ($byClient as $clientId => $clientPayments) {
            $client = $clientPayments->first()->supplierInvoice->client;

            // Agrupar pagos por usuario que los programó
            $byUser = $clientPayments->groupBy('updated_by');

            foreach ($byUser as $userId => $userPayments) {
                if (! $userId) {
                    continue;
                }

                $paymentDetails = [];

                foreach ($userPayments as $schedule) {
                    $daysOverdue = now()->diffInDays($schedule->payment_date);
                    $paymentDetails[] = [
                        'date' => $schedule->payment_date->format('d/m/Y'),
                        'supplier' => $schedule->supplierInvoice->supplier->legal_name,
                        'amount' => $schedule->amount,
                        'days_overdue' => $daysOverdue,
                    ];

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
                        'client_id' => $clientId,
                        'programmed_by' => $userId,
                    ]);
                }

                // Obtener usuario que programó el pago
                $user = User::find($userId);
                if ($user && $user->active) {
                    $userTotalDue = $userPayments->sum('amount');
                    $notification = new SupplierPaymentReminder(
                        overduePayments: $paymentDetails,
                        totalDue: $userTotalDue,
                        paymentCount: $userPayments->count(),
                        clientName: $client->name
                    );

                    Notification::send($user, $notification);
                    $totalNotifications++;

                    $this->info("✓ Notificación enviada a {$user->full_name} (quien programó el pago)");
                    $this->info('  Total pendiente: B/. '.number_format($userTotalDue, 2));
                }
            }
        }

        $this->newLine();
        $this->info('Procesamiento completado:');
        $this->info("  Pagos vencidos: {$overduePayments->count()}");
        $this->info("  Notificaciones enviadas: {$totalNotifications}");

        Log::info('SendPaymentReminders: Procesamiento completado', [
            'reminders_sent' => $totalNotifications,
            'payment_count' => $overduePayments->count(),
            'total_amount' => $overduePayments->sum('amount'),
        ]);
    }
}
