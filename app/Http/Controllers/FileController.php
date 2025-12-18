<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Practitioner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    /**
     * Servir archivos de firma del médico
     */
    public function serveSignature(Request $request, $practitioner_id)
    {
        try {
            // Verificar que el usuario esté autenticado
            if (! auth()->check()) {
                abort(401, 'No autenticado');
            }

            $practitioner = Practitioner::findOrFail($practitioner_id);

            // Verificar permisos - solo el propio médico o admin pueden ver la firma
            $isOwnPractitioner = auth()->user()->id === $practitioner->user_id;
            $isAdmin = auth()->user()->hasRole('admin');

            if (! $isOwnPractitioner && ! $isAdmin) {
                abort(403, 'No tienes permisos para acceder a este archivo');
            }

            // Buscar archivo de firma
            $file = File::where('table_name', 'practitioners')
                ->where('record_id', $practitioner_id)
                ->where('type', 'signature')
                ->latest()
                ->first();

            if (! $file) {
                abort(404, 'Archivo de firma no encontrado');
            }

            // Verificar que el archivo existe en el almacenamiento
            if (! Storage::disk('local')->exists($file->path)) {
                abort(404, 'Archivo físico no encontrado');
            }

            // Obtener contenido del archivo
            $contents = Storage::disk('local')->get($file->path);

            // Determinar tipo MIME
            $mimeType = Storage::disk('local')->mimeType($file->path);

            // Retornar respuesta con el archivo
            return Response::make($contents, 200, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="'.$file->name.'"',
                'Cache-Control' => 'private, max-age=3600',
            ]);

        } catch (\Exception $e) {
            abort(500, 'Error al servir el archivo: '.$e->getMessage());
        }
    }

    /**
     * Servir archivos de sello del médico
     */
    public function serveSeal(Request $request, $practitioner_id)
    {
        try {
            // Verificar que el usuario esté autenticado
            if (! auth()->check()) {
                abort(401, 'No autenticado');
            }

            $practitioner = Practitioner::findOrFail($practitioner_id);

            // Verificar permisos - solo el propio médico o admin pueden ver el sello
            $isOwnPractitioner = auth()->user()->id === $practitioner->user_id;
            $isAdmin = auth()->user()->hasRole('admin');

            if (! $isOwnPractitioner && ! $isAdmin) {
                abort(403, 'No tienes permisos para acceder a este archivo');
            }

            // Buscar archivo de sello
            $file = File::where('table_name', 'practitioners')
                ->where('record_id', $practitioner_id)
                ->where('type', 'seal')
                ->latest()
                ->first();

            if (! $file) {
                abort(404, 'Archivo de sello no encontrado');
            }

            // Verificar que el archivo existe en el almacenamiento
            if (! Storage::disk('local')->exists($file->path)) {
                abort(404, 'Archivo físico no encontrado');
            }

            // Obtener contenido del archivo
            $contents = Storage::disk('local')->get($file->path);

            // Determinar tipo MIME
            $mimeType = Storage::disk('local')->mimeType($file->path);

            // Retornar respuesta con el archivo
            return Response::make($contents, 200, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="'.$file->name.'"',
                'Cache-Control' => 'private, max-age=3600',
            ]);

        } catch (\Exception $e) {
            abort(500, 'Error al servir el archivo: '.$e->getMessage());
        }
    }

    /**
     * Servir archivos de firma para PDF (sin restricciones de permisos)
     * Esta función es solo para uso interno en la generación de PDFs
     */
    public function getSignatureForPDF($practitioner_id)
    {
        try {
            $file = File::where('table_name', 'practitioners')
                ->where('record_id', $practitioner_id)
                ->where('type', 'signature')
                ->latest()
                ->first();

            if (! $file || ! Storage::disk('local')->exists($file->path)) {
                return null;
            }

            return Storage::disk('local')->path($file->path);

        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Servir archivos de sello para PDF (sin restricciones de permisos)
     * Esta función es solo para uso interno en la generación de PDFs
     */
    public function getSealForPDF($practitioner_id)
    {
        try {
            $file = File::where('table_name', 'practitioners')
                ->where('record_id', $practitioner_id)
                ->where('type', 'seal')
                ->latest()
                ->first();

            if (! $file || ! Storage::disk('local')->exists($file->path)) {
                return null;
            }

            return Storage::disk('local')->path($file->path);

        } catch (\Exception $e) {
            return null;
        }
    }
}
