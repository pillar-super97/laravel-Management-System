@extends('layouts.app')
@section('pageTitle', 'View Store')
@section('content')
    <h3 class="page-title">Stores</h3>
    
    {!! Form::model($store, ['method' => 'PUT', 'route' => ['admin.stores.update', $store->id], 'files' => true,'autocomplete'=>'off']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">Edit Store</div>
        <?php $arrays = arrays();?>
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-3 form-group">
                    {!! Form::label('number', 'Number', ['class' => 'control-label required']) !!}
                    {!! Form::text('number', old('number'), ['class' => 'form-control','disabled', 'placeholder' => '', 'required' => '']) !!}
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('name', 'Name', ['class' => 'control-label required']) !!}
                    {!! Form::text('name', old('name'), ['class' => 'form-control','disabled', 'placeholder' => '', 'required' => '']) !!}
                </div>
                <div class="col-xs-3 form-group{{ $errors->has('manager_id') ? ' has-error' : '' }}">
                    {!! Form::label('manager_id', 'Manager', ['class' => 'control-label']) !!}
                    {!! Form::text('manager_id', old('manager_id'), ['class' => 'form-control','disabled']) !!}
                </div>
                <div class="col-xs-3 form-group{{ $errors->has('client_id') ? ' has-error' : '' }}">
                    <label for="client_id" class="control-label">Client</label>
                    <select id="client_id" class="form-control" disabled="" name="client_id" >
                        <option><?php echo @$store->client->name;?></option>
                    </select>
                </div>
                <div class="col-xs-3 form-group{{ $errors->has('division_id') ? ' has-error' : '' }}">
                    <label for="division_id" class="control-label">Division</label>
                    <select id="division_id" class="form-control" disabled="" name="division_id" >
                        <option><?php echo @$store->division->name;?></option>
                    </select>
                </div>
                <div class="col-xs-3 form-group{{ $errors->has('district_id') ? ' has-error' : '' }}">
                    <label for="district_id" class="control-label">District</label>
                    <select id="district_id" class="form-control" disabled="" name="district_id" >
                        <option><?php echo @$store->district->number;?></option>
                    </select>
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('address', 'Address', ['class' => 'control-label required']) !!}
                    {!! Form::text('address', old('address'), ['class' => 'form-control','disabled', 'placeholder' => '','required' => '']) !!}
                </div>
                

                <div class="col-xs-3 form-group{{ $errors->has('state_id') ? ' has-error' : '' }}">
                    <label for="primary_state" class="control-label required">State</label>
                    <select id="primary_state" dropdown="primary" disabled="" class="form-control state_dropdown" name="state_id"  required="">
                        <option><?php echo @$store->state->name;?></option>
                    </select>
                </div>

                <div class="col-xs-3 form-group{{ $errors->has('city_id') ? ' has-error' : '' }}">
                    <label for="primary_city" class="control-label required">City</label>
                    <select id="primary_city" dropdown="primary" disabled="" class="form-control city_dropdown" name="city_id"  required="">
                        <option><?php echo @$store->city->name;?></option>
                    </select>
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('zip', 'Zip', ['class' => 'control-label required']) !!}
                    {!! Form::text('zip', old('zip'), ['class' => 'form-control','disabled', 'placeholder' => '','required' => '']) !!}
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('phone', 'Phone Number', ['class' => 'control-label required']) !!}
                    <input id="phone" type="text" name="phone" disabled="" value="<?=$store->phone;?>" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask required="required">
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
                    <input id="scheduling_contact_phone" type="text" disabled="" value="<?php echo $store->scheduling_contact_phone;?>" name="scheduling_contact_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask>
                </div>
                
                <div class="col-xs-3 form-group{{ $errors->has('scheduling_contact_state_id') ? ' has-error' : '' }}">
                    <label for="scheduling_state" class="control-label required">State</label>
                    <select id="scheduling_state" dropdown="scheduling" disabled="" class="form-control state_dropdown" name="scheduling_contact_state_id"  required="">
                         <option><?php echo @$store->scheduling_state->name;?></option>
                    </select>
                </div>

                <div class="col-xs-3 form-group{{ $errors->has('scheduling_contact_city_id') ? ' has-error' : '' }}">
                    <label for="scheduling_city" class="control-label required">City</label>
                    <select id="scheduling_city" dropdown="scheduling" disabled="" class="form-control city_dropdown" name="scheduling_contact_city_id"  required="">
                        <option><?php echo @$store->scheduling_city->name;?></option>
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
                    <input id="sec_scheduling_contact_phone" disabled="" value="<?php echo $store->sec_scheduling_contact_phone;?>" type="text" name="sec_scheduling_contact_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask>
                </div>
                
                <div class="col-xs-3 form-group{{ $errors->has('sec_scheduling_contact_state_id') ? ' has-error' : '' }}">
                    <label for="sec_scheduling_state" class="control-label">State</label>
                    <select id="sec_scheduling_state" dropdown="sec_scheduling" disabled="" class="form-control state_dropdown" name="sec_scheduling_contact_state_id"  >
                        <option><?php echo @$store->sec_scheduling_state->name;?></option>
                    </select>
                   
                </div>

                <div class="col-xs-3 form-group{{ $errors->has('sec_scheduling_contact_city_id') ? ' has-error' : '' }}">
                    <label for="sec_scheduling_city" class="control-label">City</label>
                    <select id="sec_scheduling_city" dropdown="sec_scheduling" disabled="" class="form-control city_dropdown" name="sec_scheduling_contact_city_id"  >
                        <option><?php echo @$store->sec_scheduling_city->name;?></option>
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
                    <input id="billing_contact_phone" type="text" disabled="" value="<?php echo $store->billing_contact_phone;?>" name="billing_contact_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask>
                   
                </div>
                
                <div class="col-xs-3 form-group{{ $errors->has('billing_contact_state_id') ? ' has-error' : '' }}">
                    <label for="billing_state" class="control-label">State</label>
                    <select id="billing_state" dropdown="billing" disabled="" class="form-control state_dropdown" name="billing_contact_state_id"  >
                        <option><?php echo @$store->billing_state->name;?></option>
                    </select>
                   
                </div>

                <div class="col-xs-3 form-group{{ $errors->has('billing_contact_city_id') ? ' has-error' : '' }}">
                    <label for="billing_city" class="control-label">City</label>
                    <select id="billing_city" dropdown="billing" disabled="" class="form-control city_dropdown" name="billing_contact_city_id"  >
                       <option><?php echo @$store->billing_city->name;?></option>
                    </select>

                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('billing_contact_zip', 'Zip', ['class' => 'control-label']) !!}
                    {!! Form::text('billing_contact_zip', old('billing_contact_zip'), ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-xs-3 form-group{{ $errors->has('billing') ? ' has-error' : '' }}">
                    <label for="billing" class="control-label">Billing</label>
                    <select id="billing" class="form-control" disabled="" name="billing"  >
                        <option><?php echo $store->billing;?></option>
                    </select>
                </div>
                <div class="col-xs-3 form-group{{ $errors->has('store_type') ? ' has-error' : '' }}">
                    <label for="store_type" class="control-label">Store Type</label>
                    <select id="store_type" class="form-control" disabled="" name="store_type"  >
                        <option><?php echo $store->store_type;?></option>
                    </select>
                </div>
                <div class="col-xs-3 form-group{{ $errors->has('frequency') ? ' has-error' : '' }}">
                    <label for="frequency" class="control-label">Frequency</label>
                    <select id="frequency" class="form-control" disabled="" name="frequency"  >
                        <option><?php echo $store->frequency;?></option>
                    </select>
                </div>
                <div class="col-xs-3 form-group{{ $errors->has('inv_type') ? ' has-error' : '' }}">
                    <label for="inv_type" class="control-label">Inventory Type</label>
                    <select id="inv_type" class="form-control" disabled="" name="inv_type"  >
                        <option><?php echo $store->inv_type;?></option>
                    </select>
                </div>
                <div class="col-xs-3 form-group{{ $errors->has('rate_per') ? ' has-error' : '' }}">
                    <label for="days_avai_to_schedule" class="control-label">Days Available to Schedule</label>
                    <?php $schedule_availability_days = array();
                            if(count($store->schedule_availability_days)){foreach($store->schedule_availability_days as $day)$schedule_availability_days[] = $day->days;}
                            //print_r($schedule_availability_days);die;?>
                    <select id="days_avai_to_schedule" class="form-control select2" disabled="" multiple="" name="days_avai_to_schedule[]" >
                        <option value="">Select Days</option>
                        <?php foreach ($schedule_availability_days as $key=>$feq){?>
                        <option selected="selected"><?php echo $feq;?></option>
                        <?php }?>
                    </select>

                </div>
                <div class="col-xs-3 form-group{{ $errors->has('rate_per') ? ' has-error' : '' }}">
                    <label for="days_avai_to_schedule" class="control-label">Months Available to Schedule</label>
                    <?php $schedule_months = array();
                            if(count($store->schedule_months)){foreach($store->schedule_months as $day)$schedule_months[] = $day->month;}
                            //print_r($schedule_months);die;?>
                    <select id="month_to_schedule" class="form-control select2" disabled="" multiple="" name="days_avai_to_schedule[]" >
                        <option value="">Select Months</option>
                        <?php foreach ($schedule_months as $key=>$feq){?>
                        <option selected="selected"><?php echo $feq;?></option>
                        <?php }?>
                    </select>

                </div>
                <div class="col-xs-3 form-group{{ $errors->has('rate_type') ? ' has-error' : '' }}">
                    <label for="rate_type" class="control-label">Rate Type</label>
                    <select id="rate_type" class="form-control" disabled="" name="rate_type" >
                        <option><?php echo $store->rate_type;?></option>
                    </select>
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('rate', 'Rate', ['class' => 'control-label']) !!}
                    {!! Form::text('rate', old('rate'), ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('rate_effective_date', 'Rate Effective Date', ['class' => 'control-label']) !!}
                    {!! Form::text('rate_effective_date', old('rate_effective_date'), ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                </div>
            
                <div class="col-xs-3 form-group{{ $errors->has('rate_per') ? ' has-error' : '' }}">
                    <label for="rate_per" class="control-label">Rate Per</label>
                    <select id="rate_per" class="form-control" disabled="" name="rate_per" >
                        <option><?php echo $store->rate_per;?></option>
                    </select>
                </div>
                
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
                    {!! Form::label('travel_charge', 'Travel Charge', ['class' => 'control-label']) !!}
                    {!! Form::text('travel_charge', old('travel_charge'), ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('overnight_charge', 'Overnight Charge', ['class' => 'control-label']) !!}
                    {!! Form::text('overnight_charge', old('overnight_charge'), ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('inventory_level', 'Inventory Level', ['class' => 'control-label']) !!}
                    {!! Form::text('inventory_level', old('inventory_level'), ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                </div>
                <div class="col-xs-3 form-group">
                    <label for="apr" class="control-label">APR</label>
                    <select id="apr" class="form-control select2" disabled="" multiple="" name="apr" >
                        <?php //foreach ($store->apr as $apr){?>
                        <option selected="selected"><?php echo @$store->area_prime_responsibility->title;?></option>
                        <?php //}?>
                    </select>

                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('surcharge_fee', 'Surcharge Fee', ['class' => 'control-label']) !!}
                    {!! Form::text('surcharge_fee', old('surcharge_fee'), ['class' => 'form-control','disabled']) !!}
                </div>
<!--                <div class="col-xs-3 form-group">
                    <label for="jsa" class="control-label">JSA</label>
                    <select id="jsa" class="form-control select2" disabled="" multiple="" name="jsa[]" >
                        <?php //foreach ($store->jsa as $jsa){?>
                        <option selected="selected"><?php //echo $jsa->jsa->title;?></option>
                        <?php //}?>
                    </select>

                </div>-->
                
                <div class="col-xs-3 form-group">
                    <label class="control-label">Count Stockroom</label><br>
                    <?php echo $store->count_stockroom;?>
                </div>
                <div class="col-xs-3 form-group">
                    <label class="control-label">Precall</label><br>
                   <?php echo $store->precall;?>
                </div>
                <div class="col-xs-3 form-group">
                    <label class="control-label">QC Call</label><br>
                   <?php echo $store->qccall;?>
                </div>
                </div>
            <div class="row">
                <div class="col-xs-3 form-group">
                    <label class="control-label">Pieces or Dollars</label><br>
                   <?php echo title_case($store->pieces_or_dollars);?>
                </div>
                <div class="col-xs-3 form-group">
                    <label class="control-label">Fuel Center</label><br>
                   <?php echo title_case($store->fuel_center);?>
                </div>
                <div class="col-xs-3 form-group">
                    <label class="control-label">RX</label><br>
                   <?php echo title_case($store->rx);?>
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('after_hour_contact_name', 'After Hours Contact Name', ['class' => 'control-label']) !!}
                    {!! Form::text('after_hour_contact_name', old('after_hour_contact_name'), ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('after_hour_contact_number', 'After Hours Contact Number', ['class' => 'control-label']) !!}
                    {!! Form::text('after_hour_contact_number', old('after_hour_contact_number'), ['class' => 'form-control','disabled','placeholder' => '']) !!}
                </div>
            
            </div>
            
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('terms', 'Terms', ['class' => 'control-label']) !!}
                    {!! Form::textarea('terms', old('terms'), ['class' => 'form-control ','disabled', 'placeholder' => '']) !!}
                </div>
                <div class="col-xs-12 form-group">
                    {!! Form::label('notes', 'Notes', ['class' => 'control-label']) !!}
                    {!! Form::textarea('notes', old('notes'), ['class' => 'form-control ','disabled', 'placeholder' => '']) !!}
                </div>
            </div>
            
                
            
            
            
        </div>
    </div>

   <a href="{{ route('admin.stores.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
@stop
@section('javascript')
    @parent
  
@stop