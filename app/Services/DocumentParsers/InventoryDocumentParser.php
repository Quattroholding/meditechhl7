<?php

namespace App\Services\DocumentParsers;

class InventoryDocumentParser extends BaseDocumentParser
{
    private string $detectedFormat = 'standard';

    private array $batchInfo = [];

    private array $additionalFields = [];

    /**
     * Parse Google Document AI response for inventory PDF
     *
     * Expected structure: Document with tables containing inventory data
     * Expected columns: SKU, Name, Quantity, Unit Cost, Category (optional), Unit (optional)
     */
    public function parse(array $googleAIResponse): array
    {
        $this->reset();

        try {
            // Detect the invoice format
            $this->detectedFormat = $this->detectFormat($googleAIResponse);

            // Extract based on detected format
            if ($this->detectedFormat === 'impaduel') {
                $this->extractFromImpaduelFormat($googleAIResponse);
            } else {
                // Standard format (reprico, haseth, etc.)
                $this->extractFromStandardFormat($googleAIResponse);
            }

            // Check for duplicate SKUs
            $this->checkDuplicateSKUs();

        } catch (\Exception $e) {
            $this->addError('Error procesando PDF: '.$e->getMessage());
        }

        // Calculate totals
        $subtotal = 0.0;
        $totalTax = 0.0;
        foreach ($this->items as $item) {
            $quantity = (float) ($item['quantity'] ?? 0);
            $unitCost = (float) ($item['unit_cost'] ?? 0);
            $discount = (float) ($item['discount'] ?? 0);
            $lineTotal = ($quantity * $unitCost) - $discount;
            $subtotal += $lineTotal;
            $totalTax += (float) ($item['tax'] ?? 0);
        }

        // Extract invoice metadata
        $invoiceNumber = $this->extractInvoiceNumber($googleAIResponse);
        $invoiceDate = $this->extractInvoiceDate($googleAIResponse);

        // Return result with metadata
        return $this->getResultWithMetadata($googleAIResponse, [
            'detected_format' => $this->detectedFormat,
            'batch_info' => $this->batchInfo,
            'additional_fields' => $this->additionalFields,
            'subtotal' => $subtotal,
            'total_tax' => $totalTax,
            'total' => $subtotal + $totalTax,
            'invoice_number' => $invoiceNumber,
            'invoice_date' => $invoiceDate,
        ]);
    }

    /**
     * Detect invoice format from text patterns
     */
    private function detectFormat(array $googleAIResponse): string
    {
        $text = $googleAIResponse['document']['text'] ?? '';

        // Impaduel format: has "Lote y Fvenc" or batch information
        if (strpos($text, 'Lote y Fvenc') !== false) {
            return 'impaduel';
        }

        // Standard format (reprico, haseth, etc.)
        return 'standard';
    }

    /**
     * Extract inventory items from standard invoice format (reprico, haseth, etc.)
     */
    private function extractFromStandardFormat(array $googleAIResponse): void
    {
        // Extract tables first
        $tables = $this->extractTables($googleAIResponse);

        if (! empty($tables)) {
            $itemsFound = 0;

            // Process each table
            foreach ($tables as $tableIndex => $table) {
                $this->processTable($table, $tableIndex);
                $itemsFound = count($this->items);
            }

            // If no items were found from tables, try text extraction as fallback
            if ($itemsFound === 0) {
                $this->extractFromText($googleAIResponse);
            }
        } else {
            // Fallback: try extracting from text field (for text-based invoices)
            $this->extractFromText($googleAIResponse);
        }
    }

