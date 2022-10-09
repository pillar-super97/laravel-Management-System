<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class TimesheetVehicle extends Model
{
    protected $table = "timesheet_vehicle";
    protected $fillable = ['timesheet_id','idVehicle','driver_to','driver_from','dtToStoreStart','dtToStoreEnd',
        'dtFromStoreStart','dtFromStoreEnd','is_flagged'];

       
    
    
    public function driverTo()
    {
        return $this->belongsTo('App\Models\Employee', 'driver_to');
    }
    public function driverFrom()
    {
        return $this->belongsTo('App\Models\Employee', 'driver_from');
    }

    public function getTimeToStoreAttribute()
    {
        $start = Carbon::parse($this->dtToStoreStart);
        $stop = Carbon::parse($this->dtToStoreEnd);
        return $stop->diff($start)->format('%H:%I');
    }

    public function getTimeFromStoreAttribute()
    {
        $start = Carbon::parse($this->dtFromStoreStart);
        $stop = Carbon::parse($this->dtFromStoreEnd);
        return $stop->diff($start)->format('%H:%I');
    }
    
//    public function jsa()
//    {
//        return $this->belongsTo('App\Models\Jsa', 'jsa_id');
//    }
//    
//    public function store()
//    {
//        return $this->belongsTo('App\Models\Store', 'store_id');
//    }
    
}
