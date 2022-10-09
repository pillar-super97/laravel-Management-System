@extends('layouts.app')
@section('pageTitle', 'View Event')
@section('content')
    <h3 class="page-title">Events</h3>
    
    {!! Form::model($event, ['method' => 'PUT', 'route' => ['admin.events.update', $event->id], 'files' => true,'autocomplete'=>'off']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">View Event</div>
       
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-3 form-group">
                    {!! Form::label('number', 'Event Number', ['class' => 'control-label required']) !!}
                    {!! Form::text('number', old('number'), ['class' => 'form-control','disabled', 'placeholder' => '', 'required' => '']) !!}
                </div>
                <div class="col-xs-3 form-group{{ $errors->has('crew_leader') ? ' has-error' : '' }}">
                    <label for="crew_leader" class="control-label">Crew Leader</label>
                    <select id="crew_leader" class="form-control" disabled="" name="crew_leader" >
                        <option><?php echo @$event->crew_leader_name->name;?></option>
                    </select>
                </div>
                <div class="col-xs-3 form-group{{ $errors->has('store_id') ? ' has-error' : '' }}">
                    <label for="store_id" class="control-label required">Store</label>
                    <select id="store_id" disabled="" class="form-control client_id required select2" name="store_id" >
                        <option><?php echo @$event->store->name;?></option>
                    </select>
                </div>
                
                <div class="col-xs-3" style="height:75px;">
                    {!! Form::label('date', 'Date', ['class' => 'control-label required']) !!}
                    {!! Form::text('date', date('m/d/Y',strtotime($event->date)), ['class' => 'form-control datepicker','disabled','required', 'autocomplete'=>'off','placeholder' => '']) !!}
                </div>
                
            
                
                
                <div class="col-xs-3 form-group">
                    {!! Form::label('start_time', 'Start Time', ['class' => 'control-label required']) !!}
                    {!! Form::text('start_time', date('h:i A',strtotime($event->start_time)), ['class' => 'form-control timepicker','disabled','required', 'placeholder' => '']) !!}
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('run_number', 'Run Number', ['class' => 'control-label required']) !!}
                    <select id="run_number" class="form-control select2" disabled="" required="" name="run_number">
                        <?php for($i=1;$i<=9;$i++){?>
                        <option value="<?php echo $i;?>" <?php if($i==$event->run_number){echo 'selected="selected"';}?>><?php echo $i;?></option>
                        <?php }?>
                    </select>
                </div>
                <div class="col-xs-3 form-group{{ $errors->has('areas') ? ' has-error' : '' }}">
                    <label for="areas" class="control-label required">Area(s)</label>
                    <select id="areas" class="form-control select2" disabled="" required="" name="areas[]">
                        <?php foreach($event->areas as $area){?>
                        <option selected="selected"><?php echo $area->area->title;?></option>
                        <?php }?>
                    </select>
                  
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('city', 'City/State', ['class' => 'control-label']) !!}
                    {!! Form::text('city', @$event->store->city->name.', '.@$event->store->state->state_code, ['class' => 'form-control','disabled']) !!}
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
                    {!! Form::label('crew_count', 'Crew Count', ['class' => 'control-label']) !!}
                    {!! Form::text('crew_count', old('crew_count'), ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                </div>
             
                <div class="col-xs-3 form-group">
                    <label class="control-label">Overnight</label><br>
                    <?=$event->overnight?>
                </div>
                @can('view_meals_and_lodging_for_event')
                <div class="col-xs-3 form-group">
                    {!! Form::label('meal_amount', 'Meal Amount', ['class' => 'control-label']) !!}
                    {!! Form::text('meal_amount', old('meal_amount'), ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                 </div>

                 <div class="col-xs-3 form-group">
                    {!! Form::label('lodging_amount', 'Lodging Amount', ['class' => 'control-label']) !!}
                    {!! Form::text('lodging_amount', old('lodging_amount'), ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                 </div>
                 @endcan


                <div class="col-xs-3 form-group">
                    <label class="control-label">PIC</label><br>
                    <?=$event->pic?>
                </div>
                
                <div class="col-xs-3 form-group">
                    <label class="control-label">QC</label><br>
                    <?=$event->qc?>
                </div>
            </div>
           
            <div class="row">
                <div class="col-xs-3 form-group">
                    <label class="control-label">Count RX</label><br>
                    <?=$event->count_rx?>
                </div>
                <div class="col-xs-3 form-group">
                    <label class="control-label">Count Backroom</label><br>
                    <?=$event->count_backroom?>
                </div>
                <div class="col-xs-3 form-group{{ $errors->has('road_trip') ? ' has-error' : '' }}">
                    <label for="road_trip" class="control-label">Road Trip</label>
                    <select id="road_trip" class="form-control client_id" disabled="" name="road_trip" >
                        <option><?=$event->road_trip;?></option>
                    </select>
                </div>
                <?php if(@$his_data['last_inventory_date']){?>
                <div class="col-xs-3 form-group">
                    {!! Form::label('last_inventory_date', 'Last Inventory Date', ['class' => 'control-label']) !!}
                    {!! Form::text('last_inventory_date', date('m/d/Y',strtotime($his_data['last_inventory_date'])), ['class' => 'form-control datepicker','disabled', 'placeholder' => '']) !!}
                </div>
                <?php }?>
                <div class="col-xs-3 form-group">
                    {!! Form::label('last_start_time', 'Last Start Time', ['class' => 'control-label']) !!}
                    {!! Form::text('last_start_time', @$his_data['last_start_time'], ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('last_crew_count', 'Last Crew Count', ['class' => 'control-label']) !!}
                    {!! Form::text('last_crew_count', @$his_data['last_crew_count'], ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('last_count_length', 'Last Count Length', ['class' => 'control-label']) !!}
                    {!! Form::text('last_count_length', @$his_data['last_count_length'], ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('last_count_production', 'Last Production Count', ['class' => 'control-label']) !!}
                    {!! Form::text('last_count_production', @$his_data['last_production_count'], ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('last_inventory_value', 'Last Inventory Value', ['class' => 'control-label']) !!}
                    {!! Form::text('last_inventory_value', @$his_data['last_inventory_value'], ['class' => 'form-control','disabled', 'placeholder' => '']) !!}
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('district', 'District', ['class' => 'control-label']) !!}
                    {!! Form::text('district', @$event->store->district->number, ['class' => 'form-control','disabled']) !!}
                </div>
                <?php 
                    if(count($event->store->schedule_availability_days)){
                        $schedule_availability_days = array();
                        foreach($event->store->schedule_availability_days as $day)
                            $schedule_availability_days[] = $day->days;
                        $schedule_availability_days=implode(',',$schedule_availability_days);
                    }else{
                        $schedule_availability_days = '';
                    }?>
                <div class="col-xs-3 form-group">
                    {!! Form::label('days_available_to_schedule', 'Days Available To Schedule', ['class' => 'control-label']) !!}
                    {!! Form::text('days_available_to_schedule', $schedule_availability_days, ['class' => 'form-control','disabled']) !!}
                </div>
                <div class="col-xs-3 form-group">
                    {!! Form::label('alr_disk', 'ALR Disk', ['class' => 'control-label']) !!}
                    {!! Form::text('alr_disk', @$event->store->alr_disk, ['class' => 'form-control','disabled']) !!}
                </div>
                
            </div>
            
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('comments', 'Schedule Comments', ['class' => 'control-label']) !!}
                    {!! Form::textarea('comments', old('comments'), ['class' => 'form-control ','rows'=>3,'disabled', 'placeholder' => '']) !!}
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('store_notes', 'Store Notes', ['class' => 'control-label']) !!}
                    {!! Form::textarea('store_notes',@$event->store->notes, ['class' => 'form-control ','rows'=>3,'disabled', 'placeholder' => '']) !!}
                </div>
            </div>
            <div class="blackout_dates_container">
                 <?php if(count($event->truck_dates)){foreach($event->truck_dates as $key=>$dates){?> 
                    <div class="col-xs-2 blackout_counter<?=$key;?>" style="height:75px;">
                        {!! Form::label('truck_dates', 'Truck Dates', ['class' => 'control-label']) !!}
                        {!! Form::text('truck_dates[]', date("m/d/Y", strtotime($dates->truck_date)), ['class' => 'form-control datepicker','disabled', 'placeholder' => '']) !!}
                        
                    </div>
                   
               <?php }}?>
                
            </div>
                
            
            
            
        </div>
    </div>

   <a href="{{ route('admin.events.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
@stop
@section('javascript')
    @parent
  
@stop