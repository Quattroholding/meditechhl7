<?php

namespace App\Services\Treasury;

use App\Enums\TreasuryMovementType;
use App\Models\Bank;
use App\Models\CashRegister;
use App\Models\Treasury\TreasuryMovement;
use App\Services\Accounting\AccountingEngineService;
use Illuminate\Support\Facades\DB;

/**
 * Servicio para gestionar Tesorería
 * Maneja bancos, cajas y movimientos de caja
 */
class TreasuryService
{
    public function __construct(
        protected AccountingEngineService $accountingEngine,
    ) {}

    /**
     * Crea un banco
     */
    public function createBank(int $clientId, array $data): Bank
    {
        return Bank::create([
            'client_id' => $clientId,
            'bank_name' => $data['bank_name'],
            'account_number' => $data['account_number'],
            'account_type' => $data['account_type'] ?? 'checking',
            'currency' => $data['currency'] ?? 'PAB',
            'accounting_account_id' => $data['accounting_account_id'],
            'status' => 'active',
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * Crea una caja
     */
    public function createCashRegister(int $clientId, array $data): CashRegister
    {
        return CashRegister::create([
            'client_id' => $clientId,
            'name' => $data['name'],
            'branch_id' => $data['branch_id'],
            'responsible_user_id' => $data['responsible_user_id'] ?? null,
            'accounting_account_id' => $data['accounting_account_id'],
            'status' => 'active',
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * Registra un movimiento de tesorería
     * Actualiza balance de banco/caja y genera asiento contable
     */
    public function recordMovement(int $clientId, array $data): TreasuryMovement
    {
        return DB::transaction(function () use ($clientId, $data) {
            // Validar que sea banco O caja, no ambos
            if (
                (! isset($data['bank_id']) && ! isset($data['cash_register_id']))
                || (isset($data['bank_id']) && isset($data['cash_register_id']))
            ) {
                throw new \Exception('Must specify either bank_id or cash_register_id, not both');
            }

            // Validar monto
            if ($data['amount'] <= 0) {
                throw new \Exception('Amount must be greater than 0');
            }

            // Generar número de movimiento
            $year = now()->year;
            $count = TreasuryMovement::where('client_id', $clientId)
                ->whereYear('movement_date', $year)
                ->count() + 1;
            $movementNumber = sprintf('TM-%d-%06d', $year, $count);

            // Crear movimiento
            $movement = TreasuryMovement::create([
                'client_id' => $clientId,
                'movement_number' => $movementNumber,
                'movement_date' => $data['movement_date'] ?? now()->toDateString(),
                'movement_type' => $data['movement_type'],
                'source_type' => $data['source_type'] ?? null,
                'source_id' => $data['source_id'] ?? null,
                'bank_id' => $data['bank_id'] ?? null,
                'cash_register_id' => $data['cash_register_id'] ?? null,
                'amount' => $data['amount'],
                'description' => $data['description'],
                'reference_number' => $data['reference_number'] ?? null,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            // Actualizar balance
            $this->updateBalance($movement);

            // Generar asiento contable
            $this->accountingEngine->processEvent('TREASURY_MOVEMENT', $movement);

            return $movement;
        });
    }

    /**
     * Realiza una transferencia entre bancos
     */
    public function transferBetweenBanks(
        Bank $fromBank,
        Bank $toBank,
        float $amount,
        string $description
    ): array {
        return DB::transaction(function () use ($fromBank, $toBank, $amount, $description) {
            // Validar que sean del mismo cliente
            if ($fromBank->client_id !== $toBank->client_id) {
                throw new \Exception('Banks must belong to the same client');
            }

            // Crear movimiento de salida
            $outMovement = $this->recordMovement($fromBank->client_id, [
                'movement_type' => TreasuryMovementType::TRANSFER,
                'bank_id' => $fromBank->id,
                'amount' => $amount,
                'description' => "Transferencia a {$toBank->bank_name}: {$description}",
                'movement_date' => now()->toDateString(),
            ]);

            // Crear movimiento de entrada
            $inMovement = $this->recordMovement($toBank->client_id, [
                'movement_type' => TreasuryMovementType::TRANSFER,
                'bank_id' => $toBank->id,
                'amount' => $amount,
                'description' => "Transferencia desde {$fromBank->bank_name}: {$description}",
                'movement_date' => now()->toDateString(),
            ]);

            return [
                'out_movement' => $outMovement,
                'in_movement' => $inMovement,
            ];
        });
    }

    /**
     * Obtiene el balance de un banco
     */
    public function getBankBalance(Bank $bank): float
    {
        return $bank->balance;
    }

    /**
     * Obtiene el balance de una caja
     */
    public function getCashRegisterBalance(CashRegister $register): float
    {
        return $register->balance;
    }

    /**
     * Actualiza el balance de banco/caja basado en un movimiento
     */
    private function updateBalance(TreasuryMovement $movement): void
    {
        if ($movement->bank_id) {
            $bank = $movement->bank;
            $adjustment = $movement->movement_type === TreasuryMovementType::WITHDRAWAL
                ? -$movement->amount
                : $movement->amount;

            $bank->update([
                'balance' => $bank->balance + $adjustment,
            ]);
        } elseif ($movement->cash_register_id) {
            $register = $movement->cashRegister;
            $adjustment = $movement->movement_type === TreasuryMovementType::WITHDRAWAL
                ? -$movement->amount
                : $movement->amount;

            $register->update([
                'balance' => $register->balance + $adjustment,
            ]);
        }
    }

    /**
     * Obtiene movimientos de un período
     */
    public function getMovementsByPeriod(
        int $clientId,
        \DateTime $startDate,
        \DateTime $endDate,
        ?int $bankId = null,
        ?int $cashRegisterId = null
    ) {
        $query = TreasuryMovement::where('client_id', $clientId)
            ->whereBetween('movement_date', [$startDate, $endDate])
            ->orderByDesc('movement_date');

        if ($bankId) {
            $query->where('bank_id', $bankId);
        }

        if ($cashRegisterId) {
            $query->where('cash_register_id', $cashRegisterId);
        }

        return $query->get();
    }

    /**
     * Obtiene resumen de movimientos por tipo
     */
    public function getMovementsSummary(
        int $clientId,
        \DateTime $startDate,
        \DateTime $endDate
    ): array {
        $movements = TreasuryMovement::where('client_id', $clientId)
            ->whereBetween('movement_date', [$startDate, $endDate])
            ->get();

        return [
            'income' => $movements->where('movement_type', TreasuryMovementType::DEPOSIT)->sum('amount'),
            'expense' => $movements->where('movement_type', TreasuryMovementType::WITHDRAWAL)->sum('amount'),
            'transfer' => $movements->where('movement_type', TreasuryMovementType::TRANSFER)->sum('amount'),
            'net' => $movements->where('movement_type', TreasuryMovementType::DEPOSIT)->sum('amount')
                - $movements->where('movement_type', TreasuryMovementType::WITHDRAWAL)->sum('amount'),
        ];
    }
}
