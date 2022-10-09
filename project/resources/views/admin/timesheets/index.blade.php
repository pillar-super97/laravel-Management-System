@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')
@section('pageTitle', 'Timesheet List')
@section('content')
    <h3 class="page-title">Manage Timesheets
        @can('timesheet_edit')
            <a href="/admin/get-timeentries-status" class="btn btn-success pull-right">Sync with Kronos</a>
        @endcan
        </h3>
    
    <div class="panel panel-default">
        <div class="panel-heading">
           Timesheets 
           
        </div>

        <div class="panel-body table-responsive">
            @if (Session::has('successmsg'))
                <div class="col-md-12 alert alert-success"> 
                    {{ Session::get('successmsg') }}
                </div>
            @endif
            @if (Session::has('warningmsg'))
                <div class="col-md-12 alert alert-warning"> 
                    {{ Session::get('warningmsg') }}
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
                        <th>Is Flagged</th>
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
                                <td><?php //$supervisor = event_supervisor($timesheet->id)?>
                                    {{@$timesheet->area_number}}</td>
                                <td>{{ @$timesheet->run_number }}</td>
                                <td><?php echo @$timesheet->last_name.','.@$timesheet->first_name;?></td>
                                <td><?php if($timesheet->is_flagged){echo '<span class="badge badge-danger">Yes</span>';}else{echo 'No';}?></td>
                                <td style="border-bottom: 1px solid;">
                                    @can('timesheet_view')
<!--                                        <a href="{{ route('admin.timesheets.show',[$timesheet->id]) }}" class="btn btn-xs btn-primary" title="View Detail"><i class="fa fa-eye"></i></a>-->
                                    @endcan
                                    @can('timesheet_edit')
                                        <a href="{{ route('admin.timesheets.approval',[$timesheet->id]) }}" class="btn btn-xs btn-info" title="Review Timesheet"><i class="fa fa-edit"></i></a>
                                    @endcan
                                    @can('timesheet_delete')
{!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.timesheets.destroy', $timesheet->id])) !!}
                                    {{ Form::button('<i class="fa fa-trash"></i>',['title'=>'Make Inactive','class'=>'btn btn-danger btn-xs','type'=>'submit'])}}
                                    {!! Form::close() !!}
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
    <script>
       
    </script>
@endsection