<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use  Illuminate\Foundation\Auth\User as Authenticatable;

class Client extends Authenticatable
{
    use SoftDeletes;

    protected $table = 'clients';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        'phone',
        'address',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($client) {
            if (empty($client->{$client->getKeyName()})) {
                $client->{$client->getKeyName()} = (string) Str::uuid();
            }
        });
    }

}
