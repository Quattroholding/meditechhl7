<?php

namespace App\Enums;

enum PreferenceType: string
{
    case INVOICE_TEMPLATE = 'invoice_template';
    case MEDICAL_LEAVE_TEMPLATE = 'medical_leave_template';
    case PRESCRIPTION_TEMPLATE = 'prescription_template';
    case EMAIL_SETTINGS = 'email_settings';
    case NOTIFICATION_SETTINGS = 'notification_settings';
    case APPOINTMENT_SETTINGS = 'appointment_settings';
    case BILLING_SETTINGS = 'billing_settings';
    case DOCUMENT_TEMPLATE = 'document_template';
    case UI_PREFERENCES = 'ui_preferences';
    case LANGUAGE_SETTINGS = 'language_settings';
    case EXTERNAL_STORAGE = 'external_storage';

    public function label(): string
    {
        return match ($this) {
            self::INVOICE_TEMPLATE => 'Plantilla de Factura',
            self::MEDICAL_LEAVE_TEMPLATE => 'Plantilla de Licencia Médica',
            self::PRESCRIPTION_TEMPLATE => 'Plantilla de Prescripción Médica',
            self::EMAIL_SETTINGS => 'Configuración de Email',
            self::NOTIFICATION_SETTINGS => 'Configuración de Notificaciones',
            self::APPOINTMENT_SETTINGS => 'Configuración de Citas',
            self::BILLING_SETTINGS => 'Configuración de Facturación',
            self::DOCUMENT_TEMPLATE => 'Plantilla de Documentos',
            self::UI_PREFERENCES => 'Preferencias de Interfaz',
            self::LANGUAGE_SETTINGS => __('settings.language_configuration'),
            self::EXTERNAL_STORAGE => 'Almacenamiento Externo',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::INVOICE_TEMPLATE => 'Selecciona el diseño de tus facturas',
            self::MEDICAL_LEAVE_TEMPLATE => 'Selecciona el diseño de licencias médicas',
            self::PRESCRIPTION_TEMPLATE => 'Selecciona el diseño de prescripciones médicas',
            self::EMAIL_SETTINGS => 'Configura opciones de correo electrónico',
            self::NOTIFICATION_SETTINGS => 'Configura tus notificaciones',
            self::APPOINTMENT_SETTINGS => 'Configura opciones de citas',
            self::BILLING_SETTINGS => 'Configura opciones de facturación',
            self::DOCUMENT_TEMPLATE => 'Selecciona plantillas para documentos',
            self::UI_PREFERENCES => 'Personaliza tu interfaz',
            self::LANGUAGE_SETTINGS => __('settings.language_configuration_description'),
            self::EXTERNAL_STORAGE => 'Configura almacenamiento externo para archivos de consultas',
        };
    }
}
