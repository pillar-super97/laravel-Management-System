@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')
@section('pageTitle', 'User List')
@section('content')
    <h3 class="page-title">@lang('global.users.title')</h3>
    @can('user_create')
    <p>
        <a href="{{ route('admin.users.create') }}" class="btn btn-success">@lang('global.app_add_new')</a>

        <a href="{{ route('admin.users.invite.select_client') }}" class="btn btn-primary">Invite a Client's User</a>
        
    </p>
    @endcan
    <p>
        <ul class="list-inline">
            <li><a href="{{ route('admin.users.index') }}" style="{{ request('show_deleted') == 1 ? '' : 'font-weight: 700' }}">All</a></li> |
            <li><a href="{{ route('admin.users.index') }}?show_deleted=1" style="{{ request('show_deleted') == 1 ? 'font-weight: 700' : '' }}">Inactive</a></li>
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
            <table class="table table-bordered table-striped {{ count($users) > 0 ? 'datatable' : '' }} @can('user_delete') @if ( request('show_deleted') != 1 ) dt-select @endif @endcan">
                <thead>
                    <tr>
                        @can('user_delete')
                            @if ( request('show_deleted') != 1 )<th style="text-align:center;"><input type="checkbox" id="select-all" /></th>@endif
                        @endcan

                        <th>@lang('global.users.fields.name')</th>
                        <th>@lang('global.users.fields.email')</th>
                        <th>@lang('global.users.fields.role')</th>
                                                <th>&nbsp;</th>

                    </tr>
                </thead>
                
                <tbody>
                    @if (count($users) > 0)
                        @foreach ($users as $user)
                            <tr data-entry-id="{{ $user->id }}">
                                @can('user_delete')
                                    @if ( request('show_deleted') != 1 )<td></td>@endif
                                @endcan
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @foreach ($user->role as $singleRole)
                                        <span class="label label-info label-many">{{ $singleRole->title }}</span>
                                    @endforeach
                                </td>
                                @if( request('show_deleted') == 1 )
                                <td style="border-bottom: 1px solid;">
                                    {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'POST',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.users.restore', $user->id])) !!}
                                    {{ Form::button('<i class="fa fa-eye"></i>',['title'=>'Active User','class'=>'btn btn-success btn-xs','type'=>'submit'])}}
                                    {!! Form::close() !!}
                                                                    {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.users.perma_del', $user->id])) !!}
                                    {{ Form::button('<i class="fa fa-trash"></i>',['title'=>'Delete User','class'=>'btn btn-danger btn-xs','type'=>'submit'])}}
                                    {!! Form::close() !!}
                                                                </td>
                                @else
                                <td style="border-bottom: 1px solid;">
                                    @can('user_view')
                                    <a href="{{ route('admin.users.show',[$user->id]) }}" class="btn btn-xs btn-primary" title="View Detail"><i class="fa fa-eye"></i></a>
                                    @endcan
                                    @can('user_edit')
                                    <a href="{{ route('admin.users.edit',[$user->id]) }}" class="btn btn-xs btn-info" title="Edit User"><i class="fa fa-edit"></i></a>
                                    @endcan
                                    @can('user_delete')
{!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.users.destroy', $user->id])) !!}
                                    {{ Form::button('<i class="fa fa-trash"></i>',['title'=>'Make Inactive','class'=>'btn btn-danger btn-xs','type'=>'submit'])}}
                                    {!! Form::close() !!}
                                    @endcan
                                </td>
                                @endif
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="9">@lang('global.app_no_entries_in_table')</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('javascript') 
    <script>
        @can('user_delete')
            @if ( request('show_deleted') != 1 ) window.route_mass_crud_entries_destroy = '{{ route('admin.users.mass_destroy') }}'; @endif
        @endcan

    </script>
@endsection