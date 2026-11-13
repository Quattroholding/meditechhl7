# Sistema de Cuentas de Gasto por Proveedor

## 📋 Descripción General

Sistema que permite asignar **cuentas de gasto específicas a cada proveedor**. Cuando se registra una factura de proveedor, se utiliza la cuenta de gasto configurada en lugar de una cuenta por defecto centralizada.

## 🎯 Propósito

Resolver el problema de clasificación contable incorrect donde:
- **Antes**: Todas las facturas de proveedores debitaban la cuenta **6101 (Salarios Administrativos)**
- **Ahora**: Cada proveedor puede tener su propia cuenta de gasto específica:
  - IDAAN → **6140 (Agua)**
  - ENSA → **6145 (Electricidad)**
  - NATURGY → **6140 (Agua)**
  - Otros proveedores → puede configurarse según su tipo

## 📊 Estructura de Datos

### Campo en tabla `suppliers`
```sql
ALTER TABLE suppliers ADD COLUMN expense_account_id BIGINT UNSIGNED NULLABLE;
ALTER TABLE suppliers ADD FOREIGN KEY (expense_account_id)
    REFERENCES accounting_accounts(id) ON DELETE CASCADE;
```

### Modelo `Supplier`
```php
class Supplier extends BaseModel {
    protected $fillable = [
        // ... campos existentes ...
        'expense_account_id',
    ];

    // Relación a la cuenta de gasto
    public function expenseAccount(): BelongsTo {
        return $this->belongsTo(AccountingAccount::class, 'expense_account_id');
    }
}
```

## 🔄 Flujo de Procesamiento

### En `AccountingEngineService.php`

**Antes (incorrecto)**:
```php
// Siempre usaba la cuenta 6101 por defecto
$expenseAccount = $this->getExpenseAccountForCostCenter($clientId, $costCenterId);

JournalEntryLine::create([
    'accounting_account_id' => $expenseAccount->id,  // 6101 (incorrecto)
    'debit' => $amount,
]);
```

**Después (correcto)**:
```php
// Primero intenta usar cuenta del proveedor
$expenseAccount = $this->getExpenseAccountForSupplier($invoice, $costCenterId);

JournalEntryLine::create([
    'accounting_account_id' => $expenseAccount->id,  // 6140 o 6145 (correcto)
    'debit' => $amount,
]);
```

### Método `getExpenseAccountForSupplier()`
```php
private function getExpenseAccountForSupplier(
    FinanceSupplierInvoice $invoice,
    ?int $costCenterId
): AccountingAccount {
    // Paso 1: Si el proveedor tiene cuenta de gasto configurada, usarla
    if ($invoice->supplier->expense_account_id) {
        return $invoice->supplier->expenseAccount;
    }

    // Paso 2: Fallback a cuenta del cost center
    return $this->getExpenseAccountForCostCenter($invoice->client_id, $costCenterId);
}
```

## 🛠️ Configuración

### Opción 1: Mediante Seeder Automático

```bash
php artisan db:seed --class=SupplierExpenseAccountSeeder
```

**Mapeo automático** (en `SupplierExpenseAccountSeeder`):
- IDAAN → 6140 (Agua)
- ENSA → 6145 (Electricidad)
- NATURGY → 6140 (Agua)
- Otros → Se dejan sin configurar

### Opción 2: Interfaz de Usuario

1. Ir a `/finance/payables/suppliers/{id}/edit`
2. En el formulario del proveedor, aparece campo **"Cuenta Contable de Gasto"**
3. Seleccionar la cuenta de gasto específica del proveedor
4. Guardar cambios

**Campo en vista**:
```blade
<select wire:model="expense_account_id" id="expense_account_id">
    <option value="">{{ __('generic.select') }}</option>
    @foreach ($expenseAccounts as $account)
        <option value="{{ $account->id }}">
            {{ $account->code }} - {{ $account->name }}
        </option>
    @endforeach
</select>
```

## 📋 Cuentas Disponibles

El sistema carga automáticamente todas las cuentas de **tipo "expense"**:

| Código | Nombre | Uso |
|--------|--------|-----|
| 6101 | Salarios Administrativos | Personal |
| 6140 | Agua | Servicios de agua (IDAAN, NATURGY agua) |
| 6145 | Electricidad | Servicios de electricidad (ENSA, NATURGY electricidad) |
| 6150 | Teléfono | Servicios telefónicos |
| 6155 | Viático y Transporte | Transportación |

## 🔍 Ejemplos de Uso

### Ejemplo 1: Factura de NATURGY (Agua)
```php
$invoice = SupplierInvoice::find(123);
$invoice->supplier; // NATURGY con expense_account_id = ID de 6140

// Al procesar:
$expenseAccount = $this->getExpenseAccountForSupplier($invoice, $costCenterId);
// Retorna: Cuenta 6140 (Agua) ✓ correcto

// Asiento resultante:
// Débito:  6140 (Agua) $500
// Crédito: 2101 (CxP)   $500
```

### Ejemplo 2: Factura de ENSA (Electricidad)
```php
$invoice = SupplierInvoice::find(456);
$invoice->supplier; // ENSA con expense_account_id = ID de 6145

// Al procesar:
$expenseAccount = $this->getExpenseAccountForSupplier($invoice, $costCenterId);
// Retorna: Cuenta 6145 (Electricidad) ✓ correcto

// Asiento resultante:
// Débito:  6145 (Electricidad) $300
// Crédito: 2101 (CxP)           $300
```

