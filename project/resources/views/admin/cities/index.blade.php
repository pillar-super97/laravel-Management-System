@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')
@section('pageTitle', 'City List')
@section('content')
    <h3 class="page-title">Manage Cities</h3>
    @can('city_create')
    <p>
        <a href="{{ route('admin.cities.create') }}" class="btn btn-success">@lang('global.app_add_new')</a>
        
    </p>
    @endcan

    <p>
        <ul class="list-inline">
            <li><a href="{{ route('admin.cities.index') }}" style="{{ request('show_deleted') == 1 ? '' : 'font-weight: 700' }}">All</a></li> |
            <li><a href="{{ route('admin.cities.index') }}?show_deleted=1" style="{{ request('show_deleted') == 1 ? 'font-weight: 700' : '' }}">Inactive</a></li>
        </ul>
    </p>
    

    <div class="panel panel-default">
        <div class="panel-heading">
            List of All Cities
        </div>

        <div class="panel-body table-responsive">
            @if (Session::has('successmsg'))
                <div class="col-md-12 alert alert-success"> 
                    {{ Session::get('successmsg') }}
                </div>
            @endif
            <table class="table table-bordered table-striped {{ count($cities) > 0 ? 'datatable' : '' }} @can('city_delete') @if ( request('show_deleted') != 1 ) dt-select @endif @endcan">
                <thead>
                    <tr>
                        @can('city_delete')
                            @if ( request('show_deleted') != 1 )<th style="text-align:center;"><input type="checkbox" id="select-all" /></th>@endif
                        @endcan

                      
                        <th>City</th>
                        <th>State</th>
                       
                        <th>Actions</th>
                   </tr>
                </thead>
                
                <tbody>
                    @if (count($cities) > 0)
                        @foreach ($cities as $city)
                            <tr data-entry-id="{{ $city->id }}">
                                @can('city_delete')
                                    @if ( request('show_deleted') != 1 )<td></td>@endif
                                @endcan
                                <td>{{ $city->name }}</td>
                                <td>{{ $city->state->name }}</td>
                                
                                <td style="border-bottom: 1px solid;">
                                    @can('city_view')
                                        <a href="{{ route('admin.cities.show',[$city->id]) }}" class="btn btn-xs btn-primary" title="View Detail"><i class="fa fa-eye"></i></a>
                                    @endcan
                                    @can('city_edit')
                                        <a href="{{ route('admin.cities.edit',[$city->id]) }}" class="btn btn-xs btn-info" title="Edit City"><i class="fa fa-edit"></i></a>
                                    @endcan
                                    @can('city_delete')
{!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.cities.destroy', $city->id])) !!}
                                    {{ Form::button('<i class="fa fa-trash"></i>',['title'=>'Delete City','class'=>'btn btn-danger btn-xs','type'=>'submit'])}}
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
   
@endsection