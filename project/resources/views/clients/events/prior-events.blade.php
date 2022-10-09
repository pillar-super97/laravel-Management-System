@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')
@section('pageTitle', 'Event List')
@section('content')
    <h3 class="page-title">Prior Events</h3>
    @can('event_create')
    <p>
        <a href="{{ route('admin.events.create') }}" class="btn btn-success">@lang('global.app_add_new')</a>
        
    </p>
    @endcan
    
    <p>
        
    <a href="{{ route('admin.events.index') }}?show_calender=1" title="Switch to Calender View" class="btn btn-success"><i class="fa fa-calendar" aria-hidden="true"></i></a>
    </p>
    <div class="panel panel-default">
        <div class="panel-heading">
            Filter Events
        </div>

        <div class="panel-body">
            <div class="row">
               
                <div class="col-xs-3 form-group {{$request->user()->client_id ? 'hidden' : ''}}">
                    <label for="client_id" class="control-label">Clients</label>
                   
                    <select id="client_id" class="form-control client_id select2" multiple="" name="client_id[]" >
                        <option value="">Select Client</option>
                        <?php 
                         if(@$request->user()->client_id) $request->session()->put('client_id', [$request->user()->client_id] );
                        foreach ($clients as $key=>$client){?>
                        <option value="<?php echo $key;?>" <?php if(($request->session()->get('client_id')) && in_array($key,$request->session()->get('client_id'))){echo 'selected="selected"';} ?>><?php echo $client;?></option>
                        <?php }?>
                    </select>
                </div> 
                <div class="col-xs-3 form-group">
                    <label for="division_id" class="control-label">Division</label>
                    <select id="division_id" class="form-control division_id select2" multiple="" name="division_id[]" >
                        <option value="">Select Division</option>
                        <?php foreach ($divisions as $key=>$division){?>
                        <option value="<?php echo $key;?>" <?php if(($request->session()->get('division_id')) && in_array($key,$request->session()->get('division_id'))){echo 'selected="selected"';} ?>><?php echo $division;?></option>
                        <?php }?>
                    </select>
                </div>
                <div class="col-xs-3 form-group">
                    <label for="district_id" class="control-label">District</label>
                    <select id="district_id" class="form-control district_id select2" multiple="" name="district_id[]" >
                        <option value="" <?php if(($request->session()->get('district_id')) && in_array($key,$request->session()->get('district_id'))){echo 'selected="selected"';} ?>>Select District</option>
                    </select>
                </div>
            
               
                <div class="col-xs-3 form-group">
                    <label for="store_id" class="control-label">Stores</label>
                    <select id="store_id" class="form-control select2" multiple="" name="store_id[]" >
                        <option value="">Select Store</option>
                        <?php foreach ($stores as $key=>$store){?>
                        <option value="<?php echo $key;?>" <?php if(($request->session()->get('store_id')) && in_array($key,$request->session()->get('store_id'))){echo 'selected="selected"';} ?>><?php echo $store;?></option>
                        <?php }?>
                    </select>
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('date_between', 'Date', ['class' => 'control-label required']) !!}
                    <?php if(($request->session()->get('date_between'))){?>
                        {!! Form::text('date_between', $request->session()->get('date_between'), ['class' => 'form-control date_between','autocomplete'=>'off', 'placeholder' => '']) !!}
                  <?php  }else{ ?>
                    {!! Form::text('date_between', old('date_between'), ['class' => 'form-control date_between','autocomplete'=>'off', 'placeholder' => '']) !!}
                    <?php }?>    
                </div>
               
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            List of All Prior Events
        </div>

        <div class="panel-body table-responsive">
            @if (Session::has('successmsg'))
                <div class="col-md-12 alert alert-success"> 
                    {{ Session::get('successmsg') }}
                </div>
            @endif
            <table id="event_list" class="table table-bordered table-striped datatable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th >Store</th>
                        <th>City/State</th>
                        <th>Last Inventory Value</th>
                        <th>Date</th>
                        <th>Start Time</th>
                        <th >Area Number</th>
                        <th>Run</th>
                        <th >Crew Lead</th>
                        <th>Status</th>
                        <th >Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>


