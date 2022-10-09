<?php

namespace App\Repositories;

use App\Models\Timesheet;
use App\Models\Store;


class TimeSheetRepository 
{

    public function createTimeSheet(array $timesheet_header) 
    {
        $store = Store::select('id')->where('number',$timesheet_header['idStoreNo'])->first();

        $timesheet_header["event_id"] = $timesheet_header["EventNo"];
        $timesheet_header["store_id"] = 0;
        $timesheet_header["is_flagged"] = 1;
        $timesheet_header["status"] = 'Pending';

        if($store)
        {
            $timesheet_header["store_id"] = $store->id;
            $timesheet_header["is_flagged"] = 0;
        }
                    
        $timesheet_header["dEmpCount"] = $timesheet_header['total_emp_count'];
        $timesheet_header["dEmpPieces"] = $timesheet_header['total_emp_pieces'];
        $timesheet_header["InvStoreNumber"] = trim($timesheet_header["InvStoreNumber"] ?? 0) ;
        $timesheet_header["InvRecapStartTime"] = date('Y-m-d H:i:s',strtotime( $timesheet_header["InvRecapStartTime"] ));
        $timesheet_header["InvRecapEndTime"] = date('Y-m-d H:i:s',strtotime( $timesheet_header["InvRecapEndTime"] ));
        $timesheet_header["InvRecapWrapTime"] = date('Y-m-d H:i:s',strtotime( $timesheet_header["InvRecapWrapTime"] ));
        $timesheet_header["InvRecapArvlTime"] = date('Y-m-d H:i:s',strtotime( $timesheet_header["InvRecapArvlTime"] ));

        unset($timesheet_header["EventNo"]);
        unset($timesheet_header['total_emp_count']);
        unset($timesheet_header['total_emp_pieces']);
        unset($timesheet_header['idStoreNo']);

        return Timesheet::updateOrCreate([
                                            'event_id' => $timesheet_header["event_id"],
                                            'ALA_DeliveryDate' => $timesheet_header["ALA_DeliveryDate"],
                                        ], $timesheet_header );
        
    }

    
}