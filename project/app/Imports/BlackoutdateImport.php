<?php

namespace App\Imports;

use App\Models\Blackoutdate;

use Illuminate\Support\Facades\DB;
//use Illuminate\Support\Facades\Date;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;
//use Illuminate\Support\Carbon;


class BlackoutdateImport implements OnEachRow
{
    protected $financial_year = null;
    protected $client_id = null;
    public function __construct($financial_year,$client_id) 
    {
        $this->financial_year = $financial_year; 
        $this->client_id = $client_id;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function onRow(Row $row)
    {
        $rowIndex = $row->getIndex();
        if($rowIndex>1)
        {
            $row = $row->toArray();
            //echo '<pre>';print_r($row);
            
            if ($row['0']) {
                //echo $row[0];
                $blackoutdate = Carbon\Carbon::parse(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['0']));
                $client_id = $this->client_id;
                $financial_year = $this->financial_year;
                $date = date('Y-m-d',strtotime($blackoutdate));
                $division_id = ($row[2])?$row[2]:0;
                $district_id = ($row['3'])?$row['3']:0;
                $store_id = ($row['4'])?$row['4']:0;
                //echo '<pre>';print_r($row);
                $data = ['financial_year'=>$financial_year,'client_id'=>$client_id,'date' => $date,
                    'description'=>($row[1])?$row[1]:'','division_id'=>$division_id,'district_id'=>$district_id,
                    'store_id'=>$store_id];
                //echo '<pre>';print_r($data);
                Blackoutdate::where('financial_year', '=',$financial_year)
                        ->where('client_id', '=',$client_id)
                        ->where('division_id', '=',$division_id)
                         ->where('district_id', '=',$district_id)
                        ->where('store_id', '=',$store_id)
                        ->where('date', '=',$date)
                        ->delete();
                $event = Blackoutdate::create($data);
           }  
        }
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