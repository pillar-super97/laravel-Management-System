@extends('layouts.app')
@section('pageTitle', 'Edit Timesheet')
@section('content')
    <h3 class="page-title"></h3>
    
    {!! Form::model($timesheet, ['method' => 'POST','id'=>'approve_timesheet','route' => ['admin.timesheets.approve', $timesheet->id], 'files' => true,'autocomplete'=>'off']) !!}
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
                <div class="col-xs-4 form-group">
                    <?=$timesheet->store->name?><br>
                    <?=$timesheet->store->address?><br>
                    <?=$timesheet->store->city->name?>,<?=$timesheet->store->state->name?>, <?=$timesheet->store->zip?><br>
                </div>
                <div class="col-xs-4 form-group">
                  <?=$timesheet->store->number?><br>
                  <input type="text" name="pay_date" class="datepicker" value="<?=date('m/d/Y',strtotime($timesheet->dtJobDate))?>"><br>
                  <?=$timesheet->store->area_prime_responsibility->title?><br>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                    <th>Emp Name /<br> Area</th>
                    <th>Store Hours /<br> Status</th>
                    <th>PIM /<br> Wait</th>
                    <th>Lunch1 /<br> Lunch2</th>
                    <th>Drive Time /<br> Travel Time</th>
                    <th>Time in /<br> Time out</th>
                    <th>Origin /<br> Destination</th>
                    <th>JSA<br>(In Miles)</th>
                </thead>
                <tbody>
                    <?php 
    $vehicles = $timesheet->vehicles->toArray();
    //echo '<pre>';print_r($timesheet);die;
    foreach($timesheet->emp_data as $emp){
        //echo '<pre>';print_r($emp);die;
        $driverto=0;
        $driverfrom=0;
        $drivetime=0;
        $jsa_miles=0;
        $traveltime=0;
        if(strtolower($emp->sStoreReturn)!="office")
        {
            $origin=$timesheet->store->id;
            $origin_type="store";

            if(strtolower($emp->sStoreReturn)=="office")
            {
                $destination=$emp->employee->jsa->id;
                $destination_type="office";
            }else{
                $destination=$emp->sStoreReturn;
                $destination_type="store";
            }
            $dist = calDistance($origin, $origin_type, $destination, $destination_type);
            $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
            $traveltime=$distance/50;
        }

        foreach($vehicles as $vehicle)
        {
            if($vehicle['driver_to']==$emp->employee_id && $vehicle['driver_from']==$emp->employee_id)
            {
                if($emp->bIsDriver)
                {
                    if(strtolower($emp->sStoreOrigin)=="office" && strtolower($emp->sStoreReturn)=="office")
                    {
                        $origin=$emp->employee->jsa->id;
                        $origin_type="office";
                        $destination=$timesheet->store->id;
                        $destination_type="store";
                    }
                    if(strtolower($emp->sStoreOrigin)=="office" && strtolower($emp->sStoreReturn)!="office")
                    {
                        $origin=$emp->employee->jsa->id;
                        $origin_type="office";
                    }elseif(strtolower($emp->sStoreOrigin)!="office"){
                        $origin=$emp->sStoreOrigin;
                        $origin_type="store";
                    }
                    if(strtolower($emp->sStoreReturn)=="office" && strtolower($emp->sStoreOrigin)!="office")
                    {
                        $destination=$emp->employee->jsa->id;
                        $destination_type="office";
                    }elseif(strtolower($emp->sStoreReturn)!="office"){
                        $destination=$emp->sStoreReturn;
                        $destination_type="store";
                    }
                    //echo '<pre>';print_r(array($origin, $origin_type, $destination, $destination_type));
                    $dist = calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $drivetime=($distance*2)/50;
                    $jsa_miles=0;
                }
                $driverto=1;
                $driverfrom=1;
                break;
            }
            elseif($vehicle['driver_to']==$emp->employee_id && $vehicle['driver_from']!=$emp->employee_id)
            {
                if($emp->bIsDriver)
                {
                    if(strtolower($emp->sStoreOrigin)=="office")
                    {
                        $origin=$emp->employee->jsa->id;
                        $origin_type="office";
                    }else{
                        $origin=$emp->sStoreOrigin;
                        $origin_type="store";
                    }
                    $destination=$timesheet->store->id;
                    $destination_type="store";
                    $dist = calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $drivetime=($distance)/50;

                    $origin=$timesheet->store->id;
                    $origin_type="store";

                    if(strtolower($emp->sStoreReturn)=="office")
                    {
                        $destination=$emp->employee->jsa->id;
                        $destination_type="office";
                    }else{
                        $destination=$emp->sStoreReturn;
                        $destination_type="store";
                    }
                    $dist = calDistance($origin, $origin_type, $destination, $destination_type);
                    $jsa_miles = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                }
                $driverto=1;
                break;
            }
            elseif($vehicle['driver_to']!=$emp->employee_id && $vehicle['driver_from']==$emp->employee_id)
            {
                if($emp->bIsDriver)
                {

                    $origin=$timesheet->store->id;
                    $origin_type="store";

                    if(strtolower($emp->sStoreReturn)=="office")
                    {
                        $destination=$emp->employee->jsa->id;
                        $destination_type="office";
                    }else{
                        $destination=$emp->sStoreReturn;
                        $destination_type="store";
                    }
                    $dist = calDistance($origin, $origin_type, $destination, $destination_type);
                    $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                    $drivetime=($distance)/50;

                    if(strtolower($emp->sStoreOrigin)=="office")
                    {
                        $origin=$emp->employee->jsa->id;
                        $origin_type="office";
                    }else{
                        $origin=$emp->sStoreOrigin;
                        $origin_type="store";
                    }
                    $destination=$timesheet->store->id;
                    $destination_type="store";
                    $dist = calDistance($origin, $origin_type, $destination, $destination_type);
                    $jsa_miles = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                }
                $driverfrom=1;
                break;
            }else
                continue;
        }
        ?>
        <input type="hidden" name="employee_number[]" value="<?=@$emp->employee->emp_number?>">
        <input type="hidden" name="employee_id[]" value="<?=@$emp->employee->id?>">
        <input type="hidden" name="bIsDriver[]" value="<?=@$emp->bIsDriver?>">
        <input type="hidden" name="store_id" value="<?=@$timesheet->store->id?>">
        <input type="hidden" name="emp_jsa[]" value="<?=@$emp->employee->jsa->id?>">
        <input type="hidden" name="emp_area[]" value="<?=@$emp->employee->area->area_number?>">
        <tr data_id="<?=@$emp->employee_id;?>">
            <td>
                <?=@$emp->employee->last_name.','.@$emp->employee->first_name;?><br>
                <?=@$emp->employee->area->title?>
            </td>
            <td>
                 <?php
                $datetime1 = strtotime($emp->dtStopDateTime);
                $datetime2 = strtotime($emp->dtStartDateTime);
                $diff = abs($datetime1 - $datetime2)+(12*60)+($emp->PIMTime*60)+($emp->iWaitTime*60)-($emp->iLunch1*60)-($emp->iLunch2*60);  
                $hours = floor(($diff/ (60*60)));
                $minutes = floor(($diff - $hours*60*60)/ 60);  
                ?>
                <input name="store_hours[]" type="text" value="<?php echo $hours.':'.$minutes?>" readonly class="form-control store_hours<?=$emp->employee_id;?>">
                <input type="hidden" value="<?=$datetime1?>" disabled="" class="datetime1<?=$emp->employee_id;?>">
                <input type="hidden" value="<?=$datetime2?>" disabled="" class="datetime2<?=$emp->employee_id;?>">
                            
                <div class="schedule_emp_status">
                    <input class="drivetime_change" type="checkbox" value="Supervisor" name="employee_status[<?=$emp->employee_id;?>][]" <?php if($emp->bIsSuper==1){echo 'checked="checked"';}?> id="supervisor_<?=$emp->employee_id;?>"><label for="supervisor_<?=$emp->employee_id;?>">Supervisor</label><br>
                    <input class="drivetime_change" type="checkbox" value="Driver To" name="employee_status[<?=$emp->employee_id;?>][]" <?php if($driverto==1 && $driverfrom!=1){echo 'checked="checked"';}?> id="driver_to_<?=$emp->employee_id;?>"><label for="driver_to_<?=$emp->employee_id;?>">Driver To</label><br>
                    <input class="drivetime_change" type="checkbox" value="Driver From" name="employee_status[<?=$emp->employee_id;?>][]" <?php if($driverto!=1 && $driverfrom==1){echo 'checked="checked"';}?> id="driver_from_<?=$emp->employee_id;?>"><label for="driver_from_<?=$emp->employee_id;?>">Driver From</label><br>
<!--                            <input class="drivetime_change" type="checkbox" value="Driver To and From" name="employee_status[<?=$emp->employee_id;?>][]" <?php if($driverto==1 && $driverfrom==1){echo 'checked="checked"';}?> id="driver_to_from_<?=$emp->employee_id;?>"><label for="driver_to_from_<?=$emp->employee_id;?>">Driver To & From</label>-->
                </div>
            </td>

            <td>
                <input name="PIMTime[]" type="text" class="form-control schedule_change PIMTime<?=$emp->employee_id;?>" value="<?=$emp->PIMTime;?>"><br>
                <input name="iWaitTime[]" type="text" class="form-control schedule_change iWaitTime<?=$emp->employee_id;?>" value="<?=$emp->iWaitTime;?>">
            </td>
            <td>
                <input name="iLunch1[]" type="text" class="form-control schedule_change iLunch1<?=$emp->employee_id;?>" value="<?=$emp->iLunch1;?>"><br>
                <input name="iLunch2[]" type="text" class="form-control schedule_change iLunch2<?=$emp->employee_id;?>" value="<?=$emp->iLunch2;?>">
            </td>
             <td>
                <input name="drive_time[]" type="text" class="drive_time<?=$emp->employee_id;?> form-control schedule_change" value="<?=$drivetime?>"><br>
                <input name="travel_time[]" type="text" class="form-control travel_time<?=$emp->employee_id;?> schedule_change" value="<?=$traveltime?>">
            </td>
            <td>
                <?=date('h:iA',(strtotime($emp->dtFirstScan)-360));?><br>
                <?=date('h:iA',(strtotime($emp->dtLastScan)+360));?>
            </td>
            <td>
                <select id="store_id" class="form-control select2 drivetime_change" name="origin[]" >
                    <option value="">Select Store</option>
                    <?php foreach ($stores as $key=>$store){?>
                    <option value="<?php echo $key;?>" <?php if(isset($emp->store->id) && strtolower($emp->sStoreOrigin)!="office" && $key==$emp->store->id){echo 'selected="selected"';}?>><?php echo $store;?></option>
                    <?php }?>
                    <option value="OFFICE" <?php if(strtolower($emp->sStoreOrigin)=="office"){echo 'selected="selected"';}?>>OFFICE</option>
                </select><br>
                <select id="store_id" class="form-control select2 drivetime_change" name="destination[]" >
                    <option value="">Select Store</option>
                    <?php foreach ($stores as $key=>$store){?>
                    <option value="<?php echo $key;?>" <?php if(isset($emp->destination->id) && strtolower($emp->sStoreReturn)!="office" && $key==$emp->destination->id){echo 'selected="selected"';}?>><?php echo $store;?></option>
                    <?php }?>
                    <option value="OFFICE" <?php if(strtolower($emp->sStoreReturn)=="office"){echo 'selected="selected"';}?>>OFFICE</option>
                </select>
            </td>
                        
            <td><input type="text" class="form-control jsa_miles<?=$emp->employee_id;?>" value="<?=$jsa_miles?>" name="jsa_miles[]"></td>
        </tr>
        <?php    }?>
                </tbody>
                </table>
            </div>
            
            
        </div>
    </div>

    <button type="submit" name="submit" class="btn btn-success" value="Approve">Approve</button>
    <button type="submit" name="submit" class="btn btn-danger" value="Reject">Reject</button>
    {!! Form::close() !!}
