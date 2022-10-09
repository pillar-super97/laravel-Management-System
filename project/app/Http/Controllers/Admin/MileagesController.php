<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Mileage;
use App\Models\Area;
use App\Models\Jsa;
use App\Models\Store;
use App\Models\StoreApr;
use App\Models\StoreJsa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMileagesRequest;
use App\Http\Requests\Admin\UpdateMileagesRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Redirect;

class MileagesController extends Controller
{
    /**
     * Display a listing of Mileage.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (! Gate::allows('mileage_view')) {
            return abort(401);
        }

        $mileages = Mileage::with(array('area','jsa','store'));
       
        if (request('show_deleted') == 1) {
            if (! Gate::allows('mileage_delete')) {
                return abort(401);
            }
            $mileages = $mileages->onlyTrashed()->get();
        } else {
            $mileages = $mileages->get();
        }
        //print_r($mileages);die;
        //$users = User::with(array('city','state','country','parentuser'))->where('user_type', '=', 'member')->get();
         
        return view('admin.mileages.index', compact('mileages'));
    }

    /**
     * Show the form for creating new Mileage.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('mileage_create')) {
            return abort(401);
        }
        //$areas = DB::table('areas')->pluck('title','id');
        $stores = DB::table('stores')->pluck('name','id');
        
        return view('admin.mileages.create', compact('stores'));
    }

    /**
     * Store a newly created Mileage in storage.
     *
     * @param  \App\Http\Requests\StoreMileagesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreMileagesRequest $request)
    {
        if (! Gate::allows('mileage_create')) {
            return abort(401);
        }
        $dist_exist = Mileage::where('store_id','=',$request->store_id)->where('jsa_id','=',$request->jsa_id)->first();
        if($dist_exist){
            return Redirect::back()->withErrors(['Mileage from this store to JSA area is already defined.']);
        }else{
            $mileage = Mileage::create($request->all());
            return redirect()->route('admin.mileages.index')->with('successmsg', 'Mileage added successfully.');
        }
        
    }


    /**
     * Show the form for editing Mileage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('mileage_edit')) {
            return abort(401);
        }
        
        $stores = DB::table('stores')->pluck('name','id');
       
        $mileage = Mileage::with(array('area','jsa','store'))->findOrFail($id);
        $areas = DB::table('store_aprs')
            ->leftJoin('areas', 'areas.id', '=', 'store_aprs.area_id')
            ->where('store_aprs.store_id','=',$mileage->store_id)
            ->get();
        $jsas = DB::table('store_jsas')
                ->leftJoin('jsas', 'jsas.id', '=', 'store_jsas.jsa_id')
                ->where('store_jsas.store_id','=',$mileage->store_id)
                ->where('jsas.area_id','=',$mileage->area_id)
                ->get();
        return view('admin.mileages.edit', compact('mileage','stores','areas','jsas'));
        
    }

    /**
     * Update Mileage in storage.
     *
     * @param  \App\Http\Requests\UpdateMileagesRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateMileagesRequest $request, $id)
    {
        if (! Gate::allows('mileage_edit')) {
            return abort(401);
        }
        $dist_exist = Mileage::where('store_id','=',$request->store_id)->where('jsa_id','=',$request->jsa_id)->where('id','!=',$id)->first();
        if($dist_exist){
            return Redirect::back()->withErrors(['Mileage from this store to JSA area is already defined.']);
        }else{
            $mileage = Mileage::findOrFail($id);
            $mileage->update($request->all());
            return redirect()->route('admin.mileages.index')->with('successmsg', 'Mileage updated successfully.');
        }
    }


    /**
     * Display Mileage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        if (! Gate::allows('mileage_view')) {
            return abort(401);
        }
        $mileage = Mileage::with(array('area','jsa','store'))->findOrFail($id);
        
        return view('admin.mileages.show',compact('mileage'));
        
    }


    /**
     * Remove Mileage from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('mileage_delete')) {
            return abort(401);
        }
        $mileage = Mileage::findOrFail($id);
        $mileage->delete();

        return redirect()->route('admin.mileages.index')->with('successmsg', 'Mileage set as inactive successfully.');
    }
    
    /**
     * Delete all selected Mileage at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('mileage_delete')) {
            return abort(401);
        }
        if ($request->input('ids')) {
            $entries = Mileage::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }


    /**
     * Restore Mileage from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('mileage_delete')) {
            return abort(401);
        }
        $mileage = Mileage::onlyTrashed()->findOrFail($id);
        $mileage->restore();

        return redirect()->route('admin.mileages.index')->with('successmsg', 'Mileage set as active successfully.');
    }

    /**
     * Permanently delete Mileage from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('mileage_delete')) {
            return abort(401);
        }
        $mileage = Mileage::onlyTrashed()->findOrFail($id);
        $mileage->forceDelete();
        return redirect()->route('admin.mileages.index')->with('successmsg', 'Mileage deleted successfully.');
    }
    
    public function getAreaByStore(Request $request) {
        //return $request->store_id;
        //$areas = StoreApr::leftJoin('areas.id','=','store_aprs.area_id')->whereIn('store_id',$request->store_id)->get();
        $areas = DB::table('store_aprs')
            ->leftJoin('areas', 'areas.id', '=', 'store_aprs.area_id')
            ->where('store_aprs.store_id','=',$request->store_id)
            ->get();
        return Response::json(array('areas'=>$areas),200);
    }
    
    public function getJsaByArea(Request $request) {
        //DB::enableQueryLog(); 
        $jsas = DB::table('store_jsas')
                ->leftJoin('jsas', 'jsas.id', '=', 'store_jsas.jsa_id')
                ->where('store_jsas.store_id','=',$request->store_id)
                ->where('jsas.area_id','=',$request->area_id)
                ->get();
        //dd(DB::getQueryLog());
        return Response::json(array('jsas'=>$jsas),200);
    }
    
    public function calculateDistance(Request $request) {
       
        $store = Store::with(array('city','state'))->where('id','=',$request->store_id)->first();
        $jsa = Jsa::with(array('city','state'))->where('id','=',$request->jsa_id)->first();       
        //echo '<pre>';print_r($jsa);
        //$origin = urlencode($store->city->name.', '.$store->state->name.', '.$store->country->name); 
        $origin = urlencode($store->address.', '.$store->city->name.', '.$store->state->name.', United States'); 
        //$destination = urlencode($jsa->city->name.', '.$jsa->state->name.', '.$jsa->country->name);
        $destination = urlencode($jsa->address.', '.$jsa->city->name.', '.$jsa->state->name.', United States');
        $api = file_get_contents("https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$origin."&destinations=".$destination."&key=AIzaSyCtht1kYCSys9ifRKwhMcy2afLPSRt9iZ4&language=en-EN&sensor=false");
        $data = json_decode($api);
        //print_r($data);
        return Response::json(array('data'=>$data),200);
    }
}
