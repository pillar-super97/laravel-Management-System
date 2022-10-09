<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JsonFileLog extends Model
{
    protected $fillable = [
        'filename',
        'is_file_read'
    ];
    
}
