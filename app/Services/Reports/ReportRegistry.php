<?php

namespace App\Services\Reports;

class ReportRegistry
{
    protected static array $reports = [
        'appointments' => AppointmentsReport::class,
        'invoices-payments' => InvoicesPaymentsReport::class,
        // Agregar más reportes aquí en el futuro
    ];

    public static function get(string $name): ?BaseReport
    {
        $class = self::$reports[$name] ?? null;

        return $class ? new $class : null;
    }

    public static function all(): array
    {
        return collect(self::$reports)
            ->map(fn ($class) => new $class)
            ->filter(fn ($report) => $report->userHasAccess())
            ->values()
            ->toArray();
    }

    public static function exists(string $name): bool
    {
        return isset(self::$reports[$name]);
    }
}
