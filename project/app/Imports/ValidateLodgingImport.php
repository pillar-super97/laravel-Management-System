<?php

namespace App\Imports;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Collection; 
use Maatwebsite\Excel\Concerns\ToCollection;
use Carbon;


class ValidateLodgingImport implements ToCollection
{
   
    public function collection(Collection $rows)
    {
        return $rows;
    }
    
}