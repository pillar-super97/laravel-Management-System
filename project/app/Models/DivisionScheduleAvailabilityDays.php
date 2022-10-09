<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DivisionScheduleAvailabilityDays extends Model
{
    

    protected $fillable = ['division_id', 'days'];
    

//    public function students()
//    {
//        return $this->belongsToMany('App\User', 'lesson_student')->withTimestamps();
//    }
    
}
