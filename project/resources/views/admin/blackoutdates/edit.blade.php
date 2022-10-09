@extends('layouts.app')
@section('pageTitle', 'Edit Blackout Date')
@section('content')
    <h3 class="page-title">Blackout Dates</h3>
    
    {!! Form::model($blackoutdate, ['method' => 'PUT', 'route' => ['admin.blackoutdates.update', $blackoutdate->id], 'files' => true,'autocomplete'=>'off']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">Edit Blackout Date</div>
        <div class="error-container">
            @if (Session::has('message'))
                <div class="note note-info">
                    <p>{{ Session::get('message') }}</p>
                </div>
            @endif
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

        <?php $arrays = arrays();?>
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-3 form-group{{ $errors->has('blackout_division_id') ? ' has-error' : '' }}">
                    <label for="financial_year" class="control-label required">Financial Year</label>
                    <select id="financial_year" class="form-control financial_year select2 required" required="required" name="financial_year" >
                        <option value="">Select Financial Year</option>
                        <?php for($year=2021;$year<2031;$year++){?>
                        <option value="<?php echo $year;?>" <?php if($year==$blackoutdate->financial_year){echo 'selected="selected"';}?>><?php echo $year;?></option>
                        <?php }?>
                    </select>
                    @if ($errors->has('financial_year'))
                        <p class="error-block">
                            {{ $errors->first('financial_year') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group{{ $errors->has('blackout_client_id') ? ' has-error' : '' }}">
                    <label for="blackout_client_id" class="control-label required">Client</label>
                    <select id="blackout_client_id" class="form-control blackout_client_id select2 required" required="required" name="blackout_client_id" >
                        <option value="0">Select Client</option>
                        <?php foreach ($clients as $key=>$client){?>
                        <option value="<?php echo $key;?>" <?php if($key==$blackoutdate->client_id){echo 'selected="selected"';}?>><?php echo $client;?></option>
                        <?php }?>
                    </select>

                    @if ($errors->has('blackout_client_id'))
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
                        <option value="<?php echo $key;?>" <?php if($key==$blackoutdate->division_id){echo 'selected="selected"';} ?>><?php echo $division;?></option>
                        <?php }?>
                    </select>
                </div>
                <div class="col-xs-3 form-group single_blackout_add">
                    <label for="blackout_district_id" class="control-label">District</label>
                    <select id="blackout_district_id" dropdown="primary" class="form-control blackout_district_id select2" name="blackout_district_id" >
                        <option value="">Select District</option>
                        <?php foreach ($districts as $key=>$district){?>
                        <option value="<?php echo $key;?>" <?php if($key==$blackoutdate->district_id){echo 'selected="selected"';} ?>><?php echo $district;?></option>
                        <?php   }?>
                    </select>
                </div>
                <div class="col-xs-3 form-group single_blackout_add">
                    <label for="blackout_store_id" class="control-label">Store</label>
                    <select id="blackout_store_id" dropdown="primary" class="form-control blackout_store_id select2" name="blackout_store_id" >
                        <option value="">Select Store</option>
                        <?php foreach ($stores as $key=>$store){?>
                        <option value="<?php echo $key;?>" <?php if($key==$blackoutdate->store_id){echo 'selected="selected"';} ?>><?php echo $store;?></option>
                        <?php   }?>
                    </select>
                </div>
                
                <div class="col-xs-3 form-group single_blackout_add">
                    {!! Form::label('blackout_date', 'Date', ['class' => 'control-label required']) !!}
                    {!! Form::text('blackout_date', date('m/d/Y',strtotime($blackoutdate->date)), ['class' => 'form-control blackout_date required','autocomplete'=>'off', 'placeholder' => '']) !!}
                    @if($errors->has('blackout_date'))
                        <p class="error-block">
                            {{ $errors->first('blackout_date') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-6 form-group single_blackout_add">
                    {!! Form::label('description', 'Description', ['class' => 'control-label']) !!}
                    {!! Form::text('description', old('description'), ['class' => 'form-control description','autocomplete'=>'off', 'placeholder' => '']) !!}
                </div>
                 
            </div>
        </div>
    </div>

    {!! Form::submit(trans('global.app_update'), ['class' => 'btn btn-danger']) !!}
    {!! Form::close() !!}
@stop
@section('javascript')
    @parent
   <script type="text/javascript">
    $(document).ready(function(){
        
        
        $('#blackout_client_id').change(function(){
            var blackout_client_id = $(this).val();
            $("#blackout_division_id").empty();
            $("#blackout_district_id").val('<option value="0">Select District</option>');
            if(blackout_client_id){
                $.ajax({
                   type:"POST",
                   url:"{{url('admin/getDivisionByClient')}}",
                   data:{_token: _token,blackout_client_id:blackout_client_id},
                   success:function(res){               
                    if(res){
                        $("#blackout_division_id").empty();
                        $("#blackout_division_id").append('<option value="0">Select Division</option>');
                        $.each(res.divisions,function(key,value){
                            $("#blackout_division_id").append('<option value="'+value.id+'">'+value.name+'</option>');
                        });

                    }else{
                       $("#blackout_division_id").empty();
                    }
                   }
                });
            }else{
                $("#blackout_division_id").empty();
            }      
       });
        $(document).on("change", "#blackout_division_id", function(){
            var blackout_division_id = $(this).val(); 
            var blackout_client_id = $("#blackout_client_id").val();
            $("#blackout_district_id").empty();
            if(blackout_division_id){
                $.ajax({
                   type:"POST",
                   url:"{{url('admin/getDistrictByDivision')}}",
                   data:{_token: _token,blackout_client_id:blackout_client_id,blackout_division_id:blackout_division_id},
                   success:function(res){               
                    if(res){
                        $("#blackout_district_id").empty();
                        $("#blackout_district_id").append('<option value="0">Select District</option>');
                        $.each(res.districts,function(key,value){
                            $("#blackout_district_id").append('<option value="'+value.id+'">'+value.number+'</option>');
                        });

                    }else{
                       $("#blackout_district_id").empty();
                    }
                   }
                });
            }else{
                $("#blackout_district_id").empty();
            }    
        });
       
        $('body').on('focus',".datepicker", function(){
            if( $(this).hasClass('hasDatepicker') === false )  {
                $(this).datepicker();
            }

        });
        $('.blackout_date').datepicker({
            autoclose: true
        })
        
        
        
        
        
   })
</script>
@stop