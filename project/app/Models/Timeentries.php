<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Timeentries extends Model
{
    protected $table = "time_entries";
    protected $fillable = ['employee_number','pay_date','store_id','dEmpCount','dEmpPieces','mComments','InvRecapStartTime',
        'InvRecapEndTime','InvRecapWrapTime','InvRecapComments','CrewNoShowCount','TTLMH','TotalBreakTime','TotalWaitTime',
        'AccountName','InvStoreNumber','Benchmark','CrewManagerSSN','status'];
    
    public function vehicles()
    {
        return $this->hasMany('App\Models\TimesheetVehicle', 'timesheet_id','id');
    }
    
    public function emp_data()
    {
        return $this->hasMany('App\Models\TimesheetData', 'timesheet_id','id');
    }
    
    public function store()
    {
        return $this->belongsTo('App\Models\Store', 'store_id');
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
