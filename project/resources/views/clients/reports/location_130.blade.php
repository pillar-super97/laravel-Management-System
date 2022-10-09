@extends('clients.reports.layout', ['title'=> 'MSI Inventory Service Corporation'])

@section('content')



<table class="my-3 sub-heading col-12 p-1">
    <tr>

        <td class=" align-top">
            {{ $event->store->name }}<br />
            {{ $event->store->address }}
        </td>
        <td class="text-center align-top sub-heading">[130] Loaction Report <br> Final</td>
        <td class="text-right">
            @if(@$event->schedule_employees[0]->employee->name)
            MSI Manager : {{ @$event->schedule_employees[0]->employee->name}} <br />
            @endif
            Store Manager : {{$event->store->manager_id}}
        </td>
    </tr>
</table>


<table class="table table-striped" id="myTable" style="font-size:10px;">
    <thead>
        <tr>
            <th>Location</th>
            <th>Current</th>
            <th>Prior</th>
            <th>Variance Amount</th>
            <th>Variance %</th>
        </tr>
    </thead>
    <tbody>


        @foreach($locations as $location)

        <tr style="border:none; ">
            <th class="left strong" style="border:none; " colspan="5">{{$location->location}}</th>

        </tr>

        @foreach($location as $sub_location)
        <tr>

            <td class="left"> &emsp; &emsp;
                {{$sub_location->sub_location}}
            </td>

            <td class="left">
            {{ round($sub_location->current, 2)}}
            </td>

            <td class="right">

            {{ round($sub_location->prior, 2)}}

            </td>
            <td class="center">{{ round($sub_location->current - $sub_location->prior, 2)}}</td>
            <td class="right">
                {{ round(100 * ($sub_location->current - $sub_location->prior) / ($sub_location->prior == 0 ? 1 : $sub_location->prior), 2) }}
            </td>

        </tr>

        @endforeach

        <tr style="border:none; background:#000; color:#fff;">
            <th class="border-top">Total</th>


            <th class="border-top">
                {{ round($location->current_total, 2)}}
            </th>

            <th class="border-top">
                {{round($location->prior_total,2)}}
            </th>
            <th class="border-top">{{round($location->current_total - $location->prior_total, 2)}}</th>
            <th class="border-top">
            {{ round(100 * ($location->current_total - $location->prior_total) / ($location->prior_total == 0 ? 1 : $location->prior_total), 2) }}
           
            </th>
        </tr>

        <tr style="border:none; background:none;">
            <th colspan="6" style="border:none; background:none;"> </th>
        </tr>

        @endforeach

        <tr class="border-top border-bottom" style=" background:#fff; font-size:12px;">
            <th class="border-top border-bottom">Grand Total</th>


            <th class="border-top border-bottom">{{round( $locations->current_total, 2)}}</th>

            <th class="border-top border-bottom">{{round( $locations->prior_total, 2)}}</th>
            <th class="border-top border-bottom">{{round($locations->current_total - $locations->prior_total, 2)}}</th>
            <th class="border-top border-bottom">
            {{ round(100 * ($locations->current_total - $locations->prior_total) / ($locations->prior_total == 0 ? 1 : $locations->prior_total), 2) }}
            </th>
        </tr>


    </tbody>
</table>
@endsection