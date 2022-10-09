<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mileage extends Model
{
   use SoftDeletes;
    protected $fillable = ['area_id','jsa_id','store_id','distance'];
    
    public function area()
    {
        return $this->belongsTo('App\Models\Area', 'area_id');
    }
    public function jsa()
    {
        return $this->belongsTo('App\Models\Jsa', 'jsa_id');
    }
    
    public function store()
    {
        return $this->belongsTo('App\Models\Store', 'store_id');
    }
    
}
