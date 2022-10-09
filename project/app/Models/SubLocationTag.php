<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class SubLocationTag extends Model
{
    public $table = 'sub_location_tags';
    public $primaryKey = 'sub_location_id';
    public $timestamps = false;
    protected $fillable = [
        'sub_location_id',
        'sub_location_tag_1',
        'sub_location_tag_1_description'
    ];
    
}
