@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')
@section('pageTitle', 'Store Events Calendar View')
@section('custom_css')
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" href="{{ url('css/fullcalendar.css') }}"/>
@endsection
@section('content')
<?php //echo '<pre>';print_r($calendar->data());die;?>
<div class="panel panel-default">
    <div class="panel-heading">Events</div>
    <div class="panel-body table-responsive">
       
            <div class="row"><div class="col-md-10 col-md-offset-1 success_msg_container text-center"></div></div>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">{{$store->name}} Events</div>
                        <div class="panel-body">
                            <div id='calendar'></div>
                        </div>
                    </div>
                </div>
            </div>
        


    </div>
</div>

<div class="modal fade" id="addeventPopup" tabindex="-1" role="dialog" aria-labelledby="ratingReviewRate" aria-hidden="true">
    <div class="modal-dialog modal-dialog-custom" role="document">
        <div class="modal-content modal-content-custom">
            <div class="modal-body">
                <div class="row">		        
                    <div class="col-md-10"><h4 class="modal-title modal-title-custom" id="exampleModalLabel"><strong>Create New Event</strong></h4></div>
                    <div class="col-md-2">
                        <button type="button" class="close modalCloseBtn" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    
                </div>
                <hr>
                <form method="POST" id="save_event" name="save_event" action="#">
                    <div class="row">
                        <div class="feedback-response col-md-12 alert alert-success" style="display: none;"></div>
                        {{ csrf_field() }}
                        <input type="hidden" name="event_date" id="event_date">
                        <input type="hidden" name="store_id" id="store_id" value="{{$store->id}}">
