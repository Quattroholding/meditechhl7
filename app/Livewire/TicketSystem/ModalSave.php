<?php

namespace App\Livewire\TicketSystem;

use App\Models\Ticket;
use App\Services\FileService;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class ModalSave extends Component
{
    use WithFileUploads;

    public $showModal = false;

    public $ticketId = null;

    // Form fields
    public $title = '';

    public $description = '';

    public $priority = 'medium';

    public $category = 'other';

    // Archivos
    public $attachments = [];

    public $existingAttachments = [];

    /**
     * Listener para abrir modal
     */
    #[On('openTicketModal')]
    public function openTicketModal($ticketId = null)
    {
        $this->resetForm();
        $this->ticketId = $ticketId;

        if ($ticketId) {
            $this->loadTicket();
        }

        $this->showModal = true;
    }

    /**
     * Cargar ticket para edición
     */
    public function loadTicket()
    {
        $ticket = Ticket::with('files')->find($this->ticketId);

        // Solo puede editar el creador y si el estado es 'open'
        if (! $ticket || $ticket->user_id !== auth()->id() || $ticket->status !== 'open') {
            $this->dispatch('showToastr', [
                'type' => 'error',
                'message' => 'No tienes permiso para editar este ticket o el ticket ya no está en estado abierto.',
            ]);
            $this->closeModal();

            return;
        }

        $this->title = $ticket->title;
        $this->description = $ticket->description;
        $this->priority = $ticket->priority;
        $this->category = $ticket->category;
        $this->existingAttachments = $ticket->files;
    }

    /**
     * Guardar ticket
     */
    public function saveTicket()
    {
        // Validación inline
        $this->validate([
            'title' => 'required|string|min:5|max:255',
            'description' => 'required|string|min:10|max:5000',
            'priority' => 'required|in:low,medium,high,critical',
            'category' => 'required|in:bug,question,feature_request,improvement,technical_support,other',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240', // 10MB
        ], [
            'title.required' => 'El título es obligatorio.',
            'title.min' => 'El título debe tener al menos 5 caracteres.',
            'title.max' => 'El título no puede tener más de 255 caracteres.',
            'description.required' => 'La descripción es obligatoria.',
            'description.min' => 'La descripción debe tener al menos 10 caracteres.',
            'description.max' => 'La descripción no puede tener más de 5000 caracteres.',
            'attachments.*.mimes' => 'Solo se permiten archivos JPG, PNG, PDF, DOC y DOCX.',
            'attachments.*.max' => 'Cada archivo no puede superar los 10MB.',
        ]);

        try {
            if ($this->ticketId) {
                // Actualizar ticket existente
                $ticket = Ticket::find($this->ticketId);

                // Verificar permisos nuevamente
                if (! $ticket || $ticket->user_id !== auth()->id() || $ticket->status !== 'open') {
                    throw new \Exception('No tienes permiso para editar este ticket.');
                }

                $ticket->update([
                    'title' => $this->title,
                    'description' => $this->description,
                    'priority' => $this->priority,
                    'category' => $this->category,
                ]);

                $message = 'Ticket actualizado exitosamente.';
            } else {
                // Crear nuevo ticket
                $ticket = Ticket::create([
                    'client_id' => auth()->user()->clients()->first()->id ?? 1,
                    'user_id' => auth()->id(),
                    'title' => $this->title,
                    'description' => $this->description,
                    'priority' => $this->priority,
                    'category' => $this->category,
                    'status' => 'open',
                ]);

                $message = 'Ticket creado exitosamente.';
            }

            // Guardar archivos adjuntos si hay
            if (! empty($this->attachments)) {
                $fileService = new FileService;
                $fileService->guardarArchivos($this->attachments, [
                    'folder' => 'tickets',
                    'record_id' => $ticket->id,
                    'type' => 'attachment',
                ]);
            }

            $this->dispatch('showToastr', [
                'type' => 'success',
                'message' => $message,
            ]);

            $this->dispatch('refreshTable');
            $this->closeModal();
        } catch (\Exception $e) {
            $this->dispatch('showToastr', [
                'type' => 'error',
                'message' => 'Error al guardar el ticket: '.$e->getMessage(),
            ]);
        }
    }

    /**
     * Eliminar archivo adjunto antes de subir
     */
    public function removeAttachment($index)
    {
        array_splice($this->attachments, $index, 1);
    }

    /**
     * Cerrar modal
     */
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    /**
     * Resetear formulario
     */
    private function resetForm()
    {
        $this->ticketId = null;
        $this->title = '';
        $this->description = '';
        $this->priority = 'medium';
        $this->category = 'other';
        $this->attachments = [];
        $this->existingAttachments = [];
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.ticket-system.modal-save');
    }
}
