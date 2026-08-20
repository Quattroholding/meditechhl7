<?php

namespace App\Policies;

use App\Models\ClientInvoice;
use App\Models\User;

class ClientInvoicePolicy
{
    /**
     * Determine whether the user can create a payment for the invoice.
     *
     * This prevents users from creating duplicate payments when there's already
     * a pending payment for the full amount being processed.
     */
    public function createPayment(User $user, ClientInvoice $clientInvoice): bool
    {
        // User must have permission to store payments
        if (! $user->can('suscriptions.payments.store')) {
            return false;
        }

        // Cannot pay cancelled invoices (they are automatically cancelled when reactivating suspended subscriptions)
        if ($clientInvoice->status->value === 'cancelled') {
            return false;
        }

        // Cannot pay already paid invoices
        if ($clientInvoice->status->value === 'paid') {
            return false;
        }

        // Invoice must have an outstanding balance
        if ($clientInvoice->amount_due <= 0) {
            return false;
        }

        // Prevent duplicate payments: reject if there's already a pending payment for the full amount
        if ($clientInvoice->hasPendingPaymentForFullAmount()) {
            return false;
        }

        return true;
    }
}
