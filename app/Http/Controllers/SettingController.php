<?php

namespace App\Http\Controllers;

use App\Enums\InvoiceStatus;
use App\Enums\PaymentStatus;
use App\Models\Client;
use Carbon\Carbon;

class SettingController extends Controller
{
    public function consultationTemplate()
    {
        return view('settings.consultation.create');
    }

    public function rapidAccess()
    {
        return view('settings.rapidAccess.create');
    }

    public function cptUser()
    {
        return view('settings.cpt-user');
    }

    public function workingHourUser()
    {
        return view('settings.working_hour.create');
    }

    public function createUserProcedure()
    {
        return view('settings.procedures.create');
    }

    public function uploadSignatureSeal($practitioner_id)
    {
        return view('settings.practitioners.signature_and_seal', compact('practitioner_id'));
    }

    /**
     * Gestión de temas por cliente
     */
    public function themeManager($client_id)
    {
        $client = Client::findOrFail($client_id);

        // Verificar permisos - solo admin o usuarios del cliente pueden acceder
        if (! auth()->user()->hasRole('admin') && ! auth()->user()->clients->contains($client_id)) {
            abort(403, 'No tienes permisos para acceder a la configuración de este cliente.');
        }

        return view('settings.theme-manager', compact('client_id', 'client'));
    }

    /**
     * Selección de plantilla de factura
     */
    public function invoiceTemplate()
    {
        $client = auth()->user()->getCurrentClient();

        if (! $client) {
            abort(403, 'No tiene un cliente asociado');
        }

        return view('settings.invoice-template');
    }

    /**
     * Vista previa de plantilla de factura
     */
    public function invoiceTemplatePreview($template)
    {
        // Validate template name
        if (! preg_match('/^template_\d+$/', $template)) {
            abort(404, 'Plantilla no encontrada');
        }

        // Create sample branch with address
        $branch = (object) [
            'name' => 'Clínica Ejemplo S.A.',
            'address' => 'Calle Principal #123, Ciudad de Panamá',
            'phone' => '+507 123-4567',
            'email' => 'info@clinicaejemplo.com',
        ];

        // Create sample consulting room
        $consultingRoom = (object) [
            'name' => 'Consultorio 1',
            'branch' => $branch,
        ];

        // Create sample appointment
        $appointment = (object) [
            'consultingRoom' => $consultingRoom,
        ];

        // Create sample encounter
        $encounter = (object) [
            'identifier' => 'ENC-2026-0001',
            'start' => now()->subDays(7),
            'end' => now()->subDays(7)->addHour(),
            'appointment' => $appointment,
            'status' => 'finished',
        ];

        // Create sample patient
        $patient = (object) [
            'name' => 'Juan Pérez García',
            'identifier' => '8-123-456',
            'identifier_type' => 'CEDULA',
            'email' => 'juan.perez@example.com',
            'phone' => '+507 6234-5678',
            'address' => 'Avenida Central, Ciudad de Panamá',
            'birth_date' => '1985-05-15',
        ];

        // Create sample practitioner (doctor)
        $practitioner = (object) [
            'name' => 'Dr. María González',
            'license_number' => 'MED-12345',
            'registry' => 'MED-12345',
            'specialty' => 'Medicina General',
            'email' => 'dra.gonzalez@clinicaejemplo.com',
            'phone' => '+507 123-4567 ext. 101',
        ];

        // Create sample organization/client
        $organization = (object) [
            'name' => 'Clínica Ejemplo S.A.',
            'ruc' => '123456-1-123456 DV 78',
            'address' => 'Calle Principal #123, Ciudad de Panamá',
            'phone' => '+507 123-4567',
            'whatsapp' => '+507 6234-5678',
            'email' => 'info@clinicaejemplo.com',
            'logo' => '',
        ];

        // Create sample invoice data for preview
        $invoice = (object) [
            'identifier' => 'SAMPLE-2026-0001',
            'invoice_number' => 'FAC-2026-0001',
            'issue_date' => now(),
            'due_date' => now()->addDays(15),
            'status' => InvoiceStatus::PAID,
            'payment_status' => PaymentStatus::COMPLETED,
            'payment_terms' => 'Pago dentro de 15 días',
            'currency' => 'USD',
            'subtotal' => 285.00,
            'tax_amount' => 19.95,
            'discount_amount' => 0,
            'total' => 304.95,
            'notes' => 'Esta es una factura de ejemplo para vista previa. Gracias por su confianza.',
            'encounter' => $encounter,
            'patient' => $patient,
        ];

        // Sample line items
        $lineItems = collect([
            (object) [
                'sequence' => 1,
                'service_code' => 'CONS-001',
                'service_description' => 'Consulta General',
                'quantity' => 1,
                'unit_price' => 75.00,
                'line_total_gross' => 75.00,
            ],
            (object) [
                'sequence' => 2,
                'service_code' => 'LAB-002',
                'service_description' => 'Análisis de Laboratorio - Hemograma Completo',
                'quantity' => 1,
                'unit_price' => 45.00,
                'line_total_gross' => 45.00,
            ],
            (object) [
                'sequence' => 3,
                'service_code' => 'ECG-003',
                'service_description' => 'Electrocardiograma (ECG)',
                'quantity' => 1,
                'unit_price' => 65.00,
                'line_total_gross' => 65.00,
            ],
            (object) [
                'sequence' => 4,
                'service_code' => 'CONS-004',
                'service_description' => 'Consulta de Seguimiento',
                'quantity' => 2,
                'unit_price' => 50.00,
                'line_total_gross' => 100.00,
            ],
        ]);

        // Calculate totals
        $subtotal = 285.00;
        $tax = 19.95;
        $total = 304.95;

        // Generate date
        $generateDate = now();

        // Check if template exists
        $templatePath = "Invoice.templates.{$template}";
        if (! view()->exists($templatePath)) {
            abort(404, 'Plantilla no encontrada');
        }

        // Return preview view with all required variables
        return view($templatePath)
            ->with('invoice', $invoice)
            ->with('patient', $patient)
            ->with('lineItems', $lineItems)
            ->with('encounter', $encounter)
            ->with('practitioner', $practitioner)
            ->with('organization', $organization)
            ->with('subtotal', $subtotal)
            ->with('tax', $tax)
            ->with('total', $total)
            ->with('generateDate', $generateDate)
            ->with('isPreview', true);
    }

