<?php

namespace App\Http\Controllers;

use App\Models\JsonReport;
use App\Models\Event;
use Illuminate\Http\Request;

class JsonReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\JsonReport  $jsonReport
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, JsonReport $jsonReport)
    {
       $report = $request->report;
       $event = Event::with('store')->with(["schedule_employees" => function($q){
        $q->with('employee')->where('task', '=', 'Supervisor');
        }])->findOrFail($jsonReport->event_id);
        // }])->findOrFail(23287);

       return $this->$report($jsonReport,$event);


    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\JsonReport  $jsonReport
     * @return \Illuminate\Http\Response
     */
    public function edit(JsonReport $jsonReport)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\JsonReport  $jsonReport
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, JsonReport $jsonReport)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\JsonReport  $jsonReport
     * @return \Illuminate\Http\Response
     */
    public function destroy(JsonReport $jsonReport)
    {
        //
    }

    public function categories(JsonReport $jsonReport, $event)
    {
        
        $report = json_decode($jsonReport->data);

        $categories_report = collect();
        $categories_report->sales_floor_total = 0;
        $categories_report->stockroom_total = 0;
        $categories_report->current_total = 0;
        $categories_report->prior_total = 0;
        foreach($report->categories->data as $category)
        {
            $category->sales_floor = collect($report->summary_query->data)->where('category', $category->number)
            ->where("location_tag_1", "1")
            ->where("inventory", "1")
            ->sum('price');

            $category->stockroom = collect($report->summary_query->data)->where('category', $category->number)
            ->where("location_tag_1", "8")
            ->where("inventory", "1")
            ->sum('price');

            $category->current = collect($report->summary_query->data)->where('category', $category->number)
            ->where("inventory", "1")
            ->sum('price');

            $category->prior = collect($report->summary_query->data)->where('category', $category->number)
            ->where("inventory", "2")
            ->sum('price');

            if($category->sales_floor) 
            {
                $categories_report->push($category);
                $categories_report->sales_floor_total += $category->sales_floor;
                $categories_report->stockroom_total += $category->stockroom;
                $categories_report->current_total += $category->current;
                $categories_report->prior_total += $category->prior;
            }
            
        }

        // return view('clients.reports.category_110',['event' => $event, 'categories' => $categories_report]);

        $pdf = \PDF::loadView('clients.reports.category_110',[
            'title' => '[110] Category Report',
            'table_heading' => ['Category'],
            'event' => $event, 
            'categories' => $categories_report]);
        return $pdf->stream('area report.pdf');
    }

    public function area(JsonReport $jsonReport, $event)
    {
        
        $report = json_decode($jsonReport->data);

        $area_report = collect();
        $area_report->current_total = 0;
        $area_report->prior_total = 0;
        foreach($report->summary_query->data as $location)
        {
            $location->current = collect($report->summary_query->data)->where('location', $location->location)
            ->where("inventory", "1")
            ->sum('price');

            $location->prior = collect($report->summary_query->data)->where('location', $location->location)
            ->where("inventory", "2")
            ->sum('price');

            if(! $area_report->where('location', $location->location)->count())
            {
                $area_report->push($location);
            $area_report->current_total += $location->current;
            $area_report->prior_total += $location->prior;
            }
            
            
            
        }
       
        $locations = $area_report->sortBy('location');
        $locations->current_total = $area_report->current_total;
        $locations->prior_total = $area_report->prior_total;

        // return view('clients.reports.category_110',['event' => $event, 'categories' => $categories_report]);

        $pdf = \PDF::loadView('clients.reports.area_120',[
            'title' => '[120] Area Report',
            'table_heading' => ['Location'],
            'event' => $event, 
            'locations' => $locations]);
        return $pdf->stream('area report.pdf');
    }


    public function location(JsonReport $jsonReport, $event)
    {
        
        $report = json_decode($jsonReport->data);

        $areas = collect($report->summary_query->data)->groupBy('location');
        $area_report = collect();
        $area_report->current_total = collect($report->summary_query->data)->where('inventory','1')->sum('price');
        $area_report->prior_total = collect($report->summary_query->data)->where('inventory','2')->sum('price');

        foreach($areas as $area)
        {
            $item = collect();
            $item->location = $area->first()->location .' '. $area->first()->location_description;

            $item->current_total = $area->where('inventory','1')->sum('price');
            $item->prior_total = $area->where('inventory','2')->sum('price');

            $sub_locations = $area->groupBy('sub_location');

           $sub_locations->map(function ($sub_location) use ($item){
                $sub_location_detail = collect();
                $sub_location_detail->sub_location = $sub_location->first()->sub_location .' '. $sub_location->first()->sub_location_description;
                $sub_location_detail->current = $sub_location->where('inventory','1')->sum('price');
                $sub_location_detail->prior = $sub_location->where('inventory','2')->sum('price');
                $item->push($sub_location_detail);
               
            });

            

            
            $area_report->push($item);
            
            
            
            
        }

        // dd($area_report);

        // return view('clients.reports.location_130',[
        //     'title' => '[120] Area Report',
        //     'table_heading' => ['Location'],
        //     'event' => $event, 
        //     'locations' => $area_report]);

        $pdf = \PDF::loadView('clients.reports.location_130',[
            'title' => '[130] Loaction Report',
            'table_heading' => ['Location'],
            'event' => $event, 
            'locations' => $area_report]);
        return $pdf->stream('location report.pdf');
    }


    public function locationConsolidation(JsonReport $jsonReport, $event)
    {
        
        $report = json_decode($jsonReport->data);

       

        $areas = collect($report->summary_query->data)->groupBy('location');
        $area_report = collect();
        $area_report->current_total = collect($report->summary_query->data)->where('inventory','1')->sum('price');
        $area_report->prior_total = collect($report->summary_query->data)->where('inventory','2')->sum('price');

        foreach($areas as $area)
        {
            $item = collect();
            $item->location = $area->first()->location .' '. $area->first()->location_description;

            $item->current_total = $area->where('inventory','1')->sum('price');
            $item->prior_total = $area->where('inventory','2')->sum('price');

            $categories = $area->groupBy('category');

           $categories->map(function ($categories) use ($item, $report){
                $categories_detail = collect();
                $categories_detail->category = $categories->first()->category .' '. collect($report->categories->data)->where('number', $categories->first()->category)->first()->description;
                $categories_detail->current = $categories->where('inventory','1')->sum('price');
                $categories_detail->prior = $categories->where('inventory','2')->sum('price');
                $item->push($categories_detail);
               
            });
            
            $area_report->push($item); 
            
        }
        

        

        // return view('clients.reports.location_130',[
        //     'title' => '[120] Area Report',
        //     'table_heading' => ['Location'],
        //     'event' => $event, 
        //     'locations' => $area_report]);

        $pdf = \PDF::loadView('clients.reports.location_consolidation_130',[
            'title' => '[140] Loaction Consolidation Report',
            'table_heading' => ['Location'],
            'event' => $event, 
            'locations' => $area_report]);
        return $pdf->stream('location report.pdf');
    }
}
