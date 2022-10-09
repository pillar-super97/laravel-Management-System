<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeAvailabilityDays extends Model
{
    

    protected $fillable = ['employee_id', 'days'];
    

    public function employee()
    {
        return $this->belongsTo('App\Models\Employee', 'employee_id');
    }
    
}
