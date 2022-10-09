@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')
@section('pageTitle', 'District List')
@section('content')
    <h3 class="page-title">Manage Districts</h3>
    @can('district_create')
    <p>
        <a href="{{ route('admin.districts.create') }}" class="btn btn-success">@lang('global.app_add_new')</a>
        
    </p>
    @endcan

    <p>
        <ul class="list-inline">
            <li><a href="{{ route('admin.districts.index') }}" style="{{ request('show_deleted') == 1 ? '' : 'font-weight: 700' }}">All</a></li> |
            <li><a href="{{ route('admin.districts.index') }}?show_deleted=1" style="{{ request('show_deleted') == 1 ? 'font-weight: 700' : '' }}">Inactive</a></li>
        </ul>
    </p>
    

    <div class="panel panel-default">
        <div class="panel-heading">
            List of All Districts
        </div>

        <div class="panel-body table-responsive">
            @if (Session::has('successmsg'))
                <div class="col-md-12 alert alert-success"> 
                    {{ Session::get('successmsg') }}
                </div>
            @endif
            <table class="table table-bordered table-striped {{ count($districts) > 0 ? 'datatable' : '' }} @can('district_delete') @if ( request('show_deleted') != 1 ) dt-select @endif @endcan">
                <thead>
                    <tr>
                        @can('district_delete')
                            @if ( request('show_deleted') != 1 )<th style="text-align:center;"><input type="checkbox" id="select-all" /></th>@endif
                        @endcan
                        <th>Client Name</th>
                        <th>Division</th>
                        <th>District Number</th>
                        <th>Manager</th>
                        <th>Stores #</th>
                        <th>Actions</th>
                   </tr>
                </thead>
                <tbody>
                    @if (count($districts) > 0)
                        @foreach ($districts as $district)
                            <tr data-entry-id="{{ $district->id }}">
                                @can('district_delete')
                                    @if ( request('show_deleted') != 1 )<td></td>@endif
                                @endcan
                                <td>{{ @$district->clientname }}</td>
                                <td>{{ @$district->divisionname }}</td>
                                <td>{{ @$district->number }}</td>
                                <td>{{ @$district->manager }}</td>
                                <td>{{ district_store_count($district->id) }}</td>
                                @if( request('show_deleted') == 1 )
                                <td style="border-bottom: 1px solid;">
                                    {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'POST',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.districts.restore', $district->id])) !!}
                                    {{ Form::button('<i class="fa fa-eye"></i>',['title'=>'Active District','class'=>'btn btn-success btn-xs','type'=>'submit'])}}
                                    {!! Form::close() !!}
                                                                    {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.districts.perma_del', $district->id])) !!}
                                    {{ Form::button('<i class="fa fa-trash"></i>',['title'=>'Delete District','class'=>'btn btn-danger btn-xs','type'=>'submit'])}}
                                    {!! Form::close() !!}
                                                                </td>
                                @else
                                <td style="border-bottom: 1px solid;">
                                    @can('district_view')
                                        <a href="{{ route('admin.districts.show',[$district->id]) }}" class="btn btn-xs btn-primary" title="View Detail"><i class="fa fa-eye"></i></a>
                                    @endcan
                                    @can('district_edit')
                                        <a href="{{ route('admin.districts.edit',[$district->id]) }}" class="btn btn-xs btn-info" title="Edit District"><i class="fa fa-edit"></i></a>
                                    @endcan
                                    @can('district_delete')
{!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.districts.destroy', $district->id])) !!}
                                    {{ Form::button('<i class="fa fa-trash"></i>',['title'=>'Make Inactive','class'=>'btn btn-danger btn-xs','type'=>'submit'])}}
                                    {!! Form::close() !!}
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
    <script>
        @can('district_delete')
            @if ( request('show_deleted') != 1 ) window.route_mass_crud_entries_destroy = '{{ route('admin.districts.mass_destroy') }}'; @endif
        @endcan
    $(document).ready(function() {
//        var table = $('.datatable').dataTable();
//        table.order.neutral().draw();
    });
    </script>
@endsection