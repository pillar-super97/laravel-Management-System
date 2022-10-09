<?php

namespace App\Providers;

use App\Role;
use App\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        $user = \Auth::user();

        
        if (! app()->runningInConsole()) {
            $roles = Role::with('permission')->get();

            foreach ($roles as $role) {
                foreach ($role->permission as $permission) {
                    $permissionArray[$permission->title][] = $role->id;
                }
            }
            
            foreach ($permissionArray as $title => $roles) {
                Gate::define($title, function (User $user) use ($roles) {
                    return count(array_intersect($user->role->pluck('id')->toArray(), $roles));
                });
            }
            
        }
        Gate::define('isAdmin', function($user) {
           foreach($user->role as $role)
                if($role->title=='Admin')
                    return 1;
            return 0;
        });
       
        Gate::define('isCorporate', function($user) {
            foreach($user->role as $role)
                if($role->title=='Corporate')
                    return 1;
            return 0;
        });
        Gate::define('isOperations', function($user) {
            foreach($user->role as $role)
                if($role->title=='Operations')
                    return 1;
            return 0;
        });
        
        /* define a manager user role */
        Gate::define('isArea', function($user) {
            foreach($user->role as $role)
                if($role->title=='Area')
                    return 1;
            return 0;
            //return $user->role->title == 'Area';
        });
      
        /* define a user role */
        Gate::define('isTeam', function($user) {
            foreach($user->role as $role)
                if($role->title=='Team')
                    return 1;
            return 0;
        });
        
        Gate::define('isOperationsManager', function($user) {
            foreach($user->role as $role)
                if($role->title=='Operations Manager')
                    return 1;
            return 0;
        });
        
        Gate::define('isDistrict', function($user) {
            foreach($user->role as $role)
                if($role->title=='District')
                    return 1;
            return 0;
        });


        //Client Role
        Gate::define('isClient', function($user) {
            foreach($user->role as $role)
                 if($role->title=='Client')
                     return 1;
             return 0;
         });
        
    }
}
