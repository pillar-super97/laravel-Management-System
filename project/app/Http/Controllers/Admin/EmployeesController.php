<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Employee;
use App\Models\EmployeeAvailabilityDays;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreEmployeesRequest;
use App\Http\Requests\Admin\UpdateEmployeesRequest;
use App\Http\Controllers\Traits\FileUploadTrait;
use Illuminate\Support\Facades\DB;
use App\Models\Division;
use App\Models\Area;
use App\Models\Jsa;
use App\Models\Client;
use Illuminate\Support\Facades\Response;
use GuzzleHttp;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Timesheet;
use App\Models\TimesheetVehicle;
use App\Models\TimesheetData;
use App\Exports\EmployeeSchedulesExport;
use Illuminate\Support\Facades\Mail;


class EmployeesController extends Controller
{
    use FileUploadTrait;

    /**
     * Display a listing of Employee.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (! Gate::allows('employee_view')) {
            return abort(401);
        }
        //DB::enableQueryLog();
        $employees = Employee::with(array('area','jsa','availability_days'))
                ->where(function($q) {
                    $q->where('status', '=', 'active')
                    ->orWhere('status','=', 'Inactive');
                });
                //->where('status','=', 'active')
                //->orwhere('status','=', 'Inactive');
       
        if (Gate::allows('isArea') || Gate::allows('isTeam') || Gate::allows('isDistrict')) {
            $areas = DB::table('area_user')->where('user_id','=',Auth::id())->get();
            $user_area = array();
            foreach($areas as $area)
                $user_area[]=$area->area_id;
            //$user_area=implode(',',$user_area);
            $employees->whereIn('area_id', $user_area);
        }
        
        $employees->orderBy('employees.last_name','asc');
        if (request('show_deleted') == 1) {
            if (! Gate::allows('employee_delete')) {
                return abort(401);
            }
            $employees = $employees->onlyTrashed()->get();
        } else {
            $employees = $employees->get();
        }
        //dd(DB::getQueryLog());die;
        //print_r($employees);die;
        //$users = User::with(array('city','state','country','parentuser'))->where('user_type', '=', 'member')->get();
        $pending_time_entries = DB::table('employees_schedule_kronos_queue')
                ->where('status','=',1)
                ->get();
        if($pending_time_entries->isEmpty())
            $pending_timesheets=0;
        else
            $pending_timesheets=1;
        return view('admin.employees.index', compact('employees','pending_timesheets'));
    }

    /**
     * Show the form for creating new Employee.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('employee_create')) {
            return abort(401);
        }
        $states = DB::table('states')->pluck('name','id');
        $clients = DB::table('clients')->pluck('name','id');
        //$divisions = DB::table('divisions')->pluck('name','id');
        $employees = DB::table('employees')->where('status','=','Active')->pluck('name','id');
        
        return view('admin.employees.create', compact('states','clients','employees'));
    }

    /**
     * Store a newly created Employee in storage.
     *
     * @param  \App\Http\Requests\StoreEmployeesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreEmployeesRequest $request)
    {
        if (! Gate::allows('employee_create')) {
            return abort(401);
        }
        
        $employee = Employee::create($request->all());
        //echo '<pre>';print_r($request->all());die;
        if($request->days_avai_to_schedule)
        {
            foreach($request->days_avai_to_schedule as $day)
            {
                EmployeeAvailabilityDays::create([
                    'employee_id' => $employee->id,
                    'days' => $day,
                ]);
            }
        }
        //  echo "<pre>";
//        print_r($request->all());
//        die;
        return redirect()->route('admin.employees.index')->with('successmsg', 'Employee added successfully.');
    }


    /**
     * Show the form for editing Employee.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('employee_edit')) {
            return abort(401);
        }
        
        $employee = Employee::with(array('area','jsa','availability_days'))->findOrFail($id);
        //echo $employee->state;die;
//        echo "<pre>";
//        print_r($employee->schedule_availability_days);
//        die;  
//        if($request->ajax()){
//            return "AJAX";
//        }else{
        //echo '<pre>';print_r($employee->availability_days);die;
            return view('admin.employees.edit', compact('employee'));
        //}
    }

    /**
     * Update Employee in storage.
     *
     * @param  \App\Http\Requests\UpdateEmployeesRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateEmployeesRequest $request, $id)
    {
        if (! Gate::allows('employee_edit')) {
            return abort(401);
        }
        if(isset($request['overnight']) && $request['overnight'])
            $request->merge(['overnight' => 1]);
        else
            $request->merge(['overnight' => 0]);
//        echo "<pre>";
//        print_r($request->all());
//        die;
        $employee = Employee::findOrFail($id);
        $employee->update($request->all());
        EmployeeAvailabilityDays::where('employee_id', '=',$id)->delete();
        foreach($request->employee_availability as $day)
        {
            EmployeeAvailabilityDays::create([
                'employee_id' => $id,
                'days' => $day,
            ]);
        }
        return redirect()->route('admin.employees.index')->with('successmsg', 'Employee availability updated successfully.');
    }


    /**
     * Display Employee.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        if (! Gate::allows('employee_view')) {
            return abort(401);
        }
        $employee = Employee::with(array('area','jsa'))->findOrFail($id);
        if($request->ajax()){
            $states = DB::table('states')->pluck('name','id');
            $cities = DB::table('cities')->where('state_id','=',$employee->state_id)->pluck('name','id');
            return Response::json(array('employee'=>$employee,'states'=>$states,'cities'=>$cities),200);
        }else{
//            /echo '<pre>';print_r($employee);die;
            return view('admin.employees.show',compact('employee'));
        }
    }


    /**
     * Remove Employee from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('employee_delete')) {
            return abort(401);
        }
        $employee = Employee::findOrFail($id);
        $employee->delete();

        return redirect()->route('admin.employees.index')->with('successmsg', 'Employee set as inactive successfully.');
    }

    /**
     * Delete all selected Employee at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('employee_delete')) {
            return abort(401);
        }
        if ($request->input('ids')) {
            $entries = Employee::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }


    /**
     * Restore Employee from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('employee_delete')) {
            return abort(401);
        }
        $employee = Employee::onlyTrashed()->findOrFail($id);
        $employee->restore();

        return redirect()->route('admin.employees.index')->with('successmsg', 'Employee set as active successfully.');
    }

    /**
     * Permanently delete Employee from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('employee_delete')) {
            return abort(401);
        }
        $employee = Employee::onlyTrashed()->findOrFail($id);
        $employee->forceDelete();

        return redirect()->route('admin.employees.index')->with('successmsg', 'Employee deleted successfully.');
    }
    
    
    
    public function getDivisionByClient(Request $request) {
        $divisions = Division::where('client_id','=',$request->client_id)->where('status','=', 'active')->get();
        return Response::json(array('divisions'=>$divisions),200);
    }
    
    public function importEmployees(Request $request) {
//        if (!Gate::allows('isAdmin')) {
//            if (!Gate::allows('isCorporate'))
//                return abort(401);
//        }
        $postRequest = array('credentials' => array(
                    'username'   => 'apiuser',
                    'password' => 'MSIpassw@rd12',
                    'company'  => '6163534'
                ));
        $cURLConnection = curl_init('http://secure.entertimeonline.com/ta/rest/v1/login');
        curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'api-key: 4zs1mg5vsl410nq8guuxkbb7v648o2pt',
            'Accept:application/json'
        ));
        curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, json_encode($postRequest));
        curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
        $apiResponse = curl_exec($cURLConnection);
        curl_close($cURLConnection);
        $jsonArrayResponse = json_decode($apiResponse);
        //echo '<pre>';print_r($jsonArrayResponse->token);die;
        $token = 'Bearer '.$jsonArrayResponse->token;
        //$token = 'Bearer eyJhbGciOiJIUzUxMiJ9.eyJleHAiOjE1ODUyMTQyNTUsImlhdCI6MTU4NTIxMDY1NSwic2lkIjoiMTg1NTc4MjE5MTgiLCJhaWQiOiI4NjU4MTI1OTUwIiwiY2lkIjoiNjcxMjY2NTIiLCJ0eXBlIjoiciJ9.9aaX0sFrtRKQ56XyrECjC2ROuiyOI0pdVdDh-6A92i4edN-arJRMHNtDtLplG3J7MqK8c1Da8klUy2bbBfRnrw';
        $cURLConnection1 = curl_init('http://secure.entertimeonline.com/ta/rest/v1/report/saved/71090837');
        curl_setopt($cURLConnection1, CURLOPT_HTTPHEADER, array(
            'Authentication: '.$token,
            'Accept:application/xml',
            'Content-Type:application/json'
        ));
        //curl_setopt($cURLConnection1, CURLOPT_POSTFIELDS, json_encode($postRequest));
        curl_setopt($cURLConnection1, CURLOPT_RETURNTRANSFER, true);
        $apiResponse1 = curl_exec($cURLConnection1);
        curl_close($cURLConnection1);
        //$jsonArrayResponse = json_decode($apiResponse1);
        $data = simplexml_load_string($apiResponse1);
        $con = json_encode($data); 
        $newArr = json_decode($con, true); 
        //echo '<pre>';
       // echo '<pre>';print_r($newArr['header']['col']);die;
        $current_employees=array();
        foreach($newArr['body']['row'] as $emp)
        {   
            //$empl = Employee::where('emp_number', '=', $emp['col'][1])->first();
            $current_employees[] = $emp['col'][1];
        }
        
        // if ($empl === null) {
        // // user doesn't exist
        // echo '<pre>';print_r("Prabhaat Kumar");die;
        // }else{
        //     echo '<pre>';print_r("Prabhaat Kumar & happy");die;
        // }
        $available_employees = Employee::get();
        foreach($available_employees as $res){
            if(in_array($res->emp_number,$current_employees))
            {   
                $where = array('id' => $res['id']);
                $updateArr = ['status' => $emp['col'][4]];
                $emp_status  = Employee::where($where)->update($updateArr);
                //echo '<pre>';print_r($emp_status);die;
                //continue;
            }else{
                $where = array('id' => $res['id']);
                $updateArr = ['status' => 'Terminated'];
                $emp_status  = Employee::where($where)->update($updateArr);
                DB::table('event_schedule_employees')
                        ->leftJoin('events','events.id','=','event_schedule_employees.employee_id')
                        ->where('events.status', '=',"Scheduled")
                        ->where('event_schedule_employees.employee_id', '=',$res['id'])
                        ->delete();
                //echo $res->emp_number.'-'.$res->name.' terminated'.'<br>';
            }
        }
       // echo '<pre>';print_r($newArr['body']['row']);die;
        
        foreach($newArr['body']['row'] as $emp)
        {
            if($emp['col'][4]=="Active" || $emp['col'][4]=="Inactive")
            {
              
            $emp_number = $emp['col'][1];
           // echo '<pre>';print_r("A");print_r($emp_number);die;

            if($emp['col'][6])
                $ssn = str_replace('-','',$emp['col'][6]);
            else
                $ssn = '';
            $name = ($emp['col'][2])?$emp['col'][2].' ':'';
            $name.=($emp['col'][3])?$emp['col'][3]:'';
            $area_manager=NULL;
            if($emp['col'][17])
            {
                $area_manager = Employee::where('emp_number','=',$emp['col'][17])->first();
                //print_r($area_manager);die;
                $area_manager = $area_manager->id;
            }
            $district_manager=NULL;
            if($emp['col'][18])
            {
                $district_manager = Employee::where('emp_number','=',$emp['col'][18])->first();
//                echo $district_manager->id;
//                echo '<pre>';print_r($district_manager);die;
                $district_manager = $district_manager->id;
            }
            if($emp['col'][20])
            {
                $hire_date = date('Y-m-d',strtotime($emp['col'][20]));
            }else
                $hire_date=NULL;
            //echo $hire_date;
//            if($emp['col'][14] && !is_array($district_manager))
//                $gender = $emp['col'][14];
            $data = array(  'name'=>$name,
                'emp_number'=>($emp['col'][1])?$emp['col'][1]:'',
                'first_name'=>($emp['col'][2])?$emp['col'][2]:'',
                'last_name'=>($emp['col'][3])?$emp['col'][3]:'',
                'status'=>($emp['col'][4])?$emp['col'][4]:'',
                'manager'=>($emp['col'][5])?$emp['col'][5]:'',
                'ss_no'=>$ssn,
                'title'=>($emp['col'][7])?$emp['col'][7]:'',
                'benchmark'=>($emp['col'][8])?$emp['col'][8]:'0',
                'is_driver'=>($emp['col'][9])?$emp['col'][9]:'0',
                'is_rx'=>($emp['col'][10])?$emp['col'][10]:'0',
                'is_crew_leader'=>($emp['col'][11])?$emp['col'][11]:'0',
                'payrate'=>($emp['col'][12])?$emp['col'][12]:'0',
                'area_id'=>($emp['col'][13])?$emp['col'][13]:'0',
                'jsa_id'=>($emp['col'][14])?$emp['col'][14]:'0',
                'email'=>($emp['col'][15])?$emp['col'][15]:'',
                'kronos_account_id'=>($emp['col'][16])?$emp['col'][16]:'0',
                'area_manager'=>$area_manager,
                'district_manager'=>$district_manager,
                'gender'=>($emp['col'][19])?$emp['col'][19]:'',
                'hire_date'=>$hire_date,
                'cell_phone'=>($emp['col'][21])?$emp['col'][21]:'',
                'home_phone'=>($emp['col'][22])?$emp['col'][22]:'',
                'work_phone'=>($emp['col'][23])?$emp['col'][23]:'',
                );
               
               
                
                    
                    // echo '<pre>';print_r($users1);die;
            if($data['benchmark']==0 || $data['benchmark']==NULL)
                $data['benchmark'] = 50;
            if($data['payrate']==NULL)
                $data['payrate'] = 12;
            //echo '<pre>';print_r($data);
            if($emp['col'][13])
            {
                $area = Area::where('area_number', '=',$emp['col'][13])->first();
                if($area)
                {
                    $data['area_id'] = $area->id;
                }else{
                    $area1 = Area::create(array('title'=>$emp['col'][13],'area_number'=>$emp['col'][13]));
                    $data['area_id'] = $area1->id;
                }
            }
           // echo $data['area_id'];echo '<br>';
            if($emp['col'][14])
            {
                $jsa = Jsa::where('area_number', '=',$emp['col'][14])->where('area_id', '=',$data['area_id'])->first();
                if($jsa)
                {
                    $data['jsa_id'] = $jsa->id;
                }else{
                    $jsa1 = Jsa::create(array('title'=>$emp['col'][14],'area_number'=>$emp['col'][14],'area_id'=>$data['area_id']));
                    $data['jsa_id'] = $jsa1->id;
                }
            }
            $employee = Employee::where('emp_number', '=',$emp_number)->first();
            
            //$employee = DB::table('employees')->where('emp_number', '=', $emp_number)->first();
            //echo '<pre>';print_r($data);
            if($employee)
            {
                //echo 'if';
                //$data['id'] = $employee->id;
                //print_r($data);
                unset($data['benchmark']);
                DB::table('employees')->where('emp_number',$emp_number)->update($data);
                //$employee->update($data);
            }else{
                //echo 'else';
                $employee = Employee::create($data);
                $emp_id=$employee->id;
                $flagged_timesheet_driver = TimesheetVehicle::select('id','driver_to','driver_from')
                                            ->where('driver_to','=',$data['ss_no'])
                                            ->orWhere('driver_from',$data['ss_no'])
                                            ->first();
                if($flagged_timesheet_driver)
                {
                    if($flagged_timesheet_driver['driver_to']==$data['ss_no'] && $flagged_timesheet_driver['driver_from']==$data['ss_no'])
                    {
                        $where = array('driver_to' => $data['ss_no'],'is_flagged'=>1);
                        $updateArr = ['driver_to' => $emp_id,'driver_from'=>$emp_id,'is_flagged'=>0];
                        $timesheetvehicle  = TimesheetVehicle::where($where)->update($updateArr);
                    }elseif($flagged_timesheet_driver['driver_to']==$data['ss_no'])
                    {
                        $where = array('driver_to' => $data['ss_no'],'is_flagged'=>1);
                        $updateArr = ['driver_to' => $emp_id,'is_flagged'=>0];
                        $timesheetvehicle  = TimesheetVehicle::where($where)->update($updateArr);
                    }elseif($flagged_timesheet_driver['driver_from']==$data['ss_no'])
                    {
                        $where = array('driver_from' => $data['ss_no'],'is_flagged'=>1);
                        $updateArr = ['driver_from'=>$emp_id,'is_flagged'=>0];
                        $timesheetvehicle  = TimesheetVehicle::where($where)->update($updateArr);
                    }
                }
                
                $flagged_timesheet_data = TimesheetData::select('id','employee_id')
                                            ->where('employee_id','=',$data['ss_no'])
                                            ->first();
                if($flagged_timesheet_data)
                {
                    $where = array('employee_id' => $data['ss_no'],'is_flagged'=>1);
                    $updateArr = ['employee_id'=>$emp_id,'is_flagged'=>0];
                    $timesheetdata  = TimesheetData::where($where)->update($updateArr);
                }
            }
            //die();
            }
        }
        $users1 = Employee::where('emp_number', '=', $data['emp_number'])->first();
        $users1 = $users1->id;
    
        $users2 = EmployeeAvailabilityDays::where('employee_id','=',$users1)->first();
        //echo '<pre>';print_r($users1);die;
        if($users2 === null){
// <<<<<<< HEAD
            $ddays= array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');
// =======
            $ddays= array('Monday','Tuesday','Wednesday','Thursday','Friday');
// >>>>>>> staging
            foreach ($ddays as $key => $value) {
            //echo $value;die; // username: John
            EmployeeAvailabilityDays::updateOrCreate([
                'employee_id' => $users1,
                'days' =>$value,
            ]);
          }

        }
        
        // $user_detail = array(
        //     'name'            => 'Admin',
        //     'email'           => "lancebowser@msi-inv.com",
        //     'email_content'   => 'The employee data successfully pulled from Kronos.',
        //     'mail_from_email' => env('MAIL_FROM'),
        //     'mail_from'       => env('MAIL_NAME'),
        //     'subject'         => 'Employee data pulled from Kronos.'
        // );
        // $user_single = (object) $user_detail;
        // Mail::send('emails.kronos_timesheet_error',['user' => $user_single], function ($message) use ($user_single) {
        //         $message->from($user_single->mail_from_email,$user_single->mail_from);
        //         $message->to($user_single->email, $user_single->name)->subject($user_single->subject);
        //         $message->replyTo($user_single->mail_from_email,$user_single->mail_from);
        // });
                        
        //echo '<pre>';print_r($data);
        return redirect()->route('admin.employees.index')->with('successmsg', 'Employees imported from Kronos successfully.');
    }
    
    public function updateFlaggedTimesheet(Request $request)
    {
        if (!Gate::allows('isAdmin')) {
            if (!Gate::allows('isCorporate'))
                return abort(401);
        }
        $employees = Employee::select('id','ss_no')
                    ->get();
        //echo '<pre>';
        foreach($employees as $employee){
            //print_r($employee);
            if($employee['ss_no'])
            {
                $where = array('driver_from' => $employee['ss_no']);
                $updateArr = ['driver_from'=>$employee['id'],'is_flagged'=>0];
                $timesheetvehicle  = TimesheetVehicle::where($where)->update($updateArr);
                $where = array('driver_to' => $employee['ss_no']);
                $updateArr = ['driver_to'=>$employee['id'],'is_flagged'=>0];
                $timesheetvehicle  = TimesheetVehicle::where($where)->update($updateArr);
                $where = array('employee_id' => $employee['ss_no']);
                $updateArr = ['employee_id'=>$employee['id'],'is_flagged'=>0];
                $timesheetvehicle  = TimesheetData::where($where)->update($updateArr);
            }
        }
        $flagged_timesheets = Timesheet::select('id')
                            ->where('is_flagged','=',1)
                            ->get();
        foreach($flagged_timesheets as $flagged_timesheet){
            $flagged_timesheet_vehicle_exist = TimesheetVehicle::select('id')
                                ->where('is_flagged','=',1)
                                ->where('timesheet_id','=',$flagged_timesheet['id'])
                                ->get();
            if($flagged_timesheet_vehicle_exist->isEmpty()){
                $flagged_timesheet_data_exist = TimesheetData::select('id')
                                            ->where('is_flagged','=',1)
                                            ->where('timesheet_id','=',$flagged_timesheet['id'])
                                            ->get();
                if($flagged_timesheet_data_exist->isEmpty())
                {
                    $where = array('id' => $flagged_timesheet['id'],'is_flagged'=>1);
                    $updateArr = ['is_flagged'=>0];
                    $timesheetdata  = Timesheet::where($where)->update($updateArr);
                }
            }
        }
        return redirect()->route('admin.employees.index')->with('successmsg', 'Flagged timesheet updated successfully.');
    }

    public function importEmployees1(Request $request) {
        
        
        $newArr['body']['row'][0]['col']=array(1, 17602, 'missing1', 'missing1',
             'Active', 'test', 594677254, 'Auditor', 100, 1,
             1, 1, '$64.70237', 99, 1,'test2@inv.com',
             8657223018, 1975, Array());
        $newArr['body']['row'][1]['col']=array(1, 27602, 'missing2', 'missing2',
             'Active', 'test1', 437730116, 'Auditor', 100, 1,
             1, 1, '$64.70237', 99, 1,'test@inv.com',
             8657223018, 1975, Array());
        //echo '<pre>';
        //print_r($newArr['header']['col']); 
        //print_r($newArr['body']['row']);die;
        foreach($newArr['body']['row'] as $emp)
        {
            $emp_number = $emp['col'][1];
            if($emp['col'][6])
                $ssn = str_replace('-','',$emp['col'][6]);
            else
                $ssn = '';
            $name = ($emp['col'][2])?$emp['col'][2].' ':'';
            $name.=($emp['col'][3])?$emp['col'][3]:'';
            $area_manager=NULL;
            if($emp['col'][17])
            {
                $area_manager = Employee::where('emp_number','=',$emp['col'][17])->first();
                //print_r($area_manager);die;
                $area_manager = $area_manager->id;
            }
            $district_manager=NULL;
            if($emp['col'][18])
            {
                $district_manager = Employee::where('emp_number','=',$emp['col'][18])->first();
//                echo $district_manager->id;
//                echo '<pre>';print_r($district_manager);die;
                $district_manager = $district_manager->id;
            }
            $data = array(  'name'=>$name,
                'emp_number'=>($emp['col'][1])?$emp['col'][1]:'',
                'first_name'=>($emp['col'][2])?$emp['col'][2]:'',
                'last_name'=>($emp['col'][3])?$emp['col'][3]:'',
                'status'=>($emp['col'][4])?$emp['col'][4]:'',
                'manager'=>($emp['col'][5])?$emp['col'][5]:'',
                'ss_no'=>$ssn,
                'title'=>($emp['col'][7])?$emp['col'][7]:'',
                'benchmark'=>($emp['col'][8])?$emp['col'][8]:'',
                'is_driver'=>($emp['col'][9])?$emp['col'][9]:'0',
                'is_rx'=>($emp['col'][10])?$emp['col'][10]:'0',
                'is_crew_leader'=>($emp['col'][11])?$emp['col'][11]:'0',
                'payrate'=>($emp['col'][12])?$emp['col'][12]:'',
                'area_id'=>($emp['col'][13])?$emp['col'][13]:'0',
                'jsa_id'=>($emp['col'][14])?$emp['col'][14]:'0',
                'email'=>($emp['col'][15])?$emp['col'][15]:'',
                'kronos_account_id'=>($emp['col'][16])?$emp['col'][16]:'0',
                'area_manager'=>$area_manager,
                'district_manager'=>$district_manager
                );
            if($emp['col'][13])
            {
                $area = Area::where('area_number', '=',$emp['col'][13])->first();
                if($area)
                {
                    $data['area_id'] = $area->id;
                }else{
                    $area1 = Area::create(array('title'=>$emp['col'][13],'area_number'=>$emp['col'][13]));
                    $data['area_id'] = $area1->id;
                }
            }
           // echo $data['area_id'];echo '<br>';
            if($emp['col'][14])
            {
                $jsa = Jsa::where('area_number', '=',$emp['col'][14])->where('area_id', '=',$data['area_id'])->first();
                if($jsa)
                {
                    $data['jsa_id'] = $jsa->id;
                }else{
                    $jsa1 = Jsa::create(array('title'=>$emp['col'][14],'area_number'=>$emp['col'][14],'area_id'=>$data['area_id']));
                    $data['jsa_id'] = $jsa1->id;
                }
            }
            $employee = Employee::where('emp_number', '=',$emp_number)->first();
            
            //$employee = DB::table('employees')->where('emp_number', '=', $emp_number)->first();
            if($employee)
            {
                //echo 'if';
                //$data['id'] = $employee->id;
                //print_r($data);
                DB::table('employees')->where('emp_number',$emp_number)->update($data);
                //$employee->update($data);
            }else{
                //echo 'else';
                $employee = Employee::create($data);
                $emp_id=$employee->id;
                $flagged_timesheet_driver = TimesheetVehicle::select('id','driver_to','driver_from')
                                            ->where('driver_to','=',$data['ss_no'])
                                            ->orWhere('driver_from',$data['ss_no'])
                                            ->first();
                if($flagged_timesheet_driver)
                {
                    if($flagged_timesheet_driver['driver_to']==$data['ss_no'] && $flagged_timesheet_driver['driver_from']==$data['ss_no'])
                    {
                        $where = array('driver_to' => $data['ss_no'],'is_flagged'=>1);
                        $updateArr = ['driver_to' => $emp_id,'driver_from'=>$emp_id,'is_flagged'=>0];
                        $timesheetvehicle  = TimesheetVehicle::where($where)->update($updateArr);
                    }elseif($flagged_timesheet_driver['driver_to']==$data['ss_no'])
                    {
                        $where = array('driver_to' => $data['ss_no'],'is_flagged'=>1);
                        $updateArr = ['driver_to' => $emp_id,'is_flagged'=>0];
                        $timesheetvehicle  = TimesheetVehicle::where($where)->update($updateArr);
                    }elseif($flagged_timesheet_driver['driver_from']==$data['ss_no'])
                    {
                        $where = array('driver_from' => $data['ss_no'],'is_flagged'=>1);
                        $updateArr = ['driver_from'=>$emp_id,'is_flagged'=>0];
                        $timesheetvehicle  = TimesheetVehicle::where($where)->update($updateArr);
                    }
                }
                
                $flagged_timesheet_data = TimesheetData::select('id','employee_id')
                                            ->where('employee_id','=',$data['ss_no'])
                                            ->first();
                if($flagged_timesheet_data)
                {
                    $where = array('employee_id' => $data['ss_no'],'is_flagged'=>1);
                    $updateArr = ['employee_id'=>$emp_id,'is_flagged'=>0];
                    $timesheetdata  = TimesheetData::where($where)->update($updateArr);
                }
                
                
               
                
            }
            //die();
        }
        
        $flagged_timesheets = Timesheet::select('id')
                            ->where('is_flagged','=',1)
                            ->get();
        foreach($flagged_timesheets as $flagged_timesheet){
            $flagged_timesheet_vehicle_exist = TimesheetVehicle::select('id')
                                ->where('is_flagged','=',1)
                                ->where('timesheet_id','=',$flagged_timesheet['id'])
                                ->get();
            if($flagged_timesheet_vehicle_exist->isEmpty()){
                $flagged_timesheet_data_exist = TimesheetData::select('id')
                                            ->where('is_flagged','=',1)
                                            ->where('timesheet_id','=',$flagged_timesheet['id'])
                                            ->get();
                if($flagged_timesheet_data_exist->isEmpty())
                {
                    $where = array('id' => $flagged_timesheet['id'],'is_flagged'=>1);
                    $updateArr = ['is_flagged'=>0];
                    $timesheetdata  = Timesheet::where($where)->update($updateArr);
                }
            }
        }
        die;
        //echo '<pre>';print_r($data);
        return redirect()->route('importEmployees')->with('successmsg', 'Employees imported from Kronos successfully.');
    }
    
    public function export_schedules_to_kronos(Request $request)
    {
//        if (!Gate::allows('isAdmin')) {
//            if (!Gate::allows('isCorporate'))
//                return abort(401);
//        }
        $export_file_name = 'Employees-Schedules-'.date('Y-m-d h-i-s-A').'.csv';
        Excel::store(new EmployeeSchedulesExport(2018), $export_file_name,'media');
        $postRequest = array('credentials' => array(
                    'username'   => 'apiuser',
                    'password' => 'MSIpassw@rd12',
                    'company'  => '6163534'
                ));
        $cURLConnection = curl_init('http://secure.entertimeonline.com/ta/rest/v1/login');
        curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'api-key: 4zs1mg5vsl410nq8guuxkbb7v648o2pt',
            'Accept:application/json'
        ));
        curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, json_encode($postRequest));
        curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
        $apiResponse = curl_exec($cURLConnection);
        curl_close($cURLConnection);
        $jsonArrayResponse = json_decode($apiResponse);
        //echo '<pre>';print_r($jsonArrayResponse);die;
        $token = 'Bearer '.$jsonArrayResponse->token;
//        Send Timesheet to Kronos       
        $filename = base_path().'/public/uploads/'.$export_file_name;
        $cfile = $this->getCurlValue($filename,'text/csv',$export_file_name);
        $data = array('file' => $cfile);
        $ch = curl_init('https://secure3.saashr.com/ta/rest/v1/import/216');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authentication: '.$token,
            'Accept:application/xml',
            'Content-Type:multipart/form-data'
        ));
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        $result = curl_exec($ch);
        $header_info = curl_getinfo($ch,CURLINFO_HEADER_OUT);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($result, 0, $header_size);
        $body = substr($result, $header_size);
        curl_close($ch);
        $headers = [];
        $result = rtrim($result);
        $data = explode("\n",$result);
        $headers['status'] = $data[0];
        array_shift($data);
        foreach($data as $part){
            $middle = explode(":",$part,2);
            if ( !isset($middle[1]) ) { $middle[1] = null; }
            $headers[trim($middle[0])] = trim($middle[1]);
        }
        $location = $headers['Location'];
        $ch = curl_init('https://secure3.saashr.com/ta/rest/v1'.$location);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authentication: '.$token,
            'Accept:application/xml'
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        $statusResponse = curl_exec($ch);
        $array_data = json_decode(json_encode(simplexml_load_string($statusResponse)), true);
        //echo '<pre>';print_r($array_data);die;
        if(isset($array_data['status']) && $array_data['status']=="running")
        {
            $pointdata2 = array('excelsheet_name'=>$export_file_name,'location'=>$location,"status"=>1,'notify_to'=>env('ADMIN_EMAIL'),'is_notified'=>0);
            DB::table('employees_schedule_kronos_queue')->insert($pointdata2);
            return redirect()->route('admin.employees.index')->with('successmsg', 'Server is busy now. Please try after some time.');
        }elseif(count($array_data['import_message']))
        {
            $is_error=0;
            $html='<ul>';
            foreach($array_data['import_message'] as $row)
            {
                if($row['type']=="Error")
                {
                    $is_error=1;
                    $html.='<li>'.$row['message'].' on line number '.$row['line_number'].' at column '.$row['column_number'].'</li>';
                }
            }
            $html.='</ul>';
            if($is_error)
            {
                echo $html;exit;
                return redirect()->route('admin.employees.index')->with('successmsg', $html);
            }else{
                return redirect()->route('admin.employees.index')->with('successmsg', 'Employee schedule pushed into Kronos successfully.');
            }
        }
        
        exit;  
        
        exit;  
    }
    
    function sync_schedules_to_kronos()
    {
//        if (!Gate::allows('isAdmin')) {
//            if (!Gate::allows('isCorporate'))
//                return abort(401);
//        }
        $postRequest = array('credentials' => array(
                    'username'   => 'apiuser',
                    'password' => 'MSIpassw@rd12',
                    'company'  => '6163534'
                ));
        $cURLConnection = curl_init('http://secure.entertimeonline.com/ta/rest/v1/login');
        curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'api-key: 4zs1mg5vsl410nq8guuxkbb7v648o2pt',
            'Accept:application/json'
        ));
        curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, json_encode($postRequest));
        curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
        $apiResponse = curl_exec($cURLConnection);
        curl_close($cURLConnection);
        $jsonArrayResponse = json_decode($apiResponse);
        //echo '<pre>';print_r($jsonArrayResponse);die;
        if(isset($jsonArrayResponse))
        {
        $token = 'Bearer '.$jsonArrayResponse->token;
        
        $pending_schedules = DB::table('employees_schedule_kronos_queue')
                ->where('employees_schedule_kronos_queue.status','=',1)
                ->orderBy('id','asc')
                ->get();
        // dd($pending_schedules);
        if(count($pending_schedules))
        {
            foreach($pending_schedules as $pending_schedule)
            {
                $ch = curl_init('https://secure3.saashr.com/ta/rest/v1'.$pending_schedule->location);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Authentication: '.$token,
                    'Accept:application/xml'
                ));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_VERBOSE, 1);
                curl_setopt($ch, CURLINFO_HEADER_OUT, true);
                $statusResponse = curl_exec($ch);
                $array_data = json_decode(json_encode(simplexml_load_string($statusResponse)), true);
                $is_error=0;
                if(isset($array_data['import_message']) && count($array_data['import_message']))
                {
                    $is_error=0;
                    $html='<ul>';
                    foreach($array_data['import_message'] as $row)
                    {
                        if($row['type']=="Error")
                        {
                            $is_error=1;
                            $html.='<li>'.$row['message'].' on line number '.$row['line_number'].' at column '.$row['column_number'].'</li>';
                        }
                    }
                    if($is_error)
                    {
                        DB::table('employees_schedule_kronos_queue')
                        ->where('location', $pending_schedule->location)
                        ->update(array('is_notified'=>1));
                        
                        $user_detail = array(
                            'name'            => 'Admin',
                            'email'           => $pending_schedule->notify_to,
                            'email_content'   => 'The employee schedule pushed by you into Kronos gives below error. '.$html.'CSV file is also attached for your reference.',
                            'mail_from_email' => env('MAIL_FROM'),
                            'mail_from'       => env('MAIL_NAME'),
                            'subject'         => 'Error while pushing Employee schedule into Kronos.',
                            'file_name'       => $pending_schedule->excelsheet_name
                        );
                        $user_single = (object) $user_detail;
                        Mail::send('emails.kronos_timesheet_error',['user' => $user_single], function ($message) use ($user_single) {
                                $message->from($user_single->mail_from_email,$user_single->mail_from);
                                $message->to($user_single->email, $user_single->name)->cc('lancebowser@msi-inv.com')->subject($user_single->subject);
                                $message->replyTo($user_single->mail_from_email,$user_single->mail_from)
                                ->attach(public_path().'/uploads/'.$user_single->file_name, [
                                    'as' => $user_single->file_name, 
                                    'mime' => 'text/csv'
                                ]);
                        });
                        
                        echo $html;
                        echo '<a href="/uploads/'.$pending_schedule->excelsheet_name.'">Click here to download the .csv file for your reference.</a>';
                        echo '<br><a href="/admin/reset_schedule_kronos_queue" target="_blank">Reset Kronos Queue.</a>';
                        die;
                        return redirect()->route('admin.timesheets.approved')->with('successmsg', $html);
                    }else{
                        
                        DB::table('employees_schedule_kronos_queue')
                        ->where('location', $pending_schedule->location)
                        ->update(array('status' => 0,'is_notified'=>1));
                        
                        
                        $user_detail = array(
                            'name'            => 'Admin',
                            'email'           => $pending_schedule->notify_to,
                            'email_content'   => 'The employee schedule pushed by you into Kronos got successfull. CSV file is also attached for your reference.',
                            'mail_from_email' => env('MAIL_FROM'),
                            'mail_from'       => env('MAIL_NAME'),
                            'subject'         => 'Employee schedule pushed into Kronos Successfull.',
                            'file_name'       => $pending_schedule->excelsheet_name
                        );
                        $user_single = (object) $user_detail;
                        Mail::send('emails.kronos_timesheet_error',['user' => $user_single], function ($message) use ($user_single) {
                                $message->from($user_single->mail_from_email,$user_single->mail_from);
                                $message->to($user_single->email, $user_single->name)->cc('lancebowser@msi-inv.com')->subject($user_single->subject);
                                $message->replyTo($user_single->mail_from_email,$user_single->mail_from)
                                ->attach(public_path().'/uploads/'.$user_single->file_name, [
                                    'as' => $user_single->file_name, 
                                    'mime' => 'text/csv'
                                ]);
                        });
                        
                        return redirect()->route('admin.timesheets.approved')->with('successmsg', 'Employee schedule Exported to Kronos Successfully.');
                    }
                }elseif(isset($array_data['user_message']) && count($array_data['user_message']))
                {
                    //echo '<pre>';print_r($array_data);echo $array_data['user_message']['severity'];
                    $html='<ul>';
                    if($array_data['user_message']['severity']=="ERROR")
                    {
                        $is_error=1;
                        $html.='<li>'.$array_data['user_message']['text'].'</li>';
                    }
                    
                    $html.='</ul>';//echo $html;die;
                    DB::table('employees_schedule_kronos_queue')
                    ->where('location', $pending_schedule->location)
                    ->update(array('is_notified'=>1));

                    $user_detail = array(
                        'name'            => 'Admin',
                        'email'           => $pending_schedule->notify_to,
                        'email_content'   => 'The employee schedule pushed by you into Kronos gives below error. '.$html.'CSV file is also attached for your reference.',
                        'mail_from_email' => env('MAIL_FROM'),
                        'mail_from'       => env('MAIL_NAME'),
                        'subject'         => 'Error while pushing Employee schedule into Kronos.',
                        'file_name'       => $pending_schedule->excelsheet_name
                    );
                    $user_single = (object) $user_detail; 
                    Mail::send('emails.kronos_timesheet_error',['user' => $user_single], function ($message) use ($user_single) {
                            $message->from($user_single->mail_from_email,$user_single->mail_from);
                            $message->to($user_single->email, $user_single->name)->cc('lancebowser@msi-inv.com')->subject($user_single->subject);
                            $message->replyTo($user_single->mail_from_email,$user_single->mail_from)
                            ->attach(public_path().'/uploads/'.$user_single->file_name, [
                                'as' => $user_single->file_name, 
                                'mime' => 'text/csv'
                            ]);
                    });
                    echo '<br><a href="/admin/reset_schedule_kronos_queue" target="_blank">Reset Kronos Queue.</a>';
                }else{
                    echo '<br><a href="/admin/reset_schedule_kronos_queue" target="_blank">Reset Kronos Queue.</a>';
                }
                
                echo '<pre>';print_r($array_data);die;
            }
        }else{
            return redirect()->route('admin.employees.index')->with('successmsg', 'No Employee schedule pending in Queue.');
        }
        }else{
            return redirect()->route('admin.employees.index')->with('successmsg', 'No Token found. Try after some time.');
        }
    }
    
    public function reset_schedule_kronos_queue()
    {
        DB::table('employees_schedule_kronos_queue')->where('status', '=',1)->orderBy('id','asc')->limit(1)->delete();
        return redirect()->route('admin.employees.index')->with('successmsg', 'Kronos queue has been reset successfully. You can push schedule now.');
    }
    
    function getCurlValue($filename, $contentType, $postname)
    {
        // PHP 5.5 introduced a CurlFile object that deprecates the old @filename syntax
        // See: https://wiki.php.net/rfc/curl-file-upload
        if (function_exists('curl_file_create')) {
            return curl_file_create($filename, $contentType, $postname);
        }

        // Use the old style if using an older version of PHP
        $value = "@{$filename};filename=" . $postname;
        if ($contentType) {
            $value .= ';type=' . $contentType;
        }

        return $value;
    }
    
    public function calculate_benchmark()
    {
        $AgoDate=\Carbon\Carbon::now()->subWeek()->format('Y-m-d');  // returns 2016-02-03
        //echo '<br>';
        $NowDate = Carbon::yesterday()->toDateString();
        
//        $user_detail = array(
//                'name'            => 'kunal',
//                'email'           => 'kunal.kumar@pegasusone.com',
//                'comment'         => 'employee benchmark calculation',
//                'mail_from_email' => env('MAIL_FROM'),
//                'mail_from'       => env('MAIL_NAME'),
//                'store'           => 'employee benchmark calculation',
//                'date'            => date('m-d-Y',strtotime('2020-08-08')),
//                'subject'         => 'employee benchmark calculation',
//                'crew_manager_area'=>'employee benchmark calculation',
//                'crew_manager_name'=>'employee benchmark calculation'
//            );
//            $user_single = (object) $user_detail;
//            Mail::send('emails.event_qc_send_notification',['user' => $user_single], function ($message) use ($user_single) {
//                $message->from($user_single->mail_from_email,$user_single->mail_from);
//                $message->to($user_single->email, $user_single->name)->subject($user_single->subject);
//                $message->replyTo($user_single->mail_from_email,$user_single->mail_from);
//            });
        //die;
        $user_detail = array(
            'name'            => 'kunal',
            'email'           => 'kunal.kumar@pegasusone.com',
            'email_content'   => 'employee benchmark calculation',
            'mail_from_email' => env('MAIL_FROM'),
            'mail_from'       => env('MAIL_NAME'),
            'subject'         => 'employee benchmark calculation',
        );
        $user_single = (object) $user_detail;
        Mail::send('emails.mdb_error',['user' => $user_single], function ($message) use ($user_single) {
                $message->from($user_single->mail_from_email,$user_single->mail_from);
                $message->to($user_single->email, $user_single->name)->subject($user_single->subject);
                $message->replyTo($user_single->mail_from_email,$user_single->mail_from);
        });
                
                
        $timesheets = DB::table('timesheet_header')
                ->select('id')
                ->whereBetween('approved_on',array($AgoDate,$NowDate))
                //->whereBetween('approved_on',array('2020-11-08','2020-11-14'))
                ->orderBy('approved_on','asc')
                ->get();
        $timesheet_arr = array();
        foreach($timesheets as $timesheet){
            $timesheet_arr[] = $timesheet->id;
        }
        $employees = Employee::select('id','is_crew_leader','week1_benchmark','week2_benchmark','week3_benchmark','week4_benchmark')->get();
        //echo '<pre>';print_r($timesheet_arr);die;
        foreach($employees as $employee)
        {
            $total_benchmark = 0;
            $benchmark_count=0;
            $final_benchmark=0;
            //echo '<pre>';print_r($timesheet);
            
                //echo '<pre>';print_r($timesheets);die;
                $timesheet_data = DB::table('timesheet_data')
                    ->select('timesheet_data.*','stores.benchmark','stores.pieces_or_dollars','stores.spf','stores.id as store_id')
                    ->leftJoin('timesheet_header','timesheet_header.id','=','timesheet_data.timesheet_id')
                    ->leftJoin('stores','stores.id','=','timesheet_header.store_id')
                    //->leftJoin('employees','employees.id','=','timesheet_data.employee_id')
                    ->whereIn('timesheet_header.id',$timesheet_arr)
                    ->orderBy('timesheet_header.dtJobDate','asc')
                    ->where('employee_id','=',$employee->id)
                    ->get();
                //dd(DB::getQueryLog());die;
                //echo '<pre>';
                if(count($timesheet_data))
                {
                    foreach($timesheet_data as $row)
                    {
                        $timesheet_approved_data = DB::table('timesheet_approved')
                            ->where('timesheet_id','=',$row->timesheet_id)
                            ->where('employee_id','=',$employee->id)
                            ->first();
                        if($timesheet_approved_data)
                        {
                            $store_hours = explode(':',$timesheet_approved_data->store_hours);
                            if(intval($store_hours[0])>0 || intval($store_hours[1])>0)
                            {
                                $time_spent=0;
                                if($store_hours[0])
                                    $time_spent = $store_hours[0]*60;
                                if(isset($store_hours[1]))        
                                    $time_spent+=$store_hours[1];
                                $time_spent = $time_spent/60;
                                if($row->pieces_or_dollars=="dollars")
                                    $emp_count_per_hour = (@$row->dEmpCount/$time_spent);
                                else
                                    $emp_count_per_hour = (@$row->dEmpPieces/@$time_spent);
                                //echo '----';
                                //echo '<pre>';print_r($row);
                                if(!is_integer($row->spf))
                                    $row->spf = 1;
                                if($row->bIsSuper)
                                    $benchmark = ($emp_count_per_hour*100)/($row->benchmark*(($row->spf)/100));
                                else
                                    $benchmark = ($emp_count_per_hour*100)/$row->benchmark;
                                $benchmark = round($benchmark,2);
                                $total_benchmark = $total_benchmark+$benchmark;
                                $benchmark_count++;
                            }
                        }

                    }
                    if($benchmark_count)
                        $final_benchmark = $total_benchmark/$benchmark_count;
                    
                    $final_benchmark = round($final_benchmark,2);
                    //echo '<br>';
                   $average_benchmark = round((($final_benchmark+$employee->week2_benchmark+$employee->week3_benchmark+$employee->week4_benchmark)/4),2);
                   if($final_benchmark>250)
                        $final_benchmark=250;
                   if($average_benchmark>250)
                       $average_benchmark=250;
                   //echo '<pre>';print_r($timesheets);die;
                   DB::table('employees')
                    ->where('id','=', $employee->id)
                    ->update(array('benchmark'=>$average_benchmark,'week4_benchmark'=>$final_benchmark,'week3_benchmark'=>$employee->week4_benchmark,
                        'week2_benchmark'=>$employee->week3_benchmark,'week1_benchmark'=>$employee->week2_benchmark));
                }
            
        }
        
    }
    
    public function calculate_old_benchmark()
    {
        //DB::enableQueryLog();
        $timesheets = DB::table('timesheet_header')
                ->select('id')
                //->whereBetween('approved_on',array('2020-06-28','2020-07-04'))
                //->whereBetween('approved_on',array('2020-07-05','2020-07-11'))
                //->whereBetween('approved_on',array('2020-07-12','2020-07-18'))
                ->whereBetween('approved_on',array('2020-07-19','2020-07-25'))
                ->orderBy('approved_on','asc')
                ->get();
        $timesheet_arr = array();
        foreach($timesheets as $timesheet){
            $timesheet_arr[] = $timesheet->id;
        }
        $employees = Employee::select('id','is_crew_leader')->get();
        //echo '<pre>';print_r($timesheet_arr);die;
        foreach($employees as $employee)
        {
            $total_benchmark = 0;
            $benchmark_count=0;
            $final_benchmark=0;
            //echo '<pre>';print_r($timesheet);
            
                //echo '<pre>';print_r($timesheets);die
                $timesheet_data = DB::table('timesheet_data')
                    ->select('timesheet_data.*','stores.benchmark','stores.name as storename','stores.pieces_or_dollars','stores.spf','stores.id as store_id')
                    ->leftJoin('timesheet_header','timesheet_header.id','=','timesheet_data.timesheet_id')
                    ->leftJoin('stores','stores.id','=','timesheet_header.store_id')
                    //->leftJoin('employees','employees.id','=','timesheet_data.employee_id')
                    ->whereIn('timesheet_header.id',$timesheet_arr)
                    ->orderBy('timesheet_header.dtJobDate','asc')
                    ->where('employee_id','=',$employee->id)
                    ->get();
                //dd(DB::getQueryLog());die;
                //echo '<pre>';
                if(count($timesheet_data))
                {
                    foreach($timesheet_data as $row)
                    {
                        $timesheet_approved_data = DB::table('timesheet_approved')
                            ->where('timesheet_id','=',$row->timesheet_id)
                            ->where('employee_id','=',$employee->id)
                            ->first();
                        if($timesheet_approved_data)
                        {
                            //echo '<pre>';print_r($timesheet_approved_data);
                            $store_hours = explode(':',$timesheet_approved_data->store_hours);
                            if(intval($store_hours[0])>0 || intval($store_hours[1])>0)
                            {
                                $time_spent=0;
                                if($store_hours[0])
                                    $time_spent = $store_hours[0]*60;
                                if(isset($store_hours[1]))        
                                    $time_spent+=$store_hours[1];
                                $time_spent = $time_spent/60;
                                if($row->pieces_or_dollars=="dollars")
                                    $emp_count_per_hour = (@$row->dEmpCount/$time_spent);
                                else
                                    $emp_count_per_hour = (@$row->dEmpPieces/@$time_spent);
                                //echo '----';
                                if($row->bIsSuper)
                                    $benchmark = ($emp_count_per_hour*100)/($row->benchmark*(($row->spf)/100));
                                else
                                    $benchmark = ($emp_count_per_hour*100)/$row->benchmark;
                                echo $benchmark = round($benchmark,2);echo '----';
                                $total_benchmark = $total_benchmark+$benchmark;
                                $benchmark_count++;
                            }
                        }
                    }
                    //echo '<pre>';print_r($timesheet_data);
                    if($benchmark_count){
                        $final_benchmark = $total_benchmark/$benchmark_count;
                    }
                    //echo $final_benchmark;die;
                    $final_benchmark = round($final_benchmark,2);
                    DB::table('employees')
                    ->where('id','=', $row->employee_id)
                    ->update(array('week4_benchmark'=>$final_benchmark));
                }
            
        }
        //dd(DB::getQueryLog());die;
        //echo '<pre>';print_r($timesheets);die;
    }
    
}
