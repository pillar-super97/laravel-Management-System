<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreScheduleAvailabilityDays extends Model
{
    

    protected $fillable = ['store_id', 'days'];
    

//    public function students()
//    {
//        return $this->belongsToMany('App\User', 'lesson_student')->withTimestamps();
//    }
    
}
