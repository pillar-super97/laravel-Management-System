<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Event;

class WeeklyProjectedHours extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {

        if($request->user()->isAdmin()) $areas = Area::select('title','id')->get()->keyBy('id');
        else $areas = $request->user()->area->keyBy('id');

        $selected_area = $areas->get($request->area_id);

        if(!$selected_area && $request->area_id) return abort(401);

        $start_date = $request->start_date ?? date('m/d/Y');
        $date_between = [
            date('Y-m-d',strtotime($start_date)),
            date('Y-m-d', strtotime('+7 days',strtotime($start_date)))
        ];
        

        $events = Event::with(['areas','schedule_employees.employee','store', 'event_schedule_data'])
        ->whereBetween('date',$date_between)
        ->whereHas('areas', function($q) use($selected_area){
            $q->where('area_id', @$selected_area->id);
        })
        ->orderBy('date')
        ->get();
        
       
        $employees = [];

        foreach($events as $event)
        {
            foreach($event->schedule_employees as $schedule_employee) 
            {
              
                $css_class = '';

                if( strpos( strtolower($schedule_employee->task) , 'rx' ) !== false) $css_class = 'bg-yellow';
                if( strpos( strtolower($schedule_employee->task) , 'super' ) !== false) $css_class = 'bg-red';

                if (!array_key_exists($schedule_employee->employee->id,$employees)) $employees[$schedule_employee->employee->id] =  ['name' => $schedule_employee->employee->name];
                if (!array_key_exists($event->id,$employees[$schedule_employee->employee->id])) $employees[$schedule_employee->employee->id][$event->id] =  
                [ 
                    'css_class'=> $css_class,
                    'task'=> $schedule_employee->task,
                    'hours'=> @$event->event_schedule_data->schedule_length
                ];

            }
        }
        return view('admin.reports.weekly_projected_hours',[
            'areas' => $areas,
            'events' => $events,
            'stores' => [],
            'total_count' => [],
            'employees' => $employees,
            'selected_area_id' => @$selected_area->id,
            'start_date' => $start_date
        ]);
    }
}
