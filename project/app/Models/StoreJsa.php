<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreJsa extends Model
{
    

    protected $fillable = ['store_id', 'jsa_id'];
    
    public function jsa()
    {
        return $this->belongsTo('App\Models\Jsa', 'jsa_id');
    }
}
