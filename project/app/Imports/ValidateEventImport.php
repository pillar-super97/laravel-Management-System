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
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Collection; 
use Maatwebsite\Excel\Concerns\ToCollection;
use Carbon;


class ValidateEventImport implements ToCollection
{
   
    public function collection(Collection $rows)
    {
        return $rows;
    }
    
}