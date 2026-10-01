<?php

namespace App\Services\DocumentParsers;

class NaturgyBillParser extends BaseDocumentParser
{
    /**
     * Parse Google Document AI response for Naturgy electricity bill (Panama)
     *
     * Naturgy (Edemet-Edechi) is an electricity distributor in Panama
     * with a standardized bill format
     */
    public function parse(array $googleAIResponse): array
    {
        $this->reset();

        try {
            $text = $googleAIResponse['document']['text'] ?? '';

            if (empty($text)) {
                $this->addWarning('No se encontró texto extraíble en el documento');

                return $this->getResultWithMetadata($googleAIResponse, [
                    'items' => [],
                ]);
            }

            // Extract bill details
            $billNumber = $this->extractBillNumber($text);
            $nisNumber = $this->extractNisNumber($text);
            $customerName = $this->extractCustomerName($text);
            $address = $this->extractAddress($text);
            $meterNumber = $this->extractMeterNumber($text);
            $period = $this->extractBillingPeriod($text);
            $issueDate = $this->extractIssueDate($text);
            $dueDate = $this->extractDueDate($text);
            $consumption = $this->extractConsumption($text);
            $demand = $this->extractDemand($text);
            $totalAmount = $this->extractTotalAmount($text);

            // Validate key fields
            if (! $billNumber) {
                $this->addWarning('No se encontró número de factura');
            }
            if (! $nisNumber) {
                $this->addWarning('No se encontró número NIS');
            }
            if (! $consumption) {
                $this->addWarning('No se encontró consumo en kWh');
            }

            // Add confidence scores
            $this->addConfidenceScore(0.95); // Bill number
            $this->addConfidenceScore(0.95); // NIS number
            $this->addConfidenceScore(0.90); // Customer name
            $this->addConfidenceScore(0.92); // Consumption
            $this->addConfidenceScore(0.93); // Total amount

            return $this->getResultWithMetadata($googleAIResponse, [
                'items' => [],
                'bill_number' => $billNumber,
                'nis_number' => $nisNumber,
                'customer_name' => $customerName,
                'service_address' => $address,
                'meter_number' => $meterNumber,
                'billing_period' => $period,
                'billing_period_start' => $period['start'] ?? null,
                'billing_period_end' => $period['end'] ?? null,
                'issue_date' => $issueDate,
                'due_date' => $dueDate,
                'consumption_kwh' => $consumption,
                'demand_kw' => $demand,
                'total_amount' => $totalAmount,
            ]);

        } catch (\Exception $e) {
            $this->addError('Error procesando factura de Naturgy: '.$e->getMessage());

            return $this->getResultWithMetadata($googleAIResponse, [
                'items' => [],
            ]);
        }
    }

    /**
     * Extract bill number (FACTURA NO.)
     */
    private function extractBillNumber(string $text): ?string
    {
        $lines = explode("\n", $text);

        foreach ($lines as $i => $line) {
            if (stripos($line, 'FACTURA') !== false && stripos($line, 'NO') !== false) {
                // Check next 2 lines for the bill number
                for ($j = $i; $j < min($i + 3, count($lines)); $j++) {
                    if (preg_match('/F\d{6,20}/', $lines[$j], $matches)) {
                        return $matches[0];
                    }
                }
            }
        }

        return null;
    }

    /**
     * Extract NIS number (customer ID)
     */
    private function extractNisNumber(string $text): ?string
    {
        $lines = explode("\n", $text);

        // Look for "NIS" pattern
        foreach ($lines as $line) {
            if (preg_match('/NIS\s+(\d+[\s\d]+)/', $line, $matches)) {
                $nis = preg_replace('/\s+/', '', $matches[1]);
                if (strlen($nis) >= 6) {
                    return $nis;
                }
            }
        }

        // Fallback: look for patterns like "6586766 001"
        if (preg_match('/(\d{7}\s+\d{3})/', $text, $matches)) {
            return str_replace(' ', '', $matches[1]);
        }

        return null;
    }

    /**
     * Extract customer name
     */
    private function extractCustomerName(string $text): ?string
    {
        $lines = explode("\n", $text);

        // Look for "INFORMACION DEL CLIENTE" or similar
        foreach ($lines as $i => $line) {
            if (stripos($line, 'CLIENTE') !== false && stripos($line, 'INFORMACION') !== false) {
                // Next line should have the customer name
                if (isset($lines[$i + 1])) {
                    $name = trim($lines[$i + 1]);
                    if (strlen($name) > 3 && ! preg_match('/^\d+$/', $name)) {
                        return $name;
                    }
                }
            }
        }

        return null;
    }

