<?php

namespace App\Http\Controllers;

use App\Models\ClientSubscription;

class SuscriptionController extends Controller
{
    public function show()
    {
        $client = auth()->user()->getCurrentClient();

        if (! $client) {
            abort(403, 'No tiene un cliente asociado');
        }

        $subscription = ClientSubscription::with(['package', 'currentInvoice'])
            ->where('client_id', $client->id)
            ->latest()
            ->first();

        return view('subscriptions.show', compact('subscription'));
    }
}
