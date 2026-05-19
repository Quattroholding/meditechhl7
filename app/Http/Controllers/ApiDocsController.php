<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ApiDocsController extends Controller
{
    public function show(?string $page = null): View
    {
        // Si no se especifica página, mostrar index
        if ($page === null) {
            $page = 'index';
        }

        // Sanitizar el nombre del archivo para prevenir directory traversal
        $page = str_replace(['..', '/', '\\'], '', $page);

        // Verificar si la vista existe
        $viewPath = 'api-docs.'.$page;

        if (! view()->exists($viewPath)) {
            abort(404, 'Documentation page not found.');
        }

        return view($viewPath);
    }

    public function recepyIndex(): View
    {
        $viewPath = 'api-docs.recepy.index';

        if (! view()->exists($viewPath)) {
            abort(404, 'Documentation page not found.');
        }

        return view($viewPath);
    }
}
