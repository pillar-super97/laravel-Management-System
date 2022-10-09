<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use JWTAuth;
use App\User;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\Event;
use App\Models\Store;
use App\City;
use App\State;
use App\Models\Employee;


class ApiController extends Controller
{

    public function authenticate(Request $request)
    {      
        
        
        
        $credentials = $request->only('email', 'password');
        
        try {
            
           
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['Status'=>'error',
            'Status Code' =>400,'error' => 'invalid_credentials'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
        
        
        return response()->json(['Status'=>'Ok',
            'Status Code' =>200,
            'Message' =>'user authentication successful',
            'token'=>compact('token')
            ],200);  
    }
    
    public function getEventData(Request $request) {
        
        $from = date('Y-m-d',strtotime('-1 week'));
        $to = date('Y-m-d',strtotime('+1 week'));
        $events = Event::whereBetween('date', [$from, $to])
                ->select('events.id','events.date','stores.id as store_id','stores.name as store_name','stores.alr_disk as alr_disk',
                        'stores.address','cities.name as city','states.state_code as state','stores.zip','stores.manager_id')
                ->leftJoin('stores','stores.id','=','events.store_id')
                ->leftJoin('cities','stores.city_id','=','cities.id')
                ->leftJoin('states','stores.state_id','=','states.id')
                ->orderBy('events.date','asc')
                ->get();
        try {
            if (!Empty($events)) {
                return response()->json(['Status' => 'Ok', 'Status Code' => 200, 'events' => $events], 200);
            } else {
                return response()->json(['Status' => 'Error', 'Status Code' => 400, 'msg' => 'Event not available'], 400);
            }
        } catch (Exception $ex) {
            return response()->json(['Status' => 'Error', 'Status Code' => 500, 'msg' => 'Some thing went wrong'], 500);
        }
        
    }
    
    public function getUserData(Request $request) {
        
        $users = User::withTrashed()
                ->select('users.id','users.status','users.name','users.email','users.password','users.deleted_at','users.deleted_by',
                'users.employee_id','employees.emp_number','employees.ss_no','employees.first_name','employees.last_name',
                'employees.title','employees.gender','employees.cell_phone','employees.home_phone','employees.manager',
                'e1.email as area_manager','e2.email as district_manager','areas.id as area_id','areas.title as area','areas.area_number','areas.address','areas.state_id','areas.city_id','areas.zip')
                ->leftJoin('employees','employees.id','=','users.employee_id')
                ->leftJoin('employees as e1','employees.area_manager','=','e1.id')
                ->leftJoin('employees as e2','employees.district_manager','=','e2.id')
                ->leftJoin('areas','employees.area_id','=','areas.id')
                ->orderBy('users.id','asc')
                ->get()->toArray();
        //echo '<pre>';print_r($users);die;
        foreach($users as $key=>$user)
        {
           $user_areas = DB::table('area_user')
                ->select('users.email','areas.area_number','areas.id as areaid','areas.title as area','areas.area_number','areas.address','areas.state_id','areas.city_id','areas.zip')
                ->leftJoin('users','users.id','=','area_user.user_id')
                ->leftJoin('areas','areas.id','=','area_user.area_id')
                ->where('area_user.user_id','=',$user['id'])
                ->get()->toArray(); 
           $users[$key]['user_areas'] = $user_areas;
        }
        //echo '<pre>';print_r($users);die;
        try {
            if (!Empty($users)) {
                return response()->json(['Status' => 'Ok', 'Status Code' => 200, 'users' => $users], 200);
            } else {
                return response()->json(['Status' => 'Error', 'Status Code' => 400, 'msg' => 'No user available'], 400);
            }
        } catch (Exception $ex) {
            return response()->json(['Status' => 'Error', 'Status Code' => 500, 'msg' => 'Some thing went wrong'], 500);
        }
        
    }
    
    public function getEmployeeData(Request $request) {
        
        $users = Employee::select('employees.*','areas.title as area','areas.area_number','areas.address','areas.state_id','areas.city_id','areas.zip')
                //->whereNull('employees.deleted_at')
                //->where('employees.status','Active')
                ->leftJoin('areas','employees.area_id','=','areas.id')
                ->orderBy('employees.id','asc')
                ->get();
        try {
            if (!Empty($users)) {
                return response()->json(['Status' => 'Ok', 'Status Code' => 200, 'users' => $users], 200);
            } else {
                return response()->json(['Status' => 'Error', 'Status Code' => 400, 'msg' => 'No employee available'], 400);
            }
        } catch (Exception $ex) {
            return response()->json(['Status' => 'Error', 'Status Code' => 500, 'msg' => 'Some thing went wrong'], 500);
        }
        
    }
}