    /**
     * Extract inventory items from Impaduel invoice format
     * This format has:
     * - SKU - Product Name (can span multiple lines)
     * - Quantity, Unit, Cost, Discount, Total, Tax, Value
     * - Batch/Lot information on separate lines
     */
    private function extractFromImpaduelFormat(array $googleAIResponse): void
    {
        $text = $googleAIResponse['document']['text'] ?? '';

        if (empty($text)) {
            $this->addWarning('No se encontró texto extraíble en el documento');

            return;
        }

        $lines = array_filter(array_map('trim', explode("\n", $text)));
        $lines = array_values($lines);

        $itemIndex = 0;

        for ($i = 0; $i < count($lines); $i++) {
            $line = $lines[$i];

            // Skip headers and footers
            if ($this->isHeaderOrFooterLine($line)) {
                continue;
            }

            // Look for SKU - Product Name pattern (e.g., "001-470242 - WELLBUTRIN XL TAB LIB")
            if ($this->containsSKUAndName($line)) {
                $rowData = [];

                // Extract SKU and initial name
                [$sku, $name] = $this->extractSKUAndName($line);
                $rowData['sku'] = $sku;
                $rowData['name'] = $name;

                // Collect additional name lines
                $nameStart = $i + 1;
                while ($nameStart < count($lines)) {
                    $checkLine = $lines[$nameStart];

                    // Stop at numeric line or if next product is found
                    if ($this->isNumericLine($checkLine) || $this->containsSKUAndName($checkLine)) {
                        break;
                    }

                    // Add to name if not batch info
                    if (! $this->isBatchInfoLine($checkLine)) {
                        $rowData['name'] .= ' '.$checkLine;
                        $nameStart++;

                        if ($nameStart - ($i + 1) >= 2) {
                            break;
                        }
                    } else {
                        break;
                    }
                }

                $i = $nameStart - 1;

                // Extract numeric fields (quantity, unit, cost, discount, etc.)
                $numericValues = [];
                $batchLinesStart = $i + 1;

                while (isset($lines[$i + 1]) && count($numericValues) < 7) {
                    $nextLine = $lines[$i + 1];

                    // Stop if it's a batch info line or new product
                    if ($this->isBatchInfoLine($nextLine) || $this->containsSKUAndName($nextLine)) {
                        break;
                    }

                    if ($this->isNumericLine($nextLine)) {
                        $numericValues[] = $nextLine;
                        $i++;
                    } elseif ($this->isUnitLine($nextLine)) {
                        $rowData['unit'] = $nextLine;
                        $i++;
                    } else {
                        $i++;
                    }
                }

                // Map numeric values to fields
                // Impaduel format: cantidad, valor_unitario, descuento_unitario, monto, itbms, valor_item
                if (count($numericValues) >= 1) {
                    $rowData['quantity'] = $numericValues[0];
                }
                if (count($numericValues) >= 2) {
                    $rowData['unit_cost'] = $numericValues[1];
                }
                if (count($numericValues) >= 3) {
                    // Store discount per unit
                    $rowData['discount'] = $numericValues[2];
                }
                // numericValues[3] = monto (line total) - not needed, we calculate it
                // numericValues[4] = ITBMS (tax per item)
                if (count($numericValues) >= 5) {
                    $rowData['tax'] = $numericValues[4];
                }
                // numericValues[5] = valor_item (final value) - store for reference
                if (count($numericValues) >= 6) {
                    $this->additionalFields['valores_item'] = $this->additionalFields['valores_item'] ?? [];
                    $this->additionalFields['valores_item'][] = [
                        'item_index' => $itemIndex,
                        'valor_item' => $numericValues[5],
                    ];
                }

                // Capture batch information if present
                if (isset($lines[$i + 1]) && $this->isBatchInfoLine($lines[$i + 1])) {
                    $batchData = $this->extractBatchInfo($lines[$i + 1]);
                    if (! empty($batchData)) {
                        $this->batchInfo[] = array_merge($batchData, ['item_index' => $itemIndex]);
                        $i++;

                        // Check for additional batch lines (cantidad del lote)
                        if (isset($lines[$i + 1]) && preg_match('/^cant\s+(\d+)/i', $lines[$i + 1])) {
                            if (! empty($this->batchInfo)) {
                                $this->batchInfo[count($this->batchInfo) - 1]['cantidad_lote'] = (int) preg_replace('/[^\d]/', '', $lines[$i + 1]);
                            }
                            $i++;
                        }
                    }
                }

                // Process if valid
                if (! empty($rowData['sku']) && ! empty($rowData['name']) && ! empty($rowData['quantity'])) {
                    $itemIndex++;
                    $this->processTextRow($rowData, $itemIndex);
                }
            }
        }

        if (empty($this->items)) {
            $this->addWarning('No se encontraron productos en el documento');
        }
    }

