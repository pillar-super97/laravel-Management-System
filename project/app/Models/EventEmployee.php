<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class EventEmployee extends Model
{
    public $table = 'event_employees';
    public $timestamps = false;
    protected $fillable = [
        'event_id',
        'employee_id',
        'start_time',
        'end_time',
        'total_gap_hours',
        'count',
        'pieces',
        'records',
        'is_rx'
    ];
    
}
