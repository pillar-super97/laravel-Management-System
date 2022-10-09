<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimesheetApproved extends Model
{
    protected $table = "timesheet_approved";
    protected $fillable = ['timesheet_id','employee_id','is_supervisor','driver_to','driver_from','store_hours',
        'PIMTime','iWaitTime','iLunch1','iLunch2','drive_time','travel_time','origin','destination','jsa_miles','vehicle_travel'];
    
    
    public function employee()
    {
        return $this->belongsTo('App\Models\Employee', 'employee_id');
    }

    public function employees()
    {
        return $this->belongsTo('App\Models\Employee' );
    }
    
    public function timesheet()
    {
        return $this->belongsTo('App\Models\Timesheet', 'timesheet_id');
    }
    
    public function origin()
    {
        return $this->belongsTo('App\Models\Store', 'origin');
    }
    
    public function destination()
    {
        return $this->belongsTo('App\Models\Store', 'destination');
    }
}
