<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class LocationTag extends Model
{
    public $table = 'location_tags';
    public $primaryKey = 'location_id';
    public $timestamps = false;
    protected $fillable = [
        'location_id',
        'location_tag_1',
        'location_tag_1_description'
    ];
    
}
