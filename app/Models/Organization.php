<?php

namespace App\Models;

use App\Enums\TypeEntreprise;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\TenantPivot;

class Organization extends BaseTenant implements TenantWithDatabase
{
    use HasFactory, SoftDeletes, HasDomains , HasDatabase;

    protected $table = 'organizations';
    protected $fillable = [
        'id',
        'matricule',
        'nom',
        'NIU',
        'organizer_id',
        'type',
        'date_creation',
        'validation_status',
        'rejected_reason',
        'activation_status',
        'disabled_reason',
        'disabled_by_type',
        'disabled_by_id',
        'data'
    ];

    protected $casts = [
        'type' => TypeEntreprise::class,
        'id' => 'string',
        'date_creation' => 'date',
        'deleted_at' => 'datetime',
        'data' => 'array',
    ];


    public function users()
    {
        return $this->belongsToMany(Organization::class, 'organization_organizer', 'organization_id', 'organizer_id', 'id', 'global_id')
            ->using(TenantPivot::class);
    }
    /**
     * Get the names of the columns that should be managed by Stancl/Tenancy
     * as custom tenant columns, not stored in the 'data' JSON column.
     *
     * @return array
     */
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'matricule',
            'nom',
            'NIU',
            'type',
            'date_creation',
            'organizer_id',
            'validation_status',
            'rejected_reason',
            'activation_status',
            'disabled_reason',
            'disabled_by_type',
            'disabled_by_id',
            'deleted_at',
            'created_at',
            'updated_at',
        ];
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::creating(function (Organization $organization) {
            if (empty($organization->matricule)) {
                $organization->matricule = (string) Str::uuid();
            }

           /* if (empty($organization->id)) {
                $organization->id = (string) Str::uuid();
            }*/
        });
    }

    public function getTypeLabelAttribute()
    {
        return $this->type->label();
    }

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(Organizer::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(CentralEvent::class);
    }




    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrganizationStatusHistory::class);
    }

    public function disabledBy(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'disabled_by_type', 'disabled_by_id');
    }


}
