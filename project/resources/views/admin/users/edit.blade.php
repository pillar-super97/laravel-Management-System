@extends('layouts.app')
@section('pageTitle', 'Edit User')
@section('content')
<h3 class="page-title">Edit User</h3>



<div class="panel panel-default">

    <div class="error-container">
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
    {!! Form::model($user, ['method' => 'PUT', 'route' => ['admin.users.update', $user->id]]) !!}
        <div class="row">
            <div class="col-xs-12 col-md-4 form-group">
                {!! Form::label('name', 'Name', ['class' => 'control-label required']) !!}
                {!! Form::text('name', old('name'), ['class' => 'form-control', 'placeholder' => '', 'required' => ''])
                !!}
                <p class="help-block"></p>
                @if($errors->has('name'))
                <p class="help-block">
                    {{ $errors->first('name') }}
                </p>
                @endif
            </div>

            <div class="col-xs-12 col-md-4  form-group">
                {!! Form::label('email', 'Email', ['class' => 'control-label required']) !!}
                {!! Form::email('email', old('email'), ['class' => 'form-control', 'placeholder' => '', 'required' =>
                '']) !!}
                <p class="help-block"></p>
                @if($errors->has('email'))
                <p class="help-block">
                    {{ $errors->first('email') }}
                </p>
                @endif
            </div>
            @if(0)
            <div class="col-xs-12 col-md-4 form-group">
                {!! Form::label('password', 'Password', ['class' => 'control-label']) !!}
                {!! Form::password('password', ['class' => 'form-control', 'placeholder' => '']) !!}
                <p class="help-block"></p>
                @if($errors->has('password'))
                <p class="help-block">
                    {{ $errors->first('password') }}
                </p>
                @endif
            </div>
            @endif

            <div class="col-xs-12 col-md-4 form-group">
                {!! Form::label('role', 'Role', ['class' => 'control-label required']) !!}
                {!! Form::select('role[]', $roles, old('role') ? old('role') : $user->role->pluck('id')->toArray(),
                ['class' => 'form-control select2', 'multiple' => 'multiple', 'required' => '']) !!}
                <p class="help-block"></p>
                @if($errors->has('role'))
                <p class="help-block">
                    {{ $errors->first('role') }}
                </p>
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 form-group area_container"
                style="display: <?php if(in_array(4,$user->role->pluck('id')->toArray()) || in_array(5,$user->role->pluck('id')->toArray()) || in_array(8,$user->role->pluck('id')->toArray())){echo 'block';}else{ echo 'none';}?>">
                {!! Form::label('area', 'Area', ['class' => 'control-label']) !!}
                {!! Form::select('area[]', $areas, old('area'), ['class' => 'form-control select2
                area_dropdown','style'=>'width:100%', 'multiple' => 'multiple']) !!}
                <p class="help-block"></p>
                @if($errors->has('area'))
                <p class="help-block">
                    {{ $errors->first('area') }}
                </p>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-md-4 form-group">
                {!! Form::label('employee_name', 'Associate with Employee?', ['class' => 'control-label']) !!}
                {!! Form::select('employee_id', $employees, old('employee_id'), ['class' => 'form-control select2']) !!}


            </div>

            <div class="col-xs-12 col-md-4 form-group">
                {!! Form::label('employee_name', 'Associate with Client?', ['class' => 'control-label']) !!}
                {!! Form::select('client_id', $clients, $user->client_id ?? old('client_id'), ['class' => 'form-control select2']) !!}
            </div>

        </div>
        
{!! Form::submit(trans('global.app_update'), ['class' => 'btn btn-success pull-right ']) !!}
{!! Form::close() !!}
    </div>
</div>

@stop

@section('javascript')
<script type="text/javascript">
$(document).ready(function() {
    $('.role_dropdown').on('change', function() {
        roles = $(this).val();
        var arr = roles.toString().split(',');
        if (arr.indexOf('4') != -1 || arr.indexOf('5') != -1 || arr.indexOf('8') != -1) {
            $('.area_container').show();
        } else {
            $(".area_dropdown").val('').trigger('change')
            $('.area_container').hide();
        }
    });
})
</script>
@stop