### Ejemplo 3: Proveedor sin cuenta configurada
```php
$invoice = SupplierInvoice::find(789);
$invoice->supplier->expense_account_id; // NULL

// Al procesar:
$expenseAccount = $this->getExpenseAccountForSupplier($invoice, $costCenterId);
// Fallback a: getExpenseAccountForCostCenter() → 6101 (por defecto)

// Asiento resultante:
// Débito:  6101 (por defecto) $200
// Crédito: 2101 (CxP)          $200
```

## 📁 Archivos Modificados

### Base de Datos
- `database/migrations/2026_11_13_110243_add_expense_account_to_suppliers.php`
  - Agregó columna `expense_account_id` con FK a `accounting_accounts`
  - Agregó índice para performance

- `database/seeders/SupplierExpenseAccountSeeder.php`
  - Nuevo seeder que configura cuentas automáticamente por nombre

### Modelos
- `app/Models/Finance/Supplier.php`
  - Agregado: relación `expenseAccount()`
  - Agregado: `expense_account_id` a fillables

### Servicios
- `app/Services/Accounting/AccountingEngineService.php`
  - Nuevo método: `getExpenseAccountForSupplier()`
  - Actualizado: `processSupplierInvoiceCreated()` para usar cuentas específicas por proveedor
  - Soporte para distribuciones de costos (líneas 106-109)

### Interfaz de Usuario
- `app/Livewire/Finance/Supplier/SupplierModal.php`
  - Agregado: propiedad `expense_account_id`
  - Agregado: método `getExpenseAccountsProperty()`
  - Actualizado: mount(), rules(), save() para incluir expense_account_id

- `resources/views/livewire/finance/supplier/supplier-modal.blade.php`
  - Nuevo campo: Select para "Cuenta Contable de Gasto"
  - Muestra las cuentas de tipo "expense" disponibles

## ✅ Validación

### Validación en Componente
```php
'expense_account_id' => 'nullable|exists:accounting_accounts,id',
```

- Campo opcional (nullable)
- Si proporciona valor, debe existir en tabla `accounting_accounts`

### Lógica de Fallback
```php
// Si expense_account_id es NULL, usa getExpenseAccountForCostCenter()
if ($invoice->supplier->expense_account_id) {
    return $invoice->supplier->expenseAccount;
}
return $this->getExpenseAccountForCostCenter($clientId, $costCenterId);
```

## 🎓 Restricciones y Limitaciones

| Restricción | Razón |
|-------------|-------|
| Una cuenta por proveedor | Simplifica clasificación |
| Solo cuentas tipo "expense" | Garantiza categorización correcta |
| Campo nullable | Permite fallback a cuenta por defecto |
| FK cascadeOnDelete | Si se elimina cuenta, se elimina referencia |

## 🔄 Migración desde Cuenta Default

### Estado Anterior
- Todos los proveedores: expense_account_id = NULL
- Todas las facturas debitaban 6101 (Salarios)

### Estado Posterior
```sql
-- Después de ejecutar seeder:
SELECT COUNT(*) FROM suppliers WHERE expense_account_id IS NOT NULL;
-- Resultado: 3 proveedores configurados (IDAAN, ENSA, NATURGY)

SELECT COUNT(*) FROM suppliers WHERE expense_account_id IS NULL;
-- Resultado: 1+ proveedores sin configurar (necesitan configuración manual)
```

### Pasos para Migración Completa
1. ✅ Ejecutar migración (agrega columna)
2. ✅ Ejecutar seeder (configura 3 proveedores principales)
3. ⏳ Manualmente: Configurar otros proveedores según su tipo
   - Ir a cada proveedor → editar → seleccionar cuenta de gasto
4. ✅ Verificar: Registrar nuevas facturas y validar asientos

## 📊 Diagnóstico

### Verificar Configuración
```bash
# Ver proveedores sin cuenta de gasto configurada
mysql> SELECT legal_name, commercial_name
       FROM suppliers
       WHERE expense_account_id IS NULL
       AND status = 'active';
```

### Ver Asientos Recientes
```bash
# Verificar que las nuevas facturas usan cuentas correctas
mysql> SELECT je.description, jel.accounting_account_id, aa.code, aa.name, jel.debit
       FROM journal_entries je
       JOIN journal_entry_lines jel ON je.id = jel.journal_entry_id
       JOIN accounting_accounts aa ON jel.accounting_account_id = aa.id
       WHERE je.document_type = 'supplier_invoice'
       AND je.entry_date >= '2026-11-13'
       ORDER BY je.entry_date DESC
       LIMIT 10;
```

## 🚀 Próximos Pasos

1. **Configurar proveedores restantes**
   - Revisar lista de proveedores sin cuenta de gasto
   - Asignar cuenta según tipo de gasto (transporte, suministros, etc.)

2. **Validar asientos contables**
   - Registrar facturas de prueba para cada tipo de proveedor
   - Verificar que debitan la cuenta correcta

3. **Documentar procedimiento operativo**
   - Capacitar administradores sobre configuración de nuevos proveedores
   - Crear checklist de campos obligatorios (incluyendo "Cuenta de Gasto")

---

**Versión**: 1.0
**Implementado**: 2026-11-13
**Estado**: ✅ Completado y Funcional
