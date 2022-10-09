@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')
@section('pageTitle', 'Timesheet List')
@section('content')
    <h3 class="page-title">Manage Timesheets
        @can('timesheet_edit')
            <a href="/export_timesheet_to_kronos" class="kronos_queue_btn btn btn-success pull-right <?=($pending_timesheets)?'disabled':''?>">Push To Kronos Queue</a>
        @endcan
        </h3>
    <div class="panel panel-default">
        <div class="panel-heading">Approved Timesheets</div>

        <div class="panel-body table-responsive">
            @if (Session::has('successmsg'))
                <div class="col-md-12 alert alert-success"> 
                    {{ Session::get('successmsg') }}
                </div>
            @endif
            <table class="table table-bordered table-striped {{ count($timesheets) > 0 ? 'datatable' : '' }} @can('timesheet_delete') @if ( request('show_deleted') != 1 ) dt-select @endif @endcan">
                <thead>
                    <tr>
                       <th style="text-align:center;"><input type="checkbox" id="select-all" /></th>
                        <th>Store Name</th>
                        <th>Event Date</th>
                        <th>Area</th>
                        <th>Run</th>
                        <th>Supervisor</th>
                        <th>Actions</th>
                   </tr>
                </thead>
                
                <tbody>
                    @if (count($timesheets) > 0)
                        @foreach ($timesheets as $timesheet)
                            <tr data-entry-id="{{ $timesheet->id }}">
                                @if ( request('show_deleted') != 1 )<td></td>@endif
                                <td>{{ @$timesheet->storename }}</td>
                                <td>{{ date('m-d-Y',strtotime(@$timesheet->dtJobDate)) }}</td>
                                <td>{{@$timesheet->area_number}}</td>
                                <td>{{ @$timesheet->run_number }}</td>
                                <td><?php echo @$timesheet->last_name.','.@$timesheet->first_name;?></td>
                                <td style="border-bottom: 1px solid;">
                                    @can('timesheet_view')
                                        <a href="{{ route('admin.timesheets.show',[$timesheet->id]) }}" class="btn btn-xs btn-primary" title="View Detail"><i class="fa fa-eye"></i></a>
                                        <a href="{{ route('admin.timesheets.restore',[$timesheet->id]) }}" class="btn btn-xs btn-info" title="Restore Timesheet"><i class="fa fa-edit"></i></a>
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
                        data: {'table':'time_entries_queue'},
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