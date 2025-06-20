<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Event extends Model
{
    use SoftDeletes;

    protected $table = 'events';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'matricule',
        'title',
        'description',
        'date',
        'time',
        'location',
        'longitude',
        'latitude',
        'price',
        'capacity',
        'available_tickets',
        'status',
        'image_url',
        'cancelled_reason',
        'cancelled_by_type',
        'cancelled_by_id',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
        'price' => 'float',
        'capacity' => 'integer',
        'available_tickets' => 'integer',
        'longitude' => 'float',
        'latitude' => 'float',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($event) {
            if (empty($event->{$event->getKeyName()})) {
                $event->{$event->getKeyName()} = (string) Str::uuid();
            }

            if (empty($event->matricule)) {
                $event->matricule = (string) Str::uuid();
            }
        });
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'event_id', 'id');
    }

    /**
     * Vérifie si l'événement a des tickets payés et non remboursés.
     */
    public function hasPaidUnrefundedTickets(): bool
    {
        return $this->tickets()->where('is_paid', true)->where('is_refunded', false)->exists();
    }

    /**
     * Détermine si l'événement est actuellement réservable.
     * Basé sur le statut et la suppression logique.
     */
    public function isReservable(): bool
    {
        return $this->status === 'published' && is_null($this->deleted_at);
    }
}
