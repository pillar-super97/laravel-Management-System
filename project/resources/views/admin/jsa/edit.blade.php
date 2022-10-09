@extends('layouts.app')
@section('pageTitle', 'Edit Area')
@section('content')
    <h3 class="page-title">JSA Areas</h3>
    
    {!! Form::model($jsa, ['method' => 'PUT', 'route' => ['admin.areas.jsa.update',$area_id,$jsa->id], 'files' => true,'autocomplete'=>'off']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">Edit JSA Area</div>
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
                <div class="col-xs-3 form-group">
                    {!! Form::label('title', 'JSA Area Title', ['class' => 'control-label required']) !!}
                    {!! Form::text('title', old('title'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('title'))
                        <p class="error-block">
                            {{ $errors->first('title') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('area_number', 'JSA Area Number', ['class' => 'control-label required']) !!}
                    {!! Form::text('area_number', old('area_number'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('area_number'))
                        <p class="error-block">
                            {{ $errors->first('area_number') }}
                        </p>
                    @endif
                </div>
                
                <div class="col-xs-6 form-group">
                    {!! Form::label('address', 'JSA Address', ['class' => 'control-label required']) !!}
                    {!! Form::text('address', old('address'), ['class' => 'form-control', 'placeholder' => '','required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('address'))
                        <p class="error-block">
                            {{ $errors->first('address') }}
                        </p>
                    @endif
                </div>
                

                <div class="col-xs-3 form-group{{ $errors->has('state_id') ? ' has-error' : '' }}">
                    <label for="primary_state" class="control-label required">JSA State</label>
                    <select id="primary_state" dropdown="primary" class="form-control state_dropdown select2" name="state_id"  required="">
                         <?php foreach ($states as $key=>$state){?>
                        <option value="<?php echo $key;?>" <?php if($key==$jsa->state_id){echo 'selected="selected"';}?>><?php echo $state;?></option>
                        <?php }?>
                    </select>
                    @if ($errors->has('state_id'))
                        <p class="error-block">
                            {{ $errors->first('state_id') }}
                        </p>
                    @endif
                </div>

                <div class="col-xs-3 form-group{{ $errors->has('city_id') ? ' has-error' : '' }}">
                    <label for="primary_city" class="control-label required">JSA City</label>
                    <select id="primary_city" dropdown="primary" class="form-control city_dropdown select2" name="city_id"  required="">
                        <?php foreach ($cities as $key=>$city){?>
                        <option value="<?php echo $key;?>" <?php if($key==$jsa->city_id){echo 'selected="selected"';}?>><?php echo $city;?></option>
                        <?php }?>
                    </select>

                    @if ($errors->has('city_id'))
                        <p class="error-block">
                            {{ $errors->first('city_id') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('zip', 'JSA Zip', ['class' => 'control-label required']) !!}
                    {!! Form::text('zip', old('zip'), ['class' => 'form-control', 'placeholder' => '','required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('zip'))
                        <p class="error-block">
                            {{ $errors->first('zip') }}
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
        $('body').on('focus',".datepicker", function(){
            if( $(this).hasClass('hasDatepicker') === false )  {
                $(this).datepicker({
            autoclose: true,
            format:'mm-dd-yyyy'
        });
            }

        });
        $('.datepicker').datepicker({
            autoclose: true,
            format:'mm-dd-yyyy'
        })
        $('.blackout_dates_container').on('click', '.remove_blackout', function(events){
            var blackout_counter = $(this).attr('blackout');
            $('.blackout_counter'+blackout_counter).remove();
        });
        $('.add_more').click(function(){
            var blackout_counter = $('#blackout_counter').val();
            blackout_counter++;
            var html ='<div style="height:75px;" class="col-xs-2 blackout_counter'+blackout_counter+'"><label for="blackout_dates" class="control-label">Blackout Dates</label>\n\
                        <input class="form-control datepicker" name="blackout_dates[]" type="text" autocomplete="off"></div><div style="height:75px;" blackout="'+blackout_counter+'" class="col-xs-1 remove_blackout blackout_counter'+blackout_counter
                        +'"><i class="fa fa-trash" aria-hidden="true"></i></div>';
            $('#blackout_counter').val(blackout_counter);
            $(".blackout_dates_container").append(html);
           // alert('sdf');
        })
        $('[data-mask]').inputmask();
        $('.timepicker').timepicker({
            showInputs: false
        })
    
    $('.state_dropdown').on('change',function(){
    var stateID = $(this).val();
    var ele = $(this).attr('dropdown');   
    if(stateID){
        $.ajax({
           type:"GET",
           url:"{{url('get-city-list')}}?state_id="+stateID,
           success:function(res){               
            if(res){
                $("#"+ele+"_city").empty();
                $.each(res,function(key,value){
                    $("#"+ele+"_city").append('<option value="'+key+'">'+value+'</option>');
                });
           
            }else{
               $("#"+ele+"_city").empty();
            }
           }
        });
    }else{
        $("#"+ele+"_city").empty();
    }
        
   });})
</script>
@stop