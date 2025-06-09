<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ban extends Model
{
    use HasFactory , SoftDeletes;
    protected  $fillable = [
        'motif',
        'user_id',
        'guard' ,
        'banned_by',
        'banner_guard',
        'unbanned_by',
        'unbanner_guard',
    ] ;

    protected $casts = [
        'motif' => 'string',
        'user_id' => 'integer',
        'guard' => 'string'
    ];
}