@stop
@section('javascript')
    @parent
<script type="text/javascript">
 $(document).ready(function(){
    $('body').on('focus',".datepicker", function(){
        if( $(this).hasClass('hasDatepicker') === false )  {
            $(this).datepicker();
        }
    });
    $('.datepicker').datepicker({
        autoclose: true,
        
    })
    $(".schedule_change").on('change',function(){
         var data_id = $(this).parent().parent().attr('data_id');
        var datetime1 = $('.datetime1'+data_id).val();
        var datetime2 = $('.datetime2'+data_id).val();
        var PIMTime = $('.PIMTime'+data_id).val();
        var iWaitTime = $('.iWaitTime'+data_id).val();
        var iLunch1 = $('.iLunch1'+data_id).val();
        var iLunch2 = $('.iLunch2'+data_id).val();
        var diff = Math.abs((datetime1 - datetime2)+(12*60)+(PIMTime*60)+(iWaitTime*60)-(iLunch1*60)-(iLunch2*60));  
        hours = Math.floor(parseFloat(diff/ 3600));
        minutes = Math.floor(parseFloat((diff - hours*60*60)/ 60));
        if(minutes<10)
            minutes = '0'+minutes;
        //alert(minutes);
        $('.store_hours'+data_id).val(hours+':'+minutes);
     })

     $(".drivetime_change").on('change',function(){
        var data = $("#approve_timesheet").serialize();
        $.ajax({
            type:"POST",
            url:"{{url('admin/caldrivetime')}}/",
            data:data,
//            headers: {
//                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//            },
            success:function(res){
                //console.log(res);
                
                $.each(res,function(key,value){
                    
                    $.each(value,function(key1,value1){
//                        alert(value1);
//                        alert(key1);
//console.log(key1);console.log(value1);
                        $(".drive_time"+key1).val(value1.drivetime);
                        $(".jsa_miles"+key1).val(value1.jsa_miles);
                        $(".travel_time"+key1).val(value1.traveltime);
                    })
                   // $("#"+ele+"_city").append('<option value="'+key+'">'+value+'</option>');
                });
                
            }
        });
     })

 })
</script>
@stop