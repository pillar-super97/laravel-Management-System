<?php

namespace App\Exports;

use App\Models\Timesheet;
use App\Models\Timeentries;
use App\Models\Event;
use App\Models\EventScheduleEmployees;
use App\Models\Store;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Date;

class EventInfoExport implements FromCollection, WithHeadings, WithColumnFormatting
{
     use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {

        $data=array();
        $results = Event::select('events.*','events.id as eventid','stores.number','stores.name as storename','stores.address as storeaddress',
                    'cities.name as cityname','states.state_code as statename','stores.zip as storezip','clients.cust_no',
                    'stores.benchmark as storebenchmark','employees.name as empname','employees.ss_no as empss_no')
                    ->leftJoin('stores','stores.id','=','events.store_id')
                    ->leftJoin('cities','stores.city_id','=','cities.id')
                    ->leftJoin('states','stores.state_id','=','states.id')
                    ->leftJoin('clients','clients.id','=','stores.client_id')
                    ->leftJoin('employees','employees.id','=','events.crew_leader')
                    ->where('date','>=','2020-06-01')
                    ->get();
//        echo '<pre>';print_r($results);
//        echo 'dd';die;
        $key=0;
        foreach($results as $result)
        {
            $areas = DB::table('event_areas')
                ->select('areas.area_number')
                ->leftJoin('areas','areas.id','=','event_areas.area_id')
                ->where('event_areas.event_id','=',$result->eventid)
                ->get();
            if($areas->isEmpty())
            {
                $data[$key]['Store_No'] = $result->number;
                $data[$key]['ScheDate'] = date('m/d/Y',strtotime($result->date));
                $data[$key]['ScheDate_SQL'] = date('n/j/Y',strtotime($result->date));
                $data[$key]['ScheAP'] = substr(date('A',strtotime($result->start_time)), 0, 1);
                $data[$key]['ScheArea'] ='';
                $ScheTime = date('h:i',strtotime($result->start_time));
                    $data[$key]['ScheTime'] =$ScheTime;
                $data[$key]['Comments'] =$result->comments;
                $data[$key]['SName'] =$result->storename;
                $data[$key]['Street'] =$result->storeaddress;
                $data[$key]['City'] =$result->cityname;
                $data[$key]['State'] =$result->statename;
                $data[$key]['Zip'] =$result->storezip;
                $data[$key]['CustNo'] =$result->cust_no;
                $data[$key]['CRun'] =$result->run_number;
                $data[$key]['BenchMark'] =$result->storebenchmark/1000;
                $data[$key]['Crewldr'] =$result->empname;
                $data[$key]['Cl_ssn'] =$result->empss_no;
                $data[$key]['EventNo'] =$result->eventid;
                $data[$key]['RDESC1'] =$result->eventid;
                $data[$key]['DeltUser'] ='';
                $key++;
            }else{
                foreach($areas as $area)
                {
                    $data[$key]['Store_No'] = $result->number;
                    $data[$key]['ScheDate'] = date('m/d/Y',strtotime($result->date));
                    $data[$key]['ScheDate_SQL'] = date('n/j/Y',strtotime($result->date));
                    $data[$key]['ScheAP'] = substr(date('A',strtotime($result->start_time)), 0, 1);
                    //if(strlen($area->area_number)==1)
                       $area_id = substr("0000{$area->area_number}", -2);
//                    else
//                        $area_id = $area->area_number;
                    $data[$key]['ScheArea'] =$area_id;
                    $ScheTime = date('h:i',strtotime($result->start_time));
                    $data[$key]['ScheTime'] =$ScheTime;
                    $data[$key]['Comments'] =$result->comments;
                    $data[$key]['SName'] =$result->storename;
                    $data[$key]['Street'] =$result->storeaddress;
                    $data[$key]['City'] =$result->cityname;
                    $data[$key]['State'] =$result->statename;
                    $data[$key]['Zip'] =$result->storezip;
                    $data[$key]['CustNo'] =$result->cust_no;
                    $data[$key]['CRun'] =$result->run_number;
                    $data[$key]['BenchMark'] =$result->storebenchmark/1000;
                    $data[$key]['Crewldr'] =$result->empname;
                    $data[$key]['Cl_ssn'] =$result->empss_no;
                    $data[$key]['EventNo'] =$result->eventid;
                    $data[$key]['RDESC1'] =$result->eventid;
                    $data[$key]['DeltUser'] ='';
                    $key++;
                }
            }
            //echo '<pre>';print_r($event_scheduled);die;
        }
        //echo '<pre>';print_r($data);die;
        return collect($data);
    }
    
    public function headings(): array
    {
        return [
            'Store_No',
            'ScheDate',
            'ScheDate_SQL',
            'ScheAP',
            'ScheArea',
            'ScheTime',
            'Comments',
            'SName',
            'Street',
            'City',
            'State',
            'Zip',
            'CustNo',
            'CRun',
            'BenchMark',
            'Crewldr',
            'Cl_ssn',
            'EventNo',
            'RDESC1',
            'DeltUser',
        ];
    }
    
    
    public function columnFormats(): array
    {
        return [
            'E' => '@'
           
        ];
    }
}