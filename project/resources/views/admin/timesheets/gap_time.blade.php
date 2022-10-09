@extends('layouts.app')
@section('pageTitle', 'Employee Gap Display')
@section('content')
    <h3 class="page-title"></h3>
    <div class="panel panel-default">
        <div class="panel-heading">Employee Gap Display</div>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                    <th colspan="2"><?=date('m-d-Y',strtotime($timesheets->date))?></th>
                    <th>GapTimeExplanation</th>
                    <th>Gap Start</th>
                    <th>Gap End</th>
                    <th>Gap Minutes</th>
                    </thead>
                <tbody>
                    <tr>
                        <td></td>
                        <td><b>{{$timesheets->id}} , {{$timesheets->storename}}</b></td>
                        <td colspan="4"></td>
                    </tr>
                    <?php 
                    foreach($gap_reports as $i=>$gap_report){
                    //echo '<pre>';print_r($emp);die;
                    if($i==0){
                        echo '<tr>
                            <td>'.$employee->id.'</td>
                            <td>'.$employee->last_name.', '.$employee->first_name.'</td>
                            <td>'.$gaptimeexplanation->gaptimeexplanation.'</td>
                            <td colspan="3"></td>
                        </tr>';
                    }?>
                        <tr>
                            <td colspan="3"></td>
                            <td>{{date('g:i:s A',strtotime($gap_report->GapStart))}}</td>
                            <td>{{date('g:i:s A',strtotime($gap_report->GapEnd))}}</td>
                            <td><?php echo $gap_report->GapMinutes;
//                                $ts1 = strtotime($gap_report->GapStart);
//                                $ts2 = strtotime($gap_report->GapEnd);     
//                                $seconds_diff = $ts2 - $ts1;                            
//                                echo $time = round(($seconds_diff/60));
                            
                                ?></td>
                        </tr>
            <?php   }?>
                </tbody>
                </table>
            </div>
        </div>
    </div>
    <a href="{{ url()->previous() }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
@stop
@section('javascript')
    @parent
@stop