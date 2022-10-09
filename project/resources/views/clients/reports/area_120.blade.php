@extends('clients.reports.layout', ['title'=> 'MSI Inventory Service Corporation'])

@section('content')
<p class="text-center m-0">{{$title}}</p>



<table class="my-3 sub-heading col-12 p-1">
    <tr>

        <td class=" align-top">
            {{ $event->store->name }}<br />
            {{ $event->store->address }}
        </td>
        <td class="text-center align-top sub-heading"></td>
        <td class="text-right">
            @if(@$event->schedule_employees[0]->employee->name)
            MSI Manager : {{ @$event->schedule_employees[0]->employee->name}} <br />
            @endif
            Store Manager : {{$event->store->manager_id}}
        </td>
    </tr>
</table>

<p class="text-center">
    Final
</p>

<table class="table records" id="myTable">
    <thead>
        <tr>
            <th>{{$table_heading[0]}}</th>

            <th>Current</th>

            <th>Prior</th>
            <th>Variance Amount</th>
            <th>Variance (%)</th>

        </tr>
    </thead>
    <tbody>


        @foreach($locations as $location)

        <tr>

            <td>

                {{$location->location}} {{$location->location_description}}
            </td>

            <td class="left">
                {{$location->current}}
            </td>

            <td class="right">

                {{$location->prior}}

            </td>
            <td class="center">

                {{ round(  $location->current - $location->prior, 2) }}
            </td>

            <td class="center">
                {{ round(100 * ($location->current - $location->prior) / ($location->prior == 0 ? 1 : $location->prior), 2) }}
         
            </td>

        </tr>





        @endforeach



        <tr>
            <th class="border-top">Report Total</th>
            <th class="border-top" id="current_total">{{$locations->current_total}}</th>
            <th class="border-top">{{$locations->prior_total}}</th>
            <th class="border-top">{{round($locations->current_total - $locations->prior_total,2)}}</th>
            <td class="border-top">
                {{ round(100 * ($locations->current_total - $locations->prior_total) / ($locations->prior_total == 0 ? 1 : $locations->prior_total), 2) }}
         
            </td>

        </tr>

        <tr>
            <th colspan="6" style="border:none;"></th>

        </tr>

        <tr style="display:none;">
            <th class="border-top">Area 9902 TOTES</th>

            <th colspan="5" class="border-top"></th>

        </tr>



    </tbody>
</table>





</div>
</div>

@endsection