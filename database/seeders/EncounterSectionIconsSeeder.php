<?php

namespace Database\Seeders;

use App\Models\EncounterSection;
use Illuminate\Database\Seeder;

class EncounterSectionIconsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mapeo de iconos por componente Livewire
        $iconMapping = [
            // Componentes principales
            'consultation.reason' => '🗣️',
            'consultation.vital-signs' => '❤️',
            'consultation.present-illness' => '🤒',
            'consultation.physical-exam' => '🩺',
            'consultation.diagnostics' => '📋',
            'consultation.service-request' => '🧪',
            'consultation.medication-requests' => '💊',
            'consultation.procedures' => '✂️',
            'consultation.referral' => '🤝',
            'consultation.general-note' => '📝',
            'consultation.file-upload' => '📁',
            'consultation.services' => '💲',
            'consultation.supply-requests' => '📦',

            // Especialidades
            'consultation.dermatology' => '🔬',
            'consultation.urologia' => '🏥',
            'consultation.speciality-questions' => '❓',
            'consultation.growth-tracking' => '📏',
            'consultation.vaccination-record' => '💉',
            'consultation.patient-history' => '📚',
        ];

        // Mapeo por nombre de sección (español)
        $nameMapping = [
            'Motivo de Consulta' => '🗣️',
            'Signos Vitales' => '❤️',
            'Enfermedad Actual' => '🤒',
            'Examen Físico' => '🩺',
            'Diagnósticos' => '📋',
            'Laboratorios' => '🧪',
            'Imágenes' => '🩻',
            'Procedimientos' => '✂️',
            'Referencia a Especialista' => '🤝',
            'Medicamentos' => '💊',
            'Suministros' => '📦',
            'Servicios Facturables' => '💲',
            'Nota General' => '📝',
            'Archivos de Consulta' => '📁',
            'Dermatología' => '🔬',
            'Urología' => '🏥',
            'Preguntas de Especialidad' => '❓',
            'Seguimiento de Crecimiento' => '📏',
            'Registro de Vacunación' => '💉',
            'Historia del Paciente' => '📚',
        ];

        // Obtener todas las secciones
        $sections = EncounterSection::all();

        foreach ($sections as $section) {
            $icon = null;

            // Intentar encontrar por componente Livewire
            if (isset($iconMapping[$section->livewire_component_name])) {
                $icon = $iconMapping[$section->livewire_component_name];
            }
            // Intentar por nombre en español
            elseif (isset($nameMapping[$section->name_esp])) {
                $icon = $nameMapping[$section->name_esp];
            }
            // Intentar por nombre en inglés
            elseif (isset($nameMapping[$section->name])) {
                $icon = $nameMapping[$section->name];
            }
            // Buscar palabras clave en el nombre
            else {
                $icon = $this->getIconByKeywords($section->name_esp ?? $section->name);
            }

            // Actualizar la sección con el icono
            if ($icon) {
                $section->update(['icon' => $icon]);
                $this->command->info("Icono {$icon} asignado a: {$section->name_esp}");
            } else {
                // Icono por defecto
                $section->update(['icon' => '📄']);
                $this->command->warn("Icono por defecto 📄 asignado a: {$section->name_esp}");
            }
        }

        $this->command->info('Iconos de secciones actualizados exitosamente.');
    }

    /**
     * Obtener icono basado en palabras clave
     */
    private function getIconByKeywords(string $name): ?string
    {
        $name = strtolower($name);

        if (str_contains($name, 'motivo') || str_contains($name, 'reason') || str_contains($name, 'queja')) {
            return '🗣️';
        }
        if (str_contains($name, 'vital') || str_contains($name, 'sign')) {
            return '❤️';
        }
        if (str_contains($name, 'enfermedad') || str_contains($name, 'illness')) {
            return '🤒';
        }
        if (str_contains($name, 'físico') || str_contains($name, 'physical') || str_contains($name, 'exam')) {
            return '🩺';
        }
        if (str_contains($name, 'diagnóstico') || str_contains($name, 'diagnostic')) {
            return '📋';
        }
        if (str_contains($name, 'laboratorio') || str_contains($name, 'laboratory') || str_contains($name, 'lab')) {
            return '🧪';
        }
        if (str_contains($name, 'imagen') || str_contains($name, 'image') || str_contains($name, 'radiología')) {
            return '🩻';
        }
        if (str_contains($name, 'procedimiento') || str_contains($name, 'procedure')) {
            return '✂️';
        }
        if (str_contains($name, 'referencia') || str_contains($name, 'referral') || str_contains($name, 'especialista')) {
            return '🤝';
        }
        if (str_contains($name, 'medicamento') || str_contains($name, 'medication') || str_contains($name, 'prescri')) {
            return '💊';
        }
        if (str_contains($name, 'suministro') || str_contains($name, 'supply')) {
            return '📦';
        }
        if (str_contains($name, 'servicio') || str_contains($name, 'service') || str_contains($name, 'facturable')) {
            return '💲';
        }
        if (str_contains($name, 'nota') || str_contains($name, 'note')) {
            return '📝';
        }
        if (str_contains($name, 'archivo') || str_contains($name, 'file')) {
            return '📁';
        }
        if (str_contains($name, 'vacuna') || str_contains($name, 'immun') || str_contains($name, 'vaccination')) {
            return '💉';
        }
        if (str_contains($name, 'crecimiento') || str_contains($name, 'growth')) {
            return '📏';
        }

        return null;
    }
}
