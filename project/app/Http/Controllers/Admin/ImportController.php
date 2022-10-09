<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ImportController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('admin.import')->with('successmsg', 'File uploaded successfully.');
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        $sly = new \App\Services\Sly();

        if( $sly->storeJsonData()) return back()->with('successmsg', 'Data imported successfully.');
        return back()->with('errormsg', 'Something went wrong!!');
    }
}
