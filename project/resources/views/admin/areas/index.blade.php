@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')
@section('pageTitle', 'Area List')
@section('content')
    <h3 class="page-title">Manage Areas</h3>
    @can('area_create')
    <p>
        <a href="{{ route('admin.areas.create') }}" class="btn btn-success">@lang('global.app_add_new')</a>
        
    </p>
    @endcan

    <p>
        <ul class="list-inline">
            <li><a href="{{ route('admin.areas.index') }}" style="{{ request('show_deleted') == 1 ? '' : 'font-weight: 700' }}">All</a></li> |
            <li><a href="{{ route('admin.areas.index') }}?show_deleted=1" style="{{ request('show_deleted') == 1 ? 'font-weight: 700' : '' }}">Inactive</a></li>
        </ul>
    </p>
    

    <div class="panel panel-default">
        <div class="panel-heading">
            List of All Areas
        </div>

        <div class="panel-body table-responsive">
            @if (Session::has('successmsg'))
                <div class="col-md-12 alert alert-success"> 
                    {{ Session::get('successmsg') }}
                </div>
            @endif
            <table class="table table-bordered table-striped {{ count($areas) > 0 ? 'datatable' : '' }} @can('area_delete') @if ( request('show_deleted') != 1 ) dt-select @endif @endcan">
                <thead>
                    <tr>
                        @can('area_delete')
                            @if ( request('show_deleted') != 1 )<th style="text-align:center;"><input type="checkbox" id="select-all" /></th>@endif
                        @endcan

                      
                        <th>Title</th>
                        <th>Area #</th>
                        <th>City</th>
                        <th>State</th>
                        <th>Actions</th>
                   </tr>
                </thead>
                
                <tbody>
                    @if (count($areas) > 0)
                        @foreach ($areas as $area)
                            <tr data-entry-id="{{ $area->id }}">
                                @can('area_delete')
                                    @if ( request('show_deleted') != 1 )<td></td>@endif
                                @endcan
                                <td>{{ $area->title }}</td>
                                <td>{{ $area->area_number }}</td>
                                <td>{{ @$area->city->name }}</td>
                                <td>{{ @$area->state->state_code }}</td>
                                @if( request('show_deleted') == 1 )
                                <td style="border-bottom: 1px solid;">
                                    {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'POST',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.areas.restore', $area->id])) !!}
                                    {{ Form::button('<i class="fa fa-eye"></i>',['title'=>'Active Area','class'=>'btn btn-success btn-xs','type'=>'submit'])}}
                                    {!! Form::close() !!}
                                                                    {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.areas.perma_del', $area->id])) !!}
                                    {{ Form::button('<i class="fa fa-trash"></i>',['title'=>'Delete Area','class'=>'btn btn-danger btn-xs','type'=>'submit'])}}
                                    {!! Form::close() !!}
                                                                </td>
                                @else
                                <td style="border-bottom: 1px solid;">
                                    @can('area_view')
                                        <a href="{{ route('admin.areas.show',[$area->id]) }}" class="btn btn-xs btn-primary"title="View Detail"><i class="fa fa-eye"></i></a>
                                    @endcan
                                    @can('area_edit')
                                        <a href="{{ route('admin.areas.edit',[$area->id]) }}" class="btn btn-xs btn-info"title="Edit Area"><i class="fa fa-edit"></i></a>
                                    @endcan
                                    @can('area_delete')
                                    {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.areas.destroy', $area->id])) !!}
                                    {{ Form::button('<i class="fa fa-trash"></i>',['title'=>'Make Inactive','class'=>'btn btn-danger btn-xs','type'=>'submit'])}}
                                    {!! Form::close() !!}
                                    @endcan
                                    <a href="{{ route('admin.areas.jsa.index',[$area->id]) }}" class="btn btn-xs btn-primary" title="Manage JSA Area"><i class="fa fa-list"></i></a>
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
        @can('area_delete')
            @if ( request('show_deleted') != 1 ) window.route_mass_crud_entries_destroy = '{{ route('admin.areas.mass_destroy') }}'; @endif
        @endcan

    </script>
@endsection