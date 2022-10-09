@extends('layouts.app')
@section('pageTitle', 'Edit Timesheet')
@section('content')
    <h3 class="page-title"></h3>
    
    <?php
    if($timesheet_submitted){?>
        {{ Form::open(array('route' => ['admin.timesheets.approve', $timesheet->id], 'method' => 'POST','id'=>'approve_timesheet','onsubmit'=>'return (confirm(\'Note that the event has a timesheet approved for it already, are you sure you want to approve this timesheet?\') && myFunction());')) }}
    <?php }else{?>
        {{ Form::open(array('route' => ['admin.timesheets.approve', $timesheet->id], 'method' => 'POST','id'=>'approve_timesheet','onsubmit'=>'return myFunction();')) }}
    <?php }?>        
    {{ csrf_field() }}
    <div class="panel panel-default">
        <div class="panel-heading">Approve Timesheet</div>
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
                    <?=@$timesheet->store->name?><br>
                    <?=@$timesheet->store->address?><br>
                    <?=@$timesheet->store->city->name?>,<?=@$timesheet->store->state->name?>, <?=@$timesheet->store->zip?><br>
                </div>
                <div class="col-xs-3 form-group">
                  <input type="hidden" name="pay_date" class="datepicker" value="<?=@date('m/d/Y',strtotime($timesheet->dtJobDate))?>">
                  <input type="hidden" name="event_date" value="<?=@date('Y-m-d',strtotime($timesheet->event->date))?>">
                  <input type="hidden" name="storename" value="<?=@$timesheet->store->name?>">
                  <input type="hidden" name="storenumber" value="<?=@$timesheet->store->number?>">
                  <input type="hidden" class="eventid" name="eventid" value="<?=@$timesheet->event->id?>">
                  <input type="hidden" name="affected_row" id="affected_row">
                 
                    <?=@date('m/d/Y',strtotime($timesheet->dtJobDate))?><br>
                 
                  <?=@$timesheet->store->area_prime_responsibility->title?><br>
                </div>
                <div class="col-xs-3 form-group">
                    <b>Dollars</b> <?=($timesheet->dEmpCount)?'$ '.number_format($timesheet->dEmpCount):'';?><br>
                    <b>Pieces</b>  <?=number_format($timesheet->dEmpPieces)?><br>
                </div>
                <div class="col-xs-3 form-group">
                    <b>Comments:</b> <?= str_replace('{s22}',' ', $timesheet->InvRecapComments)?><br>
                    <b>Timesheet Notes:</b> <?= str_replace('{s22}',' ', $timesheet->mComments)?><br>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-3 form-group">
                    <b>Arrived:</b> <?=(strtotime($timesheet->InvRecapArvlTime))?date('h:i A',strtotime($timesheet->InvRecapArvlTime)):''?>
                </div>
                <div class="col-xs-3 form-group">
                    <b>Started:</b> <?=(strtotime($timesheet->InvRecapStartTime))?date('h:i A',strtotime($timesheet->InvRecapStartTime)):''?>
                </div>
                <div class="col-xs-3 form-group">
                    <b>End:</b> <?=(strtotime($timesheet->InvRecapEndTime))?date('h:i A',strtotime($timesheet->InvRecapEndTime)):''?>
                </div>
                <div class="col-xs-3 form-group">
                    <b>Out:</b> <?=(strtotime($timesheet->InvRecapWrapTime))?date('h:i A',strtotime($timesheet->InvRecapWrapTime)):''?>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                    <th>Emp Name /<br> Area</th>
                    <th>Store Hours (hh:mm) /<br> Status</th>
                    <th>PIM /<br> Wait</th>
                    <th>Lunch1 /<br> Lunch2</th>
                    <th>Drive Time (hh:mm) /<br> Travel Time (hh:mm)</th>
                    <th>Time in /<br> Time out</th>
                    <th>Origin /<br> Destination</th>
                    <th>JSA<br>(In Miles)</th>
                </thead>
                <tbody>
                    <?php 
    $vehicles = $timesheet->vehicles->toArray();
    $driverto_arr=array();
    $driverfrom_arr=array();
    $pay_date = @date('Y-m-d',strtotime($timesheet->dtJobDate));
    $event_date = @date('Y-m-d',strtotime($timesheet->event->date));
    foreach($vehicles as $vehicle)
    {
        $driverto_arr[]=$vehicle['driver_to'];
        $driverfrom_arr[]=$vehicle['driver_from'];
    }
    //echo '<pre>';print_r($driverto_arr);print_r($driverfrom_arr);die;
    //$employees = $timesheet->emp_data->toArray();
    //echo '<pre>';print_r($vehicles);die;
    $present_emp=0;
    $absent_emp=0;
    foreach($employees as $mainkey=>$emp){
        //echo '<pre>';print_r($emp);die;
        $driverto=0;
        $driverfrom=0;
        $drivetime=0;
        $vehicle_travel=0;
        $jsa_miles=0;
        $traveltime=0;
        if($emp->iAttendanceFlag==3 || $emp->iAttendanceFlag==4)
            $absent_emp++;
        else
            $present_emp++;
//        If driver To then miles from origin to event
//        If driver From then miles from event to destination
//        If driver To AND From then miles from origin to event and from event to destination
        if(strtolower($emp->sStoreOrigin)=="office" && strtolower($emp->sStoreReturn)=="office" && $emp->iAttendanceFlag!=3 && $emp->iAttendanceFlag!=4)
        {
            //echo 'test100';
                if(in_array($emp->employee_id,$driverto_arr) && in_array($emp->employee_id,$driverfrom_arr))
                {
                    //Driver To and From
                    $driverto=1;
                    $driverfrom=1;
                    $origin=$emp->emp_jsa_id;
                    $origin_type="office";
                    $destination=$timesheet->store->id;
                    $destination_type="store";
                    $dist = calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $drivetime=($distance*2)/50;
                    $drivetime = convertTimeToColon($drivetime);
                    
                    $origin=$timesheet->store->id;
                    $origin_type="store";
                    $destination=$emp->emp_jsa_id;
                    $destination_type="office";
                    $dist = calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance1 = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    
                    $vehicle_travel=ceil($distance+$distance1);
                    $jsa_miles=0;
                }elseif(in_array($emp->employee_id,$driverto_arr))
                {
                    //Driver To
                    $driverto=1;
                    $origin=$emp->emp_jsa_id;
                    $origin_type="office";
                    $destination=$timesheet->store->id;
                    $destination_type="store";
                    $dist = calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $drivetime=$distance/50;
                    $drivetime = convertTimeToColon($drivetime);
                    
                    $origin=$timesheet->store->id;
                    $origin_type="store";
                    $destination=$emp->emp_jsa_id;
                    $destination_type="office";
                    $dist = calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance1 = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $jsa_miles=ceil($distance1-12);
                    $vehicle_travel=ceil($distance);
                }elseif(in_array($emp->employee_id,$driverfrom_arr))
                {
                    //Driver From
                    $driverfrom=1;
                    $origin=$timesheet->store->id;
                    $origin_type="store";
                    $destination=$emp->emp_jsa_id;
                    $destination_type="office";
                    $dist = calDistance($origin, $origin_type, $destination, $destination_type);
                    //echo '<pre>';print_r($dist);
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $drivetime=$distance/50;
                    $drivetime = convertTimeToColon($drivetime);
                    
                    $origin=$emp->emp_jsa_id;
                    $origin_type="office";
                    $destination=$timesheet->store->id;
                    $destination_type="store";
                    $dist = calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance1 = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $jsa_miles=ceil($distance1-12);
                    $vehicle_travel=ceil($distance);
                }else{
                    //echo 'ss'.$emp->emp_jsa_id;die;
                    //echo '<pre>';print_r($emp->employee->jsa);die;
                    $origin = @$emp->emp_jsa_id;
                    $origin_type="office";
                    $destination=$timesheet->store->id;
                    $destination_type="store";
                    $dist = calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $jsa_miles=ceil($distance*2-25);
                }
        }elseif(strtolower($emp->sStoreOrigin)=="office" && strtolower($emp->sStoreReturn)!="office" && $emp->iAttendanceFlag!=3 && $emp->iAttendanceFlag!=4)
        {
            //die('Driver To and From');
            //echo 'test166';
                if(in_array($emp->employee_id,$driverto_arr) && in_array($emp->employee_id,$driverfrom_arr))
                {
                    //Driver To and From
                    $driverto=1;
                    $driverfrom=1;
                    $origin=$emp->emp_jsa_id;
                    $origin_type="office";
                    $destination=$timesheet->store->id;
                    $destination_type="store";
                    $dist = calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $drivetime=$distance/50;
                    $drivetime = convertTimeToColon($drivetime);
                    
                    $origin=$timesheet->store->id;
                    $origin_type="store";
                    $destination=$emp->sStoreReturn;
                    $destination_type="store";
                    $dist = calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance1 = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $vehicle_travel=ceil($distance);
                }elseif(in_array($emp->employee_id,$driverto_arr))
                {
                    //Driver To
                    $driverto=1;
                    $origin=$emp->emp_jsa_id;
                    $origin_type="office";
                    $destination=$timesheet->store->id;
                    $destination_type="store";
                    $dist = calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $drivetime=$distance/50;
                    $vehicle_travel=ceil($distance);
                    $drivetime = convertTimeToColon($drivetime);
                }elseif(in_array($emp->employee_id,$driverfrom_arr))
                {
                    //Driver From
                    $driverfrom=1;
                    $origin=$emp->emp_jsa_id;
                    $origin_type="office";
                    $destination=$timesheet->store->id;
                    $destination_type="store";
                    $dist = calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $jsa_miles=ceil($distance-12);
                    
                    $origin=$timesheet->store->id;
                    $origin_type="store";
                    $destination=$emp->sStoreReturn;
                    $destination_type="store";
                    $dist = calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance1 = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    //$vehicle_travel=ceil($distance1);
                }else{
                    $origin=@$emp->emp_jsa_id;
                    $origin_type="office";
                    $destination=$timesheet->store->id;
                    $destination_type="store";
                    $dist = calDistance($origin, $origin_type, $destination, $destination_type);
                    //echo '<pre>';print_r($dist);
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $jsa_miles=ceil($distance-12);
                }
        }elseif(strtolower($emp->sStoreOrigin)!="office" && strtolower($emp->sStoreReturn)!="office" && $emp->iAttendanceFlag!=3 && $emp->iAttendanceFlag!=4)
        {
            $originHaveEvent = originHaveEvent($emp->sStoreOrigin,$event_date);
            if($originHaveEvent)
            {
                //echo 'same event date';
                if(in_array($emp->employee_id,$driverto_arr) && in_array($emp->employee_id,$driverfrom_arr))
                {
                    //Driver To and From
                    $driverto=1;
                    $driverfrom=1;
                    $origin=$emp->sStoreOrigin;
                    $origin_type="store";
                    $destination=$timesheet->store->id;
                    $destination_type="store";
                    $dist = calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $drivetime=$distance/50;
                    $drivetime = convertTimeToColon($drivetime);
                    
                    $origin=$timesheet->store->id;
                    $origin_type="store";
                    $destination=$emp->sStoreReturn;
                    $destination_type="store";
                    $dist = calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance1 = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $vehicle_travel=ceil($distance);
                }elseif(in_array($emp->employee_id,$driverto_arr))
                {
                    //Driver To
                    $driverto=1;
                    $origin=$emp->sStoreOrigin;
                    $origin_type="store";
                    $destination=$timesheet->store->id;
                    $destination_type="store";
                    $dist = calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $drivetime=$distance/50;
                    $vehicle_travel=ceil($distance);
                    $drivetime = convertTimeToColon($drivetime);
                }elseif(in_array($emp->employee_id,$driverfrom_arr))
                {
                    //Driver From
                    $driverfrom=1;
                    $origin=$emp->sStoreOrigin;
                    $origin_type="store";
                    $destination=$timesheet->store->id;
                    $destination_type="store";
                    $dist = calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $traveltime=$distance/50;
                    $traveltime = convertTimeToColon($traveltime);
                    
                    $origin=$timesheet->store->id;
                    $origin_type="store";
                    $destination=$emp->sStoreReturn;
                    $destination_type="store";
                    $dist = calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance1 = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    //$vehicle_travel=ceil($distance1);
                }else{
                    $origin=$emp->sStoreOrigin;
                    $origin_type="store";
                    $destination=$timesheet->store->id;
                    $destination_type="store";
                    $dist = calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $traveltime=$distance/50;
                    $traveltime = convertTimeToColon($traveltime);
                }
            }else{
                if(in_array($emp->employee_id,$driverto_arr) && in_array($emp->employee_id,$driverfrom_arr))
                {
                    //Driver To and From
                    $driverto=1;
                    $driverfrom=1;
                    $origin=$emp->sStoreOrigin;
                    $origin_type="store";
                    $destination=$timesheet->store->id;
                    $destination_type="store";
                    $dist = calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $drivetime=$distance/50;
                    $drivetime = convertTimeToColon($drivetime);
                    
                    $origin=$timesheet->store->id;
                    $origin_type="store";
                    $destination=$emp->sStoreReturn;
                    $destination_type="store";
                    $dist = calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance1 = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $vehicle_travel=ceil($distance);
                }elseif(in_array($emp->employee_id,$driverto_arr))
                {
                    //Driver To
                    $driverto=1;
                    $origin=$emp->sStoreOrigin;
                    $origin_type="store";
                    $destination=$timesheet->store->id;
                    $destination_type="store";
                    $dist = calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $drivetime=$distance/50;
                    $vehicle_travel=ceil($distance);
                    $drivetime = convertTimeToColon($drivetime);
                }elseif(in_array($emp->employee_id,$driverfrom_arr))
                {
                    $origin=$emp->sStoreOrigin;
                    $origin_type="store";
                    $destination=$timesheet->store->id;
                    $destination_type="store";
                    $dist = calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $jsa_miles=ceil($distance);
                    
                    $origin=$timesheet->store->id;
                    $origin_type="store";
                    $destination=$emp->sStoreReturn;
                    $destination_type="store";
                    $dist = calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance1 = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    //$vehicle_travel=ceil($distance1);
                }else{
                    $origin=$emp->sStoreOrigin;
                    $origin_type="store";
                    $destination=$timesheet->store->id;
                    $destination_type="store";
                    $dist = calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $jsa_miles=ceil($distance);
                }
            }
        }elseif(strtolower($emp->sStoreOrigin)!="office" && strtolower($emp->sStoreReturn)=="office" && $emp->iAttendanceFlag!=3 && $emp->iAttendanceFlag!=4)
        {
            $originHaveEvent = originHaveEvent($emp->sStoreOrigin,$event_date);
            if($originHaveEvent)
            {
                //echo 'same event date';
                if(in_array($emp->employee_id,$driverto_arr) && in_array($emp->employee_id,$driverfrom_arr))
                {
                    //Driver To and From
                    $driverto=1;
                    $driverfrom=1;
                    $origin=$emp->sStoreOrigin;
                    $origin_type="store";
                    $destination=@$timesheet->store->id;
                    $destination_type="store";
                    $dist = calDistance($origin, $origin_type, $destination, $destination_type);
//                    echo '<pre>'; print_r($dist);
                   //echo 'origin to store: ';
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    //echo '<br>';
                    
                    $origin=@$timesheet->store->id;
                    $origin_type="store";
                    $destination=$emp->emp_jsa_id;
                    $destination_type="office";
                    $dist = calDistance($origin, $origin_type, $destination, $destination_type);
                    //echo 'store to office: ';
                    $distance1 = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
//                    print_r($dist);
                    //echo '<br>';
                    $vehicle_travel=$distance+$distance1;
                    $drivetime=($distance+$distance1)/50;
                    $drivetime = convertTimeToColon($drivetime);
                }elseif(in_array($emp->employee_id,$driverto_arr))
                {
                    //Driver To
                    $driverto=1;
                    $origin=$emp->sStoreOrigin;
                    $origin_type="store";
                    $destination=$timesheet->store->id;
                    $destination_type="store";
                    $dist = calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $drivetime=$distance/50;
                    $drivetime = convertTimeToColon($drivetime);
                    
                    $origin=$timesheet->store->id;
                    $origin_type="store";
                    $destination=$emp->emp_jsa_id;
                    $destination_type="office";
                    $dist = calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance1 = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $jsa_miles=ceil($distance1-12);
                    $vehicle_travel=ceil($distance);
                }elseif(in_array($emp->employee_id,$driverfrom_arr))
                {
                    //Driver From
                    $driverfrom=1;
                    $origin=$emp->sStoreOrigin;
                    $origin_type="store";
                    $destination=$timesheet->store->id;
                    $destination_type="store";
                    $dist = calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $traveltime=$distance/50;
                    $traveltime = convertTimeToColon($traveltime);
                
                    $origin=$timesheet->store->id;
                    $origin_type="store";
                    $destination=$emp->emp_jsa_id;
                    $destination_type="office";
                    $dist = calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance1 = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $drivetime=$distance1/50;
                    $vehicle_travel=ceil($distance1);
                    $drivetime = convertTimeToColon($drivetime);
                }else{
                    $origin=$emp->sStoreOrigin;
                    $origin_type="store";
                    $destination=@$timesheet->store->id;
                    $destination_type="store";
                    $dist = calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $traveltime=$distance/50;
                    $traveltime = convertTimeToColon($traveltime);

                    $origin=@$timesheet->store->id;
                    $origin_type="store";
                    $destination=@$emp->emp_jsa_id;
                    $destination_type="office";
                    $dist = calDistance($origin, $origin_type, $destination, $destination_type);

                    //echo '<pre>';print_r($dist);
                    $distance1 = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $jsa_miles=ceil($distance1-12);
                }
            }else{
                if(in_array($emp->employee_id,$driverto_arr) && in_array($emp->employee_id,$driverfrom_arr))
                {
                    //Driver To and From
                    $driverto=1;
                    $driverfrom=1;
                    $origin=$emp->sStoreOrigin;
                    $origin_type="store";
                    $destination=@$timesheet->store->id;
                    $destination_type="store";
                    $dist = calDistance($origin, $origin_type, $destination, $destination_type);
//                    echo '<pre>'; print_r($dist);
                   //echo 'origin to store: ';
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    //echo '<br>';
                    
                    $origin=@$timesheet->store->id;
                    $origin_type="store";
                    $destination=$emp->emp_jsa_id;
                    $destination_type="office";
                    $dist = calDistance($origin, $origin_type, $destination, $destination_type);
                    //echo 'store to office: ';
                    $distance1 = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
//                    print_r($dist);
                    //echo '<br>';
                    $vehicle_travel=$distance+$distance1;
                    $drivetime=($distance+$distance1)/50;
                    $drivetime = convertTimeToColon($drivetime);
                }elseif(in_array($emp->employee_id,$driverto_arr))
                {
                    //Driver To
                    $driverto=1;
                    $origin=$emp->sStoreOrigin;
                    $origin_type="store";
                    $destination=$timesheet->store->id;
                    $destination_type="store";
                    $dist = calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $drivetime=$distance/50;
                    $drivetime = convertTimeToColon($drivetime);
                    
                    $origin=$timesheet->store->id;
                    $origin_type="store";
                    $destination=$emp->emp_jsa_id;
                    $destination_type="office";
                    $dist = calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance1 = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $jsa_miles=ceil($distance1-12);
                    $vehicle_travel=ceil($distance);
                }elseif(in_array($emp->employee_id,$driverfrom_arr))
                {
                    //Driver From
                    $driverfrom=1;
                    $origin=$emp->sStoreOrigin;
                    $origin_type="store";
                    $destination=$timesheet->store->id;
                    $destination_type="store";
                    $dist = calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $jsa_miles=ceil($distance);
                    
                
                    $origin=$timesheet->store->id;
                    $origin_type="store";
                    $destination=$emp->emp_jsa_id;
                    $destination_type="office";
                    $dist = calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance1 = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $drivetime=$distance1/50;
                    $drivetime = convertTimeToColon($drivetime);
                    $vehicle_travel=ceil($distance1);
                }else{
                    $origin=$emp->sStoreOrigin;
                    $origin_type="store";
                    $destination=@$timesheet->store->id;
                    $destination_type="store";
                    $dist = calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $jsa_miles=ceil($distance);

                    $origin=@$timesheet->store->id;
                    $origin_type="store";
                    $destination=@$emp->emp_jsa_id;
                    $destination_type="office";
                    $dist = calDistance($origin, $origin_type, $destination, $destination_type);

                    //echo '<pre>';print_r($dist);
                    $distance1 = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $jsa_miles+=ceil($distance1-12);
                }
            }
        }
        ?>
        <input type="hidden" name="employee_number[]" value="<?=@$emp->emp_number?>">
        <input type="hidden" name="employee_id[]" value="<?=@$emp->employee_id?>">
        <input type="hidden" name="bIsDriver[]" value="<?=@$emp->bIsDriver?>">
        <input type="hidden" name="store_id" value="<?=@$timesheet->store->id?>">
        <input type="hidden" name="emp_jsa[]" value="<?=@$emp->emp_jsa_id?>">
        <input type="hidden" name="emp_area[]" value="<?=@$emp->area_number?>">
        <input type="hidden" name="vehicle_travel[]" class="vehicle_travel<?=$emp->employee_id;?>" value="<?php echo (@$emp->bIsDriver)?@$vehicle_travel:'0'?>">
        <input type="hidden" name="dEmpPieces[]" value="<?=@$emp->dEmpPieces?>">
        <input type="hidden" name="dEmpCount[]" value="<?=@$emp->dEmpCount?>">
        <input type="hidden" name="iAttendanceFlag[]" class="iAttendanceFlag<?=$emp->employee_id;?>" value="<?=$emp->iAttendanceFlag;?>">
        
        <tr data_id="<?=@$emp->employee_id;?>">
            <td><a target="_blank" href="{{ route('admin.timesheets.employee_other_events',[$emp->employee_id,$timesheet->dtJobDate,$timesheet->id]) }}">
                <?php if(@$emp->last_name || @$emp->first_name){
                    echo @$emp->last_name.','.@$emp->first_name;
                    }else{echo @$emp->employee_ssn;}?><br>
                <?=@$emp->area_title?><br>
                </a>
                <a target="_blank" href="{{ route('admin.timesheets.gap_time',[$emp->employee_id,$timesheet->id]) }}">Gap Time</a><br>
                <?=attendance_flag($emp->iAttendanceFlag);?><br>
                <input class="exclude_employee" type="checkbox" name="exclude_employee[<?=$emp->emp_number;?>]" id="exclude_employee<?=$mainkey;?>" value="1"><label for="exclude_employee<?=$mainkey;?>">Exclude</label>
                
            </td>
            <td>
                 <?php
                $datetime1 = strtotime($emp->dtStopDateTime);
                $datetime2 = strtotime($emp->dtStartDateTime);
                $diff = abs($datetime1 - $datetime2)+(12*60)+($emp->PIMTime*60)+($emp->iWaitTime*60)-($emp->iLunch1*60)-($emp->iLunch2*60);  
                $hours = floor(($diff/ (60*60)));
                $minutes = floor(($diff - $hours*60*60)/ 60);  
                ?>
                <input name="store_hours[]" type="text" value="<?php if($emp->iAttendanceFlag!=3 && $emp->iAttendanceFlag!=4){echo $hours.':'.$minutes;}else{echo '00:00';}?>" readonly class="form-control store_hours<?=$emp->employee_id;?>">
                <input name="store_decimal_hours[]" type="hidden" value="<?php if($emp->iAttendanceFlag!=3 && $emp->iAttendanceFlag!=4){echo $hours.'.'.$minutes;}else{echo '0.0';}?>" class="form-control store_decimal_hours<?=$emp->employee_id;?>">
                <input type="hidden" value="<?=$datetime1?>" disabled="" class="datetime1<?=$emp->employee_id;?>">
                <input type="hidden" value="<?=$datetime2?>" disabled="" class="datetime2<?=$emp->employee_id;?>">
                <?php if($emp->bIsSuper==1){?>
                    <input type="hidden" value="{{$emp->area_number}}" name="supervisor_area_number">
                <?php }?>
                <div class="schedule_emp_status">
                    <input class="" type="checkbox" value="Supervisor" name="employee_status[<?=$emp->employee_id;?>][]" <?php if($emp->bIsSuper==1){echo 'checked="checked"';}?> id="supervisor_<?=$emp->employee_id;?>"><label for="supervisor_<?=$emp->employee_id;?>">Supervisor</label><br>
                    <input class="drivetime_change" affected_row="<?=$emp->employee_id;?>" type="checkbox" value="Driver To" name="employee_status[<?=$emp->employee_id;?>][]" <?php if($driverto==1){echo 'checked="checked"';}?> id="driver_to_<?=$emp->employee_id;?>"><label for="driver_to_<?=$emp->employee_id;?>">Driver To</label><br>
                    <input class="drivetime_change" affected_row="<?=$emp->employee_id;?>" type="checkbox" value="Driver From" name="employee_status[<?=$emp->employee_id;?>][]" <?php if($driverfrom==1){echo 'checked="checked"';}?> id="driver_from_<?=$emp->employee_id;?>"><label for="driver_from_<?=$emp->employee_id;?>">Driver From</label><br>
<!--                <input class="" type="checkbox" value="Driver To and From" name="employee_status[<?=$emp->employee_id;?>][]" <?php if($driverto==1 && $driverfrom==1){echo 'checked="checked"';}?> id="driver_to_from_<?=$emp->employee_id;?>"><label for="driver_to_from_<?=$emp->employee_id;?>">Driver To & From</label>-->
                </div>
            </td>

            <td>
                <input name="PIMTime[]" type="text" class="form-control schedule_change PIMTime<?=$emp->employee_id;?>" value="<?=$emp->PIMTime;?>"><br>
                <input name="iWaitTime[]" type="text" class="form-control schedule_change iWaitTime<?=$emp->employee_id;?>" value="<?=$emp->iWaitTime;?>">
                <?php if(in_array($emp->employee_id,$driverto_arr)){
                    $getLoggedToDriveTime = getLoggedToDriveTime($emp->employee_id,$timesheet->id);
                    $datetime1 = strtotime(@$getLoggedToDriveTime->dtToStoreEnd);
                    $datetime2 = strtotime(@$getLoggedToDriveTime->dtToStoreStart);
                    $diff = abs($datetime1 - $datetime2);  
                    $hours1 = floor($diff/3600);
                    $minutes1 = floor(($diff - ($hours1*3600))/60);
                    if($datetime1)
                        $driver_to_end = 'Drive to End- '.date('h:iA',(strtotime($getLoggedToDriveTime->dtToStoreEnd)));
                    else
                        $driver_to_end = '';
                    if($datetime1 && $datetime2)
                        $driver_to_total = 'Ttl Time- '.$hours1.':'.$minutes1;
                    else
                        $driver_to_total = '';
                    if($datetime2)
                        echo 'Drive to Start- '.date('h:iA',(strtotime($getLoggedToDriveTime->dtToStoreStart))).'<br>';
                }
                if(in_array($emp->employee_id,$driverfrom_arr)){
                    $getLoggedFromDriveTime = getLoggedFromDriveTime($emp->employee_id,$timesheet->id);
                    $datetime3 = strtotime(@$getLoggedFromDriveTime->dtFromStoreEnd);
                    $datetime4 = strtotime(@$getLoggedFromDriveTime->dtFromStoreStart);
                    $diff = abs($datetime3 - $datetime4);  
                    $hours2 = floor($diff/3600);
                    $minutes2 = floor(($diff - ($hours2*3600))/60);
                    if($datetime3)
                        $driver_from_end = 'Drive from End- '.date('h:iA',(strtotime($getLoggedFromDriveTime->dtFromStoreEnd)));
                    else
                        $driver_from_end = '';
                    if($datetime3 && $datetime4)
                        $driver_from_total = 'Ttl Time- '.$hours2.':'.$minutes2;
                    else
                        $driver_from_total = '';
                    if($datetime4)
                        echo 'Drive from Start- '.date('h:iA',(strtotime($getLoggedFromDriveTime->dtFromStoreStart))).'<br>';
                }
                echo @$emp->WaitTimeExplanation;
                ?>
            </td>
            <td>
                <?php $lunchflag = lunchFlag($emp->ss_no,$timesheet->idTimesheet_SQL,$emp->iLunch1,$emp->iLunch2);
                //print_r($lunch1flag);die;?>
                <input name="iGapTime[]" type="hidden" class="form-control iGapTime<?=$emp->employee_id;?>" value="<?=$emp->iGapTime;?>">
                <input name="iLunch1[]" type="text" class="lunchgap form-control schedule_change <?php if(isset($lunchflag['lunch1']) && $lunchflag['lunch1']){echo 'flagged';}?> iLunch1<?=$emp->employee_id;?>" value="<?=$emp->iLunch1;?>"><br>
                <input name="iLunch2[]" type="text" class="lunchgap form-control schedule_change <?php if(isset($lunchflag['lunch2']) && $lunchflag['lunch2']){echo 'flagged';}?> iLunch2<?=$emp->employee_id;?>" value="<?=$emp->iLunch2;?>">
                <?php if(in_array($emp->employee_id,$driverto_arr)){echo $driver_to_end.'<br>';}
                if(in_array($emp->employee_id,$driverfrom_arr)){echo $driver_from_end;}
                echo @$emp->GapTimeExplanation;
                ?>
            </td>
             <td>
                <input name="drive_time[]" type="text" class="drive_time<?=$emp->employee_id;?> form-control schedule_change validated_time" value="<?=$drivetime?>"><br>
                <input name="travel_time[]" type="text" class="form-control travel_time<?=$emp->employee_id;?> schedule_change validated_time" value="<?=$traveltime?>">
                <?php if(in_array($emp->employee_id,$driverto_arr)){echo $driver_to_total.'<br>';}
                if(in_array($emp->employee_id,$driverfrom_arr)){echo $driver_from_total;}?>
            </td>
            <td>
                <?=date('h:iA',(strtotime($emp->dtFirstScan)-360));?><br>
                <?=date('h:iA',(strtotime($emp->dtLastScan)+360));?>
            </td>
            <td>
                <select affected_row="<?=$emp->employee_id;?>" required="required" class="form-control select2 drivetime_change required" name="origin[]" >
                    <option value="">Select Store</option>
                    <?php foreach ($stores as $key=>$store){?>
                    <option value="<?php echo $key;?>" <?php if(isset($emp->origin) && strtolower($emp->sStoreOrigin)!="office" && $key==$emp->origin){echo 'selected="selected"';}?>><?php echo $store;?></option>
                    <?php }?>
                    <option value="OFFICE" <?php if(strtolower($emp->sStoreOrigin)=="office"){echo 'selected="selected"';}?>>OFFICE</option>
                </select><br>
                <select affected_row="<?=$emp->employee_id;?>" required="required" class="form-control select2 drivetime_change required" name="destination[]" >
                    <option value="">Select Store</option>
                    <?php foreach ($stores as $key=>$store){?>
                    <option value="<?php echo $key;?>" <?php if(isset($emp->destination) && strtolower($emp->sStoreReturn)!="office" && $key==$emp->destination){echo 'selected="selected"';}?>><?php echo $store;?></option>
                    <?php }?>
                    <option value="OFFICE" <?php if(strtolower($emp->sStoreReturn)=="office"){echo 'selected="selected"';}?>>OFFICE</option>
                </select>
            </td>
                        
            <td><input type="text" class="form-control jsa_miles<?=$emp->employee_id;?>" value="<?=($jsa_miles>0)?$jsa_miles:0?>" name="jsa_miles[]"></td>
        </tr>
        <?php    }?>
                </tbody>
                </table>
                Crew Count: <?=$present_emp?> <br> No Shows: <?=$absent_emp?>
            </div>
        </div>
    </div>
    <?php if($timesheet->is_flagged){?>
        <div class="row timesheet_rejection_reason hidden">
            <div class="col-xs-12 form-group">
                {!! Form::label('rejection_reason', 'Reason for Rejection', ['class' => 'control-label']) !!}
                {!! Form::textarea('rejection_reason', old('rejection_reason'), ['class' => 'form-control ']) !!}
            </div>
            <div class="col-xs-12"><button type="submit" name="submit" class="btn btn-danger" value="Reject">Reject Timesheet</button></div>
        </div><br>
        <div class="row">
            <div class="col-xs-12 form-group">
                <a href="{{ route('admin.timesheets.index') }}" class="btn btn-default">Back</a>
                <button type="submit" name="submit" class="btn btn-success timesheet_approval_button hide timesheet_submitted_check" value="Approve">Approve</button>
                <button type="button" class="btn btn-warning timesheet_rejection_button hide">Reject</button>
            </div>
        </div>
        
 <?php }else{?>
        <div class="row timesheet_rejection_reason hidden">
            <div class="col-xs-12 form-group">
                {!! Form::label('rejection_reason', 'Reason for Rejection', ['class' => 'control-label']) !!}
                {!! Form::textarea('rejection_reason', old('rejection_reason'), ['class' => 'form-control ']) !!}
            </div>
            <div class="col-xs-12"><button type="submit" name="submit" class="btn btn-danger" value="Reject">Reject Timesheet</button></div>
        </div><br>
        <div class="row">
            <div class="col-xs-12 form-group">
                <button type="submit" name="submit" class="btn btn-success timesheet_submitted_check" value="Approve">Approve</button>
                <button type="button" class="btn btn-warning timesheet_rejection_button">Reject</button>
            </div>
        </div>
        
        
<?php }?>
    {!! Form::close() !!}
