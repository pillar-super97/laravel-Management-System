<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsentEmployee extends Model
{
    protected $casts = [
        'event_date' => 'datetime:m/d/Y',
    ];
}
