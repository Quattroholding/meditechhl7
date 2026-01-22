<?php

namespace App\Models;

use App\Enums\TicketStatus;
use App\Models\Scopes\TicketScope;
use App\Notifications\TicketCreatedNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Ticket extends BaseModel
{
    protected $fillable = [
        'identifier',
        'client_id',
        'user_id',
        'assigned_to',
        'title',
        'description',
        'priority',
        'status',
        'category',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'status' => TicketStatus::class,
    ];

    /**
     * Boot del modelo
     */
    protected static function booted(): void
    {
        // Agregar scope global de multi-tenancy
        static::addGlobalScope(new TicketScope);

        // Generar identifier automático al crear
        static::creating(function ($ticket) {
            if (empty($ticket->identifier)) {
                $ticket->identifier = 'TKT-'.strtoupper(Str::random(7));
            }
        });

        // Notificar a admins cuando se crea un nuevo ticket
        static::created(function ($ticket) {
            // Obtener admins del cliente que creó el ticket
            $admins = User::role(['admin', 'admin client'])
                ->whereHas('clients', fn ($q) => $q->where('client_id', $ticket->client_id))
                ->get();

            // Enviar notificación a cada admin
            foreach ($admins as $admin) {
                $admin->notify(new TicketCreatedNotification($ticket));
            }
        });
    }

    /**
     * Relaciones
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class)->orderBy('created_at', 'asc');
    }

    /**
     * Scopes
     */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', 'open');
    }

    public function scopeInProgress(Builder $query): Builder
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeResolved(Builder $query): Builder
    {
        return $query->where('status', 'resolved');
    }

    public function scopeClosed(Builder $query): Builder
    {
        return $query->where('status', 'closed');
    }

    public function scopeByPriority(Builder $query, string $priority): Builder
    {
        return $query->where('priority', $priority);
    }

    public function scopeByCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    public function scopeAssignedToMe(Builder $query): Builder
    {
        return $query->where('assigned_to', auth()->id());
    }

    public function scopeUnassigned(Builder $query): Builder
    {
        return $query->whereNull('assigned_to');
    }

    /**
     * Métodos estáticos de colores
     */
    public static function statusColors(): array
    {
        return [
            'open' => '6c757d',           // Gris
            'in_progress' => '0dcaf0',    // Cyan
            'on_hold' => 'ffc107',        // Amarillo
            'resolved' => '198754',       // Verde
            'closed' => '0d6efd',         // Azul
        ];
    }

    public static function priorityColors(): array
    {
        return [
            'low' => '6c757d',        // Gris
            'medium' => '0dcaf0',     // Cyan
            'high' => 'fd7e14',       // Naranja
            'critical' => 'dc3545',   // Rojo
        ];
    }

    /**
     * Accessors
     */
    public function getPriorityLabelAttribute(): string
    {
        $labels = [
            'low' => 'Baja',
            'medium' => 'Media',
            'high' => 'Alta',
            'critical' => 'Crítica',
        ];

        return $labels[$this->priority] ?? 'Desconocida';
    }

    public function getCategoryLabelAttribute(): string
    {
        $labels = [
            'bug' => 'Error',
            'question' => 'Pregunta',
            'feature_request' => 'Nueva Función',
            'improvement' => 'Mejora',
            'technical_support' => 'Soporte Técnico',
            'other' => 'Otro',
        ];

        return $labels[$this->category] ?? 'Otro';
    }

    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'open' => 'Abierto',
            'in_progress' => 'En Progreso',
            'on_hold' => 'En Espera',
            'resolved' => 'Resuelto',
            'closed' => 'Cerrado',
        ];

        return $labels[$this->status] ?? 'Desconocido';
    }

    /**
     * Métodos de utilidad
     */
    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    public function canBeReopened(): bool
    {
        return in_array($this->status, ['resolved', 'closed']);
    }

    public function assignTo(?int $userId): bool
    {
        $this->assigned_to = $userId;

        return $this->save();
    }

    public function markAsResolved(): bool
    {
        $this->status = 'resolved';

        return $this->save();
    }

    public function markAsClosed(): bool
    {
        $this->status = 'closed';

        return $this->save();
    }

    public function reopen(): bool
    {
        if ($this->canBeReopened()) {
            $this->status = 'open';

            return $this->save();
        }

        return false;
    }
}
