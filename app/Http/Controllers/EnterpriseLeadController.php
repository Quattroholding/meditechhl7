<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEnterpriseLeadRequest;
use App\Models\EnterpriseLead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EnterpriseLeadController extends Controller
{
    public function store(StoreEnterpriseLeadRequest $request)
    {
        try {
            $lead = EnterpriseLead::create([
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'company_name' => $request->company_name,
                'message' => $request->message,
                'status' => 'new',
                'source' => 'landing_page',
                'metadata' => [
                    'user_agent' => $request->userAgent(),
                    'ip' => $request->ip(),
                    'referrer' => $request->header('referer'),
                ],
            ]);

            Log::info('Enterprise lead created', [
                'lead_id' => $lead->id,
                'company' => $lead->company_name,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Gracias por tu interés. Nos contactaremos pronto.',
                ]);
            }

            return redirect()->route('welcome')
                ->with('success', 'Gracias por tu interés. Nos contactaremos contigo pronto.');

        } catch (\Exception $e) {
            Log::error('Failed to create enterprise lead', [
                'error' => $e->getMessage(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hubo un error. Por favor intenta nuevamente.',
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Hubo un error. Por favor intenta nuevamente.');
        }
    }

    public function index()
    {
        $leads = EnterpriseLead::with('assignedUser')
            ->latest()
            ->paginate(20);

        return view('admin.leads.index', compact('leads'));
    }

    public function show($id)
    {
        $lead = EnterpriseLead::with('assignedUser', 'convertedClient')->findOrFail($id);

        return view('admin.leads.show', compact('lead'));
    }

    public function update($id, Request $request)
    {
        $lead = EnterpriseLead::findOrFail($id);

        $lead->update($request->only(['status', 'assigned_to', 'notes']));

        if ($request->status === 'contacted' && ! $lead->contacted_at) {
            $lead->contacted_at = now();
            $lead->save();
        }

        return redirect()->back()->with('success', 'Lead actualizado exitosamente.');
    }

    public function convertToClient($id)
    {
        $lead = EnterpriseLead::findOrFail($id);

        // Redirigir a formulario interno de creación de cliente
        // con datos pre-poblados del lead
        return redirect()->route('client.create')
            ->with('lead_data', [
                'email' => $lead->email,
                'phone' => $lead->phone,
                'name' => $lead->company_name,
                'long_name' => $lead->company_name,
                'first_name' => explode(' ', $lead->full_name)[0] ?? '',
                'last_name' => substr($lead->full_name, strpos($lead->full_name, ' ') + 1),
            ])
            ->with('converting_lead_id', $lead->id);
    }
}
