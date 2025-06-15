<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

class Employee extends Authenticatable
{
    use HasFactory, Notifiable;
    use SoftDeletes, HasRoles , HasUuids;

    protected $table = 'employees';

    protected $fillable = [
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
        'organizer_global_id',
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

        static::creating(function ($employee) {
            if (empty($employee->matricule)) {
                $employee->matricule = Str::uuid();
            }
        });
    }


}