    /**
     * Check if line contains batch/lot information
     */
    private function isBatchInfoLine(string $line): bool
    {
        $lowerLine = strtolower($line);

        return strpos($lowerLine, 'lote') !== false || strpos($lowerLine, 'fvenc') !== false;
    }

    /**
     * Extract batch information from a line like "Lote y Fvenc.WE7G 30-11-2025"
     */
    private function extractBatchInfo(string $line): array
    {
        $result = [];

        // Pattern: Lote y Fvenc.XXXXX DD-MM-YYYY or similar
        if (preg_match('/lote\s+y\s+fvenc[\.:]?\s*([A-Z0-9]+)\s+(\d{2}-\d{2}-\d{4})/i', $line, $matches)) {
            $result['lote'] = $matches[1];
            $result['vencimiento'] = $matches[2];
        } elseif (preg_match('/([A-Z0-9]+)\s+(\d{2}-\d{2}-\d{4})/i', $line, $matches)) {
            $result['lote'] = $matches[1];
            $result['vencimiento'] = $matches[2];
        }

        return $result;
    }

    /**
     * Extract tables from Google Document AI response
     */
    private function extractTables(array $googleAIResponse): array
    {
        $tables = [];

        if (isset($googleAIResponse['document']['pages'])) {
            foreach ($googleAIResponse['document']['pages'] as $page) {
                if (isset($page['tables'])) {
                    foreach ($page['tables'] as $table) {
                        $tables[] = $table;
                    }
                }
            }
        }

        return $tables;
    }

