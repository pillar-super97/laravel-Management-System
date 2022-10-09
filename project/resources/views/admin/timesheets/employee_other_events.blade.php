@extends('layouts.app')
@section('pageTitle', 'Employee Schedule')
@section('content')
    <h3 class="page-title"></h3>
    <div class="panel panel-default">
        <div class="panel-heading">Employee Schedule</div>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                    <th>Store Number</th>
                    <th>Store Name</th>
                    <th>Start Time</th>
                    <th>Stop Time</th>
                    </thead>
                <tbody>
                    <?php 
                    foreach($events as $event){
                    //echo '<pre>';print_r($emp);die;?>
                        <tr>
                            <td>{{$event->storenumber}}</td>
                            <td>{{$event->storename}}</td>
                            <td>{{date('g:i A',strtotime($event->dtStartDateTime))}}</td>
                            <td>{{date('g:i A',strtotime($event->dtStopDateTime))}}</td>
                        </tr>
            <?php   }?>
                </tbody>
                </table>
            </div>
        </div>
    </div>
@stop
@section('javascript')
    @parent
@stop