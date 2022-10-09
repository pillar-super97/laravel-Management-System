<?php

namespace App\Http\Controllers\Admin;

use App\Models\Association;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAssociationsRequest;
use App\Http\Requests\Admin\UpdateAssociationsRequest;
use App\Http\Controllers\Traits\FileUploadTrait;
use Illuminate\Support\Facades\DB;
use App\Models\Store;

class AssociationsController extends Controller
{
    use FileUploadTrait;

    /**
     * Display a listing of Association.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (! Gate::allows('association_view')) {
            return abort(401);
        }

        $associations = Association::with(array('city','state'))->orderBy('name');

        
       
        if (request('show_deleted') == 1) {
            if (! Gate::allows('association_delete')) {
                return abort(401);
            }
            $associations = $associations->onlyTrashed()->get();
        } else {
            $associations = $associations->where('status','=', 'active')->get();
        }

        // dd($associations);
        
        //$users = User::with(array('city','state','country','parentuser'))->where('user_type', '=', 'member')->get();
         
        return view('admin.associations.index', compact('associations'));
    }

    /**
     * Show the form for creating new Association.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('association_create')) {
            return abort(401);
        }
        $states = DB::table('states')->pluck('name','id');

        return view('admin.associations.create', compact('states'));
    }

    /**
     * Store a newly created Association in storage.
     *
     * @param  \App\Http\Requests\StoreAssociationsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAssociationsRequest $request)
    {
        if (! Gate::allows('association_create')) {
            return abort(401);
        }
        
//        echo "<pre>";
//        print_r($request->all());
//        die;        
        //$request = $this->saveFiles($request);
        $association = Association::create($request->all());
        return redirect()->route('admin.associations.index')->with('successmsg', 'Association addedd successfully. ');
    }


    /**
     * Show the form for editing Association.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('association_edit')) {
            return abort(401);
        }
        
        $states = DB::table('states')->pluck('name','id')->prepend('Please select', '');
        
        //$association = Association::findOrFail($id);
        $association = Association::with(array('city','state'))->findOrFail($id);
        //echo $associations->state->id;
        //echo $associations->country_id;
        //$states = DB::table('states')->where('country_id','=',$association->country_id)->pluck('name','id')->prepend('Please select', '');
        $cities = DB::table('cities')->where('state_id','=',$association->state_id)->pluck('name','id')->prepend('Please select', '');
        //echo $associations->state;die;
//        echo "<pre>";
//        print_r($states);print_r($cities);
//        die;  
        return view('admin.associations.edit', compact('association', 'states','cities'));
    }

    /**
     * Update Association in storage.
     *
     * @param  \App\Http\Requests\UpdateAssociationsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreAssociationsRequest $request, $id)
    {
        if (! Gate::allows('association_edit')) {
            return abort(401);
        }
        $association = Association::findOrFail($id);
        $association->update($request->all());
        return redirect()->route('admin.associations.index')->with('successmsg', 'Association updated successfully.');
    }


    /**
     * Display Association.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('association_view')) {
            return abort(401);
        }
        $store_arr = array();
        $stores = Store::With('city','state','schedule_availability_days')->Where('association_id','=',$id)->get();
        foreach($stores as $store)
        {
            $historical_data = historical_data($store->id);
            // dd($historical_data);
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
            $store_arr[] = $store->name.', '.$store->city->name.', '.
                $store->state->state_code.'<br>'.$last_count_value.'<br>'.
                @$historical_data->dEmpPieces.'<br>'.
                implode(', ',$schedule_availability_days);
        }
        $association = Association::with(array('city','state'))->findOrFail($id);
        return view('admin.associations.show',compact('association','store_arr')); 
    }


    /**
     * Remove Association from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('association_delete')) {
            return abort(401);
        }
        $association = Association::findOrFail($id);
        $association->delete();

        return redirect()->route('admin.associations.index')->with('successmsg', 'Association set as inactive successfully. ');
    }

    /**
     * Delete all selected Association at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('association_delete')) {
            return abort(401);
        }
        if ($request->input('ids')) {
            $entries = Association::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }


    /**
     * Restore Association from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('association_delete')) {
            return abort(401);
        }
        $association = Association::onlyTrashed()->findOrFail($id);
        $association->restore();

        return redirect()->route('admin.associations.index')->with('successmsg', 'Association set as active successfully. ');
    }

    /**
     * Permanently delete Association from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('association_delete')) {
            return abort(401);
        }
        $association = Association::onlyTrashed()->findOrFail($id);
        $association->forceDelete();

        return redirect()->route('admin.associations.index')->with('successmsg', 'Association deleted successfully. ');
    }
}
