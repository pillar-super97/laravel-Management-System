<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Area;
use App\Models\AreaScheduleAvailabilityDays;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAreasRequest;
use App\Http\Requests\Admin\UpdateAreasRequest;
use App\Http\Controllers\Traits\FileUploadTrait;
use Illuminate\Support\Facades\DB;
use App\Models\Division;
use App\Models\Client;
use Illuminate\Support\Facades\Response;

class AreasController extends Controller
{
    use FileUploadTrait;

    /**
     * Display a listing of Area.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (! Gate::allows('area_view')) {
            return abort(401);
        }

        $areas = Area::with(array('city','state'));
       
        if (request('show_deleted') == 1) {
            if (! Gate::allows('area_delete')) {
                return abort(401);
            }
            $areas = $areas->onlyTrashed()->get();
        } else {
            $areas = $areas->get();
        }
        //print_r($areas);die;
        //$users = User::with(array('city','state','country','parentuser'))->where('user_type', '=', 'member')->get();
         
        return view('admin.areas.index', compact('areas'));
    }

    /**
     * Show the form for creating new Area.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('area_create')) {
            return abort(401);
        }
        $states = DB::table('states')->pluck('name','id');
        
        return view('admin.areas.create', compact('states'));
    }

    /**
     * Store a newly created Area in storage.
     *
     * @param  \App\Http\Requests\StoreAreasRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAreasRequest $request)
    {
        if (! Gate::allows('area_create')) {
            return abort(401);
        }
        
        $area = Area::create($request->all());
        //echo '<pre>';print_r($request->all());die;
        return redirect()->route('admin.areas.index')->with('successmsg', 'Area added successfully.');
    }


    /**
     * Show the form for editing Area.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('area_edit')) {
            return abort(401);
        }
        
        $states = DB::table('states')->pluck('name','id')->prepend('Please select', '');
        $area = Area::with(array('city','state'))->findOrFail($id);
        //$states = DB::table('states')->where('country_id','=',$area->country_id)->pluck('name','id')->prepend('Please select', '');
        $cities = DB::table('cities')->where('state_id','=',$area->state_id)->pluck('name','id')->prepend('Please select', '');
        //echo $area->state;die;
//        echo "<pre>";
//        print_r($area->schedule_availability_days);
//        die;  
//        if($request->ajax()){
//            return "AJAX";
//        }else{
            return view('admin.areas.edit', compact('area', 'states','cities'));
        //}
    }

    /**
     * Update Area in storage.
     *
     * @param  \App\Http\Requests\UpdateAreasRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAreasRequest $request, $id)
    {
        if (! Gate::allows('area_edit')) {
            return abort(401);
        }
//        echo "<pre>";
//        print_r($request->all());
//        die;
        $area = Area::findOrFail($id);
        $area->update($request->all());
        return redirect()->route('admin.areas.index')->with('successmsg', 'Area updated successfully.');
    }


    /**
     * Display Area.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('area_view')) {
            return abort(401);
        }
        $area = Area::with(array('city','state'))->findOrFail($id);
        return view('admin.areas.show',compact('area'));
    }


    /**
     * Remove Area from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('area_delete')) {
            return abort(401);
        }
        $area = Area::findOrFail($id);
        $area->delete();

        return redirect()->route('admin.areas.index')->with('successmsg', 'Area set as inactive successfully.');
    }

    /**
     * Delete all selected Area at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('area_delete')) {
            return abort(401);
        }
        if ($request->input('ids')) {
            $entries = Area::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }


    /**
     * Restore Area from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('area_delete')) {
            return abort(401);
        }
        $area = Area::onlyTrashed()->findOrFail($id);
        $area->restore();

        return redirect()->route('admin.areas.index')->with('successmsg', 'Area set as active successfully.');
    }

    /**
     * Permanently delete Area from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('area_delete')) {
            return abort(401);
        }
        $area = Area::onlyTrashed()->findOrFail($id);
        $area->forceDelete();

        return redirect()->route('admin.areas.index')->with('successmsg', 'Area deleted successfully.');
    }
}
