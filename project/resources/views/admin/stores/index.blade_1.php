@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')
@section('pageTitle', 'Store List')
@section('content')
    <h3 class="page-title">Manage Stores</h3>
    @can('store_create')
    <p>
        <a href="{{ route('admin.stores.create') }}" class="btn btn-success">@lang('global.app_add_new')</a>
        <a href="/export_cost_center_to_kronos" class="btn btn-success pull-right">Export Cost Center to Kronos</a>
    </p>
    @endcan

    <p>
        <ul class="list-inline">
            <li><a href="{{ route('admin.stores.index') }}?show_all=1" style="{{ request('show_all') == 1 ? 'font-weight: 700' : '' }}">All</a></li> | 
            <li><a href="{{ route('admin.stores.index') }}" style="<?php if(request('show_deleted') == 1 || request('show_all') == 1){echo '';}else{echo  'font-weight: 700';}?>">Active</a></li> |
            <li><a href="{{ route('admin.stores.index') }}?show_deleted=1" style="{{ request('show_deleted') == 1 ? 'font-weight: 700' : '' }}">Inactive</a></li>
        </ul>
    </p>
    
    <div class="panel panel-default">
        <div class="panel-heading">
            Filter Stores
        </div>

        <div class="panel-body table-responsive">
            <div class="row">
                
                <div class="col-xs-3 form-group">
                    <label for="client_id" class="control-label">Clients</label>
                    <select id="client_id" class="form-control client_id select2" multiple="" name="client_id[]" >
                        <option value="">Select Client</option>
                        <?php foreach ($clients as $key=>$client){?>
                        <option value="<?php echo $key;?>" <?php if(($request->session()->get('client_id')) && in_array($key,$request->session()->get('client_id'))){echo 'selected="selected"';} ?>><?php echo $client;?></option>
                        <?php }?>
                    </select>
                </div>
                <div class="col-xs-3 form-group">
                    <label for="state_id" class="control-label">States</label>
                    <select id="state_id" dropdown="primary" class="form-control state_dropdown select2" name="state_id" >
                        <option value="">Select States</option>
                        <?php foreach ($states as $key=>$state){?>
                        <option value="<?php echo $key;?>" <?php if(($request->session()->get('state_id')) && in_array($key,$request->session()->get('state_id'))){echo 'selected="selected"';} ?>><?php echo $state;?></option>
                        <?php }?>
                    </select>
                </div>
                <div class="col-xs-3 form-group">
                    <label for="city_id" class="control-label">Cities</label>
                    <select id="city_id" dropdown="primary" class="form-control city_id select2" name="city_id" >
                        <option value="">Select Cities</option>
                        <?php if(($request->session()->get('state_id'))){
                                foreach ($cities as $key=>$city){?>
                                    <option value="<?php echo $key;?>" <?php if(($request->session()->get('city_id')) && in_array($key,$request->session()->get('city_id'))){echo 'selected="selected"';} ?>><?php echo $city;?></option>
                        <?php   }
                            }?>
                    </select>
                </div>
            </div>
            
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            List of All Stores
        </div>

        <div class="panel-body table-responsive">
            @if (Session::has('successmsg'))
                <div class="col-md-12 alert alert-success"> 
                    {{ Session::get('successmsg') }}
                </div>
            @endif
            <table id="store_list" class="table table-bordered table-striped {{ count($stores) > 0 ? 'datatable' : '' }} @can('store_delete') @if ( request('show_deleted') != 1 ) dt-select @endif @endcan">
                <thead>
                    <tr>
                        @can('store_delete')
                            @if ( request('show_deleted') != 1 )<th style="text-align:center;"><input type="checkbox" id="select-all" /></th>@endif
                        @endcan

                        <th>Store Name</th>
                        <th>Client Name</th>
                        <th>District</th>
                        <th>City</th>
                        <th>State</th>
                        <th>Last Count Date</th>
                        <th>Last Count Value(Dollars)</th>
                        <th>Actions</th>
                   </tr>
                </thead>
                
                <tbody>
                    @if (count($stores) > 0)
                        @foreach ($stores as $store)
                            <tr data-entry-id="{{ $store->id }}">
                                @can('store_delete')
                                    @if ( request('show_deleted') != 1 )<td></td>@endif
                                @endcan
                                <td>{{ $store->name }}</td>
                                <td>{{ @$store->client->name }}</td>
                                <td>{{ @$store->district->number }}</td>
                                <td>{{ @$store->city->name }}</td>
                                <td>{{ @$store->state->name }}</td>
                                <td><?php $historical_data = historical_data($store->id);
                                    if($historical_data)echo date('m-d-Y',strtotime($historical_data->dtJobDate));?></td>
                                <td><?php if($historical_data)echo '$'.number_format($historical_data->dEmpCount);?></td>
                                @if( request('show_deleted') == 1 )
                                <td style="border-bottom: 1px solid;">
                                    {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'POST',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.stores.restore', $store->id])) !!}
                                    {{ Form::button('<i class="fa fa-eye"></i>',['title'=>'Active Store','class'=>'btn btn-success btn-xs','type'=>'submit'])}}
                                    {!! Form::close() !!}
                                                                    {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.stores.perma_del', $store->id])) !!}
                                    {{ Form::button('<i class="fa fa-trash"></i>',['title'=>'Delete Store','class'=>'btn btn-danger btn-xs','type'=>'submit'])}}
                                    {!! Form::close() !!}
                                                                </td>
                                @else
                                <td style="border-bottom: 1px solid;">
                                    @can('store_view')
                                        <a href="{{ route('admin.stores.show',[$store->id]) }}" class="btn btn-xs btn-primary" title="View Detail"><i class="fa fa-eye"></i></a>
                                    @endcan
                                    @can('store_edit')
                                        <a href="{{ route('admin.stores.edit',[$store->id]) }}" class="btn btn-xs btn-info" title="Edit Store"><i class="fa fa-edit"></i></a>
                                    @endcan
                                    @can('event_view')
                                        <a href="{{ route('admin.events.showstoreevents',[$store->id]) }}" class="btn btn-xs btn-primary" title="View {{ $store->name }} Events"><i class="fa fa-calendar"></i></a>
                                    @endcan
                                    @can('store_delete')
{!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.stores.destroy', $store->id])) !!}
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
        @can('store_delete')
            @if ( request('show_deleted') != 1 ) window.route_mass_crud_entries_destroy = '{{ route('admin.stores.mass_destroy') }}'; @endif
        @endcan

    </script>
    <script type="text/javascript">
    $(document).ready(function(){
        var dataTable = $('#store_list').DataTable({
            "aLengthMenu": [[25, 50, 75,100,500,1000,-1], [25,50,75,100,500,1000,"All"]],
            "iDisplayLength": 25,
            'destroy': true,
            oLanguage: {
                sProcessing: "<img src='../uploads/images/ajax-loader.gif'>"
            },
            'processing': true,
            'serverSide': true,
            'serverMethod': 'post',
            //'searching': false, // Remove default Search Control
            'ajax': {
             'url':'/admin/stores/get_store_list_by_page',
             'headers': {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
             'data': function(data){
                // Read values
                var client_id = $('#client_id').val();
                var state_id = $('#state_id').val();
                var city_id = $('#city_id').val();
                data.client_id = client_id;
                data.state_id = state_id;
                data.city_id = city_id;
             }
          },
          'columns': [
             { data: 'id'},
             { data: 'store' },
             { data: 'state' }, 
             { data: 'date' },
             { data: 'start_time' },
             { data: 'area' },
             { data: 'run' },
             { data: 'lead' },
             { data: 'status' },
             { data: 'buttons' },
          ],
          'columnDefs': [ {
               'targets': [2,4,8,9], // column index (start from 0)
               'orderable': false, // set orderable false for selected columns
            }],
          'buttons': [
            'copy','csv','excel','pdf', 'print','colvis'
            ],
            'dom': 'Blfrtip',
        });
        $('#client_id').change(function(){
          dataTable.draw();
        });
        $('#state_id').change(function(){
          dataTable.draw();
        });
        $('#city_id').change(function(){
          dataTable.draw();
        });
        $('.state_dropdown').on('change',function(){
            var stateID = $(this).val();
            if(stateID){
                $.ajax({
                   type:"GET",
                   url:"{{url('get-city-list')}}?state_id="+stateID,
                   success:function(res){               
                    if(res){
                        $("#city_id").empty();
                        $.each(res,function(key,value){
                            $("#city_id").append('<option value="'+key+'">'+value+'</option>');
                        });

                    }else{
                       $("#city_id").empty();
                    }
                   }
                });
            }else{
                $("#city_id").empty();
            }

        });
    })
    </script>
@endsection