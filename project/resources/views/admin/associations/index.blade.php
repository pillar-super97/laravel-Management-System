@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')
@section('pageTitle', 'Association List')
@section('content')
    <h3 class="page-title">Manage Associations</h3>
    @can('association_create')
    <p>
        <a href="{{ route('admin.associations.create') }}" class="btn btn-success">@lang('global.app_add_new')</a>
        
    </p>
    @endcan

    <p>
        <ul class="list-inline">
            <li><a href="{{ route('admin.associations.index') }}" style="{{ request('show_deleted') == 1 ? '' : 'font-weight: 700' }}">All</a></li> |
            <li><a href="{{ route('admin.associations.index') }}?show_deleted=1" style="{{ request('show_deleted') == 1 ? 'font-weight: 700' : '' }}">Inactive</a></li>
        </ul>
    </p>
    

    <div class="panel panel-default">
        <div class="panel-heading">
            List of All Associations
        </div>
        <div class="panel-body table-responsive">
            @if (Session::has('successmsg'))
                <div class="col-md-12 alert alert-success"> 
                    {{ Session::get('successmsg') }}
                </div>
            @endif
            <table class="table table-bordered table-striped {{ count($associations) > 0 ? 'datatable' : '' }} @can('association_delete') @if ( request('show_deleted') != 1 ) dt-select @endif @endcan">
                <thead>
                    <tr>
                        @can('association_delete')
                            @if ( request('show_deleted') != 1 )
                                <th style="text-align:center;"><input type="checkbox" id="select-all" /></th>
                            @endif
                        @endcan

                      
                        <th>Name</th>
                        <th>City</th>
                        <th>State</th>
                        <th>Phone Number</th>
                        <th>Primary Contact</th>
                        <th>Stores #</th>
                        <th>Actions</th>
                   </tr>
                </thead>
                
                <tbody>
                    @if (count($associations) > 0)
                        @foreach ($associations as $association)
                            <tr data-entry-id="{{ $association->id }}">
                                @can('association_delete')
                                    @if ( request('show_deleted') != 1 )<td></td>
                                    @endif
                                @endcan
                                <td>{{ $association->name }}</td>
                                <td>{{ @$association->city->name }}</td>
                                <td>{{ @$association->state->name }}</td>
                                <td>{{ $association->phone }}</td>
                                <td>{{ $association->primary_contact_name }}</td>
                                <td>{{ association_store_count($association->id) }}</td>
                                @if( request('show_deleted') == 1 )
                                    <td style="border-bottom: 1px solid;">
                                        {!! Form::open(array(
                                            'style' => 'display: inline-block;',
                                            'method' => 'POST',
                                            'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                            'route' => ['admin.associations.restore', $association->id])) !!}
                                        {{ Form::button('<i class="fa fa-eye"></i>',['title'=>'Active Association','class'=>'btn btn-success btn-xs','type'=>'submit'])}}
                                        {!! Form::close() !!}
                                        {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.associations.perma_del', $association->id])) !!}
                                        {{ Form::button('<i class="fa fa-trash"></i>',['title'=>'Delete Association','class'=>'btn btn-danger btn-xs','type'=>'submit'])}}
                                        {!! Form::close() !!}
                                    </td>
                                @else
                                <td style="border-bottom: 1px solid;">
                                    @can('association_view')
                                        <a href="{{ route('admin.associations.show',[$association->id]) }}" title="View Detail" class="btn btn-xs btn-primary"><i class="fa fa-eye"></i></a>
                                    @endcan
                                    @can('association_edit')
                                        <a href="{{ route('admin.associations.edit',[$association->id]) }}" title="Edit Association" class="btn btn-xs btn-info"><i class="fa fa-edit"></i></a>
                                    @endcan
                                    @can('association_delete')
                                        {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.associations.destroy', $association->id])) !!}
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
        @can('association_delete')
            @if ( request('show_deleted') != 1 ) window.route_mass_crud_entries_destroy = '{{ route('admin.associations.mass_destroy') }}'; @endif
        @endcan

    </script>
@endsection