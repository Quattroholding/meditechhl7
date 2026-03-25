<?php

namespace App\Services\Reports;

use App\Models\Branch;
use App\Models\Invoice;
use App\Models\Patient;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class InvoicesPaymentsReport extends BaseReport
{
    protected string $name = 'invoices-payments';

    protected string $title = 'Reporte de Facturas y Pagos';

    protected string $description = 'Lista completa de facturas y pagos realizados con filtros personalizables';

    protected array $roles = ['admin', 'doctor', 'recepcionista'];

    /**
     * Obtener los datos del reporte aplicando filtros
     */
    public function getData(array $filters = []): Collection
    {
        $query = Invoice::query()
            ->with(['patient', 'encounter.practitioner', 'branch', 'payments'])
            ->select(
                'invoices.*',
                DB::raw('(SELECT SUM(amount) FROM payments WHERE payments.invoice_id = invoices.id AND payments.status = "completed") as total_paid')
            )
            ->orderBy('date', 'desc');

        // Aplicar filtros por rol
        $query = $this->applyRoleFilters($query);

        // Aplicar filtros del usuario
        $query = $this->applyUserFilters($query, $filters);

        return $query->get();
    }

    /**
     * Aplicar filtros basados en el rol del usuario
     */
    protected function applyRoleFilters($query)
    {
        $user = auth()->user();

        // Filtro para doctores: solo facturas de sus consultas
        if ($user->hasRole('doctor')) {
            $practitionerId = $user->practitioner->id ?? null;
            $query->whereHas('encounter', function ($q) use ($practitionerId) {
                $q->where('practitioner_id', $practitionerId);
            });
        }

        // Filtro para pacientes: solo sus facturas
        if ($user->hasRole('paciente')) {
            $patientId = $user->patient->id ?? null;
            $query->where('patient_id', $patientId);
        }

        return $query;
    }

    /**
     * Aplicar filtros proporcionados por el usuario
     */
    protected function applyUserFilters($query, array $filters)
    {
        // Filtro por rango de fechas
        if (! empty($filters['date_from'])) {
            $query->whereDate('date', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('date', '<=', $filters['date_to']);
        }

        // Filtro por paciente (solo admin/recepcionista)
        if (! empty($filters['patient_id']) && ! auth()->user()->hasRole('doctor')) {
            $query->where('patient_id', $filters['patient_id']);
        }

        // Filtro por sede (solo admin/recepcionista)
        if (! empty($filters['branch_id']) && ! auth()->user()->hasRole('doctor')) {
            $query->where('branch_id', $filters['branch_id']);
        }

        // Filtro por estatus de factura
        if (! empty($filters['invoice_status'])) {
            $query->where('status', $filters['invoice_status']);
        }

        // Filtro por estatus de pago
        if (! empty($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        // Filtro por método de pago
        if (! empty($filters['payment_method'])) {
            $query->whereHas('payments', function ($q) use ($filters) {
                $q->where('payment_method', $filters['payment_method'])
                    ->where('status', 'completed');
            });
        }

        return $query;
    }

    /**
     * Obtener los filtros disponibles según el rol
     */
    public function getAvailableFilters(): array
    {
        $filters = [
            'date_from' => [
                'type' => 'date',
                'label' => 'Fecha Desde',
                'required' => false,
            ],
            'date_to' => [
                'type' => 'date',
                'label' => 'Fecha Hasta',
                'required' => false,
            ],
            'invoice_status' => [
                'type' => 'select',
                'label' => 'Estatus de Factura',
                'options' => [
                    'draft' => 'Borrador',
                    'issued' => 'Emitida',
                    'balanced' => 'Balanceada',
                    'cancelled' => 'Cancelada',
                ],
                'required' => false,
            ],
            'payment_status' => [
                'type' => 'select',
                'label' => 'Estatus de Pago',
                'options' => [
                    'unpaid' => 'Sin Pagar',
                    'partial' => 'Pago Parcial',
                    'paid' => 'Pagado',
                    'overpaid' => 'Sobrepago',
                ],
                'required' => false,
            ],
            'payment_method' => [
                'type' => 'select',
                'label' => 'Método de Pago',
                'options' => [
                    'cash' => 'Efectivo',
                    'credit_card' => 'Tarjeta de Crédito',
                    'debit_card' => 'Tarjeta de Débito',
                    'bank_transfer' => 'Transferencia Bancaria',
                    'check' => 'Cheque',
                    'online' => 'Pago Online',
                    'insurance' => 'Seguro',
                ],
                'required' => false,
            ],
        ];

        // Filtros adicionales para admin y recepcionista
        if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('recepcionista')) {
            $filters['patient_id'] = [
                'type' => 'select',
                'label' => 'Paciente',
                'model' => 'Patient',
                'required' => false,
                'options' => Patient::pluck('name', 'id')->toArray(),
            ];

            $filters['patient_id'] = [
                'type' => 'datalist',
                'label' => 'Paciente',
                'model' => 'Patient',
                'required' => false,
                'options' => Patient::pluck('name', 'id')->toArray(),
            ];
            $filters['branch_id'] = [
                'type' => 'select',
                'label' => 'Sede',
                'model' => 'Branch',
                'required' => false,
                'options' => Branch::pluck('name', 'id')->toArray(),
            ];
        }

        return $filters;
    }

    /**
     * Obtener las columnas para exportación a Excel
     */
    public function getExcelColumns(): array
    {
        return [
            'Nro. Factura',
            'Fecha',
            'Paciente',
            'Médico',
            'Sede',
            'Monto Total',
            'Total Pagado',
            'Saldo Pendiente',
            'Estatus Factura',
            'Estatus Pago',
            'Cantidad de Pagos',
            'Última Fecha de Pago',
        ];
    }

    /**
     * Mapear datos para Excel
     */
    public function mapDataForExcel($invoice): array
    {
        $totalPaid = $invoice->total_paid ?? 0;
        $balance = $invoice->total_amount - $totalPaid;
        $lastPayment = $invoice->payments()
            ->where('status', 'completed')
            ->orderBy('payment_date', 'desc')
            ->first();

        return [
            $invoice->invoice_number ?? 'N/A',
            $invoice->date ? $invoice->date->format('d/m/Y') : 'N/A',
            $invoice->patient->name ?? 'N/A',
            $invoice->encounter?->practitioner?->name ?? 'N/A',
            $invoice->branch?->name ?? 'N/A',
            number_format($invoice->total_amount, 2),
            number_format($totalPaid, 2),
            number_format($balance, 2),
            $this->getInvoiceStatusLabel($invoice->status),
            $this->getPaymentStatusLabel($invoice->payment_status),
            $invoice->payments()->where('status', 'completed')->count(),
            $lastPayment?->payment_date?->format('d/m/Y') ?? 'N/A',
        ];
    }

    /**
     * Obtener etiqueta de estatus de factura
     */
    protected function getInvoiceStatusLabel(string $status): string
    {
        return match ($status) {
            'draft' => 'Borrador',
            'issued' => 'Emitida',
            'balanced' => 'Balanceada',
            'cancelled' => 'Cancelada',
            default => ucfirst($status)
        };
    }

    /**
     * Obtener etiqueta de estatus de pago
     */
    protected function getPaymentStatusLabel(?string $status): string
    {
        return match ($status) {
            'unpaid' => 'Sin Pagar',
            'partial' => 'Pago Parcial',
            'paid' => 'Pagado',
            'overpaid' => 'Sobrepago',
            default => $status ? ucfirst($status) : 'N/A'
        };
    }
}
