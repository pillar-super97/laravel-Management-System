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

<!--    <p>
        <ul class="list-inline">
            <li><a href="{{ route('admin.stores.index') }}?show_all=1" style="{{ request('show_all') == 1 ? 'font-weight: 700' : '' }}">All</a></li> | 
            <li><a href="{{ route('admin.stores.index') }}" style="<?php if(request('show_deleted') == 1 || request('show_all') == 1){echo '';}else{echo  'font-weight: 700';}?>">Active</a></li> |
            <li><a href="{{ route('admin.stores.index') }}?show_deleted=1" style="{{ request('show_deleted') == 1 ? 'font-weight: 700' : '' }}">Inactive</a></li>
        </ul>
    </p>-->
    
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
                        <option value="">Select State</option>
                        <?php foreach ($states as $key=>$state){?>
                        <option value="<?php echo $key;?>" <?php if(($request->session()->get('state_id')) && ($key==$request->session()->get('state_id'))){echo 'selected="selected"';} ?>><?php echo $state;?></option>
                        <?php }?>
                    </select>
                </div>
                <div class="col-xs-3 form-group">
                    <label for="store_type" class="control-label">Type</label>
                    <select id="store_type" dropdown="primary" class="form-control store_type select2" name="store_type[]" >
                        <option value="">Select Store</option>
                        <?php foreach ($stores as $key=>$store){?>
                        <option value="<?php echo $store;?>" <?php if(($request->session()->get('store_type')) && ($store==$request->session()->get('store_type'))){echo 'selected="selected"';} ?>><?php echo $store;?></option>
                        <?php }?>
                    </select>
                </div>
                <div class="col-xs-3 form-group">
                    <label for="city_id" class="control-label">Cities</label>
                    <select id="city_id" dropdown="primary" class="form-control city_id select2" name="city_id" >
                        <option value="">Select City</option>
                        <?php if(($request->session()->get('city_id'))){
                                foreach ($cities as $key=>$city){?>
                                    <option value="<?php echo $key;?>" <?php if(($request->session()->get('city_id')) && ($key==$request->session()->get('city_id'))){echo 'selected="selected"';} ?>><?php echo $city;?></option>
                        <?php   }
                            }?>
                    </select>
                </div>
                <div class="col-xs-3 form-group" style="margin-top: 22px;">
                    <input type="checkbox" name="inactive_only" value="1" id="inactive_only" <?php if(($request->session()->get('inactive_only'))){echo 'checked="checked"';} ?>><label for="inactive_only">Inactive Only</label>&nbsp;
                    
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
            <table id="store_list" class="table table-bordered table-striped datatable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Store Name</th>
                        <th>Client Name</th>
                        <th>District</th>
                        <th>City</th>
                        <th>State</th>
                        <th>Last Count Date</th>
                        <th>Last Count Value(Dollars)</th>
                        <th>Minimum Billing</th>
                        <th>Rate</th>
                        <th>Store type</th>
                        <th>Actions</th>
                   </tr>
                </thead>
                

            </table>
        </div>
    </div>
@stop

@section('javascript') 
    
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
                var store_type = $('#store_type').val();
                if($('input[name="inactive_only"]:checked'))
                    var inactive_only = $('input[name="inactive_only"]:checked').val()
                else
                    var inactive_only = 0;
                
                data.client_id = client_id;
                data.state_id = state_id;
                data.city_id = city_id;
                data.store_type = store_type;
                data.inactive_only = inactive_only;
             }
          },
          'columns': [
             { data: 'id'},
             { data: 'store'},
             { data: 'client'}, 
             { data: 'district'},
             { data: 'city'},
             { data: 'state'},
             { data: 'last_count_date'},
             { data: 'last_count_value'},
             { data: 'min_bill'},
             { data: 'rate'},
             { data: 'type'},
             { data: 'buttons'},
          ],
          'columnDefs': [ {
               'targets': [6,7,8,9,10,11], // column index (start from 0)
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
        $('#store_type').change(function(){
          dataTable.draw();
        });
        $('#inactive_only').click(function(){
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
                        $("#city_id").append('<option value="">Select City</option>');
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
    <script>
        @can('store_delete')
            @if ( request('show_deleted') != 1 ) window.route_mass_crud_entries_destroy = '{{ route('admin.stores.mass_destroy') }}'; @endif
        @endcan

    </script>
@endsection