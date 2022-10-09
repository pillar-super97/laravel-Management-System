<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\City;
use App\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCitiesRequest;
use App\Http\Requests\Admin\UpdateCitiesRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Redirect;

class CitiesController extends Controller
{
    /**
     * Display a listing of City.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (! Gate::allows('city_view')) {
            return abort(401);
        }

        $cities = City::with(array('state'));
       
        if (request('show_deleted') == 1) {
            if (! Gate::allows('city_delete')) {
                return abort(401);
            }
            $cities = $cities->onlyTrashed()->get();
        } else {
            $cities = $cities->get();
        }
        //print_r($cities);die;
        //$users = User::with(array('city','state','country','parentuser'))->where('user_type', '=', 'member')->get();
         
        return view('admin.cities.index', compact('cities'));
    }

    /**
     * Show the form for creating new City.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('city_create')) {
            return abort(401);
        }
        $states = DB::table('states')->pluck('name','id');
        
        return view('admin.cities.create', compact('states'));
    }

    /**
     * Store a newly created City in storage.
     *
     * @param  \App\Http\Requests\StoreCitiesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCitiesRequest $request)
    {
        if (! Gate::allows('city_create')) {
            return abort(401);
        }
        $dist_exist = City::where('name','=',$request->name)->where('state_id','=',$request->state_id)->first();
        if($dist_exist){
            return Redirect::back()->withErrors(['This city is already added.']);
        }else{
            $city = City::create($request->all());
            return redirect()->route('admin.cities.index')->with('successmsg', 'City added successfully.');
        }
        
    }


    /**
     * Show the form for editing City.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('city_edit')) {
            return abort(401);
        }
        
        $states = DB::table('states')->pluck('name','id');
       
        $city = City::with(array('state'))->findOrFail($id);
        
        return view('admin.cities.edit', compact('city','states'));
        
    }

    /**
     * Update City in storage.
     *
     * @param  \App\Http\Requests\UpdateCitiesRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCitiesRequest $request, $id)
    {
        if (! Gate::allows('city_edit')) {
            return abort(401);
        }
        $dist_exist = City::where('state_id','=',$request->state_id)->where('name','=',$request->name)->where('id','!=',$id)->first();
        if($dist_exist){
            return Redirect::back()->withErrors(['This City is already added.']);
        }else{
            $city = City::findOrFail($id);
            $city->update($request->all());
            return redirect()->route('admin.cities.index')->with('successmsg', 'City updated successfully.');
        }
    }


    /**
     * Display City.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        if (! Gate::allows('city_view')) {
            return abort(401);
        }
        $city = City::with(array('state'))->findOrFail($id);
        
        return view('admin.cities.show',compact('city'));
        
    }


    /**
     * Remove City from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('city_delete')) {
            return abort(401);
        }
        $city = City::findOrFail($id);
        $city->delete();

        return redirect()->route('admin.cities.index')->with('successmsg', 'City deleted successfully.');
    }
    
    /**
     * Delete all selected City at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('city_delete')) {
            return abort(401);
        }
        if ($request->input('ids')) {
            $entries = City::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }


    /**
     * Restore City from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('city_delete')) {
            return abort(401);
        }
        $city = City::onlyTrashed()->findOrFail($id);
        $city->restore();

        return redirect()->route('admin.cities.index')->with('successmsg', 'City set as active successfully.');
    }

    /**
     * Permanently delete City from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('city_delete')) {
            return abort(401);
        }
        $city = City::onlyTrashed()->findOrFail($id);
        $city->forceDelete();
        return redirect()->route('admin.cities.index')->with('successmsg', 'City deleted successfully.');
    }
    
}
