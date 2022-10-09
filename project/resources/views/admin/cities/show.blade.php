@extends('layouts.app')
@section('pageTitle', 'View City')
@section('content')
    <h3 class="page-title">Cities</h3>
    
    {!! Form::model($city, ['method' => 'PUT', 'route' => ['admin.cities.update', $city->id], 'files' => true,'autocomplete'=>'off']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">View City</div>
        <div class="panel-body">
            <div class="row">
                
                <div class="col-xs-3 form-group{{ $errors->has('state_id') ? ' has-error' : '' }}">
                    <label for="state_id" class="control-label">State</label>
                    <select id="state_id" class="form-control" name="state_id" disabled="" >
                        <option ><?php echo $city->state->name;?></option>
                    </select>

                   
                </div>
                
               
                <div class="col-xs-3 form-group">
                    {!! Form::label('name', 'City Name', ['class' => 'control-label required']) !!}
                    {!! Form::text('name', old('name'), ['class' => 'form-control','disabled','required', 'placeholder' => '']) !!}
                   
                </div>
                
            </div>
        </div>
    </div>

   <a href="{{ route('admin.cities.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
@stop
@section('javascript')
    @parent
  
@stop