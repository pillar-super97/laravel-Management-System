@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')
@section('pageTitle', 'Event Calendar View')
@section('custom_css')
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" href="{{ url('css/fullcalendar.css') }}"/>
<link rel="stylesheet" href="{{ url('css/fullcalendar.print.css') }}"/>
<style>
    .tooltip .tooltip-inner{background-color: #0e4f87;max-width: 240px;}
    .tooltip.top .tooltip-arrow{border-top-color: #0e4f87;}
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
    padding: 0px 5px;
}
</style>
@endsection
@section('content')
<form method="POST" id="event_filters" name="event_filters" action="#">
<div class="panel panel-default">
    <div class="panel-heading">Filter Events</div>
    
    <div class="panel-body ">
        
            <div class="row">
               
                
                <div class="col-md-3 form-group">
                    <label for="division_id" class="control-label">Division</label>
                    <select id="division_id" class="form-control division_id select2" multiple="" name="division_id[]" >
                        <option value="">Select Division</option>
                        <?php foreach ($divisions as $key=>$division){?>
                        <option value="<?php echo $key;?>"><?php echo $division;?></option>
                        <?php }?>
                    </select>
                </div>
            
                <div class="col-md-3 form-group">
                    <label for="district_id" class="control-label">District</label>
                    <select id="district_id" class="form-control district_id select2" multiple="" name="district_id[]" >
                        <option value="">Select District</option>
                    </select>
                </div>
                 
                <div class="col-md-3 form-group">
                    <label for="store_id" class="control-label">Stores</label>
                    <select id="store_id" class="form-control select2" multiple="" name="store_id[]" >
                        <option value="">Select Store</option>
                        <?php foreach ($stores as $key=>$store){?>
                        <option value="<?php echo $key;?>"><?php echo $store;?></option>
                        <?php }?>
                    </select>
                </div>

            
                
                <div class="col-md-3 form-group" style="padding-top: 30px;">
                    <input type="checkbox" id="enable_blackout_filter"><label for="enable_blackout_filter">Click for Blackout Dates Filter </label>&nbsp;
                </div>
            </div>
    
    </div>
</div>
    <div class="panel panel-default blackout_filter_panel" style="display: none;">
        <div class="panel-heading">
            Blackout Dates Filter
        </div>

        <div class="panel-body ">
            <div class="row">
                
                <div class="col-md-3 form-group">
                    <label for="blackout_division_id" class="control-label">Division</label>
                    <select id="blackout_division_id" class="form-control blackout_division_id select2" multiple="" name="blackout_division_id[]" >
                        <option value="">Select Division</option>
                        <?php foreach ($divisions as $key=>$division){?>
                        <option value="<?php echo $key;?>"><?php echo $division;?></option>
                        <?php }?>
                    </select>
                </div>
                <div class="col-md-3 form-group">
                    <label for="blackout_district_id" class="control-label">District</label>
                    <select id="blackout_district_id" class="form-control blackout_district_id select2" multiple="" name="blackout_district_id[]" >
                        <option value="">Select Division</option>
                        <?php foreach ($districts as $key=>$district){?>
                        <option value="<?php echo $key;?>"><?php echo $district;?></option>
                        <?php }?>
                    </select>
                </div>
                <div class="col-md-3 form-group">
                    <label for="blackout_store_id" class="control-label">Stores</label>
                    <select id="blackout_store_id" class="form-control select2" multiple="" name="blackout_store_id[]" >
                        <option value="">Select Store</option>
                        <?php foreach ($stores as $key=>$store){?>
                        <option value="<?php echo $key;?>"><?php echo $store;?></option>
                        <?php }?>
                    </select>
                </div>
            </div>
        </div>
    </div>
</form>
<div class="panel panel-default">
    <div class="panel-body table-responsive">
<!--        <div class="container">-->
<div class="row"><div class="col-md-2"><button class="printBtn hidden-print">Print Calendar</button></div><div class="col-md-10 col-md-offset-1 success_msg_container text-center"></div></div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">Event List</div>
                        <div class="panel-body">
                            <div id='calendar'></div>
                        </div>
                    </div>
                </div>
            </div>
<!--        </div>-->


    </div>
</div>
@can('event_create')
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
<!--                        <div class="col-xs-4 form-group">
                            {!! Form::label('number', 'Event Number', ['class' => 'control-label required']) !!}
                            {!! Form::text('number', old('number'), ['class' => 'form-control required', 'placeholder' => '', 'required' => '']) !!}
                        </div>-->
                        <div class="col-xs-4 form-group{{ $errors->has('crew_leader') ? ' has-error' : '' }}">
                            <label for="crew_leader" class="control-label">Crew Leader</label>
                            <select id="crew_leader" class="form-control" name="crew_leader" >
                                <option value="">Select Crew Leader</option>
                                <?php foreach ($employees as $key=>$employee){?>
                                <option value="<?php echo $key;?>"><?php echo $employee;?></option>
                                <?php }?>
                            </select>

                          
                        </div>
                        <div class="col-xs-4 form-group{{ $errors->has('store_id') ? ' has-error' : '' }}">
                            <label for="store_id" class="control-label required">Store</label><br>
                            <select id="store_id1" class="form-control store_id1 required limitone select2" multiple=""  name="store_id" > 
                                <option value="">Select store</option>
                                <?php foreach ($stores as $key=>$store){?>
                                <option value="<?php echo $key;?>"><?php echo $store;?></option>
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
@endcan


@can('event_create')
<div class="modal fade" id="copyeventPopup" tabindex="-1" role="dialog" aria-labelledby="ratingReviewRate" aria-hidden="true">
    <div class="modal-dialog modal-dialog-custom" role="document">
        <div class="modal-content modal-content-custom">
            <div class="modal-body">
                <div class="row">		        
                    <div class="col-md-10"><h4 class="modal-title modal-title-custom" id="exampleModalLabel"><strong>Create New Event</strong></h4></div>
                    <div class="col-md-2">
                        <button type="button" class="close modalCloseBtn1" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    
                </div>
                <hr>
                <form method="POST" id="copy_save_event" name="copy_save_event" action="#">
                    <div class="row">
                        <div class="copy-event-response col-md-12 alert alert-success" style="display: none;"></div>
                        {{ csrf_field() }}
                        <input type="hidden" name="event_date" id="event_date" class="event_date">
                        <input type="hidden" name="store_id" id='store_id' class="store_id">
<!--                        <div class="col-xs-4 form-group">
                            {!! Form::label('number', 'Event Number', ['class' => 'control-label required']) !!}
                            {!! Form::text('number', old('number'), ['class' => 'form-control required', 'placeholder' => '', 'required' => '']) !!}
                        </div>-->
                        <div class="col-xs-4 form-group{{ $errors->has('crew_leader') ? ' has-error' : '' }}">
                            <label for="crew_leader" class="control-label">Crew Leader</label>
                            <select id="crew_leader" class="form-control" name="crew_leader" >
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
                    <select id="areas" class="form-control required" required="required" name="areas[]">
                        <option value="">Select Area</option>
                        <?php foreach ($areas as $key=>$area){?>
                        <option value="<?php echo $key;?>"><?php echo $area;?></option>
                        <?php }?>
                    </select>
                </div>
                <div class="col-xs-4" style="height:75px;">
                    {!! Form::label('event_date', 'Date', ['class' => 'control-label required']) !!}
                    {!! Form::text('event_date', old('event_date'), ['class' => 'form-control datepicker event_scheduled_on','required', 'autocomplete'=>'off','placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('event_date'))
                        <p class="error-block">
                            {{ $errors->first('event_date') }}
                        </p>
                    @endif
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
                        <select id="road_trip" class="form-control" name="road_trip" >
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
                        {!! Form::text('last_inventory_date', old('last_inventory_date'), ['class' => 'form-control last_inventory_date datepicker','disabled', 'placeholder' => '']) !!}

                    </div>
                    <div class="col-xs-4 form-group">
                        {!! Form::label('last_start_time', 'Last Start Time', ['class' => 'control-label']) !!}
                        {!! Form::text('last_start_time', old('last_start_time'), ['class' => 'form-control last_start_time','disabled', 'placeholder' => '']) !!}

                    </div>
                    <div class="col-xs-4 form-group">
                        {!! Form::label('last_crew_count', 'Last Crew Count', ['class' => 'control-label']) !!}
                        {!! Form::text('last_crew_count', old('last_crew_count'), ['class' => 'form-control last_crew_count','disabled', 'placeholder' => '']) !!}

                    </div>
                    <div class="col-xs-4 form-group">
                        {!! Form::label('last_count_length', 'Last Count Length', ['class' => 'control-label']) !!}
                        {!! Form::text('last_count_length', old('last_count_length'), ['class' => 'form-control last_count_length','disabled', 'placeholder' => '']) !!}

                    </div>
                    <div class="col-xs-4 form-group">
                        {!! Form::label('last_count_production', 'Last Production Count', ['class' => 'control-label']) !!}
                        {!! Form::text('last_count_production', old('last_count_production'), ['class' => 'form-control last_count_production','disabled', 'placeholder' => '']) !!}

                    </div>
                    <div class="col-xs-4 form-group">
                        {!! Form::label('last_inventory_value', 'Last Inventory Value', ['class' => 'control-label']) !!}
                        {!! Form::text('last_inventory_value', old('last_inventory_value'), ['class' => 'form-control last_inventory_value','disabled', 'placeholder' => '']) !!}

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
                        {!! Form::submit('Submit', ['class' => 'btn btn-success copy-event','id'=>'submit_event_button']) !!}
                        {!! Form::reset('Cancel', ['class' => 'btn btn-warning cancel-btn','onclick'=>'close_popup()','data-dismiss'=>'modal']) !!}
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endcan
<div class="modal fade" id="qcPopup" tabindex="-1" role="dialog" aria-labelledby="ratingReviewRate" aria-hidden="true">
    <div class="modal-dialog modal-dialog-custom" role="document">
        <div class="modal-content modal-content-custom">
            <div class="modal-body">
                <div class="row">		        
                    <div class="col-md-10"><h4 class="modal-title modal-title-custom" id="exampleModalLabel"><strong>Event QC</strong></h4></div>
                    <div class="col-md-2">
                        <button type="button" class="close modalCloseBtn1" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    
                </div>
                <hr>
                <form method="POST" id="event_qc" name="event_qc" action="#">
                    <div class="row">
                        <div class="qc-response col-md-12 alert alert-success" style="display: none;"></div>
                        {{ csrf_field() }}
                        <input type="hidden" name="event_id" class="event_id">
                        <div class="col-xs-4 form-group store_name"></div>
                        <div class="col-xs-4 form-group store_phone"></div>
                        <div class="col-xs-4 form-group store_manager"></div>
                    </div>
                    <div class="row">
                        <div class="col-xs-4 form-group event_date"></div>
                        <div class="col-xs-4 form-group start_time"></div>
                        <div class="col-xs-4 form-group qc_confirmed_with">
                            {!! Form::label('qc_confirmed_with', 'Confirmed With', ['class' => 'control-label']) !!}
                            <?php if(Gate::allows('event_qc')){?>
                                {!! Form::text('qc_confirmed_with', old('qc_confirmed_with'), ['class' => 'form-control confirmed_with']) !!}
                            <?php }else{?>
                                {!! Form::text('qc_confirmed_with', old('qc_confirmed_with'), ['class' => 'form-control confirmed_with','disabled']) !!}
                            <?php }?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-4 form-group">
                            <label class="control-label">On Time</label><br>
                            <label class="control-label">
                                <?php if(Gate::allows('event_qc')){?>
                                    <input type="radio" name="on_time" value="Yes" class="on_time">Yes
                                <?php }else{?>
                                    <input type="radio" name="on_time" value="Yes" disabled="">Yes
                                <?php }?>
                            </label>
                            <label class="control-label">
                                <?php if(Gate::allows('event_qc')){?>
                                <input type="radio" name="on_time" value="No" class="on_time">No
                                <?php }else{?>
                                <input type="radio" name="on_time" value="No" disabled="">No
                                <?php }?>
                            </label>
                        </div>
                        <div class="col-xs-4 form-group">
                            <label class="control-label">In Uniform</label><br>
                            <label class="control-label">
                                <?php if(Gate::allows('event_qc')){?>
                                <input type="radio" name="in_uniform" value="Yes" class="in_uniform">Yes
                                <?php }else{?>
                                <input type="radio" name="in_uniform" value="Yes" disabled="">Yes
                                <?php }?>
                            </label>
                            <label class="control-label">
                                <?php if(Gate::allows('event_qc')){?>
                                <input type="radio" name="in_uniform" value="No" class="in_uniform">No
                                <?php }else{?>
                                <input type="radio" name="in_uniform" value="No" disabled="">No
                                <?php }?>
                            </label>
                        </div>
                        <div class="col-xs-4 form-group">
                            <label class="control-label">Positive Experience</label><br>
                            <label class="control-label">
                                <?php if(Gate::allows('event_qc')){?>
                                <input type="radio" name="positive_exp" value="Yes" class="positive_exp">Yes
                                <?php }else{?>
                                <input type="radio" name="positive_exp" value="Yes" disabled="">Yes
                                <?php }?>
                            </label>
                            <label class="control-label">
                                <?php if(Gate::allows('event_qc')){?>
                                <input type="radio" name="positive_exp" value="No" class="positive_exp">No
                                <?php }else{?>
                                <input type="radio" name="positive_exp" value="No" disabled="">No
                                <?php }?>
                            </label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 form-group">
                            {!! Form::label('qc_comment', 'QC Comments', ['class' => 'control-label']) !!}
                            <?php if(Gate::allows('event_qc')){?>
                            {!! Form::textarea('qc_comment', old('qc_comment'), ['class' => 'form-control qc_comment', 'placeholder' => '']) !!}
                            <?php }else{?>
                            {!! Form::textarea('qc_comment', old('qc_comment'), ['class' => 'form-control qc_comment', 'disabled']) !!}
                            <?php }?>
                        </div>
                    </div>
                    <?php if(Gate::allows('event_qc')){?>
                    <div class="row">
                        <div class="col-md-12">
                            {!! Form::button('Submit', ['class' => 'btn btn-success save-event-qc']) !!}
                            {!! Form::button('Cancel', ['class' => 'btn btn-warning popup_cancel_btn']) !!}
                        </div>
                    </div>
                    <?php }?>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="precallPopup" tabindex="-1" role="dialog" aria-labelledby="ratingReviewRate" aria-hidden="true">
    <div class="modal-dialog modal-dialog-custom" role="document">
        <div class="modal-content modal-content-custom">
            <div class="modal-body">
                <div class="row">		        
                    <div class="col-md-10"><h4 class="modal-title modal-title-custom" id="exampleModalLabel"><strong>Event Pre Call</strong></h4></div>
                    <div class="col-md-2">
                        <button type="button" class="close modalCloseBtn1" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    
                </div>
                <hr>
                <form method="POST" id="event_precall" name="event_precall" action="#">
                    <div class="row">
                        <div class="precall-response col-md-12 alert alert-success" style="display: none;"></div>
                        {{ csrf_field() }}
                        <input type="hidden" name="event_id" class="event_id">
                        <div class="col-xs-4 form-group store_name"></div>
                        <div class="col-xs-4 form-group store_address"></div>
                        <div class="col-xs-4 form-group store_city"></div>
                    </div>
                    <div class="row">
                        <div class="col-xs-4 form-group store_phone"></div>
                        <div class="col-xs-4 form-group store_manager"></div>
                    </div>
                    <div class="row">
                        <div class="col-xs-4 form-group event_date"></div>
                        <div class="col-xs-4 form-group start_time"></div>
                        <div class="col-xs-4 form-group">
                            {!! Form::label('precall_manager', 'Manager', ['class' => 'control-label required']) !!}
                            <?php if(Gate::allows('event_precall')){?>
                                {!! Form::text('precall_manager', old('precall_manager'), ['class' => 'form-control required','required']) !!}
                            <?php }else{?>
                                {!! Form::text('precall_manager', old('precall_manager'), ['class' => 'form-control required','disabled']) !!}
                            <?php }?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 form-group event_comment">
                            
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 form-group">
                            {!! Form::label('precall_comments', 'Comments', ['class' => 'control-label']) !!}
                            <?php if(Gate::allows('event_precall')){?>
                                {!! Form::textarea('precall_comments', old('precall_comments'), ['class' => 'form-control']) !!}
                            <?php }else{?>
                                {!! Form::textarea('precall_comments', old('precall_comments'), ['class' => 'form-control','disabled']) !!}
                            <?php }?>
                        </div>
                    </div>
                    <?php if(Gate::allows('event_precall')){?>
                    <div class="row">
                        <div class="col-md-7">
                            {!! Form::button('Submit', ['class' => 'btn btn-success save-event-precall']) !!}
                            {!! Form::button('Cancel', ['class' => 'btn btn-warning popup_cancel_btn']) !!}
                        </div>
                        <div class="col-md-5 qcinfo" style="display: none;">
                            QC Completed On - <div class="qc_completed_on"></div>
                            By - <div class="qc_completed_by"></div>
                        </div>
                    </div>
                    <?php }?>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="copyeventSchedulePopup" tabindex="-1" role="dialog" aria-labelledby="ratingReviewRate" aria-hidden="true">
    <div class="modal-dialog modal-dialog-custom" role="document">
        <div class="modal-content modal-content-custom">
            <div class="modal-body">
                <div class="row">		        
                    <div class="col-md-10"><h4 class="modal-title modal-title-custom" id="exampleModalLabel"><strong>Copy Event Schedule</strong></h4></div>
                    <div class="col-md-2">
                        <button type="button" class="close modalCloseBtn1" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    
                </div>
                <hr>
                <form method="POST" id="save_event_schedule" name="save_event_schedule" action="#">
                    <div class="row">
                        <div class="copy-event-schedule-response col-md-12 alert alert-success" style="display: none;"></div>
                        {{ csrf_field() }}
                        <input type="hidden" name="event_id_for_schedulecopy" id="event_id_for_schedulecopy">
                        <div class="col-xs-12 form-group">
                            <label for="events" class="control-label required">Select Events you want to copy with schedules</label>
                            <select id="copy_scheduled_events" class="form-control required limitone select2" multiple="" required="required" name="events[]">
                                <option value="">Select Events</option>
                                <?php foreach ($pending_event as $key=>$event){?>
                                <option value="<?php echo $key;?>"><?php echo $event;?></option>
                                <?php }?>
                            </select>
                        </div>
                    </div>
            <div class="row">
                <div class="col-md-12">
                    {!! Form::submit('Submit', ['class' => 'btn btn-success copy-event-schedule','id'=>'submit_event_schedule_button']) !!}
                    {!! Form::button('Cancel', ['class' => 'btn btn-warning popup_cancel_btn']) !!}
<!--                    {!! Form::reset('Cancel', ['class' => 'btn btn-warning cancel-btn popup_cancel_btn','onclick'=>'','data-dismiss'=>'modal']) !!}-->
                </div>
            </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="uploadMdbZip" tabindex="-1" role="dialog" aria-labelledby="ratingReviewRate" aria-hidden="true">
    <div class="modal-dialog modal-dialog-custom" role="document">
        <div class="modal-content modal-content-custom">
            <div class="modal-body">
                <div class="row">		        
                    <div class="col-md-10"><h4 class="modal-title modal-title-custom" id="exampleModalLabel"><strong>Event Audit Data Upload</strong></h4></div>
                    <div class="col-md-2">
                        <button type="button" class="close modalCloseBtn1" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    
                </div>
                <hr>
                <form method="POST" id="mdb_upload_form" name="mdb_upload_form" enctype="multipart/form-data">
                    <div class="row">
                        <div class="mdb-response col-md-12" style="display: none;"></div>
                        {{ csrf_field() }}
                        <input type="hidden" name="event_id" class="event_id">
                        <div class="col-xs-3" style="height:75px;">
                            {!! Form::label('inventmdb', 'Upload .mdb file', ['class' => 'control-label required']) !!}
                            {!! Form::file('inventmdb', old('inventmdb'), ['class' => 'form-control required','required']) !!}
                            <p class="help-block"></p>
                            @if($errors->has('inventmdb'))
                                <p class="error-block">
                                    {{ $errors->first('inventmdb') }}
                                </p>
                            @endif
                        </div>
                    </div>
                    <?php if(Gate::allows('event_upload_mdb')){?>
                    <div class="row">
                        <div class="col-md-12">
                            {!! Form::button('Submit', ['class' => 'btn btn-success upload-event-mdb']) !!}
                            {!! Form::button('Cancel', ['class' => 'btn btn-warning popup_cancel_btn']) !!}
                        </div>
                    </div>
                    <?php }?>
                </form>
            </div>
        </div>
    </div>
</div>

@stop

@section('javascript') 
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment.min.js"></script>
<script src="{{ url('js/fullcalendar.js') }}"></script>
<script type="text/javascript">
  $('.printBtn').on('click', function (){
    window.print();
  });
</script>
<script>
var SITEURL = "{{url('/')}}";
function init_events()
{
    var store_id = $('#store_id').val();
        var area_id = $('#area_id').val();
        var date_between = $('.date_between').val();
        var association_id = $('#association_id').val();
        var client_id = [{{@$request->user()->client_id}}];
        var division_id = $('#division_id').val();
        var district_id = $('#district_id').val();
        var supervisor = $('#supervisor').val();
        var blackout_client_id = [{{@$request->user()->client_id}}];
        var blackout_division_id = $('#blackout_division_id').val();
        var blackout_district_id = $('#blackout_district_id').val();
        var blackout_store_id = $('#blackout_store_id').val();
        var events = {
              url: SITEURL + "/admin/fullcalendar",
              type: 'POST',
              data: {
                store_id: store_id,
                area_id:area_id,
                date_between:date_between,
                association_id:association_id,
                client_id:client_id,
                division_id:division_id,
                district_id:district_id,
                supervisor:supervisor,
                blackout_client_id:blackout_client_id,
                blackout_division_id:blackout_division_id,
                blackout_district_id:blackout_district_id,
                blackout_store_id:blackout_store_id
              }
        }
        $('#calendar').fullCalendar('removeEventSource', events);
        $('#calendar').fullCalendar('addEventSource', events);
}
$(document).ready(function () {

    init_events();
    
    $(".limitone").select2({
        maximumSelectionLength: 1
    });
    $("#enable_blackout_filter").on('change',function(){
        if($('#enable_blackout_filter').is(':checked'))
        {
            $('.blackout_filter_panel').slideDown(1000);
        }else{
            $('.blackout_filter_panel').slideUp(1000);
            $('#blackout_division_id').val([]).trigger('change');
            $("#blackout_district_id").val([]).trigger('change');
            $('#blackout_store_id').val([]).trigger('change');
        }
    })
    
    $('#association_id,#division_id,#district_id,#area_id,#store_id,.date_between,#supervisor,#blackout_division_id,#blackout_district_id,#blackout_store_id').change(function(){
        init_events();
      });


      $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

    var calendar = $('#calendar').fullCalendar({
          height :'auto',
          //contentHeight:'4000px',
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
          events: {
              url:SITEURL + "/admin/fullcalendar",
              type: 'POST',
              data: {
                client_id: [{{@$request->user()->client_id}}],
              }
              },
          displayEventTime: false,
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
		  eventDragStop: function(event, jsEvent, ui, view ){
            setTimeout(function(){
                $('.tooltip-inner').parents('.tooltip').remove();
            }, 100);
          },
          eventDrop: function ( event, delta, revertFunc, jsEvent, ui, view ) {
              var start = $.fullCalendar.formatDate(event.start, "YYYY-MM-DD");
              var startdisp = $.fullCalendar.formatDate(event.start, "MM-DD-YYYY");
              //console.log(event);
                //var warning = "Please confirm moving "+event.title+" to "+start+"?";
                var res = confirm("Please confirm moving "+event.title+" to "+startdisp+"?");
               // alert(res);
                if (res == true) {
                    $.ajax({
                        url: SITEURL + '/admin/fullcalendar/edit_event',
                        data: {'start':start,'id':event.id},
                        type: "POST",
                        success: function (response) {
                            $(".success_msg_container").html("<div class='success alert alert-success'>Event Rescheduled Successfully.</div>");
                            setInterval(function() { $(".success_msg_container").fadeOut(); }, 2000);
                        }
                    });
                }else {
                    revertFunc();
                    return false;
                }
            },

            eventMouseover: function(event, jsEvent, view) {
                console.log(event, this);
                var current =  $(this);
                    $.ajax({
                        type: "POST",
                        url: SITEURL + '/admin/events/getEventDetailsByID',
                        async: false,
                        data: {event_id: event.id, is_tooltip:true},
                        success: function (response) {
                            current.attr('data-title',response);
                            current.attr('data-toggle','tooltip');
                            current.attr('data-container',"body");
                            current.attr('data-html','true');
                            current.tooltip('show');
                            
                        }
                    });
},

eventMouseout: function(event, jsEvent, view) {
    $(this).tooltip('hide');
},



         

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
        $(document).on('click','.upload-event-mdb',function(){
            var inventmdb = $("#inventmdb").val();
            var event_id=$(".event_id").val();
            if(inventmdb=="")
            {
                $("#inventmdb").addClass('custom_error');
                return false;
                
            }
            $('.upload-event-mdb').addClass('disabled');
            $('.popup_cancel_btn').addClass('disabled');
            var postData = new FormData($("#mdb_upload_form")[0]);
                $.ajax({
                    type:"POST",
                    url:'/admin/events/uploadmdb',
                    data:postData,
                    cache       : false,
                    processData: false,
                    contentType: false,
                    success:function(res){
                        $('.mdb-response').html('<div class="alert alert-'+res.type+'">'+res.message+'</div>').show();
                       if(res.type=="success")
                       {
                            $('.upload-event-mdb').attr('disabled',true);
                            $('.upload-event-mdb').hide();
                            $('.cancel-btn').hide();
                            $('.event_upload_mdb_btn'+event_id).addClass('greyedout1');
                            //$('.event_precall_btn'+event_id).attr('precall_completed','1');
                            $(".event_id").val('');
                            
                            
                       }
                       $("#mdb_upload_form").trigger("reset");
                       setTimeout(function(){
                                $('#uploadMdbZip').modal('hide');
                                $('.mdb-response').html('').hide();
                            }, 15000);
                       $('.upload-event-mdb').removeClass('disabled');
                       $('.popup_cancel_btn').removeClass('disabled');
                    }
                });
           
        });
        function close_popup(){
            $("#save_event").trigger('reset');
            $("#edit_event_form").trigger('reset');
            $('#addeventPopup').modal('hide');
            $("#eventDetailPopup").modal('hide');
            $('.feedback-response').html('').hide();
            $("#save_event_schedule").trigger('reset');
            $('#copyeventPopup').modal('hide');
            $('#copyeventSchedulePopup').modal('hide');
            $('.copy-event-response').html('').hide();
            $('.copy-event-schedule-response').html('').hide();
            $("#copy_scheduled_events option:selected").remove();
            $("#copy_scheduled_events").val('');
        }
        $("#copy_save_event").validate({
                submitHandler: function(form) {
                //var data = $(form).serialize();
                //var start = $.fullCalendar.formatDate(start, "Y-MM-DD HH:mm:ss");
                //var end = $.fullCalendar.formatDate(end, "Y-MM-DD HH:mm:ss");
                //$('#event_date').val($('.event_scheduled_on').val()); 
                var event_data = $("#copy_save_event").serialize();
                 $.ajax({
                     url: "/admin/fullcalendar/add_calendar_event",
                     data: event_data,
                     type: "POST",
                     success: function (data) {
                        //console.log(data.title);
                        $('#calendar').fullCalendar( 'refetchEvents');
                        $('.copy-event-response').html('Event added successfully.').show();
                        setTimeout(function(){
                            $('.modalCloseBtn1').trigger('click');
                            //close_popup();
                        }, 3000);
                        //dataTable.draw();
                    }
                 });
            return false;    
            }
        });
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
        $("#save_event_schedule").validate({
                submitHandler: function(form) {
                var event_data = $("#save_event_schedule").serialize();
                 $.ajax({
                     url: "/admin/copy_event_schedule",
                     data: event_data,
                     type: "POST",
                     success: function (data) {
                        $('.copy-event-schedule-response').html('Event Schedule copied successfully.').show();
                        setTimeout(function(){
                            close_popup();
                            window.location.href = data.redirectto;
                        }, 3000);
                        $("#copy_scheduled_events option:selected").remove();
                        dataTable.draw();
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
        $('.date_between').daterangepicker({autoUpdateInput: false,minDate:new Date()}
        , function(start_date, end_date) {
            $('.date_between').val(start_date.format('MM/DD/YYYY')+' - '+end_date.format('MM/DD/YYYY'));
            //dataTable.draw();
        });
            
     
        //})
        $('.datepicker').datepicker({
            autoclose: true
        })
        $(document).on('change','#store_id',function(){
            var store_id = $("#store_id").val();
            if(store_id)
            {
                $.ajax({
               type:"GET",
               url:"events/get_event_feedback_data/"+store_id,
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
                        console.log(res.store.count_stockroom);
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
        });
        $('.timepicker').timepicker({
            showInputs: false
        })
        $("body").on('hide.bs.modal', function() { 
             $("#event_qc").trigger("reset");
            $("#event_precall").trigger("reset");
        });
        $(document).on('click','.popup_cancel_btn',function() {
            $('.modalCloseBtn1').trigger('click');
            $("#event_qc").trigger("reset");
            $("#event_precall").trigger("reset");
        });
        $(document).on('click','#event_feedback_call_btn,.event_qc_call_btn,.event_precall_btn,.event_upload_mdb_btn',function() {
            var eventID = $(this).attr('data-id');
            $(".event_id").val(eventID);
        });
        $(document).on('click','.event_qc_call_btn,.event_precall_btn',function() {
            var eventID = $(this).attr('data-id');
            $(".event_id").val(eventID);
            var precall_completed = parseInt($(this).attr('precall_completed'));
            var qc_completed = parseInt($(this).attr('qc_completed'));
            //alert(qc_completed);
            if(qc_completed)
            {
                $('.save-event-qc').attr('disabled',true);
                $('.save-event-qc').hide();
                $('.cancel-btn').hide();
            }else{
                $('.save-event-qc').attr('disabled',false);
                $('.save-event-qc').show();
                $('.cancel-btn').show();
            }
            if(precall_completed)
            {
                $('.save-event-precall').attr('disabled',true);
                $('.save-event-precall').hide();
                $('.cancel-btn').hide();
            }else{
                $('.save-event-precall').attr('disabled',false);
                $('.save-event-precall').show();
                $('.cancel-btn').show();
            }
            $.ajax({
                    type:"GET",
                    url:'/admin/events/'+eventID,
                    success:function(res){
                        $('#qc_comment').val(res.event.qc_comment);
                        $('#precall_manager').val(res.event.precall_manager);
                        $('#precall_comments').val(res.event.precall_comments);
                        $("input[name='on_time'][value='"+res.event.on_time+"']").prop('checked', true);
                        $("input[name='in_uniform'][value='"+res.event.in_uniform+"']").prop('checked', true);
                        $("input[name='positive_exp'][value='"+res.event.positive_exp+"']").prop('checked', true);
                        $('.store_name').html('<label>Store Name</label><br>'+res.event.store.name);
                        $('.store_address').html('<label>Store Address</label><br>'+res.event.store.address);
                        $('.store_city').html('<label>Store City/State</label><br>'+res.city);
                        if(res.event.comments)
                            $('.event_comment').html('<label>Event Comment</label><br>'+res.event.comments);
                        else
                            $('.event_comment').html('<label>Event Comment</label><br>');
                        if(res.event.qc_completed_on){
                            $('.qcinfo').show();
                            $('.qc_completed_on').html(formatDateMysql(res.event.qc_completed_on));
                            $('.qc_completed_by').html(res.event.qc_by.name);
                        }
                        $('.store_phone').html('<label>Store Phone</label><br>'+res.event.store.phone);
                        $('.store_manager').html('<label>Store Manager</label><br>'+res.event.store.manager_id);
                        $('.start_time').html('<label>Start Time</label><br>'+res.event.start_time);
                        $('.confirmed_with').val(res.event.qc_confirmed_with);
                        $('.event_date').html('<label>Event Date</label><br>'+formatDateMysql(res.event.date));
                    }
                });
            
        });

        $(document).on('click','#event_copy_btn',function() {
            var eventID = $(this).attr('data-id');
            $.ajax({
                    type: "GET",
                    url: '/admin/events/'+eventID,
                    //data: "event_id=" + event.id,
                    success: function (data) {
                        //console.log(data.event.areas[0].area_id);
                        $('#number').val(data.event.number);
                        $('#crew_leader').val(data.event.crew_leader);
                        $('#start_time').val(data.event.start_time);
                        $('#run_number').val(data.event.run_number);
                        $('.store_id').val(data.event.store_id);
                        $('#areas').val(data.event.areas[0].area_id);
                        $('#date').val(formatDateMysql(data.event.date));
                        $('.event_date').val(formatDateMysql(data.event.date));
                        $('#crew_count').val(data.event.crew_count);
                        $('input[name="overnight"][value="' + data.event.overnight + '"]').prop('checked', true);
                        $('input[name="pic"][value="' + data.event.pic + '"]').prop('checked', true);
                        $('input[name="qc"][value="' + data.event.qc + '"]').prop('checked', true);
                        $('#road_trip').val(data.event.road_trip);
                        $('#comments').val(data.event.comments);
                        $('.last_inventory_date').val(data.historical_data.date);
                        $('.last_start_time').val(data.historical_data.start_time);
                        $('.last_crew_count').val(data.his_data.last_crew_count);
                        $('.last_count_length').val(data.his_data.last_count_length);
                        $('.last_count_production').val(data.his_data.last_production_count);
                        $('.last_inventory_value').val(data.his_data.last_inventory_value);
                        //$('.event_detail_container').html(response);
                        $("#copyeventPopup").modal();
                    }
                });
        });
        $(document).on('click','#event_schedule_copy_btn',function() {
            var eventID = $(this).attr('data-id');
            $('#event_id_for_schedulecopy').val(eventID);
            $("#copyeventSchedulePopup").modal();
        });

        $(document).on('click','.save-event-qc',function(){
            var qc_comment = $("#qc_comment").val();
            var qc_confirmed_with = $("#qc_confirmed_with").val();
            if(qc_confirmed_with=="")
            {
                $("#qc_confirmed_with").addClass('custom_error');
                return false;
            }
            if($("input[name='on_time']:checked").val()==undefined)
            {
                $(".on_time").parent().addClass('custom_error');
                return false;
            }
            if($("input[name='in_uniform']:checked").val()==undefined)
            {
                $(".in_uniform").parent().addClass('custom_error');
                return false;
            }
            if($("input[name='positive_exp']:checked").val()==undefined)
            {
                $(".positive_exp").parent().addClass('custom_error');
                return false;
            }
//            alert($("input[name='positive_exp']:checked").val());
//            return false;
            var event_id=$(".event_id").val();
            var data = $("#event_qc").serialize();
            $('.save-event-qc').addClass('disabled');
            $('.popup_cancel_btn').addClass('disabled');
                $.ajax({
                    type:"POST",
                    url:'/admin/events-qc',
                    data:data,
                    success:function(res){
                        $('.qc-response').html(res.message).show();
                        $('.save-event-qc').attr('disabled',true);
                        $('.save-event-qc').hide();
                        $('.cancel-btn').hide();
                        $('.event_qc_call_btn'+event_id).addClass('greyedout1');
                        $('.event_qc_call_btn'+event_id).attr('qc_completed','1');
                        $("#event_qc").trigger("reset");
                        $(".event_id").val('');
                        setTimeout(function(){
                            $('#qcPopup').modal('hide');
                            $('.qc-response').html('').hide();
                        }, 3000);
                        $('.save-event-qc').removeClass('disabled');
                        $('.popup_cancel_btn').removeClass('disabled');
                    }
                });
           
        });
        $(document).on('click','.save-event-precall',function(){
            var precall_manager = $("#precall_manager").val();
            var precall_comments = $("#precall_comments").val();
            var event_id=$(".event_id").val();
            if(precall_manager=="")
            {
                $("#precall_manager").addClass('custom_error');
                return false;
                
            }
//            if(precall_comments=="")
//            {
//                $("#precall_comments").addClass('custom_error');
//                return false;
//            }   
            $('.save-event-precall').addClass('disabled');
            $('.popup_cancel_btn').addClass('disabled');
            var data = $("#event_precall").serialize();
                $.ajax({
                    type:"POST",
                    url:'/admin/events-precall',
                    data:data,
                    success:function(res){
                        $('.precall-response').html(res.message).show();
                        $('.save-event-precall').attr('disabled',true);
                        $('.save-event-precall').hide();
                        $('.cancel-btn').hide();
                        $('.event_precall_btn'+event_id).addClass('greyedout1');
                        $('.event_precall_btn'+event_id).attr('precall_completed','1');
                        $("#event_precall").trigger("reset");
                        $(".event_id").val('');
                        setTimeout(function(){
                            $('#precallPopup').modal('hide');
                            $('.precall-response').html('').hide();
                        }, 3000);
                        $('.save-event-precall').removeClass('disabled');
                        $('.popup_cancel_btn').removeClass('disabled');
                    }
                });
           
        });
        $('body').on('focus',".datepicker", function(){
            if( $(this).hasClass('hasDatepicker') === false )  {
                $(this).datepicker({
                    autoclose: true,
                    format:'mm/dd/yyyy'
                });
            }
        });
        $('.datepicker').datepicker({
            autoclose: true,
            format:'mm/dd/yyyy'
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
