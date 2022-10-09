@extends('layouts.app')
@section('pageTitle', 'Edit Association')
@section('content')
    <h3 class="page-title">Associations</h3>
    
    {!! Form::model($association, ['method' => 'PUT', 'route' => ['admin.associations.update', $association->id], 'files' => true,]) !!}

    <div class="panel panel-default">
        <div class="panel-heading">Add New Association</div>
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
            <div class="row">
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
                    {!! Form::text('address', old('address'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('address'))
                        <p class="error-block">
                            {{ $errors->first('address') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('phone', 'Phone', ['class' => 'control-label required']) !!}
                    {!! Form::text('phone', old('phone'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block">(01xxxxxxxxx)</p>
                    @if($errors->has('phone'))
                        <p class="error-block">
                            {{ $errors->first('phone') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('rebate', 'Rebate (%)', ['class' => 'control-label required']) !!}
                    {!! Form::text('rebate', old('rebate'), ['class' => 'form-control', 'placeholder' => '','maxlength'=>2]) !!}
                    <p class="help-block"></p>
                    @if($errors->has('rebate'))
                        <p class="error-block">
                            {{ $errors->first('rebate') }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="row">
               

                <div class="col-xs-3 form-group{{ $errors->has('state_id') ? ' has-error' : '' }}">
                    {!! Form::label('state_id', 'State', ['class' => 'control-label']) !!}
                    {!! Form::select('state_id', $states, old('state_id'), ['class' => 'form-control select2']) !!}
                    <p class="help-block"></p>
                    @if ($errors->has('state_id'))
                        <p class="error-block">
                            {{ $errors->first('state_id') }}
                        </p>
                    @endif
                </div>

                <div class="col-xs-3 form-group{{ $errors->has('city_id') ? ' has-error' : '' }}">
                    {!! Form::label('city_id', 'City', ['class' => 'control-label']) !!}
                    {!! Form::select('city_id', $cities, old('city_id'), ['class' => 'form-control select2']) !!}
                    <p class="help-block"></p>
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
                <div class="col-xs-3 form-group">
                    {!! Form::label('primary_contact_email', 'Primary Contact Title', ['class' => 'control-label']) !!}
                    {!! Form::text('primary_contact_email', old('primary_contact_email'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('primary_contact_email'))
                        <p class="error-block">
                            {{ $errors->first('primary_contact_email') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('primary_contact_name', 'Primary Contact Name', ['class' => 'control-label']) !!}
                    {!! Form::text('primary_contact_name', old('primary_contact_name'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('primary_contact_name'))
                        <p class="error-block">
                            {{ $errors->first('primary_contact_name') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('primary_contact_address', 'Primary Contact Address', ['class' => 'control-label']) !!}
                    {!! Form::text('primary_contact_address', old('primary_contact_address'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('primary_contact_address'))
                        <p class="error-block">
                            {{ $errors->first('primary_contact_address') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('primary_contact_phone', 'Primary Contact Phone', ['class' => 'control-label']) !!}
                    {!! Form::text('primary_contact_phone', old('primary_contact_phone'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('primary_contact_phone'))
                        <p class="error-block">
                            {{ $errors->first('primary_contact_phone') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('secondary_contact_email', 'Secondary Contact Email', ['class' => 'control-label']) !!}
                    {!! Form::text('secondary_contact_email', old('secondary_contact_email'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('secondary_contact_email'))
                        <p class="error-block">
                            {{ $errors->first('secondary_contact_email') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('secondary_contact_name', 'Secondary Contact Name', ['class' => 'control-label']) !!}
                    {!! Form::text('secondary_contact_name', old('secondary_contact_name'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('secondary_contact_name'))
                        <p class="error-block">
                            {{ $errors->first('secondary_contact_name') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('secondary_contact_address', 'Secondary Contact Address', ['class' => 'control-label']) !!}
                    {!! Form::text('secondary_contact_address', old('secondary_contact_address'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('secondary_contact_address'))
                        <p class="error-block">
                            {{ $errors->first('secondary_contact_address') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('secondary_contact_phone', 'Secondary Contact Phone', ['class' => 'control-label']) !!}
                    {!! Form::text('secondary_contact_phone', old('primary_contact_phone'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('secondary_contact_phone'))
                        <p class="error-block">
                            {{ $errors->first('secondary_contact_phone') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('alternate_contact_email', 'Alternate Contact Email', ['class' => 'control-label']) !!}
                    {!! Form::text('alternate_contact_email', old('alternate_contact_email'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('alternate_contact_email'))
                        <p class="error-block">
                            {{ $errors->first('alternate_contact_email') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('alternate_contact_name', 'Alternate Contact Name', ['class' => 'control-label']) !!}
                    {!! Form::text('alternate_contact_name', old('alternate_contact_name'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('alternate_contact_name'))
                        <p class="error-block">
                            {{ $errors->first('alternate_contact_name') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('alternate_contact_address', 'Alternate Contact Address', ['class' => 'control-label']) !!}
                    {!! Form::text('alternate_contact_address', old('alternate_contact_address'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('alternate_contact_address'))
                        <p class="error-block">
                            {{ $errors->first('alternate_contact_address') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('alternate_contact_phone', 'Alternate Contact Phone', ['class' => 'control-label']) !!}
                    {!! Form::text('alternate_contact_phone', old('alternate_contact_phone'), ['class' => 'form-control', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('alternate_contact_phone'))
                        <p class="error-block">
                            {{ $errors->first('alternate_contact_phone') }}
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
                        <p class="help-block">
                            {{ $errors->first('notes') }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {!! Form::submit(trans('global.app_update'), ['class' => 'btn btn-danger']) !!}
    {!! Form::close() !!}
@stop
@section('javascript')
    @parent
    <script>
    </script>
@stop