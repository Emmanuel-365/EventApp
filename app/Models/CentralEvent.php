<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Stancl\Tenancy\Database\Models\Tenant; // Import du modèle Tenant de Stancl

class CentralEvent extends Model
{
    use SoftDeletes;

    protected $table = 'central_events';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'tenant_id',
        'event_id',
        'matricule',
        'title',
        'description',
        'date',
        'time',
        'location',
        'latitude',
        'longitude',
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
        'date' => 'date',
        'time' => 'datetime',
        'longitude' => 'float',
        'latitude' => 'float',
        'price' => 'float',
        'capacity' => 'integer',
        'available_tickets' => 'integer',
        'deleted_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /**
     * Relation vers le tenant propriétaire de cet événement.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'tenant_id', 'id');
    }

}
