@extends('layouts.app')
@section('pageTitle', 'Add Area')
@section('content')
    <h3 class="page-title">Areas</h3>
    {!! Form::open(['method' => 'POST', 'route' => ['admin.areas.store'], 'files' => true,]) !!}

    <div class="panel panel-default">
        <div class="panel-heading">Add New Area</div>
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
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-3 form-group">
                    {!! Form::label('title', 'Area Title', ['class' => 'control-label required']) !!}
                    {!! Form::text('title', old('title'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('title'))
                        <p class="error-block">
                            {{ $errors->first('title') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('area_number', 'Area Number', ['class' => 'control-label required']) !!}
                    {!! Form::text('area_number', old('area_number'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('area_number'))
                        <p class="error-block">
                            {{ $errors->first('area_number') }}
                        </p>
                    @endif
                </div>
               
                <div class="col-xs-6 form-group">
                    {!! Form::label('address', 'Address', ['class' => 'control-label required']) !!}
                    {!! Form::text('address', old('address'), ['class' => 'form-control', 'placeholder' => '','required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('address'))
                        <p class="error-block">
                            {{ $errors->first('address') }}
                        </p>
                    @endif
                </div>
                

                <div class="col-xs-3 form-group{{ $errors->has('state_id') ? ' has-error' : '' }}">
                    <label for="primary_state" class="control-label required">State</label>
                    <select id="primary_state" dropdown="primary" class="form-control state_dropdown select2" name="state_id"  required="">
                        <option value="">Select State</option>
                         <?php foreach ($states as $key=>$state){?>
                        <option value="<?php echo $key;?>"><?php echo $state;?></option>
                        <?php }?>
                    </select>
                    @if ($errors->has('state_id'))
                        <p class="error-block">
                            {{ $errors->first('state_id') }}
                        </p>
                    @endif
                </div>

                <div class="col-xs-3 form-group{{ $errors->has('city_id') ? ' has-error' : '' }}">
                    <label for="primary_city" class="control-label required">City</label>
                    <select id="primary_city" dropdown="primary" class="form-control city_dropdown select2" name="city_id"  required="">
                        <option value="">Select City</option>
                         <?php foreach ($states as $key=>$state){?>
                        <option value="<?php echo $key;?>"><?php echo $state;?></option>
                        <?php }?>
                    </select>

                    @if ($errors->has('city_id'))
                        <p class="error-block">
                            {{ $errors->first('city_id') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('zip', 'Zip', ['class' => 'control-label required']) !!}
                    {!! Form::text('zip', old('zip'), ['class' => 'form-control', 'placeholder' => '','required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('zip'))
                        <p class="error-block">
                            {{ $errors->first('zip') }}
                        </p>
                    @endif
                </div>
            </div>
            
            
           
            
            
            
                
            
            
            
        </div>
    </div>

    {!! Form::submit('Add', ['class' => 'btn btn-success']) !!}
    {!! Form::reset('Cancel', ['class' => 'btn btn-warning cancel-btn']) !!}
    {!! Form::close() !!}
@stop

@section('javascript')
    @parent
<script type="text/javascript">
    $(document).ready(function(){
        $('[data-mask]').inputmask();
        
        $('.state_dropdown').on('change',function(){
            var stateID = $(this).val();
            var ele = $(this).attr('dropdown');   
            if(stateID){
                $.ajax({
                   type:"GET",
                   url:"{{url('get-city-list')}}?state_id="+stateID,
                   success:function(res){               
                    if(res){
                        $("#"+ele+"_city").empty();
                        $.each(res,function(key,value){
                            $("#"+ele+"_city").append('<option value="'+key+'">'+value+'</option>');
                        });

                    }else{
                       $("#"+ele+"_city").empty();
                    }
                   }
                });
            }else{
                $("#"+ele+"_city").empty();
            }

        });
        $('#client_id').change(function(){
            var client_id = $(this).val();    
            if(client_id){
                $.ajax({
                   type:"POST",
                   url:"{{url('admin/getDivisionByClient')}}",
                   data:{_token: _token,client_id:client_id},
                   success:function(res){               
                    if(res){
                        $("#division_id").empty();
                        $("#division_id").append('<option>Select Division</option>');
                        $.each(res.divisions,function(key,value){
                            $("#division_id").append('<option value="'+value.id+'">'+value.name+'</option>');
                        });

                    }else{
                       $("#division_id").empty();
                    }
                   }
                });
            }else{
                $("#division_id").empty();
            }      
       });
        $(document).on('change','#client_id',function(){
            var clientID = $("#client_id").val();
            //var divisionID = $("#division_id").val();
            if(clientID)
            {
                $.ajax({
               type:"GET",
               url:"{{url('admin/clients/')}}/"+clientID,
               success:function(res){
                    //console.log(res);
                    $('#name').val(res.client.name);
                    $('#address').val(res.client.address);
                    $.each(res.states,function(key,value){
                        $("#primary_state").append('<option value="'+key+'">'+value+'</option>');
                    });
                    $('#primary_state').val(res.client.state_id);
                    $.each(res.cities,function(key,value){
                        $("#primary_city").append('<option value="'+key+'">'+value+'</option>');
                    });
                    $('#primary_city').val(res.client.city_id);
                    $('#zip').val(res.client.zip);
                    $('#frequency').val(res.client.frequency);
                    $('#inv_type').val(res.client.inv_type);
                    $.each(res.client.schedule_availability_days,function(key,value){
                        $("#days_avai_to_schedule option[value="+value.days+"]").attr('selected', 'selected');
                        $('#days_avai_to_schedule').select2();
                    });
                    $('#scheduling_contact_email').val(res.client.scheduling_contact_email);
                    $('#scheduling_contact_name').val(res.client.scheduling_contact_name);
                    $('#scheduling_contact_address').val(res.client.scheduling_contact_address);
                    $('#scheduling_contact_phone').val(res.client.scheduling_contact_phone);
                    $.each(res.scheduling_states,function(key,value){
                        $("#scheduling_state").append('<option value="'+key+'">'+value+'</option>');
                    });
                    $('#scheduling_state').val(res.client.scheduling_state.id);
                    $.each(res.scheduling_cities,function(key,value){
                        $("#scheduling_city").append('<option value="'+key+'">'+value+'</option>');
                    });
                    $('#scheduling_city').val(res.client.scheduling_contact_city_id);
                    $('#scheduling_contact_zip').val(res.client.scheduling_contact_zip);
                    
                    $('#sec_scheduling_contact_email').val(res.client.sec_scheduling_contact_email);
                    $('#sec_scheduling_contact_name').val(res.client.sec_scheduling_contact_name);
                    $('#sec_scheduling_contact_address').val(res.client.sec_scheduling_contact_address);
                    $('#sec_scheduling_contact_phone').val(res.client.sec_scheduling_contact_phone);
                    $.each(res.sec_scheduling_states,function(key,value){
                        $("#sec_scheduling_state").append('<option value="'+key+'">'+value+'</option>');
                    });
                    $('#sec_scheduling_state').val(res.client.sec_scheduling_contact_state_id);
                    $.each(res.sec_scheduling_cities,function(key,value){
                        $("#sec_scheduling_city").append('<option value="'+key+'">'+value+'</option>');
                    });
                    $('#sec_scheduling_city').val(res.client.sec_scheduling_contact_city_id);
                    $('#sec_scheduling_contact_zip').val(res.client.sec_scheduling_contact_zip);
                    
                    $('#billing_contact_email').val(res.client.billing_contact_email);
                    $('#billing_contact_name').val(res.client.billing_contact_name);
                    $('#billing_contact_address').val(res.client.billing_contact_address);
                    $('#billing_contact_phone').val(res.client.billing_contact_phone);
                    $.each(res.billing_states,function(key,value){
                        $("#billing_state").append('<option value="'+key+'">'+value+'</option>');
                    });
                    $('#billing_state').val(res.client.billing_contact_state_id);
                    $.each(res.billing_cities,function(key,value){
                        $("#billing_city").append('<option value="'+key+'">'+value+'</option>');
                    });
                    $('#billing_city').val(res.client.billing_contact_city_id);
                    $('#billing_contact_zip').val(res.client.billing_contact_zip);
                    $('#rate_type').val(res.client.rate_type);
                    $('#rate').val(res.client.rate);
                    $('#rate_per').val(res.client.rate_per);
                    $('#start_time').val(res.client.start_time);
                    $('#benchmark').val(res.client.benchmark);
                    $('#max_length').val(res.client.max_length);
                    $('#min_auditors').val(res.client.min_auditors);
                    $('#spf').val(res.client.spf);
                    $('#alr_disk').val(res.client.alr_disk);
                    $("input[name='count_stockroom'][value='"+res.client.count_stockroom+"']").prop('checked', true);
                    $("input[name='precall'][value='"+res.client.precall+"']").prop('checked', true);
                    $("input[name='qccall'][value='"+res.client.qccall+"']").prop('checked', true);
                    $("input[name='pieces_or_dollars'][value='"+res.client.pieces_or_dollars+"']").prop('checked', true);
                    $('#notes').val(res.client.notes);
               }
            });
            }
        });
        $(document).on('change','#division_id',function(){
            var division_id = $("#division_id").val();
            if(division_id)
            {
                $.ajax({
               type:"GET",
               url:"{{url('admin/divisions/')}}/"+division_id,
               success:function(res){
                    //console.log(res);
                    if(res.division.address)$('#address').val(res.division.address);
                    if(res.division.state_id)
                    {
                        $.each(res.states,function(key,value){
                            $("#primary_state").append('<option value="'+key+'">'+value+'</option>');
                        });
                        $('#primary_state').val(res.division.state_id);
                    }
                    if(res.division.city_id)
                    {
                        $.each(res.cities,function(key,value){
                            $("#primary_city").append('<option value="'+key+'">'+value+'</option>');
                        });
                        $('#primary_city').val(res.division.city_id);
                    }
                    if(res.division.zip)$('#zip').val(res.division.zip);
                    if(res.division.frequency)$('#frequency').val(res.division.frequency);
                    if(res.division.inv_type)$('#inv_type').val(res.division.inv_type);
                    if(res.division.schedule_availability_days)
                    {
                        $.each(res.division.schedule_availability_days,function(key,value){
                            $("#days_avai_to_schedule option[value="+value.days+"]").attr('selected', 'selected');
                            $('#days_avai_to_schedule').select2();
                        });
                    }
                    if(res.division.scheduling_contact_email)$('#scheduling_contact_email').val(res.division.scheduling_contact_email);
                    if(res.division.scheduling_contact_name)$('#scheduling_contact_name').val(res.division.scheduling_contact_name);
                    if(res.division.scheduling_contact_address)$('#scheduling_contact_address').val(res.division.scheduling_contact_address);
                    if(res.division.scheduling_contact_phone)$('#scheduling_contact_phone').val(res.division.scheduling_contact_phone);
                    if(res.division.scheduling_state.id)
                    {
                        $.each(res.scheduling_states,function(key,value){
                            $("#scheduling_state").append('<option value="'+key+'">'+value+'</option>');
                        });
                        $('#scheduling_state').val(res.division.scheduling_state.id);
                    }
                    if(res.division.scheduling_contact_city_id)
                    {
                        $.each(res.scheduling_cities,function(key,value){
                            $("#scheduling_city").append('<option value="'+key+'">'+value+'</option>');
                        });
                        $('#scheduling_city').val(res.division.scheduling_contact_city_id);
                    }
                    if(res.division.scheduling_contact_zip)$('#scheduling_contact_zip').val(res.division.scheduling_contact_zip);
                    
                    if(res.division.sec_scheduling_contact_email)$('#sec_scheduling_contact_email').val(res.division.sec_scheduling_contact_email);
                    if(res.division.sec_scheduling_contact_name)$('#sec_scheduling_contact_name').val(res.division.sec_scheduling_contact_name);
                    if(res.division.sec_scheduling_contact_address)$('#sec_scheduling_contact_address').val(res.division.sec_scheduling_contact_address);
                    if(res.division.sec_scheduling_contact_phone)$('#sec_scheduling_contact_phone').val(res.division.sec_scheduling_contact_phone);
                    if(res.division.sec_scheduling_contact_state_id)
                    {
                        $.each(res.sec_scheduling_states,function(key,value){
                            $("#sec_scheduling_state").append('<option value="'+key+'">'+value+'</option>');
                        });
                        $('#sec_scheduling_state').val(res.division.sec_scheduling_contact_state_id);
                    }
                    if(res.division.sec_scheduling_contact_city_id)
                    {
                        $.each(res.sec_scheduling_cities,function(key,value){
                            $("#sec_scheduling_city").append('<option value="'+key+'">'+value+'</option>');
                        });
                        $('#sec_scheduling_city').val(res.division.sec_scheduling_contact_city_id);
                    }
                    if(res.division.sec_scheduling_contact_zip)$('#sec_scheduling_contact_zip').val(res.division.sec_scheduling_contact_zip);
                    
                    if(res.division.billing_contact_email)$('#billing_contact_email').val(res.division.billing_contact_email);
                    if(res.division.billing_contact_name)$('#billing_contact_name').val(res.division.billing_contact_name);
                    if(res.division.billing_contact_address)$('#billing_contact_address').val(res.division.billing_contact_address);
                    if(res.division.billing_contact_phone)$('#billing_contact_phone').val(res.division.billing_contact_phone);
                    if(res.division.billing_contact_state_id)
                    {
                        $.each(res.billing_states,function(key,value){
                            $("#billing_state").append('<option value="'+key+'">'+value+'</option>');
                        });
                        $('#billing_state').val(res.division.billing_contact_state_id);
                    }
                    if(res.division.billing_contact_city_id)
                    {
                        $.each(res.billing_cities,function(key,value){
                            $("#billing_city").append('<option value="'+key+'">'+value+'</option>');
                        });
                        $('#billing_city').val(res.division.billing_contact_city_id);
                    }
                    if(res.division.billing_contact_zip)$('#billing_contact_zip').val(res.division.billing_contact_zip);
                    if(res.division.rate_type)$('#rate_type').val(res.division.rate_type);
                    if(res.division.rate)$('#rate').val(res.division.rate);
                    if(res.division.rate_per)$('#rate_per').val(res.division.rate_per);
                    if(res.division.start_time)$('#start_time').val(res.division.start_time);
                    if(res.division.benchmark)$('#benchmark').val(res.division.benchmark);
                    if(res.division.max_length)$('#max_length').val(res.division.max_length);
                    if(res.division.min_auditors)$('#min_auditors').val(res.division.min_auditors);
                    if(res.division.spf)$('#spf').val(res.division.spf);
                    if(res.division.alr_disk)$('#alr_disk').val(res.division.alr_disk);
                    if(res.division.count_stockroom)$("input[name='count_stockroom'][value='"+res.division.count_stockroom+"']").prop('checked', true);
                    if(res.division.precall)$("input[name='precall'][value='"+res.division.precall+"']").prop('checked', true);
                    if(res.division.qccall)$("input[name='qccall'][value='"+res.division.qccall+"']").prop('checked', true);
                    if(res.division.pieces_or_dollars)$("input[name='pieces_or_dollars'][value='"+res.division.pieces_or_dollars+"']").prop('checked', true);
                    if(res.division.notes)$('#notes').val(res.division.notes);
               }
            });
            }
        });
        $('.timepicker').timepicker({
            showInputs: false
        })
   })
</script>
@stop