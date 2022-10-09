<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Permission
 *
 * @package App
 * @property string $title
*/
class Permission extends Model
{
    protected $fillable = ['title'];
    use SoftDeletes;
    
}
