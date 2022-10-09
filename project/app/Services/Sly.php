<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use \App\Models\Category;
use \App\Models\EventCategory;
use \App\Models\Employee;
use \App\Models\EventEmployee;
use \App\Models\Event;
use \App\Models\Location;
use \App\Models\SubLocation;
use \App\Models\EventInventory;
use \App\Models\EventInventoryData;
use \App\Models\LocationTag;
use \App\Models\SubLocationTag;

class Sly
{ 
    private $base_url; 
    private $client;
    private $token;

    public function __construct()
    {
        $this->base_url = config('services.sly.endpoint_url');

        $auth = array(
            "username"   => config('services.sly.username'),
            "pwd"=> config('services.sly.password')
        );

        $this->client = new \GuzzleHttp\Client(['base_uri' => $this->base_url, 'verify' =>false]);

        try
        {

            $request = $this->client->post( 'Account', ['form_params'=>$auth]);
            $this->token = json_decode($request->getBody(), true);

        }
        catch(\GuzzleHttp\Exception\BadResponseException $ex)
        {
            //return false;
        }

    }

    public function getTimeSheet($import_upto)
    {
        $latest_time_entries_log = DB::table('time_entries_import_log')->orderBy('id', 'DESC')->first();
        

        try
        {

            $request = $this->client->post( 'TimeSheetData', ['form_params'=>[
                "ALADeliveryDate"   => "'".$import_upto."'",
                "Authorization"=>$this->token
            ]]);

            return json_decode($request->getBody(), true);

        }
        catch(\GuzzleHttp\Exception\BadResponseException $ex)
        {
            return false;
        }
        
        
    }


    public function importJSON()
    {

        try
        {

            $request = $this->client->post( 'event', ['form_params'=>["Authorization"=>$this->token]]);

            $response = json_decode($request->getBody());

            if($response->status == 'success') return $response->data;
            return false;

        }
        catch(\GuzzleHttp\Exception\BadResponseException $ex)
        {
            return false;
        }

    }


    public function storeJsonData()
    {
        
        $response = $this->importJSON();
        if($response) 
        {
            if(Event::find($response->event_id))
            {
                // importing categories
                \Log::channel('json_import')->info('importing categories for event id: '.$response->event_id );
                if($response->categories && $response->categories->data )
                {
                    foreach($response->categories->data as $category)
                    {
                        $_category = Category::firstOrCreate(['name' => $category->name]);

                        EventCategory::updateOrCreate(
                            ['event_id' => $response->event_id, 'category_id' => $_category->id],
                            ['tags' => $category->tag]
                        );
                    }
                    
                }


                // importing gap_query
                \Log::channel('json_import')->info('importing gap_query for event id: '.$response->event_id );
                if($response->gap_query && $response->gap_query->data )
                {
                    foreach($response->gap_query->data as $gap_query)
                    {
                        $employee = Employee::where('emp_number', $gap_query->employee_number)->first();

                        if($employee) EventEmployee::updateOrCreate(
                            ['event_id' => $response->event_id, 'employee_id' => $employee->id],
                            [
                                'start_time' =>  \Carbon\Carbon::parse($gap_query->start_time)->format('Y-m-d H:i:s'),
                                'end_time' =>  \Carbon\Carbon::parse($gap_query->end_time)->format('Y-m-d H:i:s'),
                                'total_gap_hours' => $gap_query->total_gap_hours,
                                'count' => $gap_query->count,
                                'pieces' => $gap_query->pieces,
                                'records' => $gap_query->records,
                                'is_rx' => $gap_query->is_rx
                            ]
                        );
                    }
                    
                }

                //summary_query
                \Log::channel('json_import')->info('importing summary_query for event id: '.$response->event_id );
                if($response->summary_query && $response->summary_query->data)
                {
                    foreach($response->summary_query->data as $summary_query)
                    {

                        $inventory_id = $summary_query->inventory;
                        $evt_inv_data = new EventInventoryData();
                        
                        $loc_data = Location::where('loc', $summary_query->location)->get()->first();                            
                            if(!$loc_data){
                                $loc_data = new Location();
                                $loc_data->event_id = $response->event_id;
                                $loc_data->loc = $summary_query->location;
                                $loc_data->description = $summary_query->location_description;
                                $loc_data->save();
                                $locationId =  $loc_data->id;
                                
                                $loc_tag_data = new LocationTag();
                                $loc_tag_data->location_id = $locationId;
                                $loc_tag_data->location_tag_1 = $summary_query->location_tag_1;
                                $loc_tag_data->location_tag_1_description = $summary_query->location_tag_1_description;
                                $loc_tag_data->save();
                            }
                          
                        
                      
                            $sub_loc_data = SubLocation::where('sub_loc', $summary_query->sub_location)
                            ->where('loc_id', $loc_data->id)
                            ->get()->first();
                            if(empty($sub_loc_data)){
                                $sub_loc_data = new SubLocation();
                                $sub_loc_data->event_id = $response->event_id;
                                $sub_loc_data->sub_loc = $summary_query->sub_location;
                                $sub_loc_data->loc_id =  $loc_data->id;
                                $sub_loc_data->description = $summary_query->sub_location_description;
                                $sub_loc_data->save();

                                $sub_loc_tag_data = new SubLocationTag();
                                $sub_loc_tag_data->sub_location_id = $sub_loc_data->id;
                                $sub_loc_tag_data->sub_location_tag_1 = $summary_query->sub_location_tag_1;
                                $sub_loc_tag_data->sub_location_tag_1_description = $summary_query->sub_location_tag_1_description;
                                $sub_loc_tag_data->save();
                            }
                            
                       
                        //check for inventory_id with event id
                        $inventory_id_exist = EventInventory::where('event_id', $response->event_id)->where('inventory_id', $inventory_id)->get()->first();
                        if(empty($inventory_id_exist)){
                            $evt_inventory = new EventInventory();
                            $evt_inventory->event_id = $response->event_id;
                            $evt_inventory->inventory_id = $inventory_id;
                            $evt_inventory->save();
                            $event_inventory_id = $evt_inventory->id;
                        }else{
                            $event_inventory_id = $inventory_id_exist->id;
                        
                        }

                       
     

                        $evt_inv_data->event_inventory_id = $event_inventory_id;
                        $evt_inv_data->employee_id = $summary_query->employee;
                        $evt_inv_data->sub_location_id = $sub_loc_data->id;
                        $evt_inv_data->category_id =  $summary_query->category;
                        $evt_inv_data->records = $summary_query->records;
                        $evt_inv_data->pieces = $summary_query->pieces;
                        $evt_inv_data->cost = $summary_query->cost;
                        $evt_inv_data->price = $summary_query->price;
                        $evt_inv_data->save();

                    }
                }
            }
            \Log::channel('json_import')->info('importing done for event id: '.$response->event_id );
            return true;
        }
        else return false;
    }

    
    

   
    
}