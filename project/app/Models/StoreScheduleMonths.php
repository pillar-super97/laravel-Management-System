<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreScheduleMonths extends Model
{
    

    protected $fillable = ['store_id', 'month'];
    

//    public function students()
//    {
//        return $this->belongsToMany('App\User', 'lesson_student')->withTimestamps();
//    }
    
}
