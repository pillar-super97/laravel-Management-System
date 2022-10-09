<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
   use SoftDeletes;
    protected $fillable = ['number','store_id','date','start_time','run_number','crew_leader','crew_count','comments','road_trip','overnight',
        'pic','qc','last_inventory_date','last_start_time','last_crew_count','last_count_length','last_count_production','last_inventory_value',
        'in_uniform','on_time','positive_exp','qc_comment','schedule_notes','field_comment','count_rx','count_backroom','status','precall_manager',
        'meal_amount','lodging_amount','precall_comments','qc_completed_by','qc_completed_on','precall_completed_by','precall_completed_on','qc_confirmed_with','overall_accurate', 'last_status'];
    
    
    public function store()
    {
        return $this->belongsTo('App\Models\Store', 'store_id');
    }
    
    public function crew_leader_name()
    {
        return $this->belongsTo('App\Models\Employee', 'crew_leader');
    }
    
    public function truck_dates()
    {
        return $this->hasMany('App\Models\EventTruckDates', 'event_id','id');
    }
    
    public function timesheet()
    {
        return $this->hasOne('App\Models\Timesheet', 'event_id','id');
    }
    
    public function event_schedule_data()
    {
        return $this->hasOne('App\Models\EventSchedules', 'event_id','id');
    }
    
    public function schedule_employees()
    {
        return $this->hasMany('App\Models\EventScheduleEmployees', 'event_id','id');
    }
    
//    public function precall_comments()
//    {
//        return $this->hasMany('App\Models\EventPrecalls', 'event_id','id');
//    }
    
   public function categories()
   {
    //    return $this->hasMany('App\Models\EventQcs', 'event_id','id');
    return $this->belongsToMany('App\Models\Category', 'event_categories');
   }
    
    public function areas()
    {
        return $this->hasMany('App\Models\EventAreas', 'event_id','id');
    }
    
     public function qc_by()
    {
        return $this->belongsTo('App\User', 'qc_completed_by');
    }
    
}
