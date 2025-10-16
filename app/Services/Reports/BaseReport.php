<?php

namespace App\Services\Reports;

use Illuminate\Support\Collection;

abstract class BaseReport
{
    protected string $name = '';

    protected string $title = '';

    protected string $description = '';

    protected array $roles = []; // Roles que tienen acceso

    /**
     * Obtener los datos del reporte
     */
    abstract public function getData(array $filters = []): Collection;

    /**
     * Aplicar filtros basados en el rol
     */
    abstract protected function applyRoleFilters($query);

    /**
     * Aplicar filtros del usuario
     */
    abstract protected function applyUserFilters($query, array $filters);

    /**
     * Obtener filtros disponibles según el rol
     */
    abstract public function getAvailableFilters(): array;

    /**
     * Obtener columnas para Excel
     */
    abstract public function getExcelColumns(): array;

    /**
     * Mapear datos para exportación
     */
    abstract public function mapDataForExcel($item): array;

    /**
     * Verificar si el usuario actual tiene acceso al reporte
     */
    public function userHasAccess(): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        // Admin siempre tiene acceso
        if ($user->hasRole('admin')) {
            return true;
        }

        // Verificar permiso específico
        $permission = "reports.{$this->name}.view";

        return $user->can($permission);
    }

    /**
     * Getters
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * Verificar si el usuario puede exportar a Excel
     */
    public function canExportExcel(): bool
    {
        return auth()->check() && auth()->user()->can("reports.{$this->name}.excel");
    }

    /**
     * Verificar si el usuario puede exportar a PDF
     */
    public function canExportPdf(): bool
    {
        return auth()->check() && auth()->user()->can("reports.{$this->name}.pdf");
    }
}
