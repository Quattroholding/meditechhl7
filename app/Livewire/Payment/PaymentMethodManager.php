<?php

namespace App\Livewire\Payment;

use App\Models\PaymentToken;
use App\Services\CyberSourceService;
use Illuminate\Support\Str;
use Livewire\Component;

class PaymentMethodManager extends Component
{
    public bool $showAddForm = false;

    public string $captureContext = '';

    public string $errorMessage = '';

    public string $successMessage = '';

    public string $newCardExpMonth = '';

    public string $newCardExpYear = '';

    public string $newCardBillingName = '';

    public string $transientToken = '';

    public function boot(CyberSourceService $cyberSource): void
    {
        $this->cyberSource = $cyberSource;
    }

    protected CyberSourceService $cyberSource;

    public function getTokensProperty()
    {
        $client = auth()->user()->getCurrentClient();
        if (! $client) {
            return collect();
        }

        return PaymentToken::query()
            ->where('client_id', $client->id)
            ->where('gateway', 'cybersource')
            ->orderByDesc('is_default')
            ->orderByDesc('created_at')
            ->get();
    }

    public function showAdd(): void
    {
        $this->errorMessage = '';
        $this->successMessage = '';

        if (! config('cybersource.use_tms')) {
            $this->errorMessage = 'La gestión de tarjetas no está disponible en este momento. Por favor, contacte a soporte.';

            return;
        }

        $result = $this->cyberSource->generateMicroformKey([
            'origins' => [config('app.url')],
            'reference_code' => 'ADD-CARD-'.Str::uuid(),
        ]);

        if (! $result['success']) {
            $this->errorMessage = 'No se pudo inicializar el formulario de pago.';

            return;
        }

        $this->captureContext = is_string($result['body']) ? $result['body'] : json_encode($result['body']);
        $this->showAddForm = true;
    }

    public function cancelAdd(): void
    {
        $this->showAddForm = false;
        $this->captureContext = '';
        $this->transientToken = '';
        $this->newCardExpMonth = '';
        $this->newCardExpYear = '';
        $this->newCardBillingName = '';
        $this->errorMessage = '';
    }

    public function saveCard(): void
    {
        $this->validate([
            'transientToken' => 'required|string',
            'newCardExpMonth' => 'required|digits_between:1,2',
            'newCardExpYear' => 'required|digits:4',
            'newCardBillingName' => 'required|string|max:100',
        ]);

        $client = auth()->user()->getCurrentClient();
        $user = auth()->user();

        $nameParts = explode(' ', trim($this->newCardBillingName), 2);
        $firstName = $nameParts[0];
        $lastName = $nameParts[1] ?? 'N/A';

        $result = $this->cyberSource->createPaymentToken($this->transientToken, [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $user->email,
            'exp_month' => str_pad($this->newCardExpMonth, 2, '0', STR_PAD_LEFT),
            'exp_year' => $this->newCardExpYear,
        ]);

        if (! $result['success']) {
            $this->errorMessage = 'No se pudo guardar la tarjeta. Por favor, intente nuevamente.';

            return;
        }

        $body = $result['body'];
        $customerId = $body['id'] ?? null;

        if (! $customerId) {
            $this->errorMessage = 'Error procesando la respuesta del servidor de pagos.';

            return;
        }

        $isFirst = PaymentToken::query()
            ->where('client_id', $client->id)
            ->where('gateway', 'cybersource')
            ->count() === 0;

        PaymentToken::create([
            'client_id' => $client->id,
            'gateway' => 'cybersource',
            'token' => $customerId,
            'card_exp_month' => str_pad($this->newCardExpMonth, 2, '0', STR_PAD_LEFT),
            'card_exp_year' => $this->newCardExpYear,
            'billing_name' => $this->newCardBillingName,
            'billing_email' => $user->email,
            'is_default' => $isFirst,
        ]);

        $this->cancelAdd();
        $this->successMessage = 'Tarjeta guardada exitosamente.';
    }

    public function setDefault(int $tokenId): void
    {
        $client = auth()->user()->getCurrentClient();

        PaymentToken::query()
            ->where('client_id', $client->id)
            ->update(['is_default' => false]);

        PaymentToken::query()
            ->where('id', $tokenId)
            ->where('client_id', $client->id)
            ->update(['is_default' => true]);

        $this->successMessage = 'Tarjeta predeterminada actualizada.';
    }

    public function deleteToken(int $tokenId): void
    {
        $client = auth()->user()->getCurrentClient();

        $token = PaymentToken::query()
            ->where('id', $tokenId)
            ->where('client_id', $client->id)
            ->firstOrFail();

        $wasDefault = $token->is_default;
        $token->delete();

        if ($wasDefault) {
            $next = PaymentToken::query()
                ->where('client_id', $client->id)
                ->where('gateway', 'cybersource')
                ->latest()
                ->first();

            if ($next) {
                $next->update(['is_default' => true]);
            }
        }

        $this->successMessage = 'Tarjeta eliminada.';
    }

    public function render()
    {
        return view('livewire.payment.payment-method-manager', [
            'tokens' => $this->tokens,
            'useTms' => config('cybersource.use_tms', false),
        ]);
    }
}
