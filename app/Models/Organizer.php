<?php

namespace App\Models;

use App\Models\Tenant\Patron;
use Stancl\Tenancy\Database\Concerns\CentralConnection;
use Stancl\Tenancy\Database\Models\TenantPivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;
use Stancl\Tenancy\Contracts\SyncMaster;
use Stancl\Tenancy\Database\Concerns\ResourceSyncing;

class Organizer extends Authenticatable implements SyncMaster
{
    use HasFactory, Notifiable, ResourceSyncing;
    use SoftDeletes, HasRoles , CentralConnection;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'matricule',
        'nom',
        'prenom',
        'email',
        'password',
        'password_changed_at',
        'passcode',
        'telephone',
        'pays',
        'ville',
        'photoProfil',
        'pieceIdentiteRecto',
        'pieceIdentiteVerso',
        'passcode_reset_status',
        'passcode_reset_date',
        'profile_verification_status',
    ];

    protected $hidden = [
        'password_changed_at',
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password_changed_at' => 'datetime',
        'passcode_reset_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($organizer) {
            if (empty($organizer->matricule)) {
                $organizer->matricule = Str::uuid();
            }


            $uiid = Str::uuid();

            $organizer->id = $uiid;

            $organizer->global_id = $uiid;

        });

        static::updating(function ($organizer) {
            if ($organizer->isDirty('id') || empty($organizer->global_id)) {
                $organizer->global_id = $organizer->id;
            }
        });
    }



    public function createdOrganizations(): HasMany
    {
        return $this->hasMany(Organization::class, 'organizer_id', 'id');
    }


    /**
     * Get the tenant model class this resource syncs to.
     */
    public function getTenantModelName(): string
    {
        return Patron::class;
    }

    /**
     * Get the global identifier key.
     */
    public function getGlobalIdentifierKey()
    {
        return $this->getAttribute($this->getGlobalIdentifierKeyName());
    }

    public function getGlobalIdentifierKeyName(): string
    {
        return 'global_id';
    }

    public function getSyncedAttributeNames(): array
    {
        return [
            'global_id',
            'matricule',
            'nom',
            'prenom',
            'email',
            'password',
            'telephone',
            'pays',
            'ville',
            'photoProfil',
            'pieceIdentiteRecto',
            'pieceIdentiteVerso',
            'profile_verification_status',
        ];
    }

    /**
     * Get the central model class for this resource.
     */
    public function getCentralModelName(): string
    {
        return self::class;
    }


    public function tenants(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Organization::class, 'organization_organizer', 'organizer_id', 'organization_id', 'global_id')
            ->using(TenantPivot::class);
    }
}