<!--                        <div class="col-xs-4 form-group">
                            {!! Form::label('number', 'Event Number', ['class' => 'control-label required']) !!}
                            {!! Form::text('number', old('number'), ['class' => 'form-control required', 'placeholder' => '', 'required' => '']) !!}
                        </div>-->
                        <div class="col-xs-4 form-group{{ $errors->has('crew_leader') ? ' has-error' : '' }}">
                            <label for="crew_leader" class="control-label">Crew Leader</label>
                            <select id="crew_leader" class="form-control required" name="crew_leader" >
                                <option value="">Select Crew Leader</option>
                                <?php foreach ($employees as $key=>$employee){?>
                                <option value="<?php echo $key;?>"><?php echo $employee;?></option>
                                <?php }?>
                            </select>
                        </div>
                
                <div class="col-xs-4 form-group">
                    {!! Form::label('start_time', 'Start Time', ['class' => 'control-label required']) !!}
                    {!! Form::text('start_time', old('start_time'), ['class' => 'form-control timepicker  required','required', 'placeholder' => '']) !!}
                  
                </div>
                <div class="col-xs-4 form-group">
                    {!! Form::label('run_number', 'Run Number', ['class' => 'control-label required']) !!}
                    <select id="run_number" class="form-control required" name="run_number">
                        <?php for($i=1;$i<=9;$i++){?>
                        <option value="<?php echo $i;?>"><?php echo $i;?></option>
                        <?php }?>
                    </select>
                </div>
                <div class="col-xs-4 form-group">
                    <label for="areas" class="control-label required">Area</label>
                    <select id="areas" class="form-control required select2" multiple="" required="required" name="areas[]">
                        <option value="">Select Area</option>
                        <?php foreach ($areas as $key=>$area){?>
                        <option value="<?php echo $key;?>"><?php echo $area;?></option>
                        <?php }?>
                    </select>
                </div>
            </div>
            <div class="row">
                

                <div class="col-xs-4 form-group">
                    {!! Form::label('crew_count', 'Crew Count', ['class' => 'control-label']) !!}
                    {!! Form::text('crew_count', old('crew_count'), ['class' => 'form-control required', 'placeholder' => '']) !!}
                </div>

                <div class="col-xs-4 form-group">
                    <label class="control-label">Overnight</label><br>
                    <label class="control-label">                   
                        {!! Form::radio('overnight','Yes',['class' => 'form-control']) !!} Yes
                    </label>
                    <label class="control-label">                   
                        {!! Form::radio('overnight','No',['class' => 'form-control']) !!} No
                    </label>
                </div>
                <div class="col-xs-4 form-group">
                    <label class="control-label">PIC</label><br>
                    <label class="control-label">                   
                        {!! Form::radio('pic','Yes',['class' => 'form-control']) !!} Yes
                    </label>
                    <label class="control-label">                   
                        {!! Form::radio('pic','No',['class' => 'form-control']) !!} No
                    </label>
                </div>
            </div>
                <div class="row">    
                    

                    <div class="col-xs-4 form-group">
                        <label class="control-label">QC</label><br>
                        <label class="control-label">                   
                            {!! Form::radio('qc','Yes',['class' => 'form-control']) !!} Yes
                        </label>
                        <label class="control-label">                   
                            {!! Form::radio('qc','No',['class' => 'form-control']) !!} No
                        </label>

                    </div>
                    <div class="col-xs-3 form-group">
                        <label class="control-label">Count RX</label><br>
                        <label class="control-label">                   
                            {!! Form::radio('count_rx','Yes',['class' => 'form-control']) !!} Yes
                        </label>
                        <label class="control-label">                   
                            {!! Form::radio('count_rx','No',['class' => 'form-control']) !!} No
                        </label>
                    </div>
                    <div class="col-xs-3 form-group">
                        <label class="control-label">Count Backroom</label><br>
                        <label class="control-label">                   
                            {!! Form::radio('count_backroom','Yes',['class' => 'form-control']) !!} Yes
                        </label>
                        <label class="control-label">                   
                            {!! Form::radio('count_backroom','No',['class' => 'form-control']) !!} No
                        </label>
                    </div>
                    <div class="col-xs-4 form-group{{ $errors->has('road_trip') ? ' has-error' : '' }}">
                        <label for="road_trip" class="control-label">Road Trip</label>
                        <select id="road_trip" class="form-control client_id" name="road_trip" >
                            <option value="">Select Road Trip</option>
                            <option value="Start Road Trip">Start Road Trip</option>
                            <option value="End Road Trip">End Road Trip</option>
                            <option value="Road Trip">Road Trip</option>
                            <option value="No" selected="selected">No</option>
                        </select>

                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-4 form-group">
                        {!! Form::label('last_inventory_date', 'Last Inventory Date', ['class' => 'control-label']) !!}
                        {!! Form::text('last_inventory_date', old('last_inventory_date'), ['class' => 'form-control datepicker','disabled', 'placeholder' => '']) !!}

                    </div>
                    <div class="col-xs-4 form-group">
                        {!! Form::label('last_start_time', 'Last Start Time', ['class' => 'control-label']) !!}
                        {!! Form::text('last_start_time', old('last_start_time'), ['class' => 'form-control','disabled', 'placeholder' => '']) !!}

                    </div>
                    <div class="col-xs-4 form-group">
                        {!! Form::label('last_crew_count', 'Last Crew Count', ['class' => 'control-label']) !!}
                        {!! Form::text('last_crew_count', old('last_crew_count'), ['class' => 'form-control','disabled', 'placeholder' => '']) !!}

                    </div>
                    <div class="col-xs-4 form-group">
                        {!! Form::label('last_count_length', 'Last Count Length', ['class' => 'control-label']) !!}
                        {!! Form::text('last_count_length', old('last_count_length'), ['class' => 'form-control','disabled', 'placeholder' => '']) !!}

                    </div>
                    <div class="col-xs-4 form-group">
                        {!! Form::label('last_count_production', 'Last Production Count', ['class' => 'control-label']) !!}
                        {!! Form::text('last_count_production', old('last_count_production'), ['class' => 'form-control','disabled', 'placeholder' => '']) !!}

                    </div>
                    <div class="col-xs-4 form-group">
                        {!! Form::label('last_inventory_value', 'Last Inventory Value', ['class' => 'control-label']) !!}
                        {!! Form::text('last_inventory_value', old('last_inventory_value'), ['class' => 'form-control','disabled', 'placeholder' => '']) !!}

                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 form-group">
                        {!! Form::label('comments', 'Schedule Comments', ['class' => 'control-label']) !!}
                        {!! Form::textarea('comments', old('comments'), ['class' => 'form-control ', 'placeholder' => '']) !!}
                        <p class="help-block"></p>
                        @if($errors->has('comments'))
                            <p class="error-block">
                                {{ $errors->first('comments') }}
                            </p>
                        @endif
                    </div>
                </div>
                <div class="row blackout_dates_container">
                        <input type="hidden" value="1" id="blackout_counter">
                </div>
                <div class="row">
                    <div class="col-md-12">
                        {!! Form::submit('Submit', ['class' => 'btn btn-success save-event-feedback','id'=>'submit_event_button']) !!}
                        {!! Form::reset('Cancel', ['class' => 'btn btn-warning cancel-btn','onclick'=>'close_popup()','data-dismiss'=>'modal']) !!}
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="eventDetailPopup" tabindex="-1" role="dialog" aria-labelledby="ratingReviewRate" aria-hidden="true">
    <div class="modal-dialog modal-dialog-custom" role="document">
        <div class="modal-content modal-content-custom">
            <div class="modal-body">
                <div class="row">		        
                    <div class="col-md-10"><h4 class="modal-title modal-title-custom" id="exampleModalLabel"><strong>Event Details</strong></h4></div>
                    <div class="col-md-2">
                        <button type="button" class="close modalCloseBtn" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    
                </div>
              
                <div class="row event_detail_container">
                    
                </div>
                    
                
               
                
            </div>
        </div>
    </div>
</div>
@stop

@section('javascript') 
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment.min.js"></script>
<script src="{{ url('js/fullcalendar.js') }}"></script>

