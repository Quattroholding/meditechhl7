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
            $totalAmount = $this->extractTotalAmount($text);

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
                'total_amount' => $totalAmount,
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
        // Pattern: "Sr(a):" followed by name (usually on next line or same line)
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
        // Pattern: "Dir:" followed by address
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

        // Pattern: "Desde: 08-Mar-2023" and "Hasta: 08-Abr-2023"
        if (preg_match('/Desde\s*:\s*(\d{2}-[A-Z][a-z]{2}-\d{4})/i', $text, $matches)) {
            $period['start'] = $this->parseIdaanDate($matches[1]);
        }

        if (preg_match('/Hasta\s*:\s*(\d{2}-[A-Z][a-z]{2}-\d{4})/i', $text, $matches)) {
            $period['end'] = $this->parseIdaanDate($matches[1]);
        }

        return $period;
    }

    /**
     * Extract issue date (Fecha de Emisión)
     */
    private function extractIssueDate(string $text): ?string
    {
        // Pattern: "Fecha de Emisión" followed by date (with or without colon)
        if (preg_match('/Fecha\s+de\s+Emisión\s*:?\s*(\d{2}-[A-Z][a-z]{2}-\d{4})/i', $text, $matches)) {
            return $this->parseIdaanDate($matches[1]);
        }

        return null;
    }

    /**
     * Extract due date (Fecha de Vencimiento)
     */
    private function extractDueDate(string $text): ?string
    {
        // Pattern: "Fecha de Vencimiento" with date (formats like "15May-2023" or "15May--2023")
        if (preg_match('/Fecha\s+de\s+Vencimiento\s*:?\s*(\d{2})([A-Za-z]{3})-*-?(\d{4})/i', $text, $matches)) {
            $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
            $monthName = $matches[2];
            $year = $matches[3];
            $month = $this->monthNameToNumber($monthName);

            if ($month) {
                return $year.'-'.$month.'-'.$day;
            }
        }

        // Alternative pattern: standard "14-Abr-2023" format
        if (preg_match('/Fecha\s+de\s+Vencimiento\s*:?\s*(\d{2}-[A-Za-z]+(-|--)\d{4})/i', $text, $matches)) {
            $date = preg_replace('/--/', '-', $matches[1]);

            return $this->parseIdaanDate($date);
        }

        // Fallback: look for "15 DE MAYO DEL 2023" format
        if (preg_match('/(\d{2})\s+DE\s+([A-Z][A-Za-z]+)\s+DEL\s+(\d{4})/i', $text, $matches)) {
            $monthName = $matches[2];
            $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
            $year = $matches[3];
            $monthNum = $this->monthNameToNumber($monthName);

            return $monthNum ? $year.'-'.$monthNum.'-'.$day : null;
        }

        return null;
    }

    /**
     * Extract consumption in m3
     */
    private function extractConsumption(string $text): ?float
    {
        // Pattern: "Consumo Total" or "CONSUMO TOTAL" followed by (M3) and a number
        if (preg_match('/Consumo\s+Total\s*\([^)]*M3[^)]*\)\s*:?\s*([0-9.]+)/i', $text, $matches)) {
            return (float) $matches[1];
        }

        // Fallback: look for a number near "M3" or "(M3)"
        if (preg_match('/([0-9]+[.,][0-9]+|[0-9]+)\s*\(?\s*M3?\s*\)?/i', $text, $matches)) {
            $value = str_replace(',', '.', $matches[1]);

            return (float) $value;
        }

        // Last resort: look for consumption value in "CONSUMO DE AGUA" section
        if (preg_match('/CONSUMO\s+DE\s+AGUA\s*:?\s*([0-9]+[.,][0-9]+|[0-9]+)\s*(?!días)/i', $text, $matches)) {
            $value = str_replace(',', '.', $matches[1]);

            return (float) $value;
        }

        return null;
    }

    /**
     * Extract water charge (CONSUMO DE AGUA)
     */
    private function extractWaterCharge(string $text): ?float
    {
        // Pattern: "CONSUMO DE AGUA" with amount in B/.
        if (preg_match('/CONSUMO\s+DE\s+AGUA\s*[:\s]+B?\.?\s*([0-9]+[.,][0-9]+|[0-9]+)/i', $text, $matches)) {
            $value = str_replace(',', '.', $matches[1]);

            return (float) $value;
        }

        return null;
    }

    /**
     * Extract sewer charge (ALCANTARILLADO)
     */
    private function extractSewerCharge(string $text): ?float
    {
        // Pattern: "ALCANTARILLADO" with amount in B/.
        if (preg_match('/ALCANTARILLADO[^0-9]*[:\s]+B?\.?\s*([0-9]+[.,][0-9]+|[0-9]+)/i', $text, $matches)) {
            $value = str_replace(',', '.', $matches[1]);

            return (float) $value;
        }

        return null;
    }

    /**
     * Extract total amount (SALDO A PAGAR or TOTAL FACTURADO)
     */
    private function extractTotalAmount(string $text): ?float
    {
        // Pattern: "SALDO A PAGAR IDAAN B/." with amount
        if (preg_match('/SALDO\s+A\s+PAGAR\s+[^0-9]*[:\s]+B?\.?\s*([0-9]+[.,][0-9]+|[0-9]+)/i', $text, $matches)) {
            $value = str_replace(',', '.', $matches[1]);

            return (float) $value;
        }

        // Fallback: "TOTAL FACTURADO IDAAN"
        if (preg_match('/TOTAL\s+FACTURADO\s+IDAAN\s*[:\s]+B?\.?\s*([0-9]+[.,][0-9]+|[0-9]+)/i', $text, $matches)) {
            $value = str_replace(',', '.', $matches[1]);

            return (float) $value;
        }

        // Last resort: look for total after "TOTAL FACTURACIÓN TERCEROS"
        if (preg_match('/TOTAL\s+FACTURACIÓN\s+TERCEROS\s*[:\s]+B?\.?\s*([0-9]+[.,][0-9]+|[0-9]+)/i', $text, $matches)) {
            $value = str_replace(',', '.', $matches[1]);

            return (float) $value;
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
