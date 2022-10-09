<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Division;
use App\Models\DivisionScheduleAvailabilityDays;
//use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDivisionsRequest;
use App\Http\Requests\Admin\UpdateDivisionsRequest;
use App\Http\Controllers\Traits\FileUploadTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class DivisionsController extends Controller
{
    use FileUploadTrait;

    /**
     * Display a listing of Division.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (! Gate::allows('division_view')) {
            return abort(401);
        }

//        $divisions = Division::with(array('city','state','client'=>function ($q){
//                        $q->orderBy('name');
//                    }))
//                //->select('divisions.*','client.name as client')
//                ->where('status','=', 'active')
//                ->orderBy('divisions.name','asc');
       
        $divisions = DB::table('divisions')
            ->join('clients', 'clients.id', '=', 'divisions.client_id')
            ->select('divisions.*', 'clients.name as clientname')
            
            ->orderBy('clients.name','asc')
            ->orderBy('divisions.name','asc');
        if (request('show_deleted') == 1) {
            if (! Gate::allows('division_delete')) {
                return abort(401);
            }
            $divisions = $divisions->where('divisions.deleted_at','!=',NULL)->get();
        } else {
            $divisions = $divisions->where('divisions.status','=', 'active')->where('divisions.deleted_at','=',NULL)->get();
        }
        //print_r($divisions);die;
        //$users = User::with(array('city','state','country','parentuser'))->where('user_type', '=', 'member')->get();
         
        return view('admin.divisions.index', compact('divisions'));
    }

    /**
     * Show the form for creating new Division.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('division_create')) {
            return abort(401);
        }
        $states = DB::table('states')->pluck('name','id');
        $clients = DB::table('clients')->pluck('name','id');
        //$employees = DB::table('employees')->pluck('name','id');
        
        return view('admin.divisions.create', compact('states','clients'));
    }

    /**
     * Store a newly created Division in storage.
     *
     * @param  \App\Http\Requests\StoreDivisionsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDivisionsRequest $request)
    {
        if (! Gate::allows('division_create')) {
            return abort(401);
        }
        
        $division = Division::create($request->all());
        //echo '<pre>';print_r($request->all());die;
        if($request->days_avai_to_schedule)
        {
            foreach($request->days_avai_to_schedule as $day)
            {
                DivisionScheduleAvailabilityDays::create([
                    'division_id' => $division->id,
                    'days' => $day,
                ]);
            }
        }
        //  echo "<pre>";
//        print_r($request->all());
//        die;
        return redirect()->route('admin.divisions.index')->with('successmsg', 'Division added successfully.');
    }


    /**
     * Show the form for editing Division.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('division_edit')) {
            return abort(401);
        }
        
        $states = DB::table('states')->pluck('name','id')->prepend('Please select', '');
        $clients = DB::table('clients')->pluck('name','id');
        //$employees = DB::table('employees')->pluck('name','id');
        //$division = Division::findOrFail($id);
        $division = Division::with(array('city','state','scheduling_city','scheduling_state','sec_scheduling_city','sec_scheduling_state',
            'billing_city','billing_state','schedule_availability_days'))->findOrFail($id);
        //echo $divisions->state->id;
        //echo $divisions->country_id;
        //$states = DB::table('states')->where('country_id','=',$division->country_id)->pluck('name','id')->prepend('Please select', '');
        $cities = DB::table('cities')->where('state_id','=',$division->state_id)->pluck('name','id')->prepend('Please select', '');
        $scheduling_states = DB::table('states')->pluck('name','id')->prepend('Please select', '');
        $scheduling_cities = DB::table('cities')->where('state_id','=',$division->scheduling_contact_state_id)->pluck('name','id')->prepend('Please select', '');
        $sec_scheduling_states = DB::table('states')->pluck('name','id')->prepend('Please select', '');
        $sec_scheduling_cities = DB::table('cities')->where('state_id','=',$division->sec_scheduling_contact_state_id)->pluck('name','id')->prepend('Please select', '');
        $billing_states = DB::table('states')->pluck('name','id')->prepend('Please select', '');
        $billing_cities = DB::table('cities')->where('state_id','=',$division->billing_contact_state_id)->pluck('name','id')->prepend('Please select', '');
        //echo $division->state;die;
//        echo "<pre>";
//        print_r($division->schedule_availability_days);
//        die;  
//        if($request->ajax()){
//            return "AJAX";
//        }else{
            return view('admin.divisions.edit', compact('division', 'states','states','cities','clients','scheduling_states','scheduling_cities',
                    'sec_scheduling_states','sec_scheduling_cities','billing_states','billing_cities'));
        //}
    }

    /**
     * Update Division in storage.
     *
     * @param  \App\Http\Requests\UpdateDivisionsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDivisionsRequest $request, $id)
    {
        if (! Gate::allows('division_edit')) {
            return abort(401);
        }
//        echo "<pre>";
//        print_r($request->all());
//        die;
        $division = Division::findOrFail($id);
        $division->update($request->all());
        DivisionScheduleAvailabilityDays::where('division_id', '=',$id)->delete();
        if(isset($request->days_avai_to_schedule) && count($request->days_avai_to_schedule))
        {
            foreach($request->days_avai_to_schedule as $day)
            {
                DivisionScheduleAvailabilityDays::create([
                    'division_id' => $division->id,
                    'days' => $day,
                ]);
            }
        }
        return redirect()->route('admin.divisions.index')->with('successmsg', 'Division updated successfully.');
    }


    /**
     * Display Division.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        if (! Gate::allows('division_view')) {
            return abort(401);
        }
        $division = Division::with(array('city','state','scheduling_city','scheduling_state','sec_scheduling_city','sec_scheduling_state',
            'billing_city','billing_state','schedule_availability_days','client'))->findOrFail($id);
        if($request->ajax()){
            $states = DB::table('states')->pluck('name','id');
            $cities = DB::table('cities')->where('state_id','=',$division->state_id)->pluck('name','id');
            $scheduling_states = DB::table('states')->pluck('name','id');
            $scheduling_cities = DB::table('cities')->where('state_id','=',$division->scheduling_contact_state_id)->pluck('name','id');
            $sec_scheduling_states = DB::table('states')->pluck('name','id');
            $sec_scheduling_cities = DB::table('cities')->where('state_id','=',$division->sec_scheduling_contact_state_id)->pluck('name','id');
            $billing_states = DB::table('states')->pluck('name','id');
            $billing_cities = DB::table('cities')->where('state_id','=',$division->billing_contact_state_id)->pluck('name','id');
            return Response::json(array('division'=>$division,'states'=>$states,'cities'=>$cities,'scheduling_states'=>$scheduling_states,
                'scheduling_cities'=>$scheduling_cities,'sec_scheduling_states'=>$sec_scheduling_states,'sec_scheduling_cities'=>$sec_scheduling_cities,
                'billing_states'=>$billing_states,'billing_cities'=>$billing_cities),200);
        }else{
            return view('admin.divisions.show',compact('division'));
        }
    }


    /**
     * Remove Division from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('division_delete')) {
            return abort(401);
        }
        $division = Division::findOrFail($id);
        $division->delete();

        return redirect()->route('admin.divisions.index')->with('successmsg', 'Division set as inactive successfully.');
    }

    /**
     * Delete all selected Division at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('division_delete')) {
            return abort(401);
        }
        if ($request->input('ids')) {
            $entries = Division::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }


    /**
     * Restore Division from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('division_delete')) {
            return abort(401);
        }
        $division = Division::onlyTrashed()->findOrFail($id);
        $division->restore();

        return redirect()->route('admin.divisions.index')->with('successmsg', 'Division set as active successfully.');
    }

    /**
     * Permanently delete Division from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('division_delete')) {
            return abort(401);
        }
        $division = Division::onlyTrashed()->findOrFail($id);
        $division->forceDelete();

        return redirect()->route('admin.divisions.index')->with('successmsg', 'Division deleted successfully.');
    }
}
