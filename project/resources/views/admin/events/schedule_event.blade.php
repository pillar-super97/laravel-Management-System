@extends('layouts.app')
@section('pageTitle', 'Schedule Event')
@section('content')
<div class="panel panel-default">
    <div class="panel-heading">Schedule Event</div>
    <div class="error-container">
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
    <?php 
    $schedule_employees = $event->schedule_employees->toArray();
    $employees_assigned = array();
    foreach($schedule_employees as $schedule_employee)
        $employees_assigned[$schedule_employee['employee_id']] = $schedule_employee;
    //echo '<pre>';print_r($employees_assigned);die;
    ?>
        <div class="panel-body">
            <div class="row">
                {!! Form::open(['method' => 'POST', 'route' => ['admin.events.save_schedule_event'],'id'=>'event_schedule_form', 'files' => true]) !!}
                <input type="hidden" name="rate_per" class="rate_per" value="<?=$event->store->rate_per?>">
                <input type="hidden" name="rate" class="rate" value="<?=$event->store->rate?>">
                <input type="hidden" name="last_inventory_value" class="last_inventory_value" value="<?php if($historical_data && $historical_data->last_inventory_value){echo $historical_data->last_inventory_value;}else{echo $event->store->inventory_level;}?>">
                <input type="hidden" name="store_billing" class="store_billing">
                <input type="hidden" name="event_id" class="event_id" value="<?=$event->id?>">
                <input type="hidden" name="store_spf" class="store_spf" value="<?=$event->store->spf?>">
    <div class="col-lg-9 col-md-12">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
                    
                    <div class="col-lg-4 col-md-4 col-sm-6 empViewtField">
                        <div class="row">
                            <div class="col-lg-12 schedule_label">Event Date</div>
                            <div class="col-lg-12"><div class="readable-textfield"><?=date('m/d/Y',strtotime($event->date))?> <i class="fa fa-calendar pull-right" aria-hidden="true"></i></div></div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 empViewtField">
                        <div class="row">
                            <div class="col-lg-12 schedule_label">Start Time</div>
                            <div class="col-lg-12"><div class="readable-textfield event_start_time"><?=date('h:i A',strtotime($event->start_time))?></div></div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 empViewtField">
                        <div class="row">
                            <div class="col-lg-12 schedule_label">Travel</div>
                            <div class="col-lg-12"><div class="readable-textfield">
                                <?php   if($event->road_trip!="No")echo 'R/T ';
                                        if($event->overnight!="No")echo 'O/N ';?>
                            </div></div>
                        </div>
                    </div>


                    <div class="col-lg-4 col-md-4 col-sm-6 empViewtField">
                        <div class="row">
                            <div class="col-lg-12 schedule_label">Store Information</div>
                            <div class="col-lg-12"><div class="readable-textfield">
                                        <?php echo $event->store->name.'<br>';
                                            echo ($event->store->manager_id)?$event->store->manager_id.'<br>':'';
                                            echo ($event->store->address)?$event->store->address.'<br>':'';
                                            echo @$event->store->city->name.', '.@$event->store->state->state_code.' '.@$event->store->zip.'<br>';
                                            echo @$event->store->phone.'<br>';?>
                            </div></div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 empViewtField">
                        <div class="row">
                            <div class="col-lg-12 schedule_label">Count Type</div><div class="col-lg-12"><?=@$event->store->inv_type?></div>
                            <div class="col-lg-12 schedule_label">Count RX</div><div class="col-lg-12"><?=@$event->count_rx?></div>
                            <div class="col-md-12 schedule_label">Count Backroom</div><div class="col-lg-12"><?=$event->count_backroom;?></div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 empViewtField">
                        <div class="row">
                            <div class="col-lg-12 schedule_label">Lodging Confirmed?<br>
                                <div class="readable-redio"><input name="loading_confirmed" type="radio" value="Yes" <?php if(@$event->event_schedule_data->loading_confirmed=="Yes"){echo 'checked="checked"';}?>> Yes</div>
                                <div class="readable-redio"><input name="loading_confirmed" type="radio" value="No" <?php if(@$event->event_schedule_data->loading_confirmed=="No"){echo 'checked="checked"';}?>> No</div>
                            </div>
                            <div class="col-lg-12 schedule_label">Max Ct Length</div><div class="col-lg-12"><?=@$event->store->max_length?></div>
                            <div class="col-md-12 schedule_label">Scheduled Ct Length<br>
                                <input type="text" readonly="readonly" name="schedule_length" class="cus-formControl btn-block schedule_length" value="<?=@$event->event_schedule_data->schedule_length;?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-6 empViewtField">
                        <div class="row">
                            <div class="col-lg-12 schedule_label">Inventory Level</div>
                            <div class="col-lg-12"><div class="readable-textfield"><?='$'.number_format($event->store->inventory_level)?></div></div>
                            <input type="hidden" class="inventory_level" value="<?=$event->store->inventory_level?>">
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 empViewtField">
                        <div class="row">
                            <div class="col-lg-12 schedule_label">Benchmark</div>
                            <div class="col-lg-12"><div class="readable-textfield"><?=number_format(@$event->store->benchmark)?></div></div>
                            <input type="hidden" class="store_benchmark" value="<?=@$event->store->benchmark?>">
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 empViewtField">
                        <div class="row">
                            <div class="col-lg-12 schedule_label">Scheduled Production</div>
                            <div class="col-lg-12"><input type="text" readonly="readonly" name="scheduled_production" class="cus-formControl btn-block scheduled_production" value="<?=@$event->event_schedule_data->scheduled_production;?>"></div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-sm-8 empViewtField">
                        <div class="col-lg-6 schedule_label">Area Assigned <div style="float: right;color:red;cursor:pointer" class="add_more_area"><i class="fa fa-plus" aria-hidden="true"></i> Assign Area</div> </div>
                        <div class="col-lg-6 schedule_label">Meet Time</div>
                        <?php $round_trip_miles=0;
                            $event_time_zone= getTimezone($event->store->city->name.', '.$event->store->state->name);
                            foreach($event->areas as $area){
                                $dist = calDistance($area->area->id,'area',$event->store->id,'store');
                                //echo '<pre>';print_r($dist);
                                $duration = (int)($dist->rows[0]->elements[0]->duration->value+($dist->rows[0]->elements[0]->duration->value/3600)*15*60);
                                $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                                $round_trip_miles+=$distance*2;
                                //echo '<pre>';print_r($dist);
                                $currentTime = strtotime($event->start_time);
                                $area_time_zone= getTimezone($area->area->city->name.', '.$area->area->state->name);
                            //echo '<pre>';print_r($area->meet_time);
                            if($area->meet_time && $area_time_zone==$event_time_zone){
                                $datetime = date("h:i A",strtotime($area->meet_time));
                                $timezonealert='';
                                //die('a');
                            }elseif($area->meet_time && $area_time_zone!=$event_time_zone){
                                //$datetime = converToTz(date("h:i A", $area->area->meet_time),$area_time_zone,$event_time_zone);
                                $datetime = date("h:i A",strtotime($area->meet_time));
                                $timezonealert='<div class="error-block">Different Timezone</div>';
                                //die('b');
                            }elseif($area_time_zone!=$event_time_zone){
                                //echo 'test';
                                $datetime = converToTz(date("h:i A", $currentTime-$duration),$area_time_zone,$event_time_zone);
                                $timezonealert='<div class="error-block">Different Timezone</div>';
                                //die('c');
                            }else{
                                $datetime = date("h:i A", $currentTime-$duration);
                                $timezonealert='';
                                //die('d');
                            }
                            ?>
                        <div class="row">
                            <div class="col-lg-6"><div class="readable-textfield"><?=$area->area->title;?></div></div>
                            <div class="col-lg-6"><div class=""><input required="" value="<?=$datetime?>" type="text" name="area_meet_time[<?=$area->area->id;?>]" class="cus-formControl btn-block required timepicker col-lg-3"><?=$timezonealert?></div></div>
                        </div>
                        <?php }?>
                        <div class="extra_area_container">
                            
                        </div>
                        <input type="hidden" class="additional_area_counter" name="additional_area_counter" value="1">
                        <input type="hidden" name="round_trip_miles" class="round_trip_miles" value="<?=$round_trip_miles?>">
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 empViewtField">
                        <div class="row">
                            <div class="col-lg-12 schedule_label">Gross Profit<br>
                                <input type="text" readonly="readonly" name="gross_profit" class="cus-formControl btn-block gross_profit" value="<?=@$event->event_schedule_data->gross_profit;?>">
                            </div>
                            <div class="col-lg-12 schedule_label">Labor<br>
                                <input type="text" readonly="readonly" name="labor_percent" class="cus-formControl btn-block labor_percent" value="<?=@$event->event_schedule_data->labor_percent;?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="row">
                            <div class="col-lg-4 schedule_label">Field Notes</div>
                            <div class="col-lg-8"><input type="text" name="field_notes" class="form-control" value="<?=@$event->event_schedule_data->field_notes;?>"></div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 schedule_label">Schedule Comments:</div>
                            <div class="col-lg-8"><?=$event->schedule_notes;?></div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 schedule_label">Store Notes:</div>
                            <div class="col-lg-8"><?=@$event->store->notes;?></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 schedule_label">
                        Total Ctrs assigned by Area:
                    </div>
                    <div class="col-md-6 area_wise_employee_count">
                        
                    </div>
                    <div class="col-md-3">
                        Minimum Required ##: {{$event->store->min_auditors}}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading"><strong>Current Crew Assigned</strong></div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table table-striped scheduled_employees_container">
                                                <thead>
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Task</th>
                                                        <th>Benchmark</th>
                                                        <th>Vehicle</th>
                                                        <th>Comments</th>
                                                        <th style="text-align: center !important">Unassign</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php 
                            if(count($employees_assigned))
                            {
                                foreach($event->schedule_employees as $emp)
                                {?>
                                    <tr id="assign_emp_<?=@$emp->employee_id?>"> 
                                        <input type="hidden" name="emp_id[]" value="<?=@$emp->employee_id?>">
                                        <td><?=@$emp->employee->name?></td>
                                        <td><select class="form-control formControl-dorpdown emp_task" name="task[]">
                                            <option <?php if(@$emp->task=="Auditor"){echo 'selected="selected"';}?>>Auditor</option>
                                            <option <?php if(@$emp->task=="Driver To"){echo 'selected="selected"';}?>>Driver To</option>
                                            <option <?php if(@$emp->task=="Driver From"){echo 'selected="selected"';}?>>Driver From</option>
                                            <option <?php if(@$emp->task=="Driver To & From"){echo 'selected="selected"';}?>>Driver To & From</option>
                                            <option <?php if(@$emp->task=="RX"){echo 'selected="selected"';}?>>RX</option>
                                            <option <?php if(@$emp->task=="RX/Driver To"){echo 'selected="selected"';}?>>RX/Driver To</option>
                                            <option <?php if(@$emp->task=="RX/Driver From"){echo 'selected="selected"';}?>>RX/Driver From</option>
                                            <option <?php if(@$emp->task=="RX/Driver To & From"){echo 'selected="selected"';}?>>RX/Driver To & From</option>
                                            <option <?php if(@$emp->task=="Supervisor"){echo 'selected="selected"';}?>>Supervisor</option>
                                            <option <?php if(@$emp->task=="Super/Driver To"){echo 'selected="selected"';}?>>Super/Driver To</option>
                                            <option <?php if(@$emp->task=="Super/Driver From"){echo 'selected="selected"';}?>>Super/Driver From</option>
                                            <option <?php if(@$emp->task=="Super/Driver To & From"){echo 'selected="selected"';}?>>Super/Driver To & From</option>
                                        </select>
                <input type="hidden" class="emp_hourly_rate" name="emp_hourly_rate[<?=@$emp->employee_id?>]" value="<?=number_format(str_replace("$","",@$emp->employee->payrate),2)?>">
                </td><td><span class="employee_benchmark"><?=@$emp->employee->benchmark?></span>%</td>
                <td><input type="text" class="cus-formControl greyedout emp_vehicle<?=@$emp->employee_id?>" name="vehicle[]" value="<?=@$emp->vehicle_number?>">
                        &nbsp;&nbsp;<i data-id="<?=@$emp->employee_id?>" class="fa fa-check commentcheck<?=@$emp->employee_id?>" aria-hidden="true" style="display:none;"></i>
                        &nbsp;&nbsp;<i data-id="<?=@$emp->employee_id?>" class="fa fa-pencil-square-o commentpencil<?=@$emp->employee_id?>" aria-hidden="true"></i>
                </td>
                <td><input type="text" value="<?=@$emp->comment?>" name="comment[]" class="cus-formControl" data-id="<?=@$emp->employee_id?>">
                    <input type="text" name="custom_comment[]" data-id="<?=@$emp->employee_id?>" class="form-control custom_comment<?=@$emp->employee_id?>" style="display:<?php if(@$emp->custom_comment!="")echo 'block';else echo 'none';?>"></td>
                <td align="center" class="unassign_employee" data-id="<?=@$emp->employee_id?>"><i class="fa fa-minus-circle" aria-hidden="true"></i></td>
                                    </tr>
                <?php          }
                            }
                                                ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>	
                            </div>	
                        </div>	
                    </div>
                </div>
                <!--start Historical Info.-->

                
            </div>	
        </div>
        <div class="row historicalInfo">

                    <div class="col-lg-12">
                        <h3 class="historicalInfoHead"><strong>Historical Info.:</strong></h3>
                    </div>
                    <?php $historical_data_from_timesheet = historical_data_by_event_date($event->store->id,$event->date);?>
                    <div class="col-md-3 col-sm-6 empViewtField">
                        <div class="row">
                            <div class="col-lg-12 schedule_label">Supervisor</div>
                            <div class="col-lg-12"><div class="readable-textarea"><?=event_supervisor_by_event_id(@$historical_data->id)?></div></div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 empViewtField">
                        <div class="row">
                            <div class="col-lg-12 schedule_label">Positive Experience?</div>
                            <div class="col-lg-12"><div class="readable-textarea"><?=@$historical_data->positive_exp?></div></div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 empViewtField">
                        <div class="row">
                            <div class="col-lg-12 schedule_label">QC Comments:</div>
                            <div class="col-lg-12"><div class="readable-textarea"><?=@$historical_data->qc_comment?></div></div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 empViewtField">
                        <div class="row">
                            <div class="col-lg-12 schedule_label">Production:</div>
                            <div class="col-lg-12"><div class="readable-textarea"><?=@$historical_data_from_timesheet['production'];?></div></div>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6 empViewtField">
                        <div class="row">
                            <div class="col-xs-6">
                                <div class="row">
                                    <div class="col-lg-12 schedule_label">Ct Length:</div>
                                    <div class="col-lg-12"><div class="readable-textfield"><?=@$historical_data_from_timesheet['count_length'];?></div></div>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="row">
                                    <div class="col-lg-12 schedule_label">Crew Count:</div>
                                    <div class="col-lg-12"><div class="readable-textfield"><?=@$historical_data_from_timesheet['crew_count']?></div></div>
                                </div>
                            </div>
                        </div>	
                    </div>
                    <div class="col-md-4 col-sm-6 empViewtField">
                        <div class="row">
                            <div class="col-lg-12 schedule_label">Comments/Notes:</div>
                            <div class="col-lg-12">
                                <div class="readable-textarea">
                                    <?=@$historical_data_from_timesheet['comments']?><br>
                                    <?php //@$historical_data->field_comment?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
    </div>

    <div class="col-lg-3 col-md-12">
        <div class="row">
            <div class="error-block col-md-12">*Employees in red are already scheduled for this date.</div>
            <div class="col-lg-12 col-md-6 col-sm-12">
                
                <div class="panel panel-default">
                    
                    <div class="panel-heading"><strong>Available Employees Listing</strong></div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th align="center">Assign</th>
                                                <th>M/F</th>
                                                <th>Employee</th>
                                                <th>Area</th>
                                                <th>Leader</th>
                                                <th>Driver</th>
                                                <th>RX</th>
                                                <th>Bench</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($available_employees as $employee){?>
                                            <tr id="emp<?=$employee->id?>" <?php if(array_key_exists($employee->id,$employees_assigned)){echo 'class="disbleTableRow"';}?>>
                                                <td align="center" style="cursor: <?php if(array_key_exists($employee->id,$employees_assigned)){echo 'auto';}else{echo 'pointer';}?>;" class="<?php if(!array_key_exists($employee->id,$employees_assigned)){echo 'assign_employee';}?>" data-id="<?=$employee->id?>"><i class="fa fa-user-plus" aria-hidden="true"></i></td>
                                                <td><?=$employee->gender?></td>
                                                <td style="<?php if(in_array($employee->id, $already_scheduled_emp_arr)){echo 'color:red';}?>"><?=$employee->name?></td>
                                                <td><?=$employee->emparea?></td>
                                                <td><?php echo ($employee->is_crew_leader)?'Y':'';?></td>
                                                <td><?php echo ($employee->is_driver)?'Y':'';?></td>
                                                <td><?php echo ($employee->is_rx)?'Y':'';?></td>
                                                
                                                <td><?=$employee->benchmark?>%</td>
                                            </tr>
                                            <?php }?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>	
                    </div>	
                </div>
            </div>
            <div class="col-lg-12 col-md-6 col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading"><strong>Other Available Employees</strong></div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th align="center">Assign</th>
                                                <th>M/F</th>
                                                <th>Employees</th>
                                                <th>Area</th>
                                                <th>Leader</th>
                                                <th>Driver</th>
                                                <th>RX</th>
                                                <th>Bench</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($other_employees as $employee){?>
                                            <tr id="emp<?=$employee->id?>" <?php if(array_key_exists($employee->id,$employees_assigned)){echo 'class="disbleTableRow"';}?>>
                                                <td align="center" style="cursor: <?php if(array_key_exists($employee->id,$employees_assigned)){echo 'auto';}else{echo 'pointer';}?>;" class="<?php if(!array_key_exists($employee->id,$employees_assigned)){echo 'assign_employee';}?>" data-id="<?=$employee->id?>"><i class="fa fa-user-plus" aria-hidden="true"></i></td>
                                                <td><?=$employee->gender?></td>
                                                <td style="<?php if(in_array($employee->id, $already_scheduled_emp_arr)){echo 'color:red';}?>"><?=$employee->name?></td>
                                                <td><?=$employee->emparea?></td>
                                                <td><?php echo ($employee->is_crew_leader)?'Y':'';?></td>
                                                <td><?php echo ($employee->is_driver)?'Y':'';?></td>
                                                <td><?php echo ($employee->is_rx)?'Y':'';?></td>
                                                <td><?=$employee->benchmark?>%</td>
                                            </tr>
                                            <?php }?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>	
                    </div>	
                </div>
            </div>
            <div class="col-lg-12 col-md-6 col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading"><strong>Other Inactive Employees</strong></div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th align="center">Assign</th>
                                                <th>M/F</th>
                                                <th>Employees</th>
                                                <th>Area</th>
                                                <th>Leader</th>
                                                <th>Driver</th>
                                                <th>RX</th>
                                                <th>Bench</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($inactive_employees as $employee){?>
                                            <tr id="emp<?=$employee->id?>" <?php if(array_key_exists($employee->id,$employees_assigned)){echo 'class="disbleTableRow"';}?>>
                                                <td align="center" style="cursor: <?php if(array_key_exists($employee->id,$employees_assigned)){echo 'auto';}else{echo 'pointer';}?>;" class="<?php if(!array_key_exists($employee->id,$employees_assigned)){echo 'assign_employee';}?>" data-id="<?=$employee->id?>"><i class="fa fa-user-plus" aria-hidden="true"></i></td>
                                                <td><?=$employee->gender?></td>
                                                <td style="<?php if(in_array($employee->id, $already_scheduled_emp_arr)){echo 'color:red';}?>"><?=$employee->name?></td>
                                                <td><?=$employee->emparea?></td>
                                                <td><?php echo ($employee->is_crew_leader)?'Y':'';?></td>
                                                <td><?php echo ($employee->is_driver)?'Y':'';?></td>
                                                <td><?php echo ($employee->is_rx)?'Y':'';?></td>
                                                <td><?=$employee->benchmark?>%</td>
                                            </tr>
                                            <?php }?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>	
                    </div>	
                </div>
            </div>
        </div>

    </div>

    

    <div class="col-md-12">
        {!! Form::button('Validate Schedule', ['class' => 'btn btn-primary validateschedule'],['tabindex'=>15]) !!}
        <input type="submit" class="btn btn-primary event_schedule_form_btn" disabled="disabled" value="Submit">
        <input type="reset" class="btn btn-default cancel-btn" value="Cancel">
    </div>

            </div>
        </div>
