@extends('layouts.app')
@section('pageTitle', 'Edit Area')
@section('content')
    <h3 class="page-title">JSA Areas</h3>
    
    {!! Form::model($jsa, ['method' => 'PUT', 'route' => ['admin.areas.update', $jsa->id], 'files' => true,'autocomplete'=>'off']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">View JSA Area</div>
        <?php $arrays = arrays();?>
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-3 form-group">
                    {!! Form::label('title', 'JSA Title', ['class' => 'control-label required']) !!}
                    {!! Form::text('title', old('title'), ['class' => 'form-control','disabled', 'placeholder' => '', 'required' => '']) !!}
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('area_number', 'JSA Area Number', ['class' => 'control-label required']) !!}
                    {!! Form::text('area_number', old('area_number'), ['class' => 'form-control','disabled', 'placeholder' => '', 'required' => '']) !!}
                </div>
                <div class="col-xs-6 form-group">
                    {!! Form::label('address', 'JSA Address', ['class' => 'control-label required']) !!}
                    {!! Form::text('address', old('address'), ['class' => 'form-control','disabled', 'placeholder' => '','required' => '']) !!}
                </div>
                

                <div class="col-xs-3 form-group{{ $errors->has('state_id') ? ' has-error' : '' }}">
                    <label for="primary_state" class="control-label required">JSA State</label>
                    <select id="primary_state" dropdown="primary" disabled="" class="form-control state_dropdown" name="state_id"  required="">
                        <option><?php echo @$jsa->state->name;?></option>
                    </select>
                </div>

                <div class="col-xs-3 form-group{{ $errors->has('city_id') ? ' has-error' : '' }}">
                    <label for="primary_city" class="control-label required">JSA City</label>
                    <select id="primary_city" dropdown="primary" disabled="" class="form-control city_dropdown" name="city_id"  required="">
                        <option><?php echo @$jsa->city->name;?></option>
                    </select>
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('zip', 'JSA Zip', ['class' => 'control-label required']) !!}
                    {!! Form::text('zip', old('zip'), ['class' => 'form-control','disabled', 'placeholder' => '','required' => '']) !!}
                </div>
            </div>
            
        </div>
    </div>

   <a href="{{ route('admin.areas.jsa.index',[$area_id]) }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
@stop
@section('javascript')
    @parent
  
@stop