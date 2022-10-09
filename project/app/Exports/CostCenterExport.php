<?php

namespace App\Exports;

use App\Models\Store;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Date;

class CostCenterExport implements FromCollection, WithHeadings
{
     use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {

        $data=array();
        $stores = Store::with(array('city','state'))->orderBy('name','asc')->get();
//        echo '<pre>';print_r($stores);
//        echo 'dd';die;
        foreach($stores as $key=>$store)
        {
            //echo '<pre>';print_r($store);die;
            $store_name = substr($store->name.', '.$store->address.', '.$store->city->name.', '.$store->state->state_code,0,60);
            $data[$key]['Tree Name']="";
            $data[$key]['Tree Index']=3;
            $data[$key]['Parent Name']="";
            $data[$key]['Parent Name Path']="";
            $data[$key]['Name']=$store_name;
            $data[$key]['Abbreviation']=substr($store->number,0,12);
            $data[$key]['Description']="";
            $data[$key]['Visible']="";
            $data[$key]['Currency']="";
            $data[$key]['Allocate Time']="";
            $data[$key]['External Id']="";
            $data[$key]['Payroll Code']="";
            $data[$key]['Location Code']="";
            $data[$key]['EIN Tax Id']="";
            
            
            $data[$key]['EIN Name']="";
            $data[$key]['Cost Center Extra 1']="";
            $data[$key]['Cost Center Extra 2']="";
            $data[$key]['Cost Center Extra 3']="";
            $data[$key]['Cost Center Extra 4']="";
            $data[$key]['Cost Center Extra 5']="";
            $data[$key]['Add To Lists']="";
            $data[$key]['Remove From Lists']="";
            $data[$key]['GL Code']="";
            $data[$key]['Address Name']="";
            $data[$key]['Address Line 1']="";
            $data[$key]['Address Line 2']="";
            $data[$key]['Address City']="";
            $data[$key]['Address State']="";
            $data[$key]['Address Zip']="";
            $data[$key]['Address Country']="";
            $data[$key]['Skill 1']="";
            $data[$key]['Skill 2']="";
            $data[$key]['Skill 3']="";
            $data[$key]['Skill 4']="";
            $data[$key]['Skill 5']="";
            $data[$key]['Skill 6']="";
            $data[$key]['Skill 7']="";
            $data[$key]['Skill 8']="";
            $data[$key]['Skill 9']="";
            $data[$key]['Skill 10']="";
            $data[$key]['Skill 11']="";
            $data[$key]['Skill 12']="";
            $data[$key]['Skill 13']="";
            $data[$key]['Skill 14']="";
            $data[$key]['Skill 15']="";
            $data[$key]['Skill 16']="";
            $data[$key]['Skill 17']="";
            $data[$key]['Skill 18']="";
            $data[$key]['Skill 19']="";
            $data[$key]['Skill 20']="";
            $data[$key]['Default Manager 1']="";
            $data[$key]['Default Manager 1 EIN Name']="";
            $data[$key]['Default Manager 1 EIN Tax Id']="";
            $data[$key]['Default Manager 2']="";
            $data[$key]['Default Manager 2 EIN Name']="";
            $data[$key]['Default Manager 2 EIN Tax Id']="";
            $data[$key]['Default Manager 3']="";
            $data[$key]['Default Manager 3 EIN Name']="";
            $data[$key]['Default Manager 3 EIN Tax Id']="";
            $data[$key]['Default Manager 4']="";
            $data[$key]['Default Manager 4 EIN Name']="";
            $data[$key]['Default Manager 4 EIN Tax Id']="";
            $data[$key]['Default Manager 5']="";
            $data[$key]['Default Manager 5 EIN Name']="";
            $data[$key]['Default Manager 5 EIN Tax Id']="";
            $data[$key]['Default Manager 6']="";
            $data[$key]['Default Manager 6 EIN Name']="";
            $data[$key]['Default Manager 6 EIN Tax Id']="";
        }
        //echo '<pre>';print_r($data);die;
        return collect($data);
    }
    
    public function headings(): array
    {
        return [
            'Tree Name',
            'Tree Index',
            'Parent Name',
            'Parent Name Path',
            'Name',
            'Abbreviation',
            'Description',
            'Visible',
            'Currency',
            'Allocate Time',
            'External Id',
            'Payroll Code',
            'Location Code',
            'EIN Tax Id',
            'EIN Name',
            'Cost Center Extra 1',
            'Cost Center Extra 2',
            'Cost Center Extra 3',
            'Cost Center Extra 4',
            'Cost Center Extra 5',
            'Add To Lists',
            'Remove From Lists',
            'GL Code',
            'Address Name',
            'Address Line 1',
            'Address Line 2',
            'Address City',
            'Address State',
            'Address Zip',
            'Address Country',
            'Skill 1',
            'Skill 2',
            'Skill 3',
            'Skill 4',
            'Skill 5',
            'Skill 6',
            'Skill 7',
            'Skill 8',
            'Skill 9',
            'Skill 10',
            'Skill 11',
            'Skill 12',
            'Skill 13',
            'Skill 14',
            'Skill 15',
            'Skill 16',
            'Skill 17',
            'Skill 18',
            'Skill 19',
            'Skill 20',
            'Default Manager 1',
            'Default Manager 1 EIN Name',
            'Default Manager 1 EIN Tax Id',
            'Default Manager 2',
            'Default Manager 2 EIN Name',
            'Default Manager 2 EIN Tax Id',
            'Default Manager 3',
            'Default Manager 3 EIN Name',
            'Default Manager 3 EIN Tax Id',
            'Default Manager 4',
            'Default Manager 4 EIN Name',
            'Default Manager 4 EIN Tax Id',
            'Default Manager 5',
            'Default Manager 5 EIN Name',
            'Default Manager 5 EIN Tax Id',
            'Default Manager 6',
            'Default Manager 6 EIN Name',
            'Default Manager 6 EIN Tax Id',
        ];
    }
}