@stop
@section('javascript')
    @parent
<script type="text/javascript">
    function myFunction(){
        if($("input.flagged").length)
            return confirm('Lunch1 or Lunch2 doesn\'t have a matching gap time, please confirm approval of this timesheet.');
        else
            return true;
    } 
 $(document).ready(function(){
    $('.exclude_employee').click(function(){
         $('.timesheet_rejection_button').removeClass('hide');
         $('.timesheet_approval_button').removeClass('hide');
     })
    $('form input:not([type="submit"])').keydown(function(e) {
        if (e.keyCode == 13) {
            var inputs = $(this).parents("form").eq(0).find(":input");
            if (inputs[inputs.index(this) + 1] != null) {                    
                inputs[inputs.index(this) + 1].focus();
            }
            e.preventDefault();
            return false;
        }
    });

    $('.timesheet_rejection_button').click(function(){
         $('.timesheet_rejection_reason').removeClass('hidden');
     })
    $('body').on('focus',".datepicker", function(){
        if( $(this).hasClass('hasDatepicker') === false )  {
            $(this).datepicker();
        }
    });
    $('.datepicker').datepicker({
        autoclose: true,
    })
    
    $(".validated_time").on('change',function(){
        //alert($(this).val());
        var time = $(this).val();
        var isValid = /^([0-1]?[0-9]|2[0-4]):([0-5][0-9])(:[0-5][0-9])?$/.test(time);
        if(!isValid)
        {
            alert('Wrong time format. Enter like hh:mm');
            $(this).val('00:00');
            return false;
        }
    })
    $(".schedule_change").on('change',function(){
        var data_id = $(this).parent().parent().attr('data_id');
        var datetime1 = $('.datetime1'+data_id).val();
        var datetime2 = $('.datetime2'+data_id).val();
        var PIMTime = $('.PIMTime'+data_id).val();
        var iWaitTime = $('.iWaitTime'+data_id).val();
        var iLunch1 = $('.iLunch1'+data_id).val();
        var iLunch2 = $('.iLunch2'+data_id).val();
        var iAttendanceFlag = $('.iAttendanceFlag'+data_id).val();
        var diff = Math.abs((datetime1 - datetime2)+(12*60)+(PIMTime*60)+(iWaitTime*60)-(iLunch1*60)-(iLunch2*60));  
        hours = Math.floor(parseFloat(diff/ 3600));
        minutes = Math.floor(parseFloat((diff - hours*60*60)/ 60));
        if(minutes<10)
            minutes = '0'+minutes;
        //alert(iAttendanceFlag);
        if(iAttendanceFlag!=3 && iAttendanceFlag!=4)
        {
            $('.store_hours'+data_id).val(hours+':'+minutes);
            $('.store_decimal_hours'+data_id).val(hours+'.'+minutes);
        }
     })

    $(".drivetime_change").on('change',function(){
        $('#affected_row').val($(this).attr('affected_row'));
        var data = $("#approve_timesheet").serialize();
        $.ajax({
            type:"POST",
            url:"{{url('admin/caldrivetime')}}",
            data:data,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success:function(res){
                //console.log(res);
                
                $.each(res,function(key,value){
                    
                    $.each(value,function(key1,value1){
//                        alert(value1);
//                        alert(key1);
                        //console.log(key1);console.log(value1);
                        $(".drive_time"+key1).val(value1.drivetime);
                        if(parseInt(value1.jsa_miles)>=0)
                            $(".jsa_miles"+key1).val(value1.jsa_miles);
                        if(parseInt(value1.vehicle_travel)>=0)
                            $(".vehicle_travel"+key1).val(value1.vehicle_travel);
                        $(".travel_time"+key1).val(value1.traveltime);
                    })
                   // $("#"+ele+"_city").append('<option value="'+key+'">'+value+'</option>');
                });
                
            }
        });
     })
     
    $(".lunchgap").on('change',function(){
        var employee_id = $(this).parent().parent().attr('data_id');
        var idTimesheet_SQL = '<?php echo $timesheet->idTimesheet_SQL;?>';
        var lunch1 = $('.iLunch1'+employee_id).val();
        var lunch2 = $('.iLunch2'+employee_id).val();
        $.ajax({
            type:"POST",
            url:"{{url('admin/callunchgap')}}",
            data:{employee_id:employee_id,idTimesheet_SQL:idTimesheet_SQL,lunch1:lunch1,lunch2:lunch2},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success:function(res){
                if(res.lunch1==0)
                    $('.iLunch1'+employee_id).removeClass('flagged');
                else
                    $('.iLunch1'+employee_id).addClass('flagged');
                if(res.lunch2==0)
                    $('.iLunch2'+employee_id).removeClass('flagged');
                else
                    $('.iLunch2'+employee_id).addClass('flagged');
            }
        });
     })
 })
 function validateHhMm(inputField) {
    var isValid = /^([0-1]?[0-9]|2[0-4]):([0-5][0-9])(:[0-5][0-9])?$/.test(inputField.value);

    if (isValid) {
      inputField.style.backgroundColor = '#bfa';
    } else {
      inputField.style.backgroundColor = '#fba';
    }
    $(this).val('00:00');
    return isValid;
  }
 </script>
@stop