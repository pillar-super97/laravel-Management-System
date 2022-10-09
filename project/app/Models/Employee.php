<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;
    protected $fillable = ['id','name','email','emp_number','ss_no','last_name','first_name','title','manager','benchmark','is_driver','is_rx'
        ,'is_crew_leader','payrate','area_id','jsa_id','status','overnight'];
    
    public function area()
    {
        return $this->belongsTo('App\Models\Area', 'area_id');
    }

    public function approvedTimeSheets()
    {
        return $this->hasMany('App\Models\TimesheetApproved');
    }

    public function jsa()
    {
        return $this->belongsTo('App\Models\Jsa', 'jsa_id');
    }
    public function availability_days()
    {
        return $this->hasMany('App\Models\EmployeeAvailabilityDays', 'employee_id','id');
    }

   
}
