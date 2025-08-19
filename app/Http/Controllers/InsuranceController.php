<?php

namespace App\Http\Controllers;

use App\Http\Requests\InsuranceFormRequest;
use App\Models\Insurance;
use Illuminate\Support\Facades\Auth;

class InsuranceController extends Controller
{
    public function index()
    {
        $model = Insurance::class;

        return view('insurances.index', compact('model'));
    }

    public function create()
    {
        return view('insurances.create');
    }

    public function store(InsuranceFormRequest $request)
    {
        $data = $request->validated();
        $data['client_id'] = Auth::user()->client_id;

        Insurance::create($data);

        return redirect()->route('insurances.index')
            ->with('success', 'Aseguradora creada exitosamente.');
    }

    public function show(Insurance $insurance)
    {
        if ($insurance->client_id !== Auth::user()->client_id) {
            abort(403);
        }

        return view('insurances.show', compact('insurance'));
    }

    public function edit(Insurance $insurance)
    {
        if ($insurance->client_id !== Auth::user()->client_id) {
            abort(403);
        }

        return view('insurances.edit', compact('insurance'));
    }

    public function update(InsuranceFormRequest $request, Insurance $insurance)
    {
        if ($insurance->client_id !== Auth::user()->client_id) {
            abort(403);
        }

        $data = $request->validated();
        $insurance->update($data);

        return redirect()->route('insurances.index')
            ->with('success', 'Aseguradora actualizada exitosamente.');
    }

    public function destroy(Insurance $insurance)
    {
        if ($insurance->client_id !== Auth::user()->client_id) {
            abort(403);
        }

        $insurance->delete();

        return redirect()->route('insurances.index')
            ->with('success', 'Aseguradora eliminada exitosamente.');
    }
}