    /**
     * Extract inventory items from text-based invoice (fallback for non-table PDFs)
     * Handles multiple formats:
     * 1. Simple: SKU - Product Name / Quantity / Unit / Cost
     * 2. Expanded table: SKU / Unit / Name (multi-line) / Qty / Price / Discount / Total / Tax / Value
     */
    private function extractFromText(array $googleAIResponse): void
    {
        $text = $googleAIResponse['document']['text'] ?? '';

        if (empty($text)) {
            $this->addWarning('No se encontraron tablas ni texto extraíble en el documento');

            return;
        }

        // Split text into lines and clean
        $lines = array_filter(array_map('trim', explode("\n", $text)));
        $lines = array_values($lines); // Re-index after filtering

        $itemIndex = 0;

        // Process text line by line, looking for patterns
        for ($i = 0; $i < count($lines); $i++) {
            $line = $lines[$i];

            // Skip common header/footer lines
            if ($this->isHeaderOrFooterLine($line)) {
                continue;
            }

            // Pattern 1: SKU - Product Name (can span multiple lines)
            if ($this->containsSKUAndName($line)) {
                $rowData = [];
                [$sku, $name] = $this->extractSKUAndName($line);
                $rowData['sku'] = $sku;
                $rowData['name'] = $name;

                // Collect additional name lines until we hit a numeric line
                $nameStart = $i + 1;
                while ($nameStart < count($lines)) {
                    $checkLine = $lines[$nameStart];

                    // Stop if we hit a numeric line or header
                    if ($this->isNumericLine($checkLine) || $this->isHeaderOrFooterLine($checkLine)) {
                        break;
                    }

                    // Stop if we hit what looks like a SKU-Name combination (next product)
                    if ($this->containsSKUAndName($checkLine)) {
                        break;
                    }

                    // Add to name
                    $rowData['name'] .= ' '.$checkLine;
                    $nameStart++;

                    // Don't collect more than 3 lines for name
                    if ($nameStart - ($i + 1) >= 3) {
                        break;
                    }
                }

                // Move to the last name line collected
                $i = $nameStart - 1;

                // Next line should be quantity
                if (isset($lines[$i + 1]) && $this->isNumericLine($lines[$i + 1])) {
                    $rowData['quantity'] = $lines[$i + 1];
                    $i++;
                }

                // Next line should be unit
                if (isset($lines[$i + 1]) && $this->isUnitLine($lines[$i + 1])) {
                    $rowData['unit'] = $lines[$i + 1];
                    $i++;
                }

                // Next line should be unit cost
                if (isset($lines[$i + 1]) && $this->isNumericLine($lines[$i + 1])) {
                    $rowData['unit_cost'] = $lines[$i + 1];
                    $i++;
                }

                if (! empty($rowData['sku']) && ! empty($rowData['name'])) {
                    $itemIndex++;
                    $this->processTextRow($rowData, $itemIndex);
                }
            }
            // Pattern 2: Expanded table format (invoice with detailed columns)
            // SKU is typically alphanumeric followed by optional hyphen, not a full line like "Cantidad" or "Descripción"
            elseif ($this->isProductCodeLine($line) && ! $this->isHeaderOrFooterLine($line)) {
                $rowData = [];
                $rowData['sku'] = $line;

                // Next line could be unit or product name
                if (isset($lines[$i + 1])) {
                    $next = $lines[$i + 1];

                    // Check if it's a unit
                    if ($this->isUnitLine($next)) {
                        $rowData['unit'] = $next;
                        $i++;
                        $next = $lines[$i + 1] ?? null;
                    }

                    // Collect product name (can span multiple lines until we hit a number)
                    $name = '';
                    $nameStart = $i + 1;
                    while ($nameStart < count($lines)) {
                        $checkLine = $lines[$nameStart];

                        // Stop if we hit a numeric line, header, or footer
                        if ($this->isNumericLine($checkLine) || $this->isHeaderOrFooterLine($checkLine)) {
                            break;
                        }

                        // Collect name
                        $name .= (! empty($name) ? ' ' : '').$checkLine;
                        $nameStart++;

                        // Stop after collecting 2-3 lines for name (reasonable limit)
                        if ($nameStart - ($i + 1) >= 3) {
                            break;
                        }
                    }

                    if (! empty($name)) {
                        $rowData['name'] = $name;
                        $i = $nameStart - 1;
                    }

                    // Next should be quantity
                    if (isset($lines[$i + 1]) && $this->isNumericLine($lines[$i + 1])) {
                        $rowData['quantity'] = $lines[$i + 1];
                        $i++;

                        // Next should be unit (optional)
                        if (isset($lines[$i + 1]) && $this->isUnitLine($lines[$i + 1])) {
                            $rowData['unit'] = $lines[$i + 1];
                            $i++;
                        }

                        // Next should be unit cost (first money line after quantity/unit)
                        if (isset($lines[$i + 1]) && $this->isMoneyLine($lines[$i + 1])) {
                            $rowData['unit_cost'] = $lines[$i + 1];
                            $i++;
                        }

                        // Skip remaining money columns (discount, monto, tax, value) - up to 5 more
                        // But stop if we encounter a product code line or header
                        $skipCount = 0;
                        while (isset($lines[$i + 1]) && $skipCount < 5) {
                            $nextLine = $lines[$i + 1];

                            // Stop if it's a new product code
                            if ($this->isProductCodeLine($nextLine)) {
                                break;
                            }

                            // Stop if it's a header/footer
                            if ($this->isHeaderOrFooterLine($nextLine)) {
                                break;
                            }

                            // If it's money, skip it
                            if ($this->isMoneyLine($nextLine)) {
                                $i++;
                                $skipCount++;
                            } else {
                                // If it's not money and not a product code, it might be batch info
                                // Skip it anyway to get past lote/batch lines
                                $i++;
                                $skipCount++;
                            }
                        }
                    }
                }

                // Process if we have minimum required fields
                if (! empty($rowData['sku']) && ! empty($rowData['name']) && ! empty($rowData['quantity'])) {
                    $itemIndex++;
                    $this->processTextRow($rowData, $itemIndex);
                }
            }
        }

        if (empty($this->items)) {
            $this->addWarning('No se encontraron productos en el texto del documento');
        }
    }

    /**
     * Check if line is likely a SKU - Name combination
     */
    private function containsSKUAndName(string $line): bool
    {
        // Look for pattern like "XXX-XXXXX - Product Name" or similar
        // SKU typically contains numbers and hyphens, followed by dash and product name
        return preg_match('/^[\w\-]+\s*-\s*.+/u', $line) && strlen($line) > 10;
    }

