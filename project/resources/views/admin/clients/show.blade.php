@extends('layouts.app')
@section('pageTitle', 'View Client')
@section('content')
    <h3 class="page-title">Clients</h3>
    
   

    <div class="panel panel-default">
        <div class="panel-heading">View Client Details</div>
    
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-3 form-group">
                    {!! Form::label('cust_no', 'Customer Number', ['class' => 'control-label required']) !!}
                    {!! Form::text('cust_no', $client->cust_no, ['class' => 'form-control','disabled', 'placeholder' => '', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('cust_no'))
                        <p class="error-block">
                            {{ $errors->first('cust_no') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('name', 'Name', ['class' => 'control-label required']) !!}
                    {!! Form::text('name',$client->name, ['class' => 'form-control','disabled', 'placeholder' => '', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('name'))
                        <p class="error-block">
                            {{ $errors->first('name') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('address', 'Address', ['class' => 'control-label required']) !!}
                    {!! Form::text('address', $client->address, ['class' => 'form-control','disabled', 'placeholder' => '','required' => '']) !!}
                </div>
                <div class="col-xs-3 form-group{{ $errors->has('association_id') ? ' has-error' : '' }}">
                    <label for="association_id" class="control-label required">Association</label>
                    <select id="association_id" class="form-control" disabled="" name="association_id"  required="">
                        <option>{{@$client->association->name}}</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-3 form-group{{ $errors->has('frequency') ? ' has-error' : '' }}">
                    <label for="frequency" class="control-label">Frequency</label>
                    <select id="frequency" class="form-control" disabled="" name="frequency"  >
                        <option>{{@$client->frequency}}</option>
                    </select>
                </div>
            
                

                <div class="col-xs-3 form-group{{ $errors->has('state_id') ? ' has-error' : '' }}">
                    <label for="primary_state" class="control-label required">State</label>
                    <select id="primary_state" dropdown="primary" disabled="" class="form-control state_dropdown" name="state_id"  required="">
                       <option><?php echo @$client->state->name;?></option>
                    </select>
                   
                </div>

                <div class="col-xs-3 form-group{{ $errors->has('city_id') ? ' has-error' : '' }}">
                    <label for="primary_city" class="control-label required">City</label>
                    <select id="primary_city" dropdown="primary" disabled="" class="form-control city_dropdown" name="city_id"  required="">
                       <option>{{@$client->city->name}}</option>
                    </select>
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('zip', 'Zip', ['class' => 'control-label']) !!}
                    {!! Form::text('zip', $client->zip, ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                </div>
                </div>
            <div class="row">
                <div class="custom-heading">Scheduling Contact</div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('scheduling_contact_email', 'Email', ['class' => 'control-label required']) !!}
                    {!! Form::text('scheduling_contact_email', $client->scheduling_contact_email, ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                   
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('scheduling_contact_name', 'Name', ['class' => 'control-label required']) !!}
                    {!! Form::text('scheduling_contact_name', $client->scheduling_contact_name, ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                  
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('scheduling_contact_address', 'Address', ['class' => 'control-label required']) !!}
                    {!! Form::text('scheduling_contact_address', $client->scheduling_contact_address, ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                   
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('scheduling_contact_phone', 'Phone', ['class' => 'control-label required']) !!}
                    <?php if((new \Jenssegers\Agent\Agent())->isMobile()){?>
                    <a href="tel:<?=$client->scheduling_contact_phone;?>"><?=$client->scheduling_contact_phone;?></a>
                    <?php }else{?>
                    <input id="scheduling_contact_phone" value="<?php echo $client->scheduling_contact_phone;?>" disabled="" autocomplete="off" type="text" name="scheduling_contact_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask>
                    <?php }?>
                </div>
                

                <div class="col-xs-3 form-group{{ $errors->has('scheduling_contact_state_id') ? ' has-error' : '' }}">
                    <label for="scheduling_state" class="control-label required">State</label>
                    <select id="scheduling_state" dropdown="scheduling" disabled="" class="form-control state_dropdown" name="scheduling_contact_state_id"  required="">
                        <option>{{@$client->scheduling_state->name}}</option>
                    </select>
                  
                </div>

                <div class="col-xs-3 form-group{{ $errors->has('scheduling_contact_city_id') ? ' has-error' : '' }}">
                    <label for="scheduling_city" class="control-label required">City</label>
                    <select id="scheduling_city" dropdown="scheduling" disabled="" class="form-control city_dropdown" name="scheduling_contact_city_id"  required="">
                        <option>{{@$client->scheduling_city->name}}</option>
                    </select>

                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('scheduling_contact_zip', 'Zip', ['class' => 'control-label']) !!}
                    {!! Form::text('scheduling_contact_zip', $client->scheduling_contact_zip, ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                  
                </div>
                </div>
            <div class="row">
                <div class="custom-heading">Secondary Scheduling Contact</div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('sec_scheduling_contact_email', 'Title', ['class' => 'control-label']) !!}
                    {!! Form::text('sec_scheduling_contact_email', $client->sec_scheduling_contact_email, ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                  
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('sec_scheduling_contact_name', 'Name', ['class' => 'control-label']) !!}
                    {!! Form::text('sec_scheduling_contact_name', $client->sec_scheduling_contact_name, ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                 
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('sec_scheduling_contact_address', 'Address', ['class' => 'control-label']) !!}
                    {!! Form::text('sec_scheduling_contact_address', $client->sec_scheduling_contact_address, ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                 
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('sec_scheduling_contact_phone', 'Phone', ['class' => 'control-label']) !!}
                    <?php if((new \Jenssegers\Agent\Agent())->isMobile()){?>
                    <a href="tel:<?=$client->sec_scheduling_contact_phone;?>"><?=$client->sec_scheduling_contact_phone;?></a>
                    <?php }else{?>
                    <input id="sec_scheduling_contact_phone" disabled="" value="<?php echo $client->sec_scheduling_contact_phone;?>" type="text" name="sec_scheduling_contact_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask>
                    <?php }?>
                </div>
                
                <div class="col-xs-3 form-group{{ $errors->has('sec_scheduling_contact_state_id') ? ' has-error' : '' }}">
                    <label for="sec_scheduling_state" class="control-label">State</label>
                    <select id="sec_scheduling_state" disabled="" dropdown="sec_scheduling" class="form-control state_dropdown" name="sec_scheduling_contact_state_id"  >
                       <option>{{@$client->sec_scheduling_state->name}}</option>
                    </select>
                 
                </div>

                <div class="col-xs-3 form-group{{ $errors->has('sec_scheduling_contact_city_id') ? ' has-error' : '' }}">
                    <label for="sec_scheduling_city" class="control-label">City</label>
                    <select id="sec_scheduling_city" disabled="" dropdown="sec_scheduling" class="form-control city_dropdown" name="sec_scheduling_contact_city_id"  >
                        <option>{{@$client->sec_scheduling_city->name}}</option>
                    </select>

                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('sec_scheduling_contact_zip', 'Zip', ['class' => 'control-label']) !!}
                    {!! Form::text('sec_scheduling_contact_zip', $client->sec_scheduling_contact_zip, ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                  
                
                </div>
                </div>
            <div class="row">
                <div class="custom-heading">Billing Contact</div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('billing_contact_email', 'Title', ['class' => 'control-label']) !!}
                    {!! Form::text('billing_contact_email', $client->billing_contact_email, ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                 
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('billing_contact_name', 'Name', ['class' => 'control-label']) !!}
                    {!! Form::text('billing_contact_name', $client->billing_contact_name, ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                  
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('billing_contact_address', 'Address', ['class' => 'control-label']) !!}
                    {!! Form::text('billing_contact_address', $client->billing_contact_address, ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                </div>  
                <div class="col-xs-3 form-group">
                    {!! Form::label('billing_contact_phone', 'Phone', ['class' => 'control-label']) !!}
                    <?php if((new \Jenssegers\Agent\Agent())->isMobile()){?>
                    <a href="tel:<?=$client->billing_contact_phone;?>"><?=$client->billing_contact_phone;?></a>
                    <?php }else{?>
                    <input id="billing_contact_phone" disabled="" value="<?php echo $client->billing_contact_phone;?>" type="text" name="billing_contact_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask>
                    <?php }?>
                </div>
                
                <div class="col-xs-3 form-group{{ $errors->has('billing_contact_state_id') ? ' has-error' : '' }}">
                    <label for="billing_state" class="control-label">State</label>
                    <select id="billing_state" dropdown="billing" disabled="" class="form-control state_dropdown" name="billing_contact_state_id"  >
                       <option>{{@$client->billing_state->name}}</option>
                    </select>
                  
                </div>

                <div class="col-xs-3 form-group{{ $errors->has('billing_contact_city_id') ? ' has-error' : '' }}">
                    <label for="billing_city" class="control-label">City</label>
                    <select id="billing_city" dropdown="billing" disabled="" class="form-control city_dropdown" name="billing_contact_city_id"  >
                      <option>{{@$client->billing_city->name}}</option>
                    </select>

                   
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('billing_contact_zip', 'Zip', ['class' => 'control-label']) !!}
                    {!! Form::text('billing_contact_zip', $client->billing_contact_zip, ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                  
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-xs-3 form-group">
                    {!! Form::label('minbilling', 'Min. Billing', ['class' => 'control-label']) !!}
                    {!! Form::text('v', $client->minbilling, ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                  
                </div>
                <div class="col-xs-3 form-group{{ $errors->has('inv_type') ? ' has-error' : '' }}">
                    <label for="inv_type" class="control-label">Inventory Type</label>
                    <select id="inv_type" disabled="" class="form-control" name="inv_type"  >
                        <option>{{@$client->inv_type}}</option>
                    </select>

                </div>
                <div class="col-xs-3 form-group{{ $errors->has('billing') ? ' has-error' : '' }}">
                    <label for="billing" class="control-label required">Billing Type</label>
                    <select id="billing" class="form-control" disabled="" name="billing"  required="">
                        <option><?php echo $client->billing;?></option>
                    </select>

                </div>
                <div class="col-xs-3 form-group{{ $errors->has('rate_type') ? ' has-error' : '' }}">
                    <label for="rate_type" class="control-label">Rate Type</label>
                    <select id="rate_type" disabled="" class="form-control" name="rate_type" >
                        <option><?php echo $client->rate_type;?></option>
                    </select>
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('rate', 'Rate', ['class' => 'control-label']) !!}
                    {!! Form::text('rate', $client->rate, ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                  
                </div>
                <div class="col-xs-3 form-group">
                    <label for="rate_per" class="control-label">Rate Per</label>
                    <select id="rate_per" class="form-control" disabled="" name="rate_per" >
                        <option><?php echo $client->rate_per;?></option>
                    </select>
                </div>
                <div class="col-xs-3 form-group{{ $errors->has('days_avai_to_schedule') ? ' has-error' : '' }}">
                    <?php $schedule_availability_days = array();
                            if(count($client->schedule_availability_days)){foreach($client->schedule_availability_days as $day)$schedule_availability_days[] = $day->days;}?>
                    <label for="days_avai_to_schedule" class="control-label">Days Available to Schedule</label>
                    <select id="days_avai_to_schedule" disabled="" class="form-control select2" multiple="" name="days_avai_to_schedule[]" >
                        <option value="">Select Days</option>
                        <?php foreach ($schedule_availability_days as $key=>$feq){?>
                        <option selected="selected"><?php echo $feq;?></option>
                        <?php }?>
                    </select>

                  
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('start_time', 'Start Time', ['class' => 'control-label']) !!}
                    {!! Form::text('start_time', $client->start_time, ['class' => 'form-control timepicker','disabled', 'placeholder' => '']) !!}
                   
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('benchmark', 'Benchmark', ['class' => 'control-label']) !!}
                    {!! Form::text('benchmark', $client->benchmark, ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                  
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('max_length', 'Max. Length', ['class' => 'control-label']) !!}
                    {!! Form::text('max_length', $client->max_length, ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                  
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('min_auditors', 'Min. Auditors', ['class' => 'control-label']) !!}
                    {!! Form::text('min_auditors', $client->min_auditors, ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                  
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('spf', 'Supervisor Production Factor', ['class' => 'control-label']) !!}
                    {!! Form::text('spf', $client->spf, ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                  
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('alr_disk', 'ALR Disk', ['class' => 'control-label']) !!}
                    {!! Form::text('alr_disk', $client->alr_disk, ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                   
                </div>
                <div class="col-xs-3 form-group">
                    <label class="control-label">Count Stockroom</label><br>
                        <?php echo $client->count_stockroom;?>
                </div>
                <!-- <div class="col-xs-3 form-group">
                    <label class="control-label">Precall</label><br>
                      <?php echo $client->precall;?>
                </div> -->
               
            </div>
            <?php if($client->qccall=="Yes"){?>
            <div class="row">
            <div class="col-xs-3 form-group">
                    <label class="control-label">QC Call</label><br>
                      <?php echo $client->qccall;?>
                </div>
                <div class="col-xs-3 form-group">
                    <label class="control-label">Store or Other</label><br>
                    <?php echo $client->store_or_other;?>
                </div>
                <?php if($client->store_or_other=="other"){?>
                <div class="col-xs-3 form-group">
                    <label class="control-label">Other Contact Name</label><br>
                    <?php echo $client->other_contact_name;?>
                </div>
                <div class="col-xs-3 form-group">
                    <label class="control-label">Other Contact Number</label><br>
                    <?php echo $client->other_contact_number;?>
                </div>
                <?php }?>
            </div>
            <?php }?>
            <div class="row" >
            <div class="col-xs-3 form-group">
                    <label class="control-label">Precall</label><br>
                      <?php echo $client->precall;?>
                </div>
            
            <?php if($client->precall=="Yes"){?>
            <div>
                <div class="col-xs-3 form-group">
                    <label class="control-label">Store or Other</label><br>
                    <?php echo $client->picstore_or_other;?>
                </div>
                <?php if($client->picstore_or_other=="other"){?>
                <div class="col-xs-3 form-group">
                    <label class="control-label">Other Contact Name</label><br>
                    <?php echo $client->picother_contact_name;?>
                </div>
                <div class="col-xs-3 form-group">
                    <label class="control-label">Other Contact Number</label><br>
                    <?php echo $client->picother_contact_number;?>
                </div>
                <?php }?>
            </div>
            </div>
            <?php }?>
            <div class="row">
                <div class="col-xs-3 form-group">
                    <label class="control-label">Pieces or Dollars</label><br>
                       <?php echo title_case($client->pieces_or_dollars);?>
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('after_hour_contact_name', 'After Hours Contact Name', ['class' => 'control-label']) !!}
                    {!! Form::text('after_hour_contact_name', $client->after_hour_contact_name, ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('after_hour_contact_number', 'After Hours Contact Number', ['class' => 'control-label']) !!}
                    {!! Form::text('after_hour_contact_number', $client->after_hour_contact_number, ['class' => 'form-control','disabled','placeholder' => '']) !!}
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('terms', 'Terms', ['class' => 'control-label']) !!}
                    {!! Form::textarea('terms', $client->terms, ['class' => 'form-control ','disabled', 'placeholder' => '']) !!}
                  
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('notes', 'Notes', ['class' => 'control-label']) !!}
                    {!! Form::textarea('notes', $client->notes, ['class' => 'form-control ','disabled', 'placeholder' => '']) !!}
                    
                </div>
            </div>
            <input type="hidden" value="<?php echo ($client->blackout_dates)?count($client->blackout_dates):1;?>" id="blackout_counter">
            <div class="blackout_dates_container">
               <?php if(count($client->blackout_dates)){foreach($client->blackout_dates as $key=>$dates){?> 
                    <div class="col-xs-2 blackout_counter<?=$key;?>" style="height:75px;">
                        {!! Form::label('blackout_dates', 'Blackout Dates', ['class' => 'control-label']) !!}
                        {!! Form::text('blackout_dates[]', date("m/d/Y", strtotime($dates->date)), ['class' => 'form-control datepicker','disabled', 'placeholder' => '']) !!}
                        
                    </div>
                   
               <?php }}?>
            </div>
            
            
            
        </div>
    </div>

    <a href="{{ route('admin.clients.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
    
@stop
@section('javascript')
    @parent
   <script type="text/javascript">
    $(document).ready(function(){
        $('body').on('focus',".datepicker", function(){
            if( $(this).hasClass('hasDatepicker') === false )  {
                $(this).datepicker({
            autoclose: true,
            format:'mm-dd-yyyy'
        });
            }

        });
        $('.datepicker').datepicker({
            autoclose: true,
            format:'mm-dd-yyyy'
        })
        $('.blackout_dates_container').on('click', '.remove_blackout', function(events){
            var blackout_counter = $(this).attr('blackout');
            $('.blackout_counter'+blackout_counter).remove();
        });
        $('.add_more').click(function(){
            var blackout_counter = $('#blackout_counter').val();
            blackout_counter++;
            var html ='<div style="height:75px;" class="col-xs-2 blackout_counter'+blackout_counter+'"><label for="blackout_dates" class="control-label">Blackout Dates</label>\n\
                        <input class="form-control datepicker" name="blackout_dates[]" type="text" autocomplete="off"></div><div style="height:75px;" blackout="'+blackout_counter+'" class="col-xs-1 remove_blackout blackout_counter'+blackout_counter
                        +'"><i class="fa fa-trash" aria-hidden="true"></i></div>';
            $('#blackout_counter').val(blackout_counter);
            $(".blackout_dates_container").append(html);
           // alert('sdf');
        })
        $('[data-mask]').inputmask();
        $('.timepicker').timepicker({
            showInputs: false
        })
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
        
   });})
</script>
@stop