<?php

namespace App\Livewire\Payment;

use App\Models\ClientCreditCard;
use App\Services\NeoPaymentsService;
use Livewire\Component;

class NeoCreditCardManager extends Component
{
    public bool $showAddForm = false;

    public string $cardNumber = '';

    public string $cardHolder = '';

    public string $expMonth = '';

    public string $expYear = '';

    public string $errorMessage = '';

    public string $successMessage = '';

    public ?int $confirmDeleteId = null;

    protected NeoPaymentsService $neoPaymentsService;

    public function boot(NeoPaymentsService $neoPaymentsService): void
    {
        $this->neoPaymentsService = $neoPaymentsService;
    }

    public function getCreditCardsProperty()
    {
        $client = auth()->user()->getCurrentClient();
        if (! $client) {
            return collect();
        }

        return ClientCreditCard::query()
            ->where('client_id', $client->id)
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->orderByDesc('created_at')
            ->get();
    }

    public function showAdd(): void
    {
        $this->reset(['cardNumber', 'cardHolder', 'expMonth', 'expYear', 'errorMessage', 'successMessage']);
        $this->showAddForm = true;
    }

    public function cancelAdd(): void
    {
        $this->showAddForm = false;
        $this->reset(['cardNumber', 'cardHolder', 'expMonth', 'expYear', 'errorMessage']);
    }

    public function saveCard(): void
    {
        $this->validate([
            'cardNumber' => 'required|string|min:13|max:19',
            'cardHolder' => 'required|string|max:100',
            'expMonth' => 'required|digits:2|min:1|max:12',
            'expYear' => 'required|digits:2',
        ], [
            'cardNumber.required' => 'El número de tarjeta es requerido',
            'cardNumber.min' => 'El número de tarjeta debe tener al menos 13 dígitos',
            'cardHolder.required' => 'El nombre del titular es requerido',
            'expMonth.required' => 'El mes de expiración es requerido',
            'expMonth.min' => 'El mes debe ser entre 01 y 12',
            'expMonth.max' => 'El mes debe ser entre 01 y 12',
            'expYear.required' => 'El año de expiración es requerido',
            'expYear.digits' => 'El año debe tener 2 dígitos',
        ]);

        try {
            $client = auth()->user()->getCurrentClient();

            // Ensure client has NeoPayments customer ID
            if (! $client->neopayments_customer_id) {
                $neoCustomer = $this->neoPaymentsService->createCustomer($client);
                $client->update(['neopayments_customer_id' => $neoCustomer['id']]);
            }

            // Tokenize card with NeoPayments
            $cardData = [
                'card_holder' => strtoupper($this->cardHolder),
                'card_number' => $this->cardNumber,
                'exp_date' => $this->expMonth.'/'.$this->expYear,
            ];

            $tokenizedCard = $this->neoPaymentsService->addCard($client->neopayments_customer_id, $cardData);

            // Check if this is the first card
            $isFirst = ClientCreditCard::query()
                ->where('client_id', $client->id)
                ->where('is_active', true)
                ->count() === 0;

            // Store card in database
            ClientCreditCard::create([
                'client_id' => $client->id,
                'neopayments_customer_id' => $client->neopayments_customer_id,
                'neopayments_card_id' => $tokenizedCard['id'] ?? null,
                'card_token' => $tokenizedCard['token'],
                'card_holder' => strtoupper($this->cardHolder),
                'card_last_four' => $tokenizedCard['card_last_four'],
                'card_brand' => $tokenizedCard['card_brand'] ?? $tokenizedCard['account_type'] ?? null,
                'exp_month' => $this->expMonth,
                'exp_year' => $this->expYear,
                'is_default' => $isFirst,
                'is_active' => true,
                'alias' => ($tokenizedCard['card_brand'] ?? 'CARD').' '.$tokenizedCard['card_last_four'],
                'metadata' => $tokenizedCard,
            ]);

            $this->cancelAdd();
            $this->successMessage = 'Tarjeta guardada exitosamente.';

            $this->dispatch('card-added');
        } catch (\Exception $e) {
            $this->errorMessage = 'Error al guardar la tarjeta: '.$e->getMessage();
        }
    }

    public function setDefault(int $cardId): void
    {
        try {
            $client = auth()->user()->getCurrentClient();

            $card = ClientCreditCard::query()
                ->where('id', $cardId)
                ->where('client_id', $client->id)
                ->firstOrFail();

            $card->markAsDefault();

            $this->successMessage = 'Tarjeta predeterminada actualizada.';
        } catch (\Exception $e) {
            $this->errorMessage = 'Error al actualizar tarjeta predeterminada: '.$e->getMessage();
        }
    }

    public function confirmDelete(int $cardId): void
    {
        $this->confirmDeleteId = $cardId;
        $this->errorMessage = '';
        $this->successMessage = '';
    }

    public function cancelDelete(): void
    {
        $this->confirmDeleteId = null;
    }

    public function deleteCard(): void
    {
        if (! $this->confirmDeleteId) {
            return;
        }

        try {
            $client = auth()->user()->getCurrentClient();

            $card = ClientCreditCard::query()
                ->where('id', $this->confirmDeleteId)
                ->where('client_id', $client->id)
                ->firstOrFail();

            // Delete from NeoPayments
            if ($card->neopayments_customer_id && $card->neopayments_card_id) {
                $this->neoPaymentsService->deleteCard(
                    $card->neopayments_customer_id,
                    $card->neopayments_card_id
                );
            }

            // Soft delete from database
            $wasDefault = $card->is_default;
            $card->update(['is_active' => false]);
            $card->delete();

            // If it was default, set another card as default
            if ($wasDefault) {
                $nextCard = ClientCreditCard::query()
                    ->where('client_id', $client->id)
                    ->where('is_active', true)
                    ->latest()
                    ->first();

                if ($nextCard) {
                    $nextCard->markAsDefault();
                }
            }

            $this->confirmDeleteId = null;
            $this->successMessage = 'Tarjeta eliminada exitosamente.';

            $this->dispatch('card-deleted');
        } catch (\Exception $e) {
            $this->errorMessage = 'Error al eliminar la tarjeta: '.$e->getMessage();
            $this->confirmDeleteId = null;
        }
    }

    public function render()
    {
        return view('livewire.payment.neo-credit-card-manager', [
            'creditCards' => $this->creditCards,
        ]);
    }
}
