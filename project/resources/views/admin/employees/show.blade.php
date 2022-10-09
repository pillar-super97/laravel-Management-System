@extends('layouts.app')
@section('pageTitle', 'Edit Employee')
@section('content')
    <h3 class="page-title">Employees</h3>
    
    {!! Form::model($employee, ['method' => 'PUT', 'route' => ['admin.employees.update', $employee->id], 'files' => true,'autocomplete'=>'off']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">Employee Details</div>
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-3 form-group">
                    {!! Form::label('first_name', 'First Name', ['class' => 'control-label']) !!}
                    {!! Form::text('first_name', old('first_name'), ['class' => 'form-control','disabled']) !!}
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('last_name', 'Last Name', ['class' => 'control-label']) !!}
                    {!! Form::text('last_name', old('last_name'), ['class' => 'form-control','disabled']) !!}
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('emp_number', 'Employee Number', ['class' => 'control-label']) !!}
                    {!! Form::text('emp_number', old('emp_number'), ['class' => 'form-control','disabled']) !!}
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('ss_no', 'SS #', ['class' => 'control-label']) !!}
                    {!! Form::text('ss_no', old('ss_no'), ['class' => 'form-control','disabled']) !!}
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('title', 'Title', ['class' => 'control-label']) !!}
                    {!! Form::text('title', old('title'), ['class' => 'form-control','disabled', 'placeholder' => '','required' => '']) !!}
                </div>
                

                <div class="col-xs-3 form-group">
                    {!! Form::label('benchmark', 'Benchmark', ['class' => 'control-label']) !!}
                    {!! Form::text('benchmark', old('benchmark'), ['class' => 'form-control','disabled']) !!}
                </div>
                <?php if($employee->area->area_number!=99){?>
                <div class="col-xs-3 form-group">
                    {!! Form::label('payrate', 'Payrate', ['class' => 'control-label']) !!}
                    {!! Form::text('payrate', old('payrate'), ['class' => 'form-control','disabled']) !!}
                </div>
                <?php }?>
                <div class="col-xs-3 form-group">
                    {!! Form::label('area', 'Area', ['class' => 'control-label']) !!}
                    {!! Form::text('area', $employee->area->title, ['class' => 'form-control','disabled']) !!}
                </div>
            
                 <div class="col-xs-3 form-group">
                    {!! Form::label('jsa', 'JSA', ['class' => 'control-label']) !!}
                    {!! Form::text('jsa', $employee->jsa->title, ['class' => 'form-control','disabled']) !!}
                </div>
                <div class="col-xs-3 form-group">
                    <label class="control-label">Driver</label><br>
                    <?php echo ($employee->is_driver)?'Yes':'No';?>
                </div>
                <div class="col-xs-3 form-group">
                    <label class="control-label">RX</label><br>
                   <?php echo ($employee->is_rx)?'Yes':'No';?>
                </div>
                <div class="col-xs-3 form-group">
                    <label class="control-label">Crew Leader</label><br>
                   <?php echo ($employee->is_crew_leader)?'Yes':'No';?>
                </div>
            </div>
        </div>
    </div>

   <a href="{{ route('admin.employees.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
@stop
@section('javascript')
    @parent
  
@stop