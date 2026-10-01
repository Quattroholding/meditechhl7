<?php

namespace App\Services\DocumentParsers;

class EnsaBillParser extends BaseDocumentParser
{
    /**
     * Parse Google Document AI response for electricity bill PDF
     *
     * Extracts key information from ENSA (Panama) electricity bills:
     * - Invoice number, customer name, address
     * - Billing period, consumption, charges
     * - Total amount to pay
     */
    public function parse(array $googleAIResponse): array
    {
        $this->reset();

        try {
            // Extract text from the response
            $text = $googleAIResponse['document']['text'] ?? '';

            if (empty($text)) {
                $this->addError('No se encontró texto extraíble en el documento');

                return $this->getResult($googleAIResponse);
            }

            // Extract main bill information
            $billInfo = $this->extractBillInfo($text);

            // Extract charges/items (if any line items exist)
            $charges = $this->extractCharges($text);

            // Build result with metadata
            $result = [
                'bill_number' => $billInfo['bill_number'] ?? null,
                'customer_name' => $billInfo['customer_name'] ?? null,
                'customer_address' => $billInfo['customer_address'] ?? null,
                'service_address' => $billInfo['service_address'] ?? null,
                'service_number' => $billInfo['service_number'] ?? null,
                'billing_period_start' => $billInfo['billing_period_start'] ?? null,
                'billing_period_end' => $billInfo['billing_period_end'] ?? null,
                'issue_date' => $billInfo['issue_date'] ?? null,
                'due_date' => $billInfo['due_date'] ?? null,
                'meter_number' => $billInfo['meter_number'] ?? null,
                'consumption_kwh' => $billInfo['consumption_kwh'] ?? 0,
                'consumption_type' => $billInfo['consumption_type'] ?? 'Real',
                'charges' => $charges,
                'subtotal' => $billInfo['subtotal'] ?? 0,
                'discounts' => $billInfo['discounts'] ?? 0,
                'taxes' => $billInfo['taxes'] ?? 0,
                'total' => $billInfo['total'] ?? 0,
                'previous_balance' => $billInfo['previous_balance'] ?? 0,
                'amount_paid' => $billInfo['amount_paid'] ?? 0,
                'balance' => $billInfo['balance'] ?? 0,
            ];

            $this->addConfidenceScore(0.85); // ENSA bills have consistent format

            return $this->getResultWithMetadata($googleAIResponse, $result);

        } catch (\Exception $e) {
            $this->addError('Error procesando factura de electricidad: '.$e->getMessage());

            return $this->getResult($googleAIResponse);
        }
    }

    /**
     * Extract main bill information
     */
    private function extractBillInfo(string $text): array
    {
        $info = [];
        $lines = array_filter(array_map('trim', explode("\n", $text)));

        // Extract invoice number (NAC: XXXXXX) - NAC can be on next line
        if (preg_match('/NAC:\s*[\n\s]*(\d+)/i', $text, $matches)) {
            $info['bill_number'] = $matches[1];
        }

        // Extract customer name (handles multi-word names)
        if (preg_match('/Nombre:\s+([^\n]+?)\s+Factura/i', $text, $matches)) {
            $info['customer_name'] = trim($matches[1]);
        }

        // Extract service address (may span multiple lines)
        if (preg_match('/Dirección:\s*([^\n]+(?:\n[^\n]+)*?)(?:\n[A-Z][a-z]+:|\nDías|$)/i', $text, $matches)) {
            $address = trim(preg_replace('/\s+/', ' ', $matches[1]));
            $info['service_address'] = $address;
        }

        // Extract service number (Servicio:)
        if (preg_match('/Servicio:\s*([^\n]+)/i', $text, $matches)) {
            $info['service_number'] = trim($matches[1]);
        }

        // Extract billing dates (both dates are after the labels section)
        if (preg_match('/Desde:[\s\n]*Hasta:[\s\n]*(\d{2}\/\d{2}\/\d{4})[\s\n]+(\d{2}\/\d{2}\/\d{4})/i', $text, $matches)) {
            $info['billing_period_start'] = $matches[1];
            $info['billing_period_end'] = $matches[2];
        }

        // Extract issue date
        if (preg_match('/Emisión:\s*(\d{2}\s+de\s+\w+\s+de\s+\d{4})/i', $text, $matches)) {
            $info['issue_date'] = $matches[1];
        }

        // Extract due date (handle both formats: "14 de Octubre de 2026" and "14/oct/2026")
        if (preg_match('/Vencimiento:\s*[\n\s]*(\d{2}(?:\s+de\s+\w+)?(?:\s+de\s+\d{4})?|[\d\/]+)/i', $text, $matches)) {
            $info['due_date'] = trim($matches[1]);
        }

        // Extract meter number (includes alphanumeric)
        if (preg_match('/Medidor:\s*([0-9\-A-Z]+)/i', $text, $matches)) {
            $info['meter_number'] = $matches[1];
        }

        // Extract consumption in kWh (Consumo del mes)
        if (preg_match('/Consumo\s+del\s+mes\s+(\d+)\s*kWh/i', $text, $matches)) {
            $info['consumption_kwh'] = (int) $matches[1];
        }

        // Extract consumption type (Real/Estimado)
        if (preg_match('/Tipo\s+de\s+lectura:\s*([^\n]+)/i', $text, $matches)) {
            $info['consumption_type'] = trim($matches[1]);
        }

        // Extract financial amounts
        $this->extractFinancialAmounts($text, $info);

        return $info;
    }

