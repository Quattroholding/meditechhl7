<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;

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
            'description' => 'required|string',
            'max_users' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $model = new Package;
        $model->fill($validated);
        $model->is_active = $request->has('is_active') ? 1 : 0;

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
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'max_users' => 'required|integer|min:1',
                'is_active' => 'boolean',
            ]);

            $model = Package::find($id);
            $model->fill($validated);
            $model->is_active = $request->has('is_active') ? 1 : 0;

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
