<?php

namespace App\Exports;

use App\Models\Timesheet;
use App\Models\Timeentries;
use App\Models\Employee;
use App\Models\EventScheduleEmployees;
use App\Models\Store;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Date;

class EmployeeSchedulesExport implements FromCollection, WithHeadings
{
     use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {

        $data=array();
        $employees = Employee::select('employees.*')
                    ->where('status','Active')
                    ->get();
        $key=0;
        for($i=0;$i<15;$i++)
        {
            $date = date('Y-m-d',strtotime("+".$i." days"));
            //$date = date('Y-m-d',strtotime("-1 days"));
            //echo '<pre>';print_r($employees);
            //echo 'dd';die;
            //
            
            foreach($employees as $employee)
            {
                //echo $employee->id;
                //DB::enableQueryLog();

                //dd(DB::getQueryLog());
                //print_r($minute_part);die;
                $event_scheduled = DB::table('events')
                    ->whereDate('events.date', '=', $date)
                    ->where('event_schedule_employees.employee_id','=',$employee->id)
                    ->where('events.status','!=','inactive')
                    ->select('events.*')
                    ->leftJoin('event_schedule_employees','events.id','=','event_schedule_employees.event_id')
                    ->orderBy('events.start_time','asc')
                    ->limit(2)
                    ->get();
                if(count($event_scheduled))
                {
                    $event_schedule_count=1;
                    foreach($event_scheduled as $event)
                    {
                        $data[$key]['Employee External Id']="";
                        $data[$key]['Employee Username']="";
                        $data[$key]['Employee Employee Id']=$employee->emp_number;
                        $data[$key]['Employee EIN Name']='';
                        $data[$key]['Date']=date('m/d/Y',strtotime($date));
                        $data[$key]['Shift Number']='';
                        if($event_scheduled)
                            $data[$key]['Schedule Type Name']="Floating";
                        else
                            $data[$key]['Schedule Type Name']="No Schedule";
                        $data[$key]['Daily Rule Type Name']="";


                        if($event_scheduled)
                        {

                            $start_time = DB::table('event_areas')
                                ->where('event_id','=',$event->id)
                                ->where('area_id','=',$employee->area_id)    
                                ->select('meet_time')
                                ->first();
            //                dd(DB::getQueryLog());
            //                echo '<pre>';print_r($event);print_r($employee);
            //                echo 'dd';die;
                            if($start_time && $event_schedule_count==1)
                                $data[$key]['Start Time']=$start_time->meet_time;
                            elseif($start_time && $event_schedule_count>1)
                                $data[$key]['Start Time']=$event->start_time;
                            else
                                $data[$key]['Start Time']="";
                        }else
                            $data[$key]['Start Time']="";
                        if($event_scheduled)
                        {
                            $data[$key]['Start Time Max']=$event->start_time;
                        }else
                            $data[$key]['Start Time Max']="";
                        $data[$key]['End Time']="";
                        $data[$key]['End Time Max']="";
                        if($event_scheduled)
                        {
                            $hourpart='';
                            if($event->overnight=="Yes" && $event->road_trip!="No")
                                $hourpart='02';
                            elseif($event->overnight=="Yes")
                                $hourpart='01';
                            elseif($event->road_trip!="No")
                                $hourpart='02';
                            else
                                $hourpart='00';

                            $minute_part = DB::table('event_schedule_employees')
                            ->select('events.id as event_count')
                            ->leftJoin('events','events.id','=','event_schedule_employees.event_id')
                            ->whereDate('events.date', '=', $date)
                            ->where('event_schedule_employees.employee_id','=',$employee->id)
                            ->count();
                            //print_r($minute_part);
                            if(isset($minute_part))
                            {
                                if(strlen($minute_part)==1)
                                    $min='0'.$minute_part;
                            }else
                                $min='00';
                            $data[$key]['Total Hours']=$hourpart.':'.$min;
                        }else
                            $data[$key]['Total Hours']="";
                        $data[$key]['Standard Total Hours']="";
                        $data[$key]['Lunch Min Time']="";
                        $data[$key]['Lunch Max Time']="";
                        $data[$key]['Lunch Paid Time']="";
                        $data[$key]['Lunch Start At']="";
                        $data[$key]['Lunch Start After']="";
                        $data[$key]['Shift Premium']="";
                        $data[$key]['Cost Center 1 External Id']="";
                        $data[$key]['Cost Center 1 Name']="";
                        $data[$key]['Cost Center 2 External Id']="";
                        $data[$key]['Cost Center 2 Name']="";
                        $data[$key]['Cost Center 3 External Id']="";
                        //echo '<pre>';print_r($event_scheduled);die;
                        if($event_scheduled)
                        {
                            $store = Store::with(array('city','state'))->where('id','=',$event->store_id)->first();
                            $store_name = substr($store->name.', '.$store->address.', '.$store->city->name.', '.$store->state->state_code,0,60);
                            $data[$key]['Cost Center 3 Name']=$store_name;
                        }else
                            $data[$key]['Cost Center 3 Name']="";
                        $data[$key]['Cost Center 4 External Id']="";
                        $data[$key]['Cost Center 4 Name']="";
                        $data[$key]['Cost Center 5 External Id']="";
                        $data[$key]['Cost Center 5 Name']="";
                        $data[$key]['Cost Center 6 External Id']="";
                        $data[$key]['Cost Center 6 Name']="";
                        $data[$key]['Cost Center 7 External Id']="";
                        $data[$key]['Cost Center 7 Name']="";
                        $data[$key]['Cost Center 8 External Id']="";
                        $data[$key]['Cost Center 8 Name']="";
                        $data[$key]['Cost Center 9 External Id']="";
                        $data[$key]['Cost Center 9 Name']="";
                        $data[$key]['Job External Id']="";
                        $data[$key]['Job Name']="";
                        $data[$key]['Workday Breakdown']="";
                        $data[$key]['Day Type']="";
                        $data[$key]['Is Working']="";
                        $key++;
                        $event_schedule_count++;
                    }
                }else{
                    $data[$key]['Employee External Id']="";
                    $data[$key]['Employee Username']="";
                    $data[$key]['Employee Employee Id']=$employee->emp_number;
                    $data[$key]['Employee EIN Name']='';
                    $data[$key]['Date']=date('m/d/Y',strtotime($date));
                    $data[$key]['Shift Number']='';
                    $data[$key]['Schedule Type Name']="No Schedule";
                    $data[$key]['Daily Rule Type Name']="";
                    $data[$key]['Start Time']="";
                    $data[$key]['Start Time Max']="";
                    $data[$key]['End Time']="";
                    $data[$key]['End Time Max']="";
                    $data[$key]['Total Hours']="";
                    $data[$key]['Standard Total Hours']="";
                    $data[$key]['Lunch Min Time']="";
                    $data[$key]['Lunch Max Time']="";
                    $data[$key]['Lunch Paid Time']="";
                    $data[$key]['Lunch Start At']="";
                    $data[$key]['Lunch Start After']="";
                    $data[$key]['Shift Premium']="";
                    $data[$key]['Cost Center 1 External Id']="";
                    $data[$key]['Cost Center 1 Name']="";
                    $data[$key]['Cost Center 2 External Id']="";
                    $data[$key]['Cost Center 2 Name']="";
                    $data[$key]['Cost Center 3 External Id']="";
                    $data[$key]['Cost Center 3 Name']="";
                    $data[$key]['Cost Center 4 External Id']="";
                    $data[$key]['Cost Center 4 Name']="";
                    $data[$key]['Cost Center 5 External Id']="";
                    $data[$key]['Cost Center 5 Name']="";
                    $data[$key]['Cost Center 6 External Id']="";
                    $data[$key]['Cost Center 6 Name']="";
                    $data[$key]['Cost Center 7 External Id']="";
                    $data[$key]['Cost Center 7 Name']="";
                    $data[$key]['Cost Center 8 External Id']="";
                    $data[$key]['Cost Center 8 Name']="";
                    $data[$key]['Cost Center 9 External Id']="";
                    $data[$key]['Cost Center 9 Name']="";
                    $data[$key]['Job External Id']="";
                    $data[$key]['Job Name']="";
                    $data[$key]['Workday Breakdown']="";
                    $data[$key]['Day Type']="";
                    $data[$key]['Is Working']="";
                    $key++;
                }
            }
            
        }
        //echo '<pre>';print_r($data);die;
        return collect($data);
    }
    