<script>
  $(document).ready(function () {
         
        var SITEURL = "{{url('/')}}";
        console.log(SITEURL);
        $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
 
        var calendar = $('#calendar').fullCalendar({
            header: {
                left: 'prev,next,prevYear,nextYear',
                center: 'title',
                right: 'listYear,month,agendaWeek,agendaDay'
            },
            buttonText:{
                prev: '',
                next: '',
                prevYear: '',
                nextYear: '',
                year: 'Year',
                today: 'today',
                month: 'Month',
                week: 'Week',
                day: 'Day'
            },
            editable: true,
            events: SITEURL + "/admin/events/showstoreevents/{{$store->id}}",
            displayEventTime: true,
            editable: true,
            eventOrder: "area_number,run_number,start_time",
            eventRender: function (event, element, view) {
                if (event.allDay === 'true') {
                    event.allDay = true;
                } else {
                    event.allDay = false;
                }
            },
            selectable: true,
            selectHelper: true,
            select: function (start, end, allDay) {
                $("#addeventPopup").modal();
                $("#event_date").val(start.format())
            },
             
            eventDrop: function (event) {
                        var start = $.fullCalendar.formatDate(event.start, "YYYY-MM-DD");
                        $.ajax({
                            url: SITEURL + '/admin/fullcalendar/edit_event',
                            data: {'start':start,'id':event.id},
                            type: "POST",
                            success: function (response) {
                                $(".success_msg_container").html("<div class='success alert alert-success'>Event Rescheduled Successfully.</div>");
                                setInterval(function() { $(".success_msg_container").fadeOut(); }, 2000);
                            }
                        });
                    },
            eventClick: function (event) {
                $.ajax({
                    type: "POST",
                    url: SITEURL + '/admin/events/getEventDetailsByID',
                    data: "event_id=" + event.id,
                    success: function (response) {
                        $('.event_detail_container').html(response);
                        $("#eventDetailPopup").modal();
                        $('.datepicker').datepicker({
                            autoclose: true
                        });
                        $('.timepicker').timepicker({
                            showInputs: false
                        });
                    }
                });
                
            }
 
        });
  });
 
  function displayMessage(message) {
    $(".response").html("<div class='success'>"+message+"</div>");
    setInterval(function() { $(".success").fadeOut(); }, 1000);
  }
</script>

<script type="text/javascript" src="https://ajax.microsoft.com/ajax/jquery.validate/1.7/jquery.validate.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        function close_popup(){
            $("#save_event").trigger('reset');
            $('#addeventPopup').modal('hide');
            $("#edit_event_form").trigger('reset');
            $("#eventDetailPopup").modal('hide');
            $('.feedback-response').html('').hide();
        }
            $("#save_event").validate({
                submitHandler: function(form) {
                    //var data = $(form).serialize();
                    //var start = $.fullCalendar.formatDate(start, "Y-MM-DD HH:mm:ss");
                    //var end = $.fullCalendar.formatDate(end, "Y-MM-DD HH:mm:ss");
                    var event_data = $("#save_event").serialize();
                     $.ajax({
                         url: "/admin/fullcalendar/add_calendar_event",
                         data: event_data,
                         type: "POST",
                         success: function (data) {
                            //console.log(data.title);
                            $('#calendar').fullCalendar( 'refetchEvents');
                            $('.feedback-response').html('Event added successfully.').show();
                            setTimeout(function(){
                                close_popup();
                            }, 3000);
                        }
                     });
                return false;    
                }
            });
            $(document).on('click','#submit_event_edit_button',function(){
       
                
                    //var data = $(form).serialize();
                    //var start = $.fullCalendar.formatDate(start, "Y-MM-DD HH:mm:ss");
                    //var end = $.fullCalendar.formatDate(end, "Y-MM-DD HH:mm:ss");
                    var id = $(".edit_event_id_form").val();
                    var event_data = $("#edit_event_form").serialize();
                     $.ajax({
                         url: "/admin/events/updateEventDetailsByID",
                         data: event_data,
                         type: "POST",
                         success: function (data) {
                            //console.log(data.title);
                            $('#calendar').fullCalendar( 'refetchEvents');
                            $('.feedback-response').html('Event updated successfully.').show();
                            setTimeout(function(){
                                close_popup();
                            }, 3000);
                        }
                     });
                  
                
            });
            
     
        //})
        $('.datepicker').datepicker({
            autoclose: true
        })
        
            var store_id = {{$store->id}};
            if(store_id)
            {
                $.ajax({
               type:"GET",
               url:"/admin/events/get_event_feedback_data/"+store_id,
               success:function(res){
                    //console.log(res);
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
                        var html ='<div style="height:75px;" class="col-xs-3 blackout_counter'+blackout_counter+'"><label for="truck_dates" class="control-label">Truck Dates</label>\n\
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
                        //console.log(res.store.count_stockroom);
                        //if(res.data){$("#last_inventory_date").val(formatDateMysql(res.data.date));}
                        if(res.store.count_stockroom=="Yes")
                            $('input:radio[name=count_backroom]')[0].checked = true;
                        else
                            $('input:radio[name=count_backroom]')[1].checked = true;
                        if(res.store.rx=="Yes")
                            $('input:radio[name=count_rx]')[0].checked = true;
                        else
                            $('input:radio[name=count_rx]')[1].checked = true;
                    }
                 });
            }
        
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
@endsection