    /**
     * Selección de plantilla de licencia médica
     */
    public function medicalLeaveTemplate()
    {
        $client = auth()->user()->getCurrentClient();

        if (! $client) {
            abort(403, 'No tiene un cliente asociado');
        }

        return view('settings.medical-leave-template');
    }

    /**
     * Vista previa de plantilla de licencia médica
     */
    public function medicalLeaveTemplatePreview($template)
    {
        // Validate template name
        if (! preg_match('/^medical_leave_\d+$/', $template)) {
            abort(404, 'Plantilla no encontrada');
        }

        $medicalLeave = (object) [
            'identifier' => 'LM-20260128-JKQPIL',
            'practitioner_name' => 'Dr Federico Fonseca',
            'patient_name' => 'Rafael Gasperi',
            'duration' => '5',
            'total_days' => 5,
            'encounter' => 1,
            'address' => 'Calle Principal #123, Ciudad de Panamá',
            'phone' => '+507 123-4567',
            'email' => 'info@clinicaejemplo.com',
            'practitioner_license_number' => '86578',
        ];

        // Create sample branch
        $branch = (object) [
            'name' => 'Clínica Ejemplo S.A.',
            'address' => 'Calle Principal #123, Ciudad de Panamá',
            'phone' => '+507 123-4567',
            'email' => 'info@clinicaejemplo.com',
        ];

        // Create sample patient with birth_date as Carbon instance
        $patient = (object) [
            'full_name' => 'Juan Pérez García',
            'identifier_value' => '8-123-456',
            'identifier_type' => 'CEDULA',
            'birth_date' => Carbon::parse('1985-05-15'),
            'age' => 38,
        ];

        // Create sample doctor
        $doctor = (object) [
            'full_name' => 'Dra. María González López',
            'specialty' => 'Medicina General',
            'license' => 'MED-12345',
        ];

        // Sample data for medical leave
        $diagnosis = 'Faringitis aguda con fiebre - J02.9';
        $startDate = now()->format('d/m/Y');
        $endDate = now()->addDays(5)->format('d/m/Y');
        $start_time = now()->format('H:i');
        $start_day = now()->format('d');
        $start_month = now()->format('m');
        $start_year = now()->format('Y');
        $end_time = now()->addDays(5)->format('H:i');
        $end_day = now()->addDays(5)->format('d');
        $end_month = now()->addDays(5)->format('m');
        $end_year = now()->addDays(5)->format('Y');
        $days = 5;
        $city = 'Ciudad de Panamá';
        $issueDate = now()->format('d/m/Y');
        $documentNumber = 'LM-2026-0001';

        // Check if template exists
        $templatePath = "templates.medical_leave.{$template}";
        if (! view()->exists($templatePath)) {
            abort(404, 'Plantilla no encontrada');
        }

        // Return preview view with all required variables
        return view($templatePath)
            ->with('medicalLeave', $medicalLeave)
            ->with('branch', $branch)
            ->with('patient', $patient)
            ->with('doctor', $doctor)
            ->with('diagnosis', $diagnosis)
            ->with('startDate', $startDate)
            ->with('endDate', $endDate)

            ->with('start_time', $start_time)
            ->with('start_day', $start_day)
            ->with('start_month', $start_month)
            ->with('start_year', $start_year)
            ->with('end_time', $end_time)
            ->with('end_day', $end_day)
            ->with('end_month', $end_month)
            ->with('end_year', $end_year)

            ->with('days', $days)
            ->with('city', $city)
            ->with('issueDate', $issueDate)
            ->with('documentNumber', $documentNumber)
            ->with('isPreview', true)
            ->with('firma', '')
            ->with('sello', '')
            ->with('logo', '');
    }

