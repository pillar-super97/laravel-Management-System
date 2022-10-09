<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Division extends Model
{
    use SoftDeletes;
    protected $fillable = ['name','manager','address','client_id','phone','country_id','state_id','city_id','zip','scheduling_contact_email','scheduling_contact_name',
        'scheduling_contact_address','scheduling_contact_phone','scheduling_contact_country_id','scheduling_contact_state_id','scheduling_contact_city_id',
        'scheduling_contact_zip','sec_scheduling_contact_email','sec_scheduling_contact_name','sec_scheduling_contact_address','sec_scheduling_contact_phone',
        'sec_scheduling_contact_country_id','sec_scheduling_contact_state_id','sec_scheduling_contact_city_id','sec_scheduling_contact_zip','billing_contact_email',
        'billing_contact_name','billing_contact_address','billing_contact_phone','billing_contact_country_id','billing_contact_state_id','billing_contact_city_id',
        'billing_contact_zip','frequency','inv_type','billing','rate_type','rate','terms','notes','rate_per','start_time','benchmark','max_length','min_auditors',
        'spf','count_stockroom','precall','qccall','store_or_other','other_contact_name','other_contact_number','piccall','picstore_or_other','picother_contact_name','picother_contact_number','pieces_or_dollars','alr_disk','status','billing'];
    
    public function client()
    {
        return $this->belongsTo('App\Models\Client', 'client_id');
    }
    
    public function schedule_availability_days()
    {
        return $this->hasMany('App\Models\DivisionScheduleAvailabilityDays', 'division_id','id');
    }
    
    public function city()
    {
        return $this->belongsTo('App\City', 'city_id');
    }
    
    public function state()
    {
        return $this->belongsTo('App\State', 'state_id');
    }
    
    public function country()
    {
        return $this->belongsTo('App\Country', 'country_id');
    }
    
    public function scheduling_city()
    {
        return $this->belongsTo('App\City', 'scheduling_contact_city_id');
    }
    
    public function scheduling_state()
    {
        return $this->belongsTo('App\State', 'scheduling_contact_state_id');
    }
    
    public function scheduling_country()
    {
        return $this->belongsTo('App\Country', 'scheduling_contact_country_id');
    }

    public function sec_scheduling_city()
    {
        return $this->belongsTo('App\City', 'sec_scheduling_contact_city_id');
    }
    
    public function sec_scheduling_state()
    {
        return $this->belongsTo('App\State', 'sec_scheduling_contact_state_id');
    }
    
    public function sec_scheduling_country()
    {
        return $this->belongsTo('App\Country', 'sec_scheduling_contact_country_id');
    }
    
    public function billing_city()
    {
        return $this->belongsTo('App\City', 'billing_contact_city_id');
    }
    
    public function billing_state()
    {
        return $this->belongsTo('App\State', 'billing_contact_state_id');
    }
    
    public function billing_country()
    {
        return $this->belongsTo('App\Country', 'billing_contact_country_id');
    }

    public function district()
    {
        return $this->hasMany('App\Models\District', 'division_id', 'id');
    }
    
}
