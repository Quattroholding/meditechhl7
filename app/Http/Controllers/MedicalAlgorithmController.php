<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MedicalAlgorithmController extends Controller
{
    /**
     * Algorithm metadata with Spanish names and descriptions
     */
    private function getAlgorithmMetadata(): array
    {
        return [
            'ACLS-Acute-Coronary-Syndromes-Algorithm.pdf' => [
                'title' => 'Síndromes Coronarios Agudos',
                'description' => 'Guía para el manejo de infarto agudo de miocardio y angina inestable',
            ],
            'ACLS-Adult-Bradycardia-Algorithm.pdf' => [
                'title' => 'Bradicardia en Adultos',
                'description' => 'Protocolo para el tratamiento de frecuencia cardíaca lenta sintomática',
            ],
            'ACLS-Adult-Cardiac-Arrest-Algorithm.pdf' => [
                'title' => 'Paro Cardíaco en Adultos',
                'description' => 'Algoritmo principal de RCP avanzada para paro cardiorrespiratorio',
            ],
            'ACLS-Adult-Suspected-Stroke-Algorithm.pdf' => [
                'title' => 'Sospecha de Accidente Cerebrovascular',
                'description' => 'Evaluación inicial y manejo de ACV agudo',
            ],
            'ACLS-Adult-Tachycardia-Algorithm.pdf' => [
                'title' => 'Taquicardia en Adultos',
                'description' => 'Protocolo para frecuencia cardíaca rápida con pulso',
            ],
            'ACLS-Cardiac-Arrest-in-Pregnant-Patient-Algorithm.pdf' => [
                'title' => 'Paro Cardíaco en Paciente Embarazada',
                'description' => 'Modificaciones específicas de RCP durante el embarazo',
            ],
            'ACLS-in-a-Nutshell.pdf' => [
                'title' => 'ACLS en Resumen',
                'description' => 'Vista general rápida de todos los algoritmos ACLS',
            ],
            'ACLS-Positions-for-6-Person-Algorithm.pdf' => [
                'title' => 'Posiciones para Equipo de 6 Personas',
                'description' => 'Distribución de roles durante reanimación avanzada',
            ],
            'ACLS-The-Systematic-Approach-Algorithm.pdf' => [
                'title' => 'Enfoque Sistemático ACLS',
                'description' => 'Metodología estructurada para evaluación de pacientes críticos',
            ],
            'Adult-Immediate-Post-Cardiac-Arrest-Care-Algorithm.pdf' => [
                'title' => 'Cuidados Inmediatos Post Paro Cardíaco',
                'description' => 'Manejo del paciente tras retorno de circulación espontánea',
            ],
            'Cardiac-Arrest-Circular-Algorithm.pdf' => [
                'title' => 'Algoritmo Circular de Paro Cardíaco',
                'description' => 'Diagrama circular del ciclo de RCP avanzada',
            ],
            'Checklist-for-Fibrinolytic-Therapy.pdf' => [
                'title' => 'Lista de Verificación para Terapia Fibrinolítica',
                'description' => 'Criterios de inclusión/exclusión para trombolisis',
            ],
            'Hs-and-Ts.pdf' => [
                'title' => 'Las H\'s y las T\'s',
                'description' => 'Causas reversibles de paro cardíaco',
            ],
            'Opioid-Associated-Emergency-Algorithm-for-HCP.pdf' => [
                'title' => 'Emergencia por Opioides - Profesionales',
                'description' => 'Protocolo de sobredosis de opioides para personal sanitario',
            ],
            'Opioid-Associated-Emergency-Algorithm-for-Lay-Rescuers.pdf' => [
                'title' => 'Emergencia por Opioides - Rescatistas',
                'description' => 'Protocolo de sobredosis de opioides para público general',
            ],
        ];
    }

    /**
     * Display the list of medical algorithms
     */
    public function index(): \Illuminate\View\View
    {
        $algorithms = $this->getAlgorithmMetadata();
        return view('medical-algorithms.index', compact('algorithms'));
    }

    /**
     * View/download a specific PDF file
     */
    public function view(string $filename): StreamedResponse
    {
        // Prevent directory traversal attacks
        $filename = basename($filename);

        // Validate filename is in our allowed list
        $metadata = $this->getAlgorithmMetadata();
        if (!isset($metadata[$filename])) {
            abort(404, 'Algoritmo no encontrado');
        }

        // Check if file exists
        $filepath = "algoritmos/{$filename}";
        if (!Storage::disk('public')->exists($filepath)) {
            abort(404, 'Archivo no encontrado');
        }

        // Stream the PDF
        return response()->file(
            Storage::disk('public')->path($filepath),
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="'.$filename.'"',
            ]
        );
    }
}
