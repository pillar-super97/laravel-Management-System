<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\Request;
use App\Models\Area;
use App\Models\Jsa;
use App\Models\Store;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;

trait MsiTrait
{

    function calculateDistance($origin,$origin_type,$destination,$destination_type) {
        //return 100;
        if(strtolower($origin_type)=="office")
            $from = Jsa::with(array('city','state'))->where('id','=',$origin)->first();
        else
            $from = Store::with(array('city','state'))->where('id','=',$origin)->first();

        if(strtolower($destination_type)=="office")
            $to = Jsa::with(array('city','state'))->where('id','=',$destination)->first();
        else
            $to = Store::with(array('city','state'))->where('id','=',$destination)->first();

        $origin1 = urlencode(@$from->address.', '.@$from->city->name.', '.@$from->state->name.', United States'); 
        $destination1 = urlencode(@$to->address.', '.@$to->city->name.', '.@$to->state->name.', United States');
        $api = file_get_contents("https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$origin1."&destinations=".$destination1."&key=AIzaSyCtht1kYCSys9ifRKwhMcy2afLPSRt9iZ4&language=en-EN&sensor=false");
        $data = json_decode($api);
        return $data->rows[0];
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
            if(!$response) return false;
            if(!is_object($response)) return false;
            if(!is_string($response->timeZoneId)) return false;

            return $response->timeZoneId;
    }
    
    function converToTz($time="",$toTz='',$fromTz='')
    {	
        $date = new DateTime($time, new DateTimeZone($fromTz));
        $date->setTimezone(new DateTimeZone($toTz));
        $time= $date->format('h:i A');
        return $time;
    }
}