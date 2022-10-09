<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Lesson
 *
 * @package App
 * @property string $course
 * @property string $title
 * @property string $slug
 * @property string $lesson_image
 * @property text $short_text
 * @property text $full_text
 * @property integer $position
 * @property string $downloadable_files
 * @property tinyInteger $free_lesson
 * @property tinyInteger $published
*/
class ClientScheduleAvailabilityDays extends Model
{
    

    protected $fillable = ['client_id', 'days'];
    

//    public function students()
//    {
//        return $this->belongsToMany('App\User', 'lesson_student')->withTimestamps();
//    }
    
}
