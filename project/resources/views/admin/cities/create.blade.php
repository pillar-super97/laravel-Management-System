@extends('layouts.app')
@section('pageTitle', 'Add City')
@section('content')
    <h3 class="page-title">Cities</h3>
    {!! Form::open(['method' => 'POST', 'route' => ['admin.cities.store'], 'files' => true,]) !!}

    <div class="panel panel-default">
        <div class="panel-heading">Add New City</div>
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
                
                <div class="col-xs-5 form-group{{ $errors->has('state_id') ? ' has-error' : '' }}">
                    <label for="state_id" class="control-label">Select State</label>
                    <select id="state_id" class="form-control" name="state_id" >
                        <option value="">Select State</option>
                        <?php foreach ($states as $key=>$state){?>
                        <option value="<?php echo $key;?>"><?php echo $state;?></option>
                        <?php }?>
                    </select>

                    @if ($errors->has('state_id'))
                        <p class="error-block">
                            {{ $errors->first('state_id') }}
                        </p>
                    @endif
                </div>
                
              
                <div class="col-xs-5 form-group">
                    {!! Form::label('name', 'Name', ['class' => 'control-label required']) !!}
                    {!! Form::text('name', old('name'), ['class' => 'form-control','required', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('name'))
                        <p class="error-block">
                            {{ $errors->first('name') }}
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
    
   
</script>
@stop