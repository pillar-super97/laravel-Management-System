<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientBlackoutDates extends Model
{
    

    protected $fillable = ['client_id', 'date'];
    

//    public function students()
//    {
//        return $this->belongsToMany('App\User', 'lesson_student')->withTimestamps();
//    }
    
}
