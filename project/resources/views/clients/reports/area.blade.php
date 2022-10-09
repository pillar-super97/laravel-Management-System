@extends('clients.reports.layout', ['title'=> 'MSI Inventory Service Corporation'])

@section('content')



<table class="my-3 sub-heading col-12 p-1">
    <tr>

        <td class=" align-top">
            {{ $event->store->name }}<br />
            {{ $event->store->address }}
        </td>
        <td class="text-center align-top sub-heading">[120] Area Report <br> Final</td>
        <td class="text-right">
            @if(@$event->schedule_employees[0]->employee->name)
            MSI Manager : {{ @$event->schedule_employees[0]->employee->name}} <br />
            @endif
            Store Manager : {{$event->store->manager_id}}
        </td>
    </tr>
</table>


<table class="table records my-3">
    <thead>
        <tr>
            <th>Location</th>
            <th>{{date('m/d/y', strtotime($event->date))}} Current</th>

            <th>Prior</th>
            <th>Variance Amount</th>
            <th>Variance %</th>
        </tr>
    </thead>
    <tbody>
        @php
        $current_price_total = 0.0;
        $prior_price_total = 0.0;
        @endphp

        @foreach($result as $cr)


        @php

        $current_price = $cr['current_price'] ?? 0.0;
        $current_price_total += $current_price;

        $prior_price = $cr['prior_price'] ?? 0.0;
        $prior_price_total += $prior_price;

        @endphp

        <tr>
            <td class="left strong">
                {{  $cr['location_id'] }} {{ $cr['location_description'] }}
            </td>
            <td class="left">
                {{ sprintf('%0.2f', $current_price) }}
            </td>

            <td class="right">
                {{  sprintf('%0.2f',$prior_price) }}
            </td>


            <td class="center">
                {{ round($current_price - $prior_price,2) }}
            </td>
            <td class="right">
                {{
                                    round(( ($current_price - $prior_price) / ($prior_price>0 ? $prior_price : 1) )*100 ,2) }}
            </td>

        </tr>
        @endforeach

        <tr>
            <th class="border-top">Report Total</th>


            <th class="border-top">{{ sprintf('%0.2f',$current_price_total)}}</th>

            <th class="border-top">{{ sprintf('%0.2f',$prior_price_total)}}</th>
            <th class="border-top">{{ sprintf('%0.2f', $current_price_total - $prior_price_total)}}</th>
            <th class="border-top">
                {{
                                round(( ($current_price_total - $prior_price_total) / ($prior_price_total>0 ? $prior_price_total : 1) )*100 ,2) 
                                }}
            </th>
        </tr>



    </tbody>
</table>





@endsection