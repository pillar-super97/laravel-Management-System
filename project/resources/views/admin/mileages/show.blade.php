@extends('layouts.app')
@section('pageTitle', 'View Mileage')
@section('content')
    <h3 class="page-title">Mileages</h3>
    
    {!! Form::model($mileage, ['method' => 'PUT', 'route' => ['admin.mileages.update', $mileage->id], 'files' => true,'autocomplete'=>'off']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">View Mileage</div>
        <div class="panel-body">
            <div class="row">
                
                <div class="col-xs-3 form-group{{ $errors->has('store_id') ? ' has-error' : '' }}">
                    <label for="store_id" class="control-label">Select Store</label>
                    <select id="store_id" class="form-control" name="store_id" disabled="" >
                        <option ><?php echo $mileage->store->name;?></option>
                    </select>

                   
                </div>
                
                <div class="col-xs-3 form-group{{ $errors->has('area_id') ? ' has-error' : '' }}">
                    <label for="area_id" class="control-label">Select Area</label>
                    <select id="area_id" class="form-control" name="area_id" disabled="" >
                        <option ><?php echo $mileage->area->title;?></option>
                    </select>
                   
                </div>
                <div class="col-xs-3 form-group{{ $errors->has('jsa_id') ? ' has-error' : '' }}">
                    <label for="jsa_id" class="control-label">Select JSA</label>
                    <select id="jsa_id" class="form-control" name="jsa_id" disabled="" >
                        <option><?php echo $mileage->jsa->title;?></option>
                    </select>
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('distance', 'Distance', ['class' => 'control-label required']) !!}
                    {!! Form::text('distance', old('distance'), ['class' => 'form-control','disabled','required', 'placeholder' => '']) !!}
                   
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('duration', 'Travelling Duration', ['class' => 'control-label required']) !!}
                    {!! Form::text('duration', old('duration'), ['class' => 'form-control','disabled','required', 'placeholder' => '']) !!}
                   
                </div>
            </div>
        </div>
    </div>

   <a href="{{ route('admin.mileages.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
@stop
@section('javascript')
    @parent
  
@stop