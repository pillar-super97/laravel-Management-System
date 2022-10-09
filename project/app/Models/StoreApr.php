<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreApr extends Model
{
    

    protected $fillable = ['store_id', 'area_id'];
    
    public function area()
    {
        return $this->belongsTo('App\Models\Area', 'area_id');
    }
}
