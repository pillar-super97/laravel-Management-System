<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    public $table = 'tsp_locations';
    protected $fillable = [
        'loc',
        'description'
    ];
    
}
