@extends('layouts.app')
@section('pageTitle', 'Edit Client')
@section('content')
    <h3 class="page-title">Clients</h3>
    
    {!! Form::model($client, ['method' => 'PUT', 'route' => ['admin.clients.update', $client->id], 'files' => true,'autocomplete'=>'off']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">Edit Client</div>
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




        @if(0)
        <div class="row">
                <div class="custom-heading ">Login Details  @if (!empty($user)) {{ 'hi' }} @endif</div>

                <div class="col-xs-3 form-group">
                    {!! Form::label('cust_login_name', 'Login Name', ['class' => 'control-label required']) !!}

                    @if (!empty($user)) 
                    {!! Form::text('cust_login_name', $value = $user->login_name, ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                    @endif  
                    
                    @empty($user) 
                    {!! Form::text('cust_login_name', $value='', ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                    @endempty


                    <p class="help-block"></p>
                    @if($errors->has('cust_login_name'))
                        <p class="error-block">
                            {{ $errors->first('cust_login_name') }}
                        </p>
                    @endif
                </div>


                <div class="col-xs-3 form-group">
                    {!! Form::label('cust_login_email', 'Login Email', ['class' => 'control-label']) !!}
                
                    @if (!empty($user)) 
                    {!! Form::text('cust_login_email', $value = $user->login_email, ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                    @endif  
                    
                    @empty($user) 
                    {!! Form::text('cust_login_email', $value='', ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                    @endempty


                    <p class="help-block"></p>
                    @if($errors->has('cust_login_email'))
                        <p class="error-block">
                            {{ $errors->first('cust_login_email') }}
                        </p>
                    @endif
                </div>


                <div class="col-xs-3 form-group">
                    {!! Form::label('cust_login_password', 'Login Password', ['class' => 'control-label']) !!}
                    {!! Form::password('cust_login_password', ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('cust_login_password'))
                        <p class="error-block">
                            {{ $errors->first('cust_login_password') }}
                        </p>
                    @endif
                </div>


                <div class="col-xs-3 form-group">
                    {!! Form::label('cust_confirm_password', 'Confirm Password', ['class' => 'control-label']) !!}
                    {!! Form::password('cust_confirm_password', ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('cust_confirm_password'))
                        <p class="error-block">
                            {{ $errors->first('cust_confirm_password') }}
                        </p>
                    @endif
                </div>



        </div>

        @endif


            <div class="row">

            <div class="custom-heading">General Details</div>

            
                <div class="col-xs-3 form-group">
                    {!! Form::label('cust_no', 'Customer Number', ['class' => 'control-label required']) !!}
                    {!! Form::text('cust_no', old('cust_no'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('cust_no'))
                        <p class="error-block">
                            {{ $errors->first('cust_no') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('name', 'Name', ['class' => 'control-label required']) !!}
                    {!! Form::text('name', old('name'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('name'))
                        <p class="error-block">
                            {{ $errors->first('name') }}
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
                <div class="col-xs-3 form-group{{ $errors->has('association_id') ? ' has-error' : '' }}">
                    <label for="association_id" class="control-label required">Association</label>
                    <select id="association_id" class="form-control" name="association_id"  required="">
                        <option value="">Select Association</option>
                        <?php foreach ($associations as $key=>$association){?>
                        <option value="<?php echo $key;?>" <?php if($key==$client->association_id){echo 'selected="selected"';}?>><?php echo $association;?></option>
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
                <?php $arrays = arrays();?>
                <div class="col-xs-3 form-group{{ $errors->has('frequency') ? ' has-error' : '' }}">
                    <label for="frequency" class="control-label">Frequency</label>
                    <select id="frequency" class="form-control" name="frequency"  >
                        <option value="">Select Frequency</option>
                        <?php foreach ($arrays['frequency'] as $key=>$feq){?>
                        <option value="<?php echo $feq;?>" <?php if($feq==$client->frequency){echo 'selected="selected"';}?>><?php echo $feq;?></option>
                        <?php }?>
                    </select>

                    @if ($errors->has('frequency'))
                        <p class="error-block">
                            {{ $errors->first('frequency') }}
                        </p>
                    @endif
                </div>
            
                

                <div class="col-xs-3 form-group{{ $errors->has('state_id') ? ' has-error' : '' }}">
                    <label for="primary_state" class="control-label required">State</label>
                    <select id="primary_state" dropdown="primary" class="form-control state_dropdown select2" name="state_id"  required="">
                        <?php foreach ($states as $key=>$state){?>
                        <option value="<?php echo $key;?>" <?php if($key==$client->state_id){echo 'selected="selected"';}?>><?php echo $state;?></option>
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
                    <select id="primary_city" dropdown="primary" class="form-control city_dropdown select2 select2" name="city_id"  required="">
                        <?php foreach ($cities as $key=>$city){?>
                        <option value="<?php echo $key;?>" <?php if($key==$client->city_id){echo 'selected="selected"';}?>><?php echo $city;?></option>
                        <?php }?>
                    </select>

                    @if ($errors->has('city_id'))
                        <p class="error-block">
                            {{ $errors->first('city_id') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('zip', 'Zip', ['class' => 'control-label']) !!}
                    {!! Form::text('zip', old('zip'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('zip'))
                        <p class="error-block">
                            {{ $errors->first('zip') }}
                        </p>
                    @endif
                </div>
                </div>
            <div class="row">
                <div class="custom-heading">Scheduling Contact</div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('scheduling_contact_email', 'Email', ['class' => 'control-label']) !!}
                    {!! Form::text('scheduling_contact_email', old('scheduling_contact_email'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('scheduling_contact_email'))
                        <p class="error-block">
                            {{ $errors->first('scheduling_contact_email') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('scheduling_contact_name', 'Name', ['class' => 'control-label']) !!}
                    {!! Form::text('scheduling_contact_name', old('scheduling_contact_name'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('scheduling_contact_name'))
                        <p class="error-block">
                            {{ $errors->first('scheduling_contact_name') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('scheduling_contact_address', 'Address', ['class' => 'control-label']) !!}
                    {!! Form::text('scheduling_contact_address', old('scheduling_contact_address'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('scheduling_contact_address'))
                        <p class="error-block">
                            {{ $errors->first('scheduling_contact_address') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('scheduling_contact_phone', 'Phone', ['class' => 'control-label']) !!}
                    <input id="scheduling_contact_phone" value="<?php echo $client->scheduling_contact_phone;?>" autocomplete="off" type="text" name="scheduling_contact_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask>
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
                        <?php foreach ($scheduling_states as $key=>$state){?>
                        <option value="<?php echo $key;?>" <?php if($key==$client->scheduling_contact_state_id){echo 'selected="selected"';}?>><?php echo $state;?></option>
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
                        <?php foreach ($scheduling_cities as $key=>$city){?>
                        <option value="<?php echo $key;?>" <?php if($key==$client->scheduling_contact_city_id){echo 'selected="selected"';}?>><?php echo $city;?></option>
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
                    <input id="sec_scheduling_contact_phone" value="<?php echo $client->sec_scheduling_contact_phone;?>" type="text" name="sec_scheduling_contact_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask>
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
                        <?php foreach ($sec_scheduling_states as $key=>$state){?>
                        <option value="<?php echo $key;?>" <?php if($key==$client->sec_scheduling_contact_state_id){echo 'selected="selected"';}?>><?php echo $state;?></option>
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
                        <?php foreach ($sec_scheduling_cities as $key=>$city){?>
                        <option value="<?php echo $key;?>" <?php if($key==$client->sec_scheduling_contact_city_id){echo 'selected="selected"';}?>><?php echo $city;?></option>
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
                    {!! Form::label('billing_contact_email', 'Email', ['class' => 'control-label']) !!}
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
                    <input id="billing_contact_phone" value="<?php echo $client->billing_contact_phone;?>" type="text" name="billing_contact_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask>
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
                        <?php foreach ($billing_states as $key=>$state){?>
                        <option value="<?php echo $key;?>" <?php if($key==$client->billing_contact_state_id){echo 'selected="selected"';}?>><?php echo $state;?></option>
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
                        <?php foreach ($billing_cities as $key=>$city){?>
                        <option value="<?php echo $key;?>" <?php if($key==$client->billing_contact_city_id){echo 'selected="selected"';}?>><?php echo $city;?></option>
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
                    {!! Form::label('minbilling', 'Min. Billing', ['class' => 'control-label']) !!}
                    {!! Form::text('minbilling', old('minbilling'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('minbilling'))
                        <p class="error-block">
                            {{ $errors->first('minbilling') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group{{ $errors->has('inv_type') ? ' has-error' : '' }}">
                    <label for="inv_type" class="control-label">Inventory Type</label>
                    <select id="inv_type" class="form-control" name="inv_type"  >
                        <option value="">Select Inventory Type</option>
                        <?php foreach ($arrays['inv_type'] as $key=>$feq){?>
                        <option value="<?php echo $feq;?>" <?php if($feq==$client->inv_type){echo 'selected="selected"';}?>><?php echo $feq;?></option>
                        <?php }?>
                    </select>

                    @if ($errors->has('inv_type'))
                        <p class="error-block">
                            {{ $errors->first('inv_type') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group{{ $errors->has('billing') ? ' has-error' : '' }}">
                    <label for="billing" class="control-label required">Billing Type</label>
                    <select id="billing" class="form-control" name="billing"  required="">
                        <option value="">Select Billing Type</option>
                        <?php foreach ($arrays['billing'] as $key=>$feq){?>
                        <option value="<?php echo $feq;?>" <?php if($feq==$client->billing){echo 'selected="selected"';}?>><?php echo $feq;?></option>
                        <?php }?>
                    </select>

                    @if ($errors->has('billing'))
                        <p class="error-block">
                            {{ $errors->first('billing') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group{{ $errors->has('rate_type') ? ' has-error' : '' }}">
                    <label for="rate_type" class="control-label">Rate Type</label>
                    <select id="rate_type" class="form-control" name="rate_type" >
                        <option value="">Select Rate Type</option>
                        <?php foreach ($arrays['rate_type'] as $key=>$feq){?>
                        <option value="<?php echo $feq;?>" <?php if($feq==$client->rate_type){echo 'selected="selected"';}?>><?php echo $feq;?></option>
                        <?php }?>
                    </select>

                    @if ($errors->has('rate_type'))
                        <p class="error-block">
                            {{ $errors->first('rate_type') }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="row">
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
                        <option value="<?php echo $feq;?>" <?php if($feq==$client->rate_per){echo 'selected="selected"';}?>><?php echo $feq;?></option>
                        <?php }?>
                    </select>

                    @if ($errors->has('rate_per'))
                        <p class="error-block">
                            {{ $errors->first('rate_per') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group{{ $errors->has('days_avai_to_schedule') ? ' has-error' : '' }}">
                    <?php $schedule_availability_days = array();
                            if(count($client->schedule_availability_days)){foreach($client->schedule_availability_days as $day)$schedule_availability_days[] = $day->days;}
                            //print_r($schedule_availability_days);die;?>
                    <label for="days_avai_to_schedule" class="control-label">Days Available to Schedule</label>
                    <select id="days_avai_to_schedule" class="form-control select2" multiple="" name="days_avai_to_schedule[]" >
                        <option value="">Select Days</option>
                        <?php foreach ($arrays['days'] as $key=>$feq){?>
                        <option value="<?php echo $feq;?>" <?php if(in_array($feq,$schedule_availability_days)){echo 'selected="selected"';}?>><?php echo $feq;?></option>
                        <?php }?>
                    </select>

                    @if ($errors->has('days_avai_to_schedule'))
                        <p class="error-block">
                            {{ $errors->first('days_avai_to_schedule') }}
                        </p>
                    @endif
                </div>
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
                <!-- <div class="col-xs-3 form-group">
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
                </div> -->
                
            </div>
            <div class="row">
            <div class="col-xs-3 form-group">
                    <label class="control-label">QC Call</label><br>
                    <label class="control-label">                   
                        {!! Form::radio('qccall','Yes','',['class' => 'qccall']) !!} Yes
                    </label>
                    <label class="control-label">                   
                        {!! Form::radio('qccall','No','',['class' => 'qccall']) !!} No
                    </label>
                    <p class="help-block"></p>
                    @if($errors->has('qccall'))
                        <p class="error-block">
                            {{ $errors->first('qccall') }}
                        </p>
                    @endif
                </div>
            <div class="qccallrow <?php echo ($client->qccall=="No")?'hide':''?>">
                <div class="col-xs-3 form-group">
                    <label class="control-label">Store or Other</label><br>
                    <label class="control-label">                   
                        {!! Form::radio('store_or_other','store',true,['class'=>'qccallcol qccallstoreother']) !!} Store
                    </label>
                    <label class="control-label">                   
                        {!! Form::radio('store_or_other','other',false,['class'=>'qccallcol qccallstoreother']) !!} Other
                    </label>
                </div>
               
                <div class="col-xs-3 form-group <?php echo ($client->qccall=="No")?'hide':''?>">
                    {!! Form::label('other_contact_name', 'Other Contact Name', ['class' => 'control-label qccallother']) !!}
                    {!! Form::text('other_contact_name', old('other_contact_name'), ['class' => 'form-control qccallcol qccallother', 'placeholder' => '']) !!}
                </div>
                <div class="col-xs-3 form-group <?php echo ($client->qccall=="No")?'hide':''?>">
                    {!! Form::label('other_contact_number', 'Other Contact Number', ['class' => 'control-label qccallother']) !!}
                    {!! Form::text('other_contact_number', old('other_contact_number'), ['class' => 'form-control qccallcol qccallother', 'placeholder' => '']) !!}
                </div>
                
            </div>
            </div>
            
            <div class="row">
            <div class="col-xs-3 form-group">
                    <label class="control-label">Precall</label><br>
                    <label class="control-label">                   
                        {!! Form::radio('precall','Yes','',['class' => 'piccall']) !!} Yes
                    </label>
                    <label class="control-label">                   
                        {!! Form::radio('precall','No','',['class' => 'piccall']) !!} No
                    </label>
                    <p class="help-block"></p>
                    @if($errors->has('precall'))
                        <p class="error-block">
                            {{ $errors->first('precall') }}
                        </p>
                    @endif
                </div>
                <div class="piccallrow <?php echo ($client->precall=="No")?'hide':''?>" style= "">
                <div class="col-xs-3 form-group">
                    <label class="control-label">Store or Other</label><br>
                    <label class="control-label">                   
                        {!! Form::radio('picstore_or_other','store',true,['class'=>'piccallcol piccallstoreother']) !!} Store
                    </label>
                    <label class="control-label">                   
                        {!! Form::radio('picstore_or_other','other',false,['class'=>'piccallcol piccallstoreother']) !!} Other
                    </label>
                </div>
               
                <div class="col-xs-3 form-group <?php echo ($client->precall=="No")?'hide':''?>">
                    {!! Form::label('picother_contact_name', 'Other Contact Name', ['class' => 'control-label piccallother']) !!}
                    {!! Form::text('picother_contact_name', old('picother_contact_name'), ['class' => 'form-control piccallcol piccallother', 'placeholder' => '']) !!}
                </div>
                <div class="col-xs-3 form-group <?php echo ($client->precall=="No")?'hide':''?>">
                    {!! Form::label('picother_contact_number', 'Other Contact Number', ['class' => 'control-label piccallother']) !!}
                    {!! Form::text('picother_contact_number', old('picother_contact_number'), ['class' => 'form-control piccallcol piccallother', 'placeholder' => '']) !!}
                </div>
                
            </div>
            </div>
    
            <div class="row">
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
                <div class="col-xs-3 form-group">
                    {!! Form::label('after_hour_contact_name', 'After Hours Contact Name', ['class' => 'control-label']) !!}
                    {!! Form::text('after_hour_contact_name', old('after_hour_contact_name'), ['class' => 'form-control', 'placeholder' => '']) !!}
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('after_hour_contact_number', 'After Hours Contact Number', ['class' => 'control-label']) !!}
                    {!! Form::text('after_hour_contact_number', old('after_hour_contact_number'), ['class' => 'form-control', 'placeholder' => '']) !!}
                </div>
            </div>
            
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('terms', 'Terms', ['class' => 'control-label']) !!}
                    {!! Form::textarea('terms', old('terms'), ['class' => 'form-control ', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('terms'))
                        <p class="error-block">
                            {{ $errors->first('terms') }}
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
            <input type="hidden" value="<?php echo ($client->blackout_dates)?count($client->blackout_dates):1;?>" id="blackout_counter">
            <div class="blackout_dates_container">
               <?php if(count($client->blackout_dates)){foreach($client->blackout_dates as $key=>$dates){?> 
                    <div class="col-xs-2 blackout_counter<?=$key;?>" style="height:75px;">
                        {!! Form::label('blackout_dates', 'Blackout Dates', ['class' => 'control-label']) !!}
                        {!! Form::text('blackout_dates[]', date("m/d/Y", strtotime($dates->date)), ['class' => 'form-control datepicker', 'placeholder' => '']) !!}
                        <p class="help-block"></p>
                        @if($errors->has('blackout_dates'))
                            <p class="error-block">
                                {{ $errors->first('blackout_dates') }}
                            </p>
                        @endif
                    </div>
                    <?php if($key==0){?>
                        <div class="col-xs-1 add_more" style="height:75px;"><i class="fa fa-plus" aria-hidden="true"> Add More</i> </div>
                    <?php }else{?>
                        <div class="col-xs-1 remove_blackout blackout_counter<?=$key;?>" blackout="<?=$key;?>" style="height:75px;"><i class="fa fa-trash" aria-hidden="true"></i></div>
                    <?php }?>
               <?php }}else{?>
                        <div class="col-xs-2 blackout_counter1" style="height:75px;">
                        {!! Form::label('blackout_dates', 'Blackout Dates', ['class' => 'control-label']) !!}
                        {!! Form::text('blackout_dates[]','', ['class' => 'form-control datepicker', 'placeholder' => '','autocomplete'=>'off']) !!}
                        <p class="help-block"></p>
                        @if($errors->has('blackout_dates'))
                            <p class="error-block">
                                {{ $errors->first('blackout_dates') }}
                            </p>
                        @endif
                        </div>
                        <div class="col-xs-1 add_more" style="height:75px;"><i class="fa fa-plus" aria-hidden="true"> Add More</i> </div>
            <?php   }?>
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
                $(".qccallrow").removeClass('hide');
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
               $(".qccallother").parent().removeClass('hide');
            }else{
                $(".qccallother").hide();
                $(".qccallother").val('');
                $("#other_contact_name").val('').addClass('hide');
                $("#other_contact_number").val('').addClass('hide');
            }
            //alert(qccall);
        });

       //PIC Call
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
                $(".piccallrow").removeClass('hide');
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
       
       ///
        $('.piccallstoreother').click(function(){
            var picstore_or_other = $('input[name="picstore_or_other"]:checked').val();
            if(picstore_or_other=="other")
            {
               $(".piccallother").show().removeClass('hide');
               $(".piccallother").parent().removeClass('hide');
            }else{
                $(".piccallother").hide();
                $(".piccallother").val('');
                $("#picother_contact_name").val('').addClass('hide');
                $("#picother_contact_number").val('').addClass('hide');
            }
            //alert(qccall);
        });
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