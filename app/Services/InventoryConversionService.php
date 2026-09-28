<?php

namespace App\Services;

use App\Models\InventoryItem;

/**
 * Service for handling inventory unit conversions
 * Manages conversions between presentation units and internal units
 * Example: Caja (presentation) -> Pastilla (internal, 30 per caja)
 */
class InventoryConversionService
{
    /**
     * Convert quantity from one unit type to another
     *
     * @param  string  $fromUnitType  'presentation' or 'internal'
     * @param  string  $toUnitType  'presentation' or 'internal'
     */
    public function convert(InventoryItem $item, float $quantity, string $fromUnitType, string $toUnitType): float
    {
        // If item doesn't track internal content, return quantity as-is
        if (! $item->track_internal_content) {
            return $quantity;
        }

        // If converting to same type, return as-is
        if ($fromUnitType === $toUnitType) {
            return $quantity;
        }

        $conversionFactor = (float) $item->internal_units_per_presentation;

        // Convert from presentation to internal
        if ($fromUnitType === 'presentation' && $toUnitType === 'internal') {
            return $quantity * $conversionFactor;
        }

        // Convert from internal to presentation
        if ($fromUnitType === 'internal' && $toUnitType === 'presentation') {
            return $quantity / $conversionFactor;
        }

        return $quantity;
    }

    /**
     * Break down internal units into presentations and remaining units
     * Example: 170 pastillas (30 per caja) = 5 cajas + 20 pastillas
     *
     * @return array ['presentations' => int, 'internal_units' => float]
     */
    public function breakdownToPresentation(InventoryItem $item, float $internalUnits): array
    {
        if (! $item->track_internal_content) {
            return ['presentations' => (int) $internalUnits, 'internal_units' => 0];
        }

        $factor = (float) $item->internal_units_per_presentation;
        $presentations = (int) floor($internalUnits / $factor);
        $remaining = $internalUnits % $factor;

        return [
            'presentations' => $presentations,
            'internal_units' => (float) $remaining,
        ];
    }

    /**
     * Calculate total internal units from presentations + remaining internal units
     * Example: 5 cajas + 20 pastillas = 170 pastillas (if 30 per caja)
     */
    public function calculateTotalInternalUnits(InventoryItem $item, int $presentations, float $internalUnits): float
    {
        if (! $item->track_internal_content) {
            return $presentations + $internalUnits;
        }

        $factor = (float) $item->internal_units_per_presentation;

        return ($presentations * $factor) + $internalUnits;
    }

    /**
     * Validate if a quantity can be satisfied by current stock
     * Handles both presentation and internal unit requests
     */
    public function canSatisfy(
        InventoryItem $item,
        int $completePresentations,
        float $internalUnitsInOpen,
        float $requestedQuantity,
        string $requestUnitType
    ): bool {
        if (! $item->track_internal_content) {
            return $completePresentations >= $requestedQuantity;
        }

        if ($requestUnitType === 'presentation') {
            return $completePresentations >= $requestedQuantity;
        }

        // For internal units, check total available
        $totalInternalUnits = $this->calculateTotalInternalUnits(
            $item,
            $completePresentations,
            $internalUnitsInOpen
        );

        return $totalInternalUnits >= $requestedQuantity;
    }

    /**
     * Calculate what stock remains after fulfilling a request
     * Smart logic that handles opening presentations as needed
     *
     * @return array ['presentations' => int, 'internal_units' => float] or null if cannot satisfy
     */
    public function calculateRemainingStock(
        InventoryItem $item,
        int $completePresentations,
        float $internalUnitsInOpen,
        float $requestedQuantity,
        string $requestUnitType
    ): ?array {
        if (! $this->canSatisfy($item, $completePresentations, $internalUnitsInOpen, $requestedQuantity, $requestUnitType)) {
            return null;
        }

        if (! $item->track_internal_content) {
            return [
                'presentations' => (int) ($completePresentations - $requestedQuantity),
                'internal_units' => 0,
            ];
        }

        // If request is in presentations
        if ($requestUnitType === 'presentation') {
            return [
                'presentations' => (int) ($completePresentations - $requestedQuantity),
                'internal_units' => $internalUnitsInOpen,
            ];
        }

        // Request is in internal units - need to handle smart deduction
        $factor = (float) $item->internal_units_per_presentation;
        $remaining = $requestedQuantity;

        // First, take from open presentation
        $fromOpen = min($remaining, $internalUnitsInOpen);
        $remaining -= $fromOpen;
        $newOpenUnits = $internalUnitsInOpen - $fromOpen;

        // If we still need more, open complete presentations
        $presentationsToOpen = (int) ceil($remaining / $factor);

        if ($presentationsToOpen > $completePresentations) {
            // Should not happen if canSatisfy was true
            return null;
        }

        $newComplete = $completePresentations - $presentationsToOpen;
        $newOpenUnits += ($presentationsToOpen * $factor) - $remaining;

        return [
            'presentations' => $newComplete,
            'internal_units' => (float) $newOpenUnits,
        ];
    }

    /**
     * Format quantity for display
     * Example: 5 cajas + 20 pastillas or 165 pastillas total
     */
    public function formatQuantity(
        InventoryItem $item,
        int $presentations,
        float $internalUnits,
        bool $verbose = true
    ): string {
        if (! $item->track_internal_content) {
            return (int) $presentations.' '.$item->unit_of_measure;
        }

        if ($verbose) {
            $parts = [];
            if ($presentations > 0) {
                $parts[] = $presentations.' '.$item->unit_of_measure;
            }
            if ($internalUnits > 0) {
                $parts[] = number_format($internalUnits, 2).' '.$item->internal_unit;
            }

            if (empty($parts)) {
                return '0 '.$item->unit_of_measure;
            }

            return implode(' + ', $parts);
        }

        $total = $this->calculateTotalInternalUnits($item, $presentations, $internalUnits);

        return number_format($total, 2).' '.$item->internal_unit;
    }
}
