<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Timesheet;
use App\Models\Area;
use App\Models\Jsa;
use App\Models\Store;
use App\Models\StoreApr;
use App\Models\StoreJsa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTimesheetsRequest;
use App\Http\Requests\Admin\UpdateTimesheetsRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Redirect;
use App\Models\TimesheetData;
use App\Models\TimesheetVehicle;
use App\Exports\TimesheetExport;
use App\Models\Timeentries;
use App\Models\Employee;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Mail;
use App\Models\Event;
use App\Models\TimesheetApproved;
use App\Models\EventScheduleEmployees;
use App\Repositories\TimeSheetRepository;

class TimesheetsController extends Controller
{
    /**
     * Display a listing of Timesheet.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (! Gate::allows('timesheet_view')) {
            return abort(401);
        }
        $timesheets = DB::table('timesheet_header')
            ->select('timesheet_header.*','stores.name as storename','events.run_number',
                    'employees.first_name','employees.last_name','areas.area_number')
            ->leftJoin('stores','stores.id','=','timesheet_header.store_id')
            ->leftJoin('events','events.id','=','timesheet_header.event_id')      
            ->leftJoin('timesheet_data','timesheet_data.timesheet_id','=','timesheet_header.id')
            ->leftJoin('employees','employees.id','=','timesheet_data.employee_id')
            ->leftJoin('areas','employees.area_id','=','areas.id')
            ->where('timesheet_data.bIsSuper','=',1)
            ->where('timesheet_header.status','Pending')
            ->where('timesheet_header.dtJobDate','>','2020-04-14')
            ->orderBy('timesheet_header.dtJobDate', 'ASC')
            ->orderBy('areas.area_number', 'ASC')
            ->orderBy('events.run_number', 'ASC')
            ->get();
        //$timesheets = Timesheet::with(array('vehicles','emp_data','store','event'))->where('status','Pending')->where('dtJobDate','>','2020-04-14');
        //$timesheets = $timesheets->orderBy('dtJobDate', 'ASC')->get();
        
        //print_r($timesheets);die;
        //$users = User::with(array('city','state','country','parentuser'))->where('user_type', '=', 'member')->get();
         
        return view('admin.timesheets.index', compact('timesheets'));
    }

    /**
     * Show the form for creating new Timesheet.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('timesheet_create')) {
            return abort(401);
        }
        //$areas = DB::table('areas')->pluck('title','id');
        $stores = DB::table('stores')->pluck('name','id');
        
        return view('admin.timesheets.create', compact('stores'));
    }

    /**
     * Store a newly created Timesheet in storage.
     *
     * @param  \App\Http\Requests\StoreTimesheetsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTimesheetsRequest $request)
    {
        if (! Gate::allows('timesheet_create')) {
            return abort(401);
        }
        $dist_exist = Timesheet::where('store_id','=',$request->store_id)->where('jsa_id','=',$request->jsa_id)->first();
        if($dist_exist){
            return Redirect::back()->withErrors(['Timesheet from this store to JSA area is already defined.']);
        }else{
            $timesheet = Timesheet::create($request->all());
            return redirect()->route('admin.timesheets.index')->with('successmsg', 'Timesheet added successfully.');
        }
        
    }


    /**
     * Show the form for editing Timesheet.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('timesheet_edit')) {
            return abort(401);
        }
        
        $stores = DB::table('stores')->pluck('name','id');
        $timesheet = Timesheet::with(array('vehicles','emp_data','store'))->findOrFail($id);
        //echo '<pre>';
        //print_r($distance);die;
        return view('admin.timesheets.edit', compact('timesheet','stores'));
        
    }
    
    
    
    /**
     * Update Timesheet in storage.
     *
     * @param  \App\Http\Requests\UpdateTimesheetsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTimesheetsRequest $request, $id)
    {
        if (! Gate::allows('timesheet_edit')) {
            return abort(401);
        }
//        echo '<pre>';
//        print_r($request->all());die;
        
        $timesheet = Timesheet::findOrFail($id);
        $timesheet->update($request->all());
        return redirect()->route('admin.timesheets.index')->with('successmsg', 'Timesheet updated successfully.');
        
    }


    /**
     * Display Timesheet.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    
    function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
        $sort_col = array();
        foreach ($arr as $key=> $row) {
            $sort_col[$key] = $row[$col];
        }

        array_multisort($sort_col, $dir, $arr);
    }

    public function show(Request $request,$id)
    {
        if (! Gate::allows('timesheet_view')) {
            return abort(401);
        }
        $stores = DB::table('stores')->pluck('name','id');
        $timesheet = Timesheet::with(array('vehicles','emp_data','store','approved_timesheets','approval_detail'))->findOrFail($id);
        $arr = $timesheet->approved_timesheets->toArray();
        //$this->array_sort_by_column($arr,'');
        $approved=array();
        foreach($arr as $emp)
        {
            $approved[$emp['employee_id']]=$emp;
        }
        //echo '<pre>';print_r($timesheet->approval_detail);die;
        $employees = DB::table('timesheet_data')
            ->select('timesheet_data.*','timesheet_data.timesheet_id as timesheet_id','timesheet_data.employee_id as employee_ssn','employees.*','employees.id as employee_id',
                    'jsas.*','jsas.id as emp_jsa_id','areas.title as area_title','areas.id as area_id','origin.id as origin',
                    'dest.id as destination')
            ->leftJoin('employees','employees.id','=','timesheet_data.employee_id')
            ->leftJoin('jsas','jsas.id','=','employees.jsa_id')
            ->leftJoin('areas','areas.id','=','employees.area_id')      
            ->leftJoin('stores as origin','origin.id','=','timesheet_data.sStoreOrigin')
            ->leftJoin('stores as dest','dest.id','=','timesheet_data.sStoreReturn')
            ->where('timesheet_data.timesheet_id','=',$id)
            ->orderBy('employees.last_name', 'ASC')
            ->orderBy('employees.first_name', 'ASC')
            ->get();
        //echo '<pre>';print_r($timesheet->emp_data->toArray());die;
        //echo '<pre>';        print_r($timesheet->approval_detail);die;
        return view('admin.timesheets.show',compact('timesheet','approved','employees'));
        
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
    
    public function export_to_kronos(Request $request)
    {
        //echo auth()->user()->email;die;
        $pending_time_entries = DB::table('time_entries_queue')
                ->where('time_entries_queue.status','=',1)
                ->get();
        //print_r($pending_time_entries);die;
        if(count($pending_time_entries))
        {
            return redirect()->route('admin.timesheets.index')->with('warningmsg', 'There is already a submitted timesheet pending for sync. Please sync your data with Kronos first.');
        }
        $export_file_name = 'Time-entries-'.date('Y-m-d h-i-s-A').'.csv';
        Excel::store(new TimesheetExport(2018), $export_file_name,'media');
        //$this->testcron();
//        Get Kronos Token Start        
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
        
//        Get Kronos Token Start End
        //$token = 'Bearer eyJhbGciOiJIUzUxMiJ9.eyJleHAiOjE1ODcwNDA0NDIsImlhdCI6MTU4NzAzNjg0Miwic2lkIjoiMTg1NzQ5NTkwMTUiLCJhaWQiOiI4NjU4MTI1OTUwIiwiY2lkIjoiNjcxMjY2NTIiLCJ0eXBlIjoiciJ9.DbpSOyygJ8ScFr9L0U58w4SH1IP2rufIGSPyx409JPRmgLzYPj3SkDv3E5JzIZdmlKlT4r0DcxRsOspoxkLMTg';
        //$cURLConnection1 = curl_init('https://secure3.saashr.com/ta/rest/v1/imports');
//        $cURLConnection1 = curl_init('https://secure3.saashr.com/ta/rest/v1/import/116');
//        Send Timesheet to Kronos       
        $filename = base_path().'/public/uploads/'.$export_file_name;
        $cfile = $this->getCurlValue($filename,'text/csv',$export_file_name);
        $data = array('file' => $cfile);
        $ch = curl_init('https://secure3.saashr.com/ta/rest/v1/import/116');
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
//        Send Timesheet to Kronos End            
        //echo "<pre>";
        //print_r($headers);
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
        //$this->testcron(array('msg'=>$array_data['status']));
        if(isset($array_data['status']) && $array_data['status']=="running")
        {
            
            DB::table('time_entries')
            ->where('status', 1)
            ->update(array('location' => $location));
            
            $pointdata2 = array('excelsheet_name'=>$export_file_name,'location'=>$location,"status"=>1,'notify_to'=>env('ADMIN_EMAIL'),'is_notified'=>0);
            //$this->testcron($pointdata2);
            DB::table('time_entries_queue')->insert($pointdata2);
            
            return redirect()->route('admin.timesheets.index')->with('successmsg', 'Server is busy now. Please donot export time entries again.');
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
                //$this->testcron($array_data);
                return redirect()->route('admin.timesheets.approved')->with('successmsg', $html);
            }else{
                
                $submitted_time_entries = DB::table('time_entries')
                            ->where('status', 1)
                            ->select('timesheet_id')
                            ->groupBy('timesheet_id')
                            ->get();
                $ids=array();
                foreach($submitted_time_entries as $submitted_time_entry)
                    $ids[]=$submitted_time_entry->timesheet_id;
                $updateArr = ['status' => 'Submitted'];
                $event  = Timesheet::whereIn('id',$ids)->update($updateArr);
                        
                DB::table('time_entries')
                ->where('status', 1)
                ->update(array('status' => 0));
                return redirect()->route('admin.timesheets.approved')->with('successmsg', 'Timesheet Exported to Kronos Successfully.');
            }
        }
        
        exit;  
    }
    
    function timeentries_status()
    {
        // dd('test');
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
        
        $pending_time_entries = DB::table('time_entries_queue')
                ->where('time_entries_queue.status','=',1)
                ->get();
        // dd($pending_time_entries);
        if(count($pending_time_entries))
        {
            foreach($pending_time_entries as $pending_time_entry)
            {
                $ch = curl_init('https://secure3.saashr.com/ta/rest/v1'.$pending_time_entry->location);
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
                        DB::table('time_entries_queue')
                        ->where('location', $pending_time_entry->location)
                        ->update(array('is_notified'=>1));
                        
                        if($pending_time_entry->notify_to==0)
                        {
                            $user_detail = array(
                                'name'            => 'Admin',
                                'email'           => $pending_time_entry->notify_to,
                                'email_content'   => 'The timesheet pushed by you into Kronos gives below error. '.$html.'CSV file is also attached for your reference.',
                                'mail_from_email' => env('MAIL_FROM'),
                                'mail_from'       => env('MAIL_NAME'),
                                'subject'         => 'Error while pushing Timesheet into Kronos.',
                                'file_name'       => $pending_time_entry->excelsheet_name
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
                        }
                        echo $html;
                        echo '<a href="/uploads/'.$pending_time_entry->excelsheet_name.'">Click here to download the .csv file for your reference.</a>';
                        echo '<br><a href="/admin/reset_kronos_queue/" target="_blank">Reset Kronos Queue.</a>';
                        die;
                        return redirect()->route('admin.timesheets.approved')->with('successmsg', $html);
                    }else{
                        $submitted_time_entries = DB::table('time_entries')
                            ->where('location', $pending_time_entry->location)
                            ->select('timesheet_id')
                            ->groupBy('timesheet_id')
                            ->get();
                        $ids=array();
                        foreach($submitted_time_entries as $submitted_time_entry)
                            $ids[]=$submitted_time_entry->timesheet_id;
                        $updateArr = ['status' => 'Submitted'];
                        $event  = Timesheet::whereIn('id',$ids)->update($updateArr);
                      
                        DB::table('time_entries')
                        ->where('location', $pending_time_entry->location)
                        ->update(array('status' => 0));
                        DB::table('time_entries_queue')
                        ->where('location', $pending_time_entry->location)
                        ->update(array('status' => 0,'is_notified'=>1));
                        if($pending_time_entry->notify_to==0)
                        {
                            $user_detail = array(
                                'name'            => 'Admin',
                                'email'           => $pending_time_entry->notify_to,
                                'email_content'   => 'The timesheet pushed by you into Kronos got successfull. CSV file is also attached for your reference.',
                                'mail_from_email' => env('MAIL_FROM'),
                                'mail_from'       => env('MAIL_NAME'),
                                'subject'         => 'Timesheet pushed into Kronos Successfull.',
                                'file_name'       => $pending_time_entry->excelsheet_name
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
                        }

                        
                        return redirect()->route('admin.timesheets.approved')->with('successmsg', 'Timesheet Exported to Kronos Successfully.');
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
                    DB::table('time_entries_queue')
                    ->where('location', $pending_time_entry->location)
                    ->update(array('is_notified'=>1));
                    if($pending_time_entry->notify_to==0)
                    {
                        $user_detail = array(
                            'name'            => 'Admin',
                            'email'           => $pending_time_entry->notify_to,
                            'email_content'   => 'The timesheet pushed by you into Kronos gives below error. '.$html.'CSV file is also attached for your reference.',
                            'mail_from_email' => env('MAIL_FROM'),
                            'mail_from'       => env('MAIL_NAME'),
                            'subject'         => 'Error while pushing Timesheet into Kronos.',
                            'file_name'       => $pending_time_entry->excelsheet_name
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
                    }
                    echo '<a href="/uploads/'.$pending_time_entry->excelsheet_name.'">Click here to download the .csv file for your reference.</a>';
                    echo '<br><a href="/admin/reset_kronos_queue/" target="_blank">Reset Kronos Queue.</a>';
                }
                echo '<pre>';print_r($array_data);die;
            }
        }else{
            return redirect()->route('admin.timesheets.index')->with('successmsg', 'No Timesheet pending in Queue.');
        }
        }else{
            return redirect()->route('admin.timesheets.index')->with('successmsg', 'No Token found. Try after some time.');
        }
    }
    
    function calDistance($origin,$origin_type,$destination,$destination_type) {
        //return 100;
        if(strtolower($origin_type)=="office")
            $from = Jsa::with(array('city','state'))->where('id','=',$origin)->first();
        else
            $from = Store::with(array('city','state'))->where('id','=',$origin)->first();

        if(strtolower($destination_type)=="office")
            $to = Jsa::with(array('city','state'))->where('id','=',$destination)->first();
        else
            $to = Store::with(array('city','state'))->where('id','=',$destination)->first();

        $origin1 = urlencode(@$from->address.', '.@$from->city->name.', '.@$from->state->name.', United States'); 
        $destination1 = urlencode(@$to->address.', '.@$to->city->name.', '.@$to->state->name.', United States');
        $api = file_get_contents("https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$origin1."&destinations=".$destination1."&key=AIzaSyCtht1kYCSys9ifRKwhMcy2afLPSRt9iZ4&language=en-EN&sensor=false");
        return $data = json_decode($api);
    }
    
    public function caldrivetime(Request $request)
    {
        $time_array=array();
        $pay_date=date('Y-m-d',strtotime($request['pay_date']));
        $event_date = $request['event_date'];
        foreach($request['employee_id'] as $key=>$emp_id)
        {
            if($emp_id==$request['affected_row'])
            {
            $time_array[$emp_id]['drivetime']=0;
            $time_array[$emp_id]['jsa_miles']=0;
            $time_array[$emp_id]['vehicle_travel']=0;
            $time_array[$emp_id]['traveltime']=0;
            $driver_to=0;
            $driver_from=0;
            
            if(isset($request['employee_status'][$emp_id]))
            {
                if(in_array("Driver To",$request['employee_status'][$emp_id]))
                        $driver_to=1;
                if(in_array("Driver From",$request['employee_status'][$emp_id]))
                        $driver_from=1;
            }
            
            if(strtolower($request['origin'][$key])=="office" && strtolower($request['destination'][$key])=="office" && $request['iAttendanceFlag'][$key]!=3 && $request['iAttendanceFlag'][$key]!=4)
            {
            
                if($driver_to && $driver_from)
                {
                    //Driver To and From
                    $driverto=1;
                    $driverfrom=1;
                    $origin=$request['emp_jsa'][$key];
                    $origin_type="office";
                    $destination=$request['store_id'];
                    $destination_type="store";
                    $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    
                    $origin=$request['store_id'];
                    $origin_type="store";
                    $destination=$request['emp_jsa'][$key];
                    $destination_type="office";
                    $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance1 = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    
                    $time_array[$emp_id]['vehicle_travel']=$distance+$distance1;
                    $time_array[$emp_id]['drivetime']=number_format((float)($distance*2)/50, 2, '.', '');
                    $time_array[$emp_id]['drivetime'] = $this->convertTimeToColon($time_array[$emp_id]['drivetime']);
                                        
                    $time_array[$emp_id]['jsa_miles']=0;
                }elseif($driver_to)
                {
                    //Driver To
                    $driverto=1;
                    $origin=$request['emp_jsa'][$key];
                    $origin_type="office";
                    $destination=$request['store_id'];
                    $destination_type="store";
                    $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $time_array[$emp_id]['drivetime']=number_format((float)$distance/50, 2, '.', '');
                    $time_array[$emp_id]['drivetime'] = $this->convertTimeToColon($time_array[$emp_id]['drivetime']);
                    
                    $origin=$request['store_id'];
                    $origin_type="store";
                    $destination=$request['emp_jsa'][$key];
                    $destination_type="office";
                    $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance1 = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $time_array[$emp_id]['jsa_miles']=ceil($distance1-12);
                    $time_array[$emp_id]['vehicle_travel']=$distance;
                }elseif($driver_from)
                {
                    //Driver From
                    $driverfrom=1;
                    $origin=$request['store_id'];
                    $origin_type="store";
                    $destination=$request['emp_jsa'][$key];
                    $destination_type="office";
                    $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $time_array[$emp_id]['drivetime']=number_format((float)$distance/50, 2, '.', '');
                    $time_array[$emp_id]['drivetime'] = $this->convertTimeToColon($time_array[$emp_id]['drivetime']);
                    
                    $origin=$request['emp_jsa'][$key];
                    $origin_type="office";
                    $destination=$request['store_id'];
                    $destination_type="store";
                    $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance1 = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $time_array[$emp_id]['jsa_miles']=ceil($distance1-12);
                    $time_array[$emp_id]['vehicle_travel']=$distance;
                }else{
                    
                    $origin = @$request['emp_jsa'][$key];
                    $origin_type="office";
                    $destination=$request['store_id'];
                    $destination_type="store";
                    $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $time_array[$emp_id]['jsa_miles']=ceil($distance*2-25);
                }
        }elseif(strtolower($request['origin'][$key])=="office" && strtolower($request['destination'][$key])!="office" && $request['iAttendanceFlag'][$key]!=3 && $request['iAttendanceFlag'][$key]!=4)
        {
            if($driver_to && $driver_from)
            {
                //Driver To and From
                $driverto=1;
                $driverfrom=1;
                $origin=$request['emp_jsa'][$key];
                $origin_type="office";
                $destination=$request['store_id'];
                $destination_type="store";
                $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
                $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                $time_array[$emp_id]['drivetime']=number_format((float)$distance/50, 2, '.', '');
                $time_array[$emp_id]['drivetime'] = $this->convertTimeToColon($time_array[$emp_id]['drivetime']);
                
                $origin=$request['store_id'];
                $origin_type="store";
                $destination=$request['destination'][$key];
                $destination_type="store";
                $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
                $distance1 = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                $time_array[$emp_id]['vehicle_travel']=ceil($distance);
            }elseif($driver_to)
            {
                //Driver To
                $driverto=1;
                $origin=$request['emp_jsa'][$key];
                $origin_type="office";
                $destination=$request['store_id'];
                $destination_type="store";
                $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
                $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                $time_array[$emp_id]['drivetime']=number_format((float)$distance/50, 2, '.', '');
                $time_array[$emp_id]['drivetime'] = $this->convertTimeToColon($time_array[$emp_id]['drivetime']);
                $time_array[$emp_id]['vehicle_travel']=$distance;
            }elseif($driver_from)
            {
                //Driver From
                $driverfrom=1;
                $origin=$request['emp_jsa'][$key];
                $origin_type="office";
                $destination=$request['store_id'];
                $destination_type="store";
                $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
                $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                $time_array[$emp_id]['jsa_miles']=ceil($distance-12);
                
                $origin=$request['store_id'];
                $origin_type="store";
                $destination=$request['destination'][$key];
                $destination_type="store";
                $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
                $distance1 = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                //$time_array[$emp_id]['vehicle_travel']=$distance1;
            }else{
                $origin=$request['emp_jsa'][$key];
                $origin_type="office";
                $destination=$request['store_id'];
                $destination_type="store";
                $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
                $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                $time_array[$emp_id]['jsa_miles']=ceil($distance-12);
            }
        }elseif(strtolower($request['origin'][$key])!="office" && strtolower($request['destination'][$key])!="office" && $request['iAttendanceFlag'][$key]!=3 && $request['iAttendanceFlag'][$key]!=4)
        {
            $originHaveEvent = $this->originHaveEvent($request['origin'][$key],$event_date);
            if($originHaveEvent)
            {
                if($driver_to && $driver_from)
                {
                    //Driver To and From
                    $driverto=1;
                    $driverfrom=1;
                    $origin=$request['origin'][$key];
                    $origin_type="store";
                    $destination=$request['store_id'];
                    $destination_type="store";
                    $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $time_array[$emp_id]['drivetime']=number_format((float)$distance/50, 2, '.', '');
                    $time_array[$emp_id]['drivetime'] = $this->convertTimeToColon($time_array[$emp_id]['drivetime']);
                    
                    $origin=$request['store_id'];
                    $origin_type="store";
                    $destination=$request['destination'][$key];
                    $destination_type="store";
                    $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance1 = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $time_array[$emp_id]['vehicle_travel']=ceil($distance);
                }elseif($driver_to)
                {
                    //Driver To
                    $driverto=1;
                    $origin=$request['origin'][$key];
                    $origin_type="store";
                    $destination=$request['store_id'];
                    $destination_type="store";
                    $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $time_array[$emp_id]['drivetime']=number_format((float)$distance/50, 2, '.', '');
                    $time_array[$emp_id]['drivetime'] = $this->convertTimeToColon($time_array[$emp_id]['drivetime']);
                    $time_array[$emp_id]['vehicle_travel']=$distance;
                }elseif($driver_from)
                {
                    //Driver From
                    $driverfrom=1;
                    $origin=$request['origin'][$key];
                    $origin_type="store";
                    $destination=$request['store_id'];
                    $destination_type="store";
                    $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $time_array[$emp_id]['traveltime']=$distance/50;
                    $time_array[$emp_id]['traveltime'] = $this->convertTimeToColon($time_array[$emp_id]['traveltime']);
                    
                    $origin=$request['store_id'];
                    $origin_type="store";
                    $destination=$request['destination'][$key];
                    $destination_type="store";
                    $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance1 = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    //$time_array[$emp_id]['vehicle_travel']=ceil($distance1);
                }else{
                    $origin=$request['origin'][$key];
                    $origin_type="store";
                    $destination=$request['store_id'];
                    $destination_type="store";
                    $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $time_array[$emp_id]['traveltime']=$distance/50;
                    $time_array[$emp_id]['traveltime'] = $this->convertTimeToColon($time_array[$emp_id]['traveltime']);
                }
            }else{
                if($driver_to && $driver_from)
                {
                    //Driver To and From
                    $driverto=1;
                    $driverfrom=1;
                    $origin=$request['origin'][$key];
                    $origin_type="store";
                    $destination=$request['store_id'];
                    $destination_type="store";
                    $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $time_array[$emp_id]['drivetime']=number_format((float)$distance/50, 2, '.', '');
                    $time_array[$emp_id]['drivetime'] = $this->convertTimeToColon($time_array[$emp_id]['drivetime']);
                    
                    $origin=$request['store_id'];
                    $origin_type="store";
                    $destination=$request['destination'][$key];
                    $destination_type="store";
                    $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance1 = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $time_array[$emp_id]['vehicle_travel']=ceil($distance);
                }elseif($driver_to)
                {
                    //Driver To
                    $driverto=1;
                    $origin=$request['origin'][$key];
                    $origin_type="store";
                    $destination=$request['store_id'];
                    $destination_type="store";
                    $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $time_array[$emp_id]['drivetime']=number_format((float)$distance/50, 2, '.', '');
                    $time_array[$emp_id]['drivetime'] = $this->convertTimeToColon($time_array[$emp_id]['drivetime']);
                    $time_array[$emp_id]['vehicle_travel']=$distance;
                }elseif($driver_from)
                {
                    $driverfrom=1;
                    $origin=$request['origin'][$key];
                    $origin_type="store";
                    $destination=$request['store_id'];
                    $destination_type="store";
                    $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $time_array[$emp_id]['jsa_miles']=$distance;
                    
                    $origin=$request['store_id'];
                    $origin_type="store";
                    $destination=$request['destination'][$key];
                    $destination_type="store";
                    $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance1 = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    //$time_array[$emp_id]['vehicle_travel']=ceil($distance1);
                }else{
                    $origin=$request['origin'][$key];
                    $origin_type="store";
                    $destination=$request['store_id'];
                    $destination_type="store";
                    $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $time_array[$emp_id]['jsa_miles']=$distance;
                }
            }
        }elseif(strtolower($request['origin'][$key])!="office" && strtolower($request['destination'][$key])=="office" && $request['iAttendanceFlag'][$key]!=3 && $request['iAttendanceFlag'][$key]!=4)
        {
            $originHaveEvent = $this->originHaveEvent($request['origin'][$key],$event_date);
            if($originHaveEvent)
            {
                if($driver_to && $driver_from)
                {
                    //echo 'ttt'.$driver_to.'--'.$request['employee_id'][$key].'--'.$driver_from.'kkk';
                    //Driver To and From
                    $driverto=1;
                    $driverfrom=1;
                    $origin=$request['origin'][$key];
                    $origin_type="store";
                    $destination=$request['store_id'];
                    $destination_type="store";
                    $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
                    //echo '<pre>';print_r($dist);
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $drive1 = number_format((float)($distance)/50, 2, '.', '');

                    $origin=$request['store_id'];
                    $origin_type="store";
                    $destination=$request['emp_jsa'][$key];
                    $destination_type="office";
                    $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
                    //echo '<pre>';print_r($dist);
                    $distance1 = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $drive2 = number_format((float)($distance1)/50, 2, '.', '');

                    $time_array[$emp_id]['drivetime']=$drive1+$drive2;
                    $time_array[$emp_id]['drivetime'] = $this->convertTimeToColon($time_array[$emp_id]['drivetime']);
                    $time_array[$emp_id]['vehicle_travel']=$distance+$distance1;
                }elseif($driver_to)
                {
                    //echo 'tt'.$driver_to.'--'.$request['employee_id'][$key].'--'.$driver_from.'kk';
                    //Driver To
                    $driverto=1;
                    $origin=$request['origin'][$key];
                    $origin_type="store";
                    $destination=$request['store_id'];
                    $destination_type="store";
                    $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $time_array[$emp_id]['drivetime']=number_format((float)$distance/50, 2, '.', '');
                    $time_array[$emp_id]['drivetime'] = $this->convertTimeToColon($time_array[$emp_id]['drivetime']);

                    $origin=$request['store_id'];
                    $origin_type="store";
                    $destination=$request['emp_jsa'][$key];
                    $destination_type="office";
                    $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance1 = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $time_array[$emp_id]['jsa_miles']=ceil($distance1-12);
                    $time_array[$emp_id]['vehicle_travel']=$distance;
                }elseif($driver_from)
                {
                    //echo 't'.$driver_to.'--'.$request['employee_id'][$key].'--'.$driver_from.'k';
                    //Driver From
                    $driverfrom=1;
                    $origin=$request['origin'][$key];
                    $origin_type="store";
                    $destination=$request['store_id'];
                    $destination_type="store";
                    $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $time_array[$emp_id]['traveltime']=$distance/50;
                    $time_array[$emp_id]['traveltime'] = $this->convertTimeToColon($time_array[$emp_id]['traveltime']);

                    $origin=$request['store_id'];
                    $origin_type="store";
                    $destination=$request['emp_jsa'][$key];
                    $destination_type="office";
                    $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance1 = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $time_array[$emp_id]['drivetime']=number_format((float)$distance1/50, 2, '.', '');
                    $time_array[$emp_id]['drivetime'] = $this->convertTimeToColon($time_array[$emp_id]['drivetime']);
                    $time_array[$emp_id]['vehicle_travel']=$distance1;
                }else{
                    //echo 'tt'.$request['employee_id'][$key].'kk';
                    $origin=$request['origin'][$key];
                    $origin_type="store";
                    $destination=$request['store_id'];
                    $destination_type="store";
                    $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $time_array[$emp_id]['traveltime']=$distance/50;
                    $time_array[$emp_id]['traveltime'] = $this->convertTimeToColon($time_array[$emp_id]['traveltime']);

                    $origin=$request['store_id'];
                    $origin_type="store";
                    $destination=$request['emp_jsa'][$key];
                    $destination_type="office";
                    $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance1 = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $time_array[$emp_id]['jsa_miles']=ceil($distance1-12);
                }
            }else{
                if($driver_to && $driver_from)
                {
                    //echo 'ttt'.$driver_to.'--'.$request['employee_id'][$key].'--'.$driver_from.'kkk';
                    //Driver To and From
                    $driverto=1;
                    $driverfrom=1;
                    $origin=$request['origin'][$key];
                    $origin_type="store";
                    $destination=$request['store_id'];
                    $destination_type="store";
                    $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
                    //echo '<pre>';print_r($dist);
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $drive1 = number_format((float)($distance)/50, 2, '.', '');

                    $origin=$request['store_id'];
                    $origin_type="store";
                    $destination=$request['emp_jsa'][$key];
                    $destination_type="office";
                    $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
                    //echo '<pre>';print_r($dist);
                    $distance1 = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $drive2 = number_format((float)($distance1)/50, 2, '.', '');

                    $time_array[$emp_id]['drivetime']=$drive1+$drive2;
                    $time_array[$emp_id]['drivetime'] = $this->convertTimeToColon($time_array[$emp_id]['drivetime']);
                    $time_array[$emp_id]['vehicle_travel']=$distance+$distance1;
                }elseif($driver_to)
                {
                    //echo 'tt'.$driver_to.'--'.$request['employee_id'][$key].'--'.$driver_from.'kk';
                    //Driver To
                    $driverto=1;
                    $origin=$request['origin'][$key];
                    $origin_type="store";
                    $destination=$request['store_id'];
                    $destination_type="store";
                    $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $time_array[$emp_id]['drivetime']=number_format((float)$distance/50, 2, '.', '');
                    $time_array[$emp_id]['drivetime'] = $this->convertTimeToColon($time_array[$emp_id]['drivetime']);

                    $origin=$request['store_id'];
                    $origin_type="store";
                    $destination=$request['emp_jsa'][$key];
                    $destination_type="office";
                    $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance1 = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $time_array[$emp_id]['jsa_miles']=ceil($distance1-12);
                    $time_array[$emp_id]['vehicle_travel']=$distance;
                }elseif($driver_from)
                {
                    //echo 't'.$driver_to.'--'.$request['employee_id'][$key].'--'.$driver_from.'k';
                    //Driver From
                    $driverfrom=1;
                    $origin=$request['origin'][$key];
                    $origin_type="store";
                    $destination=$request['store_id'];
                    $destination_type="store";
                    $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $time_array[$emp_id]['jsa_miles']=$distance;
                    

                    $origin=$request['store_id'];
                    $origin_type="store";
                    $destination=$request['emp_jsa'][$key];
                    $destination_type="office";
                    $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance1 = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $time_array[$emp_id]['drivetime']=number_format((float)$distance1/50, 2, '.', '');
                    $time_array[$emp_id]['drivetime'] = $this->convertTimeToColon($time_array[$emp_id]['drivetime']);
                    $time_array[$emp_id]['vehicle_travel']=$distance1;
                }else{
                    //echo 'tt'.$request['employee_id'][$key].'kk';
                    $origin=$request['origin'][$key];
                    $origin_type="store";
                    $destination=$request['store_id'];
                    $destination_type="store";
                    $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $time_array[$emp_id]['jsa_miles']=$distance;
                    
                    $origin=$request['store_id'];
                    $origin_type="store";
                    $destination=$request['emp_jsa'][$key];
                    $destination_type="office";
                    $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance1 = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $time_array[$emp_id]['jsa_miles']+=ceil($distance1-12);
                }
            }
        }
        }
        }
        return Response::json(array('time_array'=>$time_array),200);
    }

    function convertTimeToColon($dec)
    {
        $hour = floor($dec);
        $min = round(60*($dec - $hour));
        if(strlen($min)==1)
            $min='10';
            //$min='0'.$min;
        return $hour.':'.$min;
    }
    
    function originHaveEvent($store_id,$date)
    {
        $data = DB::table('events')
                ->select('id')
                ->where('store_id','=',$store_id)
                ->where('date','=',$date)
                ->first();
        if($data)
            return $data;
        else
            return null;
    }

    public function approvalWindow(Request $request,$id)
    {
        if (! Gate::allows('timesheet_edit')) {
            return abort(401);
        }
        
        // $timesheet = Timesheet::with('store')->findOrFail($id);
        // $store = DB::table('stores')->where('id', $timesheet->store_id)->first();//Store::find(1674)->toSql();
        // dd($store);
        
        $stores = DB::table('stores')->pluck('name','id');
        $timesheet = Timesheet::with(array('vehicles','emp_data','store','event'))->findOrFail($id);

        if(!$timesheet->store) $timesheet->store = DB::table('stores')->where('id', $timesheet->store_id)->first();

        
        $timesheet_submitted = DB::table('timesheet_header')
                ->where('event_id','=',@$timesheet->event->id)
                ->where(function ($query) {
                    $query->where('status', '=','Approved')
                          ->orWhere('status', '=','Submitted');
                })
                ->get();
        if($timesheet_submitted->isEmpty())
            $timesheet_submitted = 0;
        else
            $timesheet_submitted = 1;
        //echo '<pre>';print_r($timesheet_submitted);die;
        $employees = DB::table('timesheet_data')
            ->select('timesheet_data.*','timesheet_data.timesheet_id as timesheet_id','timesheet_data.employee_id as employee_ssn','employees.*','employees.id as employee_id',
                    'jsas.*','jsas.id as emp_jsa_id','areas.area_number as area_number','areas.title as area_title','areas.id as area_id','origin.id as origin',
                    'dest.id as destination')
            ->leftJoin('employees','employees.id','=','timesheet_data.employee_id')
            ->leftJoin('jsas','jsas.id','=','employees.jsa_id')
            ->leftJoin('areas','areas.id','=','employees.area_id')      
            ->leftJoin('stores as origin','origin.id','=','timesheet_data.sStoreOrigin')
            ->leftJoin('stores as dest','dest.id','=','timesheet_data.sStoreReturn')
            //->leftJoin('areas','employees.area_id','=','areas.id')
                
            ->where('timesheet_data.timesheet_id','=',$id)
            ->orderBy('employees.last_name', 'ASC')
            ->orderBy('employees.first_name', 'ASC')
            //->orderBy('areas.area_number', 'ASC')
            //->orderBy('events.run_number', 'ASC')
            ->get();
        //echo '<pre>';print_r($employees);die;
        return view('admin.timesheets.approvalwindow', compact('timesheet','stores','employees','timesheet_submitted'));
    }
    
    public function approve(UpdateTimesheetsRequest $request, $id)
    {
        if (! Gate::allows('timesheet_edit')) {
            return abort(401);
        }
        //echo '<pre>';print_r($request->all());die;
        $pay_date=date('Y-m-d',strtotime($request['pay_date']));
        if($request->submit=="Approve")
        {
            $is_error = 0;
            if($is_error)
                return redirect()->route('admin.timesheets.approval',[$id])->withErrors($errors);
            else
            {
                TimesheetApproved::where('timesheet_id', '=',$id)->delete();
                Timeentries::where('timesheet_id', '=',$id)->delete();
                $cost_center3 = $request['storenumber'];
                $store_bench = DB::table('stores')
                        ->select('benchmark','pieces_or_dollars')
                        ->where('id','=',$request['store_id'])
                        ->first();
                $demp = DB::table('timesheet_header')
                        ->select('dEmpCount','dEmpPieces')
                        ->where('id','=',$id)
                        ->first();
                if($store_bench->pieces_or_dollars=="dollars")
                {
                    $where = array('id' => $request['store_id']);
                    $updateArr = ['inventory_level' => $demp->dEmpCount];
                    $event  = Store::where($where)->update($updateArr);
                }else{
                    $where = array('id' => $request['store_id']);
                    $updateArr = ['inventory_level' => $demp->dEmpPieces];
                    $event  = Store::where($where)->update($updateArr);
                }
                foreach($request['employee_number'] as $key=>$employee_number)
                {
                    if($employee_number)
                    {
                    $driver_to=0;
                    $driver_from=0;
                    $supervisor=0;
                    if(isset($request['exclude_employee'][$employee_number]) && $request['exclude_employee'][$employee_number])
                        $exclude_employee=1;
                    else
                        $exclude_employee=0;
                    if(isset($request['employee_status'][$request['employee_id'][$key]]))
                    {
                        if(in_array("Driver To",$request['employee_status'][$request['employee_id'][$key]]))
                                $driver_to=1;
                        if(in_array("Driver From",$request['employee_status'][$request['employee_id'][$key]]))
                                $driver_from=1;
                        if(in_array("Supervisor",$request['employee_status'][$request['employee_id'][$key]]))
                                $supervisor=1;
                    }
                    if(isset($request['vehicle_travel'][$key]) && $request['vehicle_travel'][$key])
                        $vehicle_travel=$request['vehicle_travel'][$key];
                    else
                        $vehicle_travel=0;
                    $emp_bench = DB::table('employees')
                        ->select('week4_benchmark','payrate','area_id')
                        ->where('id','=',$request['employee_id'][$key])
                        ->first();
                    
                    if($supervisor)
                        $supervisor_area_id=$emp_bench->area_id;
                    else
                        $supervisor_area_id=0;
                    $empdata = array('timesheet_id'=>$id,'employee_id'=>$request['employee_id'][$key],
                        'is_supervisor'=>$supervisor,"driver_to"=>$driver_to,
                        "driver_from"=>$driver_from,"store_hours"=>$request['store_hours'][$key],
                        "PIMTime"=>$request['PIMTime'][$key],"iWaitTime"=>$request['iWaitTime'][$key],
                        "iLunch1"=>$request['iLunch1'][$key],"iLunch2"=>$request['iLunch2'][$key],
                        "drive_time"=>$request['drive_time'][$key],"travel_time"=>$request['travel_time'][$key],
                        "origin"=>$request['origin'][$key],"destination"=>$request['destination'][$key],
                        "jsa_miles"=>$request['jsa_miles'][$key],"vehicle_travel"=>$vehicle_travel,'current_benchmark'=>$emp_bench->week4_benchmark,
                        'current_payrate'=>$emp_bench->payrate,'supervisor_area_id'=>$supervisor_area_id,'store_benchmark'=>$store_bench->benchmark);
                    
                    DB::table('timesheet_approved')->where('timesheet_id', '=', $id)->where('employee_id', '=',$request['employee_id'][$key])->delete();
                    DB::table('timesheet_approved')->insert($empdata);
//                echo '<pre>';
//                print_r($request->all());die;
                    if($request['iAttendanceFlag'][$key]==3 || $request['iAttendanceFlag'][$key]==4)
                    {
                        //$area_number=$request['emp_area'][$key];
                        $area_number=$request['supervisor_area_number'];
                        $total_time=.01;
                        $time_off='No Show';
                        $event_date=$request['event_date'];
                        $pointdata2 = array('cost_center3'=>$cost_center3,'exclude_employee'=>$exclude_employee,"area_number"=>$area_number,'time_off'=>$time_off,'custom1'=>'','timesheet_id'=>$id,'status'=>1,'employee_number'=>$employee_number,"pay_date"=>$event_date,"total_time"=>$total_time);
                        DB::table('time_entries')->insert($pointdata2);
                    }else{
                        //$area_number=$request['emp_area'][$key];
                        $area_number=$request['supervisor_area_number'];
                        if(str_replace(':','.',$request['store_hours'][$key])!=0 && $request['store_hours'][$key]!='0:00' && $request['store_hours'][$key]!='00:00')
                        {
                            $total_time=str_replace('.',':',$request['store_hours'][$key]);
                            $piece_quantity=NULL;
                            $cost_center2='Regular';

                            $custom2 = number_format($request['dEmpCount'][$key]/$request['store_decimal_hours'][$key]);
                            $custom3 = number_format($request['dEmpPieces'][$key]/$request['store_decimal_hours'][$key]);
                            //$custom4 = number_format((float)($request['iBreakTime'][$key]-($request['iLunch1'][$key]+$request['iLunch2'][$key])), 2, '.', '');
                            $custom4 = round($request['iGapTime'][$key]);
                            $pointdata2 = array('cost_center3'=>$cost_center3,'exclude_employee'=>$exclude_employee,'custom1'=>'','custom2'=>$custom2,'custom3'=>$custom3,'custom4'=>$custom4,'timesheet_id'=>$id,'status'=>1,'employee_number'=>$employee_number,"pay_date"=>$pay_date,"total_time"=>$total_time,"piece_quantity"=>$piece_quantity,"area_number"=>$area_number,"cost_center2"=>$cost_center2);
                            DB::table('time_entries')->insert($pointdata2);
                        }
                        if(str_replace(':','.',$request['drive_time'][$key])!=0 && $request['drive_time'][$key]!='0:00' && $request['drive_time'][$key]!='00:00')
                        {
                            $total_time=$request['drive_time'][$key];
                            $piece_quantity=NULL;
                            $cost_center2='Driving Pay';
                            $pointdata2 = array('cost_center3'=>$cost_center3,'exclude_employee'=>$exclude_employee,'custom1'=>$request['storename'],'timesheet_id'=>$id,'status'=>1,'employee_number'=>$employee_number,"pay_date"=>$pay_date,"total_time"=>$total_time,"piece_quantity"=>$piece_quantity,"area_number"=>$area_number,"cost_center2"=>$cost_center2);
                            DB::table('time_entries')->insert($pointdata2);
                        }
                        if(str_replace(':','.',$request['travel_time'][$key])!=0 && $request['travel_time'][$key]!='0:00' && $request['travel_time'][$key]!='00:00')
                        {
                            $total_time=$request['travel_time'][$key];
                            $piece_quantity=NULL;
                            $cost_center2='Travel Pay';
                            $pointdata2 = array('cost_center3'=>$cost_center3,'exclude_employee'=>$exclude_employee,'custom1'=>$request['storename'],'timesheet_id'=>$id,'status'=>1,'employee_number'=>$employee_number,"pay_date"=>$pay_date,"total_time"=>$total_time,"piece_quantity"=>$piece_quantity,"area_number"=>$area_number,"cost_center2"=>$cost_center2);
                            DB::table('time_entries')->insert($pointdata2);
                        }
                        if($request['jsa_miles'][$key]>0)
                        {
                            $total_time=NULL;
                            $piece_quantity=$request['jsa_miles'][$key];
                            $cost_center2='JSA';
                            $pointdata2 = array('cost_center3'=>$cost_center3,'exclude_employee'=>$exclude_employee,'custom1'=>$request['storename'],'timesheet_id'=>$id,'status'=>1,'employee_number'=>$employee_number,"pay_date"=>$pay_date,"total_time"=>$total_time,"piece_quantity"=>$piece_quantity,"area_number"=>$area_number,"cost_center2"=>$cost_center2);
                            DB::table('time_entries')->insert($pointdata2);
                        }
                    }
                }
            }
                $where = array('id' => $id);
                $updateArr = ['status' => 'Approved','approved_by'=>auth()->user()->id,'approved_on'=>date('Y-m-d H:i:s')];
                $event  = Timesheet::where($where)->update($updateArr);
                return redirect()->route('admin.timesheets.index')->with('successmsg', 'Timesheet approved successfully.');
            }
        }elseif($request->submit=="Reject")
        {
            $user_detail = array(
                'name'            => 'Team',
                'email'           => 'timesheetnotify@msi-inv.com',
                'comment'         => $request['rejection_reason'],
                'mail_from_email' => env('MAIL_FROM'),
                'mail_from'       => env('MAIL_NAME'),
                'store'           => $request['storename'],
                'date'            => date('m-d-Y',strtotime($request['pay_date'])),
                'subject'         => 'Store '.$request['storename'].' Timecard Rejected'
            );
            $user_single = (object) $user_detail;
            Mail::send('emails.timesheet_rejection_notification',['user' => $user_single], function ($message) use ($user_single) {
                $message->from($user_single->mail_from_email,$user_single->mail_from);
                $message->to($user_single->email, $user_single->name)->subject($user_single->subject);
                $message->replyTo($user_single->mail_from_email,$user_single->mail_from);
            });
            
            $team_supervisor = DB::table('timesheet_data')
                ->select('employees.*')
                ->leftJoin('employees','employees.id','=','timesheet_data.employee_id')
                ->where('timesheet_data.bIsSuper','=',1)
                ->where('timesheet_data.timesheet_id','=',$id)
                ->first();
            if($team_supervisor && $team_supervisor->email)
            {
                $user_detail = array(
                    'name'            => $team_supervisor->first_name,
                    'email'           => $team_supervisor->email,
                    'comment'         => $request['rejection_reason'],
                    'mail_from_email' => env('MAIL_FROM'),
                    'mail_from'       => env('MAIL_NAME'),
                    'store'           => $request['storename'],
                    'date'            => date('m-d-Y',strtotime($request['pay_date'])),
                    'subject'         => 'Store '.$request['storename'].' Timecard Rejected'
                );
                $user_single = (object) $user_detail;
                Mail::send('emails.timesheet_rejection_notification',['user' => $user_single], function ($message) use ($user_single) {
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
                ->select('employees.email')
                ->whereIn('area_id',$event_areas)
                ->where('title','=','Area Manager')
                ->get();
            if($area_managers)
            {
                foreach($area_managers as $area_manager)
                {
                    $user_detail = array(
                        
                        'name'            => @$area_manager->first_name,
                        'email'           => $area_manager->email,
                        'comment'         => $request['rejection_reason'],
                        'mail_from_email' => env('MAIL_FROM'),
                        'mail_from'       => env('MAIL_NAME'),
                        'store'           => $request['storename'],
                        'date'            => date('m-d-Y',strtotime($request['pay_date'])),
                        'subject'         => 'Store '.$request['storename'].' Timecard Rejected'
                    );
                    $user_single = (object) $user_detail;
                    Mail::send('emails.event_qc_send_notification',['user' => $user_single], function ($message) use ($user_single) {
                        $message->from($user_single->mail_from_email,$user_single->mail_from);
                        $message->to($user_single->email, $user_single->name)->subject($user_single->subject);
                        $message->replyTo($user_single->mail_from_email,$user_single->mail_from);
                    });
                }
            }
            
            $where = array('id' => $id);
            $updateArr = ['status' => 'Rejected','rejection_reason'=>$request['rejection_reason']];
            $event  = Timesheet::where($where)->update($updateArr);
            return redirect()->route('admin.timesheets.index')->with('successmsg', 'Timesheet rejected successfully.');
        }
    }
    
    public function approved(){
        if (! Gate::allows('timesheet_view')) {
            return abort(401);
        }
        
        $timesheets = DB::table('timesheet_header')
            ->select('timesheet_header.*','stores.name as storename','events.run_number',
                    'employees.first_name','employees.last_name','areas.area_number')
            ->leftJoin('stores','stores.id','=','timesheet_header.store_id')
            ->leftJoin('events','events.id','=','timesheet_header.event_id')      
            ->leftJoin('timesheet_data','timesheet_data.timesheet_id','=','timesheet_header.id')
            ->leftJoin('employees','employees.id','=','timesheet_data.employee_id')
            ->leftJoin('areas','employees.area_id','=','areas.id')
            ->where('timesheet_data.bIsSuper','=',1)
            ->where('timesheet_header.status','Approved')
            ->where('timesheet_header.dtJobDate','>','2020-04-14')
            ->orderBy('timesheet_header.dtJobDate', 'ASC')
            ->orderBy('areas.area_number', 'ASC')
            ->orderBy('events.run_number', 'ASC')
            ->get();
//        $timesheets = Timesheet::with(array('vehicles','emp_data','store'))->where('status','Approved');
//        $timesheets = $timesheets->orderBy('id', 'DESC')->get();
        
        //print_r($timesheets);die;
        //$users = User::with(array('city','state','country','parentuser'))->where('user_type', '=', 'member')->get();
        $pending_time_entries = DB::table('time_entries_queue')
                ->where('status','=',1)
                ->get();
        if($pending_time_entries->isEmpty())
            $pending_timesheets=0;
        else
            $pending_timesheets=1;
        return view('admin.timesheets.approved', compact('timesheets','pending_timesheets'));
    }
    
    public function restore_timesheets($id)
    {
        if (! Gate::allows('timesheet_edit')) {
            return abort(401);
        }
        TimesheetApproved::where('timesheet_id', '=',$id)->delete();
        Timeentries::where('timesheet_id', '=',$id)->delete();
        $where = array('id' => $id);
        $updateArr = ['status' => 'Pending'];
        $event  = Timesheet::where($where)->update($updateArr);
        return redirect()->route('admin.timesheets.index')->with('successmsg', 'Timesheet restored successfully.');
    }
    
    public function rejected_timesheets(){
        if (! Gate::allows('timesheet_view')) {
            return abort(401);
        }
        
        $timesheets = DB::table('timesheet_header')
            ->select('timesheet_header.*','stores.name as storename','events.run_number',
                    'employees.first_name','employees.last_name','areas.area_number')
            ->leftJoin('stores','stores.id','=','timesheet_header.store_id')
            ->leftJoin('events','events.id','=','timesheet_header.event_id')      
            ->leftJoin('timesheet_data','timesheet_data.timesheet_id','=','timesheet_header.id')
            ->leftJoin('employees','employees.id','=','timesheet_data.employee_id')
            ->leftJoin('areas','employees.area_id','=','areas.id')
            ->where('timesheet_data.bIsSuper','=',1)
            ->where('timesheet_header.status','Rejected')
            ->where('timesheet_header.dtJobDate','>','2020-04-14')
            ->orderBy('timesheet_header.dtJobDate', 'ASC')
            ->orderBy('areas.area_number', 'ASC')
            ->orderBy('events.run_number', 'ASC')
            ->get();
//        $timesheets = Timesheet::with(array('vehicles','emp_data','store'))->where('status','Rejected');
//        $timesheets = $timesheets->orderBy('id', 'DESC')->get();
        
        //print_r($timesheets);die;
        //$users = User::with(array('city','state','country','parentuser'))->where('user_type', '=', 'member')->get();
         
        return view('admin.timesheets.rejected', compact('timesheets'));
    }
    
    public function submitted_timesheets(){
        if (! Gate::allows('timesheet_view')) {
            return abort(401);
        }
        
        $timesheets = DB::table('timesheet_header')
            ->select('timesheet_header.*','stores.name as storename','events.run_number',
                    'employees.first_name','employees.last_name','areas.area_number')
            ->leftJoin('stores','stores.id','=','timesheet_header.store_id')
            ->leftJoin('events','events.id','=','timesheet_header.event_id')      
            ->leftJoin('timesheet_data','timesheet_data.timesheet_id','=','timesheet_header.id')
            ->leftJoin('employees','employees.id','=','timesheet_data.employee_id')
            ->leftJoin('areas','employees.area_id','=','areas.id')
            ->where('timesheet_data.bIsSuper','=',1)
            ->where('timesheet_header.status','Submitted')
            ->where('timesheet_header.dtJobDate','>','2020-04-14')
            ->orderBy('timesheet_header.dtJobDate', 'DESC')
            ->orderBy('areas.area_number', 'ASC')
            ->orderBy('events.run_number', 'ASC')
            ->get();
        
         
        return view('admin.timesheets.submitted', compact('timesheets'));
    }
    public function excused(Request $request){
        
        if (! Gate::allows('excused_employee')) {
            return abort(401);
        }

        $employeest = DB::table('employees')->get();
        $areas = DB::table('areas')->pluck('title','id');
        return view('admin.timesheets.excused', compact('employeest', 'areas'));
    }
    
    public function indexexcused(Request $request)
    {
        

         $request->session()->forget('emp_number');
         $request->session()->forget('area_id');
         $request->session()->forget('date_between');
         
        $draw = $request->get('draw');
        $start = $request->get("start");

        $searchQuery = " ";

        if($request['emp_number'] != ''){
         $request->session()->put('emp_number',$request['emp_number']);
         $searchQuery .= " and (employees.emp_number in(".$request['emp_number'].")) ";
        }else{
             $request->session()->forget('emp_number');
         }
 

         if($request['city_id'] != ''){
            $request->session()->put('area_id',$request['city_id']);
            $searchQuery .= " and (cities.id in(".$request['city_id'].")) ";
         }
         else{
            $request->session()->forget('city_id');
        }

         if($request['date_between'] != ''){
             $request->session()->put('date_between',$request['date_between']);
 
             $date_between = explode(' - ',$request['date_between']);
             $date_from = date('Y-m-d', strtotime($date_between[0]));
             $date_to = date('Y-m-d', strtotime($date_between[1]));
             $searchQuery .= " and (events.date between '".$date_from."' and '".$date_to."') ";
         }else{
             $request->session()->forget('date_between');
         }


            $query = DB::table('event_schedule_employees')
            ->leftJoin('timesheet_header','timesheet_header.event_id','=','event_schedule_employees.event_id')
            ->leftJoin('events','events.id','=','event_schedule_employees.event_id')
            ->leftJoin('employees','employees.id','=','event_schedule_employees.employee_id')
            ->leftJoin('areas', 'areas.id', '=' ,'event_schedule_employees.area_id')
            ->rightJoin('stores','stores.id','=','events.store_id')
             ->select('event_schedule_employees.*')
             ->addSelect([
              'stores.name as s_name',  
              'employees.name as e_name',
              'employees.emp_number as e_no',
               'timesheet_header.event_id as e_id',
               'event_schedule_employees.employee_id as ev_id',
               'event_schedule_employees.area_id as aread',
               'event_schedule_employees.id as sh_id',
               'events.date as edate'
             ]);


             if($searchQuery == ' '){
                $query->where('timesheet_header.status', '=','Approved');
             }

             if($request->session()->exists('emp_number')){
                $query->where('employees.emp_number', $request->session()->get('emp_number'));
                $query->where('timesheet_header.status', '=','Approved');
            }


            if($request->session()->exists('area_id')){
                $query->where('event_schedule_employees.area_id', $request->session()->get('area_id'));
                $query->where('timesheet_header.status', '=','Approved');
            }


            if($request->session()->exists('date_between')){
                $date_between = explode(' - ',$request['date_between']);
                $date_from = date('Y-m-d', strtotime($date_between[0]));
                $date_to = date('Y-m-d', strtotime($date_between[1]));

                 $query->whereBetween('event_schedule_employees.created_at',[$date_from, $date_to]);
                 $query->where('timesheet_header.status', '=','Approved');   
            }


           $stores = $query->get();
           $totalRecordwithFilter = count($stores);
           
     
                  
                    $records = DB::table('event_schedule_employees')
                    ->leftJoin('timesheet_header','timesheet_header.event_id','=','event_schedule_employees.event_id')
                    ->leftJoin('events','events.id','=','event_schedule_employees.event_id')
                    ->leftJoin('employees','employees.id','=','event_schedule_employees.employee_id')
                    ->rightJoin('stores','stores.id','=','events.store_id')
                     ->select('event_schedule_employees.*')
                     ->addSelect([
                      'stores.name as s_name',  
                      'employees.name as e_name',
                      'employees.emp_number as e_no',
                       'timesheet_header.event_id as e_id',
                       'event_schedule_employees.employee_id as ev_id',
                       'event_schedule_employees.area_id as aread',
                       'event_schedule_employees.id as sh_id',
                       'events.date as edate'
                   ])
                    ->where('timesheet_header.status', '=','Approved')
                    ->get();
                    
                    
                    $totalRecords = DB::table('event_schedule_employees')
                    ->leftJoin('timesheet_header','timesheet_header.event_id','=','event_schedule_employees.event_id')
                    ->leftJoin('events','events.id','=','event_schedule_employees.event_id')
                    ->leftJoin('employees','employees.id','=','event_schedule_employees.employee_id')
                    ->rightJoin('stores','stores.id','=','events.store_id')
                     ->select('event_schedule_employees.*')
                     ->addSelect([
                      'stores.name as s_name',  
                      'employees.name as e_name',
                      'employees.emp_number as e_no',
                       'timesheet_header.event_id as e_id',
                       'event_schedule_employees.employee_id as ev_id',
                       'event_schedule_employees.area_id as aread',
                       'event_schedule_employees.id as sh_id',
                       'events.date as edate'
                   ])
                    ->where('timesheet_header.status', '=','Approved')
                    ->count();
        
        $records = DB::table('event_schedule_employees')
        ->leftJoin('timesheet_header','timesheet_header.event_id','=','event_schedule_employees.event_id')
        ->leftJoin('events','events.id','=','event_schedule_employees.event_id')
        ->leftJoin('employees','employees.id','=','event_schedule_employees.employee_id')
        ->rightJoin('stores','stores.id','=','events.store_id')
         ->select('event_schedule_employees.*')
         ->addSelect([
          'stores.name as s_name',  
          'employees.name as e_name',
          'employees.emp_number as e_no',
           'timesheet_header.event_id as e_id',
           'event_schedule_employees.employee_id as ev_id',
           'event_schedule_employees.area_id as aread',
           'event_schedule_employees.id as sh_id',
           'events.date as edate'
       ])
        ->where('timesheet_header.status', '=','Approved')
        ->get();
        
        $totalRecordwithFilter = DB::table('event_schedule_employees')
        ->leftJoin('timesheet_header','timesheet_header.event_id','=','event_schedule_employees.event_id')
        ->leftJoin('events','events.id','=','event_schedule_employees.event_id')
        ->leftJoin('employees','employees.id','=','event_schedule_employees.employee_id')
        ->rightJoin('stores','stores.id','=','events.store_id')
         ->select('event_schedule_employees.*')
         ->addSelect([
          'stores.name as s_name',  
          'employees.name as e_name',
          'employees.emp_number as e_no',
           'timesheet_header.event_id as e_id',
           'event_schedule_employees.employee_id as ev_id',
           'event_schedule_employees.area_id as aread',
           'event_schedule_employees.id as sh_id',
           'events.date as edate'
       ])
        ->where('timesheet_header.status', '=','Approved')
        ->count();


       $data = array();
       foreach($stores as $row) {
        $id=$row->sh_id;
        $e_id=$row->e_id;
        $e_no=$row->e_no;
        $e_name=$row->e_name;
        $s_name=$row->s_name;
        $edate=$row->edate;
        
        
        
       $data[] = array(
        "id"=>$id,
        "e_id"=>$e_id,
        "e_no"=>$e_no,
        "e_name"=>$e_name,
        "s_name"=>$s_name,
        "edate"=>$edate,
       
       );
    }
    $response = array(
        "draw" => intval($draw),
        "iTotalRecords" => $totalRecords,
        "iTotalDisplayRecords" => $totalRecordwithFilter,
        "aaData" => $data
      );
    //  return response()->json($response);
     echo json_encode($response);
    }
    
    public function import_list()
    {
        if (! Gate::allows('timesheet_view')) {
            return abort(401);
        }

        $timeentries = DB::table('time_entries_import_log')->get();
        //print_r($timesheets);die;
        return view('admin.timesheets.import_list', compact('timeentries'));
    }
    public function import_time_entries_manually(TimeSheetRepository $time_sheet_repository)
    {
        $this->import_inventory_evaluation();
        $this->import_gap_report();
        $this->import_time_entries($time_sheet_repository);
        return redirect()->route('admin.timesheets.import_list')->with('successmsg', 'Time Entries imported successfully.');
    }

    public function import_time_entries(TimeSheetRepository $time_sheet_repository)
    {

        $latest_time_entries_log = DB::table('time_entries_import_log')->orderBy('id', 'DESC')->first();
        $import_upto = $latest_time_entries_log->import_upto;

        $sly = new \App\Services\Sly();
        
        $data1 = $sly->getTimeSheet($import_upto);

        // dd($data1);

        if($data1 && count($data1)){
            foreach($data1 as $counter=>$row)
            {
                if($row['lstTimesheetHeader'][0]['EventNo'])
                {
                $timesheet =  $time_sheet_repository->createTimeSheet($row['lstTimesheetHeader'][0]);
                $timesheet_id = $timesheet->id;

                foreach($row['lstTimesheetVehicles'] as $vehicle){
                    $is_flagged=0;
                    $driver_to=0;
                    $driver_from=0;
                    if($vehicle['sSSN']){
                        $driver_to = Employee::select('id')->where('ss_no','=',$vehicle['sSSN'])->first();
                        if($driver_to)
                        {
                            $driver_to=$driver_to['id'];
                        }else
                        {
                            $where = array('id' => $timesheet_id);
                            $updateArr = ['is_flagged' => 1];
                            $event  = Timesheet::where($where)->update($updateArr);
                            $driver_to = $vehicle['sSSN'];
                            $is_flagged=1;
                        }
                    }
                    if($vehicle['sSSN2']){
                        $driver_from = Employee::select('id')->where('ss_no','=',$vehicle['sSSN2'])->first();
                        if($driver_from)
                        {
                            $driver_from=$driver_from['id'];
                        }else{
                            $where = array('id' => $timesheet_id);
                            $updateArr = ['is_flagged' => 1];
                            $event  = Timesheet::where($where)->update($updateArr);
                            $driver_from = $vehicle['sSSN2'];
                            $is_flagged=1;
                        }
                    }
                    $dtToStoreStart=NULL;
                    if($vehicle['dtToStoreStart'])
                        $dtToStoreStart = date('Y-m-d H:i:s',strtotime($vehicle['dtToStoreStart']));
                    $dtToStoreEnd=NULL;
                    if($vehicle['dtToStoreEnd'])
                        $dtToStoreEnd = date('Y-m-d H:i:s',strtotime($vehicle['dtToStoreEnd']));
                    $dtFromStoreStart=NULL;
                    if($vehicle['dtFromStoreStart'])
                        $dtFromStoreStart = date('Y-m-d H:i:s',strtotime($vehicle['dtFromStoreStart']));
                    $dtFromStoreEnd=NULL;
                    if($vehicle['dtFromStoreEnd'])
                        $dtFromStoreEnd = date('Y-m-d H:i:s',strtotime($vehicle['dtFromStoreEnd']));
                    $vehicle_data = array('timesheet_id'=>$timesheet_id,
                        'idVehicle'=>$vehicle['idVehicle'],
                        'is_flagged'=>$is_flagged,
                        'driver_to'=>$driver_to,
                        'driver_from'=>$driver_from,
                        "dtToStoreStart"=>$dtToStoreStart,
                        "dtToStoreEnd"=>$dtToStoreEnd,
                        "dtFromStoreStart"=>$dtFromStoreStart,
                        "dtFromStoreEnd"=>$dtFromStoreEnd,
                        );
                    TimesheetVehicle::updateOrCreate( [
                                                        'timesheet_id' => $timesheet_id, 
                                                        'idVehicle'=>$vehicle['idVehicle'] 
                                                    ],
                                                    $vehicle_data);
                }

                foreach($row['lstTimesheetData'] as $data){
                    $is_flagged=0;
                    if($data['sStoreOrigin'] && $data['sStoreReturn'])
                    {
                    $employee_id=0;
                    if($data['sEmployeeSSN']){
                        $employee = Employee::select('id')->where('ss_no','=',$data['sEmployeeSSN'])->first();
                        if($employee)
                        {
                            $employee_id = $employee['id'];
                        }else{
                            $where = array('id' => $timesheet_id);
                            $updateArr = ['is_flagged' => 1];
                            $event  = Timesheet::where($where)->update($updateArr);
                            $employee_id = $data['sEmployeeSSN'];
                            $is_flagged=1;
                        }
                    }
                    if(strtolower($data['sStoreOrigin'])=="office"){
                        $sStoreOrigin='OFFICE';
                    }else{
                        $sStoreOrigin = Store::select('id')->where('number','=',$data['sStoreOrigin'])->first();
                        if(!$sStoreOrigin) {
                            $message = 'invalid store: '.$data['sStoreOrigin'].' in Event no: '.$row['lstTimesheetHeader'][0]['EventNo'];
                            //$message .= PHP_EOL.json_encode($row, JSON_PRETTY_PRINT);
                            \Log::channel('timesheet')->error($message);
                            continue;
                        }
                        $sStoreOrigin = $sStoreOrigin['id'];
                    }
                    if(strtolower($data['sStoreReturn'])=="office"){
                        $sStoreReturn = 'OFFICE';
                    }else
                    {
                        
                        $sStoreReturn = Store::select('id')->where('number','=',$data['sStoreReturn'])->first();
                        if(!$sStoreReturn) {
                            $message = 'invalid store: '.$data['sStoreReturn'].' in Event no: '.$row['lstTimesheetHeader'][0]['EventNo'];
                            //$message .= PHP_EOL.json_encode($row , JSON_PRETTY_PRINT);
                            \Log::channel('timesheet')->error($message);
                            continue;
                        }
                        $sStoreReturn = $sStoreReturn['id'];
                    }
                    $dtFirstScan=NULL;
                    if($data['dtFirstScan'])
                       $dtFirstScan = date('Y-m-d H:i:s',strtotime($data['dtFirstScan']));
                    $dtLastScan=NULL;
                    if($data['dtLastScan'])
                       $dtLastScan = date('Y-m-d H:i:s',strtotime($data['dtLastScan']));
                    $data_data = array('timesheet_id'=>$timesheet_id,
                        'employee_id'=>$employee_id,
                        'is_flagged'=>$is_flagged,
                        "dtStartDateTime"=>($data['dtStartDateTime'])?date('Y-m-d H:i:s',strtotime($data['dtStartDateTime'])):'',
                        "dtStopDateTime"=>($data['dtStopDateTime'])?date('Y-m-d H:i:s',strtotime($data['dtStopDateTime'])):'',
                        'dEmpCount'=>$data['dEmpCount'],
                        'dEmpPieces'=>$data['dEmpPieces'],
                        'sStoreOrigin'=>$sStoreOrigin,
                        'sStoreReturn'=>$sStoreReturn,
                        'bIsDriver'=>$data['bIsDriver'],
                        'bIsSuper'=>$data['bIsSuper'],
                        'iWaitTime'=>$data['iWaitTime'],
                        'iAttendanceFlag'=>$data['iAttendanceFlag'],
                        'sEmployeeComment'=>$data['sEmployeeComment'],
                        'TotalScans'=>$data['TotalScans'],
                        'ScansPerHour'=>$data['ScansPerHour'],
                        'PiecesPerHour'=>$data['PiecesPerHour'],
                        'dtFirstScan'=>$dtFirstScan,
                        'dtLastScan'=>$dtLastScan,
                        'WaitTimeExplanation'=>$data['WaitTimeExplanation'],
                        'WrapTimeExplanation'=>$data['WrapTimeExplanation'],
                        'iLunch1'=>$data['iLunch1'],
                        'iLunch2'=>$data['iLunch2'],
                        'iGapTime'=>$data['iGapTime'],
                        'GapTimeExplanation'=>$data['GapTimeExplanation'],
                        'AttendanceExplanation'=>$data['AttendanceExplanation'],
                        'PIMTime'=>$data['PIMTime'],
                        'iBreakTime'=>$data['iBreakTime']
                    );
                    
                    TimesheetData::updateOrCreate( [
                        'timesheet_id' => $timesheet_id, 
                        'employee_id'=>$employee_id, 
                    ],
                    $data_data);
                }
                }

               

            DB::table('time_entries_import_log')->where('id',$latest_time_entries_log->id)
                                                ->update(
                                                    [
                                                        'import_upto' => $row['lstTimesheetHeader'][0]['ALA_DeliveryDate'],
                                                        'imported_on' => date('Y-m-d H:i:s')
                                                     ]);

            }   
            }
        }
        return redirect()->route('admin.timesheets.import_list')->with('successmsg', 'Time Entries upto '.$import_upto.' imported successfully.');
    }
    
    public function import_time_entries_date_wise_old(Request $request,$date){
//        $timeentries = DB::table('time_entries_import_log')->orderBy('id', 'DESC')->first();
//        $date = date('Y-m-d',strtotime($timeentries->import_upto.' +1 day'));
        //$import_upto='';
        $ch = curl_init("http://".env('SLY_SERVER_ADDRESS')."/api/TimeSheet?ALADeliveryDate='".$date."'");
        $postRequest = array(
            'ALADeliveryDate'   => "'".$date."'",
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept:application/xml',
            'Content-Type:application/json'
        ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postRequest));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $curlResponse = curl_exec($ch);
        
        $data1 = json_decode($curlResponse,true);
       //echo count($data1);
        //$data1 = json_decode('[{"lstTimesheetHeader":[{"idTimesheet_SQL":"200708161148580137719","dtJobDate":"2007-05-30 00:00:00","idStoreNo":"CRACKE2","total_emp_count":56282,"total_emp_pieces":0,"mComments":"","InvRecapStartTime":"","InvRecapEndTime":"","InvRecapWrapTime":"","InvRecapComments":"","CrewNoShowCount":0,"TTLMH":0.0,"TotalBreakTime":0,"TotalWaitTime":0,"AccountName":"","InvStoreNumber":"","Benchmark":0,"CrewManagerSSN":""}],"lstTimesheetVehicles":[{"idVehicle":24,"sSSN":"345623856","sSSN2":"","dtToStoreStart":"","dtToStoreEnd":"","dtFromStoreStart":"","dtFromStoreEnd":""}],"lstTimesheetData":[{"sEmployeeSSN":"345623856","dEmpCount":0,"dEmpPieces":0,"sStoreOrigin":"CRACKER","sStoreReturn":"OFFICE","sBranch":"01","bIsDriver":true,"bIsSuper":true,"bIsLast":"True","iWaitTime":30,"iAttendanceFlag":1,"sEmployeeComment":"Supervisor","TotalScans":434,"ScansPerHour":357,"PiecesPerHour":0,"WaitTimeExplanation":"","WrapTimeExplanation":"","iLunch1":0,"iLunch2":0,"iGapTime":0,"GapTimeExplanation":"","AttendanceExplanation":"","PIMTime":0,"dtFirstScan":"","dtLastScan":"","dtStartDateTime":"2007-05-30 09:56:00","dtStopDateTime":"2007-05-30 11:09:00"},{"sEmployeeSSN":"428392416","dEmpCount":0,"dEmpPieces":0,"sStoreOrigin":"CRACKER","sStoreReturn":"OFFICE","sBranch":"01","bIsDriver":false,"bIsSuper":false,"bIsLast":"True","iWaitTime":24,"iAttendanceFlag":1,"sEmployeeComment":"recount/tags","TotalScans":652,"ScansPerHour":584,"PiecesPerHour":0,"WaitTimeExplanation":"","WrapTimeExplanation":"","iLunch1":0,"iLunch2":0,"iGapTime":0,"GapTimeExplanation":"","AttendanceExplanation":"","PIMTime":0,"dtFirstScan":"","dtLastScan":"","dtStartDateTime":"2007-05-30 09:56:00","dtStopDateTime":"2007-05-30 11:03:00"}]}]');
        //echo '<pre>';print_r($data1);die;
        foreach($data1 as $counter=>$row)
        {
             $InvRecapStartTime=NULL;
            if($row['lstTimesheetHeader'][0]['InvRecapStartTime'])
                $InvRecapStartTime=date('Y-m-d H:i:s',strtotime($row['lstTimesheetHeader'][0]['InvRecapStartTime']));
            $InvRecapEndTime=NULL;
            if($row['lstTimesheetHeader'][0]['InvRecapEndTime'])
                $InvRecapEndTime=date('Y-m-d H:i:s',strtotime($row['lstTimesheetHeader'][0]['InvRecapEndTime']));
            $InvRecapWrapTime=NULL;
            if($row['lstTimesheetHeader'][0]['InvRecapWrapTime'])
                $InvRecapWrapTime=date('Y-m-d H:i:s',strtotime($row['lstTimesheetHeader'][0]['InvRecapWrapTime']));
            $InvRecapArvlTime=NULL;
            if($row['lstTimesheetHeader'][0]['InvRecapArvlTime'])	
                $InvRecapArvlTime=date('Y-m-d H:i:s',strtotime($row['lstTimesheetHeader'][0]['InvRecapArvlTime']));
            
            $where = array('idTimesheet_SQL' => $row['lstTimesheetHeader'][0]['idTimesheet_SQL']);
            $updateArr = ['InvRecapArvlTime' => $InvRecapArvlTime,'InvRecapStartTime' => $InvRecapStartTime,
                'InvRecapEndTime' => $InvRecapEndTime,'InvRecapWrapTime' => $InvRecapWrapTime];
            $event  = Timesheet::where($where)->update($updateArr);
            
        }echo 'done';die;
        return redirect()->route('admin.timesheets.import_list')->with('successmsg', 'Time Entries upto '.$import_upto.' imported successfully.');
    }
    
    public function import_time_entries_date_wise(Request $request,$date){
//        $timeentries = DB::table('time_entries_import_log')->orderBy('id', 'DESC')->first();
//        $date = date('Y-m-d',strtotime($timeentries->import_upto.' +1 day'));
        //$import_upto='';
        $ch = curl_init("http://".env('SLY_SERVER_ADDRESS')."/api/TimeSheet?ALADeliveryDate='".$date."'");
        $postRequest = array(
            'ALADeliveryDate'   => "'".$date."'",
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept:application/xml',
            'Content-Type:application/json'
        ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postRequest));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $curlResponse = curl_exec($ch);
        
        $data1 = json_decode($curlResponse,true);
       //echo count($data1);
        //$data1 = json_decode('[{"lstTimesheetHeader":[{"idTimesheet_SQL":"200708161148580137719","dtJobDate":"2007-05-30 00:00:00","idStoreNo":"CRACKE2","total_emp_count":56282,"total_emp_pieces":0,"mComments":"","InvRecapStartTime":"","InvRecapEndTime":"","InvRecapWrapTime":"","InvRecapComments":"","CrewNoShowCount":0,"TTLMH":0.0,"TotalBreakTime":0,"TotalWaitTime":0,"AccountName":"","InvStoreNumber":"","Benchmark":0,"CrewManagerSSN":""}],"lstTimesheetVehicles":[{"idVehicle":24,"sSSN":"345623856","sSSN2":"","dtToStoreStart":"","dtToStoreEnd":"","dtFromStoreStart":"","dtFromStoreEnd":""}],"lstTimesheetData":[{"sEmployeeSSN":"345623856","dEmpCount":0,"dEmpPieces":0,"sStoreOrigin":"CRACKER","sStoreReturn":"OFFICE","sBranch":"01","bIsDriver":true,"bIsSuper":true,"bIsLast":"True","iWaitTime":30,"iAttendanceFlag":1,"sEmployeeComment":"Supervisor","TotalScans":434,"ScansPerHour":357,"PiecesPerHour":0,"WaitTimeExplanation":"","WrapTimeExplanation":"","iLunch1":0,"iLunch2":0,"iGapTime":0,"GapTimeExplanation":"","AttendanceExplanation":"","PIMTime":0,"dtFirstScan":"","dtLastScan":"","dtStartDateTime":"2007-05-30 09:56:00","dtStopDateTime":"2007-05-30 11:09:00"},{"sEmployeeSSN":"428392416","dEmpCount":0,"dEmpPieces":0,"sStoreOrigin":"CRACKER","sStoreReturn":"OFFICE","sBranch":"01","bIsDriver":false,"bIsSuper":false,"bIsLast":"True","iWaitTime":24,"iAttendanceFlag":1,"sEmployeeComment":"recount/tags","TotalScans":652,"ScansPerHour":584,"PiecesPerHour":0,"WaitTimeExplanation":"","WrapTimeExplanation":"","iLunch1":0,"iLunch2":0,"iGapTime":0,"GapTimeExplanation":"","AttendanceExplanation":"","PIMTime":0,"dtFirstScan":"","dtLastScan":"","dtStartDateTime":"2007-05-30 09:56:00","dtStopDateTime":"2007-05-30 11:03:00"}]}]');
        echo '<pre>';print_r($data1);die;
        foreach($data1 as $counter=>$row)
        {
            $store = Store::select('id')->where('number','=',$row['lstTimesheetHeader'][0]['idStoreNo'])->first();
            if($store){
                $store_id=$store->id;
                $is_flag=0;
            }else{
                $store_id=$row['lstTimesheetHeader'][0]['idStoreNo'];
                $is_flag=1;
            }
            $InvRecapStartTime=NULL;
            if($row['lstTimesheetHeader'][0]['InvRecapStartTime'])
                $InvRecapStartTime=date('Y-m-d H:i:s',strtotime($row['lstTimesheetHeader'][0]['InvRecapStartTime']));
            $InvRecapEndTime=NULL;
            if($row['lstTimesheetHeader'][0]['InvRecapEndTime'])
                $InvRecapEndTime=date('Y-m-d H:i:s',strtotime($row['lstTimesheetHeader'][0]['InvRecapEndTime']));
            $InvRecapWrapTime=NULL;
            if($row['lstTimesheetHeader'][0]['InvRecapWrapTime'])
                $InvRecapWrapTime=date('Y-m-d H:i:s',strtotime($row['lstTimesheetHeader'][0]['InvRecapWrapTime']));
            $InvRecapArvlTime=NULL;
            if($row['lstTimesheetHeader'][0]['InvRecapArvlTime'])	
                $InvRecapArvlTime=date('Y-m-d H:i:s',strtotime($row['lstTimesheetHeader'][0]['InvRecapArvlTime']));
            $timesheet_data = array('idTimesheet_SQL'=>$row['lstTimesheetHeader'][0]['idTimesheet_SQL'],
                'dtJobDate'=>$row['lstTimesheetHeader'][0]['dtJobDate'],
                "store_id"=>$store->id,
                "is_flagged"=>$is_flag,
                "dEmpCount"=>$row['lstTimesheetHeader'][0]['total_emp_count'],
                "dEmpPieces"=>$row['lstTimesheetHeader'][0]['total_emp_pieces'],
                "mComments"=>$row['lstTimesheetHeader'][0]['mComments'],
                "InvRecapStartTime"=>$InvRecapStartTime,
                "InvRecapEndTime"=>$InvRecapEndTime,
                "InvRecapWrapTime"=>$InvRecapWrapTime,
                "InvRecapComments"=>$row['lstTimesheetHeader'][0]['InvRecapComments'],
                "CrewNoShowCount"=>$row['lstTimesheetHeader'][0]['CrewNoShowCount'],
                "TTLMH"=>$row['lstTimesheetHeader'][0]['TTLMH'],
                "TotalBreakTime"=>$row['lstTimesheetHeader'][0]['TotalBreakTime'],
                "TotalWaitTime"=>$row['lstTimesheetHeader'][0]['TotalWaitTime'],
                "AccountName"=>$row['lstTimesheetHeader'][0]['AccountName'],
                "InvStoreNumber"=>($row['lstTimesheetHeader'][0]['InvStoreNumber'])?$row['lstTimesheetHeader'][0]['InvStoreNumber']:0,
                "Benchmark"=>$row['lstTimesheetHeader'][0]['Benchmark'],
                "CrewManagerSSN"=>$row['lstTimesheetHeader'][0]['CrewManagerSSN'],
                "ALA_DeliveryDate"=>$row['lstTimesheetHeader'][0]['ALA_DeliveryDate'],
                "InvRecapArvlTime"=>$InvRecapArvlTime,	
                "event_id"=>$row['lstTimesheetHeader'][0]['EventNo'],
                "status"=>'Pending');
            $timesheet_header = DB::table('timesheet_header')->insert($timesheet_data);
            $timesheet_id = DB::getPdo()->lastInsertId();
            //print_r($timesheet_data);
            
            foreach($row['lstTimesheetVehicles'] as $vehicle){
                $is_flagged=0;
                $driver_to=0;
                $driver_from=0;
                if($vehicle['sSSN']){
                    $driver_to = Employee::select('id')->where('ss_no','=',$vehicle['sSSN'])->first();
                    if($driver_to)
                    {
                        $driver_to=$driver_to['id'];
                    }else
                    {
                        $where = array('id' => $timesheet_id);
                        $updateArr = ['is_flagged' => 1];
                        $event  = Timesheet::where($where)->update($updateArr);
                        $driver_to = $vehicle['sSSN'];
                        $is_flagged=1;
                    }
                }
                if($vehicle['sSSN2']){
                    $driver_from = Employee::select('id')->where('ss_no','=',$vehicle['sSSN2'])->first();
                    if($driver_from)
                    {
                        $driver_from=$driver_from['id'];
                    }else{
                        $where = array('id' => $timesheet_id);
                        $updateArr = ['is_flagged' => 1];
                        $event  = Timesheet::where($where)->update($updateArr);
                        $driver_from = $vehicle['sSSN2'];
                        $is_flagged=1;
                    }
                }
                $dtToStoreStart=NULL;
                if($vehicle['dtToStoreStart'])
                    $dtToStoreStart = date('Y-m-d H:i:s',strtotime($vehicle['dtToStoreStart']));
                $dtToStoreEnd=NULL;
                if($vehicle['dtToStoreEnd'])
                    $dtToStoreEnd = date('Y-m-d H:i:s',strtotime($vehicle['dtToStoreEnd']));
                $dtFromStoreStart=NULL;
                if($vehicle['dtFromStoreStart'])
                    $dtFromStoreStart = date('Y-m-d H:i:s',strtotime($vehicle['dtFromStoreStart']));
                $dtFromStoreEnd=NULL;
                if($vehicle['dtFromStoreEnd'])
                    $dtFromStoreEnd = date('Y-m-d H:i:s',strtotime($vehicle['dtFromStoreEnd']));
                $vehicle_data = array('timesheet_id'=>$timesheet_id,
                    'idVehicle'=>$vehicle['idVehicle'],
                    'is_flagged'=>$is_flagged,
                    'driver_to'=>$driver_to,
                    'driver_from'=>$driver_from,
                    "dtToStoreStart"=>$dtToStoreStart,
                    "dtToStoreEnd"=>$dtToStoreEnd,
                    "dtFromStoreStart"=>$dtFromStoreStart,
                    "dtFromStoreEnd"=>$dtFromStoreEnd,
                    );
                $timesheet_vehicle = DB::table('timesheet_vehicle')->insert($vehicle_data);
            }
            
            foreach($row['lstTimesheetData'] as $data){
                $is_flagged=0;
                if($data['sStoreOrigin'] && $data['sStoreReturn'])
                {
                //print_r($data);die;
                $employee_id=0;
                if($data['sEmployeeSSN']){
                    $employee = Employee::select('id')->where('ss_no','=',$data['sEmployeeSSN'])->first();
                    if($employee)
                    {
                        $employee_id = $employee['id'];
                    }else{
                        $where = array('id' => $timesheet_id);
                        $updateArr = ['is_flagged' => 1];
                        $event  = Timesheet::where($where)->update($updateArr);
                        $employee_id = $data['sEmployeeSSN'];
                        $is_flagged=1;
                        //echo '<br>Employee SSN '.$data['sEmployeeSSN'].' not found';
                    }
                }
                if(strtolower($data['sStoreOrigin'])=="office"){
                    $sStoreOrigin=$data['sStoreOrigin'];
                }else{
                    $sStoreOrigin = Store::select('id')->where('number','=',$data['sStoreOrigin'])->first();
                    $sStoreOrigin=$sStoreOrigin['id'];
                }
                //echo strtolower($data['sStoreReturn']);
                if(strtolower($data['sStoreReturn'])=="office"){
                    $sStoreReturn = $data['sStoreReturn'];
                }else
                {
                    $sStoreReturn = Store::select('id')->where('number','=',$data['sStoreReturn'])->first();
                    $sStoreReturn=$sStoreReturn['id'];
                }
                $dtFirstScan=NULL;
                if($data['dtFirstScan'])
                   $dtFirstScan = date('Y-m-d H:i:s',strtotime($data['dtFirstScan']));
                $dtLastScan=NULL;
                if($data['dtLastScan'])
                   $dtLastScan = date('Y-m-d H:i:s',strtotime($data['dtLastScan']));
                $data_data = array('timesheet_id'=>$timesheet_id,
                    'employee_id'=>$employee_id,
                    'is_flagged'=>$is_flagged,
                    "dtStartDateTime"=>($data['dtStartDateTime'])?date('Y-m-d H:i:s',strtotime($data['dtStartDateTime'])):'',
                    "dtStopDateTime"=>($data['dtStopDateTime'])?date('Y-m-d H:i:s',strtotime($data['dtStopDateTime'])):'',
                    'dEmpCount'=>$data['dEmpCount'],
                    'dEmpPieces'=>$data['dEmpPieces'],
                    'sStoreOrigin'=>$sStoreOrigin,
                    'sStoreReturn'=>$sStoreReturn,
                    'bIsDriver'=>$data['bIsDriver'],
                    'bIsSuper'=>$data['bIsSuper'],
                    'iWaitTime'=>$data['iWaitTime'],
                    'iAttendanceFlag'=>$data['iAttendanceFlag'],
                    'sEmployeeComment'=>$data['sEmployeeComment'],
                    'TotalScans'=>$data['TotalScans'],
                    'ScansPerHour'=>$data['ScansPerHour'],
                    'PiecesPerHour'=>$data['PiecesPerHour'],
                    'dtFirstScan'=>$dtFirstScan,
                    'dtLastScan'=>$dtLastScan,
                    'WaitTimeExplanation'=>$data['WaitTimeExplanation'],
                    'WrapTimeExplanation'=>$data['WrapTimeExplanation'],
                    'iLunch1'=>$data['iLunch1'],
                    'iLunch2'=>$data['iLunch2'],
                    'iGapTime'=>$data['iGapTime'],
                    'GapTimeExplanation'=>$data['GapTimeExplanation'],
                    'AttendanceExplanation'=>$data['AttendanceExplanation'],
                    'PIMTime'=>$data['PIMTime'],
                    'iBreakTime'=>$data['iBreakTime']
                );
                //echo '<pre>';print_r($data);die;
                $timesheet_data = DB::table('timesheet_data')->insert($data_data);
            }
            }
            
            if(($counter+1)==count($data1))
            {
                $import_upto = $row['lstTimesheetHeader'][0]['ALA_DeliveryDate'];
                $pointdata = array('import_upto'=> $import_upto);
                DB::table('time_entries_import_log')->insert($pointdata);
            }
            
        }
        return redirect()->route('admin.timesheets.import_list')->with('successmsg', 'Time Entries upto '.$import_upto.' imported successfully.');
    }
    
    public function import_store_historical_data(){
        
        //$ch = curl_init("http://".env('SLY_SERVER_ADDRESS')."/api/Account?username=metsadmin&pwd=21232f297a57a5a743894a0e4a801fc3");
        $ch = curl_init("http://".env('SLY_SERVER_ADDRESS')."/api/Account/");
        $postData = array(
            'username'   => 'metsadmin',
            'pwd'=>'21232f297a57a5a743894a0e4a801fc3'
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept:application/xml',
            'Content-Type:application/json'
        ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $curlResponse = curl_exec($ch);
        $token = json_decode($curlResponse,true);
        //echo $token;
//        echo '<pre>';print_r($token);
//        die;
       
        //$ch = curl_init("http://".env('SLY_SERVER_ADDRESS')."/api/TimeSheetData?JobDate='".$import_upto."'");
        //$import_upto='2020-03-16';
        //$ch = curl_init("http://".env('SLY_SERVER_ADDRESS')."/api/TimeSheet?ALADeliveryDate='".$import_upto."'");
        
        //$stores = Store::Get();
        $stores = Store::Where('id','>',1173)->get();
        //echo '<pre>';print_r($stores);die;
        foreach($stores as $store)
        {
            //echo '<pre>'; print_r($store);die;
            $store_timesheets = Timesheet::Where('store_id',$store->id)->first();
           //
            if($store_timesheets)
            {
                continue;
            }else{
                //echo '<pre>'; print_r($store);die;
                $ch = curl_init("http://".env('SLY_SERVER_ADDRESS')."/api/TimeSheetDataByStore/");
                $postRequest = array(
                    'StoreNo'   => $store->number,
                    'Authorization'=>$token
                );
        
                //print_r($postRequest);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Accept:application/xml',
                    'Content-Type:application/json'
                ));
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postRequest));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $curlResponse = curl_exec($ch);
                $data1 = json_decode($curlResponse,true);
           //echo count($data1);
            //$data1 = json_decode('[{"lstTimesheetHeader":[{"idTimesheet_SQL":"200708161148580137719","dtJobDate":"2007-05-30 00:00:00","idStoreNo":"CRACKE2","total_emp_count":56282,"total_emp_pieces":0,"mComments":"","InvRecapStartTime":"","InvRecapEndTime":"","InvRecapWrapTime":"","InvRecapComments":"","CrewNoShowCount":0,"TTLMH":0.0,"TotalBreakTime":0,"TotalWaitTime":0,"AccountName":"","InvStoreNumber":"","Benchmark":0,"CrewManagerSSN":""}],"lstTimesheetVehicles":[{"idVehicle":24,"sSSN":"345623856","sSSN2":"","dtToStoreStart":"","dtToStoreEnd":"","dtFromStoreStart":"","dtFromStoreEnd":""}],"lstTimesheetData":[{"sEmployeeSSN":"345623856","dEmpCount":0,"dEmpPieces":0,"sStoreOrigin":"CRACKER","sStoreReturn":"OFFICE","sBranch":"01","bIsDriver":true,"bIsSuper":true,"bIsLast":"True","iWaitTime":30,"iAttendanceFlag":1,"sEmployeeComment":"Supervisor","TotalScans":434,"ScansPerHour":357,"PiecesPerHour":0,"WaitTimeExplanation":"","WrapTimeExplanation":"","iLunch1":0,"iLunch2":0,"iGapTime":0,"GapTimeExplanation":"","AttendanceExplanation":"","PIMTime":0,"dtFirstScan":"","dtLastScan":"","dtStartDateTime":"2007-05-30 09:56:00","dtStopDateTime":"2007-05-30 11:09:00"},{"sEmployeeSSN":"428392416","dEmpCount":0,"dEmpPieces":0,"sStoreOrigin":"CRACKER","sStoreReturn":"OFFICE","sBranch":"01","bIsDriver":false,"bIsSuper":false,"bIsLast":"True","iWaitTime":24,"iAttendanceFlag":1,"sEmployeeComment":"recount/tags","TotalScans":652,"ScansPerHour":584,"PiecesPerHour":0,"WaitTimeExplanation":"","WrapTimeExplanation":"","iLunch1":0,"iLunch2":0,"iGapTime":0,"GapTimeExplanation":"","AttendanceExplanation":"","PIMTime":0,"dtFirstScan":"","dtLastScan":"","dtStartDateTime":"2007-05-30 09:56:00","dtStopDateTime":"2007-05-30 11:03:00"}]}]');
            //echo '<pre>';print_r($data1);die;
            foreach($data1 as $counter=>$row)
            {
                //echo $row['lstTimesheetHeader'][0]['idStoreNo'];die;
                //echo '<pre>';print_r($row);die;
                //DB::enableQueryLog();
                //$store = Store::select('id')->where('number','=',$row['lstTimesheetHeader'][0]['idStoreNo'])->first();
                //dd(DB::getQueryLog());die;
                
                $store_id=$store->id;
                $is_flag=0;
                
                $InvRecapStartTime=NULL;
                if($row['lstTimesheetHeader'][0]['InvRecapStartTime'])
                    $InvRecapStartTime=date('Y-m-d H:i:s',strtotime($row['lstTimesheetHeader'][0]['InvRecapStartTime']));
                $InvRecapEndTime=NULL;
                if($row['lstTimesheetHeader'][0]['InvRecapEndTime'])
                    $InvRecapEndTime=date('Y-m-d H:i:s',strtotime($row['lstTimesheetHeader'][0]['InvRecapEndTime']));
                $InvRecapWrapTime=NULL;
                if($row['lstTimesheetHeader'][0]['InvRecapWrapTime'])
                    $InvRecapWrapTime=date('Y-m-d H:i:s',strtotime($row['lstTimesheetHeader'][0]['InvRecapWrapTime']));
                $InvRecapArvlTime=NULL;
                if($row['lstTimesheetHeader'][0]['InvRecapArvlTime'])
                    $InvRecapArvlTime=date('Y-m-d H:i:s',strtotime($row['lstTimesheetHeader'][0]['InvRecapArvlTime']));
                $timesheet_data = array('idTimesheet_SQL'=>$row['lstTimesheetHeader'][0]['idTimesheet_SQL'],
                    'dtJobDate'=>$row['lstTimesheetHeader'][0]['dtJobDate'],
                    "store_id"=>$store_id,
                    "is_flagged"=>$is_flag,
                    "dEmpCount"=>$row['lstTimesheetHeader'][0]['total_emp_count'],
                    "dEmpPieces"=>$row['lstTimesheetHeader'][0]['total_emp_pieces'],
                    "mComments"=>$row['lstTimesheetHeader'][0]['mComments'],
                    "InvRecapStartTime"=>$InvRecapStartTime,
                    "InvRecapEndTime"=>$InvRecapEndTime,
                    "InvRecapWrapTime"=>$InvRecapWrapTime,
                    "InvRecapComments"=>$row['lstTimesheetHeader'][0]['InvRecapComments'],
                    "CrewNoShowCount"=>$row['lstTimesheetHeader'][0]['CrewNoShowCount'],
                    "TTLMH"=>$row['lstTimesheetHeader'][0]['TTLMH'],
                    "TotalBreakTime"=>$row['lstTimesheetHeader'][0]['TotalBreakTime'],
                    "TotalWaitTime"=>$row['lstTimesheetHeader'][0]['TotalWaitTime'],
                    "AccountName"=>$row['lstTimesheetHeader'][0]['AccountName'],
                    "InvStoreNumber"=>($row['lstTimesheetHeader'][0]['InvStoreNumber'])?$row['lstTimesheetHeader'][0]['InvStoreNumber']:0,
                    "Benchmark"=>$row['lstTimesheetHeader'][0]['Benchmark'],
                    "CrewManagerSSN"=>$row['lstTimesheetHeader'][0]['CrewManagerSSN'],
                    "ALA_DeliveryDate"=>$row['lstTimesheetHeader'][0]['ALA_DeliveryDate'],
                    "InvRecapArvlTime"=>$InvRecapArvlTime,
                    "event_id"=>$row['lstTimesheetHeader'][0]['EventNo'],
                    "status"=>'Pending');
                $timesheet_header = DB::table('timesheet_header')->insert($timesheet_data);
                $timesheet_id = DB::getPdo()->lastInsertId();
                //print_r($timesheet_data);

                foreach($row['lstTimesheetVehicles'] as $vehicle){
                    $is_flagged=0;
                    $driver_to=0;
                    $driver_from=0;
                    if($vehicle['sSSN']){
                        $driver_to = Employee::select('id')->where('ss_no','=',$vehicle['sSSN'])->first();
                        if($driver_to)
                        {
                            $driver_to=$driver_to['id'];
                        }else
                        {
                            $where = array('id' => $timesheet_id);
                            $updateArr = ['is_flagged' => 1];
                            $event  = Timesheet::where($where)->update($updateArr);
                            $driver_to = $vehicle['sSSN'];
                            $is_flagged=1;
                        }
                    }
                    if($vehicle['sSSN2']){
                        $driver_from = Employee::select('id')->where('ss_no','=',$vehicle['sSSN2'])->first();
                        if($driver_from)
                        {
                            $driver_from=$driver_from['id'];
                        }else{
                            $where = array('id' => $timesheet_id);
                            $updateArr = ['is_flagged' => 1];
                            $event  = Timesheet::where($where)->update($updateArr);
                            $driver_from = $vehicle['sSSN2'];
                            $is_flagged=1;
                        }
                    }
                    $dtToStoreStart=NULL;
                    if($vehicle['dtToStoreStart'])
                        $dtToStoreStart = date('Y-m-d H:i:s',strtotime($vehicle['dtToStoreStart']));
                    $dtToStoreEnd=NULL;
                    if($vehicle['dtToStoreEnd'])
                        $dtToStoreEnd = date('Y-m-d H:i:s',strtotime($vehicle['dtToStoreEnd']));
                    $dtFromStoreStart=NULL;
                    if($vehicle['dtFromStoreStart'])
                        $dtFromStoreStart = date('Y-m-d H:i:s',strtotime($vehicle['dtFromStoreStart']));
                    $dtFromStoreEnd=NULL;
                    if($vehicle['dtFromStoreEnd'])
                        $dtFromStoreEnd = date('Y-m-d H:i:s',strtotime($vehicle['dtFromStoreEnd']));
                    $vehicle_data = array('timesheet_id'=>$timesheet_id,
                        'idVehicle'=>$vehicle['idVehicle'],
                        'is_flagged'=>$is_flagged,
                        'driver_to'=>$driver_to,
                        'driver_from'=>$driver_from,
                        "dtToStoreStart"=>$dtToStoreStart,
                        "dtToStoreEnd"=>$dtToStoreEnd,
                        "dtFromStoreStart"=>$dtFromStoreStart,
                        "dtFromStoreEnd"=>$dtFromStoreEnd,
                        );
                    $timesheet_vehicle = DB::table('timesheet_vehicle')->insert($vehicle_data);
                }

                foreach($row['lstTimesheetData'] as $data){
                    $is_flagged=0;
                    if($data['sStoreOrigin'] && $data['sStoreReturn'])
                    {
                    //print_r($data);die;
                    $employee_id=0;
                    if($data['sEmployeeSSN']){
                        $employee = Employee::select('id')->where('ss_no','=',$data['sEmployeeSSN'])->first();
                        if($employee)
                        {
                            $employee_id = $employee['id'];
                        }else{
                            $where = array('id' => $timesheet_id);
                            $updateArr = ['is_flagged' => 1];
                            $event  = Timesheet::where($where)->update($updateArr);
                            $employee_id = $data['sEmployeeSSN'];
                            $is_flagged=1;
                            //echo '<br>Employee SSN '.$data['sEmployeeSSN'].' not found';
                        }
                    }
                    if(strtolower($data['sStoreOrigin'])=="office"){
                        $sStoreOrigin=$data['sStoreOrigin'];
                    }else{
                        $sStoreOrigin = Store::select('id')->where('number','=',$data['sStoreOrigin'])->first();
                        $sStoreOrigin=$sStoreOrigin['id'];
                    }
                    //echo strtolower($data['sStoreReturn']);
                    if(strtolower($data['sStoreReturn'])=="office"){
                        $sStoreReturn = $data['sStoreReturn'];
                    }else
                    {
                        $sStoreReturn = Store::select('id')->where('number','=',$data['sStoreReturn'])->first();
                        $sStoreReturn=$sStoreReturn['id'];
                    }
                    $dtFirstScan=NULL;
                    if($data['dtFirstScan'])
                       $dtFirstScan = date('Y-m-d H:i:s',strtotime($data['dtFirstScan']));
                    $dtLastScan=NULL;
                    if($data['dtLastScan'])
                       $dtLastScan = date('Y-m-d H:i:s',strtotime($data['dtLastScan']));
                    $data_data = array('timesheet_id'=>$timesheet_id,
                        'employee_id'=>$employee_id,
                        'is_flagged'=>$is_flagged,
                        "dtStartDateTime"=>($data['dtStartDateTime'])?date('Y-m-d H:i:s',strtotime($data['dtStartDateTime'])):'',
                        "dtStopDateTime"=>($data['dtStopDateTime'])?date('Y-m-d H:i:s',strtotime($data['dtStopDateTime'])):'',
                        'dEmpCount'=>$data['dEmpCount'],
                        'dEmpPieces'=>$data['dEmpPieces'],
                        'sStoreOrigin'=>$sStoreOrigin,
                        'sStoreReturn'=>$sStoreReturn,
                        'bIsDriver'=>$data['bIsDriver'],
                        'bIsSuper'=>$data['bIsSuper'],
                        'iWaitTime'=>$data['iWaitTime'],
                        'iAttendanceFlag'=>$data['iAttendanceFlag'],
                        'sEmployeeComment'=>$data['sEmployeeComment'],
                        'TotalScans'=>$data['TotalScans'],
                        'ScansPerHour'=>$data['ScansPerHour'],
                        'PiecesPerHour'=>$data['PiecesPerHour'],
                        'dtFirstScan'=>$dtFirstScan,
                        'dtLastScan'=>$dtLastScan,
                        'WaitTimeExplanation'=>$data['WaitTimeExplanation'],
                        'WrapTimeExplanation'=>$data['WrapTimeExplanation'],
                        'iLunch1'=>$data['iLunch1'],
                        'iLunch2'=>$data['iLunch2'],
                        'iGapTime'=>$data['iGapTime'],
                        'GapTimeExplanation'=>$data['GapTimeExplanation'],
                        'AttendanceExplanation'=>$data['AttendanceExplanation'],
                        'PIMTime'=>$data['PIMTime'],
                        'iBreakTime'=>$data['iBreakTime']
                    );
                    //echo '<pre>';print_r($data);die;
                    $timesheet_data = DB::table('timesheet_data')->insert($data_data);
                }
                }

                

            }
            }
        }
        die;
        return redirect()->route('admin.timesheets.import_list')->with('successmsg', 'Time Entries upto '.$import_upto.' imported successfully.');
    }
    
    public function timesheet_data_by_id(Request $request){

        $ch = curl_init("http://".env('SLY_SERVER_ADDRESS')."/api/Account/");
        $postData = array(
            'username'   => 'metsadmin',
            'pwd'=>'21232f297a57a5a743894a0e4a801fc3'
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept:application/xml',
            'Content-Type:application/json'
        ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $curlResponse = curl_exec($ch);
        $token = json_decode($curlResponse,true);
        
         $timesheets = DB::table('timesheet_header')
                 //->select('timesheet_header.idTimesheet_SQL')
                //->leftJoin('timesheet_header','timesheet_header.id','=','timesheet_data.timesheet_id')
                ->where('id','>=',300)
                ->where('id','<',400)
                ->get();
        //echo '<pre>';print_r($timesheets);die;
        foreach($timesheets as $timesheet)
        {
            $timesheet_id = $timesheet->id;
            //echo '<pre>';print_r($timesheet);die;
            $ch = curl_init("http://".env('SLY_SERVER_ADDRESS')."/api/TimeSheetDataById/");
            $postRequest = array(
                'idTimesheet_SQL'   => $timesheet->idTimesheet_SQL,
                'Authorization'=>$token
            );

            //print_r($postRequest);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Accept:application/xml',
                'Content-Type:application/json'
            ));
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postRequest));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $curlResponse = curl_exec($ch);
            $data1 = json_decode($curlResponse,true);
                
            //echo count($data1);
            //$data1 = json_decode('[{"lstTimesheetHeader":[{"idTimesheet_SQL":"200708161148580137719","dtJobDate":"2007-05-30 00:00:00","idStoreNo":"CRACKE2","total_emp_count":56282,"total_emp_pieces":0,"mComments":"","InvRecapStartTime":"","InvRecapEndTime":"","InvRecapWrapTime":"","InvRecapComments":"","CrewNoShowCount":0,"TTLMH":0.0,"TotalBreakTime":0,"TotalWaitTime":0,"AccountName":"","InvStoreNumber":"","Benchmark":0,"CrewManagerSSN":""}],"lstTimesheetVehicles":[{"idVehicle":24,"sSSN":"345623856","sSSN2":"","dtToStoreStart":"","dtToStoreEnd":"","dtFromStoreStart":"","dtFromStoreEnd":""}],"lstTimesheetData":[{"sEmployeeSSN":"345623856","dEmpCount":0,"dEmpPieces":0,"sStoreOrigin":"CRACKER","sStoreReturn":"OFFICE","sBranch":"01","bIsDriver":true,"bIsSuper":true,"bIsLast":"True","iWaitTime":30,"iAttendanceFlag":1,"sEmployeeComment":"Supervisor","TotalScans":434,"ScansPerHour":357,"PiecesPerHour":0,"WaitTimeExplanation":"","WrapTimeExplanation":"","iLunch1":0,"iLunch2":0,"iGapTime":0,"GapTimeExplanation":"","AttendanceExplanation":"","PIMTime":0,"dtFirstScan":"","dtLastScan":"","dtStartDateTime":"2007-05-30 09:56:00","dtStopDateTime":"2007-05-30 11:09:00"},{"sEmployeeSSN":"428392416","dEmpCount":0,"dEmpPieces":0,"sStoreOrigin":"CRACKER","sStoreReturn":"OFFICE","sBranch":"01","bIsDriver":false,"bIsSuper":false,"bIsLast":"True","iWaitTime":24,"iAttendanceFlag":1,"sEmployeeComment":"recount/tags","TotalScans":652,"ScansPerHour":584,"PiecesPerHour":0,"WaitTimeExplanation":"","WrapTimeExplanation":"","iLunch1":0,"iLunch2":0,"iGapTime":0,"GapTimeExplanation":"","AttendanceExplanation":"","PIMTime":0,"dtFirstScan":"","dtLastScan":"","dtStartDateTime":"2007-05-30 09:56:00","dtStopDateTime":"2007-05-30 11:03:00"}]}]');
            //echo '<pre>';print_r($data1);die;
            foreach($data1 as $counter=>$row)
            {
                $employee = Employee::select('id')->where('ss_no','=',$row['sEmployeeSSN'])->first();
                if($employee && $row['iBreakTime'])
                {
                    $employee_id=$employee->id;
                    echo $employee_id.'--'.$timesheet_id.'--'.$row['iBreakTime'].'--done<br>'.
                    $where = array('timesheet_id' => $timesheet_id,'employee_id'=>$employee_id);
                    
                    $updateArr = ['iBreakTime' => $row['iBreakTime']];
                    $event  = TimesheetData::where($where)->update($updateArr);
                }
            }
        }
        //return redirect()->route('admin.timesheets.import_list')->with('successmsg', 'Time Entries upto '.$import_upto.' imported successfully.');
    }
    
    public function reset_kronos_queue()
    {
        $pending_time_entries = DB::table('time_entries_queue')
                ->where('time_entries_queue.status','=',1)
                ->get();
        foreach($pending_time_entries as $pending_time_entry)
        {
            DB::table('time_entries')
            ->where('location', $pending_time_entry->location)
            ->update(array('location' => ''));
            DB::table('time_entries_queue')->where('id', '=', $pending_time_entry->id)->delete();
        }
        echo 'Kronos queue has been reset successfully. You can export again now.';
    }
    
    
    public function kronos_queue_status(Request $request)
    {
        $table = $request['table'];
        $pending_time_entries = DB::table($table)
                ->where('status','=',1)
                ->get();
        if($pending_time_entries->isEmpty())
            return 0;
        else
            return 1;
    }
    
    
    function testcron($array=[])
    {
        //\Log::info(now());
    }
    
    /**
     * Check whether Timesheet for event already submitted.
     *
     * @return \Illuminate\Http\Response
     */
    public function timesheet_submitted_check(Request $request)
    {
        $pending_time_entries = DB::table('timesheet_header')
                ->where('event_id','=',$request['eventid'])
                ->where('status','=','Approved')
                ->get();
        if($pending_time_entries->isEmpty())
            return 0;
        else
            return 1;
    }
    
    
    public function export_to_kronos_manually(Request $request)
    {
        //echo auth()->user()->email;die;
        $pending_time_entries = DB::table('time_entries_queue')
                ->where('time_entries_queue.status','=',1)
                ->first();
        //print_r($pending_time_entries);die;
        
        $export_file_name = $pending_time_entries->excelsheet_name;
           
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
        
//        Get Kronos Token Start End
        //$token = 'Bearer eyJhbGciOiJIUzUxMiJ9.eyJleHAiOjE1ODcwNDA0NDIsImlhdCI6MTU4NzAzNjg0Miwic2lkIjoiMTg1NzQ5NTkwMTUiLCJhaWQiOiI4NjU4MTI1OTUwIiwiY2lkIjoiNjcxMjY2NTIiLCJ0eXBlIjoiciJ9.DbpSOyygJ8ScFr9L0U58w4SH1IP2rufIGSPyx409JPRmgLzYPj3SkDv3E5JzIZdmlKlT4r0DcxRsOspoxkLMTg';
        //$cURLConnection1 = curl_init('https://secure3.saashr.com/ta/rest/v1/imports');
//        $cURLConnection1 = curl_init('https://secure3.saashr.com/ta/rest/v1/import/116');
//        Send Timesheet to Kronos       
        $filename = base_path().'/public/uploads/'.$export_file_name;
        $cfile = $this->getCurlValue($filename,'text/csv',$export_file_name);
        $data = array('file' => $cfile);
        $ch = curl_init('https://secure3.saashr.com/ta/rest/v1/import/116');
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
//        Send Timesheet to Kronos End            
        //echo "<pre>";
        //print_r($headers);
        echo $location = $headers['Location'];
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
        
        echo '<pre>';print_r($array_data);die;
        //$this->testcron(array('msg'=>$array_data['status']));
       
        
        exit;  
    }
    
