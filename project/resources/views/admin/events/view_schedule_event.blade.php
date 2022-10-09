@extends('layouts.app')
@section('pageTitle', 'Add Event')
@section('content')
<div class="panel panel-default">
    <div class="panel-heading">Schedule Event</div>
    <?php 
    $schedule_employees = $event->schedule_employees->toArray();
    $employees_assigned = array();
    foreach($schedule_employees as $schedule_employee)
        $employees_assigned[$schedule_employee['employee_id']] = $schedule_employee;
    //echo '<pre>';print_r($employees_assigned);die;
    ?>
        <div class="panel-body">
            <div class="row">
                {!! Form::open(['method' => 'POST', 'route' => ['admin.events.save_schedule_event'],'id'=>'event_schedule_form', 'files' => true,]) !!}
                
    <div class="col-lg-12 col-md-12">
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
                            <div class="col-lg-12"><div class="readable-textfield"><?=date('h:i A',strtotime($event->start_time))?></div></div>
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
                                <div class="readable-redio"><?php if(@$event->event_schedule_data->loading_confirmed=="Yes"){echo 'Yes';}else{echo 'No';}?></div>
                                
                            </div>
                            <div class="col-lg-12 schedule_label">Max Ct Length</div><div class="col-lg-12"><?=@$event->store->max_length?></div>
                            <div class="col-md-12 schedule_label">Scheduled Ct Length<br>
                                <?=@$event->event_schedule_data->schedule_length;?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-6 empViewtField">
                        <div class="row">
                            <div class="col-lg-12 schedule_label">Inventory Level</div>
                            <div class="col-lg-12"><div class="readable-textfield"><?='$'.number_format($event->store->inventory_level)?></div></div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 empViewtField">
                        <div class="row">
                            <div class="col-lg-12 schedule_label">Benchmark</div>
                            <div class="col-lg-12"><div class="readable-textfield"><?=number_format(@$event->store->benchmark)?></div></div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 empViewtField">
                        <div class="row">
                            <div class="col-lg-12 schedule_label">Scheduled Production</div>
                            <div class="col-lg-12"><?=@$event->event_schedule_data->scheduled_production;?></div>
                        </div>
                    </div>

                    
                    
                </div>
                
                <div class="row">
                    <div class="col-sm-8 empViewtField">
                        <div class="col-lg-6 schedule_label">Area Assigned</div>
                        <div class="col-lg-6 schedule_label">Meet Time</div>
                        <?php $round_trip_miles=0;
                            $event_time_zone= getTimezone($event->store->city->name.', '.$event->store->state->name);
                            foreach($event->areas as $area){
                            $dist = calDistance($area->area->id,'area',$event->store->id,'store');
                            $duration = (int)($dist->rows[0]->elements[0]->duration->value+($dist->rows[0]->elements[0]->duration->value/3600)*15*60);
                            $distance = number_format((float)(($dist->rows[0]->elements[0]->distance->value/1000)*0.621371),2,'.','');
                            $round_trip_miles+=$distance*2;
                            //echo '<pre>';print_r($dist);
                            $currentTime = strtotime($event->start_time);
                            //echo $event->store->city->name.$event->store->state->name;
                            $area_time_zone= getTimezone($area->area->city->name.', '.$area->area->state->name);
                            
                            ?>
                        <div class="row">
                            <div class="col-lg-6"><div class="readable-textfield"><?=$area->area->title;?></div></div>
                            <div class="col-lg-6"><div class="readable-textfield">
                                <?php if($area->meet_time && $area_time_zone==$event_time_zone){
                                    echo date("h:i A",strtotime($area->meet_time));
                                }elseif($area->meet_time && $area_time_zone!=$event_time_zone){
                                    //echo converToTz(date("h:i A", $area->area->meet_time),$area_time_zone,$event_time_zone);
                                    echo date("h:i A",strtotime($area->meet_time)).'* <span class="error-block">Different Timezone</span>';
                                    //echo 'j'.date("H:i A",strtotime($area->area->meet_time));
                                }elseif($area_time_zone!=$event_time_zone){
                                    //echo date("h:i A", ($currentTime-$duration)).'---'.$area_time_zone.'---'.$event_time_zone;die;
                                    echo converToTz(date("h:i A", ($currentTime-$duration)),$area_time_zone,$event_time_zone).'* <span class="error-block">Different Timezone</span>';
                                    //echo date("H:i A", $currentTime-$duration);
                                }else{
                                    echo date("h:i A", $currentTime-$duration);
                                }?></div></div>
                        </div>
                        <?php }?>
                        
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 empViewtField">
                        <div class="row">
                            <div class="col-lg-12 schedule_label">Gross Profit<br>
                                <?=@$event->event_schedule_data->gross_profit;?>
                            </div>
                            <div class="col-lg-12 schedule_label">Labor<br>
                                <?=@$event->event_schedule_data->labor_percent;?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="row">
                            <div class="col-lg-4 schedule_label">Field Notes</div>
                            <div class="col-lg-8"><?=@$event->event_schedule_data->field_notes;?></div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 schedule_label">Schedule Comments:</div>
                            <div class="col-lg-8"><?=$event->schedule_notes;?></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 schedule_label">
                        Total Ctrs assigned by Area:
                    </div>
                    <div class="col-md-8 area_wise_employee_count">
                        
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
                                        <td><?=@$emp->task?></td>
                                        <td><span class="employee_benchmark"><?=@$emp->employee->benchmark?></span>%</td>
                                        <td><?=@$emp->vehicle_number?></td>
                                        <td><?=@$emp->comment;?><br><?=@$emp->custom_comment;?></td>
                
                                    </tr>
                <?php          }
                            }else{?>
                                <tr> 
                                    <td colspan="5" style="text-align:center">No employee scheduled for this event yet.</td>
                                </tr>
                <?php       }
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
                    <div class="col-md-4 col-sm-6 empViewtField">
                        <div class="row">
                            <div class="col-lg-12 schedule_label">Supervisor</div>
                            <div class="col-lg-12"><div class="readable-textarea"><?=event_supervisor_by_event_id(@$historical_data->id)?></div></div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 empViewtField">
                        <div class="row">
                            <div class="col-lg-12 schedule_label">Positive Experience?</div>
                            <div class="col-lg-12"><div class="readable-textarea"><?=@$historical_data->positive_exp?></div></div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 empViewtField">
                        <div class="row">
                            <div class="col-lg-12 schedule_label">QC Comments:</div>
                            <div class="col-lg-12"><div class="readable-textarea"><?=@$historical_data->qc_comment?></div></div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 empViewtField">
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
                                    <div class="col-lg-12"><div class="readable-textfield"><?=@$historical_data_from_timesheet['crew_count'];?></div></div>
                                </div>
                            </div>
                        </div>	
                    </div>
                    <div class="col-md-4 col-sm-6 empViewtField">
                        <div class="row">
                            <div class="col-lg-12 schedule_label">Comments/Notes:</div>
                            <div class="col-lg-12">
                                <div class="readable-textarea">
                                    <?=@$historical_data_from_timesheet['comments'];?><br>
                                    <?php //@$historical_data->field_comment?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
    </div>

            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <a href="javascript:history.back()" class="btn btn-default">@lang('global.app_back_to_list')</a>
                </div>
            </div>
        </div>
</div>
@stop

@section('javascript')
@parent
<script type="text/javascript">
    $(document).ready(function () {
        $('.timepicker').timepicker({
            showInputs: false
        })
        var event_data = $("#event_schedule_form").serialize();
        $.ajax({
            url: "/admin/employee_schedule_area_wise",
            data: event_data,
            type: "POST",
            success: function (data) {
               $('.area_wise_employee_count').html(data);
           }
        });
    })
</script>
@stop