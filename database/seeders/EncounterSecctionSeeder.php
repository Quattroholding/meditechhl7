<?php

namespace Database\Seeders;

use App\Models\EncounterSection;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EncounterSecctionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EncounterSection::create([
            'name'=>'Chief Complaint',
            'name_esp'=>'Queja Principal',
            'table_list'=>null,
            'table_list_filter'=>null,
            'livewire_component_name'=>'consultation.reason',
            'livewire_component_fields'=>array(array('name'=>'reason', 'type'=>'textarea','table_list_id'=>null))
        ]);

        EncounterSection::create([
            'name'=>'Vital Signs',
            'name_esp'=>'Signos Vitales',
            'table_list'=>'ClinicalObservationType',
            'table_list_filter'=>"category='vital_sign'",
            'livewire_component_name'=>'consultation.vital-signs',
            'livewire_component_fields'=>[
                ['name'=>'Temperatura Corporal', 'type'=>'number','table_list_id'=>1],
                ['name'=>'Frecuencia Cardíaca','type'=>'number', 'table_list_id'=>2],
                ['name'=>'Presión Arterial Sistólica', 'type'=>'number','table_list_id'=>3],
                ['name'=>'Presión Arterial Diastólica','type'=>'number', 'table_list_id'=>4],
                ['name'=>'Frecuencia Respiratoria', 'type'=>'number','table_list_id'=>5],
                ['name'=>'Saturación de Oxígeno','type'=>'number', 'table_list_id'=>6],
                ['name'=>'Peso Corporal', 'type'=>'number','table_list_id'=>7],
                ['name'=>'Talla','type'=>'number', 'table_list_id'=>8],
                ['name'=>'Índice de Masa Corporal', 'type'=>'number','table_list_id'=>9],
                ['name'=>'Glucemia Capilar','type'=>'number', 'table_list_id'=>10],
            ]
        ]);

        EncounterSection::create([
            'name'=>'Enfermedad Actual',
            'name_esp'=>'Present Illness',
            'table_list'=>'PresentIllnesType',
            'table_list_filter'=>"",
            'livewire_component_name'=>'consultation.present-illness',
            'livewire_component_fields'=>[
                ['name'=>'location', 'type'=>'number','table_list_id'=>[1,2,3,4,5,6,7,8,9,10,11,12,13,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,66,75,76]],
                ['name'=>'duration','type'=>'number', 'table_list_id'=>[14,15,16,17,18,19,20,21,22,23,24,25,26]],
                ['name'=>'severity', 'type'=>'number','table_list_id'=>[66,67,68,69]],
                ['name'=>'timing','type'=>'number', 'table_list_id'=>[70,71,72,73,74]],
            ]
        ]);

        EncounterSection::create([
            'name'=>'Physical Exams',
            'name_esp'=>'Examen Físico',
            'table_list'=>'ClinicalObservationType',
            'table_list_filter'=>"category='physycal_exam'",
            'livewire_component_name'=>'consultation.physical-exam',
            'livewire_component_fields'=>[
                ['name'=>'Examen Constitucional', 'type'=>'number','table_list_id'=>11],
                ['name'=>'Examen HEENT','type'=>'number', 'table_list_id'=>12],
                ['name'=>'Examen Oftalmológico', 'type'=>'number','table_list_id'=>13],
                ['name'=>'Examen Respiratorio','type'=>'number', 'table_list_id'=>14],
                ['name'=>'Examen Cardiovascular', 'type'=>'number','table_list_id'=>15],
                ['name'=>'Examen de Pecho/Senos', 'type'=>'number','table_list_id'=>16],
                ['name'=>'Examen Abdominal', 'table_list_id'=>17],
                ['name'=>'Examen Linfático', 'type'=>'number','table_list_id'=>18],
                ['name'=>'Examen Neurológico','type'=>'number', 'table_list_id'=>19],
                ['name'=>'Examen Osteomuscular', 'table_list_id'=>20],
                ['name'=>'Examen de Piel y Faneras', 'type'=>'number','table_list_id'=>21],
                ['name'=>'Examen del Estado Mental','type'=>'number', 'table_list_id'=>22],
            ]
        ]);

        EncounterSection::create([
            'name'=>'Conditions',
            'name_esp'=>'Diagnosticos',
            'table_list'=>'',
            'table_list_filter'=>"",
            'livewire_component_name'=>'consultation.diagnostics',
            'livewire_component_fields'=>[
                ['name'=>'Diagnosticos', 'type'=>'dropdown','table_list_id'=>'','api_path'=>'api/diagnostics'],
            ]
        ]);

        EncounterSection::create([
            'name'=>'Laboratories',
            'name_esp'=>'Laboratorios',
            'table_list'=>'',
            'table_list_filter'=>"",
            'livewire_component_name'=>'consultation.service-request',
            'livewire_component_fields'=>[
                ['name'=>'Laboratorios', 'type'=>'dropdown','table_list_id'=>'','api_path'=>'api/cpts/laboratory'],
            ]
        ]);

        EncounterSection::create([
            'name'=>'Images',
            'name_esp'=>'Imagenes',
            'table_list'=>'',
            'table_list_filter'=>"",
            'livewire_component_name'=>'consultation.service-request',
            'livewire_component_fields'=>[
                ['name'=>'Imagenes', 'type'=>'dropdown','table_list_id'=>'','api_path'=>'api/cpts/images'],
            ]
        ]);

        EncounterSection::create([
            'name'=>'Procedures',
            'name_esp'=>'Procedimientos',
            'table_list'=>'',
            'table_list_filter'=>"",
            'livewire_component_name'=>'consultation.service-request',
            'livewire_component_fields'=>[
                ['name'=>'Procedimentos', 'type'=>'dropdown','table_list_id'=>'','api_path'=>'api/cpts/procedure'],
            ]
        ]);

        EncounterSection::create([
            'name'=>'Procedures in situ',
            'name_esp'=>'Procedimientos en sitio',
            'table_list'=>'',
            'table_list_filter'=>"",
            'livewire_component_name'=>'consultation.procedures',
            'livewire_component_fields'=>[
                ['name'=>'Procedimentos', 'type'=>'dropdown','table_list_id'=>'','api_path'=>'api/cpts/procedure'],
            ]
        ]);

        EncounterSection::create([
            'name'=>'Referral Specialist',
            'name_esp'=>'Referencia Especialista',
            'table_list'=>'',
            'table_list_filter'=>"",
            'livewire_component_name'=>'consultation.referral',
            'livewire_component_fields'=>[
                ['name'=>'Especialidad', 'type'=>'dropdown','table_list_id'=>'','api_path'=>'api/medical_speciality'],
            ]
        ]);

        EncounterSection::create([
            'name'=>'Medicines',
            'name_esp'=>'Medicamentos',
            'table_list'=>'',
            'table_list_filter'=>"",
            'livewire_component_name'=>'consultation.medication-requests',
            'livewire_component_fields'=>[
                ['name'=>'Medicinas', 'type'=>'dropdown','table_list_id'=>'','api_path'=>'api/medicines'],
            ]
        ]);

        EncounterSection::create([
            'name'=>'International Prostate Symptom Index',
            'name_esp'=>' Índice Internacional de Síntomas Prostáticos',
            'table_list'=>'',
            'table_list_filter'=>"",
            'livewire_component_name'=>'consultation.urologia',
            'category'=>'Urologia',
            'medical_speciality_id'=>42,
            'livewire_component_fields'=>[
                [
                    'name'=>'Incomplete Emptying',
                    'name_esp'=>'Vaciado incompleto',
                    'type'=>'options',
                    'question'=>'¿Con qué frecuencia ha tenido la sensación de no vaciar la vejiga?',
                    'options'=>'{"0":"Nada","1":"Menos de 1 de cada 5 veces","2":"Menos de la mitad de las veces","3":"Cerca de la mitad de las veces","4":"Más de la mitad de las time","5":"Casi siempre"}',
                    'table_list_id'=>'',
                    'api_path'=>'api/medicines'
                ],
                [
                    'name'=>'Frecuency',
                    'name_esp'=>'Frecuencia',
                    'type'=>'options',
                    'question'=>'¿Con qué frecuencia ha tenido que orinar menos de cada dos horas?',
                    'options'=>'{"0":"Nada","1":"Menos de 1 de cada 5 veces","2":"Menos de la mitad de las veces","3":"Cerca de la mitad de las veces","4":"Más de la mitad de las time","5":"Casi siempre"}',
                    'table_list_id'=>'',
                    'api_path'=>'api/medicines'
                ],
                [
                    'name'=>'vaciado_incompleto',
                    'name_esp'=>'Intermittence',
                    'type'=>'options',
                    'question'=>'¿Con qué frecuencia ha notado que se detuvo y comenzó de nuevo varias veces al orinar?',
                    'options'=>'{"0":"Nada","1":"Menos de 1 de cada 5 veces","2":"Menos de la mitad de las veces","3":"Cerca de la mitad de las veces","4":"Más de la mitad de las time","5":"Casi siempre"}',
                    'table_list_id'=>'',
                    'api_path'=>'api/medicines'
                ],
                [
                    'name'=>'Urgency',
                    'name_esp'=>'Urgencia',
                    'type'=>'options',
                    'question'=>'¿Con qué frecuencia le ha resultado difícil posponer la micción?',
                    'options'=>'{"0":"Nada","1":"Menos de 1 de cada 5 veces","2":"Menos de la mitad de las veces","3":"Cerca de la mitad de las veces","4":"Más de la mitad de las time","5":"Casi siempre"}',
                    'table_list_id'=>'',
                    'api_path'=>'api/medicines'
                ],
                [
                    'name'=>'Weak Flow',
                    'name_esp'=>'Flujo Débil',
                    'type'=>'options',
                    'question'=>'¿Con qué frecuencia ha tenido un chorro de orina débil?',
                    'options'=>'{"0":"Nada","1":"Menos de 1 de cada 5 veces","2":"Menos de la mitad de las veces","3":"Cerca de la mitad de las veces","4":"Más de la mitad de las time","5":"Casi siempre"}',
                    'table_list_id'=>'',
                    'api_path'=>'api/medicines'
                ],
                [
                    'name'=>'Strap',
                    'name_esp'=>'Tirante',
                    'type'=>'options',
                    'question'=>'¿Con qué frecuencia ha tenido que esforzarse para empezar a orinar?',
                    'options'=>'{"0":"Nada","1":"Menos de 1 de cada 5 veces","2":"Menos de la mitad de las veces","3":"Cerca de la mitad de las veces","4":"Más de la mitad de las time","5":"Casi siempre"}',
                    'table_list_id'=>'',
                    'api_path'=>'api/medicines'
                ],
                [
                    'name'=>'Noturnal',
                    'name_esp'=>'Noturno',
                    'type'=>'options',
                    'question'=>'¿Cuántas veces suele levantarse por la noche para orinar?',
                    'options'=>'{"0":"Nunca","1":"1 Vez","2":"2 Veces ","3":"3 Veces","4":"4 Veces","5":"5 Veces o mas"}',
                    'table_list_id'=>'',
                    'api_path'=>'api/medicines'
                ],
                [
                    'name'=>'If you had to spend the rest of your life with your urinary condition as it is now, how would you feel about it?',
                    'name_esp'=>'¿Si tuviera que pasar el resto de su vida con su condición urinaria tal como está ahora, ¿cómo se sentiría al respecto?',
                    'type'=>'options',
                    'question'=>'¿Evaluación de la calidad de vida por síntomas urinarios?',
                    'options'=>'{"0":"Encantado","1":"Complacido","2":"Más satisfecho","3":"Mixto: igualmente satisfecho/insatisfecho","4":"Más bien insatisfecho","5":"Descontento","6 ":"Terrible"}',
                    'table_list_id'=>'',
                    'api_path'=>'api/medicines'
                ],
            ]
        ]);

    }
}
