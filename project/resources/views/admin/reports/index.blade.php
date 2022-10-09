@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')
@section('pageTitle', 'Report List')
@section('content')
    
    @can('report_create')
    <p>
        <a href="{{ route('admin.reports.create') }}" class="btn btn-success">@lang('global.app_add_new')</a>
        
    </p>
    @endcan

    <p>
        <ul class="list-inline">
            <li><a href="{{ route('admin.reports.index') }}" style="{{ request('show_deleted') == 1 ? '' : 'font-weight: 700' }}">All</a></li> |
            <li><a href="{{ route('admin.reports.index') }}?show_deleted=1" style="{{ request('show_deleted') == 1 ? 'font-weight: 700' : '' }}">Inactive</a></li>
        </ul>
    </p>
    
    <div class="panel panel-default">
        <div class="panel-heading">
            List of All Reports
        </div>

        <div class="panel-body table-responsive">
            @if (Session::has('successmsg'))
                <div class="col-md-12 alert alert-success"> 
                    {{ Session::get('successmsg') }}
                </div>
            @endif
            <table class="table table-bordered table-striped {{ count($reports) > 0 ? 'datatable' : '' }} @can('report_delete') @if ( request('show_deleted') != 1 ) dt-select @endif @endcan">
                <thead>
                    <tr>
                        @can('report_delete')
                            @if ( request('show_deleted') != 1 )<th style="text-align:center;"><input type="checkbox" id="select-all" /></th>@endif
                        @endcan
                        <th>Report Name</th>
                        <th>File Name</th>
                        <th>Status</th>
                        <th>Actions</th>
                   </tr>
                </thead>
                
                <tbody>
                    @if (count($reports) > 0)
                        @foreach ($reports as $report)
                            <tr data-entry-id="{{ $report->id }}">
                                @can('report_delete')
                                    @if ( request('show_deleted') != 1 )<td></td>@endif
                                @endcan
                                <td>{{ @$report->rpt_name }}</td>
                                <td>{{ @$report->rpt_file_name }}</td>
                                <td><?=@$report->status;?> </td>
                                @if( request('show_deleted') == 1 )
                                <td style="border-bottom: 1px solid;">
                                    {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'POST',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.reports.restore', $report->id])) !!}
                                    {{ Form::button('<i class="fa fa-eye"></i>',['title'=>'Active Report','class'=>'btn btn-success btn-xs','type'=>'submit'])}}
                                    {!! Form::close() !!}
                                                                    {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.reports.perma_del', $report->id])) !!}
                                    {{ Form::button('<i class="fa fa-trash"></i>',['title'=>'Delete Report','class'=>'btn btn-danger btn-xs','type'=>'submit'])}}
                                    {!! Form::close() !!}
                                                                </td>
                                @else
                                <td style="border-bottom: 1px solid;">
                                    @can('report_view')
                                        <a href="{{ route('admin.reports.show',[$report->id]) }}" class="btn btn-xs btn-primary" title="View Detail"><i class="fa fa-eye"></i></a>
                                    @endcan
                                    @can('report_edit')
                                        <a href="{{ route('admin.reports.edit',[$report->id]) }}" class="btn btn-xs btn-info" title="Edit Report"><i class="fa fa-edit"></i></a>
                                    @endcan
                                    @can('report_view')
                                        <a href="{{ route('admin.reports.view_crystal_report',$report->id) }}" class="btn btn-xs btn-info crystal" report_id="{{$report->id}}" title="View in Crystal Report"><i class="fa fa-calendar"></i></a>
                                    @endcan
                                    @can('report_edit')
                                    <a href="{{ route('admin.reports.assignusers',$report->id) }}" class="btn btn-xs btn-info" title="Give Report Permission"><i class="fa fa-users"></i></a>
                                    @endcan
                                </td>
                               @endif
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
        @can('area_delete')
            @if ( request('show_deleted') != 1 ) window.route_mass_crud_entries_destroy = '{{ route('admin.reports.mass_destroy') }}'; @endif
        @endcan
    $(document).ready(function(){
        $(document).on('click','.crystal1',function(){
            var report_id = $(this).attr('report_id');
            if(report_id)
            {
                $.ajax({
               type:"GET",
               url:"{{url('admin/reports/')}}/"+report_id,
               success:function(res){
                    //console.log(res);
                    var win = window.open('https://reports.msi-inv.com/Report.aspx?token='+res.token+'&user='+res.user_id+'&filename='+res.filename, '_blank');
                    if (win) {
                        //Browser has allowed it to be opened
                        win.focus();
                    } else {
                        //Browser has blocked it
                        alert('Please allow popups for this website');
                    }
                    
               }
            });
            }
        });
    })
</script>
@endsection