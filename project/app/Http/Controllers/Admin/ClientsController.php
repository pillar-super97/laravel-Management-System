<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\User;
use App\Models\RoleUser;
use App\Models\Client;
use App\Models\ClientBlackoutDates;
use App\Models\ClientScheduleAvailabilityDays;


use App\Models\Event;
use App\Models\EventInventory;
use App\Models\EventInventoryData;
use App\Models\Location;
use App\Models\SubLocation;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreClientsRequest;
use App\Http\Requests\Admin\UpdateClientsRequest;
use App\Http\Controllers\Traits\FileUploadTrait;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

use Illuminate\Support\Facades\Auth;


use \PDF;
use Symfony\Component\VarDumper\VarDumper;

class ClientsController extends Controller
{
    use FileUploadTrait;

    /**
     * Display a listing of Client.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (! Gate::allows('client_view')) {
            return abort(401);
        }
        //DB::enableQueryLog();
        $clients = Client::with(array('city','state'))->orderBy('name','asc');
       
        if (request('show_deleted') == 1) {
            if (! Gate::allows('client_delete')) {
                return abort(401);
            }
            $clients = $clients->onlyTrashed()->get();
            //$clients = $clients->where('clients.deleted_at','!=',NULL)->get();
        } else {
            $clients = $clients->where('status','=', 'active')->get();
        }
        //print_r($clients);
        //dd(DB::getQueryLog());die;
        //$users = User::with(array('city','state','country','parentuser'))->where('user_type', '=', 'member')->get();
         
        return view('admin.clients.index', compact('clients'));
    }

    /**
     * Show the form for creating new Client.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('client_create')) {
            return abort(401);
        }
        $states = DB::table('states')->pluck('name','id');
        $associations = DB::table('associations')->pluck('name','id');

        return view('admin.clients.create', compact('states','associations'));
    }

    /**
     * Store a newly created Client in storage.
     *
     * @param  \App\Http\Requests\StoreClientsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreClientsRequest $request)
    {
        if (! Gate::allows('client_create')) {
            return abort(401);
        }
       
        //echo '<pre>';print_r($request->all());die;
        $client = Client::create($request->all());

        if($request->cust_login_email)
        {

        //saving client to user for login
        $user = User::create([
                'name' => $request->cust_login_name,
                'email' => $request->cust_login_email,
                'password' => bcrypt($request->cust_login_password),
                'client_id' => $client->id,
                'status' => 'active'
            ]);


        //saving client to role user
        $role = RoleUser::create([
            'role_id' => 10, //assigning role for client manually as in db its on id 10
            'user_id' => $user->id
        ]);

    }

        //echo '<pre>';print_r($request->all());die;
        if($request->days_avai_to_schedule)
        {
            foreach($request->days_avai_to_schedule as $day)
            {
                ClientScheduleAvailabilityDays::create([
                    'client_id' => $client->id,
                    'days' => $day,
                ]);
            }
        }
        if($request->blackout_dates)
        {
            foreach($request->blackout_dates as $date)
            {
                if($date)
                {
                    ClientBlackoutDates::create([
                        'client_id' => $client->id,
                        'date' => date("Y-m-d", strtotime($date)),
                    ]);
                }
            }
        }
//        echo "<pre>";
//        print_r($request->all());
//        die;
        return redirect()->route('admin.clients.index')->with('successmsg', 'Client added successfully.');
    }


    /**
     * Show the form for editing Client.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('client_edit')) {
            return abort(401);
        }
        
        $user = User::select('name as login_name', 'email as login_email')->where('client_id', $id)->first();

        $states = DB::table('states')->pluck('name','id')->prepend('Please select', '');
        $associations = DB::table('associations')->pluck('name','id');
        //$client = Client::findOrFail($id);
        $client = Client::with(array('city','state','scheduling_city','scheduling_state','sec_scheduling_city','sec_scheduling_state',
            'billing_city','billing_state','blackout_dates','schedule_availability_days','association'))->findOrFail($id);
        //echo $clients->state->id;
        //echo $clients->country_id;
        //$states = DB::table('states')->where('country_id','=',$client->country_id)->pluck('name','id')->prepend('Please select', '');
        $cities = DB::table('cities')->where('state_id','=',$client->state_id)->pluck('name','id')->prepend('Please select', '');
        $scheduling_states = DB::table('states')->pluck('name','id')->prepend('Please select', '');
        $scheduling_cities = DB::table('cities')->where('state_id','=',$client->scheduling_contact_state_id)->pluck('name','id')->prepend('Please select', '');
        $sec_scheduling_states = DB::table('states')->pluck('name','id')->prepend('Please select', '');
        $sec_scheduling_cities = DB::table('cities')->where('state_id','=',$client->sec_scheduling_contact_state_id)->pluck('name','id')->prepend('Please select', '');
        $billing_states = DB::table('states')->pluck('name','id')->prepend('Please select', '');
        $billing_cities = DB::table('cities')->where('state_id','=',$client->billing_contact_state_id)->pluck('name','id')->prepend('Please select', '');
        //echo $client->state;die;
//        echo "<pre>";
//        print_r($client->schedule_availability_days);
//        die;  


//       
        return view('admin.clients.edit', compact('client', 'states','states','cities','associations','scheduling_states','scheduling_cities',
                    'sec_scheduling_states','sec_scheduling_cities','billing_states','billing_cities', 'user'));
        
    }

    /**
     * Update Client in storage.
     *
     * @param  \App\Http\Requests\UpdateClientsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateClientsRequest $request, $id)
    {
        if (! Gate::allows('client_edit')) {
            return abort(401);
        }
        
        if($request->cust_login_email)
        {



        $user = User::where('client_id', $id)->first();


        if(empty($user) || $user == null){
            //check for client in client db
            $client = Client::where('id', $id)->first();

            //saving client to user for login
            $user = User::create([
                'name' => $request->cust_login_name,
                'email' => $request->cust_login_email,
                'password' => bcrypt($request->cust_login_password),
                'client_id' => $client->id,
                'status' => 'active'
            ]);


            //saving client to role user
            $role = RoleUser::create([
                'role_id' => 10, //assigning role for client manually as in db its on id 10
                'user_id' => $user->id
            ]);


        }else{
            $user->name = $request->cust_login_name;
            $user->email = $request->cust_login_email;
            if(!empty($request->cust_login_password)){
                $user->password = bcrypt($request->cust_login_password);
            }
            $user->save();
        }

    }
    
        $client = Client::findOrFail($id);
        $client->update($request->all());
        ClientScheduleAvailabilityDays::where('client_id', '=',$id)->delete();
        ClientBlackoutDates::where('client_id', '=',$id)->delete();
        if($request->days_avai_to_schedule){
            foreach($request->days_avai_to_schedule as $day)
            {
                ClientScheduleAvailabilityDays::create([
                    'client_id' => $client->id,
                    'days' => $day,
                ]);
            }
        }
        if(count($request->blackout_dates))
        {
            foreach($request->blackout_dates as $date)
            {
                if($date)
                {
                    $dates = preg_split('/\-/',$date);
                    $month = $dates[0];
                    $day = $dates[1];
                    $year = $dates[2];
                    $finalDate = $year.'-'.$month.'-'.$day;
                    ClientBlackoutDates::create([
                        'client_id' => $client->id,
                        'date' => $finalDate,
                    ]);
                }
            }
        }
        return redirect()->route('admin.clients.index')->with('successmsg', 'Client updated successfully.');
    }


    /**
     * Display Client.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        if (! Gate::allows('client_view')) {
            return abort(401);
        }
        $client = Client::with(array('city','state','scheduling_city','scheduling_state','sec_scheduling_city','sec_scheduling_state',
            'billing_city','billing_state','blackout_dates','schedule_availability_days','association'))
            ->findOrFail($id);
        if($request->ajax()){
            $states = DB::table('states')->pluck('name','id');
            $cities = DB::table('cities')->where('state_id','=',$client->state_id)->pluck('name','id');
            $scheduling_states = DB::table('states')->pluck('name','id');
            $scheduling_cities = DB::table('cities')->where('state_id','=',$client->scheduling_contact_state_id)->pluck('name','id');
            $sec_scheduling_states = DB::table('states')->pluck('name','id');
            $sec_scheduling_cities = DB::table('cities')->where('state_id','=',$client->sec_scheduling_contact_state_id)->pluck('name','id');
            $billing_states = DB::table('states')->pluck('name','id');
            $billing_cities = DB::table('cities')->where('state_id','=',$client->billing_contact_state_id)->pluck('name','id');
            return Response::json(array('client'=>$client,'states'=>$states,'cities'=>$cities,'scheduling_states'=>$scheduling_states,
                'scheduling_cities'=>$scheduling_cities,'sec_scheduling_states'=>$sec_scheduling_states,'sec_scheduling_cities'=>$sec_scheduling_cities,
                'billing_states'=>$billing_states,'billing_cities'=>$billing_cities),200);
        }else{
            //echo '<pre>';print_r($client);die;
            return view('admin.clients.show',compact('client'));
        }
    }


    /**
     * Remove Client from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('client_delete')) {
            return abort(401);
        }

        $user = User::where('client_id', $id)->first();
        $role = RoleUser::where('client_id', $user->id)->first();

        $user->delete();
        $role->delete();


        $client = Client::findOrFail($id);
        $client->delete();

        return redirect()->route('admin.clients.index')->with('successmsg', 'Client set as inactive successfully.');
    }

    /**
     * Delete all selected Client at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('client_delete')) {
            return abort(401);
        }
        // if ($request->input('ids')) {
        //     $entries = Client::whereIn('id', $request->input('ids'))->get();

        //     foreach ($entries as $entry) {
        //         $entry->delete();
        //     }
        // }

        if ($request->input('ids')) {

            $entries = User::whereIn('client_id', $request->input('ids'))->get();
            // $entries = Client::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $role = RoleUser::where('client_id', $entry->id)->first();
                $entry->delete();
                $role->delete();
            }

            //for client
            $Centries = Client::whereIn('id', $request->input('ids'))->get();
            foreach ($Centries as $entry) {
                $entry->delete();
            }
        }


    }


    /**
     * Restore Client from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('client_delete')) {
            return abort(401);
        }
        $client = Client::onlyTrashed()->findOrFail($id);
        $client->restore();

        return redirect()->route('admin.clients.index')->with('successmsg', 'Client set as active successfully.');
    }

    /**
     * Permanently delete Client from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('client_delete')) {
            return abort(401);
        }
        $client = Client::onlyTrashed()->findOrFail($id);
        $client->forceDelete();

        return redirect()->route('admin.clients.index')->with('successmsg', 'Client deleted successfully.');
    }
    
    public function getClientByAssociation(Request $request) {
        $associations = Client::where('association_id','=',$request->association_id)->where('status','=', 'active')->get();
        return Response::json(array('associations'=>$associations),200);
    }


    public function eventReports($id){

        $event = Event::findOrFail($id);

        return view('clients.reports.index',compact('event', $event));

    }




    public function downloadAreaReport($id){

        $event = Event::with('store')->with(["schedule_employees" => function($q){
            $q->with('employee')->where('task', '=', 'Supervisor');
        }])->findOrFail($id);

        // dd($event);

        $result = Event::select('events.id as event_id', 'events.store_id as store_id', 
                    'event_inventory.id as event_inventory_id', 'event_inventory.inventory_id as inventory_type',
                    'event_inventory_data.sub_location_id as sublocation_id', 'tsp_sub_locations.loc_id as location_id', 
                    'tsp_locations.loc as location', 'tsp_locations.description as location_description', 'stores.client_id as client_id',

                    DB::raw('(CASE 
                    WHEN event_inventory.inventory_id = 1 THEN SUM(event_inventory_data.price)
                    END) as current_price'),

                    DB::raw('(CASE 
                    WHEN event_inventory.inventory_id = 2 THEN SUM(event_inventory_data.price)
                    END) as prior_price'))

            ->leftJoin('event_inventory', 'event_inventory.event_id', '=', 'events.id')
            ->leftJoin('event_inventory_data', 'event_inventory_data.event_inventory_id', '=', 'event_inventory.id')
            ->leftJoin('tsp_sub_locations', 'tsp_sub_locations.id', '=','event_inventory_data.sub_location_id')
            ->leftJoin('tsp_locations', 'tsp_locations.id', '=', 'tsp_sub_locations.loc_id')
            ->leftJoin('stores', 'stores.id', '=' , 'events.store_id')
            ->leftJoin('clients', 'clients.id', '=' , 'stores.client_id')
            ->where('events.id', $id)
            ->where('stores.client_id', Auth::user()->client_id)
            // ->where('event_inventory.inventory_id', 1)
            ->groupBy('events.id','events.store_id',  'tsp_sub_locations.loc_id')
            ->get();


            //dd($event);

        //   return view('clients.reports.area',compact('result', 'event'));

        PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif', 'defaultPaperSize' => 'a4']);
        $pdf = PDF::loadView('clients.reports.area', compact('result', 'event'));
        return $pdf->stream('area report.pdf');

    }


    public function downloadCategoryReport($id){

        $event = Event::with('store')->with(["schedule_employees" => function($q){
            $q->with('employee')->where('task', '=', 'Supervisor');
        }])->findOrFail($id);

        

          $result = Event::select('events.id as event_id', 'events.store_id as store_id', 
                    'event_inventory.id as event_inventory_id', 'event_inventory.inventory_id as inventory_type',
                    'event_inventory_data.sub_location_id as sublocation_id', 'event_inventory_data.pieces as pieces', 'tsp_sub_locations.loc_id as location_id', 
                    'tsp_locations.loc as location', 'tsp_locations.description as location_description', 'stores.client_id as client_id',
                    'categories.id as category_id', 'categories.name as category_name',

                    DB::raw('(CASE 
                    WHEN event_inventory.inventory_id = 1 THEN SUM(event_inventory_data.price)
                    END) as Salesfloor'),

                    DB::raw('(CASE 
                    WHEN event_inventory.inventory_id = 1 THEN SUM(event_inventory_data.price)
                    END) as Stockroom'),


                    DB::raw('(CASE 
                    WHEN event_inventory.inventory_id = 1 THEN SUM(event_inventory_data.price)
                    END) as current_price'),

                    DB::raw('(CASE 
                    WHEN event_inventory.inventory_id = 2 THEN SUM(event_inventory_data.price)
                    END) as prior_price'))

            ->leftJoin('event_inventory', 'event_inventory.event_id', '=', 'events.id')
            ->leftJoin('event_inventory_data', 'event_inventory_data.event_inventory_id', '=', 'event_inventory.id')
            ->leftJoin('categories', 'categories.id', '=' , 'event_inventory_data.category_id')
            ->leftJoin('tsp_sub_locations', 'tsp_sub_locations.id', '=','event_inventory_data.sub_location_id')
            ->leftJoin('tsp_locations', 'tsp_locations.id', '=', 'tsp_sub_locations.loc_id')
            ->leftJoin('stores', 'stores.id', '=' , 'events.store_id')
            ->leftJoin('clients', 'clients.id', '=' , 'stores.client_id')
            ->where('events.id', $id)
            ->where('stores.client_id', Auth::user()->client_id)
            // ->where('event_inventory.inventory_id', 1)
            ->groupBy('events.id','events.store_id',  'categories.id')
            ->get();


          

        //   return view('clients.reports.category',compact('result', 'event'));

        PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif', 'defaultPaperSize' => 'a4']);
        $pdf = PDF::loadView('clients.reports.category', compact('result', 'event'));
        return $pdf->stream('category report.pdf');

    }

    public function downloadLocationReport($id){

        $event = Event::with('store')->with(["schedule_employees" => function($q){
            $q->with('employee')->where('task', '=', 'Supervisor');
        }])->findOrFail($id);

            $result = Event::select('events.id as event_id', 'events.store_id as store_id', 
            'event_inventory.id as event_inventory_id', 'event_inventory.inventory_id as inventory_type',
            'event_inventory_data.sub_location_id as sublocation_id', 'tsp_sub_locations.loc_id as location_id', 'tsp_sub_locations.sub_loc as sublocation',
            'tsp_locations.loc as location', 'tsp_locations.description as location_description', 'stores.client_id as client_id',
            'categories.id as category_id', 'categories.name as category_name',


            DB::raw('(CASE 
            WHEN event_inventory.inventory_id = 1 THEN SUM(event_inventory_data.price)
            END) as current_price'),

            DB::raw('(CASE 
            WHEN event_inventory.inventory_id = 2 THEN SUM(event_inventory_data.price)
            END) as prior_price'))

            ->leftJoin('event_inventory', 'event_inventory.event_id', '=', 'events.id')
            ->leftJoin('event_inventory_data', 'event_inventory_data.event_inventory_id', '=', 'event_inventory.id')
            ->leftJoin('categories', 'categories.id', '=' , 'event_inventory_data.category_id')
            ->leftJoin('tsp_sub_locations', 'tsp_sub_locations.id', '=','event_inventory_data.sub_location_id')
            ->leftJoin('tsp_locations', 'tsp_locations.id', '=', 'tsp_sub_locations.loc_id')
            ->leftJoin('stores', 'stores.id', '=' , 'events.store_id')
            ->leftJoin('clients', 'clients.id', '=' , 'stores.client_id')
            ->where('events.id', $id)
            ->where('stores.client_id', Auth::user()->client_id)
            // ->where('event_inventory.inventory_id', 1)
            ->groupBy('tsp_sub_locations.loc_id')
            ->get();



            //return $result;


            //return view('clients.reports.location',compact('result', 'storeData'));

            PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif', 'defaultPaperSize' => 'a4']);
            $pdf = PDF::loadView('clients.reports.location', compact('result', 'event'));
            return $pdf->stream('location report.pdf');

    }

    public function downloadConsolidationReport($id){

        $event = Event::select('events.id as event_id', 'events.store_id as store_id', 'stores.name as store_name', 'stores.address as store_address')
        ->leftJoin('stores', 'stores.id', '=' , 'events.store_id')
        ->first();

            $result = Event::select('events.id as event_id', 'events.store_id as store_id', 
            'event_inventory.id as event_inventory_id', 'event_inventory.inventory_id as inventory_type',
            'event_inventory_data.sub_location_id as sublocation_id', 'tsp_sub_locations.loc_id as location_id', 'tsp_sub_locations.sub_loc as sublocation',
            'tsp_locations.loc as location', 'tsp_locations.description as location_description', 'stores.store_type as store_type', 'stores.client_id as client_id',
            'categories.id as category_id', 'categories.name as category_name',


            DB::raw('(CASE 
            WHEN event_inventory.inventory_id = 1 THEN SUM(event_inventory_data.price)
            END) as current_price'),

            DB::raw('(CASE 
            WHEN event_inventory.inventory_id = 2 THEN SUM(event_inventory_data.price)
            END) as prior_price'))

            ->leftJoin('event_inventory', 'event_inventory.event_id', '=', 'events.id')
            ->leftJoin('event_inventory_data', 'event_inventory_data.event_inventory_id', '=', 'event_inventory.id')
            ->leftJoin('categories', 'categories.id', '=' , 'event_inventory_data.category_id')
            ->leftJoin('tsp_sub_locations', 'tsp_sub_locations.id', '=','event_inventory_data.sub_location_id')
            ->leftJoin('tsp_locations', 'tsp_locations.id', '=', 'tsp_sub_locations.loc_id')
            ->leftJoin('stores', 'stores.id', '=' , 'events.store_id')
            ->leftJoin('clients', 'clients.id', '=' , 'stores.client_id')
            ->where('events.id', $id)
            ->where('stores.client_id', Auth::user()->client_id)
            // ->where('event_inventory.inventory_id', 1)
            ->groupBy('categories.id')
            ->get();

   

            //return $result;


            //return view('clients.reports.consolidation',compact('result', 'storeData'));

            PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif', 'defaultPaperSize' => 'a4']);
            $pdf = PDF::loadView('clients.reports.consolidation', 
            [
                'result' => $result,
                'event'=>$event,
                'current_price_total' => 0,
                'prior_price_total' => 0

            ]);
            return $pdf->stream('consolidation report.pdf');

    }


    public function downloadTimeSheetReport(Request $request, $id)
    {
        
        $event = Event::with("timesheet.emp_data.employee")->with('timesheet.vehicles.driverTo')
                            ->with('timesheet.vehicles.driverFrom')
                            ->with('store')->findOrFail($id);

        $total_hours = new \DateTime('00:00');
        $total_hours_copy = clone $total_hours;
        $d_emp_count_total = 0;
        $d_emp_pices_total = 0;
        //dd($event->timesheet);
        if($event->timesheet)
        {
            foreach($event->timesheet->emp_data as $timesheet)
            {
                $total_hours->add($timesheet->hour);
                $d_emp_count_total += $timesheet->dEmpCount;
                $d_emp_pices_total += $timesheet->dEmpPieces;

                $timesheet->hours = $timesheet->hour->format('%H:%I');

                try
                {
                    $timesheet->pices_per_hours = (int) ($timesheet->dEmpPieces / hourMinutesToDecimal($timesheet->hours));
                }
                catch(Throwable $e)
                {
                    $timesheet->pices_per_hours = 0;
                }

                try
                {
                    $timesheet->d_emp_per_hours = (int) ($timesheet->dEmpCount / hourMinutesToDecimal($timesheet->hours));
                }
                catch(Throwable $e)
                {
                    $timesheet->d_emp_per_hours = 0;
                }

                
            }
        }
        else abort(404);
        
        $event->total_hours = $total_hours_copy->diff($total_hours)->format("%H:%I");
        $event->d_emp_count_total = $d_emp_count_total;
        $event->d_emp_pices_total = $d_emp_pices_total;
        if($request->html)return view('clients.reports.timesheet',['event'=>$event]);
        PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif', 'defaultPaperSize' => 'a4']);
        $pdf = PDF::loadView('clients.reports.timesheet',['event'=>$event]);
        return $pdf->stream('consolidationreport.pdf');
        return $pdf->download('consolidationreport.pdf');

    }
}