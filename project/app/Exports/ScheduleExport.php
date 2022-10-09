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

class ScheduleExport implements FromCollection, WithHeadings
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
        //$date = date('Y-m-d');
        foreach($employees as $employee)
        {
//            $event_scheduled = DB::table('events')
//                ->whereDate('events.date', '=', $date)
//                ->where('event_schedule_employees.employee_id','=',$employee->id)
//                ->select('events.*')
//                ->leftJoin('event_schedule_employees','events.id','=','event_schedule_employees.event_id')
//                ->first();
//            if($event_scheduled)
//            {
            if($employee->area_id)
            {
                $data[$key]['PRLAST']=$employee->last_name;
                $data[$key]['PRFIRST']=$employee->first_name;
                $data[$key]['PREMPL']=$employee->emp_number;
                $data[$key]['PRSSNUM']=$employee->ss_no;
                
                    $area = DB::table('areas')
                        ->where('id','=',$employee->area_id)
                        ->select('area_number')
                        ->first();
                    if(strlen($area->area_number)==1)
                        $area_number='0'.$area->area_number;
                    else
                        $area_number=$area->area_number;
                    $data[$key]['BRANCH']=$area_number;
            }
                
            //}
            $key++;
        }
            
        
        //echo '<pre>';print_r($data);die;
        return collect($data);
    }
    
    public function headings(): array
    {
        return [
            'PRLAST',
            'PRFIRST',
            'PREMPL',
            'PRSSNUM',
            'BRANCH'
           
        ];
    }
}