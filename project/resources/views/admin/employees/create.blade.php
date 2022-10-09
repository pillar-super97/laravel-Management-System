@extends('layouts.app')
@section('pageTitle', 'Add Employee')
@section('content')
    <h3 class="page-title">Employees</h3>
    {!! Form::open(['method' => 'POST', 'route' => ['admin.employees.store'], 'files' => true,]) !!}

    <div class="panel panel-default">
        <div class="panel-heading">Add New Employee</div>
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
                <div class="col-xs-3 form-group">
                    {!! Form::label('number', 'Number', ['class' => 'control-label required']) !!}
                    {!! Form::text('number', old('number'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('number'))
                        <p class="error-block">
                            {{ $errors->first('number') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group{{ $errors->has('manager_id') ? ' has-error' : '' }}">
                    <label for="manager_id" class="control-label">Manager</label>
                    <select id="manager_id" class="form-control" name="manager_id" >
                        <option value="">Select Manager</option>
                        <?php foreach ($employees as $key=>$employee){?>
                        <option value="<?php echo $key;?>"><?php echo $employee;?></option>
                        <?php }?>
                    </select>

                    @if ($errors->has('manager_id'))
                        <p class="error-block">
                            {{ $errors->first('manager_id') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group{{ $errors->has('client_id') ? ' has-error' : '' }}">
                    <label for="client_id" class="control-label">Client</label>
                    <select id="client_id" class="form-control client_id" name="client_id" >
                        <option value="">Select Client</option>
                        <?php foreach ($clients as $key=>$client){?>
                        <option value="<?php echo $key;?>"><?php echo $client;?></option>
                        <?php }?>
                    </select>

                    @if ($errors->has('client_id'))
                        <p class="error-block">
                            {{ $errors->first('client_id') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group{{ $errors->has('division_id') ? ' has-error' : '' }}">
                    <label for="division_id" class="control-label">Division</label>
                    <select id="division_id" class="form-control division_id" name="division_id" >
                        <option value="">Select Division</option>
                    </select>

                    @if ($errors->has('division_id'))
                        <p class="error-block">
                            {{ $errors->first('division_id') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
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
            </div>
            <div class="row">
                
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
                <div class="col-xs-3 form-group">
                    {!! Form::label('phone', 'Phone Number', ['class' => 'control-label required']) !!}
                    <input id="phone" type="text" name="phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask required="required">
                    <p class="help-block"></p>
                    @if($errors->has('phone'))
                        <p class="error-block">
                            {{ $errors->first('phone') }}
                        </p>
                    @endif
                </div>
                
                <div class="col-xs-3 form-group{{ $errors->has('frequency') ? ' has-error' : '' }}">
                    <label for="frequency" class="control-label">Frequency</label>
                    <select id="frequency" class="form-control" name="frequency"  >
                        <option value="">Select Frequency</option>
                        <?php foreach ($arrays['frequency'] as $key=>$feq){?>
                        <option value="<?php echo $feq;?>"><?php echo $feq;?></option>
                        <?php }?>
                    </select>

                    @if ($errors->has('frequency'))
                        <p class="error-block">
                            {{ $errors->first('frequency') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group{{ $errors->has('inv_type') ? ' has-error' : '' }}">
                    <label for="inv_type" class="control-label">Inventory Type</label>
                    <select id="inv_type" class="form-control" name="inv_type"  >
                        <option value="">Select Inventory Type</option>
                        <?php foreach ($arrays['inv_type'] as $key=>$feq){?>
                        <option value="<?php echo $feq;?>"><?php echo $feq;?></option>
                        <?php }?>
                    </select>

                    @if ($errors->has('inv_type'))
                        <p class="error-block">
                            {{ $errors->first('inv_type') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group{{ $errors->has('rate_per') ? ' has-error' : '' }}">
                    <label for="days_avai_to_schedule" class="control-label">Days Available to Schedule</label>
                    <select id="days_avai_to_schedule" class="form-control select2" multiple="" name="days_avai_to_schedule[]" >
                        <option value="">Select Days</option>
                        <?php foreach ($arrays['days'] as $key=>$feq){?>
                        <option value="<?php echo $feq;?>"><?php echo $feq;?></option>
                        <?php }?>
                    </select>

                    @if ($errors->has('rate_per'))
                        <p class="error-block">
                            {{ $errors->first('rate_per') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group{{ $errors->has('rate_type') ? ' has-error' : '' }}">
                    <label for="rate_type" class="control-label">Rate Type</label>
                    <select id="rate_type" class="form-control" name="rate_type" >
                        <option value="">Select Rate Type</option>
                        <?php foreach ($arrays['rate_type'] as $key=>$feq){?>
                        <option value="<?php echo $feq;?>"><?php echo $feq;?></option>
                        <?php }?>
                    </select>

                    @if ($errors->has('rate_type'))
                        <p class="error-block">
                            {{ $errors->first('rate_type') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('rate', 'Rate', ['class' => 'control-label']) !!}
                    {!! Form::text('rate', old('rate'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('rate'))
                        <p class="error-block">
                            {{ $errors->first('rate') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group{{ $errors->has('rate_per') ? ' has-error' : '' }}">
                    <label for="rate_per" class="control-label">Rate Per</label>
                    <select id="rate_per" class="form-control" name="rate_per" >
                        <option value="">Select Rate Per</option>
                        <?php foreach ($arrays['rate_per'] as $key=>$feq){?>
                        <option value="<?php echo $feq;?>"><?php echo $feq;?></option>
                        <?php }?>
                    </select>

                    @if ($errors->has('rate_per'))
                        <p class="error-block">
                            {{ $errors->first('rate_per') }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="custom-heading">Scheduling Contact</div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('scheduling_contact_email', 'Title', ['class' => 'control-label required']) !!}
                    {!! Form::text('scheduling_contact_email', old('scheduling_contact_email'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('scheduling_contact_email'))
                        <p class="error-block">
                            {{ $errors->first('scheduling_contact_email') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('scheduling_contact_name', 'Name', ['class' => 'control-label required']) !!}
                    {!! Form::text('scheduling_contact_name', old('scheduling_contact_name'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('scheduling_contact_name'))
                        <p class="error-block">
                            {{ $errors->first('scheduling_contact_name') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('scheduling_contact_address', 'Address', ['class' => 'control-label required']) !!}
                    {!! Form::text('scheduling_contact_address', old('scheduling_contact_address'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('scheduling_contact_address'))
                        <p class="error-block">
                            {{ $errors->first('scheduling_contact_address') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('scheduling_contact_phone', 'Phone', ['class' => 'control-label required']) !!}
                    <input id="scheduling_contact_phone" type="text" name="scheduling_contact_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask>
                    <p class="help-block"></p>
                    @if($errors->has('scheduling_contact_phone'))
                        <p class="error-block">
                            {{ $errors->first('scheduling_contact_phone') }}
                        </p>
                    @endif
                </div>
                

                <div class="col-xs-3 form-group{{ $errors->has('scheduling_contact_state_id') ? ' has-error' : '' }}">
                    <label for="scheduling_state" class="control-label required">State</label>
                    <select id="scheduling_state" dropdown="scheduling" class="form-control state_dropdown select2" name="scheduling_contact_state_id"  required="">
                        <option value="">Select State</option>
                         <?php foreach ($states as $key=>$state){?>
                        <option value="<?php echo $key;?>"><?php echo $state;?></option>
                        <?php }?>
                    </select>
                    @if ($errors->has('scheduling_contact_state_id'))
                        <p class="error-block">
                            {{ $errors->first('scheduling_contact_state_id') }}
                        </p>
                    @endif
                </div>

                <div class="col-xs-3 form-group{{ $errors->has('scheduling_contact_city_id') ? ' has-error' : '' }}">
                    <label for="scheduling_city" class="control-label required">City</label>
                    <select id="scheduling_city" dropdown="scheduling" class="form-control city_dropdown select2" name="scheduling_contact_city_id"  required="">
                        <option value="">Select City</option>
                         <?php foreach ($states as $key=>$state){?>
                        <option value="<?php echo $key;?>"><?php echo $state;?></option>
                        <?php }?>
                    </select>

                    @if ($errors->has('scheduling_contact_city_id'))
                        <p class="error-block">
                            {{ $errors->first('scheduling_contact_city_id') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('scheduling_contact_zip', 'Zip', ['class' => 'control-label']) !!}
                    {!! Form::text('scheduling_contact_zip', old('scheduling_contact_zip'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('scheduling_contact_zip'))
                        <p class="error-block">
                            {{ $errors->first('scheduling_contact_zip') }}
                        </p>
                    @endif
                </div>
                </div>
            <div class="row">
                <div class="custom-heading">Secondary Scheduling Contact</div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('sec_scheduling_contact_email', 'Title', ['class' => 'control-label']) !!}
                    {!! Form::text('sec_scheduling_contact_email', old('sec_scheduling_contact_email'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('sec_scheduling_contact_email'))
                        <p class="error-block">
                            {{ $errors->first('sec_scheduling_contact_email') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('sec_scheduling_contact_name', 'Name', ['class' => 'control-label']) !!}
                    {!! Form::text('sec_scheduling_contact_name', old('sec_scheduling_contact_name'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('sec_scheduling_contact_name'))
                        <p class="error-block">
                            {{ $errors->first('sec_scheduling_contact_name') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('sec_scheduling_contact_address', 'Address', ['class' => 'control-label']) !!}
                    {!! Form::text('sec_scheduling_contact_address', old('sec_scheduling_contact_address'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('sec_scheduling_contact_address'))
                        <p class="error-block">
                            {{ $errors->first('sec_scheduling_contact_address') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('sec_scheduling_contact_phone', 'Phone', ['class' => 'control-label']) !!}
                    <input id="sec_scheduling_contact_phone" type="text" name="sec_scheduling_contact_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask>
                    <p class="help-block"></p>
                    @if($errors->has('sec_scheduling_contact_phone'))
                        <p class="error-block">
                            {{ $errors->first('sec_scheduling_contact_phone') }}
                        </p>
                    @endif
                </div>
                

                <div class="col-xs-3 form-group{{ $errors->has('sec_scheduling_contact_state_id') ? ' has-error' : '' }}">
                    <label for="sec_scheduling_state" class="control-label">State</label>
                    <select id="sec_scheduling_state" dropdown="sec_scheduling" class="form-control state_dropdown select2" name="sec_scheduling_contact_state_id"  >
                        <option value="">Select State</option>
                         <?php foreach ($states as $key=>$state){?>
                        <option value="<?php echo $key;?>"><?php echo $state;?></option>
                        <?php }?>
                    </select>
                    @if ($errors->has('sec_scheduling_contact_state_id'))
                        <p class="error-block">
                            {{ $errors->first('sec_scheduling_contact_state_id') }}
                        </p>
                    @endif
                </div>

                <div class="col-xs-3 form-group{{ $errors->has('sec_scheduling_contact_city_id') ? ' has-error' : '' }}">
                    <label for="sec_scheduling_city" class="control-label">City</label>
                    <select id="sec_scheduling_city" dropdown="sec_scheduling" class="form-control city_dropdown select2" name="sec_scheduling_contact_city_id"  >
                        <option value="">Select City</option>
                         <?php foreach ($states as $key=>$state){?>
                        <option value="<?php echo $key;?>"><?php echo $state;?></option>
                        <?php }?>
                    </select>

                    @if ($errors->has('sec_scheduling_contact_city_id'))
                        <p class="error-block">
                            {{ $errors->first('sec_scheduling_contact_city_id') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('sec_scheduling_contact_zip', 'Zip', ['class' => 'control-label']) !!}
                    {!! Form::text('sec_scheduling_contact_zip', old('sec_scheduling_contact_zip'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('sec_scheduling_contact_zip'))
                        <p class="error-block">
                            {{ $errors->first('sec_scheduling_contact_zip') }}
                        </p>
                    @endif
                </div>
                </div>
            <div class="row">
                <div class="custom-heading">Billing Contact</div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('billing_contact_email', 'Title', ['class' => 'control-label']) !!}
                    {!! Form::text('billing_contact_email', old('billing_contact_email'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('billing_contact_email'))
                        <p class="error-block">
                            {{ $errors->first('billing_contact_email') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('billing_contact_name', 'Name', ['class' => 'control-label']) !!}
                    {!! Form::text('billing_contact_name', old('billing_contact_name'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('billing_contact_name'))
                        <p class="error-block">
                            {{ $errors->first('billing_contact_name') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('billing_contact_address', 'Address', ['class' => 'control-label']) !!}
                    {!! Form::text('billing_contact_address', old('billing_contact_address'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('billing_contact_address'))
                        <p class="error-block">
                            {{ $errors->first('billing_contact_address') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('billing_contact_phone', 'Phone', ['class' => 'control-label']) !!}
                    <input id="billing_contact_phone" type="text" name="billing_contact_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask>
                    <p class="help-block"></p>
                    @if($errors->has('billing_contact_phone'))
                        <p class="error-block">
                            {{ $errors->first('billing_contact_phone') }}
                        </p>
                    @endif
                </div>
                

                <div class="col-xs-3 form-group{{ $errors->has('billing_contact_state_id') ? ' has-error' : '' }}">
                    <label for="billing_state" class="control-label">State</label>
                    <select id="billing_state" dropdown="billing" class="form-control state_dropdown select2" name="billing_contact_state_id"  >
                        <option value="">Select State</option>
                         <?php foreach ($states as $key=>$state){?>
                        <option value="<?php echo $key;?>"><?php echo $state;?></option>
                        <?php }?>
                    </select>
                    @if ($errors->has('billing_contact_state_id'))
                        <p class="error-block">
                            {{ $errors->first('billing_contact_state_id') }}
                        </p>
                    @endif
                </div>

                <div class="col-xs-3 form-group{{ $errors->has('billing_contact_city_id') ? ' has-error' : '' }}">
                    <label for="billing_city" class="control-label">City</label>
                    <select id="billing_city" dropdown="billing" class="form-control city_dropdown select2" name="billing_contact_city_id"  >
                        <option value="">Select City</option>
                         <?php foreach ($states as $key=>$state){?>
                        <option value="<?php echo $key;?>"><?php echo $state;?></option>
                        <?php }?>
                    </select>

                    @if ($errors->has('billing_contact_city_id'))
                        <p class="error-block">
                            {{ $errors->first('billing_contact_city_id') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('billing_contact_zip', 'Zip', ['class' => 'control-label']) !!}
                    {!! Form::text('billing_contact_zip', old('billing_contact_zip'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('billing_contact_zip'))
                        <p class="error-block">
                            {{ $errors->first('billing_contact_zip') }}
                        </p>
                    @endif
                </div>
            </div>
            <hr>
            <div class="row">
                
                
                <div class="col-xs-3 form-group">
                    {!! Form::label('start_time', 'Start Time', ['class' => 'control-label']) !!}
                    {!! Form::text('start_time', old('start_time'), ['class' => 'form-control timepicker', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('start_time'))
                        <p class="error-block">
                            {{ $errors->first('start_time') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('benchmark', 'Benchmark', ['class' => 'control-label']) !!}
                    {!! Form::text('benchmark', old('benchmark'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('benchmark'))
                        <p class="error-block">
                            {{ $errors->first('benchmark') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('max_length', 'Max. Length', ['class' => 'control-label']) !!}
                    {!! Form::text('max_length', old('max_length'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('max_length'))
                        <p class="error-block">
                            {{ $errors->first('max_length') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('min_auditors', 'Min. Auditors', ['class' => 'control-label']) !!}
                    {!! Form::text('min_auditors', old('min_auditors'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('min_auditors'))
                        <p class="error-block">
                            {{ $errors->first('min_auditors') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('spf', 'Supervisor Production Factor', ['class' => 'control-label']) !!}
                    {!! Form::text('spf', old('spf'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('spf'))
                        <p class="error-block">
                            {{ $errors->first('spf') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('alr_disk', 'ALR Disk', ['class' => 'control-label']) !!}
                    {!! Form::text('alr_disk', old('alr_disk'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('alr_disk'))
                        <p class="error-block">
                            {{ $errors->first('alr_disk') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    <label class="control-label">Count Stockroom</label><br>
                    <label class="control-label">                   
                        {!! Form::radio('count_stockroom','Yes',['class' => 'form-control']) !!} Yes
                    </label>
                    <label class="control-label">                   
                        {!! Form::radio('count_stockroom','No',['class' => 'form-control']) !!} No
                    </label>
                    <p class="help-block"></p>
                    @if($errors->has('count_stockroom'))
                        <p class="error-block">
                            {{ $errors->first('count_stockroom') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    <label class="control-label">Precall</label><br>
                    <label class="control-label">                   
                        {!! Form::radio('precall','Yes',['class' => 'form-control']) !!} Yes
                    </label>
                    <label class="control-label">                   
                        {!! Form::radio('precall','No',['class' => 'form-control']) !!} No
                    </label>
                    <p class="help-block"></p>
                    @if($errors->has('precall'))
                        <p class="error-block">
                            {{ $errors->first('precall') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    <label class="control-label">QC Call</label><br>
                    <label class="control-label">                   
                        {!! Form::radio('qccall','Yes',['class' => 'form-control']) !!} Yes
                    </label>
                    <label class="control-label">                   
                        {!! Form::radio('qccall','No',['class' => 'form-control']) !!} No
                    </label>
                    <p class="help-block"></p>
                    @if($errors->has('qccall'))
                        <p class="error-block">
                            {{ $errors->first('qccall') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    <label class="control-label">Pieces or Dollars</label><br>
                    <label class="control-label">                   
                        {!! Form::radio('pieces_or_dollars','pieces',['class' => 'form-control']) !!} Pieces
                    </label>
                    <label class="control-label">                   
                        {!! Form::radio('pieces_or_dollars','dollars',['class' => 'form-control']) !!} Dollars
                    </label>
                    <p class="help-block"></p>
                    @if($errors->has('pieces_or_dollars'))
                        <p class="error-block">
                            {{ $errors->first('pieces_or_dollars') }}
                        </p>
                    @endif
                </div>
            </div>
            
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('notes', 'Notes', ['class' => 'control-label']) !!}
                    {!! Form::textarea('notes', old('notes'), ['class' => 'form-control ', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('notes'))
                        <p class="error-block">
                            {{ $errors->first('notes') }}
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