    public function headings(): array
    {
        return [
            'Employee External Id',
            'Employee Username',
            'Employee Employee Id',
            'Employee EIN Name',
            'Date',
            'Shift Number',
            'Schedule Type Name',
            'Daily Rule Type Name',
            'Start Time',
            'Start Time Max',
            'End Time',
            'End Time Max',
            'Total Hours',
            'Standard Total Hours',
            'Lunch Min Time',
            'Lunch Max Time',
            'Lunch Paid Time',
            'Lunch Start At',
            'Lunch Start After',
            'Shift Premium',
            'Cost Center 1 External Id',
            'Cost Center 1 Name',
            'Cost Center 2 External Id',
            'Cost Center 2 Name',
            'Cost Center 3 External Id',
            'Cost Center 3 Name',
            'Cost Center 4 External Id',
            'Cost Center 4 Name',
            'Cost Center 5 External Id',
            'Cost Center 5 Name',
            'Cost Center 6 External Id',
            'Cost Center 6 Name',
            'Cost Center 7 External Id',
            'Cost Center 7 Name',
            'Cost Center 8 External Id',
            'Cost Center 8 Name',
            'Cost Center 9 External Id',
            'Cost Center 9 Name',
            'Job External Id',
            'Job Name',
            'Workday Breakdown',
            'Day Type',
            'Is Working'
        ];
    }
}