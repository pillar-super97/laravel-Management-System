@extends('layouts.app')
@section('pageTitle', 'Add Association')
@section('content')
    <h3 class="page-title">Associations</h3>
    {!! Form::open(['method' => 'POST', 'route' => ['admin.associations.store'], 'files' => true,]) !!}

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
                    {!! Form::text('address', old('address'), ['class' => 'form-control', 'placeholder' => '','required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('address'))
                        <p class="error-block">
                            {{ $errors->first('address') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('phone', 'Phone', ['class' => 'control-label required']) !!}
                    <input id="phone" type="text" name="phone" required="" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask>
                    <p class="help-block"></p>
                    @if($errors->has('phone'))
                        <p class="error-block">
                            {{ $errors->first('phone') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('rebate', 'Rebate (%)', ['class' => 'control-label required']) !!}
                    {!! Form::text('rebate', old('rebate'), ['class' => 'form-control', 'placeholder' => '']) !!}
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
                    <label for="state_id" class="control-label">State</label>
                    <select id="state_id" class="form-control select2" name="state_id"  required="">
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

                <div class="col-xs-3 form-group{{ $errors->has('city_id') ? ' has-error' : '' }}">
                    <label for="city_id" class="control-label">City</label>
                    <select id="city_id" class="form-control select2" name="city_id"  required="">
                        <option value="">Select City</option>
                    </select>

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
            </div>
            <div class="row">
                <div class="col-xs-3 form-group">
                    {!! Form::label('primary_contact_email', 'Primary Contact Email', ['class' => 'control-label']) !!}
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
                    {!! Form::text('primary_contact_phone', old('primary_contact_phone'), ['class' => 'form-control','data-inputmask'=>'"mask": "(999) 999-9999"','data-mask'=>'data-mask']) !!}
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
                    {!! Form::text('secondary_contact_phone', old('secondary_contact_phone'), ['class' => 'form-control', 'data-inputmask'=>'"mask": "(999) 999-9999"','data-mask'=>'data-mask']) !!}
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
                    {!! Form::text('alternate_contact_phone', old('alternate_contact_phone'), ['class' => 'form-control', 'data-inputmask'=>'"mask": "(999) 999-9999"','data-mask'=>'data-mask']) !!}
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
                        <p class="error-block">
                            {{ $errors->first('notes') }}
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
        $('[data-mask]').inputmask();
    $('#state_id').on('change',function(){
    var stateID = $(this).val();    
    if(stateID){
        $.ajax({
           type:"GET",
           url:"{{url('get-city-list')}}?state_id="+stateID,
           success:function(res){               
            if(res){
                $("#city_id").empty();
                $.each(res,function(key,value){
                    $("#city_id").append('<option value="'+key+'">'+value+'</option>');
                });
           
            }else{
               $("#city_id").empty();
            }
           }
        });
    }else{
        $("#city_id").empty();
    }
        
   });})
</script>
@stop