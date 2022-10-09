@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')
@section('pageTitle', 'Timesheet List')
@section('content')
    <h3 class="page-title">Time Entries Log
        @can('timesheet_edit')
            <a href="/import_time_entries_manually" class="btn btn-success pull-right">Import Time Entries</a>
        @endcan
        </h3>

    <div class="panel panel-default">
        <div class="panel-heading">
           Time Entries Log

           <a href="/admin/log/timesheet/" class="pull-right text-danger">View Error Log</a>
           
        </div>

        <div class="panel-body table-responsive">
            @if (Session::has('successmsg'))
                <div class="col-md-12 alert alert-success"> 
                    {{ Session::get('successmsg') }}
                </div>
            @endif
            <table class="table table-bordered table-striped {{ count($timeentries) > 0 ? 'datatable' : '' }} @can('timesheet_delete') @if ( request('show_deleted') != 1 ) dt-select @endif @endcan">
                <thead>
                    <tr>
                       <th>ID</th>
                        <th>Date Imported Up To</th>
                        <th>Imported On</th>
                   </tr>
                </thead>
                
                <tbody>
                    @if (count($timeentries) > 0)
                        @foreach ($timeentries as $timeentry)
                            <tr data-entry-id="{{ $timeentry->id }}">
                                <td>{{$timeentry->id}}</td>
                                <td><?php echo conver_to_time($timeentry->import_upto,'CST','UTC');?></td>
                                <td><?php echo conver_to_time($timeentry->imported_on,'CST','UTC');?></td>
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