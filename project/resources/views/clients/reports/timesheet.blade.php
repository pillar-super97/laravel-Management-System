@extends('clients.reports.layout', ['title'=> 'MSI Timesheet (Transmitting)'])

@section('content')



<table class="my-3 sub-heading col-12 p-1">
    <tr>
        <td class="align-top">{{date('m/d/y', strtotime($event->date))}}</td>
        <td class="text-center align-top">
            {{$event->store->name}}<br>
            {{$event->store->address}}
        </td>
        <td class="text-right">
            #{{$event->id}}<br />
        </td>
    </tr>
</table>


<table class="records table">
    <thead>
        <tr>
            <th>Employee Branch</th>
            <th>PIM</th>
            <th>L1<br>L2</th>
            <th>Gap<br>Wait</th>
            <th>Explanations</th>
            <th>Hours</th>
            <th>Time <br>In-Out</th>
            <th>Pieces <br>Pcs/Hr.</th>
            <th>Count <br>Prod</th>
            <th>Status</th>
            <th>Origin<br>Destination</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($event->timesheet->emp_data as $timesheet)
        <tr>

            <td>{{@$timesheet->employee->name}}</td>
            <td>{{$timesheet->PIMTime}}</td>
            <td>{{$timesheet->iLunch1}}<br> {{$timesheet->iLunch1}}</td>
            <td>{{$timesheet->iGapTime}}<br> {{$timesheet->iWaitTime}}</td>
            <td>{{$timesheet->WaitTimeExplanation}}</td>
            <td>{{$timesheet->hours}}</td>
            <td width="60px">
                @if($timesheet->dtStartDateTime)
                {{date('h:i a', strtotime($timesheet->dtStartDateTime))}}
                @endif
                <br>
                @if($timesheet->dtStopDateTime)
                {{date('h:i a', strtotime($timesheet->dtStopDateTime))}}
                @endif
            </td>
            <td>{{$timesheet->dEmpPieces}}<br>{{$timesheet->pices_per_hours}}</td>
            <td>{{$timesheet->dEmpCount}}<br>{{$timesheet->d_emp_per_hours}}</td>
            <td>{{$timesheet->status}}</td>
            <td>{{$timesheet->sStoreOrigin}}<br>{{$timesheet->sStoreReturn}}</td>

        </tr>
        @endforeach

        <tr>
            <th class="border-top" colspan="3">
                Grand Total:
            </th>
            <td> </td>
            <td> </td>
            <th class="border-top"> {{$event->total_hours}}</th>
            <td> </td>
            <td class="border-top"> {{$event->d_emp_pices_total}}</td>
            <td class="border-top"> ${{$event->d_emp_count_total}}</td>
            <td> </td>
            <td> </td>
        </tr>

    </tbody>
</table>





<div class="col-12 records my-3 p-1">
    <b>Crew Count: {{$event->crew_count}}<br>
        No Show Count: {{$event->timesheet->CrewNoShowCount}}</b>
</div>



<div class="col-12 records p-1">
    <b><u>Timesheet notes:<br>
            Went well. Store in pretty good shape.</u><br>
        Inventory recap comments:</b>

</div>

    <table class="table records my-3">
        <thead>
            <tr>
                <th>Veh#</th>
                <th>Driver to Store </th>
                <th>Start </th>
                <th>End </th>
                <th>Time </th>
                <th> Driver From Store </th>
                <th>Start </th>
                <th>End </th>
                <th>Time</th>
            </tr>
        </thead>
        <tbody>
            @foreach($event->timesheet->vehicles as $vehicle)
            <tr>

                <td> {{$vehicle->idVehicle}}</td>
                <td> {{$vehicle->driverTo->name}}</td>
                <td>
                    @if($vehicle->dtToStoreStart)
                    {{date('h:i a', strtotime($vehicle->dtToStoreStart))}}
                    @endif
                </td>
                <td>
                    @if($vehicle->dtToStoreEnd)
                    {{date('h:i a', strtotime($vehicle->dtToStoreEnd))}}
                    @endif
                </td>
                <td>{{@$vehicle->time_to_store}} </td>
                <td> {{$vehicle->driverFrom->name}}</td>
                <td>
                    @if($vehicle->dtFromStoreStart)
                    {{date('h:i a', strtotime($vehicle->dtFromStoreStart))}}
                    @endif
                </td>
                <td>
                    @if($vehicle->dtFromStoreEnd)
                    {{date('h:i a', strtotime($vehicle->dtFromStoreEnd))}}
                    @endif
                </td>
                <td> {{@$vehicle->time_from_store}}</td>


            </tr>
            @endforeach
        </tbody>
    </table>
    
</div>
</div>
</div>

@endsection