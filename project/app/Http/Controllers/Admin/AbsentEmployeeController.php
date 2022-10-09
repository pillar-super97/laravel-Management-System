<?php

namespace App\Http\Controllers\Admin;

use App\Models\AbsentEmployee;
use App\Models\ExcludeAbsentEmployee;
use App\Models\Employee;
use App\Models\Area;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class AbsentEmployeeController extends Controller
{
    public $date_between;

    public function __construct()
    {
        

        $this->date_between = date('m/d/Y', strtotime('-1 days')).' - '.date('m/d/Y');


    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('excused_employee')) {
            return abort(401);
        }

        $date_between = explode(' - ',$this->date_between);
        $date_between = [ 
                            date('Y-m-d',strtotime($date_between[0])),
                            date('Y-m-d',strtotime($date_between[1]))
                        ];

        $employees = Employee::select('name','emp_number')->get();
        $areas = Area::select('title','id')->get();
        return view('admin.timesheets.absent_employees', [
            'employees'=>$employees, 
            'areas'=>$areas,
            'date_between'=>$this->date_between
        ]);
        
    }


    public function getAbsentEmployees(Request $request)
    {
        $this->date_between = $request->date_between ?? $this->date_between;
        $date_between = explode(' - ',$this->date_between);
        $date_between = [ 
                            date('Y-m-d',strtotime($date_between[0])),
                            date('Y-m-d',strtotime($date_between[1]))
                        ];

        $absent_employees = AbsentEmployee::whereBetween('event_date',$date_between);

        //set filter on employee number
        if($request->employee_number) $absent_employees->where('employee_number', $request->employee_number);

        //set filter on area
        if($request->area_id) $absent_employees->where('area_id', $request->area_id);
        
        
        $absent_employees = $absent_employees->get();

        return datatables()->of($absent_employees)->addIndexColumn()
        ->addColumn('action', function($row){

               $btn = '<a href="javascript:void(0)" class="btn btn-primary btn-exclude">Exclude</a>';

                return $btn;
        })
        ->rawColumns(['action'])->make(true);

    }

    public function excludeAbsentEmployee(Request $request)
    {
        ExcludeAbsentEmployee::create([
            'employee_id' => $request->employee_id,
            'event_id' => $request->event_id,
            'note' => $request->note,
            'created_by' => $request->user()->id
        ]);
        return response()->json($request->all());
    }

    public function excludedAbsentEmployees()
    {
        if (! Gate::allows('excused_employee')) {
            return abort(401);
        }
        $employees = Employee::select('id','name','emp_number')->get();
        return view('admin.timesheets.excluded_absent_employees', [
            'employees'=>$employees,
            'date_between'=>$this->date_between
        ]);

    }

    public function getExcludedAbsentEmployees(Request $request)
    {
        if (! Gate::allows('excused_employee')) {
            return abort(401);
        }
        
        $this->date_between = $request->date_between ?? $this->date_between;
        $date_between = explode(' - ',$this->date_between);
        $date_between = [ 
                            date('Y-m-d',strtotime($date_between[0])),
                            date('Y-m-d',strtotime($date_between[1])).' 23:59:59'
                        ];

        $excluded_absent_employees = ExcludeAbsentEmployee::with('detail')->whereBetween('created_at',$date_between);

        //set filter on employee id
        if($request->employee_id) $excluded_absent_employees->where('employee_id', $request->employee_id);

        return datatables()->eloquent($excluded_absent_employees)
                ->addColumn('employee_name', function (  $excluded_absent_employees) {
                    return $excluded_absent_employees->detail->name;
                })
                ->addColumn('employee_number', function (  $excluded_absent_employees) {
                    return $excluded_absent_employees->detail->emp_number;
                })
                ->toJson();

    }
}