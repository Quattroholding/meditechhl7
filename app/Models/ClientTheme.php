<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientTheme extends Model
{
    protected $fillable = [
        'client_id',
        // Colores principales
        'primary_color',
        'secondary_color',
        'accent_color',
        'success_color',
        'warning_color',
        'danger_color',
        'info_color',
        // Colores de interfaz
        'text_color',
        'text_muted_color',
        'background_color',
        'sidebar_color',
        'sidebar_text_color',
        'header_color',
        'border_color',
        // Colores para documentos médicos
        'document_header_color',
        'document_title_color',
        'table_header_color',
        'diagnosis_accent_color',
        // Archivos de branding
        'logo_url',
        'favicon_url',
        'logo_sidebar_url',
        // CSS personalizado
        'custom_css',
        // Configuraciones adicionales
        'font_family',
        'dark_mode',
        'is_active',
    ];

    protected $casts = [
        'dark_mode' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Relación con Client
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Obtener variables CSS para la aplicación web
     */
    public function getCssVariables(): array
    {
        return [
            '--primary-color' => $this->primary_color,
            '--secondary-color' => $this->secondary_color,
            '--accent-color' => $this->accent_color,
            '--success-color' => $this->success_color,
            '--warning-color' => $this->warning_color,
            '--danger-color' => $this->danger_color,
            '--info-color' => $this->info_color,
            '--text-color' => $this->text_color,
            '--text-muted-color' => $this->text_muted_color,
            '--background-color' => $this->background_color,
            '--sidebar-color' => $this->sidebar_color,
            '--sidebar-text-color' => $this->sidebar_text_color,
            '--header-color' => $this->header_color,
            '--border-color' => $this->border_color,
            '--font-family' => $this->font_family,
        ];
    }

    /**
     * Obtener variables CSS para documentos PDF
     */
    public function getPdfCssVariables(): array
    {
        return [
            '--document-header-color' => $this->document_header_color,
            '--document-title-color' => $this->document_title_color,
            '--table-header-color' => $this->table_header_color,
            '--diagnosis-accent-color' => $this->diagnosis_accent_color,
            '--primary-color' => $this->primary_color,
            '--secondary-color' => $this->secondary_color,
            '--text-color' => $this->text_color,
            '--text-muted-color' => $this->text_muted_color,
            '--font-family' => $this->font_family,
        ];
    }

    /**
     * Generar CSS completo para la aplicación web
     */
    public function generateWebCSS(): string
    {
        $variables = $this->getCssVariables();
        $css = ':root {';

        foreach ($variables as $property => $value) {
            $css .= $property.': '.$value.';';
        }

        $css .= '}';

        // Agregar CSS personalizado si existe
        if ($this->custom_css) {
            $css .= "\n".$this->custom_css;
        }

        return $css;
    }

    /**
     * Generar CSS para documentos PDF
     */
    public function generatePdfCSS(): string
    {
        $variables = $this->getPdfCssVariables();

        $css = '
        <style>
            /* Variables CSS para PDFs */
            :root {';

        foreach ($variables as $property => $value) {
            $css .= $property.': '.$value.';';
        }

        $css .= '}
            
            /* Estilos personalizados para PDFs */
            body {
                font-family: var(--font-family);
                color: var(--text-color);
            }
            
            .header {
                border-bottom-color: var(--document-header-color);
            }
            
            .clinic-name {
                color: var(--document-header-color);
            }
            
            .document-title {
                color: var(--document-title-color);
            }
            
            .section-title {
                color: var(--text-color);
                border-bottom-color: var(--border-color, #bdc3c7);
            }
            
            .diagnosis-section {
                border-left-color: var(--diagnosis-accent-color);
            }
            
            .medication-table th,
            .service-table th {
                background-color: var(--table-header-color);
            }
            
            .label {
                color: var(--text-color);
            }
            
            .prescription-number,
            .order-number,
            .date-issued,
            .signature-label {
                color: var(--text-muted-color);
            }
        </style>';

        return $css;
    }

    /**
     * Crear tema por defecto para un cliente
     */
    public static function createDefaultForClient($client_id): self
    {
        return self::create([
            'client_id' => $client_id,
            'is_active' => true,
        ]);
    }

    /**
     * Obtener tema activo para un cliente
     */
    public static function getActiveForClient($client_id): ?self
    {
        return self::where('client_id', $client_id)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Restablecer valores por defecto
     */
    public function resetToDefaults(): void
    {
        $this->update([
            'primary_color' => '#3498db',
            'secondary_color' => '#2ecc71',
            'accent_color' => '#e74c3c',
            'success_color' => '#27ae60',
            'warning_color' => '#f39c12',
            'danger_color' => '#e74c3c',
            'info_color' => '#3498db',
            'text_color' => '#2c3e50',
            'text_muted_color' => '#7f8c8d',
            'background_color' => '#ffffff',
            'sidebar_color' => '#34495e',
            'sidebar_text_color' => '#ffffff',
            'header_color' => '#2c3e50',
            'border_color' => '#dee2e6',
            'document_header_color' => '#2c3e50',
            'document_title_color' => '#e74c3c',
            'table_header_color' => '#34495e',
            'diagnosis_accent_color' => '#3498db',
            'font_family' => 'Arial, sans-serif',
            'dark_mode' => false,
            'custom_css' => null,
        ]);
    }

    /**
     * Verificar si tiene logo personalizado
     */
    public function hasLogo(): bool
    {
        return ! empty($this->logo_url);
    }

    /**
     * Verificar si tiene favicon personalizado
     */
    public function hasFavicon(): bool
    {
        return ! empty($this->favicon_url);
    }
}
