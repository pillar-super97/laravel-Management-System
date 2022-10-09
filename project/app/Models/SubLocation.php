<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class SubLocation extends Model
{
    public $table = 'tsp_sub_locations';
    protected $fillable = [
        'loc_id',
        'sub_loc',
        'description'
    ];
    
}
