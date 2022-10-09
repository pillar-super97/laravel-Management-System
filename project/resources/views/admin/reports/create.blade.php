@extends('layouts.app')
@section('pageTitle', 'Add Report')
@section('content')
    <h3 class="page-title">Reports</h3>
    {!! Form::open(['method' => 'POST', 'route' => ['admin.reports.store'], 'files' => true,]) !!}

    <div class="panel panel-default">
        <div class="panel-heading">Add New Report</div>
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
        <?php $arrays = arrays();?>
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

    {!! Form::submit('Add', ['class' => 'btn btn-success']) !!}
    {!! Form::reset('Cancel', ['class' => 'btn btn-warning cancel-btn']) !!}
    {!! Form::close() !!}
@stop

@section('javascript')
    @parent
<script type="text/javascript">
    $(document).ready(function(){
        
    })
</script>
@stop