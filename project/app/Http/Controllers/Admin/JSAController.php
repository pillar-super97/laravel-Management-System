<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Jsa;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreJSARequest;
use App\Http\Requests\Admin\UpdateJSARequest;
use App\Http\Controllers\Traits\FileUploadTrait;
use Illuminate\Support\Facades\DB;
use App\Models\Division;
use App\Models\Client;
use Illuminate\Support\Facades\Response;

class JSAController extends Controller
{
    use FileUploadTrait;

    /**
     * Display a listing of Jsa.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,$area_id)
    {
        if (! Gate::allows('area_view')) {
            return abort(401);
        }

        $jsa = Jsa::with(array('city','state'))->where('area_id','=', $area_id)->where('status','=', 'active');
       
        if (request('show_deleted') == 1) {
            if (! Gate::allows('area_delete')) {
                return abort(401);
            }
            $jsa = $jsa->onlyTrashed()->get();
        } else {
            $jsa = $jsa->get();
        }
        //print_r($jsa);die;
        //$users = User::with(array('city','state','country','parentuser'))->where('user_type', '=', 'member')->get();
         
        return view('admin.jsa.index', compact('jsa','area_id'));
    }

    /**
     * Show the form for creating new Jsa.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($area_id)
    {
        if (! Gate::allows('area_create')) {
            return abort(401);
        }
        $states = DB::table('states')->pluck('name','id');
        
        return view('admin.jsa.create', compact('states','area_id'));
    }

    /**
     * Store a newly created Jsa in storage.
     *
     * @param  \App\Http\Requests\StoreJSAJsasRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreJSARequest $request,$area_id)
    {
        if (! Gate::allows('area_create')) {
            return abort(401);
        }
        $request->request->add(['area_id' => $area_id]); //add request
        //echo '<pre>';print_r($request->all());die;
        $jsa = Jsa::create($request->all());
        return redirect()->route('admin.areas.jsa.index',[$area_id])->with('successmsg', 'JSA added successfully.');
    }


    /**
     * Show the form for editing Jsa.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($area_id,$id)
    {
        if (! Gate::allows('area_edit')) {
            return abort(401);
        }
        
        $states = DB::table('states')->pluck('name','id')->prepend('Please select', '');
        $jsa = Jsa::with(array('city','state'))->findOrFail($id);
        //$states = DB::table('states')->where('country_id','=',$jsa->country_id)->pluck('name','id')->prepend('Please select', '');
        $cities = DB::table('cities')->where('state_id','=',$jsa->state_id)->pluck('name','id')->prepend('Please select', '');
        //echo $jsa->state;die;
//        echo "<pre>";
//        print_r($jsa->schedule_availability_days);
//        die;  
//        if($request->ajax()){
//            return "AJAX";
//        }else{
            return view('admin.jsa.edit', compact('jsa', 'states','states','cities','area_id'));
        //}
    }

    /**
     * Update Jsa in storage.
     *
     * @param  \App\Http\Requests\UpdateJSAJsasRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateJSARequest $request,$area_id, $id)
    {
        if (! Gate::allows('area_edit')) {
            return abort(401);
        }
//        echo "<pre>";
//        print_r($request->all());
//        die;
        $jsa = Jsa::findOrFail($id);
        $jsa->update($request->all());
        return redirect()->route('admin.areas.jsa.index',[$area_id])->with('successmsg', 'JSA updated successfully.');
    }


    /**
     * Display Jsa.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($area_id,$id)
    {
        if (! Gate::allows('area_view')) {
            return abort(401);
        }
        $jsa = Jsa::with(array('city','state'))->findOrFail($id);
        return view('admin.jsa.show',compact('jsa','area_id'));
    }


    /**
     * Remove Jsa from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($area_id,$id)
    {
        if (! Gate::allows('area_delete')) {
            return abort(401);
        }
        $jsa = Jsa::findOrFail($id);
        $jsa->delete();

        return redirect()->route('admin.areas.jsa.index',[$area_id])->with('successmsg', 'JSA set as inactive successfully.');
    }
    /**
     * Delete all selected JSA at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('area_delete')) {
            return abort(401);
        }
        if ($request->input('ids')) {
            $entries = Jsa::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }


    /**
     * Restore JSA from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($area_id,$id)
    {
        if (! Gate::allows('area_delete')) {
            return abort(401);
        }
        $jsa = Jsa::onlyTrashed()->findOrFail($id);
        $jsa->restore();

        return redirect()->route('admin.areas.jsa.index',[$area_id])->with('successmsg', 'JSA set as active successfully.');
    }

    /**
     * Permanently delete JSA from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($area_id,$id)
    {
        if (! Gate::allows('area_delete')) {
            return abort(401);
        }
        $jsa = Jsa::onlyTrashed()->findOrFail($id);
        $jsa->forceDelete();
        return redirect()->route('admin.areas.jsa.index',[$area_id])->with('successmsg', 'JSA deleted successfully.');
    }
}
