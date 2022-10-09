<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExcludeAbsentEmployee extends Model
{
    public $timestamps = ["created_at"]; 
    protected $fillable = ['employee_id','event_id','note','created_by'];
    const UPDATED_AT = null;
    
    protected $casts = [
        'created_at' => 'datetime:m/d/Y',
    ];

    public function detail()
    {
        return $this->belongsTo('App\Models\Employee', 'employee_id');
    }
}
