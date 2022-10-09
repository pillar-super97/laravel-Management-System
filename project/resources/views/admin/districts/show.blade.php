@extends('layouts.app')
@section('pageTitle', 'View District')
@section('content')
    <h3 class="page-title">Districts</h3>
    
    {!! Form::model($district, ['method' => 'PUT', 'route' => ['admin.districts.update', $district->id], 'files' => true,'autocomplete'=>'off']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">View District</div>
        <?php $arrays = arrays();?>
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-3 form-group">
                    {!! Form::label('number', 'Number', ['class' => 'control-label required']) !!}
                    {!! Form::text('number', old('number'), ['class' => 'form-control','disabled', 'placeholder' => '', 'required' => '']) !!}
                </div>
                <div class="col-xs-3 form-group{{ $errors->has('manager') ? ' has-error' : '' }}">
                    {!! Form::label('manager', 'Manager', ['class' => 'control-label']) !!}
                    {!! Form::text('manager', old('manager'), ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                </div>
                <div class="col-xs-3 form-group{{ $errors->has('client_id') ? ' has-error' : '' }}">
                    <label for="client_id" class="control-label">Client</label>
                    <select id="client_id" class="form-control" disabled="" name="client_id" >
                        <option><?php echo @$district->client->name;?></option>
                    </select>
                </div>
                <div class="col-xs-3 form-group{{ $errors->has('division_id') ? ' has-error' : '' }}">
                    <label for="division_id" class="control-label">Division</label>
                    <select id="division_id" class="form-control" disabled="" name="division_id" >
                        <option><?php echo @$district->division->name;?></option>
                    </select>
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('address', 'Address', ['class' => 'control-label required']) !!}
                    {!! Form::text('address', old('address'), ['class' => 'form-control','disabled', 'placeholder' => '','required' => '']) !!}
                </div>
                

                <div class="col-xs-3 form-group{{ $errors->has('state_id') ? ' has-error' : '' }}">
                    <label for="primary_state" class="control-label required">State</label>
                    <select id="primary_state" dropdown="primary" disabled="" class="form-control state_dropdown" name="state_id"  required="">
                        <option><?php echo @$district->state->name;?></option>
                    </select>
                </div>

                <div class="col-xs-3 form-group{{ $errors->has('city_id') ? ' has-error' : '' }}">
                    <label for="primary_city" class="control-label required">City</label>
                    <select id="primary_city" dropdown="primary" disabled="" class="form-control city_dropdown" name="city_id"  required="">
                        <option><?php echo @$district->city->name;?></option>
                    </select>
                </div>
            
                
                <div class="col-xs-3 form-group">
                    {!! Form::label('zip', 'Zip', ['class' => 'control-label required']) !!}
                    {!! Form::text('zip', old('zip'), ['class' => 'form-control','disabled', 'placeholder' => '','required' => '']) !!}
                </div>
            </div>
            <div class="row">
                <div class="col-xs-3 form-group">
                    {!! Form::label('phone', 'Phone Number', ['class' => 'control-label required']) !!}
                    <?php if((new \Jenssegers\Agent\Agent())->isMobile()){?>
                    <a href="tel:<?=$district->phone;?>"><?=$district->phone;?></a>
                    <?php }else{?>
                    <input id="phone" type="text" name="phone" disabled="" value="<?=$district->phone;?>" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask required="required">
                    <?php }?>
                </div>
                
                <div class="col-xs-3 form-group{{ $errors->has('frequency') ? ' has-error' : '' }}">
                    <label for="frequency" class="control-label">Frequency</label>
                    <select id="frequency" class="form-control" disabled="" name="frequency"  >
                        <option><?php echo $district->frequency;?></option>
                    </select>
                </div>
                <div class="col-xs-3 form-group{{ $errors->has('inv_type') ? ' has-error' : '' }}">
                    <label for="inv_type" class="control-label">Inventory Type</label>
                    <select id="inv_type" class="form-control" disabled="" name="inv_type"  >
                        <option><?php echo $district->inv_type;?></option>
                    </select>
                </div>
                <div class="col-xs-3 form-group{{ $errors->has('rate_per') ? ' has-error' : '' }}">
                    <label for="days_avai_to_schedule" class="control-label">Days Available to Schedule</label>
                    <?php $schedule_availability_days = array();
                            if(count($district->schedule_availability_days)){foreach($district->schedule_availability_days as $day)$schedule_availability_days[] = $day->days;}
                            //print_r($schedule_availability_days);die;?>
                    <select id="days_avai_to_schedule" class="form-control select2" disabled="" multiple="" name="days_avai_to_schedule[]" >
                        <option value="">Select Days</option>
                        <?php foreach ($schedule_availability_days as $key=>$feq){?>
                        <option selected="selected"><?php echo $feq;?></option>
                        <?php }?>
                    </select>

                </div>
            </div>
            <div class="row">
                <div class="col-xs-3 form-group{{ $errors->has('rate_type') ? ' has-error' : '' }}">
                    <label for="rate_type" class="control-label">Rate Type</label>
                    <select id="rate_type" class="form-control" disabled="" name="rate_type" >
                        <option><?php echo $district->rate_type;?></option>
                    </select>
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('rate', 'Rate', ['class' => 'control-label']) !!}
                    {!! Form::text('rate', old('rate'), ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                </div>
                <div class="col-xs-3 form-group{{ $errors->has('rate_per') ? ' has-error' : '' }}">
                    <label for="rate_per" class="control-label">Rate Per</label>
                    <select id="rate_per" class="form-control" disabled="" name="rate_per" >
                        <option><?php echo $district->rate_per;?></option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="custom-heading">Scheduling Contact</div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('scheduling_contact_email', 'Email', ['class' => 'control-label required']) !!}
                    {!! Form::text('scheduling_contact_email', old('scheduling_contact_email'), ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('scheduling_contact_name', 'Name', ['class' => 'control-label required']) !!}
                    {!! Form::text('scheduling_contact_name', old('scheduling_contact_name'), ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('scheduling_contact_address', 'Address', ['class' => 'control-label required']) !!}
                    {!! Form::text('scheduling_contact_address', old('scheduling_contact_address'), ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('scheduling_contact_phone', 'Phone', ['class' => 'control-label required']) !!}
                    <?php if((new \Jenssegers\Agent\Agent())->isMobile()){?>
                    <a href="tel:<?=$district->scheduling_contact_phone;?>"><?=$district->scheduling_contact_phone;?></a>
                    <?php }else{?>
                    <input id="scheduling_contact_phone" type="text" disabled="" value="<?php echo $district->scheduling_contact_phone;?>" name="scheduling_contact_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask>
                    <?php }?>
                </div>
                
                <div class="col-xs-3 form-group{{ $errors->has('scheduling_contact_state_id') ? ' has-error' : '' }}">
                    <label for="scheduling_state" class="control-label required">State</label>
                    <select id="scheduling_state" dropdown="scheduling" disabled="" class="form-control state_dropdown" name="scheduling_contact_state_id"  required="">
                         <option><?php echo @$district->scheduling_state->name;?></option>
                    </select>
                </div>

                <div class="col-xs-3 form-group{{ $errors->has('scheduling_contact_city_id') ? ' has-error' : '' }}">
                    <label for="scheduling_city" class="control-label required">City</label>
                    <select id="scheduling_city" dropdown="scheduling" disabled="" class="form-control city_dropdown" name="scheduling_contact_city_id"  required="">
                        <option><?php echo @$district->scheduling_city->name;?></option>
                    </select>
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('scheduling_contact_zip', 'Zip', ['class' => 'control-label']) !!}
                    {!! Form::text('scheduling_contact_zip', old('scheduling_contact_zip'), ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                </div>
                </div>
            <div class="row">
                <div class="custom-heading">Secondary Scheduling Contact</div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('sec_scheduling_contact_email', 'Email', ['class' => 'control-label']) !!}
                    {!! Form::text('sec_scheduling_contact_email', old('sec_scheduling_contact_email'), ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('sec_scheduling_contact_name', 'Name', ['class' => 'control-label']) !!}
                    {!! Form::text('sec_scheduling_contact_name', old('sec_scheduling_contact_name'), ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('sec_scheduling_contact_address', 'Address', ['class' => 'control-label']) !!}
                    {!! Form::text('sec_scheduling_contact_address', old('sec_scheduling_contact_address'), ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('sec_scheduling_contact_phone', 'Phone', ['class' => 'control-label']) !!}
                    <?php if((new \Jenssegers\Agent\Agent())->isMobile()){?>
                    <a href="tel:<?=$district->sec_scheduling_contact_phone;?>"><?=$district->sec_scheduling_contact_phone;?></a>
                    <?php }else{?>
                    <input id="sec_scheduling_contact_phone" disabled="" value="<?php echo $district->sec_scheduling_contact_phone;?>" type="text" name="sec_scheduling_contact_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask>
                    <?php }?>
                </div>
                
                <div class="col-xs-3 form-group{{ $errors->has('sec_scheduling_contact_state_id') ? ' has-error' : '' }}">
                    <label for="sec_scheduling_state" class="control-label">State</label>
                    <select id="sec_scheduling_state" dropdown="sec_scheduling" disabled="" class="form-control state_dropdown" name="sec_scheduling_contact_state_id"  >
                        <option><?php echo @$district->sec_scheduling_state->name;?></option>
                    </select>
                   
                </div>

                <div class="col-xs-3 form-group{{ $errors->has('sec_scheduling_contact_city_id') ? ' has-error' : '' }}">
                    <label for="sec_scheduling_city" class="control-label">City</label>
                    <select id="sec_scheduling_city" dropdown="sec_scheduling" disabled="" class="form-control city_dropdown" name="sec_scheduling_contact_city_id"  >
                        <option><?php echo @$district->sec_scheduling_city->name;?></option>
                    </select>
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('sec_scheduling_contact_zip', 'Zip', ['class' => 'control-label']) !!}
                    {!! Form::text('sec_scheduling_contact_zip', old('sec_scheduling_contact_zip'), ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                </div>
                </div>
            <div class="row">
                <div class="custom-heading">Billing Contact</div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('billing_contact_email', 'Email', ['class' => 'control-label']) !!}
                    {!! Form::text('billing_contact_email', old('billing_contact_email'), ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('billing_contact_name', 'Name', ['class' => 'control-label']) !!}
                    {!! Form::text('billing_contact_name', old('billing_contact_name'), ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                    
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('billing_contact_address', 'Address', ['class' => 'control-label']) !!}
                    {!! Form::text('billing_contact_address', old('billing_contact_address'), ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                   
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('billing_contact_phone', 'Phone', ['class' => 'control-label']) !!}
                    <?php if((new \Jenssegers\Agent\Agent())->isMobile()){?>
                    <a href="tel:<?=$district->billing_contact_phone;?>"><?=$district->billing_contact_phone;?></a>
                    <?php }else{?>
                    <input id="billing_contact_phone" type="text" disabled="" value="<?php echo $district->billing_contact_phone;?>" name="billing_contact_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask>
                    <?php }?>
                </div>
                

                <div class="col-xs-3 form-group{{ $errors->has('billing_contact_state_id') ? ' has-error' : '' }}">
                    <label for="billing_state" class="control-label">State</label>
                    <select id="billing_state" dropdown="billing" disabled="" class="form-control state_dropdown" name="billing_contact_state_id"  >
                        <option><?php echo @$district->billing_state->name;?></option>
                    </select>
                   
                </div>

                <div class="col-xs-3 form-group{{ $errors->has('billing_contact_city_id') ? ' has-error' : '' }}">
                    <label for="billing_city" class="control-label">City</label>
                    <select id="billing_city" dropdown="billing" disabled="" class="form-control city_dropdown" name="billing_contact_city_id"  >
                       <option><?php echo @$district->billing_city->name;?></option>
                    </select>

                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('billing_contact_zip', 'Zip', ['class' => 'control-label']) !!}
                    {!! Form::text('billing_contact_zip', old('billing_contact_zip'), ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                </div>
            </div>
            <div class="row">
                <div class="custom-heading">Stores Within District</div>
                <?php foreach($store_arr as $row)
                {
                   echo '<div class="col-xs-3 form-group">'.$row.'</div>'; 
                }?>
                
            </div>
            <hr>
            <div class="row">
                
                
                <div class="col-xs-3 form-group">
                    {!! Form::label('start_time', 'Start Time', ['class' => 'control-label']) !!}
                    {!! Form::text('start_time', old('start_time'), ['class' => 'form-control timepicker','disabled', 'placeholder' => '']) !!}
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('benchmark', 'Benchmark', ['class' => 'control-label']) !!}
                    {!! Form::text('benchmark', old('benchmark'), ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('max_length', 'Max. Length', ['class' => 'control-label']) !!}
                    {!! Form::text('max_length', old('max_length'), ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('min_auditors', 'Min. Auditors', ['class' => 'control-label']) !!}
                    {!! Form::text('min_auditors', old('min_auditors'), ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('spf', 'Supervisor Production Factor', ['class' => 'control-label']) !!}
                    {!! Form::text('spf', old('spf'), ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('alr_disk', 'ALR Disk', ['class' => 'control-label']) !!}
                    {!! Form::text('alr_disk', old('alr_disk'), ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                </div>
                <div class="col-xs-3 form-group">
                    <label class="control-label">Count Stockroom</label><br>
                    <?php echo $district->count_stockroom;?>
                </div>
                <!-- <div class="col-xs-3 form-group">
                    <label class="control-label">Precall</label><br>
                   <?php echo $district->precall;?>
                </div> -->
            </div>
            <div class="row">
                <div class="col-xs-3 form-group">
                    <label class="control-label">QC Call</label><br>
                   <?php echo $district->qccall;?>
                </div>
                <?php if($district->qccall=="Yes"){?>
            
                <div class="col-xs-3 form-group">
                    <label class="control-label">Store or Other</label><br>
                    <?php echo $district->store_or_other;?>
                </div>
                <?php if($district->store_or_other=="other"){?>
                <div class="col-xs-3 form-group">
                    <label class="control-label">Other Contact Name</label><br>
                    <?php echo $district->other_contact_name;?>
                </div>
                <div class="col-xs-3 form-group">
                    <label class="control-label">Other Contact Number</label><br>
                    <?php echo $district->other_contact_number;?>
                </div>
                <?php }?>
            
            <?php }?>
            </div>
            <div class="row" >
            <div class="col-xs-3 form-group">
                    <label class="control-label">Precall</label><br>
                      <?php echo $district->precall;?>
                </div>
            
            <?php if($district->precall=="Yes"){?>
            <div>
                <div class="col-xs-3 form-group">
                    <label class="control-label">Store or Other</label><br>
                    <?php echo $district->picstore_or_other;?>
                </div>
                <?php if($district->picstore_or_other=="other"){?>
                <div class="col-xs-3 form-group">
                    <label class="control-label">Other Contact Name</label><br>
                    <?php echo $district->picother_contact_name;?>
                </div>
                <div class="col-xs-3 form-group">
                    <label class="control-label">Other Contact Number</label><br>
                    <?php echo $district->picother_contact_number;?>
                </div>
                <?php }?>
            </div>
            <?php }?>
            </div>
            <div class="row">
                <div class="col-xs-3 form-group">
                    <label class="control-label">Pieces or Dollars</label><br>
                   <?php echo title_case($district->pieces_or_dollars);?>
                </div>
                <div class="col-xs-3 form-group{{ $errors->has('billing') ? ' has-error' : '' }}">
                    <label for="billing" class="control-label">Rate Type</label>
                    <select id="billing" class="form-control" disabled="" name="billing" >
                        <option><?php echo $district->billing;?></option>
                    </select>
                </div>
            </div>
            
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('description', 'Description', ['class' => 'control-label']) !!}
                    {!! Form::textarea('description', old('description'), ['class' => 'form-control ','disabled', 'placeholder' => '']) !!}
                </div>
                <div class="col-xs-12 form-group">
                    {!! Form::label('notes', 'Notes', ['class' => 'control-label']) !!}
                    {!! Form::textarea('notes', old('notes'), ['class' => 'form-control ','disabled', 'placeholder' => '']) !!}
                </div>
                
                <div class="col-xs-12 form-group">
                    {!! Form::label('terms', 'Terms', ['class' => 'control-label']) !!}
                    {!! Form::textarea('terms', old('terms'), ['class' => 'form-control ','disabled', 'placeholder' => '']) !!}
                </div>
            </div>
            
                
            
            
            
        </div>
    </div>

   <a href="{{ route('admin.districts.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
@stop
@section('javascript')
    @parent
  
@stop