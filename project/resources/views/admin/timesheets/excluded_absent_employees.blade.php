@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')
@section('pageTitle', 'Excluded Absent Employees')
@section('content')
<?php



?>
<h3 class="page-title">Excluded Absent Employees
</h3>

<div class="panel panel-default">
    <div class="panel-heading">
        Filter
        <a href="/admin/timesheets/absent-employees" class="pull-right">View absent employees</a>
    </div>

    <div class="panel-body">
        <div class="row">

            <div class="col-xs-3 form-group">
                <label for="employee_number" class="control-label">Employee Number</label>
                <select id="employee_id" class="form-control employee_number select2" name="employee_id">
                    <option value="">Select Employee</option>
                    @foreach($employees as $employee)
                    <option value="{{$employee->id}}">{{ $employee->emp_number }} {{ $employee->name }}</option>
                    @endforeach
                </select>
            </div>


            <div class="col-xs-3 form-group">
                {!! Form::label('date_between', 'Date', ['class' => 'control-label required']) !!}
                {!! Form::text('date_between', $date_between , ['class' => 'form-control
                date_between','autocomplete'=>'off', 'placeholder' => '', 'readonly'=>true]) !!}
            </div>


        </div>

    </div>
</div>

<div class="panel panel-default">


    <div class="panel-body table-responsive">

        <table class="table table-bordered table-striped" id="table">
            <thead>
                <tr>
                    <th>Event ID</th>
                    <th>Employee Number</th>
                    <th>Employee Name</th>

                    <th>Note</th>

                    <th>Excluded On</th>

                </tr>
            </thead>
            <tbody>

            </tbody>



        </table>
    </div>
</div>
@stop

@section('javascript')
<script type="text/javascript">
$(document).ready(function() {
    var dataTable = $('#table').DataTable({
        "aLengthMenu": [
            [25, 50, 75, 100, 500, 1000, -1],
            [25, 50, 75, 100, 500, 1000, "All"]
        ],
        "iDisplayLength": 25,
        'destroy': true,
        processing: true,
        serverSide: true,
        'serverMethod': 'POST',
        'ajax': {
            'url': '',
            'headers': {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            'data': function(data) {
                // Read values
                var employee_id = $('#employee_id').val();
                var date_between = $('.date_between').val();


                data.employee_id = employee_id;
                data.date_between = date_between;
            }
        },
        'columns': [
            {
                data: 'event_id'
            },
            {
                data: 'employee_number'
            },
            {
                data: 'employee_name'
            },
            {
                data: 'note',
                orderable: false,
                searchable: false
            },
            {
                data: 'created_at'
            }

        ],



    });


    $('#employee_id').change(function() {
        dataTable.draw();
    });

    $('.date_between').change(function() {
        dataTable.draw();
    });

    
    var today = new Date();
    today.setDate(today.getDate());
    
    $('.date_between').daterangepicker({
        autoUpdateInput: false,
        maxDate:today,
    }, function(start_date, end_date) {
        $('.date_between').val(start_date.format('MM/DD/YYYY') + ' - ' + end_date.format('MM/DD/YYYY'));
        dataTable.draw();
    });


});
</script>
@endsection