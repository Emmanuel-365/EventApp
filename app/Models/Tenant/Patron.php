<?php
namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

use Stancl\Tenancy\Contracts\Syncable;
use Stancl\Tenancy\Database\Concerns\ResourceSyncing;

class Patron extends Authenticatable implements Syncable
{
    use HasFactory, Notifiable, ResourceSyncing;
    use SoftDeletes, HasRoles , HasUuids;

    protected $table = 'patrons';

    protected $fillable = [
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
        'organizer_global_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $keyType = 'string';
    public $incrementing = false;

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($patron) {

            if (empty($patron->matricule)) {
                $patron->matricule = (string) Str::uuid();
            }
        });
    }


    /**
     * Get the central model class this resource syncs from.
     */
    public function getCentralModelName(): string
    {
        return \App\Models\Organizer::class;
    }

    /**
     * Get the global identifier key. This is the value of the central model's primary key.
     */
    public function getGlobalIdentifierKey()
    {
        return $this->organizer_global_id;
    }

    /**
     * Get the name of the global identifier key.
     */
    public function getGlobalIdentifierKeyName(): string
    {
        return 'organizer_global_id';
    }

    /**
     * Get the attributes that should be synced.
     */
    public function getSyncedAttributeNames(): array
    {
        return [
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


}
