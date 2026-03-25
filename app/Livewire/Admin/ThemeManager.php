<?php

namespace App\Livewire\Admin;

use App\Models\Client;
use App\Models\ClientTheme;
use App\Models\File;
use App\Services\FileService;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class ThemeManager extends Component
{
    use WithFileUploads;

    public $client_id;

    public $client;

    public $theme;

    public $preview_mode = false;

    // Colores principales
    public $primary_color;

    public $secondary_color;

    public $accent_color;

    public $success_color;

    public $warning_color;

    public $danger_color;

    public $info_color;

    // Colores de interfaz
    public $text_color;

    public $text_muted_color;

    public $background_color;

    public $sidebar_color;

    public $sidebar_text_color;

    public $header_color;

    public $border_color;

    // Colores para documentos médicos
    public $document_header_color;

    public $document_title_color;

    public $table_header_color;

    public $diagnosis_accent_color;

    // Configuraciones adicionales
    public $font_family;

    public $dark_mode;

    public $custom_css;

    // Archivos
    public $logo_file;

    public $favicon_file;

    public $logo_sidebar_file;

    // Estado
    public $uploading_logo = false;

    public $uploading_favicon = false;

    public $uploading_sidebar_logo = false;

    protected $rules = [
        'primary_color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
        'secondary_color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
        'accent_color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
        'success_color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
        'warning_color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
        'danger_color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
        'info_color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
        'text_color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
        'text_muted_color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
        'background_color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
        'sidebar_color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
        'sidebar_text_color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
        'header_color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
        'border_color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
        'document_header_color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
        'document_title_color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
        'table_header_color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
        'diagnosis_accent_color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
        'font_family' => 'required|string|max:255',
        'custom_css' => 'nullable|string',
        'logo_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'favicon_file' => 'nullable|image|mimes:ico,png|max:512',
        'logo_sidebar_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
    ];

    protected $messages = [
        '*.regex' => 'El color debe ser un código hexadecimal válido (ej: #FF0000)',
        'logo_file.image' => 'El logo debe ser una imagen',
        'logo_file.mimes' => 'El logo debe ser: jpeg, png, jpg, gif, svg',
        'favicon_file.mimes' => 'El favicon debe ser: ico, png',
        'logo_sidebar_file.mimes' => 'El logo del sidebar debe ser: jpeg, png, jpg, gif, svg',
    ];

    public function mount($client_id)
    {
        $this->client_id = $client_id;
        $this->client = Client::findOrFail($client_id);
        $this->loadTheme();
    }

    public function loadTheme()
    {
        $this->theme = $this->client->theme ?? ClientTheme::createDefaultForClient($this->client_id);

        // Cargar colores principales
        $this->primary_color = $this->theme->primary_color;
        $this->secondary_color = $this->theme->secondary_color;
        $this->accent_color = $this->theme->accent_color;
        $this->success_color = $this->theme->success_color;
        $this->warning_color = $this->theme->warning_color;
        $this->danger_color = $this->theme->danger_color;
        $this->info_color = $this->theme->info_color;

        // Cargar colores de interfaz
        $this->text_color = $this->theme->text_color;
        $this->text_muted_color = $this->theme->text_muted_color;
        $this->background_color = $this->theme->background_color;
        $this->sidebar_color = $this->theme->sidebar_color;
        $this->sidebar_text_color = $this->theme->sidebar_text_color;
        $this->header_color = $this->theme->header_color;
        $this->border_color = $this->theme->border_color;

        // Cargar colores para documentos médicos
        $this->document_header_color = $this->theme->document_header_color;
        $this->document_title_color = $this->theme->document_title_color;
        $this->table_header_color = $this->theme->table_header_color;
        $this->diagnosis_accent_color = $this->theme->diagnosis_accent_color;

        // Cargar configuraciones adicionales
        $this->font_family = $this->theme->font_family;
        $this->dark_mode = $this->theme->dark_mode;
        $this->custom_css = $this->theme->custom_css;
    }

    public function save()
    {
        $this->validate();

        try {
            $this->theme->update([
                'primary_color' => $this->primary_color,
                'secondary_color' => $this->secondary_color,
                'accent_color' => $this->accent_color,
                'success_color' => $this->success_color,
                'warning_color' => $this->warning_color,
                'danger_color' => $this->danger_color,
                'info_color' => $this->info_color,
                'text_color' => $this->text_color,
                'text_muted_color' => $this->text_muted_color,
                'background_color' => $this->background_color,
                'sidebar_color' => $this->sidebar_color,
                'sidebar_text_color' => $this->sidebar_text_color,
                'header_color' => $this->header_color,
                'border_color' => $this->border_color,
                'document_header_color' => $this->document_header_color,
                'document_title_color' => $this->document_title_color,
                'table_header_color' => $this->table_header_color,
                'diagnosis_accent_color' => $this->diagnosis_accent_color,
                'font_family' => $this->font_family,
                'dark_mode' => $this->dark_mode,
                'custom_css' => $this->custom_css,
            ]);

            session()->flash('message', 'Tema actualizado exitosamente.');
            $this->dispatch('theme-updated');

        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar el tema: '.$e->getMessage());
        }
    }

    public function uploadLogo()
    {
        $this->validate(['logo_file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048']);

        try {
            $this->uploading_logo = true;

            // Eliminar logo anterior si existe
            if ($this->theme->logo_url) {
                $this->deleteFileFromStorage($this->theme->logo_url);
            }

            // Subir nuevo logo
            $fileService = new FileService;
            $data = [
                'record_id' => $this->client_id,
                'folder' => 'branding',
                'type' => 'logo',
                'name' => 'logo_'.$this->client_id.'_'.time(),
            ];

            $files = $fileService->guardarArchivos([$this->logo_file], $data);

            if (! empty($files)) {
                $file = File::find($files[0]);
                $this->theme->update(['logo_url' => $file->path]);
            }

            // Limpiar archivo temporal
            $this->logo_file = null;
            $this->uploading_logo = false;

            session()->flash('message', 'Logo subido exitosamente.');

        } catch (\Exception $e) {
            $this->uploading_logo = false;
            session()->flash('error', 'Error al subir el logo: '.$e->getMessage());
        }
    }

    public function uploadFavicon()
    {
        $this->validate(['favicon_file' => 'required|image|mimes:ico,png|max:512']);

        try {
            $this->uploading_favicon = true;

            // Eliminar favicon anterior si existe
            if ($this->theme->favicon_url) {
                $this->deleteFileFromStorage($this->theme->favicon_url);
            }

            // Subir nuevo favicon
            $fileService = new FileService;
            $data = [
                'record_id' => $this->client_id,
                'folder' => 'branding',
                'type' => 'favicon',
                'name' => 'favicon_'.$this->client_id.'_'.time(),
            ];

            $files = $fileService->guardarArchivos([$this->favicon_file], $data);

            if (! empty($files)) {
                $file = File::find($files[0]);
                $this->theme->update(['favicon_url' => $file->path]);
            }

            // Limpiar archivo temporal
            $this->favicon_file = null;
            $this->uploading_favicon = false;

            session()->flash('message', 'Favicon subido exitosamente.');

        } catch (\Exception $e) {
            $this->uploading_favicon = false;
            session()->flash('error', 'Error al subir el favicon: '.$e->getMessage());
        }
    }

    public function resetToDefaults()
    {
        if (confirm('¿Está seguro de restablecer el tema a los valores por defecto?')) {
            $this->theme->resetToDefaults();
            $this->loadTheme();
            session()->flash('message', 'Tema restablecido a valores por defecto.');
        }
    }

    public function togglePreview()
    {
        $this->preview_mode = ! $this->preview_mode;
    }

    public function deleteLogo()
    {
        if ($this->theme->logo_url) {
            $this->deleteFileFromStorage($this->theme->logo_url);
            $this->theme->update(['logo_url' => null]);
            session()->flash('message', 'Logo eliminado exitosamente.');
        }
    }

    public function deleteFavicon()
    {
        if ($this->theme->favicon_url) {
            $this->deleteFileFromStorage($this->theme->favicon_url);
            $this->theme->update(['favicon_url' => null]);
            session()->flash('message', 'Favicon eliminado exitosamente.');
        }
    }

    private function deleteFileFromStorage($path)
    {
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    public function getPreviewStylesProperty()
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
            '--background-color' => $this->background_color,
            '--sidebar-color' => $this->sidebar_color,
            '--sidebar-text-color' => $this->sidebar_text_color,
            '--header-color' => $this->header_color,
            '--border-color' => $this->border_color,
            '--font-family' => $this->font_family,
        ];
    }

    public function getPreviewCssString()
    {
        if (! $this->preview_mode) {
            return '';
        }

        $styles = $this->getPreviewStylesProperty();

        return collect($styles)->map(fn ($value, $key) => $key.': '.$value)->implode('; ');
    }

    public function render()
    {
        return view('livewire.admin.theme-manager');
    }
}
