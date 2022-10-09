<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventPrecalls extends Model
{
    protected $table = "event_precall_comments";

    protected $fillable = ['event_id', 'precall_manager','precall_comments','precall_completed_by','precall_completed_on'];
    
    public function event()
    {
        return $this->belongsTo('App\Models\Event', 'event_id');
    }
    
}
