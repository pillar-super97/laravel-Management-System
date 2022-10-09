@extends('layouts.app')
@section('pageTitle', 'Edit Event')
@section('content')
<h3 class="page-title">Events</h3>

{!! Form::model($event, ['method' => 'PUT', 'route' => ['admin.events.update', $event->id], 'files' =>
true,'autocomplete'=>'off']) !!}

<div class="panel panel-default">
    <div class="panel-heading">Edit Event</div>
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
                <label for="crew_leader" class="control-label">Crew Leader</label>
                <select id="crew_leader" class="form-control select2" name="crew_leader">
                    <option value="">Select Crew Leader</option>
                    <?php foreach ($employees as $key=>$employee){?>
                    <option value="<?php echo $key;?>"
                        <?php if($key==$event->crew_leader){echo 'selected="selected"';}?>><?php echo $employee;?>
                    </option>
                    <?php }?>
                </select>

                @if ($errors->has('manager_id'))
                <p class="error-block">
                    {{ $errors->first('manager_id') }}
                </p>
                @endif
            </div>
            <div class="col-xs-3 form-group{{ $errors->has('store_id') ? ' has-error' : '' }}">
                <label for="store_id" class="control-label required">Store</label>
                <select id="store_id" class="form-control client_id required select2" name="store_id">
                    <option value="">Select store</option>
                    <?php foreach ($stores as $key=>$store){?>
                    <option value="<?php echo $key;?>" <?php if($key==$event->store_id){echo 'selected="selected"';}?>>
                        <?php echo $store;?></option>
                    <?php }?>
                </select>

                @if ($errors->has('store_id'))
                <p class="error-block">
                    {{ $errors->first('store_id') }}
                </p>
                @endif
            </div>

            <div class="col-xs-3" style="height:75px;">
                {!! Form::label('date', 'Date', ['class' => 'control-label required']) !!}
                {!! Form::text('date', date('m/d/Y',strtotime($event->date)), ['class' => 'form-control
                datepicker','required', 'autocomplete'=>'off','placeholder' => '']) !!}
                <p class="help-block"></p>
                @if($errors->has('date'))
                <p class="error-block">
                    {{ $errors->first('date') }}
                </p>
                @endif
            </div>
            <?php //echo '<pre>';print_r($event->store);die;?>
            <div class="col-xs-3 form-group">
                {!! Form::label('alr_disk', 'ALR Disk', ['class' => 'control-label']) !!}
                {!! Form::text('alr_disk', @$event->store->alr_disk, ['class' => 'form-control']) !!}
                <p class="help-block"></p>
                @if($errors->has('alr_disk'))
                <p class="error-block">
                    {{ $errors->first('alr_disk') }}
                </p>
                @endif
            </div>
        </div>

        <div class="row">


            <div class="col-xs-3 form-group">
                {!! Form::label('start_time', 'Start Time', ['class' => 'control-label required']) !!}
                {!! Form::text('start_time', date('h:i A',strtotime($event->start_time)), ['class' => 'form-control
                timepicker','required', 'placeholder' => '']) !!}
                <p class="help-block"></p>
                @if($errors->has('start_time'))
                <p class="error-block">
                    {{ $errors->first('start_time') }}
                </p>
                @endif
            </div>
            <div class="col-xs-3 form-group">
                {!! Form::label('run_number', 'Run Number', ['class' => 'control-label required']) !!}
                <select id="run_number" class="form-control select2" required="" name="run_number">
                    <?php for($i=1;$i<=9;$i++){?>
                    <option value="<?php echo $i;?>" <?php if($i==$event->run_number){echo 'selected="selected"';}?>>
                        <?php echo $i;?></option>
                    <?php }?>
                </select>
                <p class="help-block"></p>
                @if($errors->has('run_number'))
                <p class="error-block">
                    {{ $errors->first('run_number') }}
                </p>
                @endif
            </div>
            <div class="col-xs-3 form-group{{ $errors->has('areas') ? ' has-error' : '' }}">
                <?php $selected_area = array();foreach($event->areas as $area){$selected_area[] = $area->area->id;}?>
                <label for="areas" class="control-label required">Area(s)</label>
                <select id="areas" class="form-control select2" multiple="" required="" name="areas[]">
                    <option value="">Select Area</option>
                    <?php foreach ($areas as $key=>$area){?>
                    <option value="<?php echo $key;?>"
                        <?php if(in_array($key,$selected_area)){echo 'selected="selected"';}?>><?php echo $area;?>
                    </option>
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
                {!! Form::text('crew_count', old('crew_count'), ['class' => 'form-control', 'placeholder' => '']) !!}
                <p class="help-block"></p>
                @if($errors->has('crew_count'))
                <p class="error-block">
                    {{ $errors->first('crew_count') }}
                </p>
                @endif
            </div>

                    </div>
                <div class="row">

                    <div class="col-xs-3 form-group">
                        <label class="control-label">Overnight</label><br>
                        <label class="control-label">
                            {!! Form::radio('overnight','Yes',['class' => 'form-control']) !!} Yes
                        </label>
                        <label class="control-label">
                            {!! Form::radio('overnight','No',['class' => 'form-control']) !!} No
                        </label>
                        <p class="help-block"></p>
                        @if($errors->has('overnight'))
                        <p class="error-block">
                            {{ $errors->first('overnight') }}
                        </p>
                        @endif
                    </div>


                        @can('edit_meals_and_lodging_for_event')
                    <div class="col-xs-3 form-group">
                        {!! Form::label('meal_amount', 'Meal Amount', ['class' => 'control-label']) !!}
                        {!! Form::number('meal_amount', old('meal_amount'), ['class' => 'form-control', 'placeholder' =>
                        '']) !!}
                        <p class="help-block"></p>
                        @if($errors->has('meal_amount'))
                        <p class="error-block">
                            {{ $errors->first('meal_amount') }}
                        </p>
                        @endif
                    </div>

                    

                    <div class="col-xs-3 form-group">
                        {!! Form::label('lodging_amount', 'Lodging Amount', ['class' => 'control-label']) !!}
                        {!! Form::number('lodging_amount', old('lodging_amount'), ['class' => 'form-control',
                        'placeholder' => '']) !!}
                        <p class="help-block"></p>
                        @if($errors->has('lodging_amount'))
                        <p class="error-block">
                            {{ $errors->first('lodging_amount') }}
                        </p>
                        @endif
                    </div>
                    @endcan

                </div>
                <div class="row">


                    <div class="col-xs-3 form-group">
                        <label class="control-label">PIC</label><br>
                        <label class="control-label">
                            {!! Form::radio('pic','Yes',['class' => 'form-control']) !!} Yes
                        </label>
                        <label class="control-label">
                            {!! Form::radio('pic','No',['class' => 'form-control']) !!} No
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
                            {!! Form::radio('qc','Yes',['class' => 'form-control']) !!} Yes
                        </label>
                        <label class="control-label">
                            {!! Form::radio('qc','No',['class' => 'form-control']) !!} No
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
                            {!! Form::radio('count_rx','Yes',['class' => 'form-control']) !!} Yes
                        </label>
                        <label class="control-label">
                            {!! Form::radio('count_rx','No',['class' => 'form-control']) !!} No
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
                            {!! Form::radio('count_backroom','Yes',['class' => 'form-control']) !!} Yes
                        </label>
                        <label class="control-label">
                            {!! Form::radio('count_backroom','No',['class' => 'form-control']) !!} No
                        </label>
                        <p class="help-block"></p>
                        @if($errors->has('count_backroom'))
                        <p class="error-block">
                            {{ $errors->first('count_backroom') }}
                        </p>
                        @endif
                    </div>
                    <div class="col-xs-3 form-group{{ $errors->has('road_trip') ? ' has-error' : '' }}">
                        <label for="road_trip" class="control-label">Road Trip</label>
                        <select id="road_trip" class="form-control client_id" name="road_trip">
                            <option value="">Select Road Trip</option>
                            <option value="Start Road Trip"
                                <?php if($event->road_trip=="Start Road Trip"){echo 'selected="selected"';}?>>Start Road
                                Trip</option>
                            <option value="End Road Trip"
                                <?php if($event->road_trip=="End Road Trip"){echo 'selected="selected"';}?>>End Road
                                Trip</option>
                            <option value="Road Trip"
                                <?php if($event->road_trip=="Road Trip"){echo 'selected="selected"';}?>>Road Trip
                            </option>
                            <option value="No" <?php if($event->road_trip=="No"){echo 'selected="selected"';}?>>No
                            </option>
                        </select>
                        @if ($errors->has('road_trip'))
                        <p class="error-block">
                            {{ $errors->first('road_trip') }}
                        </p>
                        @endif
                    </div>
                    <?php $historical_data_from_timesheet = historical_data_by_event_date(@$event->store->id,$event->date);
                //print_r($historical_data_from_timesheet);die;?>
                    <div class="col-xs-3 form-group">
                        {!! Form::label('last_inventory_date', 'Last Inventory Date', ['class' => 'control-label']) !!}
                        <?php if(isset($historical_data)){?>
                        {!! Form::text('last_inventory_date', @date('m/d/Y',strtotime(@$historical_data->date)),
                        ['class' => 'form-control datepicker','disabled', 'placeholder' => '']) !!}
                        <?php }else{?>
                        {!! Form::text('last_inventory_date', '', ['class' => 'form-control datepicker','disabled',
                        'placeholder' => '']) !!}
                        <?php  }?>

                        <p class="help-block"></p>
                        @if($errors->has('last_inventory_date'))
                        <p class="error-block">
                            {{ $errors->first('last_inventory_date') }}
                        </p>
                        @endif
                    </div>
                    <div class="col-xs-3 form-group">
                        {!! Form::label('last_start_time', 'Last Start Time', ['class' => 'control-label']) !!}
                        {!! Form::text('last_start_time', @$historical_data->start_time, ['class' =>
                        'form-control','disabled', 'placeholder' => '']) !!}
                        <p class="help-block"></p>
                        @if($errors->has('last_start_time'))
                        <p class="error-block">
                            {{ $errors->first('last_start_time') }}
                        </p>
                        @endif
                    </div>
                    <?php //historical_data_by_event_date($event->store->id,$event->date);die;?>
                    <div class="col-xs-3 form-group">
                        {!! Form::label('last_crew_count', 'Last Crew Count', ['class' => 'control-label']) !!}
                        {!! Form::text('last_crew_count', @$historical_data_from_timesheet['crew_count'], ['class' =>
                        'form-control','disabled', 'placeholder' => '']) !!}
                        <p class="help-block"></p>
                        @if($errors->has('last_crew_count'))
                        <p class="error-block">
                            {{ $errors->first('last_crew_count') }}
                        </p>
                        @endif
                    </div>
                    <div class="col-xs-3 form-group">
                        {!! Form::label('last_count_length', 'Last Count Length', ['class' => 'control-label']) !!}
                        {!! Form::text('last_count_length', @$historical_data_from_timesheet['count_length'], ['class'
                        => 'form-control','disabled', 'placeholder' => '']) !!}
                        <p class="help-block"></p>
                        @if($errors->has('last_count_length'))
                        <p class="error-block">
                            {{ $errors->first('last_count_length') }}
                        </p>
                        @endif
                    </div>
                    <div class="col-xs-3 form-group">
                        {!! Form::label('last_count_production', 'Last Production Count', ['class' => 'control-label'])
                        !!}
                        {!! Form::text('last_count_production', @$historical_data_from_timesheet['production'], ['class'
                        => 'form-control','disabled', 'placeholder' => '']) !!}
                        <p class="help-block"></p>
                        @if($errors->has(' last_count_production'))
                        <p class="error-block">
                            {{ $errors->first(' last_count_production') }}
                        </p>
                        @endif
                    </div>
                    <div class="col-xs-3 form-group">
                        {!! Form::label('last_inventory_value', 'Last Inventory Value', ['class' => 'control-label'])
                        !!}
                        {!! Form::text('last_inventory_value', @$historical_data_from_timesheet['last_inventory_value'],
                        ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                        <p class="help-block"></p>
                        @if($errors->has('last_inventory_value'))
                        <p class="error-block">
                            {{ $errors->first('last_inventory_value') }}
                        </p>
                        @endif
                    </div>


                </div>

                <div class="row">
                    <div class="col-xs-12 form-group">
                        {!! Form::label('comments', 'Schedule Comments', ['class' => 'control-label']) !!}
                        {!! Form::textarea('comments', old('comments'), ['class' => 'form-control ', 'placeholder' =>
                        '']) !!}
                        <p class="help-block"></p>
                        @if($errors->has('comments'))
                        <p class="error-block">
                            {{ $errors->first('comments') }}
                        </p>
                        @endif
                    </div>
                </div>
                <div class="truck_dates_container">
                    <?php if($event && count($event->truck_dates)){foreach($event->truck_dates as $key=>$dates){?>
                    <div class="col-xs-2 blackout_counter<?=$key;?>" style="height:75px;">
                        {!! Form::label('truck_dates', 'Truck Dates', ['class' => 'control-label']) !!}
                        {!! Form::text('truck_dates[]', date("m/d/Y", strtotime($dates->truck_date)), ['class' =>
                        'form-control datepicker','disabled', 'placeholder' => '']) !!}
                    </div>
                    <?php }}else{?>
                    <div class="col-xs-2 blackout_counter1" style="height:75px;">
                        {!! Form::label('truck_dates', 'Truck Dates', ['class' => 'control-label']) !!}
                        {!! Form::text('truck_dates[]','', ['class' => 'form-control datepicker','disabled',
                        'placeholder' => '','autocomplete'=>'off']) !!}
                    </div>
                    <?php   }?>
                </div>




            </div>
        </div>

        {!! Form::submit(trans('global.app_update'), ['class' => 'btn btn-danger']) !!}
        {!! Form::close() !!}
        @stop
        @section('javascript')
        @parent
        <script type="text/javascript">
        $(document).ready(function() {
            $('body').on('focus', ".datepicker", function() {
                if ($(this).hasClass('hasDatepicker') === false) {
                    $(this).datepicker({
                        autoclose: true,
                        format: 'mm/dd/yyyy'
                    });
                }

            });
            $('.datepicker').datepicker({
                autoclose: true,
                format: 'mm/dd/yyyy'
            })


            $('[data-mask]').inputmask();
            $('.timepicker').timepicker({
                showInputs: false
            })
        })
        </script>
        @stop