    public function employee_other_events(Request $request,$emp_id,$jobdate,$timesheet_id)
    {
        //echo $emp_id;echo $jobdate;die;
        $timesheets = DB::table('timesheet_header')
                //->select('id')
                ->where('dtJobDate','=',$jobdate)
                //->where('id','!=',$timesheet_id)
                ->pluck('id')->toarray();
        //$timesheets = implode($timesheets);
       // echo '<pre>';        print_r($timesheets);die;
//            $event_areas=array();
//            foreach($areas as $area)
//            {
//                $event_areas[]=$area->area_id;
//            }
        $events = DB::table('timesheet_data')
                ->select('stores.number as storenumber','stores.name as storename','timesheet_data.dtStartDateTime','timesheet_data.dtStopDateTime')
                ->leftJoin('timesheet_header','timesheet_header.id','=','timesheet_data.timesheet_id')
                ->leftJoin('stores','stores.id','=','timesheet_header.store_id')
                ->whereIn('timesheet_id',$timesheets)
                ->where('employee_id','=',$emp_id)
                ->orderBy('timesheet_data.dtStartDateTime', 'ASC')
                ->get();
        return view('admin.timesheets.employee_other_events', compact('events'));
        //echo '<pre>';        print_r($events);die;
    }
    
    public function import_inventory_evaluation(){
        $ch = curl_init("http://".env('SLY_SERVER_ADDRESS')."/api/Account/");
        $postData = array(
            'username'   => 'metsadmin',
            'pwd'=>'21232f297a57a5a743894a0e4a801fc3'
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept:application/xml',
            'Content-Type:application/json'
        ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $curlResponse = curl_exec($ch);
        $token = json_decode($curlResponse,true);
        //echo $token;
//        echo '<pre>';print_r($token);
//        die;
        $timeentries = DB::table('inventory_evaluation_import_log')->orderBy('id', 'DESC')->first();
        $import_upto = $timeentries->import_upto;
        
        $ch = curl_init("http://".env('SLY_SERVER_ADDRESS')."/api/InventoryEvaluationData/");
        $postRequest = array(
            'METSJobDate'   => "'".date('Y-m-d',strtotime($import_upto))."'",
            'Authorization'=>$token
        );
        //print_r($postRequest);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept:application/xml',
            'Content-Type:application/json'
        ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postRequest));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $curlResponse = curl_exec($ch);
        $data1 = json_decode($curlResponse,true);
        //echo '<pre>';print_r($data1);die;
        if($data1 && count($data1)){
            foreach($data1 as $counter=>$row)
            {
                DB::table('inventory_evaluation')->insert(array($row));
                $METS_JobDate=$row['METS_JobDate'];
                //echo '<pre>';print_r($row);die;
                //DB::enableQueryLog();
                //print_r($timesheet_data);
            }
            $pointdata = array('import_upto'=> $METS_JobDate);
            DB::table('inventory_evaluation_import_log')->insert($pointdata);
        }
        
