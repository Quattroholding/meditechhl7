<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function consultationTemplate()
    {
        return view('settings.consultation-template');
    }

    public function rapidAccess()
    {
        return view('settings.rapid-access');
    }

    public function cptUser()
    {
        return view('settings.cpt-user');
    }

    public function workingHourUser()
    {
        return view('settings.working-hour-user');
    }

    public function createUserProcedure()
    {
        return view('settings.user-procedures');
    }

    public function uploadSignatureSeal($practitioner_id)
    {
        return view('settings.signature-seal', compact('practitioner_id'));
    }

    /**
     * Gestión de temas por cliente
     */
    public function themeManager($client_id)
    {
        $client = Client::findOrFail($client_id);
        
        // Verificar permisos - solo admin o usuarios del cliente pueden acceder
        if (!auth()->user()->hasRole('admin') && !auth()->user()->clients->contains($client_id)) {
            abort(403, 'No tienes permisos para acceder a la configuración de este cliente.');
        }

        return view('settings.theme-manager', compact('client_id', 'client'));
    }
}