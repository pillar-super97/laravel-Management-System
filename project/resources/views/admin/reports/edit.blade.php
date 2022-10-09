@extends('layouts.app')
@section('pageTitle', 'Edit Report')
@section('content')
    <h3 class="page-title">Reports</h3>
    
    {!! Form::model($report, ['method' => 'PUT', 'route' => ['admin.reports.update', $report->id], 'files' => true,'autocomplete'=>'off']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">Edit Report</div>
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-6 form-group">
                    {!! Form::label('rpt_name', 'Report Name', ['class' => 'control-label required']) !!}
                    {!! Form::text('rpt_name', old('rpt_name'), ['class' => 'form-control required', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('rpt_name'))
                        <p class="error-block">
                            {{ $errors->first('rpt_name') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-6 form-group">
                    {!! Form::label('rpt_file_name', 'Report File Name(.rpt)', ['class' => 'control-label required']) !!}
                    {!! Form::text('rpt_file_name', old('rpt_name'), ['class' => 'form-control required', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('rpt_name'))
                        <p class="error-block">
                            {{ $errors->first('rpt_name') }}
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
<script type="text/javascript">
    $(document).ready(function(){
        
    })
</script>
@stop