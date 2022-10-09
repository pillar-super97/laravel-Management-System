@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')
@section('pageTitle', 'Weekly Projected Hours')
@section('content')

<h3 class="page-title">Weekly Projected Hours
</h3>

<div class="panel panel-default">
    <div class="panel-heading">
        Filter

    </div>

    <div class="panel-body">
        <div class="row">

            <form method="GET" action="">
                <div class="col-xs-3 form-group">
                    <label for="area_number" class="control-label required">Area</label>
                    <select id="area_id" class="form-control area_number select2" name="area_id" required>
                        <option value="">Select area</option>
                        @foreach($areas as $area)
                        <option value="{{$area->id}}" {{ $area->id==$selected_area_id? "selected" : '' }}>
                            {{ $area->title }}</option>
                        @endforeach
                    </select>
                </div>


                <div class="col-xs-3 form-group">
                    {!! Form::label('start_date', 'Date', ['class' => 'control-label required']) !!}
                    {!! Form::text('start_date', $start_date , ['class' => 'form-control
                    start_date','autocomplete'=>'off', 'placeholder' => '']) !!}
                </div>


                <div class="col-xs-3 form-group">
                    <label for="filter" class="control-label"> &nbsp;</label>
                    <input id="filter" type="submit" class="btn btn-primary form-control" value="Filter" />
                </div>



            </form>


        </div>

    </div>
</div>

@if(!count($events) && $selected_area_id)
<div class="panel panel-default">


    <div class="panel-body">
        <h2>Record not found</h2>

                    </div>
                    </div>
@endif

@if(count($events))

<div class="panel panel-default">


    <div class="panel-body table-responsive">

        <table class="table table-bordered table-striped" id="table">
            <thead>

                <tr>
                    <th>Employee</th>
                    @foreach($events as $event)
                    @php
                    $stores[] = $event->id;
                    $total_count[$event->id] = 0;
                    @endphp
                    <th>{{$event->date}} <br>{{$event->store->name}} </th>
                    @endforeach
                    <th> Total Hours Scheduled</th>

                </tr>
            </thead>
            <tbody>
                @foreach($employees as $employee)

                <tr>

                    <td>{{$employee['name']}}</td>
                    @foreach($stores as $store)



                    @if(@$employee[$store])

                    @php
                    $total_count[$store]++;
                    @endphp

                    <td
                        title="{{@$employee[$store]['task']}}"
                        class="schedule_length {{ @$employee[$store]['css_class'] }}">
                        {{ @$employee[$store]['hours'] }}
                    </td>
                    @else
                    <td></td>
                    @endif



                    @endforeach
                    <td class="total_length"></td>
                </tr>

                @endforeach
            </tbody>



            <thead>

                <tr>
                    <th>Ttl Ctrs Scheduled</th>
                    @foreach($total_count as $count)

                    <td> {{$count}}</td>
                    @endforeach
                    <td></td>

                </tr>
            </thead>



        </table>
        <br><br>
        <p><span class="label label-default bg-red">Red</span> => Superviser, Super/Driver To, Super/Driver From, Super/Driver To & From</p>
        <p><span class="label label-default bg-yellow">Yellow</span> => RX, RX/Driver To, RX/Driver From, RX/Driver To & From</p>
    </div>

</div>

@endif
@stop

@section('javascript')
<script type="text/javascript">
$(document).ready(function() {
    var before7 = new Date();
    before7.setDate(before7.getDate() - 7);
    $('.start_date').daterangepicker({

        "singleDatePicker": true,
        // "startDate": "06/29/2022",
        // "endDate": "07/05/2022"
        minDate: before7,
    });

    $('.total_length').each(function() {
        var total = 0.0;
        $(this).parent().find('td.schedule_length').each(function() {
            total = total + parseFloat($(this).text());
        });

        if(!isNaN(total)) $(this).text(total.toFixed(1));
    });

});
</script>
@endsection