<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Models\Association;
use App\Models\Client;
use App\Models\Division;
use App\Models\District;
use App\Models\Store;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $user = Auth::user();

        if($user->client_id !== '' || $user->client_id !== null){

            $reportstores = Store::select('stores.id as store_id', 'stores.name as store_name', 'cities.id as city_id', 'cities.name as city_name',
                                    'states.id as state_id', 'states.name as state_name')
                                    ->leftJoin('cities','stores.city_id','=','cities.id')       
                                    ->leftJoin('states','cities.state_id','=','states.id')
                                    ->where('client_id', '=' ,$user->client_id)       
                                    ->get();
        }else{
            $reportstores = [];
        }


        $associations = Association::where('status','=', 'active')->count();
        $clients = Client::where('status','=', 'active')->count();
        $divisions = Division::where('status','=', 'active')->count();
        $districts = District::where('status','=', 'active')->count();
        $stores = Store::where('status','=', 'active')->count();
        
        $employees = Employee::where('status','=', 'active');
        if (Gate::allows('isArea') || Gate::allows('isTeam') || Gate::allows('isDistrict')) 
        {
            $areas = DB::table('area_user')->where('user_id','=',Auth::id())->get();
            $user_area = array();
            foreach($areas as $area)
                $user_area[]=$area->area_id;
            //$user_area=implode(',',$user_area);
            $employees->whereIn('area_id', $user_area);
        }
        $employees = $employees->get();
        $emp_benchmark = 0;
        $empcount = 0;
        $supervisor = 0;
        $driver = 0;
        $rx = 0;
        $avg_benchmark=0;
        foreach($employees as $emp)
        {
            $emp_benchmark+=round($emp->benchmark,2);
            $empcount++;
            if($emp->is_crew_leader==1)
                $supervisor++;
            if($emp->is_driver==1)
                $driver++;
            if($emp->is_rx==1)
                $rx++;
        }
        if($emp_benchmark && $empcount)
            $avg_benchmark = round($emp_benchmark/$empcount,2);
        
        //upcoming events
        $nextweek_events = 0;
        $nextthirtydaysevents = 0;
        
        if (Gate::allows('isArea') || Gate::allows('isTeam') || Gate::allows('isDistrict')) 
        {
            $date_from = date('Y-m-d');
            $date_to = date('Y-m-d',strtotime("+7 days"));
            //DB::enableQueryLog();
            $nextweek_events = DB::table('event_areas')
                        ->leftJoin('events','events.id','=','event_areas.event_id')
                        ->whereBetween('events.date',[$date_from,$date_to])
                        ->whereIn('event_areas.area_id', $user_area)
                        ->count();
            $date_from1 = date('Y-m-d');
            $date_to1 = date('Y-m-d',strtotime("+30 days"));
            //DB::enableQueryLog();
            $nextthirtydaysevents = DB::table('event_areas')
                        ->leftJoin('events','events.id','=','event_areas.event_id')
                        ->whereBetween('events.date',[$date_from1,$date_to1])
                        ->whereIn('event_areas.area_id', $user_area)
                        ->count();
            //dd(DB::getQueryLog());
            //print_r($nextthirtydaysevents);die;
        }
        //upcomin events end
        //performance 1 start
        $date_from2 = date('Y-m-d',strtotime("-7 days"));
        $date_to2 = date('Y-m-d');
        if (Gate::allows('isArea') || Gate::allows('isTeam')) 
        {
//            $areas = DB::select( DB::raw("SELECT event_id from event_areas where area_id in(".implode(',',$user_area).")") );
//            $sel_area = array();
//            foreach($areas as $area)
//                $sel_area[] = $area->event_id;
            $priorsevendays_data = DB::table('event_areas')
                    ->leftJoin('events','events.id','=','event_areas.event_id')
                    ->select('events.on_time','events.in_uniform','events.positive_exp')
                    ->whereBetween('events.qc_completed_on',[$date_from2,$date_to2])
                    //->whereIn('events.id',$sel_area)
                    ->whereIn('event_areas.area_id', $user_area)
                    ->get();
        }else{
            $priorsevendays_data = DB::table('events')
                    ->select('on_time','in_uniform','positive_exp')
                    ->whereBetween('qc_completed_on',[$date_from2,$date_to2])
                    ->get();
        }
        $earned_points = 0;
        $total_points = 0;
        $count = 0;
        $positive_counter = 0;
        foreach($priorsevendays_data as $row)
        {
           $total_points+=5;
           $count++;
           if($row->on_time=="Yes")
               $earned_points+=1;
           if($row->in_uniform=="Yes")
               $earned_points+=1;
           if($row->positive_exp=="Yes")
           {
               $positive_counter++;
               $earned_points+=3;
           }elseif($row->positive_exp=="No")
               $earned_points-=5;
        }
        if($count)
            $sevendayaverage = round($earned_points/$count,3);
        else
            $sevendayaverage = 0;
        //$percent = round(($earned_points*100)/$total_points,3);
        //$positivepercent = round(($positive_counter*100)/$count,3);
        //echo '<pre>';print_r($performance1_data);die;
        
        $date_from3 = date('Y-m-d',strtotime("-30 days"));
        $date_to3 = date('Y-m-d');
        if (Gate::allows('isArea') || Gate::allows('isTeam') || Gate::allows('isDistrict')) 
        {
//            $areas = DB::select( DB::raw("SELECT event_id from event_areas where area_id in(".implode(',',$user_area).")") );
//            $sel_area = array();
//            foreach($areas as $area)
//                $sel_area[] = $area->event_id;
            $priorthirtydays_data = DB::table('event_areas')
                    ->leftJoin('events','events.id','=','event_areas.event_id')
                    ->select('events.on_time','events.in_uniform','events.positive_exp')
                    ->whereBetween('events.qc_completed_on',[$date_from3,$date_to3])
                    ->whereIn('event_areas.area_id', $user_area)
                    ->get();
        }else{
            $priorthirtydays_data = DB::table('events')
                    ->select('on_time','in_uniform','positive_exp')
                    ->whereBetween('qc_completed_on',[$date_from3,$date_to3])
                    ->get();
        }
        $earned_points = 0;
        $total_points = 0;
        $count = 0;
        $positive_counter = 0;
        foreach($priorthirtydays_data as $row)
        {
           $total_points+=5;
           $count++;
           if($row->on_time=="Yes")
               $earned_points+=1;
           if($row->in_uniform=="Yes")
               $earned_points+=1;
           if($row->positive_exp=="Yes")
           {
               $positive_counter++;
               $earned_points+=3;
           }elseif($row->positive_exp=="No")
               $earned_points-=5;
        }
        if($count){
            $thirtydayaverage = round($earned_points/$count,3);
            $positivepercent = round(($positive_counter*100)/$count,2);
        }else{
            $thirtydayaverage = round($earned_points,3);
            $positivepercent = round(($positive_counter*100),2);
        }
        
        //team performance 2 average calculation
        $teamsevendayaverage = 0;
        $teamthirtydayaverage = 0;
        $teampositivepercent = 0;
        
        if (Gate::allows('isTeam')) 
        {
            $employee_id = Auth::user()->employee_id;
            $date_from4 = date('Y-m-d',strtotime("-7 days"));
            $date_to4 = date('Y-m-d');
            $teamsevendayaverage_data = DB::table('timesheet_data')
                    ->leftJoin('timesheet_header','timesheet_header.id','=','timesheet_data.timesheet_id')
                    ->leftJoin('events','events.id','=','timesheet_header.event_id')
                    ->select('events.on_time','events.in_uniform','events.positive_exp')
                    ->whereBetween('events.qc_completed_on',[$date_from4,$date_to4])
                    ->where('timesheet_data.employee_id','=',$employee_id)
                    ->get();
            $earned_points = 0;
            $count = 0;
            $positive_counter = 0;
            foreach($teamsevendayaverage_data as $row)
            {
               $count++;
               if($row->on_time=="Yes")
                   $earned_points+=1;
               if($row->in_uniform=="Yes")
                   $earned_points+=1;
               if($row->positive_exp=="Yes")
               {
                   $positive_counter++;
                   $earned_points+=3;
               }elseif($row->positive_exp=="No")
                   $earned_points-=5;
            }
            if($count)
                $teamsevendayaverage = round($earned_points/$count,3);
            
            $date_from5 = date('Y-m-d',strtotime("-30 days"));
            $date_to5 = date('Y-m-d');
            
            $teamthirtydayaverage_data = DB::table('timesheet_data')
                    ->leftJoin('timesheet_header','timesheet_header.id','=','timesheet_data.timesheet_id')
                    ->leftJoin('events','events.id','=','timesheet_header.event_id')
                    ->select('events.on_time','events.in_uniform','events.positive_exp')
                    ->whereBetween('events.qc_completed_on',[$date_from5,$date_to5])
                    ->where('timesheet_data.employee_id','=',$employee_id)
                    ->get();
            $earned_points = 0;
            $count = 0;
            $positive_counter = 0;
            if(count($teamthirtydayaverage_data))
            {
                foreach($teamthirtydayaverage_data as $row)
                {
                    $count++;
                    if($row->on_time=="Yes")
                        $earned_points+=1;
                    if($row->in_uniform=="Yes")
                        $earned_points+=1;
                    if($row->positive_exp=="Yes")
                    {
                        $positive_counter++;
                        $earned_points+=3;
                    }elseif($row->positive_exp=="No")
                        $earned_points-=5;
                }
                $teamthirtydayaverage = round($earned_points/$count,3);
                $teampositivepercent = round(($positive_counter*100)/$count,2);
            }
            
            
            
            
        }
        
        return view('home', compact('associations','clients','divisions','districts','stores','employees',
                'avg_benchmark','supervisor','driver','rx','empcount','nextweek_events','nextthirtydaysevents',
                'sevendayaverage','thirtydayaverage','positivepercent','teamsevendayaverage','teamthirtydayaverage',
                'teampositivepercent', 'reportstores'));
    }
}
