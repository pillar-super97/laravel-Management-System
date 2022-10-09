@extends('layouts.app')
@section('pageTitle', 'Edit Mileage')
@section('content')
    <h3 class="page-title">Mileages</h3>
    
    {!! Form::model($mileage, ['method' => 'PUT', 'route' => ['admin.mileages.update', $mileage->id], 'files' => true,'autocomplete'=>'off']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">Edit Mileage</div>
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
                
                <div class="col-xs-3 form-group{{ $errors->has('store_id') ? ' has-error' : '' }}">
                    <label for="store_id" class="control-label">Select Store</label>
                    <select id="store_id" class="form-control" name="store_id" >
                        <option value="">Select Store</option>
                        <?php foreach ($stores as $key=>$store){?>
                        <option value="<?php echo $key;?>" <?php if($mileage->store_id==$key){echo 'selected';}?>><?php echo $store;?></option>
                        <?php }?>
                    </select>

                    @if ($errors->has('store_id'))
                        <p class="error-block">
                            {{ $errors->first('store_id') }}
                        </p>
                    @endif
                </div>
                
                <div class="col-xs-3 form-group{{ $errors->has('area_id') ? ' has-error' : '' }}">
                    <label for="area_id" class="control-label">Select Area</label>
                    <select id="area_id" class="form-control" name="area_id" >
                        <option value="">Select Areas</option>
                        <?php foreach ($areas as $area){?>
                        <option value="<?php echo $area->id;?>" <?php if($mileage->area_id==$area->id){echo 'selected';}?>><?php echo $area->title;?></option>
                        <?php }?>
                    </select>

                    @if ($errors->has('area_id'))
                        <p class="error-block">
                            {{ $errors->first('area_id') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group{{ $errors->has('jsa_id') ? ' has-error' : '' }}">
                    <label for="jsa_id" class="control-label">Select JSA</label>
                    <select id="jsa_id" class="form-control" name="jsa_id" >
                        <option value="">Select JSA</option>
                        <?php foreach ($jsas as $jsa){?>
                        <option value="<?php echo $jsa->id;?>" <?php if($mileage->jsa_id==$jsa->id){echo 'selected';}?>><?php echo $jsa->title;?></option>
                        <?php }?>
                    </select>

                    @if ($errors->has('jsa_id'))
                        <p class="error-block">
                            {{ $errors->first('jsa_id') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('distance', 'Distance', ['class' => 'control-label required']) !!}
                    {!! Form::text('distance', old('distance'), ['class' => 'form-control','required', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('distance'))
                        <p class="error-block">
                            {{ $errors->first('distance') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('duration', 'Travelling Duration', ['class' => 'control-label required']) !!}
                    {!! Form::text('duration', old('duration'), ['class' => 'form-control','required', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('duration'))
                        <p class="error-block">
                            {{ $errors->first('duration') }}
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
        $('#store_id').change(function(){
            var store_id = $(this).val();
            if(store_id){
                $.ajax({
                   type:"POST",
                   url:"{{url('admin/getAreaByStore')}}",
                   data:{_token: _token,store_id:store_id},
                   success:function(res){               
                    if(res){
                        $("#area_id").empty();
                        $("#area_id").append('<option>Select Area</option>');
                        $.each(res.areas,function(key,value){
                            $("#area_id").append('<option value="'+value.id+'">'+value.title+'</option>');
                        });

                    }else{
                       $("#area_id").empty();
                    }
                   }
                });
            }else{
                $("#area_id").empty();
            }      
        });
        $('#area_id').change(function(){
            var area_id = $(this).val();
            var store_id = $("#store_id").val();
            if(area_id){
                $.ajax({
                   type:"POST",
                   url:"{{url('admin/getJsaByArea')}}",
                   data:{_token: _token,area_id:area_id,store_id:store_id},
                   success:function(res){               
                    if(res){
                        $("#jsa_id").empty();
                        $("#jsa_id").append('<option>Select JSA</option>');
                        $.each(res.jsas,function(key,value){
                            $("#jsa_id").append('<option value="'+value.id+'">'+value.title+'</option>');
                        });

                    }else{
                       $("#jsa_id").empty();
                    }
                   }
                });
            }else{
                $("#jsa_id").empty();
            }      
        });
        $(document).on('change','#jsa_id',function(){
            var jsa_id = $("#jsa_id").val();
            var store_id = $("#store_id").val();
            if(jsa_id && store_id)
            {
                $.ajax({
                type:"POST",
                url:"{{url('admin/calculateDistance/')}}",
                data:{_token: _token,jsa_id:jsa_id,store_id:store_id},
                success:function(res){
                    console.log(res);
                    var dist = (res.data.rows[0].elements[0].distance.value/1000)*0.621371;
                    $('#distance').val(dist.toFixed(2));
                    var duration = secondsToHms(res.data.rows[0].elements[0].duration.value+(res.data.rows[0].elements[0].duration.value/3600)*15*60);
                    //alert(duration);
                    $('#duration').val(duration);
               }
            });
            }
        });
    })
    function secondsToHms(d) {
        d = Number(d);
        var h = Math.floor(d / 3600);
        var m = Math.floor(d % 3600 / 60);
        //var s = Math.floor(d % 3600 % 60);

        var hDisplay = h > 0 ? h + (h == 1 ? " hour, " : " hours ") : "";
        var mDisplay = m > 0 ? m + (m == 1 ? " minute, " : " minutes ") : "";
        //var sDisplay = s > 0 ? s + (s == 1 ? " second" : " seconds") : "";
        return hDisplay + mDisplay; 
    }
</script>
@stop