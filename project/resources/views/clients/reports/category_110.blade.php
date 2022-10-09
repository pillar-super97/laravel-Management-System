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
            <th> Salesfloor</th>
            <th> Stockroom</th>

            <th>Current</th>

            <th>Prior</th>
            <th>Variance Amount</th>

        </tr>
    </thead>
    <tbody>


        @foreach($categories as $category)

        <tr>

            <td>
               
                {{$category->number}} {{$category->description}} 
            </td>


            <td class="left" >
            {{$category->sales_floor}}

            </td>

            <td class="left" >
            {{$category->stockroom}}

            </td>


            <td class="left" >
            {{$category->current}}
            </td>

            <td class="right" >

            {{$category->prior}}

            </td>


            <td class="center" >

            {{ round( $category->current - $category->prior, 2) }}
            </td>

        </tr>

        



        @endforeach



        <tr>
            <th class="border-top">Report Total</th>

            <th class="border-top" id="Salesfloor">{{$categories->sales_floor_total}}</th>

            <th class="border-top" id="Stockroom">{{$categories->stockroom_total}}</th>
            <th class="border-top" id="current_total">{{$categories->current_total}}</th>
            <th class="border-top">{{$categories->prior_total}}</th>
            <th class="border-top">{{round($categories->current_total - $categories->prior_total, 2)}}</th>

        </tr>

        <tr >
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