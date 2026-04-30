<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class MedicineController extends Controller
{
    /**
     * Display a listing of the medicines.
     */
    public function index(): View
    {
        return view('medicine.index');
    }

    /**
     * Show the form for creating a new medicine.
     */
    public function create(): View
    {
        return view('medicine.create');
    }

    /**
     * Show the form for editing the specified medicine.
     */
    public function edit(int $id): View
    {
        $medicine = Medicine::find($id);
        $response = Gate::inspect('update',$medicine);


        if ($response->denied()) {
            abort(403, 'No tienes permisos para acceder a esta página.');
        }

        return view('medicine.edit', ['medicine_id' => $id]);
    }
}