        //echo 'Inventory evaluation upto '.$METS_JobDate.' imported successfully.';
    }
    
    public function calculate_vehicle_travel($from,$to)
    {
        //echo $to;die;
        $employees = DB::table('timesheet_approved')
                ->select('timesheet_approved.*','employees.jsa_id','timesheet_header.store_id')
                ->leftJoin('employees','employees.id','=','timesheet_approved.employee_id')
                ->leftJoin('timesheet_header','timesheet_header.id','=','timesheet_approved.timesheet_id')
                ->where('timesheet_approved.created_at','>=','2021-03-01 00:00')
                ->where(function ($query) {
                    $query->where('driver_to', '=','1')
                          ->orWhere('driver_from', '=','1');
                })
                ->orderBy('timesheet_approved.id', 'ASC')
                ->offset($from)
                ->limit($to)
                ->get();
        //$employees=$employees->toArray();
        //echo '<pre>';print_r($employees);die;
        foreach($employees as $emp_id)
        {
            $driver_to=0;
            $driver_from=0;    
            //echo '<pre>';print_r($emp_id);die;
            if($emp_id->driver_to)
                $driver_to=1;
            if($emp_id->driver_from)
                $driver_from=1;
            $vehicle_travel=0;
            if(strtolower($emp_id->origin)=="office" && strtolower($emp_id->destination)=="office")
            {
                if($driver_to && $driver_from)
                {
                    $origin=$emp_id->jsa_id;
                    $origin_type="office";
                    $destination=$emp_id->store_id;
                    $destination_type="store";
                    $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    
                    $origin=$emp_id->store_id;
                    $origin_type="store";
                    $destination=$emp_id->jsa_id;
                    $destination_type="office";
                    $dist = calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance1 = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    
                    $vehicle_travel=$distance+$distance1;
                }elseif($driver_to)
                {
                    $origin=$emp_id->jsa_id;
                    $origin_type="office";
                    $destination=$emp_id->store_id;
                    $destination_type="store";
                    $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $vehicle_travel=$distance;
                }elseif($driver_from)
                {
                    $origin=$emp_id->store_id;
                    $origin_type="store";
                    $destination=$emp_id->jsa_id;
                    $destination_type="office";
                    $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance1 = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $vehicle_travel=$distance1;
                }
        }elseif(strtolower($emp_id->origin)=="office" && strtolower($emp_id->destination)!="office")
            {
                if($driver_to && $driver_from)
                {
                    $origin=$emp_id->jsa_id;
                    $origin_type="office";
                    $destination=$emp_id->store_id;
                    $destination_type="store";
                    $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                
//                    $origin=$emp_id->store_id;
//                    $origin_type="store";
//                    $destination=$emp_id->destination;
//                    $destination_type="store";
//                    $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
//                    $distance1 = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $vehicle_travel=ceil($distance);
                }elseif($driver_to)
                {
                    $origin=$emp_id->jsa_id;
                    $origin_type="office";
                    $destination=$emp_id->store_id;
                    $destination_type="store";
                    $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $vehicle_travel=$distance;
                }elseif($driver_from)
                {
//                    $origin=$emp_id->store_id;
//                    $origin_type="store";
//                    $destination=$emp_id->destination;
//                    $destination_type="store";
//                    $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
//                    $distance1 = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    //$vehicle_travel=$distance1;
                }
            }elseif(strtolower($emp_id->origin)!="office" && strtolower($emp_id->destination)!="office")
            {
                if($driver_to && $driver_from)
                {
                    $origin=$emp_id->origin;
                    $origin_type="store";
                    $destination=$emp_id->store_id;
                    $destination_type="store";
                    $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                
//                    $origin=$emp_id->store_id;
//                    $origin_type="store";
//                    $destination=$emp_id->destination;
//                    $destination_type="store";
//                    $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
//                    $distance1 = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $vehicle_travel=ceil($distance);
                }elseif($driver_to)
                {
                    $origin=$emp_id->origin;
                    $origin_type="store";
                    $destination=$emp_id->store_id;
                    $destination_type="store";
                    $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $vehicle_travel=$distance;
                }elseif($driver_from)
                {
//                    $origin=$emp_id->store_id;
//                    $origin_type="store";
//                    $destination=$emp_id->destination;
//                    $destination_type="store";
//                    $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
//                    $distance1 = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    //$vehicle_travel=ceil($distance1);
                }
            }elseif(strtolower($emp_id->origin)!="office" && strtolower($emp_id->destination)=="office")
            {
                if($driver_to && $driver_from)
                {
                    $origin=$emp_id->origin;
                    $origin_type="store";
                    $destination=$emp_id->store_id;
                    $destination_type="store";
                    $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');

                    $origin=$emp_id->store_id;
                    $origin_type="store";
                    $destination=$emp_id->jsa_id;
                    $destination_type="office";
                    $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance1 = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $vehicle_travel=$distance+$distance1;
                }elseif($driver_to)
                {
                    $origin=$emp_id->origin;
                    $origin_type="store";
                    $destination=$emp_id->store_id;
                    $destination_type="store";
                    $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $vehicle_travel=$distance;
                }elseif($driver_from)
                {
                    $origin=$emp_id->store_id;
                    $origin_type="store";
                    $destination=$emp_id->jsa_id;
                    $destination_type="office";
                    $dist = $this->calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance1 = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $vehicle_travel=$distance1;
                }
            }
            
            if($vehicle_travel!=$emp_id->vehicle_travel)
            {
                echo '<pre>';print_r($emp_id);
                DB::table('timesheet_approved')
                ->where('id', $emp_id->id)
                ->update(array('vehicle_travel' => $vehicle_travel));
                
            }
        }
    }
    
    public function import_gap_report(){
        $ch = curl_init("http://".env('SLY_SERVER_ADDRESS')."/api/Account/");
        $postData = array(
            'username'   => 'metsadmin',
            'pwd'=>'21232f297a57a5a743894a0e4a801fc3'
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept:application/xml',
            'Content-Type:application/json'
        ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $curlResponse = curl_exec($ch);
        $token = json_decode($curlResponse,true);
        //echo $token;
//        echo '<pre>';print_r($token);
//        die;
        $timeentries = DB::table('gap_reports_import_log')->orderBy('id', 'DESC')->first();
        $import_upto = $timeentries->import_upto;
        
        $ch = curl_init("http://".env('SLY_SERVER_ADDRESS')."/api/GapReport/");
        $postRequest = array(
            'InterLinkDate'   => "'".date('Y-m-d H:i:s',strtotime($import_upto))."'",
            'Authorization'=>$token
        );
        //print_r($postRequest);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept:application/xml',
            'Content-Type:application/json'
        ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postRequest));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $curlResponse = curl_exec($ch);
        $data1 = json_decode($curlResponse,true);
        //echo '<pre>';print_r($data1);die;
        if($data1 && count($data1)){
            foreach($data1 as $counter=>$row)
            {
                $ts1 = strtotime($row['GapStart']);
                $ts2 = strtotime($row['GapEnd']);     
                $seconds_diff = $ts2 - $ts1;                            
                $row['GapMinutes'] = ($seconds_diff/60);
                
                $METS_JobDate=$row['InterLinkDate'];
                unset($row['IgnoreGap']);unset($row['InterLinkDate']);unset($row['idTimesheet']);
                DB::table('gap_reports')->insert(array($row));
                //echo '<pre>';print_r($row);die;
                //DB::enableQueryLog();
                //print_r($timesheet_data);
            }
            $pointdata = array('import_upto'=> $METS_JobDate);
            DB::table('gap_reports_import_log')->insert($pointdata);
        }
        
        //echo 'Gap reports upto '.$METS_JobDate.' imported successfully.';
    }
    
    public function gap_time($emp_id,$timesheet_id)
    {
        //echo $emp_id;echo $timesheet_id;die;
        $timesheets = DB::table('timesheet_header')
                ->select('timesheet_header.idTimesheet_SQL','events.*','stores.name as storename')
                ->leftJoin('events','events.id','=','timesheet_header.event_id')
                ->leftJoin('stores','stores.id','=','timesheet_header.store_id')
                ->where('timesheet_header.id','=',$timesheet_id)
                ->first();
        $idTimesheet_SQL = $timesheets->idTimesheet_SQL;
        
        $employee = DB::table('employees')
                ->where('id','=',$emp_id)
                ->first();
        $ss_no = $employee->ss_no;
        $gaptimeexplanation = DB::table('timesheet_data')
                ->select('gaptimeexplanation')
                ->where('timesheet_id','=',$timesheet_id)
                ->where('employee_id','=',$emp_id)
                ->first();
        $gap_reports = DB::table('gap_reports')
                ->where('idTimesheet_SQL','=',$idTimesheet_SQL)
                ->where('sEmployeeSSN','=',$ss_no)
                ->get();
        //echo '<pre>';print_r($employee);die;
        return view('admin.timesheets.gap_time', compact('timesheets','employee','gap_reports','gaptimeexplanation'));
        //echo '<pre>';        print_r($events);die;
    }
    public function scUpdate($id) {
      //  dd("ABCD");
        $event = EventScheduleEmployees::findOrFail($id);
       // $link = Link::find( $id );
      //  $link->is_active = '0';
      //  $link->is_archive = '0';
        $event->delete();
     //   $link->withTrashed()->first();
        return response()->json("Deleted");

       
    }
    
    function callunchgap(Request $request)
    {
        $employee = DB::table('employees')
                ->where('id','=',$request['employee_id'])
                ->first();
        $ssn_no = $employee->ss_no;
        
        $idTimesheet_SQL=$request['idTimesheet_SQL'];
        $lunch1=$request['lunch1'];
        $lunch2=$request['lunch2'];
        
        $counter=1;
        $lunch1flag=1;
        $lunch2flag=1;
        
        if($lunch2 && $lunch1)
        {
            
            $gap_reports1 = DB::table('gap_reports')
                ->where('idTimesheet_SQL','=',$idTimesheet_SQL)
                ->where('sEmployeeSSN','=',$ssn_no)
                ->where('GapMinutes','>=',$lunch1)    
                ->orderby('GapMinutes','ASC')
                ->first();
            //print_r($gap_reports1);
            $gap_reports2 = DB::table('gap_reports')
                ->where('idTimesheet_SQL','=',$idTimesheet_SQL)
                ->where('sEmployeeSSN','=',$ssn_no)
                ->where('GapMinutes','>=',$lunch2);
            if(count($gap_reports1))
                $gap_reports2 = $gap_reports2->where('id','!=',$gap_reports1->id);
            
                $gap_reports2 = $gap_reports2->orderby('GapMinutes','ASC')
                ->first();
                
            if(count($gap_reports1))
                $lunch1flag=0;
            if(count($gap_reports2))
                $lunch2flag=0;
        }elseif($lunch1)
        {
            $gap_reports2 = DB::table('gap_reports')
                ->where('idTimesheet_SQL','=',$idTimesheet_SQL)
                ->where('sEmployeeSSN','=',$ssn_no)
                ->where('GapMinutes','>=',$lunch1)    
                ->orderby('GapMinutes','asc')
                ->first();
            if(count($gap_reports2))
                $lunch1flag=0;
        }elseif($lunch2)
        {
            $gap_reports2 = DB::table('gap_reports')
                ->where('idTimesheet_SQL','=',$idTimesheet_SQL)
                ->where('sEmployeeSSN','=',$ssn_no)
                ->where('GapMinutes','>=',$lunch2)    
                ->orderby('GapMinutes','asc')
                ->first();
            if(count($gap_reports2))
                $lunch2flag=0;
        }
        //echo '<pre>';print_r($gap_reports);die;
        
        if($lunch1==0)$lunch1flag=0;if($lunch2==0)$lunch2flag=0;
        return ['lunch1'=>$lunch1flag,'lunch2'=>$lunch2flag];
    }


}
