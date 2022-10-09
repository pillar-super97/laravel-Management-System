<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Blackoutdate extends Model
{
    //use SoftDeletes;
    protected $table='client_blackout_dates';
    protected $fillable = ['financial_year','client_id','division_id','district_id','store_id','date','description'];
    
    public function district()
    {
        return $this->belongsTo('App\Models\District', 'district_id');
    }
    public function division()
    {
        return $this->belongsTo('App\Models\Division', 'division_id');
    }
    public function client()
    {
        return $this->belongsTo('App\Models\Client', 'client_id');
    }
    public function store()
    {
        return $this->belongsTo('App\Models\Client', 'store_id');
    }
}
