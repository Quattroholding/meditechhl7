<?php

namespace App\Models;

use App\Notifications\TicketCommentedNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketComment extends Model
{
    protected $fillable = [
        'ticket_id',
        'user_id',
        'comment',
        'is_internal',
        'marks_as_resolved',
    ];

    protected $casts = [
        'is_internal' => 'boolean',
        'marks_as_resolved' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot del modelo
     */
    protected static function booted(): void
    {
        // Notificar al creador del ticket cuando hay un nuevo comentario
        static::created(function ($comment) {
            // No notificar si es una nota interna (solo visible para admins)
            if ($comment->is_internal) {
                return;
            }

            // No notificar al mismo usuario que comentó
            if ($comment->ticket->user_id === $comment->user_id) {
                return;
            }

            // Notificar al creador del ticket
            $comment->ticket->user->notify(new TicketCommentedNotification($comment->ticket, $comment));
        });
    }

    /**
     * Relaciones
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scopes
     */
    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_internal', false);
    }

    public function scopeInternal(Builder $query): Builder
    {
        return $query->where('is_internal', true);
    }

    /**
     * Métodos de utilidad
     */
    public function isInternal(): bool
    {
        return $this->is_internal;
    }

    public function marksAsResolved(): bool
    {
        return $this->marks_as_resolved;
    }
}
