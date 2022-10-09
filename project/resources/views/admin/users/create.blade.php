@extends('layouts.app')
@section('pageTitle', 'Add User')
@section('content')
    <h3 class="page-title">@lang('global.users.title')</h3>
    {!! Form::open(['method' => 'POST', 'route' => ['admin.users.store'],'autocomplete'=>'off']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_create')
        </div>
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
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('name', 'Name', ['class' => 'control-label required']) !!}
                    {!! Form::text('name', old('name'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('name'))
                        <p class="help-block">
                            {{ $errors->first('name') }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('email', 'Email', ['class' => 'control-label required']) !!}
                    {!! Form::email('email', old('email'), ['class' => 'form-control','autocomplete'=>'off', 'placeholder' => '', 'required' => 'required']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('email'))
                        <p class="help-block">
                            {{ $errors->first('email') }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('password', 'Password', ['class' => 'control-label required']) !!}
                    {!! Form::password('password', ['class' => 'form-control', 'placeholder' => '', 'required' => '','autocomplete'=>'off']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('password'))
                        <p class="help-block">
                            {{ $errors->first('password') }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('role', 'Role', ['class' => 'control-label required']) !!}
                    {!! Form::select('role[]', $roles, old('role'), ['class' => 'form-control select2 role_dropdown', 'multiple' => 'multiple', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('role'))
                        <p class="help-block">
                            {{ $errors->first('role') }}
                        </p>
                    @endif
                </div>
            </div>
            
            <div class="row">
                <div class="col-xs-12 form-group area_container" style="display: none;">
                    {!! Form::label('area', 'Area', ['class' => 'control-label']) !!}
                    {!! Form::select('area[]', $areas, old('area'), ['class' => 'form-control select2 area_dropdown','style'=>'width:100%', 'multiple' => 'multiple']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('area'))
                        <p class="help-block">
                            {{ $errors->first('area') }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('employee_name', 'Employee ID', ['class' => 'control-label']) !!}
                    {!! Form::text('employee_name', old('employee_name'),['class' => 'form-control']) !!}

                    {!! Form::hidden('employee_id', old('employee_id'),['id'=>'employee_id']) !!}
                </div>
            </div>
        </div>
    </div>

    {!! Form::submit(trans('global.app_save'), ['class' => 'btn btn-danger']) !!}
    {!! Form::close() !!}
@stop

@section('javascript')
<script type="text/javascript">
    $(document).ready(function(){
        $( "#employee_name" ).autocomplete({
        source: <?php echo json_encode($emps)?>,
        select: function (event, ui) {
         $('#employee_name').val(ui.item.label); // display the selected text
         $('#employee_id').val(ui.item.value); // save selected id to input
         return false;
        }
       });
        $('.role_dropdown').on('change',function(){
            roles = $(this).val();
            var arr = roles.toString().split(',');
            if(arr.indexOf('4') != -1 || arr.indexOf('5') != -1 || arr.indexOf('8') != -1)
            {
                $('.area_container').show();
            }else{
                $(".area_dropdown").val('').trigger('change')
                $('.area_container').hide();
            }
        });
   })
</script>
@stop