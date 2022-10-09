@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')
@section('pageTitle', 'Store List')
@section('content')
    <h3 class="page-title">Manage Blackout Dates</h3>
    
    <div class="panel panel-default">
        <div class="panel-heading">
            Filter Dates
        </div>
        <div class="error-container">
            @if ($errors->count() > 0)
                <div class="note note-danger">
                    <ul class="list-unstyled">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
        <div class="panel-body table-responsive">
            {!! Form::open(['method' => 'POST','id'=>'importblackoutdateform', 'route' => ['admin.blackoutdates.store'], 'files' => true,]) !!}
            <div class="row">
                
                <div class="col-xs-3 form-group">
                    <label for="financial_year" class="control-label">Financial Year</label>
                    <select id="financial_year" class="form-control financial_year select2 required" required="required" name="financial_year" >
                        <option value="">Select Financial Year</option>
                        <?php for($year=2021;$year<2031;$year++){?>
                        <option value="<?php echo $year;?>" <?php if(($request->session()->get('financial_year')) && ($year==$request->session()->get('financial_year'))){echo 'selected="selected"';} ?>><?php echo $year;?></option>
                        <?php }?>
                    </select>
                </div>
                <div class="col-xs-3 form-group">
                    <label for="blackout_client_id" class="control-label required">Client</label>
                    <select id="blackout_client_id" class="form-control blackout_client_id select2 required" required="required" name="blackout_client_id" >
                        <option value="">Select Client</option>
                        <?php foreach ($clients as $key=>$client){?>
                        <option value="<?php echo $key;?>" <?php if(($request->session()->get('blackout_client_id')) && ($key==$request->session()->get('blackout_client_id'))){echo 'selected="selected"';} ?>><?php echo $client;?></option>
                        <?php }?>
                    </select>
                    @if($errors->has('blackout_client_id'))
                        <p class="error-block">
                            {{ $errors->first('blackout_client_id') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group single_blackout_add">
                    <label for="blackout_division_id" class="control-label">Division</label>
                    <select id="blackout_division_id" dropdown="primary" class="form-control blackout_division_id select2" name="blackout_division_id" >
                        <option value="">Select Division</option>
                        <?php foreach ($divisions as $key=>$division){?>
                        <option value="<?php echo $key;?>" <?php if(($request->session()->get('blackout_division_id')) && ($key==$request->session()->get('blackout_division_id'))){echo 'selected="selected"';} ?>><?php echo $division;?></option>
                        <?php }?>
                    </select>
                </div>
                <div class="col-xs-3 form-group single_blackout_add">
                    <label for="blackout_district_id" class="control-label">District</label>
                    <select id="blackout_district_id" dropdown="primary" class="form-control blackout_district_id select2" name="blackout_district_id" >
                        <option value="">Select District</option>
                        <?php foreach ($districts as $key=>$district){?>
                        <option value="<?php echo $key;?>" <?php if(($request->session()->get('blackout_district_id')) && ($key==$request->session()->get('blackout_district_id'))){echo 'selected="selected"';} ?>><?php echo $district;?></option>
                        <?php   }?>
                    </select>
                </div>
                <div class="col-xs-3 form-group single_blackout_add">
                    <label for="blackout_store_id" class="control-label">Store</label>
                    <select id="blackout_store_id" dropdown="primary" class="form-control blackout_store_id select2" name="blackout_store_id" >
                        <option value="">Select Store</option>
                        <?php foreach ($stores as $key=>$store){?>
                        <option value="<?php echo $key;?>" <?php if(($request->session()->get('blackout_store_id')) && ($key==$request->session()->get('blackout_store_id'))){echo 'selected="selected"';} ?>><?php echo $store;?></option>
                        <?php   }?>
                    </select>
                </div>
                <div class="col-xs-3 form-group single_blackout_add">
                    {!! Form::label('blackout_date', 'Date', ['class' => 'control-label required']) !!}
                    {!! Form::text('blackout_date', old('blackout_date'), ['class' => 'form-control blackout_date required','autocomplete'=>'off', 'placeholder' => '']) !!}
                    @if($errors->has('blackout_date'))
                        <p class="error-block">
                            {{ $errors->first('blackout_date') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group single_blackout_add">
                    {!! Form::label('description', 'Description', ['class' => 'control-label']) !!}
                    {!! Form::text('description', old('description'), ['class' => 'form-control description','autocomplete'=>'off', 'placeholder' => '']) !!}
                </div>
                <div class="col-xs-3 form-group blackout_bulk_add" style="height:75px;display: none;">
                    {!! Form::label('blackoutxls', 'Upload .xls file', ['class' => 'control-label required']) !!}
                    {!! Form::file('blackoutxls', old('blackoutxls'), ['class' => 'form-control required','required']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('blackoutxls'))
                        <p class="error-block">
                            {{ $errors->first('blackoutxls') }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 form-group" style="margin-top: 22px;">
                    <input type="checkbox" id="enable_bulk_upload"><label for="enable_bulk_upload">Click for Bulk Upload</label>&nbsp;
                    {!! Form::button('Validate Excel', ['class' => 'btn btn-success validateblackoutdateexcel','style'=>'display:none;'],['tabindex'=>15]) !!}
                    {!! Form::submit('Submit', ['class' => 'btn btn-success blackoutdatesubmitbtn']) !!}
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 form-group" style="margin-top: 22px;">
                    <label class="required"></label> Older blackout date will be removed automatically if duplicate blackout date exist. 
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            List of All Blackout Dates
        </div>

        <div class="panel-body table-responsive">
            @if (Session::has('successmsg'))
                <div class="col-md-12 alert alert-success"> 
                    {{ Session::get('successmsg') }}
                </div>
            @endif
            <table id="blackout_date_list" class="table table-bordered table-striped datatable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Client</th>
                        <th>Division</th>
                        <th>District</th>
                        <th>Store</th>
                        <th>Date</th>
                        <th>Description</th>
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
        $(document).on('click','.validateblackoutdateexcel',function(){
            var blackoutxls = $("#blackoutxls").val();
            if(blackoutxls=="")
            {
                $("#blackoutxls").addClass('custom_error');
                return false;
            }
            $('.blackoutdatesubmitbtn').addClass('disabled');
            $('.cancel-btn').addClass('disabled');
            var postData = new FormData($("#importblackoutdateform")[0]);
                $.ajax({
                    type:"POST",
                    url:'/admin/blackoutdates/validateBlackoutdateImportExcel',
                    data:postData,
                    cache       : false,
                    processData: false,
                    contentType: false,
                    success:function(res){
                        $(".error-container").html('');
                        if(res.status=="Success")
                        {
                            $('.validateblackoutdateexcel').attr('disabled',true);
                            $('.blackoutdatesubmitbtn').attr('disabled',false);
                            $('.blackoutdatesubmitbtn').removeClass('disabled');
                            $('.cancel-btn').removeClass('disabled');
                        }else{
                            var html='<div class="note note-danger"><ul class="list-unstyled">';
                            $.each( res.errors, function( key, value ) {
                                html+="<li>"+value+"</li>";
                            });
                            html+='</ul></div>';
                            $(".error-container").html('');
                            $(".error-container").html(html);
                            $('.cancel-btn').removeClass('disabled');
                            $("#importblackoutdateform").trigger("reset");
                            $('.validateblackoutdateexcel').attr('disabled',false);
                            $('.validateblackoutdateexcel').removeClass('disabled');
                        }
                    }
                });
           
        });
        $("#enable_bulk_upload").on('change',function(){
            if($('#enable_bulk_upload').is(':checked'))
            {
                $('.validateblackoutdateexcel').show();
                $('.blackoutdatesubmitbtn').addClass('disabled');
                $('.blackout_bulk_add').show();
                $('.single_blackout_add').hide();
                $('#blackout_date').removeClass('required');
                $('#blackout_division_id').val('').trigger('change');
                $("#blackout_district_id").val('').trigger('change');
                $('#blackout_store_id').val('').trigger('change');
                $('#blackout_date').val('');
                $('#description').val('');
            }else{
                $('.blackoutdatesubmitbtn').removeClass('disabled');
                $('.validateblackoutdateexcel').hide();
                $('.blackout_bulk_add').hide();
                $('.single_blackout_add').show();
                $('#blackout_date').addClass('required');
            }
        })
        var dataTable = $('#blackout_date_list').DataTable({
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
             'url':'/admin/blackoutdates/get_list_by_page',
             'headers': {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
             'data': function(data){
                // Read values
                var blackout_client_id = $('#blackout_client_id').val();
                var financial_year = $('#financial_year').val();
                var blackout_division_id = $('#blackout_division_id').val();
                var blackout_district_id = $('#blackout_district_id').val();
                var blackout_store_id = $('#blackout_store_id').val();
                data.blackout_client_id = blackout_client_id;
                data.blackout_division_id = blackout_division_id;
                data.blackout_district_id = blackout_district_id;
                data.blackout_store_id = blackout_store_id;
                data.financial_year = financial_year;
             }
          },
          'columns': [
             { data: 'id'},
             { data: 'client'},
             { data: 'division'},
             { data: 'district'},
             { data: 'store'},
             { data: 'date'},
             { data: 'description'},
             { data: 'buttons'},
          ],
          'columnDefs': [ {
               'targets': [6,7], // column index (start from 0)
               'orderable': false, // set orderable false for selected columns
            }],
          'buttons': [
            'copy','csv','excel','pdf', 'print','colvis'
            ],
            'dom': 'Blfrtip',
        });
        $('body').on('focus',".datepicker", function(){
            if( $(this).hasClass('hasDatepicker') === false )  {
                $(this).datepicker();
            }

        });
        $('.blackout_date').datepicker({
            autoclose: true
        })
        
        $('#financial_year').change(function(){
          dataTable.draw();
        });
        $('#blackout_client_id').change(function(){
          dataTable.draw();
        });
        $('#blackout_division_id').change(function(){
          dataTable.draw();
        });
        $('#blackout_district_id').change(function(){
          dataTable.draw();
        });
        $('#blackout_store_id').change(function(){
          dataTable.draw();
        });
        
        
    })
    </script>
    <script>
        @can('store_delete')
            @if ( request('show_deleted') != 1 ) window.route_mass_crud_entries_destroy = '{{ route('admin.stores.mass_destroy') }}'; @endif
        @endcan

    </script>
@endsection