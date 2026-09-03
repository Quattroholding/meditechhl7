<?php

namespace Database\Seeders;

use App\Models\EncounterSection;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EncounterSectionIconPathSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mapeo de nombres de sección a archivos PNG
        $iconPathMapping = [
            'Motivo de Consulta' => 'MotivoDeConsulta.png',
            'Reason for Visit' => 'MotivoDeConsulta.png',
            'Signos Vitales' => 'SignosVitales.png',
            'Vital Signs' => 'SignosVitales.png',
            'Enfermedad Actual' => 'EnfermedadActual.png',
            'Present Illness' => 'EnfermedadActual.png',
            'Examen Físico' => 'ExamenFisico.png',
            'Physical Exam' => 'ExamenFisico.png',
            'Diagnósticos' => 'Diagnosticos.png',
            'Diagnostics' => 'Diagnosticos.png',
            'Laboratorios' => 'Laboratorios.png',
            'Laboratory' => 'Laboratorios.png',
            'Service Request' => 'Laboratorios.png',
            'Dermatología' => 'Dermatologia.png',
            'Dermatology' => 'Dermatologia.png',
            'Urología' => 'Urologia.png',
            'Urology' => 'Urologia.png',
            'Procedimientos' => 'Procedimientos.png',
            'Imagenes' => 'Imagenes.png',
            'Procedures' => 'Procedimientos.png',
            'Referencia a Especialista' => 'RefEspecialista.png',
            'Referral' => 'RefEspecialista.png',
            'Medicamentos' => 'Medicamentos.png',
            'Medications' => 'Medicamentos.png',
            'Suministros' => 'Suministros.png',
            'Supplies' => 'Suministros.png',
            'Servicios Facturables' => 'ServiciosFacturables.png',
            'Services' => 'ServiciosFacturables.png',
            'Nota General' => 'NotaGeneral.png',
            'General Note' => 'NotaGeneral.png',
            'Archivos de Consulta' => 'ArchivosConsulta.png',
            'File Upload' => 'ArchivosConsulta.png',
            'Pediatría' => 'Pediatria.png',
        ];

        // Obtener todas las secciones
        $sections = EncounterSection::all();

        foreach ($sections as $section) {
            $iconPath = null;

            // Intentar encontrar por nombre en español
            if (isset($iconPathMapping[$section->name_esp])) {
                $iconPath = $iconPathMapping[$section->name_esp];
            }
            // Intentar por nombre en inglés
            elseif (isset($iconPathMapping[$section->name])) {
                $iconPath = $iconPathMapping[$section->name];
            }
            // Buscar por palabras clave
            else {
                $iconPath = $this->getIconPathByKeywords($section->name_esp ?? $section->name);
            }

            // Actualizar la sección con la ruta del icono
            if ($iconPath) {
                $section->update(['icon_path' => 'images/consultation_icons/' . $iconPath]);
                $this->command->info("Icon path asignado a: {$section->name_esp} → {$iconPath}");
            } else {
                $this->command->warn("No se encontró icono para: {$section->name_esp}");
            }
        }

        $this->command->info('Icon paths de secciones actualizados exitosamente.');
    }

    /**
     * Obtener ruta de icono basada en palabras clave
     */
    private function getIconPathByKeywords(string $name): ?string
    {
        $name = strtolower($name);

        if (str_contains($name, 'motivo') || str_contains($name, 'reason')) {
            return 'MotivoDeConsulta.png';
        }
        if (str_contains($name, 'vital') || str_contains($name, 'sign')) {
            return 'SignosVitales.png';
        }
        if (str_contains($name, 'enfermedad') || str_contains($name, 'illness')) {
            return 'EnfermedadActual.png';
        }
        if (str_contains($name, 'físico') || str_contains($name, 'physical') || str_contains($name, 'exam')) {
            return 'ExamenFisico.png';
        }
        if (str_contains($name, 'diagnóstico') || str_contains($name, 'diagnostic')) {
            return 'Diagnosticos.png';
        }
        if (str_contains($name, 'laboratorio') || str_contains($name, 'laboratory') || str_contains($name, 'lab')) {
            return 'Laboratorios.png';
        }
        if (str_contains($name, 'dermatolog')) {
            return 'Dermatologia.png';
        }
        if (str_contains($name, 'urolog')) {
            return 'Urologia.png';
        }
        if (str_contains($name, 'procedimiento') || str_contains($name, 'procedure')) {
            return 'Procedimientos.png';
        }
        if (str_contains($name, 'referencia') || str_contains($name, 'referral') || str_contains($name, 'especialista')) {
            return 'RefEspecialista.png';
        }
        if (str_contains($name, 'medicamento') || str_contains($name, 'medication') || str_contains($name, 'prescri')) {
            return 'Medicamentos.png';
        }
        if (str_contains($name, 'suministro') || str_contains($name, 'supply')) {
            return 'Suministros.png';
        }
        if (str_contains($name, 'servicio') || str_contains($name, 'service') || str_contains($name, 'facturable')) {
            return 'ServiciosFacturables.png';
        }
        if (str_contains($name, 'nota') || str_contains($name, 'note')) {
            return 'NotaGeneral.png';
        }
        if (str_contains($name, 'archivo') || str_contains($name, 'file')) {
            return 'ArchivosConsulta.png';
        }
        if (str_contains($name, 'crecimiento')) {
            return 'Pediatria.png';
        }
        if (str_contains($name, 'vacunación')) {
            return 'Vacuna-Pediatria.png';
        }
        if (str_contains($name, 'imagenes')) {
            return 'Images.png';
        }
        if (str_contains($name, 'prostáticos')) {
            return 'Urologia.png';
        }

        return null;
    }
}