    /**
     * Extract SKU and Name from a line
     * Returns [$sku, $name]
     */
    private function extractSKUAndName(string $line): array
    {
        // Split on the first dash followed by space
        if (preg_match('/^([\w\-]+)\s*-\s*(.+)$/u', $line, $matches)) {
            return [
                trim($matches[1]),
                trim($matches[2]),
            ];
        }

        return ['', ''];
    }

    /**
     * Check if line is a numeric value (quantity or price)
     */
    private function isNumericLine(string $line): bool
    {
        // Remove common currency symbols and whitespace
        $cleaned = preg_replace('/[\s,]/', '', $line);

        return preg_match('/^[\d.,]+$/', $cleaned) && strlen($cleaned) > 0;
    }

    /**
     * Check if line is a money amount (like "$ 3.78" or "44.27")
     */
    private function isMoneyLine(string $line): bool
    {
        // Look for currency symbols or just numbers with decimals
        return preg_match('/^\s*[\$]?\s*[\d,\.]+\s*$/', $line);
    }

    /**
     * Check if line is likely a product code/SKU
     * Codes are typically short (5-20 chars), alphanumeric with at least one digit, may include hyphens
     * But NOT common words like "Cantidad", "Descripción", "Código", etc.
     */
    private function isProductCodeLine(string $line): bool
    {
        $lowerLine = strtolower($line);

        // Reject if it's a header/label
        $rejectWords = ['cantidad', 'descripción', 'código', 'nombre', 'valor', 'costo', 'precio', 'total', 'impuesto', 'descuento', 'monto', 'concepto', 'pagina', 'página', 'ruc', 'teléfono', 'dirección', 'receptor', 'emisor', 'factura', 'tipo', 'cufe', 'protocolo', 'fecha', 'unitario', 'impuestos', 'pago', 'consulte', 'resolución', 'itbms', 'item', 'lote', 'fvenc', 'cant'];

        foreach ($rejectWords as $word) {
            if (strpos($lowerLine, $word) !== false) {
                return false;
            }
        }

        // Product codes:
        // - Must be 3-30 characters
        // - Must contain at least one digit
        // - Can only contain letters, numbers, hyphens, dots
        // - Must NOT be all letters (must have digit)
        if (strlen($line) >= 3 && strlen($line) < 30
            && preg_match('/^[A-Z0-9\-\.]+$/i', $line)  // Only alphanumeric, hyphens, dots
            && preg_match('/\d/', $line)) {  // At least one digit
            return true;
        }

        return false;
    }

