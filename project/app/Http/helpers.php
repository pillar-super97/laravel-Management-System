<?php
use App\Models\Jsa;
use App\Models\Store;
use Illuminate\Support\Facades\DB;
use App\Models\Area;
use App\Models\Timesheet;
use App\Models\TimesheetData;
use App\Models\TimesheetVehicle;
use Carbon\Carbon;


function lunchFlag($ssn_no,$idTimesheet_SQL,$lunch1,$lunch2)
{
    $counter=1;
    $lunch1flag=1;
    $lunch2flag=1;
           
    if($lunch2 && $lunch1)
    {
        $gap_reports1 = DB::table('gap_reports')
            ->where('idTimesheet_SQL','=',$idTimesheet_SQL)
            ->where('sEmployeeSSN','=',$ssn_no)
            ->where('GapMinutes','>=',$lunch1)    
            ->orderby('GapMinutes','ASC')
            ->first();
        //print_r($gap_reports1);
        $gap_reports2 = DB::table('gap_reports')
            ->where('idTimesheet_SQL','=',$idTimesheet_SQL)
            ->where('sEmployeeSSN','=',$ssn_no)
            ->where('GapMinutes','>=',$lunch2);
        if(count((array)$gap_reports1))
            $gap_reports2 = $gap_reports2->where('id','!=',$gap_reports1->id);

            $gap_reports2 = $gap_reports2->orderby('GapMinutes','ASC')
            ->first();

        if(count((array)$gap_reports1))
            $lunch1flag=0;
        if(count((array)$gap_reports2))
            $lunch2flag=0;
    }elseif($lunch1)
    {
        $gap_reports2 = DB::table('gap_reports')
            ->where('idTimesheet_SQL','=',$idTimesheet_SQL)
            ->where('sEmployeeSSN','=',$ssn_no)
            ->where('GapMinutes','>=',$lunch1)    
            ->orderby('GapMinutes','asc')
            ->first();
        if(count((array)$gap_reports2))
            $lunch1flag=0;
    }elseif($lunch2)
    {
        $gap_reports2 = DB::table('gap_reports')
            ->where('idTimesheet_SQL','=',$idTimesheet_SQL)
            ->where('sEmployeeSSN','=',$ssn_no)
            ->where('GapMinutes','>=',$lunch2)    
            ->orderby('GapMinutes','asc')
            ->first();
        if(count((array)$gap_reports2))
            $lunch2flag=0;
    }
        
    
//    if($ssn_no==255134998)
//    {
//        echo '<pre>';print_r(array($ssn_no,$idTimesheet_SQL,$lunch1,$lunch2,$lunch1flag,$lunch2flag));die;
//    }
    if($lunch1==0)$lunch1flag=0;if($lunch2==0)$lunch2flag=0;
    return ['lunch1'=>$lunch1flag,'lunch2'=>$lunch2flag];
}
function generateAccessToken(){
    $data = array(1,2,3,4,5,6,7,8,9,0,'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
    $res = '';
    for($i=0;$i<15;$i++)
    {
        $res .= $data[rand(0,35)];
    }
    return $res;
}

function originHaveEvent($store_id,$date)
{
    $data = DB::table('events')
            ->select('id')
            ->where('store_id','=',$store_id)
            ->where('date','=',$date)
            ->first();
    if($data)
        return $data;
    else
        return null;
}
function getLoggedToDriveTime($emp_id,$timesheet_id)
{
    $data = DB::table('timesheet_vehicle')
            ->select('dtToStoreStart','dtToStoreEnd')
            ->where('driver_to','=',$emp_id)
            ->where('timesheet_id','=',$timesheet_id)
            ->first();
    if($data)
        return $data;
    else
        return null;
}

function getLoggedFromDriveTime($emp_id,$timesheet_id)
{
    $data = DB::table('timesheet_vehicle')
            ->select('dtFromStoreStart','dtFromStoreEnd')
            ->where('driver_to','=',$emp_id)
            ->where('timesheet_id','=',$timesheet_id)
            ->first();
    if($data)
        return $data;
    else
        return null;
}
function convertTimeToColon($dec)
{
    $hour = floor($dec);
    $min = round(60*($dec - $hour));
    if(strlen($min)==1)
        $min='10';
        //$min='0'.$min;
    return $hour.':'.$min;
}
function historical_data($store_id)
{
    //DB::enableQueryLog();
    $historical_data = Timesheet::Where('store_id','=',$store_id)
                ->orderBy('dtJobDate','desc')->limit(1)->first();
    //dd(DB::getQueryLog());die;
    //print_r($historical_data);die;
    if($historical_data)
        return $historical_data;
    else
        return null;
}

function historical_data_by_event_date($store_id,$dtJobDate)
{
    //DB::enableQueryLog();
    $store = Store::Where('id','=',$store_id)->first();
    
    $historical_data = Timesheet::Where('store_id','=',$store_id)
                ->where('dtJobDate','<',$dtJobDate)
                ->orderBy('dtJobDate','desc')->first();
    
    $data=array();
    if($historical_data)
    {
        $data['comments'] = $historical_data->InvRecapComments;
        $crew_count = TimesheetData::Where('timesheet_id','=',$historical_data->id)->count();
        $data['crew_count'] = $crew_count;
        
        $datetime1 = strtotime($historical_data->InvRecapWrapTime);
        $datetime2 = strtotime($historical_data->InvRecapStartTime);
        $diff = abs($datetime1 - $datetime2);  
        $hours = floor(($diff/ (60*60)));
        $minutes = floor(($diff - $hours*60*60)/ 60);  
        $count_length = $diff/3600;
        $data['count_length'] = $hours.':'.$minutes;
        $emp_count_per_hour=0;
        if($store->pieces_or_dollars=="dollars" && (int)@$historical_data->TTLMH) 
            $emp_count_per_hour = (@$historical_data->dEmpCount/($historical_data->TTLMH ?? 1));
        elseif((int)@$historical_data->TTLMH)
            $emp_count_per_hour = (@$historical_data->dEmpPieces/($historical_data->TTLMH ?? 1));
        
        $data['production'] = round($emp_count_per_hour,2);                 
        $data['last_inventory_value'] = @$historical_data->dEmpCount;               
    }else{
        $data['comments'] = '';
        $data['crew_count'] = 0;
        $data['count_length'] = 0;
        $data['production'] = 0;
        $data['last_inventory_value'] = 0;
    }
    
    //dd(DB::getQueryLog());die;
    //echo '<pre>';print_r($historical_data);die;
    return $data;
}

function event_supervisor_by_event_id($event_id)
{
    $timesheet_data = Timesheet::Where('event_id','=',$event_id)->first();
    if(!$timesheet_data)
        return null;
    $data = DB::table('timesheet_data')
            ->select('employees.first_name','employees.last_name')
            ->leftJoin('employees','employees.id','=','timesheet_data.employee_id')
            ->where('timesheet_data.timesheet_id','=',$timesheet_data->id)
            ->where('timesheet_data.bIsSuper','=',1)
            ->get();
    //echo '<pre>';print_r($data);die;
    if($data)
    {
        $html='';
        foreach($data as $row)
            $html .= $row->first_name.' '.$row->last_name.'<br>';
        return $html;
    }else{
        return null;
    }
}

function event_supervisor($timesheet_id)
{
    $data = DB::table('timesheet_data')
            ->select('employees.first_name','employees.last_name','areas.area_number')
            ->leftJoin('employees','employees.id','=','timesheet_data.employee_id')
            ->leftJoin('areas','employees.area_id','=','areas.id')
            ->where('timesheet_id','=',$timesheet_id)
            ->where('bIsSuper','=',1)
            ->first();
    if($data)
        return $data;
    else
        return null;
}    
function district_store_count($district_id)
{
    $store_count = Store::Where('district_id','=',$district_id)->count();
    if($store_count)
        return $store_count;
    else
        return 0;
}

function association_store_count($association_id)
{
    $store_count = Store::Where('association_id','=',$association_id)->count();
    if($store_count)
        return $store_count;
    else
        return 0;
}

function division_store_count($division_id)
{
    $store_count = Store::Where('division_id','=',$division_id)->count();
    if($store_count)
        return $store_count;
    else
        return 0;
}

function client_store_count($client_id)
{
    $store_count = Store::Where('client_id','=',$client_id)->count();
    if($store_count)
        return $store_count;
    else
        return 0;
}

function arrays()
{
    $frequency = array('Weekly','Monthly','Bi Monthly','Quarterly','Tri Annually','Semi Annual','Annual','Once');
    $inv_type = array('Financial','Scan','Manual Hand Written');
    $billing = array('Corporate','Store');
    $rate_type = array('Dollar','Flat','Man Hour','Pieces','Records');
    $rate_per = array('1','10','100','1000');
    $store_types = array('APPAREL','COSMETIC','CSTORE','GROCERY','HARDWARE','LIQUOR','MAGIC','PHARMACY','SPECIALTY','WAREHOUSE');
    $days = array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');
    $months = array('January','February','March','April','May','June','July','August','September','October','November','December');
    return ['months'=>$months,'frequency'=>$frequency,'inv_type'=>$inv_type,'billing'=>$billing,'rate_type'=>$rate_type,'rate_per'=>$rate_per,'days'=>$days,'store_types'=>$store_types];
}

function store_name_from_id($store_id)
{
    $store = Store::Where('id','=',$store_id)->select('name')->first();
    if($store)
        return $store['name'];
    else
        return '';
}

function attendance_flag($flag)
{
    $flags = array('1'=>'','2'=>'Tardy','3'=>'No Show','4'=>'No Show - Called In','5'=>'Unauthorized Departure',
        '6'=>'Reprimand - Accuracy','7'=>'Reprimand - Behavior','8'=>'Reprimand - Cell Phone',
        '9'=>'Reprimand - Eating/Drinking','10'=>'Reprimand - Excessive Gaps','11'=>'Reprimand - Uniform',
        '12'=>'Reprimand - Wandering','13'=>'Dismissed - Accuracy','14'=>'Dismissed - Behavior','15'=>'Dismissed - Cell Phone',
        '16'=>'Dismissed - Eating/Drinking','17'=>'Dismissed - Excessive Gaps','18'=>'Dismissed - Excused',
        '19'=>'Dismissed - Health','20'=>'Dismissed - Uniform','21'=>'Dismissed - Wandering');
    return $flags[$flag];
}

function calDistance($origin,$origin_type,$destination,$destination_type)
{
    //return rand(50,500);
    if(strtolower($origin_type)=="office")
        $from = Jsa::with(array('city','state'))->where('id','=',$origin)->first();
    elseif(strtolower($origin_type)=="area")
        $from = Area::with(array('city','state'))->where('id','=',$origin)->first();
    else
        $from = Store::with(array('city','state'))->where('id','=',$origin)->first();
    
    if(strtolower($destination_type)=="office")
        $to = Jsa::with(array('city','state'))->where('id','=',$destination)->first();
    else
        $to = Store::with(array('city','state'))->where('id','=',$destination)->first();
    //echo @$from->address.', '.@$from->city->name.', '.@$from->state->name.', United States';echo '<br>';
    //echo @$to->address.', '.@$to->city->name.', '.@$to->state->name.', United States';echo '<br>';
    $origin1 = urlencode(@$from->address.', '.@$from->city->name.', '.@$from->state->name.', United States'); 
    $destination1 = urlencode(@$to->address.', '.@$to->city->name.', '.@$to->state->name.', United States');
    //echo "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$origin1."&destinations=".$destination1."&key=AIzaSyCtht1kYCSys9ifRKwhMcy2afLPSRt9iZ4&language=en-EN&sensor=false";
    $api = file_get_contents("https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$origin1."&destinations=".$destination1."&key=AIzaSyCtht1kYCSys9ifRKwhMcy2afLPSRt9iZ4&language=en-EN&sensor=false");
    return $data = json_decode($api);
}

function getTimezone($location)
{
        $location = urlencode($location);
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$location}&key=AIzaSyCtht1kYCSys9ifRKwhMcy2afLPSRt9iZ4&sensor=false";
        $data = file_get_contents($url);
        //print_r($data);
        // Get the lat/lng out of the data
        $data = json_decode($data);
        if(!$data) return false;
        if(!is_array($data->results)) return false;
        if(!isset($data->results[0])) return false;
        if(!is_object($data->results[0])) return false;
        if(!is_object($data->results[0]->geometry)) return false;
        if(!is_object($data->results[0]->geometry->location)) return false;
        if(!is_numeric($data->results[0]->geometry->location->lat)) return false;
        if(!is_numeric($data->results[0]->geometry->location->lng)) return false;
        $lat = $data->results[0]->geometry->location->lat;
        $lng = $data->results[0]->geometry->location->lng;

        // get the API response for the timezone
        $timestamp = time();
        $timezoneAPI = "https://maps.googleapis.com/maps/api/timezone/json?location={$lat},{$lng}&key=AIzaSyCtht1kYCSys9ifRKwhMcy2afLPSRt9iZ4&sensor=false&timestamp={$timestamp}";
        $response = file_get_contents($timezoneAPI);
        if(!$response) return false;
        $response = json_decode($response);
        //echo '<pre>';;print_r($response);die;
        if(!$response) return false;
        if(!is_object($response)) return false;
        if(!is_string($response->timeZoneId)) return false;

        return $response->timeZoneId;
}

function conver_to_time($time="",$toTz='',$fromTz='')
{
    $date = new DateTime($time, new DateTimeZone($fromTz));
    $date->setTimezone(new DateTimeZone($toTz));
    $time= $date->format('m/d/Y h:i:s A');
    return $time;
}
function converToTz($time="",$toTz='',$fromTz='')
{	
//    echo $time.'---';
//    echo $fromTz.'----';
//    echo $toTz.'----';
//    $k = Carbon::createFromFormat('H:i A', $time, $fromTz)->setTimezone($toTz)->format('H:i A');
//    print_r($k);die;
//    $timezone = new DateTimeZone($fromTz);
//$strdate  = $time;
//$date     = DateTime::createFromFormat('H:i A', $strdate, $timezone);print_r($date);die;
//    echo $time.'time---';
//    echo $toTz.'to-----';
//    echo $fromTz.'from';die;
    $date = new DateTime($time, new DateTimeZone($fromTz));
    $date->setTimezone(new DateTimeZone($toTz));
    $time= $date->format('h:i A');
    return $time;
}
function getHolidayList()
{
    return $holiday=array('2020-12-25'=>'Christmas Day','2021-01-01'=>'New Year\'s Day',
            '2021-02-07'=>'Superbowl','2021-02-14'=>'Valentine\'s Day','2021-02-16'=>'Fat Tuesday',
            '2021-03-14'=>'Daylight Savings Starts','2021-04-04'=>'Easter','2021-05-31'=>'Memorial Day',
            '2021-07-04'=>'Independence Day','2021-09-06'=>'Labor Day',
            '2021-10-31'=>'Halloween','2021-11-07'=>'Daylight Savings Ends','2021-11-25'=>'Thanksgiving Day',
            '2021-12-25'=>'Christmas Day','2022-01-01'=>'New Year\'s Day',
            '2022-02-06'=>'Superbowl','2022-02-14'=>'Valentine\'s Day','2022-03-01'=>'Fat Tuesday',
            '2022-03-13'=>'Daylight Savings Starts','2022-04-17'=>'Easter','2022-05-30'=>'Memorial Day',
            '2022-07-04'=>'Independence Day','2022-09-05'=>'Labor Day',
            '2022-10-31'=>'Halloween','2022-11-06'=>'Daylight Savings Ends','2022-11-24'=>'Thanksgiving Day',
            '2022-12-25'=>'Christmas Day','2023-01-01'=>'New Year\'s Day');
}

// @param $hour_minutes String "hh:mm"
// return decimal number ex: input "1.30" return 1.5
function hourMinutesToDecimal($hour_minutes)
{
    $hour_minutes = explode(":", $hour_minutes);
    return ($hour_minutes[0] + ($hour_minutes[1]/60));
}
?>
