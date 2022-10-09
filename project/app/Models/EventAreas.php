<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventAreas extends Model
{
    

    protected $fillable = ['event_id', 'area_id','meet_time'];
    
    public function area()
    {
        return $this->belongsTo('App\Models\Area', 'area_id');
    }
    
}
