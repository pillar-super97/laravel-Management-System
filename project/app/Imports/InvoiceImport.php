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


class InvoiceImport implements OnEachRow
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function onRow(Row $row)
    {
        $rowIndex = $row->getIndex();
        if($rowIndex>1)
        {
            $row = $row->toArray();
            
            if ($row['0'] && $row['1']) {
                $where = array('id' => $row[0]);
                $updateArr = ['invoice_amount' => ($row[1])?$row[1]:null];
                $event  = Event::where($where)->update($updateArr);
           }  
        }
    }
    
}