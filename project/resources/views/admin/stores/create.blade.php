@extends('layouts.app')
@section('pageTitle', 'Add Store')
@section('content')
    <h3 class="page-title">Stores</h3>
    {!! Form::open(['method' => 'POST', 'route' => ['admin.stores.store'], 'files' => true,]) !!}

    <div class="panel panel-default">
        <div class="panel-heading">Add New Store</div>
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
                    {!! Form::text('number', old('number'), ['class' => 'form-control', 'required' => 'required']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('number'))
                        <p class="error-block">
                            {{ $errors->first('number') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('name', 'Name', ['class' => 'control-label required']) !!}
                    {!! Form::text('name', old('name'), ['class' => 'form-control','required' => 'required']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('name'))
                        <p class="error-block">
                            {{ $errors->first('name') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group{{ $errors->has('manager_id') ? ' has-error' : '' }}">
                    {!! Form::label('manager_id', 'Manager', ['class' => 'control-label']) !!}
                    {!! Form::text('manager_id', old('manager_id'), ['class' => 'form-control']) !!}
                    @if ($errors->has('manager_id'))
                        <p class="error-block">
                            {{ $errors->first('manager_id') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group{{ $errors->has('association_id') ? ' has-error' : '' }}">
                    <label for="association_id" class="control-label">Association</label>
                    <select id="association_id" class="form-control association_id" name="association_id" >
                        <option value="0">Select Association</option>
                        <?php foreach ($associations as $key=>$association){?>
                        <option value="<?php echo $key;?>"><?php echo $association;?></option>
                        <?php }?>
                    </select>

                    @if ($errors->has('association_id'))
                        <p class="error-block">
                            {{ $errors->first('association_id') }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-xs-3 form-group{{ $errors->has('client_id') ? ' has-error' : '' }}">
                    <label for="client_id" class="control-label">Client</label>
                    <select id="client_id" class="form-control client_id" name="client_id" >
                        <option value="0">Select Client</option>
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
                        <option value="0">Select Division</option>
                    </select>

                    @if ($errors->has('division_id'))
                        <p class="error-block">
                            {{ $errors->first('division_id') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group{{ $errors->has('district_id') ? ' has-error' : '' }}">
                    <label for="district_id" class="control-label">District</label>
                    <select id="district_id" class="form-control district_id" name="district_id" >
                        <option value="0">Select District</option>
                    </select>

                    @if ($errors->has('district_id'))
                        <p class="error-block">
                            {{ $errors->first('district_id') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('address', 'Address', ['class' => 'control-label required']) !!}
                    {!! Form::text('address', old('address'), ['class' => 'form-control','required' => 'required']) !!}
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
                    {!! Form::text('zip', old('zip'), ['class' => 'form-control','required' => 'required']) !!}
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
            </div>
            
            <div class="row">
                <div class="custom-heading">Scheduling Contact</div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('scheduling_contact_email', 'Email', ['class' => 'control-label']) !!}
                    {!! Form::text('scheduling_contact_email', old('scheduling_contact_email'), ['class' => 'form-control']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('scheduling_contact_email'))
                        <p class="error-block">
                            {{ $errors->first('scheduling_contact_email') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('scheduling_contact_name', 'Name', ['class' => 'control-label']) !!}
                    {!! Form::text('scheduling_contact_name', old('scheduling_contact_name'), ['class' => 'form-control']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('scheduling_contact_name'))
                        <p class="error-block">
                            {{ $errors->first('scheduling_contact_name') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('scheduling_contact_address', 'Address', ['class' => 'control-label']) !!}
                    {!! Form::text('scheduling_contact_address', old('scheduling_contact_address'), ['class' => 'form-control']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('scheduling_contact_address'))
                        <p class="error-block">
                            {{ $errors->first('scheduling_contact_address') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('scheduling_contact_phone', 'Phone', ['class' => 'control-label']) !!}
                    <input id="scheduling_contact_phone" type="text" name="scheduling_contact_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask>
                    <p class="help-block"></p>
                    @if($errors->has('scheduling_contact_phone'))
                        <p class="error-block">
                            {{ $errors->first('scheduling_contact_phone') }}
                        </p>
                    @endif
                </div>
                

                <div class="col-xs-3 form-group{{ $errors->has('scheduling_contact_state_id') ? ' has-error' : '' }}">
                    <label for="scheduling_state" class="control-label">State</label>
                    <select id="scheduling_state" dropdown="scheduling" class="form-control state_dropdown select2" name="scheduling_contact_state_id">
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
                    <label for="scheduling_city" class="control-label">City</label>
                    <select id="scheduling_city" dropdown="scheduling" class="form-control city_dropdown select2" name="scheduling_contact_city_id">
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
                    {!! Form::text('scheduling_contact_zip', old('scheduling_contact_zip'), ['class' => 'form-control']) !!}
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
                    {!! Form::label('sec_scheduling_contact_email', 'Email', ['class' => 'control-label']) !!}
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
                    {!! Form::text('sec_scheduling_contact_name', old('sec_scheduling_contact_name'), ['class' => 'form-control']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('sec_scheduling_contact_name'))
                        <p class="error-block">
                            {{ $errors->first('sec_scheduling_contact_name') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('sec_scheduling_contact_address', 'Address', ['class' => 'control-label']) !!}
                    {!! Form::text('sec_scheduling_contact_address', old('sec_scheduling_contact_address'), ['class' => 'form-control']) !!}
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
                    {!! Form::text('sec_scheduling_contact_zip', old('sec_scheduling_contact_zip'), ['class' => 'form-control']) !!}
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
                    {!! Form::label('billing_contact_email', 'Email', ['class' => 'control-label']) !!}
                    {!! Form::text('billing_contact_email', old('billing_contact_email'), ['class' => 'form-control']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('billing_contact_email'))
                        <p class="error-block">
                            {{ $errors->first('billing_contact_email') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('billing_contact_name', 'Name', ['class' => 'control-label']) !!}
                    {!! Form::text('billing_contact_name', old('billing_contact_name'), ['class' => 'form-control']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('billing_contact_name'))
                        <p class="error-block">
                            {{ $errors->first('billing_contact_name') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('billing_contact_address', 'Address', ['class' => 'control-label']) !!}
                    {!! Form::text('billing_contact_address', old('billing_contact_address'), ['class' => 'form-control']) !!}
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
                    {!! Form::text('billing_contact_zip', old('billing_contact_zip'), ['class' => 'form-control']) !!}
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
                    {!! Form::label('minbilling', 'Min. Billing', ['class' => 'control-label']) !!}
                    {!! Form::text('minbilling', old('minbilling'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('minbilling'))
                        <p class="error-block">
                            {{ $errors->first('minbilling') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group{{ $errors->has('billing') ? ' has-error' : '' }}">
                    <label for="billing" class="control-label required">Billing</label>
                    <select id="billing" class="form-control required" required="" name="billing" >
                        <option value="">Select Billing Type</option>
                        <?php foreach ($arrays['billing'] as $key=>$feq){?>
                        <option value="<?php echo $feq;?>" <?php if($feq=="Store"){echo 'selected="selected"';}?>><?php echo $feq;?></option>
                        <?php }?>
                    </select>

                    @if ($errors->has('billing'))
                        <p class="error-block">
                            {{ $errors->first('billing') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group{{ $errors->has('store_type') ? ' has-error' : '' }}">
                    <label for="store_type" class="control-label required">Store Type</label>
                    <select id="store_type" class="form-control required" required="" name="store_type"  >
                        <option value="">Select Store Type</option>
                        <?php foreach ($arrays['store_types'] as $key=>$feq){?>
                        <option value="<?php echo $feq;?>"><?php echo $feq;?></option>
                        <?php }?>
                    </select>

                    @if ($errors->has('store_type'))
                        <p class="error-block">
                            {{ $errors->first('store_type') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group{{ $errors->has('frequency') ? ' has-error' : '' }}">
                    <label for="frequency" class="control-label required">Frequency</label>
                    <select id="frequency" class="form-control" required="" name="frequency"  >
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
            </div>
            <div class="row">
                <div class="col-xs-3 form-group{{ $errors->has('inv_type') ? ' has-error' : '' }}">
                    <label for="inv_type" class="control-label required">Inventory Type</label>
                    <select id="inv_type" class="form-control" required="" name="inv_type"  >
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

                    @if ($errors->has('days_avai_to_schedule'))
                        <p class="error-block">
                            {{ $errors->first('days_avai_to_schedule') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group{{ $errors->has('rate_per') ? ' has-error' : '' }}">
                    <label for="month_to_schedule" class="control-label">Month Available to Schedule</label>
                    <select id="month_to_schedule" class="form-control select2" multiple="" name="month_to_schedule[]" >
                        <option value="">Select Month</option>
                        <?php foreach ($arrays['months'] as $key=>$feq){?>
                        <option value="<?php echo $feq;?>"><?php echo $feq;?></option>
                        <?php }?>
                    </select>

                    @if ($errors->has('month_to_schedule'))
                        <p class="error-block">
                            {{ $errors->first('month_to_schedule') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group{{ $errors->has('rate_type') ? ' has-error' : '' }}">
                    <label for="rate_type" class="control-label required">Rate Type</label>
                    <select id="rate_type" class="form-control" required="" name="rate_type" >
                        <option value="">Select Rate Type</option>
                        <?php foreach ($arrays['rate_type'] as $key=>$feq){?>
                        <option value="<?php echo $feq;?>" <?php if($feq=="Dollar"){echo 'selected="selected"';}?>><?php echo $feq;?></option>
                        <?php }?>
                    </select>

                    @if ($errors->has('rate_type'))
                        <p class="error-block">
                            {{ $errors->first('rate_type') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('rate', 'Rate', ['class' => 'control-label required']) !!}
                    {!! Form::text('rate', old('rate'), ['class' => 'form-control','required' => 'required']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('rate'))
                        <p class="error-block">
                            {{ $errors->first('rate') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('rate_effective_date', 'Rate Effective Date', ['class' => 'control-label required ']) !!}
                    {!! Form::text('rate_effective_date', old('rate_effective_date'), ['class' => 'form-control datepicker','required' => 'required']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('rate_effective_date'))
                        <p class="error-block">
                            {{ $errors->first('rate_effective_date') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group{{ $errors->has('rate_per') ? ' has-error' : '' }}">
                    <label for="rate_per" class="control-label required">Rate Per</label>
                    <select id="rate_per" class="form-control" required="" name="rate_per" >
                        <option value="">Select Rate Per</option>
                        <?php foreach ($arrays['rate_per'] as $key=>$feq){?>
                        <option value="<?php echo $feq;?>" <?php if($feq=="1000"){echo 'selected="selected"';}?>><?php echo $feq;?></option>
                        <?php }?>
                    </select>

                    @if ($errors->has('rate_per'))
                        <p class="error-block">
                            {{ $errors->first('rate_per') }}
                        </p>
                    @endif
                </div>
                
                <div class="col-xs-3 form-group">
                    {!! Form::label('start_time', 'Start Time', ['class' => 'control-label required']) !!}
                    {!! Form::text('start_time', old('start_time'), ['class' => 'form-control timepicker','required' => 'required']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('start_time'))
                        <p class="error-block">
                            {{ $errors->first('start_time') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('benchmark', 'Benchmark', ['class' => 'control-label required']) !!}
                    {!! Form::text('benchmark', old('benchmark'), ['class' => 'form-control','required' => 'required']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('benchmark'))
                        <p class="error-block">
                            {{ $errors->first('benchmark') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('max_length', 'Max. Length', ['class' => 'control-label']) !!}
                    {!! Form::text('max_length', old('max_length'), ['class' => 'form-control']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('max_length'))
                        <p class="error-block">
                            {{ $errors->first('max_length') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('min_auditors', 'Min. Auditors', ['class' => 'control-label']) !!}
                    {!! Form::text('min_auditors', old('min_auditors'), ['class' => 'form-control']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('min_auditors'))
                        <p class="error-block">
                            {{ $errors->first('min_auditors') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('spf', 'Supervisor Production Factor', ['class' => 'control-label required']) !!}
                    {!! Form::text('spf', old('spf'), ['class' => 'form-control required']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('spf'))
                        <p class="error-block">
                            {{ $errors->first('spf') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('alr_disk', 'ALR Disk', ['class' => 'control-label required']) !!}
                    {!! Form::text('alr_disk', old('alr_disk'), ['class' => 'form-control','required','placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('alr_disk'))
                        <p class="error-block">
                            {{ $errors->first('alr_disk') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('travel_charge', 'Travel Charge', ['class' => 'control-label']) !!}
                    {!! Form::text('travel_charge', old('travel_charge'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('travel_charge'))
                        <p class="error-block">
                            {{ $errors->first('travel_charge') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('overnight_charge', 'Overnight Charge', ['class' => 'control-label']) !!}
                    {!! Form::text('overnight_charge', old('overnight_charge'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('overnight_charge'))
                        <p class="error-block">
                            {{ $errors->first('overnight_charge') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('inventory_level', 'Inventory Level', ['class' => 'control-label required']) !!}
                    {!! Form::text('inventory_level', old('inventory_level'), ['class' => 'form-control','required', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('inventory_level'))
                        <p class="error-block">
                            {{ $errors->first('inventory_level') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group{{ $errors->has('apr') ? ' has-error' : '' }}">
                    <label for="apr" class="control-label required">Select APR</label>
                    <select id="apr" class="form-control select2" required="" name="apr">
                        <option value="">Select Area</option>
                        <?php foreach ($areas as $key=>$area){?>
                        <option value="<?php echo $key;?>"><?php echo $area;?></option>
                        <?php }?>
                    </select>

                    @if ($errors->has('apr'))
                        <p class="error-block">
                            {{ $errors->first('apr') }}
                        </p>
                    @endif
                </div>
            
                <div class="col-xs-3 form-group">
                    {!! Form::label('surcharge_fee', 'Surcharge Fee', ['class' => 'control-label']) !!}
                    {!! Form::text('surcharge_fee', old('surcharge_fee'), ['class' => 'form-control']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('surcharge_fee'))
                        <p class="error-block">
                            {{ $errors->first('surcharge_fee') }}
                        </p>
                    @endif
                </div>
<!--                <div class="col-xs-3 form-group{{ $errors->has('jsa') ? ' has-error' : '' }}">
                    <label for="jsa" class="control-label required">Select JSA</label>
                    <select id="jsa" class="form-control select2" required="" multiple="" name="jsa[]">
                        <option value="">Select JSA</option>
                    </select>
                    @if ($errors->has('jsa'))
                        <p class="error-block">
                            {{ $errors->first('jsa') }}
                        </p>
                    @endif
                </div>-->
                <div class="col-xs-3 form-group">
                    <label class="control-label required">Count Stockroom</label><br>
                    <label class="control-label">                   
                        {!! Form::radio('count_stockroom','Yes',true) !!} Yes
                    </label>
                    <label class="control-label">                   
                        {!! Form::radio('count_stockroom','No',false) !!} No
                    </label>
                    <p class="help-block"></p>
                    @if($errors->has('count_stockroom'))
                        <p class="error-block">
                            {{ $errors->first('count_stockroom') }}
                        </p>
                    @endif
                </div>
                <!-- <div class="col-xs-3 form-group">
                    <label class="control-label required">Precall</label><br>
                    <label class="control-label">                   
                        {!! Form::radio('precall','Yes',true) !!} Yes
                    </label>
                    <label class="control-label">                   
                        {!! Form::radio('precall','No',false) !!} No
                    </label>
                    <p class="help-block"></p>
                    @if($errors->has('precall'))
                        <p class="error-block">
                            {{ $errors->first('precall') }}
                        </p>
                    @endif
                </div> -->
            </div>
            <div class="row">
                <div class="col-xs-3 form-group">
                    <label class="control-label required">QC Call</label><br>
                    <label class="control-label">                   
                        {!! Form::radio('qccall','Yes',true,['class' => 'qccall']) !!} Yes
                    </label>
                    <label class="control-label">                   
                        {!! Form::radio('qccall','No',false,['class' => 'qccall']) !!} No
                    </label>
                    <p class="help-block"></p>
                    @if($errors->has('qccall'))
                        <p class="error-block">
                            {{ $errors->first('qccall') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group qccallrow">
                    <label class="control-label">Store or Other</label><br>
                    <label class="control-label">                   
                        {!! Form::radio('store_or_other','store',true,['class'=>'qccallcol qccallstoreother']) !!} Store
                    </label>
                    <label class="control-label">                   
                        {!! Form::radio('store_or_other','other',false,['class'=>'qccallcol qccallstoreother']) !!} Other
                    </label>
                </div>
                <div class="col-xs-3 form-group qccallrow">
                    {!! Form::label('other_contact_name', 'Other Contact Name', ['class' => 'control-label qccallother hide']) !!}
                    {!! Form::text('other_contact_name', old('other_contact_name'), ['class' => 'form-control qccallcol qccallother hide', 'placeholder' => '']) !!}
                </div>
                <div class="col-xs-3 form-group qccallrow">
                    {!! Form::label('other_contact_number', 'Other Contact Number', ['class' => 'control-label qccallother hide']) !!}
                    {!! Form::text('other_contact_number', old('other_contact_number'), ['class' => 'form-control qccallcol qccallother hide', 'placeholder' => '']) !!}
                </div>
            </div>
            <div class="row">
            <div class="col-xs-3 form-group">
                    <label class="control-label required">PreCall</label><br>
                    <label class="control-label">                   
                        {!! Form::radio('precall','Yes',true,['class'=>'piccall']) !!} Yes
                    </label>
                    <label class="control-label">                   
                        {!! Form::radio('precall','No',false,['class'=>'piccall']) !!} No
                    </label>
                    <p class="help-block"></p>
                    @if($errors->has('precall'))
                        <p class="error-block">
                            {{ $errors->first('precall') }}
                        </p>
                    @endif
                </div>
                        
            
            <div class="piccallrow">
                <div class="col-xs-3 form-group">
                    <label class="control-label">Store or Other</label><br>
                    <label class="control-label">                   
                        {!! Form::radio('picstore_or_other','store',true,['class'=>'piccallcol piccallstoreother']) !!} Store
                    </label>
                    <label class="control-label">                   
                        {!! Form::radio('picstore_or_other','other',false,['class'=>'piccallcol piccallstoreother']) !!} Other
                    </label>
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('picother_contact_name', 'Other Contact Name', ['class' => 'control-label piccallother hide']) !!}
                    {!! Form::text('picother_contact_name', old('other_contact_name'), ['class' => 'form-control piccallcol piccallother hide', 'placeholder' => '']) !!}
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('picother_contact_number', 'Other Contact Number', ['class' => 'control-label piccallother hide']) !!}
                    {!! Form::text('picother_contact_number', old('picother_contact_number'), ['class' => 'form-control piccallcol piccallother hide', 'placeholder' => '']) !!}
                </div>
            </div>
            </div>
            
            <div class="row">
                <div class="col-xs-3 form-group">
                    <label class="control-label required">Pieces or Dollars</label><br>
                    <label class="control-label">                   
                        {!! Form::radio('pieces_or_dollars','pieces',false) !!} Pieces
                    </label>
                    <label class="control-label">                   
                        {!! Form::radio('pieces_or_dollars','dollars',true) !!} Dollars
                    </label>
                    <p class="help-block"></p>
                    @if($errors->has('pieces_or_dollars'))
                        <p class="error-block">
                            {{ $errors->first('pieces_or_dollars') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    <label class="control-label required">Fuel Center</label><br>
                    <label class="control-label">                   
                        {!! Form::radio('fuel_center','Yes',false) !!} Yes
                    </label>
                    <label class="control-label">                   
                        {!! Form::radio('fuel_center','No',true) !!} No
                    </label>
                    <p class="help-block"></p>
                    @if($errors->has('fuel_center'))
                        <p class="error-block">
                            {{ $errors->first('fuel_center') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    <label class="control-label required">RX</label><br>
                    <label class="control-label">                   
                        {!! Form::radio('rx','Yes',false) !!} Yes
                    </label>
                    <label class="control-label">                   
                        {!! Form::radio('rx','No',true) !!} No
                    </label>
                    <p class="help-block"></p>
                    @if($errors->has('rx'))
                        <p class="error-block">
                            {{ $errors->first('rx') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('after_hour_contact_name', 'After Hours Contact Name', ['class' => 'control-label']) !!}
                    {!! Form::text('after_hour_contact_name', old('after_hour_contact_name'), ['class' => 'form-control', 'placeholder' => '']) !!}
                </div>
            </div>
            <div class="row">
                <div class="col-xs-3 form-group">
                    {!! Form::label('after_hour_contact_number', 'After Hours Contact Number', ['class' => 'control-label']) !!}
                    {!! Form::text('after_hour_contact_number', old('after_hour_contact_number'), ['class' => 'form-control', 'placeholder' => '']) !!}
                </div>
            </div>
            
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('terms', 'Terms', ['class' => 'control-label required']) !!}
                    {!! Form::textarea('terms', old('terms'), ['class' => 'form-control ', 'required']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('terms'))
                        <p class="error-block">
                            {{ $errors->first('terms') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-12 form-group">
                    {!! Form::label('notes', 'Notes', ['class' => 'control-label']) !!}
                    {!! Form::textarea('notes', old('notes'), ['class' => 'form-control ']) !!}
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
        $('.qccall').click(function(){
            var qccall = $('input[name="qccall"]:checked').val();
            if(qccall=="No")
            {
                $(".qccallrow").hide();
                //$(".qccallcol").val('');
                $("#other_contact_name").val('').addClass('hide');
                $("#other_contact_number").val('').addClass('hide');
            }else{
                $(".qccallrow").show();
                var store_or_other = $('input[name="store_or_other"]:checked').val();
                if(store_or_other=="other")
                {
                   $(".qccallother").show().removeClass('hide');

                }else{
                    $(".qccallother").hide();
                    $(".qccallother").val('');
                    $("#other_contact_name").val('').addClass('hide');
                    $("#other_contact_number").val('').addClass('hide');
                }
            }
            //alert(qccall);
        });
        $('.qccallstoreother').click(function(){
            var store_or_other = $('input[name="store_or_other"]:checked').val();
            if(store_or_other=="other")
            {
               $(".qccallother").show().removeClass('hide');
               
            }else{
                $(".qccallother").hide();
                $(".qccallother").val('');
                $("#other_contact_name").val('').addClass('hide');
                $("#other_contact_number").val('').addClass('hide');
            }
            //alert(qccall);
        });
        ///PIC Call
        $('.piccall').click(function(){
            var piccall = $('input[name="precall"]:checked').val();
            if(piccall=="No")
            {
                $(".piccallrow").hide();
                //$(".qccallcol").val('');
                $("#picother_contact_name").val('').addClass('hide');
                $("#picother_contact_number").val('').addClass('hide');
            }else{
                $(".piccallrow").show();
                var picstore_or_other = $('input[name="picstore_or_other"]:checked').val();
                if(picstore_or_other=="other")
                {
                   $(".piccallother").show().removeClass('hide');

                }else{
                    $(".piccallother").hide();
                    $(".piccallother").val('');
                    $("#picother_contact_name").val('').addClass('hide');
                    $("#picother_contact_number").val('').addClass('hide');
                }
            }
            //alert(qccall);
        });
        $('.piccallstoreother').click(function(){
            var picstore_or_other = $('input[name="picstore_or_other"]:checked').val();
            if(picstore_or_other=="other")
            {
               $(".piccallother").show().removeClass('hide');
               
            }else{
                $(".piccallother").hide();
                $(".piccallother").val('');
                $("#picother_contact_name").val('').addClass('hide');
                $("#picother_contact_number").val('').addClass('hide');
            }
            //alert(qccall);
        });
        $('[data-mask]').inputmask();
        $('.datepicker').datepicker({
            autoclose: true
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

        });
        $('#association_id').change(function(){
            var association_id = $(this).val();
            $("#client_id").empty();
            $("#division_id").empty();
            $("#district_id").val('<option value="0">Select District</option>');
            if(association_id){
                $.ajax({
                   type:"POST",
                   url:"{{url('admin/getClientByAssociation')}}",
                   data:{_token: _token,association_id:association_id},
                   success:function(res){               
                    if(res){
                        $("#client_id").empty();
                        $("#client_id").append('<option>Select Client</option>');
                        $.each(res.associations,function(key,value){
                            $("#client_id").append('<option value="'+value.id+'">'+value.name+'</option>');
                        });

                    }else{
                       $("#client_id").empty();
                    }
                   }
                });
            }else{
                $("#client_id").empty();
            }      
       });
        $('#client_id').change(function(){
            var client_id = $(this).val();
            $("#division_id").empty();
            $("#district_id").val('<option value="0">Select District</option>');
            if(client_id){
                $.ajax({
                   type:"POST",
                   url:"{{url('admin/getDivisionByClient')}}",
                   data:{_token: _token,client_id:client_id},
                   success:function(res){               
                    if(res){
                        $("#division_id").empty();
                        $("#division_id").append('<option value="0">Select Division</option>');
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
//        $('#apr').change(function(){
//            var apr = $(this).val();
//            if(apr){
//                $.ajax({
//                   type:"POST",
//                   url:"{{url('admin/getJSAByArea')}}",
//                   data:{_token: _token,area_id:apr},
//                   success:function(res){               
//                    if(res){
//                        $("#jsa").empty();
//                        $("#jsa").append('<option>Select JSA</option>');
//                        $.each(res.jsas,function(key,value){
//                            $("#jsa").append('<option value="'+value.id+'">'+value.title+'</option>');
//                        });
//
//                    }else{
//                       $("#jsa").empty();
//                    }
//                   }
//                });
//            }else{
//                $("#jsa").empty();
//            }      
//        });
        $(document).on("change", "#division_id", function(){
            var division_id = $(this).val(); 
            var client_id = $("#client_id").val();
            $("#district_id").empty();
            if(division_id){
                $.ajax({
                   type:"POST",
                   url:"{{url('admin/getDistrictByDivision')}}",
                   data:{_token: _token,client_id:client_id,division_id:division_id},
                   success:function(res){               
                    if(res){
                        $("#district_id").empty();
                        $("#district_id").append('<option value="0">Select District</option>');
                        $.each(res.districts,function(key,value){
                            $("#district_id").append('<option value="'+value.id+'">'+value.number+'</option>');
                        });

                    }else{
                       $("#district_id").empty();
                    }
                   }
                });
            }else{
                $("#district_id").empty();
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
                    console.log(res);
                    //$('#name').val(res.client.name);
                    $('#address').val(res.client.address);
                    $('#after_hour_contact_name').val(res.client.after_hour_contact_name);
                    $('#after_hour_contact_number').val(res.client.after_hour_contact_number);
                    $.each(res.states,function(key,value){
                            $("#primary_state").append('<option value="'+key+'">'+value+'</option>');
                    });
                    $('#primary_state').val(res.client.state_id).trigger('change');
                    $.each(res.cities,function(key,value){
                        $("#primary_city").append('<option value="'+key+'">'+value+'</option>');
                    });
                    $('#primary_city').val(res.client.city_id).trigger('change');
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
                    if(res.client.scheduling_state)
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
                    $('#sec_scheduling_state').val(res.client.sec_scheduling_contact_state_id).trigger('change');
                    $.each(res.sec_scheduling_cities,function(key,value){
                        $("#sec_scheduling_city").append('<option value="'+key+'">'+value+'</option>');
                    });
                    $('#sec_scheduling_city').val(res.client.sec_scheduling_contact_city_id).trigger('change');
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
                    $('#billing').val(res.client.billing);
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
                    $('#terms').val(res.client.terms);
                    
                    $("input[name='store_or_other'][value='"+res.client.store_or_other+"']").prop('checked', true);
                    if(res.client.store_or_other=="other")
                    {
                        $(".qccallother").show().removeClass('hide');
                        $("#other_contact_name").val(res.client.other_contact_name);
                        $("#other_contact_number").val(res.client.other_contact_number);
                    }else{
                        $(".qccallother").hide();
                        $(".qccallother").val('');
                        $("#other_contact_name").val('').addClass('hide');
                        $("#other_contact_number").val('').addClass('hide');
                    }
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
                    console.log(res);
                    if(res.division.address)$('#address').val(res.division.address);
                    if(res.division.state_id)
                    {
                        $.each(res.states,function(key,value){
                            $("#primary_state").append('<option value="'+key+'">'+value+'</option>');
                        });
                        $('#primary_state').val(res.division.state_id).trigger('change');
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
                    if(res.division.scheduling_state)
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
                    if(res.division.billing)$('#billing').val(res.division.billing);
                    if(res.division.alr_disk)$('#alr_disk').val(res.division.alr_disk);
                    if(res.division.count_stockroom)$("input[name='count_stockroom'][value='"+res.division.count_stockroom+"']").prop('checked', true);
                    if(res.division.precall)$("input[name='precall'][value='"+res.division.precall+"']").prop('checked', true);
                    if(res.division.qccall)$("input[name='qccall'][value='"+res.division.qccall+"']").prop('checked', true);
                    if(res.division.pieces_or_dollars)$("input[name='pieces_or_dollars'][value='"+res.division.pieces_or_dollars+"']").prop('checked', true);
                    if(res.division.notes)$('#notes').val(res.division.notes);
                    if(res.division.terms)$('#terms').val(res.division.terms);
                    
                    $("input[name='store_or_other'][value='"+res.division.store_or_other+"']").prop('checked', true);
                    if(res.division.store_or_other=="other")
                    {
                        $(".qccallother").show().removeClass('hide');
                        $("#other_contact_name").val(res.division.other_contact_name);
                        $("#other_contact_number").val(res.division.other_contact_number);
                    }else{
                        $(".qccallother").hide();
                        $(".qccallother").val('');
                        $("#other_contact_name").val('').addClass('hide');
                        $("#other_contact_number").val('').addClass('hide');
                    }
               }
            });
            }
        });
        
        $(document).on('change','#district_id',function(){
            var district_id = $("#district_id").val();
            if(district_id)
            {
                $.ajax({
               type:"GET",
               url:"{{url('admin/districts/')}}/"+district_id,
               success:function(res){
                    console.log(res);
                    if(res.district.address)$('#address').val(res.district.address);
                    if(res.district.state_id)
                    {
                        $.each(res.states,function(key,value){
                            $("#primary_state").append('<option value="'+key+'">'+value+'</option>');
                        });
                        $('#primary_state').val(res.district.state_id).trigger('change');
                    }
                    if(res.district.city_id)
                    {
                        $.each(res.cities,function(key,value){
                            $("#primary_city").append('<option value="'+key+'">'+value+'</option>');
                        });
                        $('#primary_city').val(res.district.city_id);
                    }
                    if(res.district.zip)$('#zip').val(res.district.zip);
                    if(res.district.frequency)$('#frequency').val(res.district.frequency);
                    if(res.district.inv_type)$('#inv_type').val(res.district.inv_type);
                    if(res.district.schedule_availability_days)
                    {
                        $.each(res.district.schedule_availability_days,function(key,value){
                            $("#days_avai_to_schedule option[value="+value.days+"]").attr('selected', 'selected');
                            $('#days_avai_to_schedule').select2();
                        });
                    }
                    if(res.district.scheduling_contact_email)$('#scheduling_contact_email').val(res.district.scheduling_contact_email);
                    if(res.district.scheduling_contact_name)$('#scheduling_contact_name').val(res.district.scheduling_contact_name);
                    if(res.district.scheduling_contact_address)$('#scheduling_contact_address').val(res.district.scheduling_contact_address);
                    if(res.district.scheduling_contact_phone)$('#scheduling_contact_phone').val(res.district.scheduling_contact_phone);
                    if(res.district.scheduling_state)
                    {
                        $.each(res.scheduling_states,function(key,value){
                            $("#scheduling_state").append('<option value="'+key+'">'+value+'</option>');
                        });
                        $('#scheduling_state').val(res.district.scheduling_state.id);
                    }
                    if(res.district.scheduling_contact_city_id)
                    {
                        $.each(res.scheduling_cities,function(key,value){
                            $("#scheduling_city").append('<option value="'+key+'">'+value+'</option>');
                        });
                        $('#scheduling_city').val(res.district.scheduling_contact_city_id);
                    }
                    if(res.district.scheduling_contact_zip)$('#scheduling_contact_zip').val(res.district.scheduling_contact_zip);
                    
                    if(res.district.sec_scheduling_contact_email)$('#sec_scheduling_contact_email').val(res.district.sec_scheduling_contact_email);
                    if(res.district.sec_scheduling_contact_name)$('#sec_scheduling_contact_name').val(res.district.sec_scheduling_contact_name);
                    if(res.district.sec_scheduling_contact_address)$('#sec_scheduling_contact_address').val(res.district.sec_scheduling_contact_address);
                    if(res.district.sec_scheduling_contact_phone)$('#sec_scheduling_contact_phone').val(res.district.sec_scheduling_contact_phone);
                    if(res.district.sec_scheduling_contact_state_id)
                    {
                        $.each(res.sec_scheduling_states,function(key,value){
                            $("#sec_scheduling_state").append('<option value="'+key+'">'+value+'</option>');
                        });
                        $('#sec_scheduling_state').val(res.district.sec_scheduling_contact_state_id);
                    }
                    if(res.district.sec_scheduling_contact_city_id)
                    {
                        $.each(res.sec_scheduling_cities,function(key,value){
                            $("#sec_scheduling_city").append('<option value="'+key+'">'+value+'</option>');
                        });
                        $('#sec_scheduling_city').val(res.district.sec_scheduling_contact_city_id);
                    }
                    if(res.district.sec_scheduling_contact_zip)$('#sec_scheduling_contact_zip').val(res.district.sec_scheduling_contact_zip);
                    
                    if(res.district.billing_contact_email)$('#billing_contact_email').val(res.district.billing_contact_email);
                    if(res.district.billing_contact_name)$('#billing_contact_name').val(res.district.billing_contact_name);
                    if(res.district.billing_contact_address)$('#billing_contact_address').val(res.district.billing_contact_address);
                    if(res.district.billing_contact_phone)$('#billing_contact_phone').val(res.district.billing_contact_phone);
                    if(res.district.billing_contact_state_id)
                    {
                        $.each(res.billing_states,function(key,value){
                            $("#billing_state").append('<option value="'+key+'">'+value+'</option>');
                        });
                        $('#billing_state').val(res.district.billing_contact_state_id);
                    }
                    if(res.district.billing_contact_city_id)
                    {
                        $.each(res.billing_cities,function(key,value){
                            $("#billing_city").append('<option value="'+key+'">'+value+'</option>');
                        });
                        $('#billing_city').val(res.district.billing_contact_city_id);
                    }
                    if(res.district.billing_contact_zip)$('#billing_contact_zip').val(res.district.billing_contact_zip);
                    if(res.district.rate_type)$('#rate_type').val(res.district.rate_type);
                    if(res.district.rate)$('#rate').val(res.district.rate);
                    if(res.district.rate_per)$('#rate_per').val(res.district.rate_per);
                    if(res.district.start_time)$('#start_time').val(res.district.start_time);
                    if(res.district.benchmark)$('#benchmark').val(res.district.benchmark);
                    if(res.district.max_length)$('#max_length').val(res.district.max_length);
                    if(res.district.min_auditors)$('#min_auditors').val(res.district.min_auditors);
                    if(res.district.spf)$('#spf').val(res.district.spf);
                    if(res.district.billing)$('#billing').val(res.district.billing);
                    if(res.district.alr_disk)$('#alr_disk').val(res.district.alr_disk);
                    if(res.district.count_stockroom)$("input[name='count_stockroom'][value='"+res.district.count_stockroom+"']").prop('checked', true);
                    if(res.district.precall)$("input[name='precall'][value='"+res.district.precall+"']").prop('checked', true);
                    if(res.district.qccall)$("input[name='qccall'][value='"+res.district.qccall+"']").prop('checked', true);
                    if(res.district.pieces_or_dollars)$("input[name='pieces_or_dollars'][value='"+res.district.pieces_or_dollars+"']").prop('checked', true);
                    if(res.district.notes)$('#notes').val(res.district.notes);
                    if(res.district.terms)$('#terms').val(res.district.terms);
                    $("input[name='store_or_other'][value='"+res.district.store_or_other+"']").prop('checked', true);
                    if(res.district.store_or_other=="other")
                    {
                        $(".qccallother").show().removeClass('hide');
                        $("#other_contact_name").val(res.district.other_contact_name);
                        $("#other_contact_number").val(res.district.other_contact_number);
                    }else{
                        $(".qccallother").hide();
                        $(".qccallother").val('');
                        $("#other_contact_name").val('').addClass('hide');
                        $("#other_contact_number").val('').addClass('hide');
                    }
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