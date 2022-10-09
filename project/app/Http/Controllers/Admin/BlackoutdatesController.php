<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Blackoutdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBlackoutdatesRequest;
use App\Http\Requests\Admin\UpdateBlackoutdatesRequest;
use App\Http\Controllers\Traits\FileUploadTrait;
use Illuminate\Support\Facades\DB;
use App\Models\Division;
use App\Models\Client;
use App\Models\District;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CostCenterExport;
use Illuminate\Support\Facades\Mail;
//use Carbon;
use App\Imports\ValidateBlackoutdateImport;
use App\Imports\BlackoutdateImport;

class BlackoutdatesController extends Controller
{
    /**
     * Display a listing of Blackout Date.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (! Gate::allows('manage_blackout_dates')) {
            return abort(401);
        }
        $clients = DB::table('clients')->where('status','=', 'active')->pluck('name','id');
        $divisions = DB::table('divisions')->where('status','=', 'active')->pluck('name','id');
        $districts = DB::table('districts')->where('status','=', 'active')->pluck('number','id');
        $stores = DB::table('stores')->where('status','=', 'active')->pluck('name','id');
        return view('admin.blackoutdates.index', compact('clients','divisions','districts','stores'));
    }

    /**
     * Show the form for creating new Blackout Date.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('manage_blackout_dates')) {
            return abort(401);
        }
        $states = DB::table('states')->pluck('name','id');
        $associations = DB::table('associations')->pluck('name','id');
        //$divisions = DB::table('divisions')->pluck('name','id');
        //$employees = DB::table('employees')->where('status','=','Active')->pluck('name','id');
        $areas = DB::table('areas')->pluck('title','id');
        
        return view('admin.blackoutdates.create', compact('states','associations','areas'));
    }

    /**
     * Store a newly created Blackout Date in storage.
     *
     * @param  \App\Http\Requests\StoreBlackoutdatesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBlackoutdatesRequest $request)
    {
        if (! Gate::allows('manage_blackout_dates')) {
            return abort(401);
        }
        //echo $request->financial_year;die;
        //echo '<pre>';print_r($request->all());die;
        if($request->has('blackoutxls'))
        {
            //echo '<pre>';print_r($request->all());die;
            Excel::import(new BlackoutdateImport($request->financial_year,$request->blackout_client_id), request()->file('blackoutxls'));
            //echo '<pre>';print_r($request->all());die;
            if(!isset($request['blackoutxls']))
                return redirect()->route('admin.blackoutdates.index')->withErrors('Please upload xls file.');
            $file = $request->file('blackoutxls');
            $filename = time().$file->getClientOriginalName();
            $destinationPath = 'uploads/blackoutdateexcel/';
            $file->move($destinationPath,$filename);
            return redirect()->route('admin.blackoutdates.index')->with('successmsg', 'Blackout date imported successfully.');
        }
        $request->merge(['date' => date("Y-m-d", strtotime($request->blackout_date)),
            'client_id'=>$request->blackout_client_id,
            'division_id'=>$request->blackout_division_id,
            'district_id'=>$request->blackout_district_id,
            'store_id'=>$request->blackout_store_id]);
        $store = Blackoutdate::create($request->all());
        //die;
        return redirect()->route('admin.blackoutdates.index')->with('successmsg', 'Blackout date added successfully.');
    }

    public function validateBlackoutdateImportExcel()
    {
        $events = Excel::toArray(new ValidateBlackoutdateImport, request()->file('blackoutxls'));
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
//                    $event = Event::select('id')->where('id','=',$row['0'])->get();
//                    if($event->isEmpty())
//                    {
//                        $errors[]="Invalid Event ID on Row#".($rowcount+1);
//                        $errorflag=1;
//                    }
                }else{
                    $errors[]="Blackout date is missing on Row#".($rowcount+1);
                    $errorflag=1;
                }
//                if(!$row['2'] && !$row['3'] && !$row['4'])
//                {
//                    $errors[]="None of Division, District, Store id is filled on Row#".($rowcount+1);
//                    $errorflag=1;
//                }
            }
            $rowcount++;
        }
        //echo '<pre>';print_r($k);die;
        if($errorflag)
            return Response::json(array('status'=>'Error','errors'=>$errors),200);
        else
            return Response::json(array('status'=>'Success'),200);
    }
    
    /**
     * Show the form for editing Blackout Date.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('manage_blackout_dates')) {
            return abort(401);
        }
        $clients = DB::table('clients')->where('status','=', 'active')->pluck('name','id');
        $divisions = DB::table('divisions')->where('status','=', 'active')->pluck('name','id');
        $districts = DB::table('districts')->where('status','=', 'active')->pluck('number','id');
        $stores = DB::table('stores')->where('status','=', 'active')->pluck('name','id');
        
        $blackoutdate = Blackoutdate::with(array('client','division','district','store'))->findOrFail($id);
        
        return view('admin.blackoutdates.edit', compact('blackoutdate','stores','clients','divisions','districts'));
    }

    /**
     * Update Blackout Date in storage.
     *
     * @param  \App\Http\Requests\UpdateBlackoutdatesControllersRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBlackoutdatesRequest $request, $id)
    {
        if (! Gate::allows('manage_blackout_dates')) {
            return abort(401);
        }
        //DB::enableQueryLog();
        
        $client_id = $request->blackout_client_id;
        $financial_year = $request->financial_year;
        $date = date('Y-m-d',strtotime($request->blackout_date));
        $division_id = ($request->blackout_division_id)?$request->blackout_division_id:0;
        $district_id = ($request->blackout_district_id)?$request->blackout_district_id:0;
        $store_id = ($request->blackout_store_id)?$request->blackout_store_id:0;
        //echo '<pre>';print_r($request->all());
        $already_exist = Blackoutdate::where('financial_year', '=',$financial_year)
                        ->where('client_id', '=',$client_id)
                        ->where('division_id', '=',$division_id)
                         ->where('district_id', '=',$district_id)
                        ->where('store_id', '=',$store_id)
                        ->where('date', '=',$date)
                        ->where('id', '!=',$id)
                        ->get();
        //echo '<pre>';print_r($already_exist);
        //dd(DB::getQueryLog());
        if(!$already_exist->isEmpty())
                return redirect()->route('admin.blackoutdates.edit',$id)->withErrors('Blackout date already exist.');
            
        
        $blackoutdate = Blackoutdate::findOrFail($id);
        $request->merge(['date' => $date]);
        $blackoutdate->update($request->all());
        return redirect()->route('admin.blackoutdates.index')->with('successmsg', 'Store updated successfully.');
    }


    /**
     * Display Blackout Date.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        if (! Gate::allows('manage_blackout_dates')) {
            return abort(401);
        }
        $store = BlackoutdatesController::with(array('district','division','client','store'))->findOrFail($id);
        
        //$selected_jsa = StoreApr::with('area')->where('store_id','=',$id)->get();
        //echo '<pre>';print_r($store->area_prime_responsibility->title);die;
//        $area_sel = array();
//        foreach($selected_areas as $row)
//            $area_sel[] = $row->area->id;
//        $jsas = Jsa::whereIn('area_id', $area_sel)->get();
        if($request->ajax())
        {
            return Response::json(array('store'=>$store),200);
        }else{
            return view('admin.blackoutdates.show',compact('store'));
        }
    }


    /**
     * Remove Blackout Date from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('manage_blackout_dates')) {
            return abort(401);
        }
        //echo $id;die;
        $store = Blackoutdate::findOrFail($id);
        $store->delete();

        return redirect()->route('admin.blackoutdates.index')->with('successmsg', 'Blackout date deleted successfully.');
    }

    /**
     * Delete all selected Blackout Date at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('manage_blackout_dates')) {
            return abort(401);
        }
        if ($request->input('ids')) {
            $entries = BlackoutdatesController::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }


    /**
     * Restore Blackout Date from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('manage_blackout_dates')) {
            return abort(401);
        }
        $store = BlackoutdatesController::onlyTrashed()->findOrFail($id);
        $store->restore();

        return redirect()->route('admin.blackoutdates.index')->with('successmsg', 'Blackout date set as active successfully.');
    }

    /**
     * Permanently delete Blackout Date from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('manage_blackout_dates')) {
            return abort(401);
        }
        $store = BlackoutdatesController::onlyTrashed()->findOrFail($id);
        $store->forceDelete();

        return redirect()->route('admin.blackoutdates.index')->with('successmsg', 'Blackout date deleted successfully.');
    }
    
    /**
     * Permanently delete Blackout Date from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
    */
    public function get_list_by_page(Request $request) {
        
        //print_r($request->all());die;
        $draw = $request['draw'];
        $row = $request['start'];
        $rowperpage = $request['length']; // Rows display per page
        //echo $rowperpage;
//        if($rowperpage==-1)
//            $limit='';
//        else
        if($rowperpage<0)
            $limit='';
        elseif(intval($rowperpage) && $row>=0)
            $limit = " limit ".$row.",".$rowperpage;
        else
            $limit = ' limit 0,25';
        $columnIndex = $request['order'][0]['column']; // Column index
        if($columnIndex==0)
        {
            $columnName = 'client_blackout_dates.id asc';
        }elseif($columnIndex==1)
        {
            $columnName = 'clients.name asc';
        }elseif($columnIndex==2)
        {
            $columnName = 'divisions.name asc';
        }elseif($columnIndex==3)
        {
            $columnName = 'districts.number asc';
        }elseif($columnIndex==4)
        {
            $columnName = 'stores.name asc';
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
        
        if($request['financial_year'] != ''){
            $financial_year = $request['financial_year'];
            $request->session()->put('financial_year',$request['financial_year']);
            $searchQuery .= " and (client_blackout_dates.financial_year=".$request['financial_year'].") ";
        }else{
            $request->session()->forget('financial_year');
        }
        
        if($request['blackout_client_id'] != ''){
            $clients = $request['blackout_client_id'];
            $request->session()->put('blackout_client_id',$request['blackout_client_id']);
            $searchQuery .= " and (client_blackout_dates.client_id=".$request['blackout_client_id'].") ";
        }else{
            $request->session()->forget('blackout_client_id');
        }
        
        if($request['blackout_division_id'] != ''){
            $request->session()->put('blackout_division_id',$request['blackout_division_id']);
            $searchQuery .= " and (client_blackout_dates.division_id=".$request['blackout_division_id'].") ";
        }else{
            $request->session()->forget('blackout_division_id');
        }
        
        if($request['blackout_district_id'] != ''){
            $request->session()->put('blackout_district_id',$request['blackout_district_id']);
            $searchQuery .= " and (client_blackout_dates.district_id=".$request['blackout_district_id'].") ";
        }else{
            $request->session()->forget('blackout_district_id');
        }
        
        if($request['blackout_store_id'] != ''){
            $request->session()->put('blackout_store_id',$request['blackout_store_id']);
            $searchQuery .= " and (client_blackout_dates.store_id=".$request['blackout_store_id'].") ";
        }else{
            $request->session()->forget('blackout_store_id');
        }
       
        if($searchValue != ''){
           $searchQuery .= " and (client_blackout_dates.description like '%".$searchValue."%' or "
                . "client_blackout_dates.date like '%".$searchValue."%') ";
        }
        
        //echo $searchQuery;
        ## Total number of records without filtering
        $records = DB::select( DB::raw("SELECT count(*) as allcount FROM client_blackout_dates") );
        $totalRecords = $records[0]->allcount;

        ## Total number of records with filtering
        
        $records = DB::select( DB::raw("SELECT count(*) as allcount FROM client_blackout_dates "
                . "left join clients on clients.id=client_blackout_dates.client_id "
                . "left join divisions on divisions.id=client_blackout_dates.division_id "
                . "left join districts on districts.id=client_blackout_dates.district_id "
                . "left join stores on stores.id=client_blackout_dates.store_id "
                . "where 1=1 ".$searchQuery) );
        $totalRecordwithFilter = $records[0]->allcount;
        
        ## Fetch records
        $blackoutdates = DB::select( DB::raw("select client_blackout_dates.*,clients.name as client,"
                . "divisions.name as division,districts.number as district,stores.name as store "
                . " from client_blackout_dates "
                . "left join clients on clients.id=client_blackout_dates.client_id "
                . "left join divisions on divisions.id=client_blackout_dates.division_id "
                . "left join districts on districts.id=client_blackout_dates.district_id "
                . "left join stores on stores.id=client_blackout_dates.store_id "
                . "WHERE 1=1  ".$searchQuery." order by ".$columnName.$limit) );
        //print_r($blackoutdates);die;
        $data = array();
        foreach($blackoutdates as $row) {
            $action_buttons = '';
            if (Gate::allows('manage_blackout_dates')) {
                $action_buttons.=' <a href="'.route('admin.blackoutdates.edit',[$row->id]).'" style="margin-right:1px;" title="Edit Blackout date" class="btn btn-xs btn-info pull-left"><i class="fa fa-edit"></i></a>';
                $action_buttons.=' <form method="POST" action="'.route('admin.blackoutdates.destroy',[$row->id]).'" accept-charset="UTF-8" style="display: inline-block;" onsubmit="return confirm(\'Are you sure?\');">
                    <input name="_method" type="hidden" value="DELETE">
                    <input name="_token" type="hidden" value="'.$request->session()->token().'">
                    <button title="Delete Blackout Date" class="btn btn-danger btn-xs" type="submit"><i class="fa fa-trash"></i></button>
                    </form>';
            }
            $data[] = array(
            "id"=>$row->id,
            "store"=>$row->store,
            "client"=>$row->client,
            "district"=>$row->district,
            "division"=>$row->division,
            "date"=>$row->date,
            "description"=>$row->description,
            "buttons"=>$action_buttons
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
    
}
