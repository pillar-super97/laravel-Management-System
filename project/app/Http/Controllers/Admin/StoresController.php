<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Store;
use App\Models\StoreScheduleAvailabilityDays;
use App\Models\StoreScheduleMonths;
//use App\Models\StoreApr;
//use App\Models\StoreJsa;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStoresRequest;
use App\Http\Requests\Admin\UpdateStoresRequest;
use App\Http\Controllers\Traits\FileUploadTrait;
use Illuminate\Support\Facades\DB;
use App\Models\Division;
use App\Models\Client;
use App\Models\District;
//use App\Models\Jsa;
use App\Models\Area;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CostCenterExport;
use Illuminate\Support\Facades\Mail;
//use Carbon;

class StoresController extends Controller
{
    use FileUploadTrait;

    /**
     * Display a listing of Store.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (! Gate::allows('store_view')) {
            return abort(401);
        }

//        $stores = Store::with(array('district','division'))->orderBy('name','asc');
//       
//        if (request('show_deleted') == 1) {
//            if (! Gate::allows('store_delete')) {
//                return abort(401);
//            }
//            $stores = $stores->onlyTrashed()->get();
//        }elseif (request('show_all') == 1) {
//            if (! Gate::allows('store_delete')) {
//                return abort(401);
//            }
//            $stores = $stores->withTrashed()->get();
//        } else {
//            $stores = $stores->get();
//        }
        //print_r($stores);die;
        //$users = User::with(array('city','state','country','parentuser'))->where('user_type', '=', 'member')->get();
        $clients = DB::table('clients')->pluck('name','id');
        $states = DB::table('states')->pluck('name','id');
        $stores = DB::table('stores')->distinct()->pluck('store_type');
        //echo $request->session()->get('state_id');die;
        if(($request->session()->get('state_id'))){
            $cities = DB::table('cities')->where('state_id','=',$request->session()->get('state_id'))->pluck('name','id');
        }else
           $cities=array(); 
        //print_r($cities);die;
        return view('admin.stores.index', compact('clients','states','cities','stores'));
    }

    /**
     * Show the form for creating new Store.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('store_create')) {
            return abort(401);
        }
        $states = DB::table('states')->pluck('name','id');
        $associations = DB::table('associations')->pluck('name','id');
        //$divisions = DB::table('divisions')->pluck('name','id');
        //$employees = DB::table('employees')->pluck('name','id');
        $areas = DB::table('areas')->pluck('title','id');
        
        return view('admin.stores.create', compact('states','associations','areas'));
    }

    /**
     * Store a newly created Store in storage.
     *
     * @param  \App\Http\Requests\StoreStoresRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreStoresRequest $request)
    {
        if (! Gate::allows('store_create')) {
            return abort(401);
        }
        $request->merge(['rate_effective_date' => date("Y-m-d", strtotime($request->rate_effective_date))]);
        $store = Store::create($request->all());
        //echo '<pre>';print_r($request->all());die;
//        if($request->apr)
//        {
//            foreach($request->apr as $apr)
//            {
//                StoreApr::create([
//                    'store_id' => $store->id,
//                    'area_id' => $apr,
//                ]);
//            }
//        }
        
//        if($request->jsa)
//        {
//            foreach($request->jsa as $jsa)
//            {
//                StoreJsa::create([
//                    'store_id' => $store->id,
//                    'jsa_id' => $jsa,
//                ]);
//            }
//        }
        
        if($request->days_avai_to_schedule)
        {
            foreach($request->days_avai_to_schedule as $day)
            {
                StoreScheduleAvailabilityDays::create([
                    'store_id' => $store->id,
                    'days' => $day,
                ]);
            }
        }
        if($request->month_to_schedule)
        {
            foreach($request->month_to_schedule as $month)
            {
                StoreScheduleMonths::create([
                    'store_id' => $store->id,
                    'month' => $month,
                ]);
            }
        }
        //  echo "<pre>";
//        print_r($request->all());
//        die;
        return redirect()->route('admin.stores.index')->with('successmsg', 'Store added successfully.');
    }


    /**
     * Show the form for editing Store.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('store_edit')) {
            return abort(401);
        }
        
        $states = DB::table('states')->pluck('name','id')->prepend('Please select', '');
        $clients = DB::table('clients')->pluck('name','id');
        $associations = DB::table('associations')->pluck('name','id');
        //$employees = DB::table('employees')->pluck('name','id');
        $areas = Area::where('status','=','active')->get();
        //$store = Store::findOrFail($id);
        $store = Store::with(array('city','state','scheduling_city','scheduling_state','sec_scheduling_city','sec_scheduling_state',
            'billing_city','billing_state','schedule_availability_days','schedule_months','client','division',
            'district'))->findOrFail($id);
        //echo $stores->state->id;
        //echo $stores->country_id;
        $divisions = DB::table('divisions')->where('client_id','=',$store->client_id)->pluck('name','id');
        $districts = DB::table('districts')->where('division_id','=',$store->division_id)->pluck('number','id');
        $states = DB::table('states')->pluck('name','id')->prepend('Please select', '');
        $cities = DB::table('cities')->where('state_id','=',$store->state_id)->pluck('name','id')->prepend('Please select', '');
        $scheduling_states = DB::table('states')->pluck('name','id')->prepend('Please select', '');
        $scheduling_cities = DB::table('cities')->where('state_id','=',$store->scheduling_contact_state_id)->pluck('name','id')->prepend('Please select', '');
        $sec_scheduling_states = DB::table('states')->pluck('name','id')->prepend('Please select', '');
        $sec_scheduling_cities = DB::table('cities')->where('state_id','=',$store->sec_scheduling_contact_state_id)->pluck('name','id')->prepend('Please select', '');
        $billing_states = DB::table('states')->pluck('name','id')->prepend('Please select', '');
        $billing_cities = DB::table('cities')->where('state_id','=',$store->billing_contact_state_id)->pluck('name','id')->prepend('Please select', '');
        //echo $store->state;die;
        //echo $store->city_id;die;
        //echo '<pre>';print_r($cities);die;
//        $selected_jsa_areas = array();
//        foreach($store->jsa as $js)
//            $selected_jsa_areas[] = $js->jsa_id;
//        
//        $selected_areas = Jsa::whereIn('id', $selected_jsa_areas)->get();
//        $area_sel = array();
//        foreach($store->apr as $row)
//            $area_sel[] = $row->area->id;
        //$jsas = Jsa::whereIn('area_id', $area_sel)->get();
        return view('admin.stores.edit', compact('store', 'states','states','cities','clients','scheduling_states','scheduling_cities',
                    'sec_scheduling_states','sec_scheduling_cities','billing_states','billing_cities','divisions','districts','areas','associations'));
    }

    /**
     * Update Store in storage.
     *
     * @param  \App\Http\Requests\UpdateStoresRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateStoresRequest $request, $id)
    {
        if (! Gate::allows('store_edit')) {
            return abort(401);
        }
        
        $store = Store::findOrFail($id);
        //echo "<pre>";
        //print_r($request->all());
//        print_r($store);
        $request->merge(['rate_effective_date' => date("Y-m-d", strtotime($request->rate_effective_date))]);
        if($request['rate_effective_date'] != $store->rate_effective_date)
        {
            if($store->rate_effective_date)
                $rate_effective_date = $store->rate_effective_date;
            else
                $rate_effective_date=date('Y-m-d');
            $data = array('store_id'=>$id,'rate'=>$store->rate,"rate_from"=>$rate_effective_date);
            DB::table('store_effective_rate_date_log')->insert($data);
        }
//                echo "<pre>";
//        print_r($request->all());die;
        //print_r($store);
        $store->update($request->all());
        StoreScheduleAvailabilityDays::where('store_id', '=',$id)->delete();
        StoreScheduleMonths::where('store_id', '=',$id)->delete();
        //StoreApr::where('store_id', '=',$id)->delete();
        //StoreJsa::where('store_id', '=',$id)->delete();
        if($request->days_avai_to_schedule)
        {
            foreach($request->days_avai_to_schedule as $day)
            {
                StoreScheduleAvailabilityDays::create([
                    'store_id' => $store->id,
                    'days' => $day,
                ]);
            }
        }
        if($request->month_to_schedule)
        {
            foreach($request->month_to_schedule as $month)
            {
                StoreScheduleMonths::create([
                    'store_id' => $store->id,
                    'month' => $month,
                ]);
            }
        }
//        if($request->apr)
//        {
//            foreach($request->apr as $apr)
//            {
//                StoreApr::create([
//                    'store_id' => $store->id,
//                    'area_id' => $apr,
//                ]);
//            }
//        }
        
//        if($request->jsa)
//        {
//            foreach($request->jsa as $jsa)
//            {
//                StoreJsa::create([
//                    'store_id' => $store->id,
//                    'jsa_id' => $jsa,
//                ]);
//            }
//        }
        return redirect()->route('admin.stores.index')->with('successmsg', 'Store updated successfully.');
    }


    /**
     * Display Store.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        if (! Gate::allows('store_view')) {
            return abort(401);
        }
        $store = Store::with(array('city','state','scheduling_city','scheduling_state','sec_scheduling_city','sec_scheduling_state',
            'billing_city','billing_state','schedule_availability_days','client','division',
            'district','area_prime_responsibility'))->findOrFail($id);
        
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
            return view('admin.stores.show',compact('store'));
        }
    }


    /**
     * Remove Store from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('store_delete')) {
            return abort(401);
        }
        $store = Store::findOrFail($id);
        $store->delete();

        return redirect()->route('admin.stores.index')->with('successmsg', 'Store set as inactive successfully.');
    }

    /**
     * Delete all selected Store at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('store_delete')) {
            return abort(401);
        }
        if ($request->input('ids')) {
            $entries = Store::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }


    /**
     * Restore Store from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('store_delete')) {
            return abort(401);
        }
        $store = Store::onlyTrashed()->findOrFail($id);
        $store->restore();

        return redirect()->route('admin.stores.index')->with('successmsg', 'Store set as active successfully.');
    }

    /**
     * Permanently delete Store from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('store_delete')) {
            return abort(401);
        }
        $store = Store::onlyTrashed()->findOrFail($id);
        $store->forceDelete();

        return redirect()->route('admin.stores.index')->with('successmsg', 'Store deleted successfully.');
    }
    
    
    
    public function getDistrictByDivision(Request $request) {
        $districts = District::where('division_id','=',$request->division_id)->where('client_id','=',$request->client_id)->where('status','=', 'active')->get();
        return Response::json(array('districts'=>$districts),200);
    }
    
    public function getJSAByArea(Request $request) {
        $jsas = Jsa::whereIn('area_id',$request->area_id)->where('status','=', 'active')->get();
        return Response::json(array('jsas'=>$jsas),200);
    }
    function testcron1($array)
    {
        $html='<ul>';
        foreach($array as $key=>$row)
        {
            $html.='<li>'.$key.'--'.$row.'</li>';
        }
        $html.='</ul>';
        $user_detail = array(
            'name'            => 'Admin',
            'email'           => 'kunal.kumar@pegasusone.com',
            'email_content'   => 'The timesheet pushed by you into Kronos gives following error<br> .'.$html,
            'mail_from_email' => env('MAIL_FROM'),
            'mail_from'       => env('MAIL_NAME'),
            'subject'         => 'Cost center export'
        );
        $user_single = (object) $user_detail;
        Mail::send('emails.kronos_timesheet_error',['user' => $user_single], function ($message) use ($user_single) {
                $message->from($user_single->mail_from_email,$user_single->mail_from);
                $message->to($user_single->email, $user_single->name)->subject($user_single->subject);
                $message->replyTo($user_single->mail_from_email,$user_single->mail_from);
        });
    }
    public function export_cost_center_to_kronos(Request $request)
    {
        $export_file_name = 'Cost-Center-'.date('Y-m-d h-i-s-A').'.csv';
        Excel::store(new CostCenterExport(2018), $export_file_name,'media');
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
        $ch = curl_init('https://secure3.saashr.com/ta/rest/v1/import/105');
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
        //$this->testcron1(array('msg'=>$location));
        $array_data = json_decode(json_encode(simplexml_load_string($statusResponse)), true);
        if(isset($array_data['status']) && $array_data['status']=="running")
        {
            return redirect()->route('admin.stores.index')->with('successmsg', 'Server is busy now. Please try after some time.');
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
                $user_detail = array(
                    'name'            => 'Admin',
                    'email'           => "lancebowser@msi-inv.com",
                    'email_content'   => 'Cost center pushed into Kronos gives following error on .'.date('m-d-Y').'<br>'.$html,
                    'mail_from_email' => env('MAIL_FROM'),
                    'mail_from'       => env('MAIL_NAME'),
                    'subject'         => 'Cost center pushed into Kronos.'
                );
                $user_single = (object) $user_detail;
                Mail::send('emails.kronos_timesheet_error',['user' => $user_single], function ($message) use ($user_single) {
                        $message->from($user_single->mail_from_email,$user_single->mail_from);
                        $message->to($user_single->email, $user_single->name)->subject($user_single->subject);
                        $message->replyTo($user_single->mail_from_email,$user_single->mail_from);
                });
                echo $html;exit;
                return redirect()->route('admin.stores.index')->with('successmsg', $html);
            }else{
                $user_detail = array(
                    'name'            => 'Admin',
                    'email'           => "lancebowser@msi-inv.com",
                    'email_content'   => 'Cost center pushed into Kronos successfully on .'.date('m-d-Y'),
                    'mail_from_email' => env('MAIL_FROM'),
                    'mail_from'       => env('MAIL_NAME'),
                    'subject'         => 'Cost center pushed into Kronos.'
                );
                $user_single = (object) $user_detail;
                Mail::send('emails.kronos_timesheet_error',['user' => $user_single], function ($message) use ($user_single) {
                        $message->from($user_single->mail_from_email,$user_single->mail_from);
                        $message->to($user_single->email, $user_single->name)->subject($user_single->subject);
                        $message->replyTo($user_single->mail_from_email,$user_single->mail_from);
                });
                return redirect()->route('admin.stores.index')->with('successmsg', 'Cost Center pushed into Kronos successfully.');
            }
        }
        
        exit;  
        
        exit;  
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
    
    public function cost_center_to_kronos()
    {
        if (! Gate::allows('store_view')) {
            return abort(401);
        }
        $stores = Store::with(array('district','division'))->where('status','=', 'active')->orderBy('name','asc');
       
        if (request('show_deleted') == 1) {
            if (! Gate::allows('store_delete')) {
                return abort(401);
            }
            $stores = $stores->onlyTrashed()->get();
        } else {
            $stores = $stores->get();
        }
        //print_r($stores);die;
        //$users = User::with(array('city','state','country','parentuser'))->where('user_type', '=', 'member')->get();
         
        return view('admin.stores.index', compact('stores'));
    }
    
    public function get_store_list_by_page(Request $request) {
        
        // echo '<pre>';
        // print_r($request->all());
        // echo '</pre>';
        // die;
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
            $columnName = 'stores.name asc';
        }elseif($columnIndex==1)
        {
            $columnName = 'stores.name asc';
        }elseif($columnIndex==2)
        {
            $columnName = 'clients.name asc';
        }elseif($columnIndex==3)
        {
            $columnName = 'districts.number asc';
        }elseif($columnIndex==4)
        {
            $columnName = 'cities.name asc';
        }elseif($columnIndex==5)
        {
            $columnName = 'states.name asc';
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
        if($request['client_id'] != ''){
            $clients = implode(',', $request['client_id']);
            $request->session()->put('client_id',$request['client_id']);
            $searchQuery .= " and (stores.client_id in(".$clients.")) ";
            
        }else{
            $request->session()->forget('client_id');
        }
        // if($request['store_type'] != ''){
        //     $stores= $request->store_type;
        //     $request->session()->put('store_type',$request['store_type']);
        //     $searchQuery .= " and (stores.store_type in(".$stores.")) ";
        //     // echo '<pre>';print_r($searchQuery);die;
            
        // }else{
        //     $request->session()->forget('store_type');
        // }
        
        if($request['state_id'] != ''){
            $request->session()->put('state_id',$request['state_id']);
            $searchQuery .= " and (stores.state_id=".$request['state_id'].") ";
        }else{
            $request->session()->forget('state_id');
        }
        
        if($request['city_id'] != ''){
            $request->session()->put('city_id',$request['city_id']);
            $searchQuery .= " and (stores.city_id=".$request['city_id'].") ";
        }else{
            $request->session()->forget('city_id');
        }
        
        if($request['inactive_only'] == 1){
            $request->session()->put('inactive_only',$request['inactive_only']);
            $searchQuery .= " and (stores.deleted_at IS NOT NULL) ";
        }else{
            $searchQuery .= " and (stores.deleted_at IS NULL) ";
            $request->session()->forget('inactive_only');
        }
        
        //echo '<pre>';print_r($store_id);die;
       
       
        if($searchValue != ''){
           $searchQuery .= " and (stores.name like '%".$searchValue."%' or "
                . "clients.name like '%".$searchValue."%' or "
                . "cities.name like'%".$searchValue."%' or "
                . "stores.store_type like'%".$searchValue."%' or "
                . "states.name like '%".$searchValue."%') ";
        }
        
        //echo $searchQuery;
        ## Total number of records without filtering
        $records = DB::select( DB::raw("SELECT count(*) as allcount FROM stores") );
        $totalRecords = $records[0]->allcount;

        ## Total number of records with filtering
        
        $records = DB::select( DB::raw("SELECT count(*) as allcount FROM stores "
                . "left join clients on stores.client_id=clients.id "
                . "left join cities on cities.id=stores.city_id "
                . "left join states on states.id=stores.state_id "
                . "where 1=1 ".$searchQuery) );
        $totalRecordwithFilter = $records[0]->allcount;
        
        ## Fetch records
        if($request['store_type'] != ''){
            $stores =DB::table('stores')
            ->leftjoin('clients','clients.id','=','stores.client_id')
            ->leftjoin('districts','districts.id','=','stores.district_id')
            ->leftjoin('cities','cities.id','=','stores.city_id')
            ->leftjoin('states','states.id','=','stores.state_id')
            ->select('stores.*')
            ->where('stores.name', 'like', '%' .$searchValue. '%')
            ->addSelect(['clients.name as clientname',
            'cities.name as cityname',
            'states.name as statename',
            'districts.number as district'])
            ->where('stores.store_type', $request->store_type)
            ->where('stores.deleted_at', NULL)
            ->get();
            $totalRecordwithFilter = count($stores);
            
           
        }else{
            $stores = DB::select( DB::raw("select stores.*,clients.name as clientname,"
            . "cities.name as cityname,states.name as statename,districts.number as district "
            . " from stores "
            . "left join clients on clients.id=stores.client_id "
            . "left join districts on districts.id=stores.district_id "
            . "left join cities on cities.id=stores.city_id "
            . "left join states on states.id=stores.state_id "
            . "WHERE 1=1  ".$searchQuery." order by ".$columnName.$limit) );

        }

        //print_r($stores);die;
        $data = array();
        foreach($stores as $row) {
            $minbilling=$row->minbilling;
            $rate=$row->rate;
            $type=$row->store_type;
            $action_buttons = '';
            if($request['inactive_only'] == 1){
                if (Gate::allows('store_delete')) {
                    $action_buttons.=' <form method="POST" action="'.route('admin.stores.restore',[$row->id]).'" accept-charset="UTF-8" style="display: inline-block;margin-top:-1px;margin-right:1px;" onsubmit="return confirm(\'Are you sure?\');" class="pull-left">
                        <input name="_token" type="hidden" value="'.$request->session()->token().'">
                        <button title="Active Store" class="btn btn-success btn-xs" type="submit"><i class="fa fa-eye"></i></button>
                        </form>';
                    $action_buttons.=' <form method="POST" action="'.route('admin.stores.perma_del',[$row->id]).'" accept-charset="UTF-8" style="display: inline-block;margin-top:-1px;margin-right:1px;" onsubmit="return confirm(\'Are you sure?\');" class="pull-left">
                         <input name="_method" type="hidden" value="DELETE">
                        <input name="_token" type="hidden" value="'.$request->session()->token().'">
                        <button title="Delete Store" class="btn btn-danger btn-xs" type="submit"><i class="fa fa-trash"></i></button>
                        </form>';
                }
            }else
            {
                if (Gate::allows('store_view')) {
                    $action_buttons.=' <a href="'.route('admin.stores.show',[$row->id]).'" style="margin-right:1px;" title="View Detail" class="btn btn-xs btn-primary pull-left"><i class="fa fa-eye"></i></a>';
                }
                if (Gate::allows('store_edit')) {
                    $action_buttons.=' <a href="'.route('admin.stores.edit',[$row->id]).'" style="margin-right:1px;" title="Edit Store" class="btn btn-xs btn-info pull-left"><i class="fa fa-edit"></i></a>';
                }
                if (Gate::allows('event_view')) {
                    $action_buttons.=' <a href="'.route('admin.events.showstoreevents',[$row->id]).'" title="View '.$row->name.' Events" class="btn btn-xs btn-primary pull-left"><i class="fa fa-calendar"></i></a>';
                }
                if (Gate::allows('store_delete')) {
                    $action_buttons.=' <form method="POST" action="'.route('admin.stores.destroy',[$row->id]).'" accept-charset="UTF-8" style="display: inline-block;" onsubmit="return confirm(\'Are you sure?\');">
                        <input name="_method" type="hidden" value="DELETE">
                        <input name="_token" type="hidden" value="'.$request->session()->token().'">
                        <button title="Make Inactive" class="btn btn-danger btn-xs" type="submit"><i class="fa fa-trash"></i></button>
                        </form>';
                }
            }
            $historical_data = historical_data($row->id);
            if($historical_data)
                $last_count_date= date('m-d-Y',strtotime($historical_data->dtJobDate));
            else
                $last_count_date = '';
            if($historical_data)
                $last_count_value = '$'.number_format($historical_data->dEmpCount);
            else
                $last_count_value = 0;
            
            
           $data[] = array(
            "id"=>$row->id,
            "store"=>$row->name,
            "client"=>$row->clientname,
            "district"=>$row->district,
            "city"=>$row->cityname,
            "state"=>$row->statename,
            "last_count_date"=>$last_count_date,
            "last_count_value"=>$last_count_value,
            "min_bill"=>$minbilling,
            "rate"=>$rate,
            "type"=>$type,
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
