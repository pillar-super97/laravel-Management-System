<?php

namespace App\Exports;

use App\Models\Timesheet;
use App\Models\Timeentries;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Date;
class TimesheetExport implements FromCollection, WithHeadings, WithStrictNullComparison
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
//         $timesheets = DB::table('time_entries')
//                ->selectRaw("username,employee_number,badge_id,employee_external_id,"
//                . "ein_tax_id,ein_name,DATE_FORMAT('pay_date','%m/%d/%Y') as pay_date,in_date,time_in,time_out,"
//                . "total_time,piece_quantity,timezone,override,time_off,area_number,cost_center2")
//                ->where('status','1')
//                ->get();
//         //$newDateFormat = $timesheets->pay_date->format('m/d/Y');
//         echo '<pre>';print_r($timesheets);die;
        $data=array();
        return $timesheets = Timeentries::select('username','employee_number','badge_id','employee_external_id',
                'ein_tax_id','ein_name',DB::raw('DATE_FORMAT(pay_date, "%m/%d/%Y") as pay_date'),'in_date','time_in',
                'time_out','total_time','piece_quantity','timezone','override','time_off','area_number','cost_center2',
                'cost_center3','cost_center4','cost_center5','custom1','custom2','custom3','custom4','custom5')
                //->where('pay_date','2020-05-02')
                //->where('timesheet_id','237')
                ->where('exclude_employee',0)
                ->where('status','1')->get();
        //echo '<pre>';print_r($timesheets);
//        echo 'dd';die;
        foreach($timesheets as $key=>$timesheet)
        {
            $data[$key]['Username']='';
            $data[$key]['Employee Id']=$timesheet->employee_number;
            $data[$key]['Badge Id']='';
            $data[$key]['Employee External Id']='';
            $data[$key]['EIN Tax Id']='';
            $data[$key]['EIN Name']='';
            $data[$key]['Pay Date']=$timesheet->pay_date;
            $data[$key]['In Date']='';
            $data[$key]['Time In']='';
            $data[$key]['Time Out']='';
            $data[$key]['Total Time']=$timesheet->total_time;
            $data[$key]['Piece Quantity']='';
            $data[$key]['Time Zone']='';
            $data[$key]['Override']='';
            $data[$key]['Time Off']='';
            $data[$key]['Cost Center 1']=$timesheet->area_number;
            $data[$key]['Cost Center 2']=$timesheet->cost_center2;
            $data[$key]['Cost Center 3']=$timesheet->cost_center3;
            $data[$key]['Cost Center 4']='';
            $data[$key]['Cost Center 5']='';
            $data[$key]['Custom 1']='';
            $data[$key]['Custom 2']='';
            $data[$key]['Custom 3']='';
            $data[$key]['Custom 4']='';
            $data[$key]['Custom 5']='';
           
        }
        //echo '<pre>';print_r($data);
        return $data;
    }
    
    public function headings(): array
    {
        return [
            'Username',
            'Employee Id',
            'Badge Id',	
            'Employee External Id',	
            'EIN Tax Id',
            'EIN Name',	
            'Pay Date',	
            'In Date',	
            'Time In',	
            'Time Out',	
            'Total Time',	
            'Piece Quantity',	
            'Time Zone',	
            'Override',	
            'Time Off',	
            'Cost Center 1',	
            'Cost Center 2',	
            'Cost Center 3',	
            'Cost Center 4',	
            'Cost Center 5',	
            'Custom 1',	
            'Custom 2',	
            'Custom 3',	
            'Custom 4',	
            'Custom 5',	
            'Rate Override 1',	
            'Rate Override 2',	
            'Rate Override 3',	
            'Rate Override 4',	
            'Rate Override 5',	
            'Shift',	
            'PieceRate Override 1',	
            'Piece Rate Override 2',	
            'Piece Rate Override 3',	
            'Piece Rate Override 4',	
            'Piece Rate Override 5',	
            'Pay Category',	
            'Reason Code In',	
            'Reason Code Out',	
            'Note',

//            'employee_number',
//            'pay_date',
//            'total_time',
//            'area_number',
//            'cost_center2'
        ];
    }
}