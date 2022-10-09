<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\District;
use App\Models\DistrictScheduleAvailabilityDays;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDistrictsRequest;
use App\Http\Requests\Admin\UpdateDistrictsRequest;
use App\Http\Controllers\Traits\FileUploadTrait;
use Illuminate\Support\Facades\DB;
use App\Models\Division;
use App\Models\Client;
use Illuminate\Support\Facades\Response;
use App\Models\Store;


class DistrictsController extends Controller
{
    use FileUploadTrait;

    /**
     * Display a listing of District.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (! Gate::allows('district_view')) {
            return abort(401);
        }
        //DB::enableQueryLog();
        $districts = DB::table('districts')
                    ->select('districts.*','clients.name as clientname','divisions.name as divisionname')
                    ->leftJoin('cities','cities.id','=','districts.city_id')
                    ->leftJoin('states','states.id','=','districts.state_id')
                    ->leftJoin('divisions','divisions.id','=','districts.division_id')
                    ->leftJoin('clients','clients.id','=','districts.client_id')
                    ->orderBy('clients.name','asc')
                    ->orderBy('divisions.name','asc')
                    ->orderBy('districts.number','asc');
        
        
//        $districts = District::with(array('city','state','client'=> function($query) {
//            $query->orderBy('name');
//        }))->where('status','=', 'active');
       
        if (request('show_deleted') == 1) {
            if (! Gate::allows('district_delete')) {
                return abort(401);
            }
            $districts = $districts->where('districts.deleted_at','!=',NULL)->get();
        } else {
            $districts = $districts->where('districts.status','=',"active")->where('districts.deleted_at','=',NULL)->get();
        }
//        echo '<pre>';
//        foreach($districts as $district)
//            print_r($district);
//        dd(DB::getQueryLog());die;
//        die;
        //$users = User::with(array('city','state','country','parentuser'))->where('user_type', '=', 'member')->get();
         
        return view('admin.districts.index', compact('districts'));
    }

    /**
     * Show the form for creating new District.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('district_create')) {
            return abort(401);
        }
        $states = DB::table('states')->pluck('name','id');
        $clients = DB::table('clients')->pluck('name','id');
        //$divisions = DB::table('divisions')->pluck('name','id');
        //$employees = DB::table('employees')->pluck('name','id');
        
        return view('admin.districts.create', compact('states','clients'));
    }

    /**
     * Store a newly created District in storage.
     *
     * @param  \App\Http\Requests\StoreDistrictsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDistrictsRequest $request)
    {
        if (! Gate::allows('district_create')) {
            return abort(401);
        }
        
        $district = District::create($request->all());
        //echo '<pre>';print_r($request->all());die;
        if($request->days_avai_to_schedule)
        {
            foreach($request->days_avai_to_schedule as $day)
            {
                DistrictScheduleAvailabilityDays::create([
                    'district_id' => $district->id,
                    'days' => $day,
                ]);
            }
        }
        //  echo "<pre>";
//        print_r($request->all());
//        die;
        return redirect()->route('admin.districts.index')->with('successmsg', 'District added successfully.');
    }


    /**
     * Show the form for editing District.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('district_edit')) {
            return abort(401);
        }
        
        $states = DB::table('states')->pluck('name','id')->prepend('Please select', '');
        $clients = DB::table('clients')->pluck('name','id');
        //$employees = DB::table('employees')->pluck('name','id');
        //$district = District::findOrFail($id);
        $district = District::with(array('city','state','scheduling_city','scheduling_state','sec_scheduling_city','sec_scheduling_state',
            'billing_city','billing_state','schedule_availability_days'))->findOrFail($id);
        //echo $districts->state->id;
        //echo $districts->country_id;
        $divisions = DB::table('divisions')->where('client_id','=',$district->client_id)->pluck('name','id');
        //$states = DB::table('states')->where('country_id','=',$district->country_id)->pluck('name','id')->prepend('Please select', '');
        $cities = DB::table('cities')->where('state_id','=',$district->state_id)->pluck('name','id')->prepend('Please select', '');
        $scheduling_states = DB::table('states')->pluck('name','id')->prepend('Please select', '');
        $scheduling_cities = DB::table('cities')->where('state_id','=',$district->scheduling_contact_state_id)->pluck('name','id')->prepend('Please select', '');
        $sec_scheduling_states = DB::table('states')->pluck('name','id')->prepend('Please select', '');
        $sec_scheduling_cities = DB::table('cities')->where('state_id','=',$district->sec_scheduling_contact_state_id)->pluck('name','id')->prepend('Please select', '');
        $billing_states = DB::table('states')->pluck('name','id')->prepend('Please select', '');
        $billing_cities = DB::table('cities')->where('state_id','=',$district->billing_contact_state_id)->pluck('name','id')->prepend('Please select', '');
        //echo $district->state;die;
//        echo "<pre>";
//        print_r($district->schedule_availability_days);
//        die;  
//        if($request->ajax()){
//            return "AJAX";
//        }else{
            return view('admin.districts.edit', compact('district', 'states','states','cities','clients','scheduling_states','scheduling_cities',
                    'sec_scheduling_states','sec_scheduling_cities','billing_states','billing_cities','divisions'));
        //}
    }

    /**
     * Update District in storage.
     *
     * @param  \App\Http\Requests\UpdateDistrictsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDistrictsRequest $request, $id)
    {
        if (! Gate::allows('district_edit')) {
            return abort(401);
        }
//        echo "<pre>";
//        print_r($request->all());
//        die;
        $district = District::findOrFail($id);
        $district->update($request->all());
        DistrictScheduleAvailabilityDays::where('district_id', '=',$id)->delete();
        if($request->days_avai_to_schedule)
        {
        foreach($request->days_avai_to_schedule as $day)
        {
            DistrictScheduleAvailabilityDays::create([
                'district_id' => $district->id,
                'days' => $day,
            ]);
        }
        }
        return redirect()->route('admin.districts.index')->with('successmsg', 'District updated successfully.');
    }


    /**
     * Display District.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        if (! Gate::allows('district_view')) {
            return abort(401);
        }
        $district = District::with(array('city','state','scheduling_city','scheduling_state','sec_scheduling_city','sec_scheduling_state',
            'billing_city','billing_state'))->findOrFail($id);
        if($request->ajax()){
            $states = DB::table('states')->pluck('name','id');
            $cities = DB::table('cities')->where('state_id','=',$district->state_id)->pluck('name','id');
            $scheduling_states = DB::table('states')->pluck('name','id');
            $scheduling_cities = DB::table('cities')->where('state_id','=',$district->scheduling_contact_state_id)->pluck('name','id');
            $sec_scheduling_states = DB::table('states')->pluck('name','id');
            $sec_scheduling_cities = DB::table('cities')->where('state_id','=',$district->sec_scheduling_contact_state_id)->pluck('name','id');
            $billing_states = DB::table('states')->pluck('name','id');
            $billing_cities = DB::table('cities')->where('state_id','=',$district->billing_contact_state_id)->pluck('name','id');
            return Response::json(array('district'=>$district,'states'=>$states,'cities'=>$cities,'scheduling_states'=>$scheduling_states,
                'scheduling_cities'=>$scheduling_cities,'sec_scheduling_states'=>$sec_scheduling_states,'sec_scheduling_cities'=>$sec_scheduling_cities,
                'billing_states'=>$billing_states,'billing_cities'=>$billing_cities),200);
        }else{
            $store_arr = array();
            $stores = Store::With('city','state','schedule_availability_days')->Where('district_id','=',$id)->get();
            foreach($stores as $store)
            {
                $historical_data = historical_data($store->id);
                if($historical_data && $historical_data->dEmpCount>1000)
                {
                    $last_count_value = '$'.round($historical_data->dEmpCount,-3)/1000;
                }elseif($historical_data)
                {
                    $last_count_value = '$'.$historical_data->dEmpCount;
                }else{
                    $last_count_value = '$0';
                }
                $schedule_availability_days = array();
                if(count($store->schedule_availability_days)){
                    foreach($store->schedule_availability_days as $day)
                        $schedule_availability_days[] = $day->days;
                }
                $store_arr[] = $store->name.', '.$store->city->name.', '.$store->state->state_code.'<br>'.$last_count_value.'<br>'.implode(', ',$schedule_availability_days);
            }
            
            //echo '<pre>';print_r($store_arr);die;
            return view('admin.districts.show',compact('district','store_arr'));
        }
    }


    /**
     * Remove District from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('district_delete')) {
            return abort(401);
        }
        $district = District::findOrFail($id);
        $district->delete();

        return redirect()->route('admin.districts.index')->with('successmsg', 'District set as inactive successfully.');
    }

    /**
     * Delete all selected District at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('district_delete')) {
            return abort(401);
        }
        if ($request->input('ids')) {
            $entries = District::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }


    /**
     * Restore District from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('district_delete')) {
            return abort(401);
        }
        $district = District::onlyTrashed()->findOrFail($id);
        $district->restore();

        return redirect()->route('admin.districts.index')->with('successmsg', 'District set as active successfully.');
    }

    /**
     * Permanently delete District from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('district_delete')) {
            return abort(401);
        }
        $district = District::onlyTrashed()->findOrFail($id);
        $district->forceDelete();

        return redirect()->route('admin.districts.index')->with('successmsg', 'District deleted successfully.');
    }
    
    
    
    public function getDivisionByClient(Request $request) {
        $divisions = Division::where('client_id','=',$request->client_id)->where('status','=', 'active')->get();
        return Response::json(array('divisions'=>$divisions),200);
    }
}
