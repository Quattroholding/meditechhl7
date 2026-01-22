<?php

namespace App\Livewire\TicketSystem;

use App\Models\StatusHistoryLog;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;

class DetailModal extends Component
{
    public $showModal = false;

    public $ticketId = null;

    public $ticket = null;

    // Comentarios
    public $newComment = '';

    public $isInternalNote = false;

    public $markAsResolved = false;

    // Admin fields
    public $canManage = false;

    public $newStatus = '';

    public $assignedTo = null;

    public $availableAdmins = [];

    /**
     * Listener para abrir modal
     */
    #[On('openTicketDetailModal')]
    public function openTicketDetailModal($ticketId)
    {
        $this->ticketId = $ticketId;
        $this->loadTicket();
        $this->showModal = true;
    }

    /**
     * Cargar ticket con todas sus relaciones
     */
    public function loadTicket()
    {
        $this->ticket = Ticket::with([
            'user',
            'assignedTo',
            'client',
            'comments.user',
            'files',
        ])->find($this->ticketId);

        if (! $this->ticket) {
            $this->dispatch('showToastr', [
                'type' => 'error',
                'message' => 'Ticket no encontrado.',
            ]);
            $this->closeModal();

            return;
        }

        // Verificar si puede gestionar (admin o admin client)
        $this->canManage = auth()->user()->hasRole(['admin', 'admin client']);

        if ($this->canManage) {
            // Cargar admins disponibles para asignación
            $this->availableAdmins = User::role(['admin', 'admin client'])->get();
            $this->newStatus = $this->ticket->status;
            $this->assignedTo = $this->ticket->assigned_to;
        }
    }

    /**
     * Agregar comentario
     */
    public function addComment()
    {
        $this->validate([
            'newComment' => 'required|string|min:3|max:2000',
        ], [
            'newComment.required' => 'El comentario es obligatorio.',
            'newComment.min' => 'El comentario debe tener al menos 3 caracteres.',
            'newComment.max' => 'El comentario no puede superar los 2000 caracteres.',
        ]);

        try {
            $comment = TicketComment::create([
                'ticket_id' => $this->ticket->id,
                'user_id' => auth()->id(),
                'comment' => $this->newComment,
                'is_internal' => $this->isInternalNote && $this->canManage,
                'marks_as_resolved' => $this->markAsResolved && $this->canManage,
            ]);

            // Si se marca como resuelto, actualizar estado del ticket
            if ($this->markAsResolved && $this->canManage) {
                $this->ticket->markAsResolved();
            }

            $this->newComment = '';
            $this->isInternalNote = false;
            $this->markAsResolved = false;

            $this->dispatch('showToastr', [
                'type' => 'success',
                'message' => 'Comentario agregado exitosamente.',
            ]);

            $this->loadTicket();
            $this->dispatch('refreshTable');
        } catch (\Exception $e) {
            $this->dispatch('showToastr', [
                'type' => 'error',
                'message' => 'Error al agregar comentario: '.$e->getMessage(),
            ]);
        }
    }

    /**
     * Actualizar estado (solo admins)
     */
    public function updateStatus()
    {
        if (! $this->canManage) {
            $this->dispatch('showToastr', [
                'type' => 'error',
                'message' => 'No tienes permiso para cambiar el estado.',
            ]);

            return;
        }

        try {
            $this->ticket->status = $this->newStatus;
            $this->ticket->save();

            $this->dispatch('showToastr', [
                'type' => 'success',
                'message' => 'Estado actualizado exitosamente.',
            ]);

            $this->loadTicket();
            $this->dispatch('refreshTable');
        } catch (\Exception $e) {
            $this->dispatch('showToastr', [
                'type' => 'error',
                'message' => 'Error al actualizar estado: '.$e->getMessage(),
            ]);
        }
    }

    /**
     * Asignar ticket (solo admins)
     */
    public function assignTicket()
    {
        if (! $this->canManage) {
            $this->dispatch('showToastr', [
                'type' => 'error',
                'message' => 'No tienes permiso para asignar tickets.',
            ]);

            return;
        }

        try {
            $this->ticket->assignTo($this->assignedTo);

            $this->dispatch('showToastr', [
                'type' => 'success',
                'message' => 'Ticket asignado exitosamente.',
            ]);

            $this->loadTicket();
            $this->dispatch('refreshTable');
        } catch (\Exception $e) {
            $this->dispatch('showToastr', [
                'type' => 'error',
                'message' => 'Error al asignar ticket: '.$e->getMessage(),
            ]);
        }
    }

    /**
     * Cerrar ticket
     */
    public function closeTicket()
    {
        // Admins pueden cerrar siempre
        // Usuarios solo pueden cerrar si está en estado 'resolved'
        if (! $this->canManage && $this->ticket->status !== 'resolved') {
            $this->dispatch('showToastr', [
                'type' => 'error',
                'message' => 'Solo puedes cerrar un ticket cuando está resuelto.',
            ]);

            return;
        }

        try {
            $this->ticket->markAsClosed();

            $this->dispatch('showToastr', [
                'type' => 'success',
                'message' => 'Ticket cerrado exitosamente.',
            ]);

            $this->loadTicket();
            $this->dispatch('refreshTable');
        } catch (\Exception $e) {
            $this->dispatch('showToastr', [
                'type' => 'error',
                'message' => 'Error al cerrar ticket: '.$e->getMessage(),
            ]);
        }
    }

    /**
     * Reabrir ticket
     */
    public function reopenTicket()
    {
        if (! $this->ticket->canBeReopened()) {
            $this->dispatch('showToastr', [
                'type' => 'error',
                'message' => 'Este ticket no puede ser reabierto.',
            ]);

            return;
        }

        try {
            $this->ticket->reopen();

            $this->dispatch('showToastr', [
                'type' => 'success',
                'message' => 'Ticket reabierto exitosamente.',
            ]);

            $this->loadTicket();
            $this->dispatch('refreshTable');
        } catch (\Exception $e) {
            $this->dispatch('showToastr', [
                'type' => 'error',
                'message' => 'Error al reabrir ticket: '.$e->getMessage(),
            ]);
        }
    }

    /**
     * Cerrar modal
     */
    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['ticketId', 'ticket', 'newComment', 'isInternalNote', 'markAsResolved', 'newStatus', 'assignedTo']);
    }

    public function render()
    {
        // Obtener historial de estados si está disponible
        $statusHistory = [];
        if ($this->ticket && $this->canManage) {
            $statusHistory = StatusHistoryLog::where('table_name', 'tickets')
                ->where('record_id', $this->ticket->id)
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('livewire.ticket-system.detail-modal', [
            'statusHistory' => $statusHistory,
        ]);
    }
}
