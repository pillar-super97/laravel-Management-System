@extends('layouts.app')
@section('pageTitle', 'Approved TimeSheet Absent Employees')
@section('content')
<?php



?>
<h3 class="page-title">Approved TimeSheet Absent Employees
</h3>

<div class="panel panel-default">
    <div class="panel-heading">
        Filter

        <a href="/admin/timesheets/excluded-absent-employees" class="pull-right">View excluded employees</a>
    </div>

    <div class="panel-body">
        <div class="row">

            <div class="col-xs-3 form-group">
                <label for="employee_number" class="control-label">Employee Number</label>
                <select id="employee_number" class="form-control employee_number select2" name="employee_number">
                    <option value="">Select Employee</option>
                    @foreach($employees as $employee)
                    <option value="{{$employee->emp_number}}">{{ $employee->emp_number }} {{ $employee->name }}</option>
                    @endforeach
                </select>
            </div>



            <div class="col-xs-3 form-group">
                <label for="area_name" class="control-label">Areas</label>
                <select id="area_name" class="form-control area_name select2" name="area_name">
                    <option value="">Select Areas</option>
                    @foreach($areas as $area)
                    <option value="{{$area->id}}">{{ $area->title }}</option>
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
                    <th>Employee ID</th>
                    <th>Event ID</th>
                    <th>Employee Number</th>
                    <th>Employee Name</th>

                    <th>Store Name</th>

                    <th>Event Date</th>
                    <th>Action</th>

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
                var employee_number = $('#employee_number').val();
                var area_id = $('#area_name').val();
                var date_between = $('.date_between').val();


                data.employee_number = employee_number;
                data.area_id = area_id;
                data.date_between = date_between;
            }
        },
        'columns': [{
                data: 'employee_id'
            },
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
                data: 'store_name'
            },
            {
                data: 'event_date'
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            },

        ],



    });


    $('#employee_number').change(function() {
        dataTable.draw();
    });

    $('#area_name').change(function() {
        dataTable.draw();
    })

    $('.date_between').change(function() {
        dataTable.draw();
    });

    $("#table").on("click", ".btn-exclude", function() {
        var row = [];
        $(this).closest('tr').find('td').each(function(index, td) {


            row.push($(td).text());
        });

        console.log(row);

        swal({
                text: `Exclude ${row[3]} for event #${row[0]}?`,
                icon: "warning",
                content: {
                    element: "input",
                    attributes: {
                        placeholder: "note/reason for excluding employee",
                        type: 'text',
                        required: 'required'

                    },
                },
                button: {
                    text: "Exclude",
                    closeModal: false,
                    className: "btn btn-primary",
                },
            })
            .then(note => {
                if(note===null) return false;
                if (!note) return swal("Error", "Please write reason of excluding", "error");

                return fetch('/admin/timesheets/exclude-absent-employee', {
                    method: 'post',
                    body: JSON.stringify({event_id: row[1], employee_id: row[0], note: note}),
                    headers: {
                        'Content-Type': 'application/json',
                        "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content')
                    }
                });

            })
            .then(results => {
               
                return results.json();
            })
            .then(json => {
                swal("Success", 'Excluded successfully', 'success');
                dataTable.draw();
            });



    });
   
    var today = new Date();
    today.setDate(today.getDate());
    //    $('.date_between').daterangepicker({autoUpdateInput: false,minDate:new Date()}
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