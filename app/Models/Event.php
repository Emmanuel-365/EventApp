<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    use HasFactory , SoftDeletes;

    protected $fillable = [
        'matricule',
        'nom',
        'description',
        'prix',
        'dateDebut',
        'dateFin',
        'location',
        'image',
        'video',
        'organization_id',
    ];

    protected $casts = [
        'dateDebut' => 'datetime',
        'dateFin' => 'datetime',
        'prix' => 'integer',

    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($event) {
            if (empty($event->matricule)) {
                $event->matricule = Str::uuid();
            }
        });
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
