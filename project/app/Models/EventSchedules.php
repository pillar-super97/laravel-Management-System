<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventSchedules extends Model
{
    
    protected $fillable = ['event_id','loading_confirmed','last_inventory_value', 'store_billing','scheduled_production','schedule_length','gross_profit',
        'labor_percent','round_trip_miles','field_notes'];
    
    public function event()
    {
        return $this->belongsTo('App\Models\Event', 'event_id');
    }
    
}
