@extends('layouts.app')
@section('pageTitle', 'View Association')
@section('content')
    <h3 class="page-title">Associations</h3>
    
    <div class="panel panel-default">
        <div class="panel-heading">View Association Details</div>
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-3 form-group">
                    {!! Form::label('name', 'Name', ['class' => 'control-label required']) !!}
                    {!! Form::text('name', $association->name, ['class' => 'form-control','disabled', 'placeholder' => '', 'required' => '']) !!}
                  
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('address', 'Address', ['class' => 'control-label required']) !!}
                    {!! Form::text('address', $association->address, ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                 
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('phone', 'Phone', ['class' => 'control-label required']) !!}
                    {!! Form::text('phone', $association->phone, ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                 
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('rebate', 'Rebate (%)', ['class' => 'control-label required']) !!}
                    {!! Form::text('rebate', $association->rebate, ['class' => 'form-control','disabled', 'placeholder' => '','maxlength'=>2]) !!}
                  
                </div>
            </div>
            <div class="row">
                

                <div class="col-xs-3 form-group">
                    {!! Form::label('state_id', 'State', ['class' => 'control-label']) !!}
                    <select id="state_id" dropdown="primary" disabled="" class="form-control country_dropdown" required="">
                        <option>{{@$association->state->name}}</option>
                    </select>
                  
                </div>

                <div class="col-xs-3 form-group{{ $errors->has('city_id') ? ' has-error' : '' }}">
                    {!! Form::label('city_id', 'City', ['class' => 'control-label']) !!}
                    <select id="state_id" dropdown="primary" disabled="" class="form-control country_dropdown" required="">
                        <option>{{@$association->city->name}}</option>
                    </select>
                   
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('zip', 'Zip', ['class' => 'control-label']) !!}
                    {!! Form::text('zip', $association->zip, ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                  
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('primary_contact_email', 'Primary Contact Email', ['class' => 'control-label']) !!}
                    {!! Form::text('primary_contact_email', $association->primary_contact_email, ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                   
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('primary_contact_name', 'Primary Contact Name', ['class' => 'control-label']) !!}
                    {!! Form::text('primary_contact_name', $association->primary_contact_name, ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                   
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('primary_contact_address', 'Primary Contact Address', ['class' => 'control-label']) !!}
                    {!! Form::text('primary_contact_address', $association->primary_contact_address, ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                  
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('primary_contact_phone', 'Primary Contact Phone', ['class' => 'control-label']) !!}
                    {!! Form::text('primary_contact_phone', $association->primary_contact_phone, ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                  
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('secondary_contact_email', 'Secondary Contact Email', ['class' => 'control-label']) !!}
                    {!! Form::text('secondary_contact_email', $association->secondary_contact_email, ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                  
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('secondary_contact_name', 'Secondary Contact Name', ['class' => 'control-label']) !!}
                    {!! Form::text('secondary_contact_name', $association->secondary_contact_name, ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                 
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('secondary_contact_address', 'Secondary Contact Address', ['class' => 'control-label']) !!}
                    {!! Form::text('secondary_contact_address', $association->secondary_contact_address, ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                  
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('secondary_contact_phone', 'Secondary Contact Phone', ['class' => 'control-label']) !!}
                    {!! Form::text('secondary_contact_phone', $association->secondary_contact_phone, ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                  
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('alternate_contact_email', 'Alternate Contact Email', ['class' => 'control-label']) !!}
                    {!! Form::text('alternate_contact_email', $association->alternate_contact_email, ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                    
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('alternate_contact_name', 'Alternate Contact Name', ['class' => 'control-label']) !!}
                    {!! Form::text('alternate_contact_name', $association->alternate_contact_name, ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                   
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('alternate_contact_address', 'Alternate Contact Address', ['class' => 'control-label']) !!}
                    {!! Form::text('alternate_contact_address', $association->alternate_contact_address, ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                   
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('alternate_contact_phone', 'Alternate Contact Phone', ['class' => 'control-label']) !!}
                    {!! Form::text('alternate_contact_phone', $association->alternate_contact_phone, ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                  
                </div>
                
            </div>
            <div class="row">
                <div class="custom-heading">Stores Within District</div>
                <?php foreach($store_arr as $row)
                {
                   echo '<div class="col-xs-3 form-group">'.$row.'</div>'; 
                }?>
                
            </div>
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('notes', 'Notes', ['class' => 'control-label']) !!}
                    {!! Form::textarea('notes', $association->notes, ['class' => 'form-control ','disabled', 'placeholder' => '']) !!}
                  
                </div>
            </div>
        </div>
    </div>
    <a href="{{ route('admin.associations.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
@stop
@section('javascript')
    @parent
    <script>
    </script>
@stop