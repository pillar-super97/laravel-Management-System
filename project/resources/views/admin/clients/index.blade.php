@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')
@section('pageTitle', 'Client List')
@section('content')
    <h3 class="page-title">Manage Clients</h3>
    @can('client_create')
    <p>
        <a href="{{ route('admin.clients.create') }}" class="btn btn-success">@lang('global.app_add_new')</a>
        
    </p>
    @endcan

    <p>
        <ul class="list-inline">
            <li><a href="{{ route('admin.clients.index') }}" style="{{ request('show_deleted') == 1 ? '' : 'font-weight: 700' }}">All</a></li> |
            <li><a href="{{ route('admin.clients.index') }}?show_deleted=1" style="{{ request('show_deleted') == 1 ? 'font-weight: 700' : '' }}">Inactive</a></li>
        </ul>
    </p>
    

    <div class="panel panel-default">
        <div class="panel-heading">
            List of All Clients
        </div>

        <div class="panel-body table-responsive">
            @if (Session::has('successmsg'))
                <div class="col-md-12 alert alert-success"> 
                    {{ Session::get('successmsg') }}
                </div>
            @endif
            <table class="table table-bordered table-striped {{ count($clients) > 0 ? 'datatable' : '' }} @can('client_delete') @if ( request('show_deleted') != 1 ) dt-select @endif @endcan">
                <thead>
                    <tr>
                        @can('client_delete')
                            @if ( request('show_deleted') != 1 )<th style="text-align:center;"><input type="checkbox" id="select-all" /></th>@endif
                        @endcan

                      
                        <th>Name</th>
                        <th>City</th>
                        <th>State</th>
                        <th>No. of Stores</th>
                        <th>Actions</th>
                   </tr>
                </thead>
                
                <tbody>
                    @if (count($clients) > 0)
                        @foreach ($clients as $client)
                            <tr data-entry-id="{{ $client->id }}">
                                @can('client_delete')
                                    @if ( request('show_deleted') != 1 )<td></td>@endif
                                @endcan
                                <td>{{ $client->name }}</td>
                                <td>{{ @$client->city->name }}</td>
                                <td>{{ @$client->state->name }}</td>
                                <td>{{ client_store_count($client->id) }}</td>
                                @if( request('show_deleted') == 1 )
                                <td style="border-bottom: 1px solid;">
                                    {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'POST',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.clients.restore', $client->id])) !!}
                                    {{ Form::button('<i class="fa fa-eye"></i>',['title'=>'Active Client','class'=>'btn btn-success btn-xs','type'=>'submit'])}}
                                    {!! Form::close() !!}
                                                                    {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.clients.perma_del', $client->id])) !!}
                                    {{ Form::button('<i class="fa fa-trash"></i>',['title'=>'Delete Client','class'=>'btn btn-danger btn-xs','type'=>'submit'])}}
                                    {!! Form::close() !!}
                                                                </td>
                                @else
                                <td style="border-bottom: 1px solid;">
                                    @can('client_view')
                                        <a href="{{ route('admin.clients.show',[$client->id]) }}" class="btn btn-xs btn-primary" title="View Detail"><i class="fa fa-eye"></i></a>
                                    @endcan
                                    @can('client_edit')
                                        <a href="{{ route('admin.clients.edit',[$client->id]) }}" class="btn btn-xs btn-info" title="Edit Client"><i class="fa fa-edit"></i></a>
                                    @endcan
                                    @can('client_edit')
                                        <a href="{{ route('admin.users.invite_client', $client->id) }}" class="btn btn-xs btn-warning" title="Invite"><i class="fa fa-user-plus"></i></a>
                                    @endcan
                                    @can('client_delete')
{!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.clients.destroy', $client->id])) !!}
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
        @can('client_delete')
            @if ( request('show_deleted') != 1 ) window.route_mass_crud_entries_destroy = '{{ route('admin.clients.mass_destroy') }}'; @endif
        @endcan

    </script>
@endsection