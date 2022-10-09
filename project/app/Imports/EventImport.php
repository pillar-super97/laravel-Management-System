<?php

namespace App\Imports;

use App\Models\Timesheet;
use App\Models\Timeentries;
use App\Models\Event;
use App\Models\EventAreas;
use App\Models\Employee;
use App\Models\Store;
use App\Models\Area;
use Illuminate\Support\Facades\DB;
//use Illuminate\Support\Facades\Date;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;
//use Illuminate\Support\Carbon;


class EventImport implements OnEachRow
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function onRow(Row $row)
    {
        $errors = [];
        try{
            $rowIndex = $row->getIndex();
            if($rowIndex>1)
            {
                $cols = $row->toArray();
                //\Illuminate\Support\Facades\Log::info(json_encode($cols));
                DB::beginTransaction();
                if (count($cols) > 13 
                    && !empty($cols['0']) 
                    && !empty($cols['1']) 
                    && !empty($cols['2'])
                    && !empty($cols['3'])
                    && !empty($cols['4']) 
                    && !empty($cols['5'])
                    && !empty($cols['6'])) {
                    //echo $cols[3];
                    $eventdate = Carbon\Carbon::parse(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($cols['2']));
                    // echo $eventdate = $this->transformDateTime($cols[2]);echo '<br>';
                    //$start_time=date('g:i A',strtotime($cols[3]));
                    //echo $start_time = $this->transformTime($cols[3],'g:i A');
                    //added 
                    if(gettype($cols[3]) == 'string'){
                        $dateTime = $eventdate->format('Y-m-d').' '.((string)$cols[3]);
                        $start_time = Carbon\Carbon::parse($dateTime)->format('H:i');
                    }else{
                        $start_time = Carbon\Carbon::parse(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($cols['3']));
                    }
                    // $start_time = Carbon\Carbon::parse($cols['3'])->format('H:i');
                    //$eventdate = Carbon\Carbon::parse(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($cols[2]))->format('Y-m-d');
                    //$eventdate = Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($cols[2]));
                    //echo $start_time = Carbon\Carbon::parse(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($cols[3]))->toTimeString();die;
                    $employee_id=0;
                    //echo '<pre>';print_r($cols);die;
                    $store_id=0;
                    if($cols['0'])
                    {
                        $employee = Employee::select('id')
                            ->where('emp_number','=',$cols['0'])
                            ->first();
                        if($employee)
                            $employee_id=$employee->id;
                    }
                    if($cols['1'])
                    {
                        $store = Store::select('id')
                            ->where('number','=',$cols['1'])
                            ->first();
                        if($store)
                            $store_id=$store->id;
                    }
                    $data = [
                        'crew_leader'=>$employee_id,
                        'store_id'=>$store_id,
                        'date' => date('Y-m-d',strtotime($eventdate)),
                        'start_time'=>date('H:i',strtotime($start_time)),
                        'run_number'=>($cols[4])?$cols[4]:0,
                        'crew_count'=>($cols['6'])?$cols['6']:0,
                        'overnight'=>($cols['7'])?$cols['7']:'No',
                        'pic'=>($cols['8'])?$cols[8]:'No',
                        'qc'=>($cols[9])?$cols['9']:'No',
                        'count_rx'=>($cols[10])?$cols[10]:'No',
                        'count_backroom'=>($cols[11])?$cols[11]:'No',
                        'road_trip'=>($cols[12])?$cols[12]:'No',
                        'comments'=>$cols[13],
                        'status' =>'Pending'
                    ];
                    //echo '<pre>';print_r($data);

                    $event = Event::create($data);
                    $event_id = $event->id;
                    //echo '<pre>';print_r($event_id->id);die;
                    if($cols[5])
                    {
                        $areas = explode(',',$cols[5]);
                        foreach($areas as $area)
                        {
                            if($area)
                            {
                                $area_id = Area::select('id')
                                    ->where('area_number','=',$area)
                                    ->first();
                                if($area_id)
                                {
                                    EventAreas::create([
                                        'event_id' => $event_id,
                                        'area_id' => $area_id->id,
                                    ]);
                                }
                            }
                        }
                    }

                    $event_number = date('ymd').str_pad($event_id, 4, '0', STR_PAD_LEFT);
                    $where = array('id' => $event_id);
                    $updateArr = ['number' => $event_number];
                    $event  = Event::where($where)->update($updateArr);
                }  
    //            echo '<pre>';print_r($cols);
            }
        }catch(\Exception $e){
            $errors[] = $e->getMessage();
            // \Illuminate\Support\Facades\Log::error($e->getMessage());
        }
        if(count($errors)){
            DB::rollBack();
        }else{
            DB::commit();
        }
        // dd($errors);
    }
    
    public function transformDateTime(string $value, string $format = 'Y-m-d')
    {
         try {
                return Carbon\Carbon::instance(Date::excelToDateTimeObject($value))->format($format);
             } catch (\ErrorException $e) {
                return Carbon\Carbon::createFromFormat($format, $value);
             }
    }
    
    public function transformTime(string $value, string $format = 'g:i A')
    {
         try {
                return Carbon\Carbon::instance(Date::excelToDateTimeObject($value))->format($format);
             } catch (\ErrorException $e) {
                return '';
             }
    }
    
}
