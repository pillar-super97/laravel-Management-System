@extends('layouts.app')
@section('pageTitle', 'View Timesheet')
@section('content')
  
    {!! Form::model($timesheet, ['method' => 'PUT', 'route' => ['admin.timesheets.update', $timesheet->id], 'files' => true,'autocomplete'=>'off']) !!}

    <div class="panel panel-default">
        <div class="panel-heading">View Timesheet</div>
         <div class="panel-body">
            <div class="row">
                <div class="col-xs-3 form-group">
                    <?=@$timesheet->store->name?><br>
                    <?=@$timesheet->store->address?><br>
                    <?=@$timesheet->store->city->name?>,<?=@$timesheet->store->state->name?>, <?=@$timesheet->store->zip?><br>
                </div>
                <div class="col-xs-3 form-group">
                  <?=date('m/d/Y',strtotime($timesheet->dtJobDate))?><br>
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
                    <b>Arrived:</b> <?=(strtotime($timesheet->InvRecapArvlTime))?date('H:i:s',strtotime($timesheet->InvRecapArvlTime)):''?>
                </div>
                <div class="col-xs-3 form-group">
                    <b>Started:</b> <?=(strtotime($timesheet->InvRecapStartTime))?date('H:i:s',strtotime($timesheet->InvRecapStartTime)):''?>
                </div>
                <div class="col-xs-3 form-group">
                    <b>End:</b> <?=(strtotime($timesheet->InvRecapEndTime))?date('H:i:s',strtotime($timesheet->InvRecapEndTime)):''?>
                </div>
                <div class="col-xs-3 form-group">
                    <b>Out:</b> <?=(strtotime($timesheet->InvRecapWrapTime))?date('H:i:s',strtotime($timesheet->InvRecapWrapTime)):''?>
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
    foreach($vehicles as $vehicle)
    {
        $driverto_arr[]=$vehicle['driver_to'];
        $driverfrom_arr[]=$vehicle['driver_from'];
    }
    //$employees = $timesheet->emp_data->toArray();
    //echo '<pre>';print_r($employees);die;
    $present_emp=0;
    $absent_emp=0;
    foreach($employees as $emp){
        //echo '<pre>';print_r($emp);die;
        if($emp->iAttendanceFlag==3 || $emp->iAttendanceFlag==4)
            $absent_emp++;
        else
            $present_emp++;
        ?>
        
        <tr data_id="<?=@$emp->employee_id;?>">
            <td>
                <?php if(@$emp->last_name || @$emp->first_name){
                    echo @$emp->last_name.','.@$emp->first_name;
                    }else{echo @$emp->employee_id;}?><br>
                <?=@$emp->area_title?><br>
                <?=attendance_flag($emp->iAttendanceFlag);?>
            </td>
            <td>
                 <?php
                $datetime1 = strtotime($emp->dtStopDateTime);
                $datetime2 = strtotime($emp->dtStartDateTime);
                $diff = abs($datetime1 - $datetime2)+(12*60)+($emp->PIMTime*60)+($emp->iWaitTime*60)-($emp->iLunch1*60)-($emp->iLunch2*60);  
                $hours = floor(($diff/ (60*60)));
                $minutes = floor(($diff - $hours*60*60)/ 60);  
                ?>
                <input type="text" value="<?=@$approved[$emp->employee_id]['store_hours']?>" disabled class="form-control">
                
                            
                <div class="schedule_emp_status">
                    <?php if(@$approved[$emp->employee_id]['is_supervisor']){echo 'Supervisor';}?><br>
                    <?php if(@$approved[$emp->employee_id]['driver_to']==1){echo 'Driver To';}?><br>
                    <?php if(@$approved[$emp->employee_id]['driver_from']==1){echo 'Driver From';}?><br>
                </div>
            </td>

            <td>
                <input type="text" class="form-control" value="<?=$emp->PIMTime;?>" disabled><br>
                <input type="text" class="form-control" value="<?=$emp->iWaitTime;?>" disabled>
                <?php if(in_array($emp->employee_id,$driverto_arr)){
                    $getLoggedToDriveTime = getLoggedToDriveTime($emp->employee_id,$timesheet->id);
                    $datetime1 = strtotime($getLoggedToDriveTime->dtToStoreEnd);
                    $datetime2 = strtotime($getLoggedToDriveTime->dtToStoreStart);
                    $diff = abs($datetime1 - $datetime2);  
                    $hours1 = floor($diff/3600);
                    $minutes1 = (strlen(floor(($diff - ($hours1*3600))/60))==1)?'0'.floor(($diff - ($hours1*3600))/60):floor(($diff - ($hours1*3600))/60);
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
                    //echo $emp->employee_id;die;
                    $getLoggedFromDriveTime = getLoggedFromDriveTime($emp->employee_id,$timesheet->id);
                    $datetime3 = strtotime(@$getLoggedFromDriveTime->dtFromStoreEnd);
                    $datetime4 = strtotime(@$getLoggedFromDriveTime->dtFromStoreStart);
                    $diff = abs($datetime3 - $datetime4);  
                    $hours2 = floor($diff/3600);
                    $minutes2 = (strlen(floor(($diff - ($hours2*3600))/60))==1)?'0'.floor(($diff - ($hours2*3600))/60):floor(($diff - ($hours2*3600))/60);
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
                }?>
            </td>
            <td>
                <input type="text" class="form-control" value="<?=$emp->iLunch1;?>" disabled><br>
                <input type="text" class="form-control" value="<?=$emp->iLunch2;?>" disabled>
                <?php if(in_array($emp->employee_id,$driverto_arr)){echo $driver_to_end.'<br>';}
                if(in_array($emp->employee_id,$driverfrom_arr)){echo $driver_from_end;}?>
            </td>
             <td>
                <input type="text" class="form-control" value="<?=@$approved[$emp->employee_id]['drive_time']?>" disabled><br>
                <input type="text" class="form-control" value="<?=@$approved[$emp->employee_id]['travel_time']?>" disabled>
                <?php if(in_array($emp->employee_id,$driverto_arr)){echo $driver_to_total.'<br>';}
                if(in_array($emp->employee_id,$driverfrom_arr)){echo $driver_from_total;}?>
            </td>
            <td>
                <?=date('h:iA',(strtotime($emp->dtFirstScan)-360));?><br>
                <?=date('h:iA',(strtotime($emp->dtLastScan)+360));?>
            </td>
            <td>
<?php if(@strtolower($approved[$emp->employee_id]['origin'])=="office"){echo 'OFFICE';}else{echo store_name_from_id(@$approved[$emp->employee_id]['origin']);}?>
                <br><br>
<?php if(@strtolower($approved[$emp->employee_id]['destination'])=="office"){echo 'OFFICE';}else{echo store_name_from_id(@$approved[$emp->employee_id]['destination']);}?>                
            </td>
                        
            <td><input type="text" class="form-control" value="<?=@$approved[$emp->employee_id]['jsa_miles']?>" disabled></td>
        </tr>
        <?php    }?>
                </tbody>
                </table>
                <div class="col-xs-3 form-group">
                    Crew Count: <?=$present_emp?> <br> No Shows: <?=$absent_emp?>
                </div>
                <div class="col-xs-3 form-group">
                    Approved By: <?=@$timesheet->approval_detail->name?><br>
                    Approved On: <?=($timesheet->approved_on)?date('m/d/Y',strtotime($timesheet->approved_on)):''?>
                </div>
            </div>
            
            
        </div>
    </div>

   <a href="{{ url()->previous() }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
@stop
@section('javascript')
    @parent
  
@stop