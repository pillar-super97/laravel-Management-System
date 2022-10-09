<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AjaxController extends Controller
{

    public function getStateList(Request $request)
    {
        $states = DB::table("states")
        ->where("country_id",$request->country_id)
        ->pluck("name","id");
        return response()->json($states);
    }

    public function getCityList(Request $request)
    {
        $cities = DB::table("cities")
        ->where("state_id",$request->state_id)
        ->pluck("name","id");
        return response()->json($cities);
    }

    

}
