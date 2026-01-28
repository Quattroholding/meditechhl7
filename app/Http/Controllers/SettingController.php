<?php

namespace App\Http\Controllers;

use App\Enums\InvoiceStatus;
use App\Enums\PaymentStatus;
use App\Models\Client;

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
                'sequence'=>1,
                'service_code' => 'CONS-001',
                'service_description' => 'Consulta General',
                'quantity' => 1,
                'unit_price' => 75.00,
                'line_total_gross' => 75.00,
            ],
            (object) [
                'sequence'=>2,
                'service_code' => 'LAB-002',
                'service_description' => 'Análisis de Laboratorio - Hemograma Completo',
                'quantity' => 1,
                'unit_price' => 45.00,
                'line_total_gross' => 45.00,
            ],
            (object) [
                'sequence'=>3,
                'service_code' => 'ECG-003',
                'service_description' => 'Electrocardiograma (ECG)',
                'quantity' => 1,
                'unit_price' => 65.00,
                'line_total_gross' => 65.00,
            ],
            (object) [
                'sequence'=>4,
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
}
