<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMedia;

class Association extends Model
{
    use HasMediaTrait;
    use SoftDeletes;

    protected $fillable = ['name','address','phone','country_id','state_id','city_id','zip','primary_contact_email','primary_contact_name',
        'primary_contact_address','primary_contact_phone','secondary_contact_email','secondary_contact_name','secondary_contact_address','secondary_contact_phone','alternate_contact_email'
        ,'alternate_contact_name','alternate_contact_address','alternate_contact_phone','rebate','notes'];
    
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


    public function client()
    {
        return $this->hasMany('App\Models\Client', 'association_id', 'id');
    }
    
    
}
