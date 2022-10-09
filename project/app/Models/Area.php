<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Area extends Model
{
    use SoftDeletes;
   
    protected $fillable = ['title','area_number','address','country_id','state_id','city_id','zip','status'];
    
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
}
