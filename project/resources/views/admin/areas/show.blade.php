@extends('layouts.app')
@section('pageTitle', 'Edit Area')
@section('content')
    <h3 class="page-title">Areas</h3>
    
    {!! Form::model($area, ['method' => 'PUT', 'route' => ['admin.areas.update', $area->id], 'files' => true,'autocomplete'=>'off']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">Edit Area</div>
        <?php $arrays = arrays();?>
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-3 form-group">
                    {!! Form::label('title', 'Title', ['class' => 'control-label required']) !!}
                    {!! Form::text('title', old('title'), ['class' => 'form-control','disabled', 'placeholder' => '', 'required' => '']) !!}
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('area_number', 'Area Number', ['class' => 'control-label required']) !!}
                    {!! Form::text('area_number', old('area_number'), ['class' => 'form-control','disabled', 'placeholder' => '', 'required' => '']) !!}
                </div>
                <div class="col-xs-6 form-group">
                    {!! Form::label('address', 'Address', ['class' => 'control-label required']) !!}
                    {!! Form::text('address', old('address'), ['class' => 'form-control','disabled', 'placeholder' => '','required' => '']) !!}
                </div>
                

                <div class="col-xs-3 form-group{{ $errors->has('state_id') ? ' has-error' : '' }}">
                    <label for="primary_state" class="control-label required">State</label>
                    <select id="primary_state" dropdown="primary" disabled="" class="form-control state_dropdown" name="state_id"  required="">
                        <option><?php echo @$area->state->state_code;?></option>
                    </select>
                </div>

                <div class="col-xs-3 form-group{{ $errors->has('city_id') ? ' has-error' : '' }}">
                    <label for="primary_city" class="control-label required">City</label>
                    <select id="primary_city" dropdown="primary" disabled="" class="form-control city_dropdown" name="city_id"  required="">
                        <option><?php echo @$area->city->name;?></option>
                    </select>
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('zip', 'Zip', ['class' => 'control-label required']) !!}
                    {!! Form::text('zip', old('zip'), ['class' => 'form-control','disabled', 'placeholder' => '','required' => '']) !!}
                </div>
            </div>
            
        </div>
    </div>

   <a href="{{ route('admin.areas.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
@stop
@section('javascript')
    @parent
  
@stop