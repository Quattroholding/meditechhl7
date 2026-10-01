<?php

namespace App\Services\DocumentParsers;

class IdaanBillParser extends BaseDocumentParser
{
    /**
     * Parse Google Document AI response for IDAAN water bill (Panama)
     *
     * IDAAN (Instituto de Acueductos y Alcantarillados Nacionales) bills
     * have a standardized format in Panama
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
            $clientNumber = $this->extractClientNumber($text);
            $customerName = $this->extractCustomerName($text);
            $address = $this->extractAddress($text);
            $period = $this->extractBillingPeriod($text);
            $issueDate = $this->extractIssueDate($text);
            $dueDate = $this->extractDueDate($text);
            $consumption = $this->extractConsumption($text);
            $waterCharge = $this->extractWaterCharge($text);
            $sewerCharge = $this->extractSewerCharge($text);
            $totalIdaan = $this->extractTotalIdaan($text);
            $totalAseo = $this->extractTotalAseo($text);

            // Validate key fields
            if (! $billNumber) {
                $this->addWarning('No se encontró número de factura');
            }
            if (! $clientNumber) {
                $this->addWarning('No se encontró número de cliente');
            }
            if (! $consumption) {
                $this->addWarning('No se encontró consumo en m3');
            }

            // Add confidence scores
            $this->addConfidenceScore(0.95); // Bill number
            $this->addConfidenceScore(0.95); // Client number
            $this->addConfidenceScore(0.90); // Customer name
            $this->addConfidenceScore(0.92); // Consumption
            $this->addConfidenceScore(0.93); // Total amount

            return $this->getResultWithMetadata($googleAIResponse, [
                'items' => [],
                'bill_number' => $billNumber,
                'client_number' => $clientNumber,
                'customer_name' => $customerName,
                'service_address' => $address,
                'billing_period' => $period,
                'billing_period_start' => $period['start'] ?? null,
                'billing_period_end' => $period['end'] ?? null,
                'issue_date' => $issueDate,
                'due_date' => $dueDate,
                'consumption_m3' => $consumption,
                'water_charge' => $waterCharge,
                'sewer_charge' => $sewerCharge,
                'total_amount' => $totalIdaan,
                'total_idaan' => $totalIdaan,
                'total_aseo' => $totalAseo,
            ]);

        } catch (\Exception $e) {
            $this->addError('Error procesando factura de agua: '.$e->getMessage());

            return $this->getResultWithMetadata($googleAIResponse, [
                'items' => [],
            ]);
        }
    }

    /**
     * Extract bill number (No. DE FACTURA)
     */
    private function extractBillNumber(string $text): ?string
    {
        // Pattern: "No. DE FACTURA: 98612562"
        if (preg_match('/No\.\s+DE\s+FACTURA\s*:\s*([0-9]{6,15})/i', $text, $matches)) {
            return $matches[1];
        }

        // Fallback: look for bill number in barcode format
        if (preg_match('/FAC([0-9]{6,15})/i', $text, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Extract client number (No. DE CLIENTE)
     */
    private function extractClientNumber(string $text): ?string
    {
        // Pattern: "No. DE CLIENTE: 908638"
        if (preg_match('/No\.\s+DE\s+CLIENTE\s*:\s*([0-9]{6,15})/i', $text, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Extract customer name
     */
    private function extractCustomerName(string $text): ?string
    {
        // Pattern: "Sr(a):" followed by name on next line or same line
        if (preg_match('/Sr\s*\(\s*a\s*\)\s*:\s*\n\s*([^\n]+)/i', $text, $matches)) {
            $name = trim($matches[1]);
            // Remove trailing colons or common artifacts
            $name = preg_replace('/[\s:;]+$/', '', $name);

            return $name ?: null;
        }

        // Fallback: name on same line
        if (preg_match('/Sr\s*\(\s*a\s*\)\s*:\s*([^\n]+)/i', $text, $matches)) {
            $name = trim($matches[1]);
            // Remove trailing colons or common artifacts
            $name = preg_replace('/[\s:;]+$/', '', $name);

            return $name ?: null;
        }

        return null;
    }

    /**
     * Extract service address
     */
    private function extractAddress(string $text): ?string
    {
        // Pattern: "Ref:" followed by address on next lines
        if (preg_match('/Ref\s*:\s*\n\s*([^\n]+)/i', $text, $matches)) {
            $address = trim($matches[1]);
            // Remove trailing artifacts
            $address = preg_replace('/[\s:;]+$/', '', $address);

            return $address ?: null;
        }

        // Fallback: "Dir:" followed by address
        if (preg_match('/Dir\s*:\s*([^\n]+)/i', $text, $matches)) {
            $address = trim($matches[1]);
            // Remove trailing artifacts
            $address = preg_replace('/[\s:;]+$/', '', $address);

            return $address ?: null;
        }

        return null;
    }

    /**
     * Extract billing period (Desde - Hasta)
     */
    private function extractBillingPeriod(string $text): array
    {
        $period = [
            'start' => null,
            'end' => null,
        ];

        $lines = explode("\n", $text);

        foreach ($lines as $i => $line) {
            if (stripos($line, 'Desde') !== false) {
                // Check this line and next 2 for date
                for ($j = $i; $j < min($i + 3, count($lines)); $j++) {
                    if (preg_match('/(\d{2})-([A-Za-z]+)-(\d{4})/', $lines[$j], $matches)) {
                        $day = $matches[1];
                        $monthName = $matches[2];
                        $year = $matches[3];
                        $month = $this->monthNameToNumber($monthName);
                        if ($month) {
                            $period['start'] = $year.'-'.$month.'-'.$day;
                            break 2;
                        }
                    }
                }
            }
        }

        foreach ($lines as $i => $line) {
            if (stripos($line, 'Hasta') !== false) {
                // Check this line and next 2 for date
                for ($j = $i; $j < min($i + 3, count($lines)); $j++) {
                    if (preg_match('/(\d{2})-([A-Za-z]+)-(\d{4})/', $lines[$j], $matches)) {
                        $day = $matches[1];
                        $monthName = $matches[2];
                        $year = $matches[3];
                        $month = $this->monthNameToNumber($monthName);
                        if ($month) {
                            $period['end'] = $year.'-'.$month.'-'.$day;
                            break 2;
                        }
                    }
                }
            }
        }

        return $period;
    }

    /**
     * Extract issue date (Fecha de Emisión)
     */
    private function extractIssueDate(string $text): ?string
    {
        $lines = explode("\n", $text);

        foreach ($lines as $i => $line) {
            if (stripos($line, 'Fecha') !== false && stripos($line, 'Emisi') !== false) {
                // Check next lines for date
                for ($j = $i; $j < min($i + 3, count($lines)); $j++) {
                    if (preg_match('/(\d{2})-([A-Za-z]+)-(\d{4})/', $lines[$j], $matches)) {
                        $day = $matches[1];
                        $monthName = $matches[2];
                        $year = $matches[3];
                        $month = $this->monthNameToNumber($monthName);
                        if ($month) {
                            return $year.'-'.$month.'-'.$day;
                        }
                    }
                }
            }
        }

        return null;
    }

    /**
     * Extract due date (Fecha de Vencimiento)
     */
    private function extractDueDate(string $text): ?string
    {
        $lines = explode("\n", $text);

        foreach ($lines as $i => $line) {
            if (stripos($line, 'Fecha') !== false && stripos($line, 'Vencimiento') !== false) {
                // Check next lines for date
                for ($j = $i; $j < min($i + 3, count($lines)); $j++) {
                    if (preg_match('/(\d{2})-([A-Za-z]+)-(\d{4})/', $lines[$j], $matches)) {
                        $day = $matches[1];
                        $monthName = $matches[2];
                        $year = $matches[3];
                        $month = $this->monthNameToNumber($monthName);
                        if ($month) {
                            return $year.'-'.$month.'-'.$day;
                        }
                    }
                }
            }
        }

        return null;
    }

    /**
     * Extract consumption in m3
     */
    private function extractConsumption(string $text): ?float
    {
        $lines = explode("\n", $text);

        // Look for "Consumo Total" line
        foreach ($lines as $i => $line) {
            if (stripos($line, 'Consumo') !== false && stripos($line, 'Total') !== false) {
                // Check this line and next 3 lines for a number
                for ($j = $i; $j < min($i + 4, count($lines)); $j++) {
                    if (preg_match('/([0-9]+[.,][0-9]+|[0-9]+)/', $lines[$j], $matches)) {
                        // Skip if this looks like a date or measurement unit
                        if (! preg_match('/\d{2}.*\d{4}/', $lines[$j]) && ! preg_match('/kWh|kwh/i', $lines[$j])) {
                            $value = str_replace(',', '.', $matches[1]);

                            return (float) $value;
                        }
                    }
                }
            }
        }

        // Look for M3 indicator
        foreach ($lines as $i => $line) {
            if (stripos($line, 'M3') !== false) {
                // Check previous and next lines for number
                for ($j = max(0, $i - 1); $j < min($i + 2, count($lines)); $j++) {
                    if (preg_match('/([0-9]+[.,][0-9]+|[0-9]+)/', $lines[$j], $matches)) {
                        $value = str_replace(',', '.', $matches[1]);

                        return (float) $value;
                    }
                }
            }
        }

        return null;
    }

    /**
     * Extract water charge (CONSUMO DE AGUA)
     */
    private function extractWaterCharge(string $text): ?float
    {
        // Split text into lines for easier parsing
        $lines = explode("\n", $text);

        // Look for CONSUMO DE AGUA line
        foreach ($lines as $i => $line) {
            if (stripos($line, 'CONSUMO') !== false && stripos($line, 'AGUA') !== false) {
                // Check next 3 lines for the amount
                for ($j = $i + 1; $j < min($i + 4, count($lines)); $j++) {
                    if (preg_match('/([0-9]+[.,][0-9]+|[0-9]+)/', $lines[$j], $matches)) {
                        $value = str_replace(',', '.', $matches[1]);

                        return (float) $value;
                    }
                }
            }
        }

        return null;
    }

    /**
     * Extract sewer charge (ALCANTARILLADO)
     */
    private function extractSewerCharge(string $text): ?float
    {
        // Split text into lines for easier parsing
        $lines = explode("\n", $text);

        // Look for ALCANTARILLADO line
        foreach ($lines as $i => $line) {
            if (stripos($line, 'ALCANTARILLADO') !== false) {
                // Check next 3 lines for the amount
                for ($j = $i + 1; $j < min($i + 4, count($lines)); $j++) {
                    if (preg_match('/([0-9]+[.,][0-9]+|[0-9]+)/', $lines[$j], $matches)) {
                        $value = str_replace(',', '.', $matches[1]);

                        return (float) $value;
                    }
                }
            }
        }

        return null;
    }

    /**
     * Extract total amount for IDAAN (SALDO A PAGAR IDAAN)
     */
    private function extractTotalIdaan(string $text): ?float
    {
        $lines = explode("\n", $text);

        // Look for "SALDO A PAGAR IDAAN" specifically (not ASEO)
        foreach ($lines as $i => $line) {
            if (stripos($line, 'SALDO') !== false && stripos($line, 'PAGAR') !== false && stripos($line, 'IDAAN') !== false && stripos($line, 'ASEO') === false) {
                // Check this line and next 4 lines for the amount (barcode and other text may be in between)
                for ($j = $i; $j < min($i + 5, count($lines)); $j++) {
                    if (preg_match('/([0-9]+[.,][0-9]+|[0-9]+)/', $lines[$j], $matches)) {
                        $value = str_replace(',', '.', $matches[1]);
                        // Skip if this looks like a barcode or year
                        if (! preg_match('/^[A-Z0-9]{20,}$/', $lines[$j]) && ! preg_match('/^\d{4}$/', $value)) {
                            if ((float) $value > 0) {
                                return (float) $value;
                            }
                        }
                    }
                }
            }
        }

        return null;
    }

    /**
     * Extract total amount for ASEO (SALDO A PAGAR ASEO)
     */
    private function extractTotalAseo(string $text): ?float
    {
        $lines = explode("\n", $text);

        // Look for "SALDO A PAGAR ASEO" specifically
        foreach ($lines as $i => $line) {
            if (stripos($line, 'SALDO') !== false && stripos($line, 'PAGAR') !== false && stripos($line, 'ASEO') !== false) {
                // Check this line and next 4 lines for the amount (barcode and other text may be in between)
                for ($j = $i; $j < min($i + 5, count($lines)); $j++) {
                    if (preg_match('/([0-9]+[.,][0-9]+|[0-9]+)/', $lines[$j], $matches)) {
                        $value = str_replace(',', '.', $matches[1]);
                        // Skip if this looks like a barcode or year
                        if (! preg_match('/^[A-Z0-9]{20,}$/', $lines[$j]) && ! preg_match('/^\d{4}$/', $value)) {
                            if ((float) $value > 0) {
                                return (float) $value;
                            }
                        }
                    }
                }
            }
        }

        return null;
    }

    /**
     * Parse IDAAN date format: "08-Mar-2023" to "2023-03-08"
     */
    private function parseIdaanDate(string $dateStr): ?string
    {
        $dateStr = trim($dateStr);
        // Fix double dashes
        $dateStr = preg_replace('/--+/', '-', $dateStr);

        // Pattern: DD-MMM-YYYY or DD-MM-YYYY (flexible on month format)
        if (preg_match('/^(\d{2})-([A-Za-z]+)-(\d{4})$/i', $dateStr, $matches)) {
            $day = $matches[1];
            $month = $this->monthNameToNumber($matches[2]);
            $year = $matches[3];

            if ($month) {
                return $year.'-'.$month.'-'.$day;
            }
        }

        return null;
    }

    /**
     * Convert month name to number
     */
    private function monthNameToNumber(string $monthName): ?string
    {
        $months = [
            // Spanish
            'ene' => '01', 'enero' => '01',
            'feb' => '02', 'febrero' => '02',
            'mar' => '03', 'marzo' => '03',
            'abr' => '04', 'abril' => '04',
            'may' => '05', 'mayo' => '05',
            'jun' => '06', 'junio' => '06',
            'jul' => '07', 'julio' => '07',
            'ago' => '08', 'agosto' => '08',
            'sep' => '09', 'sept' => '09', 'septiembre' => '09',
            'oct' => '10', 'octubre' => '10',
            'nov' => '11', 'noviembre' => '11',
            'dic' => '12', 'diciembre' => '12',
            // English
            'jan' => '01', 'january' => '01',
            'feb' => '02', 'february' => '02',
            'mar' => '03', 'march' => '03',
            'apr' => '04', 'april' => '04',
            'may' => '05',
            'jun' => '06', 'june' => '06',
            'jul' => '07', 'july' => '07',
            'aug' => '08', 'august' => '08',
            'sep' => '09', 'sept' => '09', 'september' => '09',
            'oct' => '10', 'october' => '10',
            'nov' => '11', 'november' => '11',
            'dec' => '12', 'december' => '12',
        ];

        $lowerMonth = strtolower(trim($monthName));

        // Exact match first
        if (isset($months[$lowerMonth])) {
            return $months[$lowerMonth];
        }

        // Try first 3 characters
        $shortMonth = substr($lowerMonth, 0, 3);
        if (isset($months[$shortMonth])) {
            return $months[$shortMonth];
        }

        return null;
    }
}
