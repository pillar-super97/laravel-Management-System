<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Hash;
use App\Models\Area;

/**
 * Class User
 *
 * @package App
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $remember_token
*/
class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use SoftDeletes;
    protected $fillable = ['name', 'email', 'password', 'remember_token','employee_id', 'client_id','status', 'crystal_token'];
    
    
    public function getJWTIdentifier()
    {
      return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
      return [];
    }
    
    
    /**
     * Hash password
     * @param $input
     */
    public function setPasswordAttribute($input)
    {
        if ($input)
            $this->attributes['password'] = app('hash')->needsRehash($input) ? Hash::make($input) : $input;
    }
    
    public function employee()
    {
        return $this->belongsTo('App\Models\Employee', 'employee_id');
    }
    

    public function client()
    {
        return $this->belongsTo('App\Models\Client', 'client_id');
    }


    public function role()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }
    
    public function area()
    {
        return $this->belongsToMany(Area::class, 'area_user');
    }


    public function isAdmin()
    {
        return $this->role()->where('role_id', 1)->first();
    }


    public function isClient()
    {
        return $this->role()->where('role_id', 10)->first();
    }

}
