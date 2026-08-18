<?php

namespace App\Services;

use App\Models\MedicalSpeciality;
use App\Models\Practitioner;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Str;

class PractitionerService
{
    /**
     * Crea o actualiza un practitioner asociado a un usuario
     *
     * @param  User  $user  Usuario al que se asociará el practitioner
     * @param  array  $data  Datos del practitioner (first_name, last_name, gender, birth_date, etc.)
     * @param  array  $specialties  IDs de especialidades médicas (opcional)
     */
    public function createOrUpdatePractitioner(User $user, array $data, array $specialties = []): Practitioner
    {
        // Buscar practitioner existente por email o user_id
        $practitioner = Practitioner::where('email', $user->email)
            ->orWhere('user_id', $user->id)
            ->first();

        if (! $practitioner) {
            $practitioner = new Practitioner;
        }

        // Asignar datos básicos
        $this->fillPractitionerData($practitioner, $user, $data);

        // Guardar el practitioner
        $practitioner->save();

        // Sincronizar especialidades si se proporcionaron
        if (! empty($specialties)) {
            $this->syncSpecialties($practitioner, $specialties);
        }

        return $practitioner;
    }

    /**
     * Llena los datos del practitioner
     */
    private function fillPractitionerData(Practitioner $practitioner, User $user, array $data): void
    {
        // Determinar prefijo según género (solo si NO es estudiante de medicina)
        $prefix = '';
        $isMedicalStudent = $data['is_medical_student'] ?? false;

        if ($user->hasRole('practitioner') && ! $isMedicalStudent) {
            $prefix = ($data['gender'] ?? 'male') === 'female' ? 'Dra. ' : 'Dr. ';
        }

        // Nombre completo
        $firstName = $data['first_name'] ?? $user->first_name ?? '';
        $lastName = $data['last_name'] ?? $user->last_name ?? '';

        $practitioner->name = $prefix.trim($firstName.' '.$lastName);
        $practitioner->given_name = $firstName;
        $practitioner->family_name = $lastName;

        // Identificación
        $practitioner->identifier_type = $data['identifier_type'] ?? $data['id_type'] ?? 'CC';
        $practitioner->identifier = $data['id_number'] ?? $data['identifier'] ?? null;

        // Registro y licencia
        $practitioner->registry = $data['registry'] ?? Str::random(6);
        $practitioner->licence_code = $data['licence_code'] ?? null;

        // Contacto
        $practitioner->email = $user->email;
        $practitioner->phone = $data['full_phone'] ?? $data['phone'] ?? $data['whatsapp'] ?? $user->whatsapp_phone ?? null;

        // Dirección y género
        $practitioner->address = $data['address'] ?? null;
        $practitioner->gender = $data['gender'] ?? 'male';

        // Fecha de nacimiento
        if (isset($data['birth_date'])) {
            $practitioner->birth_date = $this->parseBirthDate($data['birth_date']);
        }

        // FHIR ID si no existe
        if (empty($practitioner->fhir_id)) {
            $practitioner->fhir_id = 'practitioner-'.Str::uuid();
        }

        // Asociar usuario
        $practitioner->user_id = $user->id;

        // Estudiante de medicina
        $practitioner->is_medical_student = $data['is_medical_student'] ?? false;
        if (isset($data['estimated_graduation_date'])) {
            $practitioner->estimated_graduation_date = $this->parseBirthDate($data['estimated_graduation_date']);
        }
    }

    /**
     * Parsea la fecha de nacimiento desde diferentes formatos
     */
    private function parseBirthDate(string $birthDate): ?string
    {
        try {
            // Intentar formato d/m/Y
            $fecha = DateTime::createFromFormat('d/m/Y', $birthDate);
            if ($fecha) {
                $fecha->setTime(0, 0, 0);

                return $fecha->format('Y-m-d H:i:s');
            }

            // Intentar formato Y-m-d
            $fecha = DateTime::createFromFormat('Y-m-d', $birthDate);
            if ($fecha) {
                $fecha->setTime(0, 0, 0);

                return $fecha->format('Y-m-d H:i:s');
            }

            // Si no se puede parsear, intentar con Carbon
            return Carbon::parse($birthDate)->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Sincroniza las especialidades del practitioner
     *
     * @param  array  $specialtyIds  IDs de especialidades médicas
     */
    private function syncSpecialties(Practitioner $practitioner, array $specialtyIds): void
    {
        $sync = [];
        $i = 0;

        foreach ($specialtyIds as $specialtyId) {
            $speciality = MedicalSpeciality::find($specialtyId);
            if (! $speciality) {
                continue;
            }

            $default = ($i === 0); // La primera especialidad es la predeterminada

            $sync[$specialtyId] = [
                'code' => $specialtyId,
                'default' => $default,
                'medical_speciality_id' => $specialtyId,
                'practitioner_id' => $practitioner->id,
                'display' => $speciality->name,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ];

            $i++;
        }

        $practitioner->specialties()->sync($sync);
    }

    /**
     * Crea un practitioner básico con datos mínimos (para UserController)
     * cuando no se tienen todos los datos completos
     */
    public function createBasicPractitioner(User $user): Practitioner
    {
        $data = [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'gender' => 'male', // Valor por defecto
            'registry' => 'N/A',
            'licence_code' => 'N/A',
            'phone' => $user->phone,
        ];

        return $this->createOrUpdatePractitioner($user, $data);
    }
}