<div class="modal fade" id="copyeventPopup" tabindex="-1" role="dialog" aria-labelledby="ratingReviewRate" aria-hidden="true">
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
                        <div class="copy-event-response col-md-12 alert alert-success" style="display: none;"></div>
                        {{ csrf_field() }}
                        <input type="hidden" name="event_date" id="event_date" class="event_date">
                        <input type="hidden" name="store_id" id='store_id' class="store_id">

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
                    {!! Form::label('date', 'Date', ['class' => 'control-label required']) !!}
                    {!! Form::text('date', old('date'), ['class' => 'form-control datepicker event_scheduled_on','required', 'autocomplete'=>'off','placeholder' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('date'))
                        <p class="error-block">
                            {{ $errors->first('date') }}
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

<div class="modal fade" id="qcPopup" tabindex="-1" role="dialog" aria-labelledby="ratingReviewRate" aria-hidden="true">
    <div class="modal-dialog modal-dialog-custom" role="document">
        <div class="modal-content modal-content-custom">
        <div class="modal-header">
        <div class="row">		        
                    <div class="col-md-10"><h4 class="modal-title modal-title-custom" id="exampleModalLabel"><strong>Event QC</strong></h4></div>
                    <div class="col-md-2">
                        <button type="button" class="close modalCloseBtn" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    
                </div>
                        </div>
            <div class="modal-body">
            <div class="preloader qc" style="position: absolute; opacity: 1;"></div>
                <form method="POST" id="event_qc" name="event_qc" action="#">
                    <div class="row">
                        <div class="qc-response col-md-12 alert alert-success" style="display: none;"></div>
                        {{ csrf_field() }}
                        <input type="hidden" name="event_id" class="event_id">
                        <div class="col-xs-4 form-group store_name"></div>
                        <div class="col-xs-4 form-group store_phone picstore_phone_other"></div>
                        <div class="col-xs-4 form-group store_manager picstore_manager_other"></div>
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
                            <label class="control-label">Professional Appearance</label><br>
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
                        <div class="col-xs-4 form-group">
                            <label class="control-label">Overall Accurate</label><br>
                            <label class="control-label">
                                <?php if(Gate::allows('event_qc')){?>
                                    <input type="radio" name="overall_accurate" value="Yes" class="overall_accurate">Yes
                                <?php }else{?>
                                    <input type="radio" name="overall_accurate" value="Yes" disabled="">Yes
                                <?php }?>
                            </label>
                            <label class="control-label">
                                <?php if(Gate::allows('event_qc')){?>
                                <input type="radio" name="overall_accurate" value="No" class="on_time">No
                                <?php }else{?>
                                <input type="radio" name="overall_accurate" value="No" disabled="">No
                                <?php }?>
                            </label>
                        </div>
                        <div class="col-xs-4 form-group qc_contact">
                            {!! Form::label('qc_contact', 'QC Contact', ['class' => 'control-label']) !!}
                            <?php if(Gate::allows('event_qc')){?>
                                {!! Form::text('qc_contact', old('qc_contact'), ['class' => 'form-control qc_contact']) !!}
                            <?php }else{?>
                                {!! Form::text('qc_contact', old('qc_contact'), ['class' => 'form-control qc_contact','disabled']) !!}
                            <?php }?>
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
                        <button type="button" class="close modalCloseBtn" data-dismiss="modal" aria-label="Close">
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
                    <div class="col-xs-4 form-group store_phone store_phone_other"></div>
                        <div class="col-xs-4 form-group store_manager store_manager_other"></div>
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
                    <div class ="row" >
                    <div class="col-xs-4 form-group update_at"></div>
                    <div class="col-xs-4 form-group update_at2"></div>

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
                        <button type="button" class="close modalCloseBtn" data-dismiss="modal" aria-label="Close">
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
                                
                            </select>
                        </div>
                    </div>
            <div class="row">
                <div class="col-md-12">
                    {!! Form::submit('Submit', ['class' => 'btn btn-success copy-event-schedule','id'=>'submit_event_schedule_button']) !!}
                    {!! Form::reset('Cancel', ['class' => 'btn btn-warning cancel-btn','onclick'=>'close_popup()','data-dismiss'=>'modal']) !!}
                </div>
            </div>
                </form>
            </div>
        </div>
    </div>
</div>

@stop

@section('javascript') 
<script type="text/javascript" src="https://ajax.microsoft.com/ajax/jquery.validate/1.7/jquery.validate.js"></script>
    <script type="text/javascript">
    $(document).ready(function(){
        $(".limitone").select2({
            maximumSelectionLength: 1
        });
        function close_popup(){
            $("#save_event").trigger('reset');
            $("#save_event_schedule").trigger('reset');
            $('#copyeventPopup').modal('hide');
            $('#copyeventSchedulePopup').modal('hide');
            $('.copy-event-response').html('').hide();
            $('.copy-event-schedule-response').html('').hide();
            $("#copy_scheduled_events option:selected").remove();
            $("#copy_scheduled_events").val('');
        }
        $("#save_event").validate({
                submitHandler: function(form) {
                //var data = $(form).serialize();
                //var start = $.fullCalendar.formatDate(start, "Y-MM-DD HH:mm:ss");
                //var end = $.fullCalendar.formatDate(end, "Y-MM-DD HH:mm:ss");
                $('#event_date').val($('.event_scheduled_on').val()); 
                var event_data = $("#save_event").serialize();
                 $.ajax({
                     url: "/admin/fullcalendar/add_calendar_event",
                     data: event_data,
                     type: "POST",
                     success: function (data) {
                        //console.log(data.title);
                        //$('#calendar').fullCalendar( 'refetchEvents');
                        $('.copy-event-response').html('Event added successfully.').show();
                        setTimeout(function(){
                            close_popup();
                        }, 3000);
                        dataTable.draw();
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
        $('.date_between').daterangepicker({autoUpdateInput: false,maxDate:new Date()}
        , function(start_date, end_date) {
            $('.date_between').val(start_date.format('MM/DD/YYYY')+' - '+end_date.format('MM/DD/YYYY'));
            dataTable.draw();
        });
        var dataTable = $('#event_list').DataTable({
            "aLengthMenu": [[25, 50, 75,100,500,1000,-1], [25,50,75,100,500,1000,"All"]],
            "iDisplayLength": 25,
            'destroy': true,
            "autoWidth": false,
            oLanguage: {
                sProcessing: "<img src='../uploads/images/ajax-loader.gif'>"
            },
            'processing': true,
            'serverSide': true,
            'serverMethod': 'post',
            "aaSorting": [[ 0, "desc" ]],
            //'searching': false, // Remove default Search Control
            'ajax': {
             'url':'/admin/events/get_prior_event_list_by_page',
             'headers': {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
             'data': function(data){
                // Read values
                var store_id = $('#store_id').val();
                var area_id = $('#area_id').val();
                var date_between = $('.date_between').val();
                var association_id = $('#association_id').val();
                var client_id = $('#client_id').val();
                var division_id = $('#division_id').val();
                var district_id = $('#district_id').val();
                if($('input[name="exclude_pic"]:checked'))
                    var exclude_pic = $('input[name="exclude_pic"]:checked').val()
                else
                    var exclude_pic = 0;
                if($('input[name="exclude_qc"]:checked'))
                    var exclude_qc = $('input[name="exclude_qc"]:checked').val();
                else
                    var exclude_qc = 0;
                if($('input[name="exclude_scheduled"]:checked'))
                    var exclude_scheduled = $('input[name="exclude_scheduled"]:checked').val();
                else
                    var exclude_scheduled = 0;
                // Append to data
                data.store_id = store_id;
                data.area_id = area_id;
                data.date_between = date_between;
                data.association_id = association_id;
                data.client_id = client_id;
                data.division_id = division_id;
                data.district_id = district_id;
                data.exclude_qc = exclude_qc;
                data.exclude_scheduled = exclude_scheduled;
                data.exclude_pic = exclude_pic;
             }
          },
          'columns': [
             { data: 'id'},
             { data: 'store' },
             { data: 'state' }, 
             { data: 'last_inventory_value' },
             { data: 'date' },
             { data: 'start_time' },
             { data: 'area' },
             { data: 'run' },
             { data: 'lead' },
             { data: 'status' },
             { data: 'buttons' ,width: '10%'},
          ],
          'columnDefs': [ {
               'targets': [2,3,6,9,10], // column index (start from 0)
               'orderable': false, // set orderable false for selected columns
            }],
          'buttons': [
            'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            'dom': 'Blfrtip',
        });

        dataTable.columns( [0,6,7,8,9] ).visible( false );

        $('#searchByName').keyup(function(){
          dataTable.draw();
        });
        $('#association_id').change(function(){
          dataTable.draw();
        });
        $('#client_id').change(function(){
          dataTable.draw();
        });
        $('#division_id').change(function(){
          dataTable.draw();
        });
        $('#district_id').change(function(){
          dataTable.draw();
        });
        $('#store_id').change(function(){
          dataTable.draw();
        });
        $('#area_id').change(function(){
          dataTable.draw();
        });
        $('.date_between').change(function(){
          dataTable.draw();
        });
        $('#exclude_pic').click(function(){
          dataTable.draw();
        });
        $('#exclude_qc').change(function(){
          dataTable.draw();
        });
        $('#exclude_scheduled').change(function(){
          dataTable.draw();
        });
    });
    $(document).ready(function(){
        $("body").on('hide.bs.modal', function() { 
             $("#event_qc").trigger("reset");
            $("#event_precall").trigger("reset");
        });
        $(document).on('click','.popup_cancel_btn',function() {
            $('.modalCloseBtn').trigger('click');
            $("#event_qc").trigger("reset");
            $("#event_precall").trigger("reset");
        });
        $(document).on('click','#event_feedback_call_btn,.event_qc_call_btn,.event_precall_btn',function() {
            var eventID = $(this).attr('data-id');
            $(".event_id").val(eventID);
        });
        $(document).on('click','.event_qc_call_btn,.event_precall_btn',function() {
            $('.qc.preloader').show();
            var eventID = $(this).attr('data-id');
            $('.qc_completed_on,.qc_completed_by').html('');
            $('.qcinfo').hide();
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
                        $('.qc.preloader').hide();
                        $('#qc_comment').val(res.event.qc_comment);
                        $('#precall_manager').val(res.event.precall_manager);
                        $('#precall_comments').val(res.event.precall_comments);
                        $("input[name='on_time'][value='"+res.event.on_time+"']").prop('checked', true);
                        $("input[name='in_uniform'][value='"+res.event.in_uniform+"']").prop('checked', true);
                        $("input[name='positive_exp'][value='"+res.event.positive_exp+"']").prop('checked', true);
                        $("input[name='overall_accurate'][value='"+res.event.overall_accurate+"']").prop('checked', true);
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
                        if(res.event.store.store_or_other=="other")
                        {
                            $('.store_phone_other').html('<label>Store Phone</label><br>'+res.event.store.other_contact_number);
                            $('.store_manager_other').html('<label>Store Manager</label><br>'+res.event.store.other_contact_name);
                        }
                        if(res.event.store.picstore_or_other=="other")
                        {
                            $('.picstore_phone_other').html('<label>Store Phone</label><br>'+res.event.store.picother_contact_number);
                            $('.picstore_manager_other').html('<label>Store Manager</label><br>'+res.event.store.picother_contact_name);
                        }
                        $('.start_time').html('<label>Start Time</label><br>'+res.event.start_time);
                        $('.confirmed_with').val(res.event.qc_confirmed_with);
                        $('.qc_contact').val(res.event.qc_contact);
                        $('.event_date').html('<label>Event Date</label><br>'+formatDateMysql(res.event.date));
                        $('.update_at').html('<label>Confirmation Date</label><br>'+formatDateMysql(res.event.precall_completed_on));
                        $('.update_at2').html('<label>Confirmation Time</label><br>'+ toTimestamp(res.event.precall_completed_on));
                       
                    }
                });
            
        });
//        $(document).on('click','#event_precall_btn',function() {
//            var eventID = $(this).attr('data-id');
//            $(".event_id").val(eventID);
//        });
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
            //$("#copyeventSchedulePopup").modal();
            $.ajax({
                    type:"GET",
                    url:"/admin/events/pendingeventlist/"+eventID,
                    success:function(res){
                        if(res.pending_events)
                        {
                            $("#copy_scheduled_events").empty();
                            $("#copy_scheduled_events").append(res.pending_events);
                            setTimeout(function(){
                                $("#copyeventSchedulePopup").modal();
                            }, 3000);
                            
                        }
                        
                    }
            });
        });
        
//        $(document).on('click','.save-event-feedback',function(){
//            var data = $("#event_feedback").serialize();
//                $.ajax({
//                    type:"POST",
//                    url:'/admin/events-feedback',
//                    data:data,
//                    success:function(res){
//                        $('.feedback-response').html(res.message).show();
//                        
//                        $("#event_feedback").trigger("reset");
//                        $(".event_id").val('');
//                        setTimeout(function(){
//                            $('#feedbackPopup').modal('hide');
//                            $('.feedback-response').html('').hide();
//                        }, 3000);
//                    }
//                });
//           
//        });
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
            var data = $("#event_precall").serialize();
            $('.save-event-precall').addClass('disabled');
            $('.popup_cancel_btn').addClass('disabled');
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
            var html ='<div style="height:75px;" class="col-xs-3 blackout_counter'+blackout_counter+'"><label for="truck_dates" class="control-label">Truck Dates</label>\n\
                        <input class="form-control datepicker" name="truck_dates[]" autocomplete="off" type="text"></div><div style="height:75px;" blackout="'+blackout_counter+'" class="col-xs-3 remove_blackout blackout_counter'+blackout_counter
                        +'"><i class="fa fa-trash" aria-hidden="true"></i></div>';
            $('#blackout_counter').val(blackout_counter);
            $(".blackout_dates_container").append(html);
           // alert('sdf');
        })
        
        $('[data-mask]').inputmask();
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
    function toTimestamp(strDate){
        var datum = Date.parse(strDate);
    let unix_timestamp = datum/1000;
    var date = new Date(unix_timestamp * 1000);
    var hours = date.getHours();
    var minutes = "0" + date.getMinutes();
    var seconds = "0" + date.getSeconds();
    var formattedTime = hours + ':' + minutes.substr(-2) + ':' + seconds.substr(-2);
    console.log(formattedTime);
    var time = formattedTime; // your input

time = time.split(':'); // convert to array

// fetch
var hours = Number(time[0]);
var minutes = Number(time[1]);
var seconds = Number(time[2]);

// calculate
var timeValue;

if (hours > 0 && hours <= 12) {
  timeValue= "" + hours;
} else if (hours > 12) {
  timeValue= "" + (hours - 12);
} else if (hours == 0) {
  timeValue= "12";
}
 
timeValue += (minutes < 10) ? ":0" + minutes : ":" + minutes;  // get minutes
timeValue += (seconds < 10) ? ":0" + seconds : ":" + seconds;  // get seconds
timeValue += (hours >= 12) ? " P.M." : " A.M.";  // get AM/PM
    return timeValue;
    }
</script>
@endsection