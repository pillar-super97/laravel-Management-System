<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventScheduleEmployees extends Model
{
    protected $fillable = ['event_id', 'employee_id','area_id','task','vehicle_number','comment','custom_comment'];
    
    public function employee()
    {
        return $this->belongsTo('App\Models\Employee');
    }

    public function event()
    {
        return $this->belongsTo('App\Models\Event');
    }

    public function area()
    {
        return $this->belongsTo('App\Models\Area');
    }
    
}
