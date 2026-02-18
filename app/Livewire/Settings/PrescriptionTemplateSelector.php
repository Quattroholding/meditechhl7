<?php

namespace App\Livewire\Settings;

use App\Models\ClientPreference;
use Illuminate\Support\Facades\File;
use Livewire\Component;

class PrescriptionTemplateSelector extends Component
{
    public $selectedTemplate;

    public $availableTemplates = [];

    public $showPreviewModal = false;

    public $previewUrl = '';

    public $previewTitle = '';

    public function mount()
    {
        $client = auth()->user()->getCurrentClient();

        if (! $client) {
            abort(403, 'No tiene un cliente asociado');
        }

        // Get current selected template
        $this->selectedTemplate = ClientPreference::getPrescriptionTemplate($client->id);

        // Load available templates
        $this->loadAvailableTemplates();
    }

    public function loadAvailableTemplates()
    {
        $templatePath = resource_path('views/documents/prescriptions/templates');
        $files = File::glob($templatePath.'/template_*.blade.php');

        $this->availableTemplates = collect($files)->map(function ($file) {
            $filename = basename($file);
            $templateName = str_replace('.blade.php', '', $filename);
            $templateNumber = (int) str_replace('template_', '', $templateName);

            return [
                'name' => $templateName,
                'label' => "Plantilla {$templateNumber}",
                'number' => $templateNumber,
                'path' => $file,
            ];
        })->sortBy('number')->values()->toArray();
    }

    public function selectTemplate($templateName)
    {

        $client = auth()->user()->getCurrentClient();

        if (! $client) {
            $this->dispatch('error', 'No tiene un cliente asociado');

            return;
        }

        try {
            ClientPreference::setPrescriptionTemplate($client->id, $templateName);
            $this->selectedTemplate = $templateName;

            $this->dispatch('success', 'Plantilla de prescripción actualizada exitosamente');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Error al actualizar la plantilla: '.$e->getMessage());
        }
    }

    public function openPreviewModal($templateName, $templateLabel)
    {
        $this->previewUrl = route('setting.prescription_template.preview', $templateName);
        $this->previewTitle = 'Vista Previa - '.$templateLabel;
        $this->showPreviewModal = true;
    }

    public function render()
    {
        return view('livewire.settings.prescription-template-selector');
    }

    public function closePreviewModal()
    {
        $this->showPreviewModal = false;
        $this->previewUrl = '';
        $this->previewTitle = '';
    }
}
