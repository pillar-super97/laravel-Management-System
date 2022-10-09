<?php

namespace App\Http\Controllers\Admin;

use Carbon;
use App\Models\Event;
use App\Models\EventTruckDates;
use App\Models\EventSchedules;
use App\Models\EventScheduleEmployees;
use App\Models\EventAreas;
use App\Models\EventPrecalls;
use App\Models\EventQcs;
use App\Models\Employee;
use App\Models\EmployeeAvailabilityDays;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Http\Requests\Admin\StoreEventsRequest;
use App\Http\Requests\Admin\UpdateEventsRequest;
use App\Http\Controllers\Traits\FileUploadTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use MaddHatter\LaravelFullcalendar\Facades\Calendar;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Exports\EventInfoExport;
use App\Exports\ScheduleExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\EventImport;
use App\Imports\InvoiceImport;
use App\Imports\ValidateEventImport;
use App\Imports\ValidateInvoiceImport;
use App\Imports\MealImport;
use App\Imports\ValidateMealImport;
use App\Imports\LodgingImport;
use App\Imports\ValidateLodgingImport;
use PDO;
use Illuminate\Support\Facades\File;
use SplFileInfo;
use App\Http\Controllers\Traits\MsiTrait;
use App\Models\Area;
use Illuminate\Support\Facades\URL;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Jenssegers;

class EventsController extends Controller
{
    use FileUploadTrait;
    use MsiTrait;

