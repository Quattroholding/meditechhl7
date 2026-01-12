<?php

namespace App\Http\Controllers;

use App\Models\ClientSubscription;
use App\Models\Package;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;

class SuscriptionController extends Controller
{
    public function __construct(
        protected SubscriptionService $subscriptionService
    ) {}

    public function index()
    {
        return view('subscriptions.index');
    }

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

    public function upgrade()
    {
        $client = auth()->user()->getCurrentClient();

        if (! $client) {
            abort(403, 'No tiene un cliente asociado');
        }

        $subscription = ClientSubscription::with(['package'])
            ->where('client_id', $client->id)
            ->latest()
            ->first();

        if (! $subscription) {
            return redirect()->route('suscriptions.show')
                ->with('message.error', 'No tiene una suscripción activa');
        }

        // Get all active packages
        $packages = Package::where('is_active', true)
            ->where('id', '!=', $subscription->package_id)
            ->where('id', '<>', 4)
            ->orderBy('base_price', 'asc')
            ->get();

        return view('subscriptions.upgrade', compact('subscription', 'packages'));
    }

    public function processUpgrade(Request $request)
    {
        $validated = $request->validate([
            'package_id' => 'required|exists:packages,id',
            'prorate' => 'boolean',
        ]);

        $client = auth()->user()->getCurrentClient();

        if (! $client) {
            abort(403, 'No tiene un cliente asociado');
        }

        $subscription = ClientSubscription::with(['package'])
            ->where('client_id', $client->id)
            ->latest()
            ->first();

        if (! $subscription) {
            return redirect()->route('suscriptions.show')
                ->with('message.error', 'No tiene una suscripción activa');
        }

        $newPackage = Package::findOrFail($validated['package_id']);

        // Prevent downgrading if not allowed
        if ($newPackage->base_price < $subscription->package->base_price) {
            return redirect()->route('suscriptions.upgrade')
                ->with('message.error', 'No puede cambiar a un plan de menor precio. Contacte con soporte para asistencia.');
        }

        // Prevent selecting the same package
        if ($newPackage->id === $subscription->package_id) {
            return redirect()->route('suscriptions.upgrade')
                ->with('message.error', 'Este ya es su plan actual');
        }

        try {
            $prorate = $validated['prorate'] ?? true;
            $updatedSubscription = $this->subscriptionService->changePlan($subscription, $newPackage, $prorate);

            return redirect()->route('suscriptions.show')
                ->with('message.success', "¡Plan actualizado exitosamente a {$newPackage->name}!");
        } catch (\Exception $e) {
            return redirect()->route('suscriptions.upgrade')
                ->with('message.error', 'Error al actualizar el plan: '.$e->getMessage());
        }
    }
}
