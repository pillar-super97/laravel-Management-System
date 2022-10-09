<?php

namespace App\Imports;
use App\Models\Event;
use Illuminate\Support\Facades\DB;
//use Illuminate\Support\Facades\Date;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;
//use Illuminate\Support\Carbon;


class MealImport implements OnEachRow
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
            //print_r($row);die;
            
            if ($row['0'] && $row['1']) {
                //echo '<pre>';print_r($row);die;
                $where = array('id' => $row[0]);
                $updateArr = ['meal_amount' => ($row[1])?$row[1]:0];
                $event  = Event::where($where)->update($updateArr);
           }  
        }
    }
    
}