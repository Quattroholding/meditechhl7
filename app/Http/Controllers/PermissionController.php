<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        return view('permissions.index');
    }

    public function create()
    {
        return view('permissions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'description' => 'nullable|string|max:500',
        ]);

        Permission::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('permission.index')
            ->with('success', 'Permiso creado exitosamente.');
    }

    public function edit($id)
    {
        $permission = Permission::findOrFail($id);

        return view('permissions.edit', compact('permission'));
    }

    public function update(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,'.$permission->id,
            'description' => 'nullable|string|max:500',
        ]);

        $permission->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('permission.index')
            ->with('success', 'Permiso actualizado exitosamente.');
    }

    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);

        // Check if permission is assigned to any roles
        if ($permission->roles()->count() > 0) {
            return redirect()->route('permission.index')
                ->with('error', 'No se puede eliminar el permiso porque está asignado a roles.');
        }

        $permission->delete();

        return redirect()->route('permission.index')
            ->with('success', 'Permiso eliminado exitosamente.');
    }
}
