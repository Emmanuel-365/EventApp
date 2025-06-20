<?php

namespace App\Models\Tenant;

use App\Models\Admin;
use App\Models\Tenant\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventActivityLog extends Model
{
    use SoftDeletes;

    protected $table = 'event_activity_logs';
    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = [
        'event_id',
        'field_name',
        'old_value',
        'new_value',
        'changed_by_id',
        'changed_by_type',
        'reason',
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
        return $this->belongsTo(Employee::class, 'changed_by_id')->where('changed_by_type', 'employee');
    }
}
