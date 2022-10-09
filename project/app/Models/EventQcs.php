<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventQcs extends Model
{
    protected $table = "event_qc_comments";

    protected $fillable = ['event_id', 'on_time','in_uniform','positive_exp','qc_comment','qc_completed_by','qc_completed_on'];
    
    public function event()
    {
        return $this->belongsTo('App\Models\Event', 'event_id');
    }
    
}
