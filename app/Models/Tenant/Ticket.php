<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Models\Client; // Import du modèle Client de l'application centrale

class Ticket extends Model
{
    use SoftDeletes;

    protected $table = 'tickets';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'event_id',
        'attendee_name',
        'attendee_email',
        'price',
        'is_paid',
        'is_refunded',
        'payment_id',
        'ticket_code',
        'scanned_at',
        'status',
        'client_id',
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'is_refunded' => 'boolean',
        'price' => 'float',
        'scanned_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($ticket) {
            if (empty($ticket->{$ticket->getKeyName()})) {
                $ticket->{$ticket->getKeyName()} = (string) Str::uuid();
            }
            if (empty($ticket->ticket_code)) {
                $ticket->ticket_code = (string) Str::uuid();
            }
        });
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id', 'id');
    }

    public function isPaidAndNotRefunded(): bool
    {
        return $this->is_paid && !$this->is_refunded;
    }

    /**
     * Récupère le modèle Client associé depuis la base de données centrale.
     * C'est une récupération manuelle, PAS une relation Eloquent BelongsTo
     * qui est optimisée pour les requêtes sur une seule connexion.
     */
    public function getClientAttribute(): ?Client
    {
        if (empty($this->client_id)) {
            return null;
        }
        return Client::on('mysql')->find($this->client_id);
    }
}
