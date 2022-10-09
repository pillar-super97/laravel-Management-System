<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Division;
use App\Models\Area;
use App\Models\Jsa;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Twilio\Rest\Client;

class NotificationsController extends Controller
{
//    protected $client;
//    public function __construct(Client $client)
//    {
//        $this->client = $client;
//    }
    /**
     * Display a listing of Notification.
     *
     * @return \Illuminate\Http\Response
     */
    private function sendMessage($message, $recipients)
{
    $account_sid = env("TWILIO_SID");
    $auth_token = env("TWILIO_AUTH_TOKEN");
    $twilio_number = env("TWILIO_NUMBER");
    $client = new Client($account_sid, $auth_token);
    $client->messages->create($recipients, 
            ['from' => $twilio_number, 'body' => $message] );
}
    public function unscheduled_stores(Request $request) {
        //$this->sendMessage('lorem ipsum', '+19199164300');die;
        $from = Carbon::today()->toDateString();
        $to = date('Y-m-d',strtotime("+13 days"));  // returns 2016-02-03
        $events = DB::table('event_areas')->whereBetween('events.date',[$from,$to])
                ->leftJoin('events','events.id','=','event_areas.event_id')
                ->leftJoin('stores','events.store_id','=','stores.id')
                ->select('events.id','event_areas.area_id','events.date','stores.name')->get();
        //echo env("TWILIO_SID");die;
        $messages = array();
        //echo '<pre>';print_r($events);die;
        foreach($events as $event)
        {
            $scheduled_employees = DB::table('event_schedule_employees')
                ->leftJoin('employees','employees.id','=','event_schedule_employees.employee_id')
                ->select('event_schedule_employees.id')->where('event_schedule_employees.event_id','=',$event->id)
                ->where('employees.area_id','=',$event->area_id)->get();
            if($scheduled_employees->isEmpty())
            {
                $area_manager = DB::table('employees')->select('employees.*')->where('area_id','=',$event->area_id)
                    ->where('title','=','Area Manager')->first();
                if($area_manager)
                {
                    //echo '<pre>';print_r($area_manager);print_r($event);
                    if($area_manager->work_phone)
                        $messages[$area_manager->id][$event->id]['phone']=$area_manager->work_phone;
                    elseif($area_manager->cell_phone)
                        $messages[$area_manager->id][$event->id]['phone']=$area_manager->cell_phone;
                    elseif($area_manager->home_phone)
                        $messages[$area_manager->id][$event->id]['phone']=$area_manager->home_phone;
                    $messages[$area_manager->id][$event->id]['date']=$event->date;
                    $messages[$area_manager->id][$event->id]['store']=$event->name;
                     //echo '<pre>';print_r($messages);die;
                }
            }
        }
        foreach($messages as $emp_id=>$message)
        {
            $msg = "There are events assigned to your area for the following dates"
                            . " that have not been scheduled: \r\n";
            $counter =1;
            foreach($message as $event_id=>$employee)
            {
                $mobile = $employee['phone'];
                $msg.=$counter.". ".$event_id."/".$employee['date']."/".$employee['store']." \r\n";
                $counter++;
            }
            //echo '<pre>';print_r($messages);die;
            $this->sendMessage($msg, $mobile);
            //$this->sendMessage($msg, '+19199164300');echo $msg;die;
        }
        //echo '<pre>';print_r($messages);19199164300
        
        
    }
}