</div>

<div class="modal confrm-pop fade" id="confirm-back" tabindex="-2" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="z-index: 1051 !important;">
     <div class="modal-dialog modal-md">
         <div class="modal-content text-center">
             <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
             <strong>Are you sure you want to leave this page?</strong>
             <p>Changes you made may not be saved.</p>
             <button type="button" class="btn btn-danger confirm_back_button_no" data-dismiss="modal">No</button>
             <a href="" class="btn btn-success btn-ok confirm_back_button_alert">Yes</a>
         </div>
     </div>
 </div>
@stop

@section('javascript')
@parent
<script type="text/javascript">
    
    $(document).ready(function(){
        $(document).on('click','.validateschedule',function(){
            $('.validateschedule').attr('disabled',true);
            $('.cancel-btn').addClass('disabled');
            var postData = new FormData($("#event_schedule_form")[0]);
                $.ajax({
                    type:"POST",
                    url:'/admin/events/validateSchedule',
                    data:postData,
                    cache       : false,
                    processData: false,
                    contentType: false,
                    success:function(res){
                        if(res.status=="Success")
                        {
                            $('.validateschedule').attr('disabled',true);
                            $('.event_schedule_form_btn').attr('disabled',false);
                            $('.cancel-btn').removeClass('disabled');
                        }else{
                            var html='<div class="note note-danger"><ul class="list-unstyled">';
                            $.each( res.errors, function( key, value ) {
                                html+="<li>"+value+"</li>";
                            });
                            html+='</ul></div>';
                            $(".error-container").html(html).show();
                            $("html, body").animate({ scrollTop: 0 }, "slow");
                            $('.cancel-btn').removeClass('disabled');
                            $('.validateschedule').attr('disabled',false);
                            
                            setTimeout(function(){
                                $('.error-container').html('').hide();
                            }, 15000);
                        }
                    }
                });
           
        });
    })
    $(document).ready(function () {
        "use strict";
        (() => {
        const modified_inputs = new Set;
        const defaultValue = "defaultValue";
        // store default values
        addEventListener("beforeinput", (evt) => {
            const target = evt.target;
            if (!(defaultValue in target || defaultValue in target.dataset)) {
                target.dataset[defaultValue] = ("" + (target.value || target.textContent)).trim();
            }
        });
        // detect input modifications
        addEventListener("input", (evt) => {
            const target = evt.target;
            let original;
            if (defaultValue in target) {
                original = target[defaultValue];
            } else {
                original = target.dataset[defaultValue];
            }
            if (original !== ("" + (target.value || target.textContent)).trim()) {
                if (!modified_inputs.has(target)) {
                    modified_inputs.add(target);
                }
            } else if (modified_inputs.has(target)) {
                modified_inputs.delete(target);
            }
        });
        // clear modified inputs upon form submission
        addEventListener("submit", () => {
            //alert('yy');
            modified_inputs.clear();
            formSubmitting = true;
            // to prevent the warning from happening, it is advisable
            // that you clear your form controls back to their default
            // state with form.reset() after submission
        });
        // warn before closing if any inputs are modified
        addEventListener("beforeunload", (evt) => {
            $("#event_schedule_form").on("submit", function(){
                return true;
              })
            if ((modified_inputs.size || $(".unassign_employee").length) && formSubmitting==false) {
                const unsaved_changes_warning = "Changes you made may not be saved.";
                evt.returnValue = unsaved_changes_warning;
                return unsaved_changes_warning;
            }
        });
        })();
        var formSubmitting = false;
        var setFormSubmitting = function() { formSubmitting = true; };
        var enable_calc=<?php echo $enable_calc;?>;
        
                    
        var isFilled = false;
        $('.timepicker').timepicker({
            showInputs: false
        })
        .on('changeTime.timepicker', function(e) { 
            var event_start_time = "11/24/2014 "+$(".event_start_time").html();
            var updatedtime = "11/24/2014 "+e.time.value;
            var aDate = new Date(event_start_time).getTime();
            var bDate = new Date(updatedtime).getTime();
            if(aDate < bDate){
                 $(this).timepicker('setTime', $(".event_start_time").html());
                return false;
            }else if (aDate > bDate){
                return true;
            }else{
                return true;
            }
        });
        
        $(document).on("change",".additional_area_dropdown",function(){
            $('.validateschedule').attr('disabled',false);
            $('.event_schedule_form_btn').attr('disabled',true);
            var area_id = $(this).val();
            var additional_area_count = $(this).attr('additional_area_count');
            $.ajax({
                url: "/admin/calculate_additional_area_distance",
                data: {'event_id':$(".event_id").val(),'origin':area_id,'origin_type':'area','destination':'<?php echo $event->store->id?>','destination_type':'store'},
                type: "POST",
                success: function (data) {
                   $('.additional_area_meet_time'+additional_area_count).val(data.meet_time);
                   
               },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            //$(".extra_area_container").append('');
        })
        $(document).on("click",".add_more_area",function(){
            var additional_area_counter = $(".additional_area_counter").val();
            var event_data = $("#event_schedule_form").serialize();
            $.ajax({
                url: "/admin/event_schedule_extra_area",
                data: event_data,
                type: "POST",
                success: function (data) {
                   $('.extra_area_container').append(data.html);
                   $('.timepicker').timepicker({
                        showInputs: false
                    })
                    .on('changeTime.timepicker', function(e) { 
                        var event_start_time = "11/24/2014 "+$(".event_start_time").html();
                        var updatedtime = "11/24/2014 "+e.time.value;
                        var aDate = new Date(event_start_time).getTime();
                        var bDate = new Date(updatedtime).getTime();
                        if(aDate < bDate){
                             $(this).timepicker('setTime', $(".event_start_time").html());
                            return false;
                        }else if (aDate > bDate){
                            return true;
                        }else{
                            return true;
                        }
                    });
                    $(".additional_area_counter").val(additional_area_counter+1);
               }
            });
            //$(".extra_area_container").append('');
        })
        $(document).on("click",".remove_additional_area",function(){
            var additional_counter = $(this).attr('additional_counter');
            $(".arearow"+additional_counter).remove();
        })
        $(document).on("click",".event_schedule_form_btn",function(){
            formSubmitting = true;
            //modified_inputs.clear();
            return true;
        })
        $('.assign_employee').click(function () {
            var employeeID = $(this).attr('data-id');
            $(this).closest("tr").addClass("disbleTableRow");
            $(this).removeClass("assign_employee");
            $(this).css('cursor','auto');
            $.ajax({
                type: "GET",
                url: '/admin/employees/'+employeeID,
                success: function (data) {
                    var html='<tr id="assign_emp_'+data.employee.id+'"> <input type="hidden" name="emp_id[]" value="'+data.employee.id+'"><td>'+data.employee.name+'</td>\n\
                                    <td><select class="form-control formControl-dorpdown emp_task" name="task[]">\n\
                                        <option>Auditor</option>\n\
                                        <option>Driver To</option>\n\
                                        <option>Driver From</option>\n\
                                        <option>Driver To & From</option>\n\
                                        \n\<option>RX</option>\n\<option>RX/Driver To</option>\n\
                                        <option>RX/Driver From</option>\n\
                                        \n\<option>RX/Driver To & From</option>\n\
                                        \n\<option>Supervisor</option>\n\
                                        <option>Super/Driver To</option>\n\
                                        <option>Super/Driver From</option>\n\
                                     <option>Super/Driver To & From</option>\n\
                                    \n\</select>\n\
<input type="hidden" class="emp_hourly_rate" name="emp_hourly_rate['+data.employee.id+']" value="'+parseFloat(data.employee.payrate.replace("$","")).toFixed(2)+'">\n\
                                    </td><td><span class="employee_benchmark">'+data.employee.benchmark+'</span>%</td>\n\
                                    <td><input type="text" class="cus-formControl col-md-3 emp_vehicle'+data.employee.id+'" name="vehicle[]">\n\
                                            &nbsp;&nbsp;<i data-id="'+data.employee.id+'" class="fa fa-check commentcheck'+data.employee.id+'" aria-hidden="true"></i>\n\
                                            &nbsp;&nbsp;<i data-id="'+data.employee.id+'" class="fa fa-pencil-square-o commentpencil'+data.employee.id+'" aria-hidden="true" style="display:none;"></i>\n\
                                    </td>\n\
                                    <td><input type="text" name="comment[]" class="cus-formControl" data-id="'+data.employee.id+'">\n\
                                        <input type="text" name="custom_comment[]" data-id="'+data.employee.id+'" class="form-control custom_comment'+data.employee.id+'" style="display:none"></td>\n\
                                    <td align="center" class="unassign_employee" data-id="'+data.employee.id+'"><i class="fa fa-minus-circle" aria-hidden="true"></i></td>\n\
                                </tr>';
                    $(".scheduled_employees_container").append(html);
                    $('.validateschedule').attr('disabled',false);
                    $('.event_schedule_form_btn').attr('disabled',true);
                    if(enable_calc)
                    {
                        calScheduleProduction();
                        calScheduleLength();
                        calGrossProfit();
                    }
                    var event_data = $("#event_schedule_form").serialize();
                     $.ajax({
                         url: "/admin/employee_schedule_area_wise",
                         data: event_data,
                         type: "POST",
                         success: function (data) {
                            $('.area_wise_employee_count').html(data);
                        }
                     });
                }
            });
        })
        $(document).on("change",".emp_task",function(){
            var supercount=0;
            $(".emp_task").each(function(){
                var str = $(this).val();
                var res = str.match(/Super/g);
                if(res)
                    supercount++;
                if(supercount>=2)
                {
                    alert('Only 1 Supervisor allowed for an Event.');
                    $(this).val('Auditor');
                    //return false;
                }
            });
            if(enable_calc)
            {
                calGrossProfit();
                calScheduleProduction();
            }
        })
        $(document).on("click", ".unassign_employee", function(){
            var employeeID = $(this).attr('data-id');
            $('#emp'+employeeID).removeClass('disbleTableRow');
            $("#emp"+employeeID+"tr td:nth-child(3)").addClass("assign_employee").css('cursor','pointer');
            $("#assign_emp_"+employeeID).remove();
            $("#emp"+employeeID+" tr td:nth-child(3)").css('cursor','auto');
            if(enable_calc)
            {
                calScheduleProduction();
                calScheduleLength();
                calGrossProfit();
            }
            var event_data = $("#event_schedule_form").serialize();
            $.ajax({
                url: "/admin/employee_schedule_area_wise",
                data: event_data,
                type: "POST",
                success: function (data) {
                   $('.area_wise_employee_count').html(data);
               }
            });
        });
        $(document).on("change", ".comment_dropdown", function(){
            var employeeID = $(this).attr('data-id');
            var comment = $(this).val();
            if(comment=="Other")
            {
                $(".custom_comment"+employeeID).show();
            }else{
                $(".custom_comment"+employeeID).val('');
                $(".custom_comment"+employeeID).hide();
            }
        });
        $(document).on("click", ".fa-check", function(){
            var employeeID = $(this).attr('data-id');
            var vechile = $('.emp_vehicle'+employeeID).addClass('greyedout');
            $(".commentcheck"+employeeID).hide();
            $(".commentpencil"+employeeID).show();
        });
        $(document).on("click", ".fa-pencil-square-o", function(){
            var employeeID = $(this).attr('data-id');
            var vechile = $('.emp_vehicle'+employeeID).removeClass('greyedout');
            $(".commentcheck"+employeeID).show();
            $(".commentpencil"+employeeID).hide();
        });
        
    if(enable_calc)
    {
        calScheduleProduction();
        calScheduleLength();
        calGrossProfit();
        var event_data = $("#event_schedule_form").serialize();
        $.ajax({
            url: "/admin/employee_schedule_area_wise",
            data: event_data,
            type: "POST",
            success: function (data) {
               $('.area_wise_employee_count').html(data);
           }
        });
    }    
    
    
    })
    
const convertTime12to24 = (time12h) => {
  const [time, modifier] = time12h.split(' ');

  let [hours, minutes] = time.split(':');

  if (hours === '12') {
    hours = '00';
  }

  if (modifier === 'PM') {
    hours = parseInt(hours, 10) + 12;
  }

  return `${hours}:${minutes}`;
}
function checkFormFilled(classname)
{
    alert('fff');return false
    
    //alert(isFilled);
    $("input[type=text],select").each(function(key,value) {
               
        if ($(this).val()) {
            isFilled = true;
            //console.log($(this));
        }
        
    });
    if($('input:radio').is(':checked'))
    {
        isFilled = true;
    }

    if(isFilled===true){
        
        alert('chang');
        
    }else{
        return true;
    }
}

function calScheduleProduction()
{
    var store_benchmark = parseInt($(".store_benchmark").val());
    var emp_count = parseInt($(".employee_benchmark").length);
    var total_prod=0;
    $(".employee_benchmark").each(function() {
        var employee_benchmark = parseInt($(this).html());
        //total_prod = parseFloat(total_prod+(store_benchmark*employee_benchmark/100));
        var emp_benchmark = (store_benchmark*employee_benchmark)/100
        var task = $(this).closest('tr').find('.emp_task option:selected').text();
        var res = task.match(/Super/g);
        var store_spf = $('.store_spf').val();
        if(res)
        {
            //alert('super');
            emp_benchmark = ((emp_benchmark*store_spf)/100);
            //console.log('SPF='+emp_benchmark);
        }
        
        //alert(emp_benchmark);
        total_prod = parseFloat(total_prod+emp_benchmark);
        
    });
    var scheduled_production = number_format(parseFloat(total_prod/emp_count).toFixed(0));
    ////console.log(scheduled_production);
    $(".scheduled_production").val(scheduled_production);
}

function calScheduleLength()
{
    var inventory_level = parseInt($(".inventory_level").val());
    var store_benchmark = parseInt($(".store_benchmark").val());
    var emp_count = parseInt($(".employee_benchmark").length);
    ////console.log('emp_count='+emp_count);
    var total_prod=0;
    $(".employee_benchmark").each(function() {
        var employee_benchmark = parseInt($(this).html());
        
        
        
        //total_prod = parseFloat(total_prod+(store_benchmark*employee_benchmark/100));
        var emp_benchmark = (store_benchmark*employee_benchmark)/100
        var task = $(this).closest('tr').find('.emp_task option:selected').text();
        var res = task.match(/Super/g);
        var store_spf = $('.store_spf').val();
        if(res)
        {
            //alert('super');
            emp_benchmark = ((emp_benchmark*store_spf)/100);
            total_prod = parseFloat(total_prod+emp_benchmark);
            ////console.log('total_prod='+total_prod);
        }else{
            total_prod = parseFloat(total_prod+(store_benchmark*employee_benchmark/100));
            ////console.log('total_prod='+total_prod);
        }
        
        
    });
    ////console.log('total_prod='+total_prod);
    var scheduled_production = parseFloat(total_prod/emp_count).toFixed(0);
    var total_count_per_hour = parseFloat(scheduled_production*emp_count);
    ////console.log('total_count_per_hour='+total_count_per_hour);
    var scheduled_length = parseFloat(inventory_level/total_count_per_hour).toFixed(1);
    $(".schedule_length").val(scheduled_length);
    calGrossProfit();
}

function calGrossProfit()
{
    var last_inventory_value = parseInt($(".last_inventory_value").val());
    var rate_per = parseInt($(".rate_per").val());
    var rate = parseFloat($(".rate").val());
    ////console.log('last_inventory_value='+last_inventory_value);
    //console.log('rate_per='+rate_per);
    //console.log('rate='+rate);
        var store_billing = parseFloat((last_inventory_value/rate_per)*rate).toFixed(2);
    $('.store_billing').val(store_billing);
    //console.log('store_billing='+store_billing);
    var vehicle_count=0;
    $(".emp_task").each(function() {
        if($(this).val()=="Driver To" || $(this).val()=="Driver To & From")
            vehicle_count++;
    });
    var emp_count = parseInt($(".employee_benchmark").length);
    var project_count_time = $(".schedule_length").val();
    var total_hourly_rate = 0;
    $(".emp_hourly_rate").each(function() {
        total_hourly_rate += parseFloat($(this).val());
    });
    //console.log('project_count_time='+project_count_time);
    //console.log('total_hourly_rate='+total_hourly_rate);
    var cost1 = total_hourly_rate*project_count_time;
    var round_trip_miles = $(".round_trip_miles").val();
    var cost2 = parseFloat(round_trip_miles*.07*emp_count).toFixed(2);
    var cost3 = parseFloat(vehicle_count*.60*round_trip_miles).toFixed(2);
    var cost = parseFloat(cost1+cost2+cost3).toFixed(2);
    //console.log('vehicle count='+vehicle_count);
    //console.log('cost1='+cost1);
    //console.log('cost2='+cost2);
    //console.log('cost3='+cost3);
    //console.log('Total Cost='+cost);
    var profit = parseFloat(store_billing-cost).toFixed(2);
    //console.log('profit='+profit);
    //console.log('store_billing='+store_billing);
    if(store_billing>0)
        var gross_profit = parseFloat(profit/store_billing*100).toFixed(2);
    else
        var gross_profit = '0.00';
    $(".gross_profit").val(gross_profit);
    calLaborPercent();
}
function calLaborPercent()
{
    var schedule_length = $(".schedule_length").val();
    var total_labor_cost = 0;
    
    $(".emp_hourly_rate").each(function() {
        total_labor_cost += parseFloat($(this).val()*schedule_length);
    });
    //console.log('total_labor_cost=='+total_labor_cost);
    var store_billing = $('.store_billing').val();
    if(store_billing>0)
        var labor_percent = parseFloat(total_labor_cost/store_billing*100).toFixed(2);
    else
        var labor_percent = '0.00';
    $(".labor_percent").val(labor_percent);
}

function number_format (number, decimals, decPoint, thousandsSep) { // eslint-disable-line camelcase
  number = (number + '').replace(/[^0-9+\-Ee.]/g, '')
  var n = !isFinite(+number) ? 0 : +number
  var prec = !isFinite(+decimals) ? 0 : Math.abs(decimals)
  var sep = (typeof thousandsSep === 'undefined') ? ',' : thousandsSep
  var dec = (typeof decPoint === 'undefined') ? '.' : decPoint
  var s = ''

  var toFixedFix = function (n, prec) {
    if (('' + n).indexOf('e') === -1) {
      return +(Math.round(n + 'e+' + prec) + 'e-' + prec)
    } else {
      var arr = ('' + n).split('e')
      var sig = ''
      if (+arr[1] + prec > 0) {
        sig = '+'
      }
      return (+(Math.round(+arr[0] + 'e' + sig + (+arr[1] + prec)) + 'e-' + prec)).toFixed(prec)
    }
  }

  // @todo: for IE parseFloat(0.55).toFixed(0) = 0;
  s = (prec ? toFixedFix(n, prec).toString() : '' + Math.round(n)).split('.')
  if (s[0].length > 3) {
    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep)
  }
  if ((s[1] || '').length < prec) {
    s[1] = s[1] || ''
    s[1] += new Array(prec - s[1].length + 1).join('0')
  }

  return s.join(dec)
}


//window.onbeforeunload = function(formSubmitting) {
//    if (formSubmitting) {
//            return undefined;
//        }
//   return "Do you really want to leave our brilliant application?";
//   //if we return nothing here (just calling return;) then there will be no pop-up question at all
//   //return;
//};

</script>
@stop