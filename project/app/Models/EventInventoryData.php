<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class EventInventoryData extends Model
{
    public $table = 'event_inventory_data';
    protected $fillable = [
        'employee_id',
        'sub_location_id',
        'category_id',
        'records',
        'pieces',
        'cost',
        'price'
    ];
    
}
