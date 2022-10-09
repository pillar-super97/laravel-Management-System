@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')
@section('pageTitle', 'Permission List')
@section('content')
    <h3 class="page-title">@lang('global.permissions.title')</h3>
    @can('permission_create')
    <p>
        <a href="{{ route('admin.permissions.create') }}" class="btn btn-success">@lang('global.app_add_new')</a>
        
    </p>
    @endcan
    <p>
        <ul class="list-inline">
            <li><a href="{{ route('admin.permissions.index') }}" style="{{ request('show_deleted') == 1 ? '' : 'font-weight: 700' }}">All</a></li> |
            <li><a href="{{ route('admin.permissions.index') }}?show_deleted=1" style="{{ request('show_deleted') == 1 ? 'font-weight: 700' : '' }}">Inactive</a></li>
        </ul>
    </p>
    

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_list')
        </div>

        <div class="panel-body table-responsive">
            @if (Session::has('successmsg'))
                <div class="col-md-12 alert alert-success"> 
                    {{ Session::get('successmsg') }}
                </div>
            @endif
            <table class="table table-bordered table-striped {{ count($permissions) > 0 ? 'datatable' : '' }} @can('permission_delete') @if ( request('show_deleted') != 1 ) dt-select @endif @endcan">
                <thead>
                    <tr>
                        @can('permission_delete')
                            @if ( request('show_deleted') != 1 )<th style="text-align:center;"><input type="checkbox" id="select-all" /></th>@endif
                            
                        @endcan

                        <th>@lang('global.permissions.fields.title')</th>
                                                <th>&nbsp;</th>

                    </tr>
                </thead>
                
                <tbody>
                    @if (count($permissions) > 0)
                        @foreach ($permissions as $permission)
                            <tr data-entry-id="{{ $permission->id }}">
                                @can('permission_delete')
                                    @if ( request('show_deleted') != 1 )<td></td>@endif
                                @endcan
                                <td>{{ $permission->title }}</td>
                                @if( request('show_deleted') == 1 )
                                <td style="border-bottom: 1px solid;">
                                    {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'POST',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.permissions.restore', $permission->id])) !!}
                                    {{ Form::button('<i class="fa fa-eye"></i>',['title'=>'Active Permission','class'=>'btn btn-success btn-xs','type'=>'submit'])}}
                                    {!! Form::close() !!}
                                                                    {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.permissions.perma_del', $permission->id])) !!}
                                    {{ Form::button('<i class="fa fa-trash"></i>',['title'=>'Delete Permission','class'=>'btn btn-danger btn-xs','type'=>'submit'])}}
                                    {!! Form::close() !!}
                                                                </td>
                                @else
                                <td style="border-bottom: 1px solid;">
                                    @can('permission_view')
                                    <a href="{{ route('admin.permissions.show',[$permission->id]) }}" class="btn btn-xs btn-primary" title="View Detail"><i class="fa fa-eye"></i></a>
                                    @endcan
                                    @can('permission_edit')
                                    <a href="{{ route('admin.permissions.edit',[$permission->id]) }}" class="btn btn-xs btn-info" title="Edit Permission"><i class="fa fa-edit"></i></a>
                                    @endcan
                                    @can('permission_delete')
{!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.permissions.destroy', $permission->id])) !!}
                                    {{ Form::button('<i class="fa fa-trash"></i>',['title'=>'Make Inactive','class'=>'btn btn-danger btn-xs','type'=>'submit'])}}
                                    {!! Form::close() !!}
                                    @endcan
                                </td>
                                @endif
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5">@lang('global.app_no_entries_in_table')</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('javascript') 
    <script>
        @can('permission_delete')
            @if ( request('show_deleted') != 1 ) window.route_mass_crud_entries_destroy = '{{ route('admin.permissions.mass_destroy') }}'; @endif
        @endcan

    </script>
@endsection