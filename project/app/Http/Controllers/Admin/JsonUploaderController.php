<?php

namespace App\Http\Controllers\Admin;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Filesystem\Filesystem;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreJSONRequest;
use App\Models\JsonFileLog;
use App\Models\Category;
use App\Models\EventCategory;
use App\Models\Employee;
use App\Models\EventEmployee;
use App\Models\Event;
use App\Models\Location;
use App\Models\SubLocation;
use App\Models\EventInventory;
use App\Models\EventInventoryData;
use App\Models\LocationTag;
use App\Models\SubLocationTag;
use Session;


class JsonUploaderController extends Controller
{

    /**
     * Display a json file uploader view
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (! Gate::allows('upload_json_index')) {
            return abort(401);
        }
        $json_files = JsonFileLog::orderBy('id', 'DESC')->get();

        return view('admin.json_data.index', compact('json_files'));
    }

    /**
     * Display a json file uploader view
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('upload_json_data')) {
            return abort(401);
        }

        return view('admin.json_data.create');
    }

    /**
     * Store a json event data
     *
     * @param  \App\Http\Requests\StoreJSONRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        if (! Gate::allows('create_json_data')) {
            return abort(401);
        }


        $DataFile = $request->file('data');
        
        if(!empty($DataFile)){

            if($this->ZipValidate($DataFile)){ //id file is ZIP


                //upload zip
                $data = [];
                if (! file_exists(public_path('uploads').'/json')) {
                    mkdir(public_path('uploads').'/json', 0777);
                }
                $data['filename'] = time() . '-' . $request->file('data')->getClientOriginalName();            
                if(JsonFileLog::create($data)){
                    $request->file('data')->move(public_path('uploads').'/json/', $data['filename']);
                    return redirect()->route('admin.json_data.index')->with('successmsg', 'File uploaded successfully.');
                }


                

            }elseif ($this->JsonValidate($DataFile)){   // if file is JSON
                if($request->hasFile('data')){
                    $data = [];
                    if (! file_exists(public_path('uploads').'/json')) {
                        mkdir(public_path('uploads').'/json', 0777);
                    }
                    $data['filename'] = time() . '-' . $request->file('data')->getClientOriginalName();            
                    if(JsonFileLog::create($data)){
                        $request->file('data')->move(public_path('uploads').'/json/', $data['filename']);
                        return redirect()->route('admin.json_data.index')->with('successmsg', 'File uploaded successfully.');
                    }
                }
            }else{
                Session::flash('error_message', 'Only .zip or .json files are allowed!'); 


                return view('admin.json_data.create');
            }
    

        }//empty check ends
        else{
                 Session::flash('error_message', 'No file selected!'); 
                
                 return view('admin.json_data.create');
        }
    }



    //File Validation
    function ZipValidate($DataFile){
        $Filecontent=file_get_contents($DataFile);
 
        if (strpos($Filecontent, "\x50\x4b\x03\x04") === false)
        {
           return false;
          
        }else{
            return true;
        }
    }

    function JsonValidate($DataFile){
        $Filecontent=file_get_contents($DataFile);

        $jsonFile = json_decode($Filecontent, true);

        if($jsonFile === null){
            return false;
        }else{
            return true;
        }
    }




    




    // public function store(StoreJSONRequest $request)
    // {

    //     if (! Gate::allows('create_json_data')) {
    //         return abort(401);
    //     }
    //     if($request->hasFile('data')){
    //         $data = [];
    //         if (! file_exists(public_path('uploads').'/json')) {
    //             mkdir(public_path('uploads').'/json', 0777);
    //         }
    //         $data['filename'] = time() . '-' . $request->file('data')->getClientOriginalName();            
    //         if(JsonFileLog::create($data)){
    //             $request->file('data')->move(public_path('uploads').'/json/', $data['filename']);
    //             return redirect()->route('admin.json_data.index')->with('successmsg', 'File uploaded successfully.');
    //         }
    //     }
    // }




    /**
     * Read JSON and Store data in database
     *
     * @param  \App\Http\Requests\StoreJSONRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function appendData($jsonId)
    {
        // if (! Gate::allows('append_json_data')) {
        //     return abort(401);
        // }


        $jsonFileLog = JsonFileLog::find(base64_decode(($jsonId)));
        $fileType = substr( $jsonFileLog->filename, strpos($jsonFileLog->filename, ".") + 1);

        //check if file is json on zip

        if($fileType == 'zip'){
            //handeling Zip
               $zipper = new \Chumper\Zipper\Zipper;
               $Path = public_path('uploads').'/json/'.$jsonFileLog->filename;
               $zipper->make($Path)->extractTo('uploads/json/Appdividend');

               $jsonFilepath =  public_path('uploads').'/json/';
               
               //pick json file from that folder
               $files = glob("$jsonFilepath*.json");

               //print_r($files);
               $this->uploadJsonFileData($files);

               //later delete extracted folder
               
               //\File::deleteDirectory(public_path('uploads/json/Appdividend'));

        }else{
            $this->uploadJsonFileData($jsonFileLog->filename, $jsonId);
        }

        
      
    }





    public function uploadJsonFileData($jsonlogFile, $jsonId){
        // dd(public_path('uploads').'/json/'.$jsonFileLog->filename);
        $jsonFileLog = JsonFileLog::find(base64_decode(($jsonId)));

        $filenameWithPath = public_path('uploads').'/json/'. $jsonlogFile;
        $jsonDataRes = fopen($filenameWithPath, 'r');
        $readFileData = fread($jsonDataRes, filesize($filenameWithPath));
        $errors = [];
        $cachedData = [
            'employees' => [],
            'categories' => [],
            'locations' => [],
            'sub_locations' => [],
        ];
        \DB::beginTransaction();
        if(!empty($readFileData)){
            $evtData = json_decode($readFileData);
            $evt = Event::find($evtData->event_id);

            $eventId = $evtData->event_id;

            if(!empty($evt)){
                // Category
                if(isset($evtData->categories) && isset($evtData->categories->data)){
                    foreach($evtData->categories->data as $catData){
                        $evt_data = Category::where('name', $catData->name)->get()->first();
                        $evt_data_cat = new EventCategory();
                        if(!empty($evt_data)){                                
                            $evt_data_cat->category_id = $evt_data->id;
                            $cachedData['categories'][$evt_data->id] = $evt_data;                              
                        }else{
                            $evt_data = new Category();
                            $evt_data->name = $catData->name;
                            if($evt_data->save()){
                                $cachedData['categories'][$evt_data->id] = $evt_data;
                                $evt_data_cat->category_id = $evt_data->id;
                            }
                        }
                        $evt_data_cat->event_id = $evtData->event_id;                                
                        $evt_data_cat->tags = $catData->tag;
                        $evt_data_cat->save();
                    }
                }
                //gap_query 
                if(isset($evtData->gap_query) && isset($evtData->gap_query->data)){
                    foreach($evtData->gap_query->data as $gapData){
                        if(!isset($cachedData['employees'][$gapData->employee_number])){
                            $emp_data = Employee::where('emp_number', $gapData->employee_number)
                                ->get()
                                ->first();
                            $cachedData['employees'][$gapData->employee_number] = $emp_data;
                        }else{
                            $emp_data = $cachedData['employees'][$gapData->employee_number];
                        }

                 
                        $evt_emp_data = new EventEmployee();
                        if(!empty($emp_data)){                            
                            $evt_emp_data->employee_id = $emp_data->id;   
                            $evt_emp_data->event_id = $evtData->event_id;                                
                            $evt_emp_data->start_time = \Carbon\Carbon::parse($gapData->start_time)->format('Y-m-d H:i:s');
                            $evt_emp_data->end_time = \Carbon\Carbon::parse($gapData->end_time)->format('Y-m-d H:i:s');
                            $evt_emp_data->total_gap_hours = $gapData->total_gap_hours;
                            $evt_emp_data->count = $gapData->count;
                            $evt_emp_data->pieces = $gapData->pieces;
                            $evt_emp_data->records = $gapData->records;
                            $evt_emp_data->is_rx = $gapData->is_rx;
                            $evt_emp_data->save();
                            // dd($evt_emp_data);
                        }else{
                            if(!isset($errors[$evtData->event_id]['employee'])){
                                $errors[$evtData->event_id]['employee'] = [];
                            }
                            $errors[$evtData->event_id]['employee'][] = $gapData;
                        }                        
                    }
                }

                //summary_query
                if(isset($evtData->summary_query) && isset($evtData->summary_query->data)){
                    foreach($evtData->summary_query->data as $invData){

                        $inventory_id = $invData->inventory;

                      
                        $evt_inv_data = new EventInventoryData();



                        if(!isset($cachedData['locations'][$invData->location])){
                            $loc_data = Location::where('loc', $invData->location)->get()->first();                            
                            if(empty($loc_data)){
                                $loc_data = new Location();
                                $loc_data->event_id = $eventId;
                                $loc_data->loc = $invData->location;
                                $loc_data->description = $invData->location_description;
                                $loc_data->save();
                                $locationId =  $loc_data->id;
                                
                                $loc_tag_data = new LocationTag();
                                $loc_tag_data->location_id = $locationId;
                                $loc_tag_data->location_tag_1 = $invData->location_tag_1;
                                $loc_tag_data->location_tag_1_description = $invData->location_tag_1_description;
                                $loc_tag_data->save();
                            }
                            //caching location
                            $cachedData['locations'][$invData->location] = $loc_data;
                        }else{
                            $loc_data = $cachedData['locations'][$invData->location];
                        }
                        
                        if(!isset($cachedData['sub_locations'][$invData->location.'_'.$invData->sub_location])){
                            $sub_loc_data = SubLocation::where('sub_loc', $invData->sub_location)
                            ->where('loc_id', $loc_data->id)
                            ->get()->first();
                            if(empty($sub_loc_data)){
                                
                                // $array = ['location'=>$invData->location,
                                //           'location_id'=>$loc_data->id,
                                //           'location_description'=>$invData->location_description, 
                                //           'sublocation'=> $invData->sub_location,
                                //           'sublocation_description' => $invData->sub_location_description
                                //          ];

                                // print_r($array);
                                // die;

                                $sub_loc_data = new SubLocation();
                                $sub_loc_data->event_id = $eventId;
                                $sub_loc_data->sub_loc = $invData->sub_location;
                                $sub_loc_data->loc_id = $locationId;
                                $sub_loc_data->description = $invData->sub_location_description;
                                $sub_loc_data->save();

                                $sub_loc_tag_data = new SubLocationTag();
                                $sub_loc_tag_data->sub_location_id = $sub_loc_data->id;
                                $sub_loc_tag_data->sub_location_tag_1 = $invData->sub_location_tag_1;
                                $sub_loc_tag_data->sub_location_tag_1_description = $invData->sub_location_tag_1_description;
                                $sub_loc_tag_data->save();
                            }
                            
                            // echo 'outaa loop';
                            // die;
                            //caching sub location
                            $cachedData['sub_locations'][$invData->location.'_'.$invData->sub_location] = $sub_loc_data;
                        }else{
                            $sub_loc_data = $cachedData['sub_locations'][$invData->location.'_'.$invData->sub_location];
                        }

                        // if(!isset($cachedData['employees'][$invData->employee])){
                        //     $emp_data = Employee::where('emp_number', $gapData->employee_number)
                        //         ->get()
                        //         ->first();
                        //     $cachedData['employees'][$invData->employee] = $emp_data;
                        // }else{
                        //     $emp_data = $cachedData['employees'][$invData->employee];
                        // }

                        if(!isset($cachedData['categories'][$invData->category])){
                            $cat_data = Category::where('name', $invData->category)->get()->first();
                            if(empty($cat_data)){
                                $cat_data = new Category();
                                $cat_data->name = $invData->category;
                                $cat_data->save();
                            }
                            $cachedData['categories'][$invData->category] = $cat_data;
                        }else{
                            $cat_data = $cachedData['categories'][$invData->category];
                        }

                        //check for inventory_id with event id
                        $inventory_id_exist = EventInventory::where('event_id', $eventId)->where('inventory_id', $inventory_id)->get()->first();
                        if(empty($inventory_id_exist)){
                            $evt_inventory = new EventInventory();
                            $evt_inventory->event_id = $eventId;
                            $evt_inventory->inventory_id = $inventory_id;
                            $evt_inventory->save();
                            $event_inventory_id = $evt_inventory->id;
                        }else{
                            $event_inventory_id = $inventory_id_exist->id;
                        //   echo '<pre>';
                        //   print_r($inventory_id_exist);
                        //   echo '<pre/>';

                        //   die;
                        }

     

                        $evt_inv_data->event_inventory_id = $event_inventory_id;
                        $evt_inv_data->employee_id = $emp_data->id;
                        $evt_inv_data->sub_location_id = $sub_loc_data->id;
                        $evt_inv_data->category_id = $cat_data->id;
                        $evt_inv_data->records = $invData->records;
                        $evt_inv_data->pieces = $invData->pieces;
                        $evt_inv_data->cost = $invData->cost;
                        $evt_inv_data->price = $invData->price;
                        $evt_inv_data->save();

                    }
                }
            }else{
                if(!isset($errors[$evtData->event_id])){
                    $errors[$evtData->event_id] = [];
                }
                $errors[$evtData->event_id][] = $evtData;
            }
            if(empty($errors)){
                $jsonFileLog->is_file_read = 1;
                $jsonFileLog->save();
                \DB::commit();
                return redirect()->route('admin.json_data.index')
                    ->with('successmsg', 'File read and data imported successfully.');
            }else{
                dd($errors);
                \DB::rollBack();
            }      
        }  
    }



    /**
     * Remove Json File from storage and database
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('json_data_delete')) {
            return abort(401);
        }
        $jsonFileLog = JsonFileLog::findOrFail($id);
        $filenameWithPath = public_path('uploads').'/json/'. $jsonFileLog->filename;
        if(file_exists($filenameWithPath) && unlink($filenameWithPath)){
            $jsonFileLog->delete();
        }

        return redirect()->route('admin.json_data.index')->with('successmsg', 'File deleted successfully.');
    }




    public function getClientEventData(){
        
    }
}
