<?php

namespace App\Http\Controllers;

use App\Enums\BillingPeriod;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PackageController extends Controller
{
    public function index()
    {
        $model = Package::class;

        return view('packages.index', compact('model'));
    }

    public function create()
    {
        return view('packages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:packages,slug',
            'description' => 'required|string',
            'base_price' => 'required|numeric|min:0',
            'max_doctors_included' => 'required|integer|min:1',
            'max_users' => 'required|integer|min:1',
            'price_per_extra_doctor' => 'nullable|numeric|min:0',
            'billing_period' => 'required|in:monthly,quarterly,yearly',
            'billing_period_days' => 'nullable|integer|min:1',
            'features' => 'nullable|string',
            'is_active' => 'boolean',
            'agent_available' => 'boolean',
            'role_payer' => 'nullable|string|max:255',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Set billing period days based on billing period if not provided
        if (empty($validated['billing_period_days'])) {
            $validated['billing_period_days'] = BillingPeriod::from($validated['billing_period'])->days();
        }

        // Convert features from textarea (line by line) to array
        if (! empty($validated['features'])) {
            $featuresArray = array_filter(
                array_map('trim', explode("\n", $validated['features'])),
                fn ($feature) => ! empty($feature)
            );
            $validated['features'] = $featuresArray;
        } else {
            $validated['features'] = [];
        }

        $model = new Package;
        $model->fill($validated);
        $model->is_active = $request->has('is_active') ? 1 : 0;
        $model->agent_available = $request->has('agent_available') ? 1 : 0;

        if ($model->save()) {
            $request->session()->flash('message.success', 'Paquete creado con éxito.');
        } else {
            $request->session()->flash('message.error', 'Hubo un error y no se pudo crear el paquete.');
        }

        return redirect(route('package.index'));
    }

    public function edit($id)
    {
        $data = Package::find($id);

        return view('packages.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        try {
            $model = Package::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255|unique:packages,slug,'.$id,
                'description' => 'required|string',
                'base_price' => 'required|numeric|min:0',
                'max_doctors_included' => 'required|integer|min:1',
                'max_users' => 'required|integer|min:1',
                'price_per_extra_doctor' => 'nullable|numeric|min:0',
                'billing_period' => 'required|in:monthly,quarterly,yearly',
                'billing_period_days' => 'nullable|integer|min:1',
                'features' => 'nullable|string',
                'is_active' => 'boolean',
                'agent_available' => 'boolean',
                'role_payer' => 'nullable|string|max:255',
            ]);

            // Generate slug if not provided
            if (empty($validated['slug'])) {
                $validated['slug'] = Str::slug($validated['name']);
            }

            // Set billing period days based on billing period if not provided
            if (empty($validated['billing_period_days'])) {
                $validated['billing_period_days'] = BillingPeriod::from($validated['billing_period'])->days();
            }

            // Convert features from textarea (line by line) to array
            if (! empty($validated['features'])) {
                $featuresArray = array_filter(
                    array_map('trim', explode("\n", $validated['features'])),
                    fn ($feature) => ! empty($feature)
                );
                $validated['features'] = $featuresArray;
            } else {
                $validated['features'] = [];
            }

            $model->fill($validated);
            $model->is_active = $request->has('is_active') ? 1 : 0;
            $model->agent_available = $request->has('agent_available') ? 1 : 0;

            if ($model->save()) {
                $request->session()->flash('message.success', 'Paquete actualizado con éxito.');
            } else {
                $request->session()->flash('message.error', 'Hubo un error y no se pudo actualizar el paquete.');
            }

        } catch (\Exception $e) {
            $request->session()->flash('message.error', $e->getMessage());
        }

        return redirect(route('package.edit', $id));
    }

    public function destroy($id)
    {
        $data = Package::find($id);
        $data->delete();

        return redirect(route('package.index'));
    }
}
