<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

class LogController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, $channel, $action=null)
    {
        

        $log_file = storage_path('logs/'.$channel.'.log');
        $content = '';
        if(File::exists( $log_file) && $request->clear) 
        {
            File::delete($log_file);
            return redirect()->route('admin.log', [$channel]);
        }

        if(File::exists( $log_file)) $content = File::get($log_file);
        return view('admin.logs.index',
                    [
                        'content' => $content,
                        'title' => $channel.' Log',

                    ]) ;
    }
}
