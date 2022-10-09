<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class EventInventory extends Model
{
    public $table = 'event_inventory';
    protected $fillable = [
        'inventory_id'
    ];
    
}
