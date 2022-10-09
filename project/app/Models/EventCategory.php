<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class EventCategory extends Model
{
    public $table = 'event_categories';
    public $timestamps = false;
    protected $fillable = [
        'event_id',
        'category_id',
        'tags'
    ];

    
    
}