    /**
     * Extract service address
     */
    private function extractAddress(string $text): ?string
    {
        $lines = explode("\n", $text);

        // Look for address information after customer name or "INFORMACION DEL CLIENTE"
        $foundClient = false;
        $addressLines = [];

        foreach ($lines as $i => $line) {
            if (stripos($line, 'CLIENTE') !== false) {
                $foundClient = true;

                continue;
            }

            if ($foundClient && ! empty(trim($line)) && strlen(trim($line)) > 5) {
                // Collect address lines until we hit "MEDE" or other headers
                if (! preg_match('/MEDE|CONTRATO|NO\.|RUTA/i', $line)) {
                    $addressLines[] = trim($line);
                    if (count($addressLines) >= 2) {
                        break;
                    }
                } else {
                    break;
                }
            }
        }

        return ! empty($addressLines) ? implode(', ', $addressLines) : null;
    }

    /**
     * Extract meter number
     */
    private function extractMeterNumber(string $text): ?string
    {
        $lines = explode("\n", $text);

        foreach ($lines as $i => $line) {
            if (stripos($line, 'MEDIDOR') !== false || stripos($line, 'MED ') !== false) {
                // Check this line and next lines for a number
                for ($j = $i; $j < min($i + 3, count($lines)); $j++) {
                    if (preg_match('/\b(\d{9})\b/', $lines[$j], $matches)) {
                        return $matches[1];
                    }
                }
            }
        }

        return null;
    }

    /**
     * Extract billing period
     */
    private function extractBillingPeriod(string $text): array
    {
        $lines = explode("\n", $text);
        $period = ['start' => null, 'end' => null];

        foreach ($lines as $i => $line) {
            if (stripos($line, 'PERIODO') !== false || stripos($line, 'LECTURA') !== false) {
                // Look for date pattern in next lines
                for ($j = $i; $j < min($i + 5, count($lines)); $j++) {
                    if (preg_match('/(\d{2})\s*[\\/\-]\s*(\d{2})\s*[\\/\-]\s*(\d{4})/', $lines[$j], $matches)) {
                        $date = $this->formatDate($matches[1], $matches[2], $matches[3]);
                        if (! $period['start']) {
                            $period['start'] = $date;
                        } else {
                            $period['end'] = $date;
                            break;
                        }
                    }
                }
                if ($period['start'] && $period['end']) {
                    break;
                }
            }
        }

        return $period;
    }

    /**
     * Extract issue date
     */
    private function extractIssueDate(string $text): ?string
    {
        $lines = explode("\n", $text);

        foreach ($lines as $i => $line) {
            if (stripos($line, 'EMISIÓN') !== false) {
                // Check next 2 lines for date
                for ($j = $i; $j < min($i + 3, count($lines)); $j++) {
                    if (preg_match('/(\d{2})\s*[\\/\-]\s*(\d{2})\s*[\\/\-]\s*(\d{4})/', $lines[$j], $matches)) {
                        return $this->formatDate($matches[1], $matches[2], $matches[3]);
                    }
                }
            }
        }

        return null;
    }

    /**
     * Extract due date
     */
    private function extractDueDate(string $text): ?string
    {
        $lines = explode("\n", $text);

        foreach ($lines as $i => $line) {
            if (stripos($line, 'VENCIMIENTO') !== false) {
                // Check next 2 lines for date
                for ($j = $i; $j < min($i + 3, count($lines)); $j++) {
                    if (preg_match('/(\d{2})\s*[\\/\-]\s*(\d{2})\s*[\\/\-]\s*(\d{4})/', $lines[$j], $matches)) {
                        return $this->formatDate($matches[1], $matches[2], $matches[3]);
                    }
                }
            }
        }

        return null;
    }

