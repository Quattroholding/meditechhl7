<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        $model = Branch::class;

        return view('clients.branchs.index', compact('model'));
    }

    public function create()
    {
        $countries = \App\Models\Country::all();

        return view('clients.branchs.create', compact('countries'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'client_id' => 'required',
            'name' => 'required',
            'phone' => 'required',
            'full_phone' => 'required',
            'address' => 'required',
            'type' => 'required',
            'country_id' => 'nullable|exists:countries,id',
            'state_id' => 'nullable|exists:states,id',
        ]);

        $model = new Branch;
        $model->client_id = $request->client_id;
        $model->name = $request->name;
        $model->phone = $request->full_phone;
        $model->address = $request->address;
        $model->type = $request->type;
        $model->country_id = $request->country_id;
        $model->state_id = $request->state_id;
        $model->active = 1;

        if ($model->save()) {
            $request->session()->flash('message.success', 'Sucursal registrada con éxito.');
        } else {
            $request->session()->flash('message.success', 'Hubo un error y no se pudo crear la sucursal.');
        }

        return redirect(route('client.branch.index'));
    }

    public function edit($id)
    {
        $data = Branch::findOrFail($id);
        $countries = \App\Models\Country::all();
        $states = $data->country_id ? \App\Models\State::where('country_id', $data->country_id)->get() : collect();

        return view('clients.branchs.edit', compact('data', 'countries', 'states'));
    }

    public function update(Request $request, $id)
    {

        $validated = $request->validate([
            'client_id' => 'required',
            'name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'type' => 'required',
            'country_id' => 'nullable|exists:countries,id',
            'state_id' => 'nullable|exists:states,id',
        ]);

        $model = Branch::findOrFail($id);
        $model->fill($request->all());

        if ($model->save()) {
            $request->session()->flash('message.success', 'Sucursal actualizada con éxito.');
        } else {
            $request->session()->flash('message.success', 'Hubo un error y no se pudo crear la sucursal.');
        }

        return redirect(route('client.branch.edit', $id));
    }

    public function destroy(Request $request, $id)
    {

        try {
            $data = Branch::findOrFail($id);
            $data->delete();

            session()->flash('message.success', 'Eliminado con exito.');
        } catch (\Exception $e) {
            session()->flash('message.error', $e->getMessage());
        }

        return redirect(route('client.branch.index'));
    }
}
