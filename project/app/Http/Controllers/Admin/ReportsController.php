<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Report;
use App\Models\ReportAvailabilityDays;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreReportsRequest;
use App\Http\Requests\Admin\UpdateReportsRequest;
use App\Http\Controllers\Traits\FileUploadTrait;
use Illuminate\Support\Facades\DB;
use App\Models\Division;
use App\Models\Area;
use App\Models\Jsa;
use App\Models\Client;
use App\Models\StoreScheduleAvailabilityDays;
use App\Models\StoreScheduleMonths;
use Illuminate\Support\Facades\Response;
use GuzzleHttp;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Timesheet;
use App\Models\TimesheetVehicle;
use App\Models\TimesheetData;
use App\Exports\ReportSchedulesExport;
use Illuminate\Support\Facades\Mail;


class ReportsController extends Controller
{
    use FileUploadTrait;

    /**
     * Display a listing of Report.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (! Gate::allows('report_view')) {
            return abort(401);
        }
        //DB::enableQueryLog();
        if (Gate::allows('isAdmin')){
            $reports = Report::where(function($q) {
                    $q->where('status', '=', 'active')
                    ->orWhere('status','=', 'Inactive');
            })->orderBy('rpt_name','asc');
            if (request('show_deleted') == 1) {
                if (! Gate::allows('report_delete')) {
                    return abort(401);
                }
                $reports = $reports->onlyTrashed()->get();
            } else {
                $reports = $reports->get();
            }
        }else{
            $reports = Report::where(function($q) {
                    $q->where('status', '=', 'active')
                    ->orWhere('status','=', 'Inactive');
            })
            ->orderBy('rpt_name','asc');
            
            $report_permission = DB::table('report_access')->where('user_id','=',Auth::id())->get();
            $report_arr = array();
            foreach($report_permission as $report)
                $report_arr[]=$report->report_id;
            $reports = $reports->whereIn('id', $report_arr);
            
            if (request('show_deleted') == 1) {
                if (! Gate::allows('report_delete')) {
                    return abort(401);
                }
                $reports = $reports->onlyTrashed()->get();
            } else {
                $reports = $reports->get();
            }
        }
        
        //dd(DB::getQueryLog());die;
        //print_r($reports);die;
        return view('admin.reports.index', compact('reports'));
    }

    /**
     * Show the form for creating new Report.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('report_create')) {
            return abort(401);
        }
        return view('admin.reports.create');
    }

    /**
     * Store a newly created Report in storage.
     *
     * @param  \App\Http\Requests\StoreReportsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreReportsRequest $request)
    {
        if (! Gate::allows('report_create')) {
            return abort(401);
        }
        
        $report = Report::create($request->all());
        //echo '<pre>';print_r($request->all());die;
        
        //  echo "<pre>";
//        print_r($request->all());
//        die;
        return redirect()->route('admin.reports.index')->with('successmsg', 'Report added successfully.');
    }


    /**
     * Show the form for editing Report.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('report_edit')) {
            return abort(401);
        }
        
        $report = Report::findOrFail($id);
        //echo $report->state;die;
//        echo "<pre>";
//        print_r($report->schedule_availability_days);
//        die;  
//        if($request->ajax()){
//            return "AJAX";
//        }else{
        //echo '<pre>';print_r($report->availability_days);die;
            return view('admin.reports.edit', compact('report'));
        //}
    }

    /**
     * Update Report in storage.
     *
     * @param  \App\Http\Requests\UpdateReportsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateReportsRequest $request, $id)
    {
        if (! Gate::allows('report_edit')) {
            return abort(401);
        }
//        echo "<pre>";
//        print_r($request->all());
//        die;
        $report = Report::findOrFail($id);
        $report->update($request->all());
        return redirect()->route('admin.reports.index')->with('successmsg', 'Report updated successfully.');
    }


    /**
     * Display Report.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        if (! Gate::allows('report_view')) {
            return abort(401);
        }
        $report = Report::findOrFail($id);
        if($request->ajax()){
            $token = generateAccessToken();
            $where = array('id' => Auth::id());
            $updateArr = ['crystal_token' => $token];
            $emp_status  = \App\User::where($where)->update($updateArr);
            return Response::json(array('token'=>$token,'user_id'=>Auth::id(),'filename'=>$report->rpt_file_name),200);
        }else{
//            /echo '<pre>';print_r($report);die;
            return view('admin.reports.show',compact('report'));
        }
    }


    /**
     * Remove Report from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('report_delete')) {
            return abort(401);
        }
        $report = Report::findOrFail($id);
        $report->delete();

        return redirect()->route('admin.reports.index')->with('successmsg', 'Report set as inactive successfully.');
    }

    /**
     * Delete all selected Report at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('report_delete')) {
            return abort(401);
        }
        if ($request->input('ids')) {
            $entries = Report::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }


    /**
     * Restore Report from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('report_delete')) {
            return abort(401);
        }
        $report = Report::onlyTrashed()->findOrFail($id);
        $report->restore();

        return redirect()->route('admin.reports.index')->with('successmsg', 'Report set as active successfully.');
    }

    /**
     * Permanently delete Report from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('report_delete')) {
            return abort(401);
        }
        $report = Report::onlyTrashed()->findOrFail($id);
        $report->forceDelete();

        return redirect()->route('admin.reports.index')->with('successmsg', 'Report deleted successfully.');
    }
    
    public function view_crystal_report($id)
    {
        //die($id);
        $token = generateAccessToken();
        $where = array('id' => Auth::id());
        $updateArr = ['crystal_token' => $token];
        $emp_status  = \App\User::where($where)->update($updateArr);
        $report = Report::findOrFail($id);
        //echo '<pre>';print_r($report);die;
        return view('admin.reports.view_crystal_report', compact('report','token'));  
    }
    
    
    public function assignusers($report_id)
    {
        $users = DB::table('users')->pluck('name','id');
        $user = DB::table('report_access')
                ->select('user_id')
                ->where('report_id','=',$report_id)
                ->get();
        $assigned_users=array();
        foreach($user as $user1)
        {
            $assigned_users[]=$user1->user_id;
        }
        return view('admin.reports.assignusers', compact('report_id','users','assigned_users'));  
    }
    
    public function saveassignedusers(Request $request)
    {
        //echo '<pre>';print_r($request->all());die;
        if(count($request->report_users))
        {
            DB::table('report_access')->where('report_id', '=',$request['report_id'])->delete();
            foreach($request->report_users as $user)
            {
                $data=array('report_id'=>$request['report_id'],'user_id'=>$user);
                DB::table('report_access')->insert($data);
            }
        }
        return redirect()->route('admin.reports.index')->with('successmsg', 'Report permission assigned successfully.');
    }
    
    public function unscheduled_store_list_view(Request $request) {
        if (!Gate::allows('isAdmin') && !Gate::allows('isCorporate')){
            return abort(401);
        }
        $request->session()->put('month',date('m'));
        $request->session()->put('year',date('Y'));
        return view('admin.reports.unscheduled_stores');
    }
    
    public function unscheduled_store_list(Request $request) {
        
        //print_r($request->all());die;
        $draw = $request['draw'];
        $row = $request['start'];
        $rowperpage = $request['length']; // Rows display per page
        //echo $rowperpage;
        if($rowperpage==-1)
            $limit='';
        else
        {
            if(intval($rowperpage) && $row>=0)
                $limit = " limit ".$row.",".$rowperpage;
            else
                $limit = ' limit 0,25';
        }
        $columnIndex = $request['order'][0]['column']; // Column index
        if($columnIndex==0)
        {
            $columnName = 'stores.id asc';
        }elseif($columnIndex==1)
        {
            $columnName = 'stores.name asc';
        }elseif($columnIndex==2)
        {
            $columnName = 'cities.name asc';
        }elseif($columnIndex==3)
        {
            $columnName = 'states.name asc';
        }elseif($columnIndex==4)
        {
            $columnName = 'stores.inventory_level asc';
        }elseif($columnIndex==5)
        {
            $columnName = 'areas.title asc';
        }else
        {
            $columnName = $request['columns'][$columnIndex]['data']; // Column name
        }
        //$columnSortOrder = $request['order'][0]['dir']; // asc or desc
        $searchValue = $request['search']['value']; // Search value

        ## Custom Field value
        //print_r($request->all());die;
        ## Search 
        $searchQuery = " ";


        if($request['month'] != ''){
            $request->session()->put('month',$request['month']);
        }else{
            $request->session()->put('month',date('m'));
        }

        if($request['year'] != ''){
            $request->session()->put('year',$request['year']);
            //$searchQuery .= " and (stores.city_id=".$request['city_id'].") ";
        }else{
            $request->session()->put('year',date('Y'));
        }
        
        $monthname= date("F", mktime(0, 0, 0, $request->session()->get('month'), 10));
        $stores = DB::select( DB::raw("select store_id from store_schedule_months WHERE month='".$monthname."'"));
        $store_ids=array();
        $unscheduled_stores=array();
        foreach($stores as $store)
        {
            $startyear=$request->session()->get('year');
            $endyear=$request->session()->get('year');
            if(intval($request->session()->get('month'))==1)
            {
                $prev_month=12;
                $startyear=$startyear-1;
            }else
                $prev_month=intval($request->session()->get('month'))-1;
            if(intval($request->session()->get('month'))==12)
            {
                $next_month=1;
                $endyear=$endyear+1;
            }else
                $next_month=intval($request->session()->get('month'))+1;

            $start = $startyear.'-'.$prev_month.'-01';
            $end = $endyear.'-'.$next_month.'-30';
            //DB::enableQueryLog();
            $events = DB::table('events')
                    ->whereBetween('events.date',[$start,$end])
                    ->where('store_id','=',$store->store_id)
                    ->select('id')
                    ->get();
            //dd(DB::getQueryLog());
            if($events->isEmpty())
                $unscheduled_stores[]=$store->store_id;
           //$store_ids[]=$store->id; 
        }
        //die;
        //$store_id = implode(',',$store_id);
        //$events = $events->whereIn('events.store_id',$store_id);
        //echo '<pre>';print_r($unscheduled_stores);
        $searchQuery .= " and (stores.id in(".implode(',',$unscheduled_stores).")) ";
            
        //echo '<pre>';print_r($store_id);die;
        //echo $searchQuery;
        ## Total number of records without filtering
        $records = DB::select( DB::raw("SELECT count(*) as allcount FROM stores") );
        $totalRecords = $records[0]->allcount;
        ## Total number of records with filtering
        $records = DB::select( DB::raw("SELECT count(*) as allcount FROM stores "
                . "where 1=1 and stores.deleted_at IS NULL ".$searchQuery) );
        $totalRecordwithFilter = $records[0]->allcount;
        ## Fetch records
        $stores = DB::select( DB::raw("select stores.*,cities.name as cityname,states.name as statename,"
                . "areas.title as apr from stores "
                . "left join areas on areas.id=stores.apr "
                . "left join cities on cities.id=stores.city_id "
                . "left join states on states.id=stores.state_id "
                . "WHERE 1=1 and stores.deleted_at IS NULL  ".$searchQuery." order by ".$columnName.$limit) );
        //print_r($stores);die;
        $data = array();
        foreach($stores as $row) {
            $historical_data = historical_data($row->id);
            if($historical_data)
                $last_count_date= date('m-d-Y',strtotime($historical_data->dtJobDate));
            else
                $last_count_date = '';
            $daysavailabletoschedule = StoreScheduleAvailabilityDays::where('store_id', '=',$row->id)->pluck('days')->toArray();
            $daysavailabletoschedule = implode(', ',$daysavailabletoschedule);
            $monthavailabletoschedule = StoreScheduleMonths::where('store_id', '=',$row->id)->pluck('month')->toArray(' ');
            $monthavailabletoschedule = implode(', ',$monthavailabletoschedule);
            $data[] = array(
            "id"=>$row->id,
            "store"=>$row->name,
            "city"=>$row->cityname,
            "state"=>$row->statename,
            "inventory_level"=>$row->inventory_level,
            "apr"=>$row->apr,
            "last_count_date"=>$last_count_date,
            "scheduling_contact_name"=>$row->scheduling_contact_name,
            "scheduling_contact_email"=>$row->scheduling_contact_email,
            "scheduling_contact_phone"=>$row->scheduling_contact_phone,
            "daysavailabletoschedule"=>$daysavailabletoschedule,
            "monthavailabletoschedule"=>$monthavailabletoschedule,
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
        
    }
}
