<?php

namespace App\Models\Tenant;

use App\Models\Admin;
use App\Models\Tenant\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventStatusHistory extends Model
{
    use SoftDeletes;

    protected $table = 'event_status_histories';
    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = [
        'event_id',
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
        'deleted_at' => 'datetime',
    ];


    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }


    public function changerAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'changed_by_id')->where('changed_by_type', 'admin');
    }


    public function changerEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'changed_by_id')->where('changed_by_type', 'organizer');
    }
}
