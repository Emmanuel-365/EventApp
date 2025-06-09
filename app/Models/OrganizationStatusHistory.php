<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrganizationStatusHistory extends Model
{
    use HasFactory , SoftDeletes;

    protected $table = 'organization_status_history';

    protected $fillable = [
        'organization_id',
        'status_type',
        'old_status',
        'new_status',
        'reason',
        'changed_by_id',
        'changed_by_type',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Définir la relation polymorphique vers l'utilisateur qui a effectué le changement (Admin ou Organizer).
     */
    public function changedBy(): MorphTo
    {
        return $this->morphTo('changed_by');
    }

    public function changerAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'changed_by_id')->where('changed_by_type', 'admin');
    }

    public function changerOrganizer(): BelongsTo
    {
        return $this->belongsTo(Organizer::class, 'changed_by_id')->where('changed_by_type', 'organizer');
    }

}