    /**
     * Display a listing of Event.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ( Gate::allows('event_view') ||  Gate::allows('view_client_event')) {
            
        
        if (request('show_calender') == 1) 
        {
            if(request()->ajax()) 
            {

                $event[] = array();
                foreach ($events as $key => $value) {
                    $event[$key]['allDay'] = true;
                    $event[$key]['id'] = $value->id;
                    $event[$key]['title'] = $value->store->name;
                    $event[$key]['start'] = new \DateTime($value->date);
                    $event[$key]['end'] = new \DateTime($value->date.' +1 day');
                }
                return Response::json($event);
            }
            $template = 'admin';
            $pending_event = $this->getPendingEnvents($request);
            $associations = DB::table('associations')->pluck('name','id');
            
            if(@$request->user()->client_id) 
            {
                $template = 'clients';
                $stores = DB::table('stores')->where('client_id',$request->user()->client_id)->orderBy('name','asc')->pluck('name','id');
                $divisions = DB::table('divisions')->where('client_id',$request->user()->client_id)->pluck('name','id');
                $districts = DB::table('districts')->where('client_id',$request->user()->client_id)->where('status','=', 'active')->pluck('number','id');
            }
            else 
            
            {
                $stores = DB::table('stores')->orderBy('name','asc')->pluck('name','id');
                $divisions = DB::table('divisions')->pluck('name','id');
                $districts = DB::table('districts')->where('status','=', 'active')->pluck('number','id');

            }
            
            $clients = DB::table('clients')->pluck('name','id');
            
            $employees = DB::table('employees')->where('is_crew_leader',1)->where('status', '=', 'active')->orderBy('last_name','asc')->pluck('name','id');
            $areas = DB::table('areas')->where('status','=', 'active')->pluck('title','id'); 
            
            return view($template.'.events.index-calender', compact('stores','employees','areas','associations','clients','divisions','pending_event','districts'));
        }
        else{
            return $this->getEventListing($request);
        }

    }
    
    return abort(401);
        
    }

    private function getEventListing($request){
        $pending_event = $this->getPendingEnvents($request);
        $associations = DB::table('associations')->pluck('name','id');
        dd($associations);
        if(@$request->user()->client_id) 
        {
            $stores = DB::table('stores')->where('client_id',$request->user()->client_id)->orderBy('name','asc')->pluck('name','id');
            $divisions = DB::table('divisions')->where('client_id',$request->user()->client_id)->pluck('name','id');
            $districts = DB::table('districts')->where('client_id',$request->user()->client_id)->where('status','=', 'active')->pluck('number','id');

           // dd($district);
        }
        else
        {
            $stores = DB::table('stores')->orderBy('name','asc')->pluck('name','id');
            $divisions = DB::table('divisions')->pluck('name','id');
        $districts = DB::table('districts')->where('status','=', 'active')->pluck('number','id');
        } 

        $clients = DB::table('clients')->pluck('name','id');
        
        $employees = DB::table('employees')->where('is_crew_leader',1)->where('status', '=', 'active')->pluck('name','id');
        $areas = DB::table('areas')->where('status','=', 'active')->pluck('title','id');
        $template = 'admin';
        if(@$request->user()->client_id) 
            {
                $template = 'clients';
            }

        return view($template.'.events.index', compact('stores','employees','areas','associations','clients','divisions','pending_event','districts'));
    }

    private function getPendingEnvents($request){
        $pending_events = Event::select('events.*',DB::raw('MIN(date) as event_date'),'stores.name as storename')
            ->leftJoin('stores','stores.id','=','events.store_id')
            ->where('events.status','=','Pending')
            ->where('events.date','>=',date('Y-m-d'))
            ->groupBy('events.store_id');
            
        if (Gate::allows('isArea') || Gate::allows('isTeam') || Gate::allows('isDistrict')) {
            $user_assigned_areas = DB::table('area_user')->where('user_id','=',Auth::id())->get();
            $user_areas = array();
            foreach($user_assigned_areas as $area)
                $user_areas[]=$area->area_id;
            $user_assigned_areas = EventAreas::whereIn('area_id', $user_areas)->get();
            
            $area_event = array();
            foreach($user_assigned_areas as $area)
                $area_event[]=$area->event_id;
            $pending_events = $pending_events->whereIn('events.id',$area_event);
        }
        $pending_events = $pending_events->orderBy('id','asc')->get();
        $pending_event = array();
        foreach ($pending_events as $value) {
            $pending_event[$value->id] = $value->id.'-'.$value->storename.'-'.date('m-d-Y',strtotime($value->date)).'-'.date('h:i A',strtotime($value->start_time));
        }
        return $pending_event;
    }

    /**
     * Show the form for creating new Event.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('event_create')) {
            return abort(401);
        }
        $employees = DB::table('employees')->where('is_crew_leader',1)->where('status', '=', 'active')->pluck('name','id');
        $emps = array();
        foreach($employees as $key=>$emp)
        {
            $emps[]=array('value'=>$key,'label'=>$emp);
        }
        $stores_data = DB::table('stores')->pluck('name','id');
        $stores = array();
        foreach($stores_data as $key=>$row)
        {
            $stores[]=array('value'=>$key,'label'=>$row);
        }
        //echo '<pre>';print_r($employees);die;
        $areas = DB::table('areas')->where('status','=', 'active')->pluck('title','id'); 
        return view('admin.events.create', compact('stores','areas','emps'));
    }

    /**
     * Store a newly created Event in storage.
     *
     * @param  \App\Http\Requests\StoreEventsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreEventsRequest $request)
    {
        if (! Gate::allows('event_create')) {
            return abort(401);
        }
        //echo date("g:i A",strtotime($request->start_time));
        //echo '<pre>';print_r($request->all());die;
        $request->merge(['status' =>'Pending']);
        //echo date("g:i A",$k);
        $request->merge(['date' => date("Y-m-d", strtotime($request->date))]);
        $request->merge(['start_time' => date("H:i:s", strtotime($request->start_time))]);
        //echo '<pre>';print_r($request->all());die;
        
        $event = Event::create($request->all());
        $this->generateEventNumber($event->id);
        //echo '<pre>';print_r($request->all());die;
        if(count($request->areas))
        {
            foreach($request->areas as $area)
            {
                if($area)
                {
                    EventAreas::create([
                        'event_id' => $event->id,
                        'area_id' => $area,
                    ]);
                }
            }
        }
        if($request->truck_dates && count($request->truck_dates))
        {
            foreach($request->truck_dates as $dates)
            {
                if($dates)
                {
                    EventTruckDates::create([
                        'event_id' => $event->id,
                        'truck_date' => date("Y-m-d", strtotime($dates)),
                    ]);
                }
            }
        }
        //  echo "<pre>";
//        print_r($request->all());
//        die;
        if(request()->ajax()) 
        {
            $events = DB::table('events')
                ->whereDate('events.id', '=', $event->id)
                ->select('stores.name as title','events.*')
                ->leftJoin('stores','stores.id','=','events.store_id')
                ->first();
            $event[] = array();
            $event['id'] = $events->id;
            $event['title'] = $events->title;
            $event['start'] = new \DateTime($events->date);
            $event['end'] = new \DateTime($events->date.' +1 day');
            return Response::json($event);
        }
        return redirect()->route('admin.events.index')->with('successmsg', 'Event added successfully.');
    }


    /**
     * Show the form for editing Event.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('event_edit')) {
            return abort(401);
        }
        
        $stores = DB::table('stores')->pluck('name','id');
        $employees = DB::table('employees')->where('is_crew_leader',1)->where('status','=','Active')->pluck('name','id');
        $areas = DB::table('areas')->where('status','=', 'active')->pluck('title','id'); 
        $event = Event::with(array('store','areas','crew_leader_name','truck_dates'))->findOrFail($id);
        $historical_data = Event::with(array('truck_dates'))
            ->where('store_id','=',$event->store_id)
            ->where('date','<',$event->date)
            ->orderBy('date','desc')->limit(1)->first();
        //echo '<pre>';print_r($employees);die;
        return view('admin.events.edit', compact('event', 'stores','employees','areas','historical_data'));
    }

    /**
     * Update Event in storage.
     *
     * @param  \App\Http\Requests\UpdateEventsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateEventsRequest $request, $id)
    {
        if (! Gate::allows('event_edit')) {
            return abort(401);
        }
        $resetEventStatus = 0;
        $areas = EventAreas::where('event_id', '=',$id)->pluck('area_id')->toArray();
        foreach($areas as $area)
        {
            if(in_array($area, $request['areas']))
            {
                    continue;
            }else
            {
                EventAreas::where('event_id', '=',$id)->where('area_id', '=',$area)->delete();
                $area_employees = Employee::where('area_id', '=',$area)->pluck('id')->toArray();
                foreach($area_employees as $emps)
                {
                    EventScheduleEmployees::where('event_id', '=',$id)->where('employee_id', '=',$emps)->delete();
                }
//                $resetEventStatus=1;
//                break;
            }
        }
        
        $request->merge(['date' => date("Y-m-d", strtotime($request->date))]);
        $request->merge(['start_time' => date("H:i:s", strtotime($request->start_time))]);
        $event = Event::findOrFail($id);
        if($event->date!=$request['date'])
        {
            $request->merge(['precall_manager'=>NULL,'precall_comments'=>NULL,'precall_completed_by'=>NULL,'precall_completed_on'=>NULL]);
            $resetEventStatus=1;
        }
        if($resetEventStatus)
        {
            EventSchedules::where('event_id', '=',$id)->delete();
            EventScheduleEmployees::where('event_id', '=',$id)->delete();
            $request->merge(['status' => 'Pending']);
//            $where1 = array('id' => $id);
//            $updateArr1 = ['status' =>'Pending' ];
//            $event  = Event::where($where1)->update($updateArr1);
        }
        if($event->start_time!=$request['start_time'])
        {
            $request->merge(['precall_manager'=>NULL,'precall_comments'=>NULL,'precall_completed_by'=>NULL,'precall_completed_on'=>NULL]);
        }
        //echo '<pre>';print_r($event);print_r($request->all());die;
       
        $event->update($request->all());
        if($request->areas && count($request->areas))
        {
            $old_areas = EventAreas::where('event_id', '=',$event->id)->pluck('area_id')->toArray();
            $new_areas=array();
            foreach($request->areas as $area)
                    $new_areas[]=$area;
            foreach($old_areas as $old_area)
            {
                if(in_array($old_area, $new_areas))
                    continue;
                else
                    EventAreas::where('event_id', '=',$id)->where('area_id', '=',$old_area)->delete();
            }

            foreach($request->areas as $area)
            {
                $area_already_exist = EventAreas::where('area_id','=',$area)->where('event_id', '=',$event->id)->get();
                if($area_already_exist->isEmpty())
                        EventAreas::create(['event_id' => $event->id,'area_id' => $area]);
                else
                    continue;
            }
        }
        if($request->truck_dates && count($request->truck_dates))
        {
            EventTruckDates::where('event_id', '=',$id)->delete();
            foreach($request->truck_dates as $date)
            {
                if($date)
                {
                    EventTruckDates::create([
                        'event_id' => $event->id,
                        'truck_date' => date("Y-m-d", strtotime($date)),
                    ]);
                }
            }
        }
        return redirect()->route('admin.events.index')->with('successmsg', 'Event updated successfully.');
    }


    /**
     * Display Event.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        if (! Gate::allows('event_view')) {
            return abort(401);
        }
        
        $event = Event::with(array('store','areas','crew_leader_name','truck_dates','qc_by'))->findOrFail($id);

        

        $historical_data = Event::with(array('truck_dates'))
            ->where('store_id','=',$event->store_id)
            ->where('date','<',$event->date)
            ->orderBy('date','desc')->limit(1)->first();

        // use current event if there is no any previous event for current store

        if(!$historical_data) $historical_data = clone $event;
          
        if($event->store_id)
        {
        $history_data = DB::table('timesheet_header')
                ->where('timesheet_header.store_id','=',$event->store_id)
                ->where('timesheet_header.dtJobDate','<',$event->date)
                 ->orderBy('timesheet_header.dtJobDate','desc')->limit(1)
                ->first();
        }
        $his_data=array();
        if($history_data && $history_data->id)
        {
            $last_crew_count = DB::table('timesheet_data')
                ->where('timesheet_data.timesheet_id','=',$history_data->id)
                 ->where('timesheet_data.iAttendanceFlag','!=',3)
                 ->where('timesheet_data.iAttendanceFlag','!=',4)
                ->count();
            $his_data=array();
            $his_data['last_crew_count'] = $last_crew_count;
            //echo '<pre>';print_r($history_data);die;
            $datetime1 = strtotime($history_data->InvRecapWrapTime);
            $datetime2 = strtotime($history_data->InvRecapStartTime);
            $diff = abs($datetime1 - $datetime2);  
            $hours = floor(($diff/ (60*60)));
            $minutes = floor(($diff - $hours*60*60)/ 60);

            $his_data['last_count_length'] = $hours.':'.$minutes;
            if($event->store->pieces_or_dollars=="dollars")
                $emp_count_per_hour = (@$history_data->dEmpCount/@$history_data->TTLMH);
            else
                $emp_count_per_hour = (@$history_data->dEmpPieces/@$history_data->TTLMH);
            $his_data['last_production_count'] = round($emp_count_per_hour,2);
            //$his_data['last_production_count'] = number_format((float)($history_data->dEmpCount/$history_data->TTLMH),2,'.','');
            $his_data['last_inventory_value'] = $history_data->dEmpCount;
            $his_data['last_inventory_date'] = date('m/d/Y',strtotime($history_data->dtJobDate));
            $his_data['last_start_time'] = $historical_data->start_time;
        }else{
            $last_crew_count=array();
        }
        if(request()->ajax()) 
        {
            if(@$historical_data->date)
                $historical_data->date = date('m/d/Y',strtotime($historical_data->date));
            if(@$event->store->city_id)
                $city = DB::table('cities')->select('name')->where('id','=',$event->store->city_id)->first();
            else
                $city = '';
            if(@$event->store->state_id)
                $state = DB::table('states')->select('state_code')->where('id','=',$event->store->state_id)->first();
            else
                $state = '';
            $city = @$city->name.','.@$state->state_code;
            $event->start_time=date('g:i A',strtotime($event->start_time));
            return Response::json(array('city'=>$city,'event'=>$event,'historical_data'=>$historical_data,'his_data'=>$his_data));
        }
                
        
        
        return view('admin.events.show',compact('event','historical_data','his_data'));
    }


    /**
     * Remove Event from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('event_delete')) {
            return abort(401);
        }
        if(request()->input('_recover')){
            //$event = Event::findOrFail($id);
            DB::table('events')->where('id', $id)->update(['status' => DB::raw("(CASE WHEN deleted_at < date('2021-10-27') THEN 'Pending' ELSE events.last_status END)"), 'deleted_at'=> null, 'deleted_by' => null, 'last_status' => null]);  
            return redirect()->route('admin.events.get_inactive')->with('successmsg', 'Event recovered successfully.');          
        }else{
            $event = Event::findOrFail($id);
            $event->last_status = $event->status;
            $event->status = 'inactive';
            if($event->save()){
                $event->delete();
            }
        }
        return redirect()->route('admin.events.index')->with('successmsg', 'Event deleted successfully.');
    }

    /**
     * Delete all selected Event at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('event_mass_delete')) {
            return abort(401);
        }
        $deletedResponse = [];
        if ($request->input('ids')) {
            $entries = Event::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->last_status = $entry->status;
                $entry->status = 'inactive';
                $entry->save();
                $deletedResponse[] = $entry->delete();
            }
        }
        //added ajax functionality for mass deletion of event on October 22, 2021
        if($request->ajax()){
            $resp = ['success' => false, 'message' => 'Sorry! Something went wrong!'];
            if(count($entries) == count($deletedResponse)){
                $resp['redirectURL'] = route('admin.events.index');
                $resp['message'] = 'Events deleted successfully.';    
                $resp['success'] = true;
            }
            return response()->json($resp);
        }
        //end
    }


    /**
     * Restore Event from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('event_delete')) {
            return abort(401);
        }
        $event = Event::onlyTrashed()->findOrFail($id);
        $event->restore();

        return redirect()->route('admin.events.index')->with('successmsg', 'Event set as active successfully.');
    }

    /**
     * Permanently delete Event from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('event_delete')) {
            return abort(401);
        }
        $event = Event::onlyTrashed()->findOrFail($id);
        $event->forceDelete();

        return redirect()->route('admin.events.index')->with('successmsg', 'Event deleted successfully.');
    }
    
    public function feedback(Request $request)
    {
        //echo '<pre>';        print_r($request->all());die;
        EventTruckDates::where('event_id', '=',$request['event_id'])->delete();
        if(count($request->truck_dates))
        {
            foreach($request->truck_dates as $date)
            {
                if($date)
                {           
                    EventTruckDates::create([
                        'event_id' => $request['event_id'],
                        'truck_date' => date("Y-m-d", strtotime($date)),
                    ]);
                }
            }
        }
        $event = Event::findOrFail($request['event_id']);
        $event->update($request->all());
        return Response::json(array('message'=>'Feedback submitted successfully.'),200);
    }
    
    public function qc(Request $request)
    {

        $event = Event::with(array('store','crew_leader_name'))->findOrFail($request['event_id']);
        //echo $event->crew_leader_name->area->title;die;
        $team_supervisor = DB::table('timesheet_header')
                ->select('employees.*')
                ->leftJoin('employees','employees.ss_no','=','timesheet_header.CrewManagerSSN')
                ->leftJoin('timesheet_approved','timesheet_approved.timesheet_id','=','timesheet_header.id')
                ->where('timesheet_approved.is_supervisor','=',1)
                ->where('timesheet_header.event_id','=',$request['event_id'])
                ->first();
        if($team_supervisor)
        {
            $crew_leader=$team_supervisor->first_name;
        }elseif(@$event->crew_leader_name->name)
        {
            $crew_leader=$event->crew_leader_name->name;
        }else{
            $crew_leader='';
        }
        
        $request->request->add(['qc_completed_by' => Auth::id(),'qc_completed_on'=>date("Y-m-d H:i:s")]);
        if($request['qc_comment']!="" || $request['positive_exp']=="No")
        {
            if($request['positive_exp']=="No")
                $qc_comment = 'Client did not have a positive experience.<br>'.$request['qc_comment'];
            else
                $qc_comment = $request['qc_comment'];
            $user_detail = array(
                'name'            => 'QC Team',
                'email'           => 'qccalls@msi-inv.com',
                //'email'           => 'kunal.kumar@pegasusone.com',
                'qc_contact'      => $request['qc_contact'],
                'comment'         => $qc_comment,
                'mail_from_email' => env('MAIL_FROM'),
                'mail_from'       => env('MAIL_NAME'),
                'store'           => $event->store->name.','.$event->store->city->name.','.$event->store->state->name,
                'date'            => date('m-d-Y',strtotime($event->date)),
                'subject'         => 'Store '.$event->store->name.' on '.date('m-d-Y',strtotime($event->date)),
                'crew_manager_area'=>@$event->crew_leader_name->area->title,
                'crew_manager_name'=>@$crew_leader
            );
            //echo '<pre>';print_r($user_detail);die;
            $user_single = (object) $user_detail;
            Mail::send('emails.event_qc_send_notification',['user' => $user_single], function ($message) use ($user_single) {
                $message->from($user_single->mail_from_email,$user_single->mail_from);
                $message->to($user_single->email, $user_single->name)->subject($user_single->subject);
                $message->replyTo($user_single->mail_from_email,$user_single->mail_from);
            });
            
            $team_supervisor = DB::table('timesheet_header')
                ->select('employees.*')
                ->leftJoin('employees','employees.ss_no','=','timesheet_header.CrewManagerSSN')
                ->leftJoin('timesheet_approved','timesheet_approved.timesheet_id','=','timesheet_header.id')
                ->where('timesheet_approved.is_supervisor','=',1)
                ->where('timesheet_header.event_id','=',$request['event_id'])
                ->first();
            if($team_supervisor && $team_supervisor->email)
            {
                $user_detail = array(
                    'name'            => $team_supervisor->first_name,
                    'email'           => $team_supervisor->email,
                    'qc_contact'      => $request['qc_contact'],
                    'comment'         => $qc_comment,
                    'mail_from_email' =>env('MAIL_FROM'),
                    'mail_from'       =>env('MAIL_NAME'),
                    'store'           => $event->store->name.','.$event->store->city->name.','.$event->store->state->name,
                    'date'            => date('m-d-Y',strtotime($event->date)),
                    'subject'         => 'Store '.$event->store->name.' on '.date('m-d-Y',strtotime($event->date)),
                    'crew_manager_area'=>@$event->crew_leader_name->area->title,
                    'crew_manager_name'=>@$crew_leader
                );
                $user_single = (object) $user_detail;
                Mail::send('emails.event_qc_send_notification',['user' => $user_single], function ($message) use ($user_single) {
                    $message->from($user_single->mail_from_email,$user_single->mail_from);
                    $message->to($user_single->email, $user_single->name)->subject($user_single->subject);
                    $message->replyTo($user_single->mail_from_email,$user_single->mail_from);
                });
            }else{
                $event_supervisor = DB::table('events')
                ->select('employees.*')
                ->leftJoin('employees','employees.id','=','events.crew_leader')
                ->where('events.id','=',$request['event_id'])
                ->first();
                if($event_supervisor && $event_supervisor->email)
                {
                    $user_detail = array(
                        'name'            => $event_supervisor->first_name,
                        'email'           => $event_supervisor->email,
                        'qc_contact'      => $request['qc_contact'],
                        'comment'         => $qc_comment,
                        'mail_from_email' =>env('MAIL_FROM'),
                        'mail_from'       =>env('MAIL_NAME'),
                        'store'           => $event->store->name.','.$event->store->city->name.','.$event->store->state->name,
                        'date'            => date('m-d-Y',strtotime($event->date)),
                        'subject'         => 'Store '.$event->store->name.' on '.date('m-d-Y',strtotime($event->date)),
                        'crew_manager_area'=>@$event->crew_leader_name->area->title,
                        'crew_manager_name'=>@$crew_leader
                    );
                    $user_single = (object) $user_detail;
                    Mail::send('emails.event_qc_send_notification',['user' => $user_single], function ($message) use ($user_single) {
                        $message->from($user_single->mail_from_email,$user_single->mail_from);
                        $message->to($user_single->email, $user_single->name)->subject($user_single->subject);
                        $message->replyTo($user_single->mail_from_email,$user_single->mail_from);
                    });
                }
            }
                      
            $areas = DB::table('event_areas')
                ->where('event_id','=',$request['event_id'])
                ->get();
            $event_areas=array();
            foreach($areas as $area)
            {
                $event_areas[]=$area->area_id;
            }
            $area_managers = DB::table('employees')
                ->select('employees.email','employees.first_name','employees.area_manager')
                ->whereIn('area_id',$event_areas)
                ->where('status','=','Active')
                ->where('title','=','Area Manager')
                ->get();
            if($area_managers)
            {
                foreach($area_managers as $area_manager)
                {
                    if($area_manager->email)
                    {
                        $user_detail = array(
                            'name'            => @$area_manager->first_name,
                            'email'           => $area_manager->email,
                            'qc_contact'      => $request['qc_contact'],
                            'comment'         => $qc_comment,
                            'mail_from_email' =>env('MAIL_FROM'),
                            'mail_from'       =>env('MAIL_NAME'),
                            'store'           => $event->store->name.','.$event->store->city->name.','.$event->store->state->name,
                            'date'            => date('m-d-Y',strtotime($event->date)),
                            'subject'         => 'Store '.$event->store->name.' on '.date('m-d-Y',strtotime($event->date)),
                            'crew_manager_area'=>@$event->crew_leader_name->area->title,
                            'crew_manager_name'=>@$crew_leader
                        );
                        $user_single = (object) $user_detail;
                        Mail::send('emails.event_qc_send_notification',['user' => $user_single], function ($message) use ($user_single) {
                            $message->from($user_single->mail_from_email,$user_single->mail_from);
                            $message->to($user_single->email, $user_single->name)->subject($user_single->subject);
                            $message->replyTo($user_single->mail_from_email,$user_single->mail_from);
                        });
                    }
                    $district_manager = DB::table('employees')
                        ->select('employees.email','employees.first_name')
                        ->where('status','=','Active')
                        ->where('id','=',$area_manager->area_manager)
                        ->first();
                    if($district_manager && $district_manager->email)
                    {
                       $user_detail = array(
                            'name'            => @$district_manager->first_name,
                            'email'           => $district_manager->email,
                            'qc_contact'      => $request['qc_contact'],
                            'comment'         => $qc_comment,
                            'mail_from_email' =>env('MAIL_FROM'),
                            'mail_from'       =>env('MAIL_NAME'),
                            'store'           => $event->store->name.','.$event->store->city->name.','.$event->store->state->name,
                            'date'            => date('m-d-Y',strtotime($event->date)),
                            'subject'         => 'Store '.$event->store->name.' on '.date('m-d-Y',strtotime($event->date)),
                            'crew_manager_area'=>@$event->crew_leader_name->area->title,
                            'crew_manager_name'=>@$crew_leader
                        );
                        $user_single = (object) $user_detail;
                        Mail::send('emails.event_qc_send_notification',['user' => $user_single], function ($message) use ($user_single) {
                            $message->from($user_single->mail_from_email,$user_single->mail_from);
                            $message->to($user_single->email, $user_single->name)->subject($user_single->subject);
                            $message->replyTo($user_single->mail_from_email,$user_single->mail_from);
                        }); 
                    }
                }
            }
        }
        $event->update($request->all());
        //EventQcs::create($request->all());
        return Response::json(array('message'=>'QC information submitted successfully.'),200);
    }
    public function precall(Request $request)
    {
        $event = Event::with(array('store','crew_leader_name'))->findOrFail($request['event_id']);
        //echo '<pre>';        print_r($event);print_r($request->all());die;
        $request->request->add(['precall_completed_by' => Auth::id(),'precall_completed_on'=>date("Y-m-d H:i:s")]);
        $precall_completed_by = Auth::id();
        $precall_completed_on = date("Y-m-d H:i:s");
        if($request['precall_comments']!="")
        {
            //echo '<pre>';
            $user_detail = array(
                'name'            => 'PIC Team',
                'email'           => 'piccalls@msi-inv.com',
                'comment'         => $request['precall_comments'],
                'mail_from_email' => env('MAIL_FROM'),
                'mail_from'       => env('MAIL_NAME'),
                'store'           => $event->store->name.','.$event->store->city->name.','.$event->store->state->name,
                'date'            => date('m-d-Y',strtotime($event->date)),
                'subject'         => 'Store '.$event->store->name.' on '.date('m-d-Y',strtotime($event->date))
            );
            //echo '<pre>';print_r($user_detail);die;
            $user_single = (object) $user_detail;
            Mail::send('emails.event_send_notification',['user' => $user_single], function ($message) use ($user_single) {
                $message->from($user_single->mail_from_email,$user_single->mail_from);
                $message->to($user_single->email, $user_single->name)->subject($user_single->subject);
                $message->replyTo($user_single->mail_from_email,$user_single->mail_from);
            });
            
            $team_supervisor = DB::table('event_schedule_employees')
                ->select('employees.*')
                ->leftJoin('employees','employees.id','=','event_schedule_employees.employee_id')
                ->where('event_schedule_employees.task','=','Supervisor')
                ->where('event_schedule_employees.event_id','=',$request['event_id'])
                ->first();
            if($team_supervisor && $team_supervisor->email)
            {
                $user_detail = array(
                    'name'            => $team_supervisor->first_name,
                    'email'           => $team_supervisor->email,
                    'comment'         => $request['precall_comments'],
                    'mail_from_email' =>env('MAIL_FROM'),
                    'mail_from'       =>env('MAIL_NAME'),
                    'store'           => $event->store->name.','.$event->store->city->name.','.$event->store->state->name,
                    'date'            => date('m-d-Y',strtotime($event->date)),
                    'subject'         => 'Store '.$event->store->name.' on '.date('m-d-Y',strtotime($event->date))
                );
                $user_single = (object) $user_detail;
                Mail::send('emails.event_send_notification',['user' => $user_single], function ($message) use ($user_single) {
                    $message->from($user_single->mail_from_email,$user_single->mail_from);
                    $message->to($user_single->email, $user_single->name)->subject($user_single->subject);
                    $message->replyTo($user_single->mail_from_email,$user_single->mail_from);
                });
            }elseif($event->crew_leader_name->email){
                $user_detail = array(
                    'name'            => $event->crew_leader_name->first_name,
                    'email'           => $event->crew_leader_name->email,
                    'comment'         => $request['precall_comments'],
                    'mail_from_email' =>env('MAIL_FROM'),
                    'mail_from'       =>env('MAIL_NAME'),
                    'store'           => $event->store->name.','.$event->store->city->name.','.$event->store->state->name,
                    'date'            => date('m-d-Y',strtotime($event->date)),
                    'subject'         => 'Store '.$event->store->name.' on '.date('m-d-Y',strtotime($event->date))
                );
                $user_single = (object) $user_detail;
                Mail::send('emails.event_send_notification',['user' => $user_single], function ($message) use ($user_single) {
                    $message->from($user_single->mail_from_email,$user_single->mail_from);
                    $message->to($user_single->email, $user_single->name)->subject($user_single->subject);
                    $message->replyTo($user_single->mail_from_email,$user_single->mail_from);
                });
            }
            
            $areas = DB::table('event_areas')
                ->where('event_id','=',$request['event_id'])
                ->get();
            $event_areas=array();
            foreach($areas as $area)
            {
                $event_areas[]=$area->area_id;
            }
            
            $area_managers = DB::table('employees')
                ->select('employees.email','employees.first_name','employees.area_manager')
                ->whereIn('area_id',$event_areas)
                ->where('status','=','Active')
                ->where('title','=','Area Manager')
                ->get();
            
            if($area_managers)
            {
                foreach($area_managers as $area_manager)
                {
                    if($area_manager->email)
                    {
                        $user_detail = array(
                            'name'            => @$area_manager->first_name,
                            'email'           => $area_manager->email,
                            'comment'         => $request['precall_comments'],
                            'mail_from_email' =>env('MAIL_FROM'),
                            'mail_from'       =>env('MAIL_NAME'),
                            'store'           => $event->store->name.','.$event->store->city->name.','.$event->store->state->name,
                            'date'            => date('m-d-Y',strtotime($event->date)),
                            'subject'         => 'Store '.$event->store->name.' on '.date('m-d-Y',strtotime($event->date))
                        );
                        $user_single = (object) $user_detail;
                        Mail::send('emails.event_send_notification',['user' => $user_single], function ($message) use ($user_single) {
                            $message->from($user_single->mail_from_email,$user_single->mail_from);
                            $message->to($user_single->email, $user_single->name)->subject($user_single->subject);
                            $message->replyTo($user_single->mail_from_email,$user_single->mail_from);
                        });
                    }
                    $district_manager = DB::table('employees')
                        ->select('employees.email','employees.first_name')
                        ->where('status','=','Active')
                        ->where('id','=',$area_manager->area_manager)
                        ->first();
                    if($district_manager && $district_manager->email)
                    {
                       $user_detail = array(
                        'name'            => @$district_manager->first_name,
                        'email'           => $district_manager->email,
                        'comment'         => $request['precall_comments'],
                        'mail_from_email' =>env('MAIL_FROM'),
                        'mail_from'       =>env('MAIL_NAME'),
                        'store'           => $event->store->name.','.$event->store->city->name.','.$event->store->state->name,
                        'date'            => date('m-d-Y',strtotime($event->date)),
                        'subject'         => 'Store '.$event->store->name.' on '.date('m-d-Y',strtotime($event->date))
                        );
                        $user_single = (object) $user_detail;
                        Mail::send('emails.event_send_notification',['user' => $user_single], function ($message) use ($user_single) {
                            $message->from($user_single->mail_from_email,$user_single->mail_from);
                            $message->to($user_single->email, $user_single->name)->subject($user_single->subject);
                            $message->replyTo($user_single->mail_from_email,$user_single->mail_from);
                        }); 
                    }
                }
            }
        }
        $event->update($request->all());
        //EventPrecalls::create($request->all());
        return Response::json(array('message'=>'Precall information submitted successfully.'),200);
    }
    
    public function get_event_feedback_data($store_id) {
        //echo $store_id;die;
        $data = Event::with(array('truck_dates'))->where('store_id','=',$store_id)->orderBy('id','desc')->limit(1)->first();
        return Response::json(array('data'=>$data),200);
    }
    
    public function savecalendarevent(Request $request) {
        $request->merge(['date' => date("Y-m-d", strtotime($request->event_date))]);
        //echo '<pre>';print_r($request->all());die;
        
        $event = Event::create($request->all());
        $this->generateEventNumber($event->id);
        //echo '<pre>';print_r($request->all());die;
        if(count($request->areas))
        {
            foreach($request->areas as $area)
            {
                if($area)
                {
                    EventAreas::create([
                        'event_id' => $event->id,
                        'area_id' => $area,
                    ]);
                }
            }
        }
//        if($request->truck_dates && count($request->truck_dates))
//        {
//            foreach($request->truck_dates as $dates)
//            {
//                if($dates)
//                {
//                    EventTruckDates::create([
//                        'event_id' => $event->id,
//                        'truck_date' => date("Y-m-d", strtotime($dates)),
//                    ]);
//                }
//            }
//        }
        
        
        $events = Event::with(array('store'))->orderBy('id','desc')->get();
        $event = [];
        if($events->count()) {
            foreach ($events as $key => $value) {
//                $event[] = Calendar::event(
//                    $value->store->name,
//                    true,
//                    new \DateTime($value->date),
//                    new \DateTime($value->date.' +1 day'),
//                    $value->id,
//                    // Add color and link on event
//                        [
//                            'color' => '#f05050',
//                            'url' => '#',
//                        ]
//                );
                $event[$key+1]['id'] = $value->id;
                $event[$key+1]['title'] = $value->store->name;
                $event[$key+1]['isAllDay'] = 1;
                $event[$key+1]['start'] = new \DateTime($value->date);
                $event[$key+1]['end'] = new \DateTime($value->date.' +1 day');
            }
         }
        //echo '<pre>'; print_r($event);die;
        return Response::json(array($event),200);
    }
    
    public function get_list(Request $request) {
        $start = (!empty($_POST["start"])) ? ($_POST["start"]) : ('');
        $end = (!empty($_POST["end"])) ? ($_POST["end"]) : ('');
        $events = DB::table('event_areas')
                ->whereDate('events.date', '>=', $start)->whereDate('events.date',   '<=', $end)
                ->where('events.status','!=','inactive')
                ->select(
					'events.start_time','event_areas.meet_time as meet_time','stores.client_id as client_id','stores.name as title','areas.area_number',
					'states.name as state','states.state_code as state_code','cities.name as city','events.*','events.start_time as start_time',
					\DB::raw('isEmployeeAssignedToEventArea(events.id,areas.id) as is_employee_assigned'))
                ->leftJoin('events','events.id','=','event_areas.event_id')
                ->leftJoin('stores','stores.id','=','events.store_id')
                ->leftJoin('cities','stores.city_id','=','cities.id')
                ->leftJoin('states','stores.state_id','=','states.id')
                ->leftJoin('areas','areas.id','=','event_areas.area_id');
                //->leftJoin('event_schedule_employees','event_schedule_employees.area_id','=','event_areas.area_id');
        dd($event);
        //$events = $events->where('event_schedule_employees.id','!=',null);
        if($request['date_between'] != ''){
            $date_between = explode(' - ',$request['date_between']);
            $date_from = date('Y-m-d', strtotime($date_between[0]));
            $date_to = date('Y-m-d', strtotime($date_between[1]));
            $events = $events->whereBetween('events.date',[$date_from,$date_to]);
            //$searchQuery .= " and (events.date between '".$date_from."' and '".$date_to."') ";
        }
        if (Gate::allows('isArea') || Gate::allows('isTeam') || Gate::allows('isDistrict')) {
            $areas = DB::table('area_user')->where('user_id','=',Auth::id())->get();
            $user_area = array();
            foreach($areas as $area)
                $user_area[]=$area->area_id;
            $event_area = EventAreas::whereIn('area_id', $user_area)->get();
            $area_event = array();
            foreach($event_area as $area)
                $area_event[]=$area->event_id;
            $events = $events->whereIn('events.id',$area_event);
            //$searchQuery .= " and (events.id in (".implode(',',$area_event).")) ";
        }
        if($request['association_id'] != ''){
            $association_id = implode(',',$request['association_id']);
            dd($association_id);
            $stores = DB::select( DB::raw("select id from stores WHERE association_id in(".$association_id.")") );
            $store_id=array();
            foreach($stores as $store)
            {
               $store_id[]=$store->id; 
            }
            //$store_id = implode(',',$store_id);
            $events = $events->whereIn('events.store_id',$store_id);
            //$searchQuery .= " and (events.store_id in(".$store_id.")) ";
        }
        if($request['client_id'] != ''){
            $client_id = implode(',',$request['client_id']);
            $stores = DB::select( DB::raw("select id from stores WHERE client_id in(".$client_id.")") );
            $store_id=array();
            foreach($stores as $store)
            {
               $store_id[]=$store->id; 
            }
            //$store_id = implode(',',$store_id);
            $events = $events->whereIn('events.store_id',$store_id);
            //$searchQuery .= " and (events.store_id in(".$store_id.")) ";
        }
        if($request['division_id'] != ''){
            $division_id = implode(',',$request['division_id']);
            $stores = DB::select( DB::raw("select id from stores WHERE division_id in(".$division_id.")") );
            $store_id=array();
            foreach($stores as $store)
            {
               $store_id[]=$store->id; 
            }
            //$store_id = implode(',',$store_id);
            $events = $events->whereIn('events.store_id',$store_id);
            //$searchQuery .= " and (events.store_id in(".$store_id.")) ";
        }
        if($request['district_id'] != ''){
            $district_id = implode(',',$request['district_id']);
            $stores = DB::select( DB::raw("select id from stores WHERE district_id in(".$district_id.")") );
            $store_id=array();
            foreach($stores as $store)
            {
               $store_id[]=$store->id; 
            }
            //$store_id = implode(',',$store_id);
            $events = $events->whereIn('events.store_id',$store_id);
            //$searchQuery .= " and (events.store_id in(".$store_id.")) ";
        }
        //echo '<pre>';print_r($store_id);die;
        if($request['store_id'] != ''){
            $store_id=array();
            foreach($request['store_id'] as $store)
            {
               $store_id[]=$store; 
            }
            //$store_id = implode(',',$request['store_id']);
            $events = $events->whereIn('events.store_id',$store_id);
            //$searchQuery .= " and (events.store_id in(".$store_id.")) ";
        }
        if($request['area_id'] != ''){
            $area_id = implode(',',$request['area_id']);
            $areas = DB::select( DB::raw("SELECT event_id from event_areas where area_id in(".$area_id.")") );
            $sel_area = array();
            foreach($areas as $area)
                $sel_area[] = $area->event_id;
            $events = $events->whereIn('events.id',$sel_area);
            //$searchQuery .= " and (events.id in(".implode(',',$sel_area).")) ";
        }
        
        if($request['supervisor'] != ''){
            $supervisor = implode(',',$request['supervisor']);
            $events_res = DB::select( DB::raw("select id from events WHERE crew_leader in(".$supervisor.") and status='Pending'") );
            $event_arr=array();
            foreach($events_res as $row)
            {
               $event_arr[]=$row->id; 
            }
            $events_res1 = DB::table('event_schedule_employees')
                            ->select('events.id')
                            ->leftJoin('events','events.id','=','event_schedule_employees.event_id')
                            ->where('events.status','=',"Scheduled")
                            ->where(function($q) {
                                $q->where('event_schedule_employees.task', '=', 'Supervisor')
                                ->orWhere('event_schedule_employees.task','=', 'Super/Driver To')
                                ->orWhere('event_schedule_employees.task','=', 'Super/Driver From')
                                ->orWhere('event_schedule_employees.task','=', 'Super/Driver To & From');
                            })
                            ->whereIn('event_schedule_employees.employee_id',$request['supervisor'])
                            ->get();
            foreach($events_res1 as $row)
            {
               $event_arr[]=$row->id; 
            }
            $events = $events->whereIn('events.id',$event_arr);
        }
        
        $events = $events->orderBy('areas.area_number','asc')
                ->orderBy('events.run_number','asc')
                ->orderBy('events.start_time','asc')
//                ->orderBy(function($events) {
//                    return Carbon::createFromFormat('g:i a', $events->start_time)->format('H:i:s');
//               })
                //->orderBy('events.start_time','asc')
                ->get();
        //dd(DB::getQueryLog());
//        \App\Shirt::with('size')
//    ->select(‘shirts.*', \DB::raw('(SELECT sort FROM sizes WHERE shirts.size_id = sizes.id ) as sort'))
//    ->orderBy('sort')
//    ->get(); 
        $key=0;
        $event[] = array();
        foreach ($events as $key => $value) {
            if(strlen($value->area_number)==1)
                $area_no = '0'.$value->area_number;
            else
                $area_no = $value->area_number;
            
            if(strlen($value->run_number)==1)
                $run_number = '0'.$value->run_number;
            else
                $run_number = $value->run_number;
            
            //$event[$key]['id'] = $value->id;
            
            $event[$key]['id'] = $value->id;
            //if($request['client_id'] && in_array(343,$request['client_id']))
            if($value->client_id==343)
                $event[$key]['title'] = $value->area_number.' '.$value->title.' '.$value->city.'-'.$value->state_code.'  '.substr($value->comments,0,45);
            else
                $event[$key]['title'] = $value->area_number.' '.$value->title.' '.$value->city.'-'.$value->state_code.' -M '.date('h:i A',strtotime($value->meet_time)).' S '.date('h:i A',strtotime($value->start_time));
            //$event[$key]['title'] = $value->area_number.'-'.$value->run_number.'-'.$value->start_time.'-'.$value->title.' '.$value->city.'-'.$value->state_code;
            //$event[$key]['title'] = $key;

            if($request->user()->client_id) $event[$key]['title'] = $value->title.' '.$value->city.'-'.$value->state_code.' 
            '.date('h:i A',strtotime($value->start_time));
          
            $event[$key]['start'] = $value->date;
            $event[$key]['end'] = $value->date.' +1 day';
            $event[$key]['run_number'] = $run_number;
            $event[$key]['start_time'] = $value->start_time;
            $event[$key]['area_number'] = $area_no;
            if($value->status=="Pending" && !$value->is_employee_assigned)
                $event[$key]['color'] = '#0088D8';
            //$event[$key]['eventsequence'] = $key+1;
        }
        $searchQuery = " ";
        $filter_by_blackout = 0;
        if($request['blackout_client_id'] != ''){
            $clients = implode(',',$request['blackout_client_id']);
            $searchQuery .= " and (client_blackout_dates.client_id in(".$clients.")) ";
            $filter_by_blackout=1;
        }
        if($request['blackout_division_id'] != ''){
            $divisions = implode(',',$request['blackout_division_id']);
            $searchQuery .= " and (client_blackout_dates.division_id in(".$divisions.")) ";
            $filter_by_blackout=1;
        }
        if($request['blackout_district_id'] != ''){
            $districts = implode(',',$request['blackout_district_id']);
            $searchQuery .= " and (client_blackout_dates.district_id in(".$districts.")) ";
            $filter_by_blackout=1;
        }        
        if($request['blackout_store_id'] != ''){
            $stores = implode(',',$request['blackout_store_id']);
            $searchQuery .= " and (client_blackout_dates.store_id in(".$stores.")) ";
            $filter_by_blackout=1;
        }
        if($filter_by_blackout)
        {
            $blackoutdates = DB::select( DB::raw("select client_blackout_dates.*,clients.name as client,"
                . "divisions.name as division,districts.number as district,stores.name as store "
                . " from client_blackout_dates "
                . "left join clients on clients.id=client_blackout_dates.client_id "
                . "left join divisions on divisions.id=client_blackout_dates.division_id "
                . "left join districts on districts.id=client_blackout_dates.district_id "
                . "left join stores on stores.id=client_blackout_dates.store_id "
                . "WHERE 1=1  ".$searchQuery) );
            foreach($blackoutdates as $blackoutdate)
            {
                $title= strtoupper(($blackoutdate->description)?$blackoutdate->description:'Blackout Day');
                $key++;
                $event[$key]['title']=$title;
                $event[$key]['start'] = $blackoutdate->date;
                $event[$key]['end'] = $blackoutdate->date.' +1 day';
                $event[$key]['start_time'] = '00:01';
                $event[$key]['area_number'] = 0;
                $event[$key]['color'] = '#dce21f';
                $event[$key]['textColor'] = '#000000';
            }
        }
        
        $holidays = getHolidayList();
        foreach($holidays as $date=>$holiday){
            $key++;
            $event[$key]['title']=$holiday;
            $event[$key]['start'] = $date;
            $event[$key]['end'] = $date.' +1 day';
            $event[$key]['start_time'] = '00:01';
            $event[$key]['area_number'] = 0;
            $event[$key]['color'] = '#ed0b0b';
            
        }
        
        return Response::json($event);
    }
    
    public function add_calendar_event(Request $request) {
        if (! Gate::allows('event_create')) {
            return abort(401);
        }
        $request->merge(['date' => date("Y-m-d", strtotime($request->event_date))]);
        //echo '<pre>';print_r($request->all());die;
        
        $request->merge(['start_time' => date("H:i:s", strtotime($request->start_time))]);
        
        $event = Event::create($request->all());
        $this->generateEventNumber($event->id);
        if(count($request->areas))
        {
            foreach($request->areas as $area)
            {
                if($area)
                {
                    EventAreas::create([
                        'event_id' => $event->id,
                        'area_id' => $area,
                    ]);
                }
            }
        }
       
        
        $events = DB::table('events')
                ->where('events.id', '=', $event->id)
                ->select('stores.name as title','events.*')
                ->leftJoin('stores','stores.id','=','events.store_id')
                ->first();
        //echo '<pre>';print_r($events);
        $data[] = array();
        $data['id'] = $events->id;
        $data['title'] = $events->title;
        $data['start'] = new \DateTime($events->date);
        $data['end'] = new \DateTime($events->date.' +1 day');
        //echo '<pre>';print_r($data);die;
        return Response::json($data);
    }
    
    public function edit_event(Request $request) {
        //echo '<pre>';print_r($request->all());die;
        $request->merge(['date' => date("Y-m-d", strtotime($request['start']))]);
        $event = Event::findOrFail($request->id);
        $resetEventStatus=0;
        if($event->date!=$request['date'])
        {
            $request->merge(['precall_manager'=>NULL,'precall_comments'=>NULL,'precall_completed_by'=>NULL,'precall_completed_on'=>NULL]);
            $resetEventStatus=1;
        }

        if($resetEventStatus)
        {
            EventSchedules::where('event_id', '=',$request->id)->delete();
            EventScheduleEmployees::where('event_id', '=',$request->id)->delete();
            $request->merge(['status' => 'Pending']);
        //            $where1 = array('id' => $id);
        //            $updateArr1 = ['status' =>'Pending' ];
        //            $event  = Event::where($where1)->update($updateArr1);
        }
        //echo '<pre>';print_r($request->all());die;
        $event->update($request->all());
//        $where = array('id' => $request->id);
//        $updateArr = ['date' => $request->start];
//        $event  = Event::where($where)->update($updateArr);
 
        return Response::json($event);
    }
    
    public function delete_event(Request $request) {
        $event = Event::where('id',$request->id)->delete();
        return Response::json($event);
    }
    
    //creation date: October 25, 2021
    //method for listing deleted events
    public function getDeleted(Request $request){
        $request->request->add(['isDeleted'=> true]);
        return $this->getEventListing($request);
    }

    public function get_event_list_by_page(Request $request) {

        // ini_set('max_execution_time', 1200);
        //print_r($area_event);die;
        $draw = $request['draw'];
        $row = $request['start'];
        $rowperpage = $request['length']; // Rows display per page
        //echo $rowperpage;
        if($rowperpage==-1)
            $limit='';
        else{
            if(intval($rowperpage) && $row>=0)
                $limit = " limit ".$row.",".$rowperpage;
            else
                $limit = ' limit 0,25';
        }
        $columnIndex = $request['order'][0]['column']; // Column index
        if($columnIndex==0)
        {
            $columnName = 'events.date asc,areas.area_number asc,events.run_number asc,events.start_time asc';
        }else
        {
            if($request['columns'][$columnIndex]['data']=='area')
                $columnName = 'events.start_time'.' '.$request['order'][0]['dir']; // Column name
            else
                //$columnName = $request['columns'][$columnIndex]['data']; // Column name
                $columnName = 'events.date'.' '.$request['order'][0]['dir'];
        }
        //$columnSortOrder = $request['order'][0]['dir']; // asc or desc
        $searchValue = $request['search']['value']; // Search value

        ## Custom Field value
        
        //print_r($request->all());die;

        ## Search 
        $searchQuery = " ";
        
        if($request['date_between'] != ''){
            $request->session()->put('date_between',$request['date_between']);
//            $date_between = explode(' - ',$request['date_between']);
//            $date_from = date('Y-m-d', strtotime($date_between[0]));
//            $date_to = date('Y-m-d', strtotime($date_between[1]));
            
            $date_between = explode(' - ',$request['date_between']);
            $date_from = date('Y-m-d', strtotime($date_between[0]));
            $date_to = date('Y-m-d', strtotime($date_between[1]));
            
            //$store_id = implode(',',$request['store_id']);
            $searchQuery .= " and (events.date between '".$date_from."' and '".$date_to."') ";
        }else{
            $request->session()->forget('date_between');
        }
        if (Gate::allows('isArea') || Gate::allows('isTeam') || Gate::allows('isDistrict')) {
            $areas = DB::table('area_user')->where('user_id','=',Auth::id())->get();
            $user_area = array();
            foreach($areas as $area)
                $user_area[]=$area->area_id;
            $event_area = EventAreas::whereIn('area_id', $user_area)->get();
            $area_event = array();
            foreach($event_area as $area)
                $area_event[]=$area->event_id;
            
            $searchQuery .= " and (events.id in (".implode(',',$area_event).")) ";
        }
        if($request['association_id'] != ''){
            $request->session()->put('association_id',$request['association_id']);
            $association_id = implode(',',$request['association_id']);
            $stores = DB::select( DB::raw("select id from stores WHERE association_id in(".$association_id.")") );
            $store_id=array();
            if($stores)
            {
                foreach($stores as $store)
                {
                   $store_id[]=$store->id; 
                }
                $store_id = implode(',',$store_id);
                $searchQuery .= " and (events.store_id in(".$store_id.")) ";
            }
        }else{
            $request->session()->forget('association_id');
        }
        if($request['client_id'] != ''){
            $request->session()->put('client_id',$request['client_id']);
            $client_id = implode(',',$request['client_id']);
            $stores = DB::select( DB::raw("select id from stores WHERE client_id in(".$client_id.")") );
            $store_id=array();
            if($stores)
            {
                foreach($stores as $store)
                {
                   $store_id[]=$store->id; 
                }
                $store_id = implode(',',$store_id);
                $searchQuery .= " and (events.store_id in(".$store_id.")) ";
            }
        }else{
            $request->session()->forget('client_id');
        }
        if($request['division_id'] != ''){
            $request->session()->put('division_id',$request['division_id']);
            $division_id = implode(',',$request['division_id']);
            $stores = DB::select( DB::raw("select id from stores WHERE division_id in(".$division_id.")") );
            $store_id=array();
            if($stores)
            {
                foreach($stores as $store)
                {
                   $store_id[]=$store->id; 
                }
                $store_id = implode(',',$store_id);
                $searchQuery .= " and (events.store_id in(".$store_id.")) ";
            }
        }else{
            $request->session()->forget('division_id');
        }
        if($request['district_id'] != ''){
            $request->session()->put('district_id',$request['district_id']);
            $district_id = implode(',',$request['district_id']);
            $stores = DB::select( DB::raw("select id from stores WHERE district_id in(".$district_id.")") );
            $store_id=array();
            if($stores)
            {
                foreach($stores as $store)
                {
                   $store_id[]=$store->id; 
                }
                $store_id = implode(',',$store_id);
                $searchQuery .= " and (events.store_id in(".$store_id.")) ";
            }
        }else{
            $request->session()->forget('district_id');
        }
        //echo '<pre>';print_r($store_id);die;
        if($request['store_id'] != ''){
            $request->session()->put('store_id',$request['store_id']);
            $store_id = implode(',',$request['store_id']);
            $searchQuery .= " and (events.store_id in(".$store_id.")) ";
        }else{
            $request->session()->forget('store_id');
        }
        if($request['area_id'] != ''){
            $request->session()->put('area_id',$request['area_id']);
            $area_id = implode(',',$request['area_id']);
            $areas = DB::select( DB::raw("SELECT event_id from event_areas where area_id in(".$area_id.")") );
            $sel_area = array();
            if($areas)
            {
                foreach($areas as $area)
                    $sel_area[] = $area->event_id;
                $searchQuery .= " and (events.id in(".implode(',',$sel_area).")) ";
            }
        }else{
            $request->session()->forget('area_id');
        }
        if($searchValue != ''){
           $searchQuery .= " and (stores.name like '%".$searchValue."%' or "
                . "events.number like '%".$searchValue."%' or "
                . "employees.name like'%".$searchValue."%' or "
                . "events.status like '%".$searchValue."%') ";
        }
        if($request['exclude_pic'] == 1){
            $request->session()->put('exclude_pic',$request['exclude_pic']);
            $searchQuery .= " and (events.precall_completed_by IS NULL or events.precall_completed_by='') and stores.precall='Ýes' ";
        }else{
            $request->session()->forget('exclude_pic');
        }
        if($request['exclude_qc'] == 1){
            $request->session()->put('exclude_qc',$request['exclude_qc']);
            $searchQuery .= " and (events.qc_completed_by IS NULL or events.qc_completed_by='') and stores.qccall='Ýes' ";
        }else{
            $request->session()->forget('exclude_qc');
        }
        if($request['exclude_scheduled'] == 1){
            $request->session()->put('exclude_scheduled',$request['exclude_scheduled']);
            $searchQuery .= " and events.status='Pending' ";
        }else{
            $request->session()->forget('exclude_scheduled');
        }
        $withoutDeleted = " date>='".date('Y-m-d')."' and events.deleted_at IS NULL ";
        $inActive = " and events.status!='inactive' ";
        //echo $searchQuery;
        if(!empty($request->input('isDeleted'))){
            $withoutDeleted = ' events.deleted_at IS NOT NULL ';
            $inActive = '';
        }

        ## Total number of records without filtering
        $records = DB::select( DB::raw("SELECT count(*) as allcount FROM events where $withoutDeleted $inActive") );
        $totalRecords = $records[0]->allcount;

        ## Total number of records with filtering
        $records = DB::select( DB::raw("SELECT count(*) as allcount FROM events "
                . "left join stores on stores.id=events.store_id left join employees on employees.id=events.crew_leader "
                . "where $withoutDeleted $inActive ".$searchQuery) );
        $totalRecordwithFilter = $records[0]->allcount;
        
        
        
        // DB::enableQueryLog();
        ## Fetch records
        $events = DB::select( DB::raw("select DISTINCT events.id as custom_event_id, events.*,stores.number as storeid,stores.qccall,stores.precall,"
                . "cities.name as cityname,states.state_code as statename, checkIfAllAreasAssignedEmployees(events.id) as evt_status,"
                . "stores.name as store,employees.name as `lead`,stores.client_id,areas.area_number from events "
                . "left join stores on stores.id=events.store_id "
                . "left join cities on cities.id=stores.city_id "
                . "left join event_areas on event_areas.event_id=events.id "
                . "left join areas on event_areas.area_id=areas.id "
                . "left join states on states.id=stores.state_id "
                . "left join employees on employees.id=events.crew_leader "
                . "WHERE $withoutDeleted $inActive ".$searchQuery." order by ".$columnName.$limit) );
        
        //  dd(DB::getQueryLog());
                                            
        $data = array();
        foreach($events as $row) {
            //print_r($row);
            $history_data = DB::table('timesheet_header')
                ->select('dEmpCount')
                ->where('timesheet_header.store_id','=',$row->store_id)
                ->where('timesheet_header.dtJobDate','<',$row->date)
                 ->orderBy('timesheet_header.dtJobDate','desc')->limit(1)
                ->first();
            $action_buttons = '';
            if (empty(request()->input('isDeleted')) && Gate::allows('event_view')) {
                $action_buttons.=' <a href="'.route('admin.events.show',[$row->id]).'" style="margin-right:1px;" title="View Event Detail" class="btn btn-xs btn-primary pull-left"><i class="fa fa-eye"></i></a>';
            }
            if (empty(request()->input('isDeleted')) && Gate::allows('event_edit')) {
                $action_buttons.=' <a href="'.route('admin.events.edit',[$row->id]).'" style="margin-right:1px;" title="Edit Event" class="btn btn-xs btn-info pull-left"><i class="fa fa-edit"></i></a>';
            }
            
            if (empty(request()->input('isDeleted')) && Gate::allows('event_mass_delete')){
                $action_buttons.=' <form method="POST" action="'.route('admin.events.destroy',[$row->id]).'" accept-charset="UTF-8" style="display: inline-block;margin-top:-1px;margin-right:1px;" onsubmit="return confirm(\'Are you sure?\');" class="pull-left">
                    <input name="_method" type="hidden" value="DELETE">
                    <input name="_token" type="hidden" value="'.$request->session()->token().'">
                    <button title="Delete Event" class="btn btn-danger btn-xs" type="submit"><i class="fa fa-trash"></i></button>
                    </form>';
            }else{
                if (!empty(request()->input('isDeleted'))){
                    if(Gate::allows('show_inactive_events')){
                        $action_buttons.=' <form method="POST" action="'.route('admin.events.destroy',[$row->id]).'" accept-charset="UTF-8" style="display: inline-block;margin-top:-1px;margin-right:1px;" onsubmit="return confirm(\'Are you sure?\');" class="pull-left">
                            <input name="_method" type="hidden" value="DELETE">
                            <input name="_recover" type="hidden" value="1">
                            <input name="_token" type="hidden" value="'.$request->session()->token().'">
                            <button title="Recover Event" class="btn btn-success btn-xs" type="submit"><i class="fa fa-undo"></i></button>
                            </form>';
                    }
                }
            }
//            if (Gate::allows('event_edit')) {
//                $action_buttons.=' <a href="#feedbackPopup" title="Submit Feedback" style="margin-right:1px;" data-toggle="modal" data-id="'.$row->id.'" id="event_feedback_call_btn" class="btn btn-xs btn-info pull-left"><i class="fa fa-comments"></i></a>';
//            }
            if (empty(request()->input('isDeleted')) && (Gate::allows('event_qc') || Gate::allows('view_event_qc')) && (($row->qccall=="Yes" && $row->qc=="Yes") || ($row->qc=="Yes"))) {
                if($row->qc_completed_by)
                    $action_buttons.=' <a href="#qcPopup" qc_completed="'.$row->qc_completed_by.'" title="Quality Control" style="margin-right:1px;" data-toggle="modal" data-id="'.$row->id.'" id="" class="btn btn-xs greyedout1 pull-left event_qc_call_btn event_qc_call_btn'.$row->id.'"><i class="fa fa-comments"></i></a>';
                else
                    $action_buttons.=' <a href="#qcPopup" title="Quality Control" style="margin-right:1px;" data-toggle="modal" data-id="'.$row->id.'" id="" class="btn btn-xs btn-info pull-left event_qc_call_btn event_qc_call_btn'.$row->id.'"><i class="fa fa-comments"></i></a>';
            }
            if (empty(request()->input('isDeleted')) && (Gate::allows('event_precall') || Gate::allows('view_event_precall')) && (($row->precall=="Yes" && $row->pic=="Yes") || ($row->pic=="Yes"))) {
                if($row->precall_completed_by)
                    $action_buttons.=' <a href="#precallPopup" precall_completed="'.$row->precall_completed_by.'" title="Pre Call" style="margin-right:1px;" data-toggle="modal" data-id="'.$row->id.'" class="btn btn-xs greyedout1 pull-left event_precall_btn event_precall_btn'.$row->id.'"><i class="fa fa-comments"></i></a>';
                else
                    $action_buttons.=' <a href="#precallPopup" title="Pre Call" style="margin-right:1px;" data-toggle="modal" data-id="'.$row->id.'" class="btn btn-xs btn-info pull-left event_precall_btn event_precall_btn'.$row->id.'"><i class="fa fa-comments"></i></a>';
            }
            if (empty(request()->input('isDeleted')) && Gate::allows('event_create')) {
                $action_buttons.=' <a href="#copyeventPopup" title="Copy this Event" style="margin-right:1px;" data-toggle="modal" data-id="'.$row->id.'" id="event_copy_btn" class="btn btn-xs btn-primary pull-left"><i class="fa fa-copy"></i></a>';
            }
            if (empty(request()->input('isDeleted')) && Gate::allows('schedule_employees_create')) {
                $action_buttons.=' <a href="'.route('admin.events.schedule-event',[$row->id]).'" style="margin-right:1px;" title="Schedule Event" class="btn btn-xs btn-info pull-left"><i class="fa fa-edit"></i></a>';
            }
            
            if (empty(request()->input('isDeleted')) && Gate::allows('schedule_employees_create') && $row->status=="Scheduled") {
                $action_buttons.=' <a href="#copyeventSchedulePopup" title="Copy this Event Schedule" style="margin-right:1px;" data-toggle="modal" data-id="'.$row->id.'" id="event_schedule_copy_btn" class="btn btn-xs btn-primary pull-left"><i class="fa fa-copy"></i></a>';
            }
            if (empty(request()->input('isDeleted')) && Gate::allows('schedule_employees_view')) {  
                $action_buttons.=' <a href="'.route('admin.events.view-schedule-event',[$row->id]).'" style="margin-right:1px;" title="View Event Schedule" class="btn btn-xs btn-primary pull-left"><i class="fa fa-eye"></i></a>';
            }
            if (empty(request()->input('isDeleted')) && Gate::allows('event_upload_mdb')) { 
                $action_buttons.=' <a href="#uploadMdbZip" style="margin-right:1px;" title="Upload .mdb Zip File" data-toggle="modal" data-id="'.$row->id.'" class="btn btn-xs btn-info pull-left event_upload_mdb_btn event_upload_mdb_btn'.$row->id.'"><i class="fa fa-upload"></i></a>';
            }
            $storeid = str_replace(' ','{s00}',$row->storeid);
            $storeid = str_replace("'",'{s00}',$storeid);
            $storeid = str_replace('(','{s09}',$storeid);
            $storeid = str_replace(')','{s10}',$storeid);
            $storeid = str_replace('#','{s13}',$storeid);
            $storeid = str_replace('+','{s21}',$storeid);
            $storeid = str_replace('&','{s12}',$storeid);
            
            $storename = str_replace(' ','{s00}',$row->store);
            $storename = str_replace("'",'{s00}',$storename);
            $storename = str_replace('(','{s09}',$storename);
            $storename = str_replace(')','{s10}',$storename);
            $storename = str_replace('#','{s13}',$storename);
            $storename = str_replace('+','{s21}',$storename);
            $storename = str_replace('&','{s12}',$storename);
            
            $prior_file_download_link='http://www.msi-inv.com/private/scheduleSQL/PriorPopup.asp?StoreName='.$storename.'&StoreID='.$storeid;
            if (empty(request()->input('isDeleted')))
            $action_buttons.=' <a href="'.$prior_file_download_link.'" target="popup" onclick="window.open(\''.$prior_file_download_link.'\',\'popup\',\'width=600,height=600\'); return false;" style="margin-right:1px;" title="Download Prior Files" class="btn btn-xs btn-info pull-left"><i class="fa fa-download"></i></a>';
            
            if($row->client_id==314)
            {
                $store_arr = explode('#',$row->store);
                if(isset($store_arr[1]) && $store_arr[1])
                {
                    $Clientcode = trim($store_arr[1]);
                    $ch = curl_init("http://".env('SLY_SERVER_ADDRESS')."/api/LatestFilePath");
                    $postRequest = array(
                        'ClientCode'   => $Clientcode
                    );
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Accept:application/xml',
                        'Content-Type:application/json'
                    ));
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postRequest));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $curlResponse = curl_exec($ch);
                    $file_data = json_decode($curlResponse,true);
                    if (empty(request()->input('isDeleted')) && isset($file_data['MasterFilePath']) && $file_data['MasterFilePath']!="")
                    {
                        $action_buttons.=' <a href="'.$file_data['MasterFilePath'].'" target="popup" onclick="window.open(\''.$file_data['MasterFilePath'].'\',\'popup\',\'width=600,height=600\'); return false;" style="margin-right:1px;" title="Download Master Files" class="btn btn-xs btn-info pull-left"><i class="fa fa-download"></i></a>';
                    }
                    if (empty(request()->input('isDeleted')) && isset($file_data['OnHandFilePath']) && $file_data['OnHandFilePath']!="")
                    {
                        $action_buttons.=' <a href="'.$file_data['OnHandFilePath'].'" target="popup" onclick="window.open(\''.$file_data['OnHandFilePath'].'\',\'popup\',\'width=600,height=600\'); return false;" style="margin-right:1px;" title="Download On Hand Files" class="btn btn-xs btn-info pull-left"><i class="fa fa-download"></i></a>';
                    }
                }
            }
            
                             

            $areas = DB::select( DB::raw("select areas.title,ea.event_id,ea.area_id from event_areas as ea "
                . "left join areas on areas.id=ea.area_id where ea.event_id=".$row->id) );
            $areaname = '';
            //echo '<pre>';print_r($areas);die;
            //echo $events;die
            foreach($areas as $area)
                $areaname.=$area->title.', ';
            if($areaname)
                $areaname=substr($areaname, 0, -2);
            //added code on october 22, 2021 for mass deletion

            $evtStatus = 'Pending';
            if($row->evt_status == 1){
                $evtStatus = 'Partial';
            }elseif($row->evt_status == 2){
                $evtStatus = 'Scheduled';
            }
            $objectArr = [
                "id"=>$row->id,
                "store"=>$row->store,
                "lead"=>$row->lead,
                "run"=>$row->run_number,
                "area"=>$areaname,
                "state"=>$row->cityname.','.$row->statename,
                "last_inventory_value"=>'$'.number_format(@$history_data->dEmpCount),
                "date"=>date('m/d/Y',strtotime($row->date)),
                "start_time"=>date('h:i A',strtotime($row->start_time)),
                "status"=> $evtStatus,//$row->status,
                "buttons"=>$action_buttons
            ];
            if (empty($request->input('isDeleted')) && Gate::allows('event_mass_delete')) {
                $objectArr["_"] = '<input type="checkbox" class="custom-delete" value="'.$row->id.'" name="custom_delete" />';
            }
            $data[] = $objectArr;
        }

        ## Response
        $response = array(
          "draw" => intval($draw),
          "iTotalRecords" => $totalRecords,
          "iTotalDisplayRecords" => $totalRecordwithFilter,
          "aaData" => $data
        );

        echo json_encode($response);
        //return Response::json($event);
        
    }
    
    public function get_prior_event_list_by_page(Request $request) {
        //DB::enableQueryLog();
        $draw = $request['draw'];
        $row = $request['start'];
        $rowperpage = $request['length']; // Rows display per page
        if($rowperpage<0)
            $limit='';
        elseif(intval($rowperpage) && $row>=0)
            $limit = " limit ".$row.",".$rowperpage;
        else
            $limit = ' limit 0,25';
        
        $columnIndex = $request['order'][0]['column']; // Column index
        if($columnIndex==0)
        {
            $columnName = 'events.date desc,events.run_number asc,events.start_time asc';
        }else
        {
            if($request['columns'][$columnIndex]['data']=='area')
                $columnName = 'events.start_time'.' '.$request['order'][0]['dir']; // Column name
            else
                //$columnName = $request['columns'][$columnIndex]['data']; // Column name
                $columnName = 'events.date'.' '.$request['order'][0]['dir'];
        }
        //$columnName = $request['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $request['order'][0]['dir']; // asc or desc
        $searchValue = $request['search']['value']; // Search value

        ## Custom Field value
        
        //print_r($request->all());die;

        ## Search 
        $searchQuery = " ";
        
        if($request['date_between'] != ''){
            $request->session()->put('date_between',$request['date_between']);
//            $date_between = explode(' - ',$request['date_between']);
//            $date_from = date('Y-m-d', strtotime($date_between[0]));
//            $date_to = date('Y-m-d', strtotime($date_between[1]));
            
            $date_between = explode(' - ',$request['date_between']);
            $date_from = date('Y-m-d', strtotime($date_between[0]));
            $date_to = date('Y-m-d', strtotime($date_between[1]));
            
            //$store_id = implode(',',$request['store_id']);
            $searchQuery .= " and (events.date between '".$date_from."' and '".$date_to."') ";
        }else{
            $request->session()->forget('date_between');
            $searchQuery .= " and (events.date between '".date('Y-m-d',strtotime('-1 year'))."' and '".date('Y-m-d')."') ";
        }
        if (Gate::allows('isArea') || Gate::allows('isTeam') || Gate::allows('isDistrict')) {
            $areas = DB::table('area_user')->where('user_id','=',Auth::id())->get();
            $user_area = array();
            foreach($areas as $area)
                $user_area[]=$area->area_id;
            $event_area = EventAreas::whereIn('area_id', $user_area)->get();
            $area_event = array();
            foreach($event_area as $area)
                $area_event[]=$area->event_id;
            
            $searchQuery .= " and (events.id in (".implode(',',$area_event).")) ";
        }
        if($request['association_id'] != ''){
            $request->session()->put('association_id',$request['association_id']);
            $association_id = implode(',',$request['association_id']);
            $stores = DB::select( DB::raw("select id from stores WHERE association_id in(".$association_id.")") );
            $store_id=array();
            foreach($stores as $store)
            {
               $store_id[]=$store->id; 
            }
            $store_id = implode(',',$store_id);
            $searchQuery .= " and (events.store_id in(".$store_id.")) ";
        }else{
            $request->session()->forget('association_id');
        }
        if($request['client_id'] != ''){
            $request->session()->put('client_id',$request['client_id']);
            $client_id = implode(',',$request['client_id']);
            $stores = DB::select( DB::raw("select id from stores WHERE client_id in(".$client_id.")") );
            $store_id=array();
            foreach($stores as $store)
            {
               $store_id[]=$store->id; 
            }
            $store_id = implode(',',$store_id);
            $searchQuery .= " and (events.store_id in(".$store_id.")) ";
        }else{
            $request->session()->forget('client_id');
        }
        if($request['division_id'] != ''){
            $request->session()->put('division_id',$request['division_id']);
            $division_id = implode(',',$request['division_id']);
            $stores = DB::select( DB::raw("select id from stores WHERE division_id in(".$division_id.")") );
            $store_id=array();
            foreach($stores as $store)
            {
               $store_id[]=$store->id; 
            }
            $store_id = implode(',',$store_id);
            $searchQuery .= " and (events.store_id in(".$store_id.")) ";
        }else{
            $request->session()->forget('division_id');
        }
        if($request['district_id'] != ''){
            $request->session()->put('district_id',$request['district_id']);
            $district_id = implode(',',$request['district_id']);
            $stores = DB::select( DB::raw("select id from stores WHERE district_id in(".$district_id.")") );
            $store_id=array();
            foreach($stores as $store)
            {
               $store_id[]=$store->id; 
            }
            $store_id = implode(',',$store_id);
            $searchQuery .= " and (events.store_id in(".$store_id.")) ";
        }else{
            $request->session()->forget('district_id');
        }
        //echo '<pre>';print_r($store_id);die;
        if($request['store_id'] != ''){
            $request->session()->put('store_id',$request['store_id']);
            $store_id = implode(',',$request['store_id']);
            $searchQuery .= " and (events.store_id in(".$store_id.")) ";
        }else{
            $request->session()->forget('store_id');
        }
        if($request['area_id'] != ''){
            $request->session()->put('area_id',$request['area_id']);
            $area_id = implode(',',$request['area_id']);
            $areas = DB::select( DB::raw("SELECT event_id from event_areas where area_id in(".$area_id.")") );
            $sel_area = array();
            foreach($areas as $area)
                $sel_area[] = $area->event_id;
            $searchQuery .= " and (events.id in(".implode(',',$sel_area).")) ";
        }else{
            $request->session()->forget('area_id');
        }
        if($searchValue != ''){
           $searchQuery .= " and (stores.name like '%".$searchValue."%' or "
                . "events.number like '%".$searchValue."%' or "
                . "employees.name like'%".$searchValue."%' or "
                . "events.status like '%".$searchValue."%') ";
        }
        if($request['exclude_pic'] == 1){
            $request->session()->put('exclude_pic',$request['exclude_pic']);
            $searchQuery .= " and (events.precall_completed_by IS NULL or events.precall_completed_by='') and stores.precall='Ýes' ";
        }else{
            $request->session()->forget('exclude_pic');
        }
        if($request['exclude_qc'] == 1){
            $request->session()->put('exclude_qc',$request['exclude_qc']);
            $searchQuery .= " and (events.qc_completed_by IS NULL or events.qc_completed_by='') and stores.qccall='Ýes' ";
        }else{
            $request->session()->forget('exclude_qc');
        }
        if($request['exclude_scheduled'] == 1){
            $request->session()->put('exclude_scheduled',$request['exclude_scheduled']);
            $searchQuery .= " and events.status='Pending' ";
        }else{
            $request->session()->forget('exclude_scheduled');
        }
        ## Total number of records without filtering
        $records = DB::select( DB::raw("SELECT count(*) as allcount FROM events where date<'".date('Y-m-d')."' ") );
        $totalRecords = $records[0]->allcount;

        ## Total number of records with filtering
        $records = DB::select( DB::raw("SELECT count(*) as allcount FROM events "
                . "left join stores on stores.id=events.store_id left join employees on employees.id=events.crew_leader "
                . "where date<'".date('Y-m-d')."' and events.status!='inactive' ".$searchQuery) );
        $totalRecordwithFilter = $records[0]->allcount;
        
        ## Fetch records
        $events = DB::select( DB::raw("select events.*,timesheet_header.id as timesheet_id,stores.qccall,"
                . "stores.precall,stores.number as storeid,stores.name as store,employees.name as `lead`, checkIfAllAreasAssignedEmployees(events.id) as evt_status,"
                . "cities.name as cityname,states.state_code as statename,stores.client_id from events "
                
                . "left join stores on stores.id=events.store_id "
                . "left join cities on cities.id=stores.city_id "
                . "left join states on states.id=stores.state_id "
                . "left join employees on employees.id=events.crew_leader "
                . "left join timesheet_header on timesheet_header.event_id=events.id "
                . "WHERE  date<'".date('Y-m-d')."' and events.status!='inactive' ".$searchQuery." order by ".$columnName.$limit) );
        
        
        //dd(DB::getQueryLog());die; 
        //print_r($events);
        $data = array();
        foreach($events as $row) {
            $history_data = DB::table('timesheet_header')
                ->select('dEmpCount')
                ->where('timesheet_header.store_id','=',$row->store_id)
                ->where('timesheet_header.dtJobDate','<',$row->date)
                 ->orderBy('timesheet_header.dtJobDate','desc')->limit(1)
                ->first();
            $action_buttons = '';
            if (Gate::allows('event_view')) {
                $action_buttons.=' <a href="'.route('admin.events.show',[$row->id]).'" style="margin-right:1px;" title="View Event Detail" class="btn btn-xs btn-primary pull-left"><i class="fa fa-eye"></i></a>';
            }
            if (Gate::allows('event_edit')) {
                $action_buttons.=' <a href="'.route('admin.events.edit',[$row->id]).'" style="margin-right:1px;" title="Edit Event" class="btn btn-xs btn-info pull-left"><i class="fa fa-edit"></i></a>';
            }
            if (Gate::allows('event_delete')) {
                $action_buttons.=' <form method="POST" action="'.route('admin.events.destroy',[$row->id]).'" accept-charset="UTF-8" style="display: inline-block;margin-top:-1px;margin-right:1px;" onsubmit="return confirm(\'Are you sure?\');" class="pull-left">
                    <input name="_method" type="hidden" value="DELETE">
                    <input name="_token" type="hidden" value="'.$request->session()->token().'">
                    <button title="Delete Event" class="btn btn-danger btn-xs" type="submit"><i class="fa fa-trash"></i></button>
                    </form>';
            }
//            if (Gate::allows('event_edit')) {
//                $action_buttons.=' <a href="#feedbackPopup" title="Submit Feedback" style="margin-right:1px;" data-toggle="modal" data-id="'.$row->id.'" id="event_feedback_call_btn" class="btn btn-xs btn-info pull-left"><i class="fa fa-comments"></i></a>';
//            }
            if ((Gate::allows('event_qc') || Gate::allows('view_event_qc')) && (($row->qccall=="Yes" && $row->qc=="Yes") || ($row->qc=="Yes"))) {
                if($row->qc_completed_by)
                    $action_buttons.=' <a href="#qcPopup" qc_completed="'.$row->qc_completed_by.'" title="Quality Control" style="margin-right:1px;" data-toggle="modal" data-id="'.$row->id.'" id="" class="btn btn-xs greyedout1 pull-left event_qc_call_btn event_qc_call_btn'.$row->id.'"><i class="fa fa-comments"></i></a>';
                else
                    $action_buttons.=' <a href="#qcPopup" title="Quality Control" style="margin-right:1px;" data-toggle="modal" data-id="'.$row->id.'" id="" class="btn btn-xs btn-info pull-left event_qc_call_btn event_qc_call_btn'.$row->id.'"><i class="fa fa-comments"></i></a>';
            }
            if ((Gate::allows('event_precall') || Gate::allows('view_event_precall')) && (($row->precall=="Yes" && $row->pic=="Yes") || ($row->pic=="Yes"))) {
                if($row->precall_completed_by)
                    $action_buttons.=' <a href="#precallPopup" precall_completed="'.$row->precall_completed_by.'" title="Pre Call" style="margin-right:1px;" data-toggle="modal" data-id="'.$row->id.'" class="btn btn-xs greyedout1 pull-left event_precall_btn event_precall_btn'.$row->id.'"><i class="fa fa-comments"></i></a>';
                else
                    $action_buttons.=' <a href="#precallPopup" title="Pre Call" style="margin-right:1px;" data-toggle="modal" data-id="'.$row->id.'" class="btn btn-xs btn-info pull-left event_precall_btn event_precall_btn'.$row->id.'"><i class="fa fa-comments"></i></a>';
            }
//            if (Gate::allows('event_qc') && $row->qccall=="Yes") {
//                if($row->qc_completed_by)
//                    $action_buttons.=' <a href="#qcPopup" qc_completed="'.$row->qc_completed_by.'" title="Quality Control" style="margin-right:1px;" data-toggle="modal" data-id="'.$row->id.'" id="" class="btn btn-xs greyedout1 event_qc_call_btn pull-left event_qc_call_btn'.$row->id.'"><i class="fa fa-comments"></i></a>';
//                else
//                    $action_buttons.=' <a href="#qcPopup" title="Quality Control" style="margin-right:1px;" data-toggle="modal" data-id="'.$row->id.'" id="" class="btn btn-xs btn-info pull-left event_qc_call_btn event_qc_call_btn'.$row->id.'"><i class="fa fa-comments"></i></a>';
//            }
//            
//            if (Gate::allows('event_precall') && $row->precall=="Yes") {
//                if($row->precall_completed_by)
//                    $action_buttons.=' <a href="#precallPopup" precall_completed="'.$row->precall_completed_by.'" title="Pre Call" style="margin-right:1px;" data-toggle="modal" data-id="'.$row->id.'" id="" class="btn btn-xs greyedout1 pull-left event_precall_btn event_precall_btn'.$row->id.'"><i class="fa fa-comments"></i></a>';
//                else
//                    $action_buttons.=' <a href="#precallPopup" title="Pre Call" style="margin-right:1px;" data-toggle="modal" data-id="'.$row->id.'" id="" class="btn btn-xs btn-info pull-left event_precall_btn event_precall_btn'.$row->id.'"><i class="fa fa-comments"></i></a>';
//            }
            
            if (Gate::allows('event_create')) {
                $action_buttons.=' <a href="#copyeventPopup" title="Copy this Event" style="margin-right:1px;" data-toggle="modal" data-id="'.$row->id.'" id="event_copy_btn" class="btn btn-xs btn-primary pull-left"><i class="fa fa-copy"></i></a>';
            }
            if (Gate::allows('schedule_employees_create')) {
                $action_buttons.=' <a href="'.route('admin.events.schedule-event',[$row->id]).'" style="margin-right:1px;" title="Schedule Event" class="btn btn-xs btn-info pull-left"><i class="fa fa-edit"></i></a>';
            }
            
            if (Gate::allows('schedule_employees_create') && $row->status=="Scheduled") {
                $action_buttons.=' <a href="#copyeventSchedulePopup" title="Copy this Event Schedule" style="margin-right:1px;" data-toggle="modal" data-id="'.$row->id.'" id="event_schedule_copy_btn" class="btn btn-xs btn-primary pull-left"><i class="fa fa-copy"></i></a>';
            }
            
            if (Gate::allows('schedule_employees_view')) {
                $action_buttons.=' <a href="'.route('admin.events.view-schedule-event',[$row->id]).'" style="margin-right:1px;" title="View Event Schedule" class="btn btn-xs btn-primary pull-left"><i class="fa fa-eye"></i></a>';
            }
            if (Gate::allows('event_upload_mdb')) { 
                $action_buttons.=' <a href="'.route('admin.events.upload_timesheet_mdb',[$row->id]).'" target="_blank" style="margin-right:1px;" title="Upload .mdb Zip File" class="btn btn-xs btn-info pull-left"><i class="fa fa-upload"></i></a>';
            }
            $storeid = str_replace(' ','{s00}',$row->storeid);
            $storeid = str_replace("'",'{s00}',$storeid);
            $storeid = str_replace('(','{s09}',$storeid);
            $storeid = str_replace(')','{s10}',$storeid);
            $storeid = str_replace('#','{s13}',$storeid);
            $storeid = str_replace('+','{s21}',$storeid);
            //$storeid = str_replace('&','%7Bs00%7D',$storeid);
            
            $storename = str_replace(' ','{s00}',$row->store);
            $storename = str_replace("'",'{s00}',$storename);
            $storename = str_replace('(','{s09}',$storename);
            $storename = str_replace(')','{s10}',$storename);
            $storename = str_replace('#','{s13}',$storename);
            $storename = str_replace('+','{s21}',$storename);
            $prior_file_download_link='http://www.msi-inv.com/private/scheduleSQL/PriorPopup.asp?StoreName='.$storename.'&StoreID='.$storeid;
            $action_buttons.=' <a href="'.$prior_file_download_link.'" target="popup" onclick="window.open(\''.$prior_file_download_link.'\',\'popup\',\'width=600,height=600\'); return false;" style="margin-right:1px;" title="Download Prior Files" class="btn btn-xs btn-info pull-left"><i class="fa fa-download"></i></a>';
            if($row->client_id==314)
            {
                $store_arr = explode('#',$row->store);
                if(isset($store_arr[1]) && $store_arr[1])
                {
                    $Clientcode = trim($store_arr[1]);
                    $ch = curl_init("http://".env('SLY_SERVER_ADDRESS')."/api/LatestFilePath");
                    $postRequest = array(
                        'ClientCode'   => $Clientcode
                    );
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Accept:application/xml',
                        'Content-Type:application/json'
                    ));
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postRequest));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $curlResponse = curl_exec($ch);
                    $file_data = json_decode($curlResponse,true);
                    if(isset($file_data['MasterFilePath']) && $file_data['MasterFilePath']!="")
                    {
                        $action_buttons.=' <a href="'.$file_data['MasterFilePath'].'" target="popup" onclick="window.open(\''.$file_data['MasterFilePath'].'\',\'popup\',\'width=600,height=600\'); return false;" style="margin-right:1px;" title="Download Master Files" class="btn btn-xs btn-info pull-left"><i class="fa fa-download"></i></a>';
                    }
                    if(isset($file_data['OnHandFilePath']) && $file_data['OnHandFilePath']!="")
                    {
                        $action_buttons.=' <a href="'.$file_data['OnHandFilePath'].'" target="popup" onclick="window.open(\''.$file_data['OnHandFilePath'].'\',\'popup\',\'width=600,height=600\'); return false;" style="margin-right:1px;" title="Download On Hand Files" class="btn btn-xs btn-info pull-left"><i class="fa fa-download"></i></a>';
                    }
                }
            }
            if($row->timesheet_id)
               $action_buttons.=' <a href="'.route('admin.timesheets.show',[$row->timesheet_id]).'" target="_blank" style="margin-right:1px;" title="View Event Timesheet" class="btn btn-xs btn-info pull-left"><i class="fa fa-eye"></i></a>';
            $areas = DB::select( DB::raw("select areas.title,ea.event_id,ea.area_id from event_areas as ea "
                . "left join areas on areas.id=ea.area_id where ea.event_id=".$row->id) );
            $areaname = '';
            //echo '<pre>';print_r($areas);die;
            //echo $events;die
            foreach($areas as $area)
                $areaname.=$area->title.', ';
            if($areaname)
                $areaname=substr($areaname, 0, -2);
                
            $evtStatus = 'Pending';
            if($row->evt_status == 1){
                $evtStatus = 'Partial';
            }elseif($row->evt_status == 2){
                $evtStatus = 'Scheduled';
            }

            if($request->user()->client_id) $action_buttons ='<a href="'.url('event-reports', [$row->id]).'"  class="btn btn-xs btn-info"><i class="fa fa-download"></i></a>';

            $data[] = array(
            "id"=>$row->id,
            "store"=>$row->store,
            "lead"=>$row->lead,
            "run"=>$row->run_number,
            "last_inventory_value"=>'$'.number_format(@$history_data->dEmpCount),
            "area"=>$areaname,
            "state"=>$row->cityname.','.$row->statename,
            "date"=>date('m/d/Y',strtotime($row->date)),
            "start_time"=>date('h:i A',strtotime($row->start_time)),
            "status"=> $evtStatus,//$row->status,
            "buttons"=>$action_buttons,
           );
        }

        ## Response
        $response = array(
          "draw" => intval($draw),
          "iTotalRecords" => $totalRecords,
          "iTotalDisplayRecords" => $totalRecordwithFilter,
          "aaData" => $data
        );

        echo json_encode($response);
        //return Response::json($event);
        
    }
    
    public function showstoreevents($store_id)
    {
        if (! Gate::allows('event_view')) {
            return abort(401);
        }
        
        if(request()->ajax()) 
        {
            $start = (!empty($_GET["start"])) ? ($_GET["start"]) : ('');
            $end = (!empty($_GET["end"])) ? ($_GET["end"]) : ('');
            $events = DB::table('events')
                    ->whereDate('events.date', '>=', $start)->whereDate('events.date',   '<=', $end)
                    ->where('events.store_id','=',$store_id)
                    ->select('stores.name as title','events.*')
                    ->leftJoin('stores','stores.id','=','events.store_id')
                    ->get();
            $event[] = array();
            foreach ($events as $key => $value) {
                $event[$key]['id'] = $value->id;
                $event[$key]['title'] = $value->title;
                $event[$key]['start'] = $value->date.' '.date('H:i:s',strtotime($value->start_time));
                $event[$key]['end'] =$value->date.' +1 day';
                $event[$key]['allDay'] = 'false';
                if($value->status=="Pending")
                    $event[$key]['color'] = '#ADD8E6';
            }
            return Response::json($event);
        }
        
        
        $store = DB::table('stores')->where('id','=',$store_id)->first();
        $employees = DB::table('employees')->where('status','=','Active')->where('is_crew_leader',1)->pluck('name','id');
        $areas = DB::table('areas')->where('status','=', 'active')->pluck('title','id'); 
        return view('admin.events.store-events-calender', compact('store','employees','areas'));
        
        
    }
    
    public function getEventDetailsByID(Request $request) {
        $id = $request->event_id;
        
        $event = Event::with(array('store','areas','crew_leader_name','truck_dates', 'timesheet','schedule_employees'))->findOrFail($id);
        $nextEvent = Event::where('store_id', $event->store_id)->where('date', '>', $event->date)->orderBy('date', 'asc')->first();

        $historical_data = Event::with(array('truck_dates'))
                ->where('store_id','=',$event->store_id)
                ->where('id','<',$id)
                ->orderBy('id','desc')->limit(1)->first();
        $areas = DB::table('areas')->where('status','=', 'active')->pluck('title','id'); 
        $supervisor = DB::table('event_schedule_employees')
                ->select('employees.last_name','employees.first_name')
                ->leftJoin('employees','employees.id','=','event_schedule_employees.employee_id')
                ->where(function($q) {
                    $q->where('event_schedule_employees.task', '=', 'Supervisor')
                    ->orWhere('event_schedule_employees.task','=', 'Super/Driver To')
                    ->orWhere('event_schedule_employees.task','=', 'Super/Driver From')
                    ->orWhere('event_schedule_employees.task','=', 'Super/Driver To & From');
                })
                //->where('event_schedule_employees.task','=','Supervisor')
                ->where('event_schedule_employees.event_id','=',$id)
                ->first();
        $schedule_data = DB::table('event_schedules')
                ->select('schedule_length','scheduled_production','field_notes')
                ->where('event_schedules.event_id','=',$id)
                ->first();
         $crew_count = DB::table('event_schedule_employees')
                ->where('event_schedule_employees.event_id','=',$id)
                ->count();
         $history_data = DB::table('timesheet_header')
                ->where('timesheet_header.store_id','=',$event->store->id)
                ->where('timesheet_header.dtJobDate','<',$event->date)
                 ->orderBy('timesheet_header.dtJobDate','desc')->limit(1)
                ->first();
         $employees = DB::table('employees')->where('status','=','Active')->where('is_crew_leader',1)->orderBy('last_name','asc')->pluck('name','id');
         if($history_data)
         {
            $last_crew_count = DB::table('timesheet_data')
                ->where('timesheet_data.timesheet_id','=',$history_data->id)
                 ->where('timesheet_data.iAttendanceFlag','!=',3)
                 ->where('timesheet_data.iAttendanceFlag','!=',4)
                ->count();
         }else
             $last_crew_count=0;
        //echo '<pre>';print_r($history_data);die;
        if (Gate::allows('event_edit')) {
            $html = '<form method="POST" id="edit_event_form" name="edit_event_form" action="#">';
        }else{
            $html = '';
        }
        
        $html.='<div class="feedback-response col-md-12 alert alert-success" style="display: none;"></div>
            <input type="hidden" name="id" class="edit_event_id_form" value="'.$id.'"> 
<div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-3" style="height:75px;">
                    <label for="date" class="control-label">Date</label><br>';
                if (Gate::allows('event_edit'))
                    $html.='<input class="form-control datepicker" required="" name="date" type="text" value="'.date('m/d/Y',strtotime($event->date)).'" id="date">';
                else
                    $html.=$event->date;
                $html.='</div>
                <div class="col-xs-3 form-group">
                    <label for="start_time" class="control-label">Start Time</label><br>';
                if (Gate::allows('event_edit'))
                    $html.='<input class="form-control timepicker" required="" placeholder="" name="start_time" type="text" value="'.date('h:i A',strtotime($event->start_time)).'" id="start_time">';
                else
                    $html.=date('h:i A',strtotime($event->start_time));
                $html.='</div>
                <div class="col-xs-3 form-group">
                    <label for="store_id" class="control-label">Store</label><br>'.@$event->store->name.'
                </div>
                <div class="col-xs-3 form-group">
                    <label for="store_id" class="control-label">Store Manager</label><br>'.@$event->store->manager_id.'
                </div>
            </div>
            <div class="row">
                <div class="col-xs-3 form-group">
                    <label for="store_id" class="control-label">Store Phone</label><br>';
            $agent = new \Jenssegers\Agent\Agent;
            $result = $agent->isMobile();//isDesktop,isTablet
            if($result)
                $html.='<a href="tel:'.@$event->store->phone.'">'.@$event->store->phone.'</a>';
            else
                $html.=@$event->store->phone;
                
                $html.='</div>
                <div class="col-xs-6 form-group">
                    <label for="store_id" class="control-label">City,State</label><br>
                    <a target="_blank" href="https://www.google.com/maps/dir/?api=1&destination='.@$event->store->address.'+'.@$event->store->city->name.'+'.@$event->store->state->state_code.'">
                    '.@$event->store->address.', '.@$event->store->city->name.' '.@$event->store->state->state_code.'</a>
                </div>
                <div class="col-xs-3 form-group">
                    <label for="store_id" class="control-label">ALR Disk</label><br>'.@$event->store->alr_disk.'
                </div>
            </div>
            <div class="row">
                
            
                <div class="col-xs-3 form-group">
                    <label for="areas" class="control-label">Area</label><br>
                    <select id="areas" class="form-control select2" multiple="multiple" required="" name="areas[]">
                        <option value="">Select Area</option>';
                        $selected_area = array();foreach($event->areas as $area){$selected_area[] = $area->area->id;}
                        foreach ($areas as $key=>$area){
                        $html.='<option value="'.$key.'"';
                        if(in_array($key,$selected_area)){
                            $html.=' selected="selected"';}
                        $html.='>'.$area.'</option>';
                        }
                    $html.='</select>';
                    
                  
               $html.='</div>
                <div class="col-xs-3 form-group">
                    <label for="crew_leader" class="control-label">Crew Leader</label><br>';
                if (Gate::allows('event_edit'))
                {
                    $html.='<select id="crew_leader" class="form-control select2" name="crew_leader" >
                        <option value="">Select Crew Leader</option>';
                    foreach($employees as $key=>$employee){
                            $html.='<option value="'.$key.'"';
                            if($key==$event->crew_leader){
                                $html.='selected="selected"';
                            }
                            $html.='>'.$employee.'</option>';
                        }
                    $html.='</select>';
                }else{
                    $html.=@$event->crew_leader_name->name;
                }
                $html.='</div>
                <div class="col-xs-3 form-group">
                    <label for="crew_count" class="control-label">Crew Count</label><br>';
                if (Gate::allows('event_edit'))
                    $html.='<input class="form-control" placeholder="" name="crew_count" type="text" value="'.$event->crew_count.'" id="crew_count">';
                else
                    $html.= $event->crew_count;
                $html.='</div>
                <div class="col-xs-3 form-group">
                    <label for="district_name" class="control-label">District Name</label><br>'.@$event->store->district->number.'
                </div>
            </div>
            <div class="row">
                <div class="col-xs-3 form-group">
                    <label class="control-label">Overnight</label><br>';
                    if (Gate::allows('event_edit'))
                    {
                        $html.='<label class="control-label">                   
                            <input name="overnight" type="radio" value="Yes"';
                        if($event->overnight=="Yes")
                             $html.=' checked="checked"';
                        $html.='> Yes
                        </label>
                        <label class="control-label">                   
                            <input name="overnight" type="radio" value="No"';
                        if($event->overnight=="No")
                             $html.=' checked="checked"';
                        $html.='> No
                        </label>';
                    }else{
                        $html.=$event->overnight;
                    }
                    $html.='</div>
            
                <div class="col-xs-3 form-group">
                    <label for="road_trip" class="control-label">Road Trip</label><br>';
                    if (Gate::allows('event_edit'))
                    {
                        $html.='<select id="road_trip" class="form-control client_id" name="road_trip">
                            <option value="">Select Road Trip</option>
                            <option value="Start Road Trip"';
                            if($event->road_trip=="Start Road Trip")  $html.= 'selected="selected"';
                            $html.= '>Start Road Trip</option><option value="End Road Trip"';
                            if($event->road_trip=="End Road Trip") $html.= 'selected="selected"';
                            $html.= '>End Road Trip</option><option value="Road Trip"';
                            if($event->road_trip=="Road Trip") $html.= 'selected="selected"';
                            $html.= '>Road Trip</option><option value="No"';
                            if($event->road_trip=="No") $html.= 'selected="selected"';
                            $html.= '>No</option></select>';
                    }else{
                        $html.=$event->road_trip;
                    }
                $html.='</div>
                <div class="col-xs-3 form-group">
                    <label for="rx" class="control-label">Count RX</label><br>';
                    if (Gate::allows('event_edit'))
                    {
                        $html.='<label class="control-label">                   
                            <input name="count_rx" type="radio" value="Yes"';
                        if($event->count_rx=="Yes")
                             $html.=' checked="checked"';
                        $html.='> Yes
                             </label>
                             <label class="control-label">                   
                                 <input name="count_rx" type="radio" value="No"';
                        if($event->count_rx=="No")
                             $html.=' checked="checked"';
                        $html.='> No
                         </label>';
                    }else{
                        $html.=$event->count_rx;
                    }
                $html.='</div>
                <div class="col-xs-3 form-group">
                    <label for="count_backroom" class="control-label">Count Backroom</label><br>';
                    if (Gate::allows('event_edit'))
                    {
                        $html.='<label class="control-label">                   
                            <input name="count_backroom" type="radio" value="Yes"';
                        if($event->count_backroom=="Yes")
                             $html.=' checked="checked"';
                        $html.='> Yes
                             </label>
                             <label class="control-label">                   
                                 <input name="count_backroom" type="radio" value="No"';
                        if($event->count_backroom=="No")
                             $html.=' checked="checked"';
                        $html.='> No
                         </label>';
                    }else{
                        $html.=$event->count_backroom;
                    }
                $html.='</div>
                <div class="col-xs-3 form-group">
                    <label for="fuel_center" class="control-label">Fuel Center</label><br>'.@$event->store->fuel_center.'
                </div>';
               if(count($event->store->schedule_availability_days)){
                    $schedule_availability_days = array();
                    foreach($event->store->schedule_availability_days as $day)
                        $schedule_availability_days[] = $day->days;
                    $schedule_availability_days=implode(', ',$schedule_availability_days);
                }else{
                    $schedule_availability_days = '';
                }
                
            $html.='<div class="col-xs-3 form-group">
                    <label for="areas" class="control-label">Run Number</label><br>';
            if (Gate::allows('event_edit'))
            {
                $html.='<select id="run_number" class="form-control select2" name="run_number">
                    <option value="">Select Run Number</option>';
                    for($i=1;$i<10;$i++){
                    $html.='<option value="'.$i.'"';
                    if($i==$event->run_number){
                        $html.=' selected="selected"';}
                    $html.='>'.$i.'</option>';
                    }
                $html.='</select>';
            }else{
                $html.=$event->run_number;
            }        
                  
               $html.='</div>
                </div>
            <div class="row">
                <div class="col-xs-12 form-group">
                    <label for="days_available_to_schedule" class="control-label">Days Available To Schedule</label><br>'.$schedule_availability_days.'
                </div>
                </div>
            <div class="row"><div class="col-xs-12 form-group text-center"><label>Scheduled Crew</label></div></div>';
//            if($supervisor)
//            {
                
            $html.='<div class="row">
                        <div class="col-xs-3 form-group">
                            <label for="road_trip" class="control-label">Supervisor</label><br>';
            if(@$supervisor->last_name)
                $html.=@$supervisor->last_name.' ';
            if(@$supervisor->first_name)
                $html.=@$supervisor->first_name;
            $html.='    </div>
                        <div class="col-xs-3 form-group">
                            <label for="rx" class="control-label">Crew Count</label><br>'.$crew_count.'
                        </div>
                        <div class="col-xs-3 form-group">
                            <label for="count_backroom" class="control-label">Count Length</label><br>';
            if(@$schedule_data->schedule_length)
                $html.=@$schedule_data->schedule_length;
            $html.='            </div>
                        <div class="col-xs-3 form-group">
                            <label for="fuel_center" class="control-label">Count Production</label><br>';
            if(@$schedule_data->scheduled_production)
                $html.=@$schedule_data->scheduled_production;
            $html.='            </div>
                    </div>';
                
//            }else{
//                $html.='<div class="row"><div class="col-xs-12 form-group text-center">This event is not scheduled yet.</div></div>';
//            }
            $html.='<div class="row">
                        <div class="col-xs-12 form-group">
                            <label for="field_notes" class="control-label">Field Notes</label><br>'.@$schedule_data->field_notes.'
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 form-group">
                            <label for="comments" class="control-label">Schedule Comments</label><br>';
                            if (Gate::allows('event_edit')){
                                $html.='<textarea name="comments" class="form-control">'.@$event->comments.'</textarea>';
                            }else{
                                $html.=@$event->comments;
                            }
                        $html.='</div>
                    </div>
            <div class="row"><div class="col-xs-12 form-group text-center"><label>Historical Data</label></div></div>        
            <div class="row">    
                <div class="col-xs-3 form-group">
                    <label for="last_start_time" class="control-label">Last Date</label><br>'.date('m/d/Y',strtotime(@$history_data->dtJobDate)).'
                </div>
                <div class="col-xs-3 form-group">
                    <label for="last_start_time" class="control-label">Last Start Time</label><br>'.@$historical_data->start_time.'
                </div>
                <div class="col-xs-3 form-group">
                    <label for="last_crew_count" class="control-label">Last Crew Count</label><br>'.$last_crew_count.'
                </div>
                <div class="col-xs-3 form-group">
                    <label for="last_inventory_value" class="control-label">Last Inventory Value</label><br>'.@$history_data->dEmpCount.'
                </div>
            </div>
            <div class="row">
                <div class="col-xs-3 form-group">
                    <label for="last_count_length" class="control-label">Last Count Length</label><br>';
            
            $datetime1 = strtotime(@$history_data->InvRecapWrapTime);
            $datetime2 = strtotime(@$history_data->InvRecapStartTime);
            $diff = abs($datetime1 - $datetime2);  
            $hours = floor(($diff/ (60*60)));
            $minutes = floor(($diff - $hours*60*60)/ 60);   
            
            $html.=$hours.'.'.$minutes.'
                </div>
                <div class="col-xs-3 form-group">
                    <label for="last_count_production" class="control-label">Last DPH</label><br>';
            if(@$history_data->TTLMH)
                $html.=number_format((float)($history_data->dEmpCount/$history_data->TTLMH),2,'.','');
            else
                $html.='No TTLMH available';
                $html.='
                </div>
                <div class="col-xs-3 form-group">
                    <label for="last_count_production" class="control-label">Last PPH</label><br>';
            if(@$history_data->TTLMH)    
                $html.=number_format((float)($history_data->dEmpPieces/$history_data->TTLMH),2,'.','');
            else
                $html.='No TTLMH available';
            $html.='
                </div>
                
            </div>
            
            
            <div class="blackout_dates_container">';
//            if(count($event->truck_dates)){
//                foreach($event->truck_dates as $key=>$dates){
//                    $html.='<div class="col-xs-2 blackout_counter'.$key.'" style="height:75px;">
//                    <label for="blackout_dates" class="control-label">Blackout Dates</label><br>'.$dates->truck_date.'
//                    
//
//         </div>';
//
//                }
//            }           
            $html.='</div>
                
            
            
            
        </div>
        </div>';
        
            $html.='';
            if (Gate::allows('event_edit')){
                $html.='<div class="col-md-4">
                            <input class="btn btn-success save-event-edit_info" id="submit_event_edit_button" type="button" value="Submit">
                            <input class="btn btn-warning cancel-btn" onclick="close_popup()" data-dismiss="modal" type="reset" value="Cancel">
                        </div>
                </form>';
            }
            $action_buttons = '<div class="col-md-8 calendar_action_btn" style="margin-top:5px;padding-left:0;">';
            if (Gate::allows('event_view')) {
                $action_buttons.='<a target="_blank" href="'.route('admin.events.show',[$event->id]).'" style="margin-right:1px;" title="View Event Detail" class="btn btn-xs btn-primary pull-left"><i class="fa fa-eye"></i></a>&nbsp;';
            }
            if (Gate::allows('event_edit')) {
                $action_buttons.='<a target="_blank" href="'.route('admin.events.edit',[$event->id]).'" style="margin-right:1px;" title="Edit Event" class="btn btn-xs btn-info pull-left"><i class="fa fa-edit"></i></a>&nbsp;';
            }
            if (Gate::allows('event_delete')) {
                $action_buttons.=' <form method="POST" action="'.route('admin.events.destroy',[$event->id]).'" accept-charset="UTF-8" style="display: inline-block;margin-top:-1px;margin-right:1px;" onsubmit="return confirm(\'Are you sure?\');" class="pull-left">
                    <input name="_method" type="hidden" value="DELETE">
                    <input name="_token" type="hidden" value="'.$request->session()->token().'">
                    &nbsp;<button title="Delete Event" class="btn btn-danger btn-xs" type="submit"><i class="fa fa-trash"></i></button>
                    </form>';
            }
            //echo $event->qc;die;
            if ((Gate::allows('event_qc') || Gate::allows('view_event_qc')) && (($event->store->qccall=="Yes" && $event->qc=="Yes") || ($event->qc=="Yes"))) {
                if($event->qc_completed_by)
                    $action_buttons.='<a href="#qcPopup" qc_completed="'.$event->qc_completed_by.'" title="Quality Control" style="margin-right:1px;" data-toggle="modal" data-id="'.$event->id.'" id="" class="btn btn-xs greyedout1 pull-left event_qc_call_btn event_qc_call_btn'.$event->id.'"><i class="fa fa-comments"></i></a>';
                else
                    $action_buttons.='<a href="#qcPopup" title="Quality Control" style="margin-right:1px;" data-toggle="modal" data-id="'.$event->id.'" id="" class="btn btn-xs btn-info pull-left event_qc_call_btn event_qc_call_btn'.$event->id.'"><i class="fa fa-comments"></i></a>&nbsp;';
            }
            if ((Gate::allows('event_precall') || Gate::allows('view_event_precall')) && (($event->store->precall=="Yes" && $event->pic=="Yes") || ($event->pic=="Yes"))) {
                if($event->precall_completed_by)
                    $action_buttons.='<a href="#precallPopup" precall_completed="'.$event->precall_completed_by.'" title="Pre Call" style="margin-right:1px;" data-toggle="modal" data-id="'.$event->id.'" class="btn btn-xs greyedout1 pull-left event_precall_btn event_precall_btn'.$event->id.'"><i class="fa fa-comments"></i></a>';
                else
                    $action_buttons.='<a href="#precallPopup" title="Pre Call" style="margin-right:1px;" data-toggle="modal" data-id="'.$event->id.'" class="btn btn-xs btn-info pull-left event_precall_btn event_precall_btn'.$event->id.'"><i class="fa fa-comments"></i></a>';
            }
            if (Gate::allows('event_create')) {
                $action_buttons.='<a href="#copyeventPopup" title="Copy this Event" style="margin-right:1px;" data-toggle="modal" data-id="'.$event->id.'" id="event_copy_btn" class="btn btn-xs btn-primary pull-left"><i class="fa fa-copy"></i></a>';
            }
            if (Gate::allows('schedule_employees_create')) {
                $action_buttons.='<a target="_blank" href="'.route('admin.events.schedule-event',[$event->id]).'" style="margin-right:1px;" title="Schedule Event" class="btn btn-xs btn-info pull-left"><i class="fa fa-edit"></i></a>';
            }
            
            if (Gate::allows('schedule_employees_create') && $event->status=="Scheduled") {
                $action_buttons.='<a href="#copyeventSchedulePopup" title="Copy this Event Schedule" style="margin-right:1px;" data-toggle="modal" data-id="'.$event->id.'" id="event_schedule_copy_btn" class="btn btn-xs btn-primary pull-left"><i class="fa fa-copy"></i></a>';
            }
            if (Gate::allows('schedule_employees_view')) {  
                $action_buttons.='<a target="_blank" href="'.route('admin.events.view-schedule-event',[$event->id]).'" style="margin-right:1px;" title="View Event Schedule" class="btn btn-xs btn-primary pull-left"><i class="fa fa-eye"></i></a>';
            }
            if (Gate::allows('event_upload_mdb')) { 
                $action_buttons.='<a href="#uploadMdbZip" style="margin-right:1px;" title="Upload .mdb Zip File" data-toggle="modal" data-id="'.$event->id.'" class="btn btn-xs btn-info pull-left event_upload_mdb_btn event_upload_mdb_btn'.$event->id.'"><i class="fa fa-upload"></i></a>';
            }
            
            $storeid = str_replace(' ','{s00}',$event->store->number);
            $storeid = str_replace("'",'{s00}',$storeid);
            $storeid = str_replace('(','{s09}',$storeid);
            $storeid = str_replace(')','{s10}',$storeid);
            $storeid = str_replace('#','{s13}',$storeid);
            $storeid = str_replace('+','{s21}',$storeid);
            $storeid = str_replace('&','{s12}',$storeid);
            
            $storename = str_replace(' ','{s00}',$event->store->name);
            $storename = str_replace("'",'{s00}',$storename);
            $storename = str_replace('(','{s09}',$storename);
            $storename = str_replace(')','{s10}',$storename);
            $storename = str_replace('#','{s13}',$storename);
            $storename = str_replace('+','{s21}',$storename);
            $storename = str_replace('&','{s12}',$storename);
            
            $prior_file_download_link='http://www.msi-inv.com/private/scheduleSQL/PriorPopup.asp?StoreName='.$storename.'&StoreID='.$storeid;
            $action_buttons.='<a href="'.$prior_file_download_link.'" target="popup" onclick="window.open(\''.$prior_file_download_link.'\',\'popup\',\'width=600,height=600\'); return false;" style="margin-right:1px;" title="Download Prior Files" class="btn btn-xs btn-info pull-left"><i class="fa fa-download"></i></a>';
            if (Gate::allows('timesheet_view') && !empty($event->timesheet->id)) { 
                $action_buttons.=' <a href="'.route('admin.timesheets.show',[$event->timesheet->id]).'" target="_blank" style="margin-right:1px;" title="View Event Timesheet" class="btn btn-xs btn-info pull-left"><i class="fa fa-eye"></i></a>';
            }
            
            if($event->store->client_id==314)
            {
                $store_arr = explode('#',$event->store->name);
                if(isset($store_arr[1]) && $store_arr[1])
                {
                    $Clientcode = trim($store_arr[1]);
                    $ch = curl_init("http://".env('SLY_SERVER_ADDRESS')."/api/LatestFilePath");
                    $postRequest = array(
                        'ClientCode'   => $Clientcode
                    );
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Accept:application/xml',
                        'Content-Type:application/json'
                    ));
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postRequest));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $curlResponse = curl_exec($ch);
                    $file_data = json_decode($curlResponse,true);
                    if(isset($file_data['MasterFilePath']) && $file_data['MasterFilePath']!="")
                    {
                        $action_buttons.=' &nbsp;<a href="'.$file_data['MasterFilePath'].'" target="popup" onclick="window.open(\''.$file_data['MasterFilePath'].'\',\'popup\',\'width=600,height=600\'); return false;" style="margin-right:1px;" title="Download Master Files" class="btn btn-xs btn-info pull-left"><i class="fa fa-download"></i></a>&nbsp;';
                    }
                    if(isset($file_data['OnHandFilePath']) && $file_data['OnHandFilePath']!="")
                    {
                        $action_buttons.=' &nbsp;<a href="'.$file_data['OnHandFilePath'].'" target="popup" onclick="window.open(\''.$file_data['OnHandFilePath'].'\',\'popup\',\'width=600,height=600\'); return false;" style="margin-right:1px;" title="Download On Hand Files" class="btn btn-xs btn-info pull-left"><i class="fa fa-download"></i></a>&nbsp;';
                    }
                }
            }
            $html.=$action_buttons.'</div>';
        
        
            if($request->is_tooltip) 
            {
                $prior_value = '-';
                $name = @$event->store->name;
                $prior_inventory = @$history_data->dEmpCount;
                if(!empty($prior_inventory)){
                    $prior_value = '$'.number_format($prior_inventory, 0);
                }
                $days_from_last = 'No previous event';
                $days_to_next = 'Not scheduled yet.';
                $countLength = '-';
                $district = '-';
                $available_days = '-';
                $scheduleCtrs ='-';
                if(!empty($event->store) && !empty($event->store->district->number)){
                    $district = $event->store->district->number;
                }

                if(!empty($schedule_data->schedule_length))
                    $countLength = $schedule_data->schedule_length;

                if(!empty($history_data->dtJobDate)){
                    $eventDate = \Carbon\Carbon::parse($event->date);

                    $historyEventDate = \Carbon\Carbon::parse($history_data->dtJobDate);
                    
                    if($historyEventDate->diffInDays($eventDate) > 0){
                        $days_from_last = $historyEventDate->format('d M Y').', '.($historyEventDate->diffInDays($eventDate)).' days ago.';
                    }
                }
                if(!empty($event->date) && !empty($nextEvent)){
                    $eventDate = \Carbon\Carbon::parse($event->date);
                    $nextEventDate = \Carbon\Carbon::parse($nextEvent->date);
                    if($nextEventDate->diffInDays($eventDate) > 0){
                        $days_to_next = $nextEventDate->format('d M Y').', after '.($nextEventDate->diffInDays($eventDate)).' days.';
                    }
                }
               
                if(!empty($crew_count)){
                    $scheduleCtrs = $crew_count;
                }
                if(!empty($schedule_availability_days)){
                    $available_days = $schedule_availability_days;
                }
                // dd([$schedule_data, $event]);
                $html = "<div class='text-left'><b>Store Name: </b>$name<span><br>
                    <b>Prior Value: </b>$prior_value <br>";
                if(!$request->user()->client_id) $html .= "<b>Count Length: </b>$countLength <br><b>Scheduled Ctrs: </b>$scheduleCtrs <br>";
                    $html .= "<b>District: </b>$district <br>
                    <b>Days available: </b>$available_days <br>
                    <b>Prev event: </b>$days_from_last<br> 
                    <b>Next event: </b>$days_to_next<br>

                    </span></div>";
                
            }

        echo $html;
        
    }
    
    public function updateEventDetailsByID(Request $request)
    {
        if (! Gate::allows('event_edit')) {
            return abort(401);
        }
        if(request()->ajax()) 
        {
            $resetEventStatus=0;
            $areas = EventAreas::where('event_id', '=',$request['id'])->pluck('area_id')->toArray();
            foreach($areas as $area)
            {
                if(in_array($area, $request['areas']))
                {
                    continue;
                }else
                {
                    EventAreas::where('event_id', '=',$request['id'])->where('area_id', '=',$area)->delete();
                    $area_employees = Employee::where('area_id', '=',$area)->pluck('id')->toArray();
                    foreach($area_employees as $emps)
                    {
                        EventScheduleEmployees::where('event_id', '=',$request['id'])->where('employee_id', '=',$emps)->delete();
                    }
//                    $resetEventStatus=1;
//                    break;
                }
            }
            //echo '<pre>';print_r($request['date']);die;
            $request->merge(['date' => date("Y-m-d", strtotime($request['date']))]);
            $request->merge(['start_time' => date("H:i:s", strtotime($request->start_time))]);
            $event = Event::findOrFail($request['id']);
            if($event->date!=$request['date'])
            {
                $request->merge(['precall_manager'=>NULL,'precall_comments'=>NULL,'precall_completed_by'=>NULL,'precall_completed_on'=>NULL]);
                $resetEventStatus=1;
            }
            if($resetEventStatus)
            {
                EventSchedules::where('event_id', '=',$request['id'])->delete();
                EventScheduleEmployees::where('event_id', '=',$request['id'])->delete();
                $request->merge(['status' => 'Pending']);
            }
            if($event->start_time!=$request['start_time'])
            {
                $request->merge(['precall_manager'=>NULL,'precall_comments'=>NULL,'precall_completed_by'=>NULL,'precall_completed_on'=>NULL]);
            }
            $event->update($request->all());
            if($request->areas && count($request->areas))
            {
                $old_areas = EventAreas::where('event_id', '=',$event->id)->pluck('area_id')->toArray();
                $new_areas=array();
                foreach($request->areas as $area)
                        $new_areas[]=$area;
                foreach($old_areas as $old_area)
                {
                    if(in_array($old_area, $new_areas))
                        continue;
                    else
                        EventAreas::where('event_id', '=',$event->id)->where('area_id', '=',$old_area)->delete();
                }

                foreach($request->areas as $area)
                {
                    $area_already_exist = EventAreas::where('area_id','=',$area)->where('event_id', '=',$event->id)->get();
                    if($area_already_exist->isEmpty())
                            EventAreas::create(['event_id' => $event->id,'area_id' => $area]);
                    else
                        continue;
                }
//                EventAreas::where('event_id', '=',$request['id'])->delete();
//                foreach($request->areas as $area)
//                {
//                    if($area)
//                    {
//                        EventAreas::create([
//                            'event_id' => $event->id,
//                            'area_id' => $area,
//                        ]);
//                    }
//                }
            }
            return Response::json(array('msg'=>'Event updated successfully.'));
        }
    }
    public function schedule_event(Request $request,$event_id) {
        $event = Event::with(array('store','areas','crew_leader_name','truck_dates','event_schedule_data','schedule_employees'))->findOrFail($event_id);
        $historical_data = Event::with(array('truck_dates'))
                ->where('store_id','=',$event->store_id)
                ->where('date','<',$event->date)
                ->orderBy('date','desc')->limit(1)->first();
        //echo '<pre>';print_r($historical_data);die;
        $event_day = date('l',strtotime($event->date));
        
        $other_scheduled_events = DB::table('events')->where('date','=',$event->date)->where('id','!=',$event->id)->get();
        $other_events = array();
        foreach($other_scheduled_events as $rows)
            $other_events[]=$rows->id;
        
        $already_scheduled_employees = DB::table('event_schedule_employees')->whereIn('event_id',$other_events)->get();
        $already_scheduled_emp_arr = array();
        foreach($already_scheduled_employees as $row)
            $already_scheduled_emp_arr[]=$row->employee_id;
        if(@$event->store->client->name=="MSI Corp")
            $enable_calc=0;
        else
            $enable_calc=1;
        //echo '<pre>';print_r($event->store->client->name);die;
        //DB::enableQueryLog();
        $event_areas = DB::table('event_areas')
                ->where('event_id','=',$event_id)
                ->orderBy('area_id','asc')
                ->get();
        $event_areas_arr = array();
        foreach($event_areas as $event_area)
            $event_areas_arr[]=$event_area->area_id;
        $other_areas = DB::table('areas')
                ->whereNotIn('id',$event_areas_arr)
                ->orderBy('id','asc')
                ->get();
        foreach($other_areas as $area)
            $event_areas_arr[]=$area->id;
        $available_employees = DB::table('employee_availability_days')
                ->select('employees.*','areas.title as emparea')
                ->leftJoin('employees','employees.id','=','employee_availability_days.employee_id')
                ->leftJoin('areas','employees.area_id','=','areas.id')
                ->where('employee_availability_days.days','=',$event_day)
                ->where('employees.status','=','Active');
        if (Gate::allows('isArea') || Gate::allows('isTeam') || Gate::allows('isDistrict')) {
            $areas = DB::table('area_user')->where('user_id','=',Auth::id())->get();
            $user_area = array();
            foreach($areas as $area)
                $user_area[]=$area->area_id;
            $available_employees->whereIn('employees.area_id', $user_area);
        }else{
            $area_arr=array();
            foreach($event->areas as $row)
                $area_arr[]=$row->area_id;
            $available_employees->whereIn('employees.area_id', $area_arr);
        }
        if(count($event_areas_arr))
        {
            $available_employees = $available_employees
                    ->orderByRaw('FIELD (employees.area_id, ' . implode(', ', $event_areas_arr) . ') ASC')
                    ->orderBy('employees.area_id','asc')
                   ->orderBy('employees.last_name','asc');
        }else{
            $available_employees = $available_employees
                   ->orderBy('employees.last_name','asc'); 
        }
        $available_employees = $available_employees
                //->orderBy('event_areas.area_id','asc')
                //->orderBy('employees.last_name','asc')
                ->get();
        //dd(DB::getQueryLog());die;
        $emps = array();
        foreach($available_employees as $emp)
            $emps[]=$emp->id;
        $other_employees = DB::table('employee_availability_days')
                ->select('employees.*','areas.title as emparea')
                ->leftJoin('employees','employees.id','=','employee_availability_days.employee_id')
                ->leftJoin('areas','employees.area_id','=','areas.id')
                ->whereNotIn('employees.id',$emps)
                ->groupBy('employee_availability_days.employee_id')
                ->where('employee_availability_days.days','!=',$event_day)
                ->where('employees.status','=','Active');
        if (Gate::allows('isArea') || Gate::allows('isTeam') || Gate::allows('isDistrict')) {
            $other_employees->whereIn('employees.area_id', $user_area);
        }else{
            $other_employees->whereIn('employees.area_id', $area_arr);
        }
        if(count($event_areas_arr))
        {
            $other_employees = $other_employees
                    ->orderByRaw('FIELD (employees.area_id, ' . implode(', ', $event_areas_arr) . ') ASC')
                    ->orderBy('employees.area_id','asc')
                   ->orderBy('employees.last_name','asc');
        }else{
            $other_employees = $other_employees
                   ->orderBy('employees.last_name','asc'); 
        }
        $other_employees = $other_employees->get();
        
        $inactive_employees = DB::table('employee_availability_days')
                ->select('employees.*','areas.title as emparea')
                ->leftJoin('employees','employees.id','=','employee_availability_days.employee_id')
                ->leftJoin('areas','employees.area_id','=','areas.id')
                ->whereNotIn('employees.id',$emps)
                ->groupBy('employee_availability_days.employee_id')
                ->where('employee_availability_days.days','!=',$event_day)
                ->where('employees.status','=','Inactive');
        if (Gate::allows('isArea') || Gate::allows('isTeam') || Gate::allows('isDistrict')) {
            $inactive_employees->whereIn('employees.area_id', $user_area);
        }else{
            $inactive_employees->whereIn('employees.area_id', $area_arr);
        }
        if(count($event_areas_arr))
        {
            $inactive_employees = $inactive_employees
                    ->orderByRaw('FIELD (employees.area_id, ' . implode(', ', $event_areas_arr) . ') ASC')
                    ->orderBy('employees.area_id','asc')
                   ->orderBy('employees.last_name','asc');
        }else{
            $inactive_employees = $inactive_employees
                   ->orderBy('employees.last_name','asc'); 
        }
        $inactive_employees = $inactive_employees->get();
        //dd(DB::getQueryLog());
        //echo '<pre>';print_r($other_employees);die;
//        print_r($event->schedule_employees);//print_r($employees);
//        die;
        //echo 'k';
        //echo '<pre>';print_r($historical_data);die;
        
        return view('admin.events.schedule_event', compact('event','historical_data','available_employees','other_employees','inactive_employees','already_scheduled_emp_arr','enable_calc'));
    }
    
    
    
    public function view_schedule_event(Request $request,$event_id) {
        
        if (! Gate::allows('schedule_employees_view')) {
            return abort(401);
        }
        $event = Event::with(array('store','areas','crew_leader_name','truck_dates','event_schedule_data','schedule_employees'))->findOrFail($event_id);
        $historical_data = Event::with(array('truck_dates'))
                ->where('store_id','=',$event->store_id)
                ->where('date','<',$event->date)
                ->orderBy('date','desc')->limit(1)->first();
        //echo '<pre>';print_r($event->areas->area);die;
       //   echo '<pre>';//print_r($other_employees);
//        print_r($event->schedule_employees);//print_r($employees);
//        die;
        //echo 'k';
        //echo '<pre>';print_r($this->getTimezone("hazaribagh"));die;
        
        return view('admin.events.view_schedule_event', compact('event','historical_data'));
    }
    
    
    function getTimezoneold($location)
    {
            $location = urlencode($location);
            $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$location}&key=AIzaSyCtht1kYCSys9ifRKwhMcy2afLPSRt9iZ4&sensor=false";
            $data = file_get_contents($url);
            print_r($data);
            // Get the lat/lng out of the data
            $data = json_decode($data);
            if(!$data) return false;
            if(!is_array($data->results)) return false;
            if(!isset($data->results[0])) return false;
            if(!is_object($data->results[0])) return false;
            if(!is_object($data->results[0]->geometry)) return false;
            if(!is_object($data->results[0]->geometry->location)) return false;
            if(!is_numeric($data->results[0]->geometry->location->lat)) return false;
            if(!is_numeric($data->results[0]->geometry->location->lng)) return false;
            $lat = $data->results[0]->geometry->location->lat;
            $lng = $data->results[0]->geometry->location->lng;

            // get the API response for the timezone
            $timestamp = time();
            $timezoneAPI = "https://maps.googleapis.com/maps/api/timezone/json?location={$lat},{$lng}&key=AIzaSyCtht1kYCSys9ifRKwhMcy2afLPSRt9iZ4&sensor=false&timestamp={$timestamp}";
            $response = file_get_contents($timezoneAPI);
            if(!$response) return false;
            $response = json_decode($response);
            if(!$response) return false;
            if(!is_object($response)) return false;
            if(!is_string($response->timeZoneId)) return false;

            return $response->timeZoneId;
    }

    public function save_schedule_event(Request $request) 
    {
        if (! Gate::allows('schedule_employees_create')) {
            return abort(401);
        }
        //echo '<pre>';print_r($request->all());die;
        $meet_time_arr = array();
        foreach($request['area_meet_time'] as $area_id=>$meettime)
        {
            $meet_time_arr[]=$area_id;
        }
        if(isset($request['additional_area']))
        {
            foreach($request['additional_area'] as $area_id)
            {
                if($area_id)
                    $meet_time_arr[]=$area_id;
            }
        }
        //$meet_time_arr = implode(',',$meet_time_arr);
        if(isset($request->emp_id))
        {
            foreach($request->emp_id as $emp_id)
            {
                $emp_area_id = DB::table('employees')
                    ->select('area_id')
                    ->where('id','=',$emp_id)
                    ->first();
                if(!in_array($emp_area_id->area_id,$meet_time_arr))
                {
                    return redirect()->route('admin.events.schedule-event',[$request->event_id])->withErrors('Area meet time is missing. Please add all missing area assigned meet time.');
                    echo 'meet time missing';
                }
            }
        }
//        echo "<pre>";
//        print_r($request->all());
//        die;
        EventSchedules::where('event_id', '=',$request->event_id)->delete();
        $event = EventSchedules::create($request->all());
        
        $where1 = array('id' => $request->event_id);
        $updateArr1 = ['status' =>'Scheduled' ];
        $event  = Event::where($where1)->update($updateArr1);
        
        if($request->area_meet_time && count($request->area_meet_time))
        {
            foreach($request->area_meet_time as $area_id=>$time)
            {
                $where = array('event_id' => $request->event_id,'area_id'=>$area_id);
                $updateArr = ['meet_time' => $time];
                $event  = EventAreas::where($where)->update($updateArr);
            }
        }
        if($request->additional_area && count($request->additional_area))
        {
            foreach($request->additional_area as $key=>$area_id)
            {
                if($area_id)
                {
                    $area_info = array('event_id' => $request->event_id,'area_id'=>$area_id,'meet_time'=>$request['additional_area_meet_time'][$key]);
                    $event  = EventAreas::create($area_info);
                }
            }
        }
//        echo "<pre>";
//        print_r($request->all());
//        die;
       if($request->emp_id && count($request->emp_id))
        {
// return $request->emp_id;
//             dd($request->emp_id);
            EventScheduleEmployees::where('event_id',$request->event_id)->whereNotIn('employee_id', $request->emp_id)->delete();

       
            foreach($request->emp_id as $key=>$emp)
            {
                $emp_area_id = DB::table('employees')->select('area_id')->where('id','=',$emp)->first();

                EventScheduleEmployees::updateOrCreate(
                    ['event_id' => $request->event_id, 'employee_id' => $emp],
                    [
                    'area_id' => $emp_area_id->area_id,
                    'task' => $request['task'][$key],
                    'vehicle_number'=>$request['vehicle'][$key],
                    'comment'=>$request['comment'][$key],
                    'custom_comment'=>$request['custom_comment'][$key]
                    ]
                );
                   
                
               
            }
        }

        
        
        //echo '<pre>';print_r($request->all());die;
        return redirect()->route('admin.events.index')->with('successmsg', 'Event scheduled successfully.');
    }
    
    public function generateEventNumber($event_id){
        
        $event_number = date('ymd').str_pad($event_id, 4, '0', STR_PAD_LEFT);
        $where = array('id' => $event_id);
        $updateArr = ['number' => $event_number];
        $event  = Event::where($where)->update($updateArr);
    }
    
    public function getCompletedEvents(Request $request){
        
        if (! (Gate::allows('event_view') || Gate::allows('view_client_event') )) {
            return abort(401);
        }
        $events = Event::with(array('store','crew_leader_name','areas'))->orderBy('id','desc')->get();
        
        $template = 'admin';
        

        if(@$request->user()->client_id) 
            {
                $template = 'clients';
                $stores = DB::table('stores')->where('client_id',$request->user()->client_id)->orderBy('name','asc')->pluck('name','id');
                $divisions = DB::table('divisions')->where('client_id',$request->user()->client_id)->pluck('name','id');
            }
            else 
            
            {
                $stores = DB::table('stores')->orderBy('name','asc')->pluck('name','id');
                $divisions = DB::table('divisions')->pluck('name','id');

            }

        if (request('show_calender') == 1) {
            if(request()->ajax()) 
            {
                $event[] = array();
                foreach ($events as $key => $value) {
                    $event[$key]['allDay'] = true;
                    $event[$key]['id'] = $value->id;
                    $event[$key]['title'] = $value->store->name;
                    $event[$key]['start'] = new \DateTime($value->date);
                    $event[$key]['end'] = new \DateTime($value->date.' +1 day');
                }
                return Response::json($event);
            }
            
            $employees = DB::table('employees')->where('status','=','Active')->where('is_crew_leader',1)->pluck('name','id');
            $areas = DB::table('areas')->where('status','=', 'active')->pluck('title','id'); 
            return view('admin.events.index-calender', compact('stores','employees','areas'));
        }
        else{
            $associations = DB::table('associations')->pluck('name','id');
            //$divisions = DB::table('divisions')->pluck('name','id');
            $clients = DB::table('clients')->pluck('name','id'); 
            $employees = DB::table('employees')->where('status','=','Active')->where('is_crew_leader',1)->pluck('name','id');
            $areas = DB::table('areas')->where('status','=', 'active')->pluck('title','id'); 
            $pending_events = Event::select('events.*',DB::raw('MIN(date) as event_date'),'stores.name as storename')
                ->leftJoin('stores','stores.id','=','events.store_id')
                ->where('events.status','=','Pending')
                ->where('events.date','>=',date('Y-m-d'))
                    ->groupBy('events.store_id');
                
            if (Gate::allows('isArea') || Gate::allows('isTeam') || Gate::allows('isDistrict')) {
                $user_assigned_areas = DB::table('area_user')->where('user_id','=',Auth::id())->get();
                $user_areas = array();
                foreach($user_assigned_areas as $area)
                    $user_areas[]=$area->area_id;
                $user_assigned_areas = EventAreas::whereIn('area_id', $user_areas)->get();
                
                $area_event = array();
                foreach($user_assigned_areas as $area)
                    $area_event[]=$area->event_id;
                $pending_events = $pending_events->whereIn('events.id',$area_event);
            }
            $pending_events = $pending_events->get();
            $pending_event = array();
            foreach ($pending_events as $value) {
                $pending_event[$value->id] = $value->id.'-'.$value->storename.'-'.date('m-d-Y',strtotime($value->date)).'-'.date('h:i A',strtotime($value->start_time));
            }
            

            return view($template.'.events.prior-events', compact('events','stores','employees','areas','associations','clients','divisions','pending_event'));
        }
    }
    
    public function getprecallcomment(Request $request,$id)
    {
        if (! Gate::allows('event_view')) {
            return abort(401);
        }
        $event = Event::with(array('store','areas','crew_leader_name','truck_dates'))->findOrFail($id);
        $historical_data = Event::with(array('truck_dates'))
                ->where('store_id','=',$event->store_id)
                ->where('date','<',$event->date)
                ->orderBy('date','desc')->limit(1)->first();
        if(request()->ajax()) 
        {
//            $event = Event::with(array('store','areas','crew_leader_name','truck_dates'))->findOrFail($id);
//            $historical_data = Event::with(array('truck_dates'))
//                ->where('store_id','=',$event->store_id)
//                ->where('id','<',$id)
//                ->orderBy('id','desc')->limit(1)->first();
            return Response::json(array('event'=>$event,'historical_data'=>$historical_data));
        }
//        $event = Event::with(array('store','areas','crew_leader_name','truck_dates'))->findOrFail($id);
//        $historical_data = Event::with(array('truck_dates'))
//                ->where('store_id','=',$event->store_id)
//                ->where('id','<',$id)
//                ->orderBy('id','desc')->limit(1)->first();
       // echo $event->areas[1]->area->title;echo '<pre>';print_r($event);die;
        return view('admin.events.show',compact('event','historical_data'));
    }
    
    public function getqccomment(Request $request,$id)
    {
        if (! Gate::allows('event_view')) {
            return abort(401);
        }
        $event = Event::with(array('store','areas','crew_leader_name','truck_dates'))->findOrFail($id);
        $historical_data = Event::with(array('truck_dates'))
            ->where('store_id','=',$event->store_id)
            ->where('date','<',$event->date)
            ->orderBy('date','desc')->limit(1)->first();
        if(request()->ajax()) 
        {
            
            return Response::json(array('event'=>$event,'historical_data'=>$historical_data));
        }
//        $event = Event::with(array('store','areas','crew_leader_name','truck_dates'))->findOrFail($id);
//        $historical_data = Event::with(array('truck_dates'))
//                ->where('store_id','=',$event->store_id)
//                ->where('id','<',$id)
//                ->orderBy('id','desc')->limit(1)->first();
       // echo $event->areas[1]->area->title;echo '<pre>';print_r($event);die;
        return view('admin.events.show',compact('event','historical_data'));
    }
    
    public function employee_schedule_area_wise(Request $request)
    {
        //echo '<pre>';print_r($request->all());die;
        $employees=array();
//        foreach($request['emp_id'] as $emp)
//        {
//            $emp = DB::table('employees')
//                ->select('employees.*','areas.title as area','areas.id as area_id')
//                ->leftJoin('areas','employees.area_id','=','areas.id')
//                ->where('employees.id','=',$emp)
//                ->first();
//            
//        }
        if(count($request['emp_id']))
        {
            foreach($request['emp_id'] as $emp)
            {
                $emp = DB::table('employees')
                    ->select('employees.*','areas.title as area','areas.id as area_id')
                    ->leftJoin('areas','employees.area_id','=','areas.id')
                    ->where('employees.id','=',$emp)
                    ->first();
    //            $employees[$emp->area_id]['m']=0;
    //            $employees[$emp->area_id]['f']=0;
    //            $employees[$emp->area_id]['u']=0;

                $employees[$emp->area_id]['area']=$emp->area;
                if($emp->gender=="M"){
                    if(isset($employees[$emp->area_id]['m']))
                        $employees[$emp->area_id]['m']+=1;
                    else
                        $employees[$emp->area_id]['m']=1;
                }elseif($emp->gender=="F"){
                    if(isset($employees[$emp->area_id]['f']))
                        $employees[$emp->area_id]['f']+=1;
                    else
                        $employees[$emp->area_id]['f']=1;
                }else{
                    if(isset($employees[$emp->area_id]['u']))
                        $employees[$emp->area_id]['u']+=1;
                    else
                        $employees[$emp->area_id]['u']=1;
                }
            }
        }
        //echo '<pre>';print_r($employees);die;
        if(count($employees))
        {
            foreach($employees as $key=>$emps)
            {
                if(isset($emps['f']))
                    $f_count=$emps['f'];
                else
                    $f_count=0;
                if(isset($emps['m']))
                    $m_count=$emps['m'];
                else
                    $m_count=0;
                if(isset($emps['u']))
                    $u_count=$emps['u'];
                else
                    $u_count=0;
                echo $html='<div class="row">
                    <div class="col-md-6">'.$emps['area'].'</div>
                    <div class="col-md-6">'.$f_count.' F, '.$m_count.' M, '.$u_count.' U Total '.($f_count+$m_count+$u_count).'</div>
                    </div>';
            }
        }
    }
    
    public function get_mini_schedule(Request $request) {
        //echo '<pre>';print_r($request->all());die;
        //DB::enableQueryLog();
        $date = date('Y-m-d',strtotime($request['date']));
        //$area_id = explode(',',$request['areas']);
//        $areas = DB::select( DB::raw("SELECT event_id from event_areas where area_id in(".$area_id.")") );
        $sel_area = array();
        foreach($request['areas'] as $area)
            $sel_area[] = $area;
        $events = DB::table('event_areas')
                ->select('stores.name as storename','areas.area_number','areas.title as area_title','events.*')
                ->leftJoin('events','events.id','=','event_areas.event_id')
                ->leftJoin('stores','stores.id','=','events.store_id')
                ->leftJoin('areas','areas.id','=','event_areas.area_id')
                ->whereDate('events.date', '=', $date)
                //->whereIn('events.id',$sel_area)
                ->where('events.status','!=','inactive')
                ->whereIn('event_areas.area_id',$sel_area)
                ->orderBy('areas.area_number','asc')
                ->orderBy('events.run_number','asc')
                ->get();
        //dd(DB::getQueryLog());
        return Response::json(array('events'=>$events),200);
    }
    
    public function export_event_info(Request $request)
    {
        $export_file_name = 'Event-Info-'.date('Y-m-d h-i-s-A').'.xlsx';
        return Excel::download(new EventInfoExport(2018), $export_file_name);
    
    }
    
    public function copy_event_schedule(Request $request)
    {
        //echo url('/');die;
        //$event_schedule = EventSchedules::where('event_id', '=',$request->event_id_for_schedulecopy)->first();
        //$schedule_employees = $event_schedule->toArray();
        $event_schedule_employees = EventScheduleEmployees::where('event_id', '=',$request->event_id_for_schedulecopy)->get();
        $event_schedule_employees = $event_schedule_employees->toArray();
        //unset($schedule_employees['íd']);
        //echo count($request['events']);die;
        //$event_id = $request['events'];
        if($request['events'])
        {
            foreach($request['events'] as $event_id)
            {
                //$schedule_employees['event_id']=$event_id;
                //print_r($schedule_employees);
                //EventSchedules::where('event_id', '=',$event_id)->delete();
                //$event = EventSchedules::create($schedule_employees);
                $where1 = array('id' => $event_id);
                $updateArr1 = ['status' =>'Pending' ];
                $event  = Event::where($where1)->update($updateArr1);
    //            if($request->area_meet_time && count($request->area_meet_time))
    //            {
    //                foreach($request->area_meet_time as $area_id=>$time)
    //                {
    //                    $where = array('event_id' => $event_id,'area_id'=>$area_id);
    //                    $updateArr = ['meet_time' => $time];
    //                    $event  = EventAreas::where($where)->update($updateArr);
    //                }
    //            }
    //        echo "<pre>";
    //        print_r($event_schedule_employees);
    //        die;
                EventScheduleEmployees::where('event_id', '=',$event_id)->delete();
                foreach($event_schedule_employees as $emp)
                {
                    $emp['event_id']=$event_id;
                    $emp['task']='Auditor';
                    $emp['comment']='';
                    $emp['custom_comment']='';
                    $emp['vehicle_number']='';
                    //print_r($emp);
                    EventScheduleEmployees::create($emp);
                }
            }
        }
        
        //echo $event['redirectto']=url('/').'/admin/schedule-event/'.$event_id;die;
        return Response::json(['redirectto'=>url('/').'/admin/schedule-event/'.$event_id]);
    }
    
    public function employee_export(Request $request)
    {
        $export_file_name = 'Employee-Export-'.date('Y-m-d h-i-s-A').'.xlsx';
        return Excel::download(new ScheduleExport(2018), $export_file_name);
    
    }
    
    public function upload_timesheet_mdb(Request $request,$event_id)
    {
        //die($event_id);
//        $files = glob(public_path('js/*'));
//        \Zipper::make(public_path('test.zip'))->add($files)->close();
//        return response()->download(public_path('test.zip'));
        return view('admin.events.upload_timesheet_mdb',compact('event_id'));
    }
    
    public function uploadmdb(Request $request)
    {
        $already_exist = DB::table('tsp_event_mdbs')->select('id')
                ->where('event_id','=',$request['event_id'])
                ->where('validation_status','=','Pending')
                ->first();
        if($already_exist)
            return Response::json(array('type'=>'danger','message'=>'There is already an event file uploaded for this event which is in queue. Either you can delete that first then upload new file or you can wait for some time.'),200);
            //return redirect()->route('admin.events.upload_timesheet_mdb',[$request['event_id']])->withErrors('There is already an event file uploaded for this event which is in queue. Either you can delete that first then upload new file or you can wait for some time.');
        //echo '<pre>';print_r($request->all());die;
        if(!isset($request['inventmdb']))
            return Response::json(array('type'=>'danger','message'=>'Please upload zipped mdb file.'),200);
            //return redirect()->route('admin.events.upload_timesheet_mdb',[$request['event_id']])->withErrors('Please upload zipped mdb file.');
        $file = $request->file('inventmdb');
        //echo 'File Name: '.$file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        if($extension!="zip")
            return Response::json(array('type'=>'danger','message'=>'This file is not a valid zipped file.'),200);
            //return redirect()->route('admin.events.upload_timesheet_mdb',[$request['event_id']])->withErrors('This file is not a valid zipped file.');
        //echo 'File Real Path: '.$file->getRealPath();
        //echo 'File Size: '.$file->getSize();
        //Display File Mime Type
        $mime_type = $file->getMimeType();
        if($mime_type!="application/zip")
            return Response::json(array('type'=>'danger','message'=>'This file is not a valid zipped file.'),200);
            //return redirect()->route('admin.events.upload_timesheet_mdb',[$request['event_id']])->withErrors('This file is not a valid zipped file.');
        $store = Event::select('store_id')->where('id', '=',$request['event_id'])->first();
        $store_id = $store->store_id;
        $filename = $request['event_id'].'-'.$file->getClientOriginalName();
        $destinationPath = 'uploads/event_mdbs/store'.$store_id;
        if($file->move($destinationPath,$filename))
        {
            $Path = public_path($destinationPath).'/'.$filename;
            $values = array('event_id'=>$request['event_id'],'mdb_path' => $Path,'validation_status' =>'Pending','import_status'=>'Pending','notify_to'=>auth()->user()->email);
            DB::table('tsp_event_mdbs')->insert($values);
            //\Zipper::make($Path)->extractTo($destinationPath.'/'.$request['event_id']);die;
            $job_count = DB::table('tsp_event_mdbs')->select('id')
                ->where('validation_status','=','Pending')
                ->get();
            if(count($job_count)==0){
                return Response::json(array('type'=>'success','message'=>'Adding jobs to queue. There is already 1 job ahead you. Estimated time to process your request is 20 minutes. We will send you an email once this file is get processed.'),200);
                //return redirect()->route('admin.events.upload_timesheet_mdb',[$request['event_id']])->with('successmsg', 'Adding jobs to queue. There is already 1 job ahead you. Estimated time to process your request is 20 minutes. We will send you an email once this file is get processed.');
            }elseif(count($job_count)==1){
                return Response::json(array('type'=>'success','message'=>'Adding jobs to queue. There is already 1 job ahead you. Estimated time to process your request is 40 minutes. We will send you an email once this file is get processed.'),200);
                //return redirect()->route('admin.events.upload_timesheet_mdb',[$request['event_id']])->with('successmsg', 'Adding jobs to queue. There is already 1 job ahead you. Estimated time to process your request is 40 minutes. We will send you an email once this file is get processed.');
            }elseif(count($job_count)>1)
            {
                $minutes = ((count($job_count))*20);
                $hrs = floor(($minutes/ (60)));
                $mins = floor(($minutes - $hrs*60));
                if($hrs==1 && $mins==0)
                    $process_time = $hrs.' hour';
                elseif($hrs==1 && $mins)
                    $process_time = $hrs.' hour & '.$mins.' minutes';
                elseif($hrs==0 && $mins)
                    $process_time = $mins.' minutes';
                elseif($hrs>1 && $mins)
                    $process_time = $hrs.' hours & '.$mins.' minutes';
                return Response::json(array('type'=>'success','message'=>'Adding jobs to queue. There is already '.(count($job_count)-1).' jobs ahead you. Estimated time to process your request is '.$process_time.'. We will send you an email once this file is get processed.'),200);
                //return redirect()->route('admin.events.upload_timesheet_mdb',[$request['event_id']])->with('successmsg', 'Adding jobs to queue. There is already '.(count($job_count)-1).' jobs ahead you. Estimated time to process your request is '.$process_time.'. We will send you an email once this file is get processed.');
            }
        }else{
            return Response::json(array('type'=>'danger','message'=>'Their is some error. Kindly try after some time.'),200);
            //return redirect()->route('admin.events.upload_timesheet_mdb',[$request['event_id']])->withErrors('Their is some error. Kindly try after some time.');
        }
        
    }
    
    public function validateMdb()
    {
        //echo '<pre>';
        $events = DB::table('tsp_event_mdbs')
                ->where('validation_status','=','Progress')
                ->get();
        if(count($events))
            return;
        $event = DB::table('tsp_event_mdbs')
                ->where('validation_status','=','Pending')
                ->orderBy('created_on','asc')
                ->first();
        DB::table('tsp_event_mdbs')->where('id',$event->id)->update(array('validation_status'=>"Progress"));
        $store = Event::select('store_id')->where('id', '=',$event->event_id)->first();
        $store_id = $store->store_id;
        
        $dir_path = public_path('uploads/event_mdbs/store'.$store_id .'/'.$event->event_id.'/');
        \Zipper::make($event->mdb_path)->extractTo($dir_path.'/');
        $files = File::allFiles($dir_path);
        foreach($files as $file)
        {
            $file = new SplFileInfo($file); 
            $filename = $file->getFilename();
            if(preg_match("/Master/",$filename))
            {
                $master_file_name = $filename;
            }
            if(preg_match("/invent/",$filename))
            {
                $invent_file_name = $filename;
            }
        }
        //echo '<pre>';print_r($files[0]->fileName);die;
        $dbName = $dir_path.$invent_file_name;
        $db = new PDO("odbc:DRIVER={Microsoft Access Driver (*.mdb)}; DBQ=$dbName; Uid=; Pwd=;");
        $errorlog=array();
        $errorflag=0;
        
        $result = $db->query("SELECT * FROM sub_locations");
        while($row = $result->fetch())
        {
            $loc = DB::table('tsp_locations')->select('id')->where('loc','=',$row['Loc'])->first();
            if(!$loc)
            {
                array_push ($errorlog, 'Reference of Sublocation '.$row['Sub_Loc'].' not found in location table.');
                $errorflag=1;
            }
        }
        
//        $result = $db->query("SELECT * FROM details");
//        while($row = $result->fetch())
//        {
//            if($row['event_id']!=$event->event_id)
//            {
//                array_push ($errorlog, 'Event id mismatch in details table.');
//                $errorflag=1;
//            }
//        }
        if($errorflag)
        {
            DB::table('tsp_event_mdbs')->where('id',$event->id)
                    ->update(array('validation_status'=>"Failed",
                                    'validation_error'=>serialize($errorlog)));
            if($event->notify_to!="")
            {
                $user_detail = array(
                    'name'            => 'User',
                    'email'           => $event->notify_to,
                    'email_content'   => 'The audit data uploaded by you have some error . You can view the error log after login into the application. Please upload again after removing old data and correct the entries.',
                    'mail_from_email' => env('MAIL_FROM'),
                    'mail_from'       => env('MAIL_NAME'),
                    'subject'         => 'Error while validating audit data uploaded by you.',
                );
                $user_single = (object) $user_detail;
                Mail::send('emails.mdb_error',['user' => $user_single], function ($message) use ($user_single) {
                        $message->from($user_single->mail_from_email,$user_single->mail_from);
                        $message->to($user_single->email, $user_single->name)->cc('lancebowser@msi-inv.com')->subject($user_single->subject);
                        $message->replyTo($user_single->mail_from_email,$user_single->mail_from);
                });
            }
        }else{
            if($event->notify_to!="")
            {
                $user_detail = array(
                    'name'            => 'Admin',
                    'email'           => $event->notify_to,
                    'email_content'   => 'The audit data uploaded by you is validated successfully. We will let you know once data is imported into application.',
                    'mail_from_email' => env('MAIL_FROM'),
                    'mail_from'       => env('MAIL_NAME'),
                    'subject'         => 'Audit data validated successfully.'
                    
                );
                $user_single = (object) $user_detail;
                Mail::send('emails.mdb_error',['user' => $user_single], function ($message) use ($user_single) {
                        $message->from($user_single->mail_from_email,$user_single->mail_from);
                        $message->to($user_single->email, $user_single->name)->cc('lancebowser@msi-inv.com')->subject($user_single->subject);
                        $message->replyTo($user_single->mail_from_email,$user_single->mail_from);
                });
            }
            DB::table('tsp_event_mdbs')->where('id',$event->id)->update(array('validation_status'=>"Passed"));
        }
    }
    
    public function importFromMdb()
    {
        echo '<pre>';
        $events = DB::table('tsp_event_mdbs')
                ->where('import_status','=','Progress')
                ->get();
        if(count($events))
            return;
        $event = DB::table('tsp_event_mdbs')
                ->where('import_status','=','Pending')
                ->where('validation_status','=','Passed')
                ->orderBy('created_on','asc')
                ->first();
        DB::table('tsp_event_mdbs')->where('id',$event->id)->update(array('import_status'=>"Progress"));
        $store = Event::select('store_id')->where('id', '=',$event->event_id)->first();
        $store_id = $store->store_id;
        $dir_path = public_path('uploads/event_mdbs/store'.$store_id .'/'.$event->event_id.'/');
        $files = File::allFiles($dir_path);
        foreach($files as $file)
        {
            $file = new SplFileInfo($file); 
            $filename = $file->getFilename();
            if(preg_match("/Master/",$filename))
            {
                $master_file_name = $filename;
            }
            if(preg_match("/invent/",$filename))
            {
                $invent_file_name = $filename;
            }
        }
        //echo '<pre>';print_r($files[0]->fileName);die;
        $dbName = $dir_path.$invent_file_name;
        $db = new PDO("odbc:DRIVER={Microsoft Access Driver (*.mdb)}; DBQ=$dbName; Uid=; Pwd=;");
        $masterdbName = $dir_path.$master_file_name;
        $masterdb = new PDO("odbc:DRIVER={Microsoft Access Driver (*.mdb)}; DBQ=$masterdbName; Uid=; Pwd=;");
            
        $errorlog=array();
        $errorflag=0;
       
        $result = $db->query("SELECT * FROM locations");
        while($row = $result->fetch())
        {
            $values = array('event_id'=>$event->id,'loc' => $row['Loc'],'description' =>$row['Description']);
            DB::table('tsp_locations')->insert($values);
        }
        
        $result = $db->query("SELECT * FROM sub_locations");
        while($row = $result->fetch())
        {
            $loc = DB::table('tsp_locations')->select('id')->where('loc','=',$row['Loc'])->first();
            $loc_id=($loc)?$loc->id:0;
            $values = array('event_id'=>$event->id,'loc_id'=>$loc_id,'sub_loc' => $row['Sub_Loc'],'description' =>$row['Description']);
            DB::table('tsp_sub_locations')->insert($values);
        }
        
        $result = $db->query("SELECT * FROM store");
        while($row = $result->fetch())
        {
//            $loc = DB::table('stores')->select('id')->where('loc','=',$row['Loc'])->first();
//            $loc_id=($loc)?$loc->id:0;
            $values = array('event_id'=>$event->id,'inventory'=>$row['Inventory'],'inventory_no' => $row['Inventory No'],'account' =>$row['Account'],
                'store_id'=>$row['Store'],'date'=>$row['Date'],'manager'=>$row['Manager'],'user_defined1'=>$row['User Defined 1'],
                'user_defined2'=>$row['User Defined 2'],'user_defined3'=>$row['User Defined 3'],'user_defined4'=>$row['User Defined 4'],
                'user_defined5'=>$row['User Defined 5']);
            DB::table('tsp_stores')->insert($values);
        }
        
        $result = $db->query("SELECT * FROM details");
        while($row = $result->fetch())
        {
            $loc = DB::table('tsp_locations')->select('id')->where('loc','=',$row['LOC'])->first();
            $loc_id=($loc)?$loc->id:0;
            $sub_loc = DB::table('tsp_sub_locations')->select('id')->where('sub_loc','=',$row['SUB_LOC'])->first();
            $sub_loc_id=($sub_loc)?$sub_loc->id:0;
            
            
            $masterresult = $masterdb->query("SELECT * FROM master where ItemNo='".$row['SKU']."'");
            $masterres = $masterresult->fetch();
            
                
            
        
            $values = array('event_id'=>$event->id,'inventory' => $row['Inventory'],'loc_id' =>$loc_id,
                'sub_loc_id'=>$sub_loc_id,'category_id'=>$row['CAT'],'sku'=>$row['SKU'],'qty'=>$row['QTY'],
                'mult'=>$row['Mult'],'price'=>$row['PRICE'],'cost'=>$row['Cost'],'employee'=>$row['Employee'],
                'type'=>$row['Type'],'time'=>$row['Time'],'scan_type'=>$row['ScanType'],'detail_tag3'=>$row['Detail Tag 3'],
                'non_match'=>$row['Non-Match'],'price_non_match'=>$row['Price Non-Match'],'item_no'=>$row['ItemNo'],'retail' =>$row['Retail'],'cost'=>$row['Cost'],
                    'category_id'=>$row['Cat'],'description'=>$row['Desc'],'user1'=>$row['User1'],'user2'=>$row['User2'],'user3'=>$row['User3'],'user7'=>$row['User7']);
            DB::table('tsp_details')->insert($values);
        }
        $result = $db->query("SELECT * FROM segradio");
        while($row = $result->fetch())
        {
//            $loc = DB::table('tsp_locations')->select('id')->where('loc','=',$row['LOC'])->first();
//            $loc_id=($loc)?$loc->id:0;
//            $sub_loc = DB::table('tsp_sub_locations')->select('id')->where('sub_loc','=',$row['SUB_LOC'])->first();
//            $sub_loc_id=($sub_loc)?$sub_loc->id:0;
            $values = array('event_id'=>$event->id,'date'=>$row['Date'],'store_id' => $row['Store'],'parent_code' =>$row['Parent_Code'],
                'parent_upc'=>$row['Parent_UPC'],'description'=>$row['Description'],'qty_on_hand'=>$row['Qty_On_Hand'],
                'sum_qty_on_hand'=>$row['Sum_Qty_On_Hand'],'cgo'=>$row['CGO'],'category_id'=>$row['Category']);
            DB::table('tsp_segradio')->insert($values);
        }
        
        $result = $db->query("SELECT * FROM correctiondetails");
        while($row = $result->fetch())
        {
            $loc = DB::table('tsp_locations')->select('id')->where('loc','=',$row['LOC'])->first();
            $loc_id=($loc)?$loc->id:0;
            $sub_loc = DB::table('tsp_sub_locations')->select('id')->where('sub_loc','=',$row['SUB_LOC'])->first();
            $sub_loc_id=($sub_loc)?$sub_loc->id:0;
            $values = array('event_id'=>$event->id,'detail_id'=>$row['DetailID'],'loc_id' => $loc_id,'sub_loc_id' =>$sub_loc_id,
                'sku'=>$row['SKU'],'qty'=>$row['CORRECTION_QTY'],'scan_time'=>$row['ScanTime'],
                'scan_type'=>$row['ScanType'],'accepted'=>$row['Accepted'],'auditor_no'=>$row['AuditorNum'],
                'counter_no'=>$row['CounterNum'],'price'=>$row['Price'],'original_qty'=>$row['OriginalQuantity'],
                'reason_code'=>$row['ReasonCode']);
            DB::table('tsp_correction_details')->insert($values);
        }
        
        $result = $db->query("SELECT * FROM storesurvey");
        while($row = $result->fetch())
        {
            $values = array('event_id'=>$event->id,'AccuracyLevelRating'=>$row['AccuracyLevelRating'],'CommentAccuracyLevelRating' => $row['CommentAccuracyLevelRating'],
                'StoreConditionRating' =>$row['StoreConditionRating'],'CommentStoreConditionRating'=>$row['CommentStoreConditionRating'],
                'OverallSatisfactionRating'=>$row['OverallSatisfactionRating'],'CommentOverallSatisfactionRating'=>$row['CommentOverallSatisfactionRating'],
                'TimeRXBegan'=>$row['TimeRXBegan'],'CommentTimeRXBegan'=>$row['CommentTimeRXBegan'],'RXNumberOfCounters'=>$row['RXNumberOfCounters'],
                'CommentRXNumberOfCounters'=>$row['CommentRXNumberOfCounters'],'TimeRXFinished'=>$row['TimeRXFinished'],'CommentTimeRXFinished'=>$row['CommentTimeRXFinished'],
                'TimeRXPaperworkGiven'=>$row['CommentRXTimePaperworkGiven'],'RXAuditTrailsCompleted'=>$row['RXAuditTrailsCompleted'],
                'CommentRXAuditTrailsCompleted'=>$row['CommentRXAuditTrailsCompleted'],'RXAccurateCount'=>$row['RXAccurateCount'],
                'CommentRXAccurateCount'=>$row['CommentRXAccurateCount'],'SignOffKey'=>$row['SignOffKey'],
                'CommentSignOffKey'=>$row['CommentSignOffKey'],'StorePassword'=>$row['StorePassword']);
            DB::table('tsp_store_survey')->insert($values);
        }
        
        $result = $db->query("SELECT * FROM inventoryevaluation");
        while($row = $result->fetch())
        {
            $values = array('event_id'=>$event->id,'AppVersion'=>$row['AppVersion'],'AccountID_AirLink' => $row['AccountID_AirLink'],
                'InventoryDate'=>$row['InventoryDate'],'StoreManager'=>$row['StoreManager'],'MSI_StartTime'=>$row['MSI_StartTime'],'MSI_StopTime'=>$row['MSI_StopTime'],
                'MSI_TotalCounters'=>$row['MSI_TotalCounters'],'Team_Rate_Accuracy'=>$row['Team_Rate_Accuracy'],
                'Team_Rate_Efficiency'=>$row['Team_Rate_Efficiency'],'Team_Rate_Supervisor'=>$row['Team_Rate_Supervisor'],
                'Team_Rate_CAC'=>$row['Team_Rate_CAC'],'Team_Rate_ATP'=>$row['Team_Rate_ATP'],
                'Team_Rate_Performance'=>$row['Team_Rate_Performance'],'Team_Rate_TeamLeader'=>$row['Team_Rate_TeamLeader'],
                'Team_YN_AOT'=>$row['Team_YN_AOT'],'Team_YN_Professional' => $row['Team_YN_Professional'],
                'Team_YN_Organized' =>$row['Team_YN_Organized'],'Team_YN_CheckStore'=>$row['Team_YN_CheckStore'],
                'Team_YN_CompareCount'=>$row['Team_YN_CompareCount'],'Team_YN_Recounts'=>$row['Team_YN_Recounts'],
                'Team_YN_RecountsResolved'=>$row['Team_YN_RecountsResolved'],'Team_YN_TeamAgain'=>$row['Team_YN_TeamAgain'],'Team_Section_Comments'=>$row['Team_Section_Comments'],
                'Team_YN_NoSectionAnswers'=>$row['Team_YN_NoSectionAnswers'],'TEAM_SectionPassword'=>$row['TEAM_SectionPassword'],'TEAM_SectionComplete'=>$row['TEAM_SectionComplete'],
                'TEAM_ProtectionOffered'=>$row['TEAM_ProtectionOffered'],'Client_Rate_SFPrep'=>$row['Client_Rate_SFPrep'],
                'Client_Rate_RXPrep'=>$row['Client_Rate_RXPrep'],'Client_Rate_RXSuper'=>$row['Client_Rate_RXSuper'],
                'Client_Rate_BRAccuracy'=>$row['Client_Rate_BRAccuracy'],'Client_Rate_ItemLabels'=>$row['Client_Rate_ItemLabels'],
                'Client_YN_Cooperative'=>$row['Client_YN_Cooperative'],'Client_YN_Cooperative_Comments'=>$row['Client_YN_Cooperative_Comments'],
                'Client_YN_CountDamagedMerch'=>$row['Client_YN_CountDamagedMerch'],'Client_YN_CountDamagedMerch_Comments'=>$row['Client_YN_CountDamagedMerch_Comments'],
                'Client_YN_OmitMerch'=>$row['Client_YN_OmitMerch'],'Client_YN_OmitMerch_Comments'=>$row['Client_YN_OmitMerch_Comments'],
                'Client_Section_Comments'=>$row['Client_Section_Comments'],'Client_YN_NoSectionAnswers'=>$row['Client_YN_NoSectionAnswers'],'CLIENT_SectionPassword'=>$row['CLIENT_SectionPassword'],'CLIENT_SectionComplete'=>$row['CLIENT_SectionComplete'],'CLIENT_ProtectionOffered'=>$row['CLIENT_ProtectionOffered'],'Store_SF_Rate_ConditionShelves'=>$row['Store_SF_Rate_ConditionShelves'],
                'Store_SF_Rate_ConditionPriceTags'=>$row['Store_SF_Rate_ConditionPriceTags'],'Store_SF_Rate_ConditionMerchPricing'=>$row['Store_SF_Rate_ConditionMerchPricing'],'Store_SF_Rate_ConditionDumpsDisplays'=>$row['Store_SF_Rate_ConditionDumpsDisplays'],'Store_SF_Rate_ConditionDairyCase'=>$row['Store_SF_Rate_ConditionDairyCase'],'Store_SF_Rate_ConditionFrozenCase'=>$row['Store_SF_Rate_ConditionFrozenCase'],'Store_SF_Rate_ConditionFloors'=>$row['Store_SF_Rate_ConditionFloors'],'Store_SF_Rate_ConditionGeneral'=>$row['Store_SF_Rate_ConditionGeneral'],
                'Store_SF_Comments'=>$row['Store_SF_Comments'],'Store_BR_Rate_SplitCases'=>$row['Store_BR_Rate_SplitCases'],'Store_BR_Rate_ConditionDairyCase'=>$row['Store_BR_Rate_ConditionDairyCase'],
                'Store_BR_Rate_ConditionFrozenCase'=>$row['Store_BR_Rate_ConditionFrozenCase'],'Store_BR_Rate_AmountOfStock'=>$row['Store_BR_Rate_AmountOfStock'],'Store_BR_Rate_Pricing'=>$row['Store_BR_Rate_Pricing'],'Store_BR_Comments'=>$row['Store_BR_Comments'],'Store_YN_NoSectionAnswers'=>$row['Store_YN_NoSectionAnswers'],'STORE_SectionPassword'=>$row['STORE_SectionPassword'],'STORE_SectionComplete'=>$row['STORE_SectionComplete'],
                'STORE_ProtectionOffered'=>$row['STORE_ProtectionOffered'],'MSI_ReportComplete'=>$row['MSI_ReportComplete'],'MSI_ReportCompleteDate'=>$row['MSI_ReportCompleteDate']);
            DB::table('tsp_inventory_evaluation')->insert($values);
        }
        
        $result = $db->query("SELECT * FROM inventorysurvey");
        while($row = $result->fetch())
        {
            $values = array('event_id'=>$event->id,'TimeCrewArrived'=>$row['TimeCrewArrived'],'CommentTimeCrewArrived' => $row['CommentTimeCrewArrived'],
                'TimeStoreOpened' =>$row['TimeStoreOpened'],'CommentTimeStoreOpened'=>$row['CommentTimeStoreOpened'],
                'NumberOfCounters'=>$row['NumberOfCounters'],'CommentNumberOfCounters'=>$row['CommentNumberOfCounters'],
                'TimeRXOpened'=>$row['TimeRXOpened'],'CommentTimeRXOpened'=>$row['CommentTimeRXOpened'],'TimeBackroomCompleted'=>$row['TimeBackroomCompleted'],
                'CommentTimeBackroomCompleted'=>$row['CommentTimeBackroomCompleted'],'TimeRXCompleted'=>$row['TimeRXCompleted'],
                'CommentTimeRXCompleted'=>$row['CommentTimeRXCompleted'],
                'TimeReportsHung'=>$row['TimeReportsHung'],'CommentTimeReportsHung'=>$row['CommentTimeReportsHung'],
                'TimeBeganReportCheck'=>$row['TimeBeganReportCheck'],'CommentTimeBeganReportCheck'=>$row['CommentTimeBeganReportCheck'],
                'TimeCompletedReportCheck'=>$row['TimeCompletedReportCheck'],'CommentTimeCompletedReportCheck'=>$row['CommentTimeCompletedReportCheck'],
                'TimeLeftBuilding'=>$row['TimeLeftBuilding'],'CommentTimeLeftBuilding'=>$row['CommentTimeLeftBuilding'],
                'LateReasonsCommunicated'=>$row['LateReasonsCommunicated'],'CommentLateReasonsCommunicated' => $row['CommentLateReasonsCommunicated'],
                'StoreDirectorPresent' =>$row['StoreDirectorPresent'],'CommentStoreDirectorPresent'=>$row['CommentStoreDirectorPresent'],
                'Prewalked'=>$row['Prewalked'],'CommentPrewalked'=>$row['CommentPrewalked'],
                'CrewSharpDressed'=>$row['CrewSharpDressed'],'CommentCrewSharpDressed'=>$row['CommentCrewSharpDressed'],'RXManagerPresent'=>$row['RXManagerPresent'],
                'CommentRXManagerPresent'=>$row['CommentRXManagerPresent'],'RXBottlesPrepared'=>$row['RXBottlesPrepared'],'CommentRXBottlesPrepared'=>$row['CommentRXBottlesPrepared'],
                'ShelvesPrepared'=>$row['ShelvesPrepared'],'CommentShelvesPrepared'=>$row['CommentShelvesPrepared'],
                'WDBackroomAssist'=>$row['WDBackroomAssist'],'CommentWDBackroomAssist'=>$row['CommentWDBackroomAssist'],
                'BackroomPrepared'=>$row['BackroomPrepared'],'CommentBackroomPrepared'=>$row['CommentBackroomPrepared'],
                'StoreResolveSatisfied'=>$row['StoreResolveSatisfied'],'CommentStoreResolvedSatisfied'=>$row['CommentStoreResolvedSatisfied'],
                'UnresolvedIssues'=>$row['UnresolvedIssues'],'CommentUnresolvedIssues'=>$row['CommentUnresolvedIssues'],
                'ActualAccuracy'=>$row['ActualAccuracy'],'CommentActualAccuracy'=>$row['CommentActualAccuracy'],
                'StorePreparationRating'=>$row['StorePreparationRating'],'CommentStorePreparationRating'=>$row['CommentStorePreparationRating'],
                'StoreManagersKnowledge'=>$row['StoreManagersKnowledge'],'CommentStoreManagersKnowledge'=>$row['CommentStoreManagersKnowledge'],
                'OverallAccuracyRating'=>$row['OverallAccuracyRating'],'CommentOverallAccuracyRating'=>$row['CommentOverallAccuracyRating'],
                'CrewStoreCondition'=>$row['CrewStoreCondition'],'CommentCrewStoreCondition'=>$row['CommentCrewStoreCondition'],
                'SignOffKey'=>$row['SignOffKey'],'CommentSignOffKey'=>$row['CommentSignOffKey'],'InventoryPassword'=>$row['InventoryPassword']);
            DB::table('tsp_inventory_survey')->insert($values);
        }
        
        $result = $db->query("SELECT * FROM summary");
        while($row = $result->fetch())
        {
            $loc = DB::table('tsp_locations')->select('id')->where('loc','=',$row['Loc'])->first();
            $loc_id=($loc)?$loc->id:0;
            $sub_loc = DB::table('tsp_sub_locations')->select('id')->where('sub_loc','=',$row['Sub_Loc'])->first();
            $sub_loc_id=($sub_loc)?$sub_loc->id:0;
            $values = array('event_id'=>$event->id,'inventory'=>$row['Inventory'],'employee' => $row['Employee'],'loc_id' =>$loc_id,'sub_loc_id'=>$sub_loc_id,
                'category_id'=>$row['Cat'],'records'=>$row['Records'],'pieces'=>$row['Pieces'],'cost'=>$row['Cost'],'price'=>$row['Price']);
            DB::table('tsp_summary')->insert($values);
        }
        
        
        
        if($errorflag)
        {
            DB::table('tsp_event_mdbs')->where('id',$event->id)
                    ->update(array('import_status'=>"Error",
                                    'import_error'=>serialize($errorlog)));
        }else{
            DB::table('tsp_event_mdbs')->where('id',$event->id)->update(array('import_status'=>"Completed"));
        }
         
    }
    
//    public function upload(Request $request,$event_id)
//    {
//        //die($event_id);
//        return view('admin.events.upload_timesheet_mdb',compact('event_id'));
//    }
    
    public function validateEventImportExcel()
    {
        
        $events = Excel::toArray(new ValidateEventImport, request()->file('eventxls'));
        $errors=array();
        $rowcount=0;
        $errorflag=0;
        foreach($events[0] as $row)
        {
            if($rowcount && $row[2])
            {
               
               // $start_time = Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[3]));
                if($row['0'])
                {
                    $employee = Employee::select('id')->where('emp_number','=',$row['0'])->get();
                    if($employee->isEmpty())
                    {
                        $errors[]="Invalid Employee on Row#".($rowcount+1);
                        $errorflag=1;
                    }
                }else{
                    $errors[]="Employee missing on Row#".($rowcount+1);
                    $errorflag=1;
                }
                if($row['1'])
                {
                    $store = Store::select('id')->where('number','=',$row['1'])->get();
                    if($store->isEmpty()){
                        $errors[]="Invalid Store on Row#".($rowcount+1);
                        $errorflag=1;
                    }
                }else{
                    $errors[]="Store missing on Row#".($rowcount+1);
                    $errorflag=1;
                }
                if($row['2'])
                {
                    try {
                        Carbon\Carbon::parse(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['2']));
                    } catch (\Exception $e) {
                        $errors[]="Invalid Event date on Row#".($rowcount+1);;
                        $errorflag=1;
                    }
                }else{
                    $errors[]="Event date missing on Row#".($rowcount+1);
                    $errorflag=1;
                }
                if($row['3'])
                {
                    try {
                        Carbon\Carbon::parse($row['3'])->format('g:i A');
                    } catch (\Exception $e) {
                        //echo '<pre>';print_r($row);die;
                        $errors[]="Invalid Event time on Row#".($rowcount+1);;
                        $errorflag=1;
                    }
                }else{
                    $errors[]="Event time missing on Row#".($rowcount+1);
                    $errorflag=1;
                }

                if(!$row['4']){
                    $errors[]="Run Number is missing on Row#".($rowcount+1);
                    $errorflag=1;
                }
                if($row[5])
                {
                    $areas = explode(',',$row[5]);
                    foreach($areas as $area)
                    {
                        if($area)
                        {
                            $area_id = Area::select('id')->where('area_number','=',$area)->get();
                            if($area_id->isEmpty()){
                                $errors[]="Invalid Area on Row#".($rowcount+1);
                                $errorflag=1;
                            }
                        }
                    }
                }else{
                    $errors[]="Area missing on Row#".($rowcount+1);
                    $errorflag=1;
                }
                if(!$row['6']){
                    $errors[]="Crew count is missing on Row#".($rowcount+1);
                    $errorflag=1;
                }
                if(!$row['7']){
                    $errors[]="Overnight is missing on Row#".($rowcount+1);
                    $errorflag=1;
                }
                if(!$row['8']){
                    $errors[]="PIC is missing on Row#".($rowcount+1);
                    $errorflag=1;
                }
                if(!$row['9']){
                    $errors[]="QC is missing on Row#".($rowcount+1);
                    $errorflag=1;
                }
                if(!$row['10']){
                    $errors[]="Count RX is missing on Row#".($rowcount+1);
                    $errorflag=1;
                }
                if(!$row['11']){
                    $errors[]="Count Backroom is missing on Row#".($rowcount+1);
                    $errorflag=1;
                }
                if(!$row['12']){
                    $errors[]="Road Trip is missing on Row#".($rowcount+1);
                    $errorflag=1;
                }
            }
            $rowcount++;
        }
        //echo '<pre>';print_r($k);die;
        if($errorflag)
            return Response::json(array('status'=>'Error','errors'=>$errors),200);
        else
            return Response::json(array('status'=>'Success'),200);
    }
    
    public function importevents(Request $request)
    {
        if($request->has('eventxls'))
        {
            //echo '<pre>';print_r($request->all());die;
            Excel::import(new EventImport, request()->file('eventxls'));
            //echo '<pre>';print_r($request->all());die;
            if(!isset($request['eventxls']))
                return redirect()->route('admin.events.import')->withErrors('Please upload xls file.');
            $file = $request->file('eventxls');
            $filename = time().$file->getClientOriginalName();
            $destinationPath = 'uploads/eventsexcel/';
            $file->move($destinationPath,$filename);
            return redirect()->route('admin.events.index')->with('successmsg', 'Event imported successfully.');
        }
        //echo '<pre>';print_r($request->all());die;
        return view('admin.events.upload_event');
    }
    
    public function importinvoice(Request $request)
    {
        
        if($request->has('invoicexls')) 
        {
            
            //echo '<pre>';print_r($request->all());die;
            Excel::import(new InvoiceImport, request()->file('invoicexls'));
            //echo '<pre>';print_r($request->all());die;
            if(!isset($request['invoicexls']))
                return redirect()->route('admin.events.invoice')->withErrors('Please upload xls file.');
            $file = $request->file('invoicexls');
            $filename = time().$file->getClientOriginalName();
            $destinationPath = 'uploads/invoiceexcel/';
            $file->move($destinationPath,$filename);
            return redirect()->route('admin.events.index')->with('successmsg', 'Event invoice imported successfully.');
        }
        //echo '<pre>';print_r($request->all());die;
        return view('admin.events.upload_invoice');
    }
    
    public function validateEventInvoiceImportExcel()
    {
        $events = Excel::toArray(new ValidateInvoiceImport, request()->file('invoicexls'));
        $errors=array();
        $rowcount=0;
        $errorflag=0;
        $warning=false;
        foreach($events[0] as $row)
        {
            if($rowcount)
            {
                if($row['0'])
                {
                    $event = Event::select('id')->where('id','=',$row['0'])->get();
                    if($event->isEmpty())
                    {
                        $errors[]="Invalid Event ID on Row#".($rowcount+1);
                        $errorflag=1;
                    }
                }else{
                    $errors[]="Event ID missing on Row#".($rowcount+1);
                    $errorflag=1;
                }
                if($row['1'])
                {
                    $event = Event::select('invoice_amount')->where('id','=',$row['0'])->first();
                    if(@$event->invoice_amount){
                        $errors[]="Invoice already added for Event ID ".$row['0']." on Row#".($rowcount+1);
                        $warning=true;
                    }
                }else{
                    $errors[]="Invoice amount missing on Row#".($rowcount+1);
                    $errorflag=1;
                }
            }
            $rowcount++;
        }
        if($errorflag || $warning)
            return Response::json(array('status'=>'Error','errors'=>$errors,'warning'=>$warning, 'error'=>$errorflag),200);
        else
            return Response::json(array('status'=>'Success'),200);
    }
    
    public function importlodging(Request $request)
    {
        if($request->has('lodgingxls'))
        {
            //echo '<pre>';print_r($request->all());die;
            Excel::import(new LodgingImport, request()->file('lodgingxls'));
            //echo '<pre>';print_r($request->all());die;
            if(!isset($request['lodgingxls']))
                return redirect()->route('admin.events.upload_lodging')->withErrors('Please upload xls file.');
            $file = $request->file('lodgingxls');
            $filename = time().$file->getClientOriginalName();
            $destinationPath = 'uploads/lodgingexcel/';
            $file->move($destinationPath,$filename);
            return redirect()->route('admin.events.index')->with('successmsg', 'Event Lodging imported successfully.');
        }
        //echo '<pre>';print_r($request->all());die;
        return view('admin.events.upload_lodging');
    }
    
    public function validateEventLodgingImportExcel()
    {
        $events = Excel::toArray(new ValidateLodgingImport, request()->file('lodgingxls'));
        $errors=array();
        $rowcount=0;
        $errorflag=0;
        //echo '<pre>';print_r($events);die;
        foreach($events[0] as $row)
        {
            if($rowcount)
            {
                if($row['0'])
                {
                    $event = Event::select('id')->where('id','=',$row['0'])->get();
                    if($event->isEmpty())
                    {
                        $errors[]="Invalid Event ID on Row#".($rowcount+1);
                        $errorflag=1;
                    }
                }else{
                    $errors[]="Event ID missing on Row#".($rowcount+1);
                    $errorflag=1;
                }
                if($row['1'])
                {
                    $event = Event::select('lodging_amount')->where('id','=',$row['0'])->first();
                    if(@$event->lodging_amount){
                        $errors[]="Lodging already added for Event ID ".$row['0']." on Row#".($rowcount+1);
                        $errorflag=1;
                    }
                }else{
                    $errors[]="Lodging amount missing on Row#".($rowcount+1);
                    $errorflag=1;
                }
            }
            $rowcount++;
        }
        //echo '<pre>';print_r($k);die;
        if($errorflag)
            return Response::json(array('status'=>'Error','errors'=>$errors),200);
        else
            return Response::json(array('status'=>'Success'),200);
    }
    
    public function importmeal(Request $request)
    {
        if($request->has('mealxls'))
        {
            //echo '<pre>';print_r($request->all());die;
            Excel::import(new MealImport, request()->file('mealxls'));
            //echo '<pre>';print_r($request->all());die;
            if(!isset($request['mealxls']))
                return redirect()->route('admin.events.upload_meal')->withErrors('Please upload xls file.');
            $file = $request->file('mealxls');
            $filename = time().$file->getClientOriginalName();
            $destinationPath = 'uploads/mealexcel/';
            $file->move($destinationPath,$filename);
            return redirect()->route('admin.events.index')->with('successmsg', 'Event Meal imported successfully.');
        }
        //echo '<pre>';print_r($request->all());die;
        return view('admin.events.upload_meal');
    }
    
    public function validateEventMealImportExcel()
    {
        $events = Excel::toArray(new ValidateMealImport, request()->file('mealxls'));
        $errors=array();
        $rowcount=0;
        $errorflag=0;
        //echo '<pre>';print_r($events);die;
        foreach($events[0] as $row)
        {
            if($rowcount)
            {
                if($row['0'])
                {
                    $event = Event::select('id')->where('id','=',$row['0'])->get();
                    if($event->isEmpty())
                    {
                        $errors[]="Invalid Event ID on Row#".($rowcount+1);
                        $errorflag=1;
                    }
                }else{
                    $errors[]="Event ID missing on Row#".($rowcount+1);
                    $errorflag=1;
                }
                if($row['1'])
                {
                    $event = Event::select('meal_amount')->where('id','=',$row['0'])->first();
                    if(@$event->meal_amount){
                        $errors[]="Meal amount already added for Event ID ".$row['0']." on Row#".($rowcount+1);
                        $errorflag=1;
                    }
                }else{
                    $errors[]="Meal amount missing on Row#".($rowcount+1);
                    $errorflag=1;
                }
            }
            $rowcount++;
        }
        //echo '<pre>';print_r($k);die;
        if($errorflag)
            return Response::json(array('status'=>'Error','errors'=>$errors),200);
        else
            return Response::json(array('status'=>'Success'),200);
    }
    
    public function event_schedule_extra_area(Request $request)
    {
        //echo '<pre>';print_r($request->all());die;
        $selected_areas=array();
        foreach($request['area_meet_time'] as $key=>$emp)
        {
            $selected_areas[]= $key;
        }
        if($request['additional_area'])
            foreach($request['additional_area'] as $are)
                if($are)
                    $selected_areas[]= $are;
        
        
        $areas = DB::table('areas')
            ->whereNotIn('id',$selected_areas);
        if (Gate::allows('isArea') || Gate::allows('isTeam') || Gate::allows('isDistrict')) {
            $areas1 = DB::table('area_user')->where('user_id','=',Auth::id())->get();
            $user_area = array();
            foreach($areas1 as $area)
                $user_area[]=$area->area_id;
            if($user_area)
                $areas = $areas->whereIn('id', $user_area);
        }
        $areas = $areas->get();
        $html='<div class="row arearow'.$request['additional_area_counter'].'">
                    <div class="col-lg-6"><div class="readable-textfield">
                    <select name="additional_area[]" additional_area_count="'.$request['additional_area_counter'].'" class="form-control additional_area_dropdown">';
        if($areas->isEmpty())
            $html.='<option value="">No other area assigned</option>';
        else
            $html.='<option value="">Select Areas</option>';
        foreach($areas as $area)
        {
            $html.='<option value="'.$area->id.'">'.$area->title.'</option>';
        }
        $html.='</select></div></div>
                    <div class="col-lg-6">
                    <div class="col-lg-10 no-padding">
                        <input required="required" type="text" name="additional_area_meet_time[]" class="cus-formControl btn-block required timepicker col-lg-3  additional_area_meet_time'.$request['additional_area_counter'].'">
                    </div>
                    <div class="col-lg-2"><i style="cursor:pointer;" class="fa fa-trash remove_additional_area" additional_counter="'.$request['additional_area_counter'].'" aria-hidden="true"></i></div>
                    </div>
                </div>';    
        
        
        return Response::json(array('html'=>$html),200);
    }
    
    public function calculate_additional_area_distance(Request $request)
    {
        //echo '<pre>';print_r($request->all());die;
        $dist = $this->calculateDistance($request['origin'],$request['origin_type'],$request['destination'],$request['destination_type']);
        //$dist = json_decode($dist);
        //echo '<pre>';print_r($dist);die;
        $duration = (int)($dist->elements[0]->duration->value+($dist->elements[0]->duration->value/3600)*15*60);
        
        $distance = number_format((float)(($dist->elements[0]->distance->value/1000)*0.621371),2,'.','');
        
        //echo '<pre>';print_r($duration);die;
        $event = Event::with(array('store'))->findOrFail($request['event_id']);
        $currentTime = strtotime($event->start_time);
        $area = Area::with(array('city','state'))->where('id',$request['origin'])->first();
        //echo '<pre>';print_r($area->city->name);die;
        $area_time_zone= $this->getTimezone($area->city->name.', '.$area->state->name);
        $event_time_zone= $this->getTimezone($event->store->city->name.', '.$event->store->state->name);
        if($area_time_zone!=$event_time_zone){
            $datetime = $this->converToTz(date("h:i A", $currentTime-$duration),$area_time_zone,$event_time_zone);
            $timezonealert='<div class="error-block">Different Timezone</div>';
        }else{
            $datetime = date("h:i A", $currentTime-$duration);
            $timezonealert='';
        }
        //echo '<pre>';print_r($dist);die;
        return Response::json(array('meet_time'=>$datetime,'timezonealert'=>$timezonealert),200);
    }
    
    public function validateSchedule(Request $request)
    {
        //
        $events_Details = Event::select('date','start_time')->findOrFail($request['event_id']);
        
        $errors=array();
        $errorflag=0;
        $meet_time_arr = array();
        foreach($request['area_meet_time'] as $area_id=>$meettime)
        {
            $meet_time_arr[]=$area_id;
            if(strtotime($events_Details->start_time)<strtotime($meettime))
            {
                $errors[] = "Area meet time can't be after event start time.";
                $errorflag=1;
            }
            
        }
        if(isset($request['additional_area']))
        {
            foreach($request['additional_area'] as $area_id)
            {
                if($area_id)
                    $meet_time_arr[]=$area_id;
            }
        }
        if(isset($request['additional_area_meet_time']))
        {
            foreach($request['additional_area_meet_time'] as $meettime)
            {
                if(strtotime($events_Details->start_time)<strtotime($meettime))
                {
                    $errors[] = "Additional area meet time can't be after event start time.";
                    $errorflag=1;
                }
            }
        }
        //$meet_time_arr = implode(',',$meet_time_arr);
        if(isset($request->emp_id))
        {
            foreach($request->emp_id as $emp_id)
            {
                $events = DB::table('events')
                    ->select('id')
                    ->where('date',$events_Details->date)
                    ->where('start_time',$events_Details->start_time)
                    ->where('id','!=',$request->event_id)
                    ->where('status','!=','inactive')    
                    ->get();
                
                $event_arr = array();
                foreach($events as $event)
                    $event_arr[]=$event->id;
                //echo '<pre>';print_r($event_arr);die;
                $emp_already_scheduled = EventScheduleEmployees::select('event_schedule_employees.id','employees.name')
                        ->where('event_schedule_employees.employee_id','=',$emp_id)
                        ->whereIn('event_schedule_employees.event_id',$event_arr)
                        ->leftJoin('employees','employees.id','=','event_schedule_employees.employee_id')
                        ->first();
                //echo '<pre>';print_r($emp_already_scheduled);die;
                if($emp_already_scheduled)
                {
                    $errors[] = $emp_already_scheduled->name." is already schedule for other event on same date and time.";
                    $errorflag=1;
                }
                $emp_area_id = DB::table('employees')
                    ->select('area_id','name')
                    ->where('id','=',$emp_id)
                    ->first();
                if(!in_array($emp_area_id->area_id,$meet_time_arr))
                {
                    $errors[]='Area meet time for '.$emp_area_id->name." is not available as he/she belongs to different area.";
                    $errorflag=1;
                }
            }
        }

        //New Validations :: October 20, 2021
        $inputTasks = $request->task;
        $task_coll = collect($inputTasks);
        $supervisorCount = $task_coll->filter(function ($value, $key) {
            return \Illuminate\Support\Str::contains(strtolower($value), 'super');
        })->count();
        $driverFromCount = $task_coll->filter(function ($value, $key) {
            return \Illuminate\Support\Str::contains(strtolower($value), 'from');
        })->count();
        $driverToCount = $task_coll->filter(function ($value, $key) {
            return \Illuminate\Support\Str::contains(strtolower($value), 'driver to');
        })->count();
        $inputTasks = [
            'Supervisor' => $supervisorCount,
            'Driver_To' => $driverToCount,
            'Driver_From' => $driverFromCount
        ];
        //supervisor validation
        if($inputTasks['Supervisor'] > 1){
            $errors[]='There must be only one person marked as supervisor';
            $errorflag=1;
        }else if($inputTasks['Supervisor'] == 0){                
            $errors[]='There must be one person marked as supervisor';
            $errorflag=1;
        }
        //Driver T0 validation
        if($inputTasks['Driver_To'] == 0){
            $errors[]='There must be at least one person marked as driver to';
            $errorflag=1;
        }
        //Driver From validation
        if($inputTasks['Driver_From'] == 0){
            $errors[]='There must be at least one person marked as driver from';
            $errorflag=1;
        }
        //// New Validations :: October 20, 2021
        
        //echo '<pre>';print_r($k);die;
        if($errorflag)
            return Response::json(array('status'=>'Error','errors'=>$errors),200);
        else
            return Response::json(array('status'=>'Success'),200);
    }
    
    public function pendingEventList(Request $request,$id){
        
        $event = DB::table('events')
                ->where('id', '=', $id)
                ->first();
        $date_from = date('Y-m-d',strtotime($event->date));
        $date_to = date('Y-m-d',strtotime($date_from." +5 days"));
        //DB::enableQueryLog();
        $pending_events = Event::select('events.*','stores.name as storename')
                ->leftJoin('stores','stores.id','=','events.store_id')
                ->where('events.status','=','Pending')
                //->where('events.date','>=',date('Y-m-d'))
                ->whereBetween('events.date',[$date_from,$date_to]);
                //->groupBy('events.store_id');
        if (Gate::allows('isArea') || Gate::allows('isTeam') || Gate::allows('isDistrict')) {
            $user_assigned_areas = DB::table('area_user')->where('user_id','=',Auth::id())->get();
            $user_areas = array();
            foreach($user_assigned_areas as $area)
                $user_areas[]=$area->area_id;
            $user_assigned_areas = EventAreas::whereIn('area_id', $user_areas)->get();

            $area_event = array();
            foreach($user_assigned_areas as $area)
                $area_event[]=$area->event_id;
            $pending_events = $pending_events->whereIn('events.id',$area_event);
        }
        $pending_events = $pending_events->orderBy('events.date','asc')->orderBy('events.start_time','asc')->get()->toArray();
        //dd(DB::getQueryLog());
        $pending_event = array();
        //echo '<pre>'; print_r($pending_events);
        foreach ($pending_events as $value) {
            $pending_event[$value['id']] = $value['id'].'-'.$value['storename'].'-'.date('m-d-Y',strtotime($value['date'])).'-'.date('h:i A',strtotime($value['start_time']));
        }
        $html = '';
        foreach($pending_event as $key=>$event)
            $html.='<option value="'.$key.'">'.$event.'</option>';
        //echo '<pre>';print_r($pending_event);die;
        return Response::json(['status' => 'success','pending_events'=>$html],200);
    }
}