@extends('layouts.app')
@section('pageTitle', 'Add Event')
@section('content')
    <h3 class="page-title">Events</h3>
    {!! Form::open(['method' => 'POST', 'route' => ['admin.events.store'], 'files' => true,]) !!}

    <div class="panel panel-default">
        <div class="panel-heading">Add New Event</div>
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
            <div class="col-xs-9">
            <div class="row">
<!--                <div class="col-xs-3 form-group">
                    {!! Form::label('number', 'Event Number', ['class' => 'control-label required']) !!}
                    {!! Form::text('number', old('number'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('number'))
                        <p class="error-block">
                            {{ $errors->first('number') }}
                        </p>
                    @endif
                </div>-->
                <div class="col-xs-3 form-group{{ $errors->has('crew_leader') ? ' has-error' : '' }}">
                    {!! Form::label('crew_leader_name', 'Crew Leader', ['class' => 'control-label']) !!}
                    {!! Form::text('crew_leader_name', old('crew_leader_name'),['class' => 'form-control','tabindex'=>1]) !!}
                    
                    {!! Form::hidden('crew_leader', old('crew_leader'),['class' => 'form-control','id'=>'crew_leader']) !!}
                    @if ($errors->has('crew_leader'))
                        <p class="error-block">
                            {{ $errors->first('crew_leader') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group{{ $errors->has('store_id') ? ' has-error' : '' }}">
                    {!! Form::label('store_name', 'Store', ['class' => 'control-label required']) !!}
                    {!! Form::text('store_name', old('store_name'),['class' => 'form-control','tabindex'=>2,'required' => 'required']) !!}
                    {!! Form::hidden('store_id', old('store_id'),['id'=>'store_id','required' => 'required']) !!}
                    @if ($errors->has('store_id'))
                        <p class="error-block">
                            {{ $errors->first('store_id') }}
                        </p>
                    @endif
                </div>
                
                <div class="col-xs-3" style="height:75px;">
                    {!! Form::label('date', 'Date', ['class' => 'control-label required']) !!}
                    {!! Form::text('date', old('date'), ['class' => 'form-control datepicker','tabindex'=>3,'required', 'autocomplete'=>'off','placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('date'))
                        <p class="error-block">
                            {{ $errors->first('date') }}
                        </p>
                    @endif
                </div>
            </div>
           
            <div class="row">
                
                
                <div class="col-xs-3 form-group">
                    {!! Form::label('start_time', 'Start Time', ['class' => 'control-label required']) !!}
                    {!! Form::text('start_time', old('start_time'), ['class' => 'form-control timepicker','tabindex'=>4,'required', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('start_time'))
                        <p class="error-block">
                            {{ $errors->first('start_time') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('run_number', 'Run Number', ['class' => 'control-label required']) !!}
                    {!! Form::text('run_number', old('run_number'),['class' => 'form-control','tabindex'=>5,'required' => 'required']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('run_number'))
                        <p class="error-block">
                            {{ $errors->first('run_number') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group{{ $errors->has('areas') ? ' has-error' : '' }}">
                    <label for="areas" class="control-label required">Area</label>
                    <select id="areas" class="form-control select2" required="" name="areas[]" multiple="" tabindex="6">
                        <option value="">Select Area</option>
                        <?php foreach ($areas as $key=>$area){?>
                        <option value="<?php echo $key;?>"><?php echo $area;?></option>
                        <?php }?>
                    </select>

                    @if ($errors->has('areas'))
                        <p class="error-block">
                            {{ $errors->first('areas') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('crew_count', 'Crew Count', ['class' => 'control-label']) !!}
                    {!! Form::text('crew_count', old('crew_count'), ['class' => 'form-control','tabindex'=>7, 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('max_length'))
                        <p class="error-block">
                            {{ $errors->first('max_length') }}
                        </p>
                    @endif
                </div>
            </div>
           
            <div class="row">   
                
             
                <div class="col-xs-3 form-group">
                    <label class="control-label">Overnight</label><br>
                    <label class="control-label">                   
                        {!! Form::radio('overnight','Yes',['class' => 'form-control'],['tabindex'=>8]) !!} Yes
                    </label>
                    <label class="control-label">                   
                        {!! Form::radio('overnight','No',['class' => 'form-control'],['tabindex'=>8]) !!} No
                    </label>
                    <p class="help-block"></p>
                    @if($errors->has('overnight'))
                        <p class="error-block">
                            {{ $errors->first('overnight') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    <label class="control-label">PIC</label><br>
                    <label class="control-label">                   
                        {!! Form::radio('pic','Yes',['class' => 'form-control'],['tabindex'=>9]) !!} Yes
                    </label>
                    <label class="control-label">                   
                        {!! Form::radio('pic','No',['class' => 'form-control'],['tabindex'=>9]) !!} No
                    </label>
                    <p class="help-block"></p>
                    @if($errors->has('pic'))
                        <p class="error-block">
                            {{ $errors->first('pic') }}
                        </p>
                    @endif
                </div>
                
                <div class="col-xs-3 form-group">
                    <label class="control-label">QC</label><br>
                    <label class="control-label">                   
                        {!! Form::radio('qc','Yes',['class' => 'form-control'],['tabindex'=>10]) !!} Yes
                    </label>
                    <label class="control-label">                   
                        {!! Form::radio('qc','No',['class' => 'form-control'],['tabindex'=>10]) !!} No
                    </label>
                    <p class="help-block"></p>
                    @if($errors->has('qc'))
                        <p class="error-block">
                            {{ $errors->first('qc') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    <label class="control-label">Count RX</label><br>
                    <label class="control-label">                   
                        {!! Form::radio('count_rx','Yes',['class' => 'form-control'],['tabindex'=>11]) !!} Yes
                    </label>
                    <label class="control-label">                   
                        {!! Form::radio('count_rx','No',['class' => 'form-control'],['tabindex'=>11]) !!} No
                    </label>
                    <p class="help-block"></p>
                    @if($errors->has('count_rx'))
                        <p class="error-block">
                            {{ $errors->first('count_rx') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    <label class="control-label">Count Backroom</label><br>
                    <label class="control-label">                   
                        {!! Form::radio('count_backroom','Yes',['class' => 'form-control'],['tabindex'=>12]) !!} Yes
                    </label>
                    <label class="control-label">                   
                        {!! Form::radio('count_backroom','No',['class' => 'form-control'],['tabindex'=>12]) !!} No
                    </label>
                    <p class="help-block"></p>
                    @if($errors->has('count_backroom'))
                        <p class="error-block">
                            {{ $errors->first('count_backroom') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group{{ $errors->has('road_trip') ? ' has-error' : '' }}">
                    {!! Form::label('road_trip', 'Road Trip', ['class' => 'control-label']) !!}
                    {!! Form::text('road_trip', 'No',['class' => 'form-control','tabindex'=>13,'required' => 'required']) !!}
                    @if ($errors->has('road_trip'))
                        <p class="error-block">
                            {{ $errors->first('road_trip') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('last_inventory_date', 'Last Inventory Date', ['class' => 'control-label']) !!}
                    {!! Form::text('last_inventory_date', old('last_inventory_date'), ['class' => 'form-control datepicker','disabled', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('last_inventory_date'))
                        <p class="error-block">
                            {{ $errors->first('last_inventory_date') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('last_start_time', 'Last Start Time', ['class' => 'control-label']) !!}
                    {!! Form::text('last_start_time', old('last_start_time'), ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('last_start_time'))
                        <p class="error-block">
                            {{ $errors->first('last_start_time') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('last_crew_count', 'Last Crew Count', ['class' => 'control-label']) !!}
                    {!! Form::text('last_crew_count', old('last_crew_count'), ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('last_crew_count'))
                        <p class="error-block">
                            {{ $errors->first('last_crew_count') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('last_count_length', 'Last Count Length', ['class' => 'control-label']) !!}
                    {!! Form::text('last_count_length', old('last_count_length'), ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('last_count_length'))
                        <p class="error-block">
                            {{ $errors->first('last_count_length') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('last_count_production', 'Last Production Count', ['class' => 'control-label']) !!}
                    {!! Form::text('last_count_production', old('last_count_production'), ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has(' last_count_production'))
                        <p class="error-block">
                            {{ $errors->first(' last_count_production') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('last_inventory_value', 'Last Inventory Value', ['class' => 'control-label']) !!}
                    {!! Form::text('last_inventory_value', old('last_inventory_value'), ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('last_inventory_value'))
                        <p class="error-block">
                            {{ $errors->first('last_inventory_value') }}
                        </p>
                    @endif
                </div>
            </div>
            </div>
            <div class="col-xs-3">
                <div class="col-xs-3 form-group text-xs-left">Area</div>
                <div class="col-xs-2 form-group text-left">Run</div>
                <div class="col-xs-7 form-group text-left">Store</div>
                <div class="col-xs-12 form-group mini-schedule"></div>
            </div>
            </div>
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('comments', 'Schedule Comments', ['class' => 'control-label']) !!}
                    {!! Form::textarea('comments', old('comments'), ['class' => 'form-control ','tabindex'=>14, 'placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('comments'))
                        <p class="error-block">
                            {{ $errors->first('comments') }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="blackout_dates_container">
                    <input type="hidden" value="1" id="blackout_counter">
<!--                <div class="col-xs-2" style="height:75px;">
                    
                    {!! Form::label('truck_dates', 'Truck Dates', ['class' => 'control-label']) !!}
                    {!! Form::text('truck_dates[]', old('truck_dates'), ['class' => 'form-control datepicker','disabled', 'autocomplete'=>'off','placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('truck_dates'))
                        <p class="error-block">
                            {{ $errors->first('truck_dates') }}
                        </p>
                    @endif
                </div>
                <div class="col-xs-1 add_more" style="height:75px;"><i class="fa fa-plus" aria-hidden="true"></i> Add More </div>-->
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
        $( "#crew_leader_name" ).autocomplete({
        source: <?php echo json_encode($emps)?>,
        select: function (event, ui) {
         $('#crew_leader_name').val(ui.item.label); // display the selected text
         $('#crew_leader').val(ui.item.value); // save selected id to input
         return false;
        }
       });
       $( "#store_name" ).autocomplete({
        source: <?php echo json_encode($stores)?>,
        select: function (event, ui) {
         $('#store_name').val(ui.item.label); // display the selected text
         $('#store_id').val(ui.item.value); // save selected id to input
         return false;
        }
       });
       $( "#road_trip" ).autocomplete({
        source: ['No','Start Road Trip','End Road Trip','Road Trip'],
        select: function (event, ui) {
         $('#road_trip').val(ui.item.value); // display the selected text
         //$('#store_id').val(ui.item.value); // save selected id to input
         return false;
        }
       });
       $( "#run_number" ).autocomplete({
        source: ['1','2','3','4','5','6','7','8','9'],
        select: function (event, ui) {
         $('#run_number').val(ui.item.value); // display the selected text
         //$('#store_id').val(ui.item.value); // save selected id to input
         return false;
        }
       });
        $('body').on('focus',".datepicker", function(){
            if( $(this).hasClass('hasDatepicker') === false )  {
                $(this).datepicker();
            }

        });
        $('.datepicker').datepicker({
            autoclose: true
        })
        
        $('.blackout_dates_container').on('click', '.remove_blackout', function(events){
            var blackout_counter = $(this).attr('blackout');
            $('.blackout_counter'+blackout_counter).remove();
        });
        $('.add_more').click(function(){
            var blackout_counter = $('#blackout_counter').val();
            blackout_counter++;
            var html ='<div style="height:75px;" class="col-xs-2 blackout_counter'+blackout_counter+'"><label for="truck_dates" class="control-label">Truck Dates</label>\n\
                        <input class="form-control datepicker" name="truck_dates[]" autocomplete="off" type="text"></div><div style="height:75px;" blackout="'+blackout_counter+'" class="col-xs-1 remove_blackout blackout_counter'+blackout_counter
                        +'"><i class="fa fa-trash" aria-hidden="true"></i></div>';
            $('#blackout_counter').val(blackout_counter);
            $(".blackout_dates_container").append(html);
           // alert('sdf');
        })
        
        $('[data-mask]').inputmask();
        
        $(document).on('change','#store_name',function(){
            var store_id = $("#store_id").val();
            if(store_id)
            {
                $.ajax({
               type:"GET",
               url:"get_event_feedback_data/"+store_id,
               success:function(res){
                    console.log(res);
                   //var d =  new Date(res.data.created_at);
                   if(res.data && res.data.date){$("#last_inventory_date").val(formatDateMysql(res.data.date));}else{$("#last_inventory_date").val('');}
                   if(res.data && res.data.start_time){$("#last_start_time").val(res.data.start_time);}else{$("#last_start_time").val('');}
                   if(res.data && res.data.crew_count){$("#last_crew_count").val(res.data.crew_count);}else{$("#last_crew_count").val('');}
                   if(res.data && res.data.last_count_length){$("#last_count_length").val(res.data.last_count_length);}else{$("#last_count_length").val('');}
                   if(res.data && res.data.last_count_production){$("#last_count_production").val(res.data.last_count_production);}else{$("#last_count_production").val('');}
                   if(res.data && res.data.last_inventory_value){$("#last_inventory_value").val(res.data.last_inventory_value);}else{$("#last_inventory_value").val('');}
                   if(res.data && res.data.truck_dates)
                   {
                        $.each(res.data.truck_dates,function(key,value){
                        //$("#"+ele+"_city").append('<option value="'+key+'">'+value+'</option>');
                        var html ='<div style="height:75px;" class="col-xs-2 blackout_counter'+blackout_counter+'"><label for="truck_dates" class="control-label">Truck Dates</label>\n\
                            <input class="form-control datepicker" value="'+formatDateMysql(value.truck_date)+'" name="truck_dates[]" disabled autocomplete="off" type="text"></div><div style="height:75px;" blackout="'+blackout_counter+'" class="col-xs-1 remove_blackout blackout_counter'+blackout_counter
                            +'"></div>';
                        $(".blackout_dates_container").append(html);
                    
                        });
                    }else{
                        $(".blackout_dates_container").html('');
                    }
               }
            });
                $.ajax({
                    type:"GET",
                    url:"/admin/stores/"+store_id,
                    success:function(res){
                        //console.log(res);
                        //if(res.data){$("#last_inventory_date").val(formatDateMysql(res.data.date));}
                        if(res.store.area_prime_responsibility)
                        {
                        $.each(res.store.area_prime_responsibility,function(key,value){
                            //console.log(value);
                            //console.log(key);
                            if(key=="id")
                            {
                            $("#areas option[value="+value+"]").attr('selected', 'selected');
                            $('#areas').select2();
                        }
                        });
                    }
                        $('#crew_count').val(res.store.min_auditors);
                        if(res.store.start_time)
                            $('#start_time').val(res.store.start_time);
                        if(res.store.count_stockroom=="Yes")
                            $('input:radio[name=count_backroom]')[0].checked = true;
                        else
                            $('input:radio[name=count_backroom]')[1].checked = true;
                        if(res.store.rx=="Yes")
                            $('input:radio[name=count_rx]')[0].checked = true;
                        else
                            $('input:radio[name=count_rx]')[1].checked = true;
//                        alert(res.store.qccall);
//                        alert(res.store.precall);
                        if(res.store.qccall=="Yes")
                            $('input:radio[name=qc]')[0].checked = true;
                        else
                            $('input:radio[name=qc]')[1].checked = true;
                        if(res.store.precall=="Yes")
                            $('input:radio[name=pic]')[0].checked = true;
                        else
                            $('input:radio[name=pic]')[1].checked = true;
                    }
                 });
            }
        });
        
        $(document).on('change','#areas,#date',function(){
            var date = $("#date").val();
            var areas = $("#areas").val();
            if(date && areas)
            {
                $(".mini-schedule").html('');
                $.ajax({
                    type:"POST",
                    url:"{{url('admin/get_mini_schedule')}}",
                    data:{_token: _token,date: date,areas:areas},
                    success:function(res){
                         
                         $.each(res.events,function(key,value){
                             console.log(res.events);
                            var html ='<div class="row">\n\
                                <div class="col-xs-3 form-group">'+value.area_title+'</div>\n\
                                <div class="col-xs-2 form-group">'+value.run_number+'</div>\n\
                                <div class="col-xs-7 form-group">'+value.storename+'</div>\n\
                                </div>';
                            $(".mini-schedule").append(html);
                        });
                    }
                });
            }
        });
        
        $('.timepicker').timepicker({
            showInputs: false
        })
   })
   function formatDateMysql(mysqldate) {
    let dateTimeParts= mysqldate.split(/[- :]/); // regular expression split that creates array with: year, month, day, hour, minutes, seconds values
    dateTimeParts[1]--; // monthIndex begins with 0 for January and ends with 11 for December so we need to decrement by one
    const monthNames = ["January", "February", "March", "April", "May", "June",
    "July", "August", "September", "October", "November", "December"
  ];
    //alert(dateTimeParts[0]);
    const d = new Date(...dateTimeParts); // our Date object
    var dd   = d.getDate();
    var mon = d.getMonth()+1;
    var hour = d.getHours();
    var minu = d.getMinutes();
    if(mon<10){mon='0'+mon}
    if(dd<10)  { dd='0'+dd }
    if(minu<10){ minu='0'+minu } 

    var amOrPm = (d.getHours() < 12) ? "AM" : "PM";
    var hour = (d.getHours() < 12) ? d.getHours() : d.getHours() - 12;
    //return monthNames[d.getUTCMonth()].toUpperCase()+" "+dd+", "+d.getUTCFullYear()+" "+hour+":"+minu +" "+amOrPm;
    return mon+"/"+dd+"/"+ (d.getFullYear());
  }
</script>
@stop