    /**
     * Extract financial information
     */
    private function extractFinancialAmounts(string $text, array &$info): void
    {
        // Extract subtotal (amount can be on next line)
        if (preg_match('/Sub-Total[\s\n]+[\d.]+[\s\n]+([\d.,]+)/i', $text, $matches)) {
            $info['subtotal'] = $this->parseAmount($matches[1]);
        }

        // Extract total ENSA (ignore currency symbols and any content between label and amount)
        if (preg_match('/Total\s+Ensa\s*\n\s*[^\n]*\s+([\d.]+)/i', $text, $matches)) {
            $info['total'] = $this->parseAmount($matches[1]);
        } elseif (preg_match('/TOTAL\s+A\s+PAGAR[^0-9]+([\d.]+)/i', $text, $matches)) {
            // Fallback to "TOTAL A PAGAR" if "Total ENSA" not found
            $info['total'] = $this->parseAmount($matches[1]);
        }

        // Extract discounts/subsidies (amount can be negative on next line)
        if (preg_match('/Fondo\s+estabilización[^0-9]*[\d.]+[^0-9]*(-[\d.]+)/i', $text, $matches)) {
            $info['discounts'] = abs($this->parseAmount($matches[1]));
        }

        // Extract previous balance
        if (preg_match('/Saldo\s+anterior[^0-9]*([\d.,]+)/i', $text, $matches)) {
            $info['previous_balance'] = $this->parseAmount($matches[1]);
        }

        // Extract amount paid (this month's total) - use the total from financial section
        if (preg_match('/TOTAL\s+A\s+PAGAR[^0-9]+([\d.]+)/i', $text, $matches)) {
            $info['amount_paid'] = $this->parseAmount($matches[1]);
        }

        // Extract current balance
        if (preg_match('/Saldo\s+a\s+corte[^0-9]*([\d.,]+)/i', $text, $matches)) {
            $info['balance'] = $this->parseAmount($matches[1]);
        }
    }

    /**
     * Extract charge line items
     */
    private function extractCharges(string $text): array
    {
        $charges = [];

        // Pattern to extract charges from the "DETALLES DE SU FACTURA" section
        if (preg_match('/CARGOS POR ENERGÍA(.*?)(?:SUBSIDIOS|OTROS|$)/is', $text, $match)) {
            $chargeSection = $match[1];
            $lines = array_filter(array_map('trim', explode("\n", $chargeSection)));

            $index = 0;
            foreach ($lines as $line) {
                // Look for lines with description and amount
                if (preg_match('/([A-Za-z\s]+?)\s+([\d.]+)\s+([\d.,]+)/i', $line, $matches)) {
                    $description = trim($matches[1]);
                    $unitCost = $this->parseAmount($matches[2]);
                    $amount = $this->parseAmount($matches[3]);

                    if (! empty($description) && is_numeric($unitCost) && is_numeric($amount)) {
                        $charges[] = [
                            'sku' => 'ELEC-'.strtoupper(substr(preg_replace('/[^A-Z0-9]/', '', $description), 0, 8)),
                            'name' => $description,
                            'quantity' => 1,
                            'unit_cost' => $unitCost,
                            'unit' => 'service',
                            'unit_type' => 'presentation',
                            'internal_units_per_presentation' => 1,
                            'base_price' => $amount,
                        ];
                        $index++;
                    }
                }
            }
        }

        return $charges;
    }

    /**
     * Parse monetary amount from various formats
     * Handles: B/.25.82, Β/.25.82, 25,82, 25.82, etc.
     */
    private function parseAmount(string $value): float
    {
        // Remove Β/. or B/. currency symbols and spaces (keep decimal point and minus sign)
        $cleaned = preg_replace('/[BΒ\/\s]/', '', $value);
        // Replace comma with dot if it's a thousands/decimal separator
        $cleaned = str_replace(',', '.', $cleaned);

        return (float) $cleaned ?: 0;
    }
}