    /**
     * Extract consumption in kWh
     */
    private function extractConsumption(string $text): ?float
    {
        $lines = explode("\n", $text);

        // Look for "Activa kWh" pattern and get the consumption value (the third number)
        foreach ($lines as $i => $line) {
            if (stripos($line, 'Activa') !== false && stripos($line, 'kWh') !== false) {
                // Next lines should contain: previous meter, meter number, current meter, multiplier, consumption
                // We want the last number which is the consumption
                $consumption = null;
                for ($j = $i + 1; $j < min($i + 6, count($lines)); $j++) {
                    if (preg_match('/\b(\d{3,5})\b/', $lines[$j], $matches)) {
                        $value = (float) $matches[1];
                        // The largest number in this range is usually the consumption
                        if ($value > 100 && $value < 50000) {
                            $consumption = $value;
                        }
                    }
                }
                if ($consumption !== null) {
                    return $consumption;
                }
            }
        }

        // Fallback: look for the largest 4-digit number in consumption section
        foreach ($lines as $i => $line) {
            if (stripos($line, 'CONSUMO') !== false || stripos($line, 'Consumo') !== false) {
                // Check next 10 lines for numbers
                $numbers = [];
                for ($j = $i; $j < min($i + 10, count($lines)); $j++) {
                    if (preg_match_all('/\b(\d{3,5})\b/', $lines[$j], $matches)) {
                        foreach ($matches[1] as $num) {
                            $numbers[] = (float) $num;
                        }
                    }
                }
                // Return the largest number that makes sense as consumption
                $consumption = array_filter($numbers, function ($n) {
                    return $n > 100 && $n < 50000;
                });
                if (! empty($consumption)) {
                    return (float) max($consumption);
                }
            }
        }

        return null;
    }

    /**
     * Extract demand in kW
     */
    private function extractDemand(string $text): ?float
    {
        $lines = explode("\n", $text);

        foreach ($lines as $i => $line) {
            if (stripos($line, 'Demanda') !== false && stripos($line, 'kW') !== false) {
                // Check next 3 lines for the demand value
                for ($j = $i; $j < min($i + 4, count($lines)); $j++) {
                    if (preg_match('/(\d+[.,]\d+)/', $lines[$j], $matches)) {
                        $value = str_replace(',', '.', $matches[1]);

                        return (float) $value;
                    }
                }
            }
        }

        return null;
    }

    /**
     * Extract total amount to pay
     */
    private function extractTotalAmount(string $text): ?float
    {
        $lines = explode("\n", $text);

        // Look for "TOTAL" line and the B/. amount
        foreach ($lines as $i => $line) {
            if (stripos($line, 'TOTAL') !== false && stripos($line, 'GRAN TOTAL') === false) {
                // Check this line and next 3 lines for the amount
                for ($j = $i; $j < min($i + 4, count($lines)); $j++) {
                    $currentLine = $lines[$j];

                    // Look for B/. pattern with amount
                    if (preg_match('/B\s*[\/\\\\]\s*\.\s*([0-9]+[.,][0-9]+)/', $currentLine, $matches)) {
                        $value = str_replace(',', '.', $matches[1]);
                        if ((float) $value > 0) {
                            return (float) $value;
                        }
                    }

                    // Alternative: look for pattern like "253,49" or "253.49"
                    if (preg_match('/([0-9]{2,3}[.,][0-9]{2})/', $currentLine, $matches)) {
                        $value = str_replace(',', '.', $matches[1]);
                        if ((float) $value > 0 && (float) $value < 10000) {
                            return (float) $value;
                        }
                    }
                }
            }
        }

        // Fallback: look for the amount after "DETALLE" or "CONCEPTO"
        foreach ($lines as $i => $line) {
            if (stripos($line, 'DETALLE') !== false || stripos($line, 'CONCEPTO') !== false) {
                // Look in the next 30 lines for the total
                for ($j = $i; $j < min($i + 30, count($lines)); $j++) {
                    if (stripos($lines[$j], 'TOTAL') !== false) {
                        // Check this and next 2 lines
                        for ($k = $j; $k < min($j + 3, count($lines)); $k++) {
                            if (preg_match('/([0-9]{2,3}[.,][0-9]{2})/', $lines[$k], $matches)) {
                                $value = str_replace(',', '.', $matches[1]);
                                if ((float) $value > 0 && (float) $value < 10000) {
                                    return (float) $value;
                                }
                            }
                        }
                    }
                }
            }
        }

        return null;
    }

    /**
     * Format date to YYYY-MM-DD
     */
    private function formatDate(string $day, string $month, string $year): string
    {
        $day = str_pad($day, 2, '0', STR_PAD_LEFT);
        $month = str_pad($month, 2, '0', STR_PAD_LEFT);

        return "$year-$month-$day";
    }
}
