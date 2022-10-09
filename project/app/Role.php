<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
/**
 * Class Role
 *
 * @package App
 * @property string $title
*/
class Role extends Model
{
    protected $fillable = ['title'];
    use SoftDeletes;
    
    public function permission()
    {
        return $this->belongsToMany(Permission::class, 'permission_role');
    }
    
}
