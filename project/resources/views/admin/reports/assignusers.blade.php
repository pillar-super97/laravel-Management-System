@extends('layouts.app')
@section('pageTitle', 'Add Event')
@section('content')
    <h3 class="page-title">Report Access</h3>
    {!! Form::open(['method' => 'POST', 'route' => ['admin.reports.saveassignedusers']]) !!}
    <input type="hidden" name="report_id" class="report_id" value="{{$report_id}}">
    <div class="panel panel-default">
        <div class="panel-heading">Give Report access to Users</div>
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-12 form-group">
                        <label for="report_users" class="control-label">Users</label>
                        <select id="report_users" class="form-control select2" multiple="" name="report_users[]" >
                            <option value="">Select Users</option>
                            <?php foreach ($users as $key=>$user){?>
                            <option value="<?php echo $key;?>" <?php if(in_array($key,$assigned_users)){echo 'selected="selected"';} ?>><?php echo $user;?></option>
                            <?php }?>
                        </select>
                </div>
            </div>
            
            
        </div>
    </div>
    {!! Form::submit('Add', ['class' => 'btn btn-success'],['tabindex'=>15]) !!}
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