    /**
     * Selección de plantilla de prescripción médica
     */
    public function prescriptionTemplate()
    {
        $client = auth()->user()->getCurrentClient();

        if (! $client) {
            abort(403, 'No tiene un cliente asociado');
        }

        return view('settings.prescription-template');
    }

    /**
     * Vista previa de plantilla de prescripción médica
     */
    public function prescriptionTemplatePreview($template)
    {
        // Validate template name
        if (! preg_match('/^template_\d+$/', $template)) {
            abort(404, 'Plantilla no encontrada');
        }

        // Create sample client
        $client = (object) [
            'name' => 'Clínica Ejemplo S.A.',
            'address' => 'Calle Principal #123, Ciudad de Panamá',
            'phone' => '+507 123-4567',
            'email' => 'info@clinicaejemplo.com',
            'whatsapp' => '+507 6234-5678',
        ];

        // Create sample branch with address
        $branch = (object) [
            'name' => 'Clínica Ejemplo S.A.',
            'address' => 'Calle Principal #123, Ciudad de Panamá',
            'phone' => '+507 123-4567',
            'email' => 'info@clinicaejemplo.com',
        ];

        // Create sample consulting room
        $consultingRoom = (object) [
            'name' => 'Consultorio 1',
            'branch' => $branch,
        ];

        // Create sample medical speciality
        $medicalSpeciality = (object) [
            'name' => 'Medicina General',
        ];

        // Create sample appointment
        $appointment = (object) [
            'consultingRoom' => $consultingRoom,
            'client' => $client,
            'medicalSpeciality' => $medicalSpeciality,
        ];

        // Create sample encounter
        $encounter = (object) [
            'identifier' => 'ENC-2026-0001',
            'start' => now()->subDays(7),
            'end' => now()->subDays(7)->addHour(),
            'appointment' => $appointment,
            'status' => 'finished',
            'created_at' => now()->subDays(7),
        ];

        // Create sample patient
        $patient = (object) [
            'name' => 'Juan Pérez García',
            'identifier' => '8-123-456',
            'identifier_type' => 'CEDULA',
            'email' => 'juan.perez@example.com',
            'phone' => '+507 6234-5678',
            'address' => 'Avenida Central, Ciudad de Panamá',
            'birth_date' => '1985-05-15',
            'age' => 40,
        ];

        // Create sample practitioner (doctor)
        $practitioner = (object) [
            'name' => 'Dr. María González',
            'license_number' => 'MED-12345',
            'registry' => 'MED-12345',
            'specialty' => 'Medicina General',
            'email' => 'dra.gonzalez@clinicaejemplo.com',
            'phone' => '+507 123-4567 ext. 101',
        ];

        // Create sample diagnoses
        $diagnoses = collect([
            (object) [
                'code' => 'J06.9',
                'display' => 'Infección aguda de las vías respiratorias superiores',
                'note' => null,
                'condition' => (object) [
                    'icd10Code' => (object) [
                        'code' => 'J06.9',
                        'description_es' => 'Infección aguda de las vías respiratorias superiores',
                    ],
                    'onset_info' => 'Infección aguda de las vías respiratorias superiores',
                    'clinical_status' => 'active',
                ],
            ],
        ]);

        // Create sample service requests (prescriptions)
        $serviceRequests = collect([
            (object) [
                'code' => 'RX-001',
                'service_type' => 'medication',
                'cpt' => [
                    'description_es' => 'Amoxicilina 500mg - Vía Oral',
                ],
                'body_site' => null,
                'patient_instruction' => 'Tomar 1 cápsula cada 8 horas por 7 días',
                'note' => 'Tomar con alimentos para evitar molestias gástricas',
                'reason_code' => 'Infección respiratoria',
            ],
            (object) [
                'code' => 'RX-002',
                'service_type' => 'medication',
                'cpt' => [
                    'description_es' => 'Ibuprofeno 400mg - Vía Oral',
                ],
                'body_site' => null,
                'patient_instruction' => 'Tomar 1 tableta cada 6-8 horas según necesidad',
                'note' => 'No exceder 4 dosis al día. Suspender si hay molestias gástricas',
                'reason_code' => 'Dolor y fiebre',
            ],
            (object) [
                'code' => 'RX-003',
                'service_type' => 'medication',
                'cpt' => [
                    'description_es' => 'Loratadina 10mg - Vía Oral',
                ],
                'body_site' => null,
                'patient_instruction' => 'Tomar 1 tableta cada 24 horas',
                'note' => 'Preferiblemente en la noche',
                'reason_code' => 'Alergia estacional',
            ],
        ]);

        // Create sample medications for prescription template preview
        $medications = collect([
            (object) [
                'medication_id' => null,
                'medication_id2' => null,
                'medication' => 'Amoxicilina 500mg',
                'medicine' => null,
                'medication2' => null,
                'quantity' => 21,
                'dosage_text' => 'Tomar 1 cápsula cada 8 horas por 7 días con alimentos',
                'dosage_instruction' => 'Cada 8 horas',
            ],
            (object) [
                'medication_id' => null,
                'medication_id2' => null,
                'medication' => 'Ibuprofeno 400mg',
                'medicine' => null,
                'medication2' => null,
                'quantity' => 20,
                'dosage_text' => 'Tomar 1 tableta cada 6-8 horas según necesidad. No exceder 4 dosis al día',
                'dosage_instruction' => 'Cada 6-8 horas',
            ],
            (object) [
                'medication_id' => null,
                'medication_id2' => null,
                'medication' => 'Loratadina 10mg',
                'medicine' => null,
                'medication2' => null,
                'quantity' => 30,
                'dosage_text' => 'Tomar 1 tableta cada 24 horas, preferiblemente en la noche',
                'dosage_instruction' => 'Una vez al día',
            ],
        ]);

        // Generate order number
        $orderNumber = 'RX-2026-0001';

        // Create sample doctor profile to avoid calling methods on practitioner
        $doctorProfile = (object) [
            'facility' => 'Clínica Ejemplo S.A.',
            'address' => 'Calle Principal #123, Ciudad de Panamá',
            'phone' => '+507 123-4567',
            'signature' => null,
            'seal' => null,
            'logo' => null,
        ];

        // Check if template exists
        $templatePath = "documents.prescriptions.templates.{$template}";
        if (! view()->exists($templatePath)) {
            abort(404, 'Plantilla no encontrada');
        }

        // Return preview view with all required variables
        return view($templatePath)
            ->with('medications', $medications)
            ->with('prescriptionNumber', $orderNumber)
            ->with('orderNumber', $orderNumber)
            ->with('serviceRequests', $serviceRequests)
            ->with('practitioner', $practitioner)
            ->with('encounter', $encounter)
            ->with('patient', $patient)
            ->with('diagnoses', $diagnoses)
            ->with('doctorProfile', $doctorProfile)
            ->with('client', null)
            ->with('pdfService', null)
            ->with('serviceType', null)
            ->with('isPreview', true);
    }
}
