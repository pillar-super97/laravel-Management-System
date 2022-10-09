<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistrictScheduleAvailabilityDays extends Model
{
    

    protected $fillable = ['district_id', 'days'];
    

//    public function students()
//    {
//        return $this->belongsToMany('App\User', 'lesson_student')->withTimestamps();
//    }
    
}
