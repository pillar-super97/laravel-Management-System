<?php

namespace App\Http\Controllers\Admin;


use Carbon\Carbon;
use App\Models\Employee;
use App\Models\EmployeeAvailabilityDays;
use App\Models\TimesheetApproved;
use Illuminate\Http\Request;
use App\Http\Controllers\Traits\FileUploadTrait;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;

class ExcusedemployeeController extends Controller
{
    use FileUploadTrait;
   
    public function index(Request $request)
    {

        $d = Employee::with(['approvedTimeSheets' => function($query){
            $query->groupBy('employee_id');
        }])
        ->withCount('approvedTimeSheets')
        ->limit(10)->get();

        dd($d);
        
        return view('admin.excusedemployee.index');
    }
    
}
