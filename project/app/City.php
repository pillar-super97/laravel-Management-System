<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{
    
    protected $fillable = ['name','state_id'];
    
    public function state()
    {
        return $this->belongsTo('App\State', 'state_id');
    }
        
}