    /**
     * Check if line is a unit of measure
     */
    private function isUnitLine(string $line): bool
    {
        $lowerLine = strtolower($line);

        // Common units
        $units = ['und', 'unidad', 'caja', 'box', 'kg', 'l', 'ml', 'g', 'pz', 'pza', 'pack', 'lote', 'blister'];

        foreach ($units as $unit) {
            if ($lowerLine === $unit || strpos($lowerLine, $unit) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if line is a header or footer line to skip
     */
    private function isHeaderOrFooterLine(string $line): bool
    {
        $lowerLine = strtolower($line);

        // Common header/footer patterns
        $patterns = [
            'factura', 'invoice', 'número', 'number', 'fecha', 'date',
            'cliente', 'customer', 'empresa', 'company', 'total', 'subtotal',
            'impuesto', 'tax', 'iva', 'descuento', 'discount', 'condición',
            'plazo', 'pago', 'payment', 'términos', 'términos de pago',
            'página', 'page', 'de', 'de ', 'versión', 'version',
        ];

        foreach ($patterns as $pattern) {
            if (strpos($lowerLine, $pattern) !== false && strlen($line) < 50) {
                return true;
            }
        }

        return false;
    }

    /**
     * Process a row extracted from text
     */
    private function processTextRow(array $rowData, int $rowIndex): void
    {
        // Validate required fields
        if (! $this->validateRequiredField($rowData['sku'] ?? null, 'SKU', $rowIndex)) {
            return;
        }

        if (! $this->validateRequiredField($rowData['name'] ?? null, 'Nombre', $rowIndex)) {
            return;
        }

        $quantity = $this->validatePositiveNumber(
            $rowData['quantity'] ?? null,
            'Cantidad',
            $rowIndex,
            true
        );

        if ($quantity === null) {
            return;
        }

        $unitCost = $this->validatePositiveNumber(
            $rowData['unit_cost'] ?? null,
            'Costo Unitario',
            $rowIndex,
            false
        );

        if ($unitCost === null) {
            $this->addWarning("Fila {$rowIndex}: Costo unitario no detectado, se usará 0.00");
            $unitCost = 0.0;
        }

        $sku = trim($rowData['sku']);
        $name = trim($rowData['name']);

        // Clean name: remove single-letter unit prefixes at start (e.g., "U GLUCERNA..." -> "GLUCERNA...")
        $name = preg_replace('/^[A-Z]\s+/', '', $name);

        // Validate SKU format
        if (! $this->isValidSKUFormat($sku, $rowIndex)) {
            return;
        }

        // Add confidence scores (slightly lower than table-based since text parsing is less reliable)
        $this->addConfidenceScore(0.85); // Confidence for SKU
        $this->addConfidenceScore(0.88); // Confidence for Name
        $this->addConfidenceScore(0.92); // Confidence for Quantity
        $this->addConfidenceScore($unitCost !== null ? 0.80 : 0.60); // Lower confidence if cost missing

        // Create item
        $item = [
            'sku' => $sku,
            'name' => $name,
            'quantity' => (int) $quantity,
            'unit_cost' => $unitCost ?? 0.0,
        ];

        // Add optional fields
        if (! empty($rowData['unit'] ?? null)) {
            $item['unit'] = trim($rowData['unit']);
        }

        // Add discount if present
        if (! empty($rowData['discount'] ?? null)) {
            $discount = $this->validateNumericField($rowData['discount'], 'Descuento', $rowIndex, false);
            if ($discount !== null) {
                $item['discount'] = $discount;
            }
        }

        // Add tax if present
        if (! empty($rowData['tax'] ?? null)) {
            $tax = $this->validateNumericField($rowData['tax'], 'Impuesto', $rowIndex, false);
            if ($tax !== null) {
                $item['tax'] = $tax;
            }
        }

        $this->items[] = $item;
    }

    /**
     * Process a single table from the PDF
     */
    private function processTable(array $table, int $tableIndex): void
    {
        if (! isset($table['body'])) {
            $this->addWarning("Tabla {$tableIndex}: No tiene datos");

            return;
        }

        $rows = $table['body'];
        $headers = $this->extractHeaders($rows[0] ?? []);

        // Skip header row
        $startIndex = 1;

        for ($i = $startIndex; $i < count($rows); $i++) {
            $rowData = $this->extractRowData($rows[$i], $headers);

            // Skip empty rows
            if (empty(array_filter($rowData))) {
                continue;
            }

            $this->processRow($rowData, $i + 1); // +1 for display (1-indexed)
        }
    }

    /**
     * Extract headers from first row
     */
    private function extractHeaders(array $headerRow): array
    {
        $headers = [];

        foreach ($headerRow['cells'] ?? [] as $cell) {
            $text = $this->extractCellText($cell);
            $headers[] = strtolower(trim($text));
        }

        return $headers;
    }

    /**
     * Extract row data from cells
     */
    private function extractRowData(array $row, array $headers): array
    {
        $rowData = [];

        foreach ($row['cells'] ?? [] as $index => $cell) {
            $text = $this->extractCellText($cell);
            $header = $headers[$index] ?? "field_{$index}";
            $rowData[$header] = $text;
        }

        return $rowData;
    }

    /**
     * Extract text from a cell (handles nested structure)
     */
    private function extractCellText(array $cell): string
    {
        $text = '';

        if (isset($cell['layout'])) {
            foreach ($cell['layout']['textAnchor']['textSegments'] ?? [] as $segment) {
                $text .= $segment['startIndex'] ?? '';
            }
        }

        // Alternative: use normalized_text if available
        if (isset($cell['normalizedText'])) {
            $text = $cell['normalizedText'];
        }

        // Fallback: extract from Document text
        if (empty($text) && isset($cell['layout']['textAnchor'])) {
            // This would require access to the full document text
            $text = '';
        }

        return trim($text);
    }

    /**
     * Process a single row of inventory data
     */
    private function processRow(array $rowData, int $rowIndex): void
    {
        // Find SKU column (common variations)
        $skuField = $this->findField($rowData, ['sku', 'code', 'codigo', 'producto', 'product code']);
        $nameField = $this->findField($rowData, ['name', 'descripcion', 'nombre', 'description', 'item']);
        $quantityField = $this->findField($rowData, ['quantity', 'cantidad', 'qty', 'cant']);
        $costField = $this->findField($rowData, ['cost', 'costo', 'unit cost', 'costo unitario', 'unit price', 'precio']);
        $categoryField = $this->findField($rowData, ['category', 'categoria', 'type', 'tipo']);
        $unitField = $this->findField($rowData, ['unit', 'unidad', 'uom', 'unidad de medida']);

        // Validate required fields
        if (! $this->validateRequiredField($rowData[$skuField] ?? null, 'SKU', $rowIndex)) {
            return;
        }

        if (! $this->validateRequiredField($rowData[$nameField] ?? null, 'Nombre', $rowIndex)) {
            return;
        }

        $quantity = $this->validatePositiveNumber(
            $rowData[$quantityField] ?? null,
            'Cantidad',
            $rowIndex,
            true
        );

        if ($quantity === null) {
            return;
        }

        $unitCost = $this->validatePositiveNumber(
            $rowData[$costField] ?? null,
            'Costo Unitario',
            $rowIndex,
            false
        );

        if ($unitCost === null && isset($costField)) {
            $this->addWarning("Fila {$rowIndex}: Costo unitario no detectado, se usará 0.00");
            $unitCost = 0.0;
        }

        $sku = trim($rowData[$skuField]);
        $name = trim($rowData[$nameField]);

        // Validate SKU format
        if (! $this->isValidSKUFormat($sku, $rowIndex)) {
            return;
        }

        // Add confidence scores (simulate confidence from Google AI)
        $this->addConfidenceScore(0.95); // Confidence for SKU
        $this->addConfidenceScore(0.92); // Confidence for Name
        $this->addConfidenceScore(0.98); // Confidence for Quantity
        $this->addConfidenceScore($unitCost !== null ? 0.90 : 0.70); // Lower confidence if cost missing

        // Create item
        $item = [
            'sku' => $sku,
            'name' => $name,
            'quantity' => (int) $quantity,
            'unit_cost' => $unitCost ?? 0.0,
        ];

        // Add optional fields
        if (! empty($rowData[$categoryField] ?? null)) {
            $item['category'] = trim($rowData[$categoryField]);
        }

        if (! empty($rowData[$unitField] ?? null)) {
            $item['unit'] = trim($rowData[$unitField]);
        }

        $this->items[] = $item;
    }

    /**
     * Find column header by matching variations
     */
    private function findField(array $rowData, array $possibleNames): ?string
    {
        foreach ($possibleNames as $name) {
            $lowerName = strtolower($name);
            foreach (array_keys($rowData) as $key) {
                if (strtolower($key) === $lowerName || stripos($key, $lowerName) !== false) {
                    return $key;
                }
            }
        }

        return null;
    }

    /**
     * Validate SKU format
     */
    private function isValidSKUFormat(string $sku, int $rowIndex): bool
    {
        if (strlen($sku) > 100) {
            $this->addError("Fila {$rowIndex}: SKU muy largo (máximo 100 caracteres)");

            return false;
        }

        // Check for suspicious characters
        if (preg_match('/[<>{}|\\\\^`]/', $sku)) {
            $this->addWarning("Fila {$rowIndex}: SKU contiene caracteres especiales, podría causar problemas");
        }

        return true;
    }

    /**
     * Check for duplicate SKUs
     */
    private function checkDuplicateSKUs(): void
    {
        $skus = array_column($this->items, 'sku');
        $duplicates = array_diff_assoc($skus, array_unique($skus));

        if (! empty($duplicates)) {
            foreach (array_unique($duplicates) as $duplicate) {
                $this->addWarning("SKU duplicado en el documento: {$duplicate}");
            }
        }
    }

    /**
     * Extract invoice/factura number from OCR response
     */
    private function extractInvoiceNumber(array $googleAIResponse): ?string
    {
        $text = $googleAIResponse['document']['text'] ?? '';

        if (empty($text)) {
            return null;
        }

        // Patterns for invoice numbers - be more specific to avoid false matches
        $patterns = [
            // "Número: 0000005614" or "No: 0000005614"
            '/(?:n[úu]mero|no\.?)\s*[:=]?\s*([0-9]{4,15})/i',
            // "Factura No: 123456" or "Factura No 123456"
            '/factura\s+(?:no\.?|#)\s*[:=]?\s*([A-Z0-9]{3,20})/i',
            // "Invoice #123456" or "Invoice: 123456"
            '/invoice\s+#?\s*[:=]?\s*([A-Z0-9]{3,20})/i',
            // "No. Factura: 123456" or "Nro. Factura: 123456"
            '/(?:no\.?|nro\.?)\s+(?:de\s+)?factura\s*[:=]?\s*([A-Z0-9]{3,20})/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $number = trim($matches[1]);
                // Reject if it's just a single letter or too short
                if (strlen($number) > 2) {
                    return $number;
                }
            }
        }

        return null;
    }

    /**
     * Extract invoice date from OCR response
     */
    private function extractInvoiceDate(array $googleAIResponse): ?string
    {
        $text = $googleAIResponse['document']['text'] ?? '';

        if (empty($text)) {
            return null;
        }

        // Patterns for dates - be specific to avoid future dates or invalid dates
        $datePatterns = [
            // "Fecha de emisión:" followed by DD/M/YYYY or DD/MM/YYYY (including single digit months)
            '/fecha\s+de\s+emisi[óo]n\s*:?\s*(\d{1,2}[-\/]\d{1,2}[-\/]\d{4})/i',
            // "Fecha:" followed by DD-MM-YYYY or DD/MM/YYYY
            '/fecha\s*:?\s*(\d{1,2}[-\/]\d{1,2}[-\/]\d{4})/i',
            // "Date:" followed by date
            '/date\s*:?\s*(\d{1,2}[-\/]\d{1,2}[-\/]\d{4})/i',
            // YYYY-MM-DD format (but only valid dates 2020-2030)
            '/([2][0][2-3]\d[-\/]\d{1,2}[-\/]\d{1,2})/i',
            // Standalone DD/M/YYYY or DD-MM-YYYY (must be 20xx to 20xx range)
            '/(\d{1,2}[-\/]\d{1,2}[-\/]20\d{2})/i',
        ];

        foreach ($datePatterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $dateStr = trim($matches[1]);
                $parsed = $this->parseAndFormatDate($dateStr);

                // Only return valid dates from 2020 onwards
                if ($parsed && strtotime($parsed) >= strtotime('2020-01-01')) {
                    return $parsed;
                }
            }
        }

        return null;
    }

    /**
     * Parse date string and return in YYYY-MM-DD format
     */
    private function parseAndFormatDate(string $dateStr): ?string
    {
        // Replace slashes with hyphens for consistency
        $dateStr = str_replace('/', '-', trim($dateStr));

        // Try to parse different date formats - some formats may have single-digit months/days
        $formats = [
            'd-m-Y',   // 30-7-2024 or 30-07-2024
            'j-n-Y',   // 30-7-2024 (flexible day and month)
            'Y-m-d',   // 2024-07-30
            'Y-n-j',   // 2024-7-30
            'd-m-y',   // 30-07-24
            'j-n-y',   // 30-7-24
        ];

        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $dateStr);
            if ($date && $date->format('Y') >= 2020) {
                return $date->format('Y-m-d');
            }
        }

        return null;
    }
}
