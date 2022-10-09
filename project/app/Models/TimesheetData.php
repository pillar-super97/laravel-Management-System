<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class TimesheetData extends Model
{
    protected $table = "timesheet_data";
    protected $fillable = ['timesheet_id','employee_id','dtStartDateTime','dtStopDateTime','dEmpCount','dEmpPieces',
        'sStoreOrigin','sStoreReturn','bIsDriver','bIsSuper','iWaitTime','iAttendanceFlag',
        'sEmployeeComment','TotalScans','ScansPerHour','PiecesPerHour','dtFirstScan','dtLastScan','WaitTimeExplanation','WrapTimeExplanation',
        'iLunch1','iLunch2','iGapTime','GapTimeExplanation','AttendanceExplanation','PIMTime','is_flagged','iBreakTime'];
    
        
    
    public function employee()
    {
        return $this->belongsTo('App\Models\Employee', 'employee_id')->orderBy('last_name', 'asc');
    }
    
    public function store()
    {
        return $this->belongsTo('App\Models\Store', 'sStoreOrigin');
    }
    
    public function destination()
    {
        return $this->belongsTo('App\Models\Store', 'sStoreReturn');
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

    public function getHourAttribute()
    {
        $start = Carbon::parse($this->dtStartDateTime);
        $stop = Carbon::parse($this->dtStopDateTime);
        return $stop->diff($start);//->format('%H:%I');
    }

    public function getStatusAttribute()
    {
        $status = '';
        if($this->blsDriver) $status = 'Driver';
        if($this->blsSuper) $status = 'Super';
        return $status;
    }
    
}
