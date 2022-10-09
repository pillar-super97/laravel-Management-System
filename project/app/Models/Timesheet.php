<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Timesheet extends Model
{
    protected $table = "timesheet_header";
    protected $fillable = [
                            'event_id','idTimesheet_SQL','dtJobDate','store_id','dEmpCount','dEmpPieces','mComments',
                            'InvRecapStartTime', 'InvRecapEndTime','InvRecapWrapTime','InvRecapComments','CrewNoShowCount',
                            'TTLMH','TotalBreakTime','TotalWaitTime', 'ALADeliveryDate', 'InvRecapArvlTime', 'ALA_DeliveryDate',
                            'AccountName','InvStoreNumber','Benchmark','CrewManagerSSN','status'];
                        
    public function vehicles()
    {
        return $this->hasMany('App\Models\TimesheetVehicle', 'timesheet_id','id');
    }
    
    public function emp_data()
    {
        return $this->hasMany('App\Models\TimesheetData', 'timesheet_id','id');
    }
    
    public function approved_timesheets()
    {
        return $this->hasMany('App\Models\TimesheetApproved', 'timesheet_id','id');
    }
    
    public function store()
    {
        return $this->belongsTo('App\Models\Store');
    }
    
    public function event()
    {
        return $this->belongsTo('App\Models\Event', 'event_id');
    }
    public function approval_detail()
    {
        return $this->belongsTo('App\User', 'approved_by');
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
