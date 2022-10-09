@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')
@section('pageTitle', 'Employee List')
@section('content')
    <h3 class="page-title">Manage Employees
        <?php if (Gate::allows('isAdmin') || Gate::allows('isCorporate')){?>
            <a href="/importEmployees" class="btn btn-success pull-right">Import Employees From Kronos</a>
            <a href="/updateFlaggedTimesheet" class="btn btn-success pull-right" style="margin-right: 5px;">Update Flagged Timesheet</a>
            <a href="/export_schedules_to_kronos" class="kronos_queue_btn btn btn-success pull-right <?=($pending_timesheets)?'disabled':''?>" style="margin-right: 5px;">Push Employee Schedule to Kronos</a>
            <a href="/sync_schedules_to_kronos" class="btn btn-success pull-right" style="margin-right: 5px;">Sync Employee Schedule with Kronos</a>
        <?php }?>
    </h3>
<!--    @can('employee_create')
    <p>
        <a href="{{ route('admin.employees.create') }}" class="btn btn-success">@lang('global.app_add_new')</a>
        
    </p>
    @endcan

    <p>
        <ul class="list-inline">
            <li><a href="{{ route('admin.employees.index') }}" style="{{ request('show_deleted') == 1 ? '' : 'font-weight: 700' }}">All</a></li> |
            <li><a href="{{ route('admin.employees.index') }}?show_deleted=1" style="{{ request('show_deleted') == 1 ? 'font-weight: 700' : '' }}">Inactive</a></li>
        </ul>
    </p>-->
    

    <div class="panel panel-default">
        <div class="panel-heading">
            List of All Employees
        </div>

        <div class="panel-body table-responsive">
            @if (Session::has('successmsg'))
                <div class="col-md-12 alert alert-success"> 
                    {{ Session::get('successmsg') }}
                </div>
            @endif
            <table class="table table-bordered table-striped {{ count($employees) > 0 ? 'datatable' : '' }} @can('employee_delete') @if ( request('show_deleted') != 1 ) dt-select @endif @endcan">
                <thead>
                    <tr>
                        @can('employee_delete')
                            @if ( request('show_deleted') != 1 )<th style="text-align:center;"><input type="checkbox" id="select-all" /></th>@endif
                        @endcan

                        <th>Emp. Number</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone #</th>
                        <th>Area</th>
                        <th>Title</th>
                        <th>Benchmark</th>
                        <th>Driver</th>
                        <th>Crew Leader</th>
                        <th>RX</th>
                        <th>Overnight</th>
                        <th>Availability Days</th>
                        <th>Status</th>
                        <th>Actions</th>
                   </tr>
                </thead>
                
                <tbody>
                    @if (count($employees) > 0)
                        @foreach ($employees as $employee)
                            <tr data-entry-id="{{ $employee->id }}">
                                @can('employee_delete')
                                    @if ( request('show_deleted') != 1 )<td></td>@endif
                                @endcan
                                <td>{{ @$employee->emp_number }}</td>
                                <td>{{ @$employee->last_name.' '.@$employee->first_name }}</td>
                                <td>{{ @$employee->email }}</td>
                                <td>{{ @$employee->cell_phone}}</td>
                                <td>{{ @$employee->area->area_number }}</td>
                                <td>{{ @$employee->title }}</td>
                                <td><?php if($employee->benchmark){
                                    echo $employee->benchmark.' %';
                                //echo '<a href="#" class="pull-right" title="Update Benchmark"><i class="fa fa-edit"></i></a>';
                                    }?> </td>
                                <td><?=($employee->is_driver)?'Yes':'';?> </td>
                                <td><?=($employee->is_crew_leader)?'Yes':'';?> </td>
                                <td><?=($employee->is_rx)?'Yes':'';?> </td>
                                <td><?=($employee->overnight==0)?'No':'';?> </td>
                                <td><?php foreach($employee->availability_days as $days){echo ' <span class="badge">'.$days->days.'</span>';}?></td>
                                <td><?=@$employee->status;?> </td>
                                <td style="border-bottom: 1px solid;">
                                    @can('employee_view')
                                        <a href="{{ route('admin.employees.show',[$employee->id]) }}" class="btn btn-xs btn-primary" title="View Detail"><i class="fa fa-eye"></i></a>
                                    @endcan
                                    @can('employee_edit')
                                        <a href="{{ route('admin.employees.edit',[$employee->id]) }}" class="btn btn-xs btn-info" title="Edit Employee"><i class="fa fa-edit"></i></a>
                                    @endcan
                                   
                                </td>
                               
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="14">@lang('global.app_no_entries_in_table')</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('javascript') 
    <script type="text/javascript">
    $(document).ready(function(){
        if($('.kronos_queue_btn').hasClass('disabled'))
        {
            setInterval(function() {
                $.ajax({
                        url: "/admin/kronos_queue_status",
                        data: {'table':'employees_schedule_kronos_queue'},
                        type: "POST",
                        'headers': {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                       if(data==0)
                        $('.kronos_queue_btn').removeClass('disabled');
                    }
                });
            }, 6000);
        }
    })
</script>
@endsection