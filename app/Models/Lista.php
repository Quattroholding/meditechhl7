<?php

namespace App\Models;

use App\Enums\Gender;
use App\Enums\MaritalStatus;
use Illuminate\Database\Eloquent\Model;

class Lista extends Model
{
    public static function bloodTypes()
    {

        return [
            'A+' => 'A+',
            'A-' => 'A-',
            'B+' => 'B+',
            'B-' => 'B-',
            'AB+' => 'AB+',
            'AB-' => 'AB-',
            'O+' => '0+',
            'O-' => '0-',
            'RH+' => 'RH+',
            'RH-' => 'RH-',
        ];
    }

    public static function documentType()
    {

        return [
            'PA' => 'PA: Pasaporte',
            'CC' => 'CC: Cédula de ciudadania',
            'CE' => 'CE: Cédula extranjera',
            'PT' => 'PT: Permiso temporal de permanencia',
            // Opción de Seguro Social eliminado - solicitado por Dr. Rafael 9/12/25
            // 'SS' => 'SS: Número seguro social',
        ];
    }

    public static function gender()
    {
        return Gender::options();
    }

    public static function branchType()
    {

        return [
            'consultorio' => 'Consultorio Privado',
            'centro de salud' => 'Centro de atención primaria',
            'clinica' => 'Clinica',
            'hospital' => 'Hospital',
        ];
    }

    public static function medicalHistoryCategory()
    {

        return [
            'medication' => 'Medicamento',
            'allergy' => 'Alergia',
            'surgery' => 'Cirugía',
            'chronic-illness' => 'Enfermedad Crónica',
            'hospitalization' => 'Hospitalizacíon',
            'immunization' => 'Inmunizacíon',
            'family-history' => 'Historia Familiar',
            'social-history' => 'Historia Social',
            'other' => 'Otro',
        ];
    }

    public static function medicationVias()
    {

        return [
            'Oral' => 'Oral',
            'Sublingual' => 'Sublingual',
            'Intramuscular' => 'Intramuscular',
            'Subcutáneo' => 'Subcutáneo',
            'Otro' => 'Otro',
        ];
    }

    public static function userProcedureTypes()
    {

        return [
            'consulta' => 'Consulta',
            'injectable' => 'Injectable',
            'procedimiento' => 'Procedimiento',
            'otro' => 'Otro',
        ];
    }

    public static function maritalStatus()
    {
        return MaritalStatus::options();
    }

    public static function medicineTypes()
    {
        return Medicine::selectRaw('distinct(type) as type')->pluck('type', 'type')->toArray();
    }

    public static function medicineMgsTypes()
    {
        return Medicine::selectRaw('distinct(mgs_type) as type')->pluck('type', 'type')->toArray();
    }

    public static function conditionClinicalStatus()
    {
        return [
            'active' => __('status.active'),
            'recurrence' => __('status.recurrence'),
            'relapse' => __('status.relapse'),
            'inactive' => __('status.inactive'),
            'remission' => __('status.remission'),
            'resolved' => __('status.resolved'),
        ];
    }

    public static function conditionSeverity()
    {
        return [
            'mild' => __('status.mild'),
            'moderate' => __('status.moderate'),
            'severe' => __('status.severe'),
            'critical' => __('status.critical'),
        ];
    }
}
